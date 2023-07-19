<?php
/**
 * WooCommerce checkout customizations.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\WooCommerce;

/**
 * Checkout class.
 */
class Checkout {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_before_checkout_form', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue checkout scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'woo-paddle-gateway' );
		wp_enqueue_script( 'woo-paddle-gateway' );
	}
}
