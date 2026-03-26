<?php
use \Mindverse\Inc\Integrations\Elementor\Elementor_Helpers;
$post_id = get_the_ID();
$img_width = null;
$img_height = null;
$img_attrs = [];
$thumbnail_id = get_post_thumbnail_id( $post_id );
$title_tag = 'h3';
$author_id = get_post_field( 'post_author', $post_id );
$author_name = get_the_author_meta( 'display_name', $author_id );
$author_url = get_author_posts_url( $author_id ); 
?>
<div class="grid-item">
    <div id="post-id-<?php echo esc_attr($post_id); ?>" <?php post_class('post'); ?>>
        <div class="post-featured-image image">
            <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                <?php //Elementor_Helpers::the_image_to_size($thumbnail_id, $img_width, $img_height, $img_attrs); ?>
            </a>
        </div>
        <div class="post-content">
            <div class="post-date"><?php echo get_the_date( 'd M Y', $post_id ); ?></div>
            <<?php echo esc_attr( $title_tag ); ?> class="post-title">
                <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" 
                <?php if( isset( $title_hover_style ) ) : ?> data-hover="<?php echo esc_attr($title_hover_style); ?>" <?php endif; ?>
                <?php if( isset($title_hover_style) && $title_hover_style === 'text-flip-3d' ) : ?> data-text="<?php echo get_the_title( $post_id ); ?>" <?php endif; ?>>
                    <span><?php echo get_the_title( $post_id ); ?></span>
                </a>
            </<?php echo esc_attr( $title_tag ); ?>>
            <p class="post-excerpt">
                <?php echo wp_trim_words( $post->post_excerpt, 20, $more = null); ?>
            </p>
            <div class="post-author">
                <?php echo esc_html__( 'By', 'mindverse' ); ?>
                <a href="<?php echo esc_url( $author_url ); ?>">
                    <?php echo esc_html( $author_name ); ?>
                </a>
            </div>
        </div>
    </div>
</div>