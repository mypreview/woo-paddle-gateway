<?php
/**
 * The Template for displaying Paddle payment details in the order admin.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// If the renewal log is empty, bail.
if ( empty( $args['meta'] ) ) {
	esc_html_e( 'No Paddle subscription renewal found.', 'woo-paddle-gateway' );
	return;
}

?>
<div class="woo-paddle-gateway-paddle-renewal-history">
	<?php
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	foreach ( $args['meta'] as $renewal ) :
		?>
		<details>
			<summary>
				<?php
				printf( /* translators: %s: Event name. */
					esc_html__( 'Subscription renewal: "%s"', 'woo-paddle-gateway' ),
					'<strong>' . esc_html( date_i18n( wc_date_format(), strtotime( $renewal['date'] ) ) ) . '</strong>'
				);
				?>
			</summary>
			<table class="widefat fixed striped">
				<tbody>
					<?php if ( ! empty( $renewal['order_id'] ) ) : ?>
					<tr class="woo-paddle-gateway-paddle-renewal-history-order-id">
						<th scope="row"><?php esc_html_e( 'Order ID', 'woo-paddle-gateway' ); ?></th>
						<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $renewal['order_id'] . '&action=edit' ) ); ?>"><?php echo esc_html( $renewal['order_id'] ); ?>&rarr;</a></td>
					</tr>
					<?php endif; ?>
					<?php if ( ! empty( $renewal['total'] ) ) : ?>
						<tr class="woo-paddle-gateway-paddle-renewal-history-total">
							<th scope="row"><?php esc_html_e( 'Total', 'woo-paddle-gateway' ); ?></th>
							<td><?php echo wc_clean( wc_price( $renewal['total'] ) ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( $renewal['status'] ) ) : ?>
						<tr class="woo-paddle-gateway-paddle-renewal-history-status">
							<th scope="row"><?php esc_html_e( 'Status', 'woo-paddle-gateway' ); ?></th>
							<td><mark class="order-status status-<?php echo esc_attr( $renewal['status'] ); ?>"><span><?php echo wc_clean( ucfirst( $renewal['status'] ) ); ?></span></mark>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( $renewal['receipt_url'] ) ) : ?>
						<tr class="woo-paddle-gateway-paddle-renewal-history-receipt-url">
							<th scope="row"><?php esc_html_e( 'Receipt URL', 'woo-paddle-gateway' ); ?></th>
							<td><?php echo wp_kses( make_clickable( $renewal['receipt_url'] ), array( 'a' => array( 'href' => array() ) ) ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</details>
		<?php
	endforeach;
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	?>
</div>
