<?php
/**
 * Additional plugin settings fields.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings;

/**
 * Register additional plugin settings fields.
 */
class Fields {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_generate_info_enhanced_html', array( $this, 'info_enhanced_field' ), 10, 3 );
	}

	/**
	 * Display the title enhanced section.
	 *
	 * @since 1.0.0
	 *
	 * @param string $k     The field key.
	 * @param array  $v     The field data.
	 * @param array  $value The field value.
	 *
	 * @return string
	 */
	public function info_enhanced_field( $k, $v, $value ) {

		return woo_paddle_gateway()->service( 'template_manager' )->get_template(
			'admin/fields/info-enhanced.php',
			$value
		);
	}
}
