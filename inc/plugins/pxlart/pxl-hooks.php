<?php
 namespace Mytheme\Inc\Plugins\Pxlart;

/**
 * Handles integration with the Case-Addons plugin.
 *
 * @package    Mytheme
 * @subpackage Inc\Plugins
 */

use Mytheme\Inc\Core\Option;

class Pxl_Hooks {
    private $option;

    public function __construct( Option $option_instance ) {
        $this->option = $option_instance;

        add_action( 'init', [ $this, 'remove_plugin_actions' ], 11 );

        add_filter( 'pxl_support_e_control_icons', [ $this, 'disable_hook' ] );
        add_filter( 'pxl_support_e_control_list', [ $this, 'disable_hook' ] );
        add_filter( 'pxl_enable_pagepopup', [ $this, 'disable_hook' ] );
        add_filter( 'pxl_enable_megamenu', [ $this, 'enable_hook' ] );
        add_filter( 'pxl_enable_onepage', [ $this, 'enable_hook' ] );
        add_filter( 'pxl_support_awesome_pro', [ $this, 'disable_hook' ] );
        add_filter( 'pxl_scssc_on', [ $this, 'disable_hook' ] );

        // Disable register widget
        // add_filter( 'pxl-register-widgets-folder', [ $this, 'get_folder_widgets_path' ] );

        /**
         * Disable Enqueue Swiper to Core
         */
        add_filter( 'pxl-swiper-version-active', [ $this, 'disable_hook' ] );

        // add_filter( 'post_type_archive_link', [ $this, 'set_post_types_archive_url' ], 10, 2 );

        add_filter( 'pxl_server_info', [ $this, 'server_info' ] );
        add_filter( 'pxl_export_wp_settings', [ $this, 'export_wp_settings' ] );
        // add_filter( 'pxl_wg_get_source_id_builder', [ $this, 'wg_get_source_builder' ] );

        add_filter( 'pxl_template_type_support', [ $this, 'template_type_support' ] );
        add_filter( 'pxl_support_default_cpt', [ $this, 'cpt_support_default' ] );
        add_filter( 'pxl_extra_post_types', [ $this, 'register_post_types' ] );
        add_filter( 'pxl_theme_builder_post_types', [ $this, 'post_type_theme_supports_builder' ] );

        add_filter( 'pxl_extra_taxonomies', [ $this, 'register_taxonomies' ] );
    }

    public function enable_hook() {
        return true;
    }

    public function disable_hook() {
        return false;
    }

    public function get_folder_widgets_path($folder) {
        $new_folder = get_stylesheet_directory() . '/elementor/widgets/';
        if ( is_dir( $new_folder ) ) {
            return $new_folder;
        }
        return '';
    }

    public function remove_plugin_actions() {
        if ( class_exists( 'Pxl_Elementor' ) ) {
            remove_action( 'elementor/widgets/register', [ \Pxl_Elementor::instance(), 'register_widgets' ] );
		}
    }

	public function server_info( $infos ) {
		return [
            'api_url' => 'https://api.casethemes.net/',
            'docs_url' => 'https://doc.casethemes.net/mytheme/',
            'plugin_url' => 'https://api.casethemes.net/plugins/',
            'demo_url' => 'https://mytheme.casethemes.net/',
            'support_url' => 'https://casethemes.ticksy.com/',
            'help_url' => 'https://doc.casethemes.net/mytheme',
            'email_support' => 'casethemesagency@gmail.com',
            'video_url' => '#'
		];
	}

	public function export_wp_settings( $wp_options ) {
		$wp_options[] = 'mc4wp_default_form_id';
		return $wp_options;
	}

	public function wg_get_source_builder( $wg_datas ) {
		$wg_datas['tabs']   = ['control_name' => 'tabs', 'source_name' => 'content_template'];
		$wg_datas['slides'] = ['control_name' => 'slides', 'source_name' => 'slide_template'];
		return $wg_datas;
	}
    
    public function template_type_support( $type ) {
		$extra_type = [
            'header'          => __('Header Desktop', 'mytheme'),
            'header-mobile'   => __('Header Mobile', 'mytheme'),
            'footer'          => __('Footer', 'mytheme'), 
            'mega-menu'       => __('Mega Menu', 'mytheme') ,
            'hero'            => __('Hero', 'mytheme'), 
            'panel'           => __('Panel', 'mytheme'),
            // 'archive'      => __('Archive', 'mytheme')
            'page'            => __('Page', 'mytheme'),
            'section'         => __('Section', 'mytheme')
		];
		return $extra_type;
	}

