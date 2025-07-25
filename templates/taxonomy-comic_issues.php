<?php
/**
 * The template for displaying comic_issues archives (full issue view).
 * This page will either redirect to the first comic page, or display
 * all comic pages in a vertical scroll, based on the display style
 * of the first comic post in the issue.
 */

// Get the current comic_issues term object from the URL
$current_issue = get_queried_object();

// Determine the display style for this issue based on its first comic page's setting
$issue_display_style = 'page_by_page'; // Default to page by page

if ( $current_issue ) {
    // Query for the FIRST comic page of this issue by _comic_page_number
    $first_comic_args = array(
        'post_type'      => 'comic',
        'posts_per_page' => 1,
        'meta_key'       => '_comic_page_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'comic_issues',
                'field'    => 'term_id',
                'terms'    => $current_issue->term_id,
            ),
        ),
        'post_status'    => 'publish', // Only consider published published comics
        'fields'         => 'ids', // Only need the ID to get meta
    );

    $first_comic_query = new WP_Query( $first_comic_args );

    if ( $first_comic_query->have_posts() ) {
        $first_comic_id = $first_comic_query->posts[0];
        // Get the '_comic_display_style' from the first comic post
        $first_comic_display_style = get_post_meta( $first_comic_id, '_comic_display_style', true );

        if ( ! empty( $first_comic_display_style ) ) {
            $issue_display_style = $first_comic_display_style;
        }
    }
    wp_reset_postdata(); // Reset query for safety
}

// --- Display Logic Starts Here ---
if ( $issue_display_style === 'vertical_scroll' ) {
    get_header(); // Include header for vertical scroll display
    ?>
    <div id="primary" class="content-area full-issue-view vertical-scroll-issue">
        <main id="main" class="site-main">
            <?php if ( $current_issue ) : ?>
                <header class="page-header">
                    <h1 class="page-title full-issue-title">
                        <?php echo esc_html( $current_issue->name ); ?>
                    </h1>
                    <?php if ( $current_issue->description ) : ?>
                        <div class="taxonomy-description full-issue-description">
                            <?php echo wpautop( $current_issue->description ); ?>
                        </div>
                    <?php endif; ?>
                    </header>

                <div class="comic-vertical-scroll-wrapper">
                    <?php
                    // Query all comic posts for this issue, ordered by page number
                    $args = array(
                        'post_type'      => 'comic',
                        'posts_per_page' => -1, // Get all pages
                        'meta_key'       => '_comic_page_number',
                        'orderby'        => 'meta_value_num',
                        'order'          => 'ASC',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'comic_issues',
                                'field'    => 'term_id',
                                'terms'    => $current_issue->term_id,
                            ),
                        ),
                        'post_status'    => 'publish',
                    );

                    $all_comic_pages_query = new WP_Query( $args );

                    if ( $all_comic_pages_query->have_posts() ) {
                        while ( $all_comic_pages_query->have_posts() ) {
                            $all_comic_pages_query->the_post();
                            $comic_page_image_id = get_post_meta( get_the_ID(), '_comic_page_image_id', true );

                            if ( $comic_page_image_id ) {
                                // **** THIS IS THE MODIFIED SECTION ****
                                echo '<div class="single-comic-page-in-vertical-issue">';
                                echo wp_get_attachment_image(
                                    $comic_page_image_id,
                                    'comic-full', // Use your custom size for full comic pages
                                    false,
                                    array( 'class' => 'comic-page-image' ) // Keep your CSS class
                                );
                                echo '</div>';
                                // ************************************
                            } else {
                                // Fallback for missing image
                                ?>
                                <div class="comic-image-placeholder">
                                    <p>Image missing for <?php the_title(); ?>!</p>
                                </div>
                                <?php
                            }
                        }
                        wp_reset_postdata();
                    } else {
                        // No comic pages found for this issue
                        ?>
                        <p class="no-comic-pages-found">No comic pages found for this issue yet. Please add comic posts and assign them to this issue.</p>
                        <?php
                    }
                    ?>
                </div><?php else : ?>
                <p>No issue information available.</p>
            <?php endif; ?>
        </main>
    </div>
    <?php
    get_footer(); // Includes your theme's footer

} else { // issue_display_style is 'page_by_page' or not set (default)

    // --- ORIGINAL REDIRECTION LOGIC (modified to use _comic_page_number) ---
    if ( $current_issue ) {
        // Arguments to query for the FIRST comic page of this issue by _comic_page_number
        $args = array(
            'post_type'      => 'comic',
            'posts_per_page' => 1,
            'meta_key'       => '_comic_page_number',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'comic_issues',
                    'field'    => 'term_id',
                    'terms'    => $current_issue->term_id,
                ),
            ),
            'post_status'    => array('publish', 'private'),
        );

        $first_comic_page_query = new WP_Query( $args );

        if ( $first_comic_page_query->have_posts() ) {
            $first_comic_page_query->the_post(); // Set up post data for the first comic post found
            $first_page_permalink = get_permalink(); // Get the permalink
            wp_reset_postdata(); // Reset post data before redirection

            // Perform the redirection to the first comic page's URL
            wp_redirect( $first_page_permalink );
            exit();
        }
    }

    // If we reach this point (no redirection), display fallback
    get_header();
    ?>
    <div id="primary" class="content-area full-issue-view">
        <main id="main" class="site-main">
            <?php if ( $current_issue ) : ?>
                <header class="page-header">
                    <h1 class="page-title full-issue-title">
                        <?php echo esc_html( $current_issue->name ); ?>
                    </h1>
                    <?php if ( $current_issue->description ) : ?>
                        <div class="taxonomy-description full-issue-description">
                            <?php echo wpautop( $current_issue->description ); ?>
                        </div>
                    <?php endif; ?>
                    </header>
                <p class="no-comic-pages-found">No comic pages found for this issue yet. Please add comic posts and assign them to this issue, or the redirection to the first page failed.</p>
            <?php else : ?>
                <p>No issue information available.</p>
            <?php endif; ?>
        </main>
    </div>
    <?php
    get_footer();
}
// --- Display Logic Ends Here ---
?>