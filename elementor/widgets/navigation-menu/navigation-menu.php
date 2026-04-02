<?php
namespace Mytheme\Elementor\Widgets;

use Mytheme\Elementor\Elements\Base\Mytheme_Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class Navigation_Menu extends Mytheme_Widget_Base {
    /**
     * Get the widget information.
     */
    protected function widget_info() {
        return [
            'name'       => 'mytheme-navigation-menu',
            'title'      => __( 'Case Navigation Menu', 'mytheme' ),
            'icon'       => 'eicon-nav-menu',
            'keywords'   => [ 'nav', 'menu', 'header', 'mytheme', 'navigation' ],
            'script'     => [ '' ],
        ];
    }
}