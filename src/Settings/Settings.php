<?php
/**
 * The plugin settings.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings;

use WC_Payment_Gateway;
use Woo_Paddle_Gateway\Helper;

/**
 * Class Settings.
 */
class Settings extends WC_Payment_Gateway {

	/**
	 * Setup settings class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		$this->assign();
		$this->setup();
		$this->enqueue();
		$this->init_form_fields();
		$this->init_settings();
	}

	/**
	 * Assign the settings properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function assign() {

		$this->id                 = sanitize_key( woo_paddle_gateway()->get_slug() );
		$this->method_title       = _x( 'Paddle', 'settings tab label', 'woo-paddle-gateway' );
		$this->method_description = _x( 'Paddle payment gateway for WooCommerce', 'settings tab description', 'woo-paddle-gateway' );
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->has_fields         = false;
		$this->supports           = array( 'products' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->icon               = woo_paddle_gateway()->service( 'file' )->plugin_url( 'assets/img/payment-icons.svg' );
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Add plugin specific class to body.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Classes to be added to the body element.
	 *
	 * @return string
	 */
	public function add_body_class( $classes ) {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return $classes;
		}

		$classes .= sprintf( ' %s-page', sanitize_html_class( $this->id ) );

		return $classes;
	}

	/**
	 * Enqueue the settings assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function enqueue() {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return;
		}

		// Enqueue the settings assets.
		wp_enqueue_style( 'woo-paddle-gateway-admin' );
		wp_enqueue_script( 'woo-paddle-gateway-admin' );
	}

	/**
	 * Initialize the gateway settings form fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = woo_paddle_gateway()->service( 'settings_general' )->get_fields();
	}
}
