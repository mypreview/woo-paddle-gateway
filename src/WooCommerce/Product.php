<?php
/**
 * The WooCommerce product extensions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway/gateway
 */

namespace Woo_Paddle_Gateway\WooCommerce;

/**
 * Class Product.
 */
class Product {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup(): void {

		add_filter( 'product_type_options', array( $this, 'type_options' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'general_settings' ) );
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
	public function type_options( array $options ): array {

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
	public function general_settings(): void {

		wp_enqueue_script( 'woo-paddle-gateway-product' );

		$data = get_post_meta( get_the_ID(), '_woo_paddle_gateway', true );

		echo '<div class="options_group show_if_is_paddle_product">';

		// Display the "Type" dropdown field.
		woocommerce_wp_select(
			array(
				'label'         => __( 'Product type', 'woo-paddle-gateway' ),
				'description'   => __( 'Use the dropdown list to select the Paddle product type.', 'woo-paddle-gateway' ),
				'id'            => '_woo_paddle_gateway[type]',
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
				'label'         => __( 'Product ID', 'woo-paddle-gateway' ),
				'description'   => __( 'Enter the "ID" of the Paddle catalog product.', 'woo-paddle-gateway' ),
				'type'          => 'number',
				'id'            => '_woo_paddle_gateway[catalog]',
				'class'         => 'wc-enhanced-select',
				'style'         => 'width:50%;',
				'wrapper_class' => 'show_if_is_paddle_catalog',
				'value'         => wc_clean( $data['catalog'] ?? '' ),
				'desc_tip'      => true,
				'options'       => array(
					'' => __( 'Select a product', 'woo-paddle-gateway' ),
				) + woo_paddle_gateway()->service( 'api' )->get_product_options(),
			)
		);

		// Display the "Plan ID" field.
		woocommerce_wp_select(
			array(
				'label'         => __( 'Plan ID', 'woo-paddle-gateway' ),
				'description'   => __( 'Enter the "ID" of the Paddle subscription plan.', 'woo-paddle-gateway' ),
				'type'          => 'number',
				'id'            => '_woo_paddle_gateway[subscription]',
				'class'         => 'wc-enhanced-select',
				'style'         => 'width:50%;',
				'wrapper_class' => 'show_if_is_paddle_subscription',
				'value'         => wc_clean( $data['subscription'] ?? '' ),
				'desc_tip'      => true,
				'options'       => array(
					'' => __( 'Select a plan', 'woo-paddle-gateway' ),
				) + woo_paddle_gateway()->service( 'api' )->get_plan_options(),
			)
		);

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
	public function save_settings( object $product ): void {

		$is_enabled = filter_input( INPUT_POST, '_is_paddle_product', FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';
		$data       = wc_string_to_bool( $is_enabled ) ? filter_input( INPUT_POST, '_woo_paddle_gateway', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY ) : array();

		// Save the "Paddle" product type.
		$product->update_meta_data( '_is_paddle_product', $is_enabled );

		// Save the "Paddle" product settings.
		$product->update_meta_data( '_woo_paddle_gateway', $data );
	}
}
