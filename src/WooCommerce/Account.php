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

	const ALLOWED_DETAILS = array(
		'status',
		'next_bill_date',
		'subscription_id',
		'cancellation_effective_date',
	);

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup(): void {

		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'subscription_details' ) );
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
				'meta'            => get_post_meta( $order->get_id(), Admin\Order::META_KEY, true ),
				'allowed_details' => self::ALLOWED_DETAILS,
			)
		);
	}
}
