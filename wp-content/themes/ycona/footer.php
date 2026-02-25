<?php
/**
 * Copyright (c) 2025 by Granit Nebiu
 *
 * All rights are reserved. Reproduction or transmission in whole or in part, in
 * any form or by any means, electronic, mechanical or otherwise, is prohibited
 * without the prior written consent of the copyright owner.
 *
 * Functions and definitions
 *
 * @package WordPress
 * @subpackage ycona
 * @author Granit Nebiu
 * @since 1.0
 */
$current_lang_code = "";
if (defined('ICL_LANGUAGE_CODE'))
{
    $current_lang_code = ICL_LANGUAGE_CODE;
}

$theme_options     = get_option( 'wt_shop_theme_options_' . $current_lang_code, array() );
$theme_options_all = get_option( 'wt_shop_theme_options_all', array() );
if ( ! is_array( $theme_options ) ) {
	$theme_options = array();
}
if ( ! is_array( $theme_options_all ) ) {
	$theme_options_all = array();
}


$footer_title_1                 = $theme_options['footer_title_1'] ?? "";
$footer_title_2                 = $theme_options['footer_title_2'] ?? "";
$footer_title_3                 = $theme_options['footer_title_3'] ?? "";
$footer_title_4                 = $theme_options['footer_title_4'] ?? "";
$footer_title_5                 = $theme_options['footer_title_5'] ?? "";
$footer_title_6                 = $theme_options['footer_title_6'] ?? "";
$footer_description             = $theme_options['footer_description'] ?? "";
$footer_support_text            = $theme_options['footer_support_text'] ?? "";

//echo '<pre>';
//print_r($theme_options);
//echo '</pre>';

$footer_address         = $theme_options['footer_address'] ?? "";
$footer_address_2       = $theme_options['footer_address_2'] ?? "";
$footer_address_2_link  = $theme_options['footer_address_2_link'] ?? "";

$copyright                      = $theme_options['copyright'] ?? "";

$footer_phone_number_title      = $theme_options['footer_phone_number_title'] ?? "";
$footer_phone_number            = $theme_options['footer_phone_number'] ?? "";
$footer_phone_number_link       = $theme_options['footer_phone_number_link'] ?? "";

$footer_logo                  = $theme_options_all['wt_shop_footer_logo'] ?? "";
$footer_logo_2                = $theme_options_all['wt_shop_footer_logo_2'] ?? "";
$footer_logo_3                = $theme_options_all['wt_shop_footer_logo_3'] ?? "";

$footer_apple_link         = $theme_options_all['footer_apple_link'] ?? "";
$footer_android_link       = $theme_options_all['footer_android_link'] ?? "";

$footer_support_theme_icons   = $theme_options_all['footer_support_theme_icons'] ?? array();
$footer_support_payment_icons = $theme_options_all['footer_support_payment_icons'] ?? array();

if ( ! is_array( $footer_support_payment_icons ) ) {
    $footer_support_payment_icons = array();
}
$social_links               = $theme_options_all['social_links'] ?? array();
if ( ! is_array( $social_links ) ) {
    $social_links = array();
}
$footer_right_column_order  = get_option( 'wt_shop_footer_right_column_order', array( 'payments', 'social' ) );
if ( ! is_array( $footer_right_column_order ) ) {
    $footer_right_column_order = array( 'payments', 'social' );
}
$footer_right_column_order  = array_values(
    array_filter(
        $footer_right_column_order,
        static function( $value ) {
            return in_array( $value, array( 'payments', 'social' ), true );
        }
    )
);
if ( count( $footer_right_column_order ) < 2 ) {
    $footer_right_column_order = array( 'payments', 'social' );
}
$footer_social_twitter_alt   = $theme_options_all['footer_social_twitter_alt'] ?? '';
$footer_social_instagram_alt = $theme_options_all['footer_social_instagram_alt'] ?? '';
$footer_social_tiktok_alt    = $theme_options_all['footer_social_tiktok_alt'] ?? '';
$template_uri = get_template_directory_uri();
$base_url = ( parse_url( home_url(), PHP_URL_SCHEME ) ?: 'https' ) . '://' . ( parse_url( home_url(), PHP_URL_HOST ) ?: $_SERVER['HTTP_HOST'] ?? '' );

