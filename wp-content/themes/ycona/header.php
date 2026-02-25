<?php
	/**
	 * Copyright (c) 2025 by Granit Nebiu
	 * @package WordPress
	 * @subpackage ycona
	 * @since 1.0
	 */
	
	$current_lang_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';
	
	$theme_options     = get_option( 'wt_shop_theme_options_' . $current_lang_code, array() );
	$theme_options_all = get_option( 'wt_shop_theme_options_all', array() );
	if ( ! is_array( $theme_options ) ) {
		$theme_options = array();
	}
	if ( ! is_array( $theme_options_all ) ) {
		$theme_options_all = array();
	}
	$wt_shop_template_logo        = $theme_options_all['wt_shop_logo'] ?? "";
	$wt_shop_template_logo_active = $theme_options_all['wt_shop_logo_active'] ?? "";
	$wt_shop_template_logo_mobile = $theme_options_all['wt_shop_logo_mobile'] ?? "";
	
	$top_header_text          = $theme_options['top_header_text'] ?? "";
	$button_login_in          = $theme_options['button_login_in'] ?? "";
	$button_registration      = $theme_options['button_registration'] ?? "";
	$button_login_in_link     = $theme_options['button_login_in_link'] ?? "";
	$button_registration_link = $theme_options['button_registration_link'] ?? "";
    $search_link              = $theme_options['search_icon'] ?? "";
	
	/** ===== WPML languages ===== */
	$languages    = function_exists('icl_get_languages') ? icl_get_languages('skip_missing=0') : [];
	$current_lang = $languages ? (array_values(array_filter($languages, fn($l)=>!empty($l['active'])))[0] ?? reset($languages)) : null;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <link rel="apple-touch-icon" sizes="57x57" href="/wp-content/themes/webthinkershop/assets/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/wp-content/themes/webthinkershop/assets/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/wp-content/themes/webthinkershop/assets/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/wp-content/themes/webthinkershop/assets/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/wp-content/themes/webthinkershop/assets/img/favicon//apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/wp-content/themes/webthinkershop/assets/img/favicon//apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/wp-content/themes/webthinkershop/assets/img/favicon//apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/wp-content/themes/webthinkershop/assets/img/favicon//apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/wp-content/themes/webthinkershop/assets/img/favicon//apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/wp-content/themes/webthinkershop/assets/img/favicon//android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/wp-content/themes/webthinkershop/assets/img/favicon//favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/wp-content/themes/webthinkershop/assets/img/favicon//favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/wp-content/themes/webthinkershop/assets/img/favicon//favicon-16x16.png">
    <link rel="manifest" href="/wp-content/themes/webthinkershop/assets/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/wp-content/themes/webthinkershop/assets/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="header-wt-shop">
    <div class="top-header">
        <p><?php echo $top_header_text ?></p>
    </div>
    <nav class="desktop-nav<?php echo is_front_page() ? '' : ' nav-other'; ?>">
        <div class="container">
            <div class="header-wrapper">
                <div class="logo-wrapper">
                    <a class="logo-desktop" href="<?php echo esc_url(home_url('/')); ?>">
                        <img width="120" height="auto" alt="logo webthinkershop" src="<?php echo $wt_shop_template_logo ?>"/>
                    </a>
                    <a class="logo-desktop-active d-none" href="<?php echo esc_url(home_url('/')); ?>">
                        <img width="120" height="auto" alt="logo webthinkershop" src="<?php echo $wt_shop_template_logo_active ?>"/>
                    </a>
                </div>

                <div class="d-flex align-items-center">
					<?php
						wp_nav_menu([
							'theme_location'  => 'primary-menu',
							'container'       => 'nav',
							'container_class' => 'main-menu',
							'menu_class'      => 'nav-menu',
							'walker'          => new Desktop_Walker_Nav_Menu(),
							'fallback_cb'     => false
						]);
					?>

					<div class="header-account d-flex align-items-center gap-2">
						<?php
						if ( function_exists( 'WC' ) ) :
							$mini_cart_model = function_exists( 'wt_shop_mini_cart_model' ) ? wt_shop_mini_cart_model() : 'panel';
							?>
                            <!-- Search trigger -->
                            <button type="button" id="wt-shop-search-trigger" class="d-none d-md-block wt-shop-search-trigger" aria-label="<?php esc_attr_e( 'Search by Product Name or SKU', 'webthinkershop' ); ?>">
                                <i class="bi bi-search" aria-hidden="true"></i>
                            </button>

                            <?php if ( $mini_cart_model === 'dropdown' ) : ?>
                            <div class="wt-mini-cart-wrap">
                                <button type="button" id="wt-mini-cart-trigger" class="wt-mini-cart-trigger d-flex align-items-center gap-1" aria-label="<?php esc_attr_e( 'Open cart', 'webthinkershop' ); ?>" aria-expanded="false" aria-controls="wt-mini-cart-panel">
                                    <i class="bi bi-cart3" aria-hidden="true"></i>
                                    <span class="wt-mini-cart-count-wrap"><?php echo function_exists( 'wt_shop_mini_cart_count_html' ) ? wt_shop_mini_cart_count_html() : '<span class="wt-mini-cart-count" data-count="0">0</span>'; ?></span>
                                </button>
                                <?php get_template_part( 'template-parts/mini-cart-panel' ); ?>
                            </div>
                            <?php else : ?>
                            <button type="button" id="wt-mini-cart-trigger" class="wt-mini-cart-trigger d-flex align-items-center gap-1" aria-label="<?php esc_attr_e( 'Open cart', 'webthinkershop' ); ?>" aria-expanded="false" aria-controls="wt-mini-cart-panel">
                                <i class="bi bi-cart3" aria-hidden="true"></i>
                                <span class="wt-mini-cart-count-wrap"><?php echo function_exists( 'wt_shop_mini_cart_count_html' ) ? wt_shop_mini_cart_count_html() : '<span class="wt-mini-cart-count" data-count="0">0</span>'; ?></span>
                            </button>
                            <?php endif; ?>
						<?php else : ?>
                            <a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" class="wt-mini-cart-trigger d-flex align-items-center gap-1" aria-label="<?php esc_attr_e( 'View cart', 'webthinkershop' ); ?>">
                                <i class="bi bi-cart3" aria-hidden="true"></i>
                                <span class="wt-mini-cart-count-wrap"><span class="wt-mini-cart-count" data-count="0">0</span></span>
                            </a>
						<?php endif; ?>

                        <?php get_template_part( 'template-parts/language-switch' ); ?>
      
						<?php if ( is_user_logged_in() ) : ?>
							<?php
							$myaccount_url = home_url( '/my-account/' );
							if ( function_exists( 'wc_get_page_permalink' ) ) {
								$myaccount_url = wc_get_page_permalink( 'myaccount' );
							} elseif ( get_option( 'woocommerce_myaccount_page_id' ) ) {
								$myaccount_url = get_permalink( (int) get_option( 'woocommerce_myaccount_page_id' ) );
							}
							if ( ! $myaccount_url ) {
								$myaccount_url = admin_url();
							}
							?>
							<a href="<?php echo esc_url( $myaccount_url ); ?>" class="d-none d-lg-block ms-0 ms-lg-2 header-account-btn btn-full btn-full-primary"><?php esc_html_e( 'My Account', 'webthinkershop' ); ?></a>
						<?php else : ?>
							<?php
							$login_link    = ! empty( $button_login_in_link ) ? $button_login_in_link : '#';
							$register_link = ! empty( $button_registration_link ) ? $button_registration_link : '#';
							$login_text    = ! empty( $button_login_in ) ? $button_login_in : __( 'Login', 'webthinkershop' );
							$register_text = ! empty( $button_registration ) ? $button_registration : __( 'Register', 'webthinkershop' );
							$login_attr    = ( $login_link === '#' || strpos( $login_link, '#' ) === 0 ) ? ' data-bs-toggle="modal" data-bs-target="#wt-shop-login"' : '';
							$register_attr = ( $register_link === '#' || strpos( $register_link, '#' ) === 0 ) ? ' data-bs-toggle="modal" data-bs-target="#wt-shop-register"' : '';
							?>
							<a href="<?php echo esc_url( $login_link ); ?>" class="header-account-btn btn-full btn-full-primary"<?php echo $login_attr; ?>><?php echo esc_html( $login_text ); ?></a>
							<a href="<?php echo esc_url( $register_link ); ?>" class="header-account-btn btn-full btn-full-secondary"<?php echo $register_attr; ?>><?php echo esc_html( $register_text ); ?></a>
						<?php endif; ?>
					</div>
					<?php get_template_part('template-parts/mega-menu'); ?>

                </div>

            </div>
        </div>
    </nav>
