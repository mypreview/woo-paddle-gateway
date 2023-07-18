<?php
/**
 * Settings helpers.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Helper;

/**
 * Helpers for the plugin settings.
 */
abstract class Settings {

	/**
	 * Check if the current page is the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_page() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return (
			isset( $_GET['page'] )
			&& 'wc-settings' === $_GET['page']
			&& isset( $_GET['tab'] )
			&& woo_paddle_gateway()->get_slug() === $_GET['tab']
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the plugin settings URI.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function page_uri() {

		// e.g, "http://example.com/wp-admin/admin.php?page=wc-settings&tab=woo-paddle-gateway".
		return add_query_arg(
			array(
				'page' => 'wc-settings',
				'tab'  => woo_paddle_gateway()->get_slug(),
			),
			admin_url( 'admin.php' )
		);
	}
}