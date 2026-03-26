<?php
namespace MyTheme\Inc\Integrations\Redux;

/**
 *
 * This file defines the base class for all other classes in the theme that need to
 * interact with the WordPress hook system (actions and filters).
 *
 * @package    MyTheme
 * @subpackage Inc\Core
 * @author     Case Theme
 */

use \MyTheme\Inc\Core\Option;

// Prevents direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SingularOptions {

    private $option;

    public function __construct( Option $option_instance ) {
        $this->option = $option_instance;
        add_action( 'pxl_post_metabox_register', [$this, 'singular_options_register'] );
    }

    function singular_options_register( $metabox ) {
        $panels = [
            /** Singular Page */
            'page' => [
                'opt_name'            => 'pxl_page_options',
                'display_name'        => __( 'Page Settings', 'mytheme' ),
                'show_options_object' => false,
                'context'  => 'advanced',
                'priority' => 'default',
                'sections'  => $this->singular_general_options(),
            ],
            /** Template Page */
            'pxl-template' => [
                'opt_name'            => 'pxl_hidden_template_options',
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
                                    'hero-section' => __('Hero Section', 'mytheme'), 
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
                                    'default'       => __('Default', 'mytheme'), 
                                    'transparent'   => __('Transparent', 'mytheme'),
                                ],
                                'select2'  => array(
                                    'allowClear' => false,
                                ),
                                'default' => 'default',
                                'required' => ['template_type', '=', 'header'],
                            ),
                            array(
                                'id'    => 'hero_section_display_on',
                                'type'  => 'select',
                                'title' => __('Display On', 'mytheme'),
                                'multi' => true,
                                'select2'  => array(
                                    'allowClear' => false,
                                ),
                                'options' => [
                                    'page'         => __('Page', 'mytheme'), 
                                    'single'       => __('Single Post', 'mytheme'),
                                    'archive'      => __('Archive', 'mytheme'),
                                ],
                                'default' => 'page',
                                'required' => ['template_type', '=', 'hero-section'],
                            ),
                        ), 
                    ],
                ]
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
            'career' => [
                'opt_name'            => 'pxl_career_options',
                'display_name'        => __('Career Settings', 'mytheme' ),
                'show_options_object' => false,
                'context'  => 'advanced',
                'priority' => 'default',
                'sections'  => $this->single_career_options(),
            ]
        ];

        $post_types = $this->option->get_theme_option('pxl_post_type', []);
        if( is_array($post_types) ) {
            foreach( $post_types as $post_type ) {
                $post_type_slug = sanitize_title($post_type);
                $panels[$post_type_slug] = [
                    'opt_name'            => 'pxl_'.$post_type.'_options',
                    'display_name'        => $post_type.__( ' Settings', 'mytheme' ),
                    'show_options_object' => false,
                    'context'  => 'advanced',
                    'priority' => 'default',
                    'sections'  => [],
                ];
            }
        }
        $metabox->add_meta_data( $panels );
    }

    function singular_general_options() {
        return [
            // Header
            'header' => [
                'title'  => __( 'Header', 'mytheme' ),
                'icon'   => 'eicon-header',
                'fields' => array_merge(
                    array(
                        array(
                            'id' => 'header_desktop_heading',
                            'title' => __('Header Desktop', 'mytheme'),
                            'type'  => 'section',
                            'indent' => true,
                        ),
                    ),
                    // Helpers::get_header_options('private'),
                    array(
                        array(
                            'id'       => 'header_logo',
                            'type'     => 'media',
                            'title'    => __('Header Logo', 'mytheme'),
                            'default' => array(
                                'url' => get_template_directory_uri() . '/assets/img/site-logo.webp'
                            ),
                            'url'      => false,
                            'required' => ['header_mode', '=', 'default'],
                        ),
                        array(
                            'id' => 'header_mobile_heading',
                            'title' => __('Header Mobile', 'mytheme'),
                            'type'  => 'section',
                            'indent' => true,
                        ),
                    ),
                    array(
                        array(
                            'id'       => 'header_mobile_logo',
                            'type'     => 'media',
                            'title'    => __('Mobile Logo', 'mytheme'),
                            'default' => array(
                                'url'=> get_template_directory_uri() . '/assets/img/site-logo.webp'
                            ),
                            'url'      => false,
                        ),
                        array(
                            'id'             => 'header_mobile_logo_height',
                            'type'           => 'dimensions',
                            'units'          => array('px'), 
                            'units_extended' => 'false',
                            'title'          => __('Mobile Logo Height', 'mytheme'),
                            'height'         => true,
                            'width'          => false, 
                        ),
                    )
                ),
            ],
            // Hero Section
            'hero-section' => [
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
                    array(
                        'id'    => 'breadcrumb_label',
                        'type'  => 'text',
                        'title' => __( 'Breadcrumb Label', 'mytheme' ),
                        'placeholder' => __('Ex: ABC', 'mytheme'),
                        'required' => [ 'breadcrumb_mode', '=', 'custom' ]
                    ),
                    array(
                        'id'    => 'breadcrumb_highight',
                        'type'  => 'text',
                        'title' => __( 'Breadcrumb Highight', 'mytheme' ),
                        'placeholder' => __('Ex: ABC', 'mytheme'),
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

    function single_career_options() {
        return [
            'info' => [
                'title'  => __( 'Info', 'mytheme' ),
                'icon'   => 'eicon-text-field',
                'fields' => [
                    array(
                        'id'    => 'career_salary',
                        'type'  => 'text',
                        'title' => __('Salary', 'mytheme'),
                        'placeholder' => __('Ex: $99k/year', 'mytheme')
                    ),
                ],
            ],
        ];
    }

}