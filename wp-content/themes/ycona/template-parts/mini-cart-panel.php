<?php
/**
 * Mini cart panel (aside) – used in header.
 * Requires WooCommerce. Cart content updated via fragments.
 *
 * @package WordPress
 * @subpackage ycona
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'WC' ) || ! function_exists( 'wc_get_cart_url' ) ) {
	return;
}
$mini_cart_model = function_exists( 'wt_shop_mini_cart_model' ) ? wt_shop_mini_cart_model() : 'panel';
$mini_cart_class = $mini_cart_model === 'dropdown' ? 'wt-mini-cart-panel wt-mini-cart-panel--dropdown' : 'wt-mini-cart-panel';
?>
<aside id="wt-mini-cart-panel" class="<?php echo esc_attr( $mini_cart_class ); ?>" aria-hidden="true" data-open="false" data-model="<?php echo esc_attr( $mini_cart_model ); ?>">
	<div class="wt-mini-cart-head">
		<strong><?php esc_html_e( 'Cart', 'webthinkershop' ); ?></strong>
		<button id="wt-mini-cart-close" type="button" class="wt-mini-cart-close" aria-label="<?php esc_attr_e( 'Close', 'webthinkershop' ); ?>">×</button>
	</div>
	<div id="wt-mini-cart-content" class="wt-mini-cart-content">
		<?php echo wt_shop_mini_cart_content_html(); ?>
	</div>
</aside>