$nav_footer_1 = array(
    'theme_location' => 'footer-menu-1',
    'menu_class' => 'footer-menu',
    'items_wrap' => '%3$s',
);

$nav_footer_2 = array(
    'theme_location' => 'footer-menu-2',
    'menu_class' => 'footer-menu',
    'items_wrap' => '%3$s',
);

$nav_footer_3 = array(
    'theme_location' => 'footer-menu-3',
    'menu_class' => 'footer-menu',
    'items_wrap' => '%3$s',
);

$nav_footer_4 = array(
    'theme_location' => 'footer-menu-4',
    'menu_class' => 'footer-menu',
    'items_wrap' => '%3$s',
);


?>

<footer class="footer-container">
    <div class="container py-5 ">
        <div class="footer-body d-flex flex-column flex-xl-row justify-content-between">
            <div class="col-12 col-lg-3 pe-md-2">
                <div class="footer-logo">
                    <img width="100" src="<?php echo $footer_logo ?>" alt="webthinkershop logo footer" />
                </div>
                <p class="font-bold pb-2 pt-5 pt-lg-0 wt-shop-h4"><?php echo $footer_title_1 ?></p>
                <?php
                if ( ! empty( $footer_description ) ) {
                    echo wp_kses_post( $footer_description );
                } else {
                    ?>
                    <p>Add text in theme options</p>
                    <?php
                }
                ?>
            </div>
            <div class="col-12 col-lg-2 ps-md-2">
                <p class="font-bold  pb-2 pb-lg-5 pt-5 pt-lg-0 wt-shop-h4"><?php echo $footer_title_2 ?></p>
	            <?php wp_nav_menu($nav_footer_1); ?>
		  
            </div>
            <div class="col-12 col-lg-2">
                <p class="font-bold pb-2 pb-lg-5 pt-5 pt-lg-0 wt-shop-h4"><?php echo $footer_title_3 ?></p>
	            <?php wp_nav_menu($nav_footer_2); ?>
            </div>
            <div class="col-12 col-lg-2">
                <p class="font-bold pb-2 pb-lg-5 pt-5 pt-lg-0 wt-shop-h4"><?php echo $footer_title_4 ?></p>
	            <?php wp_nav_menu($nav_footer_3); ?>
                <?php if ( ! empty( $footer_support_text ) ) : ?>
                    <p><?php echo esc_html( $footer_support_text ); ?></p>
                <?php else : ?>
                    <p>Support response: 24–48h</p>
                <?php endif; ?>
            </div>
            <div class="col-12 col-lg-3">
                <?php
                $footer_right_first = true;
                foreach ( $footer_right_column_order as $block_key ) :
                    if ( $block_key === 'payments' ) :
                        $block_mt = $footer_right_first ? '' : ' mt-5';
                        if ( $footer_title_5 !== '' ) : ?>
                            <p class="font-bold pb-2 pt-5 pt-lg-0 wt-shop-h4<?php echo esc_attr( $block_mt ); ?>"><?php echo esc_html( $footer_title_5 ); ?></p>
                        <?php endif; ?>
                        <div class="support d-flex flex-row justify-content-start flex-wrap gap-3<?php echo ( $footer_title_5 === '' ) ? esc_attr( $block_mt ) : ''; ?>">
                            <?php
                            if ( ! empty( $footer_support_payment_icons ) ) {
                                foreach ( $footer_support_payment_icons as $icon ) {
                                    $img = is_array( $icon ) ? ( $icon['image'] ?? '' ) : '';
                                    $alt = is_array( $icon ) ? ( $icon['alt'] ?? '' ) : '';
                                    if ( $img === '' ) continue;
                                    if ( strpos( $img, 'http' ) === 0 || strpos( $img, '//' ) === 0 ) {
                                        $src = $img;
                                    } elseif ( isset( $img[0] ) && $img[0] === '/' ) {
                                        $src = $base_url . $img;
                                    } else {
                                        $src = $template_uri . '/' . $img;
                                    }
                                    echo '<img width="auto" height="50" class="search-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( $src ) . '" />';
                                }
                            } else {
                                ?>
                                <img width="auto" height="50" class="search-icon" alt="visa" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/visa.svg"/>
                                <img width="auto" height="50" class="search-icon" alt="mastercard" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/mastercard.svg"/>
                                <img width="auto" height="50" class="search-icon" alt="stripe" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/stripe.svg"/>
                                <img width="auto" height="50" class="search-icon" alt="paypal" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/paypal.svg"/>
                                <img width="auto" height="50" class="search-icon" alt="klarna" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/klarna.svg"/>
                                <?php
                            }
                            ?>
                        </div>
                    <?php
                    elseif ( $block_key === 'social' ) :
                        $block_mt = $footer_right_first ? '' : ' mt-5';
                        if ( $footer_title_6 !== '' ) : ?>
                            <p class="font-bold pb-2 pt-5 pt-lg-0 wt-shop-h4<?php echo esc_attr( $block_mt ); ?>"><?php echo esc_html( $footer_title_6 ); ?></p>
                        <?php endif; ?>
                        <div class="socials d-flex flex-row justify-content-start align-content-center flex-wrap gap-3<?php echo ( $footer_title_6 === '' ) ? esc_attr( $block_mt ) : ''; ?>">
                            <?php
                            if ( ! empty( $social_links ) ) {
                                foreach ( $social_links as $item ) {
                                    $url = is_array( $item ) ? ( $item['url'] ?? '' ) : '';
                                    $img = is_array( $item ) ? ( $item['image'] ?? '' ) : '';
                                    $alt = is_array( $item ) ? ( $item['alt'] ?? '' ) : '';
                                    if ( $url === '' && $img === '' ) continue;
                                    if ( $img !== '' ) {
                                        if ( strpos( $img, 'http' ) === 0 || strpos( $img, '//' ) === 0 ) {
                                            $src = $img;
                                        } elseif ( isset( $img[0] ) && $img[0] === '/' ) {
                                            $src = $base_url . $img;
                                        } else {
                                            $src = $template_uri . '/' . $img;
                                        }
                                    } else {
                                        $src = $template_uri . '/assets/img/vectors/twitter.svg';
                                    }
                                    echo '<div><a href="' . esc_url( $url ?: '#' ) . '"><img width="40" class="search-icon" alt="' . esc_attr( $alt ?: 'social' ) . '" src="' . esc_url( $src ) . '" /></a></div>';
                                }
                            } else {
                                ?>
                                <div>
                                    <a href="/">
                                        <img width="40" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/twitter.svg" alt="<?php echo esc_attr( $footer_social_twitter_alt ?: 'social twitter' ); ?>">
                                    </a>
                                </div>
                                <div>
                                    <a href="/">
                                        <img width="40" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/tiktok.svg" alt="<?php echo esc_attr( $footer_social_tiktok_alt ?: 'social tiktok' ); ?>">
                                    </a>
                                </div>
                                <div>
                                    <a href="/">
                                        <img width="40" src="<?php echo esc_url( $template_uri ); ?>/assets/img/vectors/instagram.svg" alt="<?php echo esc_attr( $footer_social_instagram_alt ?: 'social instagram' ); ?>">
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    <?php
                    endif;
                    $footer_right_first = false;
                endforeach;
                ?>
            </div>

        </div>
        <div class="footer-bottom py-4">
            <div class="container d-flex justify-content-center align-items-center px-3 text-center">
                <p class="first-copyright mb-0">© <?php echo esc_html( date( 'Y' ) ); ?> <?php echo wp_kses_post( $copyright ); ?></p>
            </div>
        </div>
    </div>



</footer>
<?php
//	add_action('wp_footer', function() {
//		if (is_user_logged_in()) {
//			$user_id = get_current_user_id();
//			echo "<div style='background:#ff0; padding:8px; position:fixed; bottom:0; right:0; z-index:9999;'>User ID: $user_id</div>";
//		}
//	});
//
//?>
<?php wp_footer(); ?>

</body>
</html>
