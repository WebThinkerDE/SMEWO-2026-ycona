<?php
/**
 * Single product – Floorplan theme override
 *
 * Full-width layout, no sidebar. Image left, data right; long description below.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<div class="wt-shop-single-product-wrap container">
	<?php do_action( 'woocommerce_before_main_content' ); ?>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php wc_get_template_part( 'content', 'single-product' ); ?>
	<?php endwhile; ?>

	<?php do_action( 'woocommerce_after_main_content' ); ?>
</div><!-- .wt-shop-single-product-wrap -->

<?php
// No sidebar on single product.
get_footer( 'shop' );
