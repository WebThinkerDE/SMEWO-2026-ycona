<?php

/**
 * My Account – Licenses tab (endpoint + menu + content).
 * The "Licenses" tab appears in the frontend My Account menu. Content lists licenses
 * for the current user from post type `isofloor_license` (must be registered by a plugin).
 * If the tab or URL 404s: go to Settings → Permalinks and click Save once (no auto-flush on init).
 */

function wt_shop_myaccount_add_licenses_endpoint() {
    if ( ! function_exists( 'WC' ) ) {
        return;
    }
    add_rewrite_endpoint( 'licenses', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'wt_shop_myaccount_add_licenses_endpoint', 5 );

function wt_shop_myaccount_licenses_query_vars( $vars ) {
    $vars[] = 'licenses';
    return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'wt_shop_myaccount_licenses_query_vars' );
add_filter( 'query_vars', 'wt_shop_myaccount_licenses_query_vars' );

function wt_shop_myaccount_licenses_menu_item( $items ) {
    $new_items = array();
    foreach ( $items as $key => $label ) {
        $new_items[ $key ] = $label;
        if ( $key === 'dashboard' ) {
            $new_items['licenses'] = __( 'Licenses', 'webthinkershop' );
        }
    }
    return $new_items;
}
add_filter( 'woocommerce_account_menu_items', 'wt_shop_myaccount_licenses_menu_item' );

function wt_shop_myaccount_licenses_content() {
    if ( ! is_user_logged_in() || ! post_type_exists( 'eample' ) ) {
        return;
    }
    $user    = wp_get_current_user();
    $licenses = get_posts( array(
        'post_type'      => 'eample',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'   => '_customer_email',
                'value' => $user->user_email,
                'compare' => '=',
            ),
        ),
        'orderby'       => 'date',
        'order'         => 'DESC',
    ) );

    $tier_labels = array(
        'starter'      => _x( 'Starter', 'license tier', 'webthinkershop' ),
        'professional' => _x( 'Professional', 'license tier', 'webthinkershop' ),
        'enterprise'   => _x( 'Enterprise', 'license tier', 'webthinkershop' ),
        'free'         => _x( 'Free', 'license tier', 'webthinkershop' ),
    );

    if ( empty( $licenses ) ) {
        echo '<p class="woocommerce-info">' . esc_html__( 'You have no licenses yet. Licenses are created when you purchase an IsoFloor product.', 'webthinkershop' ) . '</p>';
        return;
    }

    echo '<div class="wt-shop-myaccount-licenses">';
    echo '<table class="woocommerce-orders-table woocommerce-MyAccount-licenses-table">';
    echo '<thead><tr>';
    echo '<th>' . esc_html__( 'License key', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'Package', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'Status', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'Expires', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'Domains', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'Order', 'webthinkershop' ) . '</th>';
    echo '<th>' . esc_html__( 'View', 'webthinkershop' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( $licenses as $license_post ) {
        $license_id   = $license_post->ID;
        $license_key  = get_post_meta( $license_id, '_license_key', true );
        $tier         = get_post_meta( $license_id, '_tier', true );
        $status       = get_post_meta( $license_id, '_status', true );
        $expires_at   = get_post_meta( $license_id, '_expires_at', true );
        $max_domains  = (int) get_post_meta( $license_id, '_max_domains', true );
        $activated    = get_post_meta( $license_id, '_activated_domains', true );
        $activated    = is_array( $activated ) ? $activated : array();
        $order_id     = get_post_meta( $license_id, '_order_id', true );

        $tier_label   = isset( $tier_labels[ $tier ] ) ? $tier_labels[ $tier ] : ucfirst( (string) $tier );
        $status_label = $status === 'active' ? __( 'Active', 'webthinkershop' ) : ( $status === 'expired' ? __( 'Expired', 'webthinkershop' ) : ( $status === 'revoked' ? __( 'Revoked', 'webthinkershop' ) : esc_html( $status ) ) );
        $expires_text = empty( $expires_at ) ? _x( 'Lifetime', 'license expiry', 'webthinkershop' ) : date_i18n( get_option( 'date_format' ), strtotime( $expires_at ) );
        $domains_text = count( $activated ) . ' / ' . ( $max_domains < 0 ? _x( 'Unlimited', 'domains', 'webthinkershop' ) : $max_domains );
        $order_link   = $order_id && function_exists( 'wc_get_endpoint_url' ) ? '<a href="' . esc_url( wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) ) . '">#' . absint( $order_id ) . '</a>' : ( $order_id ? '#' . absint( $order_id ) : '—' );

        $domains_json = wp_json_encode( array_values( $activated ) );
        $view_btn     = '<button type="button" class="wt-shop-view-websites-btn button" data-domains="' . esc_attr( $domains_json ) . '" aria-label="' . esc_attr__( 'View active websites', 'webthinkershop' ) . '"><span class="wt-shop-view-websites-btn-text">' . esc_html__( 'View websites', 'webthinkershop' ) . '</span></button>';

        echo '<tr>';
        echo '<td data-title="' . esc_attr__( 'License key', 'webthinkershop' ) . '"><code class="wt-shop-license-key">' . esc_html( $license_key ) . '</code></td>';
        echo '<td data-title="' . esc_attr__( 'Package', 'webthinkershop' ) . '">' . esc_html( $tier_label ) . '</td>';
        echo '<td data-title="' . esc_attr__( 'Status', 'webthinkershop' ) . '"><span class="wt-shop-license-status wt-shop-license-status-' . esc_attr( $status ) . '">' . esc_html( $status_label ) . '</span></td>';
        echo '<td data-title="' . esc_attr__( 'Expires', 'webthinkershop' ) . '">' . esc_html( $expires_text ) . '</td>';
        echo '<td data-title="' . esc_attr__( 'Domains', 'webthinkershop' ) . '">' . esc_html( $domains_text ) . '</td>';
        echo '<td data-title="' . esc_attr__( 'Order', 'webthinkershop' ) . '">' . $order_link . '</td>';
        echo '<td data-title="' . esc_attr__( 'View', 'webthinkershop' ) . '">' . $view_btn . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';

    // Modal: active websites for this license
    echo '<div class="wt-shop-license-websites-modal" id="wt-shop-license-websites-modal" role="dialog" aria-modal="true" aria-labelledby="wt-shop-license-websites-modal-title" aria-hidden="true">';
    echo '<div class="wt-shop-license-websites-modal-backdrop" aria-hidden="true"></div>';
    echo '<div class="wt-shop-license-websites-modal-box">';
    echo '<div class="wt-shop-license-websites-modal-head">';
    echo '<h3 class="wt-shop-license-websites-modal-title" id="wt-shop-license-websites-modal-title">' . esc_html__( 'Active websites', 'webthinkershop' ) . '</h3>';
    echo '<button type="button" class="wt-shop-license-websites-modal-close" aria-label="' . esc_attr__( 'Close', 'webthinkershop' ) . '">&times;</button>';
    echo '</div>';
    echo '<div class="wt-shop-license-websites-modal-body">';
    echo '<ul class="wt-shop-license-websites-list" id="wt-shop-license-websites-list"></ul>';
    echo '<p class="wt-shop-license-websites-empty" id="wt-shop-license-websites-empty" style="display:none;">' . esc_html__( 'No websites activated for this license.', 'webthinkershop' ) . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
add_action( 'woocommerce_account_licenses_endpoint', 'wt_shop_myaccount_licenses_content' );
