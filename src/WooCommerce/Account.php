<?php
/**
 * The WooCommerce my-account extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\WooCommerce;

use WC_Order;
use Woo_Paddle_Gateway\Admin;

/**
 * Class Account.
 */
class Account {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'my_orders_query' ) );
		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'subscription_details' ) );
	}

	/**
	 * Modify the my orders query.
	 * Only show orders that have a Paddle subscription.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The query arguments.
	 *
	 * @return array
	 */
	public function my_orders_query( $args ) {

		$args['meta_key']     = Admin\Order::META_KEY;
		$args['meta_compare'] = 'EXISTS';

		return $args;
	}

	/**
	 * Show the subscription details in the order details.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order The order object.
	 *
	 * @return void
	 */
	public function subscription_details( $order ) {

		// Bail out if the order is not an instance of WC_Order.
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Display the Paddle details template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'order/subscription-details.php',
			array(
				'meta' => get_post_meta( $order->get_id(), Admin\Order::META_KEY, true ),
			)
		);
	}
}
