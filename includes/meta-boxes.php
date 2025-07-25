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

    $comic_page_image_id = get_post_meta( $post->ID, '_comic_page_image_id', true );
    $comic_page_number = get_post_meta( $post->ID, '_comic_page_number', true );
    $comic_page_content_type = get_post_meta( $post->ID, '_comic_page_content_type', true );

    ?>

    <p>
    <label for="comixcore_comic_page_image_id"><?php esc_html_e( 'Comic Page Image:', 'comixcore-comics' ); ?></label><br>
    <input type="hidden" id="comixcore_comic_page_image_id" name="_comic_page_image_id" value="<?php echo esc_attr( $comic_page_image_id ); ?>" />
    <div id="comixcore_comic_page_image_preview">
        <?php if ( $comic_page_image_id ) : ?>
            <?php echo wp_get_attachment_image( $comic_page_image_id, 'medium' ); ?>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="comixcore_comic_page_image_button"><?php _e( 'Add/Replace Image', 'comixcore-comics' ); ?></button>
    <button type="button" class="button button-secondary" id="comixcore_comic_page_image_remove_button" style="<?php echo $comic_page_image_id ? '' : 'display:none;'; ?>"><?php _e( 'Remove Image', 'comixcore-comics' ); ?></button>
    </p>

    <p>
        <label for="comixcore_comic_page_number"><?php esc_html_e( 'Comic Page Number:', 'comixcore-comics' ); ?></label>
        <input type="number" name="_comic_page_number" id="comixcore_comic_page_number" value="<?php echo esc_attr( $comic_page_number ); ?>" min="1" step="1" style="width: 80px;" />
        <p class="description"><?php esc_html_e( 'Enter the sequential page number for this comic within its issue. Used for ordering.', 'comixcore-comics' ); ?></p>
    </p>

    <p>
        <label for="comixcore_comic_page_content_type"><?php esc_html_e( 'Comic Content Type:', 'comixcore-comics' ); ?></label>
        <select name="_comic_page_content_type" id="comixcore_comic_page_content_type">
            <option value="single_page" <?php selected( $comic_page_content_type, 'single_page' ); ?>><?php esc_html_e( 'Single Page', 'comixcore-comics' ); ?></option>
            <option value="scrolling" <?php selected( $comic_page_content_type, 'scrolling' ); ?>><?php esc_html_e( 'Scrolling (for full issue view)', 'comixcore-comics' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Choose how this comic page contributes to issue display (e.g., individual page or part of a long scrolling issue).', 'comixcore-comics' ); ?></p>
    </p>
    <?php
    // Enqueue Media Uploader scripts for the image selection.
    wp_enqueue_media();
    ?>
    <?php
}

/**
 * Saves the comic page details meta box data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function comixcore_comics_save_comic_page_details( $post_id ) {
    if ( ! isset( $_POST['comixcore_comics_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['comixcore_comics_meta_box_nonce'], 'comixcore_comics_save_comic_page_details' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save Comic Page Image ID
    if ( isset( $_POST['_comic_page_image_id'] ) ) {
        update_post_meta( $post_id, '_comic_page_image_id', sanitize_text_field( $_POST['_comic_page_image_id'] ) );
    } else {
        delete_post_meta( $post_id, '_comic_page_image_id' );
    }

    // Save Comic Page Number
    if ( isset( $_POST['_comic_page_number'] ) ) {
        $page_number = absint( $_POST['_comic_page_number'] );
        update_post_meta( $post_id, '_comic_page_number', $page_number );
    } else {
        delete_post_meta( $post_id, '_comic_page_number' );
    }

    // Save Comic Page Content Type
    if ( isset( $_POST['_comic_page_content_type'] ) && in_array( $_POST['_comic_page_content_type'], array( 'single_page', 'scrolling' ) ) ) {
        update_post_meta( $post_id, '_comic_page_content_type', sanitize_text_field( $_POST['_comic_page_content_type'] ) );
    } else {
        delete_post_meta( $post_id, '_comic_page_content_type' );
    }
}
add_action( 'save_post_comic', 'comixcore_comics_save_comic_page_details' );


/* --- Taxonomy Term Meta for Comic Series --- */

/**
 * Adds fields to the Comic Series taxonomy edit page.
 *
 * @param WP_Term $term The term object.
 */
