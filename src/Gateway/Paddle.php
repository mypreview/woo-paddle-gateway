<?php
/**
 * The plugin assets (static resources).
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/gateway
 */

namespace Woo_Paddle_Gateway\Gateway;

use Woo_Paddle_Gateway\Api\Endpoints;
use WC_Payment_Gateway;

/**
 * Paddle payment gateway.
 */
class Paddle extends WC_Payment_Gateway {

	/**
	 * The gateway ID.
	 *
	 * @since 1.0.0
	 */
	const ID = 'woo-paddle-gateway';

	/**
	 * Setup settings class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		$this->assign();
		$this->hooks();
		$this->enqueue();
		$this->init_form_fields();
		$this->init_settings();
	}

	/**
	 * Assign the gateway properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function assign(): void {

		$this->id                 = sanitize_key( self::ID );
		$this->method_title       = __( 'Paddle', 'woo-paddle-gateway' );
		$this->method_description = __( 'Paddle payment gateway for WooCommerce', 'woo-paddle-gateway' );
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->has_fields         = false;
		$this->supports           = array( 'products' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->icon               = woo_paddle_gateway()->service( 'file' )->plugin_url( 'assets/images/payment-icons.svg' );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function hooks() {

		add_action( "woocommerce_update_options_payment_gateways_{$this->id}", array( $this, 'process_admin_options' ) );
	}

	/**
	 * Enqueue the gateway assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function enqueue() {

		// Bail early if the current page is not the gateway settings page.
		if ( ! $this->is_page() ) {
			return;
		}

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
	public function init_form_fields(): void {

		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Status', 'woo-paddle-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable the gateway.', 'woo-paddle-gateway' ),
				'default' => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woo-paddle-gateway' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woo-paddle-gateway' ),
				'default'     => __( 'Paddle', 'woo-paddle-gateway' ),
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'woo-paddle-gateway' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woo-paddle-gateway' ),
				'default'     => __( 'Pay securely using your credit card, debit card, iDEAL, Google Pay, Apple Pay, AliPay, and PayPal.', 'woo-paddle-gateway' ),
				'desc_tip'    => true,
			),
			'is_readonly' => array(
				'title'   => __( 'Protect Fields', 'woo-paddle-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Disable the ability to edit any gateway credential fields.', 'woo-paddle-gateway' ),
				'default' => 'no',
			),
			'sandbox_mode' => array(
				'title'       => __( 'Test Mode (Sandbox)', 'woo-paddle-gateway' ),
				'type'        => 'checkbox',
				'label'       => '&#9888; ' . __( 'Warning! Check this option to prevent Stripe from processing live transactions.', 'woo-paddle-gateway' ),
				'description' => __( 'Place the payment gateway in test mode using test account credentials.', 'woo-paddle-gateway' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'live_vendor_verify' => array(
				'title'             => __( 'Connection', 'woo-paddle-gateway' ),
				'type'              => 'checkbox',
				'label'             => '…',
				'default'           => 'no',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-label' => $this->verify_connection_labels(),
				),
			),
			'live_vendor_id' => array(
				'title'       => __( 'Vendor ID', 'woo-paddle-gateway' ),
				'type'        => 'number',
				'description' => __( 'Enter your Paddle vendor ID.', 'woo-paddle-gateway' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_vendor_auth_code' => array(
				'title'       => __( 'Auth Code', 'woo-paddle-gateway' ),
				'type'        => 'text',
				'description' => __( 'Enter your Paddle Auth Code. This token will be used to interact with the API.', 'woo-paddle-gateway' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_vendor_public_key' => array(
				'title'       => __( 'Public Key', 'woo-paddle-gateway' ),
				'type'        => 'textarea',
				'description' => __( 'Enter your Paddle Public Key. This key will be used to encrypt the payment data.', 'woo-paddle-gateway' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_vendor_verify' => array(
				'title'             => __( 'Connection', 'woo-paddle-gateway' ),
				'type'              => 'checkbox',
				'class'             => 'test_vendor_verify',
				'label'             => '…',
				'default'           => 'no',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-label' => $this->verify_connection_labels(),
				),
			),
			'test_vendor_id' => array(
				'title'       => __( 'Vendor ID', 'woo-paddle-gateway' ),
				'type'        => 'number',
				'description' => __( 'Enter your Paddle vendor ID.', 'woo-paddle-gateway' ),
				'placeholder' => __( 'Test Mode (Sandbox)', 'woo-paddle-gateway' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_vendor_auth_code' => array(
				'title'       => __( 'Auth Code', 'woo-paddle-gateway' ),
				'type'        => 'text',
				'description' => __( 'Enter your Paddle Auth Code. This token will be used to interact with the API.', 'woo-paddle-gateway' ),
				'placeholder' => __( 'Test Mode (Sandbox)', 'woo-paddle-gateway' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_vendor_public_key' => array(
				'title'        => __( 'Public Key', 'woo-paddle-gateway' ),
				'type'         => 'textarea',
				'description'  => __( 'Enter your Paddle Public Key. This key will be used to encrypt the payment data.', 'woo-paddle-gateway' ),
				'placeholder'  => __( 'Test Mode (Sandbox)', 'woo-paddle-gateway' ),
				'default'      => '',
				'desc_tip'     => true,
			),
		);
	}

	/**
	 * Process the gateway settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function process_admin_options(): void {

		// Get the current API credentials.
		$credentials = woo_paddle_gateway()->service( 'paddle_manager' )->get_gateway_keys();

		// Save the gateway settings.
		parent::process_admin_options();

		$data       = $this->get_post_data();
		$is_sandbox = wc_string_to_bool( $this->get_field_value( 'sandbox_mode', 'checkbox', $data ) );
		$mode       = $is_sandbox ? 'test' : 'live';
		$verify     = $this->verify_connection_status( $data, $mode, $credentials );

		// Bail early in case the API connection status is not set.
		if ( ! $verify ) {
			return;
		}

		// Update the API connection status.
		$this->update_option( "{$mode}_vendor_verify", $verify );

		// Flush the cached products.
		delete_transient( "_woo_paddle_gateway_catalog_products_{$mode}" );
		delete_transient( "_woo_paddle_gateway_subscription_plans_{$mode}" );
	}

	/**
	 * Verify the API connection status.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $data        The POST data.
	 * @param string       $mode        The current mode.
	 * @param array        $credentials The API credentials.
	 *
	 * @return null|string
	 */
	private function verify_connection_status( $data, string $mode, array $credentials ): ?string {

		$vendor_id        = $this->get_field_value( "{$mode}_vendor_id", 'text', $data );
		$vendor_auth_code = $this->get_field_value( "{$mode}_vendor_auth_code", 'text', $data );
		$public_key       = $this->get_field_value( "{$mode}_vendor_public_key", 'textarea', $data );

		// Bail early in case the API credentials have not changed.
		if ( $credentials['vendor_id'] === $vendor_id
			&& $credentials['vendor_auth_code'] === $vendor_auth_code
			&& $credentials['public_key'] === $public_key
		) {
			return null;
		}

		$request = wp_remote_post(
			Endpoints::get_endpoint( $mode, 'public_key' ),
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
			return 'no';
		}

		$response = json_decode( $request['body'], true );

		// Check if the response is valid.
		if ( ! isset( $response['success'] )
			|| ! (bool) $response['success']
			|| ! isset( $response['response']['public_key'] )
		) {
			return 'no';
		}

		return 'yes';
	}

	/**
	 * Connection status labels.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function verify_connection_labels(): string {

		return wp_json_encode(
			array(
				'1' => __( 'The API connection has been successfully established.', 'woo-paddle-gateway' ),
				'0' => __( 'Please check your network connection and verify the API credentials.', 'woo-paddle-gateway' ),
			)
		);
	}

	/**
	 * Check if the current page is the gateway settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_page(): bool {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return (
			isset( $_GET['page'] )
			&& 'wc-settings' === $_GET['page']
			&& isset( $_GET['tab'] )
			&& 'checkout' === $_GET['tab']
			&& isset( $_GET['section'] )
			&& $this->id === $_GET['section']
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
