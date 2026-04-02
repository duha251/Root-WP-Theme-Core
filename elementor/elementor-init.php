<?php
namespace Mytheme\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Elementor_Init {

    public function __construct() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }
        new Elementor_Hooks();
        add_action( 'elementor/widgets/register', [ $this, 'load_and_register_widgets' ] );
        add_action( 'elementor/elements/elements_registered', [ $this, 'load_and_register_elements' ] );
    }


    /**
     * Register custom elements.
     *
     * @param mixed $element_manager Elementor element manager.
     * @return void
     */
    public function load_and_register_elements( $element_manager ) {
        // Register custom elements here.
    }

    /**
     * Load and register Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     * @return void
     */
    public function load_and_register_widgets( $widgets_manager ) {

        $base_widget_file = get_template_directory() . '/elementor/elements/base/widget-base.php';
        if ( file_exists( $base_widget_file ) ) {
            require_once $base_widget_file;
        }

        $widgets_path = get_template_directory() . '/elementor/widgets/*/*.php';
        $widget_files = glob( $widgets_path );


        require_once get_template_directory() . '/elementor/atomic-widgets/atomic-button/atomic-button.php';

        if ( empty( $widget_files ) ) {
            return;
        }
        $widgets_manager->register( new \Mytheme\Elementor\Atomic_Widgets\Atomic_Button\Mytheme_Atomic_Button() ); //Line 52

        foreach ( $widget_files as $file ) {
            require_once $file;

            $filename        = basename( $file, '.php' );
            $class_name      = str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $filename ) ) );
            $full_class_name = '\\Mytheme\\Elementor\\Widgets\\' . $class_name;

            if ( ! class_exists( $full_class_name ) ) {
                continue;
            }

            if ( ! is_subclass_of( $full_class_name, '\Elementor\Widget_Base' ) ) {
                continue;
            }

            try {
                $reflection = new \ReflectionClass( $full_class_name );

                if ( ! $reflection->isAbstract() ) {
                    $widgets_manager->register( new $full_class_name() );
                }
            } catch ( \Throwable $e ) {
                error_log( 'Elementor widget register error: ' . $e->getMessage() );
            }

            
        }
    }
}