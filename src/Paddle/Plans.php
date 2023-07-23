<?php
/**
 * Utility class to get subscription plans from Paddle.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Paddle;

/**
 * Class Plans.
 */
class Plans {

	/**
	 * Get the products.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get() {

		$saved_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// Check if the credentials are set.
		if ( empty( $saved_keys->vendor_id ) || empty( $saved_keys->vendor_auth_code ) ) {
			return array();
		}

		return woo_paddle_gateway()->service( 'options' )->get( "plans_{$saved_keys->current_mode}", array() );
	}

	/**
	 * Fetch the catalog products from Paddle.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function fetch() {

		$saved_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// Check if the credentials are set.
		if ( empty( $saved_keys->vendor_id ) || empty( $saved_keys->vendor_auth_code ) ) {
			return;
		}

		$request_body = array(
			'vendor_id'        => wc_clean( $saved_keys->vendor_id ),
			'vendor_auth_code' => wc_clean( $saved_keys->vendor_auth_code ),
		);

		$request = wp_remote_post(
			woo_paddle_gateway()->service( 'endpoints' )->get( 'subscription_plans', $saved_keys->is_sandbox ),
			array(
				'timeout' => 30,
				'body'    => $request_body,
			)
		);

		// Check if the request was successful.
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return;
		}

		// Decode the response body.
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		// Check if the response is valid.
		if ( empty( $response->success ) || empty( $response->response ) ) {
			return;
		}

		woo_paddle_gateway()->service( 'options' )->update(
			(array) $response->response,
			"plans_{$saved_keys->current_mode}"
		);
	}
}
