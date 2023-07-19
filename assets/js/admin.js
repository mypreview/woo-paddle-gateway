/* global jQuery */

( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	const admin = {
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
			this.vars.protect = 'woocommerce_woo-paddle-gateway_is_readonly';
			this.vars.sandbox = 'woocommerce_woo-paddle-gateway_sandbox_mode';
			this.vars.verify = '[for*="_vendor_verify"]';
			this.els.$protect = $( `#${ this.vars.protect }` );
			this.els.$sandbox = $( `#${ this.vars.sandbox }` );
			this.els.$verify = $( `.forminp ${ this.vars.verify }` );
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
			this.showConnectionStatus();
			this.handleOnToggleProtect();
			this.handleOnToggleSandbox();
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		bindEvents() {
			this.els.$protect.on( 'click', this.handleOnToggleProtect );
			this.els.$sandbox.on( 'click', this.handleOnToggleSandbox );
		},

		/**
		 * Handle toggle read-only mode.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		handleOnToggleProtect() {
			// Whether the checkbox is checked or not.
			const isChecked = admin.els.$protect.is( ':checked' );

			admin.alterFields( '[name*="_vendor_"]:not(.disabled)', { readonly: isChecked ? 'readonly' : null } );
		},

		/**
		 * Handle toggle test mode.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		handleOnToggleSandbox() {
			// Whether the checkbox is checked or not.
			const isChecked = admin.els.$sandbox.is( ':checked' );
			const visible = 'table-row';
			const hidden = 'none';

			admin.alterFields( '[name*="_test_"]', { style: `display: ${ isChecked ? visible : hidden }` }, 'tr' );
			admin.alterFields( '[name*="_live_"]', { style: `display: ${ isChecked ? hidden : visible }` }, 'tr' );
		},

		/**
		 * Determine the status of the connection.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		showConnectionStatus() {
			// Bail if no verify labels found.
			if ( ! this.els.$verify.length ) {
				return;
			}

			// Iterate over verify labels and update text.
			this.els.$verify.each( ( index, element ) => {
				const $this = $( element );
				const $input = $this.find( 'input' );
				const isChecked = $input.is( ':checked' ) ? '1' : '0';
				const labels = $input.data( 'label' );
				const replacement = labels[ isChecked ];

				$this
					.contents()
					.filter( ( index, el ) => el.nodeType === 3 )
					.each( ( index, el ) => {
						el.nodeValue = el.nodeValue.replace( '…', replacement );
					} );
			} );
		},

		/**
		 * Alter fields attributes.
		 *
		 * @since 1.0.0
		 *
		 * @param {string}      selector Selector.
		 * @param {Object}      attrs    Attributes.
		 * @param {null|string} parent   Parent selector.
		 *
		 * @return {void}
		 */
		alterFields( selector, attrs, parent ) {
			const $fields = $( selector );

			// Bail if no fields found.
			if ( ! $fields.length ) {
				return;
			}

			$fields.each( ( index, element ) => {
				let $selector = $( element );

				if ( parent ) {
					$selector = $selector.closest( parent );
				}

				$selector.attr( attrs );
			} );
		},
	};

	admin.init();
} )( window.wp, jQuery );
