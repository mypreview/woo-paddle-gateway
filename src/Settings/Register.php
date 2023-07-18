<?php
/**
 * Plugin settings registerer.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings;

/**
 * Register plugin settings fields.
 */
class Register {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'woocommerce_payment_gateways', array( $this, 'settings' ) );
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
	public function settings( $settings ) {

		// Add our settings page.
		$settings[] = woo_paddle_gateway()->service( 'settings' );
		return $settings;
	}
}
