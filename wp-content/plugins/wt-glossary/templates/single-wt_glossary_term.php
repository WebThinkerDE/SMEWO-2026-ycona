<?php
/**
 * Single Glossary Term Template
 *
 * This template is loaded by the plugin for single wt_glossary_term posts.
 * It uses get_header() / get_footer() from the active theme.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $post_id     = get_the_ID();
    $short_desc  = get_post_meta( $post_id, '_wt_glossary_short_desc', true );
    $synonyms    = get_post_meta( $post_id, '_wt_glossary_synonyms', true );
    $related_ids = get_post_meta( $post_id, '_wt_glossary_related', true );
    $first_letter = wt_glossary_get_first_letter( get_the_title() );

    if ( ! is_array( $related_ids ) ) {
        $related_ids = array();
    }

    // Get categories
    $categories = get_the_terms( $post_id, 'wt_glossary_category' );
?>

<section class="wt-glossary-single-wrapper">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="wt-glossary-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'wt-glossary' ); ?>">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'wt_glossary_term' ) ); ?>">
                <?php esc_html_e( 'Glossary', 'wt-glossary' ); ?>
            </a>
            <span class="wt-glossary-breadcrumb-sep">|</span>
            <span class="wt-glossary-breadcrumb-letter">
                <?php echo esc_html( $first_letter ); ?>
            </span>
            <span class="wt-glossary-breadcrumb-sep">|</span>
            <span class="wt-glossary-breadcrumb-current">
                <?php the_title(); ?>
            </span>
        </nav>

        <article class="wt-glossary-single-article">

            <!-- Header -->
            <header class="wt-glossary-single-header">
                <div class="wt-glossary-single-letter-badge">
                    <?php echo esc_html( $first_letter ); ?>
                </div>
                <h1 class="wt-glossary-single-title"><?php the_title(); ?></h1>

                <?php if ( ! empty( $short_desc ) ) : ?>
                    <p class="wt-glossary-single-short-desc"><?php echo esc_html( $short_desc ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $synonyms ) ) : ?>
                    <div class="wt-glossary-single-synonyms">
                        <strong><?php esc_html_e( 'Also known as:', 'wt-glossary' ); ?></strong>
                        <span><?php echo esc_html( $synonyms ); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
                    <div class="wt-glossary-single-categories">
                        <?php foreach ( $categories as $cat ) : ?>
                            <span class="wt-glossary-category-badge">
                                <?php echo esc_html( $cat->name ); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Featured Image -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="wt-glossary-single-image">
                    <?php the_post_thumbnail( 'large', array( 'class' => 'wt-glossary-featured-img' ) ); ?>
                </div>
            <?php endif; ?>

            <!-- Content / Description -->
            <div class="wt-glossary-single-content">
                <?php the_content(); ?>
            </div>

            <!-- Related Terms -->
            <?php if ( ! empty( $related_ids ) ) : ?>
                <div class="wt-glossary-related-terms">
                    <h3 class="wt-glossary-related-heading"><?php esc_html_e( 'Related Terms', 'wt-glossary' ); ?></h3>
                    <div class="row wt-glossary-related-row">
                        <?php foreach ( $related_ids as $related_id ) :
                            $related_post = get_post( $related_id );
                            if ( ! $related_post || $related_post->post_status !== 'publish' ) {
                                continue;
                            }
                            $related_short_desc = get_post_meta( $related_id, '_wt_glossary_short_desc', true );
                        ?>
                            <div class="col-12 col-md-6 col-lg-4 wt-glossary-term-col">
                                <div class="wt-glossary-term-card">
                                    <h4 class="wt-glossary-term-title">
                                        <a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
                                            <?php echo esc_html( $related_post->post_title ); ?>
                                        </a>
                                    </h4>
                                    <?php if ( ! empty( $related_short_desc ) ) : ?>
                                        <p class="wt-glossary-term-excerpt"><?php echo esc_html( $related_short_desc ); ?></p>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" class="wt-glossary-read-more">
                                        <?php esc_html_e( 'Read more', 'wt-glossary' ); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Back to glossary -->
            <div class="wt-glossary-back-link">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'wt_glossary_term' ) ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <?php esc_html_e( 'Back to Glossary', 'wt-glossary' ); ?>
                </a>
            </div>

        </article>

    </div>
</section>

<?php
endwhile;

get_footer();
