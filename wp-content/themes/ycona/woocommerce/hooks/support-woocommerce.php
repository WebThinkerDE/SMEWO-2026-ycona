<?php
// WooCommerce support (shop page, product layout, gallery)
add_theme_support( 'woocommerce' );
add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-slider' );

/**
 * Disable WooCommerce default styles – theme uses its own (main.css, etc.).
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Disable Select2/SelectWoo on checkout, cart and My Account so native select keeps theme style.
 * WooCommerce replaces <select> with Select2 after load, which overrides our CSS.
 * Register a no-op stub for selectWoo so plugins (e.g. Checkout Field Editor Pro) that call .selectWoo() do not throw.
 */

function wt_shop_disable_select2_on_checkout_cart() {
    if ( ! function_exists( 'is_checkout' ) || ! function_exists( 'is_cart' ) || ! function_exists( 'is_account_page' ) ) {
        return;
    }
    if ( ! is_checkout() && ! is_cart() && ! is_account_page() ) {
        return;
    }
    wp_dequeue_style( 'selectWoo' );
    wp_deregister_style( 'selectWoo' );
    wp_dequeue_script( 'selectWoo' );
    wp_deregister_script( 'selectWoo' );
    wp_dequeue_style( 'select2' );
    wp_deregister_style( 'select2' );
    wp_dequeue_script( 'select2' );
    wp_deregister_script( 'select2' );

    /* Stub so plugins calling $(...).selectWoo() or $(...).select2() do not throw */
    $stub = "if (typeof jQuery !== 'undefined') { jQuery.fn.selectWoo = jQuery.fn.selectWoo || function() { return this; }; jQuery.fn.select2 = jQuery.fn.select2 || function() { return this; }; }";
    wp_register_script( 'selectWoo', false, array( 'jquery' ), null, true );
    wp_add_inline_script( 'selectWoo', $stub, 'after' );
    wp_enqueue_script( 'selectWoo' );
}
add_action( 'wp_enqueue_scripts', 'wt_shop_disable_select2_on_checkout_cart', 100 );

/**
 * My Account page – add WordPress-style 2-column body class.
 */
function wt_shop_myaccount_body_class( $classes ) {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        $classes[] = 'has-2-columns';
    }
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        $classes[] = 'wt-shop-shop-page';
    }
    return $classes;
}
add_filter( 'body_class', 'wt_shop_myaccount_body_class' );

/**
 * Shop page – order products by date added (newest first).
 */
function wt_shop_shop_order_by_date( $args ) {
    if ( function_exists( 'is_shop' ) && is_shop() && ( ! isset( $_GET['orderby'] ) || empty( $_GET['orderby'] ) ) ) {
        $args['orderby'] = 'date';
        $args['order']   = 'ASC';
    }
    return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'wt_shop_shop_order_by_date' );

/**
 * Shop page – show 9 products per page.
 */
add_filter( 'loop_shop_per_page', function () {
    return 9;
}, 20 );

/**
 * Shop sidebar – render category tree with subcategories and product counts.
 */
