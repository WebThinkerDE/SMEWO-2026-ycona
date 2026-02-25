<?php
/**
 * 404 – Page not found
 *
 * Modern design consistent with the Thank-you page and empty cart.
 *
 * @package wt-shop
 * @since   1.0
 */

get_header(); ?>

<div id="primary" class="content-area container">
    <main id="main" class="site-main" role="main">
        <section class="wt-shop-404-wrap">
            <div class="wt-shop-404-hero">

                <!-- Large "404" number -->
                <div class="wt-shop-404-number" aria-hidden="true">404</div>

                <!-- Circle icon -->
                <div class="wt-shop-404-icon" aria-hidden="true">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>

                <h1 class="wt-shop-404-heading">
                    <?php esc_html_e( 'Page not found', 'ycona' ); ?>
                </h1>

                <p class="wt-shop-404-subheading">
                    <?php esc_html_e( "The page you're looking for doesn't exist or has been moved. Don't worry, let's get you back on track.", 'ycona' ); ?>
                </p>

                <div class="wt-shop-404-actions">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wt-shop-btn-404 wt-shop-btn-404-primary">
                        <i class="bi bi-house" aria-hidden="true"></i>
                        <?php esc_html_e( 'Back to homepage', 'ycona' ); ?>
                    </a>
                    <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="wt-shop-btn-404 wt-shop-btn-404-outline">
                            <i class="bi bi-bag" aria-hidden="true"></i>
                            <?php esc_html_e( 'Visit the shop', 'ycona' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            /* ── Popular products (random, same grid as thank-you & empty cart) ── */
            if ( function_exists( 'wc_get_products' ) ) :
                $suggested = wc_get_products( array(
                    'status'  => 'publish',
                    'limit'   => 4,
                    'orderby' => 'rand',
                    'return'  => 'ids',
                ) );

                if ( ! empty( $suggested ) ) : ?>
                    <section class="wt-shop-thankyou-also-like wt-shop-404-products">
                        <h2 class="wt-shop-thankyou-also-like-title">
                            <?php esc_html_e( 'While you\'re here, check these out', 'ycona' ); ?>
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
        </section>
    </main>
</div>

<?php get_footer(); ?>
