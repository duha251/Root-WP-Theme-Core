<?php
/**
 * Handles integration with the PXL plugin.
 *
 * @package    Mytheme
 * @subpackage Inc\Plugins
 */

namespace Mytheme\Inc\Plugins\Redux;

use \Mytheme\Inc\Core\Option;

class Redux_Hooks {
    private $option;
	public function __construct( Option $option_instance ) {
        $this->option = $option_instance;
        add_filter( 'redux_pxl_iconpicker_field/get_icons', [$this, 'set_icons_to_pxl_iconpicker_field'] );
        add_filter( 'redux/'.$this->option->get_option_name().'/field/typography/custom_fonts', [$this, 'set_custom_font_to_redux_typography_option'], 10, 1 ); 
	}

    public function enable_hook() {
        return false;
    }

    public function disable_hook() {
        return false;
    }

    function set_icons_to_pxl_iconpicker_field($icons){
        $custom_icons = []; //'Flaticon' => array(array('flaticon-marker' => 'flaticon-marker')),
        // $icons = array_merge($custom_icons, $icons);
        return $icons;
    }

    function set_custom_font_to_redux_typography_option($fonts){
        $fonts = [
            'Theme Custom Fonts' => [
                'Glittery-Snowfall' => 'Glittery Snowfall',
            ]
        ];
        return $fonts;
    }
}