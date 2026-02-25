<?php
/* Custom Post Type - Testimonials */

function show_testimonials_custom_fields() {

    $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
    $css_src = includes_url('css/') . 'editor.css';

    wp_register_style('tinymce_css', $css_src);
    wp_enqueue_style('tinymce_css');

    global $post;

    $meta = get_post_meta($post->ID,'testimonials_fields',true);
    $c = 0;

    ?>


    <script src="<?php echo $js_src; ?>" type="text/javascript"></script>
    <div>

        <input type="hidden" name="testimonialsMetaNonce" value="<?php echo wp_create_nonce( "testimonialsFields" ); ?>">

        <div class="testimonials-settings">

            <h3><?php _e( 'Testimonials', 'ycona' ); ?></h3>

        </div>

        <div id="wt-wrapper-testimonials" class="wt-wrapper-cpt">

            <?php

            if ( is_array($meta) && count( $meta ) > 0 )
            {
                foreach( $meta["testimonials"] as $testimonials )
                {
                    $location           = $testimonials["location"] ?? '';
	                $message            = $testimonials["message"] ?? "";
                    $name_surname       = $testimonials["name_surname"] ?? '';
	                $tick_type          = $testimonials["tick_type"] ?? '';
                    $img_src             = $testimonials["image"] ?? "";
                    $img_id              = $testimonials["imageId"] ?? "";
	          
                    echo '<div class="testimonials cpt-element" data-count="'.$c.'">

            <div class="sort-buttons">
                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                </button>
            </div>
            
            <div id="box-wrapper-'.$c.'" class="testimonials-box cpt-box">
                
                <div class="click-area">
                    <h3>' . esc_html( sprintf( __( 'Testimonial #%d', 'ycona' ), $c + 1 ) ) . '</h3>
                </div>
                
                <div class="content-area "> <!--Hier bei JS einfach link als Klasse-->
                    <dl>
                        <dt></dt>
                        <dd>
                            <hr>
                        </dd>
                        
                        <dt>'.__('Image profile','ycona').' </dt>
                        <dd>
                            <input type="hidden" name="testimonials_fields[testimonials]['.$c.'][image]" class="meta-image" value="'.$img_src.'">
                            <input type="hidden" name="testimonials_fields[testimonials]['.$c.'][imageId]" class="meta-image-id" value="'.$img_id.'">
                            <input type="button" data-id="'.$c.'" class="button image-upload" value="'.__('Browse','ycona').'">
                            <input type="button" class="button image-upload-remove" data-id="'.$c.'" value="'.__('Remove','ycona').'">
                        </dd>
                        
                        <dt>'.__('Icon Preview','ycona').'</dt>
                        <dd>
                            <div class="image-preview"><img src="'.$img_src.'" alt=""></div>
                        </dd>
                        
                        <dd>
                            <hr>
                        </dd>

                        <div class="elements-hexagon-style">
                            <dt>' . __('Message', "ycona") . '</dt>
                            ' . get_wp_editor($message, "testimonials_fields_message_$c", "testimonials_fields[testimonials][" . $c . "][message]") . '
                        </div>
                        
                        <dt></dt>
                        
                        <dt>'.__('Name and Surname','ycona').'</dt>
                        <dd>
                            <input type="text" name="testimonials_fields[testimonials]['.$c.'][name_surname]" placeholder="'.__('Write here','ycona').'..." class="regular-text" value="'.$name_surname.'">
                        </dd>
                        
                        <dt>
                            '.__('Location','ycona').'
                        </dt>
                        
                        <dd>
                            <input type="text" name="testimonials_fields[testimonials]['.$c.'][location]" placeholder="'.__('Write here','ycona').'..." class="regular-text" value="'.$location.'">
                        </dd>
                        
                        <dt>'.__('Tick Type Type','ycona').'</dt>
                        <dd>
                            <select name="testimonials_fields[testimonials]['.$c.'][tick_type]" class="testimonials-option">
                                <option value="green" '. selected($tick_type, "green", false) .'>' . esc_html__( 'Tick Green', 'webthinkershop' ) . '</option>
                                <option value="orange" '. selected($tick_type, "orange", false) .'>' . esc_html__( 'Tick Orange', 'webthinkershop' ) . '</option>
                                <option value="blue" '. selected($tick_type, "blue", false) .'>' . esc_html__( 'Tick Blue', 'webthinkershop' ) . '</option>
                            </select>
                        </dd>
                        
                        <div class="cpt-remove">
                            <button type="button" class="remove">'.__('Remove Testimonial', 'webthinkershop').'</button>
                        </div>
                    </dl>
                </div>
            </div>
        </div>';
                    $c = $c+1;
                }
            }

            ?>
        </div>
        <button type="button" class="add"><?php _e('Add Testimonial','webthinkershop'); ?></button>
    </div>

    <script>

        jQuery(document).ready(function() {

            jQuery("#wt-wrapper-testimonials + .add").click(function() {
                var $add_btn = jQuery(this);
                var $wrapper = $add_btn.prev(".wt-wrapper-cpt");
                $add_btn.hide();

                let count = get_existing_elements(".testimonials");

                var testimonials_html = `<div class="testimonials cpt-element" data-count="${count}">

                        <div class="sort-buttons">
                        <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                            <span class="dashicons dashicons-arrow-up-alt2"></span>
                        </button>
                    </div>

                    <div id="box-wrapper-${count}" class="testimonials-box cpt-box">

                        <div class="click-area">
                            <h3><?php echo esc_html__( 'Testimonial', 'webthinkershop' ); ?> #${count}</h3>
                        </div>

                        <div class="content-area link">
                            <dl>
                                <dt></dt>
                                <dd>
                                    <hr>
                                </dd>

                                <dt><?php _e('Image Profile','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="hidden" name="testimonials_fields[testimonials][${count}][image]" class="meta-image" value="">
                                    <input type="hidden" name="testimonials_fields[testimonials][${count}][imageId]" class="meta-image-id" value="">
                                    <input type="button" data-id="${count}" class="button image-upload" value="<?php _e('Browse','webthinkershop'); ?>">
                                    <input type="button" data-id="${count}" class="button image-upload-remove" value="<?php _e('Remove','webthinkershop'); ?>">
                                </dd>

                                <dt><?php _e('Icon Preview','webthinkershop'); ?></dt>
                                <dd>
                                    <div class="image-preview"><img src="" alt=""></div>
                                </dd>
                                
                                <dd>
                                    <hr>
                                </dd>

                               <div class="elements-hexagon-style">
                                      <dt><?php _e('Message', "webthinkershop"); ?></dt>
                                      <span id="box-row2-${count}-testimonials_fields_${count}_message"></span>
                               </div>
                                
                                
                                <dt><?php _e('Location','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="text" name="testimonials_fields[testimonials][${count}][location]" placeholder="<?php _e('Write here','webthinkershop'); ?>..." class="regular-text" value="">
                                </dd>

                                <dt><?php _e('Name and Surname','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="text" name="testimonials_fields[testimonials][${count}][name_surname]" placeholder="<?php _e('Write here','webthinkershop'); ?>..." class="regular-text" value="">
                                </dd>

                                <dt><?php _e("Tick Type","webthinkershop"); ?></dt>
                                <dd>
                                    <select name="testimonials_fields[testimonials][${count}][tick_type]">
                                        <option value="green"><?php esc_html_e( 'Tick Green', 'webthinkershop' ); ?></option>
                                        <option value="orange"><?php esc_html_e( 'Tick Orange', 'webthinkershop' ); ?></option>
                                        <option value="blue"><?php esc_html_e( 'Tick Blue', 'webthinkershop' ); ?></option>
                                    </select>
                                </dd>

                                <div class="cpt-remove">
                                    <button type="button" class="remove"><?php _e('Remove Testimonial', 'webthinkershop'); ?></button>
                                </div>

                            </dl>
                        </div>
                    </div>

                </div>`;
	            
	            $wrapper.append(testimonials_html);
	            
	            let target = "<?php echo admin_url('admin-ajax.php'); ?>";
	            
	            let create_wp_editor = function(editor_id, editor_name, $add_btn) {
		            let data_text = {
			            'action': 'wt_get_text_editor',
			            'text_editor_id': editor_id,
			            'textarea_name': editor_name
		            }
		            
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
	            }
	            
	            // message Editor
	            let message_id = "testimonials_fields_" + count + "_message";
	            let message_name = "testimonials_fields[testimonials][" + count + "][message]";

	            create_wp_editor(message_id, message_name, $add_btn);
				
                set_buttons();
                reset_sort();
            });

            set_buttons();
        });
    </script>
<?php }
/* END - Custom Post Type - testimonials */
