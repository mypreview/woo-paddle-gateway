<?php
/**
 * The plugin admin notices class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Enhancements;

/**
 * The plugin notices class.
 */
class Notices {

	/**
	 * The dismiss nonce name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const DISMISS_NONCE_NAME = 'woo-paddle-gateway-dismiss';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'admin_notices', array( $this, 'print' ) );
	}

	/**
	 * Display the admin notices.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function print() {

		/**
		 * Outputs any plugin related admin notices.
		 *
		 * @since 1.0.0
		 */
		do_action( 'woo_paddle_gateway_admin_notices' );
	}
}
