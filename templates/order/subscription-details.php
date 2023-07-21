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
		<?php
        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		foreach ( $args['allowed_details'] as $key ) :

			// Skip if the meta key is empty.
			if ( empty( $args['meta'][ $key ] ) ) {
				continue;
			}

			// Format the date.
			if ( strpos( $key, 'date' ) !== false ) {
				$args['meta'][ $key ] = date_i18n( wc_date_format(), strtotime( $args['meta'][ $key ] ) );
			}
			?>
			<tr class="woo-paddle-gateway-paddle-details-<?php echo sanitize_html_class( $key ); ?>">
				<th scope="row"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></th>
				<td><?php echo esc_html( ucwords( $args['meta'][ $key ] ) ); ?></td>
			</tr>
			<?php
		endforeach;
        // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		?>
	</tbody>
</table>
