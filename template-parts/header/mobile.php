<?php

extract( $args );

$header_mobible_logo_html = (!empty($logo['url'])) 
    ? sprintf(
        '<a href="%1$s" title="%2$s" rel="home"><img src="%3$s" alt="%2$s"  style="height:%4$s;"/></a>',
        esc_url( home_url( '/' ) ),
        esc_attr( get_bloginfo( 'name' ) ),
        esc_url( $logo['url'] ),
        (  $logo_h['height']  ?? ''  )
    ) : '';
$layout_attr = $layout !== 0 ? 'builder' : 'default';
?>
<header id="header-mobile" class="header header-mobible">
    <div class="container">
        <div class="inner">
            <div class="header-logo">
                <?php echo wp_kses_post($header_mobible_logo_html); ?>
            </div>
            <button class="button button-hamburger" data-target=".header-drawer">
                <span class="icon-hamburger">
                    <span class="line line-1"></span>
                    <span class="line line-2"></span>
                    <span class="line line-3"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="header-drawer drawer" data-layout="<?php echo esc_attr($layout_attr); ?>">
        <button class="button-close"><span class="icon-close"></span></button>
        <?php if( $layout !== 0 ) : ?>
            <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $layout ); ?>
        <?php else : ?>
            <div class="header-logo">
                <?php echo wp_kses_post($header_mobible_logo_html); ?>
            </div>
            <div class="header-searchform">
                <?php mindverse()->layout->the_get_search_form(); ?>
            </div>
            <hr class="header-divider">
            <div class="header-navigation">
                <?php  if( has_nav_menu('primary-mobile') ) {
                    mindverse()->layout->get_nav_menu(['theme_location' => 'primary-mobile']);
                } elseif( has_nav_menu('primary') ) {
                    mindverse()->layout->get_nav_menu();
                } else {
                    mindverse()->layout->get_nav_menu(['menu' => 'empty']);
                } ?>
            </div>
        <?php endif; ?>
    </div>
</header>

