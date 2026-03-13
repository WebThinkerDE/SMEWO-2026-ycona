<?php
/**
 * Settings page for WebThinker Glossary.
 *
 * WP Admin → Glossary → Settings
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Option key ───────────────────────────────────────────────── */

define( 'WT_GLOSSARY_OPTION', 'wt_glossary_settings' );

/* ── Defaults ─────────────────────────────────────────────────── */

function wt_glossary_get_defaults() {
    return array(
        // Colors
        'color_primary'       => '#2563eb',
        'color_primary_hover' => '#1d4ed8',
        'color_primary_light' => '#dbeafe',
        'color_dark'          => '#1e293b',
        'color_text'          => '#334155',
        'color_text_light'    => '#64748b',
        'color_border'        => '#e2e8f0',
        'color_bg'            => '#ffffff',
        'color_bg_subtle'     => '#f8fafc',
        'color_card_gradient' => '#7c3aed',

        // Typography
        'border_radius'       => '12',
        'border_radius_sm'    => '8',

        // Archive defaults
        'archive_title'       => 'Glossary',
        'archive_description' => '',
        'archive_search_label'=> 'What word are you interested in?',
        'archive_columns'     => '2',
        'archive_read_more'   => 'Know More',

        // Features
        'enable_auto_link'    => 'yes',
        'enable_tooltips'     => 'yes',
        'recent_search_limit' => '5',
    );
}

function wt_glossary_get_settings() {
    $saved = get_option( WT_GLOSSARY_OPTION, array() );
    return wp_parse_args( $saved, wt_glossary_get_defaults() );
}

/* ── Admin menu entry ─────────────────────────────────────────── */

function wt_glossary_settings_menu() {
    add_submenu_page(
        'edit.php?post_type=wt_glossary_term',
        __( 'Glossary Settings', 'wt-glossary' ),
        __( 'Settings', 'wt-glossary' ),
        'manage_options',
        'wt-glossary-settings',
        'wt_glossary_settings_page'
    );
}
add_action( 'admin_menu', 'wt_glossary_settings_menu' );

/* ── Register settings ────────────────────────────────────────── */

function wt_glossary_register_settings() {
    register_setting( 'wt_glossary_settings_group', WT_GLOSSARY_OPTION, array(
        'type'              => 'array',
        'sanitize_callback' => 'wt_glossary_sanitize_settings',
    ));
}
add_action( 'admin_init', 'wt_glossary_register_settings' );

/* ── Sanitize ─────────────────────────────────────────────────── */

function wt_glossary_sanitize_settings( $input ) {
    $defaults  = wt_glossary_get_defaults();
    $sanitized = array();

    $color_keys = array(
        'color_primary', 'color_primary_hover', 'color_primary_light',
        'color_dark', 'color_text', 'color_text_light',
        'color_border', 'color_bg', 'color_bg_subtle', 'color_card_gradient',
    );

    foreach ( $color_keys as $key ) {
        $sanitized[ $key ] = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : $defaults[ $key ];
        if ( empty( $sanitized[ $key ] ) ) {
            $sanitized[ $key ] = $defaults[ $key ];
        }
    }

    $sanitized['border_radius']    = isset( $input['border_radius'] ) ? absint( $input['border_radius'] ) : $defaults['border_radius'];
    $sanitized['border_radius_sm'] = isset( $input['border_radius_sm'] ) ? absint( $input['border_radius_sm'] ) : $defaults['border_radius_sm'];

    $sanitized['archive_title']        = isset( $input['archive_title'] ) ? sanitize_text_field( $input['archive_title'] ) : $defaults['archive_title'];
    $sanitized['archive_description']  = isset( $input['archive_description'] ) ? wp_kses_post( $input['archive_description'] ) : '';
    $sanitized['archive_search_label'] = isset( $input['archive_search_label'] ) ? sanitize_text_field( $input['archive_search_label'] ) : $defaults['archive_search_label'];
    $sanitized['archive_read_more']    = isset( $input['archive_read_more'] ) ? sanitize_text_field( $input['archive_read_more'] ) : $defaults['archive_read_more'];

    $valid_cols = array( '1', '2', '3', '4' );
    $sanitized['archive_columns'] = ( isset( $input['archive_columns'] ) && in_array( $input['archive_columns'], $valid_cols, true ) )
        ? $input['archive_columns']
        : $defaults['archive_columns'];

    $sanitized['enable_auto_link'] = ( isset( $input['enable_auto_link'] ) && $input['enable_auto_link'] === 'yes' ) ? 'yes' : 'no';
    $sanitized['enable_tooltips']  = ( isset( $input['enable_tooltips'] ) && $input['enable_tooltips'] === 'yes' ) ? 'yes' : 'no';

    $limit = isset( $input['recent_search_limit'] ) ? absint( $input['recent_search_limit'] ) : 5;
    $sanitized['recent_search_limit'] = max( 0, min( 20, $limit ) );

    return $sanitized;
}

