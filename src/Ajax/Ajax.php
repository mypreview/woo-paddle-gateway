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
	 * The action prefix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $action_prefix;

	/**
	 * The action scope.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $action_scope;

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

		add_action( "{$this->action_scope}_ajax_{$this->action_prefix}{$this->action}", array( $this, 'ajax_callback' ) );
	}

	/**
	 * Register the AJAX action for the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_frontend() {

		add_action( "{$this->action_scope}_ajax_nopriv_{$this->action_prefix}{$this->action}", array( $this, 'ajax_callback' ) );
	}

	/**
	 * Verify the AJAX nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function verify_nonce() {

		// Skip verification if nonce is empty.
		if ( ! $this->nonce ) {
			return;
		}

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

	/**
	 * Default action hook prefix.
	 * Note that given prefix will be suffixed with `_`.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix The action prefix. Default to plugin's slug.
	 *
	 * @return void
	 */
	public function set_action_prefix( $prefix = 'woo_paddle_gateway' ) {

		$this->action_prefix = ! empty( $prefix ) ? rtrim( sanitize_key( $prefix ), '_' ) . '_' : '';
	}

	/**
	 * Default action hook scope.
	 *
	 * @since 1.0.0
	 *
	 * @param string $scope The action scope. Default is `wp`.
	 *
	 * @return void
	 */
	public function set_action_scope( $scope = 'wp' ) {

		$this->action_scope = sanitize_key( $scope );
	}
}
