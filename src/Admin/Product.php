<?php
/**
 * The WooCommerce product extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Admin;

/**
 * Class Product.
 */
class Product {

	/**
	 * Post meta key.
	 *
	 * @since 1.0.0
	 */
	const META_KEY = '_woo_paddle_gateway';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'product_type_options', array( $this, 'add_type_option' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_settings' ) );
		add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_settings' ) );
	}

	/**
	 * Add the Paddle product type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Default product-type options.
	 *
	 * @return array
	 */
	public function add_type_option( $options ) {

		$options['is_paddle_product'] = array(
			'id'            => '_is_paddle_product',
			'wrapper_class' => 'show_if_simple show_if_variable',
			'label'         => __( 'Paddle', 'woo-paddle-gateway' ),
			'description'   => __( 'Enable this option if this is a Paddle catalog product or subscription plan.', 'woo-paddle-gateway' ),
			'value'         => wc_clean( get_post_meta( get_the_ID(), '_is_paddle_product', true ) ),
		);

		return $options;
	}

	/**
	 * Add the Paddle product settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_settings() {

		// Enqueue the product settings script.
		wp_enqueue_script( 'woo-paddle-gateway-product' );

		$data = get_post_meta( get_the_ID(), self::META_KEY, true );

		// Start outputting the settings fields.
		echo '<div class="options_group show_if_is_paddle_product">';

		// Display the "Type" dropdown field.
		woocommerce_wp_select(
			array(
				'label'         => __( 'Product type', 'woo-paddle-gateway' ),
				'description'   => __( 'Use the dropdown list to select the Paddle product type.', 'woo-paddle-gateway' ),
				'id'            => self::META_KEY . '[type]',
				'class'         => 'wc-enhanced-select',
				'style'         => 'width:50%;',
				'value'         => wc_clean( $data['type'] ?? 'subscription' ),
				'desc_tip'      => true,
				'options'       => array(
					'catalog'      => __( 'Catalog', 'woo-paddle-gateway' ),
					'subscription' => __( 'Subscription', 'woo-paddle-gateway' ),
				),
			)
		);

		// Display the "Product ID" field.
		woocommerce_wp_select(
			array(
				'label'         => __( 'Catalog product', 'woo-paddle-gateway' ),
				'description'   => __( 'Enter the "ID" of the Paddle catalog product.', 'woo-paddle-gateway' ),
				'type'          => 'number',
				'id'            => self::META_KEY . '[catalog]',
				'class'         => 'wc-enhanced-select',
				'style'         => 'width:50%;',
				'wrapper_class' => 'show_if_is_paddle_catalog',
				'value'         => wc_clean( $data['catalog'] ?? '' ),
				'desc_tip'      => true,
				'options'       => array(
					'' => __( 'Select a product', 'woo-paddle-gateway' ),
				) + woo_paddle_gateway()->service( 'choices' )->get( 'catalog_products' ),
			)
		);

		// Display the "Plan ID" field.
		woocommerce_wp_select(
			array(
				'label'         => __( 'Subscription plan', 'woo-paddle-gateway' ),
				'description'   => __( 'Enter the "ID" of the Paddle subscription plan.', 'woo-paddle-gateway' ),
				'type'          => 'number',
				'id'            => self::META_KEY . '[subscription]',
				'class'         => 'wc-enhanced-select',
				'style'         => 'width:50%;',
				'wrapper_class' => 'show_if_is_paddle_subscription',
				'value'         => wc_clean( $data['subscription'] ?? '' ),
				'desc_tip'      => true,
				'options'       => array(
					'' => __( 'Select a plan', 'woo-paddle-gateway' ),
				) + woo_paddle_gateway()->service( 'choices' )->get( 'subscription_plans' ),
			)
		);

		// End the settings fields.
		echo '</div>';
	}

	/**
	 * Fires after a product has been updated or published.
	 *
	 * @since 1.0.0
	 *
	 * @param object $product Product object.
	 *
	 * @return void
	 */
	public function save_settings( $product ) {

		$is_enabled = filter_input( INPUT_POST, '_is_paddle_product', FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';
		$data       = wc_string_to_bool( $is_enabled ) ? filter_input( INPUT_POST, self::META_KEY, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY ) : array();

		// Save the "Paddle" product type.
		$product->update_meta_data( '_is_paddle_product', $is_enabled );

		// Save the "Paddle" product settings.
		$product->update_meta_data( self::META_KEY, $data );
	}
}
