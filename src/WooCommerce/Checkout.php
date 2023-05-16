<?php
/**
 * The WooCommerce checkout extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/gateway
 */

namespace Woo_Paddle_Gateway\WooCommerce;

/**
 * Class Checkout.
 */
class Checkout {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup(): void {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ), 20 );
		add_action( 'woocommerce_api_woo_paddle_gateway_ajax_process_checkout', array( $this, 'ajax_process_checkout' ) );
	}

	/**
	 * Enqueue front-end scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_frontend(): void {

		wp_enqueue_script( 'woo-paddle-gateway-checkout' );
	}

	public function ajax_process_checkout() {

		wp_send_json_success( 'www' );
		// WC()->checkout()->process_checkout();

		exit();
	}

	/**
	 * Check if the current page is the checkout page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_page(): bool {

		return is_wc_endpoint_url( 'order-pay' )
			|| is_wc_endpoint_url( 'order-received' )
			|| is_checkout();
	}
}