/* ── Settings page ────────────────────────────────────────────── */

function wt_glossary_settings_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    $settings = wt_glossary_get_settings();
    $defaults = wt_glossary_get_defaults();
    ?>
    <div class="wrap wt-glossary-settings-wrap">
        <h1><?php esc_html_e( 'Glossary Settings', 'wt-glossary' ); ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'wt_glossary_settings_group' ); ?>

            <!-- ─── Tabs ─────────────────────────────────── -->
            <nav class="nav-tab-wrapper wt-glossary-tabs">
                <a href="#wt-tab-colors" class="nav-tab nav-tab-active" data-tab="wt-tab-colors">
                    <?php esc_html_e( 'Colors', 'wt-glossary' ); ?>
                </a>
                <a href="#wt-tab-general" class="nav-tab" data-tab="wt-tab-general">
                    <?php esc_html_e( 'General', 'wt-glossary' ); ?>
                </a>
                <a href="#wt-tab-archive" class="nav-tab" data-tab="wt-tab-archive">
                    <?php esc_html_e( 'Archive Page', 'wt-glossary' ); ?>
                </a>
                <a href="#wt-tab-features" class="nav-tab" data-tab="wt-tab-features">
                    <?php esc_html_e( 'Features', 'wt-glossary' ); ?>
                </a>
            </nav>

            <!-- ─── Colors ───────────────────────────────── -->
            <div id="wt-tab-colors" class="wt-glossary-tab-panel wt-glossary-tab-panel-active">
                <table class="form-table" role="presentation">
                    <?php
                    $color_fields = array(
                        'color_primary'       => __( 'Primary Color', 'wt-glossary' ),
                        'color_primary_hover' => __( 'Primary Hover', 'wt-glossary' ),
                        'color_primary_light' => __( 'Primary Light (backgrounds)', 'wt-glossary' ),
                        'color_card_gradient' => __( 'Card Accent / Gradient End', 'wt-glossary' ),
                        'color_dark'          => __( 'Dark (headings)', 'wt-glossary' ),
                        'color_text'          => __( 'Text Color', 'wt-glossary' ),
                        'color_text_light'    => __( 'Text Light (secondary)', 'wt-glossary' ),
                        'color_border'        => __( 'Border Color', 'wt-glossary' ),
                        'color_bg'            => __( 'Background', 'wt-glossary' ),
                        'color_bg_subtle'     => __( 'Background Subtle', 'wt-glossary' ),
                    );

                    foreach ( $color_fields as $key => $label ) :
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="wt_glossary_<?php echo esc_attr( $key ); ?>"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[<?php echo esc_attr( $key ); ?>]"
                                   value="<?php echo esc_attr( $settings[ $key ] ); ?>"
                                   class="wt-glossary-color-picker"
                                   data-default-color="<?php echo esc_attr( $defaults[ $key ] ); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- ─── General ──────────────────────────────── -->
            <div id="wt-tab-general" class="wt-glossary-tab-panel" style="display:none;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_border_radius"><?php esc_html_e( 'Border Radius (px)', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="wt_glossary_border_radius"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[border_radius]"
                                   value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
                                   min="0" max="50" step="1" class="small-text">
                            <p class="description"><?php esc_html_e( 'Cards, search bar, navigation buttons.', 'wt-glossary' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_border_radius_sm"><?php esc_html_e( 'Small Border Radius (px)', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="wt_glossary_border_radius_sm"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[border_radius_sm]"
                                   value="<?php echo esc_attr( $settings['border_radius_sm'] ); ?>"
                                   min="0" max="30" step="1" class="small-text">
                            <p class="description"><?php esc_html_e( 'Nav buttons, tooltips, badges.', 'wt-glossary' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_recent_search_limit"><?php esc_html_e( 'Top Searched Limit', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="wt_glossary_recent_search_limit"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[recent_search_limit]"
                                   value="<?php echo esc_attr( $settings['recent_search_limit'] ); ?>"
                                   min="0" max="20" step="1" class="small-text">
                            <p class="description"><?php esc_html_e( 'Max items shown in "Top searched" tags. Set 0 to disable.', 'wt-glossary' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ─── Archive Page ─────────────────────────── -->
            <div id="wt-tab-archive" class="wt-glossary-tab-panel" style="display:none;">
                <p class="description" style="margin-bottom:1rem;">
                    <?php esc_html_e( 'These defaults apply to the /glossary/ archive page. The Gutenberg block has its own per-block settings.', 'wt-glossary' ); ?>
                </p>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_archive_title"><?php esc_html_e( 'Title', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wt_glossary_archive_title"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[archive_title]"
                                   value="<?php echo esc_attr( $settings['archive_title'] ); ?>"
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_archive_description"><?php esc_html_e( 'Description', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <textarea id="wt_glossary_archive_description"
                                      name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[archive_description]"
                                      rows="3"
                                      class="large-text"><?php echo esc_textarea( $settings['archive_description'] ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Shows below the title on the archive page.', 'wt-glossary' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_archive_search_label"><?php esc_html_e( 'Search Label', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wt_glossary_archive_search_label"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[archive_search_label]"
                                   value="<?php echo esc_attr( $settings['archive_search_label'] ); ?>"
                                   class="regular-text">
                            <p class="description"><?php esc_html_e( 'Text above the search bar, e.g. "What word are you interested in?"', 'wt-glossary' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_archive_read_more"><?php esc_html_e( 'Read More Text', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wt_glossary_archive_read_more"
                                   name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[archive_read_more]"
                                   value="<?php echo esc_attr( $settings['archive_read_more'] ); ?>"
                                   class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wt_glossary_archive_columns"><?php esc_html_e( 'Columns', 'wt-glossary' ); ?></label>
                        </th>
                        <td>
                            <select id="wt_glossary_archive_columns"
                                    name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[archive_columns]">
                                <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                                    <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $settings['archive_columns'], (string) $i ); ?>>
                                        <?php printf( esc_html__( '%d Column(s)', 'wt-glossary' ), $i ); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ─── Features ─────────────────────────────── -->
            <div id="wt-tab-features" class="wt-glossary-tab-panel" style="display:none;">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Auto-link Terms', 'wt-glossary' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[enable_auto_link]"
                                       value="yes"
                                       <?php checked( $settings['enable_auto_link'], 'yes' ); ?>>
                                <?php esc_html_e( 'Automatically link glossary terms found in post/page content.', 'wt-glossary' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Tooltips', 'wt-glossary' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="<?php echo esc_attr( WT_GLOSSARY_OPTION ); ?>[enable_tooltips]"
                                       value="yes"
                                       <?php checked( $settings['enable_tooltips'], 'yes' ); ?>>
                                <?php esc_html_e( 'Show tooltip with short description on hover over auto-linked terms.', 'wt-glossary' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>

            <p>
                <button type="button" id="wt-glossary-reset-colors" class="button button-link-delete">
                    <?php esc_html_e( 'Reset All Colors to Defaults', 'wt-glossary' ); ?>
                </button>
            </p>
        </form>
    </div>

    <style>
        .wt-glossary-settings-wrap { max-width: 860px; }
        .wt-glossary-tabs { margin-bottom: 0; }
        .wt-glossary-tab-panel { background: #fff; border: 1px solid #c3c4c7; border-top: none; padding: 1rem 1.5rem; }
        .wt-glossary-tab-panel .form-table th { width: 220px; padding-left: 0.5rem; }
        .wt-glossary-color-picker { width: 80px; }
    </style>

    <script>
    (function(){
        var tabs = document.querySelectorAll('.wt-glossary-tabs .nav-tab');
        var panels = document.querySelectorAll('.wt-glossary-tab-panel');

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                var target = tab.getAttribute('data-tab');
                tabs.forEach(function(t) { t.classList.remove('nav-tab-active'); });
                panels.forEach(function(p) { p.style.display = 'none'; });
                tab.classList.add('nav-tab-active');
                var panel = document.getElementById(target);
                if (panel) { panel.style.display = ''; }
            });
        });

        var resetBtn = document.getElementById('wt-glossary-reset-colors');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                var pickers = document.querySelectorAll('.wt-glossary-color-picker');
                pickers.forEach(function(input) {
                    var def = input.getAttribute('data-default-color');
                    if (def) {
                        input.value = def;
                        jQuery(input).wpColorPicker('color', def);
                    }
                });
            });
        }
    })();
    </script>
    <?php
}

