<?php
/**
 * AJAX handler for glossary search and letter filtering.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── AJAX: Search / filter ────────────────────────────────────── */

function wt_glossary_ajax_search() {

    check_ajax_referer( 'wt_glossary_nonce', 'nonce' );

    $search_query = sanitize_text_field( $_POST['search_query'] ?? '' );
    $letter       = sanitize_text_field( $_POST['letter'] ?? '' );
    $category     = sanitize_text_field( $_POST['category'] ?? '' );
    $columns      = absint( $_POST['columns'] ?? 2 );

    $col_class_map = array(
        1 => 'col-12',
        2 => 'col-12 col-md-6',
        3 => 'col-12 col-md-6 col-lg-4',
        4 => 'col-12 col-md-6 col-lg-3',
    );
    $col_class = $col_class_map[ $columns ] ?? 'col-12 col-md-6';

    $query_args = array(
        'post_type'        => 'wt_glossary_term',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
        'suppress_filters' => false,
    );

    // Search query: match title, content, synonyms, or short_desc (OR logic)
    if ( ! empty( $search_query ) ) {
        // 1) Main query: WordPress native search (post_title + post_content only)
        $query_args['s'] = $search_query;
    }

    // Letter filter
    if ( ! empty( $letter ) && $letter !== 'all' ) {
        $letter_slug = $letter === '#' ? 'hash' : strtolower( $letter );
        $query_args['tax_query'][] = array(
            'taxonomy' => 'wt_glossary_letter',
            'field'    => 'slug',
            'terms'    => $letter_slug,
        );
    }

    // Category filter
    if ( ! empty( $category ) ) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'wt_glossary_category',
            'field'    => 'slug',
            'terms'    => $category,
        );
    }

    if ( isset( $query_args['tax_query'] ) && count( $query_args['tax_query'] ) > 1 ) {
        $query_args['tax_query']['relation'] = 'AND';
    }

    $terms_query = new WP_Query( $query_args );
    $terms_posts = $terms_query->posts;

    // Add results that match only in synonyms or short_desc (not in title/content)
    if ( ! empty( $search_query ) ) {
        $meta_query = new WP_Query( array(
            'post_type'        => 'wt_glossary_term',
            'posts_per_page'   => -1,
            'post_status'      => 'publish',
            'suppress_filters' => false,
            'meta_query'       => array(
                'relation' => 'OR',
                array(
                    'key'     => '_wt_glossary_synonyms',
                    'value'   => $search_query,
                    'compare' => 'LIKE',
                ),
                array(
                    'key'     => '_wt_glossary_short_desc',
                    'value'   => $search_query,
                    'compare' => 'LIKE',
                ),
            ),
        ));

        $existing_ids = wp_list_pluck( $terms_posts, 'ID' );
        foreach ( $meta_query->posts as $meta_post ) {
            if ( ! in_array( $meta_post->ID, $existing_ids, true ) ) {
                $terms_posts[] = $meta_post;
            }
        }

        // Re-sort by title
        usort( $terms_posts, function( $a, $b ) {
            return strcasecmp( $a->post_title, $b->post_title );
        });
    }

    // Group by letter
    $grouped = array();
    foreach ( $terms_posts as $term_post ) {
        $first_letter = wt_glossary_get_first_letter( $term_post->post_title );
        $grouped[ $first_letter ][] = $term_post;
    }
    ksort( $grouped );

    // Build HTML
    ob_start();

    if ( empty( $grouped ) ) {
        echo '<p class="wt-glossary-no-results">' . esc_html__( 'No terms found.', 'wt-glossary' ) . '</p>';
    } else {
        foreach ( $grouped as $letter_key => $letter_terms ) {
            echo '<div class="wt-glossary-letter-section" data-letter="' . esc_attr( $letter_key ) . '">';
            echo '<h2 class="wt-glossary-letter-heading" id="wt-glossary-letter-' . esc_attr( strtolower( $letter_key === '#' ? 'hash' : $letter_key ) ) . '">' . esc_html( $letter_key ) . '</h2>';
            echo '<div class="row wt-glossary-terms-row">';

            foreach ( $letter_terms as $term_post ) {
                $short_desc = get_post_meta( $term_post->ID, '_wt_glossary_short_desc', true );
                $permalink  = get_permalink( $term_post->ID );

                echo '<div class="' . esc_attr( $col_class ) . ' wt-glossary-term-col">';
                echo '  <div class="wt-glossary-term-card" data-term-id="' . esc_attr( $term_post->ID ) . '">';
                echo '    <h3 class="wt-glossary-term-title">';
                echo '      <a href="' . esc_url( $permalink ) . '">' . esc_html( $term_post->post_title ) . '</a>';
                echo '    </h3>';
                if ( ! empty( $short_desc ) ) {
                    echo '    <p class="wt-glossary-term-excerpt">' . esc_html( $short_desc ) . '</p>';
                }
                echo '    <a href="' . esc_url( $permalink ) . '" class="wt-glossary-read-more">';
                echo        esc_html__( 'Read more', 'wt-glossary' );
                echo '      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
                echo '    </a>';
                echo '  </div>';
                echo '</div>';
            }

            echo '</div>'; // .row
            echo '</div>'; // .wt-glossary-letter-section
        }
    }

    $html = ob_get_clean();

    $terms_payload = array();
    foreach ( $terms_posts as $term_post ) {
        $short_desc = get_post_meta( $term_post->ID, '_wt_glossary_short_desc', true );
        $terms_payload[] = array(
            'id'         => $term_post->ID,
            'title'      => $term_post->post_title,
            'short_desc' => wp_strip_all_tags( $short_desc ),
            'permalink'  => get_permalink( $term_post->ID ),
        );
        if ( count( $terms_payload ) >= 10 ) {
            break;
        }
    }

    wp_send_json_success( array(
        'html'  => $html,
        'count' => count( $terms_posts ),
        'terms' => $terms_payload,
    ));
}
add_action( 'wp_ajax_wt_glossary_search', 'wt_glossary_ajax_search' );
add_action( 'wp_ajax_nopriv_wt_glossary_search', 'wt_glossary_ajax_search' );
