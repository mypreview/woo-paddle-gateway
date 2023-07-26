<?php
/**
 * The plugin settings fields.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Settings\Sections;

use Woo_Paddle_Gateway\WooCommerce;

/**
 * Class Settings fields.
 */
class General extends Section {

	/**
	 * Retrieve the settings fields for the general (default) settings tab.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_fields() {

		return array(
			'enabled' => array(
				'title'   => _x( 'Status', 'settings field title', 'woo-paddle-gateway' ),
				'label'   => _x( 'Enable the gateway.', 'settings field label', 'woo-paddle-gateway' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'title' => array(
				'title'       => _x( 'Title', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'This controls the title which the user sees during checkout.', 'settings field description', 'woo-paddle-gateway' ),
				'default'     => _x( 'Paddle', 'settings field default', 'woo-paddle-gateway' ),
				'type'        => 'text',
				'desc_tip'    => true,
			),
			'description' => array(
				'title'       => _x( 'Description', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'This controls the description which the user sees during checkout.', 'settings field description', 'woo-paddle-gateway' ),
				'default'     => _x( 'Pay securely using your credit card, debit card, iDEAL, Google Pay, Apple Pay, AliPay, and PayPal.', 'settings field default', 'woo-paddle-gateway' ),
				'type'        => 'textarea',
				'css'         => 'max-width:400px;',
				'desc_tip'    => true,
			),
			'is_readonly' => array(
				'title'   => _x( 'Protect Fields', 'settings field title', 'woo-paddle-gateway' ),
				'label'   => _x( 'Disable the ability to edit any gateway credential fields.', 'settings field description', 'woo-paddle-gateway' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'sandbox_mode' => array(
				'title'       => _x( 'Sandbox Mode (Test)', 'settings field title', 'woo-paddle-gateway' ),
				'label'       => '&#9888; ' . _x( 'Warning! Check this option to prevent Paddle from processing live transactions.', 'settings field description', 'woo-paddle-gateway' ),
				'description' => _x( 'Place the payment gateway in test mode using test account credentials.', 'settings field description', 'woo-paddle-gateway' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'live_vendor_verify' => array(
				'title'             => _x( 'Connection', 'settings field title', 'woo-paddle-gateway' ),
				'label'             => '…',
				'type'              => 'checkbox',
				'default'           => 'no',
				'custom_attributes' => array(
					'data-label' => $this->connection_statuses(),
				),
				'desc_tip'          => true,
			),
			'live_vendor_id' => array(
				'title'       => _x( 'Vendor ID', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'Enter your Paddle vendor ID.', 'settings field description', 'woo-paddle-gateway' ),
				'type'        => 'number',
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_vendor_auth_code' => array(
				'title'       => _x( 'Auth Code', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'Enter your Paddle Auth Code. This token will be used to interact with the API.', 'settings field description', 'woo-paddle-gateway' ),
				'type'        => 'textarea',
				'css'         => 'max-width:400px;',
				'default'     => '',
				'desc_tip'    => true,
			),
			'live_vendor_public_key' => array(
				'title'             => _x( 'Public Key', 'settings field title', 'woo-paddle-gateway' ),
				'description'       => _x( 'This key will be used to encrypt the payment data.', 'settings field description', 'woo-paddle-gateway' ),
				'placeholder'       => _x( 'The public key will be automatically generated. Do not change this value.', 'settings field placeholder', 'woo-paddle-gateway' ),
				'type'              => 'textarea',
				'class'             => 'disabled',
				'css'               => 'max-width:400px;',
				'default'           => '',
				'custom_attributes' => array(
					'readonly' => 'true',
					'tabindex' => '-1',
				),
				'desc_tip'          => true,
			),
			'test_vendor_verify' => array(
				'title'             => _x( 'Connection', 'settings field title', 'woo-paddle-gateway' ),
				'label'             => '…',
				'type'              => 'checkbox',
				'default'           => 'no',
				'custom_attributes' => array(
					'data-label' => $this->connection_statuses(),
				),
				'desc_tip'          => true,
			),
			'test_vendor_id' => array(
				'title'       => _x( 'Vendor ID', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'Enter your Paddle vendor ID.', 'settings field description', 'woo-paddle-gateway' ),
				'type'        => 'number',
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_vendor_auth_code' => array(
				'title'       => _x( 'Auth Code', 'settings field title', 'woo-paddle-gateway' ),
				'description' => _x( 'Enter your Paddle Auth Code. This token will be used to interact with the API.', 'settings field description', 'woo-paddle-gateway' ),
				'type'        => 'textarea',
				'css'         => 'max-width:400px;',
				'default'     => '',
				'desc_tip'    => true,
			),
			'test_vendor_public_key' => array(
				'title'             => _x( 'Public Key', 'settings field title', 'woo-paddle-gateway' ),
				'description'       => _x( 'This key will be used to encrypt the payment data.', 'settings field description', 'woo-paddle-gateway' ),
				'placeholder'       => _x( 'The public key will be automatically generated. Do not change this value.', 'settings field placeholder', 'woo-paddle-gateway' ),
				'type'              => 'textarea',
				'class'             => 'disabled',
				'css'               => 'max-width:400px;',
				'default'           => '',
				'custom_attributes' => array(
					'readonly' => 'true',
					'tabindex' => '-1',
				),
				'desc_tip'          => true,
			),
			'refresh_responses' => array(
				'title'       => _x( 'Refresh Responses', 'settings field title', 'woo-paddle-gateway' ),
				'description' => sprintf( /* translators: %1$s and %2$s opening and closing anchor tags respectively. */
					_x( '%1$sFetch the latest responses from the Paddle API%2$s', 'settings field description', 'woo-paddle-gateway' ),
					'<button class="button button-link" id="refresh-responses">',
					'</button>'
				),
				'class'       => 'refresh-responses',
				'type'        => 'woo_paddle_gateway_info_enhanced',
			),
		);
	}

	/**
	 * Connection status labels.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function connection_statuses() {

		return wp_json_encode(
			array(
				'1' => __( 'The API connection has been successfully established.', 'woo-paddle-gateway' ),
				'0' => __( 'Please check your network connection and verify the API credentials.', 'woo-paddle-gateway' ),
			)
		);
	}
}
