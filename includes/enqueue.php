<?php
/**
 * Enqueue Scripts and Styles for ComixCore Comics Plugin.
 * This file handles loading custom stylesheets and scripts specifically for the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue plugin-specific stylesheets and scripts for the public-facing site.
 */
function comixcore_comics_enqueue_public_assets() {
    // Enqueue comic-specific stylesheet
    wp_enqueue_style(
        'comixcore-comics-style', // Unique handle for your stylesheet
        COMIXCORE_COMICS_PLUGIN_URL . 'assets/css/style-comics.css', // Path to your stylesheet
        array(), // Dependencies (e.g., array('your-theme-style') if you want it to load after theme styles)
        COMIXCORE_COMICS_VERSION, // Version based on plugin version for cache busting
        'all' // Media type
    );

    // If you had any public-facing JavaScript, you would enqueue it here.
}
add_action( 'wp_enqueue_scripts', 'comixcore_comics_enqueue_public_assets' );

/**
 * Enqueue plugin-specific stylesheets and scripts for the WordPress admin area.
 */
function comixcore_comics_enqueue_admin_assets() {
    // Get current screen information
    $screen = get_current_screen();

    // Exit if screen object is not available
    if ( ! $screen ) {
        return;
    }

    // Enqueue on 'comic' post edit screens (both 'Add New' and 'Edit')
    if ( ('post' === $screen->base || 'post-new' === $screen->base) && 'comic' === $screen->post_type ) {
        wp_enqueue_script( 'jquery' ); // Ensure jQuery is loaded
        wp_enqueue_media(); // Enqueue WordPress Media Uploader scripts and styles

        wp_enqueue_script(
            'comixcore-comics-meta-box-script', // Unique handle for your script
            COMIXCORE_COMICS_PLUGIN_URL . 'assets/js/comic-meta-box.js', // Corrected path
            array('jquery', 'wp-mediaelement'), // Dependencies: jQuery and wp-mediaelement are crucial
            COMIXCORE_COMICS_VERSION, // Use plugin version for cache busting
            true // Load in footer
        );
    }

    // Enqueue on 'comic_series' and 'comic_issues' taxonomy edit screens ('edit-tags' page)
    if ( 'edit-tags' === $screen->base && in_array( $screen->taxonomy, array( 'comic_series', 'comic_issues' ) ) ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_media();

        wp_enqueue_script(
            'comixcore-comics-meta-box-script',
            COMIXCORE_COMICS_PLUGIN_URL . 'assets/js/comic-meta-box.js', // Corrected path
            array('jquery', 'wp-mediaelement'), // Dependencies
            COMIXCORE_COMICS_VERSION,
            true
        );
    }

    // Enqueue on specific term edit screens ('term' page, when editing an individual term)
    if ( 'term' === $screen->base && in_array( $screen->taxonomy, array( 'comic_series', 'comic_issues' ) ) ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_media();

        wp_enqueue_script(
            'comixcore-comics-meta-box-script',
            COMIXCORE_COMICS_PLUGIN_URL . 'assets/js/comic-meta-box.js', // Corrected path
            array('jquery', 'wp-mediaelement'), // Dependencies
            COMIXCORE_COMICS_VERSION,
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'comixcore_comics_enqueue_admin_assets' );