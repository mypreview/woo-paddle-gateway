<?php
/**
 * The core plugin class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

use Pimple\Container;

/**
 * The plugin class.
 */
class Plugin extends Container {

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version The plugin version.
	 * @param string $file    The plugin file.
	 *
	 * @return void
	 */
	public function __construct( string $version, string $file ) {

		// Set the version.
		$this->version = $version;

		// Pimple Container construct.
		parent::__construct();

		// Register the file service.
		$this['file'] = fn() => new File( $file );

		// Register services early.
		$this->register_services();

		// Load the plugin.
		$this->load();
	}

	/**
	 * Register services.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function register_services(): void {

		$provider = new PluginServiceProvider();
		$provider->register( $this );
	}

	/**
	 * Get a service by given key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The service key.
	 *
	 * @return mixed
	 */
	public function service( string $key ) {

		return $this[ $key ];
	}

	/**
	 * Get the plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_version(): string {

		return $this->version;
	}

	/**
	 * Start loading classes on `woocommerce_loaded`, priority 20.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load(): void {

		// Register payment gateways.
		$gateways = new Gateway\Register();
		$gateways->setup();

		// WooCommerce cart.
		$wc_cart = new WooCommerce\Cart();
		$wc_cart->setup();

		// WooCommerce checkout.
		$wc_checkout = new WooCommerce\Checkout();
		$wc_checkout->setup();

		// WooCommerce product.
		$wc_product = new WooCommerce\Product();
		$wc_product->setup();

		add_action( 'wp_enqueue_scripts', array( 'Woo_Paddle_Gateway\\Assets', 'enqueue_frontend' ) );
		add_action( 'admin_enqueue_scripts', array( 'Woo_Paddle_Gateway\\Assets', 'enqueue_admin' ) );
	}
}
