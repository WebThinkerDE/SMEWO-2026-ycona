<?php
/**
 * Header language switch trigger.
 *
 * @package ycona
 */

$languages = function_exists( 'icl_get_languages' ) ? icl_get_languages( 'skip_missing=0' ) : array();
$current   = null;
$opts_all  = get_option( 'wt_shop_theme_options_all', array() );
$model     = ( is_array( $opts_all ) && ! empty( $opts_all['language_switch_model'] ) ) ? (string) $opts_all['language_switch_model'] : 'modal';

if ( in_array( $model, array( 'spotlight', 'clean' ), true ) ) {
    $model = 'dropdown';
}
if ( ! in_array( $model, array( 'modal', 'dropdown', 'flags', 'abbr' ), true ) ) {
    $model = 'modal';
}

if ( ! empty( $languages ) && is_array( $languages ) ) {
    $active = array_values(
        array_filter(
            $languages,
            static function( $lang ) {
                return ! empty( $lang['active'] );
            }
        )
    );
    $current = $active[0] ?? reset( $languages );
}

if ( empty( $current ) ) {
    return;
}

$flag_url = ! empty( $current['country_flag_url'] ) ? (string) $current['country_flag_url'] : '';
if ( $flag_url === '' ) {
    return;
}

$lang_code = strtoupper( (string) ( $current['code'] ?? '' ) );

if ( $model === 'modal' ) :
    ?>
    <a class="ms-0 ms-lg-2 language-switch d-none d-lg-flex align-items-center gap-2"
       data-bs-toggle="modal"
       data-bs-target="#lang-currency-modal"
       data-start-tab="#tab-lang">
        <img src="<?php echo esc_url( $flag_url ); ?>" alt="" width="20" height="20">
    </a>
<?php
else :
    $root_id = 'wt-lang-' . $model;
    ?>
    <div class="ms-0 ms-lg-2 d-none d-lg-flex wt-lang-dd wt-lang-dd--<?php echo esc_attr( $model ); ?>" id="<?php echo esc_attr( $root_id ); ?>">
        <button type="button" class="wt-lang-dd__trigger" aria-haspopup="true" aria-expanded="false" aria-label="<?php esc_attr_e( 'Change language', 'ycona' ); ?>">
            <?php if ( $model !== 'abbr' ) : ?>
                <img src="<?php echo esc_url( $flag_url ); ?>" alt="" width="20" height="20">
            <?php endif; ?>
            <?php if ( $model === 'dropdown' ) : ?>
                <span><?php echo esc_html( $lang_code ); ?></span>
            <?php elseif ( $model === 'abbr' ) : ?>
                <strong><?php echo esc_html( $lang_code ); ?></strong>
            <?php endif; ?>
            <i class="bi bi-chevron-down" aria-hidden="true"></i>
        </button>
        <div class="wt-lang-dd__menu" role="menu" aria-label="<?php esc_attr_e( 'Languages', 'ycona' ); ?>">
            <?php foreach ( $languages as $lang ) :
                $is_active = ! empty( $lang['active'] );
                if ( $is_active ) {
                    continue;
                }
                $href      = $is_active ? '#' : ( $lang['url'] ?? '#' );
                $name      = $lang['native_name'] ?? ( $lang['translated_name'] ?? strtoupper( (string) ( $lang['code'] ?? '' ) ) );
                $code      = strtoupper( (string) ( $lang['code'] ?? '' ) );
                $lang_flag = $lang['country_flag_url'] ?? '';
                ?>
                <a href="<?php echo esc_url( $href ); ?>"
                   class="wt-lang-dd__item">
                    <?php if ( $model === 'flags' ) : ?>
                        <?php if ( ! empty( $lang_flag ) ) : ?>
                            <img src="<?php echo esc_url( $lang_flag ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="22" height="22">
                        <?php endif; ?>
                    <?php elseif ( $model === 'abbr' ) : ?>
                        <span class="wt-lang-dd__abbr"><?php echo esc_html( $code ); ?></span>
                    <?php else : ?>
                        <?php if ( ! empty( $lang_flag ) ) : ?>
                            <img src="<?php echo esc_url( $lang_flag ); ?>" alt="" width="18" height="18">
                        <?php endif; ?>
                        <span><?php echo esc_html( $name ); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
