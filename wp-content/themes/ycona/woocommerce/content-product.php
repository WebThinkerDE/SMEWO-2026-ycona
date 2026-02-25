<?php
/**
 * Product loop item – Floorplan theme override
 *
 * Output: image, title, short description, price, Read more link, Add to cart button.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product && function_exists( 'wc_get_product' ) && get_the_ID() ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
	return;
}

if ( ! $product->is_visible() ) {
	return;
}

$permalink   = $product->get_permalink();
$title       = $product->get_name();
$short_desc  = $product->get_short_description();
/* Variations have no short description – use parent product’s */
if ( empty( $short_desc ) && $product->is_type( 'variation' ) && $product->get_parent_id() ) {
	$parent = wc_get_product( $product->get_parent_id() );
	if ( $parent && is_a( $parent, 'WC_Product' ) ) {
		$short_desc = $parent->get_short_description();
	}
}
if ( $short_desc ) {
	$short_desc_plain = wp_strip_all_tags( $short_desc );
	$short_desc       = ( function_exists( 'mb_strlen' ) && mb_strlen( $short_desc_plain ) > 100 )
		? ( function_exists( 'mb_substr' ) ? mb_substr( $short_desc_plain, 0, 100 ) : substr( $short_desc_plain, 0, 100 ) ) . '...'
		: $short_desc_plain;
}
$image_id    = $product->get_image_id();
$is_free     = $product->is_type( 'simple' ) && (float) $product->get_price() <= 0;
$is_featured = $product->is_featured();
?>

<li <?php wc_product_class( '', $product ); ?>>
	<?php if ( $image_id ) : ?>
		<a href="<?php echo esc_url( $permalink ); ?>" class="woocommerce-loop-product__link" aria-hidden="true" tabindex="-1">
			<?php echo wp_get_attachment_image( $image_id, 'woocommerce_thumbnail', false, array( 'class' => 'attachment-woocommerce_thumbnail', 'loading' => 'lazy', 'alt' => esc_attr( $title ) ) ); ?>
			<?php woocommerce_show_product_loop_sale_flash(); ?>
			<?php if ( $is_featured ) : ?>
				<span class="wt-shop-shop-badge wt-shop-shop-badge-most-popular" aria-hidden="true"><?php esc_html_e( 'Most Popular', 'webthinkershop' ); ?></span>
			<?php endif; ?>
		</a>
	<?php else : ?>
		<a href="<?php echo esc_url( $permalink ); ?>" class="woocommerce-loop-product__link woocommerce-loop-product__link--no-img" aria-hidden="true" tabindex="-1">
			<span class="attachment-woocommerce_placeholder"><?php esc_html_e( 'No image', 'webthinkershop' ); ?></span>
			<?php woocommerce_show_product_loop_sale_flash(); ?>
			<?php if ( $is_featured ) : ?>
				<span class="wt-shop-shop-badge wt-shop-shop-badge-most-popular" aria-hidden="true"><?php esc_html_e( 'Most Popular', 'webthinkershop' ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<h2 class="woocommerce-loop-product__title">
		<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
	</h2>

	<?php if ( $short_desc ) : ?>
		<div class="wt-shop-shop-product-desc"><?php echo esc_html( $short_desc ); ?></div>
	<?php endif; ?>

	<?php woocommerce_template_loop_price(); ?>

	<?php woocommerce_template_loop_add_to_cart(); ?>
</li>
