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
	esc_html_e( 'No Paddle payment details found.', 'woo-paddle-gateway' );
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

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
