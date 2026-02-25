<?php
/**
 * Shop / product archive – Floorplan theme override
 *
 * Sidebar (categories) on the left, product grid on the right.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<div class="wt-shop-shop-wrap container">
	<?php
	/**
	 * Hook: woocommerce_before_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper - 10
	 * @hooked woocommerce_breadcrumb - 20
	 * @hooked WC_Structured_Data::generate_website_data() - 30
	 */
	do_action( 'woocommerce_before_main_content' );
	?>

	<div class="wt-shop-shop-layout">

		<div class="wt-shop-shop-sidebar-wrap">
			<button type="button" class="wt-shop-sidebar-mobile-toggle wt-shop-sidebar-toggle-cats" aria-expanded="false" aria-controls="wt-shop-sidebar-cats">
				<span><?php esc_html_e( 'Categories', 'ycona' ); ?></span>
				<svg width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<button type="button" class="wt-shop-sidebar-mobile-toggle wt-shop-sidebar-toggle-filters" aria-expanded="false" aria-controls="wt-shop-sidebar-filters">
				<span><?php esc_html_e( 'Filters', 'webthinkershop' ); ?></span>
				<svg width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		<?php
		// Sidebar with category tree + filters.
		if ( function_exists( 'wt_shop_shop_category_sidebar' ) ) {
			wt_shop_shop_category_sidebar();
		}
		?>
		</div>

		<div class="wt-shop-shop-main">

			<div class="wt-shop-shop-toolbar">
			<?php
			/**
			 * Hook: woocommerce_shop_loop_header.
			 *
			 * @hooked woocommerce_product_taxonomy_archive_header - 10
			 */
			do_action( 'woocommerce_shop_loop_header' );
			?>

			<?php if ( woocommerce_product_loop() ) : ?>

				<?php
				/**
				 * Hook: woocommerce_before_shop_loop.
				 *
				 * @hooked woocommerce_output_all_notices - 10
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
				?>
			</div><!-- .wt-shop-shop-toolbar -->

				<?php woocommerce_product_loop_start(); ?>

				<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
					<?php
					while ( have_posts() ) {
						the_post();
						if ( function_exists( 'wc_setup_product_data' ) ) {
							wc_setup_product_data( $GLOBALS['post'] );
						}
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					}
					?>
				<?php endif; ?>

				<?php woocommerce_product_loop_end(); ?>

				<?php
				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
				?>

			<?php else : ?>
			</div><!-- .wt-shop-shop-toolbar -->
				<?php
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
				?>
			<?php endif; ?>

		</div><!-- .wt-shop-shop-main -->

	</div><!-- .wt-shop-shop-layout -->

	<?php
	/**
	 * Hook: woocommerce_after_main_content.
	 *
	 * @hooked woocommerce_output_content_wrapper_end - 10
	 */
	do_action( 'woocommerce_after_main_content' );
	?>
</div><!-- .wt-shop-shop-wrap -->

<?php
get_footer( 'shop' );
