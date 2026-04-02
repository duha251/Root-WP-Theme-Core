<?php
namespace Mytheme\Inc\Frontend;

/**
 * Frontend Layout
 *
 * Handles layout structure for frontend.
 *
 * @package Mytheme
 */

use Mytheme\Inc\Core\Option;

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
        $this->get_header();
        $this->get_header_mobile();
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

            mytheme_get_template('template-parts/header/desktop/default',[
                'logo_html' => $logo_html
            ]);
            return;
        }
        mytheme_get_template('template-parts/header/desktop/builder', [
            'layout_id' => $layout_id,
        ]);
    }

    public function get_header_mobile() {
        $prefix_id = '';
        $mode = $this->option->get_option( $prefix_id.'header_mobile_mode', 'default');
        $layout_id = $this->option->get_option( $prefix_id.'header_mobile_layout', 0);

        if( $mode === 'inherit' ) {
            $mode = $this->option->get_theme_option( $prefix_id.'header_mobile_mode', 'hide');
            $layout_id = $this->option->get_theme_option( $prefix_id.'header_mobile_layout', 0);
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
            $logo = $this->option->get_theme_option('header_mobile_logo', ['url'=> get_template_directory_uri() . '/assets/img/site-logo.webp']);
            $logo_html = (!empty($logo['url'])) 
                ? sprintf(
                    '<a href="%1$s" title="%2$s" rel="home"><img src="%3$s" alt="%2$s"/></a>',
                    esc_url( home_url( '/' ) ),
                    esc_attr( get_bloginfo( 'name' ) ),
                    esc_url( $logo['url'] )
                ) : '';

            mytheme_get_template('template-parts/header/mobile/default',[
                'logo_html' => $logo_html
            ]);
            return;
        }
        mytheme_get_template('template-parts/header/mo/builder', [
            'layout_id' => $layout_id,
        ]);
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

}