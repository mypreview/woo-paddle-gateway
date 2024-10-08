<?php
/**
 * The plugin settings sections.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings\Sections;

/**
 * Class Settings sections.
 */
abstract class Section {

	/**
	 * Retrieve the settings fields for the section.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	abstract public function get_fields();

	/**
	 * Compile a list of the available field keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_field_keys() {

		static $keys = array();

		if ( empty( $keys ) ) {
			$keys = array_filter(
				array_keys( $this->get_fields() ),
				static fn( $key) => mb_strpos( $key, 'section_' ) !== 0
			);
		}

		return $keys;
	}
}
