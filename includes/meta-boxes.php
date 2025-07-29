<?php
/**
 * Custom Fields / Meta Boxes for ComixCore Comics Plugin.
 * This file contains functions to add custom meta boxes to the 'comic' post type
 * and custom fields to 'comic_series' and 'comic_issues' taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/* --- Comic Post Meta Box --- */

/**
 * Adds a meta box for comic page details to the 'comic' post type.
 */
function comixcore_comics_add_comic_page_meta_box() {
    add_meta_box(
        'comixcore_comic_page_details',
        __( 'Comic Page Details', 'comixcore-comics' ),
        'comixcore_comics_render_comic_page_meta_box',
        'comic',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'comixcore_comics_add_comic_page_meta_box' );

/**
 * Renders the content of the comic page details meta box.
 *
 * @param WP_Post $post The current post object.
 */
function comixcore_comics_render_comic_page_meta_box( $post ) {
    wp_nonce_field( 'comixcore_comics_save_comic_page_details', 'comixcore_comics_meta_box_nonce' );

    $comic_page_image_id    = get_post_meta( $post->ID, '_comixcore-comics_comic_page_image_id', true );
    $comic_page_number      = get_post_meta( $post->ID, '_comixcore-comics_comic_page_number', true );
    $comic_page_content_type = get_post_meta( $post->ID, '_comixcore-comics_comic_page_content_type', true );
    ?>
    <p>
        <label for="comixcore_comic_page_image_id"><?php esc_html_e( 'Comic Page Image', 'comixcore-comics' ); ?></label><br />
        <input type="hidden" id="comixcore_comic_page_image_id" name="_comixcore-comics_comic_page_image_id" value="<?php echo esc_attr( $comic_page_image_id ); ?>" />
        <div id="comixcore_comic_page_image_preview">
            <?php if ( $comic_page_image_id ) : ?>
                <?php echo wp_get_attachment_image( $comic_page_image_id, 'thumbnail' ); ?>
            <?php endif; ?>
        </div>
        <button type="button" class="button comixcore-comics-upload-button" id="comixcore_comic_page_image_button">
            <?php esc_html_e( 'Select/Upload Image', 'comixcore-comics' ); ?>
        </button>
        <button type="button" class="button comixcore-comics-remove-button" id="comixcore_comic_page_image_remove_button" <?php echo empty( $comic_page_image_id ) ? 'style="display:none;"' : ''; ?>>
            <?php esc_html_e( 'Remove Image', 'comixcore-comics' ); ?>
        </button>
        <p class="description"><?php esc_html_e( 'Upload the main image for this comic page.', 'comixcore-comics' ); ?></p>
    </p>

    <p>
        <label for="comixcore_comic_page_number"><?php esc_html_e( 'Page Number', 'comixcore-comics' ); ?></label>
        <input type="number" id="comixcore_comic_page_number" name="_comixcore-comics_comic_page_number" value="<?php echo esc_attr( $comic_page_number ); ?>" min="0" step="1" style="width: 80px;" />
        <p class="description"><?php esc_html_e( 'The sequential page number within the comic issue. Used for ordering.', 'comixcore-comics' ); ?></p>
    </p>

    <p>
        <label for="comixcore_comic_page_content_type"><?php esc_html_e( 'Content Type', 'comixcore-comics' ); ?></label>
        <select id="comixcore_comic_page_content_type" name="_comixcore-comics_comic_page_content_type">
            <option value="comic-page" <?php selected( $comic_page_content_type, 'comic-page' ); ?>><?php esc_html_e( 'Comic Page (Image)', 'comixcore-comics' ); ?></option>
            <option value="cover" <?php selected( $comic_page_content_type, 'cover' ); ?>><?php esc_html_e( 'Cover', 'comixcore-comics' ); ?></option>
            <option value="epilogue" <?php selected( $comic_page_content_type, 'epilogue' ); ?>><?php esc_html_e( 'Epilogue', 'comixcore-comics' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Categorize this comic post (e.g., standard page, cover, or epilogue).', 'comixcore-comics' ); ?></p>
    </p>
    <?php
}

/**
 * Saves custom fields for Comic Post Type.
 *
 * @param int $post_id The ID of the post being saved.
 */
function comixcore_comics_save_comic_page_details( $post_id ) {
    // Check if our nonce is set and if it is valid.
    if ( ! isset( $_POST['comixcore_comics_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['comixcore_comics_meta_box_nonce'], 'comixcore_comics_save_comic_page_details' ) ) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    // Save comic page image ID
    if ( isset( $_POST['_comixcore-comics_comic_page_image_id'] ) ) {
        update_post_meta( $post_id, '_comixcore-comics_comic_page_image_id', sanitize_text_field( $_POST['_comixcore-comics_comic_page_image_id'] ) );
    } else {
        delete_post_meta( $post_id, '_comixcore-comics_comic_page_image_id' );
    }

    // Save comic page number
    if ( isset( $_POST['_comixcore-comics_comic_page_number'] ) ) {
        update_post_meta( $post_id, '_comixcore-comics_comic_page_number', intval( $_POST['_comixcore-comics_comic_page_number'] ) );
    } else {
        delete_post_meta( $post_id, '_comixcore-comics_comic_page_number' );
    }

    // Save comic page content type
    if ( isset( $_POST['_comixcore-comics_comic_page_content_type'] ) && in_array( $_POST['_comixcore-comics_comic_page_content_type'], array( 'comic-page', 'cover', 'epilogue' ) ) ) {
        update_post_meta( $post_id, '_comixcore-comics_comic_page_content_type', sanitize_text_field( $_POST['_comixcore-comics_comic_page_content_type'] ) );
    } else {
        delete_post_meta( $post_id, '_comixcore-comics_comic_page_content_type' );
    }
}
add_action( 'save_post_comic', 'comixcore_comics_save_comic_page_details' );


/* --- Comic Series Taxonomy Fields --- */

/**
 * Displays custom fields for Comic Series taxonomy.
 *
 * @param WP_Term|string $term The current term object on edit screen, or taxonomy name string on add screen.
 */
function comixcore_comics_add_comic_series_fields( $term ) {
    // In 'add' screen, $term is a string (the taxonomy name, e.g., 'comic_series').
    // In 'edit' screen, $term is the WP_Term object.
    // We need to handle both cases.

    $term_id = ( is_object( $term ) && isset( $term->term_id ) ) ? $term->term_id : 0;

    // Get existing meta values
    $series_logo_id = get_term_meta( $term_id, 'series_logo_id', true );

    // Nonce for security
    wp_nonce_field( 'comixcore_comics_save_comic_series_fields', 'comixcore_comics_series_nonce' );
    ?>
    <tr class="form-field term-logo-wrap">
        <th scope="row"><label for="series_logo_id"><?php esc_html_e( 'Series Logo', 'comixcore-comics' ); ?></label></th>
        <td>
            <input type="hidden" id="comixcore_series_logo_id" name="series_logo_id" value="<?php echo esc_attr( $series_logo_id ); ?>" />
            <div id="comixcore_series_logo_preview">
                <?php if ( $series_logo_id ) : ?>
                    <?php echo wp_get_attachment_image( $series_logo_id, 'thumbnail' ); ?>
                <?php endif; ?>
            </div>
            <button type="button" class="button comixcore-comics-upload-button" id="comixcore_series_logo_button">
                <?php esc_html_e( 'Select/Upload Logo', 'comixcore-comics' ); ?>
            </button>
            <button type="button" class="button comixcore-comics-remove-button" id="comixcore_series_logo_remove_button" <?php echo empty( $series_logo_id ) ? 'style="display:none;"' : ''; ?>>
                <?php esc_html_e( 'Remove Logo', 'comixcore-comics' ); ?>
            </button>
            <p class="description"><?php esc_html_e( 'Upload an image for the comic series logo.', 'comixcore-comics' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'comic_series_edit_form_fields', 'comixcore_comics_add_comic_series_fields', 10, 2 );
add_action( 'comic_series_add_form_fields', 'comixcore_comics_add_comic_series_fields', 10, 1 ); // Corrected: Expects only 1 argument for 'add' screen


/**
 * Saves custom fields for Comic Series taxonomy.
 *
 * @param int $term_id The ID of the term being saved.
 * @param int $tt_id   The term taxonomy ID. (Added for consistency with hook arguments)
 */
function comixcore_comics_save_comic_series_fields( $term_id, $tt_id ) { // Added $tt_id to function signature
    // Verify nonce
    if ( ! isset( $_POST['comixcore_comics_series_nonce'] ) || ! wp_verify_nonce( $_POST['comixcore_comics_series_nonce'], 'comixcore_comics_save_comic_series_fields' ) ) {
        return;
    }

    // Save series logo ID
    if ( isset( $_POST['series_logo_id'] ) ) {
        update_term_meta( $term_id, 'series_logo_id', sanitize_text_field( $_POST['series_logo_id'] ) );
    } else {
        delete_term_meta( $term_id, 'series_logo_id' );
    }
}
add_action( 'edited_comic_series', 'comixcore_comics_save_comic_series_fields', 10, 2 );
add_action( 'create_comic_series', 'comixcore_comics_save_comic_series_fields', 10, 2 );


/* --- Comic Issues Taxonomy Fields --- */

/**
 * Displays custom fields for Comic Issues taxonomy.
 *
 * @param WP_Term|string $term The current term object on edit screen, or taxonomy name string on add screen.
 */
function comixcore_comics_add_comic_issues_fields( $term ) {
    $term_id = ( is_object( $term ) && isset( $term->term_id ) ) ? $term->term_id : 0;

    $issue_cover_id = get_term_meta( $term_id, 'issue_cover_id', true );
    $issue_display_style = get_term_meta( $term_id, '_issue_display_style', true );
    if ( empty( $issue_display_style ) ) {
        $issue_display_style = 'page_by_page'; // Default value
    }

    wp_nonce_field( 'comixcore_comics_save_comic_issues_fields', 'comixcore_comics_issues_nonce' );
    ?>
    <tr class="form-field term-cover-wrap">
        <th scope="row"><label for="issue_cover_id"><?php esc_html_e( 'Issue Cover', 'comixcore-comics' ); ?></label></th>
        <td>
            <input type="hidden" id="comixcore_issue_cover_id" name="issue_cover_id" value="<?php echo esc_attr( $issue_cover_id ); ?>" />
            <div id="comixcore_issue_cover_preview">
                <?php if ( $issue_cover_id ) : ?>
                    <?php echo wp_get_attachment_image( $issue_cover_id, 'thumbnail' ); ?>
                <?php endif; ?>
            </div>
            <button type="button" class="button comixcore-comics-upload-button" id="comixcore_issue_cover_button">
                <?php esc_html_e( 'Select/Upload Cover', 'comixcore-comics' ); ?>
            </button>
            <button type="button" class="button comixcore-comics-remove-button" id="comixcore_issue_cover_remove_button" <?php echo empty( $issue_cover_id ) ? 'style="display:none;"' : ''; ?>>
                <?php esc_html_e( 'Remove Cover', 'comixcore-comics' ); ?>
            </button>
            <p class="description"><?php esc_html_e( 'Upload an image for the comic issue cover.', 'comixcore-comics' ); ?></p>
        </td>
    </tr>
    <tr class="form-field term-display-style-wrap">
        <th scope="row"><label for="_issue_display_style"><?php esc_html_e( 'Issue Display Style', 'comixcore-comics' ); ?></label></th>
        <td>
            <select id="_issue_display_style" name="_issue_display_style">
                <option value="page_by_page" <?php selected( $issue_display_style, 'page_by_page' ); ?>><?php esc_html_e( 'Page by Page (redirect to first page)', 'comixcore-comics' ); ?></option>
                <option value="scrolling" <?php selected( $issue_display_style, 'scrolling' ); ?>><?php esc_html_e( 'Scrolling (all pages on one)', 'comixcore-comics' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( 'Choose how this issue should be displayed on its archive page.', 'comixcore-comics' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'comic_issues_edit_form_fields', 'comixcore_comics_add_comic_issues_fields', 10, 2 );
add_action( 'comic_issues_add_form_fields', 'comixcore_comics_add_comic_issues_fields', 10, 1 ); // Corrected: Expects only 1 argument for 'add' screen


/**
 * Saves custom fields for Comic Issues taxonomy.
 *
 * @param int $term_id The ID of the term being saved.
 * @param int $tt_id   The term taxonomy ID. (Added for consistency with hook arguments)
 */
function comixcore_comics_save_comic_issues_fields( $term_id, $tt_id ) { // Added $tt_id to function signature
    if ( isset( $_POST['issue_cover_id'] ) ) {
        update_term_meta( $term_id, 'issue_cover_id', sanitize_text_field( $_POST['issue_cover_id'] ) );
    } else {
        delete_term_meta( $term_id, 'issue_cover_id' );
    }

    if ( isset( $_POST['_issue_display_style'] ) && in_array( $_POST['_issue_display_style'], array( 'page_by_page', 'scrolling' ) ) ) {
        update_term_meta( $term_id, '_issue_display_style', sanitize_text_field( $_POST['_issue_display_style'] ) );
    } else {
        delete_term_meta( $term_id, '_issue_display_style' );
    }
}
add_action( 'edited_comic_issues', 'comixcore_comics_save_comic_issues_fields', 10, 2 );
add_action( 'create_comic_issues', 'comixcore_comics_save_comic_issues_fields', 10, 2 );