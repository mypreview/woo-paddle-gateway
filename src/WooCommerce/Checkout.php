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

use WC;
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

		add_action( 'woocommerce_before_checkout_form', array( $this, 'enqueue_checkout_assets' ) );
		add_action( 'woocommerce_pay_order_before_submit', array( $this, 'enqueue_checkout_assets' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'apply_coupon_via_url' ) );
		add_filter( 'woocommerce_order_button_text', array( $this, 'update_place_order_button_text' ) );
		add_filter( 'woocommerce_coupon_message', array( $this, 'update_coupon_message' ), 10, 3 );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'unset_checkout_fields' ) );
		add_action( 'woocommerce_thankyou_woo-paddle-gateway', array( $this, 'save_checkout_hash' ) );
		add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
	}

	/**
	 * Enqueue checkout scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_checkout_assets() {

		wp_enqueue_script( 'woo-paddle-gateway' );
	}

	/**
	 * Apply coupon via URL parameter if provided.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function apply_coupon_via_url() {

		// Check if a coupon code is applied via URL parameter.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_GET['coupon'] ) ) {
			// If no coupon code is provided in the URL, return early.
			return;
		}

		// Check if the cart is empty or the site is in the customizer.
		if ( WC()->cart->is_empty() || is_customize_preview() ) {
			// If the cart is empty, return early.
			return;
		}

		// Get the coupon code from the URL parameter.
		$coupon_code = wp_unslash( $_GET['coupon'] );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Get the list of currently applied coupons.
		$applied_coupons = WC()->cart->get_applied_coupons();

		// If the coupon is already applied, return early.
		if ( in_array( $coupon_code, $applied_coupons, true ) ) {
			return;
		}

		// If a valid coupon code is provided, add it to the cart.
		if ( empty( $coupon_code ) ) {
			return;
		}

		WC()->cart->add_discount( wc_clean( $coupon_code ) );
	}

	/**
	 * Update the place order button text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function update_place_order_button_text() {

		return __( 'Complete Checkout', 'woo-paddle-gateway' );
	}

	/**
	 * Update the coupon message to display the coupon description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Coupon message.
	 * @param string $code    Coupon code.
	 * @param object $coupon  Coupon object.
	 *
	 * @return string
	 */
	public function update_coupon_message( $message, $code, $coupon ) {

		// Check if a coupon code is applied via URL parameter.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_GET['coupon'] ) ) {
			// If no coupon code is provided in the URL, return early.
			return $message;
		}

		// Check if a coupon code is applied via URL parameter.
		$coupon_code = wc_clean( wp_unslash( $_GET['coupon'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// If there's no coupon code applied or it doesn't match the current coupon code, return the original message.
		if ( empty( $coupon_code ) || $coupon->get_code() !== $coupon_code ) {
			return $message;
		}

		// If the coupon has no description, return the original message.
		if ( empty( $coupon->get_description() ) ) {
			return $message;
		}

		// Update the coupon message to display the coupon description.
		return esc_html( $coupon->get_description() );
	}

	/**
	 * Remove the extra fields from the checkout form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Checkout fields.
	 *
	 * @return array
	 */
	public function unset_checkout_fields( $fields ) {

		unset( $fields['billing']['billing_address_1'] );
		unset( $fields['billing']['billing_city'] );
		unset( $fields['billing']['billing_state'] );

		return $fields;
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
