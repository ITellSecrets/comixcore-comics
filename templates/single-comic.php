<?php
/**
 * The template for displaying all single comic posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package ComixCore
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

        <?php
        while ( have_posts() ) :
            the_post();

            // Get Series terms
            $series_terms = get_the_terms( get_the_ID(), 'comic_series' );
            $series_name = '';
            if ( ! is_wp_error( $series_terms ) && ! empty( $series_terms ) ) {
                $series_name = $series_terms[0]->name; // Assuming one series per comic
            }

            // Get Issue terms
            $issue_terms = get_the_terms( get_the_ID(), 'comic_issues' );
            $issue_name = '';
            if ( ! is_wp_error( $issue_terms ) && ! empty( $issue_terms ) ) {
                $current_issue_term = $issue_terms[0]; // Assuming one issue per comic page
                $issue_name = $current_issue_term->name;
            }

            // Get custom field values using native WordPress get_post_meta()
            $comic_page_image_id = get_post_meta( get_the_ID(), '_comic_page_image_id', true );
            $comic_page_number = get_post_meta( get_the_ID(), '_comic_page_number', true );
            $display_style = get_post_meta( get_the_ID(), '_comic_display_style', true );
            // Default display style if not set
            if ( empty( $display_style ) ) {
                $display_style = 'page';
            }

            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php
                    // The comic meta information now serves as the primary page header (H4)
                    echo '<h4 class="comic-meta entry-title">';
                    $meta_parts_html = [];

                    if ( $series_name ) {
                        $meta_parts_html[] = '<span class="comic-series">Series: ' . esc_html( $series_name ) . '</span>';
                    }
                    if ( $issue_name ) {
                        $meta_parts_html[] = '<span class="comic-issue">Issue: ' . esc_html( $issue_name ) . '</span>';
                    }

                    // Only show page number if NOT in 'vertical' display style
                    if ( $display_style !== 'vertical' && $comic_page_number !== false && $comic_page_number !== null && $comic_page_number !== '' ) {
                        if ( $comic_page_number == 0 ) {
                            $meta_parts_html[] = '<span class="comic-page-number">Cover</span>';
                        } else {
                            $meta_parts_html[] = '<span class="comic-page-number">Page: ' . esc_html( $comic_page_number ) . '</span>';
                        }
                    }

                    // Output each part, adding a separator span between them
                    $count = count($meta_parts_html);
                    for ($i = 0; $i < $count; $i++) {
                        echo $meta_parts_html[$i];
                        if ($i < $count - 1) {
                            echo '<span class="meta-separator"> &mdash; </span>';
                        }
                    }
                    echo '</h4>';
                    ?>
                </header><div class="entry-content">
                    <?php
                    // Check if display style is 'vertical'
                    if ( $display_style === 'vertical' ) {
                        // If 'vertical' mode, assume images are in the main post content (using Gallery Block or Image Blocks)
                        echo '<div class="comic-vertical-scroll-wrapper">';
                        the_content(); // This will output the WordPress editor content (your gallery/images)
                        echo '</div>';
                    } else {

                        if ( $comic_page_image_id ) {
                            echo '<div class="comic-image-wrapper">';
                            // Use wp_get_attachment_image() with your custom 'comic-full' size
                            // This will automatically add srcset, sizes, and loading="lazy"
                            echo wp_get_attachment_image(
                                $comic_page_image_id,
                                'comic-full', // Use your custom size defined in functions.php
                                false,        // Not an icon
                                array( 'class' => 'comic-page-image' ) // Add your CSS class
                            );
                            echo '</div>';
                        }

                        // IMPORTANT: If you want any text/content placed in the main WordPress editor
                        // to appear BELOW the image for single-page comics, uncomment the line below.
                        // For now, it's assumed single-page comics rely purely on the native custom image.
                        // the_content();
                    }
                    ?>
                </div>
                <footer class="entry-footer">
                    <nav class="comic-navigation">
                    <?php
                    // Get the current issue term ID to properly query previous/next posts within the same issue
                    $current_comic_issues_terms = get_the_terms( get_the_ID(), 'comic_issues' );
                    $current_comic_issue_id = 0;
                    if ( ! is_wp_error( $current_comic_issues_terms ) && ! empty( $current_comic_issues_terms ) ) {
                        $current_comic_issue_id = $current_comic_issues_terms[0]->term_id; // Assuming one issue per comic page
                    }

                    $prev_post_link = '';
                    $next_post_link = '';

                    if ( $current_comic_issue_id ) {
                        // Get all comic pages for the current issue, ordered by menu_order
                        // We fetch only IDs for efficiency
                        $all_comic_pages_in_issue_ids = get_posts( array(
                            'post_type'      => 'comic',
                            'posts_per_page' => -1, // Get all posts

                            'orderby'        => 'menu_order', // Order by the built-in menu_order
                            'order'          => 'ASC',        // Keep this as ascending for natural sequence (1, 2, 3...)
                            'tax_query'      => array(
                                array(
                                    'taxonomy' => 'comic_issues',
                                    'field'    => 'term_id',
                                    'terms'    => $current_comic_issue_id,
                                ),
                            ),
                            'post_status'    => 'publish', // Only consider published comics
                            'fields'         => 'ids', // Only need post IDs
                        ) );

                        $current_post_id = get_the_ID();
                        // Find the index of the current post in the ordered list
                        $current_index = array_search( $current_post_id, $all_comic_pages_in_issue_ids );

                        if ( $current_index !== false ) {
                            // Check for previous page
                            if ( isset( $all_comic_pages_in_issue_ids[ $current_index - 1 ] ) ) {
                                $prev_post_link = get_permalink( $all_comic_pages_in_issue_ids[ $current_index - 1 ] );
                            }

                            // Check for next page
                            if ( isset( $all_comic_pages_in_issue_ids[ $current_index + 1 ] ) ) {
                                $next_post_link = get_permalink( $all_comic_pages_in_issue_ids[ $current_index + 1 ] );
                            }
                        }
                    }

                    // Determine if previous or next comics exist to conditionally add placeholder
                    $prev_comic_exists = !empty($prev_post_link);
                    $next_comic_exists = !empty($next_post_link);

                    // If only the 'Next Page' link exists, add an empty div to push it to the right
                    if ( !$prev_comic_exists && $next_comic_exists ) {
                        echo '<div></div>'; // This empty div acts as a spacer
                    }

                    if( $prev_comic_exists ) {
                        echo '<span class="nav-previous"><a href="' . esc_url($prev_post_link) . '" rel="prev">&larr; Previous Page</a></span>';
                    }

                    if( $next_comic_exists ) {
                        echo '<span class="nav-next"><a href="' . esc_url($next_post_link) . '" rel="next">Next Page &rarr;</a></span>';
                    }
                    ?>
                </nav>
                </footer>
            </article>
        <?php
        endwhile; // End of the loop.
        ?>

        </main>
    </div>
<?php
get_sidebar(); // If you want to include your sidebar on comic pages
get_footer();
?>