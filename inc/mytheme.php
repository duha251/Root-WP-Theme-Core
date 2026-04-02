<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Mytheme\Inc\Core\Setup;
use Mytheme\Inc\Core\Option;
use Mytheme\Inc\Frontend\Layout;
use Mytheme\Inc\Frontend\Components;
use Mytheme\Inc\Frontend\Enqueue;

use Mytheme\Inc\Plugins\Pxlart\Pxl_Hooks; 

// Redux
use Mytheme\Inc\Plugins\Redux\Redux_Hooks;
use Mytheme\Inc\Plugins\Redux\Theme_Options;
use Mytheme\Inc\Plugins\Redux\Page_Options;

//Elementor
use \Mytheme\Elementor\Elementor_Init;


final class Mytheme {

    private static $instance = null;
    public $option;
    public $setup;
    public $component;

    private function __construct() {
        $this->register_autoloader();
        $this->init_classes();
    }

    private function register_autoloader() {
        require_once get_template_directory() . '/inc/helpers/functions.php';
        spl_autoload_register( [ $this, 'load_class' ] );
    }

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function init_classes() {
        $this->setup  = new Setup();
        $this->option = new Option();
        $this->component = new Components( $this->option );
        new Layout( $this->option );
        new Enqueue( $this->option, $this->get_theme_version() );
        if ( class_exists( 'Pxl_Elementor' ) ) {
            new Pxl_Hooks( $this->option );
        }

        if ( class_exists( 'Redux' ) && is_admin()) {
            new Redux_Hooks( $this->option );
            new Theme_Options( $this->option ); 
            new Page_Options( $this->option );
        }

        if ( did_action( 'elementor/loaded' )) {
            new Elementor_Init();
        }
    }

    public function get_theme_name() {
        $theme = wp_get_theme();
        if( $theme->parent_theme ) {
            $template_dir  = basename( get_template_directory() );
            $theme = wp_get_theme( $template_dir );
        }
        return $theme->get('Name');
    }

    public function get_theme_slug(){ 
        return get_template();
    }

    public function get_theme_version(){
        $theme = wp_get_theme();
        return $theme->get('Version');
    }

    public function get_option( $option = null, $default = false, $subset = false ) {
        return $this->option->get_option( $option, $default, $subset );
    }

    public function get_theme_option( $option = null, $default = false, $subset = false ) {
        return $this->option->get_theme_option( $option, $default, $subset );
    }

    public function get_page_option( $option = null, $default = false, $subset = false ) {
        return $this->option->get_page_option( $option, $default, $subset );
    }

    public function load_class( $class_name ) {
        $prefixes = [
            'Mytheme\\Inc\\'       => get_template_directory() . '/inc/',
            'Mytheme\\Elementor\\' => get_template_directory() . '/elementor/',
        ];

        foreach ( $prefixes as $prefix => $base_dir ) {
            if ( strpos( $class_name, $prefix ) !== 0 ) {
                continue;
            }

            $relative_class  = substr( $class_name, strlen( $prefix ) );
            $parts           = explode( '\\', $relative_class );
            $file_class_name = array_pop( $parts );

            $file_class_name = str_replace( '_', '-', $file_class_name );
            $file_name       = preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $file_class_name );
            $file_name       = strtolower( $file_name ) . '.php';

            $path_parts = array_map( 'strtolower', $parts );
            $path       = $base_dir . implode( '/', $path_parts );
            $path       = rtrim( $path, '/' ) . '/';
            $path      .= $file_name;

            if ( file_exists( $path ) ) {
                require_once $path;
            }

            return;
        }
    }
}

function mytheme() {
    return Mytheme::instance();
}

mytheme();

if( ! function_exists( 'pxl_action' ) ) :
	function pxl_action() {

		$args   = func_get_args();

		if( !isset( $args[0] ) || empty( $args[0] ) ) {
			return;
		}

		$action = 'pxltheme_' . $args[0];
		unset( $args[0] );

		do_action_ref_array( $action, $args );
	}
    pxl_action( 'init' );
endif;