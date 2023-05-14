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

/**
 * Register payment gateways.
 */
class Register {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup(): void {

		add_filter( 'woocommerce_payment_gateways', array( $this, 'gateways' ) );
	}

	/**
	 * We will add our settings pages using the following filter, so that the code that
	 * being used to hook into that filter is init by a filter later than `wp_loaded`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Setting pages.
	 *
	 * @return array
	 */
	public function gateways( array $settings ): array {

		$settings[] = woo_paddle_gateway()->service( 'paddle' );
		return $settings;
	}
}
