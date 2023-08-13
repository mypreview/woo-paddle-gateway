<?php
/**
 * The Template for displaying the subscription history for Paddle subscriptions in the order details page.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// If the renewal log is empty, bail.
if ( empty( $args['meta'] ) ) {
	return;
}

?>
<h2 class="woocommerce-order-details__title">
	<?php esc_html_e( 'Subscription history', 'woo-paddle-gateway' ); ?>
</h2>

<table class="woocommerce-table woocommerce-table--order-details shop_table order_details woo-paddle-gateway-paddle-subscription-history">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Date', 'woo-paddle-gateway' ); ?></th>
			<th><?php esc_html_e( 'Total', 'woo-paddle-gateway' ); ?></th>
			<th><?php esc_html_e( 'Status', 'woo-paddle-gateway' ); ?></th>
			<th><?php esc_html_e( 'Receipt URL', 'woo-paddle-gateway' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		foreach ( $args['meta'] as $renewal ) :
			?>
			<tr class="woocommerce-table__line-item order_item">
				<td class="woocommerce-table__subscription-renewal-date" data-title="<?php esc_attr_e( 'Date', 'woo-paddle-gateway' ); ?>">
					<?php if ( ! empty( $renewal['date'] ) ) : ?>
						<?php echo esc_html( date_i18n( wc_date_format(), strtotime( $renewal['date'] ) ) ); ?>
					<?php endif; ?>
				</td>
				<td class="woocommerce-table__subscription-renewal-total" data-title="<?php esc_attr_e( 'Total', 'woo-paddle-gateway' ); ?>">
					<?php if ( ! empty( $renewal['total'] ) ) : ?>
						<?php echo wc_clean( wc_price( $renewal['total'] ) ); ?>
					<?php endif; ?>
				</td>
				<td class="woocommerce-table__subscription-renewal-status" data-title="<?php esc_attr_e( 'Status', 'woo-paddle-gateway' ); ?>">
					<?php if ( ! empty( $renewal['status'] ) ) : ?>
					<mark class="order-status status-<?php echo esc_attr( $renewal['status'] ); ?>"><span><?php echo wc_clean( ucfirst( $renewal['status'] ) ); ?></span></mark>
					<?php endif; ?>
				</td>
				<td class="woocommerce-table__subscription-renewal-receipt-url" data-title="<?php esc_attr_e( 'Receipt URL', 'woo-paddle-gateway' ); ?>">
					<?php if ( ! empty( $renewal['receipt_url'] ) ) : ?>
					<a href="<?php echo esc_url( $renewal['receipt_url'] ); ?>" class="woocommerce-button button view" rel="noopener noreferrer" target="_blank"><?php esc_html_e( 'View receipt', 'woo-paddle-gateway' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<?php
		endforeach;
        // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		?>
	</tbody>
</table>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
