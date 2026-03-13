<?php
/**
 * Shortcode: [wt_glossary]
 * Shared render function used by both the shortcode and the Gutenberg block.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Shortcode registration ───────────────────────────────────── */

function wt_glossary_shortcode_handler( $atts ) {

    $atts = shortcode_atts( array(
        'title'        => 'Glossary',
        'description'  => '',
        'search_label' => 'What word are you interested in?',
        'show_search'  => 'yes',
        'show_nav'     => 'yes',
        'columns'      => '2',
        'category'     => '',
    ), $atts, 'wt_glossary' );

    return wt_glossary_render_output( $atts );
}
add_shortcode( 'wt_glossary', 'wt_glossary_shortcode_handler' );

/* ── Shared render function ───────────────────────────────────── */

function wt_glossary_render_output( $atts ) {

    $title        = sanitize_text_field( $atts['title'] ?? 'Glossary' );
    $description  = wp_kses_post( $atts['description'] ?? '' );
    $search_label = sanitize_text_field( $atts['search_label'] ?? '' );
    $show_search  = ( $atts['show_search'] ?? 'yes' ) === 'yes';
    $show_nav     = ( $atts['show_nav'] ?? 'yes' ) === 'yes';
    $columns      = absint( $atts['columns'] ?? 2 );
    $category     = sanitize_text_field( $atts['category'] ?? '' );
    $saved_settings = function_exists( 'wt_glossary_get_settings' ) ? wt_glossary_get_settings() : array();
    $recent_limit = absint( apply_filters( 'wt_glossary_recent_limit', $saved_settings['recent_search_limit'] ?? 5 ) );

    if ( $columns < 1 ) {
        $columns = 2;
    }
    if ( $columns > 4 ) {
        $columns = 4;
    }
    if ( $recent_limit < 1 ) {
        $recent_limit = 5;
    }

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

    if ( ! empty( $category ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'wt_glossary_category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $terms_query = new WP_Query( $query_args );
    $terms_posts = $terms_query->posts;

    $grouped = array();
    $active_letters = array();

    foreach ( $terms_posts as $term_post ) {
        $letter = wt_glossary_get_first_letter( $term_post->post_title );
        $grouped[ $letter ][] = $term_post;
        $active_letters[ $letter ] = true;
    }

    ksort( $grouped );

    $all_letters = array_merge( range( 'A', 'Z' ), array( '#' ) );

    ob_start();
    ?>
    <div id="wt-glossary-wrapper"
         class="wt-glossary-wrapper"
         data-columns="<?php echo esc_attr( $columns ); ?>"
         data-category="<?php echo esc_attr( $category ); ?>">

        <?php if ( ! empty( $title ) ) : ?>
        <div class="wt-glossary-header">
            <h2 class="wt-glossary-title"><?php echo esc_html( $title ); ?></h2>
            <?php if ( ! empty( $description ) ) : ?>
                <p class="wt-glossary-description"><?php echo $description; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $show_search ) : ?>
        <div class="wt-glossary-search-wrapper">
            <?php if ( ! empty( $search_label ) ) : ?>
                <div class="wt-glossary-search-label"><?php echo esc_html( $search_label ); ?></div>
            <?php endif; ?>
            <div class="wt-glossary-search-inner">
                <svg class="wt-glossary-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text"
                       id="wt-glossary-search"
                       class="wt-glossary-search"
                       placeholder="<?php esc_attr_e( 'Search', 'wt-glossary' ); ?>"
                       autocomplete="off">
                <button type="button"
                        id="wt-glossary-search-clear"
                        class="wt-glossary-search-clear"
                        aria-label="<?php esc_attr_e( 'Clear search', 'wt-glossary' ); ?>"
                        style="display:none;">
                    &times;
                </button>
            </div>
        </div>

        <!-- Top searched -->
        <div class="wt-glossary-recent" data-max-items="<?php echo esc_attr( $recent_limit ); ?>" style="display:none;">
            <div class="wt-glossary-recent-header">
                <span class="wt-glossary-recent-label"><?php esc_html_e( 'Top searched:', 'wt-glossary' ); ?></span>
                <div class="wt-glossary-recent-list"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $show_nav ) : ?>
        <nav id="webthinker-glossary-nav"
             class="webthinker-glossary-nav"
             aria-label="<?php esc_attr_e( 'Alphabetical navigation', 'wt-glossary' ); ?>">
            <button type="button"
                    class="wt-glossary-nav-btn wt-glossary-nav-btn-active"
                    data-letter="all">
                <?php esc_html_e( 'All', 'wt-glossary' ); ?>
            </button>
            <?php foreach ( $all_letters as $letter ) :
                $has_terms = isset( $active_letters[ $letter ] );
                $disabled_class = $has_terms ? '' : ' wt-glossary-nav-btn-disabled';
            ?>
                <button type="button"
                        class="wt-glossary-nav-btn<?php echo esc_attr( $disabled_class ); ?>"
                        data-letter="<?php echo esc_attr( $letter ); ?>"
                        <?php echo $has_terms ? '' : 'disabled'; ?>>
                    <?php echo esc_html( $letter ); ?>
                </button>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <!-- Terms listing -->
        <div id="wt-glossary-list" class="wt-glossary-list">
            <?php if ( empty( $grouped ) ) : ?>
                <p class="wt-glossary-no-results"><?php esc_html_e( 'No glossary terms found.', 'wt-glossary' ); ?></p>
            <?php else : ?>
                <?php foreach ( $grouped as $letter => $letter_terms ) : ?>
                    <div class="wt-glossary-letter-section" data-letter="<?php echo esc_attr( $letter ); ?>">
                        <h2 class="wt-glossary-letter-heading" id="wt-glossary-letter-<?php echo esc_attr( strtolower( $letter === '#' ? 'hash' : $letter ) ); ?>">
                            <?php echo esc_html( $letter ); ?>
                        </h2>
                        <div class="row wt-glossary-terms-row">
                            <?php foreach ( $letter_terms as $term_post ) :
                                $short_desc = get_post_meta( $term_post->ID, '_wt_glossary_short_desc', true );
                                $permalink  = get_permalink( $term_post->ID );
                            ?>
                                <div class="<?php echo esc_attr( $col_class ); ?> wt-glossary-term-col">
                                    <div class="wt-glossary-term-card" data-term-id="<?php echo esc_attr( $term_post->ID ); ?>">
                                        <h3 class="wt-glossary-term-title">
                                            <a href="<?php echo esc_url( $permalink ); ?>">
                                                <?php echo esc_html( $term_post->post_title ); ?>
                                            </a>
                                        </h3>
                                        <?php if ( ! empty( $short_desc ) ) : ?>
                                            <p class="wt-glossary-term-excerpt">
                                                <?php echo esc_html( $short_desc ); ?>
                                            </p>
                                        <?php endif; ?>
                                        <a href="<?php echo esc_url( $permalink ); ?>" class="wt-glossary-read-more">
                                            <?php esc_html_e( 'Know More', 'wt-glossary' ); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                <polyline points="12 5 19 12 12 19"></polyline>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- AJAX results container (hidden by default) -->
        <div id="wt-glossary-ajax-results" class="wt-glossary-list" style="display:none;"></div>

        <!-- Loading indicator -->
        <div id="wt-glossary-loading" class="wt-glossary-loading" style="display:none;">
            <div class="wt-glossary-spinner"></div>
            <span><?php esc_html_e( 'Searching…', 'wt-glossary' ); ?></span>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

/* ── Helper: get normalised first letter ──────────────────────── */

function wt_glossary_get_first_letter( $title ) {

    $title = trim( $title );
    if ( empty( $title ) ) {
        return '#';
    }

    $first_char = mb_strtoupper( mb_substr( $title, 0, 1, 'UTF-8' ), 'UTF-8' );

    $map = array(
        'Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U',
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
        'Ñ' => 'N', 'Ç' => 'C',
    );

    if ( isset( $map[ $first_char ] ) ) {
        $first_char = $map[ $first_char ];
    }

    if ( ! preg_match( '/^[A-Z]$/', $first_char ) ) {
        return '#';
    }

    return $first_char;
}
