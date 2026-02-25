<?php
/**
 * CTA Block – Description, working time, phone, email + image (talking person). Image can overflow top/bottom on frontend.
 *
 * @package WordPress
 * @subpackage gn
 * @author Granit Nebiu
 * @since 1.0
 */

function wt_cta_block_rc( $attributes, $content ) {
	global $theme_path;
	wp_register_style( 'wt_cta_block', $theme_path . '/wt-blocks/cta-block/cta_block.css', array( 'css-main', 'boo-icons' ), '1' );
	wp_enqueue_style( 'wt_cta_block' );

	$class_name       = $attributes['class_name'] ?? '';
	$description      = $attributes['description'] ?? '';
	$working_time     = $attributes['working_time'] ?? '';
	$phone            = $attributes['phone'] ?? '';
	$email            = $attributes['email'] ?? '';
	$image            = $attributes['image'] ?? null;
	$image_alt        = $attributes['image_alt'] ?? '';
	$space_top        = $attributes['space_top'] ?? 'yes';
	$space_bottom     = $attributes['space_bottom'] ?? 'yes';
	$background_color = $attributes['background_color'] ?? 'light-gray';
	$text_color       = $attributes['text_color'] ?? 'black';

	$img_url = is_array( $image ) && ! empty( $image['url'] ) ? esc_url( $image['url'] ) : '';
	$phone_link = $phone !== '' ? 'tel:' . preg_replace( '/\s+/', '', $phone ) : '';
	$email_link = $email !== '' ? 'mailto:' . antispambot( $email ) : '';

	ob_start();
	?>
	<section class="cta-block bg-color-<?php echo esc_attr( $background_color ); ?> text-color-<?php echo esc_attr( $text_color ); ?> space-top-<?php echo esc_attr( $space_top ); ?> space-bottom-<?php echo esc_attr( $space_bottom ); ?> <?php echo esc_attr( $class_name ); ?>"<?php echo $class_name ? ' id="' . esc_attr( $class_name ) . '"' : ''; ?>>
		<div class="container">
			<div class="cta-block-inner">
				<div class="cta-block-content-col">
					<div class="cta-block-content">
						<?php if ( $description !== '' ) : ?>
							<div class="cta-block-description"><?php echo wp_kses_post( $description ); ?></div>
						<?php endif; ?>
						<?php if ( $working_time !== '' || $phone !== '' || $email !== '' ) : ?>
							<div class="cta-block-contact">
								<?php if ( $working_time !== '' ) : ?>
									<div class="cta-block-row">
										<span class="cta-block-icon" aria-hidden="true"><i class="bi bi-clock"></i></span>
										<span class="cta-block-label"><?php echo esc_html( $working_time ); ?></span>
									</div>
								<?php endif; ?>
								<?php if ( $phone !== '' ) : ?>
									<div class="cta-block-row">
										<span class="cta-block-icon" aria-hidden="true"><i class="bi bi-telephone"></i></span>
										<?php if ( $phone_link ) : ?>
											<a class="cta-block-link" href="<?php echo esc_attr( $phone_link ); ?>"><?php echo esc_html( $phone ); ?></a>
										<?php else : ?>
											<span class="cta-block-label"><?php echo esc_html( $phone ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if ( $email !== '' ) : ?>
									<div class="cta-block-row">
										<span class="cta-block-icon" aria-hidden="true"><i class="bi bi-envelope"></i></span>
										<?php if ( $email_link ) : ?>
											<a class="cta-block-link" href="<?php echo esc_attr( $email_link ); ?>"><?php echo esc_html( $email ); ?></a>
										<?php else : ?>
											<span class="cta-block-label"><?php echo esc_html( $email ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $img_url !== '' ) : ?>
					<div class="cta-block-image-col">
						<div class="cta-block-image-wrap">
							<img loading="lazy" decoding="async" src="<?php echo esc_url( $img_url ); ?>" class="cta-block-img" alt="<?php echo esc_attr( $image_alt ); ?>" />
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
