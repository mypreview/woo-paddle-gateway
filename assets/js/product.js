/* global jQuery */

( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

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
			this.vars.wrapper = 'show_if_is_paddle_product';
			this.vars.isPaddleProduct = '_is_paddle_product';
			this.vars.productTypes = '_woo_paddle_gateway[type]';
			this.vars.productTypeOptions = [];
			this.els.wrapper = $( `.${ this.vars.wrapper }` );
			this.els.isPaddleProduct = $( `#${ this.vars.isPaddleProduct }` );
			this.els.productTypes = $( `[name="${ this.vars.productTypes }"]` );
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 */
		init() {
			this.cache();
			this.events();
			this.typeOptions();
			this.handleOnChangePaddleProduct();
			this.handleOnChangePaddleProductType();
		},

		/**
		 * Events.
		 *
		 * @since 1.0.0
		 */
		events() {
			this.els.isPaddleProduct.on( 'change', this.handleOnChangePaddleProduct );
			this.els.productTypes.on( 'change', this.handleOnChangePaddleProductType );
		},

		/**
		 * Get type options.
		 *
		 * @since 1.0.0
		 */
		typeOptions() {
			this.vars.productTypeOptions = $.map( this.els.productTypes.find( 'option' ), ( { value } ) => value );
		},

		/**
		 * Handle on enable/disable paddle product.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event Event.
		 */
		handleOnChangePaddleProduct( event ) {
			const $this = event ? $( event.target ) : wooPaddleGateway.els.isPaddleProduct;
			const isChecked = $this.prop( 'checked' );

			wooPaddleGateway.els.wrapper.toggle( isChecked );
		},

		/**
		 * Handle on a change product type.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event Event.
		 */
		handleOnChangePaddleProductType( event ) {
			const $this = event ? $( event.target ) : wooPaddleGateway.els.productTypes;
			const value = $this.val();
			const otherOptions = wooPaddleGateway.vars.productTypeOptions.filter( ( option ) => option !== value );

			$( `.show_if_is_paddle_${ value }` ).show();

			$.each( otherOptions, ( index, item ) => {
				$( `.show_if_is_paddle_${ item }` ).hide();
			} );
		},
	};

	wooPaddleGateway.init();
} )( window.wp, jQuery );
