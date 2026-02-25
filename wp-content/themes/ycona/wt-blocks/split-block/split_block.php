<?php
/**
 * Copyright (c) 2025 by Granit Nebiu
 *
 * Split Block - Content + Image, image position (left/right), mobile order
 *
 * @package WordPress
 * @subpackage gn
 * @author Granit Nebiu
 * @since 1.0
 */

function wt_split_block_rc( $attributes, $content ) {
	global $theme_path;
	wp_register_style( 'wt_split_block', $theme_path . '/wt-blocks/split-block/split_block.css', array( 'css-main' ), '1' );
	wp_enqueue_style( 'wt_split_block' );

	$class_name             = $attributes['class_name'] ?? '';
	$layout                 = $attributes['layout'] ?? 'fullWidth';
	$image                  = $attributes['image'] ?? null;
	$image_position_desktop = $attributes['image_position_desktop'] ?? 'right';
	$mobile_order           = $attributes['mobile_order'] ?? 'content_first';
	$subheadline            = $attributes['subheadline'] ?? '';
	$headline               = $attributes['headline'] ?? '';
	$description            = $attributes['description'] ?? '';
	$background_color       = $attributes['background_color'] ?? 'primary';
	$text_color             = $attributes['text_color'] ?? 'white';
	$button_1_text          = $attributes['button_1_text'] ?? '';
	$button_1_link          = $attributes['button_1_link'] ?? '#';
	$button_1_target        = $attributes['button_1_target'] ?? '_self';
	$button_1_style         = $attributes['button_1_style'] ?? 'outline';
	$button_2_text          = $attributes['button_2_text'] ?? '';
	$button_2_link          = $attributes['button_2_link'] ?? '#';
	$button_2_target        = $attributes['button_2_target'] ?? '_self';
	$button_2_style         = $attributes['button_2_style'] ?? 'outline';
	$image_alt              = $attributes['image_alt'] ?? '';
	$space_top              = $attributes['space_top'] ?? 'yes';
	$space_bottom           = $attributes['space_bottom'] ?? 'yes';

	$img_url = is_array( $image ) && ! empty( $image['url'] ) ? esc_url( $image['url'] ) : '';

	// Desktop: image left => image col order-xl-1, content order-xl-2. Image right => content order-xl-1, image order-xl-2.
	$content_order_xl = ( $image_position_desktop === 'right' ) ? 'order-xl-1' : 'order-xl-2';
	$image_order_xl   = ( $image_position_desktop === 'right' ) ? 'order-xl-2' : 'order-xl-1';
	$content_pe_ps   = ( $image_position_desktop === 'right' ) ? 'pe-xl-0' : 'ps-xl-0';
	$image_pe_ps     = ( $image_position_desktop === 'right' ) ? 'ps-xl-0' : 'pe-xl-0';

	// Mobile: content_first => content order-1, image order-2. image_first => content order-2, image order-1.
	$content_order_mob = ( $mobile_order === 'content_first' ) ? 'order-1' : 'order-2';
	$image_order_mob   = ( $mobile_order === 'content_first' ) ? 'order-2' : 'order-1';

	$container_class = ( $layout === 'fullWidth' ) ? 'container-fluid' : 'container';

	$buttons_html = '';
	if ( $button_1_text !== '' || $button_2_text !== '' ) {
		$buttons_html = '<div class="split-block-btn-wrapper d-flex flex-wrap gap-3">';
		if ( $button_1_text !== '' ) {
			$buttons_html .= '<a class="btn-split btn-split-' . esc_attr( $button_1_style ) . ' mb-3" href="' . esc_url( $button_1_link ) . '" target="' . esc_attr( $button_1_target ) . '"><span>' . esc_html( $button_1_text ) . '</span></a>';
		}
		if ( $button_2_text !== '' ) {
			$buttons_html .= '<a class="btn-split btn-split-' . esc_attr( $button_2_style ) . ' mb-3" href="' . esc_url( $button_2_link ) . '" target="' . esc_attr( $button_2_target ) . '"><span>' . esc_html( $button_2_text ) . '</span></a>';
		}
		$buttons_html .= '</div>';
	}

	ob_start();
	?>
	<section id="<?php echo esc_attr( $class_name ); ?>" class="split-block split-block-layout-<?php echo esc_attr( $layout ); ?> space-top-<?php echo esc_attr( $space_top ); ?> space-bottom-<?php echo esc_attr( $space_bottom ); ?> <?php echo esc_attr( $class_name ); ?>">
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<div class="split-block-wrapper">
				<div class="row g-0">
					<div class="col-12 col-xl-6 split-block-content-col <?php echo esc_attr( $content_order_mob . ' ' . $content_order_xl . ' ' . $content_pe_ps ); ?>">
						<div class="split-block-content bg-color-<?php echo esc_attr( $background_color ); ?> text-color-<?php echo esc_attr( $text_color ); ?>">
							<div class="split-block-content-inner">
								<?php if ( $subheadline !== '' ) : ?>
									<span class="split-block-subheadline"><?php echo esc_html( $subheadline ); ?></span>
								<?php endif; ?>
								<?php if ( $headline !== '' ) : ?>
									<h2 class="split-block-headline"><?php echo esc_html( $headline ); ?></h2>
								<?php endif; ?>
								<?php if ( $description !== '' ) : ?>
									<div class="split-block-description"><?php echo wp_kses_post( $description ); ?></div>
								<?php endif; ?>
								<?php echo $buttons_html; ?>
							</div>
						</div>
					</div>
					<div class="col-12 col-xl-6 split-block-image-col d-flex align-items-center <?php echo esc_attr( $image_order_mob . ' ' . $image_order_xl . ' ' . $image_pe_ps ); ?>">
						<?php if ( $img_url !== '' ) : ?>
							<img loading="lazy" decoding="async" src="<?php echo esc_url( $img_url ); ?>" class="img-fluid split-block-img" alt="<?php echo esc_attr( $image_alt ); ?>" />
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
