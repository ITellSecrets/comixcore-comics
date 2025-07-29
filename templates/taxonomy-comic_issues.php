<?php
/**
 * The template for displaying comic_issues archives (full issue view).
 * This page will either redirect to the first comic page, or display
 * all comic pages in a vertical scroll, based on the display style
 * of the issue term itself.
 */

// Get the current comic_issues term object from the URL
$current_issue = get_queried_object();

// Determine the display style for this issue based on its setting on the issue term
$issue_display_style = 'page_by_page'; // Default to page by page

if ( $current_issue ) {
    // Get the display style directly from the current issue (taxonomy term) meta
    // Using the correctly identified meta key '_issue_display_style'
    $term_display_style = get_term_meta( $current_issue->term_id, '_issue_display_style', true ); 

    if ( ! empty( $term_display_style ) ) {
        $issue_display_style = $term_display_style;
    }

    // Query for the FIRST comic page of this issue by _comic_page_number.
    // This is still needed to get the $first_comic_id for potential redirection.
    $first_comic_args = array(
        'post_type'      => 'comic',
        'posts_per_page' => 1,
        'meta_key'       => '_comixcore-comics_comic_page_number', 
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
        'fields'         => 'ids', 
    );

    $first_comic_query = new WP_Query( $first_comic_args );
    $first_comic_id = null; 

    if ( $first_comic_query->have_posts() ) {
        $first_comic_id = $first_comic_query->posts[0];
    }
    wp_reset_postdata(); 

    // If the issue's display style is 'page_by_page' AND we found a first comic page, redirect.
    if ( 'page_by_page' === $issue_display_style && ! empty( $first_comic_id ) ) {
        $first_page_permalink = get_permalink( $first_comic_id ); 
        if ( $first_page_permalink ) {
            wp_redirect( $first_page_permalink );
            exit();
        }
    }
}

// --- Display Logic for 'Scrolling' or if no redirection occurred ---
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

            <?php
            // Query to get ALL comic posts for the current issue for vertical scrolling
            $all_comics_in_issue_args = array(
                'post_type'      => 'comic',
                'posts_per_page' => -1, 
                'meta_key'       => '_comixcore-comics_comic_page_number', 
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

            $all_comics_query = new WP_Query( $all_comics_in_issue_args );

            if ( $all_comics_query->have_posts() ) :
                echo '<div class="comic-pages-scrolling-container">'; 
                while ( $all_comics_query->have_posts() ) : $all_comics_query->the_post();
                    $comic_page_image_id = get_post_meta( get_the_ID(), '_comixcore-comics_comic_page_image_id', true ); 

                    if ( $comic_page_image_id ) {
                        echo '<div class="comic-page-scrolling-item">';
                        echo wp_get_attachment_image( $comic_page_image_id, 'comic-full', false, array( 'class' => 'comic-page-scrolling-image' ) );
                        echo '</div>';
                    }
                endwhile;
                echo '</div>'; 
                wp_reset_postdata(); 
            else :
                echo '<p class="no-comic-pages-found">No comic pages found for this issue yet. Please add comic posts and assign them to this issue, and ensure they are published.</p>';
            endif;
            ?>
        <?php else : ?>
            <p>No issue information available.</p>
        <?php endif; ?>
    </main>
</div>
<?php
get_footer();
?>