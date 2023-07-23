/* eslint-disable camelcase */
/* global jQuery, woo_paddle_gateway_params */

( function ( $, l10n ) {
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
			this.vars.messages = '.woocommerce-error, .woocommerce-message';
			this.els.$form = $( 'form.woocommerce-checkout' );
			this.els.$orderReview = $( 'form#order_review' );
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
			this.bindEvents();
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
			this.els.$orderReview.on( 'submit', this.handleOnSubmit );
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
			event.stopImmediatePropagation();
			event.stopPropagation();

			const $form = $( this );

			if ( $form.is( '.processing' ) ) {
				return false;
			}

			$.ajax( {
				type: 'POST',
				async: true,
				dataType: 'json',
				url: l10n.checkout_uri,
				data: $form.serialize(),
				beforeSend() {
					$form.block( {
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6,
						},
					} );
				},
				success( response ) {
					$( script.vars.messages ).remove();

					try {
						if ( 'success' === response.result ) {
							if (
								-1 === response.generate_pay_link.indexOf( 'https://' ) ||
								-1 === response.generate_pay_link.indexOf( 'http://' )
							) {
								window.location = response.generate_pay_link;
							} else {
								window.location = decodeURI( response.generate_pay_link );
							}
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

						// Remove the UI blocker.
						$form.unblock();

						// Remove old errors.
						$( script.vars.messages ).remove();

						// Add new errors
						if ( response.messages ) {
							$form.prepend( response.messages );
						}

						// Cancel processing
						$form.removeClass( 'processing' ).unblock();

						// Lose focus for all fields
						$form.find( '.input-text, select' ).blur();

						// Scroll to the notices.
						script.scrollToNotices();
					}
				},
			} );
		},

		/**
		 * Scroll to notices.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		scrollToNotices() {
			const $selector = $( this.vars.messages );

			if ( ! $selector.length ) {
				return;
			}

			$( 'html, body' ).animate(
				{
					scrollTop: $selector.offset().top - 100,
				},
				1000
			);
		},
	};

	script.init();
} )( jQuery, woo_paddle_gateway_params );
