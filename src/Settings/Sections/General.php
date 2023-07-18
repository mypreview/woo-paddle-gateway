<?php
/**
 * The plugin settings fields.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings\Sections;

use Woo_Paddle_Gateway\WooCommerce;

/**
 * Class Settings fields.
 */
class General extends Section {

	/**
	 * Retrieve the settings fields for the general (default) settings tab.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_fields() {

		return array(
			'enabled' => array(
				'title'   => _x( 'Status', 'settings field title', 'woo-paddle-gateway' ),
				'label'   => _x( 'Enable the gateway.', 'settings field label', 'woo-paddle-gateway' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'title' => array(
				'title'       => _x( 'Title', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'This controls the title which the user sees during checkout.', 'settings field description', 'woo-paddle-gateway' ),
				'default'     => __( 'Paddle', 'woo-paddle-gateway' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
		);
	}
}
