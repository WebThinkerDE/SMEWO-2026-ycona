<?php
/**
 * You may like block – product slider (same design as single product / 404).
 * Options: show random products or choose specific product IDs.
 *
 * @package dtf
 * @since 1.0
 */

defined( 'ABSPATH' ) || exit;

function wt_you_may_like_block_rc( $attributes, $content ) {
	global $theme_path;

	if ( ! function_exists( 'wc_get_product' ) ) {
		return '';
	}

	wp_enqueue_style( 'wt_you_may_like_block', $theme_path . '/wt-blocks/you-may-like-block/you_may_like_block.css', array( 'css-main' ), '1' );
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wt_swiper_js', $theme_path . '/assets/js/swiper/swiper-bundle.min.js', array(), null, true );
		wp_enqueue_script( 'wt-shop-woocommerce-js', $theme_path . '/assets/js/woocommerce.min.js', array( 'jquery', 'wt_swiper_js' ), null, true );
	}

	$layout       = isset( $attributes['layout'] ) ? $attributes['layout'] : 'container';
	$show_title   = isset( $attributes['show_title'] ) ? $attributes['show_title'] : 'yes';
	$title        = isset( $attributes['title'] ) ? $attributes['title'] : __( 'You may also like', 'webthinkershop' );
	$use_manual   = isset( $attributes['use_manual'] ) ? $attributes['use_manual'] : 'no';
	$product_ids  = isset( $attributes['product_ids'] ) ? $attributes['product_ids'] : '';
	$limit_raw    = isset( $attributes['limit'] ) ? $attributes['limit'] : '';
	$limit        = ( $limit_raw !== '' && $limit_raw !== null ) ? max( 1, min( 12, (int) $limit_raw ) ) : 8;
	$space_top    = isset( $attributes['space_top'] ) ? $attributes['space_top'] : 'yes';
	$space_bottom = isset( $attributes['space_bottom'] ) ? $attributes['space_bottom'] : 'yes';

	$ids = array();
	if ( $use_manual === 'yes' && $product_ids !== '' ) {
		$ids = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $product_ids ) ) ) );
	}

	if ( ! empty( $ids ) ) {
		$query_args = array(
			'post_type'      => 'product',
			'posts_per_page' => $limit,
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
	<section class="related products wt-shop-you-may-like-block space-top-<?php echo esc_attr( $space_top ); ?> space-bottom-<?php echo esc_attr( $space_bottom ); ?>">
		<?php if ( $show_title !== 'no' ) : ?>
		<h2 class="wt-shop-thankyou-also-like-title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<div class="wt-shop-products-swiper-wrap">
			<div class="swiper wt-shop-products-swiper">
				<div class="swiper-wrapper">
					<?php
					while ( $q->have_posts() ) {
						$q->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product || ! $product->is_visible() ) {
							continue;
						}
						global $product;
						ob_start();
						wc_get_template_part( 'content', 'product' );
						$li = ob_get_clean();
						// Convert <li ...> to <div class="swiper-slide ..."> and </li> to </div>
						$li = preg_replace( '/<li\s+class="/', '<div class="swiper-slide ', $li );
						$li = preg_replace( '/<li\s/', '<div class="swiper-slide" ', $li );
						$li = str_replace( '</li>', '</div>', $li );
						echo $li; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
			<button type="button" class="wt-shop-swiper-btn wt-shop-swiper-btn-prev" aria-label="<?php esc_attr_e( 'Previous', 'webthinkershop' ); ?>"><i class="bi bi-chevron-left"></i></button>
			<button type="button" class="wt-shop-swiper-btn wt-shop-swiper-btn-next" aria-label="<?php esc_attr_e( 'Next', 'webthinkershop' ); ?>"><i class="bi bi-chevron-right"></i></button>
		</div>
	</section>
	</div>
	<?php
	return ob_get_clean();
}
