<?php
/**
 * Abstract AJAX class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Ajax;

/**
 * AJAX class.
 */
abstract class Ajax {

	/**
	 * The action name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $action;

	/**
	 * The nonce name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $nonce;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action The action name.
	 * @param string $nonce  The nonce name. Default is empty.
	 *
	 * @return void
	 */
	public function __construct( $action, $nonce = '' ) {

		$this->action = sanitize_key( $action );
		$this->nonce  = ! empty( $nonce ) ? sanitize_key( $nonce ) : $this->action;
	}

	/**
	 * Register the AJAX action for the admin area.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_admin() {

		add_action( "wp_ajax_woo_paddle_gateway_{$this->action}", array( $this, 'ajax_callback' ) );
	}

	/**
	 * Verify the AJAX nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function verify_nonce() {

		check_ajax_referer( $this->nonce );
	}

	/**
	 * AJAX callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function ajax_callback();
}
