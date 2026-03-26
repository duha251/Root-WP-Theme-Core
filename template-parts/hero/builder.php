<?php
if($layout_id <= 0) {
    return;
}
?>
<section id="hero" class="hero" data-layout="builder">
    <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $layout_id ); ?>
</section>

