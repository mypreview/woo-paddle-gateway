<?php
/**
 * Payment gateway manager.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/gateway
 */

namespace Woo_Paddle_Gateway\Gateway;

use WC;
use WC_Payment_Gateway;

/**
 * Payment gateway manager.
 */
class Manager {

	/**
	 * Get the payment gateway object.
	 *
	 * @since 1.0.0
	 *
	 * @return null|WC_Payment_Gateway
	 */
	public function get_gateway(): ?WC_Payment_Gateway {

		// Bail early in case Payment Gateways are not available.
		if ( ! WC()->payment_gateways() ) {
			return null;
		}

		// Get the available payment gateways.
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		// Bail early in case the Paddle gateway is not available.
		if ( ! isset( $available_gateways[ Paddle::ID ] ) ) {
			return null;
		}

		// Return the Paddle gateway object.
		return $available_gateways[ Paddle::ID ];
	}

	/**
	 * Get the payment gateway keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_gateway_keys(): array {

		// Get the payment gateway object.
		$gateway = $this->get_gateway();

		// Bail early in case the Paddle gateway is not available.
		if ( ! $gateway ) {
			return array();
		}

		// Whether the gateway is enabled or not.
		$enabled = wc_string_to_bool( $gateway->enabled );

		// Bail early in case the gateway is not enabled.
		if ( ! $enabled ) {
			return array();
		}

		// Get the current mode.
		$is_sandbox   = wc_string_to_bool( $gateway->get_option( 'sandbox_mode' ) );
		$current_mode = $is_sandbox ? 'test' : 'live';

		// Return the keys.
		return array(
			'test_mode'        => $is_sandbox,
			'current_mode'     => $current_mode,
			'vendor_id'        => $gateway->get_option( "{$current_mode}_vendor_id" ),
			'vendor_auth_code' => $gateway->get_option( "{$current_mode}_vendor_auth_code" ),
			'public_key'       => $gateway->get_option( "{$current_mode}_vendor_public_key" ),
		);
	}
}
