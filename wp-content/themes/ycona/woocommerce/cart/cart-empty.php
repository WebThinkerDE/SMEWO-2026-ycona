<?php
/**
 * Empty cart page – custom WebThinker Shop  design
 *
 * This template overrides WooCommerce's cart/cart-empty.php.
 * Uses the same colour language as the Thank-you page (primary, light-gray).
 *
 * @package webthinkershop
 * @since   1.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 * We suppress the default WC message because we render our own below.
 */
// Remove default empty-cart notice so we don't get a double message.
if ( function_exists( 'wc_clear_notices' ) ) {
    wc_clear_notices();
}

$shop_url = function_exists( 'wc_get_page_permalink' )
    ? wc_get_page_permalink( 'shop' )
    : home_url( '/shop/' );

$home_url = home_url( '/' );
?>

<div class="wt-shop-empty-cart-wrap">
    <div class="wt-shop-empty-cart-hero">

        <!-- Large circle icon (same style as TY checkmark, but cart icon) -->
        <div class="wt-shop-empty-cart-icon" aria-hidden="true">
            <i class="bi bi-cart-x"></i>
        </div>

        <h1 class="wt-shop-empty-cart-heading">
            <?php esc_html_e( 'Your cart is empty', 'webthinkershop' ); ?>
        </h1>

        <p class="wt-shop-empty-cart-subheading">
            <?php esc_html_e( 'Looks like you haven\'t added anything to your cart yet. Start exploring and find something you love!', 'webthinkershop' ); ?>
        </p>

        <div class="wt-shop-empty-cart-actions">
            <a href="<?php echo esc_url( $shop_url ); ?>" class="wt-shop-btn-empty-cart wt-shop-btn-empty-primary">
                <i class="bi bi-bag" aria-hidden="true"></i>
                <?php esc_html_e( 'Continue shopping', 'webthinkershop' ); ?>
            </a>
            <a href="<?php echo esc_url( $home_url ); ?>" class="wt-shop-btn-empty-cart wt-shop-btn-empty-outline">
                <i class="bi bi-house" aria-hidden="true"></i>
                <?php esc_html_e( 'Back to homepage', 'webthinkershop' ); ?>
            </a>
        </div>
    </div>

    <?php
    /* ── Popular products (random, same grid as thank-you page) ── */
    if ( function_exists( 'wc_get_products' ) ) :
        $suggested = wc_get_products( array(
            'status'  => 'publish',
            'limit'   => 4,
            'orderby' => 'rand',
            'return'  => 'ids',
        ) );

        if ( ! empty( $suggested ) ) : ?>
            <section class="wt-shop-thankyou-also-like wt-shop-empty-cart-products">
                <h2 class="wt-shop-thankyou-also-like-title">
                    <?php esc_html_e( 'Discover something new', 'webthinkershop' ); ?>
                </h2>
                <ul class="products columns-4">
                    <?php
                    foreach ( $suggested as $product_id ) {
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
        <?php endif;
    endif;
    ?>
</div>
