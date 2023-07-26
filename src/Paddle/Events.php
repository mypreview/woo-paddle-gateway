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

		// Retrieve the order object.
		$order = $this->get_order( $webhook_data );

		// Bail early, if the order does not exist.
		if ( ! $order ) {
			return;
		}

		// Set the order status to "Completed".
		$order->update_status( 'completed' );

		// Set the order meta data.
		$this->save_payload_log( $order->get_id(), $webhook_data );
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

		// Retrieve the order object.
		$order = $this->get_order( $webhook_data );

		// Bail early, if the order does not exist.
		if ( ! $order ) {
			return;
		}

		// Set the order status to "Cancelled".
		$order->update_status( 'cancelled' );

		// Set the order meta data.
		$this->save_payload_log( $order->get_id(), $webhook_data );
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

		// Retrieve the order object.
		$order = $this->get_order( $webhook_data );

		// Bail early, if the order does not exist.
		if ( ! $order ) {
			return;
		}

		// Set the order status to "Refunded".
		$order->update_status( 'refunded' );

		// Set the order meta data.
		$this->save_payload_log( $order->get_id(), $webhook_data );
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

		// Retrieve the order object.
		$order = $this->get_order( $webhook_data );

		// Bail early, if the order does not exist.
		if ( ! $order ) {
			return;
		}

		// Add a note to the order for the next bill date.
		if ( ! empty( $webhook_data['next_bill_date'] ) ) {
			$order->add_order_note(
				sprintf( /* translators: %s: Checkout ID. */
					__( 'New subscription payment received. Next bill date: %s.', 'woo-paddle-gateway' ),
					wc_clean( $webhook_data['next_bill_date'] )
				)
			);
		}

		// Set the order meta data.
		$this->save_payload_log( $order->get_id(), $webhook_data );

		if ( isset( $webhook_data['initial_payment'] ) && ! wc_string_to_bool( $webhook_data['initial_payment'] ) ) {
			// Create a renewal order.
			$this->create_renewal_order( $order, $webhook_data );
		}
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

		// Retrieve the order object.
		$order = $this->get_order( $webhook_data );

		// Bail early, if the order does not exist.
		if ( ! $order ) {
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
		$this->save_payload_log( $order->get_id(), $webhook_data );

		// Create a renewal order.
		$this->create_renewal_order( $order, $webhook_data );
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

		// Save the subscription meta data.
		$this->save_subscription_meta( $order_id, $webhook_data );

		// Retrieve existing order meta data.
		$paddle_log = (array) get_post_meta( $order_id, Admin\Order::LOG_KEY, true );
		$paddle_log = array_filter( $paddle_log );

		// Avoid duplicate entries.
		// Bail early, if the webhook data already exists.
		if ( ! empty( $paddle_log ) && ! empty( $webhook_data['subscription_payment_id'] ) ) {
			$subscription_payment_ids = array_column( $paddle_log, 'subscription_payment_id' );

			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( in_array( $webhook_data['subscription_payment_id'], $subscription_payment_ids ) ) {
				return;
			}
		}

		// Add the webhook data to the order meta data.
		array_push( $paddle_log, $webhook_data );

		// Save updated order meta data.
		update_post_meta( $order_id, Admin\Order::LOG_KEY, $paddle_log );
	}

	/**
	 * Create a renewal order based on the original order.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order        The original order instance.
	 * @param array    $webhook_data The webhook data.
	 *
	 * @return void
	 */
	private function create_renewal_order( $order, $webhook_data ) {

		$order_meta = (array) get_post_meta( $order->get_id(), Admin\Order::RENEWAL_KEY, true );
		$order_meta = array_filter( $order_meta );

		// Bail early, if the renewal order already exists.
		if ( empty( $webhook_data['subscription_payment_id'] ) || isset( $order_meta[ $webhook_data['subscription_payment_id'] ] ) ) {
			return;
		}

		// Step 1: Create a new order instance and copy customer info to it.
		$renewal = wc_create_order();

		$renewal->set_customer_id( $order->get_user_id() );
		$renewal->set_address(
			array(
				'first_name' => $order->get_billing_first_name(),
				'last_name'  => $order->get_billing_last_name(),
				'country'    => $order->get_billing_country(),
				'postcode'   => $order->get_billing_postcode(),
				'email'      => $order->get_billing_email(),
			),
			'billing'
		);

		// Step 2: Add the product to the new order.
		// Retrieve the first item from the original order.
		$items = $order->get_items();
		$item  = $items[ array_keys( $items )[0] ];
		$renewal->add_product( $item->get_product(), $item->get_quantity() );
		$renewal->set_total( $order->get_total() );
		$renewal->set_payment_method( $order->get_payment_method() );
		$renewal->set_payment_method_title( $order->get_payment_method_title() );

		// Step 3: Set the order status for the renewal order.
		$renewal->update_status( $order->get_status() );

		// Step 4: Add order notes for the renewal order and the original order.
		// Note: The order ID is cleaned to prevent any potential security issues.
		$renewal->add_order_note(
			sprintf( /* translators: %s: Checkout ID. */
				__( 'Renewal order created for the subscription order #%1$s %2$s.', 'woo-paddle-gateway' ),
				'<a href="' . esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ) . '">' . $order->get_id() . '</a>',
				wc_clean( $order->get_status() )
			)
		);

		// Step 5: Save the renewal order.
		$renewal->save();

		// Step 6: Add a note to the original order about the renewal order.
		$order->add_order_note(
			sprintf( /* translators: %s: Checkout ID. */
				__( 'A renewal order #%1$s with %2$s status has been created.', 'woo-paddle-gateway' ),
				'<a href="' . esc_url( admin_url( 'post.php?post=' . $renewal->get_id() . '&action=edit' ) ) . '">' . $renewal->get_id() . '</a>',
				wc_clean( $renewal->get_status() )
			)
		);

		// Step 7: Store the info in the meta-data.
		// Add the renewal order ID and other data to the order meta data.
		$renewal_meta = array(
			'order_id'    => $renewal->get_id(),
			'total'       => $renewal->get_total(),
			'date'        => $renewal->get_date_created(),
			'status'      => $renewal->get_status(),
		);

		$keys_to_update = array(
			'next_bill_date',
			'subscription_id',
			'subscription_payment_id',
			'next_payment_amount',
			'receipt_url',
		);

		// Loop through the keys to be updated and clean the data if available.
		foreach ( $keys_to_update as $key ) {
			// Skip if the key is not set.
			if ( empty( $webhook_data[ $key ] ) ) {
				continue;
			}

			$renewal_meta[ $key ] = wc_clean( $webhook_data[ $key ] );
		}

		// Add the webhook data to the order meta data.
		$order_meta[ $webhook_data['subscription_payment_id'] ] = $renewal_meta;

		// Step 8: Save the updated renewal meta data.
		update_post_meta( $order->get_id(), Admin\Order::RENEWAL_KEY, $order_meta );
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

			// Avoid overriding the cancel and update URLs.
			if ( in_array( $key, array( 'cancel_url', 'update_url' ), true ) && ! empty( $order_meta[ $key ] ) ) {
				continue;
			}

			$order_meta[ $key ] = wc_clean( $webhook_data[ $key ] );
		}

		// Save updated order meta data.
		update_post_meta( $order_id, Admin\Order::META_KEY, $order_meta );
	}

	/**
	 * Verify the order-id passed through the webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook_data The webhook data.
	 *
	 * @return bool|WC_Order
	 */
	private function get_order( $webhook_data ) {

		if ( empty( $webhook_data['passthrough'] ) ) {
			return false;
		}

		$order = wc_get_order( $webhook_data['passthrough'] );

		// Bail early, if the order does not exist.
		if ( ! $order || ! $order instanceof WC_Order ) {
			return false;
		}

		return $order;
	}
}
