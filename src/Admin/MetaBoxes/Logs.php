<?php
/**
 * "Logs" meta box.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Admin\MetaBoxes;

use WP_Post;

/**
 * Class Logs.
 */
class Logs extends MetaBox {

	/**
	 * Log key.
	 *
	 * @since 1.0.0
	 */
	const META_KEY = '_woo_paddle_gateway_log';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Register the meta box.
		parent::__construct(
			'logs',
			__( 'Paddle Logs', 'woo-paddle-gateway' )
		);
	}

	/**
	 * Function that fills the box with the desired content.
	 * The function should echo its output.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Order object.
	 *
	 * @return void
	 */
	public function show_meta_box( $post ) {

		// Check if the current user has the required capability to manage WooCommerce.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$order_id = $post instanceof WC_Order ? $post->get_id() : $post->ID;

		// Display the Paddle details template.
		woo_paddle_gateway()->service( 'template_manager' )->echo_template(
			'admin/order/paddle-log.php',
			array(
				'meta' => get_post_meta( $order_id, self::META_KEY, true ),
			)
		);
	}
}
