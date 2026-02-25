<?php

	/* Add Custom Post Type - Accordion */
	function add_custom_post_type_accordion() {
		
		$labels = array(
			'name' => _x( 'Accordion', 'Post Type General Name', 'ycona' ),
			'singular_name' => _x( 'Accordion', 'Post Type Singular Name', 'ycona' ),
			'menu_name' => __( 'Accordion', 'ycona' ),
			'name_admin_bar' => __( 'Accordion', 'ycona' ),
			'archives' => __( 'Accordion Archives', 'ycona' ),
			'attributes' => __( 'Accordion Attributes', 'ycona' ),
			'parent_item_colon' => __( 'Parent Accordion:', 'ycona' ),
			'all_items' => __( 'All Accordions ', 'ycona' ),
			'add_new_item' => __( 'Add New Accordion', 'ycona' ),
			'add_new' => __( 'Add New', 'ycona' ),
			'new_item' => __( 'New Accordion', 'ycona' ),
			'edit_item' => __( 'Edit Accordion', 'ycona' ),
			'update_item' => __( 'Update Accordion', 'ycona' ),
			'view_item' => __( 'View Accordion', 'ycona' ),
			'view_items' => __( 'View Accordions', 'ycona' ),
			'search_items' => __( 'Search Accordion', 'ycona' ),
			'not_found' => __( 'Not found', 'ycona' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'ycona' ),
			'featured_image' => __( 'Accordion Image', 'ycona' ),
			'set_featured_image' => __( 'Set Accordion image', 'ycona' ),
			'remove_featured_image' => __( 'Remove Accordion image', 'ycona' ),
			'use_featured_image' => __( 'Use as Accordion image', 'webthinkershop' ),
			'insert_into_item' => __( 'Insert into Accordion', 'webthinkershop' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Accordion', 'webthinkershop' ),
			'items_list' => __( 'Accordions list', 'webthinkershop' ),
			'items_list_navigation' => __( 'Accordions list navigation', 'webthinkershop' ),
			'filter_items_list' => __( 'Filter Accordion list', 'webthinkershop' ),
		);
		
		$args = array(
			'label' => __( 'Accordion', 'webthinkershop' ),
			'description' => __( 'Accordion', 'webthinkershop' ),
			'labels' => $labels,
			'supports' => array( 'title' ),
			'public' => true,
			'show_in_rest' => true,
			'show_ui' => true,
			'menu_position' => 21,
			'menu_icon' => 'dashicons-menu',
			'has_archive' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
		);
		
		register_post_type( 'Accordion', $args );
	}
	add_action("init", "add_custom_post_type_accordion");
	
	// add HTML for Accordion CPT
	function add_accordion_meta_box() {
		
		$text = __( 'Accordion information', 'webthinkershop' );
		
		add_meta_box(
			'accordion_fields_meta_box',
			$text,
			'show_accordion_custom_fields',
			'Accordion'
		);
	}
	add_action( 'add_meta_boxes', 'add_accordion_meta_box' );
	
	function save_custom_post_accordion_metas( $post_id ) {
		
		$meta_nonce    = "accordionMetaNonce";
		$save_fields   = "saveAccordionFields";
		$fields       = "accordion_fields";
		
		return save_custom_post_metas($post_id, $meta_nonce, $save_fields, $fields);
	}
	add_action( 'save_post', 'save_custom_post_accordion_metas' );
	/* END - Add Custom Post Type - Accordion */
	
	function add_custom_post_type_testimonials() {
		$labels = array(
			'name' => _x( 'Testimonials', 'Post Type General Name', "webthinkershop" ),
			'singular_name' => _x( 'Testimonials', 'Post Type Singular Name', "webthinkershop" ),
			'menu_name' => __( 'Testimonials', "webthinkershop" ),
			'name_admin_bar' => __( 'Testimonials', "webthinkershop" ),
			'archives' => __( 'Testimonials Archives', "webthinkershop" ),
			'attributes' => __( 'Testimonials Attributes', "webthinkershop" ),
			'parent_item_colon' => __( 'Parent Testimonials:', "webthinkershop" ),
			'all_items' => __( 'All Testimonials', "webthinkershop" ),
			'add_new_item' => __( 'Add New Testimonials', "webthinkershop" ),
			'add_new' => __( 'Add New', "webthinkershop" ),
			'new_item' => __( 'New Testimonials', "webthinkershop" ),
			'edit_item' => __( 'Edit Testimonials', "webthinkershop" ),
			'update_item' => __( 'Update Testimonials', "webthinkershop" ),
			'view_item' => __( 'View Testimonials', "webthinkershop" ),
			'view_items' => __( 'View Testimonials', "webthinkershop" ),
			'search_items' => __( 'Search Testimonials', "webthinkershop" ),
			'not_found' => __( 'Not found', "webthinkershop" ),
			'not_found_in_trash' => __( 'Not found in Trash', "webthinkershop" ),
			'insert_into_item' => __( 'Insert into Testimonials', "webthinkershop" ),
			'uploaded_to_this_item' => __( 'Uploaded to this Testimonials', "webthinkershop" ),
			'items_list' => __( 'Testimonials list', "webthinkershop" ),
			'items_list_navigation' => __( 'Testimonials list navigation', "webthinkershop" ),
			'filter_items_list' => __( 'Filter Testimonials list', "webthinkershop" ),
		);
		
		$args = array(
			'label' => __( 'Testimonials', "webthinkershop" ),
			'description' => __( 'Testimonials', "webthinkershop" ),
			'labels' => $labels,
			'supports' => array( 'title' ),
			'public' => true,
			'show_in_rest' => true,
			'show_ui' => true,
			'menu_position' => 39,
			'menu_icon' => 'dashicons-images-alt2',
			'has_archive' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
		);
		
		register_post_type( 'Testimonials', $args );
	}
	add_action("init", "add_custom_post_type_testimonials");
	
	// add HTML for Testimonials CPT
	function add_testimonials_meta_box() {
		
		$text = __( 'Testimonials information', "webthinkershop" );
		
		add_meta_box(
			'testimonials_fields_meta_box',
			$text,
			'show_testimonials_custom_fields',
			'Testimonials'
		);
	}
	add_action( 'add_meta_boxes', 'add_testimonials_meta_box' );
	
	// saves metas for CPT Testimonials
	function save_custom_post_testimonials_metas( $post_id ) {
		
		$meta_nonce    = "testimonialsMetaNonce";
		$save_fields   = "testimonialsFields";
		$fields       = "testimonials_fields";
		
		return save_custom_post_metas($post_id, $meta_nonce, $save_fields, $fields);
	}
	add_action( 'save_post', 'save_custom_post_testimonials_metas' );
	/* END - Add Custom Post Type - Testimonials */

	/* Add Custom Post Type - Cards */
	function add_custom_post_type_cards() {
		
		$labels = array(
			'name' => _x( 'Cards', 'Post Type General Name', 'webthinkershop' ),
			'singular_name' => _x( 'Card', 'Post Type Singular Name', 'webthinkershop' ),
			'menu_name' => __( 'Cards', 'webthinkershop' ),
			'name_admin_bar' => __( 'Cards', 'webthinkershop' ),
			'archives' => __( 'Cards Archives', 'webthinkershop' ),
			'attributes' => __( 'Cards Attributes', 'webthinkershop' ),
			'parent_item_colon' => __( 'Parent Card:', 'webthinkershop' ),
			'all_items' => __( 'All Cards', 'webthinkershop' ),
			'add_new_item' => __( 'Add New Card', 'webthinkershop' ),
			'add_new' => __( 'Add New', 'webthinkershop' ),
			'new_item' => __( 'New Card', 'webthinkershop' ),
			'edit_item' => __( 'Edit Card', 'webthinkershop' ),
			'update_item' => __( 'Update Card', 'webthinkershop' ),
			'view_item' => __( 'View Card', 'webthinkershop' ),
			'view_items' => __( 'View Cards', 'webthinkershop' ),
			'search_items' => __( 'Search Cards', 'webthinkershop' ),
			'not_found' => __( 'Not found', 'webthinkershop' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'webthinkershop' ),
			'featured_image' => __( 'Card Image', 'webthinkershop' ),
			'set_featured_image' => __( 'Set Card image', 'webthinkershop' ),
			'remove_featured_image' => __( 'Remove Card image', 'webthinkershop' ),
			'use_featured_image' => __( 'Use as Card image', 'webthinkershop' ),
			'insert_into_item' => __( 'Insert into Card', 'webthinkershop' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Card', 'webthinkershop' ),
			'items_list' => __( 'Cards list', 'webthinkershop' ),
			'items_list_navigation' => __( 'Cards list navigation', 'webthinkershop' ),
			'filter_items_list' => __( 'Filter Cards list', 'webthinkershop' ),
		);
		
		$args = array(
			'label' => __( 'Cards', 'webthinkershop' ),
			'description' => __( 'Cards', 'webthinkershop' ),
			'labels' => $labels,
			'supports' => array( 'title' ),
			'public' => true,
			'show_in_rest' => true,
			'show_ui' => true,
			'menu_position' => 40,
			'menu_icon' => 'dashicons-index-card',
			'has_archive' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
		);
		
		register_post_type( 'Cards', $args );
	}
	add_action("init", "add_custom_post_type_cards");
	
	// add HTML for Cards CPT
	function add_cards_meta_box() {
		
		$text = __( 'Cards information', 'webthinkershop' );
		
		add_meta_box(
			'cards_fields_meta_box',
			$text,
			'show_cards_custom_fields',
			'Cards'
		);
	}
	add_action( 'add_meta_boxes', 'add_cards_meta_box' );
	
	// saves metas for CPT Cards
	function save_custom_post_cards_metas( $post_id ) {
		
		$meta_nonce    = "cardsMetaNonce";
		$save_fields   = "saveCardsFields";
		$fields       = "cards_fields";
		
		return save_custom_post_metas($post_id, $meta_nonce, $save_fields, $fields);
	}
	add_action( 'save_post', 'save_custom_post_cards_metas' );
	/* END - Add Custom Post Type - Cards */


/* Add Custom Post Type - Slider */
function add_custom_post_type_slider() {

    $labels = array(
        'name' => _x( 'Slider', 'Post Type General Name', 'webthinkershop' ),
        'singular_name' => _x( 'Slider', 'Post Type Singular Name', 'webthinkershop' ),
        'menu_name' => __( 'Slider', 'webthinkershop' ),
        'name_admin_bar' => __( 'Slider', 'webthinkershop' ),
        'archives' => __( 'Slider Archives', 'webthinkershop' ),
        'attributes' => __( 'Slider Attributes', 'webthinkershop' ),
        'parent_item_colon' => __( 'Parent Slider:', 'webthinkershop' ),
        'all_items' => __( 'All Sliders', 'webthinkershop' ),
        'add_new_item' => __( 'Add New Slider', 'webthinkershop' ),
        'add_new' => __( 'Add New', 'webthinkershop' ),
        'new_item' => __( 'New Slider', 'webthinkershop' ),
        'edit_item' => __( 'Edit Slider', 'webthinkershop' ),
        'update_item' => __( 'Update Slider', 'webthinkershop' ),
        'view_item' => __( 'View Slider', 'webthinkershop' ),
        'view_items' => __( 'View Sliders', 'webthinkershop' ),
        'search_items' => __( 'Search Slider', 'webthinkershop' ),
        'not_found' => __( 'Not found', 'webthinkershop' ),
        'not_found_in_trash' => __( 'Not found in Trash', 'webthinkershop' ),
        'featured_image' => __( 'Slide Image', 'webthinkershop' ),
        'set_featured_image' => __( 'Set slide image', 'webthinkershop' ),
        'remove_featured_image' => __( 'Remove slide image', 'webthinkershop' ),
        'use_featured_image' => __( 'Use as slide image', 'webthinkershop' ),
        'insert_into_item' => __( 'Insert into Slider', 'webthinkershop' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Slider', 'webthinkershop' ),
        'items_list' => __( 'Sliders list', 'webthinkershop' ),
        'items_list_navigation' => __( 'Sliders list navigation', 'webthinkershop' ),
        'filter_items_list' => __( 'Filter Slider list', 'webthinkershop' ),
    );

    $args = array(
        'label' => __( 'Slider', 'webthinkershop' ),
        'description' => __( 'Slider', 'webthinkershop' ),
        'labels' => $labels,
        'supports' => array( 'title' ),
        'public' => true,
        'show_in_rest' => true,
        'show_ui' => true,
        'menu_position' => 41,
        'menu_icon' => 'dashicons-slides',
        'has_archive' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
    );

    register_post_type( 'slider', $args );
}
add_action( 'init', 'add_custom_post_type_slider' );

// add HTML for Slider CPT
function add_slider_meta_box() {

    $text = __( 'Slider information', 'webthinkershop' );

    add_meta_box(
        'slider_fields_meta_box',
        $text,
        'show_slider_custom_fields',
        'slider'
    );
}
add_action( 'add_meta_boxes', 'add_slider_meta_box' );

// saves metas for CPT Slider
function save_custom_post_slider_metas( $post_id ) {

    $meta_nonce    = 'slider_meta_nonce';
    $save_fields   = 'save_slider_fields';
    $fields        = 'slider_fields';

    return save_custom_post_metas( $post_id, $meta_nonce, $save_fields, $fields );
}
add_action( 'save_post', 'save_custom_post_slider_metas' );
/* END - Add Custom Post Type - Slider */

	
