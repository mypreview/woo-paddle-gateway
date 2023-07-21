<?php
/**
 * The WooCommerce cart extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\WooCommerce;

/**
 * Class Cart.
 */
class Cart {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup(): void {

		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'only_one_in_cart' ) );
	}

	/**
	 * Only one product in cart.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $passed The product that is being added to the cart.
	 *
	 * @return bool
	 */
	public function only_one_in_cart( $passed ) {

		// Make sure to empty the cart before adding a new product.
		wc_empty_cart();

		return $passed;
	}
}
