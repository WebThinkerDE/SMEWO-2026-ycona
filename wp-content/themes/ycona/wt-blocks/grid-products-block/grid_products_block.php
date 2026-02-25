<?php
/**
 * Grid products block – same options as "You may also like", products in a grid (no slider).
 *
 * @package dtf
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

function wt_grid_products_block_rc( $attributes, $content ) {
	global $theme_path;

	if ( ! function_exists( 'wc_get_product' ) ) {
		return '';
	}

	wp_enqueue_style( 'wt_grid_products_block', $theme_path . '/wt-blocks/grid-products-block/grid_products_block.css', array( 'css-main' ), '1' );

	$layout       = isset( $attributes['layout'] ) ? $attributes['layout'] : 'container';
	$show_title   = isset( $attributes['show_title'] ) ? $attributes['show_title'] : 'yes';
	$title        = isset( $attributes['title'] ) ? $attributes['title'] : __( 'Products', 'webthinkershop' );
	$use_manual   = isset( $attributes['use_manual'] ) ? $attributes['use_manual'] : 'no';
	$product_ids  = isset( $attributes['product_ids'] ) ? $attributes['product_ids'] : '';
	$limit_raw    = isset( $attributes['limit'] ) ? $attributes['limit'] : '';
	$limit        = ( $limit_raw !== '' && $limit_raw !== null ) ? max( 1, (int) $limit_raw ) : 8;
	$space_top    = isset( $attributes['space_top'] ) ? $attributes['space_top'] : 'yes';
	$space_bottom = isset( $attributes['space_bottom'] ) ? $attributes['space_bottom'] : 'yes';

	$ids = array();
	if ( $use_manual === 'yes' && $product_ids !== '' ) {
		$ids = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $product_ids ) ) ) );
	}

	if ( ! empty( $ids ) ) {
		$query_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'post_status'    => 'publish',
		);
	} else {
		$query_args = array(
			'post_type'      => 'product',
			'posts_per_page' => $limit,
			'orderby'        => 'rand',
			'post_status'    => 'publish',
		);
	}

	$q = new WP_Query( $query_args );
	if ( ! $q->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $layout ); ?>">
	<section class="related products wt-shop-grid-products-block space-top-<?php echo esc_attr( $space_top ); ?> space-bottom-<?php echo esc_attr( $space_bottom ); ?>">
		<?php if ( $show_title !== 'no' ) : ?>
		<h2 class="wt-shop-thankyou-also-like-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<ul class="products wt-shop-products-grid">
			<?php
			while ( $q->have_posts() ) {
				$q->the_post();
				$product = wc_get_product( get_the_ID() );
				if ( ! $product || ! $product->is_visible() ) {
					continue;
				}
				global $product;
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</section>
	</div>
	<?php
	return ob_get_clean();
}
