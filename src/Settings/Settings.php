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

use WC_Order;
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
	 * Process the payment and return the result.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|int $order_id Order ID.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {

		// Get the current API credentials.
		$saved_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// Check if the credentials are set.
		if ( empty( $saved_keys->vendor_id ) || empty( $saved_keys->vendor_auth_code ) ) {
			wc_add_notice(
				__(
					'We encountered an issue while processing your request. It appears that the necessary API keys for the payment gateway are missing. Please ensure that you have provided the correct API keys required for the integration.',
					'woo-paddle-gateway'
				),
				'error'
			);

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Paddle API credentials are missing.' );

			return wp_json_encode(
				array(
					'result'  => 'failure',
				)
			);
		}

		// Initialize the order.
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		$item  = $items[ array_keys( $items )[0] ];
		$meta  = get_post_meta( $item->get_product_id(), '_woo_paddle_gateway', true );
		$type  = $meta['type'] ?? 'subscription';

		$request_args = array(
			'discountable'      => 0,
			'quantity_variable' => 0,
			'is_recoverable'    => 0,
			'vendor_id'         => wc_clean( $saved_keys->vendor_id ),
			'vendor_auth_code'  => wc_clean( $saved_keys->vendor_auth_code ),
			'passthrough'       => wc_clean( $order_id ),
			'product_id'        => wc_clean( $meta[ $meta['type'] ?? 'subscription' ] ),
			'quantity'          => wc_clean( $item->get_quantity() ),
			'customer_email'    => sanitize_email( $order->get_billing_email() ),
			'prices'            => array( wc_clean( $order->get_currency() ) . ':' . wc_clean( $order->get_total() ) ),
			'title'             => wc_clean( $item->get_name() ),
			'return_url'        => add_query_arg( 'checkout', '{checkout_hash}', $order->get_checkout_order_received_url() ),
			'image_url'         => esc_url( wp_get_attachment_image_src( get_post_thumbnail_id( $item->get_product_id() ), array( '220', '220' ), true )[0] ),
		);

		// Check if the order is a subscription.
		if ( 'subscription' === $type ) {
			// Recurring price(s) of the checkout (excluding the initial payment) only if the product_id specified is a subscription.
			$request_args['recurring_prices'] = $request_args['prices'];
		}

		$request = wp_remote_post(
			woo_paddle_gateway()->service( 'endpoints' )->get( 'generate_pay_link', $saved_keys->is_sandbox ),
			array(
				'timeout' => 30,
				'body'    => $request_args,
			)
		);

		// Check if the request was successful.
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			wc_add_notice(
				__(
					'An error occurred while retrieving the checkout URL. Please verify if the gateway has been properly integrated.',
					'woo-paddle-gateway'
				),
				'error'
			);

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Paddle API request failed.' );

			return wp_json_encode(
				array(
					'result'  => 'failure',
				)
			);
		}

		// Decode the response body.
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		// Check if the response is valid.
		if ( empty( $response->success ) || empty( $response->response->url ) ) {
			wc_add_notice(
				__(
					'Our system encountered an error while attempting to generate the payment link. Unfortunately, we are unable to proceed with the payment process at the moment.',
					'woo-paddle-gateway'
				),
				'error'
			);

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Generated Paddle checkout URL is invalid or failed to generate.' );

			return wp_json_encode(
				array(
					'result'  => 'failure',
				)
			);
		}

		echo wp_json_encode(
			array(
				'result'            => 'success',
				'generate_pay_link' => wc_clean( $response->response->url ),
			)
		);

		exit();
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
		if ( $this->saved_keys->vendor_id === $vendor_id && $this->saved_keys->vendor_auth_code === $vendor_auth_code ) {
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

		woo_paddle_gateway()->service( 'products' )->fetch();
		woo_paddle_gateway()->service( 'plans' )->fetch();

		return 'yes';
	}
}