/* ── Enqueue color picker on settings page ────────────────────── */

function wt_glossary_settings_enqueue( $hook ) {
    if ( $hook !== 'wt_glossary_term_page_wt-glossary-settings' ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_add_inline_script( 'wp-color-picker', '
        jQuery(document).ready(function($){
            $(".wt-glossary-color-picker").wpColorPicker();
        });
    ');
}
add_action( 'admin_enqueue_scripts', 'wt_glossary_settings_enqueue' );

/* ── Output custom CSS variables on frontend ──────────────────── */

function wt_glossary_custom_css_variables() {

    $settings = wt_glossary_get_settings();
    $defaults = wt_glossary_get_defaults();

    $overrides = array();

    $map = array(
        'color_primary'       => '--wt-glossary-primary',
        'color_primary_hover' => '--wt-glossary-primary-hover',
        'color_primary_light' => '--wt-glossary-primary-light',
        'color_dark'          => '--wt-glossary-dark',
        'color_text'          => '--wt-glossary-text',
        'color_text_light'    => '--wt-glossary-text-light',
        'color_border'        => '--wt-glossary-border',
        'color_bg'            => '--wt-glossary-bg',
        'color_bg_subtle'     => '--wt-glossary-bg-subtle',
    );

    foreach ( $map as $key => $var ) {
        if ( ! empty( $settings[ $key ] ) && $settings[ $key ] !== $defaults[ $key ] ) {
            $overrides[] = $var . ':' . esc_attr( $settings[ $key ] );
        }
    }

    if ( ! empty( $settings['border_radius'] ) && $settings['border_radius'] !== $defaults['border_radius'] ) {
        $overrides[] = '--wt-glossary-radius:' . absint( $settings['border_radius'] ) . 'px';
    }
    if ( ! empty( $settings['border_radius_sm'] ) && $settings['border_radius_sm'] !== $defaults['border_radius_sm'] ) {
        $overrides[] = '--wt-glossary-radius-sm:' . absint( $settings['border_radius_sm'] ) . 'px';
    }

    if ( empty( $overrides ) ) {
        return;
    }

    echo '<style id="wt-glossary-custom-vars">:root{' . implode( ';', $overrides ) . '}</style>' . "\n";
}
add_action( 'wp_head', 'wt_glossary_custom_css_variables', 20 );

/* ── Plugin action link to settings ───────────────────────────── */

function wt_glossary_plugin_action_links( $links ) {
    $settings_url = admin_url( 'edit.php?post_type=wt_glossary_term&page=wt-glossary-settings' );
    $settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'wt-glossary' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . WT_GLOSSARY_BASENAME, 'wt_glossary_plugin_action_links' );
