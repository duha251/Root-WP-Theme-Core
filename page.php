<?php
/**
 * The template for displaying all pages
 *
 * @package Mytheme
 * @author Case-Themes
 * @link https://mytheme.casethemes.net
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<main id="main" class="main">
    <?php while ( have_posts() ) :
        the_post();

        the_content();

        wp_link_pages([
            'before'      => '<div class="page-links">',
            'after'       => '</div>',
            'link_before' => '<span>',
            'link_after'  => '</span>',
        ]);

    endwhile; 
    
    if ( comments_open() || get_comments_number() ) {
        comments_template();
    } ?>
</main>

<?php get_footer(); ?>