    function cpt_support_default($postypes){
        return $postypes; // pxl-template
    }

    /**
     * 
     */
    function post_type_theme_supports_builder($postypes){
        //default are header, footer, mega-menu
        return $postypes;
    }

    /**
     * Register post types
     */
    function register_post_types( $postypes ) {
        $post_types = $this->option->get_theme_option('pxl_post_type', []);
        $post_type_labels = $this->option->get_theme_option('pxl_post_type_label', []);
        $post_type_slugs = $this->option->get_theme_option('pxl_post_type_slug', []);
        $post_type_status = $this->option->get_theme_option('pxl_post_type_status', []);

        $team_label = $this->option->get_theme_option('team_label', 'Team');;
        $team_status = $this->option->get_theme_option('team_status', true);

        array_unshift($post_types, 'team');
        array_unshift($post_type_labels, $team_label);
        array_unshift($post_type_slugs, 'team');
        array_unshift($post_type_status, $team_status);

        $career_label = $this->option->get_theme_option('career_label', 'Career');
        $career_status = $this->option->get_theme_option('career_status', true);

        array_unshift($post_types, 'career');
        array_unshift($post_type_labels, $career_label);
        array_unshift($post_type_slugs, 'career');
        array_unshift($post_type_status, $career_status);

        $post_type_status = array_map('boolval', $post_type_status);

        if( !is_array( $post_types ) || empty( $post_types ) ) {
            return [];
        }

        foreach( $post_types as $i => $post_type ) {
            $sanitize_text = sanitize_title( $post_type );
            if( empty( $post_type ) ) {
                continue;
            }
            $postypes[$sanitize_text] = array(
                'status'     => (bool)$post_type_status,
                'item_name'  => $post_type_labels[$i],
                'items_name' => $post_type_labels[$i],
                'args'       => array(
                    'has_archive' => false,
                    'rewrite'             => array(
                        'slug'       => sanitize_title( $post_type_slugs[$i] ),
                    ),
                ),
                'labels'     => array(
                    'add_new_item' => __('Add ', 'mytheme').ucwords($post_type),
                ),
            );
        }
    
        return $postypes;
    }

    /**
     * Resgister taxonomies
     */
    function register_taxonomies( $taxonomies ) {
        $on_category = $this->option->get_theme_option('pxl_on_category', []); 
        $category_labels = $this->option->get_theme_option('pxl_category_label', []);
        $post_types = $this->option->get_theme_option('pxl_post_type', []);
        $career_status = $this->option->get_theme_option('career_status', true);

        // Xử lý career đồng bộ
        if( $career_status ) {
            array_unshift($post_types, 'career');
            array_unshift($category_labels, 'Categories');
            array_unshift($on_category, true); 
        }

        if( !is_array( $post_types ) || empty( $post_types ) ) {
            return $taxonomies;
        }

        foreach( $post_types as $i => $post_type ) {
            if( isset($on_category[$i]) && $on_category[$i] ) {
                $sanitize_text = sanitize_title( $post_type.'_category' );
                $taxonomies[$sanitize_text] = array(
                    'status'     => true,
                    'post_type'  => [ sanitize_title( $post_type ) ],
                    'taxonomy'   => !empty($category_labels[$i]) ? $category_labels[$i] : ucwords($post_type).' Category',
                    'taxonomies' => !empty($category_labels[$i]) ? $category_labels[$i] : ucwords($post_type).' Categories',
                    'args'       => array(
                        'hierarchical' => true, 
                        'show_in_rest' => true,
                        'rewrite'      => array(
                            'slug' => sanitize_title( $post_type.'-category' )
                        ),
                    ),
                    'labels'     => array(
                        'add_new_item' => __('Add ', 'mytheme').ucwords($post_type). __(' Category', 'mytheme'),
                    ),
                );
            }
        }

        if( $career_status ) {
            $taxonomies['career_tag'] = array(
                'status'     => true,
                'post_type'  => array('career'), 
                'taxonomy'   => esc_html__('Career Tag', 'mytheme'), 
                'taxonomies' => esc_html__('Tags', 'mytheme'), 
                'args'       => array(
                    'hierarchical'      => true,
                    'show_admin_column' => true,  
                    'show_in_rest'      => true, 
                    'rewrite'           => array(
                        'slug' => 'career-tag'
                    ),
                ),
                'labels'     => array(
                    'menu_name' => esc_html__('Career Tags', 'mytheme'),
                ),
            );
        }

        return $taxonomies;
    }

}