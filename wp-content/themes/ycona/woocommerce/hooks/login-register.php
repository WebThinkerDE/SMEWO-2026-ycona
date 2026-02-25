<?php
/**
 * Shortcode: [wc_login_form_wt_shop] – WooCommerce login form.
 * Wraps login password field in .password-input and adds show/hide button.
 */
function wt_shop_shortcode_wc_login_form( $atts ) {
    if ( function_exists( 'woocommerce_login_form' ) ) {
        ob_start();
        woocommerce_login_form();
        $html = ob_get_clean();
        $html = wt_shop_wrap_password_field_with_toggle( $html, 'password', 'password' );
        return $html;
    }
    ob_start();
    wp_login_form( array( 'echo' => false ) );
    $html = ob_get_clean();
    $html = wt_shop_wrap_password_field_with_toggle( $html, 'pwd', 'user_pass' );
    return $html;
}
add_shortcode( 'wc_login_form_wt_shop', 'wt_shop_shortcode_wc_login_form' );

/**
 * Wrap a password input in .password-input and add a single show/hide button (for login/other forms).
 * Removes any existing show-password / display-password buttons first so only one remains.
 *
 * @param string $html       Form HTML.
 * @param string $id_value   Value of id attribute to find (e.g. 'password' or 'pwd').
 * @param string $name_value Value of name attribute to find (e.g. 'password' or 'user_pass').
 * @return string Modified HTML.
 */
function wt_shop_wrap_password_field_with_toggle( $html, $id_value, $name_value ) {
    $id_quoted   = preg_quote( $id_value, '/' );
    $name_quoted = preg_quote( $name_value, '/' );

    // Remove any existing show-password / display-password buttons (WooCommerce may add one) to avoid duplicates.
    // Match class with either single or double quotes.
    $html = preg_replace( '/<button\s[^>]*\bclass=["\'][^"\']*show-password-input[^"\']*["\'][^>]*>\s*<\/button>/i', '', $html );
    $html = preg_replace( '/<button\s[^>]*\bclass=["\'][^"\']*display-password[^"\']*["\'][^>]*>\s*<\/button>/i', '', $html );

    $pattern = '/<input\s(?=[^>]*\bid=["\']' . $id_quoted . '["\'])(?=[^>]*\bname=["\']' . $name_quoted . '["\'])[^>]*>/i';
    if ( ! preg_match( $pattern, $html ) ) {
        return $html;
    }
    $replacement = '<span class="password-input">$0<button type="button" class="show-password-input" aria-label="' . esc_attr__( 'Show password', 'webthinkershop' ) . '" aria-describedby="' . esc_attr( $id_value ) . '"></button></span>';
    $html        = preg_replace( $pattern, $replacement, $html, 1 );
    return $html;
}

/**
 * Shortcode: [wt_shop_template_wc_registration] – WooCommerce registration form only (no login).
 * Ensures only one show-password button (removes duplicates added by WooCommerce hook).
 */
function wt_shop_shortcode_wc_registration( $atts ) {
    if ( ! function_exists( 'WC' ) ) {
        return '<p>' . esc_html__( 'WooCommerce is required for registration.', 'webthinkershop' ) . '</p>';
    }
    if ( is_user_logged_in() ) {
        return '<p>' . esc_html__( 'You are already logged in.', 'webthinkershop' ) . '</p>';
    }
    ob_start();
    $template = get_stylesheet_directory() . '/woocommerce/myaccount/form-register-only.php';
    if ( file_exists( $template ) ) {
        include $template;
    } else {
        $_GET['action'] = 'register';
        wc_get_template( 'myaccount/form-login.php' );
    }
    $html = ob_get_clean();
    $html = wt_shop_remove_duplicate_password_buttons( $html );
    return $html;
}
add_shortcode( 'wt_shop_template_wc_registration', 'wt_shop_shortcode_wc_registration' );

/**
 * Output reCAPTCHA v2 checkbox on registration form when site key is set (Theme Options → Other Settings).
 */
