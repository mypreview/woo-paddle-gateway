<?php
/**
 * The plugin installer class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

/**
 * The plugin installer class.
 */
class Installer {

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate() {

		// Add the activation timestamp, if not already added.
		woo_paddle_gateway()->service( 'options' )->add_usage_timestamp();
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate() {}
}
