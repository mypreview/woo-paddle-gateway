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

		wp_register_style( 'woo-paddle-gateway', self::get_asset_path( 'admin.css' ), array(), woo_paddle_gateway()->get_version(), 'screen' );
		wp_register_script( 'woo-paddle-gateway', self::get_asset_path( 'admin.js' ), array( 'jquery' ), woo_paddle_gateway()->get_version(), true );
	}

	/**
	 * Returns a full path for the asset (static resource CSS/JS) file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name Asset file name (filename).
	 *
	 * @return string
	 */
	private static function get_asset_path( string $file_name ): string {

		$min       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : trailingslashit( 'minified' );
		$directory = pathinfo( $file_name, PATHINFO_EXTENSION );

		return woo_paddle_gateway()->service( 'file' )->plugin_url( "assets/{$directory}/{$min}{$file_name}" );
	}
}
