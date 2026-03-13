<?php
/**
 * Custom Post Type: wt_glossary_term
 * Meta boxes: Short description, Synonyms, Related Terms.
 *
 * @package WT_Glossary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Register CPT ─────────────────────────────────────────────── */

function wt_glossary_register_cpt() {

    $labels = array(
        'name'                  => __( 'Glossary', 'wt-glossary' ),
        'singular_name'         => __( 'Glossary Term', 'wt-glossary' ),
        'add_new'               => __( 'Add New', 'wt-glossary' ),
        'add_new_item'          => __( 'Add New Term', 'wt-glossary' ),
        'edit_item'             => __( 'Edit Term', 'wt-glossary' ),
        'new_item'              => __( 'New Term', 'wt-glossary' ),
        'view_item'             => __( 'View Term', 'wt-glossary' ),
        'view_items'            => __( 'View Glossary', 'wt-glossary' ),
        'search_items'          => __( 'Search Terms', 'wt-glossary' ),
        'not_found'             => __( 'No terms found.', 'wt-glossary' ),
        'not_found_in_trash'    => __( 'No terms found in Trash.', 'wt-glossary' ),
        'all_items'             => __( 'All Terms', 'wt-glossary' ),
        'menu_name'             => __( 'Glossary', 'wt-glossary' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,   // Gutenberg + WPML
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-book-alt',
        'capability_type'    => 'post',
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'glossary', 'with_front' => false ),
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
        'taxonomies'         => array( 'wt_glossary_letter', 'wt_glossary_category' ),
    );

    register_post_type( 'wt_glossary_term', $args );
}
add_action( 'init', 'wt_glossary_register_cpt' );

/* ── Meta boxes ───────────────────────────────────────────────── */

