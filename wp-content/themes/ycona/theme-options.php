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

/* Theme options page */
add_action( "admin_init", "theme_options_init" );
add_action( "admin_menu", "add_theme_menu_item" );

function add_theme_menu_item() {
    add_menu_page( "Theme-Optionen", "Theme-Optionen", "manage_options", "theme-panel", "add_theme_options", null, 99 );
}

/**
 * Sanitize and save footer right column order (dedicated option so it is never stripped by other validators).
 */
function wt_shop_sanitize_footer_right_column_order( $input ) {
    if ( ! is_array( $input ) ) {
        return array( 'payments', 'social' );
    }
    $allowed = array( 'payments', 'social' );
    $order   = array_values(
        array_filter(
            array_map( 'sanitize_text_field', wp_unslash( $input ) ),
            static function( $value ) use ( $allowed ) {
                return in_array( $value, $allowed, true );
            }
        )
    );
    if ( count( $order ) >= 2 ) {
        return $order;
    }
    if ( count( $order ) === 1 ) {
        $other = ( $order[0] === 'payments' ) ? 'social' : 'payments';
        return array( $order[0], $other );
    }
    return array( 'payments', 'social' );
}

// register settings
function theme_options_init() {

    $current_lang_code = "";
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
        $current_lang_code = ICL_LANGUAGE_CODE;
    }

    register_setting( 'theme_options', 'wt_shop_theme_options_' . $current_lang_code, 'itg_validate_options' );
    register_setting( 'theme_options', 'wt_shop_theme_options_all', 'itg_validate_options' );
    register_setting( 'theme_options', 'wt_shop_footer_right_column_order', array(
        'type'              => 'array',
        'sanitize_callback' => 'wt_shop_sanitize_footer_right_column_order',
    ) );

    $js_src = includes_url('js/tinymce/') . 'tinymce.min.js';
    $css_src = includes_url('css/') . 'editor.css';

    wp_register_style('tinymce_css', $css_src);
    wp_enqueue_style('tinymce_css');
	

    
}



