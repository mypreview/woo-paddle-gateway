<?php
/**
 * The plugin assets (static resources).
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

/**
 * Load plugin static resources (CSS and JS files).
 */
abstract class Assets {

	/**
	 * Enqueue front-end scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_frontend(): void {}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_admin(): void {

		$version = woo_paddle_gateway()->get_version();

		wp_register_style(
			'woo-paddle-gateway-admin',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'admin.css' ),
			array(),
			$version,
			'screen'
		);
		wp_register_script(
			'woo-paddle-gateway-admin',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'admin.js' ),
			array( 'jquery' ),
			$version,
			true
		);
		wp_register_script(
			'woo-paddle-gateway-product',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'product.js' ),
			array( 'jquery' ),
			$version,
			true
		);
	}
}
