/* global jQuery */

( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	const product = {
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
			this.vars.wrapper = 'show_if_is_paddle_product';
			this.vars.productCheckbox = '_is_paddle_product';
			this.vars.productTypes = '_woo_paddle_gateway[type]';
			this.vars.productTypeOptions = [];
			this.els.$wrapper = $( `.${ this.vars.wrapper }` );
			this.els.$productCheckbox = $( `#${ this.vars.productCheckbox }` );
			this.els.$productTypes = $( `[name="${ this.vars.productTypes }"]` );
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
			this.typeOptions();
			this.handleOnToggleProduct();
			this.handleOnChangeProductType();
		},

		/**
		 * Get type options.
		 *
		 * @since 1.0.0
		 */
		typeOptions() {
			this.vars.productTypeOptions = $.map( this.els.$productTypes.find( 'option' ), ( { value } ) => value );
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		bindEvents() {
			this.els.$productCheckbox.on( 'change', this.handleOnToggleProduct );
			this.els.$productTypes.on( 'change', this.handleOnChangeProductType );
		},

		/**
		 * Handle on enable/disable paddle product.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event Event.
		 */
		handleOnToggleProduct( event ) {
			const $this = event ? $( event.target ) : product.els.$productCheckbox;
			const isChecked = $this.prop( 'checked' );

			product.els.$wrapper.toggle( isChecked );
		},

		/**
		 * Handle on a change product type.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event Event.
		 */
		handleOnChangeProductType( event ) {
			const $this = event ? $( event.target ) : product.els.$productTypes;
			const value = $this.val();
			const otherOptions = product.vars.productTypeOptions.filter( ( option ) => option !== value );

			$( `.show_if_is_paddle_${ value }` ).show();

			$.each( otherOptions, ( index, item ) => {
				$( `.show_if_is_paddle_${ item }` ).hide();
			} );
		},
	};

	product.init();
} )( window.wp, jQuery );
