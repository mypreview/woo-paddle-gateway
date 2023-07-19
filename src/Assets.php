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

use Woo_Paddle_Gateway\Enhancements\Notices;

/**
 * Load plugin static resources (CSS and JS files).
 */
abstract class Assets {

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_admin() {

		$version = woo_paddle_gateway()->get_version();

		wp_register_style(
			'woo-paddle-gateway-admin',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'admin.css' ),
			array( 'woocommerce_admin_styles' ),
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

		wp_register_script(
			'woo-paddle-gateway-dismiss',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'dismiss.js' ),
			array( 'jquery' ),
			$version,
			true
		);
		wp_localize_script(
			'woo-paddle-gateway-dismiss',
			'woo_paddle_gateway_params',
			array(
				'dismiss_nonce' => wp_create_nonce( Notices::DISMISS_NONCE_NAME ),
			)
		);
	}

	/**
	 * Enqueue front-end scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_frontend() {

		$version      = woo_paddle_gateway()->get_version();
		$gateway_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_register_script(
			'paddle',
			'https://cdn.paddle.com/paddle/paddle.js',
			array( 'jquery', 'wc-checkout' ),
			null,
			true
		);

		wp_register_style(
			'woo-paddle-gateway',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'style.css' ),
			array(),
			$version,
			'screen'
		);
		wp_register_script(
			'woo-paddle-gateway',
			woo_paddle_gateway()->service( 'file' )->asset_path( 'script.js' ),
			array( 'jquery', 'wc-checkout', 'paddle' ),
			$version,
			true
		);
		wp_localize_script(
			'woo-paddle-gateway',
			'woo_paddle_gateway_admin_params',
			array(
				'is_sandbox'   => wc_clean( $saved_keys->is_sandbox ?? '' ),
				'vendor_id'    => wc_clean( $saved_keys->vendor_id ?? '' ),
				'checkout_uri' => add_query_arg(
					array(
						'wc-ajax' => is_wc_endpoint_url( 'order-pay' ) ? 'payment' : 'checkout',
					),
					home_url( '/' )
				),
			)
		);
	}
}
