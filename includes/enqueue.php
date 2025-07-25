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
        filemtime( COMIXCORE_COMICS_PLUGIN_DIR . 'assets/css/style-comics.css' ), // Version based on file modification time for cache busting
        'all' // Media type
    );

    // If you had any public-facing JavaScript, you would enqueue it here.
}
add_action( 'wp_enqueue_scripts', 'comixcore_comics_enqueue_public_assets' );

/**
 * Enqueue plugin-specific stylesheets and scripts for the WordPress admin area.
 */
function comixcore_comics_enqueue_admin_assets() {
    // Only enqueue on post edit screens and for the 'comic' post type
    global $pagenow, $post_type;

    if ( ('post.php' == $pagenow || 'post-new.php' == $pagenow) && 'comic' == $post_type ) {
        // Enqueue WordPress Media Uploader scripts and styles
        wp_enqueue_media();

        // Enqueue our custom script for the comic meta box
        wp_enqueue_script(
            'comixcore-comics-meta-box-script',
            COMIXCORE_COMICS_PLUGIN_URL . 'assets/js/comic-meta-box.js',
            array('jquery'), // Depends on jQuery
            filemtime( COMIXCORE_COMICS_PLUGIN_DIR . 'assets/js/comic-meta-box.js' ),
            true // Load in footer
        );
    }

    // You can add other admin-specific styles or scripts here if needed.
}
add_action( 'admin_enqueue_scripts', 'comixcore_comics_enqueue_admin_assets' );