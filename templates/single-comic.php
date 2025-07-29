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
            $current_issue_term = null; // Initialize to null
            if ( ! is_wp_error( $issue_terms ) && ! empty( $issue_terms ) ) {
                $current_issue_term = $issue_terms[0]; // Assuming one issue per comic page
                $issue_name = $current_issue_term->name;
            }

            // Get custom field values using native WordPress get_post_meta()
            // IMPORTANT: Using the corrected meta keys as found in your database
            $comic_page_image_id = get_post_meta( get_the_ID(), '_comixcore-comics_comic_page_image_id', true );
            $comic_page_number = get_post_meta( get_the_ID(), '_comixcore-comics_comic_page_number', true );
            $display_style = get_post_meta( get_the_ID(), '_comixcore-comics_comic_display_style', true );
            // Default display style if not set
            if ( empty( $display_style ) ) {
                $display_style = 'page';
            }

            // --- Logic to get all comic pages in the current issue for navigation ---
            // Get the ID of the currently displayed comic post
            $current_comic_id = get_the_ID();

            // Initialize variables for comic navigation
            $all_comic_pages_in_issue_ids = []; // IMPORTANT: Initialize as an empty array
            $current_index = false; // Initialize to false
            $prev_post_link = '';
            $next_post_link = '';

            // Ensure $current_issue_term is available
            if ( ! empty( $current_issue_term ) ) {
                $all_pages_args = array(
                    'post_type'      => 'comic',
                    'posts_per_page' => -1, // Get all posts in the issue
                    'orderby'        => 'meta_value_num',
                    'meta_key'       => '_comixcore-comics_comic_page_number', // Use the corrected meta key
                    'order'          => 'ASC',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'comic_issues',
                            'field'    => 'term_id',
                            'terms'    => $current_issue_term->term_id,
                        ),
                    ),
                    'post_status'    => 'publish', // Only consider published comics
                    'fields'         => 'ids', // Only get post IDs for performance
                );

                $all_pages_query = new WP_Query( $all_pages_args );

                if ( $all_pages_query->have_posts() ) {
                    $all_comic_pages_in_issue_ids = $all_pages_query->posts;
                    wp_reset_postdata(); // Reset post data after custom query
                }

                // Determine the index of the current comic page within the ordered list
                // This is crucial for calculating previous/next links
                $current_index = array_search( $current_comic_id, $all_comic_pages_in_issue_ids );

                // Determine previous/next comic links based on the ordered list
                if ( $current_index !== false ) { // If current comic is found in the list
                    if ( isset( $all_comic_pages_in_issue_ids[ $current_index - 1 ] ) ) {
                        $prev_post_link = get_permalink( $all_comic_pages_in_issue_ids[ $current_index - 1 ] );
                    }
                    if ( isset( $all_comic_pages_in_issue_ids[ $current_index + 1 ] ) ) {
                        $next_post_link = get_permalink( $all_comic_pages_in_issue_ids[ $current_index + 1 ] );
                    }
                }
            }

            // Determine if previous or next comics exist to conditionally add placeholder
            // These variables are used in the navigation section
            $prev_comic_exists = !empty($prev_post_link);
            $next_comic_exists = !empty($next_post_link);
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
                </header>

                <div class="entry-content">
                    <?php
                    // Check if display style is 'vertical'
                    if ( $display_style === 'vertical' ) {
                        // If 'vertical' mode, assume images are in the main post content (using Gallery Block or Image Blocks)
                        echo '<div class="comic-vertical-scroll-wrapper">';
                        the_content(); // This will output the WordPress editor content (your gallery/images)
                        echo '</div>';
                    } else {
                        // For 'page' or other styles, display the single comic page image
                        if ( $comic_page_image_id ) {
                            echo '<div class="comic-image-wrapper">';
                            // Use wp_get_attachment_image() with your custom 'comic-full' size
                            // This will automatically add srcset, sizes, and loading="lazy"
                            echo wp_get_attachment_image(
                                $comic_page_image_id,
                                'comic-full', // Use your custom size defined in functions.php
                                false,       // Not an icon
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
                        // Determine First Page and Last Page IDs and Permalinks
                        // These variables are safe to use here because they are populated above
                        $first_page_id = reset($all_comic_pages_in_issue_ids); // Get the first element
                        $last_page_id = end($all_comic_pages_in_issue_ids);   // Get the last element

                        $first_page_link = '';
                        if ( $first_page_id ) {
                            $first_page_link = get_permalink( $first_page_id );
                        }

                        $last_page_link = '';
                        if ( $last_page_id ) {
                            $last_page_link = get_permalink( $last_page_id );
                        }

                        // --- Navigation Links ---

                        // First Page link
                        // Only display if there's more than one page AND we are not on the very first page
                        if ( count($all_comic_pages_in_issue_ids) > 1 && $current_comic_id !== $first_page_id ) {
                            echo '<span class="nav-first"><a href="' . esc_url($first_page_link) . '" rel="first">&larr;&larr; First Page</a></span>';
                        }

                        // Previous Page link
                        // $prev_comic_exists should be true if $prev_post_link is not empty
                        if( !empty($prev_post_link) ) {
                            echo '<span class="nav-previous"><a href="' . esc_url($prev_post_link) . '" rel="prev">&larr; Prev Page</a></span>';
                        }

                        // If neither "First" nor "Previous" links are present, but "Next" or "Last" are, add a spacer for alignment
                        if ( (count($all_comic_pages_in_issue_ids) <= 1 || $current_comic_id === $first_page_id) && (!empty($next_post_link) || ($current_comic_id !== $last_page_id && count($all_comic_pages_in_issue_ids) > 1)) ) {
                            echo '<div class="nav-spacer"></div>'; // This empty div acts as a flexible spacer
                        }


                        // Next Page link
                        // $next_comic_exists should be true if $next_post_link is not empty
                        if( !empty($next_post_link) ) {
                            echo '<span class="nav-next"><a href="' . esc_url($next_post_link) . '" rel="next">Next Page &rarr;</a></span>';
                        }

                        // Last Page link
                        // Only display if there's more than one page AND we are not on the very last page
                        if ( count($all_comic_pages_in_issue_ids) > 1 && $current_comic_id !== $last_page_id ) {
                            echo '<span class="nav-last"><a href="' . esc_url($last_page_link) . '" rel="last">Last Page &rarr;&rarr;</a></span>';
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


     