<?php
/**
 * The Template for displaying Paddle payment details in the order admin.
 *
 * @since 1.0.0
 *
 * @package woo-store-vacation-pro
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// If the order meta is empty, return.
if ( ! isset( $args['meta'] ) || empty( $args['meta'] ) ) {
	return;
}

?>

<div class="woo-paddle-gateway-paddle-details">
	<?php
	// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	foreach ( $args['meta'] as $event ) :
		?>
		<details>
			<summary>
				<?php
				printf(
					esc_html__( 'Payload response: "%s"', 'mypreview-paddle' ),
					'<strong>' . esc_html( str_replace( '_', ' ', $event['alert_name'] ) ) . '</strong>'
				);
				?>
			</summary>
			<table class="widefat fixed striped">
				<tbody>
					<?php foreach ( $event as $label => $value ) : ?>
						<tr class="woo-paddle-gateway-paddle-details-<?php echo sanitize_html_class( $label ); ?>">
							<th scope="row"><?php echo esc_html( str_replace( '_', ' ', $label ) ); ?></th>
							<td><?php echo wp_kses( make_clickable( $value ), array( 'a' => array( 'href' => array() ) ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</details>
		<?php
	endforeach;
	// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	?>
</div>