</header>
<?php
if ( function_exists( 'WC' ) && function_exists( 'wt_shop_mini_cart_model' ) && wt_shop_mini_cart_model() === 'panel' ) {
	get_template_part( 'template-parts/mini-cart-panel' );
}
?>
<?php if ( function_exists( 'WC' ) ) : ?>
    <!-- Product search overlay -->
    <div id="wt-shop-search-overlay" class="wt-shop-search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Product search', 'webthinkershop' ); ?>">
        <div class="wt-shop-search-box">
            <div class="wt-shop-search-header">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input id="wt-shop-search-input"
                       class="wt-shop-search-input"
                       type="search"
                       autocomplete="off"
                       placeholder="<?php esc_attr_e( 'Search by Product Name or SKU...', 'webthinkershop' ); ?>"
                       aria-label="<?php esc_attr_e( 'Search by Product Name or SKU', 'webthinkershop' ); ?>" />
                <button type="button" id="wt-shop-search-close" class="wt-shop-search-close" aria-label="<?php esc_attr_e( 'Close search', 'webthinkershop' ); ?>">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div class="wt-shop-search-hint">
                <kbd>&uarr;</kbd><kbd>&darr;</kbd> <?php esc_html_e( 'to navigate', 'webthinkershop' ); ?>
                <kbd>Enter</kbd> <?php esc_html_e( 'to open', 'webthinkershop' ); ?>
                <kbd>Esc</kbd> <?php esc_html_e( 'to close', 'webthinkershop' ); ?>
            </div>
            <div id="wt-shop-search-results" class="wt-shop-search-results" role="listbox" aria-label="<?php esc_attr_e( 'Search results', 'webthinkershop' ); ?>"></div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Login -->
