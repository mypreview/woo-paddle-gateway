<?php
/**
 * The plugin settings.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings;

use WC_Payment_Gateway;
use Woo_Paddle_Gateway\Helper;

/**
 * Class Settings.
 */
class Settings extends WC_Payment_Gateway {

	/**
	 * The saved keys.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $saved_keys;

	/**
	 * Whether the current mode is sandbox or not.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_sandbox;

	/**
	 * The current mode.
	 * It could be either test or live.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $current_mode;

	/**
	 * Setup settings class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		$this->assign();
		$this->setup();
		$this->enqueue();
		$this->init_form_fields();
		$this->init_settings();
	}

	/**
	 * Assign the settings properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function assign() {

		$this->id                 = sanitize_key( woo_paddle_gateway()->get_slug() );
		$this->method_title       = _x( 'Paddle', 'settings tab label', 'woo-paddle-gateway' );
		$this->method_description = _x( 'Paddle payment gateway for WooCommerce', 'settings tab description', 'woo-paddle-gateway' );
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->has_fields         = false;
		$this->supports           = array( 'products' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->icon               = woo_paddle_gateway()->service( 'file' )->plugin_url( 'assets/img/payment-icons.svg' );
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		add_action( "woocommerce_update_options_payment_gateways_{$this->id}", array( $this, 'process_admin_options' ) );
	}

	/**
	 * Add plugin specific class to body.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Classes to be added to the body element.
	 *
	 * @return string
	 */
	public function add_body_class( $classes ) {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return $classes;
		}

		$classes .= sprintf( ' %s-page', sanitize_html_class( $this->id ) );

		return $classes;
	}

	/**
	 * Process the gateway settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function process_admin_options() {

		$this->saved_keys   = woo_paddle_gateway()->service( 'gateway' )->get_keys();
		$this->is_sandbox   = wc_string_to_bool( $this->get_field_value( 'sandbox_mode', 'checkbox', $this->get_post_data() ) );
		$this->current_mode = $this->is_sandbox ? 'test' : 'live';

		// Save the gateway settings.
		parent::process_admin_options();

		$verify = $this->verify_connection_status();

		// Bail early in case the API connection status is not set.
		if ( ! $verify ) {
			return;
		}

		// Update the API connection status.
		$this->update_option( "{$this->current_mode}_vendor_verify", wc_bool_to_string( $verify ) );

		/**
		 * Fires after the gateway settings are saved.
		 *
		 * @since 1.0.0
		 *
		 * @param string $mode The gateway mode (test/live).
		 */
		do_action( 'woo_paddle_gateway_settings_saved', $this->current_mode );
	}

	/**
	 * Enqueue the settings assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function enqueue() {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return;
		}

		// Enqueue the settings assets.
		wp_enqueue_style( 'woo-paddle-gateway-admin' );
		wp_enqueue_script( 'woo-paddle-gateway-admin' );
	}

	/**
	 * Initialize the gateway settings form fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = woo_paddle_gateway()->service( 'settings_general' )->get_fields();
	}

	/**
	 * Verify the API connection status.
	 *
	 * @since 1.0.0
	 *
	 * @return null|string
	 */
	private function verify_connection_status() {

		$data             = $this->get_post_data();
		$vendor_id        = $this->get_field_value( "{$this->current_mode}_vendor_id", 'text', $data );
		$vendor_auth_code = $this->get_field_value( "{$this->current_mode}_vendor_auth_code", 'text', $data );
		$public_key       = $this->get_field_value( "{$this->current_mode}_vendor_public_key", 'textarea', $data );

		// Bail early in case the API credentials have not changed.
		if ( $this->saved_keys['vendor_id'] === $vendor_id && $this->saved_keys['vendor_auth_code'] === $vendor_auth_code ) {
			return null;
		}

		$request = wp_remote_post(
			woo_paddle_gateway()->service( 'endpoints' )->get( 'public_key', $this->is_sandbox ),
			array(
				'timeout' => 30,
				'body'    => array(
					'vendor_id'        => wc_clean( $vendor_id ),
					'vendor_auth_code' => wc_clean( $vendor_auth_code ),
				),
			)
		);

		// Check if the request was successful.
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			// Flush the public key.
			$this->update_option( "{$this->current_mode}_vendor_public_key", '' );

			return 'no';
		}

		// Decode the response body.
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		// Check if the response is valid.
		if ( empty( $response->success ) || empty( $response->response->public_key ) ) {
			// Flush the public key.
			$this->update_option( "{$this->current_mode}_vendor_public_key", '' );

			return 'no';
		}

		// Update the public key.
		$this->update_option( "{$this->current_mode}_vendor_public_key", wc_clean( $response->response->public_key ) );

		return 'yes';
	}
}
