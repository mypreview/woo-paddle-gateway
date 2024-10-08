<?php
/**
 * The `Paddle Payment Gateway for WooCommerce` bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * You can redistribute this plugin/software and/or modify it under
 * the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * @link https://mypreview.one
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @copyright © 2015 - 2023 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:          Woo Paddle Gateway
 * Plugin URI:           https://mypreview.one
 * Description:          Accept Visa, MasterCard, American Express, Discover, JCB, Diners Club, iDEAL, Alipay, and more directly on your store with the Paddle payment gateway for WooCommerce, including PayPal, Apple Pay, Google Pay, and Microsoft Pay for mobile and desktop.
 * Version:              1.0.0
 * Author:               MyPreview
 * Author URI:           https://mypreview.one
 * Requires at least:    5.9
 * Requires PHP:         7.4
 * License:              GPL-3.0
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:          woo-paddle-gateway
 * Domain Path:          /languages
 * WC requires at least: 5.5
 * WC tested up to:      7.8
 */

use Woo_Paddle_Gateway\Plugin;
use WC_Install_Notice\Nag;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Loads the PSR-4 autoloader implementation.
 *
 * @since 1.0.0
 *
 * @return void
 */
require_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/vendor/autoload.php';

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 *
 * @return mixed|Plugin
 */
function woo_paddle_gateway() {

	static $instance;

	if ( is_null( $instance ) ) {
		$version  = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		$instance = new Plugin( $version['Version'] ?? '1.0.0', __FILE__ );
	}

	return $instance;
}

/**
 * Load the plugin after all plugins are loaded.
 *
 * @since 1.0.0
 *
 * @return void
 */
function woo_paddle_gateway_load() {

	// Fetch the instance.
	woo_paddle_gateway();
}

if ( ! (
		( new Nag() )
		->set_file_path( __FILE__ )
		->set_plugin_name( 'Woo Paddle Gateway' )
		->does_it_requires_nag()
	)
) {

	add_action( 'woocommerce_loaded', 'woo_paddle_gateway_load', 20 );

	// Register activation and deactivation hooks.
	register_activation_hook( __FILE__, array( 'Woo_Paddle_Gateway\\Installer', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Woo_Paddle_Gateway\\Installer', 'deactivate' ) );
}
