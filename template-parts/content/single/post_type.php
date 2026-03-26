<?php

$post_type = $args['post_type'];

$post_header_template_id = (int) mindverse()->get_theme_option('single_' . $post_type . '_header_template_id', 0);
$post_footer_template_id = (int) mindverse()->get_theme_option('single_' . $post_type . '_footer_template_id', 0);
$post_before_template_id = (int) mindverse()->get_theme_option('single_' . $post_type . '_before_template_id', 0);
$post_after_template_id = (int) mindverse()->get_theme_option('single_' . $post_type . '_after_template_id', 0);

?>

<?php get_header(); ?>

<main id="main">
    <?php
        if( $post_before_template_id !== 0 ) {
            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_before_template_id );
        }
    ?>
    <div class="inner">
        <div class="content-area">            
            <?php while ( have_posts() ) : ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                        if( $post_header_template_id !== 0 ) {
                            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_header_template_id );
                        }
                        the_post();

                        the_content();

                        wp_link_pages([
                            'before'      => '<div class="page-links">Pages:',
                            'after'       => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ]); 

                        if( $post_footer_template_id !== 0 ) {
                            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_footer_template_id );
                        }
                    ?>
                    <div class="post-tags" style="display:none;">
                        <?php the_tags(); ?>
                    </div>
                </article>
                <?php
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
            endwhile; ?>
        </div>
    </div>
    <?php
        if( $post_after_template_id !== 0 ) {
            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_after_template_id );
        }
    ?>
</main>


<?php get_footer(); ?>