function wt_shop_register_form_recaptcha() {
	$options_all = get_option( 'wt_shop_theme_options_all', array() );
	$site_key    = isset( $options_all['recaptcha_site_key'] ) ? trim( (string) $options_all['recaptcha_site_key'] ) : '';
	if ( $site_key === '' ) {
		return;
	}
	echo '<div class="wt-shop-recaptcha-wrap form-row form-row-wide" style="margin-bottom:1rem;">';
	echo '<div class="g-recaptcha" data-sitekey="' . esc_attr( $site_key ) . '" data-theme="light" aria-label="' . esc_attr__( 'reCAPTCHA', 'webthinkershop' ) . '"></div>';
	echo '</div>';
}
add_action( 'woocommerce_register_form', 'wt_shop_register_form_recaptcha', 20 );

/**
 * Remove duplicate show-password / display-password buttons inside .password-input (keep only the first).
 *
 * @param string $html HTML that may contain .password-input with multiple buttons.
 * @return string Modified HTML.
 */
function wt_shop_remove_duplicate_password_buttons( $html ) {
    // Match span whose class contains password-input (double or single quotes).
    $pattern = '/(<span\s+class=["\'][^"\']*password-input[^"\']*["\'][^>]*>)(.*?)(<\/span>)/is';
    return preg_replace_callback( $pattern, function ( $m ) {
        $inner = $m[2];
        $first = true;
        // Match button class with either single or double quotes.
        $inner = preg_replace_callback( '/<button\s[^>]*\bclass=["\'][^"\']*(?:show-password-input|display-password)[^"\']*["\'][^>]*>\s*<\/button>/i', function ( $btn ) use ( &$first ) {
            if ( $first ) {
                $first = false;
                return $btn[0];
            }
            return '';
        }, $inner );
        return $m[1] . $inner . $m[3];
    }, $html );
}

/**
 * Save first name and last name on WooCommerce registration (form-register-only and standard).
 */
function wt_shop_save_registration_first_last_name( $customer_id ) {
    if ( ! empty( $_POST['first_name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) );
    }
    if ( ! empty( $_POST['last_name'] ) ) {
        update_user_meta( $customer_id, 'last_name', sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) );
    }
}
add_action( 'woocommerce_created_customer', 'wt_shop_save_registration_first_last_name' );

/**
 * Validate first name and last name on WooCommerce registration when form includes them.
 */
function wt_shop_validate_registration_first_last_name( $validation_error, $username, $email ) {
    if ( ! isset( $_POST['first_name'] ) && ! isset( $_POST['last_name'] ) ) {
        return $validation_error;
    }
    if ( empty( $_POST['first_name'] ) || ! is_string( $_POST['first_name'] ) || trim( wp_unslash( $_POST['first_name'] ) ) === '' ) {
        $validation_error->add( 'first_name_required', __( 'First name is required.', 'webthinkershop' ) );
    }
    if ( empty( $_POST['last_name'] ) || ! is_string( $_POST['last_name'] ) || trim( wp_unslash( $_POST['last_name'] ) ) === '' ) {
        $validation_error->add( 'last_name_required', __( 'Last name is required.', 'webthinkershop' ) );
    }
    return $validation_error;
}
add_filter( 'woocommerce_registration_errors', 'wt_shop_validate_registration_first_last_name', 10, 3 );

/**
 * Add First name and Last name to WooCommerce My Account register form (not in modal – modal has them in template).
 */
function wt_shop_add_register_first_last_name_myaccount() {
    if ( ! is_account_page() ) {
        return;
    }
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
        <label for="reg_first_name_myaccount"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="first_name" id="reg_first_name_myaccount" autocomplete="given-name" value="<?php echo ( ! empty( $_POST['first_name'] ) ) ? esc_attr( wp_unslash( $_POST['first_name'] ) ) : ''; ?>" required aria-required="true" />
    </p>
    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
        <label for="reg_last_name_myaccount"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="last_name" id="reg_last_name_myaccount" autocomplete="family-name" value="<?php echo ( ! empty( $_POST['last_name'] ) ) ? esc_attr( wp_unslash( $_POST['last_name'] ) ) : ''; ?>" required aria-required="true" />
    </p>
    <?php
}
add_action( 'woocommerce_register_form_start', 'wt_shop_add_register_first_last_name_myaccount' );
