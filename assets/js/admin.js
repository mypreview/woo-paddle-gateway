/* global jQuery */

( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	/**
	 * Woo Paddle Gateway.
	 *
	 * @since 1.0.0
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
			this.vars.protect = 'woocommerce_woo-paddle-gateway_is_readonly';
			this.vars.testMode = 'woocommerce_woo-paddle-gateway_sandbox_mode';
			this.els.protect = $( `#${ this.vars.protect }` );
			this.els.testMode = $( `#${ this.vars.testMode }` );
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 */
		init() {
			this.cache();
			this.events();
			this.handleToggleProtect();
			this.handleToggleTestMode();
		},

		/**
		 * Events.
		 *
		 * @since 1.0.0
		 */
		events() {
			this.els.protect.on( 'click', this.handleToggleProtect );
			this.els.testMode.on( 'click', this.handleToggleTestMode );
		},

		/**
		 * Handle toggle read-only mode.
		 *
		 * @since 1.0.0
		 */
		handleToggleProtect() {
			// Whether the checkbox is checked or not.
			const isChecked = wooPaddleGateway.els.protect.is( ':checked' );

			wooPaddleGateway.alterFields( 'vendor', { readonly: isChecked ? 'readonly' : null }, null );
		},

		/**
		 * Handle toggle test mode.
		 *
		 * @since 1.0.0
		 */
		handleToggleTestMode() {
			// Whether the checkbox is checked or not.
			const isChecked = wooPaddleGateway.els.testMode.is( ':checked' );
			const visibleDisplay = 'table-row';
			const hiddenDisplay = 'none';

			wooPaddleGateway.alterFields(
				'test',
				{ style: `display: ${ isChecked ? visibleDisplay : hiddenDisplay }` },
				'tr'
			);
			wooPaddleGateway.alterFields(
				'live',
				{ style: `display: ${ isChecked ? hiddenDisplay : visibleDisplay }` },
				'tr'
			);
		},

		/**
		 * Alter fields attributes.
		 *
		 * @since 1.0.0
		 *
		 * @param {string}      id     Field ID.
		 * @param {Object}      attrs  Attributes.
		 * @param {null|string} parent Parent selector.
		 */
		alterFields( id, attrs, parent ) {
			const $fields = $( `[name*="_${ id }_"]` );

			// Bail if no fields found.
			if ( ! $fields.length ) {
				return;
			}

			$fields.each( function () {
				let $selector = $( this );

				if ( parent ) {
					$selector = $selector.closest( parent );
				}

				$selector.attr( attrs );
			} );
		},
	};

	wooPaddleGateway.init();
} )( window.wp, jQuery );
