<?php
/**
 * Class Unpaid to handle scheduled CRON task for deleting unpaid orders.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Cron;

use WC_Data_Store;

/**
 * Unpaid Class.
 */
class Unpaid extends Cron {

	/**
	 * Unpaid held duration in minutes.
	 *
	 * @since 1.0.0
	 */
	const UNPAID_HELD_DURATION = 60;

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Parent constructor.
		parent::__construct( 'delete_unpaid_orders' );
	}

	/**
	 * Schedule the CRON task to run once a day.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function schedule() {

		// Schedule the event to run once a day if not already scheduled.
		if ( wp_next_scheduled( $this->get_hook_name() ) ) {
			return;
		}

		wp_schedule_event( time(), 'daily', $this->get_hook_name() );
	}

	/**
	 * Callback function to be executed when the CRON task runs.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function run() {

		$data_store    = WC_Data_Store::load( 'order' );
		$unpaid_orders = $data_store->get_unpaid_orders(
			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			strtotime( '-' . self::UNPAID_HELD_DURATION . ' MINUTES', current_time( 'timestamp' ) )
		);

		// Leave early in case there are no unpaid orders.
		if ( ! $unpaid_orders ) {
			return;
		}

		// Get the Paddle gateway object.
		$gateway = woo_paddle_gateway()->service( 'gateway' )->get();

		// Bail early in case the Paddle gateway is not available.
		if ( empty( $gateway->id ) ) {
			return;
		}

		// Get the Paddle gateway ID.
		$gateway_id = wc_clean( $gateway->id );

		foreach ( $unpaid_orders as $unpaid_order ) {
			$order = wc_get_order( $unpaid_order );

			if ( 'checkout' !== $order->get_created_via() && $gateway_id !== $order->get_payment_method() ) {
				continue;
			}

			// Bypass trash and permanently delete the order.
			$order->delete( true );
		}
	}
}
