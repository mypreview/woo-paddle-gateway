<?php
/**
 * WooCommerce checkout customizations.
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
 * Checkout class.
 */
class Checkout {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_before_checkout_form', array( $this, 'enqueue' ) );
		add_action( 'woocommerce_thankyou_woo-paddle-gateway', array( $this, 'save_checkout_hash' ) );
	}

	/**
	 * Enqueue checkout scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'woo-paddle-gateway' );
		wp_enqueue_script( 'woo-paddle-gateway' );
	}

	/**
	 * Save the checkout hash.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function save_checkout_hash( $order_id ) {

		// Get the order object.
		$order = wc_get_order( $order_id );

		// Verify the order object and if the 'checkout' parameter exists in the request.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! $order instanceof WC_Order || empty( $_GET['checkout'] ) ) {
			return;
		}

		if ( ! empty( $_GET['key'] ) ) {
			$order->add_order_note(
				sprintf( /* translators: %s: Checkout ID. */
					__( 'WooCommerce order created. (Key: %s).', 'woo-paddle-gateway' ),
					wc_clean( wp_unslash( $_GET['key'] ) )
				)
			);
		}

		$checkout_hash = wp_unslash( $_GET['checkout'] );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Add the checkout hash to the order notes.
		$order->add_order_note(
			sprintf( /* translators: %s: Checkout ID. */
				__( 'Paddle charge complete. (Checkout ID: %s).', 'woo-paddle-gateway' ),
				wc_clean( $checkout_hash )
			)
		);

		// Redirect to the order received page to remove the query string.
		wp_safe_redirect( $order->get_checkout_order_received_url() );

		exit;
	}

}
