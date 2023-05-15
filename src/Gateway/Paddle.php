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

use WC_Payment_Gateway;

/**
 * Paddle payment gateway.
 */
class Paddle extends WC_Payment_Gateway {

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

		$this->id                 = 'woo-paddle-gateway';
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

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
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
			'live_vendor_id' => array(
				'title'       => __( 'Vendor ID', 'woo-paddle-gateway' ),
				'type'        => 'text',
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
			'test_vendor_id' => array(
				'title'       => __( 'Vendor ID', 'woo-paddle-gateway' ),
				'type'        => 'text',
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
