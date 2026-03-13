<?php
/**
 * Glossary taxonomies — Letter (auto) and Category (user-managed).
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Letter taxonomy (A–Z + #) ───────────────────────────────── */

function wt_glossary_register_taxonomy_letter() {

    $labels = array(
        'name'              => __( 'Letters', 'wt-glossary' ),
        'singular_name'     => __( 'Letter', 'wt-glossary' ),
        'search_items'      => __( 'Search Letters', 'wt-glossary' ),
        'all_items'         => __( 'All Letters', 'wt-glossary' ),
        'edit_item'         => __( 'Edit Letter', 'wt-glossary' ),
        'update_item'       => __( 'Update Letter', 'wt-glossary' ),
        'add_new_item'      => __( 'Add New Letter', 'wt-glossary' ),
        'new_item_name'     => __( 'New Letter Name', 'wt-glossary' ),
        'menu_name'         => __( 'Letters', 'wt-glossary' ),
    );

    register_taxonomy( 'wt_glossary_letter', 'wt_glossary_term', array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => false,   // Hidden — managed automatically
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'glossary-letter' ),
    ));
}
add_action( 'init', 'wt_glossary_register_taxonomy_letter', 5 );

/* ── Category taxonomy (optional, user-managed) ───────────────── */

function wt_glossary_register_taxonomy_category() {

    $labels = array(
        'name'              => __( 'Glossary Categories', 'wt-glossary' ),
        'singular_name'     => __( 'Glossary Category', 'wt-glossary' ),
        'search_items'      => __( 'Search Categories', 'wt-glossary' ),
        'all_items'         => __( 'All Categories', 'wt-glossary' ),
        'parent_item'       => __( 'Parent Category', 'wt-glossary' ),
        'parent_item_colon' => __( 'Parent Category:', 'wt-glossary' ),
        'edit_item'         => __( 'Edit Category', 'wt-glossary' ),
        'update_item'       => __( 'Update Category', 'wt-glossary' ),
        'add_new_item'      => __( 'Add New Category', 'wt-glossary' ),
        'new_item_name'     => __( 'New Category Name', 'wt-glossary' ),
        'menu_name'         => __( 'Categories', 'wt-glossary' ),
    );

    register_taxonomy( 'wt_glossary_category', 'wt_glossary_term', array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'glossary-category' ),
    ));
}
add_action( 'init', 'wt_glossary_register_taxonomy_category', 5 );

/* ── Seed A–Z + # terms on activation ─────────────────────────── */

function wt_glossary_seed_letters() {

    $letters = array_merge( range( 'A', 'Z' ), array( '#' ) );

    foreach ( $letters as $letter ) {
        if ( ! term_exists( $letter, 'wt_glossary_letter' ) ) {
            wp_insert_term( $letter, 'wt_glossary_letter', array(
                'slug' => strtolower( $letter === '#' ? 'hash' : $letter ),
            ));
        }
    }
}