function wt_glossary_add_meta_boxes() {

    add_meta_box(
        'wt-glossary-fields',
        __( 'Glossary Details', 'wt-glossary' ),
        'wt_glossary_render_meta_box',
        'wt_glossary_term',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wt_glossary_add_meta_boxes' );

/* ── Render meta box ──────────────────────────────────────────── */

function wt_glossary_render_meta_box( $post ) {

    wp_nonce_field( 'wt_glossary_save_meta', 'wt_glossary_meta_nonce' );

    $short_desc    = get_post_meta( $post->ID, '_wt_glossary_short_desc', true );
    $synonyms      = get_post_meta( $post->ID, '_wt_glossary_synonyms', true );
    $related_ids   = get_post_meta( $post->ID, '_wt_glossary_related', true );

    if ( ! is_array( $related_ids ) ) {
        $related_ids = array();
    }

    // Query all glossary terms for the Related Terms selector
    $all_terms = get_posts( array(
        'post_type'      => 'wt_glossary_term',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'exclude'        => array( $post->ID ),
        'suppress_filters' => false,   // Respect WPML
    ));

    ?>
    <table class="form-table" role="presentation">

        <!-- Short Description -->
        <tr>
            <th scope="row">
                <label for="wt-glossary-short-desc"><?php esc_html_e( 'Short Description', 'wt-glossary' ); ?></label>
            </th>
            <td>
                <textarea id="wt-glossary-short-desc"
                          name="wt_glossary_short_desc"
                          rows="3"
                          class="large-text"
                          placeholder="<?php esc_attr_e( 'Brief summary of the term…', 'wt-glossary' ); ?>"
                ><?php echo esc_textarea( $short_desc ); ?></textarea>
            </td>
        </tr>

        <!-- Synonyms -->
        <tr>
            <th scope="row">
                <label for="wt-glossary-synonyms"><?php esc_html_e( 'Synonyms', 'wt-glossary' ); ?></label>
            </th>
            <td>
                <input type="text"
                       id="wt-glossary-synonyms"
                       name="wt_glossary_synonyms"
                       value="<?php echo esc_attr( $synonyms ); ?>"
                       class="large-text"
                       placeholder="<?php esc_attr_e( 'Comma-separated, e.g. Term A, Term B', 'wt-glossary' ); ?>">
            </td>
        </tr>

        <!-- Related Terms -->
        <tr>
            <th scope="row">
                <label for="wt-glossary-related"><?php esc_html_e( 'Related Terms', 'wt-glossary' ); ?></label>
            </th>
            <td>
                <select id="wt-glossary-related"
                        name="wt_glossary_related[]"
                        multiple="multiple"
                        style="width:100%;min-height:120px;">
                    <?php foreach ( $all_terms as $term_post ) : ?>
                        <option value="<?php echo esc_attr( $term_post->ID ); ?>"
                            <?php echo in_array( $term_post->ID, $related_ids ) ? 'selected' : ''; ?>>
                            <?php echo esc_html( $term_post->post_title ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e( 'Hold Ctrl / Cmd to select multiple terms.', 'wt-glossary' ); ?></p>
            </td>
        </tr>

    </table>
    <?php
}

/* ── Save meta ────────────────────────────────────────────────── */

function wt_glossary_save_meta( $post_id ) {

    // Nonce check
    if ( ! isset( $_POST['wt_glossary_meta_nonce'] ) ||
         ! wp_verify_nonce( $_POST['wt_glossary_meta_nonce'], 'wt_glossary_save_meta' ) ) {
        return;
    }

    // Autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Capability
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Only for our CPT
    if ( get_post_type( $post_id ) !== 'wt_glossary_term' ) {
        return;
    }

    // Short description
    if ( isset( $_POST['wt_glossary_short_desc'] ) ) {
        update_post_meta( $post_id, '_wt_glossary_short_desc', sanitize_textarea_field( $_POST['wt_glossary_short_desc'] ) );
    }

    // Synonyms
    if ( isset( $_POST['wt_glossary_synonyms'] ) ) {
        update_post_meta( $post_id, '_wt_glossary_synonyms', sanitize_text_field( $_POST['wt_glossary_synonyms'] ) );
    }

    // Related terms
    if ( isset( $_POST['wt_glossary_related'] ) && is_array( $_POST['wt_glossary_related'] ) ) {
        $related_ids = array_map( 'absint', $_POST['wt_glossary_related'] );
        update_post_meta( $post_id, '_wt_glossary_related', $related_ids );
    } else {
        delete_post_meta( $post_id, '_wt_glossary_related' );
    }

    // Auto-assign letter taxonomy
    wt_glossary_assign_letter( $post_id );
}
add_action( 'save_post', 'wt_glossary_save_meta' );

/* ── Auto-assign letter ───────────────────────────────────────── */

function wt_glossary_assign_letter( $post_id ) {

    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'wt_glossary_term' ) {
        return;
    }

    $title = trim( $post->post_title );
    if ( empty( $title ) ) {
        return;
    }

    // Normalise: get first character, uppercase
    $first_char = mb_strtoupper( mb_substr( $title, 0, 1, 'UTF-8' ), 'UTF-8' );

    // Map umlauts / accented characters to base letter
    $map = array(
        'Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U',
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
        'Ñ' => 'N', 'Ç' => 'C',
    );

    if ( isset( $map[ $first_char ] ) ) {
        $first_char = $map[ $first_char ];
    }

    // If not A-Z, assign '#'
    if ( ! preg_match( '/^[A-Z]$/', $first_char ) ) {
        $first_char = '#';
    }

    $slug = $first_char === '#' ? 'hash' : strtolower( $first_char );

    wp_set_object_terms( $post_id, $slug, 'wt_glossary_letter' );
}

/* ── Admin columns: show short description ────────────────────── */

function wt_glossary_admin_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $label ) {
        $new_columns[ $key ] = $label;
        if ( $key === 'title' ) {
            $new_columns['wt_glossary_short_desc'] = __( 'Short Description', 'wt-glossary' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_wt_glossary_term_posts_columns', 'wt_glossary_admin_columns' );

function wt_glossary_admin_column_content( $column, $post_id ) {
    if ( $column === 'wt_glossary_short_desc' ) {
        $short_desc = get_post_meta( $post_id, '_wt_glossary_short_desc', true );
        echo esc_html( wp_trim_words( $short_desc, 12, '…' ) );
    }
}
add_action( 'manage_wt_glossary_term_posts_custom_column', 'wt_glossary_admin_column_content', 10, 2 );
