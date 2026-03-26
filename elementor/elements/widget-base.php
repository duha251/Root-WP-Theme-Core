<?php
namespace Mindverse\Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;


use Elementor\Widget_Base;
use Mindverse\Inc\Integrations\Elementor\Traits\Controls_Trait;
use Mindverse\Inc\Integrations\Elementor\Traits\Group_Controls_Trait;
use Mindverse\Inc\Integrations\Elementor\Traits\Css_Trait;

use Mindverse\Inc\Integrations\Elementor\Elementor_Helpers;


abstract class Mindverse_Widget_Base extends Widget_Base {

    use Controls_Trait, Group_Controls_Trait, Css_Trait;

    protected $config = [];

    abstract protected function widget_info();

    public function __construct( $data = [], $args = null ) {
        $widget_args = $this->widget_info();

        $this->setup_widget_info( $widget_args );
        
        parent::__construct( $data, $args );
    }

    private function setup_widget_info( $args ) {
        $defaults = [
            'name'       => '',
            'title'      => '',
            'icon'       => 'eicon-code',
            'categories' => [ 'mindverse-theme' ],
            'keywords'   => [],
            'style'      => [],
            'script'     => [],
        ];
        $this->config = wp_parse_args( $args, $defaults );
    }

    public function get_name() {
        return $this->config['name'];
    }

    public function get_title() {
        return $this->config['title'];
    }
    public function get_icon() { return $this->config['icon']; }
    public function get_categories() { return $this->config['categories']; }
    public function get_keywords() { return $this->config['keywords']; }
    public function get_style_depends() { return $this->config['style']; }
    public function get_script_depends() { return $this->config['script']; }

    /**
     * Not render elementor-widget-container
     */
    public function has_widget_inner_wrapper(): bool {
        return false;
    }

     /**
     * Get the path to the render template for this widget
     */
    protected function get_template_path( $layout = '1' ) {
        $name = str_replace( 'mindverse2', '', $this->get_name() );
        $name = str_replace( '_', '-', $name ); // ensure folder name consistency
        $template_name = "layout-{$layout}.php";
        return get_template_directory() . "/elementor/render/{$name}/{$template_name}";
    }

    /**
     * Default render logic shared by all child widgets
     */
    protected function render() {
        $widget = $this;
        $settings = $widget->get_settings_for_display();
        $layout = $settings['layout'] ?? 1;

        $template = $this->get_template_path( $layout );


        if( !empty( $_wrapper_attrs ) ) {
            $this->add_render_attribute('_wrapper', $_wrapper_attrs);
        }

        if ( file_exists( $template ) ) {
            include $template;
        } else {
            printf(
                '<div style="color:red;">Template not found: <code>%s</code></div>',
                esc_html( str_replace( get_template_directory(), '', $template ) )
            );
        }
    }
}