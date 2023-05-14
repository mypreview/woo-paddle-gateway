<?php
/**
 * The plugin file path class.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

/**
 * Class File.
 */
class File {

	/**
	 * The plugin file path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private string $file;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The plugin file.
	 *
	 * @return void
	 */
	public function __construct( string $file ) {

		$this->file = $file;
	}

	/**
	 * Return the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_file(): string {

		return $this->file;
	}

	/**
	 * Return the plugin path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_path(): string {

		return untrailingslashit( plugin_dir_path( $this->file ) );
	}

	/**
	 * Return the plugin url.
	 *
	 * @param string $path The path to the file.
	 *
	 * @return string
	 */
	public function plugin_url( string $path = '' ): string {

		return plugins_url( $path, $this->file );
	}
}
