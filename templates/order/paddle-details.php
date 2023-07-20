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

<table class="widefat fixed striped woo-paddle-gateway-paddle-details">
	<tbody>
		<?php
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		foreach ( $args['meta'] as $label => $value ) :
			?>
		<tr class="woo-paddle-gateway-paddle-details-<?php echo sanitize_html_class( $label ); ?>">
			<th scope="row"><?php echo esc_html( str_replace( '_', ' ', $label ) ); ?></th>
			<td><?php echo wp_kses( make_clickable( $value ), array( 'a' => array( 'href' => array() ) ) ); ?></td>
		</tr>
			<?php
		endforeach;
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		?>
	</tbody>
</table>
