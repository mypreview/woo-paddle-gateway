<?php
/**
 * Dismiss onboarding (welcome) admin notice.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Ajax;

use WC;

/**
 * Onboarding admin notice class.
 */
class Checkout extends Ajax {

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
			'ajax_process_checkout',
			'woocommerce-process-checkout-nonce'
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

		// Register the AJAX action.
		$this->set_action_prefix( '' );
		$this->set_action_scope( 'wc' );
		$this->register_admin();
		$this->register_frontend();
	}

	/**
	 * AJAX dismiss the admin notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_callback() {

		// Process the checkout.
		WC()->checkout()->process_checkout();

		exit();
	}
}
