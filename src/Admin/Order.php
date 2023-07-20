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
	 *
	 * @var string
	 */
	const META_KEY = '_woo_paddle_gateway';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'add_meta_boxes_shop_order', array( $this, 'register_paddle_details' ) );
	}

	/**
	 * Register a dedicated meta-box.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_paddle_details() {

		// Enqueue the order meta-box assets.
		wp_enqueue_style( 'woo-paddle-gateway-order' );

		// Register the meta-box.
		add_meta_box(
			woo_paddle_gateway()->get_slug(),
			__( 'Paddle details', 'woo-paddle-gateway' ),
			array( $this, 'show_paddle_details' ),
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
			'order/paddle-details.php',
			array(
				'meta' => get_post_meta( $post->ID, self::META_KEY, true ),
			)
		);
	}
}
