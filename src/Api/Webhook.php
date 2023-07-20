<?php
/**
 * REST API Paddle webhook controller.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Api;

use WC_Order;
use WP_Error;
use WP_REST_Server;
use Woo_Paddle_Gateway\Admin;

/**
 * Class Webhook.
 */
class Webhook {

	/**
	 * The namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const NAMESPACE = 'woo-paddle-gateway/v1';

	/**
	 * The route.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const ROUTE = 'webhook';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'rest_api_init', array( $this, 'register_listener' ) );
	}

	/**
	 * Register the webhook listener.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_listener() {

		register_rest_route(
			self::NAMESPACE,
			'/' . self::ROUTE,
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'dispatch_webhook_payload' ),
				'show_in_index'       => false,
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Dispatch the webhook payload.
	 *
	 * @since 1.0.0
	 *
	 * @return void|WP_Error|true Returns void if the dispatch is successful,
	 *                           WP_Error if any verification fails,
	 *                           true if the dispatch is successful and valid.
	 */
	public function dispatch_webhook_payload() {

		// Retrieve the saved public key.
		$saved_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// Check if the public key is set.
		if ( empty( $saved_keys->public_key ) ) {
			return new WP_Error(
				'woo_paddle_gateway_rest_cannot_view',
				__( 'Paddle public key is not set.', 'woo-paddle-gateway' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Retrieve the webhook data.
		$payload = file_get_contents( 'php://input' );

		parse_str( $payload, $webhook_data );

		// Process each element in the webhook data to remove slashes.
		foreach ( $webhook_data as $key => $value ) {
			$webhook_data[ $key ] = stripslashes( $value );
		}

		// Check if the webhook signature is set.
		if ( empty( $webhook_data['p_signature'] ) ) {
			return new WP_Error(
				'woo_paddle_gateway_rest_cannot_view',
				__( 'Paddle webhook signature is not set.', 'woo-paddle-gateway' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Pop the signature from the webhook data.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$signature = base64_decode( $webhook_data['p_signature'] );
		unset( $webhook_data['p_signature'] );

		// Check signature and return result.
		ksort( $webhook_data );
		$data = maybe_serialize( $webhook_data );

		// Verify the signature.
		$verification = openssl_verify( $data, $signature, $saved_keys->public_key, OPENSSL_ALGO_SHA1 );

		if ( 1 !== $verification ) {
			return new WP_Error(
				'woo_paddle_gateway_rest_cannot_view',
				__( 'Paddle webhook signature verification failed.', 'woo-paddle-gateway' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Check if the alert name is set.
		if ( empty( $webhook_data['alert_name'] ) ) {
			return new WP_Error(
				'woo_paddle_gateway_rest_cannot_view',
				__( 'Alert name is not set.', 'woo-paddle-gateway' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Retrieve the alert name.
		$alert_name = $webhook_data['alert_name'];

		// Check if the alert name is valid and if the corresponding method exists.
		if ( ! method_exists( $this, $alert_name ) ) {
			return new WP_Error(
				'woo_paddle_gateway_rest_cannot_view',
				__( 'Alert name is not valid.', 'woo-paddle-gateway' ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Call the appropriate method based on the alert name.
		call_user_func( array( $this, $alert_name ), $webhook_data );

		// Return true to indicate successful dispatch and validation.
		return true;
	}


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
		$this->set_subscription_meta_data( $order_id, $webhook_data );
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
	private function set_subscription_meta_data( $order_id, $webhook_data ) {

		// Define the keys to be updated and cleaned.
		$keys_to_update = array(
			'status',
			'checkout_id',
			'marketing_consent',
			'next_bill_date',
			'subscription_id',
			'subscription_plan_id',
			'linked_subscriptions',
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
