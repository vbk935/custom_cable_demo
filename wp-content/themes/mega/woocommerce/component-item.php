<?php
/**
 * Component item data template.
 * @version 	2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<dl class="component">
	<?php
	$key = sanitize_text_field( $component_data['key'] );
	?>
	<dt class="component-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( $component_data['key'] ); ?>:</dt>
	<dd class="component-<?php echo sanitize_html_class( $key ); ?>"><?php echo wp_kses_post( wpautop( $component_data['value'] ) ); ?></dd>
</dl>
