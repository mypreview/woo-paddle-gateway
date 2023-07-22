<?php
/**
 * Refresh the products and plans from Paddle.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Ajax;

/**
 * RefreshResponses class.
 */
class RefreshResponses extends Ajax {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Parent constructor.
		parent::__construct(
			'refresh_responses',
			'woocommerce-settings'
		);
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		$this->register_admin();
	}

	/**
	 * AJAX request to fetch the products and plans from Paddle.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_callback() {

		// Check the nonce.
		$this->verify_nonce();

		// Get the saved Paddle API credentials.
		$saved_keys = woo_paddle_gateway()->service( 'gateway' )->get_keys();

		// Check if the credentials are set.
		if ( empty( $saved_keys->vendor_id ) || empty( $saved_keys->vendor_auth_code ) || empty( $saved_keys->public_key ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Paddle credentials are not set or invalid.', 'woo-paddle-gateway' ),
				),
				400 // Return 400 (Bad Request) status code.
			);
		}

		// Fetch the products and plans.
		woo_paddle_gateway()->service( 'products' )->fetch();
		woo_paddle_gateway()->service( 'plans' )->fetch();

		wp_send_json_success(
			array(
				'message' => __( 'Products and plans have been fetched successfully.', 'woo-paddle-gateway' ),
			)
		);

		exit();
	}
}