function wt_shop_shop_category_sidebar() {
    if ( ! function_exists( 'wc_get_page_id' ) ) {
        return;
    }

    $shop_url       = get_permalink( wc_get_page_id( 'shop' ) );
    $default_cat_id = (int) get_option( 'default_product_cat', 0 );

    // Get all top-level categories.
    $parent_terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
    if ( is_wp_error( $parent_terms ) || empty( $parent_terms ) ) {
        return;
    }

    // Detect current category (if on a product_cat archive).
    $current_term_id  = 0;
    $current_ancestors = array();
    if ( is_product_category() ) {
        $queried = get_queried_object();
        if ( $queried && ! is_wp_error( $queried ) ) {
            $current_term_id   = (int) $queried->term_id;
            $current_ancestors = get_ancestors( $current_term_id, 'product_cat', 'taxonomy' );
        }
    }
    ?>
    <aside id="wt-shop-sidebar-cats" class="wt-shop-shop-sidebar" aria-label="<?php esc_attr_e( 'Product categories', 'webthinkershop' ); ?>">
        <h3 class="wt-shop-shop-sidebar-title"><?php esc_html_e( 'Categories', 'webthinkershop' ); ?></h3>

        <ul class="wt-shop-cat-list">
            <li class="wt-shop-cat-item<?php echo is_shop() ? ' wt-shop-cat-item-active' : ''; ?>">
                <a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'All Products', 'webthinkershop' ); ?></a>
            </li>
            <?php foreach ( $parent_terms as $parent ) :
                if ( $parent->slug === 'uncategorized' || (int) $parent->term_id === $default_cat_id ) {
                    continue;
                }
                $parent_link  = get_term_link( $parent );
                if ( is_wp_error( $parent_link ) ) {
                    continue;
                }
                $is_active    = ( $current_term_id === (int) $parent->term_id );
                $is_ancestor  = in_array( (int) $parent->term_id, $current_ancestors, true );
                $children     = get_terms( array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => $parent->term_id,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ) );
                $has_children = ! is_wp_error( $children ) && ! empty( $children );
                $is_open      = $is_active || $is_ancestor || $current_term_id === 0;
                ?>
                <li class="wt-shop-cat-item<?php echo $is_active ? ' wt-shop-cat-item-active' : ''; ?><?php echo $has_children ? ' wt-shop-cat-has-children' : ''; ?><?php echo $is_open && $has_children ? ' wt-shop-cat-open' : ''; ?>">
                    <a href="<?php echo esc_url( $parent_link ); ?>">
                        <?php echo esc_html( $parent->name ); ?>
                        <span class="wt-shop-cat-count"><?php echo (int) $parent->count; ?></span>
                    </a>
                    <?php if ( $has_children ) : ?>
                        <button type="button" class="wt-shop-cat-toggle" aria-label="<?php echo esc_attr( sprintf( __( 'Toggle %s subcategories', 'webthinkershop' ), $parent->name ) ); ?>">
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <ul class="wt-shop-cat-sublist">
                            <?php foreach ( $children as $child ) :
                                if ( $child->slug === 'uncategorized' || (int) $child->term_id === $default_cat_id ) {
                                    continue;
                                }
                                $child_link = get_term_link( $child );
                                if ( is_wp_error( $child_link ) ) {
                                    continue;
                                }
                                $child_active    = ( $current_term_id === (int) $child->term_id );
                                $child_ancestor  = in_array( (int) $child->term_id, $current_ancestors, true );
                                // Sub-subcategories
                                $grandchildren   = get_terms( array(
                                    'taxonomy'   => 'product_cat',
                                    'hide_empty' => true,
                                    'parent'     => $child->term_id,
                                    'orderby'    => 'name',
                                    'order'      => 'ASC',
                                ) );
                                $has_grandchildren = ! is_wp_error( $grandchildren ) && ! empty( $grandchildren );
                                $child_open        = $child_active || $child_ancestor;
                                ?>
                                <li class="wt-shop-cat-item<?php echo $child_active ? ' wt-shop-cat-item-active' : ''; ?><?php echo $has_grandchildren ? ' wt-shop-cat-has-children' : ''; ?><?php echo $child_open && $has_grandchildren ? ' wt-shop-cat-open' : ''; ?>">
                                    <a href="<?php echo esc_url( $child_link ); ?>">
                                        <?php echo esc_html( $child->name ); ?>
                                        <span class="wt-shop-cat-count"><?php echo (int) $child->count; ?></span>
                                    </a>
                                    <?php if ( $has_grandchildren ) : ?>
                                        <button type="button" class="wt-shop-cat-toggle" aria-label="<?php echo esc_attr( sprintf( __( 'Toggle %s subcategories', 'webthinkershop' ), $child->name ) ); ?>">
                                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </button>
                                        <ul class="wt-shop-cat-sublist">
                                            <?php foreach ( $grandchildren as $gc ) :
                                                if ( $gc->slug === 'uncategorized' || (int) $gc->term_id === $default_cat_id ) {
                                                    continue;
                                                }
                                                $gc_link = get_term_link( $gc );
                                                if ( is_wp_error( $gc_link ) ) {
                                                    continue;
                                                }
                                                $gc_active = ( $current_term_id === (int) $gc->term_id );
                                                ?>
                                                <li class="wt-shop-cat-item<?php echo $gc_active ? ' wt-shop-cat-item-active' : ''; ?>">
                                                    <a href="<?php echo esc_url( $gc_link ); ?>">
                                                        <?php echo esc_html( $gc->name ); ?>
                                                        <span class="wt-shop-cat-count"><?php echo (int) $gc->count; ?></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <?php wt_shop_shop_filters_sidebar(); ?>
    <?php
}

