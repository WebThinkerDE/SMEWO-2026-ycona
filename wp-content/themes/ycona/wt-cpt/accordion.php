<?php
/* Custom Post Type - Accordion */

function show_accordion_custom_fields() {

    $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
    $css_src = includes_url('css/') . 'editor.css';

    wp_register_style('tinymce_css', $css_src);
    wp_enqueue_style('tinymce_css');

    global $post;
    $meta = get_post_meta($post->ID,'accordion_fields',true);
    $c = 0;

    ?>

    <script src="<?php echo $js_src; ?>"></script>
    <div>

        <input type="hidden" name="accordionMetaNonce" value="<?php echo wp_create_nonce( "saveAccordionFields" ); ?>">

        <div id="wt-wrapper-accordion" class="wt-wrapper-cpt">

            <?php

            if ( is_array($meta) && count( $meta ) > 0 )
            {
                foreach( $meta["accordions"]  as $track )
                {
                    $headline               = $track["headline"] ?? "";
                    $headline_type          = $track["headline_type"] ?? "p";
                    $content                = $track["content"] ?? "";
                    $add_content_position   = $track["add_content_position"] ?? "";
                    $add_content            = $track["add_content"] ?? "";

                    if ($headline == "o")
                    {
                        continue;
                    }

                    echo '<div class="accordion cpt-element" data-count="'.$c.'">

                            <div class="sort-buttons">
                                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                                </button>
                            </div>
            
                            <div id="box-wrapper-'.$c.'" class="accordion-box cpt-box">
                                
                                <div class="click-area">
                                    <h3>' . esc_html( sprintf( __( 'Accordion #%d', 'ycona' ), $c + 1 ) ) . '</h3>
                                </div>
                                
                                <div class="content-area">
                                    <dl>
                                    
                                        <dt></dt>
                                        <dd>
                                            <hr>
                                        </dd>
                                        
                                        <dt>'.__("Accordion Title",'ycona').'</dt>
                                        <dd>
                                            <input type="text" name="accordion_fields[accordions]['.$c.'][headline]" placeholder="'.__('Write here','ycona').'..." class="regular-text" value="'.$headline.'">
                                        </dd>
                                        
                                        <dt>'.__('Überschriftentyp','ycona').'</dt>
                                        <dd>
                                            <select name="accordion_fields[accordions]['.$c.'][headline_type]" class="slider-option">   
                                               <option value="p" '. selected($headline_type, "p", false) .'>p</option>
                                               <option value="h1" '. selected($headline_type, "h1", false) .'>h1</option>                                             
                                               <option value="h2" '. selected($headline_type, "h2", false) .'>h2</option>
                                               <option value="h3" '. selected($headline_type, "h3", false) .'>h3</option>
                                             </select>
                                        </dd>
                    
                                        <dt>'.__('Content','ycona').'</dt>
                                        <dd>
                                            '.get_wp_editor($content, "accordion_fields_" . $c . "_content", "accordion_fields[accordions][" . $c . "][content]").'
                                        </dd>
                                        
                                        <div class="cpt-remove">
                                            <button type="button" class="remove" data-type="cpt-element">'.__('Remove Accordion', 'ycona').'</button>
                                        </div>
                                    </dl>
                                </div>
                                
                            </div>
                            
                        </div>';
                    $c = $c+1;
                }
            }?>

        </div>
        <button type="button" class="add" id="add_shortcode"><?php _e('Add Accordion','ycona'); ?></button>
    </div>

    <script>

        jQuery(document).ready(function() {

            jQuery("#wt-wrapper-accordion + .add").click(function() {

                var $add_btn = jQuery(this);
                var $wrapper = $add_btn.prev(".wt-wrapper-cpt");
                $add_btn.hide();

                let count = get_existing_elements(".accordion");

                var accordion_html = `<div class="accordion cpt-element" data-count="${count}">

                <div class="sort-buttons">
                    <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-down">
                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary float-right mr-1 sort-up">
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                    </button>
                </div>

                <div id="box-wrapper-${count}" class="accordion-box cpt-box">

                    <div class="click-area">
                        <h3><?php echo esc_html__( 'Accordion', 'ycona' ); ?> #${count}</h3>
                    </div>

                    <div class="content-area">
                        <dl>

                            <dt></dt>
                            <dd>
                                <hr>
                            </dd>

                            <dt><?php _e('Accordion Title','ycona'); ?></dt>
                            <dd>
                                <input type="text" name="accordion_fields[accordions][${count}][headline]" placeholder="<?php _e('Write here','ycona'); ?>..." class="regular-text" value="">
                            </dd>

                             <dt><?php _e("Überschriftentyp","ycona"); ?></dt>
                             <dd>
                                <select name="accordion_fields[accordions][${count}][headline_type]">
                                                <option value="p">p</option>
                                                <option value="h1">h1</option>
                                                <option value="h2">h2</option>
                                                <option value="h3">h3</option>
                                </select>
                             </dd>
                            <dt><?php _e('Content','ycona'); ?></dt>
                                <dd>
                                 <span id="box-${count}-accordion_fields_${count}_content">    </span>
                                </dd>
                            <dd>
                            </dd>

                            <div class="cpt-remove">
                                <button type="button" class="remove" data-type="cpt-element"><?php _e('Remove Accordion', 'ycona'); ?></button>
                            </div>

                        </dl>

                    </div>
                </div>
            </div>`;


                $wrapper.append(accordion_html);

                let target = "<?php echo admin_url('admin-ajax.php'); ?>";

                let create_wp_editor = function(editor_id, editor_name, $add_btn) {
                    let data_text = {
                        'action': 'wt_get_text_editor',
                        'text_editor_id': editor_id,
                        'textarea_name': editor_name
                    }

                    jQuery.post(target, data_text)
                        .done(function (response) {
                            let cont = "span#box-" + count + "-" + editor_id;
                            jQuery(cont).append(response);
                            if (typeof tinymce !== "undefined") tinymce.execCommand('mceAddEditor', false, editor_id);
                            if (typeof quicktags !== "undefined") quicktags({id: editor_id});
                        })
                        .always(function() {
                            $add_btn.show();
                        });
                }

                // Content Editor
                let content_id = "accordion_fields_" + count + "_content";
                let content_name = "accordion_fields[accordions][" + count + "][content]";
                create_wp_editor(content_id, content_name, $add_btn);

                set_buttons();
                reset_sort();

            });

            set_buttons();
        });
    </script>
<?php }
/* END - Custom Post Type - Accordion */