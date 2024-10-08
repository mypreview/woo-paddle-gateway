<?php
/**
 * Add compatibility with WooCommerce (core) features.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Compatibility;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * WooCommerce Compatibility class.
 */
class WooCommerce {

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'before_woocommerce_init', array( $this, 'add_block_editor_compatibility' ) );
		add_action( 'before_woocommerce_init', array( $this, 'add_hpos_compatibility' ) );
	}

	/**
	 * Declaring compatibility with product block editor.
	 *
	 * Despite being unrelated to the "Product Block Editor,"
	 * a compatibility flag has been added to prevent WooCommerce from labeling it as "uncertain."
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_block_editor_compatibility() {

		// Declare compatibility with product block editor.
		FeaturesUtil::declare_compatibility( 'product_block_editor', woo_paddle_gateway()->service( 'file' )->plugin_file() );
	}

	/**
	 * Declaring compatibility with HPOS.
	 *
	 * This plugin has nothing to do with "High-Performance Order Storage".
	 * However, the compatibility flag has been added to avoid WooCommerce declaring the plugin as "uncertain".
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_hpos_compatibility() {

		// Declare compatibility with HPOS.
		FeaturesUtil::declare_compatibility( 'custom_order_tables', woo_paddle_gateway()->service( 'file' )->plugin_file() );
	}
}
