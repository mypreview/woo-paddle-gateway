<?php
/**
 * The plugin internationalization class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

/**
 * Loads and defines the internationalization files for this plugin.
 */
abstract class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function textdomain() {

		$domain = 'woo-paddle-gateway';
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// Load the translation file for current language.
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . "{$domain}/{$domain}-{$locale}.mo" );
		load_plugin_textdomain( $domain, false, woo_paddle_gateway()->service( 'file' )->dirname() . '/languages/' );
	}
}
