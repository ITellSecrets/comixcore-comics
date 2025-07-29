<?php
// Register Custom Post Type: Comic
function comixcore_comics_register_comic_assets() { // Renamed function for clarity
    // Register Custom Post Type: Comic
    $labels = array(
        'name'                  => _x( 'Comics', 'Post Type General Name', 'comixcore-comics' ),
        'singular_name'         => _x( 'Comic', 'Post Type Singular Name', 'comixcore-comics' ),
        'menu_name'             => _x( 'Comics', 'Admin Menu text', 'comixcore-comics' ),
        'name_admin_bar'        => _x( 'Comic', 'Add New on Toolbar', 'comixcore-comics' ),
        'archives'              => __( 'Comic Archives', 'comixcore-comics' ),
        'attributes'            => __( 'Comic Attributes', 'comixcore-comics' ),
        'parent_item_colon'     => __( 'Parent Comic:', 'comixcore-comics' ),
        'all_items'             => __( 'All Comics', 'comixcore-comics' ),
        'add_new_item'          => __( 'Add New Comic', 'comixcore-comics' ),
        'add_new'               => __( 'Add New', 'comixcore-comics' ),
        'new_item'              => __( 'New Comic', 'comixcore-comics' ),
        'edit_item'             => __( 'Edit Comic', 'comixcore-comics' ),
        'update_item'           => __( 'Update Comic', 'comixcore-comics' ),
        'view_item'             => __( 'View Comic', 'comixcore-comics' ),
        'view_items'            => __( 'View Comics', 'comixcore-comics' ),
        'search_items'          => __( 'Search Comic', 'comixcore-comics' ),
        'not_found'             => __( 'Not found', 'comixcore-comics' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'comixcore-comics' ),
        'featured_image'        => __( 'Featured Image', 'comixcore-comics' ),
        'set_featured_image'    => __( 'Set featured image', 'comixcore-comics' ),
        'remove_featured_image' => __( 'Remove featured image', 'comixcore-comics' ),
        'use_featured_image'    => __( 'Use as featured image', 'comixcore-comics' ),
        'insert_into_item'      => __( 'Insert into comic', 'comixcore-comics' ),
        'uploaded_to_this_item' => __( 'Uploaded to this comic', 'comixcore-comics' ),
        'items_list'            => __( 'Comics list', 'comixcore-comics' ),
        'items_list_navigation' => __( 'Comics list navigation', 'comixcore-comics' ),
        'filter_items_list'     => __( 'Filter comics list', 'comixcore-comics' ),
    );
    $args = array(
        'label'                 => __( 'Comic', 'comixcore-comics' ),
        'description'           => __( 'Custom post type for individual comic issues.', 'comixcore-comics' ),
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
        'name'                       => _x( 'Comic Series', 'Taxonomy General Name', 'comixcore-comics' ),
        'singular_name'              => _x( 'Comic Series', 'Taxonomy Singular Name', 'comixcore-comics' ),
        'menu_name'                  => __( 'Comic Series', 'comixcore-comics' ),
        'all_items'                  => __( 'All Series', 'comixcore-comics' ),
        'parent_item'                => __( 'Parent Series', 'comixcore-comics' ),
        'parent_item_colon'          => __( 'Parent Series:', 'comixcore-comics' ),
        'new_item_name'              => __( 'New Series Name', 'comixcore-comics' ),
        'add_new_item'               => __( 'Add New Series', 'comixcore-comics' ),
        'edit_item'                  => __( 'Edit Series', 'comixcore-comics' ),
        'update_item'                => __( 'Update Series', 'comixcore-comics' ),
        'view_item'                  => __( 'View Series', 'comixcore-comics' ),
        'separate_items_with_commas' => __( 'Separate series with commas', 'comixcore-comics' ),
        'add_or_remove_items'        => __( 'Add or remove series', 'comixcore-comics' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'comixcore-comics' ),
        'popular_items'              => __( 'Popular Series', 'comixcore-comics' ),
        'search_items'               => __( 'Search Series', 'comixcore-comics' ),
        'not_found'                  => __( 'Not Found', 'comixcore-comics' ),
        'no_terms'                   => __( 'No series', 'comixcore-comics' ),
        'items_list'                 => __( 'Series list', 'comixcore-comics' ),
        'items_list_navigation'      => __( 'Series list navigation', 'comixcore-comics' ),
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
        'name'                       => _x( 'Comic Issues', 'Taxonomy General Name', 'comixcore-comics' ),
        'singular_name'              => _x( 'Comic Issue', 'Taxonomy Singular Name', 'comixcore-comics' ),
        'menu_name'                  => __( 'Comic Issues', 'comixcore-comics' ),
        'all_items'                  => __( 'All Issues', 'comixcore-comics' ),
        'parent_item'                => __( 'Parent Issue', 'comixcore-comics' ),
        'parent_item_colon'          => __( 'Parent Issue:', 'comixcore-comics' ),
        'new_item_name'              => __( 'New Issue Name', 'comixcore-comics' ),
        'add_new_item'               => __( 'Add New Issue', 'comixcore-comics' ),
        'edit_item'                  => __( 'Edit Issue', 'comixcore-comics' ),
        'update_item'                => __( 'Update Issue', 'comixcore-comics' ),
        'view_item'                  => __( 'View Issue', 'comixcore-comics' ),
        'separate_items_with_commas' => __( 'Separate issues with commas', 'comixcore-comics' ),
        'add_or_remove_items'        => __( 'Add or remove issues', 'comixcore-comics' ),
        'choose_from_most_used'      => __( 'Choose from the most used issues', 'comixcore-comics' ),
        'popular_items'              => __( 'Popular Issues', 'comixcore-comics' ),
        'search_items'               => __( 'Search Issues', 'comixcore-comics' ),
        'not_found'                  => __( 'Not Found', 'comixcore-comics' ),
        'no_terms'                   => __( 'No issues', 'comixcore-comics' ),
        'items_list'                 => __( 'Issues list', 'comixcore-comics' ),
        'items_list_navigation'      => __( 'Issues list navigation', 'comixcore-comics' ),
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
add_action( 'init', 'comixcore_comics_register_comic_assets', 0 ); // Keep the hook and priority