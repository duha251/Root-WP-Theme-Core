<?php

/**
 * Handles integration with the Elementor plugin.
 * @package    Mytheme
 * @subpackage 
 */

namespace Mytheme\Elementor;

use Mytheme\Inc\Frontend\Assets;


if ( !defined( 'ABSPATH' ) ) {

    exit;

}

class Elementor_Hooks {
    private $assets;
    public function __construct( ) {
        add_action( 'init', [$this, 'ensure_cpt_support'] );

        add_action( 'elementor/elements/categories_registered', [$this, 'register_elementor_widget_categories'] );

        add_action('elementor/element/container/section_layout/after_section_start', [$this, 'update_elementor_style'], 10, 2);

        add_filter( 'elementor/fonts/groups', [$this, 'update_elementor_font_groups_control'] );
        add_filter( 'elementor/fonts/additional_fonts', [$this, 'update_elementor_font_control'] );
    }

    public function ensure_cpt_support() {
        if ( ! is_admin() ) {
            return;
        }
        $required_cpts = [ 'page', 'post', 'pxl-template', 'career', 'team' ];
        $current_cpts = get_option( 'elementor_cpt_support', [] );
        $has_changed = false;
        foreach ( $required_cpts as $cpt ) {
            if ( ! in_array( $cpt, $current_cpts ) ) {
                $current_cpts[] = $cpt;
                $has_changed = true;
            }
        }

        if ( $has_changed ) {
            update_option( 'elementor_cpt_support', $current_cpts );
        }
    }


    public function register_elementor_widget_categories( $elements_manager ) {
        $categories = [];
        $categories['mytheme-theme'] = [
            'title' => 'Mytheme Widgets',
            'icon' => 'fa fa-plug'
        ];
        $existent_categories = $elements_manager->get_categories();
        $categories = array_merge($categories, $existent_categories);
        $set_categories = function ($categories) {
            $this->categories = $categories;
        };
        $set_categories->call($elements_manager, $categories);
    }

    function update_elementor_font_groups_control($font_groups){
        $pxlfonts_group = array( 'pxlfonts' => esc_html__( 'Theme Fonts', 'mytheme' ) );
        return array_merge( $pxlfonts_group, $font_groups );
    }
    
    function update_elementor_font_control($additional_fonts){
        // $additional_fonts['Geist'] = 'pxlfonts';
        return $additional_fonts;
    }


    function update_elementor_style ( $element, $args ) {
        // Set default padding
        $element->update_control(
            'padding',
            [
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        // Set default gap (column gap / row gap)
        $element->update_control(
            'gap',
            [
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
            ]
        );
    }
}