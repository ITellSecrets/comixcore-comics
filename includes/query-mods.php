<?php
/**
 * Query Modifications for ComixCore Comics Plugin.
 * This file contains functions to modify WordPress queries related to comic posts and taxonomies.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Modify the main query for 'comic_series' and 'comic_issues' archives.
 *
 * This ensures that:
 * - On 'comic_series' archives, the main query for posts is controlled to allow manual
 * querying of issues within the theme/plugin template.
 * - On 'comic_issues' archives, 'comic' posts are queried, ordered by 'menu_order'
 * (which is used for page numbers), and initially limited to 1 for redirection.
 *
 * @param WP_Query $query The main WP_Query instance.
 */
function comixcore_comics_pre_get_posts_for_comics( $query ) {
    // Only modify the main query on the front end.
    if ( $query->is_main_query() && ! is_admin() ) {

        // For 'comic_series' archives:
        // The display logic for issues is handled directly in taxonomy-comic_series.php now,
        // so we prevent the default post query for 'comic' posts on this page.
        if ( $query->is_tax( 'comic_series' ) ) {
            // Set posts_per_page to 1. We still need have_posts() to return true for
            // the header/footer to load, but we don't want to loop through actual comic posts here.
            $query->set( 'posts_per_page', 1 );
            $query->set( 'post_type', 'comic' ); // Keep context as 'comic' post type.
            return;
        }

        // For 'comic_issues' archives:
        // Ensure only 'comic' posts are queried, order them by 'menu_order' (page number),
        // and fetch only the first one for potential redirection.
        if ( $query->is_tax( 'comic_issues' ) ) {
            $query->set( 'post_type', 'comic' );
            $query->set( 'orderby', 'menu_order' );
            $query->set( 'order', 'ASC');
            // Fetch only the first comic page to determine issue display style/redirect.
            $query->set( 'posts_per_page', 1 );
            return;
        }
    }
}
add_action( 'pre_get_posts', 'comixcore_comics_pre_get_posts_for_comics' );

// Note: The redirection logic for taxonomy-comic_issues.php is best handled
// directly within that template file itself, or within a dedicated template loader.
// The `pre_get_posts` hook primarily sets up the query parameters.