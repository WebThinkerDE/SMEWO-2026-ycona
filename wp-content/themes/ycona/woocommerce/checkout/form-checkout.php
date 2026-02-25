<?php
/**
 * Checkout Form – WebThinker Shop  theme override
 *
 * Modern two-column checkout: billing/shipping left, order summary right.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo '<div class="woocommerce-info">';
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	echo '</div>';
	return;
}
?>

<div class="wt-shop-checkout-wrap">
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php esc_attr_e( 'Checkout', 'woocommerce' ); ?>">

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<!-- Left column: billing + shipping -->
			<div class="wt-shop-checkout-left">
				<div class="wt-shop-checkout-section">
					<h3 class="wt-shop-checkout-section-title"><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>

				<div class="wt-shop-checkout-section">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			</div>

			<!-- Right column: order review (sticky) -->
			<div class="wt-shop-checkout-right">
				<div class="wt-shop-checkout-order-box">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
					<h3 id="order_review_heading" class="wt-shop-checkout-order-heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>
					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>
			</div>

		<?php endif; ?>

	</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
