<?php
namespace Mytheme\Inc\Plugins\Redux;

/**
 *
 * This file defines the base class for all other classes in the theme that need to
 * interact with the WordPress hook system (actions and filters).
 *
 * @package    Mytheme
 * @subpackage Inc\Core
 * @author     Case Theme
 */

use \Mytheme\Inc\Core\Option;
use Mytheme\Inc\Helpers\StaticOptions;

// Prevents direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Page_Options {

    private $option;

    public function __construct( Option $option_instance ) {
        $this->option = $option_instance;
        add_action( 'pxl_post_metabox_register', [$this, 'page_options_register'] );
    }

    function page_options_register( $metabox ) {
        $panels = [
            /** 
             * Template Page 
             */
            'pxl-template' => [
                'opt_name'            => 'pxl_template_options',
                'display_name'        => __( 'Template Options', 'mytheme' ),
                'show_options_object' => false,
                'context'  => 'advanced',
                'priority' => 'default',
                'sections'  => [
                    'header' => [
                        'title'  => __( 'General', 'mytheme' ),
                        'icon'   => 'el-icon-website',
                        'fields' => array(
                            array(
                                'id'    => 'template_type',
                                'type'  => 'select',
                                'title' => __('Template Type', 'mytheme'),
                                'options' => [
                                    'df'       	   => __('Select Type', 'mytheme'), 
                                    'header'       => __('Header Desktop', 'mytheme'),
                                    'header-mobile'=> __('Header Mobile', 'mytheme'),
                                    'footer'       => __('Footer', 'mytheme'), 
                                    'mega-menu'    => __('Mega Menu', 'mytheme'), 
                                    'hero'         => __('Hero', 'mytheme'), 
                                    'panel'        => __('Panel', 'mytheme'),
                                    'page'         => __('Page', 'mytheme'),
                                    'section'      => __('Section', 'mytheme')
                                ],
                                'select2'  => array(
                                    'allowClear' => false,
                                ),
                                'default' => 'df',
                            ),
                            
                            array(
                                'id'    => 'header_type',
                                'type'  => 'select',
                                'title' => __('Header Type', 'mytheme'),
                                'options' => [
                                    'default'      => __('Default', 'mytheme'), 
                                    'transparent'  => __('Transparent', 'mytheme'),
                                    'sticky'       => __('Sticky', 'mytheme'),
                                ],
                                'select2'  => array(
                                    'allowClear' => false,
                                ),
                                'default' => 'default',
                                'required' => ['template_type', '=', 'header'],
                            ),
                            array(
                                'id'    => 'hero_display_on',
                                'type'  => 'select',
                                'title' => __('Display On', 'mytheme'),
                                'multi' => true,
                                'select2'  => array(
                                    'allowClear' => false,
                                ),
                                'options' => [
                                    'page'         => __('Page', 'mytheme'), 
                                    'single'       => __('Single', 'mytheme'),
                                    'archive'      => __('Archive', 'mytheme'),
                                ],
                                'default' => 'page',
                                'required' => ['template_type', '=', 'hero'],
                            ),
                        ), 
                    ],
                ]
            ],
            'page' => [
                'opt_name'            => 'pxl_page_options',
                'display_name'        => __( 'Page Settings', 'mytheme' ),
                'show_options_object' => false,
                'context'  => 'advanced',
                'priority' => 'default',
                'sections'  => $this->page_options(),
            ],
            // Team
            'team' => [
                'opt_name'            => 'pxl_team_options',
                'display_name'        => __('Team Settings', 'mytheme' ),
                'show_options_object' => false,
                'context'  => 'advanced',
                'priority' => 'default',
                'sections'  => $this->single_team_options(),
            ],
        ];

        $metabox->add_meta_data( $panels );
    }

    function page_options() {
        return [
            // Header
            'header' => [
                'title'  => __( 'Header', 'mytheme' ),
                'icon'   => 'eicon-header',
                'fields' => array_merge(
                    StaticOptions::header_options( [ 'scope' => 'private' ] ),
                    StaticOptions::header_sticky_options( [ 'scope' => 'private' ] ),
                ),
            ],
            // Hero Section
            'hero' => [
                'title'  => __( 'Hero Section', 'mytheme' ),
                'icon'   => 'eicon-archive-title',
                'fields' => array_merge(
                    array(
                        // array(
                        //     'id' => 'hero_section_heading',
                        //     'title' => __('Hero Section', 'mytheme'),
                        //     'type'  => 'section',
                        //     'indent' => true,
                        // ),
                    ),
                    // Helpers::get_page_hero_options('page', 'private'),
                ),
            ],
            // Footer
            'footer' => [
                'title'  => __( 'Footer', 'mytheme' ),
                'icon'   => 'eicon-footer',
                'fields' => array_merge(
                    array(
                        array(
                            'id' => 'footer_heading',
                            'title' => __('Footer', 'mytheme'),
                            'type'  => 'section',
                            'indent' => true,
                        ),
                    ),
                    // Helpers::get_footer_options('private'),
                )
            ],
            'breadcrumb' => [
                'title'  => __('Breadcrumb', 'mytheme'),
                'icon'   => 'eicon-animated-headline',
                'fields' => array(
                    array(
                        'id' => 'breadcrumb_heading',
                        'title' => __('Breadcrumb', 'mytheme'),
                        'type'  => 'section',
                        'indent' => true,
                    ),
                    array(
                        'id'      => 'breadcrumb_mode',
                        'type'    => 'button_set',
                        'title'   => __( 'Breadcrumb Mode', 'mytheme' ),
                        'options' => [
                            'default'   => __( 'Default', 'mytheme' ),
                            'custom'    => __( 'Custom', 'mytheme' ),
                        ], 
                        'default' => 'default',
                    ),
                ) 
            ],
            'appearance' => [
                'title'  => __( 'Appearance', 'mytheme' ),
                'icon'   => 'eicon-custom',
                'fields' => array(
                    array(
                        'id' => 'general_heading',
                        'title' => __('General', 'mytheme'),
                        'type'  => 'section',
                        'indent' => true,
                    ),
                    array(
                        'id' => 'body_custom_class',
                        'type' => 'text',
                        'title' => __('Body Custom Class', 'mytheme'),
                    ), 
                    array(
                        'id' => 'color_heading',
                        'title' => __('Colors', 'mytheme'),
                        'type'  => 'section',
                    ),
                    array(
                        'id'        => 'body_bg_color',
                        'type'      => 'color',
                        'title'     => __('Body Background Color', 'mytheme'),
                        'transparent' => false,
                    ),
                    array(
                        'id'          => 'primary_color',
                        'type'        => 'color',
                        'title'       => __('Primary Color', 'mytheme'),
                        'transparent' => false,
                        'default'     => ''
                    ),
                    array(
                        'id'          => 'secondary_color',
                        'type'        => 'color',
                        'title'       => __('Secondary Color', 'mytheme'),
                        'transparent' => false,
                        'default'     => ''
                    ),
                    array(
                        'id'          => 'third_color',
                        'type'        => 'color',
                        'title'       => __('Third Color', 'mytheme'),
                        'transparent' => false,
                        'default'     => ''
                    ),
                    array(
                        'id'          => 'heading_color',
                        'type'        => 'color',
                        'title'       => __('Heading Color', 'mytheme'),
                        'transparent' => false,
                        'default'     => ''
                    ),
                    array(
                        'id' => 'font_heading',
                        'title' => __('Font Family', 'mytheme'),
                        'type'  => 'section',
                    ),
                    array(
                        'id'          => 'primary_font',
                        'type'        => 'typography',
                        'title'       => __('Primary Font', 'mytheme'),
                        'google'      => true,
                        'font-backup' => false,
                        'all_styles'  => false,
                        'line-height'  => false,
                        'font-size'  => false,
                        'color'  => false,
                        'font-style'  => false,
                        'font-weight'  => false,
                        'text-align'  => false,
                    ),
                    array(
                        'id'          => 'secondary_font',
                        'type'        => 'typography',
                        'title'       => __('Secondary Font', 'mytheme'),
                        'google'      => true,
                        'font-backup' => false,
                        'all_styles'  => false,
                        'line-height'  => false,
                        'font-size'  => false,
                        'color'  => false,
                        'font-style'  => false,
                        'font-weight'  => false,
                        'text-align'  => false,
                    ),
                    array(
                        'id'          => 'third_font',
                        'type'        => 'typography',
                        'title'       => __('Third Font', 'mytheme'),
                        'google'      => true,
                        'font-backup' => false,
                        'all_styles'  => false,
                        'line-height'  => false,
                        'font-size'  => false,
                        'color'  => false,
                        'font-style'  => false,
                        'font-weight'  => false,
                        'text-align'  => false,
                    ),
                    array(
                        'id'          => 'heading_font',
                        'type'        => 'typography',
                        'title'       => __('Heading Font', 'mytheme'),
                        'google'      => true,
                        'font-backup' => false,
                        'all_styles'  => false,
                        'line-height'  => false,
                        'font-size'  => false,
                        'font-style'  => false,
                        'font-weight'  => false,
                        'text-align'  => false,
                        'color'       => false,
                    ),
                )
            ],
        ];
    }

    function single_team_options() {
        return [
            'info' => [
                'title'  => __( 'Info', 'mytheme' ),
                'icon'   => 'eicon-text-field',
                'fields' => [
                    array(
                        'id'    => 'team_role',
                        'type'  => 'text',
                        'title' => __('Role', 'mytheme'),
                        'placeholder' => __('CEO', 'mytheme')
                    ),
                    array(
                        'id'    => 'team_email',
                        'type'  => 'text',
                        'title' => __('Email', 'mytheme'),
                        'placeholder' => __('info@gmail.com', 'mytheme')
                    ),
                    array(
                        'id'    => 'team_phone_number',
                        'type'  => 'text',
                        'title' => __('Phone Number', 'mytheme'),
                        'placeholder' => __('+84260325111', 'mytheme')
                    ),
                    array(
                        'id'    => 'team_address',
                        'type'  => 'text',
                        'title' => __('Address', 'mytheme'),
                        'placeholder' => __('25/26 Hai Ba Trung street, Ha Noi, Viet Nam', 'mytheme')
                    ),
                ],
            ],
            'socials' => [
                'title'  => __( 'Socials', 'mytheme' ),
                'icon'   => 'eicon-text-field',
                'fields' => [
                    array(
                        'id'       => 'team_socials',
                        'type'     => 'repeater',
                        'title'    => __('Socials', 'mytheme'),
                        'full_width' => true, 
                        'sortable' => true,
                        'group_values' => true,
                        'bind_title' => 'social_label',
                        'fields'   => array(
                            array(
                                'id'       => 'social_icon',
                                'type'     => 'media', 
                                'url'      => true,
                                'title'    => esc_html__('Social Icon', 'mytheme'),
                            ),
                            array(
                                'id'    => 'social_link',
                                'type'  => 'text',
                                'title' => __('Social Link', 'mytheme'),
                                'default' => '#'
                            ),
                        ),
                    )
                ]
            ]
        ];
    }
}