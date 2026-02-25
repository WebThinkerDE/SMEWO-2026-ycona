<?php
/**
 * Slider Block – render callback.
 * Travel Geometric Slider; same structure and class names as reference.
 *
 * @package WordPress
 * @subpackage dtf
 */

function wt_slider_block_rc( $attributes, $content ) {
	global $theme_path;

	wp_register_style( 'wt_slider_block', $theme_path . '/wt-blocks/slider-block/slider_block.css', array( 'css-main' ), '4.0' );
	wp_enqueue_style( 'wt_slider_block' );
	wp_enqueue_script( 'wt_slider_block_js', $theme_path . '/wt-blocks/slider-block/slider_block.min.js', array( 'js-main' ), '4.0', true );

	$post_id        = $attributes['post_id'] ?? '';
	$class_name     = $attributes['className'] ?? 'default-wt-shop';
	$section_layout = $attributes['layout'] ?? 'container';
	$uid            = uniqid( 'slider_' );
	$space_bottom   = $attributes['space_bottom'] ?? 'yes';
	$space_top      = $attributes['space_top'] ?? 'yes';

	$slider_data  = get_post_meta( $post_id, 'slider_fields', true );
	$slides       = $slider_data['slides'] ?? array();
	$slides_count = count( $slides );

	$show_nav  = $slides_count > 1;
	$nav_class = $show_nav ? 'slider-block-has-nav' : 'slider-block-no-nav';

	$slides_html = '';
	foreach ( $slides as $slide ) {
		$subtitle        = $slide['subtitle'] ?? '';
		$image_url       = $slide['image'] ?? '';
		$slide_title     = $slide['slide_title'] ?? '';
		$description     = $slide['description'] ?? '';
		$website         = $slide['website'] ?? '';
		$button_url      = $slide['button_url'] ?? '';
		$button_text     = $slide['button_text'] ?? '';
		$show_overlay    = isset( $slide['show_overlay'] ) ? ( $slide['show_overlay'] === '1' || $slide['show_overlay'] === true ) : true;
		$effect_color_1  = ! empty( $slide['effect_color_1'] ) ? sanitize_hex_color( $slide['effect_color_1'] ) : '#FACC15';
		if ( ! $effect_color_1 ) {
			$effect_color_1 = '#FACC15';
		}

		$effect_style = ' style="--slider-effect-1:' . esc_attr( $effect_color_1 ) . '"';

		$img_tag = ! empty( $image_url )
			? '<img class="bg-image-anim" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $slide_title ) . '" loading="lazy">'
			: '';
		$overlay_div = $show_overlay ? '<div class="slide-image-overlay"></div>' : '';

		$subtitle_html = $subtitle !== ''
			? '<div class="slide-subtitle-wrap animate-title"><h3 class="slide-subtitle">' . esc_html( $subtitle ) . '</h3><div class="dots-grid"></div></div>'
			: '';

		$title_html   = $slide_title !== '' ? '<h1 class="slide-title animate-subtitle">' . wp_kses_post( $slide_title ) . '</h1>' : '';
		$desc_html    = $description !== '' ? '<p class="slide-desc animate-desc">' . wp_kses_post( $description ) . '</p>' : '';
		$website_html = $website !== '' ? '<div class="slide-website animate-desc">' . esc_html( $website ) . '</div>' : '';

		$btn_html = '';
		if ( $button_url !== '' ) {
			$btn_label = $button_text !== '' ? $button_text : __( 'Read more', 'webthinkershop' );
			$btn_html  = '<a href="' . esc_url( $button_url ) . '" class="slide-btn animate-btn">' . esc_html( $btn_label ) . ' <i class="bi bi-chevron-right"></i></a>';
		}

		$slides_html .= '<div class="slider-block-slide">
			<div class="slider-block-slide-inner"' . $effect_style . '>
				<div class="slide-image-layer">
					' . $img_tag . '
					' . $overlay_div . '
					<div class="slide-edge-accent clip-chevron-right"></div>
				</div>
				<div class="desktop-geometry slide-stripe-accent clip-chevron-strip"></div>
				<div class="desktop-geometry slide-stripe-white clip-chevron-main"></div>
				<div class="slide-content-layer">
					<div class="content-wrapper">
						' . $subtitle_html . '
						' . $title_html . '
						' . $desc_html . '
						' . $website_html . '
						' . $btn_html . '
					</div>
				</div>
			</div>
		</div>';
	}

	$prev_btn = $show_nav ? '<button type="button" class="slider-block-btn-prev" aria-label="' . esc_attr__( 'Previous', 'webthinkershop' ) . '"><i class="bi bi-chevron-left"></i></button>' : '';
	$next_btn = $show_nav ? '<button type="button" class="slider-block-btn-next" aria-label="' . esc_attr__( 'Next', 'webthinkershop' ) . '"><i class="bi bi-chevron-right"></i></button>' : '';

	$html = '<section id="' . esc_attr( $uid ) . '" class="slider-block-section slider-block-count-' . (int) $slides_count . ' ' . esc_attr( $nav_class ) . ' ' . esc_attr( $class_name ) . ' space-bottom-' . esc_attr( $space_bottom ) . ' space-top-' . esc_attr( $space_top ) . '" data-slides-count="' . (int) $slides_count . '" data-autoplay="5000">
		<div class="' . esc_attr( $section_layout ) . '">
			<div class="slider-block-wrap">
				<div class="slider-block-viewport">
					<div class="slider-block-track">' . $slides_html . '</div>
				</div>
				<div class="slider-block-nav-wrap">' . $prev_btn . $next_btn . '</div>
			</div>
		</div>
	</section>';

	return $html;
}
