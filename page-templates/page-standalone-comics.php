<?php
/**
 * Template Name: Standalone Comics Page (ComixCore)
 * Description: Displays comic posts that are not assigned to any issue.
 */
get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="page-header">
            <h1 class="page-title"><?php single_post_title(); ?></h1>
        </header>

        <div class="standalone-comics-grid">
            <?php
            $standalone_args = array(
                'post_type'      => 'comic',
                'posts_per_page' => -1, // Show all
                'orderby'        => 'date',
                'order'          => 'DESC',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'comic_issues',
                        'field'    => 'slug',
                        'terms'    => '', // Empty terms means posts not in this taxonomy
                        'operator' => 'NOT IN', // This checks for posts NOT in any 'comic_issues' term
                    ),
                ),
                'meta_query' => array(
                    array(
                        'key' => '_comic_page_content_type',
                        'value' => 'comic-page', // Only show actual comic pages, not covers/epilogues intended for issues
                        'compare' => '=',
                    ),
                ),
            );

            $standalone_query = new WP_Query( $standalone_args );

            if ( $standalone_query->have_posts() ) :
                while ( $standalone_query->have_posts() ) : $standalone_query->the_post();
                    $comic_page_image_id = get_post_meta( get_the_ID(), '_comic_page_image_id', true );
                    ?>
                    <div class="standalone-comic-item">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( $comic_page_image_id ) : ?>
                                <?php echo wp_get_attachment_image( $comic_page_image_id, 'comic-medium', false, array( 'alt' => get_the_title() ) ); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url( COMIXCORE_COMICS_PLUGIN_URL . 'assets/images/placeholder-comic.png' ); ?>" alt="<?php esc_attr_e( 'No image available', 'comixcore-comics' ); ?>" />
                            <?php endif; ?>
                            <h3><?php the_title(); ?></h3>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>' . esc_html__( 'No standalone comics found.', 'comixcore-comics' ) . '</p>';
            endif;
            ?>
        </div>
    </main>
</div>

<?php get_footer();