<?php
/**
 * Template part for displaying header builder by Elementor.
 *
 * @package Mindverse
 */
// HTML
if($layout_id <= 0) {
    return;
}
$style = get_post_meta($layout_id, 'header_type', true);
$style_class = ( $style === 'transparent' ) ? ' header-transparent' : '';
?>
<header id="headerDesktop" class="header header-desktop<?php echo esc_attr($style_class); ?>" data-layout="builder">
    <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $layout_id ); ?>
</header>