<div class="modal fade" id="wt-shop-login" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content d-flex flex-lg-row flex-wrap">
            <div class="col-12 col-lg-6 modal-left d-flex justify-content-center align-items-start">
                <img src="<?php echo $wt_shop_template_logo ?>" alt="">
            </div>
            <div class="col-12 col-lg-6 modal-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <p class="modal-wt-shop-title"><?php echo __('Don’t have an account yet?', 'webthinkershop'); ?></p>
                <div class="modal-body">
					<?php echo do_shortcode('[wc_login_form_wt_shop]'); ?>
					<div class="wt-shop-modal-errors mt-2" id="wt-shop-login-errors" role="alert" aria-live="polite"></div>
                </div>
                <div class="text-lined d-flex align-items-center">
                    <div class="line-middle"></div>
                    <div class="line-text"><?php echo esc_html__('or', 'webthinkershop'); ?></div>
                    <div class="line-middle"></div>
                </div>
                <p class="text-center modal-text-style"><?php echo __('Don’t have an account yet?', 'webthinkershop'); ?></p>
                <div class="modal-button-footer">
                    <a class="btn-outline btn-outline-primary text-uppercase" data-bs-toggle="modal" data-bs-target="#wt-shop-register">
						<?php echo __('Create your account', 'webthinkershop'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Register -->
<div class="modal fade" id="wt-shop-register" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content d-flex flex-lg-row flex-wrap">
            <div class="col-12 col-lg-6 modal-left d-flex justify-content-center align-items-start">
                <img src="<?php echo $wt_shop_template_logo ?>" alt="">
            </div>
            <div class="col-12 col-lg-6 modal-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <p class="modal-wt-shop-title"><?php echo __('Create account', 'webthinkershop'); ?></p>
                <div class="modal-body">
					<?php echo do_shortcode('[wt_shop_template_wc_registration]'); ?>
					<div class="wt-shop-modal-errors mt-2" id="wt-shop-register-errors" role="alert" aria-live="polite"></div>
                </div>
                <div class="text-lined d-flex align-items-center">
                    <div class="line-middle"></div>
                    <div class="line-text"><?php echo esc_html__('or', 'webthinkershop'); ?></div>
                    <div class="line-middle"></div>
                </div>
                <p class="text-center modal-text-style">
					<?php echo __('Already have an account?', 'webthinkershop'); ?>
                    <a data-bs-toggle="modal" data-bs-target="#wt-shop-login"><?php echo __('Login', 'webthinkershop'); ?></a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lang/Currency -->
<div class="modal fade" id="lang-currency-modal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 position-relative">
                <button type="button"
                        class="btn-close position-absolute end-0 top-0 mt-2 me-2 z-index-2"
                        data-bs-dismiss="modal"
                        aria-label="<?php esc_attr_e('Close', 'webthinkershop'); ?>">
                </button>
            </div>


            <div class="modal-body pt-0">
                <div class="tab-content">
                    <!-- Language -->
                    <div class="tab-pane fade show active" id="tab-lang">
						<?php if (!empty($languages)): ?>
                            <div class="row gap-x-3 ">
								<?php foreach ($languages as $lang):
									$is_active = !empty($lang['active']);
									$href = $is_active ? '#' : ($lang['url'] ?? '#'); ?>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <a href="<?php echo esc_url($href); ?>"
                                           class="tile d-flex align-items-center justify-content-between w-100 p-3 rounded-3 text-decoration-none <?php echo $is_active ? 'active' : ''; ?>"
											<?php echo $is_active ? 'aria-current="true" aria-disabled="true"' : ''; ?>>
                                            <span class="d-flex align-items-center gap-3">
                                                <?php if (!empty($lang['country_flag_url'])): ?>
                                                    <img src="<?php echo esc_url($lang['country_flag_url']); ?>" width="28" height="20" class="rounded" alt="">
                                                <?php endif; ?>
                                                <span class="d-flex flex-column">
                                                    <strong class="text-body"><?php
		                                                    $custom_lang_names = [
			                                                    'sq' => __('Albanian', 'webthinkershop'),
			                                                    'de' => __('German', 'webthinkershop'),
			                                                    'fr' => __('French', 'webthinkershop'),
			                                                    'en' => __('English', 'webthinkershop'),
		                                                    ];
		                                                    echo esc_html($custom_lang_names[$lang['code']] ?? $lang['native_name']); ?></strong>
                                                    <small class="text-muted"><?php echo esc_html($lang['translated_name'] ?? $lang['english_name'] ?? strtoupper($lang['code'] ?? '')); ?></small>
                                                </span>
                                            </span>
                                            <span class="select-dot"></span>
                                        </a>
                                    </div>
								<?php endforeach; ?>
                            </div>
						<?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


