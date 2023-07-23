<?php
/**
 * Plugin options class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings;

use Woo_Paddle_Gateway\Installer;

/**
 * Options class.
 */
class Options {

	/**
	 * The plugin options name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_NAME = 'woo_paddle_gateway_options';

	/**
	 * The activation timestamp option name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const TIMESTAMP_OPTION_NAME = 'woo_paddle_gateway_activation_timestamp';

	/**
	 * Get the plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     The option key.
	 * @param mixed  $default The default value.
	 *
	 * @return string|array
	 */
	public function get( $key = '', $default = null ) {

		$options = (array) get_option( self::OPTION_NAME, $this->get_defaults() );

		// If the key is empty, then return the whole options.
		if ( empty( $key ) ) {
			return $options;
		}

		// Look for the nested options' identifier.
		if ( false === strpos( $key, '::' ) ) {
			return $options[ $key ] ?? $default;
		}

		$key_stack = explode( '::', $key );
		$subkey    = array_shift( $key_stack );

		// Return the default value if the subkey is not set.
		if ( ! isset( $options[ $subkey ] ) ) {
			return $default;
		}

		$value = $options[ $subkey ];

		// Return the default value if the subkey is not an array.
		if ( ! count( $key_stack ) ) {
			return $value;
		}

		foreach ( $key_stack as $subkey ) {
			if ( is_array( $value ) && isset( $value[ $subkey ] ) ) {
				$value = $value[ $subkey ];
			} else {
				$value = $default;
				break;
			}
		}

		return $value;
	}

	/**
	 * Get the plugin options defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_defaults() {

		return array(
			'products_live' => array(),
			'plans_live'    => array(),
			'products_test' => array(),
			'plans_test'    => array(),
		);
	}

	/**
	 * Update the plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $value The new options value.
	 * @param string $key   The option key.
	 *
	 * @return void
	 */
	public function update( $value, $key = null ) {

		// Bail early if the value is not an array or empty.
		if ( ! is_array( $value ) ) {
			return;
		}

		// If the key is empty, then update the whole options.
		if ( empty( $key ) ) {
			update_option( self::OPTION_NAME, $value );
			return;
		}

		// If the key is not empty, then update the specific option.
		$value = $this->set_nested_value( $this->get(), $key, $value );

		// Look for the nested options identifier.
		if ( false === strpos( $key, '::' ) ) {
			update_option( self::OPTION_NAME, $value );
			return;
		}

		$key_stack = explode( '::', $key );
		$subkey    = array_shift( $key_stack );
		$value     = $this->set_nested_value( $this->get(), $subkey, $value );

		update_option( self::OPTION_NAME, $value );
	}

	/**
	 * Unset the nested plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key    The option key.
	 * @param string $subkey The option subkey.
	 *
	 * @return void
	 */
	public function unset( $key, $subkey ) {

		// If the key is empty, then update the whole options.
		if ( empty( $key ) || empty( $subkey ) ) {
			return;
		}

		$value = $this->get( $key, array() );

		// Bail early if the value is not an array or empty.
		if ( ! isset( $value[ $subkey ] ) ) {
			return;
		}

		unset( $value[ $subkey ] );

		$this->update( $value, $key );
	}

	/**
	 * Get the plugin activation timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_usage_timestamp() {

		return (int) get_site_option( self::TIMESTAMP_OPTION_NAME, 0 );
	}

	/**
	 * Store a timestamp option on plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_usage_timestamp() {

		$activation_timestamp = get_site_option( self::TIMESTAMP_OPTION_NAME );

		// Store the activation timestamp if it doesn't exist.
		if ( ! $activation_timestamp ) {
			add_site_option( self::TIMESTAMP_OPTION_NAME, time() );
		}
	}

	/**
	 * Set a value in a nested array by specifying the entire key hierarchy with '::' as separator.
	 *
	 * E.g. for [ 'foo' => [ 'bar' => [ 'fizz' => 'buzz' ] ] ] setting the value 'new_value' for key 'foo::bar::fizz'
	 * would result in [ 'foo' => [ 'bar' => [ 'fizz' => 'new_value' ] ] ].
	 *
	 * @param array  $array The array to set the value in.
	 * @param string $key   The complete key hierarchy, using '::' as separator.
	 * @param mixed  $value The value to set.
	 *
	 * @return array
	 */
	private function set_nested_value( $array, $key, $value ) {

		$key_stack = explode( '::', $key );
		$subkey    = array_shift( $key_stack );

		if ( count( $key_stack ) ) {
			// If the subkey is not set or not an array, set it to an empty array.
			if ( ! isset( $array[ $subkey ] ) || ! is_array( $array[ $subkey ] ) ) {
				$array[ $subkey ] = array();
			}

			// Recursively set the value in the subkey.
			return $this->set_nested_value( $array[ $subkey ], implode( '::', $key_stack ), $value );
		}

		$array[ $subkey ] = $value;

		return $array;
	}
}