function comixcore_comics_add_comic_series_fields( $term ) {
    $series_logo_id = get_term_meta( $term->term_id, 'series_logo_id', true );
    ?>
    <tr class="form-field term-series-logo-wrap">
    <th scope="row"><label for="comixcore_series_logo_id"><?php esc_html_e( 'Series Logo', 'comixcore-comics' ); ?></label></th>
    <td>
        <?php
        // Get current logo ID
        $series_logo_id = get_term_meta( $term->term_id, 'series_logo_id', true );
        ?>
        <input type="hidden" id="comixcore_series_logo_id" name="series_logo_id" value="<?php echo esc_attr( $series_logo_id ); ?>" />
        <div id="comixcore_series_logo_preview">
            <?php if ( $series_logo_id ) : ?>
                <?php echo wp_get_attachment_image( $series_logo_id, 'thumbnail' ); ?>
            <?php endif; ?>
        </div>
        <p>
            <button type="button" class="button button-secondary" id="comixcore_series_logo_button"><?php esc_attr_e( 'Add/Replace Logo', 'comixcore-comics' ); ?></button>
            <button type="button" class="button button-secondary" id="comixcore_series_logo_remove_button" style="<?php echo empty( $series_logo_id ) ? 'display:none;' : ''; ?>"><?php esc_attr_e( 'Remove Logo', 'comixcore-comics' ); ?></button>
        </p>
        <p class="description"><?php esc_html_e( 'Upload or select a logo for this comic series.', 'comixcore-comics' ); ?></p>
    </td>
</tr>
    <script type="text/javascript">
        // Re-initialize media uploader for term edit page if needed
        jQuery(document).ready(function($){
            if (typeof wp.media !== 'undefined') {
                var mediaUploader;
                $('.term-series-logo-wrap .comixcore-comics-upload-button').off('click').on('click', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var $imageField = $button.siblings('.comixcore-comics-image-id-field');
                    var $imagePreview = $button.siblings('.comixcore-comics-image-preview');
                    var $removeButton = $button.siblings('.comixcore-comics-remove-image-button');

                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media({
                        title: '<?php esc_html_e( "Choose Series Logo", "comixcore-comics" ); ?>',
                        button: {
                            text: '<?php esc_html_e( "Choose Logo", "comixcore-comics" ); ?>'
                        },
                        multiple: false
                    });

                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $imageField.val(attachment.id);
                        $imagePreview.html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;" />');
                        $removeButton.show();
                    });

                    mediaUploader.open();
                });

                $('.term-series-logo-wrap .comixcore-comics-remove-image-button').off('click').on('click', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    $button.siblings('.comixcore-comics-image-id-field').val('');
                    $button.siblings('.comixcore-comics-image-preview').html('');
                    $button.hide();
                });
            }
        });
    </script>
    <?php
}
add_action( 'comic_series_edit_form_fields', 'comixcore_comics_add_comic_series_fields', 10, 2 );
add_action( 'comic_series_add_form_fields', 'comixcore_comics_add_comic_series_fields', 10, 2 ); // For new term creation page

/**
 * Saves custom fields for Comic Series taxonomy.
 *
 * @param int $term_id The ID of the term being saved.
 */
function comixcore_comics_save_comic_series_fields( $term_id ) {
    if ( isset( $_POST['series_logo_id'] ) ) {
        update_term_meta( $term_id, 'series_logo_id', sanitize_text_field( $_POST['series_logo_id'] ) );
    } else {
        delete_term_meta( $term_id, 'series_logo_id' );
    }
}
add_action( 'edited_comic_series', 'comixcore_comics_save_comic_series_fields', 10, 2 );
add_action( 'create_comic_series', 'comixcore_comics_save_comic_series_fields', 10, 2 );


/* --- Taxonomy Term Meta for Comic Issues --- */

/**
 * Adds fields to the Comic Issues taxonomy edit page.
 *
 * @param WP_Term $term The term object.
 */
function comixcore_comics_add_comic_issues_fields( $term ) {
    $issue_cover_id = get_term_meta( $term->term_id, 'issue_cover_id', true );
    $issue_display_style = get_term_meta( $term->term_id, '_issue_display_style', true );
    if ( empty( $issue_display_style ) ) {
        $issue_display_style = 'page_by_page'; // Default value
    }
    ?>
    <tr class="form-field term-issue-cover-wrap">
    <th scope="row"><label for="comixcore_issue_cover_id"><?php _e( 'Issue Cover', 'comixcore-comics' ); ?></label></th>
    <td>
        <?php
        // Get current image ID
        $issue_cover_id = get_term_meta( $term->term_id, 'issue_cover_id', true );
        ?>
        <input type="hidden" id="comixcore_issue_cover_id" name="issue_cover_id" value="<?php echo esc_attr( $issue_cover_id ); ?>" />
        <div id="comixcore_issue_cover_preview">
            <?php if ( $issue_cover_id ) : ?>
                <?php echo wp_get_attachment_image( $issue_cover_id, 'thumbnail' ); ?>
            <?php endif; ?>
        </div>
        <p>
            <button type="button" class="button button-secondary" id="comixcore_issue_cover_button"><?php _e( 'Add/Replace Image', 'comixcore-comics' ); ?></button>
            <button type="button" class="button button-secondary" id="comixcore_issue_cover_remove_button" style="<?php echo $issue_cover_id ? '' : 'display:none;'; ?>"><?php _e( 'Remove Image', 'comixcore-comics' ); ?></button>
        </p>
        <p class="description"><?php _e( 'Upload an image for the issue cover.', 'comixcore-comics' ); ?></p>
    </td>
</tr>

    <tr class="form-field term-issue-display-style-wrap">
        <th scope="row"><label for="_issue_display_style"><?php esc_html_e( 'Issue Display Style', 'comixcore-comics' ); ?></label></th>
        <td>
            <select name="_issue_display_style" id="_issue_display_style">
                <option value="page_by_page" <?php selected( $issue_display_style, 'page_by_page' ); ?>><?php esc_html_e( 'Page by Page', 'comixcore-comics' ); ?></option>
                <option value="scrolling" <?php selected( $issue_display_style, 'scrolling' ); ?>><?php esc_html_e( 'Scrolling (all pages on one)', 'comixcore-comics' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( 'Choose how this issue should be displayed on its archive page.', 'comixcore-comics' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'comic_issues_edit_form_fields', 'comixcore_comics_add_comic_issues_fields', 10, 2 );
add_action( 'comic_issues_add_form_fields', 'comixcore_comics_add_comic_issues_fields', 10, 2 );

/**
 * Saves custom fields for Comic Issues taxonomy.
 *
 * @param int $term_id The ID of the term being saved.
 */
function comixcore_comics_save_comic_issues_fields( $term_id ) {
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