<?php
/**
 * The Template for displaying Paddle subscription details.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// If the order is not a subscription, bail.
if ( empty( $args['meta'] ) || empty( $args['meta']['subscription_id'] ) ) {
	return;
}

?>
<h2 class="woocommerce-order-details__title">
	<?php esc_html_e( 'Subscription details', 'woo-paddle-gateway' ); ?>
</h2>

<table class="woocommerce-table woocommerce-table--order-details shop_table order_details woo-paddle-gateway-paddle-details">
	<tbody>
		<?php if ( ! empty( $args['meta']['status'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-status">
			<th scope="row"><?php esc_html_e( 'Status:', 'woo-paddle-gateway' ); ?></th>
			<td><?php echo esc_html( ucwords( str_replace( '_', ' ', $args['meta']['status'] ) ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $args['meta']['next_bill_date'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-next-bill-date">
			<th scope="row"><?php esc_html_e( 'Next billing date:', 'woo-paddle-gateway' ); ?></th>
			<td><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $args['meta']['next_bill_date'] ) ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $args['meta']['subscription_id'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-subscription-id">
			<th scope="row"><?php esc_html_e( 'Subscription ID:', 'woo-paddle-gateway' ); ?></th>
			<td><?php echo esc_html( $args['meta']['subscription_id'] ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $args['meta']['cancellation_effective_date'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-cancellation-effective-date">
			<th scope="row"><?php esc_html_e( 'Cancellation effective date:', 'woo-paddle-gateway' ); ?></th>
			<td><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $args['meta']['cancellation_effective_date'] ) ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $args['meta']['next_payment_amount'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-next-payment-amount">
			<th scope="row"><?php esc_html_e( 'Next payment amount:', 'woo-paddle-gateway' ); ?></th>
			<td><?php echo wc_clean( wc_price( $args['meta']['next_payment_amount'] ) ); ?></td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $args['meta']['receipt_url'] ) ) : ?>
		<tr class="woo-paddle-gateway-paddle-details-receipt-url">
			<th scope="row"><?php esc_html_e( 'Receipt URL:', 'woo-paddle-gateway' ); ?></th>
			<td>
				<a href="<?php echo esc_url( $args['meta']['receipt_url'] ); ?>" class="woocommerce-button button view" rel="noopener noreferrer" target="_blank">
					<?php esc_html_e( 'View receipt', 'woo-paddle-gateway' ); ?>
				</a>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<p class="order-again woo-paddle-gateway-paddle-details-actions">
	<?php if ( ! empty( $args['meta']['update_url'] ) && empty( $args['meta']['cancellation_effective_date'] ) ) : ?>
	<a href="<?php echo esc_url( $args['meta']['update_url'] ); ?>" rel="noopener noreferrer" class="woocommerce-button button" target="_blank">
		<?php esc_html_e( 'Edit Billing Info', 'woo-paddle-gateway' ); ?>
	</a>
	<?php endif; ?>

	<?php if ( ! empty( $args['meta']['cancel_url'] ) && empty( $args['meta']['cancellation_effective_date'] ) ) : ?>
	<a href="<?php echo esc_url( $args['meta']['cancel_url'] ); ?>" rel="noopener noreferrer" class="woocommerce-button button" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to cancel your subscription?', 'woo-paddle-gateway' ); ?>');">
		<?php esc_html_e( 'Cancel subscription', 'woo-paddle-gateway' ); ?>
	</a>
	<?php endif; ?>
</p>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
