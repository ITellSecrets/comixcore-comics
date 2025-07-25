<?php
/**
 * Custom Page Templates for ComixCore Comics Plugin.
 * This file registers custom page templates provided by the plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register the custom page templates provided by the plugin.
 *
 * This function hooks into WordPress's template system to make
 * 'page-comic-hub.php' available as a selectable page template.
 *
 * @param array $templates An associative array of template filename => template name.
 * @return array The filtered array of templates.
 */
function comixcore_comics_register_page_templates( $templates ) {
    $plugin_templates = array();

    // Define the path to your page-comic-hub.php template within the plugin
    $comic_hub_template_path = COMIXCORE_COMICS_PLUGIN_DIR . 'page-templates/page-comic-hub.php';

    // Check if the file exists before adding it
    if ( file_exists( $comic_hub_template_path ) ) {
        $plugin_templates['page-comic-hub.php'] = 'Comic Hub Page (ComixCore)';
    }

    // Merge plugin templates with existing theme templates
    return array_merge( $templates, $plugin_templates );
}
add_filter( 'theme_page_templates', 'comixcore_comics_register_page_templates' );


/**
 * Filter the page template to ensure the plugin's template is loaded.
 *
 * This function ensures that when a page is assigned the 'Comic Hub Page (ComixCore)'
 * template, the correct file from the plugin is used.
 *
 * @param string $template The path to the template file.
 * @return string The modified path to the template file.
 */
function comixcore_comics_load_page_template( $template ) {
    global $post;

    // Check if we are on a page, and if a template is assigned to it
    if ( is_page() && isset( $post->page_template ) ) {
        // If the assigned template matches our plugin's comic hub template filename
        if ( $post->page_template === 'page-comic-hub.php' ) {
            $plugin_template = COMIXCORE_COMICS_PLUGIN_DIR . 'page-templates/page-comic-hub.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
    }
    // Always return the original template if our specific conditions aren't met
    return $template;
}
add_filter( 'template_include', 'comixcore_comics_load_page_template' );