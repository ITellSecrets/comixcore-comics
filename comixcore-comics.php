<?php
/**
 * Plugin Name: ComixCore Comics
 * Plugin URI:  https://wyrdnorthwest.com/
 * Description: A plugin to manage custom post types, taxonomies, and functionality for comic content on Urth.
 * Version:     1.0.0
 * Author:      Tait
 * Author URI:  https://wyrdnorthwest.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: comixcore-comics
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'COMIXCORE_COMICS_VERSION', '1.0.0' );
define( 'COMIXCORE_COMICS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'COMIXCORE_COMICS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing hooks.
 */
class ComixCore_Comics {

    /**
     * Constructor for the plugin.
     * Sets up all the hooks and includes necessary files.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->hooks();
    }

    /**
     * Define the plugin constants.
     * (Already defined above, but good practice for class context)
     */
    private function define_constants() {
        if ( ! defined( 'COMIXCORE_COMICS_VERSION' ) ) {
            define( 'COMIXCORE_COMICS_VERSION', '1.0.0' );
        }
        if ( ! defined( 'COMIXCORE_COMICS_PLUGIN_DIR' ) ) {
            define( 'COMIXCORE_COMICS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        if ( ! defined( 'COMIXCORE_COMICS_PLUGIN_URL' ) ) {
            define( 'COMIXCORE_COMICS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }
    }

    /**
     * Include all necessary files.
     */
    private function includes() {
        // Core functionality
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/post-types-taxonomies.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/meta-boxes.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/query-mods.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/image-sizes.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/template-loader.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/page-templates.php';
        require_once COMIXCORE_COMICS_PLUGIN_DIR . 'includes/enqueue.php';
        // Add more includes here as you develop more features (e.g., admin, shortcodes, widgets)
    }

    /**
     * Register any necessary hooks.
     * (Most hooks are inside the included files, but you could add global ones here)
     */
    private function hooks() {
        // Example: Add a general activation hook if needed
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        // register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }

    /**
     * Plugin activation hook.
     * Runs when the plugin is activated.
     */
    public function activate() {
        // We no longer need to call the CPT/Taxonomy registration functions directly here.
        // They are hooked to 'init' and will run automatically when WordPress loads.
        // The primary goal of activation is to flush rewrite rules for permalinks.

        // Flush rewrite rules to make sure our custom post type and taxonomy URLs work.
        flush_rewrite_rules();
    }

    // You can add a deactivate() method here if needed for cleanup
    // public function deactivate() {}

} // End of ComixCore_Comics class

/**
 * Initialize the plugin.
 * This is the main function that kicks off the plugin's execution.
 */
function run_comixcore_comics() {
    new ComixCore_Comics();
}
run_comixcore_comics();