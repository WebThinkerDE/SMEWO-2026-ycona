<?php
/**
 * Plugin Name:       WebThinker Glossary
 * Plugin URI:        https://webthinker.de
 * Description:       A glossary module for managing and displaying terms with A–Z navigation, search, auto-linking with tooltips, WPML support, Gutenberg block and shortcode.
 * Version:           1.0.0
 * Author:            WebThinker / Granit Nebiu
 * Author URI:        https://webthinker.de
 * Text Domain:       wt-glossary
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Constants ────────────────────────────────────────────────── */
define( 'WT_GLOSSARY_VERSION', '1.0.0' );
define( 'WT_GLOSSARY_PATH', plugin_dir_path( __FILE__ ) );
define( 'WT_GLOSSARY_URL', plugin_dir_url( __FILE__ ) );
define( 'WT_GLOSSARY_BASENAME', plugin_basename( __FILE__ ) );

/* ── Includes ─────────────────────────────────────────────────── */
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-taxonomy.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-cpt.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-shortcode.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-ajax.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-auto-link.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-template.php';
require_once WT_GLOSSARY_PATH . 'includes/wt-glossary-settings.php';
require_once WT_GLOSSARY_PATH . 'import-demo-data.php';

/* ── Bootstrap detection ──────────────────────────────────────── */

/**
 * Check whether Bootstrap CSS is already enqueued by the theme or another plugin.
 *
 * @return bool
 */
function wt_glossary_detect_bootstrap() {
    return wp_style_is( 'bootstrap-css', 'enqueued' ) || wp_style_is( 'bootstrap-css', 'registered' );
}

/* ── Frontend assets ──────────────────────────────────────────── */

function wt_glossary_enqueue_assets() {

    $has_bootstrap = wt_glossary_detect_bootstrap();

    if ( $has_bootstrap ) {
        wp_enqueue_style(
            'wt-glossary-skin',
            WT_GLOSSARY_URL . 'assets/css/wt-glossary-skin.css',
            array( 'bootstrap-css' ),
            WT_GLOSSARY_VERSION
        );
    } else {
        wp_enqueue_style(
            'wt-glossary-css',
            WT_GLOSSARY_URL . 'assets/css/wt-glossary.css',
            array(),
            WT_GLOSSARY_VERSION
        );
    }

    wp_enqueue_script(
        'wt-glossary-js',
        WT_GLOSSARY_URL . 'assets/js/wt-glossary.js',
        array(),
        WT_GLOSSARY_VERSION,
        true
    );

    wp_localize_script( 'wt-glossary-js', 'wt_glossary_params', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wt_glossary_nonce' ),
        'i18n'     => array(
            'searching'  => esc_html__( 'Searching…', 'wt-glossary' ),
            'no_results' => esc_html__( 'No terms found.', 'wt-glossary' ),
            'read_more'  => esc_html__( 'Read more', 'wt-glossary' ),
            'all'        => esc_html__( 'All', 'wt-glossary' ),
        ),
    ));
}
add_action( 'wp_enqueue_scripts', 'wt_glossary_enqueue_assets' );

/* ── Block category (fallback if theme category does not exist) ── */

function wt_glossary_register_block_category( $categories ) {

    $category_slugs = wp_list_pluck( $categories, 'slug' );

    if ( in_array( 'wt-shop-blocks', $category_slugs, true ) ) {
        return $categories;
    }

    return array_merge(
        $categories,
        array(
            array(
                'slug'  => 'wt-shop-blocks',
                'title' => __( 'WebThinker Blocks', 'wt-glossary' ),
                'icon'  => null,
            ),
        )
    );
}
add_filter( 'block_categories_all', 'wt_glossary_register_block_category', 10, 1 );

/* ── Gutenberg block registration ─────────────────────────────── */

function wt_glossary_register_block() {

    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    wp_register_script(
        'wt_glossary_block',
        WT_GLOSSARY_URL . 'blocks/glossary-block/glossary_block_editor.long.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data' ),
        WT_GLOSSARY_VERSION
    );

    wp_register_style(
        'wt_glossary_block_editor',
        WT_GLOSSARY_URL . 'blocks/glossary-block/glossary_block_editor.css',
        array( 'wp-edit-blocks' ),
        WT_GLOSSARY_VERSION
    );

    wp_localize_script( 'wt_glossary_block', 'wt_glossary_editor_i18n', array(
        'content'            => __( 'Content', 'wt-glossary' ),
        'title'              => __( 'Title', 'wt-glossary' ),
        'description'        => __( 'Description', 'wt-glossary' ),
        'search_label'       => __( 'Search Label', 'wt-glossary' ),
        'display_settings'   => __( 'Display Settings', 'wt-glossary' ),
        'show_search'        => __( 'Show Search', 'wt-glossary' ),
        'show_nav'           => __( 'Show A–Z Navigation', 'wt-glossary' ),
        'columns'            => __( 'Columns', 'wt-glossary' ),
        'col_1'              => __( '1 Column', 'wt-glossary' ),
        'col_2'              => __( '2 Columns', 'wt-glossary' ),
        'col_3'              => __( '3 Columns', 'wt-glossary' ),
        'col_4'              => __( '4 Columns', 'wt-glossary' ),
        'category_slug'      => __( 'Category Slug (optional)', 'wt-glossary' ),
        'category_slug_hint' => __( 'e.g. logistics', 'wt-glossary' ),
        'search'             => __( 'Search', 'wt-glossary' ),
        'top_searched'       => __( 'Top searched:', 'wt-glossary' ),
        'know_more'          => __( 'Know More', 'wt-glossary' ),
    ));

    register_block_type( 'wt/glossary-block', array(
        'editor_script'   => 'wt_glossary_block',
        'editor_style'    => 'wt_glossary_block_editor',
        'render_callback' => 'wt_glossary_block_rc',
    ));

    include_once WT_GLOSSARY_PATH . 'blocks/glossary-block/glossary_block.php';
}
add_action( 'init', 'wt_glossary_register_block' );

/* ── Activation: seed letter taxonomy terms ───────────────────── */

function wt_glossary_activate() {
    // Make sure taxonomy is registered before seeding.
    wt_glossary_register_taxonomy_letter();
    wt_glossary_register_taxonomy_category();
    wt_glossary_register_cpt();

    wt_glossary_seed_letters();

    // Import demo glossary terms (only inserts if they don't already exist)
    wt_glossary_insert_demo_terms();

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wt_glossary_activate' );

/* ── Deactivation ─────────────────────────────────────────────── */

function wt_glossary_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wt_glossary_deactivate' );

/* ── Load text domain ─────────────────────────────────────────── */

function wt_glossary_load_textdomain() {
    load_plugin_textdomain( 'wt-glossary', false, dirname( WT_GLOSSARY_BASENAME ) . '/languages' );
}
add_action( 'plugins_loaded', 'wt_glossary_load_textdomain' );