// create option site
function add_theme_options() {



    if ( ! isset( $_REQUEST['settings-updated'] ) ) {
        $_REQUEST['settings-updated'] = false;
    } ?>

    <div class="wrap wt-theme-options-wrapper">
        <header class="wt-options-header">
            <div class="wt-header-content">
                <div class="wt-header-icon">
                    <i class="ph ph-gear-six"></i>
            </div>
                <div class="wt-header-text">
                    <h1 class="wt-header-title"><?php _e( 'Theme Options', "webthinkershop" ); ?></h1>
                    <p class="wt-header-subtitle"><?php _e( 'Customize your website appearance and functionality', "webthinkershop" ); ?></p>
                </div>
            </div>
        </header>

        <!-- Modern Notification Container -->
        <div class="wt-notification-container" id="wt-notification-container" role="region" aria-live="polite" aria-label="Notifications">
            <?php 
            // Handle different notification types based on URL parameters
            $notification_type = $_GET['notification'] ?? '';
            $notification_message = $_GET['message'] ?? '';
            
            if ( false !== $_REQUEST['settings-updated'] ) : ?>
                <div class="wt-notification success show" role="alert">
                    <div class="wt-notification-icon" aria-hidden="true">✓</div>
                    <div class="wt-notification-content">
                        <div class="wt-notification-title">Settings Saved</div>
                        <div class="wt-notification-message">All theme options have been saved successfully!</div>
                    </div>
                    <button class="wt-notification-close" onclick="this.parentElement.remove()" aria-label="Close notification">×</button>
                    <div class="wt-notification-progress"></div>
                </div>
            <?php elseif ( $notification_type && $notification_message ) : 
                $icons = [
                    'success' => '✓',
                    'error' => '✕',
                    'warning' => '⚠',
                    'info' => 'ℹ'
                ];
                $icon = $icons[$notification_type] ?? $icons['info'];
                $title = ucfirst($notification_type);
            ?>
                <div class="wt-notification <?php echo esc_attr($notification_type); ?> show" role="alert">
                    <div class="wt-notification-icon" aria-hidden="true"><?php echo $icon; ?></div>
                    <div class="wt-notification-content">
                        <div class="wt-notification-title"><?php echo esc_html($title); ?></div>
                        <div class="wt-notification-message"><?php echo esc_html(urldecode($notification_message)); ?></div>
                    </div>
                    <button class="wt-notification-close" onclick="this.parentElement.remove()" aria-label="Close notification">×</button>
                    <div class="wt-notification-progress"></div>
                </div>
            <?php endif; ?>
        </div>

        <form method="post" action="options.php">

            <?php

            $current_lang_code = "";
            if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                $current_lang_code = ICL_LANGUAGE_CODE;
            }


            settings_fields( 'theme_options' );
            $options     = get_option( 'wt_shop_theme_options_' . $current_lang_code );
            $options_all = get_option( 'wt_shop_theme_options_all' );

            $wt_shop_logo           = $options_all['wt_shop_logo'] ?? "";
            $wt_shop_logo_active    = $options_all['wt_shop_logo_active'] ?? "";

            $wt_shop_logo_mobile    = $options_all['wt_shop_logo_mobile'] ?? "";

            // header slider
            $wt_shop_slider_background   = $options_all['wt_shop_slider_background'] ?? "";

            // Top Head
            $mega_menu_title            = $options['mega_menu_title'] ?? "";
            $top_header_text            = $options['top_header_text'] ?? "";
            
            // footer

            $footer_title_1 = $options['footer_title_1'] ?? "";
            $footer_title_2 = $options['footer_title_2'] ?? "";
            $footer_title_3 = $options['footer_title_3'] ?? "";
            $footer_title_4 = $options['footer_title_4'] ?? "";
            $footer_title_5 = $options['footer_title_5'] ?? "";
            $footer_title_6 = $options['footer_title_6'] ?? "";
            $footer_support_text = $options['footer_support_text'] ?? "";

            $footer_address         = $options['footer_address'] ?? "";
            $footer_address_2       = $options['footer_address_2'] ?? "";
            $footer_address_2_link  = $options['footer_address_2_link'] ?? "";

            $copyright                      = $options['copyright'] ?? "";

            $footer_phone_number_title      = $options['footer_phone_number_title'] ?? "";
            $footer_phone_number            = $options['footer_phone_number'] ?? "";
            $footer_phone_number_link       = $options['footer_phone_number_link'] ?? "";

            $footer_description            = $options['footer_description'] ?? "";

            $wt_shop_footer_logo      =  $options_all['wt_shop_footer_logo'] ?? "";
            $wt_shop_footer_logo_2    =  $options_all['wt_shop_footer_logo_2'] ?? "";
            $wt_shop_footer_logo_3    =  $options_all['wt_shop_footer_logo_3'] ?? "";
            
            $footer_apple_link         = $options_all['footer_apple_link'] ?? "";
            $footer_android_link       = $options_all['footer_android_link'] ?? "";

            $footer_support_theme_icons = $options_all['footer_support_theme_icons'] ?? array();
            $footer_support_payment_icons = $options_all['footer_support_payment_icons'] ?? array();
            if ( ! is_array( $footer_support_theme_icons ) ) {
                $footer_support_theme_icons = array();
            }
            if ( ! is_array( $footer_support_payment_icons ) ) {
                $footer_support_payment_icons = array();
            }


            $social_links = $options_all['social_links'] ?? array();
            if ( ! is_array( $social_links ) ) {
                $social_links = array();
            }
            $footer_right_column_order = get_option( 'wt_shop_footer_right_column_order', array( 'payments', 'social' ) );
            if ( ! is_array( $footer_right_column_order ) ) {
                $footer_right_column_order = array( 'payments', 'social' );
            }
            $footer_right_column_order = array_values(
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
            $icon_preview_base  = ( parse_url( home_url(), PHP_URL_SCHEME ) ?: 'https' ) . '://' . ( parse_url( home_url(), PHP_URL_HOST ) ?: '' );
            $icon_preview_theme = get_template_directory_uri();

            // social link
            $social_title       = $options['social_title'] ?? "";
	           
            //Other Settungs
            $other_title               = $options['other_title'] ?? "";
            $recaptcha_site_key        = $options_all['recaptcha_site_key'] ?? "";
            $recaptcha_secret_key      = $options_all['recaptcha_secret_key'] ?? "";
            $language_switch_model     = $options_all['language_switch_model'] ?? 'modal';
            $mini_cart_model           = $options_all['mini_cart_model'] ?? 'panel';

	        $button_login_in            = $options['button_login_in'] ?? "";
	        $button_registration        = $options['button_registration'] ?? "";
	        $button_login_in_link       = $options['button_login_in_link'] ?? "";
            $button_registration_link   = $options['button_registration_link'] ?? "";
            $search_link                = $options['search_link'] ?? "";

            /* Thank you page (order received) – translatable per language */
            $thank_you_heading             = $options['thank_you_heading'] ?? "";
            $thank_you_subheading          = $options['thank_you_subheading'] ?? "";
            $thank_you_order_message       = $options['thank_you_order_message'] ?? "";
            $thank_you_confirmation_label   = $options['thank_you_confirmation_label'] ?? "";
            $thank_you_delivery_label      = $options['thank_you_delivery_label'] ?? "";
            $thank_you_button_text         = $options['thank_you_button_text'] ?? "";
            $thank_you_contact_intro       = $options['thank_you_contact_intro'] ?? "";
            $thank_you_estimated_delivery  = $options['thank_you_estimated_delivery'] ?? "";
            $thank_you_contact_page_id     = isset( $options_all['thank_you_contact_page_id'] ) ? absint( $options_all['thank_you_contact_page_id'] ) : 0;

            ?>
            <br>
            <script src="https://unpkg.com/phosphor-icons"></script>

        <div class="wt-options-body">
            <div class="wt-options-container">
                <div class="wt-options-layout">
                    <aside class="wt-options-sidebar" role="complementary" aria-label="Theme Options Navigation">
                        <div class="wt-sidebar-header">
                            <div class="wt-sidebar-logo">
                                <i class="ph ph-gear-six"></i>
                        </div>
                            <h2 class="wt-sidebar-title"><?php _e( 'Theme Options', 'webthinkershop' ); ?></h2>
                        </div>
                        <nav class="wt-sidebar-nav" role="navigation" aria-label="Options Navigation">
                            <ul class="wt-nav-list" role="tablist">
                                <li class="wt-nav-item active" data-target="#general" role="tab" aria-selected="true" tabindex="0" aria-controls="general-panel">
                                    <i class="ph ph-house" aria-hidden="true"></i>
                                <span><?php _e( 'General', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#design-settings" role="tab" aria-selected="false" tabindex="0" aria-controls="design-settings-panel">
                                    <i class="ph ph-palette" aria-hidden="true"></i>
                                <span><?php _e( 'Design Settings', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#social-media" role="tab" aria-selected="false" tabindex="0" aria-controls="social-media-panel">
                                    <i class="ph ph-share-network" aria-hidden="true"></i>
                                <span><?php _e( 'Social Media', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#payments" role="tab" aria-selected="false" tabindex="0" aria-controls="payments-panel">
                                    <i class="ph ph-credit-card" aria-hidden="true"></i>
                                <span><?php _e( 'Payments', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#other-setting" role="tab" aria-selected="false" tabindex="0" aria-controls="other-setting-panel">
                                <i class="ph ph-star" aria-hidden="true"></i>
                                <span><?php esc_html_e( 'Other Settings', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#thank-you-page" role="tab" aria-selected="false" tabindex="0" aria-controls="thank-you-page-panel">
                                    <i class="ph ph-check-circle" aria-hidden="true"></i>
                                    <span><?php esc_html_e( 'Thank you page', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#footer" role="tab" aria-selected="false" tabindex="0" aria-controls="footer-panel">
                                    <i class="ph ph-layout" aria-hidden="true"></i>
                                <span><?php _e( 'Footer', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                                <li class="wt-nav-item" data-target="#settings" role="tab" aria-selected="false" tabindex="0" aria-controls="settings-panel">
                                    <i class="ph ph-gear" aria-hidden="true"></i>
                                <span><?php _e( 'Settings', 'webthinkershop' ); ?></span>
                                    <div class="wt-nav-indicator"></div>
                            </li>
                        </ul>
                    </nav>
                    </aside>
                    <main class="wt-options-main" role="main">
                        <div class="wt-tab-panels">
                            <section class="wt-tab-panel active" id="general" role="tabpanel" aria-labelledby="general-tab" aria-hidden="false">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-house"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'General Settings', "webthinkershop" ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Configure basic theme settings and branding', "webthinkershop" ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">

                                    <!-- Logo Upload Section -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="image_url_wt_shop_logo">
                                            <i class="ph ph-image" aria-hidden="true"></i>
                                            <?php _e( 'Main Logo', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Upload your main website logo (recommended: 200x60px)', "webthinkershop" ); ?></div>
                                        <div class="wt-upload-container">
                                            <input id="image_url_wt_shop_logo" type="text" name="wt_shop_theme_options_all[wt_shop_logo]" value="<?php esc_attr_e( $wt_shop_logo ); ?>" class="wt-hidden-input" />
                                            <div class="wt-upload-actions">
                                                <button id="upload_button_wt_shop_logo" type="button" class="wt-btn wt-btn-primary">
                                                    <i class="ph ph-upload" aria-hidden="true"></i>
                                                    <?php _e( 'Upload Logo', "webthinkershop" ); ?>
                                                </button>
                                                <button id="remove_button_wt_shop_logo" type="button" class="wt-btn wt-btn-secondary">
                                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                                    <?php _e( 'Remove', "webthinkershop" ); ?>
                                                </button>
                                            </div>
                                            <div class="wt-image-preview">
                                                <img id="preview_image_wt_shop_logo" src="<?php echo esc_url($wt_shop_logo); ?>" alt="Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_logo === null || $wt_shop_logo == '') ? 'style="display: none;"' : ''; ?>>
                                                <div class="wt-preview-placeholder" <?php echo ($wt_shop_logo !== null && $wt_shop_logo != '') ? 'style="display: none;"' : ''; ?>>
                                                    <i class="ph ph-image" aria-hidden="true"></i>
                                                    <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Active Logo Upload Section -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="image_url_wt_shop_logo_active">
                                            <i class="ph ph-cursor-click" aria-hidden="true"></i>
                                            <?php _e( 'Active Logo', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Logo shown when menu is active or hovered (optional)', "webthinkershop" ); ?></div>
                                        <div class="wt-upload-container">
                                            <input id="image_url_wt_shop_logo_active" type="text" name="wt_shop_theme_options_all[wt_shop_logo_active]" value="<?php esc_attr_e( $wt_shop_logo_active ); ?>" class="wt-hidden-input" />
                                            <div class="wt-upload-actions">
                                                <button id="upload_button_wt_shop_logo_active" type="button" class="wt-btn wt-btn-primary">
                                                    <i class="ph ph-upload" aria-hidden="true"></i>
                                                    <?php _e( 'Upload Active Logo', "webthinkershop" ); ?>
                                                </button>
                                                <button id="remove_button_wt_shop_logo_active" type="button" class="wt-btn wt-btn-secondary">
                                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                                    <?php _e( 'Remove', "webthinkershop" ); ?>
                                                </button>
                                            </div>
                                            <div class="wt-image-preview">
                                                <img id="preview_image_wt_shop_logo_active" src="<?php echo esc_url($wt_shop_logo_active); ?>" alt="Active Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_logo_active === null || $wt_shop_logo_active == '') ? 'style="display: none;"' : ''; ?>>
                                                <div class="wt-preview-placeholder" <?php echo ($wt_shop_logo_active !== null && $wt_shop_logo_active != '') ? 'style="display: none;"' : ''; ?>>
                                                    <i class="ph ph-image" aria-hidden="true"></i>
                                                    <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Logo Upload Section -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="image_url_wt_shop_logo_mobile">
                                            <i class="ph ph-device-mobile" aria-hidden="true"></i>
                                            <?php _e( 'Mobile Logo', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Logo optimized for mobile devices (recommended: 150x45px)', "webthinkershop" ); ?></div>
                                        <div class="wt-upload-container">
                                            <input id="image_url_wt_shop_logo_mobile" type="text" name="wt_shop_theme_options_all[wt_shop_logo_mobile]" value="<?php esc_attr_e( $wt_shop_logo_mobile ); ?>" class="wt-hidden-input" />
                                            <div class="wt-upload-actions">
                                                <button id="upload_button_wt_shop_logo_mobile" type="button" class="wt-btn wt-btn-primary">
                                                    <i class="ph ph-upload" aria-hidden="true"></i>
                                                    <?php _e( 'Upload Mobile Logo', "webthinkershop" ); ?>
                                                </button>
                                                <button id="remove_button_wt_shop_logo_mobile" type="button" class="wt-btn wt-btn-secondary">
                                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                                    <?php _e( 'Remove', "webthinkershop" ); ?>
                                                </button>
                                            </div>
                                            <div class="wt-image-preview">
                                                <img id="preview_image_wt_shop_logo_mobile" src="<?php echo esc_url($wt_shop_logo_mobile); ?>" alt="Mobile Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_logo_mobile === null || $wt_shop_logo_mobile == '') ? 'style="display: none;"' : ''; ?>>
                                                <div class="wt-preview-placeholder" <?php echo ($wt_shop_logo_mobile !== null && $wt_shop_logo_mobile != '') ? 'style="display: none;"' : ''; ?>>
                                                    <i class="ph ph-image" aria-hidden="true"></i>
                                                    <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Slider Background Upload Section -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="image_url_wt_shop_slider_background">
                                            <i class="ph ph-image-square" aria-hidden="true"></i>
                                            <?php _e( 'Header Slider Background', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Background image for the header slider (recommended: 1920x600px)', "webthinkershop" ); ?></div>
                                        <div class="wt-upload-container">
                                            <input id="image_url_wt_shop_slider_background" type="text" name="wt_shop_theme_options_all[wt_shop_slider_background]" value="<?php esc_attr_e( $wt_shop_slider_background ); ?>" class="wt-hidden-input" />
                                            <div class="wt-upload-actions">
                                                <button id="upload_button_wt_shop_slider_background" type="button" class="wt-btn wt-btn-primary">
                                                    <i class="ph ph-upload" aria-hidden="true"></i>
                                                    <?php _e( 'Upload Background', "webthinkershop" ); ?>
                                                </button>
                                                <button id="remove_button_wt_shop_slider_background" type="button" class="wt-btn wt-btn-secondary">
                                                    <i class="ph ph-trash" aria-hidden="true"></i>
                                                    <?php _e( 'Remove', "webthinkershop" ); ?>
                                                </button>
                                            </div>
                                            <div class="wt-image-preview">
                                                <img id="preview_image_wt_shop_slider_background" src="<?php echo esc_url($wt_shop_slider_background); ?>" alt="Slider Background Preview" class="wt-preview-image" <?php echo ($wt_shop_slider_background === null || $wt_shop_slider_background == '') ? 'style="display: none;"' : ''; ?>>
                                                <div class="wt-preview-placeholder" <?php echo ($wt_shop_slider_background !== null && $wt_shop_slider_background != '') ? 'style="display: none;"' : ''; ?>>
                                                    <i class="ph ph-image" aria-hidden="true"></i>
                                                    <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Text Input Fields -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="top_header_text">
                                            <i class="ph ph-text-aa" aria-hidden="true"></i>
                                            <?php _e( 'Top Header Text', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Text displayed in the top header area', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="text"
                                               id="top_header_text"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[top_header_text]"
                                               value="<?php esc_attr_e( $top_header_text ); ?>"
                                               placeholder="<?php _e( 'Enter top header text...', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="mega_menu_title">
                                            <i class="ph ph-list" aria-hidden="true"></i>
                                            <?php _e( 'Mega Menu Title', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Title for the mega menu navigation', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="text"
                                               id="mega_menu_title"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[mega_menu_title]"
                                               value="<?php esc_attr_e( $mega_menu_title ); ?>"
                                               placeholder="<?php _e( 'Enter mega menu title...', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="button_login_in">
                                            <i class="ph ph-sign-in" aria-hidden="true"></i>
                                            <?php _e( 'Login Button Text', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Text displayed on the login button', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="text"
                                               id="button_login_in"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[button_login_in]"
                                               value="<?php esc_attr_e( $button_login_in ); ?>"
                                               placeholder="<?php _e( 'Enter login button text...', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="button_login_in_link">
                                            <i class="ph ph-link" aria-hidden="true"></i>
                                            <?php _e( 'Login Button Link', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'URL for the login button', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="url"
                                               id="button_login_in_link"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[button_login_in_link]"
                                               value="<?php esc_attr_e( $button_login_in_link ); ?>"
                                               placeholder="<?php _e( 'https://example.com/login', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="button_registration">
                                            <i class="ph ph-user-plus" aria-hidden="true"></i>
                                            <?php _e( 'Registration Button Text', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'Text displayed on the registration button', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="text"
                                               id="button_registration"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[button_registration]"
                                               value="<?php esc_attr_e( $button_registration ); ?>"
                                               placeholder="<?php _e( 'Enter registration button text...', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="button_registration_link">
                                            <i class="ph ph-link" aria-hidden="true"></i>
                                            <?php _e( 'Registration Button Link', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'URL for the registration button', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="url"
                                               id="button_registration_link"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[button_registration_link]"
                                               value="<?php esc_attr_e( $button_registration_link ); ?>"
                                               placeholder="<?php _e( 'https://example.com/register', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="search_link">
                                            <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
                                            <?php _e( 'Search Link', "webthinkershop" ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php _e( 'URL for the search functionality', "webthinkershop" ); ?></div>
                                        <input class="wt-input-field"
                                               type="url"
                                               id="search_link"
                                               name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[search_link]"
                                               value="<?php esc_attr_e( $search_link ); ?>"
                                               placeholder="<?php _e( 'https://example.com/search', 'webthinkershop' ); ?>"
                                        />
                                    </div>

                                </div>
                            </section>

                            <section class="wt-tab-panel" id="design-settings" role="tabpanel" aria-labelledby="design-settings-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-palette"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Design Settings', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Customize colors, typography, and visual appearance', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <!-- Colors Section -->
                                    <div class="wt-design-colors-section">
                                    <h2 class="wt-section-title">
                                        <i class="ph ph-palette" aria-hidden="true"></i>
                                        <?php _e( 'Theme Colors (Live Preview)', 'webthinkershop' ); ?>
                                    </h2>
                                    <p class="wt-section-description"><?php _e( 'Customize your color palette. Changes are applied instantly for preview.', 'webthinkershop' ); ?></p>
                                    
                                    <div class="wt-color-grid">
                                        <!-- Primary Colors -->
                                        <div class="wt-color-category">
                                            <div class="wt-category-header">
                                                <h3 class="wt-category-title">
                                                    <i class="ph ph-circle" aria-hidden="true"></i>
                                                    <?php _e( 'Primary Colors', 'webthinkershop' ); ?>
                                                </h3>
                                                <p class="wt-category-description"><?php _e( 'Main brand colors used throughout the theme', 'webthinkershop' ); ?></p>
                                            </div>
                                            <div class="wt-color-items">
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="primary_color">
                                                        <span class="wt-color-name"><?php _e( 'Primary', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="primary_color_value"><?php echo get_option('primary_color', '#091057'); ?></span>
                                                    </label>
                                                    <input type="color" id="primary_color" value="<?php echo get_option('primary_color', '#091057'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="primary_dark">
                                                        <span class="wt-color-name"><?php _e( 'Primary Dark', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="primary_dark_value"><?php echo get_option('wt_shop_primary_dark', '#05063F'); ?></span>
                                                    </label>
                                                    <input type="color" id="primary_dark" value="<?php echo get_option('wt_shop_primary_dark', '#05063F'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="primary_hover">
                                                        <span class="wt-color-name"><?php _e( 'Primary Hover', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="primary_hover_value"><?php echo get_option('wt_shop_primary_hover', '#1326A1'); ?></span>
                                                    </label>
                                                    <input type="color" id="primary_hover" value="<?php echo get_option('wt_shop_primary_hover', '#1326A1'); ?>" class="wt-color-picker">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Secondary Colors -->
                                        <div class="wt-color-category">
                                            <div class="wt-category-header">
                                                <h3 class="wt-category-title">
                                                    <i class="ph ph-circle" aria-hidden="true"></i>
                                                    <?php _e( 'Secondary Colors', 'webthinkershop' ); ?>
                                                </h3>
                                                <p class="wt-category-description"><?php _e( 'Accent colors for highlights and call-to-actions', 'webthinkershop' ); ?></p>
                                            </div>
                                            <div class="wt-color-items">
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="secondary_color">
                                                        <span class="wt-color-name"><?php _e( 'Secondary', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="secondary_color_value"><?php echo get_option('secondary_color', '#FF6900'); ?></span>
                                                    </label>
                                                    <input type="color" id="secondary_color" value="<?php echo get_option('secondary_color', '#FF6900'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="secondary_darker">
                                                        <span class="wt-color-name"><?php _e( 'Secondary Darker', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="secondary_darker_value"><?php echo get_option('wt_shop_secondary_darker', '#CC4B02'); ?></span>
                                                    </label>
                                                    <input type="color" id="secondary_darker" value="<?php echo get_option('wt_shop_secondary_darker', '#CC4B02'); ?>" class="wt-color-picker">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Neutral Colors -->
                                        <div class="wt-color-category">
                                            <div class="wt-category-header">
                                                <h3 class="wt-category-title">
                                                    <i class="ph ph-circle" aria-hidden="true"></i>
                                                    <?php _e( 'Neutral Colors', 'webthinkershop' ); ?>
                                                </h3>
                                                <p class="wt-category-description"><?php _e( 'Text and background colors for content', 'webthinkershop' ); ?></p>
                                            </div>
                                            <div class="wt-color-items">
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="black">
                                                        <span class="wt-color-name"><?php _e( 'Black', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="black_value"><?php echo get_option('wt_shop_black', '#111111'); ?></span>
                                                    </label>
                                                    <input type="color" id="black" value="<?php echo get_option('wt_shop_black', '#111111'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="white">
                                                        <span class="wt-color-name"><?php _e( 'White', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="white_value"><?php echo get_option('wt_shop_white', '#ffffff'); ?></span>
                                                    </label>
                                                    <input type="color" id="white" value="<?php echo get_option('wt_shop_white', '#ffffff'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="gray">
                                                        <span class="wt-color-name"><?php _e( 'Gray', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="gray_value"><?php echo get_option('wt_shop_gray', '#737373'); ?></span>
                                                    </label>
                                                    <input type="color" id="gray" value="<?php echo get_option('wt_shop_gray', '#737373'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="light_gray">
                                                        <span class="wt-color-name"><?php _e( 'Light Gray', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="light_gray_value"><?php echo get_option('wt_shop_light_gray', '#F7F7F8'); ?></span>
                                                    </label>
                                                    <input type="color" id="light_gray" value="<?php echo get_option('wt_shop_light_gray', '#F7F7F8'); ?>" class="wt-color-picker">
                                                </div>
                                            </div>
                                </div>

                                        <!-- Additional Colors -->
                                        <div class="wt-color-category">
                                            <div class="wt-category-header">
                                                <h3 class="wt-category-title">
                                                    <i class="ph ph-circle" aria-hidden="true"></i>
                                                    <?php _e( 'Additional Colors', 'webthinkershop' ); ?>
                                                </h3>
                                                <p class="wt-category-description"><?php _e( 'Special colors for specific elements', 'webthinkershop' ); ?></p>
                                            </div>
                                            <div class="wt-color-items">
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="tertiary">
                                                        <span class="wt-color-name"><?php _e( 'Tertiary', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="tertiary_value"><?php echo get_option('wt_shop_tertiary', '#0077FF'); ?></span>
                                                    </label>
                                                    <input type="color" id="tertiary" value="<?php echo get_option('wt_shop_tertiary', '#0077FF'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="accent_color">
                                                        <span class="wt-color-name"><?php _e( 'Accent', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="accent_color_value"><?php echo get_option('accent_color', '#0077FF'); ?></span>
                                                    </label>
                                                    <input type="color" id="accent_color" value="<?php echo get_option('accent_color', '#0077FF'); ?>" class="wt-color-picker">
                                                </div>
                                                <div class="wt-color-item">
                                                    <label class="wt-color-label" for="background_color">
                                                        <span class="wt-color-name"><?php _e( 'Background', 'webthinkershop' ); ?></span>
                                                        <span class="wt-color-value" id="background_color_value"><?php echo get_option('background_color', '#ffffff'); ?></span>
                                                    </label>
                                                    <input type="color" id="background_color" value="<?php echo get_option('background_color', '#ffffff'); ?>" class="wt-color-picker">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="wt-color-actions">
                                        <button class="wt-btn wt-btn-primary" id="save_colors_btn">
                                            <i class="ph ph-floppy-disk" aria-hidden="true"></i>
                                            <?php _e( 'Save Colors', 'webthinkershop' ); ?>
                                        </button>
                                        <button type="button" class="wt-btn wt-btn-secondary" id="reset_colors_btn">
                                            <i class="ph ph-arrow-clockwise" aria-hidden="true"></i>
                                            <?php _e( 'Reset to Defaults', 'webthinkershop' ); ?>
                                        </button>
                                    </div>
                                </div>

                                    <!-- Custom CSS Section -->
                                    <div class="wt-code-section">
                                        <div class="wt-section-header">
                                            <h2 class="wt-section-title">
                                                <i class="ph ph-code" aria-hidden="true"></i>
                                                <?php _e( 'Custom CSS', 'webthinkershop' ); ?>
                                            </h2>
                                            <p class="wt-section-description"><?php _e( 'Add your custom CSS code to override theme styles', 'webthinkershop' ); ?></p>
                                        </div>
                                        
                                        <div class="wt-code-editor">
                                            <div class="wt-code-header">
                                                <span class="wt-code-label">styles.css</span>
                                                <div class="wt-code-actions">
                                                    <button type="button" class="wt-btn wt-btn-secondary wt-btn-sm" id="format_css_btn">
                                                        <i class="ph ph-brackets-curly" aria-hidden="true"></i>
                                                        <?php _e( 'Format', 'webthinkershop' ); ?>
                                                    </button>
                                                    <button type="button" class="wt-btn wt-btn-secondary wt-btn-sm" id="clear_css_btn">
                                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                                        <?php _e( 'Clear', 'webthinkershop' ); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <textarea id="custom_css" class="wt-code-textarea" placeholder="/* Add your custom CSS here */"><?php echo esc_textarea(get_option('custom_css', '')); ?></textarea>
                                        </div>
                                        
                                        <div class="wt-code-actions">
                                            <button type="button" class="wt-btn wt-btn-primary" id="save_css_btn">
                                                <i class="ph ph-floppy-disk" aria-hidden="true"></i>
                                                <?php _e( 'Save CSS', 'webthinkershop' ); ?>
                                            </button>
                                            <button type="button" class="wt-btn wt-btn-secondary" id="download_css_btn">
                                                <i class="ph ph-download" aria-hidden="true"></i>
                                                <?php _e( 'Download CSS', 'webthinkershop' ); ?>
                                            </button>
                                            <button type="button" class="wt-btn wt-btn-secondary" id="reset_css_btn">
                                                <i class="ph ph-arrow-clockwise" aria-hidden="true"></i>
                                                <?php _e( 'Reset CSS', 'webthinkershop' ); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Custom JavaScript Section -->
                                    <div class="wt-code-section">
                                        <div class="wt-section-header">
                                            <h2 class="wt-section-title">
                                                <i class="ph ph-code" aria-hidden="true"></i>
                                                <?php _e( 'Custom JavaScript', 'webthinkershop' ); ?>
                                            </h2>
                                            <p class="wt-section-description"><?php _e( 'Add your custom JavaScript code for enhanced functionality', 'webthinkershop' ); ?></p>
                                        </div>
                                        
                                        <div class="wt-code-editor">
                                            <div class="wt-code-header">
                                                <span class="wt-code-label">scripts.js</span>
                                                <div class="wt-code-actions">
                                                    <button type="button" class="wt-btn wt-btn-secondary wt-btn-sm" id="format_js_btn">
                                                        <i class="ph ph-brackets-curly" aria-hidden="true"></i>
                                                        <?php _e( 'Format', 'webthinkershop' ); ?>
                                                    </button>
                                                    <button type="button" class="wt-btn wt-btn-secondary wt-btn-sm" id="clear_js_btn">
                                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                                        <?php _e( 'Clear', 'webthinkershop' ); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <textarea id="custom_js" class="wt-code-textarea" placeholder="// Add your custom JavaScript here"><?php echo esc_textarea(get_option('custom_js', '')); ?></textarea>
                                        </div>
                                        
                                        <div class="wt-code-actions">
                                            <button type="button" class="wt-btn wt-btn-primary" id="save_js_btn">
                                                <i class="ph ph-floppy-disk" aria-hidden="true"></i>
                                                <?php _e( 'Save JS', 'webthinkershop' ); ?>
                                            </button>
                                            <button type="button" class="wt-btn wt-btn-secondary" id="download_js_btn">
                                                <i class="ph ph-download" aria-hidden="true"></i>
                                                <?php _e( 'Download JS', 'webthinkershop' ); ?>
                                            </button>
                                            <button type="button" class="wt-btn wt-btn-secondary" id="reset_js_btn">
                                                <i class="ph ph-arrow-clockwise" aria-hidden="true"></i>
                                                <?php _e( 'Reset JS', 'webthinkershop' ); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="wt-tab-panel" id="social-media" role="tabpanel" aria-labelledby="social-media-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-share-network"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Social Media Settings', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Configure your social media links and profiles', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <!-- Social Media Title Section -->
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="social_title">
                                            <i class="ph ph-text-aa" aria-hidden="true"></i>
                                            <?php _e( 'Social Media Section Title', 'webthinkershop' ); ?>
                                        </label>
                                        <p class="wt-field-description"><?php _e( 'Enter the title for your social media section', 'webthinkershop' ); ?></p>
                                        <input id="social_title" class="wt-input-field" type="text" name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[social_title]" value="<?php esc_attr_e( $social_title ); ?>" placeholder="<?php esc_attr_e( 'Follow Us', 'webthinkershop' ); ?>">
                                    </div>

                                    <hr/>
                                    <h3 class="wt-section-title" style="margin-top:1.5rem;"><?php _e( 'Social links', 'webthinkershop' ); ?></h3>
                                    <p class="wt-field-description"><?php _e( 'These links and icons are shown in the footer. Add, remove, or reorder items. Each: URL, icon (upload), and alt text.', 'webthinkershop' ); ?></p>
                                    <div id="social-links-container" class="wt-footer-icons-wrap">
                                        <?php
                                        $sidx = 0;
                                        foreach ( $social_links as $item ) {
                                            $url  = is_array( $item ) ? ( $item['url'] ?? '' ) : '';
                                            $img  = is_array( $item ) ? ( $item['image'] ?? '' ) : '';
                                            $alt  = is_array( $item ) ? ( $item['alt'] ?? '' ) : '';
                                            $input_id = 'social_link_' . $sidx . '_image';
                                            $preview_src = ( $img === '' ) ? '' : ( ( strpos( $img, 'http' ) === 0 || strpos( $img, '//' ) === 0 ) ? $img : ( ( isset( $img[0] ) && $img[0] === '/' ) ? $icon_preview_base . $img : $icon_preview_theme . '/' . ltrim( $img, '/' ) ) );
                                            ?>
                                            <div class="wt-footer-icon-row wt-social-link-row" data-row="<?php echo (int) $sidx; ?>" draggable="true">
                                                <span class="wt-footer-row-drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'webthinkershop' ); ?>" aria-hidden="true"><i class="ph ph-dots-six-vertical"></i></span>
                                                <div class="wt-footer-icon-preview-wrap">
                                                    <img class="wt-footer-icon-preview" src="<?php echo $preview_src ? esc_url( $preview_src ) : ''; ?>" width="50" height="50" alt="" style="width:50px;height:50px;object-fit:contain;<?php echo $preview_src ? '' : 'display:none;'; ?>" data-empty="<?php echo $preview_src ? '0' : '1'; ?>" />
                                                    <?php if ( ! $preview_src ) : ?><span class="wt-footer-icon-preview-placeholder">50×50</span><?php endif; ?>
                                                </div>
                                                <label><?php _e( 'Link URL', 'webthinkershop' ); ?></label>
                                                <input type="url" class="wt-option-fields" name="wt_shop_theme_options_all[social_links][<?php echo (int) $sidx; ?>][url]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://twitter.com/..." />
                                                <label><?php _e( 'Icon', 'webthinkershop' ); ?></label>
                                                <input type="text" id="<?php echo esc_attr( $input_id ); ?>" class="wt-option-fields wt-footer-icon-url" name="wt_shop_theme_options_all[social_links][<?php echo (int) $sidx; ?>][image]" value="<?php echo esc_attr( $img ); ?>" placeholder="<?php echo esc_attr( get_template_directory_uri() ); ?>/assets/img/vectors/icon.svg" />
                                                <button type="button" class="wt-btn wt-btn-primary js-footer-icon-upload" data-target-id="<?php echo esc_attr( $input_id ); ?>"><?php _e( 'Upload', 'webthinkershop' ); ?></button>
                                                <label><?php _e( 'Alt text', 'webthinkershop' ); ?></label>
                                                <input type="text" class="wt-option-fields" name="wt_shop_theme_options_all[social_links][<?php echo (int) $sidx; ?>][alt]" value="<?php echo esc_attr( $alt ); ?>" placeholder="social twitter" />
                                                <button type="button" class="wt-btn wt-btn-secondary js-footer-icon-remove"><?php _e( 'Remove', 'webthinkershop' ); ?></button>
                                            </div>
                                            <?php
                                            $sidx++;
                                        }
                                        ?>
                                    </div>
                                    <button type="button" class="wt-btn wt-btn-secondary" id="add-social-link"><?php _e( 'Add social', 'webthinkershop' ); ?></button>
                                </div>
                            </section>

                            <section class="wt-tab-panel" id="payments" role="tabpanel" aria-labelledby="payments-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-credit-card"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Payment icons', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Icons shown in the footer. Add, remove, or reorder. Each item: image (upload) and alt text.', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <div id="payments-icons-container" class="wt-footer-icons-wrap">
                                        <?php
                                        $pidx = 0;
                                        foreach ( $footer_support_payment_icons as $icon ) {
                                            $img = is_array( $icon ) ? ( $icon['image'] ?? '' ) : '';
                                            $alt = is_array( $icon ) ? ( $icon['alt'] ?? '' ) : '';
                                            $input_id = 'footer_support_payment_' . $pidx . '_image';
                                            $preview_src = ( $img === '' ) ? '' : ( ( strpos( $img, 'http' ) === 0 || strpos( $img, '//' ) === 0 ) ? $img : ( ( isset( $img[0] ) && $img[0] === '/' ) ? $icon_preview_base . $img : $icon_preview_theme . '/' . ltrim( $img, '/' ) ) );
                                            ?>
                                            <div class="wt-footer-icon-row" data-row="<?php echo (int) $pidx; ?>" draggable="true">
                                                <span class="wt-footer-row-drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'webthinkershop' ); ?>" aria-hidden="true"><i class="ph ph-dots-six-vertical"></i></span>
                                                <div class="wt-footer-icon-preview-wrap">
                                                    <img class="wt-footer-icon-preview" src="<?php echo $preview_src ? esc_url( $preview_src ) : ''; ?>" width="50" height="50" alt="" style="width:50px;height:50px;object-fit:contain;<?php echo $preview_src ? '' : 'display:none;'; ?>" data-empty="<?php echo $preview_src ? '0' : '1'; ?>" />
                                                    <?php if ( ! $preview_src ) : ?><span class="wt-footer-icon-preview-placeholder">50×50</span><?php endif; ?>
                                                </div>
                                                <label><?php _e( 'Image', 'webthinkershop' ); ?></label>
                                                <input type="text" id="<?php echo esc_attr( $input_id ); ?>" class="wt-option-fields wt-footer-icon-url" name="wt_shop_theme_options_all[footer_support_payment_icons][<?php echo (int) $pidx; ?>][image]" value="<?php echo esc_attr( $img ); ?>" placeholder="<?php echo esc_attr( get_template_directory_uri() ); ?>/assets/img/vectors/icon.svg" />
                                                <button type="button" class="wt-btn wt-btn-primary js-footer-icon-upload" data-target-id="<?php echo esc_attr( $input_id ); ?>"><?php _e( 'Upload', 'webthinkershop' ); ?></button>
                                                <label><?php _e( 'Alt text', 'webthinkershop' ); ?></label>
                                                <input type="text" class="wt-option-fields" name="wt_shop_theme_options_all[footer_support_payment_icons][<?php echo (int) $pidx; ?>][alt]" value="<?php echo esc_attr( $alt ); ?>" placeholder="visa" />
                                                <button type="button" class="wt-btn wt-btn-secondary js-footer-icon-remove"><?php _e( 'Remove', 'webthinkershop' ); ?></button>
                                            </div>
                                            <?php
                                            $pidx++;
                                        }
                                        ?>
                                    </div>
                                    <button type="button" class="wt-btn wt-btn-secondary" id="add-payment-icon"><?php _e( 'Add payment icon', 'webthinkershop' ); ?></button>
                                </div>
                            </section>

                            <section class="wt-tab-panel" id="other-setting" role="tabpanel" aria-labelledby="other-setting-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-star"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Other Settings', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Configure additional settings', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="other_title">
                                            <i class="ph ph-text-aa" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Title', 'webthinkershop' ); ?>
                                        </label>
                                        <input id="other_title" class="wt-input-field" type="text"
                                           name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[other_title]"
                                           value="<?php esc_attr_e( $other_title ); ?>"/>
                                    </div>

                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="recaptcha_site_key">
                                            <i class="ph ph-shield-check" aria-hidden="true"></i>
                                            <?php esc_html_e( 'reCAPTCHA Site Key', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Used for registration form (v2 checkbox). Get keys at google.com/recaptcha/admin.', 'webthinkershop' ); ?></div>
                                        <input id="recaptcha_site_key" class="wt-input-field" type="text"
                                           name="wt_shop_theme_options_all[recaptcha_site_key]"
                                           value="<?php echo esc_attr( $recaptcha_site_key ); ?>"
                                           placeholder="6Lc..."/>
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="recaptcha_secret_key">
                                            <i class="ph ph-key" aria-hidden="true"></i>
                                            <?php esc_html_e( 'reCAPTCHA Secret Key', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Keep secret. Used to verify reCAPTCHA on the server.', 'webthinkershop' ); ?></div>
                                        <input id="recaptcha_secret_key" class="wt-input-field" type="password"
                                           name="wt_shop_theme_options_all[recaptcha_secret_key]"
                                           value="<?php echo esc_attr( $recaptcha_secret_key ); ?>"
                                           placeholder="6Lc..."/>
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="language_switch_model">
                                            <i class="ph ph-globe-hemisphere-west" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Language Switch Model', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Choose how the language switch is shown in the header.', 'webthinkershop' ); ?></div>
                                        <select id="language_switch_model" class="wt-input-field" name="wt_shop_theme_options_all[language_switch_model]">
                                            <option value="modal" <?php selected( $language_switch_model, 'modal' ); ?>><?php esc_html_e( 'Modal (current)', 'webthinkershop' ); ?></option>
                                            <option value="dropdown" <?php selected( $language_switch_model, 'dropdown' ); ?>><?php esc_html_e( 'Dropdown (modern)', 'webthinkershop' ); ?></option>
                                            <option value="flags" <?php selected( $language_switch_model, 'flags' ); ?>><?php esc_html_e( 'Flags only', 'webthinkershop' ); ?></option>
                                            <option value="abbr" <?php selected( $language_switch_model, 'abbr' ); ?>><?php esc_html_e( 'Abbreviation only', 'webthinkershop' ); ?></option>
                                        </select>
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="mini_cart_model">
                                            <i class="ph ph-shopping-cart" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Mini Cart Model', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Choose how mini cart opens in header.', 'webthinkershop' ); ?></div>
                                        <select id="mini_cart_model" class="wt-input-field" name="wt_shop_theme_options_all[mini_cart_model]">
                                            <option value="panel" <?php selected( $mini_cart_model, 'panel' ); ?>><?php esc_html_e( 'Slide panel (current)', 'webthinkershop' ); ?></option>
                                            <option value="dropdown" <?php selected( $mini_cart_model, 'dropdown' ); ?>><?php esc_html_e( 'Dropdown', 'webthinkershop' ); ?></option>
                                        </select>
                                    </div>

                                
                            </section>

                            <section class="wt-tab-panel" id="thank-you-page" role="tabpanel" aria-labelledby="thank-you-page-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-check-circle"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php esc_html_e( 'Thank you page', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php esc_html_e( 'Edit the text shown on the order confirmation (thank you) page. These strings are translatable per language.', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_heading">
                                            <i class="ph ph-text-t" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Heading', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Main headline (e.g. "Thank You!")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_heading"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_heading]"
                                               value="<?php echo esc_attr( $thank_you_heading ); ?>"
                                               placeholder="<?php esc_attr_e( 'Thank You!', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_subheading">
                                            <i class="ph ph-text-aa" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Subheading', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Line below the heading (e.g. "Your order is confirmed.")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_subheading"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_subheading]"
                                               value="<?php echo esc_attr( $thank_you_subheading ); ?>"
                                               placeholder="<?php esc_attr_e( 'Your order is confirmed.', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_order_message">
                                            <i class="ph ph-hand-heart" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Order message', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Message in the order card (e.g. "Thank you for your purchase!")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_order_message"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_order_message]"
                                               value="<?php echo esc_attr( $thank_you_order_message ); ?>"
                                               placeholder="<?php esc_attr_e( 'Thank you for your purchase!', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_confirmation_label">
                                            <i class="ph ph-envelope" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Confirmation sent to (label)', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Use %s where the email appears (e.g. "Confirmation sent to: %s")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_confirmation_label"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_confirmation_label]"
                                               value="<?php echo esc_attr( $thank_you_confirmation_label ); ?>"
                                               placeholder="<?php esc_attr_e( 'Confirmation sent to: %s', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_delivery_label">
                                            <i class="ph ph-truck" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Estimated delivery (label)', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Use %s where the delivery text appears (e.g. "Estimated delivery: %s")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_delivery_label"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_delivery_label]"
                                               value="<?php echo esc_attr( $thank_you_delivery_label ); ?>"
                                               placeholder="<?php esc_attr_e( 'Estimated delivery: %s', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_button_text">
                                            <i class="ph ph-arrow-left" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Button text', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Primary button (e.g. "Continue shopping")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_button_text"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_button_text]"
                                               value="<?php echo esc_attr( $thank_you_button_text ); ?>"
                                               placeholder="<?php esc_attr_e( 'Continue shopping', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_estimated_delivery">
                                            <i class="ph ph-package" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Estimated delivery text', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Shown in the order card (e.g. "3–5 business days")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_estimated_delivery"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_estimated_delivery]"
                                               value="<?php echo esc_attr( $thank_you_estimated_delivery ); ?>"
                                               placeholder="<?php esc_attr_e( '3–5 business days', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_contact_intro">
                                            <i class="ph ph-chat-circle" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Contact intro text', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Text before the contact link (e.g. "If you have any issues, contact us.")', 'webthinkershop' ); ?></div>
                                        <input class="wt-input-field" type="text" id="thank_you_contact_intro"
                                               name="wt_shop_theme_options_<?php echo esc_attr( $current_lang_code ); ?>[thank_you_contact_intro]"
                                               value="<?php echo esc_attr( $thank_you_contact_intro ); ?>"
                                               placeholder="<?php esc_attr_e( 'If you have any issues, contact us.', 'webthinkershop' ); ?>"
                                        />
                                    </div>
                                    <div class="wt-field-group">
                                        <label class="wt-field-label" for="thank_you_contact_page_id">
                                            <i class="ph ph-link" aria-hidden="true"></i>
                                            <?php esc_html_e( 'Contact page', 'webthinkershop' ); ?>
                                        </label>
                                        <div class="wt-field-description"><?php esc_html_e( 'Page used for the "Contact us" link (same for all languages).', 'webthinkershop' ); ?></div>
                                        <?php
                                        wp_dropdown_pages( array(
                                            'id'                => 'thank_you_contact_page_id',
                                            'name'              => 'wt_shop_theme_options_all[thank_you_contact_page_id]',
                                            'selected'          => $thank_you_contact_page_id,
                                            'show_option_none'  => '— ' . esc_attr__( 'Select page', 'webthinkershop' ) . ' —',
                                            'option_none_value' => 0,
                                            'class'             => 'wt-input-field',
                                        ) );
                                        ?>
                                    </div>
                                </div>
                            </section>

                            <section class="wt-tab-panel" id="footer" role="tabpanel" aria-labelledby="footer-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-layout"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Footer Settings', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'What you see is what you get — edit the footer description below and it appears as-is on the site.', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                <hr>
                                <!-- Footer description (WYSIWYG) -->
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="footer_description">
                                        <i class="ph ph-text-align-left" aria-hidden="true"></i>
                                        <?php _e( 'Footer description', 'webthinkershop' ); ?>
                                    </label>
                                    <p class="wt-field-description"><?php _e( 'What you see is what you get. This text appears in the footer under the logo and first title. You can use HTML (e.g. &lt;p&gt; tags).', 'webthinkershop' ); ?></p>
                                    <textarea id="footer_description" class="wt-option-fields" name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_description]" rows="4" style="width:100%;"><?php echo esc_textarea( $footer_description ); ?></textarea>
                                </div>
                                <hr/>
                                <!--Footer Logo -->
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="image_url_wt_shop_footer_logo">
                                        <i class="ph ph-image" aria-hidden="true"></i>
                                        <?php _e( 'Footer Logo', "webthinkershop" ); ?>
                                    </label>
                                    <p class="wt-field-description"><?php _e( 'Upload your footer logo image', 'webthinkershop' ); ?></p>
                                    
                                    <input id="image_url_wt_shop_footer_logo" type="text" name="wt_shop_theme_options_all[wt_shop_footer_logo]" value="<?php esc_attr_e( $wt_shop_footer_logo ); ?>" class="wt-hidden-input" />
                                    <div class="wt-upload-actions">
                                        <button id="upload_button_wt_shop_footer_logo" type="button" class="wt-btn wt-btn-primary">
                                            <i class="ph ph-upload" aria-hidden="true"></i>
                                            <?php _e( 'Upload Logo', "webthinkershop" ); ?>
                                        </button>
                                        <button id="remove_button_wt_shop_footer_logo" type="button" class="wt-btn wt-btn-secondary">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                            <?php _e( 'Remove', "webthinkershop" ); ?>
                                        </button>
                                    </div>
                                    <div class="wt-image-preview">
                                        <img id="preview_image_wt_shop_footer_logo" src="<?php echo esc_url($wt_shop_footer_logo); ?>" alt="Footer Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_footer_logo === null || $wt_shop_footer_logo == '') ? 'style="display: none;"' : ''; ?>>
                                        <div class="wt-preview-placeholder" <?php echo ($wt_shop_footer_logo !== null && $wt_shop_footer_logo != '') ? 'style="display: none;"' : ''; ?>>
                                            <i class="ph ph-image" aria-hidden="true"></i>
                                            <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <hr/>
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="footer_android_link">
                                        <i class="ph ph-link" aria-hidden="true"></i>
                                        <?php _e( 'Footer Android Link', 'webthinkershop' ); ?>
                                    </label>
                                    <input id="footer_android_link" class="wt-input-field" type="text"
                                           name="wt_shop_theme_options_all[footer_android_link]"
                                           value="<?php esc_attr_e( $footer_android_link ); ?>"/>
                                </div>


                                <!--Footer Android Logo -->
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="image_url_wt_shop_footer_logo_2">
                                        <i class="ph ph-android-logo" aria-hidden="true"></i>
                                        <?php _e( 'Footer Android Logo', "webthinkershop" ); ?>
                                    </label>
                                    <p class="wt-field-description"><?php _e( 'Upload your Android app logo for the footer', 'webthinkershop' ); ?></p>
                                    
                                    <input id="image_url_wt_shop_footer_logo_2" type="text" name="wt_shop_theme_options_all[wt_shop_footer_logo_2]" value="<?php esc_attr_e( $wt_shop_footer_logo_2 ); ?>" class="wt-hidden-input" />
                                    <div class="wt-upload-actions">
                                        <button id="upload_button_wt_shop_footer_logo_2" type="button" class="wt-btn wt-btn-primary">
                                            <i class="ph ph-upload" aria-hidden="true"></i>
                                            <?php _e( 'Upload Logo', "webthinkershop" ); ?>
                                        </button>
                                        <button id="remove_button_wt_shop_footer_logo_2" type="button" class="wt-btn wt-btn-secondary">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                            <?php _e( 'Remove', "webthinkershop" ); ?>
                                        </button>
                                    </div>
                                    <div class="wt-image-preview">
                                        <img id="preview_image_wt_shop_footer_logo_2" src="<?php echo esc_url($wt_shop_footer_logo_2); ?>" alt="Footer Android Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_footer_logo_2 === null || $wt_shop_footer_logo_2 == '') ? 'style="display: none;"' : ''; ?>>
                                        <div class="wt-preview-placeholder" <?php echo ($wt_shop_footer_logo_2 !== null && $wt_shop_footer_logo_2 != '') ? 'style="display: none;"' : ''; ?>>
                                            <i class="ph ph-android-logo" aria-hidden="true"></i>
                                            <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <hr/>
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="footer_apple_link">
                                        <i class="ph ph-link" aria-hidden="true"></i>
                                        <?php _e( 'Footer Apple Link', 'webthinkershop' ); ?>
                                    </label>
                                    <input id="footer_apple_link" class="wt-input-field" type="text"
                                           name="wt_shop_theme_options_all[footer_apple_link]"
                                           value="<?php esc_attr_e( $footer_apple_link ); ?>"/>
                                </div>

                                <!--Footer Apple Logo -->
                                <div class="wt-field-group">
                                    <label class="wt-field-label" for="image_url_wt_shop_footer_logo_3">
                                        <i class="ph ph-apple-logo" aria-hidden="true"></i>
                                        <?php _e( 'Footer Apple Logo', "webthinkershop" ); ?>
                                    </label>
                                    <p class="wt-field-description"><?php _e( 'Upload your Apple app logo for the footer', 'webthinkershop' ); ?></p>
                                    
                                    <input id="image_url_wt_shop_footer_logo_3" type="text" name="wt_shop_theme_options_all[wt_shop_footer_logo_3]" value="<?php esc_attr_e( $wt_shop_footer_logo_3 ); ?>" class="wt-hidden-input" />
                                    <div class="wt-upload-actions">
                                        <button id="upload_button_wt_shop_footer_logo_3" type="button" class="wt-btn wt-btn-primary">
                                            <i class="ph ph-upload" aria-hidden="true"></i>
                                            <?php _e( 'Upload Logo', "webthinkershop" ); ?>
                                        </button>
                                        <button id="remove_button_wt_shop_footer_logo_3" type="button" class="wt-btn wt-btn-secondary">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                            <?php _e( 'Remove', "webthinkershop" ); ?>
                                        </button>
                                    </div>
                                    <div class="wt-image-preview">
                                        <img id="preview_image_wt_shop_footer_logo_3" src="<?php echo esc_url($wt_shop_footer_logo_3); ?>" alt="Footer Apple Logo Preview" class="wt-preview-image" <?php echo ($wt_shop_footer_logo_3 === null || $wt_shop_footer_logo_3 == '') ? 'style="display: none;"' : ''; ?>>
                                        <div class="wt-preview-placeholder" <?php echo ($wt_shop_footer_logo_3 !== null && $wt_shop_footer_logo_3 != '') ? 'style="display: none;"' : ''; ?>>
                                            <i class="ph ph-apple-logo" aria-hidden="true"></i>
                                            <span><?php _e( 'No image selected', "webthinkershop" ); ?></span>
                                        </div>
                                    </div>
                                </div>

                            <hr/>
                                    <div class="wt-footer-fields">
                                        <div class="wt-field-group">
                                            <label class="wt-field-label">
                                                <i class="ph ph-arrows-down-up" aria-hidden="true"></i>
                                                <?php _e( 'Footer right column: order of blocks', 'webthinkershop' ); ?>
                                            </label>
                                            <div class="wt-field-description"><?php _e( 'Drag to reorder. Top = shown first in the footer.', 'webthinkershop' ); ?></div>
                                            <div id="footer-right-column-order" class="wt-footer-order-list" role="list">
                                                <?php
                                                foreach ( $footer_right_column_order as $key ) {
                                                    $label = $key === 'payments' ? __( 'Payments', 'webthinkershop' ) : __( 'Social media', 'webthinkershop' );
                                                    $icon  = $key === 'payments' ? 'ph-credit-card' : 'ph-share-network';
                                                    ?>
                                                    <div class="wt-footer-order-item" role="listitem" draggable="true" data-key="<?php echo esc_attr( $key ); ?>">
                                                        <span class="wt-footer-order-grip" aria-hidden="true"><i class="ph ph-dots-six-vertical"></i></span>
                                                        <span class="wt-footer-order-label"><i class="ph <?php echo esc_attr( $icon ); ?>"></i> <?php echo esc_html( $label ); ?></span>
                                                        <input type="hidden" name="wt_shop_footer_right_column_order[]" value="<?php echo esc_attr( $key ); ?>" />
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <hr class="wt-field-sep"/>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_1">
                                                <i class="ph ph-text-aa" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 1', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_title_1" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_1]"
                                                   value="<?php esc_attr_e( $footer_title_1 ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_2">
                                                <i class="ph ph-text-aa" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 2', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_title_2" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_2]"
                                                   value="<?php esc_attr_e( $footer_title_2 ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_3">
                                                <i class="ph ph-text-aa" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 3', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_title_3" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_3]"
                                                   value="<?php esc_attr_e( $footer_title_3 ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_support_text">
                                                <i class="ph ph-chat-circle-text" aria-hidden="true"></i>
                                                <?php _e( 'Footer column 3 subtext (e.g. Support response time)', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_support_text" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_support_text]"
                                                   value="<?php esc_attr_e( $footer_support_text ); ?>"
                                                   placeholder="<?php esc_attr_e( 'e.g. Support response: 24–48h', 'webthinkershop' ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_4">
                                                <i class="ph ph-text-aa" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 4', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_title_4" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_4]"
                                                   value="<?php esc_attr_e( $footer_title_4 ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_5">
                                                <i class="ph ph-credit-card" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 5', 'webthinkershop' ); ?>
                                            </label>
                                            <div class="wt-field-description"><?php _e( 'Shown above payment icons in the footer.', 'webthinkershop' ); ?></div>
                                            <input id="footer_title_5" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_5]"
                                                   value="<?php esc_attr_e( $footer_title_5 ); ?>"
                                                   placeholder="<?php esc_attr_e( 'e.g. Payment methods', 'webthinkershop' ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_title_6">
                                                <i class="ph ph-share-network" aria-hidden="true"></i>
                                                <?php _e( 'Footer Title 6', 'webthinkershop' ); ?>
                                            </label>
                                            <div class="wt-field-description"><?php _e( 'Shown above social media icons in the footer.', 'webthinkershop' ); ?></div>
                                            <input id="footer_title_6" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_title_6]"
                                                   value="<?php esc_attr_e( $footer_title_6 ); ?>"
                                                   placeholder="<?php esc_attr_e( 'e.g. Follow us', 'webthinkershop' ); ?>"/>
                                        </div>

                                        <hr class="wt-field-sep"/>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_address">
                                                <i class="ph ph-map-pin" aria-hidden="true"></i>
                                                <?php _e( 'Address', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_address" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_address]"
                                                   value="<?php esc_attr_e( $footer_address ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_address_2">
                                                <i class="ph ph-map-pin" aria-hidden="true"></i>
                                                <?php _e( 'Address 2', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_address_2" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_address_2]"
                                                   value="<?php esc_attr_e( $footer_address_2 ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_address_2_link">
                                                <i class="ph ph-link" aria-hidden="true"></i>
                                                <?php _e( 'Footer Address Link', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_address_2_link" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_address_2_link]"
                                                   value="<?php esc_attr_e( $footer_address_2_link ); ?>"/>
                                        </div>

                                        <hr class="wt-field-sep"/>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_phone_number_title">
                                                <i class="ph ph-phone" aria-hidden="true"></i>
                                                <?php _e( 'Phone number title', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_phone_number_title" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_phone_number_title]"
                                                   value="<?php esc_attr_e( $footer_phone_number_title ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_phone_number">
                                                <i class="ph ph-phone" aria-hidden="true"></i>
                                                <?php _e( 'Phone number', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_phone_number" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_phone_number]"
                                                   value="<?php esc_attr_e( $footer_phone_number ); ?>"/>
                                        </div>
                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="footer_phone_number_link">
                                                <i class="ph ph-link" aria-hidden="true"></i>
                                                <?php _e( 'Phone number link', 'webthinkershop' ); ?>
                                            </label>
                                            <input id="footer_phone_number_link" class="wt-input-field" type="text"
                                                   name="wt_shop_theme_options_<?php echo $current_lang_code; ?>[footer_phone_number_link]"
                                                   value="<?php esc_attr_e( $footer_phone_number_link ); ?>"/>
                                        </div>

                                        <p class="wt-field-description" style="margin-top:1rem;color:var(--wt-shop-primary);"><?php _e( 'Social icons in the footer are managed in the Social Media tab. Payment icons are in the Payments tab.', 'webthinkershop' ); ?></p>

                                        <div class="wt-field-group">
                                            <label class="wt-field-label" for="wt_shop_theme_options_lang_copyright">
                                                <i class="ph ph-file-text" aria-hidden="true"></i>
                                                <?php _e( 'Copyright', 'webthinkershop' ); ?>
                                            </label>
                                            <?php echo get_wp_editor( (string) $copyright, 'wt_shop_theme_options_lang_copyright', 'wt_shop_theme_options_' . $current_lang_code . '[copyright]' ); ?>
                                        </div>
                                    </div>
                                <hr>
                            </section>

                            <section class="wt-tab-panel" id="settings" role="tabpanel" aria-labelledby="settings-tab" aria-hidden="true">
                                <header class="wt-panel-header">
                                    <div class="wt-panel-header-content">
                                        <div class="wt-panel-icon">
                                            <i class="ph ph-gear"></i>
                                        </div>
                                        <div class="wt-panel-title-group">
                                            <h1 class="wt-panel-title"><?php _e( 'Additional Settings', 'webthinkershop' ); ?></h1>
                                            <p class="wt-panel-description"><?php _e( 'Advanced configuration and additional options', 'webthinkershop' ); ?></p>
                                        </div>
                                    </div>
                                </header>
                                <div class="wt-panel-content">
                                    <div class="wt-field-group">
                                        <div class="wt-info-card">
                                            <i class="ph ph-info" aria-hidden="true"></i>
                                            <div class="wt-info-content">
                                                <h3><?php _e( 'Coming Soon', 'webthinkershop' ); ?></h3>
                                                <p><?php _e( 'Additional settings and advanced configuration options will be available in future updates.', 'webthinkershop' ); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </main>
                </div>
            </div>

            <!-- Modern Submit Section -->
            <footer class="wt-options-footer">
                <div class="wt-footer-content">
                    <div class="wt-footer-info">
                        <i class="ph ph-info" aria-hidden="true"></i>
                        <span><?php _e( "Don't forget to save your changes", 'webthinkershop' ); ?></span>
                    </div>
                    
                    <div class="wt-footer-actions">
                        <button
                                type="button"
                                class="wt-btn wt-btn-secondary"
                                id="preview-changes"
                                onclick="window.open('<?php echo esc_url( home_url() ); ?>', '_blank');"
                        >
                            <i class="ph ph-eye" aria-hidden="true"></i>
                            <?php _e( 'Preview Changes', 'webthinkershop' ); ?>
                        </button>

                        <button type="submit" class="wt-btn wt-btn-primary wt-btn-save">
                            <i class="ph ph-floppy-disk" aria-hidden="true"></i>
                            <?php _e( 'Save Settings', "webthinkershop" ); ?>
                        </button>
                </div>
                </div>
            </footer>
            </div>
        </form>
    </div>
    <style>
        .wt-footer-icon-row {
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, border-color 180ms ease;
        }
        .wt-footer-row-drag-handle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            color: var(--wt-shop-gray, #737373);
            background: rgba(0, 0, 0, 0.04);
            cursor: grab;
            user-select: none;
        }
        .wt-footer-row-drag-handle .ph {
            font-size: 16px;
            line-height: 1;
        }
        .wt-footer-icon-row:active .wt-footer-row-drag-handle {
            cursor: grabbing;
        }
        .wt-footer-icons-wrap.wt-drag-active .wt-footer-icon-row:not(.wt-footer-order-dragging) {
            transform: scale(0.998);
        }
        .wt-footer-icon-row.wt-footer-order-dragging {
            background: rgba(9, 16, 87, 0.12);
            border: 1px solid rgba(9, 16, 87, 0.45);
            border-radius: 8px;
            box-shadow: 0 8px 18px rgba(9, 16, 87, 0.18);
        }
        .wt-drop-placeholder {
            border: 2px dashed rgba(9, 16, 87, 0.45);
            background: rgba(9, 16, 87, 0.06);
            border-radius: 8px;
            margin: 6px 0 10px;
            min-height: 56px;
            transition: all 120ms ease;
            pointer-events: none;
        }
        #footer-right-column-order.wt-drag-active .wt-footer-order-item:not(.wt-footer-order-dragging) {
            transform: translateX(2px);
            transition: transform 140ms ease;
        }
    </style>
    <script>
        jQuery(document).ready(function($) {

            // Modern Notification System
            function show_notification(type, title, message, duration = 500000000) {
                const container = document.getElementById('wt-notification-container');
                const notification = document.createElement('div');
                notification.className = `wt-notification ${type}`;
                
                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };
                
                notification.innerHTML = `
                    <div class="wt-notification-icon">${icons[type] || icons.info}</div>
                    <div class="wt-notification-content">
                        <div class="wt-notification-title">${title}</div>
                        <div class="wt-notification-message">${message}</div>
                    </div>
                    <button class="wt-notification-close" onclick="this.parentElement.remove()">×</button>
                `;
                
                container.appendChild(notification);
                
                // Trigger animation
                setTimeout(() => notification.classList.add('show'), 100000000);
                
                // Auto remove after duration
                if (duration > 0) {
                    setTimeout(() => {
                        notification.classList.remove('show');
                        setTimeout(() => notification.remove(), 3000000);
                    }, duration);
                }
            }

            // Handle existing notifications on page load
            function init_existing_notifications() {
                const existing_notifications = document.querySelectorAll('.wt-notification.show');
                existing_notifications.forEach(notification => {
                    const duration = 5000; // 5 seconds for PHP-generated notifications
                    const progress_bar = notification.querySelector('.wt-notification-progress');
                    
                    if (progress_bar) {
                        progress_bar.style.width = '100%';
                        progress_bar.style.transition = `width ${duration}ms linear`;
                        
                        setTimeout(() => {
                            notification.classList.remove('show');
                            setTimeout(() => notification.remove(), 300000);
                        }, duration);
                    }
                });
            }

            // Initialize existing notifications
            init_existing_notifications();

            // Modern tab navigation
            function init_tab_navigation() {
                const all_indicator = document.querySelectorAll('.wt-nav-item');
                const all_content = document.querySelectorAll('.wt-tab-panel');

                all_indicator.forEach(item => {
                    item.addEventListener('click', function () {
                        const target_id = this.dataset.target;
                        const content = document.querySelector(target_id);

                        // Update ARIA attributes
                        all_indicator.forEach(i => {
                            i.classList.remove('active');
                            i.setAttribute('aria-selected', 'false');
                        });

                        all_content.forEach(i => {
                            i.classList.remove('active');
                            i.setAttribute('aria-hidden', 'true');
                        });

                        // Activate current tab
                        this.classList.add('active');
                        this.setAttribute('aria-selected', 'true');
                        content.classList.add('active');
                        content.setAttribute('aria-hidden', 'false');
                    });

                    // Keyboard navigation
                    item.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            this.click();
                        }
                    });
                });
            }

            // Initialize tab navigation
            init_tab_navigation();


            // Hochladen
            $('#upload_button_wt_shop_logo, #upload_button_wt_shop_logo_active, #upload_button_wt_shop_logo_mobile, #upload_button_wt_shop_footer_logo, #upload_button_wt_shop_footer_logo_2,  #upload_button_wt_shop_footer_logo_3, #upload_button_wt_shop_slider_background').on('click', function(e) {
                e.preventDefault();

                // get the name of the current clicked button id (without 'upload_button_' prefix)
                var option_name = this.id.replace('upload_button_', '');

                var image = wp.media({
                    title: 'Hochladen Image',
                    multiple: false
                }).open().on('select', function(e) {
                    var uploaded_image = image.state().get('selection').first();
                    var full_image_url = uploaded_image.toJSON().url;

                    // Create a URL object
                    var url_path = new URL(full_image_url);

                    // Get the pathname (the part of the URL after the domain)
                    var image_url = url_path.pathname;

                    $('#image_url_' + option_name).val(image_url);
                    
                    // Handle modern image previews (with placeholders)
                    var $preview_image = $('#preview_image_' + option_name);
                    var $placeholder = $preview_image.siblings('.wt-preview-placeholder');
                    
                    console.log('Uploading image for:', option_name);
                    console.log('Preview image found:', $preview_image.length);
                    console.log('Placeholder found:', $placeholder.length);
                    
                    if ($placeholder.length > 0) {
                        // Modern preview with placeholder
                        $preview_image.attr('src', full_image_url).show();
                        $placeholder.hide();
                        console.log('Modern preview updated');
                    } else {
                        // Old style preview (footer logos)
                        $preview_image.attr('src', full_image_url);
                        console.log('Legacy preview updated');
                    }
                    
                    show_notification('success', 'Image Uploaded', 'Image has been successfully uploaded and preview updated.');
                });
            });

            // Entfernen
            $('#remove_button_wt_shop_logo, #remove_button_wt_shop_logo_active, #remove_button_wt_shop_logo_mobile, #remove_button_wt_shop_footer_logo, #remove_button_wt_shop_footer_logo_2,  #remove_button_wt_shop_footer_logo_3,  #remove_button_wt_shop_slider_background').on('click', function(e) {
                e.preventDefault();

                // get the name of the current clicked button id (without 'remove_button_' prefix)
                var option_name = this.id.replace('remove_button_', '');

                // Clear the input field
                $('#image_url_' + option_name).val('');
                
                // Handle modern image previews (with placeholders)
                var $preview_image = $('#preview_image_' + option_name);
                var $placeholder = $preview_image.siblings('.wt-preview-placeholder');
                
                if ($placeholder.length > 0) {
                    // Modern preview with placeholder
                    $preview_image.attr('src', '').hide();
                    $placeholder.show();
                } else {
                    // Old style preview (footer logos)
                    $preview_image.attr('src', '');
                }
                
                // Show success notification
                show_notification('success', 'Image Removed', 'Image has been removed from the preview. Save the settings to permanently remove it.');
            });

            window.wt_icon_preview = { base_url: '<?php echo esc_js( $icon_preview_base ); ?>', theme_uri: '<?php echo esc_js( $icon_preview_theme ); ?>' };
            function update_footer_icon_preview($row, src) {
                var $preview = $row.find('.wt-footer-icon-preview');
                var $placeholder = $row.find('.wt-footer-icon-preview-placeholder');
                if (src) {
                    $preview.attr('src', src).show().attr('data-empty', '0');
                    $placeholder.hide();
                } else {
                    $preview.attr('src', '').hide().attr('data-empty', '1');
                    $placeholder.show();
                }
            }
            $(document).on('input', '.wt-footer-icon-url', function() {
                var val = $(this).val().trim();
                var base = window.wt_icon_preview.base_url || '';
                var theme = window.wt_icon_preview.theme_uri || '';
                var src = '';
                if (val) {
                    if (val.indexOf('http') === 0 || val.indexOf('//') === 0) src = val;
                    else if (val.charAt(0) === '/') src = base + val;
                    else src = theme + '/' + val.replace(/^\/+/, '');
                }
                update_footer_icon_preview($(this).closest('.wt-footer-icon-row'), src);
            });
            $(document).on('click', '.js-footer-icon-upload', function(e) {
                e.preventDefault();
                var target_id = $(this).data('target-id');
                if (!target_id) return;
                var $row = $(this).closest('.wt-footer-icon-row');
                var image = wp.media({ title: 'Select image', multiple: false }).open().on('select', function() {
                    var uploaded_image = image.state().get('selection').first();
                    var full_image_url = uploaded_image.toJSON().url;
                    var url_path = new URL(full_image_url);
                    var image_url = url_path.pathname;
                    $('#' + target_id).val(image_url);
                    update_footer_icon_preview($row, full_image_url);
                    show_notification('success', 'Image selected', 'Image has been set. Save options to apply.');
                });
            });
            $(document).on('click', '.js-footer-icon-remove', function(e) {
                e.preventDefault();
                var $row = $(this).closest('.wt-footer-icon-row');
                var $container = $row.closest('.wt-footer-icons-wrap');
                $row.remove();
                if ($container.attr('id') === 'social-links-container') {
                    reindex_footer_icon_rows('social-links-container', 'social_links', true);
                } else if ($container.attr('id') === 'payments-icons-container') {
                    reindex_footer_icon_rows('payments-icons-container', 'footer_support_payment_icons', false);
                }
            });

            function reindex_footer_icon_rows(container_id, option_key, has_url) {
                var $container = $('#' + container_id);
                $container.find('.wt-footer-icon-row').each(function(idx) {
                    var $row = $(this);
                    var input_id = option_key + '_' + idx + '_image';
                    if (option_key === 'social_links') {
                        input_id = 'social_link_' + idx + '_image';
                    }
                    $row.attr('data-row', idx);
                    if (has_url) {
                        $row.find('input[name*=\"[url]\"]').attr('name', 'wt_shop_theme_options_all[' + option_key + '][' + idx + '][url]');
                    }
                    $row.find('input[name*=\"[image]\"]').attr('name', 'wt_shop_theme_options_all[' + option_key + '][' + idx + '][image]').attr('id', input_id);
                    $row.find('input[name*=\"[alt]\"]').attr('name', 'wt_shop_theme_options_all[' + option_key + '][' + idx + '][alt]');
                    $row.find('.js-footer-icon-upload').attr('data-target-id', input_id);
                });
            }

            function init_footer_icon_drag_sort(container_id, option_key, has_url) {
                var list = document.getElementById(container_id);
                if (!list) return;
                var dragged = null;
                var placeholder = document.createElement('div');
                placeholder.className = 'wt-drop-placeholder';

                function cleanup_drag_state() {
                    dragged = null;
                    list.classList.remove('wt-drag-active');
                    list.querySelectorAll('.wt-footer-icon-row').forEach(function(el) {
                        el.classList.remove('wt-footer-order-dragging', 'wt-footer-order-drag-above', 'wt-footer-order-drag-below');
                    });
                    if (placeholder.parentNode) {
                        placeholder.parentNode.removeChild(placeholder);
                    }
                }

                list.addEventListener('dragstart', function(e) {
                    var row = e.target.closest('.wt-footer-icon-row');
                    if (!row || !list.contains(row)) return;
                    dragged = row;
                    if (e.dataTransfer) {
                        e.dataTransfer.setData('text/plain', '');
                        e.dataTransfer.effectAllowed = 'move';
                    }
                    list.classList.add('wt-drag-active');
                    placeholder.style.height = Math.max(56, row.offsetHeight) + 'px';
                    row.classList.add('wt-footer-order-dragging');
                });

                list.addEventListener('dragover', function(e) {
                    if (!dragged) return;
                    e.preventDefault();
                    var other = e.target.closest('.wt-footer-icon-row');
                    if (!other || other === dragged || !list.contains(other)) {
                        return;
                    }
                    var rect  = other.getBoundingClientRect();
                    var above = e.clientY < rect.top + rect.height / 2;
                    if (above) list.insertBefore(placeholder, other);
                    else list.insertBefore(placeholder, other.nextSibling);
                });

                list.addEventListener('drop', function(e) {
                    if (!dragged) return;
                    e.preventDefault();
                    if (placeholder.parentNode === list) {
                        list.insertBefore(dragged, placeholder);
                    }
                    reindex_footer_icon_rows(container_id, option_key, has_url);
                    cleanup_drag_state();
                });

                list.addEventListener('dragend', function() {
                    cleanup_drag_state();
                });
            }

            // Footer right column order: drag and drop (payments / social media)
            (function() {
                var list = document.getElementById('footer-right-column-order');
                if (!list) return;
                var dragged = null;
                var placeholder = document.createElement('div');
                placeholder.className = 'wt-drop-placeholder';

                function cleanup() {
                    list.classList.remove('wt-drag-active');
                    list.querySelectorAll('.wt-footer-order-item').forEach(function(el) {
                        el.classList.remove('wt-footer-order-dragging', 'wt-footer-order-drag-above', 'wt-footer-order-drag-below');
                    });
                    if (placeholder.parentNode) {
                        placeholder.parentNode.removeChild(placeholder);
                    }
                    dragged = null;
                }

                list.querySelectorAll('.wt-footer-order-item').forEach(function(item) {
                    item.setAttribute('draggable', 'true');
                });

                list.addEventListener('dragstart', function(e) {
                    var item = e.target.closest('.wt-footer-order-item');
                    if (!item || !list.contains(item)) return;
                    dragged = item;
                    list.classList.add('wt-drag-active');
                    placeholder.style.height = Math.max(42, item.offsetHeight) + 'px';
                    if (e.dataTransfer) {
                        e.dataTransfer.setData('text/plain', '');
                        e.dataTransfer.effectAllowed = 'move';
                    }
                    item.classList.add('wt-footer-order-dragging');
                });

                list.addEventListener('dragover', function(e) {
                    if (!dragged) return;
                    e.preventDefault();
                    var other = e.target.closest('.wt-footer-order-item');
                    if (!other || other === dragged || !list.contains(other)) {
                        return;
                    }
                    var rect  = other.getBoundingClientRect();
                    var above = e.clientY < rect.top + rect.height / 2;
                    if (above) list.insertBefore(placeholder, other);
                    else list.insertBefore(placeholder, other.nextSibling);
                });

                list.addEventListener('drop', function(e) {
                    if (!dragged) return;
                    e.preventDefault();
                    if (placeholder.parentNode === list) {
                        list.insertBefore(dragged, placeholder);
                    }
                    cleanup();
                });

                list.addEventListener('dragend', function() {
                    cleanup();
                });
            })();

            function add_footer_icon_row(container_id, option_key) {
                var $container = $('#' + container_id);
                var idx = 0;
                $container.find('.wt-footer-icon-row').each(function() {
                    var r = parseInt($(this).attr('data-row'), 10);
                    if (!isNaN(r) && r >= idx) idx = r + 1;
                });
                var input_id = option_key + '_' + idx + '_image';
                var row = '<div class="wt-footer-icon-row" data-row="' + idx + '" draggable="true">' +
                    '<span class="wt-footer-row-drag-handle" title="<?php echo esc_js( __( 'Drag to reorder', 'webthinkershop' ) ); ?>" aria-hidden="true"><i class="ph ph-dots-six-vertical"></i></span>' +
                    '<div class="wt-footer-icon-preview-wrap">' +
                    '<img class="wt-footer-icon-preview" src="" width="50" height="50" alt="" style="width:50px;height:50px;object-fit:contain;display:none;" data-empty="1" />' +
                    '<span class="wt-footer-icon-preview-placeholder">50×50</span>' +
                    '</div>' +
                    '<label><?php echo esc_js( __( 'Image', 'webthinkershop' ) ); ?></label> ' +
                    '<input type="text" id="' + input_id + '" class="wt-option-fields wt-footer-icon-url" name="wt_shop_theme_options_all[' + option_key + '][' + idx + '][image]" value="" placeholder="<?php echo esc_js( get_template_directory_uri() ); ?>/assets/img/vectors/icon.svg" /> ' +
                    '<button type="button" class="wt-btn wt-btn-primary js-footer-icon-upload" data-target-id="' + input_id + '"><?php echo esc_js( __( 'Upload', 'webthinkershop' ) ); ?></button> ' +
                    '<label><?php echo esc_js( __( 'Alt text', 'webthinkershop' ) ); ?></label> ' +
                    '<input type="text" class="wt-option-fields" name="wt_shop_theme_options_all[' + option_key + '][' + idx + '][alt]" value="" /> ' +
                    '<button type="button" class="wt-btn wt-btn-secondary js-footer-icon-remove"><?php echo esc_js( __( 'Remove', 'webthinkershop' ) ); ?></button>' +
                    '</div>';
                $container.append(row);
                reindex_footer_icon_rows(container_id, option_key, false);
            }
            $('#add-footer-support-theme-icon').on('click', function(e) {
                e.preventDefault();
                add_footer_icon_row('footer-support-theme-icons', 'footer_support_theme_icons');
            });
            $('#add-payment-icon').on('click', function(e) {
                e.preventDefault();
                add_footer_icon_row('payments-icons-container', 'footer_support_payment_icons');
            });

            function add_social_link_row() {
                var $container = $('#social-links-container');
                var idx = 0;
                $container.find('.wt-social-link-row').each(function() {
                    var r = parseInt($(this).attr('data-row'), 10);
                    if (!isNaN(r) && r >= idx) idx = r + 1;
                });
                var input_id = 'social_link_' + idx + '_image';
                var row = '<div class="wt-footer-icon-row wt-social-link-row" data-row="' + idx + '" draggable="true">' +
                    '<span class="wt-footer-row-drag-handle" title="<?php echo esc_js( __( 'Drag to reorder', 'webthinkershop' ) ); ?>" aria-hidden="true"><i class="ph ph-dots-six-vertical"></i></span>' +
                    '<div class="wt-footer-icon-preview-wrap">' +
                    '<img class="wt-footer-icon-preview" src="" width="50" height="50" alt="" style="width:50px;height:50px;object-fit:contain;display:none;" data-empty="1" />' +
                    '<span class="wt-footer-icon-preview-placeholder">50×50</span>' +
                    '</div>' +
                    '<label><?php echo esc_js( __( 'Link URL', 'webthinkershop' ) ); ?></label> ' +
                    '<input type="url" class="wt-option-fields" name="wt_shop_theme_options_all[social_links][' + idx + '][url]" value="" placeholder="https://..." /> ' +
                    '<label><?php echo esc_js( __( 'Icon', 'webthinkershop' ) ); ?></label> ' +
                    '<input type="text" id="' + input_id + '" class="wt-option-fields wt-footer-icon-url" name="wt_shop_theme_options_all[social_links][' + idx + '][image]" value="" placeholder="<?php echo esc_js( get_template_directory_uri() ); ?>/assets/img/vectors/icon.svg" /> ' +
                    '<button type="button" class="wt-btn wt-btn-primary js-footer-icon-upload" data-target-id="' + input_id + '"><?php echo esc_js( __( 'Upload', 'webthinkershop' ) ); ?></button> ' +
                    '<label><?php echo esc_js( __( 'Alt text', 'webthinkershop' ) ); ?></label> ' +
                    '<input type="text" class="wt-option-fields" name="wt_shop_theme_options_all[social_links][' + idx + '][alt]" value="" /> ' +
                    '<button type="button" class="wt-btn wt-btn-secondary js-footer-icon-remove"><?php echo esc_js( __( 'Remove', 'webthinkershop' ) ); ?></button>' +
                    '</div>';
                $container.append(row);
                reindex_footer_icon_rows('social-links-container', 'social_links', true);
            }
            $('#add-social-link').on('click', function(e) {
                e.preventDefault();
                add_social_link_row();
            });

            init_footer_icon_drag_sort('payments-icons-container', 'footer_support_payment_icons', false);
            init_footer_icon_drag_sort('social-links-container', 'social_links', true);

            $('form[action="options.php"]').on('submit', function() {
                reindex_footer_icon_rows('payments-icons-container', 'footer_support_payment_icons', false);
                reindex_footer_icon_rows('social-links-container', 'social_links', true);
            });

            // DESIGN SETTINGS LOGIC
            $('#save_colors_btn').on('click', function() {
                const colors = {
                    action: 'save_design_colors',
                    _wpnonce: '<?php echo wp_create_nonce('save_design_colors'); ?>',
                    primary_color: $('#primary_color').val(),
                    primary_dark: $('#primary_dark').val(),
                    primary_hover: $('#primary_hover').val(),
                    secondary_color: $('#secondary_color').val(),
                    secondary_darker: $('#secondary_darker').val(),
                    black: $('#black').val(),
                    white: $('#white').val(),
                    gray: $('#gray').val(),
                    light_gray: $('#light_gray').val(),
                    tertiary: $('#tertiary').val(),
                    accent_color: $('#accent_color').val(),
                    background_color: $('#background_color').val(),
                };
                $.post(wt_ajax.ajaxurl, colors, function() {
                    show_notification('success', 'Colors Saved', 'Theme colors have been saved and applied successfully!', 3000);
                    setTimeout(() => location.reload(), 2000);
                }).fail(function() {
                    show_notification('error', 'Save Failed', 'There was an error saving the colors. Please try again.');
                });
            });

            $('#save_css_btn').on('click', function() {
                const css = { 
                    action: 'save_custom_css', 
                    _wpnonce: '<?php echo wp_create_nonce('save_custom_css'); ?>',
                    css: $('#custom_css').val() 
                };
                $.post(wt_ajax.ajaxurl, css, function() {
                    show_notification('success', 'CSS Saved', 'Custom CSS has been saved successfully!');
                }).fail(function() {
                    show_notification('error', 'Save Failed', 'There was an error saving the CSS. Please try again.');
                });
            });

            $('#save_js_btn').on('click', function() {
                const js = { 
                    action: 'save_custom_js', 
                    _wpnonce: '<?php echo wp_create_nonce('save_custom_js'); ?>',
                    js: $('#custom_js').val() 
                };
                $.post(wt_ajax.ajaxurl, js, function() {
                    show_notification('success', 'JavaScript Saved', 'Custom JavaScript has been saved successfully!');
                }).fail(function() {
                    show_notification('error', 'Save Failed', 'There was an error saving the JavaScript. Please try again.');
                });
            });

            // CSS and JS functionality
            $('#format_css_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const css = $('#custom_css').val();
                if (css.trim()) {
                    // Simple CSS formatting (basic indentation)
                    const formatted = css
                        .replace(/\{/g, ' {\n    ')
                        .replace(/\}/g, '\n}\n')
                        .replace(/;/g, ';\n    ')
                        .replace(/,\s*/g, ',\n    ');
                    $('#custom_css').val(formatted);
                    show_notification('success', 'CSS Formatted', 'CSS code has been formatted!');
                }
            });

            $('#format_js_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const js = $('#custom_js').val();
                if (js.trim()) {
                    // Simple JS formatting (basic indentation)
                    const formatted = js
                        .replace(/\{/g, ' {\n    ')
                        .replace(/\}/g, '\n}\n')
                        .replace(/;/g, ';\n    ')
                        .replace(/,\s*/g, ',\n    ');
                    $('#custom_js').val(formatted);
                    show_notification('success', 'JS Formatted', 'JavaScript code has been formatted!');
                }
            });

            $('#clear_css_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Are you sure you want to clear all CSS code?')) {
                    $('#custom_css').val('');
                    show_notification('info', 'CSS Cleared', 'CSS code has been cleared!');
                }
            });

            $('#clear_js_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Are you sure you want to clear all JavaScript code?')) {
                    $('#custom_js').val('');
                    show_notification('info', 'JS Cleared', 'JavaScript code has been cleared!');
                }
            });

            // Reset CSS
            $('#reset_css_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Are you sure you want to reset CSS to default?')) {
                    $('#custom_css').val('');
                    show_notification('success', 'CSS Reset', 'CSS has been reset to default!');
                }
            });

            // Reset JS
            $('#reset_js_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Are you sure you want to reset JavaScript to default?')) {
                    $('#custom_js').val('');
                    show_notification('success', 'JS Reset', 'JavaScript has been reset to default!');
                }
            });

            // Legacy reset button (for backward compatibility)
            $('#reset_design_btn').on('click', function() {
                if (confirm('Are you sure you want to reset all design settings?')) {
                    $.post(wt_ajax.ajaxurl, { 
                        action: 'reset_design_defaults',
                        _wpnonce: '<?php echo wp_create_nonce('reset_design_defaults'); ?>'
                    }, function() {
                        show_notification('success', 'Reset Complete', 'All design settings have been reset to defaults!', 3000);
                        setTimeout(() => location.reload(), 2000);
                    }).fail(function() {
                        show_notification('error', 'Reset Failed', 'There was an error resetting the settings. Please try again.');
                    });
                }
            });

            $('#download_css_btn').on('click', function() {
                show_notification('info', 'Download Started', 'Your custom CSS file download has started.');
                window.location.href = wt_ajax.ajaxurl + '?action=download_custom_css';
            });

            $('#download_js_btn').on('click', function() {
                show_notification('info', 'Download Started', 'Your custom JavaScript file download has started.');
                window.location.href = wt_ajax.ajaxurl + '?action=download_custom_js';
            });

            // Live preview for color change with value updates
            $('.wt-color-picker').on('input', function() {
                const color_value = $(this).val();
                const color_id = $(this).attr('id');
                const value_element = $('#' + color_id + '_value');
                
                // Update the displayed color value
                if (value_element.length) {
                    value_element.text(color_value);
                }
                
                // Update webthinkershop colors
                document.documentElement.style.setProperty('--wt-shop-primary', $('#primary_color').val());
                document.documentElement.style.setProperty('--wt-shop-primary-dark', $('#primary_dark').val());
                document.documentElement.style.setProperty('--wt-shop-primary-hover', $('#primary_hover').val());
                document.documentElement.style.setProperty('--wt-shop-secondary', $('#secondary_color').val());
                document.documentElement.style.setProperty('--wt-shop-secondary-darker', $('#secondary_darker').val());
                document.documentElement.style.setProperty('--wt-shop-black', $('#black').val());
                document.documentElement.style.setProperty('--wt-shop-white', $('#white').val());
                document.documentElement.style.setProperty('--wt-shop-gray', $('#gray').val());
                document.documentElement.style.setProperty('--wt-shop-light-gray', $('#light_gray').val());
                document.documentElement.style.setProperty('--wt-shop-tertiary', $('#tertiary').val());
                
                // Update legacy colors
                document.documentElement.style.setProperty('--primary-color', $('#primary_color').val());
                document.documentElement.style.setProperty('--secondary-color', $('#secondary_color').val());
                document.documentElement.style.setProperty('--accent-color', $('#accent_color').val());
                document.documentElement.style.setProperty('--background-color', $('#background_color').val());
                
                // Update gradient
                document.documentElement.style.setProperty('--wt-shop-gradient-90', 
                    `linear-gradient(0deg, ${$('#primary_hover').val()} 0%, ${$('#primary_dark').val()} 50%, ${$('#primary_color').val()} 100%)`);
            });

            // Reset colors button
            $('#reset_colors_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (confirm('Are you sure you want to reset all colors to their default values?')) {
                    // Reset to default values
                    $('#primary_color').val('#091057');
                    $('#primary_dark').val('#05063F');
                    $('#primary_hover').val('#1326A1');
                    $('#secondary_color').val('#FF6900');
                    $('#secondary_darker').val('#CC4B02');
                    $('#black').val('#111111');
                    $('#white').val('#ffffff');
                    $('#gray').val('#737373');
                    $('#light_gray').val('#F7F7F8');
                    $('#tertiary').val('#0077FF');
                    $('#accent_color').val('#0077FF');
                    $('#background_color').val('#ffffff');
                    
                    // Update displayed values
                    $('.wt-color-picker').each(function() {
                        const color_id = $(this).attr('id');
                        const value_element = $('#' + color_id + '_value');
                        if (value_element.length) {
                            value_element.text($(this).val());
                        }
                    });
                    
                    // Trigger live preview update
                    $('.wt-color-picker').trigger('input');
                    
                    show_notification('success', 'Colors Reset', 'All colors have been reset to their default values!');
                }
            });

        });
    </script>

    <?php
}
