<?php
namespace MyTheme\Inc\Frontend;

/**
 * Frontend Layout
 *
 * Handles layout structure for frontend.
 *
 * @package MyTheme
 */

use MyTheme\Inc\Core\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Layout {

    private $option;

    public function __construct( Option $option_instance ) {
        $this->option = $option_instance;
        add_action('mytheme_before_main_content', [$this, 'before_main_content_render']);
        add_action('mytheme_after_main_content', [$this, 'after_main_content_render']);
    }

    public function before_main_content_render() {
        $this->get_site_loader();
        $this->get_header();
        $this->get_hero();
    }

    public function after_main_content_render() {
        $this->get_footer();
    }

    public function get_header() {
        $prefix_id = '';
        $mode = $this->option->get_option( $prefix_id.'header_mode', 'default');
        $layout_id = $this->option->get_option( $prefix_id.'header_layout', 0);

        if( $mode === 'inherit' ) {
            $mode = $this->option->get_theme_option( $prefix_id.'header_mode', 'hide');
            $layout_id = $this->option->get_theme_option( $prefix_id.'header_layout', 0);
        }

        if( $mode === 'hide' || ( $mode === 'builder' && $layout_id === 0 ) ) {
            return '';
        }

        $is_builder = (
            $mode === 'builder' &&
            is_numeric($layout) &&
            $layout > 0 &&
            class_exists('Pxltheme_Core') &&
            class_exists('\Elementor\Plugin')
        );

        if( ! $is_builder ) {
            $logo = $this->option->get_theme_option('header_logo', ['url'=> get_template_directory_uri() . '/assets/img/site-logo.webp']);
            $logo_html = (!empty($logo['url'])) 
                ? sprintf(
                    '<a href="%1$s" title="%2$s" rel="home"><img src="%3$s" alt="%2$s"/></a>',
                    esc_url( home_url( '/' ) ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_url( $logo['url'] )
                ) : '';

            mytheme_get_template('template-parts/header/main/default',[
                'logo_html' => $logo_html
            ]);
            return;
        }

        mytheme_get_template('template-parts/header/main/builder', [
            'layout_id' => $layout_id,
        ]);
    }

    public function get_site_loader() {
        $enable_site_loader = (bool) $this->option->get_theme_option('site_loader', '');
        if ( !$enable_site_loader ) {
            return '';
        }
        $loader_image = $this->option->get_theme_option('loader_logo', []); ?>
        <div id="siteLoader" class="site-loader">
            <div class="site-loader-logo image">
                <?php echo wp_kses_post('<img src="' . esc_url( $loader_image['url'] ) . '" alt="Site Loader Logo">'); ?>
            </div>
        </div>
        <?php
    }

    public function get_back_to_top() {
        $enable_back_to_top = (bool) $this->option->get_theme_option('back_to_top', '');
        if( !$enable_back_to_top ) {
            return '';
        } ?>
        <button class="back-to-top">
            <span class="button-icon" data-loop-animation="bongBenhStop">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 12 12" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 10.375C6.09946 10.375 6.19484 10.3355 6.26516 10.2652C6.33549 10.1948 6.375 10.0995 6.375 10V5.375H5.625V10C5.625 10.207 5.793 10.375 6 10.375Z" fill="black"/>
                    <path d="M2.99967 5.37504C2.92555 5.37497 2.85311 5.35294 2.7915 5.31173C2.7299 5.27052 2.68188 5.21198 2.65353 5.1435C2.62517 5.07502 2.61775 4.99967 2.63219 4.92697C2.64663 4.85427 2.68229 4.78748 2.73467 4.73504L5.73467 1.73504C5.80498 1.66481 5.9003 1.62537 5.99967 1.62537C6.09905 1.62537 6.19436 1.66481 6.26467 1.73504L9.26467 4.73504C9.31705 4.78748 9.35271 4.85427 9.36715 4.92697C9.38159 4.99967 9.37417 5.07502 9.34581 5.1435C9.31746 5.21198 9.26944 5.27052 9.20784 5.31173C9.14623 5.35294 9.07379 5.37497 8.99967 5.37504H2.99967Z" fill="black"/>
                </svg>
            </span>
        </button>
        <?php
    }

    public function get_hero() {
        $prefix_id = '';
        $mode = $this->option->get_option( $prefix_id.'hero_mode', 'default');
        $layout_id = $this->option->get_option( $prefix_id.'hero_layout', 0);

        if( $mode === 'inherit' ) {
            $mode = $this->option->get_theme_option( $prefix_id.'hero_mode', 'hide');
            $layout_id = $this->option->get_theme_option( $prefix_id.'hero_layout', 0);
        }

        if( $mode === 'hide' || ( $mode === 'builder' && $layout_id === 0 ) ) {
            return '';
        }

        $is_builder = (
            $mode === 'builder' &&
            is_numeric($layout) &&
            $layout > 0 &&
            class_exists('Pxltheme_Core') &&
            class_exists('\Elementor\Plugin')
        );

        if( ! $is_builder ) {
            mytheme_get_template('template-parts/hero/default',[
                'title' => 'Okokokok'
            ]);
            return;
        }

        mytheme_get_template('template-parts/hero/builder', [
            'layout_id' => $layout_id,
        ]);
    }

    public function get_footer() {
        $mode   = $this->option->get_option('footer_mode', 'inherit');
        $layout_id_id = $this->option->get_option('footer_layout', 'hide');

        if ( $mode === 'inherit' ) {
            $mode   = $this->option->get_theme_option('footer_mode', '');
            $layout_id = $this->option->get_theme_option('footer_layout', 0);
        }

        if ( $mode === 'hide' || ( $mode === 'builder' && $layout_id <= 0 ) ) {
            return;
        }

        $is_builder = (
            $mode === 'builder' &&
            is_numeric($layout_id) &&
            class_exists('Pxltheme_Core') &&
            method_exists('\Elementor\Plugin', 'instance')
        );

        if( ! $is_builder ) {
            mytheme_get_template('template-parts/footer/default', []);
            return;
        }

        mytheme_get_template('template-parts/footer/builder', [
            'layout_id' => $layout_id,
        ]);
    }

    public function get_navigation_menu($args = []) {
        if( has_nav_menu('primary') || ( isset($args['menu']) && $args['menu'] !== 'empty') ) :
            $menu_icon = '<span class="menu-link-icon menu-link-icon--desktop">';
            if( !empty($args['menu_icon']['value']) ) {
                ob_start();
                \Elementor\Icons_Manager::render_icon( $args['menu_icon'], [ 'aria-hidden' => 'true' ] );
                $menu_icon .= ob_get_clean();
            }else {
                $menu_icon .= '<svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 12 8" fill="none">
                                    <path d="M10.1094 0L5.78125 4.32812L1.4375 0L0 1.4375L5.76562 7.5L11.5469 1.4375L10.1094 0Z" fill="white"/>
                                </svg>';
            }
            $menu_icon .= '</span>';
            wp_nav_menu(
                array_merge(
                    array(
                        'theme_location' => 'primary',
                        'container'      => '',
                        'menu_id'        => '',
                        'menu_class'     => 'header-menu menu-primary',
                        'before'         => '',
                        'after'          => '',
                        'link_before'    => '<span class="menu-link-inner">
                                                <span class="menu-link-text">',
                        'link_after'     => '   </span>
                                                <span class="menu-link-icon menu-link-icon--mobile"><span class="icon-plus"></span>
                                                </span>'.
                                                $menu_icon .
                                            '</span>',
                        'walker'         => class_exists( 'PXL_Mega_Menu_Walker' ) ? new \PXL_Mega_Menu_Walker : '',
                    ),
                    $args,
                )
            );
        else : ?>
            <ul class="header-menu header-menu-empty">
                <li>
                    <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
                        <?php echo esc_html__( 'Create New Menu', 'mindverse' ); ?>
                    </a>
                </li>
            </ul>
        <?php endif;
    }

}