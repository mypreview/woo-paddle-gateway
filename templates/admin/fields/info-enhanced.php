<?php
/**
 * The Template for displaying select dropdown field.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $args['type'] ); ?>"><?php echo wp_kses_post( $args['title'] ); ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<p class="description">
				<?php echo wp_kses_post( $args['description'] ); ?>
			</p>
		</fieldset>
	</td>
</tr>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
