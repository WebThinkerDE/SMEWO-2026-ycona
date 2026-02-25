<?php

    if ( ! defined( '_S_VERSION' ) ) {
        // Replace the version number of the theme on each release.
        define( '_S_VERSION', '1.0.0' );
    }

    // Define global variable correctly
    global $theme_path;
    $theme_path = get_template_directory_uri();

    function add_frontend_resources() {
        global $theme_path; // Correct global variable usage

        // CSS
        wp_enqueue_style("boo-icons", $theme_path . "/assets/bootstrap-icons/font/bootstrap-icons.min.css", array("bootstrap-css"), _S_VERSION);
        wp_enqueue_style("bootstrap-css", $theme_path . "/assets/css/bootstrap/bootstrap.min.css", array(), "5.2.2");

        wp_enqueue_style("css-main", $theme_path . "/assets/css/main.css", array("bootstrap-css"), _S_VERSION);

        // Dynamic CSS files - loaded after main.css to override fallback colors
        $dynamic_colors_file = get_stylesheet_directory() . '/assets/custom-js-css/dynamic-colors.css';
        $custom_css_file = get_stylesheet_directory() . '/assets/custom-js-css/custom-styles.css';
        
        if (file_exists($dynamic_colors_file)) {
            wp_enqueue_style("dynamic-colors", $theme_path . "/assets/custom-js-css/dynamic-colors.css", array("css-main"), filemtime($dynamic_colors_file));
        }
        
        if (file_exists($custom_css_file)) {
            wp_enqueue_style("custom-styles", $theme_path . "/assets/custom-js-css/custom-styles.css", array("css-main"), filemtime($custom_css_file));
        }

        // Mini-cart / basket in header (styled for both WC and non-WC)
        wp_enqueue_style( "wt-shop-mini-cart", $theme_path . "/assets/css/mini-cart.css", array( "css-main" ), _S_VERSION );

        if ( function_exists( 'WC' ) ) {
            wp_enqueue_style( "wt-shop-woocommerce", $theme_path . "/assets/css/woocommerce.css", array( "css-main" ), _S_VERSION );

            // Swiper for related / upsell / cross-sell carousels
            wp_enqueue_style( "wt_swiper_css", $theme_path . "/assets/css/swiper/swiper-bundle.min.css", array( "css-main" ), _S_VERSION );
            wp_enqueue_script( "wt_swiper_js", $theme_path . "/assets/js/swiper/swiper-bundle.min.js", array( "js-main" ), _S_VERSION, true );

            wp_enqueue_script( "wt-shop-woocommerce-js", $theme_path . "/assets/js/woocommerce.min.js", array( "jquery", "js-main", "wt_swiper_js" ), _S_VERSION, true );

            $options_all = get_option( 'wt_shop_theme_options_all', array() );
            $recaptcha_site_key = isset( $options_all['recaptcha_site_key'] ) ? trim( (string) $options_all['recaptcha_site_key'] ) : '';
            if ( $recaptcha_site_key !== '' ) {
                wp_enqueue_script( 'wt-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true );
            }

            // AJAX product search
            wp_enqueue_style( "wt-shop-product-search", $theme_path . "/assets/css/product-search.css", array( "css-main" ), _S_VERSION );
            wp_enqueue_script( "wt-shop-product-search-js", $theme_path . "/assets/js/product-search.min.js", array( "jquery" ), _S_VERSION, true );
            wp_localize_script( "wt-shop-product-search-js", "wt_shop_search", array(
                'ajaxurl'          => admin_url( 'admin-ajax.php' ),
                'nonce'            => wp_create_nonce( 'wt_shop_product_search' ),
                'i18n_searching'   => __( 'Searching...', 'ycona' ),
                'i18n_no_results'  => __( 'No products found', 'webthinkershop' ),
                'i18n_error'       => __( 'Something went wrong. Please try again.', 'webthinkershop' ),
                'i18n_sku'         => __( 'SKU', 'webthinkershop' ),
            ) );
        }

        // JS
        wp_deregister_script("wp-embed");
        wp_enqueue_script("bootstrap-js", $theme_path . "/assets/js/bootstrap.min.js", array("jquery"), "5.2.2", true);

        wp_enqueue_script("js-main", $theme_path . "/assets/js/functions.min.js", array("jquery"), "1.0", true);

        // Custom video player (no native controls)
        wp_enqueue_style("custom-video-player", $theme_path . "/assets/video-player/custom-video-player.css", array(), _S_VERSION);
        wp_enqueue_script("custom-video-player", $theme_path . "/assets/video-player/custom-video-player.min.js", array(), _S_VERSION, true);

        // Dynamic JS file
        $custom_js_file = get_stylesheet_directory() . '/assets/custom-js-css/custom-scripts.js';
        if (file_exists($custom_js_file)) {
            wp_enqueue_script("custom-scripts", $theme_path . "/assets/custom-js-css/custom-scripts.js", array("jquery"), filemtime($custom_js_file), true);
        }

        wp_localize_script("js-main", "wt_ajax", array(
            "ajaxurl" => admin_url("admin-ajax.php"),
        ));

    }

    add_action("wp_enqueue_scripts", "add_frontend_resources");

    // load backend CSS and JS
    function add_backend_resources() {

        global $theme_path;
        // CSS
        wp_enqueue_style("admin-styles", $theme_path . "/assets/css/backend.css");
        
        // Dynamic colors for backend - loaded after backend.css to override fallback colors
        $dynamic_colors_file = get_stylesheet_directory() . '/assets/custom-js-css/dynamic-colors.css';
        if (file_exists($dynamic_colors_file)) {
            wp_enqueue_style("dynamic-colors-backend", $theme_path . "/assets/custom-js-css/dynamic-colors.css", array("admin-styles"), filemtime($dynamic_colors_file));
        }

        // JS - Make sure jQuery is loaded first
        wp_enqueue_script("jquery");
        wp_enqueue_script("admin-js", $theme_path . "/assets/js/backend.min.js", array('jquery'));

        wp_localize_script('admin-js', 'wt_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));

        // Enqueue WordPress color picker styles and scripts
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker', array('jquery'));

    }
    add_action('admin_enqueue_scripts', 'add_backend_resources');

    /* ── Load theme text domain for translations ── */
    function wt_shop_load_textdomain() {
        load_theme_textdomain( 'webthinkershop', get_template_directory() . '/languages' );
    }
    add_action( 'after_setup_theme', 'wt_shop_load_textdomain' );

    function register_my_menus()
    {
        register_nav_menus(
            array(
                'primary-menu' => __('Primary Menu'),
                'mobile-menu' => __('Mobile Menu'),
                'footer-menu-1' => __('Footer Menu 1'),
                'footer-menu-2' => __('Footer Menu 2'),
                'footer-menu-3' => __('Footer Menu 3'),
                'footer-menu-4' => __('Footer Menu 4'),
            )
        );
    }

    add_action('init', 'register_my_menus');

    // theme support options
    add_theme_support('menus');
    add_theme_support('post-thumbnails');
    add_image_size( 'product_nocrop', 800, 800, false ); // false = no crop
    add_theme_support('widgets');
    add_theme_support('custom-header', array('flex-height' => true, 'flex-width' => true));
    add_theme_support( 'title-tag' );


    // add backend script media
    function load_media_files() {
        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'load_media_files');


    // allow svg upload
    function allow_svg_types($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
    add_filter('upload_mimes', 'allow_svg_types');


    // Editor-Typ WYSIWYG
    function get_wp_editor($content, $editor_id, string $name, bool $without_ob = false, $wpautop = true ) {
        $settings = array(
            'media_buttons' => false,
            'teeny' => false,
            'textarea_rows' => 4,
            'textarea_name' => $name,
            'tinymce' => array(
                'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
                'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
            ),
            'wpautop' => $wpautop,
        );

        if($without_ob)
        {
            wp_editor( $content, $editor_id, $settings );
        }
        else
        {
            ob_start();

            wp_editor( $content, $editor_id, $settings );

            return ob_get_clean();
        }
    }
        // AJAX call wp_editor
        add_action('wp_ajax_wt_get_text_editor', 'wt_get_text_editor');

        function wt_get_text_editor() {
            // Check if the required parameters are set
            if(isset($_POST['text_editor_id']) && isset($_POST['textarea_name'])) {
                // Sanitize the received parameters
                $editor_id = sanitize_text_field($_POST['text_editor_id']);
                $textarea_name = sanitize_text_field($_POST['textarea_name']);


                // Set settings for the editor
                $settings = array(
                    'media_buttons' => false,  // Show the media buttons
                    'textarea_name' => $textarea_name,  // Set the name
                    'textarea_rows' => 8, // Set text area rows
                    'teeny' => false,
                    'tinymce' => array(
                        'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
                    ),
                );

                // Generate the editor
                wp_editor('', $editor_id, $settings);
            }
            wp_die(); // this is required to terminate immediately and return a proper response
        }

    // END of ajax call for wp_editor


    // Desktop Walker
    class Desktop_Walker_Nav_Menu extends Walker_Nav_Menu
    {
        function start_lvl(&$output, $depth = 0, $args = array()) {
            $indent = str_repeat("\t", $depth);
            if ($depth === 0) {
                $submenu_class = 'sub-menu';
            } elseif ($depth === 1) {
                $submenu_class = 'sub-sub-menu';
            } else {
                $submenu_class = 'sub-sub-sub-menu';
            }
            $output .= "\n$indent<ul class=\"dropdown-menu $submenu_class depth_$depth\">\n";
        }

        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
            $indent = ($depth) ? str_repeat("\t", $depth) : '';
            $li_attributes = '';
            $class_names = $value = '';

            $classes = empty($item->classes) ? array() : (array) $item->classes;
            $classes[] = ($args->walker->has_children) ? 'dropdown' : '';

            if ($item->current) {
                $classes[] = 'active';
            }

            $classes[] = 'menu-item-' . $item->ID;
            if ($depth && $args->walker->has_children) {
                $classes[] = 'dropdown-submenu';
            }

            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
            $class_names = ' class="' . esc_attr($class_names) . '"';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
            $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

            $atts = array();
            $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
            $atts['href'] = !empty($item->url) ? $item->url : '';

            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $item_output = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= '<span class="link-drop-down">';  // Add a span tag to wrap the link text
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</span>';  // Close the span tag
            $item_output .= ($args->walker->has_children) ? ' <span class="arrow-wt-shop-desktop"></span></a>' : '</a>';
            $item_output .= $args->after;

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }


    class Mobile_Walker_Nav_Menu extends Walker_Nav_Menu
    {
        function start_lvl(&$output, $depth = 0, $args = array()) {
            $indent = str_repeat("\t", $depth);
            if ($depth === 0) {
                $submenu_class = 'sub-menu';
            } elseif ($depth === 1) {
                $submenu_class = 'sub-sub-menu';
            } else {
                $submenu_class = 'sub-sub-sub-menu';
            }
            $output .= "\n$indent<ul class=\"dropdown-menu $submenu_class depth_$depth\">\n";
        }

        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
        {
            $indent = ($depth) ? str_repeat("\t", $depth) : '';
            $li_attributes = '';
            $class_names = $value = '';

            $classes = empty($item->classes) ? array() : (array)$item->classes;
            $classes[] = ($args->walker->has_children) ? 'dropdown' : '';

            if ($item->current) {
                $classes[] = 'active';
            }

            $classes[] = 'menu-item-' . $item->ID;
            if ($depth && $args->walker->has_children) {
                $classes[] = 'dropdown-submenu';
            }

            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
            $class_names = ' class="' . esc_attr($class_names) . '"';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
            $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

            $atts = array();
            $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';

            $atts['href'] = !empty($item->url) ? $item->url : '';

            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            // Define different arrow classes based on depth
            $arrow_class = '';
            if ($args->walker->has_children) {
                if ($depth == 0) {
                    $arrow_class = 'mega-menu-mobile-arrow';
                } elseif ($depth == 1) {
                    $arrow_class = 'sub-mega-menu-mobile-arrowe';
                } else {
                    $arrow_class = 'sub-sub-mega-menu-mobile-arrow';
                }
            }

            $item_output = $args->before;

            if ($args->walker->has_children) {
                $item_output .= '<span class="menu-item-wrap">';
            }

            $item_output .= '<a class="nav-link-mob"' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a>';

            if ($args->walker->has_children) {
                $item_output .= '<div class="' . $arrow_class . '">
                        <img alt="menu-open" src="/wp-content/themes/webthinkershop/assets/img/vectors/OpendropdownMenu.svg" class="arrow-menu-open" /> 
                        <img alt="menu-close" src="/wp-content/themes/webthinkershop/assets/img/vectors/ClosedropdownMenu.svg" class="d-none arrow-menu-close" /> 
                </div>';
                $item_output .= '</span>';
            }

            $item_output .= $args->after;

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }

    // register custom-block category
    function add_block_category( $categories, $post ) {

        return array_merge(
            array(
                array(
                    'slug' => 'wt-shop-blocks',
                    'title' => __( 'webthinkershop', 'webthinkershop' ),
                ),
            ),
            $categories
        );
    }
    add_filter( 'block_categories_all', 'add_block_category', 10, 2);


    // register webthinkershop custom-blocks
    function register_wt_shop_blocks() {

        // abort if gutenberg is not active
        if(!function_exists( 'register_block_type'))
        {
            return;
        }

        // init vars
        global $theme_path;
        $blocks = array(
            array("name" => "image_block", "block-name" => "image-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "button_block", "block-name" => "button-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "multiple_buttons_block", "block-name" => "multiple-buttons-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "text_block", "block-name" => "text-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "accordion_block", "block-name" => "accordion-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "testimonials_block", "block-name" => "testimonials-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "headline_block", "block-name" => "headline-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "video_block", "block-name" => "video-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "cards_block", "block-name" => "cards-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "slider_block", "block-name" => "slider-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "you_may_like_block", "block-name" => "you-may-like-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data","wp-components")),
            array("name" => "grid_products_block", "block-name" => "grid-products-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data","wp-components")),
            array("name" => "cta_block", "block-name" => "cta-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),
            array("name" => "split_block", "block-name" => "split-block", "deps" => array("wp-block-editor","wp-blocks","wp-element","wp-data")),


        );

        // iterate blocks
        foreach($blocks as $block) {

            // register script
            wp_register_script(
                "wt_".$block["name"],
                $theme_path."/wt-blocks/".$block["block-name"]."/".$block["name"]."_editor.min.js",
                $block["deps"]
            );

            // register editor style
            wp_register_style(
                "wt_".$block["name"]."_editor",
                $theme_path."/wt-blocks/".$block["block-name"]."/".$block["name"]."_editor.css",
                array("wp-edit-blocks")
            );

            // register block
            $check = register_block_type(
                "wt/".$block["block-name"], array(
                "style" => "wt_".$block["name"],
                "editor_style" => "wt_".$block["name"]."_editor",
                "editor_script" => "wt_".$block["name"],
                "render_callback" => "wt_".$block["name"]."_rc"
            ));

            // include php return call function
            include_once(dirname(__FILE__) . "/wt-blocks/" .$block["block-name"]."/".$block["name"].".php");
        }
    }
    add_action('init', 'register_wt_shop_blocks');

/**
 * Force apiVersion 3 for WPML blocks to remove block editor deprecation warnings.
 * Only applies if WPML registers blocks via block.json; otherwise update WPML plugin.
 */
function wt_block_type_metadata_api3( $metadata ) {
    if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
        return $metadata;
    }
    if ( in_array( $metadata['name'], array( 'wpml/language-switcher', 'wpml/navigation-language-switcher' ), true ) ) {
        $metadata['apiVersion'] = 3;
    }
    return $metadata;
}
add_filter( 'block_type_metadata', 'wt_block_type_metadata_api3', 10, 1 );

    /* use archive.php for posts */
    function use_archive_for_posts_page( $template ) {
        if ( is_home() ) {
            // Redirect to archive.php when viewing the posts page
            $template = locate_template( 'archive.php' );
        }
        return $template;
    }
    add_filter( 'home_template', 'use_archive_for_posts_page' );
    /* end of archive.php for posts */


    // saves metas for all CPT
    function save_custom_post_metas( $post_id, $meta_nonce, $save_fields, $fields ) {

        // check if POST exist
        if( !$_POST )
        {
            return $post_id;
        }

        if( !isset( $_POST[$meta_nonce] ) )
        {
            return $post_id;
        }

        // verify nonce
        if ( !wp_verify_nonce( $_POST[$meta_nonce], $save_fields ) )
        {
            return $post_id;
        }

        // check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        {
            return $post_id;
        }

        // check permissions
        if ( 'page' === $_POST['post_type'] )
        {
            if ( !current_user_can( 'edit_page', $post_id ) )
            {
                return $post_id;
            }
            elseif ( !current_user_can( 'edit_post', $post_id ) )
            {
                return $post_id;
            }
        }

        $old = get_post_meta( $post_id, $fields, true );
        $new = $_POST[$fields];

        //  Update or delete
        if ( $new && $new !== $old ) {
            update_post_meta( $post_id, $fields, $new );
        } elseif ( empty($new) && !empty($old) ) {
            delete_post_meta( $post_id, $fields );
        }

        return $post_id;
    }


    /**
     * [custom_video] shortcode — outputs the same player markup as wt/video-block.
     *
     * Attributes:
     *   src              (required) — video URL
     *   poster           (optional) — poster image URL
     *   preload          (optional) — preload strategy (default: metadata)
     *   title            (optional) — heading above the player
     *   skin             (optional) — skin class: ocean | cinema | minimal
     *   width            (optional) — CSS width  (e.g. 800px, 100%, 50vw)
     *   height           (optional) — CSS height (e.g. 450px, auto)
     *   class            (optional) — extra CSS classes on the wrapper
     *   subtitle_1       (optional) — URL to first subtitle file (.vtt)
     *   subtitle_1_lang  (optional) — language code for first subtitle (default: en)
     *   subtitle_1_label (optional) — display label for first subtitle (default: English)
     *   subtitle_2       (optional) — URL to second subtitle file (.vtt)
     *   subtitle_2_lang  (optional) — language code for second subtitle
     *   subtitle_2_label (optional) — display label for second subtitle
     *   subtitle_3       (optional) — URL to third subtitle file (.vtt)
     *   subtitle_3_lang  (optional) — language code for third subtitle
     *   subtitle_3_label (optional) — display label for third subtitle
     *   audio_1          (optional) — URL to first audio track (.mp3, .ogg, .aac, etc.)
     *   audio_1_lang     (optional) — language code for first audio track
     *   audio_1_label    (optional) — display label for first audio track
     *   audio_2          (optional) — URL to second audio track
     *   audio_2_lang     (optional) — language code for second audio track
     *   audio_2_label    (optional) — display label for second audio track
     *   audio_3          (optional) — URL to third audio track
     *   audio_3_lang     (optional) — language code for third audio track
     *   audio_3_label    (optional) — display label for third audio track
     */
    function wt_shop_custom_video_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'src'              => '',
            'poster'           => '',
            'preload'          => 'metadata',
            'title'            => '',
            'skin'             => '',
            'width'            => '',
            'height'           => '',
            'class'            => '',
            'subtitle_1'       => '',
            'subtitle_1_lang'  => 'en',
            'subtitle_1_label' => 'English',
            'subtitle_2'       => '',
            'subtitle_2_lang'  => '',
            'subtitle_2_label' => '',
            'subtitle_3'       => '',
            'subtitle_3_lang'  => '',
            'subtitle_3_label' => '',
            'audio_1'          => '',
            'audio_1_lang'     => '',
            'audio_1_label'    => '',
            'audio_2'          => '',
            'audio_2_lang'     => '',
            'audio_2_label'    => '',
            'audio_3'          => '',
            'audio_3_lang'     => '',
            'audio_3_label'    => '',
        ), $atts, 'custom_video' );

        if ( empty( $atts['src'] ) ) {
            return '<p class="video-block-empty">' . esc_html__( 'Please provide a video URL (src).', 'webthinkershop' ) . '</p>';
        }

        // Enqueue player assets (same as video block)
        global $theme_path;
        wp_enqueue_style( 'custom-video-player', $theme_path . '/assets/video-player/custom-video-player.css', array(), _S_VERSION );
        wp_enqueue_script( 'custom-video-player', $theme_path . '/assets/video-player/custom-video-player.min.js', array(), _S_VERSION, true );

        // Build attributes
        $poster_attr = ! empty( $atts['poster'] ) ? ' poster="' . esc_url( $atts['poster'] ) . '"' : '';
        $title_attr  = ! empty( $atts['title'] )  ? ' title="' . esc_attr( $atts['title'] ) . '"' : '';

        $skin_class  = ! empty( $atts['skin'] )  ? ' video-player-skin-' . sanitize_html_class( $atts['skin'] ) : '';
        $extra_class = ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

        // Width & height — accept any CSS value (px, %, vw, auto, etc.)
        $dimension_styles = '';
        if ( ! empty( $atts['width'] ) ) {
            $dimension_styles .= 'width:' . esc_attr( trim( $atts['width'] ) ) . ';';
        }
        if ( ! empty( $atts['height'] ) ) {
            $dimension_styles .= 'height:' . esc_attr( trim( $atts['height'] ) ) . ';';
        }
        $style_attr = $dimension_styles !== '' ? ' style="' . $dimension_styles . '"' : '';

        $title_html  = ! empty( $atts['title'] ) ? '<h2 class="video-block-title">' . esc_html( $atts['title'] ) . '</h2>' : '';

        // Build subtitle <track> elements (up to 3)
        $track_html = '';
        for ( $i = 1; $i <= 3; $i++ ) {
            $sub_url   = $atts[ 'subtitle_' . $i ];
            $sub_lang  = $atts[ 'subtitle_' . $i . '_lang' ];
            $sub_label = $atts[ 'subtitle_' . $i . '_label' ];

            if ( ! empty( $sub_url ) ) {
                $sub_lang  = ! empty( $sub_lang )  ? $sub_lang  : 'en';
                $sub_label = ! empty( $sub_label ) ? $sub_label : 'Subtitles ' . $i;
                $track_html .= '<track kind="subtitles" src="' . esc_url( $sub_url ) . '" srclang="' . esc_attr( $sub_lang ) . '" label="' . esc_attr( $sub_label ) . '">';
            }
        }
        $crossorigin_attr = ! empty( $track_html ) ? ' crossorigin="anonymous"' : '';

        // Build audio tracks data attribute (up to 3)
        $audio_tracks_data = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $a_url   = $atts[ 'audio_' . $i ];
            $a_lang  = $atts[ 'audio_' . $i . '_lang' ];
            $a_label = $atts[ 'audio_' . $i . '_label' ];

            if ( ! empty( $a_url ) ) {
                $audio_tracks_data[] = array(
                    'url'   => esc_url( $a_url ),
                    'label' => ! empty( $a_label ) ? $a_label : 'Audio ' . $i,
                    'lang'  => ! empty( $a_lang )  ? $a_lang  : '',
                );
            }
        }
        $audio_tracks_attr = '';
        if ( ! empty( $audio_tracks_data ) ) {
            $audio_tracks_attr = " data-audio-tracks='" . wp_json_encode( $audio_tracks_data ) . "'";
        }

        $markup = '<section class="video-block container">' . $title_html . '
        <div class="video-player video-player-skeleton' . $skin_class . $extra_class . '" data-player' . $style_attr . $audio_tracks_attr . '>

            <div class="vp-skeleton" aria-hidden="true">
                <div class="vp-skeleton-shimmer"></div>
                <div class="vp-skeleton-play"></div>
                <div class="vp-skeleton-controls">
                    <div class="vp-skeleton-btn"></div>
                    <div class="vp-skeleton-bar"></div>
                    <div class="vp-skeleton-time"></div>
                    <div class="vp-skeleton-btn"></div>
                    <div class="vp-skeleton-btn-sm"></div>
                    <div class="vp-skeleton-btn"></div>
                </div>
            </div>

            <video src="' . esc_url( $atts['src'] ) . '" preload="' . esc_attr( $atts['preload'] ) . '" playsinline' . $poster_attr . $title_attr . $crossorigin_attr . '>' . $track_html . '</video>

            <div class="video-player-play-overlay" data-play-overlay aria-label="' . esc_attr__( 'Play Video', 'webthinkershop' ) . '">
                <i class="bi bi-play-fill" aria-hidden="true"></i>
            </div>

            <div class="video-player-captions" data-captions aria-live="polite" aria-atomic="true"></div>

            <div class="video-player-controls" data-controls>
                <button type="button" class="video-player-btn" data-play aria-label="' . esc_attr__( 'Play', 'webthinkershop' ) . '">
                    <i class="bi bi-play-fill" aria-hidden="true"></i>
                    <i class="bi bi-pause-fill" aria-hidden="true"></i>
                    <span class="sr-only">' . esc_html__( 'Play / Pause', 'webthinkershop' ) . '</span>
                </button>
                <div class="video-player-progress-wrap" data-progress-wrap>
                    <input type="range" class="video-player-progress" data-progress min="0" max="100" value="0" step="0.1" aria-label="' . esc_attr__( 'Seek', 'webthinkershop' ) . '">
                </div>
                <span class="video-player-time" data-time aria-live="off">0:00</span>
                <button type="button" class="video-player-btn" data-mute aria-label="' . esc_attr__( 'Mute', 'webthinkershop' ) . '">
                    <i class="bi bi-volume-up-fill" aria-hidden="true"></i>
                    <i class="bi bi-volume-mute-fill" aria-hidden="true"></i>
                    <span class="sr-only">' . esc_html__( 'Mute / Unmute', 'webthinkershop' ) . '</span>
                </button>
                <input type="range" class="video-player-volume" data-volume min="0" max="100" value="100" step="1" aria-label="' . esc_attr__( 'Volume', 'webthinkershop' ) . '">
                <button type="button" class="video-player-btn" data-cc aria-label="' . esc_attr__( 'Captions', 'webthinkershop' ) . '">
                    <span>CC</span>
                    <span class="sr-only">' . esc_html__( 'Toggle Captions', 'webthinkershop' ) . '</span>
                </button>
                <button type="button" class="video-player-btn" data-quality aria-label="' . esc_attr__( 'Quality', 'webthinkershop' ) . '" style="display:none">
                    <i class="bi bi-gear-fill" aria-hidden="true"></i>
                    <span class="vp-quality-label">AUTO</span>
                </button>
                <button type="button" class="video-player-btn" data-fullscreen aria-label="' . esc_attr__( 'Fullscreen', 'webthinkershop' ) . '">
                    <i class="bi bi-fullscreen" aria-hidden="true"></i>
                    <i class="bi bi-fullscreen-exit" aria-hidden="true"></i>
                    <span class="sr-only">' . esc_html__( 'Toggle Fullscreen', 'webthinkershop' ) . '</span>
                </button>
            </div>

            <div class="video-player-loading" data-loading aria-hidden="true" role="status">
                <span class="sr-only">' . esc_html__( 'Loading...', 'webthinkershop' ) . '</span>
            </div>

            <div class="video-player-error" data-error aria-live="assertive" hidden></div>
        </div>
    </section>';

        return $markup;
    }
    add_shortcode( 'custom_video', 'wt_shop_custom_video_shortcode' );


    // include webthinkershop utilities
    include_once("wt-utilities.php");

    // cpt
    include_once("wt-cpt.php");
    require_once(get_stylesheet_directory() . '/wt-cpt/accordion.php');
	require_once(get_stylesheet_directory() . '/wt-cpt/testimonials.php');
	require_once(get_stylesheet_directory() . '/wt-cpt/cards.php');
    require_once(get_stylesheet_directory() . '/wt-cpt/slider.php');

    if ( class_exists( 'WooCommerce' ) ) {
        /* Load from template (parent) theme so hooks work with or without child theme */
        $woo_path = get_template_directory() . '/woocommerce/hooks/';

        $files = [
            'support-woocommerce.php',
            'mini-cart.php',
            'login-register.php',
            'my-account-extra-menu-item.php',
            'modal-login-register.php',
            'ajax-product-search.php',
            'product-custom-tabs.php',
        ];

        foreach ( $files as $file ) {
            if ( file_exists( $woo_path . $file ) ) {
                require_once $woo_path . $file;
            }
        }
    }


