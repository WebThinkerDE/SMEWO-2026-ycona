<?php
/**
 * AJAX Product Search handler
 *
 * Searches WooCommerce products by title and SKU.
 * Fully respects WPML – only returns products in the active language.
 *
 * @package webthinkershop
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register AJAX actions (logged-in & guest).
 */
add_action( 'wp_ajax_wt_shop_product_search',        'wt_shop_ajax_product_search' );
add_action( 'wp_ajax_nopriv_wt_shop_product_search', 'wt_shop_ajax_product_search' );

/**
 * Handle AJAX product search request.
 */
function wt_shop_ajax_product_search() {

    // Verify nonce
    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wt_shop_product_search' ) ) {
        wp_send_json_error( array( 'message' => __( 'Security check failed.', 'webthinkershop' ) ), 403 );
    }

    $term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

    if ( mb_strlen( $term ) < 2 ) {
        wp_send_json_success( array( 'results' => array(), 'count' => 0 ) );
    }

    global $wpdb;

    $like_term = '%' . $wpdb->esc_like( $term ) . '%';

    // ── Detect current WPML language ──
    $current_lang = '';
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
        $current_lang = ICL_LANGUAGE_CODE;
    } elseif ( function_exists( 'pll_current_language' ) ) {
        $current_lang = pll_current_language();
    }

    // ── 1) SKU search – direct SQL with WPML language filter ──
    // Since SKU is the same across translations, we must JOIN on
    // icl_translations to only return the product post in the active language.
    $wpml_join  = '';
    $wpml_where = '';

    $icl_table = $wpdb->prefix . 'icl_translations';

    if ( $current_lang && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $icl_table ) ) ) {
        $wpml_join  = " INNER JOIN {$icl_table} t ON p.ID = t.element_id AND t.element_type = 'post_product'";
        $wpml_where = $wpdb->prepare( ' AND t.language_code = %s', $current_lang );
    }

    $sku_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT p.ID
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             {$wpml_join}
             WHERE pm.meta_key = '_sku'
               AND pm.meta_value LIKE %s
               AND p.post_type   = 'product'
               AND p.post_status = 'publish'
               {$wpml_where}
             LIMIT 20",
            $like_term
        )
    );

    if ( empty( $sku_ids ) ) {
        $sku_ids = array();
    }

    // ── 2) Title search via WP_Query (WPML filters apply automatically) ──
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $term,
        'posts_per_page' => 8,
        'orderby'        => 'relevance',
        'order'          => 'DESC',
        'fields'         => 'ids',
        // suppress_filters must stay false (default) so WPML language filter applies
    );

    $title_query = new WP_Query( $args );
    $title_ids   = $title_query->posts;

    // ── 3) Merge & deduplicate, title matches first, cap at 8 ──
    $all_ids = array_unique( array_merge( $title_ids, array_map( 'intval', $sku_ids ) ) );
    $all_ids = array_slice( $all_ids, 0, 8 );

    if ( empty( $all_ids ) ) {
        wp_send_json_success( array( 'results' => array(), 'count' => 0 ) );
    }

    // ── 4) Build result set ──
    $results = array();

    foreach ( $all_ids as $pid ) {
        $product = wc_get_product( $pid );
        if ( ! $product ) {
            continue;
        }

        $thumb_id  = $product->get_image_id();
        $thumb_url = $thumb_id
            ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' )
            : wc_placeholder_img_src( 'woocommerce_thumbnail' );

        $results[] = array(
            'id'        => $pid,
            'title'     => esc_html( $product->get_name() ),
            'url'       => esc_url( get_permalink( $pid ) ),
            'price'     => wp_kses_post( $product->get_price_html() ),
            'thumbnail' => esc_url( $thumb_url ),
            'sku'       => esc_html( $product->get_sku() ),
        );
    }

    wp_send_json_success( array(
        'results' => $results,
        'count'   => count( $results ),
    ) );
}
