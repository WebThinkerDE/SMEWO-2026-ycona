<?php
/**
 * Registration form only (for webthinkershop shortcode).
 * Override: copy from WooCommerce form-login.php – register section only.
 *
 * @package WordPress
 * @subpackage webthinkershop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) {
	echo '<p>' . esc_html__( 'Registration is currently disabled.', 'webthinkershop' ) . '</p>';
	return;
}

do_action( 'woocommerce_before_customer_register_form' );
?>

<div class="wt-shop-modal-form woocommerce-form-register-wrap">
	<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
		<?php do_action( 'woocommerce_register_form_start' ); ?>

		<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
			<label for="reg_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="first_name" id="reg_first_name" autocomplete="given-name" value="<?php echo ( ! empty( $_POST['first_name'] ) ) ? esc_attr( wp_unslash( $_POST['first_name'] ) ) : ''; ?>" required aria-required="true" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
			<label for="reg_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="last_name" id="reg_last_name" autocomplete="family-name" value="<?php echo ( ! empty( $_POST['last_name'] ) ) ? esc_attr( wp_unslash( $_POST['last_name'] ) ) : ''; ?>" required aria-required="true" />
		</p>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
			</p>
		<?php endif; ?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
		</p>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<span class="password-input">
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
					<button type="button" class="show-password-input" aria-label="<?php esc_attr_e( 'Show password', 'webthinkershop' ); ?>" aria-describedby="reg_password"></button>
				</span>
				<div class="wt-shop-password-strength mt-2" id="wt-shop-password-strength" aria-live="polite" aria-atomic="true" role="status">
					<div class="wt-shop-password-strength-bar" aria-hidden="true">
						<span class="wt-shop-password-strength-fill" id="wt-shop-password-strength-fill"></span>
					</div>
					<span class="wt-shop-password-strength-text" id="wt-shop-password-strength-text"></span>
				</div>
			</p>
		<?php else : ?>
			<p class="woocommerce-form-row form-row-wide"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
		<?php endif; ?>

		<?php do_action( 'woocommerce_register_form' ); ?>

		<p class="woocommerce-form-row form-row">
			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<button type="submit" class="woocommerce-Button button btn-full btn-full-primary" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
		</p>

		<?php do_action( 'woocommerce_register_form_end' ); ?>
	</form>
</div>

<?php do_action( 'woocommerce_after_customer_register_form' ); ?>
