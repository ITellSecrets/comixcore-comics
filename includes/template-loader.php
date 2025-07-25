<?php
/**
 * Template Loader for ComixCore Comics Plugin.
 * This file handles overriding WordPress's default template hierarchy
 * to use custom templates provided by the plugin for comic content.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Filter the template include path to use plugin templates for comic content.
 *
 * @param string $template The path to the template file.
 * @return string The modified path to the template file.
 */
function comixcore_comics_template_loader( $template ) {
    // Get the current queried object (post, term, etc.)
    $queried_object = get_queried_object();

    // 1. Single Comic Post
    if ( is_singular( 'comic' ) ) {
        // Look for single-comic.php in plugin's templates directory
        $new_template = COMIXCORE_COMICS_PLUGIN_DIR . 'templates/single-comic.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }

    // 2. Comic Series Taxonomy Archive
    if ( is_tax( 'comic_series' ) ) {
        // Look for taxonomy-comic_series.php in plugin's templates directory
        $new_template = COMIXCORE_COMICS_PLUGIN_DIR . 'templates/taxonomy-comic_series.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }

    // 3. Comic Issues Taxonomy Archive
    if ( is_tax( 'comic_issues' ) ) {
        // Look for taxonomy-comic_issues.php in plugin's templates directory
        $new_template = COMIXCORE_COMICS_PLUGIN_DIR . 'templates/taxonomy-comic_issues.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }

    // Always return the original template if no specific plugin template is found.
    return $template;
}
add_filter( 'template_include', 'comixcore_comics_template_loader' );