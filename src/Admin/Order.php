<?php
/**
 * The WooCommerce order extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Admin;

use WP_Post;
use WC_Order;

/**
 * Class Order.
 */
class Order {

	/**
	 * Order meta key.
	 *
	 * @since 1.0.0
	 */
	const META_KEY = '_woo_paddle_gateway';

	/**
	 * Renewal meta key.
	 *
	 * @since 1.0.0
	 */
	const RENEWAL_KEY = '_woo_paddle_gateway_renewal';

	/**
	 * Log key.
	 *
	 * @since 1.0.0
	 */
	const LOG_KEY = '_woo_paddle_gateway_log';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'show_order_thankyou_page' ) );
		add_action( 'add_meta_boxes_shop_order', array( $this, 'register_meta_boxes' ) );
	}

	/**
	 * Show order thank you page.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return void
	 */
	public function show_order_thankyou_page( $order ) {

		// Ensure the $post is a valid WP_Post object.
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Check if the current user has the required capability to manage WooCommerce.
		if ( ! (bool) get_post_meta( $order->get_id(), self::META_KEY, true ) ) {
			return;
		}

		// Display the Paddle details template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'admin/order/thankyou-page.php',
			array(
				'url' => $order->get_checkout_order_received_url(),
			)
		);
	}

	/**
	 * Register the meta-boxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_meta_boxes() {

		// Enqueue the order meta-box assets.
		wp_enqueue_style( 'woo-paddle-gateway-order' );

		// Get the plugin slug.
		$slug = woo_paddle_gateway()->get_slug();

		add_meta_box(
			"{$slug}-details",
			__( 'Paddle Details', 'woo-paddle-gateway' ),
			array( $this, 'show_paddle_details' ),
			null,
			'normal'
		);

		add_meta_box(
			"{$slug}-renewal-history",
			__( 'Paddle Renewal History', 'woo-paddle-gateway' ),
			array( $this, 'show_paddle_renewal_history' ),
			null,
			'normal'
		);

		add_meta_box(
			"{$slug}-logs",
			__( 'Paddle Log', 'woo-paddle-gateway' ),
			array( $this, 'show_paddle_log' ),
			null,
			'normal'
		);
	}

	/**
	 * Show paddle details.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function show_paddle_details( $post ) {

		// Ensure the $post is a valid WP_Post object.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Check if the current user has the required capability to manage WooCommerce.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Display the Paddle details template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'admin/order/paddle-details.php',
			array(
				'meta' => get_post_meta( $post->ID, self::META_KEY, true ),
			)
		);
	}

	/**
	 * Show paddle renewal history.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function show_paddle_renewal_history( $post ) {

		// Ensure the $post is a valid WP_Post object.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Check if the current user has the required capability to manage WooCommerce.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Display the Paddle details template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'admin/order/paddle-renewal-history.php',
			array(
				'meta' => get_post_meta( $post->ID, self::RENEWAL_KEY, true ),
			)
		);
	}

	/**
	 * Show paddle log.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function show_paddle_log( $post ) {

		// Ensure the $post is a valid WP_Post object.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Check if the current user has the required capability to manage WooCommerce.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Display the Paddle log template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'admin/order/paddle-log.php',
			array(
				'logs' => get_post_meta( $post->ID, self::LOG_KEY, true ),
			)
		);
	}
}
