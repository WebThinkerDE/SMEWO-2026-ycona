<?php
/**
 * Auto-linking: Scans post content and wraps glossary terms with tooltip links.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Content filter: auto-link glossary terms ─────────────────── */

function wt_glossary_auto_link_content( $content ) {

    $settings = function_exists( 'wt_glossary_get_settings' ) ? wt_glossary_get_settings() : array();

    if ( ( $settings['enable_auto_link'] ?? 'yes' ) !== 'yes' ) {
        return $content;
    }

    if ( ! is_singular() || is_admin() ) {
        return $content;
    }

    if ( get_post_type() === 'wt_glossary_term' ) {
        return $content;
    }

    // Get all published glossary terms
    $glossary_terms = wt_glossary_get_all_terms_cached();

    if ( empty( $glossary_terms ) ) {
        return $content;
    }

    // Sort by title length descending so longer terms match first
    usort( $glossary_terms, function( $a, $b ) {
        return mb_strlen( $b['title'] ) - mb_strlen( $a['title'] );
    });

    // Track which terms have already been linked (only link first occurrence)
    $linked_terms = array();

    foreach ( $glossary_terms as $term_data ) {

        $term_title = $term_data['title'];
        $term_url   = $term_data['url'];
        $term_desc  = $term_data['short_desc'];
        $synonyms   = $term_data['synonyms'];

        // Build all search patterns: term title + synonyms
        $search_strings = array( $term_title );
        if ( ! empty( $synonyms ) ) {
            $synonym_list = array_map( 'trim', explode( ',', $synonyms ) );
            $search_strings = array_merge( $search_strings, $synonym_list );
        }

        foreach ( $search_strings as $search_term ) {

            if ( empty( $search_term ) || isset( $linked_terms[ mb_strtolower( $search_term ) ] ) ) {
                continue;
            }

            // Escape for regex
            $escaped_term = preg_quote( $search_term, '/' );

            // Match whole word only, case-insensitive
            // Negative lookbehind/lookahead to avoid matching inside HTML tags or existing links
            $pattern = '/(?<![<\w\/])(?<!["\'>])(\b' . $escaped_term . '\b)(?![^<]*>)(?![^<]*<\/a>)/iu';

            $show_tooltips = ( $settings['enable_tooltips'] ?? 'yes' ) === 'yes';

            $replacement = '<a href="' . esc_url( $term_url ) . '" '
                . 'class="wt-glossary-tooltip-link" '
                . 'data-glossary-term="' . esc_attr( $term_title ) . '"'
                . ( $show_tooltips && ! empty( $term_desc ) ? ' data-glossary-desc="' . esc_attr( $term_desc ) . '"' : '' )
                . '>$1</a>';

            $new_content = preg_replace( $pattern, $replacement, $content, 1, $count );

            if ( $count > 0 ) {
                $content = $new_content;
                $linked_terms[ mb_strtolower( $search_term ) ] = true;
                break; // Only link the first matching variant per term
            }
        }
    }

    return $content;
}
add_filter( 'the_content', 'wt_glossary_auto_link_content', 99 );

/* ── Cache: get all glossary terms (with object cache) ────────── */

function wt_glossary_get_all_terms_cached() {

    $cache_key   = 'wt_glossary_all_terms';
    $cache_group = 'wt_glossary';

    $cached = wp_cache_get( $cache_key, $cache_group );
    if ( false !== $cached ) {
        return $cached;
    }

    $terms_query = new WP_Query( array(
        'post_type'        => 'wt_glossary_term',
        'posts_per_page'   => -1,
        'post_status'      => 'publish',
        'orderby'          => 'title',
        'order'            => 'ASC',
        'suppress_filters' => false,
    ));

    $result = array();
    foreach ( $terms_query->posts as $term_post ) {
        $result[] = array(
            'id'         => $term_post->ID,
            'title'      => $term_post->post_title,
            'url'        => get_permalink( $term_post->ID ),
            'short_desc' => get_post_meta( $term_post->ID, '_wt_glossary_short_desc', true ) ?: '',
            'synonyms'   => get_post_meta( $term_post->ID, '_wt_glossary_synonyms', true ) ?: '',
        );
    }

    wp_cache_set( $cache_key, $result, $cache_group, 3600 );

    return $result;
}

/* ── Flush cache when a glossary term is saved/deleted ────────── */

function wt_glossary_flush_cache( $post_id ) {
    if ( get_post_type( $post_id ) === 'wt_glossary_term' ) {
        wp_cache_delete( 'wt_glossary_all_terms', 'wt_glossary' );
    }
}
add_action( 'save_post', 'wt_glossary_flush_cache' );
add_action( 'delete_post', 'wt_glossary_flush_cache' );
add_action( 'trashed_post', 'wt_glossary_flush_cache' );
