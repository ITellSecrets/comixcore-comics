<?php
/**
 * The template for displaying comic_series archives.
 * This page displays a grid of issues (terms) belonging to the current series.
 *
 * @package ComixCore
 */

get_header();

// Get the current comic_series term object
$current_series = get_queried_object();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php if ( $current_series ) : ?>
            <header class="page-header">
                <h1 class="page-title">
                    <?php echo esc_html( $current_series->name ); ?> Series
                </h1>
                <?php if ( $current_series->description ) : ?>
                    <div class="taxonomy-description">
                        <?php echo wpautop( esc_html( $current_series->description ) ); ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="comic-issue-grid">
                <?php
                // To get issues belonging to this series, we first find all comic posts
                // that are assigned to the current series, then extract their unique issue terms.
                $comics_in_series_args = array(
                    'post_type'      => 'comic',
                    'posts_per_page' => -1, // Get all comics
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'comic_series',
                            'field'    => 'term_id',
                            'terms'    => $current_series->term_id,
                        ),
                    ),
                    'fields'         => 'ids', // Only need the post IDs
                    'post_status'    => 'publish', // Only consider published comics
                );
                $comics_query = new WP_Query( $comics_in_series_args );

                $issue_term_ids = array();
                if ( $comics_query->have_posts() ) {
                    foreach ( $comics_query->posts as $comic_id ) {
                        $terms = get_the_terms( $comic_id, 'comic_issues' );
                        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                            foreach ( $terms as $term ) {
                                $issue_term_ids[] = $term->term_id;
                            }
                        }
                    }
                    $issue_term_ids = array_unique( $issue_term_ids ); // Get only unique issue IDs
                }
                wp_reset_postdata(); // Important: Reset post data after custom queries

                $issues_to_display = array();
                if ( ! empty( $issue_term_ids ) ) {
                    // Now, query for the actual issue terms based on the collected IDs
                    $issues_to_display = get_terms( array(
                        'taxonomy'   => 'comic_issues',
                        'include'    => $issue_term_ids,
                        'hide_empty' => true, // Ensure only issues with comics are shown
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                }

                if ( ! empty( $issues_to_display ) && ! is_wp_error( $issues_to_display ) ) :
                    foreach ( $issues_to_display as $issue ) :
                        // Get the issue cover ID (meta-box.php defines 'issue_cover_id')
                        $issue_cover_id = get_term_meta( $issue->term_id, 'issue_cover_id', true );

                        // Get the permalink for the individual issue archive page
                        $issue_link = get_term_link( $issue );

                        if ( ! is_wp_error( $issue_link ) ) :
                            ?>
                            <div class="comic-issue-item">
                                <a href="<?php echo esc_url( $issue_link ); ?>">
                                    <?php if ( $issue_cover_id ) : ?>
                                        <?php
                                        // Display the issue cover image using the 'issue-cover' size
                                        echo wp_get_attachment_image(
                                            $issue_cover_id,
                                            'issue-cover', // Uses 'issue-cover' size from image-sizes.php
                                            false,
                                            array(
                                                'alt' => $issue->name . ' Cover', // Set alt text for accessibility
                                                'class' => 'issue-cover-grid' // Your CSS class for styling
                                            )
                                        );
                                        ?>
                                        <h2 class="issue-title-in-grid"><?php echo esc_html( $issue->name ); ?></h2>
                                    <?php else : // Fallback if no cover is set ?>
                                        <h2 class="issue-title-in-grid"><?php echo esc_html( $issue->name ); ?></h2>
                                    <?php endif; ?>

                                    <?php
                                    // Get and display the issue description
                                    if ( ! empty( $issue->description ) ) : ?>
                                        <p class="issue-description-in-grid"><?php echo wpautop( esc_html( $issue->description ) ); ?></p>
                                    <?php endif; ?>
                                </a>
                            </div><?php
                        endif;
                    endforeach;
                else :
                    // No issues found for this series
                    echo '<p>No issues found for this series yet. Please ensure comics are assigned to issues, and issues are assigned to this series.</p>';
                endif;
                ?>
            </div>

        <?php else : ?>
            <p>No series information available.</p>
        <?php endif; ?>

    </main>
</div>

<?php get_footer(); ?>