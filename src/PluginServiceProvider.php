<?php
/**
 * The implementation of the Pimple service provider interface
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway;

use Woo_Paddle_Gateway\Api\Api;
use Woo_Paddle_Gateway\Gateway\Paddle;
use Woo_Paddle_Gateway\Gateway\Manager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
	 * @param Container $pimple Container instance.
	 */
	public function register( Container $pimple ): void {

		// Gatewat services.
		$pimple['paddle']         = fn() => new Paddle();
		$pimple['paddle_manager'] = fn() => new Manager();

		// Api services.
		$pimple['api'] = fn() => new Api();
	}
}
