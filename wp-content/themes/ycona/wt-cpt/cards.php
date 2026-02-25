<?php
/* Custom Post Type - Cards */

function show_cards_custom_fields() {

    $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
    $css_src = includes_url('css/') . 'editor.css';

    wp_register_style('tinymce_css', $css_src);
    wp_enqueue_style('tinymce_css');

    global $post;

    $meta = get_post_meta($post->ID,'cards_fields',true);
    $c = 0;

    ?>

    <script src="<?php echo $js_src; ?>" type="text/javascript"></script>
    <div>

        <input type="hidden" name="cardsMetaNonce" value="<?php echo wp_create_nonce( "saveCardsFields" ); ?>">

        <div class="cards-settings">
            <h3><?php _e( 'Cards', 'ycona' ); ?></h3>
        </div>

        <div id="wt-wrapper-cards" class="wt-wrapper-cpt">

            <?php

            if ( is_array($meta) && count( $meta ) > 0 )
            {
                foreach( $meta["cards"] as $card )
                {
                    $card_title         = $card["card_title"] ?? '';
                    $description        = $card["description"] ?? '';
                    $link_url           = $card["link_url"] ?? '';
                    $link_text          = $card["link_text"] ?? '';
                    $img_src             = $card["image"] ?? '';
                    $img_id              = $card["imageId"] ?? '';

                    echo '<div class="card-item cpt-element" data-count="'.$c.'">

            <div class="sort-buttons">
                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                </button>
            </div>
            
            <div id="box-wrapper-'.$c.'" class="cards-box cpt-box">
                
                <div class="click-area">
                    <h3>' . esc_html( sprintf( __( 'Card #%d', 'ycona' ), $c + 1 ) ) . '</h3>
                </div>
                
                <div class="content-area">
                    <dl>
                        <dt></dt>
                        <dd>
                            <hr>
                        </dd>
                        
                        <dt>'.__('Image','ycona').'</dt>
                        <dd>
                            <input type="hidden" name="cards_fields[cards]['.$c.'][image]" class="meta-image" value="'.$img_src.'">
                            <input type="hidden" name="cards_fields[cards]['.$c.'][imageId]" class="meta-image-id" value="'.$img_id.'">
                            <input type="button" data-id="'.$c.'" class="button image-upload" value="'.__('Browse','webthinkershop').'">
                            <input type="button" class="button image-upload-remove" data-id="'.$c.'" value="'.__('Remove','webthinkershop').'">
                        </dd>
                        
                        <dt>'.__('Image Preview','webthinkershop').'</dt>
                        <dd>
                            <div class="image-preview"><img src="'.$img_src.'" alt=""></div>
                        </dd>
                        
                        <dd>
                            <hr>
                        </dd>
                        
                        <dt>'.__('Card Title','webthinkershop').'</dt>
                        <dd>
                            <input type="text" name="cards_fields[cards]['.$c.'][card_title]" placeholder="'.__('Write here','webthinkershop').'..." class="regular-text" value="'.$card_title.'">
                        </dd>

                        <div class="elements-hexagon-style">
                            <dt>' . __('Description', 'webthinkershop') . '</dt>
                            ' . get_wp_editor($description, "cards_fields_description_$c", "cards_fields[cards][" . $c . "][description]") . '
                        </div>
                        
                        <dt>'.__('Link URL','webthinkershop').'</dt>
                        <dd>
                            <input type="text" name="cards_fields[cards]['.$c.'][link_url]" placeholder="https://..." class="regular-text" value="'.$link_url.'">
                        </dd>
                        
                        <dt>'.__('Link Text','webthinkershop').'</dt>
                        <dd>
                            <input type="text" name="cards_fields[cards]['.$c.'][link_text]" placeholder="'.__('Write here','webthinkershop').'..." class="regular-text" value="'.$link_text.'">
                        </dd>
                        
                        <div class="cpt-remove">
                            <button type="button" class="remove">'.__('Remove Card', 'webthinkershop').'</button>
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
        <button type="button" class="add"><?php _e('Add Card','webthinkershop'); ?></button>
    </div>

    <script>

        jQuery(document).ready(function() {

            jQuery("#wt-wrapper-cards + .add").click(function() {
                var $add_btn = jQuery(this);
                var $wrapper = $add_btn.prev(".wt-wrapper-cpt");
                $add_btn.hide();

                let count = get_existing_elements(".card-item");

                var card_html = `<div class="card-item cpt-element" data-count="${count}">

                        <div class="sort-buttons">
                        <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                            <span class="dashicons dashicons-arrow-up-alt2"></span>
                        </button>
                    </div>

                    <div id="box-wrapper-${count}" class="cards-box cpt-box">

                        <div class="click-area">
                            <h3><?php echo esc_html__( 'Card', 'webthinkershop' ); ?> #${count}</h3>
                        </div>

                        <div class="content-area link">
                            <dl>
                                <dt></dt>
                                <dd>
                                    <hr>
                                </dd>

                                <dt><?php _e('Image','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="hidden" name="cards_fields[cards][${count}][image]" class="meta-image" value="">
                                    <input type="hidden" name="cards_fields[cards][${count}][imageId]" class="meta-image-id" value="">
                                    <input type="button" data-id="${count}" class="button image-upload" value="<?php _e('Browse','webthinkershop'); ?>">
                                    <input type="button" data-id="${count}" class="button image-upload-remove" value="<?php _e('Remove','webthinkershop'); ?>">
                                </dd>

                                <dt><?php _e('Image Preview','webthinkershop'); ?></dt>
                                <dd>
                                    <div class="image-preview"><img src="" alt=""></div>
                                </dd>
                                
                                <dd>
                                    <hr>
                                </dd>

                                <dt><?php _e('Card Title','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="text" name="cards_fields[cards][${count}][card_title]" placeholder="<?php _e('Write here','webthinkershop'); ?>..." class="regular-text" value="">
                                </dd>

                               <div class="elements-hexagon-style">
                                      <dt><?php _e('Description', 'webthinkershop'); ?></dt>
                                      <span id="box-row2-${count}-cards_fields_${count}_description"></span>
                               </div>

                                <dt><?php _e('Link URL','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="text" name="cards_fields[cards][${count}][link_url]" placeholder="https://..." class="regular-text" value="">
                                </dd>

                                <dt><?php _e('Link Text','webthinkershop'); ?></dt>
                                <dd>
                                    <input type="text" name="cards_fields[cards][${count}][link_text]" placeholder="<?php _e('Write here','webthinkershop'); ?>..." class="regular-text" value="">
                                </dd>

                                <div class="cpt-remove">
                                    <button type="button" class="remove"><?php _e('Remove Card', 'webthinkershop'); ?></button>
                                </div>

                            </dl>
                        </div>
                    </div>

                </div>`;
	            
	            $wrapper.append(card_html);
	            
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
	            
	            // Description Editor
	            let description_id = "cards_fields_" + count + "_description";
	            let description_name = "cards_fields[cards][" + count + "][description]";
	            
	            create_wp_editor(description_id, description_name, $add_btn);
				
                set_buttons();
                reset_sort();
            });

            set_buttons();
        });
    </script>
<?php }
/* END - Custom Post Type - Cards */
