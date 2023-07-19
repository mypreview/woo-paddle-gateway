<?php
/**
 * The implementation of the Pimple service provider interface.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Woo_Paddle_Gateway\Paddle;
use Woo_Paddle_Gateway\Settings;
use Woo_Paddle_Gateway\Util;

/**
 * Class PluginServiceProvider.
 */
class PluginServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @since 1.0.0
	 *
	 * @param Container $pimple Container instance.
	 */
	public function register( $pimple ) {

		// Plugin core.
		$pimple['template_manager'] = fn() => new TemplateManager();

		// Plugin Paddle gateway.
		$pimple['gateway'] = fn() => new Paddle\Gateway();

		// Plugin settings.
		$pimple['settings']         = fn() => new Settings\Settings();
		$pimple['settings_general'] = fn() => new Settings\Sections\General();

		// Plugin utilities.
		$pimple['file'] = fn() => new Util\Endpoints();
	}
}
