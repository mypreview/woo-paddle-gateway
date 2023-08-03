<?php
/**
 * The Template for displaying the order thank you page.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// If the order is not valid, bail.
if ( empty( $args['url'] ) ) {
	return;
}

?>

<?php /* incorrect CSS class added here so it adopts styling we want. */ ?>
<p class="form-field form-field-wide wc-order-received wc-order-status">
	<label for="order_received">
		<?php esc_html_e( 'Order receipt:', 'woo-paddle-gateway' ); ?>
		<?php
		printf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url( $args['url'] ),
			esc_html__( 'View thank you page &rarr;', 'woo-paddle-gateway' )
		);
		?>
	</label>
</p>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
