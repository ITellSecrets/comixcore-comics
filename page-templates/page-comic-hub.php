<?php
/**
 * Template Name: Comic Hub Page
 * Description: Displays a list of all comic series with their logos.
 *
 * @package ComixCore
 */

get_header(); // Loads the header.php template

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <header class="page-header">
            <h1 class="page-title"><?php single_post_title(); ?></h1>
        </header>
        <div class="comic-series-grid">
            <?php
            // Get all terms from the 'comic_series' taxonomy
            $series_terms = get_terms( array(
                'taxonomy'   => 'comic_series',
                'hide_empty' => true, // Only show series that have comics assigned
                'orderby'    => 'name',
                'order'      => 'ASC',
            ) );

            if ( ! empty( $series_terms ) && ! is_wp_error( $series_terms ) ) :
                foreach ( $series_terms as $series ) :
                    // Get the series logo ID using get_term_meta()
                    $series_logo_id = get_term_meta( $series->term_id, 'series_logo_id', true );

                    // Get the permalink for the series archive page
                    $series_link = get_term_link( $series );

                    if ( ! is_wp_error( $series_link ) ) :
                        ?>
                        <div class="comic-series-item">
                                <a href="<?php echo esc_url( $series_link ); ?>">
                                    <?php
                                    // **** MODIFIED SECTION FOR SERIES LOGO ****
                                    if ( $series_logo_id ) : // Check if a logo ID exists
                                        // Use wp_get_attachment_image() with your custom 'series-logo' size
                                        echo wp_get_attachment_image(
                                            $series_logo_id,
                                            'series-logo', // Use your custom 'series-logo' size defined in functions.php
                                            false,
                                            array(
                                                'alt' => $series->name . ' Logo', // Set alt text for accessibility
                                                'class' => 'series-logo' // Your CSS class
                                            )
                                        );
                                        ?>
                                        <h2 class="series-title-under-logo"><?php echo esc_html( $series->name ); ?></h2>
                                    <?php else : // Fallback if no logo is set ?>
                                        <h2><?php echo esc_html( $series->name ); ?></h2>
                                    <?php endif;
                                    // ****************************************
                                    ?>
                                </a>
                            </div><?php
                    endif;
                endforeach;
            else :
                // No series found
                echo '<p>No comic series found.</p>';
            endif;
            ?>
        </div>
    </main>
</div>
<?php get_footer(); // Loads the footer.php template ?>