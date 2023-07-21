<?php
/**
 * Paddle webhook events.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Paddle;

use WC_Order;
use Woo_Paddle_Gateway\Admin;

/**
 * Class Events.
 */
trait Events {

	/**
	 * Handle the subscription payment created webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function subscription_created( $webhook_data ) {

		$order_id = $webhook_data['passthrough'] ?? '';
		$order    = wc_get_order( $order_id );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return;
		}

		// Set the order status to "Completed".
		$order->update_status( 'completed' );

		// Set the order meta data.
		$this->save_payload_log( $order_id, $webhook_data );
	}

	/**
	 * Handle the subscription cancelled webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function subscription_cancelled( $webhook_data ) {

		$order_id = $webhook_data['passthrough'] ?? '';
		$order    = wc_get_order( $order_id );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return;
		}

		// Set the order status to "Cancelled".
		$order->update_status( 'cancelled' );

		// Set the order meta data.
		$this->save_payload_log( $order_id, $webhook_data );
	}

	/**
	 * Handle the subscription refunded webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function subscription_payment_refunded( $webhook_data ) {

		$order_id = $webhook_data['passthrough'] ?? '';
		$order    = wc_get_order( $order_id );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return;
		}

		// Set the order status to "Refunded".
		$order->update_status( 'refunded' );

		// Set the order meta data.
		$this->save_payload_log( $order_id, $webhook_data );
	}

	/**
	 * Handle the subscription renewal payment succeeded webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function subscription_payment_succeeded( $webhook_data ) {

		$order_id = $webhook_data['passthrough'] ?? '';
		$order    = wc_get_order( $order_id );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return;
		}

		// Set the order status to "Completed".
		$order->update_status( 'completed' );

		// Add a note to the order for the next bill date.
		if ( ! empty( $webhook_data['next_bill_date'] ) ) {
			$order->add_order_note(
				sprintf( /* translators: %s: Checkout ID. */
					__( 'New subscription renewal payment received. Next bill date: %s.', 'woo-paddle-gateway' ),
					wc_clean( $webhook_data['next_bill_date'] )
				)
			);
		}

		// Set the order meta data.
		$this->save_payload_log( $order_id, $webhook_data );
	}

	/**
	 * Handle the subscription payment failed webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function subscription_payment_failed( $webhook_data ) {

		$order_id = $webhook_data['passthrough'] ?? '';
		$order    = wc_get_order( $order_id );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return;
		}

		// Set the order status to "Failed".
		$order->update_status( 'failed' );

		// Add a note to the order for the next retry date.
		if ( ! empty( $webhook_data['next_retry_date'] ) ) {
			$order->add_order_note(
				sprintf( /* translators: %s: Checkout ID. */
					__( 'Subscription payment failed. Next bill date: %s.', 'woo-paddle-gateway' ),
					wc_clean( $webhook_data['next_retry_date'] )
				)
			);
		}

		// Add a note to the order for the attempt number.
		if ( ! empty( $webhook_data['attempt_number'] ) ) {
			$order->add_order_note(
				sprintf( /* translators: %s: Checkout ID. */
					__( 'Subscription payment failed. Attempt number: %s.', 'woo-paddle-gateway' ),
					wc_clean( $webhook_data['attempt_number'] )
				)
			);
		}

		// Set the order meta data.
		$this->save_payload_log( $order_id, $webhook_data );
	}

	/**
	 * Set subscription meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $order_id     The order ID.
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function save_payload_log( $order_id, $webhook_data ) {

		// Retrieve the alert name.
		$alert_name = $webhook_data['alert_name'];

		if ( empty( $alert_name ) ) {
			return;
		}

		// Retrieve existing order meta data.
		$paddle_log = (array) get_post_meta( $order_id, Admin\Order::LOG_KEY, true );
		$paddle_log = array_filter( $paddle_log );

		// Add the webhook data to the order meta data.
		array_push( $paddle_log, $webhook_data );

		// Save updated order meta data.
		update_post_meta( $order_id, Admin\Order::LOG_KEY, $paddle_log );

		// Save the subscription meta data.
		$this->save_subscription_meta( $order_id, $webhook_data );
	}

	/**
	 * Set subscription meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $order_id     The order ID.
	 * @param array $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function save_subscription_meta( $order_id, $webhook_data ) {

		$keys_to_update = array(
			'checkout_id',
			'cancellation_effective_date',
			'linked_subscriptions',
			'next_bill_date',
			'status',
			'subscription_id',
			'subscription_plan_id',
			'next_payment_amount',
			'receipt_url',
			'cancel_url',
			'update_url',
		);

		// Retrieve existing order meta data.
		$order_meta = (array) get_post_meta( $order_id, Admin\Order::META_KEY, true );
		$order_meta = array_filter( $order_meta );

		// Loop through the keys to be updated and clean the data if available.
		foreach ( $keys_to_update as $key ) {
			// Skip if the key is not set.
			if ( empty( $webhook_data[ $key ] ) ) {
				continue;
			}

			$order_meta[ $key ] = wc_clean( $webhook_data[ $key ] );
		}

		// Save updated order meta data.
		update_post_meta( $order_id, Admin\Order::META_KEY, $order_meta );
	}
}
