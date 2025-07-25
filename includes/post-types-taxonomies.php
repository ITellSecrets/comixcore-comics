<?php
// Register Custom Post Type: Comic
function comicxcore_register_comic_assets() { // Renamed function for clarity
    // Register Custom Post Type: Comic
    $labels = array(
        'name'                  => _x( 'Comics', 'Post Type General Name', 'comicxcore' ),
        'singular_name'         => _x( 'Comic', 'Post Type Singular Name', 'comicxcore' ),
        'menu_name'             => _x( 'Comics', 'Admin Menu text', 'comicxcore' ),
        'name_admin_bar'        => _x( 'Comic', 'Add New on Toolbar', 'comicxcore' ),
        'archives'              => __( 'Comic Archives', 'comicxcore' ),
        'attributes'            => __( 'Comic Attributes', 'comicxcore' ),
        'parent_item_colon'     => __( 'Parent Comic:', 'comicxcore' ),
        'all_items'             => __( 'All Comics', 'comicxcore' ),
        'add_new_item'          => __( 'Add New Comic', 'comicxcore' ),
        'add_new'               => __( 'Add New', 'comicxcore' ),
        'new_item'              => __( 'New Comic', 'comicxcore' ),
        'edit_item'             => __( 'Edit Comic', 'comicxcore' ),
        'update_item'           => __( 'Update Comic', 'comicxcore' ),
        'view_item'             => __( 'View Comic', 'comicxcore' ),
        'view_items'            => __( 'View Comics', 'comicxcore' ),
        'search_items'          => __( 'Search Comic', 'comicxcore' ),
        'not_found'             => __( 'Not found', 'comicxcore' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'comicxcore' ),
        'featured_image'        => __( 'Featured Image', 'comicxcore' ),
        'set_featured_image'    => __( 'Set featured image', 'comicxcore' ),
        'remove_featured_image' => __( 'Remove featured image', 'comicxcore' ),
        'use_featured_image'    => __( 'Use as featured image', 'comicxcore' ),
        'insert_into_item'      => __( 'Insert into comic', 'comicxcore' ),
        'uploaded_to_this_item' => __( 'Uploaded to this comic', 'comicxcore' ),
        'items_list'            => __( 'Comics list', 'comicxcore' ),
        'items_list_navigation' => __( 'Comics list navigation', 'comicxcore' ),
        'filter_items_list'     => __( 'Filter comics list', 'comicxcore' ),
    );
    $args = array(
        'label'                 => __( 'Comic', 'comicxcore' ),
        'description'           => __( 'Custom post type for individual comic issues.', 'comicxcore' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'page-attributes' ), // Added 'page-attributes' for menu_order
        'taxonomies'            => array( 'comic_series', 'comic_issues' ), // THIS WAS THE MISSING/INCORRECT LINE!
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-image',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'comics', // Slug for the archive page (e.g., yoursite.com/comics)
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true, // Enable for Gutenberg editor and REST API
    );
    register_post_type( 'comic', $args );

    // Register Custom Taxonomy: Comic Series
    $labels = array(
        'name'                       => _x( 'Comic Series', 'Taxonomy General Name', 'comicxcore' ),
        'singular_name'              => _x( 'Comic Series', 'Taxonomy Singular Name', 'comicxcore' ),
        'menu_name'                  => __( 'Comic Series', 'comicxcore' ),
        'all_items'                  => __( 'All Series', 'comicxcore' ),
        'parent_item'                => __( 'Parent Series', 'comicxcore' ),
        'parent_item_colon'          => __( 'Parent Series:', 'comicxcore' ),
        'new_item_name'              => __( 'New Series Name', 'comicxcore' ),
        'add_new_item'               => __( 'Add New Series', 'comicxcore' ),
        'edit_item'                  => __( 'Edit Series', 'comicxcore' ),
        'update_item'                => __( 'Update Series', 'comicxcore' ),
        'view_item'                  => __( 'View Series', 'comicxcore' ),
        'separate_items_with_commas' => __( 'Separate series with commas', 'comicxcore' ),
        'add_or_remove_items'        => __( 'Add or remove series', 'comicxcore' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'comicxcore' ),
        'popular_items'              => __( 'Popular Series', 'comicxcore' ),
        'search_items'               => __( 'Search Series', 'comicxcore' ),
        'not_found'                  => __( 'Not Found', 'comicxcore' ),
        'no_terms'                   => __( 'No series', 'comicxcore' ),
        'items_list'                 => __( 'Series list', 'comicxcore' ),
        'items_list_navigation'      => __( 'Series list navigation', 'comicxcore' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, // Make it like categories
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true, // Enable for Gutenberg and REST API
        'rewrite'                    => array( 'slug' => 'comic-series' ), // Custom slug for taxonomy archive
    );
    register_taxonomy( 'comic_series', array( 'comic' ), $args ); // Associate with 'comic' post type

    // Register Custom Taxonomy: Comic Issues (THIS WAS MISSING ENTIRELY)
    $labels = array(
        'name'                       => _x( 'Comic Issues', 'Taxonomy General Name', 'comicxcore' ),
        'singular_name'              => _x( 'Comic Issue', 'Taxonomy Singular Name', 'comicxcore' ),
        'menu_name'                  => __( 'Comic Issues', 'comicxcore' ),
        'all_items'                  => __( 'All Issues', 'comicxcore' ),
        'parent_item'                => __( 'Parent Issue', 'comicxcore' ),
        'parent_item_colon'          => __( 'Parent Issue:', 'comicxcore' ),
        'new_item_name'              => __( 'New Issue Name', 'comicxcore' ),
        'add_new_item'               => __( 'Add New Issue', 'comicxcore' ),
        'edit_item'                  => __( 'Edit Issue', 'comicxcore' ),
        'update_item'                => __( 'Update Issue', 'comicxcore' ),
        'view_item'                  => __( 'View Issue', 'comicxcore' ),
        'separate_items_with_commas' => __( 'Separate issues with commas', 'comicxcore' ),
        'add_or_remove_items'        => __( 'Add or remove issues', 'comicxcore' ),
        'choose_from_most_used'      => __( 'Choose from the most used issues', 'comicxcore' ),
        'popular_items'              => __( 'Popular Issues', 'comicxcore' ),
        'search_items'               => __( 'Search Issues', 'comicxcore' ),
        'not_found'                  => __( 'Not Found', 'comicxcore' ),
        'no_terms'                   => __( 'No issues', 'comicxcore' ),
        'items_list'                 => __( 'Issues list', 'comicxcore' ),
        'items_list_navigation'      => __( 'Issues list navigation', 'comicxcore' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, // Issues can have a hierarchy if needed (e.g., volumes > issues)
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'rewrite'                    => array( 'slug' => 'comic-issue', 'with_front' => false, 'hierarchical' => true ),
        'show_in_rest'               => true, // Enable for Gutenberg/REST API
    );
    register_taxonomy( 'comic_issues', array( 'comic' ), $args ); // Associate with 'comic' post type
}
add_action( 'init', 'comicxcore_register_comic_assets', 0 ); // Keep the hook and priority