<?php
/**
 * Single product content – webthinkershop theme override
 *
 * Layout: gallery left, summary right; tabs below.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'wt-shop-single-product', $product ); ?>>

	<div class="wt-shop-single-product-top">
		<?php
		/**
		 * Gallery (images + thumbnails).
		 * Hooked: woocommerce_show_product_images – 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Summary (title, price, short description, add-to-cart, meta).
			 * Hooked: woocommerce_template_single_title – 5
			 *         woocommerce_template_single_rating – 10
			 *         woocommerce_template_single_price – 10
			 *         woocommerce_template_single_excerpt – 20
			 *         woocommerce_template_single_add_to_cart – 30
			 *         woocommerce_template_single_meta – 40
			 *         woocommerce_template_single_sharing – 50
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div><!-- .wt-shop-single-product-top -->

	<div id="product-long-description" class="wt-shop-single-product-long">
		<?php
		/**
		 * Tabs (Description, Additional Information, Reviews) + related products.
		 * Hooked: woocommerce_output_product_data_tabs – 10
		 *         woocommerce_upsell_display – 15
		 *         woocommerce_output_related_products – 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
