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

// If the order logs is empty, return.
if ( empty( $args['meta'] ) ) {
	esc_html_e( 'No Paddle subscription renewal found.', 'woo-paddle-gateway' );
	return;
}

?>

<table class="widefat fixed striped woo-paddle-gateway-paddle-details">
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Order ID', 'woo-paddle-gateway' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Total', 'woo-paddle-gateway' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Date', 'woo-paddle-gateway' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Status', 'woo-paddle-gateway' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	foreach ( $args['meta'] as $renewal ) :
		?>
		<tr>
			<td>#<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $renewal['order_id'] . '&action=edit' ) ); ?>"><?php echo esc_html( $renewal['order_id'] ); ?></a></td>
			<td><?php echo wp_kses( wc_price( $renewal['total'] ), array( 'span' => array( 'class' => array() ) ) ); ?></td>
			<td><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $renewal['date'] ) ) ); ?></td>
			<td>
				<mark class="order-status status-<?php echo esc_attr( $renewal['status'] ); ?>">
					<span><?php echo esc_html( ucwords( $renewal['status'] ) ); ?></span>
				</mark>
			</td>
		</tr>
		<?php
	endforeach;
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	?>
	</tbody>
</table>