/**
 * Shop sidebar – additional filters: price, availability, on-sale, rating, tags.
 * Uses GET parameters on the current page URL so they work on both shop and category archives.
 */
function wt_shop_shop_filters_sidebar() {
    if ( ! function_exists( 'wc_get_page_id' ) ) {
        return;
    }

    // Current page URL (shop or category archive).
    $base_url = is_product_category() ? get_term_link( get_queried_object() ) : get_permalink( wc_get_page_id( 'shop' ) );
    if ( is_wp_error( $base_url ) ) {
        return;
    }

    // Current filter values from GET.
    $cur_min_price  = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : '';
    $cur_max_price  = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : '';
    $cur_on_sale    = ! empty( $_GET['on_sale'] );
    $cur_in_stock   = ! empty( $_GET['in_stock'] );
    $cur_rating     = isset( $_GET['rating_filter'] ) ? absint( $_GET['rating_filter'] ) : 0;
    $cur_tags       = isset( $_GET['product_tag'] ) ? array_map( 'sanitize_text_field', (array) explode( ',', wp_unslash( $_GET['product_tag'] ) ) ) : array();

    // Get global min/max prices for the range.
    global $wpdb;
    $price_range = $wpdb->get_row(
        "SELECT MIN( CAST( meta_value AS DECIMAL(10,2) ) ) AS min_price,
                MAX( CAST( meta_value AS DECIMAL(10,2) ) ) AS max_price
         FROM {$wpdb->postmeta}
         WHERE meta_key = '_price'
           AND meta_value != ''
           AND post_id IN (
               SELECT ID FROM {$wpdb->posts}
               WHERE post_type = 'product' AND post_status = 'publish'
           )"
    );
    $global_min = $price_range ? floor( (float) $price_range->min_price ) : 0;
    $global_max = $price_range ? ceil( (float) $price_range->max_price ) : 1000;
    if ( $global_min === $global_max ) {
        $global_max = $global_min + 1;
    }

    // Currency symbol.
    $currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$';

    // Product tags.
    $tags = get_terms( array(
        'taxonomy'   => 'product_tag',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );
    $has_tags = ! is_wp_error( $tags ) && ! empty( $tags );

    // Build hidden fields to preserve other query params.
    $preserve_keys = array( 'orderby', 'paged' );
    ?>
    <aside id="wt-shop-sidebar-filters" class="wt-shop-shop-sidebar wt-shop-shop-sidebar-filters" aria-label="<?php esc_attr_e( 'Product filters', 'webthinkershop' ); ?>">
        <form method="get" action="<?php echo esc_url( $base_url ); ?>" class="wt-shop-filters-form">
            <?php foreach ( $preserve_keys as $key ) :
                if ( ! empty( $_GET[ $key ] ) ) : ?>
                    <input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) ); ?>" />
                <?php endif;
            endforeach; ?>

            <!-- Price Range -->
            <div class="wt-shop-filter-section">
                <h4 class="wt-shop-filter-title"><?php esc_html_e( 'Price', 'webthinkershop' ); ?></h4>
                <div class="wt-shop-price-range-wrap">
                    <div class="wt-shop-price-slider-track" data-min="<?php echo esc_attr( $global_min ); ?>" data-max="<?php echo esc_attr( $global_max ); ?>">
                        <div class="wt-shop-price-slider-fill"></div>
                        <input type="range" class="wt-shop-price-range wt-shop-price-min-range" min="<?php echo esc_attr( $global_min ); ?>" max="<?php echo esc_attr( $global_max ); ?>" value="<?php echo esc_attr( $cur_min_price !== '' ? $cur_min_price : $global_min ); ?>" step="1" />
                        <input type="range" class="wt-shop-price-range wt-shop-price-max-range" min="<?php echo esc_attr( $global_min ); ?>" max="<?php echo esc_attr( $global_max ); ?>" value="<?php echo esc_attr( $cur_max_price !== '' ? $cur_max_price : $global_max ); ?>" step="1" />
                    </div>
                    <div class="wt-shop-price-inputs">
                        <label class="wt-shop-price-input-label">
                            <span><?php echo esc_html( $currency ); ?></span>
                            <input type="number" name="min_price" class="wt-shop-price-input wt-shop-price-min-input" value="<?php echo esc_attr( $cur_min_price !== '' ? $cur_min_price : $global_min ); ?>" min="<?php echo esc_attr( $global_min ); ?>" max="<?php echo esc_attr( $global_max ); ?>" placeholder="<?php echo esc_attr( $global_min ); ?>" />
                        </label>
                        <span class="wt-shop-price-sep">&ndash;</span>
                        <label class="wt-shop-price-input-label">
                            <span><?php echo esc_html( $currency ); ?></span>
                            <input type="number" name="max_price" class="wt-shop-price-input wt-shop-price-max-input" value="<?php echo esc_attr( $cur_max_price !== '' ? $cur_max_price : $global_max ); ?>" min="<?php echo esc_attr( $global_min ); ?>" max="<?php echo esc_attr( $global_max ); ?>" placeholder="<?php echo esc_attr( $global_max ); ?>" />
                        </label>
                    </div>
                </div>
            </div>

            <!-- Availability -->
            <div class="wt-shop-filter-section">
                <h4 class="wt-shop-filter-title"><?php esc_html_e( 'Availability', 'webthinkershop' ); ?></h4>
                <label class="wt-shop-filter-check">
                    <input type="checkbox" name="in_stock" value="1" <?php checked( $cur_in_stock ); ?> />
                    <span><?php esc_html_e( 'In Stock only', 'webthinkershop' ); ?></span>
                </label>
            </div>

            <!-- On Sale -->
            <div class="wt-shop-filter-section">
                <h4 class="wt-shop-filter-title"><?php esc_html_e( 'Offers', 'webthinkershop' ); ?></h4>
                <label class="wt-shop-filter-check">
                    <input type="checkbox" name="on_sale" value="1" <?php checked( $cur_on_sale ); ?> />
                    <span><?php esc_html_e( 'On Sale', 'webthinkershop' ); ?></span>
                </label>
            </div>

            <!-- Rating -->
            <div class="wt-shop-filter-section">
                <h4 class="wt-shop-filter-title"><?php esc_html_e( 'Rating', 'webthinkershop' ); ?></h4>
                <div class="wt-shop-filter-rating-list">
                    <?php for ( $r = 5; $r >= 1; $r-- ) : ?>
                        <label class="wt-shop-filter-rating-item<?php echo $cur_rating === $r ? ' wt-shop-filter-rating-active' : ''; ?>">
                            <input type="radio" name="rating_filter" value="<?php echo esc_attr( $r ); ?>" <?php checked( $cur_rating, $r ); ?> />
                            <span class="wt-shop-filter-stars" aria-label="<?php echo esc_attr( sprintf( __( '%d stars & up', 'webthinkershop' ), $r ) ); ?>">
                                <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" class="<?php echo $s <= $r ? 'wt-shop-star-filled' : 'wt-shop-star-empty'; ?>"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                                <?php endfor; ?>
                            </span>
                            <span class="wt-shop-filter-rating-text"><?php echo esc_html( sprintf( __( '%d & up', 'webthinkershop' ), $r ) ); ?></span>
                        </label>
                    <?php endfor; ?>
                    <?php if ( $cur_rating > 0 ) : ?>
                        <label class="wt-shop-filter-rating-item wt-shop-filter-rating-clear">
                            <input type="radio" name="rating_filter" value="0" />
                            <span><?php esc_html_e( 'All ratings', 'webthinkershop' ); ?></span>
                        </label>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( $has_tags ) : ?>
            <!-- Product Tags -->
            <div class="wt-shop-filter-section">
                <h4 class="wt-shop-filter-title"><?php esc_html_e( 'Tags', 'webthinkershop' ); ?></h4>
                <div class="wt-shop-filter-tags">
                    <?php foreach ( $tags as $tag ) : ?>
                        <label class="wt-shop-filter-tag<?php echo in_array( $tag->slug, $cur_tags, true ) ? ' wt-shop-filter-tag-active' : ''; ?>">
                            <input type="checkbox" name="product_tag[]" value="<?php echo esc_attr( $tag->slug ); ?>" <?php checked( in_array( $tag->slug, $cur_tags, true ) ); ?> />
                            <span><?php echo esc_html( $tag->name ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Apply / Reset -->
            <div class="wt-shop-filter-actions">
                <button type="submit" class="wt-shop-filter-apply"><?php esc_html_e( 'Apply Filters', 'webthinkershop' ); ?></button>
                <a href="<?php echo esc_url( $base_url ); ?>" class="wt-shop-filter-reset"><?php esc_html_e( 'Reset', 'webthinkershop' ); ?></a>
            </div>
        </form>
    </aside>
    <?php
}

