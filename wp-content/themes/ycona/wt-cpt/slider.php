<?php
/* Custom Post Type - Slider */

function show_slider_custom_fields() {

	$js_src = includes_url( 'js/tinymce/' ) . 'tinymce.min.js';
	$css_src = includes_url( 'css/' ) . 'editor.css';

	wp_register_style( 'tinymce_css', $css_src );
	wp_enqueue_style( 'tinymce_css' );

	global $post;

	$meta = get_post_meta( $post->ID, 'slider_fields', true );
	$c = 0;

	?>

	<script src="<?php echo esc_url( $js_src ); ?>" type="text/javascript"></script>
	<div>

		<input type="hidden" name="slider_meta_nonce" value="<?php echo wp_create_nonce( 'save_slider_fields' ); ?>">

		<div class="slider-settings">
			<h3><?php _e( 'Slider', 'webthinkershop' ); ?></h3>
		</div>

		<div id="wt-wrapper-slider" class="wt-wrapper-cpt">

			<?php

			if ( is_array( $meta ) && isset( $meta['slides'] ) && count( $meta['slides'] ) > 0 ) {
				foreach ( $meta['slides'] as $slide ) {
					$subtitle      = $slide['subtitle'] ?? '';
					$slide_title   = $slide['slide_title'] ?? '';
					$description   = $slide['description'] ?? '';
					$website       = $slide['website'] ?? '';
					$button_url    = $slide['button_url'] ?? '';
					$button_text   = $slide['button_text'] ?? '';
					$img_src         = $slide['image'] ?? '';
					$img_id          = $slide['imageId'] ?? '';
					$show_overlay    = isset( $slide['show_overlay'] ) ? ( $slide['show_overlay'] === '1' || $slide['show_overlay'] === true ) : true;
					$slide_layout    = $slide['slide_layout'] ?? 'default';
					$effect_color_1  = ! empty( $slide['effect_color_1'] ) ? $slide['effect_color_1'] : '#f0c000';
					$effect_color_2  = ! empty( $slide['effect_color_2'] ) ? $slide['effect_color_2'] : '#111111';
					$effect_color_1_attr = $effect_color_1;
					if ( $effect_color_1_attr !== '' && strpos( $effect_color_1_attr, '#' ) !== 0 ) {
						$effect_color_1_attr = '#' . ltrim( $effect_color_1_attr, '#' );
					}
					if ( $effect_color_1_attr === '' ) {
						$effect_color_1_attr = '#f0c000';
					}
					$effect_color_2_attr = $effect_color_2;
					if ( $effect_color_2_attr !== '' && strpos( $effect_color_2_attr, '#' ) !== 0 ) {
						$effect_color_2_attr = '#' . ltrim( $effect_color_2_attr, '#' );
					}
					if ( $effect_color_2_attr === '' ) {
						$effect_color_2_attr = '#111111';
					}

					echo '<div class="slider-item cpt-element" data-count="' . (int) $c . '">

			<div class="sort-buttons">
				<button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
				<button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</button>
			</div>

			<div id="box-wrapper-' . (int) $c . '" class="slider-box cpt-box">

				<div class="click-area">
					<h3>' . esc_html( sprintf( __( 'Slide #%d', 'webthinkershop' ), $c + 1 ) ) . '</h3>
				</div>

				<div class="content-area">
					<dl>
						<dt></dt>
						<dd><hr></dd>

						<dt>' . __( 'Background Image', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="hidden" name="slider_fields[slides][' . (int) $c . '][image]" class="meta-image" value="' . esc_attr( $img_src ) . '">
							<input type="hidden" name="slider_fields[slides][' . (int) $c . '][imageId]" class="meta-image-id" value="' . esc_attr( $img_id ) . '">
							<input type="button" data-id="' . (int) $c . '" class="button image-upload" value="' . __( 'Browse', 'webthinkershop' ) . '">
							<input type="button" class="button image-upload-remove" data-id="' . (int) $c . '" value="' . __( 'Remove', 'webthinkershop' ) . '">
						</dd>

						<dt>' . __( 'Image Preview', 'webthinkershop' ) . '</dt>
						<dd>
							<div class="image-preview"><img src="' . esc_url( $img_src ) . '" alt=""></div>
						</dd>

						<dt>' . __( 'Show Overlay', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="hidden" name="slider_fields[slides][' . (int) $c . '][show_overlay]" value="0">
							<label><input type="checkbox" name="slider_fields[slides][' . (int) $c . '][show_overlay]" value="1" ' . checked( $show_overlay, true, false ) . '> ' . __( 'Dark overlay on image', 'webthinkershop' ) . '</label>
						</dd>

						<dt>' . __( 'Slide layout', 'webthinkershop' ) . '</dt>
						<dd>
							<select name="slider_fields[slides][' . (int) $c . '][slide_layout]" class="regular-text">
								<option value="default" ' . selected( $slide_layout, 'default', false ) . '>' . __( 'Default (full image)', 'webthinkershop' ) . '</option>
								<option value="split" ' . selected( $slide_layout, 'split', false ) . '>' . __( 'Split (left content, right image with effects)', 'webthinkershop' ) . '</option>
							</select>
						</dd>

						<dt>' . __( 'Effect color 1 (accent)', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="color" name="slider_fields[slides][' . (int) $c . '][effect_color_1]" value="' . esc_attr( $effect_color_1_attr ) . '" class="slider-color-input" aria-label="' . esc_attr__( 'Accent color', 'webthinkershop' ) . '" style="width:48px;height:32px;padding:2px;cursor:pointer;border:1px solid #8c8f94;border-radius:4px;">
						</dd>

						<dt>' . __( 'Effect color 2 (dark)', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="color" name="slider_fields[slides][' . (int) $c . '][effect_color_2]" value="' . esc_attr( $effect_color_2_attr ) . '" class="slider-color-input" aria-label="' . esc_attr__( 'Dark color', 'webthinkershop' ) . '" style="width:48px;height:32px;padding:2px;cursor:pointer;border:1px solid #8c8f94;border-radius:4px;">
						</dd>

						<dd><hr></dd>

						<dt>' . __( 'Subtitle', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="text" name="slider_fields[slides][' . (int) $c . '][subtitle]" placeholder="' . __( 'e.g. Let\'s go', 'webthinkershop' ) . '" class="regular-text" value="' . esc_attr( $subtitle ) . '">
						</dd>

						<dt>' . __( 'Title', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="text" name="slider_fields[slides][' . (int) $c . '][slide_title]" placeholder="' . __( 'Write here', 'webthinkershop' ) . '..." class="regular-text" value="' . esc_attr( $slide_title ) . '">
						</dd>

						<div class="elements-hexagon-style">
							<dt>' . __( 'Description', 'webthinkershop' ) . '</dt>
							' . get_wp_editor( $description, 'slider_fields_description_' . $c, 'slider_fields[slides][' . $c . '][description]' ) . '
						</div>

						<dt>' . __( 'Website (optional)', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="text" name="slider_fields[slides][' . (int) $c . '][website]" placeholder="www.example.com" class="regular-text" value="' . esc_attr( $website ) . '">
						</dd>

						<dt>' . __( 'Button URL', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="text" name="slider_fields[slides][' . (int) $c . '][button_url]" placeholder="https://..." class="regular-text" value="' . esc_attr( $button_url ) . '">
						</dd>

						<dt>' . __( 'Button Text', 'webthinkershop' ) . '</dt>
						<dd>
							<input type="text" name="slider_fields[slides][' . (int) $c . '][button_text]" placeholder="' . __( 'Write here', 'webthinkershop' ) . '..." class="regular-text" value="' . esc_attr( $button_text ) . '">
						</dd>

						<div class="cpt-remove">
							<button type="button" class="remove">' . __( 'Remove Slide', 'webthinkershop' ) . '</button>
						</div>
					</dl>
				</div>
			</div>
		</div>';
					$c++;
				}
			}

			?>
		</div>
		<button type="button" class="add"><?php _e( 'Add Slide', 'webthinkershop' ); ?></button>
	</div>

	<script>

		jQuery(document).ready(function() {

			jQuery("#wt-wrapper-slider + .add").click(function() {
				var $add_btn = jQuery(this);
				var $wrapper = $add_btn.prev(".wt-wrapper-cpt");
				$add_btn.hide();

				// 0-based index so first slide is 0 (Slide #1), not 1 (Slide #2)
				let count = $wrapper.find(".slider-item").length;

				var slide_html = `<div class="slider-item cpt-element" data-count="${count}">

					<div class="sort-buttons">
						<button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
							<span class="dashicons dashicons-arrow-down-alt2"></span>
						</button>
						<button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
							<span class="dashicons dashicons-arrow-up-alt2"></span>
						</button>
					</div>

					<div id="box-wrapper-${count}" class="slider-box cpt-box">

						<div class="click-area">
							<h3><?php echo esc_html__( 'Slide', 'webthinkershop' ); ?> #${count + 1}</h3>
						</div>

						<div class="content-area link">
							<dl>
								<dt></dt>
								<dd><hr></dd>

								<dt><?php _e( 'Background Image', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="hidden" name="slider_fields[slides][${count}][image]" class="meta-image" value="">
									<input type="hidden" name="slider_fields[slides][${count}][imageId]" class="meta-image-id" value="">
									<input type="button" data-id="${count}" class="button image-upload" value="<?php _e( 'Browse', 'webthinkershop' ); ?>">
									<input type="button" data-id="${count}" class="button image-upload-remove" value="<?php _e( 'Remove', 'webthinkershop' ); ?>">
								</dd>

								<dt><?php _e( 'Image Preview', 'webthinkershop' ); ?></dt>
								<dd>
									<div class="image-preview"><img src="" alt=""></div>
								</dd>

								<dt><?php _e( 'Show Overlay', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="hidden" name="slider_fields[slides][${count}][show_overlay]" value="0">
									<label><input type="checkbox" name="slider_fields[slides][${count}][show_overlay]" value="1" checked> <?php _e( 'Dark overlay on image', 'webthinkershop' ); ?></label>
								</dd>

								<dt><?php _e( 'Slide layout', 'webthinkershop' ); ?></dt>
								<dd>
									<select name="slider_fields[slides][${count}][slide_layout]" class="regular-text">
										<option value="default"><?php _e( 'Default (full image)', 'webthinkershop' ); ?></option>
										<option value="split"><?php _e( 'Split (left content, right image with effects)', 'webthinkershop' ); ?></option>
									</select>
								</dd>

								<dt><?php _e( 'Effect color 1 (accent)', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="color" name="slider_fields[slides][${count}][effect_color_1]" value="#f0c000" class="slider-color-input" aria-label="<?php echo esc_attr__( 'Accent color', 'webthinkershop' ); ?>" style="width:48px;height:32px;padding:2px;cursor:pointer;border:1px solid #8c8f94;border-radius:4px;">
								</dd>

								<dt><?php _e( 'Effect color 2 (dark)', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="color" name="slider_fields[slides][${count}][effect_color_2]" value="#111111" class="slider-color-input" aria-label="<?php echo esc_attr__( 'Dark color', 'webthinkershop' ); ?>" style="width:48px;height:32px;padding:2px;cursor:pointer;border:1px solid #8c8f94;border-radius:4px;">
								</dd>

								<dd><hr></dd>

								<dt><?php _e( 'Subtitle', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="text" name="slider_fields[slides][${count}][subtitle]" placeholder="<?php echo esc_attr__( "e.g. Let's go", 'webthinkershop' ); ?>" class="regular-text" value="">
								</dd>

								<dt><?php _e( 'Title', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="text" name="slider_fields[slides][${count}][slide_title]" placeholder="<?php _e( 'Write here', 'webthinkershop' ); ?>..." class="regular-text" value="">
								</dd>

								<div class="elements-hexagon-style">
									<dt><?php _e( 'Description', 'webthinkershop' ); ?></dt>
									<span id="box-row2-${count}-slider_fields_${count}_description"></span>
								</div>

								<dt><?php _e( 'Website (optional)', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="text" name="slider_fields[slides][${count}][website]" placeholder="www.example.com" class="regular-text" value="">
								</dd>

								<dt><?php _e( 'Button URL', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="text" name="slider_fields[slides][${count}][button_url]" placeholder="https://..." class="regular-text" value="">
								</dd>

								<dt><?php _e( 'Button Text', 'webthinkershop' ); ?></dt>
								<dd>
									<input type="text" name="slider_fields[slides][${count}][button_text]" placeholder="<?php _e( 'Write here', 'webthinkershop' ); ?>..." class="regular-text" value="">
								</dd>

								<div class="cpt-remove">
									<button type="button" class="remove"><?php _e( 'Remove Slide', 'webthinkershop' ); ?></button>
								</div>
							</dl>
						</div>
					</div>

				</div>`;

				$wrapper.append(slide_html);

				let target = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";

				let create_wp_editor = function(editor_id, editor_name, $add_btn) {
					let data_text = {
						'action': 'wt_get_text_editor',
						'text_editor_id': editor_id,
						'textarea_name': editor_name
					};

					jQuery.post(target, data_text)
						.done(function (response) {
							let cont = "#box-row2-" + count + "-" + editor_id;
							jQuery(cont).append(response);
							if (typeof tinymce !== "undefined") tinymce.execCommand('mceAddEditor', false, editor_id);
							if (typeof quicktags !== "undefined") quicktags({id: editor_id});
						})
						.always(function() {
							$add_btn.show();
						});
				};

				let description_id = "slider_fields_" + count + "_description";
				let description_name = "slider_fields[slides][" + count + "][description]";

				create_wp_editor(description_id, description_name, $add_btn);

				set_buttons();
				reset_sort();
			});

			set_buttons();
		});
	</script>
<?php
}
/* END - Custom Post Type - Slider */
