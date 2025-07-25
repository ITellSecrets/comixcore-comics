<?php
/**
 * The template for displaying Comic Series archives.
 * This version is designed to display individual 'comic' posts belonging to the series,
 * without relying on Advanced Custom Fields (ACF) for content or relationships.
 *
 * @package ComixCore
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

        <?php

        if ( have_posts() ) :
        ?>

            <header class="page-header">
                <?php
                    // Get the current comic_series term (e.g., "Outcasts of Pacifica")
                    $current_series = get_queried_object();
                    if ( $current_series && ! is_wp_error( $current_series ) ) {
                        echo '<h1 class="page-title">' . esc_html( $current_series->name ) . '</h1>';
                        if ( ! empty( $current_series->description ) ) {
                            echo '<div class="taxonomy-description">' . wp_kses_post( $current_series->description ) . '</div>';
                        }

                        // Get the series logo ID
                        $series_logo_id = get_term_meta( $current_series->term_id, 'series_logo_id', true );
                        // **** MODIFIED SECTION FOR SERIES LOGO ****
                        if ( $series_logo_id ) {
                            echo '<div class="series-logo-container">';
                            echo wp_get_attachment_image(
                                $series_logo_id,
                                'series-logo', // Use your custom 'series-logo' size
                                false,
                                array( 'alt' => $current_series->name . ' Logo', 'class' => 'series-logo' )
                            );
                            echo '</div>';
                        }
                        // ****************************************

                    } else {
                        // Fallback if get_queried_object fails for some reason
                        the_archive_title( '<h1 class="page-title">', '</h1>' );
                        the_archive_description( '<div class="archive-description">', '</div>' );
                    }
                ?>
            </header>

            <div class="comic-issues-grid">
                <?php
                // --- START MODIFIED SECTION: Fetching only relevant comic issues ---
                // 1. Get IDs of 'comic' posts belonging to the current series
                $comic_post_ids_in_series = get_posts( array(
                    'post_type'      => 'comic',
                    'posts_per_page' => -1, // Get all posts
                    'fields'         => 'ids', // Only get post IDs for efficiency
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'comic_series',
                            'field'    => 'slug',
                            'terms'    => $current_series->slug,
                        ),
                    ),
                    'post_status'    => 'publish', // Only published comics
                ) );

                $issue_terms = array(); // Initialize an empty array for our issues

                if ( ! empty( $comic_post_ids_in_series ) ) {
                    // 2. Get all 'comic_issues' terms associated with these specific posts
                    $issue_terms = wp_get_object_terms( $comic_post_ids_in_series, 'comic_issues', array(
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => true, // Only show issues that have comic posts assigned to them
                    ) );

                    // Filter out issues that don't have an 'issue_cover_id' meta, if that's a strict requirement
                    // (This assumes all valid issues should have a cover)
                    $issue_terms = array_filter($issue_terms, function($term) {
                        return get_term_meta( $term->term_id, 'issue_cover_id', true );
                    });
                }
                // --- END MODIFIED SECTION ---

                if ( ! empty( $issue_terms ) && ! is_wp_error( $issue_terms ) ) :
                    foreach ( $issue_terms as $issue ) :
                        // Get the issue cover ID
                        $issue_cover_id = get_term_meta( $issue->term_id, 'issue_cover_id', true );
                        $issue_link = get_term_link( $issue );
                        if ( ! is_wp_error( $issue_link ) ) :
                            ?>
                            <div class="comic-issue-item">
                                <a href="<?php echo esc_url( $issue_link ); ?>">
                                    <?php
                                    // **** MODIFIED SECTION FOR ISSUE COVERS ****
                                    if ( $issue_cover_id ) :
                                        echo wp_get_attachment_image(
                                            $issue_cover_id,
                                            'issue-cover', // Use your custom 'issue-cover' size
                                            false,
                                            array( 'alt' => $issue->name . ' Cover', 'class' => 'issue-cover' )
                                        );
                                    endif;
                                    // ****************************************
                                    ?>
                                    <h3><?php echo esc_html( $issue->name ); ?></h3>
                                </a>
                            </div>
                            <?php
                        endif;
                    endforeach;
                else :
                    echo '<p>No issues found for this series yet.</p>';
                endif;
                ?>
            </div>

        <?php
        else : // This is the 'else' for the main 'if ( have_posts() )' on line 17
            get_template_part( 'template-parts/content', 'none' ); // Displays content-none.php if no posts are found
        endif; // This is the 'endif' for the main 'if ( have_posts() )' on line 17
        ?>

        </main>
    </div>
<?php
get_sidebar();
get_footer();
?>