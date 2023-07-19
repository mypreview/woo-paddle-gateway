/* eslint-disable camelcase */
/* global jQuery, woo_paddle_gateway_admin_params */

( function ( $, Paddle, l10n ) {
	'use strict';

	const script = {
		/**
		 * Cache.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		cache() {
			this.els = {};
			this.vars = {};
			this.vars.wc = '.woocommerce';
			this.vars.errorContainer = '.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout';
			this.els.$form = $( 'form.woocommerce-checkout' );
			this.els.$gateway = $( '#payment_method_woo-paddle-gateway' );
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		init() {
			this.cache();
			this.setup();
			this.bindEvents();
		},

		setup() {
			// Set Paddle environment.
			if ( l10n.is_sandbox ) {
				Paddle.Environment.set( 'sandbox' );
			}

			if ( ! l10n.vendor_id ) {
				return;
			}

			// Set Paddle vendor ID.
			Paddle.Setup( {
				vendor: Number( l10n.vendor_id ),
			} );
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		bindEvents() {
			this.els.$form.on( 'submit', this.handleOnSubmit );
		},

		/**
		 * Handle on submit.
		 *
		 * @since 1.0.0
		 *
		 * @param {Event} event Event.
		 *
		 * @return {void}
		 */
		handleOnSubmit( event ) {
			if ( ! script.els.$gateway.is( ':checked' ) ) {
				return;
			}

			event.preventDefault();

			const $form = $( this );

			if ( $form.is( '.processing' ) ) {
				return false;
			}

			Paddle.Spinner.show();

			$.ajax( {
				type: 'POST',
				async: true,
				dataType: 'json',
				url: l10n.checkout_uri,
				data: script.els.$form.serialize(),
				success( response ) {
					// Unblock the form.
					$form.unblock();
					$( script.vars.errorContainer ).remove();

					try {
						if ( 'success' === response.result ) {
							Paddle.Checkout.open( {
								email: response.customer_email,
								country: response.customer_country,
								override: response.generate_pay_link,
								disableLogout: true,
								method: 'overlay',
								displayModeTheme: 'light',
							} );
						} else if ( 'failure' === response.result ) {
							throw 'Result failure';
						} else {
							throw 'Invalid response';
						}
					} catch ( err ) {
						// Reload page
						if ( true === response.reload ) {
							window.location.reload();
							return;
						}

						// Trigger update in case we need a fresh nonce
						if ( true === response.refresh ) {
							$( document.body ).trigger( 'update_checkout' );
						}

						// Remove old errors.
						$( script.vars.errorContainer ).remove();

						// Add new errors
						if ( response.messages ) {
							$form.prepend( response.messages );
						}

						// Cancel processing
						$form.removeClass( 'processing' ).unblock();

						// Lose focus for all fields
						$form.find( '.input-text, select' ).blur();

						// Hide spinner.
						Paddle.Spinner.hide();
					}
				},
			} );
		},
	};

	script.init();
} )( jQuery, window.Paddle, woo_paddle_gateway_admin_params );
