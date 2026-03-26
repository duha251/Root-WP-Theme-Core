<?php

extract( $args );

if( $layout <= 0 ) {
    return;
}

?>
<div id="header-sticky" class="header header-sticky" data-scroll="<?php echo esc_attr($scroll_direction); ?>" aria-hidden="true">
    <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $layout ); ?>
</div>
