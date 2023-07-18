<?php
/**
 * The plugin meta class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Enhancements;

use Woo_Paddle_Gateway\Helper;

/**
 * The plugin meta class.
 */
class Meta {

	/**
	 * The plugin basename.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_basename The plugin basename.
	 *
	 * @return void
	 */
	public function setup( $plugin_basename ) {

		// Set the plugin basename.
		$this->plugin_basename = $plugin_basename;

		add_filter( "plugin_action_links_{$this->plugin_basename}", array( $this, 'action_links' ) );
	}

	/**
	 * Display additional links in the plugin table page.
	 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Plugin table/item action links.
	 *
	 * @return array
	 */
	public function action_links( $links ) {

		$plugin_links   = array();
		$plugin_links[] = sprintf( /* translators: 1: Open anchor tag, 2: Close anchor tag. */
			esc_html_x( '%1$sSettings%2$s', 'plugin settings page', 'woo-paddle-gateway' ),
			sprintf(
				'<a href="%s">',
				esc_url( Helper\Settings::page_uri() )
			),
			'</a>'
		);

		return array_merge( $plugin_links, $links );
	}
}