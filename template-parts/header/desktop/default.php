<?php
/**
 * Template part for displaying header default.
 *
 * @package Mytheme
 */

?>

<header id="headerDesktop" class="header header-desktop" data-layout="default">
    <div class="container">
        <div class="inner">
            <div class="header-logo">
                <?php echo wp_kses_post($logo_html); ?>
            </div>
            <div class="header-navigation">
                <?php  
                if ( has_nav_menu( 'primary' ) ) {
                    mytheme()->component->get_navigation_menu([
                        'menu_class' => 'header-menu navigation-menu',
                    ]);
                } else { 
                    printf(
                        '<ul class="header-menu header-menu-empty"><li><a href="%1$s">%2$s</a></li></ul>',
                        esc_url( admin_url( 'nav-menus.php' ) ),
                        esc_html__( 'Create New Menu', 'mytheme' )
                    );
                }
                ?>
            </div>
        </div>
    </div>
</header>