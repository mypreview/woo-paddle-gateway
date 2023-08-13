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
		if ( ! (bool) get_post_meta( $order->get_id(), MetaBoxes\Details::META_KEY, true ) ) {
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

		new MetaBoxes\Details();
		new MetaBoxes\RenewalHistory();
		new MetaBoxes\Logs();
	}
}
