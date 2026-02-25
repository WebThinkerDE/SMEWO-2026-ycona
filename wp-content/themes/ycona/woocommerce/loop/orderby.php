<?php
/**
 * Show options for ordering – theme override (visible label, filter group).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

$id_suffix = wp_unique_id();
if ( ! isset( $use_label ) ) {
	$use_label = true;
}
?>
<div class="wt-shop-shop-filter-group">
	<label for="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>" class="wt-shop-shop-filter-label"><?php esc_html_e( 'Sort by', 'woocommerce' ); ?></label>
	<form class="woocommerce-ordering" method="get">
		<select
			name="orderby"
			class="orderby"
			id="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>"
			aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>"
		>
			<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="hidden" name="paged" value="1" />
		<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
	</form>
</div>
