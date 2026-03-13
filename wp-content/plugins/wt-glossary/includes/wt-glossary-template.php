<?php
/**
 * Template overrides for glossary pages (single + archive).
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Override single + archive template ───────────────────────── */

function wt_glossary_template_include( $template ) {

    if ( is_singular( 'wt_glossary_term' ) ) {
        $plugin_template = WT_GLOSSARY_PATH . 'templates/single-wt_glossary_term.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    if ( is_post_type_archive( 'wt_glossary_term' ) || is_tax( 'wt_glossary_letter' ) || is_tax( 'wt_glossary_category' ) ) {
        $archive_template = WT_GLOSSARY_PATH . 'templates/archive-wt_glossary_term.php';
        if ( file_exists( $archive_template ) ) {
            return $archive_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'wt_glossary_template_include' );
