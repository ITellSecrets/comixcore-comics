<?php
/**
 * Image Sizes for ComixCore Comics Plugin.
 * This file defines custom image sizes specifically for comic content.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register custom image sizes for comics.
 * These sizes are used for main comic pages, series logos, and issue covers.
 */
function comixcore_comics_setup_image_sizes() {
    // Used for the main comic page image on single comic posts.
    add_image_size( 'comic-full', 1200, 9999, false ); // Max width 1200px, flexible height, no crop.

    // Used for a medium-sized version of the comic page, e.g., in grids or archives.
    add_image_size( 'comic-medium', 800, 9999, false ); // Max width 800px, flexible height, no crop.

    // Used for comic series logos (e.g., on the comic hub page).
    add_image_size( 'series-logo', 300, 300, false ); // Max 300x300, but can be smaller, no crop.

    // Used for individual comic issue covers.
    add_image_size( 'issue-cover', 200, 300, true ); // 200px wide, 300px high, hard crop.
}
add_action( 'after_setup_theme', 'comixcore_comics_setup_image_sizes' );