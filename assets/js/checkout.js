/* global jQuery, Paddle, woo_paddle_gateway_params */

( function ( $, Paddle, l10n ) {
	'use strict';

	/**
	 * MyPreview Woo Paddle.
	 */
	const wooPaddleGateway = {
		/**
		 * Cache.
		 *
		 * @since 1.0.0
		 */
		cache() {
			this.vars = {};
			this.els = {};
			this.vars.wrapper = 'woocommerce-checkout';
			this.els.form = $( `form.woocommerce-checkout` );
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 */
		init() {
			this.cache();
			this.events();
		},

		/**
		 * Events.
		 *
		 * @since 1.0.0
		 */
		events() {
			this.els.form.on( 'submit', this.handleOnSubmit );
		},

		/**
		 * Handle on enable/disable paddle product.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event Event.
		 */
		handleOnSubmit( event ) {
			event.preventDefault();

			// Paddle.Spinner.show();

			$.post(
				l10n.site_url,
				{
					_ajax_nonce: l10n.ajax_nonce,
				},
				function ( response ) {
					console.log( response );
				}
			);
		},
	};

	wooPaddleGateway.init();
} )( jQuery, Paddle, woo_paddle_gateway_params );