/**
 * Handle shop sidebar filters via pre_get_posts.
 */
function wt_shop_shop_sidebar_filters_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( ! function_exists( 'is_shop' ) ) {
        return;
    }
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }

    // Price filter – WooCommerce handles min_price/max_price natively via its own hooks,
    // but we add as fallback.
    if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
        $meta_query = $query->get( 'meta_query', array() );
        if ( ! empty( $_GET['min_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => floatval( $_GET['min_price'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
        if ( ! empty( $_GET['max_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => floatval( $_GET['max_price'] ),
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }
        $query->set( 'meta_query', $meta_query );
    }

    // In Stock only.
    if ( ! empty( $_GET['in_stock'] ) ) {
        $meta_query   = $query->get( 'meta_query', array() );
        $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        );
        $query->set( 'meta_query', $meta_query );
    }

    // On Sale.
    if ( ! empty( $_GET['on_sale'] ) ) {
        $sale_ids = wc_get_product_ids_on_sale();
        if ( ! empty( $sale_ids ) ) {
            $query->set( 'post__in', array_merge( (array) $query->get( 'post__in' ), $sale_ids ) );
        } else {
            // No products on sale – force empty result.
            $query->set( 'post__in', array( 0 ) );
        }
    }

    // Rating filter.
    if ( ! empty( $_GET['rating_filter'] ) ) {
        $min_rating   = absint( $_GET['rating_filter'] );
        $meta_query   = $query->get( 'meta_query', array() );
        $meta_query[] = array(
            'key'     => '_wc_average_rating',
            'value'   => $min_rating,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
        $query->set( 'meta_query', $meta_query );
    }

    // Product tags.
    if ( ! empty( $_GET['product_tag'] ) ) {
        $tag_slugs = is_array( $_GET['product_tag'] )
            ? array_map( 'sanitize_text_field', $_GET['product_tag'] )
            : array_map( 'sanitize_text_field', explode( ',', wp_unslash( $_GET['product_tag'] ) ) );
        $tax_query   = $query->get( 'tax_query', array() );
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tag_slugs,
        );
        $query->set( 'tax_query', $tax_query );
    }
}
add_action( 'pre_get_posts', 'wt_shop_shop_sidebar_filters_query' );

/**
 * Checkout stepper breadcrumb – Warenkorb > Kasse > Bestellung abgeschlossen.
 *
 * Shows on cart, checkout and order-received pages.
 * Active step = primary color, completed steps = black, future steps = gray.
 */
function wt_shop_checkout_stepper() {
    if ( ! function_exists( 'is_cart' ) ) {
        return;
    }

    $is_cart      = is_cart();
    $is_checkout  = is_checkout() && ! is_wc_endpoint_url( 'order-received' );
    $is_thankyou  = is_checkout() && is_wc_endpoint_url( 'order-received' );

    if ( ! $is_cart && ! $is_checkout && ! $is_thankyou ) {
        return;
    }

    // Determine current step: 1 = cart, 2 = checkout, 3 = order complete
    if ( $is_thankyou ) {
        $current_step = 3;
    } elseif ( $is_checkout ) {
        $current_step = 2;
    } else {
        $current_step = 1;
    }

    $cart_url     = wc_get_cart_url();
    $checkout_url = wc_get_checkout_url();

    $steps = array(
        1 => array(
            'label' => __( 'Warenkorb', 'webthinkershop' ),
            'url'   => $cart_url,
        ),
        2 => array(
            'label' => __( 'Kasse', 'webthinkershop' ),
            'url'   => $checkout_url,
        ),
        3 => array(
            'label' => __( 'Bestellung abgeschlossen', 'webthinkershop' ),
            'url'   => '',
        ),
    );

    echo '<nav class="wt-shop-checkout-stepper" aria-label="' . esc_attr__( 'Checkout steps', 'webthinkershop' ) . '">';
    echo '<ol class="wt-shop-stepper-list">';

    foreach ( $steps as $step_num => $step ) {
        if ( $step_num < $current_step ) {
            $class = 'wt-shop-stepper-step wt-shop-stepper-completed';
        } elseif ( $step_num === $current_step ) {
            $class = 'wt-shop-stepper-step wt-shop-stepper-active';
        } else {
            $class = 'wt-shop-stepper-step wt-shop-stepper-upcoming';
        }

        echo '<li class="' . esc_attr( $class ) . '">';

        if ( $step_num < $current_step && ! empty( $step['url'] ) ) {
            echo '<a href="' . esc_url( $step['url'] ) . '">' . esc_html( $step['label'] ) . '</a>';
        } else {
            echo '<span>' . esc_html( $step['label'] ) . '</span>';
        }

        // Arrow separator (not after last step)
        if ( $step_num < 3 ) {
            echo '<span class="wt-shop-stepper-sep" aria-hidden="true">';
            echo '<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            echo '</span>';
        }

        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}
add_action( 'woocommerce_before_cart', 'wt_shop_checkout_stepper', 5 );
add_action( 'woocommerce_before_checkout_form', 'wt_shop_checkout_stepper', 5 );

/**
 * Sale badge: append percentage (e.g. -17%) next to "Angebot", secondary color.
 * Filter woocommerce_sale_flash appends <span class="woocommerce-sale-percent">-X%</span>.
 */
function wt_shop_sale_percentage_badge( $html, $post, $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return $html;
	}
	$pct = 0;
	if ( $product->is_type( 'simple' ) ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale >= 0 ) {
			$pct = (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
		}
	} elseif ( $product->is_type( 'variable' ) ) {
		$pct = 0;
		foreach ( $product->get_children() as $variation_id ) {
			$v = wc_get_product( $variation_id );
			if ( ! $v || ! $v->is_on_sale() ) {
				continue;
			}
			$r = (float) $v->get_regular_price();
			$s = (float) $v->get_sale_price();
			if ( $r > 0 && $s >= 0 ) {
				$pct = max( $pct, (int) round( ( ( $r - $s ) / $r ) * 100 ) );
			}
		}
	}
	if ( $pct > 0 ) {
		$html .= '<span class="woocommerce-sale-percent" aria-hidden="true">-' . (int) $pct . '%</span>';
	}
	return $html;
}
add_filter( 'woocommerce_sale_flash', 'wt_shop_sale_percentage_badge', 10, 3 );

/**
 * Distinct headings: upsells vs cross-sells.
 * Upsells = "Das passt dazu" (frequently bought with this).
 * Cross-sells = "Das könnte dir auch gefallen" (you might also like).
 */
function wt_shop_upsells_heading( $heading ) {
	return __( 'Das passt dazu', 'webthinkershop' );
}
add_filter( 'woocommerce_product_upsells_products_heading', 'wt_shop_upsells_heading' );

/**
 * Cross-sells heading (used in wt_shop_single_product_cross_sells).
 */
function wt_shop_cross_sells_heading() {
	return __( 'Das könnte dir auch gefallen', 'webthinkershop' );
}

/**
 * Convert a WooCommerce product list (related / upsells) into Swiper markup.
 *
 * WooCommerce outputs:
 *   <section class="related products">
 *     <h2>…</h2>
 *     <ul class="products columns-4">
 *       <li class="product …">…</li>
 *     </ul>
 *   </section>
 *
 * We transform it to:
 *   <section class="related products">
 *     <h2>…</h2>
 *     <div class="wt-shop-products-swiper-wrap">
 *       <div class="swiper wt-shop-products-swiper">
 *         <div class="swiper-wrapper">
 *           <div class="swiper-slide product …">…</div>
 *         </div>
 *       </div>
 *       <button class="wt-shop-swiper-btn …">…</button>
 *     </div>
 *   </section>
 */
function wt_shop_convert_product_list_to_swiper( $html ) {
	/* Replace only the first <ul class="products …"> with swiper wrapper divs */
	$html = preg_replace(
		'/<ul\s+class="products([^"]*)">/i',
		'<div class="wt-shop-products-swiper-wrap"><div class="swiper wt-shop-products-swiper"><div class="swiper-wrapper">',
		$html,
		1
	);

	/* Replace only the first </ul> (the products list we opened above) */
	$html = preg_replace(
		'/<\/ul>/i',
		'</div></div>' /* close swiper-wrapper + swiper */
		. '<button type="button" class="wt-shop-swiper-btn wt-shop-swiper-btn-prev" aria-label="' . esc_attr__( 'Previous', 'webthinkershop' ) . '"><i class="bi bi-chevron-left"></i></button>'
		. '<button type="button" class="wt-shop-swiper-btn wt-shop-swiper-btn-next" aria-label="' . esc_attr__( 'Next', 'webthinkershop' ) . '"><i class="bi bi-chevron-right"></i></button>'
		. '</div>', /* close wrap */
		$html,
		1
	);

	/* Convert <li class="…"> to <div class="swiper-slide …"> */
	$html = preg_replace( '/<li\s+class="/i', '<div class="swiper-slide ', $html );
	$html = preg_replace( '/<li\s/i', '<div ', $html );
	$html = str_replace( '</li>', '</div>', $html );

	return $html;
}

/* Hook into related products */
add_action( 'woocommerce_before_template_part', function ( $template_name ) {
	if ( $template_name === 'single-product/related.php' ) {
		ob_start();
	}
}, 10 );

add_action( 'woocommerce_after_template_part', function ( $template_name ) {
	if ( $template_name === 'single-product/related.php' ) {
		echo wt_shop_convert_product_list_to_swiper( ob_get_clean() );
	}
}, 10 );

/* Hook into upsell products */
add_action( 'woocommerce_before_template_part', function ( $template_name ) {
	if ( $template_name === 'single-product/up-sells.php' ) {
		ob_start();
	}
}, 10 );

add_action( 'woocommerce_after_template_part', function ( $template_name ) {
	if ( $template_name === 'single-product/up-sells.php' ) {
		echo wt_shop_convert_product_list_to_swiper( ob_get_clean() );
	}
}, 10 );

/**
 * Show cross-sell products on single product page (normally only on cart).
 * Hooked after upsells (priority 25 on woocommerce_after_single_product_summary).
 * Outputs a standard <ul class="products">…</ul> then converts to Swiper.
 */
function wt_shop_single_product_cross_sells() {
	global $product;
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$cross_sell_ids = $product->get_cross_sell_ids();
	if ( empty( $cross_sell_ids ) ) {
		return;
	}

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'post__in'       => $cross_sell_ids,
		'orderby'        => 'post__in',
	);

	$cross_sells = new WP_Query( $args );
	if ( ! $cross_sells->have_posts() ) {
		return;
	}

	/* Build standard WooCommerce markup, then convert to Swiper */
	ob_start();
	?>
	<section class="cross-sells products">
		<h2><?php echo esc_html( wt_shop_cross_sells_heading() ); ?></h2>
		<ul class="products columns-4">
			<?php while ( $cross_sells->have_posts() ) : $cross_sells->the_post(); ?>
				<?php
				global $product;
				$product = wc_get_product( get_the_ID() );
				wc_get_template_part( 'content', 'product' );
				?>
			<?php endwhile; ?>
		</ul>
	</section>
	<?php
	wp_reset_postdata();
	echo wt_shop_convert_product_list_to_swiper( ob_get_clean() );
}
add_action( 'woocommerce_after_single_product_summary', 'wt_shop_single_product_cross_sells', 25 );

/**
 * Thank you page: "You may also like" – grid of 4 products.
 */
function wt_shop_thankyou_you_may_also_like( $order_id ) {
	if ( ! $order_id || ! function_exists( 'wc_get_products' ) ) {
		return;
	}

	$products = wc_get_products( array(
		'status'  => 'publish',
		'limit'   => 4,
		'orderby' => 'rand',
		'return'  => 'ids',
	) );

	if ( empty( $products ) ) {
		return;
	}
	?>
	<section class="wt-shop-thankyou-also-like">
		<h2 class="wt-shop-thankyou-also-like-title"><?php esc_html_e( 'You may also like', 'webthinkershop' ); ?></h2>
		<ul class="products columns-4">
			<?php
			foreach ( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product && $product->is_visible() ) {
					global $product;
					$product = wc_get_product( $product_id );
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>
		</ul>
	</section>
	<?php
}
add_action( 'woocommerce_thankyou', 'wt_shop_thankyou_you_may_also_like', 20 );

