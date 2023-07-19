<?php
/**
 * The util endpoints for the plugin.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Util;

/**
 * Class Endpoints.
 */
class Endpoints {

	/**
	 * The sandbox prefix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $sandbox_prefix;

	/**
	 * Get the internal function.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name       The function name.
	 * @param bool   $is_sandbox The sandbox mode.
	 *
	 * @return array|bool
	 */
	public function get( $name, $is_sandbox = false ) {

		// Call the internal function if exists.
		if ( method_exists( $this, "get_{$name}" ) ) {
			$this->sandbox_prefix = $is_sandbox ? 'sandbox-' : '';
			return call_user_func( array( $this, "get_{$name}" ) );
		}

		return false;
	}

	/**
	 * Get the endpoint for the catalog products.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_catalog_products() {

		return "https://{$this->sandbox_prefix}vendors.paddle.com/api/2.0/product/get_products";
	}

	/**
	 * Get the endpoint for the subscription plans.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_subscription_plans() {

		return "https://{$this->sandbox_prefix}vendors.paddle.com/api/2.0/subscription/plans";
	}

	/**
	 * Get the endpoint for the subscription plans.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_public_key() {

		return "https://{$this->sandbox_prefix}vendors.paddle.com/api/2.0/user/get_public_key";
	}

	/**
	 * Get the endpoint for the subscription plans.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_generate_pay_link() {

		return "https://{$this->sandbox_prefix}vendors.paddle.com/api/2.0/product/generate_pay_link";
	}
}
