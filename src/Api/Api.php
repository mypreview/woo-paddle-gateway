<?php
/**
 * The cached storage for the data fetched from Paddle API.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/api
 */

namespace Woo_Paddle_Gateway\Api;

/**
 * Fetch data from Paddle API.
 */
class Api {

	/**
	 * The expiration time for the cached data.
	 *
	 * @since 1.0.0
	 */
	const EXPIRATION = MONTH_IN_SECONDS;

	/**
	 * The Paddle API credentials.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private array $credentials = array(
		'current_mode'     => 'live',
		'vendor_id'        => '',
		'vendor_auth_code' => '',
	);

	/**
	 * Fetch the products from Paddle API.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_products(): array {

		$this->set_credentials();

		return $this->fetch_products();
	}

	/**
	 * Fetch the subscription plans from Paddle API.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plans(): array {

		$this->set_credentials();

		return $this->fetch_plans();
	}

	/**
	 * Get the product options for the select field.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_product_options(): array {

		$products = $this->get_products();

		// Check if the products are available.
		if ( empty( $products ) ) {
			return array();
		}

		$options = array();

		// Loop through the products.
		foreach ( $products as $product ) {

			// Skip if the product ID or name is not set.
			if ( ! isset( $product['id'] ) || ! isset( $product['name'] ) ) {
				continue;
			}

			$options[ (string) $product['id'] ] = wc_clean( $product['name'] );
		}

		return $options;
	}

	/**
	 * Get the subscription plan options for the select field.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plan_options(): array {

		$plans = $this->get_plans();

		// Check if the plans are available.
		if ( empty( $plans ) ) {
			return array();
		}

		$options = array();

		// Loop through the plans.
		foreach ( $plans as $plan ) {

			// Skip if the plan ID or name is not set.
			if ( ! isset( $plan['id'] ) || ! isset( $plan['name'] ) ) {
				continue;
			}

			$options[ (string) $plan['id'] ] = wc_clean( $plan['name'] );
		}

		return $options;
	}

	/**
	 * Fetch the products from Paddle API or return the cached data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function fetch_products(): array {

		// Check if the credentials are set.
		if ( empty( $this->credentials['vendor_id'] ) || empty( $this->credentials['vendor_auth_code'] ) ) {
			return array();
		}

		$mode   = $this->credentials['current_mode'];
		$cached = (array) get_transient( "_woo_paddle_gateway_catalog_products_{$mode}" );

		if ( ! empty( $cached ) ) {
			return $cached;
		}

		$request = wp_remote_post(
			Endpoints::get_endpoint( $mode, 'catalog_products' ),
			array(
				'timeout' => 30,
				'body'    => array(
					'vendor_id'        => wc_clean( $this->credentials['vendor_id'] ),
					'vendor_auth_code' => wc_clean( $this->credentials['vendor_auth_code'] ),
				),
			)
		);

		// Check if the request was successful.
		if ( is_wp_error( $request )
			|| 200 !== wp_remote_retrieve_response_code( $request )
		) {
			return array();
		}

		$response = json_decode( $request['body'], true );

		// Check if the response is valid.
		if ( ! isset( $response['success'] )
			|| ! (bool) $response['success']
			|| ! isset( $response['response']['products'] )
		) {
			return array();
		}

		return $this->set_catalog_products( $response['response']['products'], $mode );
	}

	/**
	 * Fetch the subscription plans from Paddle API or return the cached data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function fetch_plans(): array {

		// Check if the credentials are set.
		if ( empty( $this->credentials['vendor_id'] ) || empty( $this->credentials['vendor_auth_code'] ) ) {
			return array();
		}

		$mode   = $this->credentials['current_mode'];
		$cached = (array) get_transient( "_woo_paddle_gateway_subscription_plans_{$mode}" );

		if ( ! empty( $cached ) ) {
			return $cached;
		}

		$request = wp_remote_post(
			Endpoints::get_endpoint( $mode, 'subscription_plans' ),
			array(
				'timeout' => 30,
				'body'    => array(
					'vendor_id'        => wc_clean( $this->credentials['vendor_id'] ),
					'vendor_auth_code' => wc_clean( $this->credentials['vendor_auth_code'] ),
				),
			)
		);

		// Check if the request was successful.
		if ( is_wp_error( $request )
			|| 200 !== wp_remote_retrieve_response_code( $request )
		) {
			return array();
		}

		$response = json_decode( $request['body'], true );

		// Check if the response is valid.
		if ( ! isset( $response['success'] )
			|| ! (bool) $response['success']
			|| ! isset( $response['response'] )
		) {
			return array();
		}

		return $this->set_subscription_plans( $response['response'], $mode );
	}

	/**
	 * Set the Paddle API credentials.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function set_credentials(): void {

		$this->credentials = woo_paddle_gateway()->service( 'paddle_manager' )->get_gateway_keys();
	}

	/**
	 * Set the catalog products in the transient.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $products The products fetched from Paddle API.
	 * @param string $mode     The current mode (live or sandbox).
	 *
	 * @return array
	 */
	private function set_catalog_products( array $products, string $mode ): array {

		set_transient( "_woo_paddle_gateway_catalog_products_{$mode}", $products, self::EXPIRATION );

		return $products;
	}

	/**
	 * Set the subscription plans in the transient.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $plans The subscription plans fetched from Paddle API.
	 * @param string $mode  The current mode (live or sandbox).
	 *
	 * @return array
	 */
	private function set_subscription_plans( array $plans, string $mode ): array {

		set_transient( "_woo_paddle_gateway_subscription_plans_{$mode}", $plans, self::EXPIRATION );

		return $plans;
	}
}
