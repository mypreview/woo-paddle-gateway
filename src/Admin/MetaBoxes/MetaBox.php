<?php
/**
 * Abstract Meta Box.
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
 * Class MetaBox.
 */
abstract class MetaBox {

	/**
	 * Register meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id       Meta box ID.
	 * @param string $title    Meta box title.
	 * @param string $context  Meta box context.
	 * @param string $priority Meta box priority.
	 *
	 * @return void
	 */
	public function __construct( $id, $title, $context = 'normal', $priority = 'low' ) {

		add_meta_box(
			woo_paddle_gateway()->get_slug() . "-{$id}-data",
			$title,
			array( $this, 'show_meta_box' ),
			null,
			$context,
			$priority
		);
	}

	/**
	 * Function that fills the box with the desired content.
	 * The function should echo its output.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	abstract public function show_meta_box( $post );
}
