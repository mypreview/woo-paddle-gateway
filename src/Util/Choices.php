<?php
/**
 * The util choices for the plugin.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Util;

/**
 * Class Choices.
 */
class Choices {

	/**
	 * Get the internal function.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The function name.
	 *
	 * @return array|bool
	 */
	public function get( $name ) {

		// Call the internal function if exists.
		if ( method_exists( $this, "get_{$name}" ) ) {
			return call_user_func( array( $this, "get_{$name}" ) );
		}

		return false;
	}

	/**
	 * List of the catalog products from Paddle.
	 * The list is used for the products dropdown.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_catalog_products() {

		$products = woo_paddle_gateway()->service( 'products' )->get();

		// Check if the products are available.
		if ( empty( $products ) ) {
			return array();
		}

		$options = array();

		// Loop through the products.
		foreach ( $products as $product ) {

			// Skip if the product ID or name is not set.
			if ( ! isset( $product->id ) || ! isset( $product->name ) ) {
				continue;
			}

			$options[ wc_clean( $product->id ) ] = wc_clean( $product->name . ' (' . $product->id . ')' );
		}

		return $options;
	}

	/**
	 * List of the catalog products from Paddle.
	 * The list is used for the products dropdown.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_subscription_plans() {

		$plans = woo_paddle_gateway()->service( 'plans' )->get();

		// Check if the products are available.
		if ( empty( $plans ) ) {
			return array();
		}

		$options = array();

		// Loop through the products.
		foreach ( $plans as $plan ) {

			// Skip if the product ID or name is not set.
			if ( ! isset( $plan->id ) || ! isset( $plan->name ) ) {
				continue;
			}

			$options[ wc_clean( $plan->id ) ] = wc_clean( $plan->name . ' (' . $plan->id . ')' );
		}

		return $options;
	}
}
