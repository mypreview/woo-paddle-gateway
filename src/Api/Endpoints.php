<?php
/**
 * Paddle API endpoints.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/api
 */

namespace Woo_Paddle_Gateway\Api;

/**
 * Paddle API endpoints for fetching and posting data.
 */
abstract class Endpoints {

	/**
	 * Retrieve the Paddle API endpoints.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $is_sandbox Whether the sandbox mode is enabled or not.
	 * @param string $endpoint   The endpoint name.
	 *
	 * @return string|string[]
	 */
	public static function get_endpoint( $is_sandbox = false, $endpoint = '' ) {

		// Prefix the api endpoint with `sandbox-` in case the sandbox mode is enabled.
		$sandbox_prefix = $is_sandbox ? 'sandbox-' : '';

		// List of available endpoints.
		$endpoints = array(
			'catalog_products'   => "https://{$sandbox_prefix}vendors.paddle.com/api/2.0/product/get_products",
			'subscription_plans' => "https://{$sandbox_prefix}vendors.paddle.com/api/2.0/subscription/plans",
			'create_plan'        => "https://{$sandbox_prefix}vendors.paddle.com/api/2.0/subscription/plans_create",
		);

		// Return the endpoint or all endpoints.
		return $endpoint && isset( $endpoints[ $endpoint ] ) ? $endpoints[ $endpoint ] : $endpoints;
	}
}
