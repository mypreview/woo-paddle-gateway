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

/**
 * Class PluginServiceProvider.
 */
class PluginServiceProvider implements Vendor\Pimple\ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @since 1.0.0
	 *
	 * @param Vendor\Pimple\Container $pimple Container instance.
	 */
	public function register( $pimple ) {

		// Plugin core.
		$pimple['template_manager'] = fn() => new TemplateManager();

		// Plugin Paddle gateway.
		$pimple['gateway']  = fn() => new Paddle\Gateway();
		$pimple['plans']    = fn() => new Paddle\Plans();
		$pimple['products'] = fn() => new Paddle\Products();

		// Plugin settings.
		$pimple['settings']         = fn() => new Settings\Settings();
		$pimple['settings_general'] = fn() => new Settings\Sections\General();
		$pimple['options']          = fn() => new Settings\Options();

		// Plugin utilities.
		$pimple['choices']   = fn() => new Util\Choices();
		$pimple['endpoints'] = fn() => new Util\Endpoints();
	}
}
