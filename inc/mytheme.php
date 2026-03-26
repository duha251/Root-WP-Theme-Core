<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MyTheme\Inc\Core\Setup;
use MyTheme\Inc\Core\Option;
use MyTheme\Inc\Frontend\Layout;
use MyTheme\Inc\Frontend\Enqueue;

use MyTheme\Inc\Integrations\Pxlart\Hooks as PXL_Hooks; 

// Redux
use MyTheme\Inc\Integrations\Redux\Hooks as Redux_Hooks;
use MyTheme\Inc\Integrations\Redux\ThemeOptions;
use MyTheme\Inc\Integrations\Redux\SingularOptions;

final class MyTheme {

    private static $instance = null;
    public $option;
    public $setup;
    public $layout;
    public $pxl_hooks;

    private function __construct() {
        $this->register_autoloader();
        $this->init_components();
    }

    private function register_autoloader() {
        require_once get_template_directory() . '/inc/helpers/functions.php';
        // require_once get_template_directory() . '/inc/helpers/media.php';
        spl_autoload_register( [ $this, 'load_class' ] );
    }

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function init_components() {
        $this->setup  = new Setup();
        $this->option = new Option();
        $this->layout = new Layout( $this->option );

        new Enqueue( $this->option, $this->get_theme_version() );
        if ( class_exists( 'Pxl_Elementor' ) ) {
            $this->pxl_hooks = new PXL_Hooks( $this->option );
        }

        if ( class_exists( 'Redux' ) && is_admin()) {
            new Redux_Hooks( $this->option );
            new ThemeOptions( $this->option ); 
            new SingularOptions( $this->option );
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
        if ( strpos( $class_name, 'MyTheme\\Inc\\' ) !== 0 ) {
            return;
        }

        $relative_class = substr( $class_name, strlen( 'MyTheme\\Inc\\' ) );
        $parts = explode( '\\', $relative_class );
        $file_class_name = array_pop( $parts );

        $file_name = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $file_class_name ) ) . '.php';
        $path_parts = array_map( 'strtolower', $parts );

        $path = get_template_directory() . '/inc/' . implode( '/', $path_parts ) . '/' . $file_name;

        if ( file_exists( $path ) ) {
            require_once $path;
        }
    }
}

function mytheme() {
    return MyTheme::instance();
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