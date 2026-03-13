<?php
/**
 * Gutenberg block render callback for wt/glossary-block.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render callback for the Gutenberg glossary block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block inner content (not used).
 * @return string            Rendered HTML.
 */
function wt_glossary_block_rc( $attributes, $content ) {

    $atts = array(
        'title'        => $attributes['title'] ?? 'Glossary',
        'description'  => $attributes['description'] ?? '',
        'search_label' => $attributes['search_label'] ?? 'What word are you interested in?',
        'show_search'  => $attributes['show_search'] ?? 'yes',
        'show_nav'     => $attributes['show_nav'] ?? 'yes',
        'columns'      => $attributes['columns'] ?? '2',
        'category'     => $attributes['category'] ?? '',
    );

    return wt_glossary_render_output( $atts );
}
