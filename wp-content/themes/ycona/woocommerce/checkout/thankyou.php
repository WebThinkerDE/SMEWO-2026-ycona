<?php
/**
 * Thank you page – modern layout (hero + order card + You may also like)
 *
 * Content editable via Theme Options → Thank you page. Multilanguage via options per language.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

$current_lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';
$options      = get_option( 'wt_shop_theme_options_' . $current_lang, array() );
$options_all  = get_option( 'wt_shop_theme_options_all', array() );
if ( ! is_array( $options ) ) {
	$options = array();
}
if ( ! is_array( $options_all ) ) {
	$options_all = array();
}

$thank_you_heading           = ! empty( $options['thank_you_heading'] ) ? $options['thank_you_heading'] : __( 'Thank You!', 'webthinkershop' );
$thank_you_subheading        = ! empty( $options['thank_you_subheading'] ) ? $options['thank_you_subheading'] : __( 'Your order is confirmed.', 'webthinkershop' );
$thank_you_order_message     = ! empty( $options['thank_you_order_message'] ) ? $options['thank_you_order_message'] : __( 'Thank you for your purchase!', 'webthinkershop' );
$thank_you_confirmation_label = ! empty( $options['thank_you_confirmation_label'] ) ? $options['thank_you_confirmation_label'] : __( 'Confirmation sent to: %s', 'webthinkershop' );
$thank_you_delivery_label    = ! empty( $options['thank_you_delivery_label'] ) ? $options['thank_you_delivery_label'] : __( 'Estimated delivery: %s', 'webthinkershop' );
$thank_you_button            = ! empty( $options['thank_you_button_text'] ) ? $options['thank_you_button_text'] : __( 'Continue shopping', 'webthinkershop' );
$thank_you_contact           = ! empty( $options['thank_you_contact_intro'] ) ? $options['thank_you_contact_intro'] : __( 'If you have any issues, contact us.', 'webthinkershop' );
$contact_page_id             = isset( $options_all['thank_you_contact_page_id'] ) ? absint( $options_all['thank_you_contact_page_id'] ) : 0;
$contact_url                 = $contact_page_id ? get_permalink( $contact_page_id ) : '';
$home_url                    = home_url( '/' );
$estimated_delivery          = ! empty( $options['thank_you_estimated_delivery'] ) ? $options['thank_you_estimated_delivery'] : '';
?>

<?php
if ( function_exists( 'wt_shop_checkout_stepper' ) ) {
	wt_shop_checkout_stepper();
}
?>
<div class="woocommerce-order wt-shop-thankyou-wrap">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="wt-shop-thankyou-failed">
				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</p>
			</div>

		<?php else : ?>

			<!-- Hero: headline + checkmark + subheading -->
			<div class="wt-shop-thankyou-hero">
				<h1 class="wt-shop-thankyou-heading"><?php echo esc_html( $thank_you_heading ); ?></h1>
				<p class="wt-shop-thankyou-subheading"><?php echo esc_html( $thank_you_subheading ); ?></p>
				<div class="wt-shop-thankyou-checkmark" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M20 6L9 17l-5-5"/>
					</svg>
				</div>
			</div>

			<!-- Order info card -->
			<div class="wt-shop-thankyou-order-card">
				<p class="wt-shop-thankyou-order-number"><?php echo esc_html( sprintf( __( 'Order #%s', 'webthinkershop' ), $order->get_order_number() ) ); ?></p>
				<p class="wt-shop-thankyou-order-message"><?php echo esc_html( $thank_you_order_message ); ?></p>
				<ul class="wt-shop-thankyou-order-list">
					<li class="wt-shop-thankyou-order-email">
						<?php echo esc_html( sprintf( $thank_you_confirmation_label, $order->get_billing_email() ) ); ?>
					</li>
					<?php if ( $estimated_delivery !== '' ) : ?>
					<li class="wt-shop-thankyou-order-delivery">
						<?php echo esc_html( sprintf( $thank_you_delivery_label, $estimated_delivery ) ); ?>
					</li>
					<?php endif; ?>
				</ul>
				<p class="wt-shop-thankyou-actions">
					<a href="<?php echo esc_url( $home_url ); ?>" class="wt-shop-btn wt-shop-btn-thankyou"><?php echo esc_html( $thank_you_button ); ?></a>
				</p>
				<?php if ( $thank_you_contact || $contact_url ) : ?>
					<p class="wt-shop-thankyou-contact">
						<?php echo esc_html( $thank_you_contact ); ?><?php if ( $thank_you_contact && $contact_url ) { echo ' '; } ?>
						<?php if ( $contact_url ) : ?>
							<a href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Contact us', 'webthinkershop' ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>

			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . ( $order ? $order->get_payment_method() : '' ), $order ? $order->get_id() : 0 ); ?>

	<?php else : ?>

		<div class="wt-shop-thankyou-hero">
			<h1 class="wt-shop-thankyou-heading"><?php echo esc_html( $thank_you_heading ); ?></h1>
			<p class="wt-shop-thankyou-subheading"><?php echo esc_html( $thank_you_subheading ); ?></p>
			<div class="wt-shop-thankyou-checkmark" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 6L9 17l-5-5"/>
				</svg>
			</div>
		</div>
		<div class="wt-shop-thankyou-order-card">
			<p class="wt-shop-thankyou-actions">
				<a href="<?php echo esc_url( $home_url ); ?>" class="wt-shop-btn wt-shop-btn-thankyou"><?php echo esc_html( $thank_you_button ); ?></a>
			</p>
			<?php if ( $thank_you_contact || $contact_url ) : ?>
				<p class="wt-shop-thankyou-contact">
					<?php echo esc_html( $thank_you_contact ); ?><?php if ( $thank_you_contact && $contact_url ) { echo ' '; } ?>
					<?php if ( $contact_url ) : ?>
						<a href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Contact us', 'webthinkershop' ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>

	<?php endif; ?>

</div>
