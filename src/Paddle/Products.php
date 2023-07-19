<?php
/**
 * Utility class to get catalog products from Paddle.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Paddle;

/**
 * Class Products.
 */
class Products {

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

		return woo_paddle_gateway()->service( 'options' )->get( "products_{$saved_keys->current_mode}", array() );
	}

	/**
	 * Get the catalog products.
	 *
	 * @since 1.0.0
	 *
	 * @return array
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
			woo_paddle_gateway()->service( 'endpoints' )->get( 'catalog_products', $saved_keys->is_sandbox ),
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
		if ( empty( $response->success ) || empty( $response->response->products ) ) {
			return;
		}

		woo_paddle_gateway()->service( 'options' )->update(
			(array) $response->response->products,
			"products_{$saved_keys->current_mode}"
		);
	}
}
