<?php

/**
 * Mini cart – return cart page URL (Warenkorb).
 */
function wt_shop_mini_cart_cart_url() {
    if ( function_exists( 'wc_get_page_id' ) ) {
        $cart_id = wc_get_page_id( 'cart' );
        if ( $cart_id > 0 ) {
            return get_permalink( $cart_id );
        }
    }
    if ( function_exists( 'wc_get_cart_url' ) ) {
        return wc_get_cart_url();
    }
    return home_url( '/cart/' );
}

/**
 * Mini cart – count HTML for header (used in fragment).
 */
function wt_shop_mini_cart_count_html() {
    if ( ! function_exists( 'WC' ) ) {
        return '<span class="wt-mini-cart-count" data-count="0">0</span>';
    }
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $label = $count > 0 ? (string) $count : esc_html__( 'Empty', 'webthinkershop' );
    return '<span class="wt-mini-cart-count" data-count="' . absint( $count ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Mini cart display model: panel (default) or dropdown.
 */
function wt_shop_mini_cart_model() {
    $opts = get_option( 'wt_shop_theme_options_all', array() );
    $model = is_array( $opts ) && ! empty( $opts['mini_cart_model'] ) ? (string) $opts['mini_cart_model'] : 'panel';
    if ( ! in_array( $model, array( 'panel', 'dropdown' ), true ) ) {
        $model = 'panel';
    }
    return $model;
}

/**
 * Mini cart – panel content HTML (items, subtotal, link to cart page).
 * Cart URL uses cart page permalink directly so it is never redirected to checkout by filters.
 */
function wt_shop_mini_cart_content_html() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return '<div class="wt-mini-cart-items"><p class="wt-mini-cart-empty">' . esc_html__( 'Your cart is empty.', 'webthinkershop' ) . '</p></div>';
    }
    $cart     = WC()->cart;
    $cart_url = wt_shop_mini_cart_cart_url();
    if ( $cart->is_empty() ) {
        return '<div class="wt-mini-cart-items"><p class="wt-mini-cart-empty">' . esc_html__( 'Your cart is empty.', 'webthinkershop' ) . '</p></div>' .
            '<div class="wt-mini-cart-subtotal d-none"><span>' . esc_html__( 'Total:', 'webthinkershop' ) . '</span><strong></strong></div>' .
            '<a class="wt-btn wt-btn-primary wt-btn-cart d-none" href="' . esc_url( $cart_url ) . '">' . esc_html__( 'Go to cart', 'webthinkershop' ) . '</a>';
    }
    $model = wt_shop_mini_cart_model();
    ob_start();
    if ( $model === 'dropdown' ) {
        echo '<div class="gn-mini-cart-items">';
    } else {
        echo '<div class="wt-mini-cart-items">';
    }
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $product = $cart_item['data'];
        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            continue;
        }
        $remove_url = wc_get_cart_remove_url( $cart_item_key );
        $thumbnail_id = $product->get_image_id();
        $name = $product->get_name();
        $permalink = $product->get_permalink();
        $price = $cart_item['quantity'] * (float) $product->get_price();
        ?>
        <div class="<?php echo esc_attr( $model === 'dropdown' ? 'wt-mini-cart-item gn-mini-cart-item d-flex align-items-center mb-2' : 'wt-mini-cart-item d-flex align-items-center mb-2' ); ?>" data-cart-item="<?php echo esc_attr( $cart_item_key ); ?>">
            <a href="<?php echo esc_url( $remove_url ); ?>" class="remove remove_from_cart_button me-2" aria-label="<?php esc_attr_e( 'Remove this item', 'webthinkershop' ); ?>" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>">×</a>
            <div class="<?php echo esc_attr( $model === 'dropdown' ? 'wt-mini-cart-thumb gn-mini-cart-thumb me-2' : 'wt-mini-cart-thumb me-2' ); ?>">
                <?php if ( $thumbnail_id ) : ?>
                    <?php echo wp_get_attachment_image( $thumbnail_id, 'woocommerce_thumbnail', false, array( 'alt' => esc_attr( $name ), 'width' => '40', 'height' => '30' ) ); ?>
                <?php else : ?>
                    <span class="wt-mini-cart-thumb-placeholder" aria-hidden="true">—</span>
                <?php endif; ?>
            </div>
            <div class="<?php echo esc_attr( $model === 'dropdown' ? 'wt-mini-cart-details gn-mini-cart-details flex-grow-1' : 'wt-mini-cart-details flex-grow-1' ); ?>">
                <div class="<?php echo esc_attr( $model === 'dropdown' ? 'wt-mini-cart-name gn-mini-cart-name' : 'wt-mini-cart-name' ); ?>">
                    <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
                </div>
            </div>
            <div class="<?php echo esc_attr( $model === 'dropdown' ? 'wt-mini-cart-price gn-mini-cart-price text-muted small' : 'wt-mini-cart-price text-muted small' ); ?>"><?php echo wc_price( $price ); ?></div>
        </div>
        <?php
    }
    echo '</div>';
    echo '<div class="' . esc_attr( $model === 'dropdown' ? 'wt-mini-cart-subtotal gn-mini-cart-subtotal' : 'wt-mini-cart-subtotal' ) . '">';
    echo '<span>' . esc_html__( 'Total:', 'webthinkershop' ) . '</span>';
    echo '<strong>' . $cart->get_cart_subtotal() . '</strong>';
    echo '</div>';
    if ( $model === 'dropdown' ) {
        echo '<a class="wt-btn wt-btn-primary wt-btn-cart gn-btn gn-btn-primary gn-btn-checkout" href="' . esc_url( $cart_url ) . '">' . esc_html__( 'Go to cart', 'webthinkershop' ) . '</a>';
    } else {
        echo '<a class="wt-btn wt-btn-primary wt-btn-cart" href="' . esc_url( $cart_url ) . '">' . esc_html__( 'Go to cart', 'webthinkershop' ) . '</a>';
    }
    return ob_get_clean();
}

/**
 * WooCommerce cart fragments – update count and panel content on cart change.
 */
function wt_shop_mini_cart_fragments( $fragments ) {
    $fragments['.wt-mini-cart-count-wrap'] = '<span class="wt-mini-cart-count-wrap">' . wt_shop_mini_cart_count_html() . '</span>';
    $fragments['#wt-mini-cart-content'] = '<div id="wt-mini-cart-content" class="wt-mini-cart-content">' . wt_shop_mini_cart_content_html() . '</div>';
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'wt_shop_mini_cart_fragments' );