// load theme options
    require_once(get_stylesheet_directory() . '/theme-options.php');

    // Initialize dynamic files on theme activation
    function initialize_dynamic_files() {
        if (function_exists('theme_generate_dynamic_files')) {
            theme_generate_dynamic_files();
        }
    }
    add_action('after_switch_theme', 'initialize_dynamic_files');
    
    // Generate files immediately when theme loads (not just on activation)
    function ensure_dynamic_files_exist() {
        if (function_exists('theme_generate_dynamic_files')) {
            $theme_dir = get_stylesheet_directory() . '/assets/custom-js-css/';
            $color_file = $theme_dir . 'dynamic-colors.css';
            
            // Generate files if they don't exist
            if (!file_exists($color_file)) {
                theme_generate_dynamic_files();
            }
        }
    }
    add_action('init', 'ensure_dynamic_files_exist');

    // Generate files when theme options are saved
    function generate_files_on_save() {
        if (function_exists('theme_generate_dynamic_files')) {
            theme_generate_dynamic_files();
        }
    }
    add_action('update_option_wt_shop_theme_options_all', 'generate_files_on_save');
    add_action('update_option_wt_shop_theme_options_en', 'generate_files_on_save');
    add_action('update_option_wt_shop_theme_options_de', 'generate_files_on_save');

