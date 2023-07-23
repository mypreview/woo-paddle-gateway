<?php
/**
 * The WooCommerce my-account extensions.
 *
 * This class manages various modifications to the My Account section in WooCommerce.
 * It includes customizing the account menu items, modifying the my orders query, and displaying
 * subscription-related information in the order details.
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

		add_filter( 'woocommerce_account_menu_items', array( $this, 'account_menu_items' ) );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'my_orders_query' ) );
		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'subscription_renewal_history' ) );
		add_filter( 'woocommerce_order_details_after_order_table', array( $this, 'subscription_details' ) );
	}

	/**
	 * Manage the account menu items.
	 *
	 * This method is used to customize the account menu items displayed in the My Account section.
	 * It removes the "Downloads" and "Edit Address" tabs from the menu items.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items The menu items.
	 * 
	 * @return array Modified menu items.
	 */
	public function account_menu_items( $items ) {

		// Remove the "Downloads" and "Edit Address" tabs from the menu.
		unset( $items['downloads'] );
		unset( $items['edit-address'] );

		return $items;
	}

	/**
	 * Modify the my orders query.
	 *
	 * This method is used to customize the my orders query to only show orders that have a Paddle subscription.
	 * It sets a custom meta query to filter orders based on the existence of a specific meta key.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The query arguments.
	 * 
	 * @return array Modified query arguments.
	 */
	public function my_orders_query( $args ) {

		// Include orders that have a Paddle subscription by setting the custom meta query.
		$args['meta_key']     = Admin\Order::META_KEY;
		$args['meta_compare'] = 'EXISTS';

		return $args;
	}

	/**
	 * Show the subscription renewal history in the order details.
	 *
	 * This method is used to display the subscription renewal history in the order details page.
	 * It includes a template to show the relevant subscription-related information.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order The order object.
	 * 
	 * @return void
	 */
	public function subscription_renewal_history( $order ) {

		// Bail out if the order is not an instance of WC_Order.
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Display the Paddle details template for subscription renewal history.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'order/subscription-renewal-history.php',
			array(
				'meta' => get_post_meta( $order->get_id(), Admin\Order::RENEWAL_KEY, true ),
			)
		);
	}

	/**
	 * Show the subscription details in the order details.
	 *
	 * This method is used to display the subscription details in the order details page.
	 * It includes a template to show the relevant subscription-related information.
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

		// Display the Paddle details template for subscription details.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'order/subscription-details.php',
			array(
				'meta' => get_post_meta( $order->get_id(), Admin\Order::META_KEY, true ),
			)
		);
	}
}