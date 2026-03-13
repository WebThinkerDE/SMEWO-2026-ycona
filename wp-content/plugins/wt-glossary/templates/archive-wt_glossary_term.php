<?php
/**
 * Archive Template for Glossary
 *
 * Loaded for the /glossary/ post type archive and glossary taxonomy pages.
 * Renders the full glossary using the shared render function.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$settings = function_exists( 'wt_glossary_get_settings' ) ? wt_glossary_get_settings() : array();

$category = '';
if ( is_tax( 'wt_glossary_category' ) ) {
    $queried = get_queried_object();
    if ( $queried && ! is_wp_error( $queried ) ) {
        $category = $queried->slug;
    }
}
?>

<div class="wt-glossary-archive-page">
    <div class="container">
        <?php
        echo wt_glossary_render_output( array(
            'title'        => $settings['archive_title'] ?? __( 'Glossary', 'wt-glossary' ),
            'description'  => $settings['archive_description'] ?? '',
            'search_label' => $settings['archive_search_label'] ?? __( 'What word are you interested in?', 'wt-glossary' ),
            'show_search'  => 'yes',
            'show_nav'     => 'yes',
            'columns'      => $settings['archive_columns'] ?? '2',
            'category'     => $category,
        ) );
        ?>
    </div>
</div>

<?php
get_footer();
