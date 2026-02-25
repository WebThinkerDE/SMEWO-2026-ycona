<?php

/**
 * AJAX login from modal – keep modal open and show error under button on failure.
 */
function wt_shop_ajax_modal_login() {
    check_ajax_referer( 'woocommerce-login', 'woocommerce-login-nonce' );
    $username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
    $password = isset( $_POST['password'] ) ? $_POST['password'] : '';
    $redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';

    if ( empty( $username ) || empty( $password ) ) {
        wp_send_json_error( array( 'message' => __( 'Username and password are required.', 'webthinkershop' ) ) );
    }

    $user = wp_signon(
        array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => ! empty( $_POST['remember'] ),
        ),
        is_ssl()
    );

    if ( is_wp_error( $user ) ) {
        $message = $user->get_error_message();
        if ( empty( $message ) ) {
            $message = __( 'Invalid username or password.', 'webthinkershop' );
        }
        wp_send_json_error( array( 'message' => $message ) );
    }

    if ( empty( $redirect ) && function_exists( 'wc_get_page_permalink' ) ) {
        $redirect = wc_get_page_permalink( 'myaccount' );
    }
    if ( empty( $redirect ) ) {
        $redirect = home_url( '/' );
    }
    wp_send_json_success( array( 'redirect' => $redirect ) );
}
add_action( 'wp_ajax_wt_shop_modal_login', 'wt_shop_ajax_modal_login' );
add_action( 'wp_ajax_nopriv_wt_shop_modal_login', 'wt_shop_ajax_modal_login' );

/**
 * Verify reCAPTCHA v2 response with Google (when secret key is set in Theme Options).
 *
 * @param string $response_token g-recaptcha-response from POST.
 * @param string $secret_key     reCAPTCHA secret key.
 * @return bool True if valid, false otherwise.
 */
function wt_shop_verify_recaptcha( $response_token, $secret_key ) {
    if ( $secret_key === '' || $response_token === '' ) {
        return false;
    }
    $resp = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'body' => array(
                'secret'   => $secret_key,
                'response' => $response_token,
                'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
            ),
            'timeout' => 15,
        )
    );
    if ( is_wp_error( $resp ) ) {
        return false;
    }
    $body = wp_remote_retrieve_body( $resp );
    $data = json_decode( $body, true );
    return ! empty( $data['success'] );
}

/**
 * AJAX registration from modal – keep modal open and show error under button on failure.
 */
function wt_shop_ajax_modal_register() {
    check_ajax_referer( 'woocommerce-register', 'woocommerce-register-nonce' );

    if ( ! function_exists( 'WC' ) ) {
        wp_send_json_error( array( 'message' => __( 'Registration is not available.', 'webthinkershop' ) ) );
    }
    if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) {
        wp_send_json_error( array( 'message' => __( 'Registration is currently disabled.', 'webthinkershop' ) ) );
    }

    $options_all   = get_option( 'wt_shop_theme_options_all', array() );
    $recaptcha_sec = isset( $options_all['recaptcha_secret_key'] ) ? trim( (string) $options_all['recaptcha_secret_key'] ) : '';
    if ( $recaptcha_sec !== '' ) {
        $recaptcha_response = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
        if ( ! wt_shop_verify_recaptcha( $recaptcha_response, $recaptcha_sec ) ) {
            wp_send_json_error( array( 'message' => __( 'Please complete the reCAPTCHA verification.', 'webthinkershop' ) ) );
        }
    }

    $username = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ), true ) : '';
    $email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $password = isset( $_POST['password'] ) ? $_POST['password'] : '';

    $generate_username = 'yes' === get_option( 'woocommerce_registration_generate_username' );
    $generate_password = 'yes' === get_option( 'woocommerce_registration_generate_password' );

    if ( ! $generate_username && empty( $username ) ) {
        wp_send_json_error( array( 'message' => __( 'Username is required.', 'webthinkershop' ) ) );
    }
    if ( empty( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Email is required.', 'webthinkershop' ) ) );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a valid email address (e.g. name@example.com).', 'webthinkershop' ) ) );
    }
    if ( ! $generate_password && empty( $password ) ) {
        wp_send_json_error( array( 'message' => __( 'Password is required.', 'webthinkershop' ) ) );
    }

    $validation_error = new WP_Error();
    $validation_error = apply_filters( 'woocommerce_registration_errors', $validation_error, $username, $email );
    if ( $validation_error->has_errors() ) {
        $message = implode( ' ', $validation_error->get_error_messages() );
        wp_send_json_error( array( 'message' => $message ) );
    }

    if ( ! $generate_username && username_exists( $username ) ) {
        wp_send_json_error( array( 'message' => __( 'An account is already registered with that username.', 'woocommerce' ) ) );
    }
    if ( email_exists( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'An account is already registered with your email address.', 'woocommerce' ) ) );
    }

    $customer_id = wc_create_new_customer( $email, $username, $password );
    if ( is_wp_error( $customer_id ) ) {
        wp_send_json_error( array( 'message' => $customer_id->get_error_message() ) );
    }
    // first_name / last_name saved via woocommerce_created_customer (wt_shop_save_registration_first_last_name)

    if ( ! $generate_password && $customer_id ) {
        wp_set_current_user( $customer_id );
        wc_set_customer_auth_cookie( $customer_id );
    }

    $redirect = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
    wp_send_json_success( array( 'redirect' => $redirect, 'message' => __( 'Registration complete. Redirecting…', 'webthinkershop' ) ) );
}
add_action( 'wp_ajax_nopriv_wt_shop_modal_register', 'wt_shop_ajax_modal_register' );
