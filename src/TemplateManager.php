<?php
/**
 * The plugin template manager class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

/**
 * Class TemplateManager.
 */
class TemplateManager {

	/**
	 * Render the template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name The template name.
	 * @param array  $args          The template arguments.
	 *
	 * @return void
	 */
	public function echo_template( $template_name, $args = array() ) {

		// Supports internal WooCommerce caching.
		wc_get_template(
			$template_name,
			$args,
			'',
			trailingslashit( woo_paddle_gateway()->service( 'file' )->plugin_path( 'templates' ) )
		);
	}

	/**
	 * Render the template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name The template name.
	 * @param array  $args          The template arguments.
	 *
	 * @return string
	 */
	public function get_template( $template_name, $args = array() ) {

		// Supports internal WooCommerce caching.
		return wc_get_template_html(
			$template_name,
			$args,
			'',
			trailingslashit( woo_paddle_gateway()->service( 'file' )->plugin_path( 'templates' ) )
		);
	}
}
