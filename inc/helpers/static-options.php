<?php
namespace Mytheme\Inc\Helpers;

class StaticOptions {

	public static function get_templates_by_type($template_type = 'df', $meta_type = null){
		$post_list = ['' => 'None'];

		$meta_query = [
			[
				'key'     => 'template_type',
				'value'   => $template_type,
				'compare' => '='
			]
		];

		if( !is_null($meta_type) ) {
			switch ($template_type) {
				case 'header':
					$meta_query[] = [
						'key'     => 'header_type',
						'value'   => $meta_type,
						'compare' => '='
					];
					break;
				case 'hero' :
					$meta_query[] = [
						'key'     => 'hero_display_on',
						'value'   => $meta_type,
						'compare' => 'LIKE'
					];
					break;
			}
		}

		$args = [
			'post_type'      => 'pxl-template',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'meta_query'     => $meta_query,
		];

        $posts = get_posts($args);
        foreach($posts as $post){  
        	$template_type = get_post_meta( $post->ID, 'template_type', true );
        	if($template_type == 'df') {
				continue;
			}
            $post_list[$post->ID] = $post->post_title;
        }
         
        return $post_list;
    }

    public static function header_options( $args = [] ){	
        $args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );
        extract( $args );

        $mode_options = [
            'default' => __('Default', 'mytheme'),
            'builder' => __('Builder', 'mytheme'),
            'hide'    => __('Hide', 'mytheme')
        ];
    
        if ( $scope === 'private' ) {
            unset($mode_options['default']);
            $mode_options = ['inherit' => __('Inherit', 'mytheme')] + $mode_options;
        }
    
        return array(
			array(
				'id' => 'header_desktop_heading',
				'title' => __('Header Desktop', 'mytheme'),
				'type'  => 'section',
				'indent' => true,
			),
            array(
                'id'      => $prefix_id.'header_mode',
                'type'    => 'button_set',
                'title'   => __( 'Header Mode', 'mytheme' ),
                'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default'
            ),
            array(
                'id'      => $prefix_id.'header_layout',
                'type'    => 'select',
                'title'   => __('Header Layout', 'mytheme'),
                'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mytheme'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
                'options' => self::get_templates_by_type('header'),
                'select2'  => [ 'allowClear' => false ],
                'required' => [ $prefix_id.'header_mode', '=', 'builder' ],
            ),
            array(
                'id'       => $prefix_id.'header_logo',
                'type'     => 'media',
                'title'    => __('Header Logo', 'mytheme'),
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/imgs/logo.webp'
                ),
                'url'      => false,
                'required' => [ $prefix_id.'header_mode', '=', 'default' ],
            ),
        );    
    }

	public static function header_sticky_options( $args = [] ){	
        $args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );
        extract( $args );

        $mode_options = [
            'default' => __('Default', 'mytheme'),
            'builder' => __('Builder', 'mytheme'),
            'hide'    => __('Hide', 'mytheme')
        ];
    
        if ( $scope === 'private' ) {
            unset($mode_options['default']);
            $mode_options = ['inherit' => __('Inherit', 'mytheme')] + $mode_options;
        }
    
        return array(
			array(
				'id' => 'header_sticky_heading',
				'title' => __('Header Sticky', 'mytheme'),
				'type'  => 'section',
				'indent' => true,
			),
            array(
                'id'      => $prefix_id.'header_sticky_mode',
                'type'    => 'button_set',
                'title'   => __( 'Header Sticky Mode', 'mytheme' ),
                'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default',
            ),
            array(
                'id'      => $prefix_id.'header_sticky_layout',
                'type'    => 'select',
                'title'   => __('Header Sticky Layout', 'mytheme'),
                'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mytheme'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
                'options' => self::get_templates_by_type('header', 'sticky'),
                'select2'  => [ 'allowClear' => false ],
                'required' => [
                    [ $prefix_id.'header_sticky_mode', '=', 'builder' ],
                ],
            ),
            array(
                'id'      => $prefix_id.'header_sticky_display_on',
                'type'    => 'button_set',
                'title'   => __( 'Display on', 'mytheme' ),
                'options' => [
                    'up'   => __('Scroll Up', 'mytheme'),
                    'down'   => __('Scroll Down', 'mytheme'),
                ], 
                'default' => 'up',
                'required' => [ $prefix_id.'header_sticky_mode', '!=', 'hide' ],
            ),
        );    
    }

	public static function header_mobile_options( $args = [] ){	
		$args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );
        extract( $args );

        $mode_options = [
			'default' => __('Default', 'mytheme'),
            'builder' => __('Builder', 'mytheme'),
            'hide'    => __('Hide', 'mytheme')
        ];
    
        if ( $scope === 'private' ) {
			unset($mode_options['default']);
            $mode_options = ['inherit' => __('Inherit', 'mytheme')] + $mode_options;
        }
    
        return array(
			array(
				'id' => 'header_mobile_heading',
				'title' => __('Header Mobile', 'mytheme'),
				'type'  => 'section',
				'indent' => true,
			),
            array(
                'id'      => $prefix_id.'header_mobile_mode',
                'type'    => 'button_set',
                'title'   => __( 'Header Mobile Mode', 'mytheme' ),
                'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default'
            ),
            array(
                'id'      => $prefix_id.'header_mobile_layout',
                'type'    => 'select',
                'title'   => __('Header Mobile Layout', 'mytheme'),
                'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mytheme'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
                'options' => self::get_templates_by_type('header-mobile'),
                'select2'  => [ 'allowClear' => false ],
                'required' => [ $prefix_id.'header_mobile_mode', '=', 'builder' ],
            ),
					array(
                'id'       => $prefix_id.'header_mobile_logo',
                'type'     => 'media',
                'title'    => __('Header Mobile Logo', 'mytheme'),
                'default' => array(
                    'url' => get_template_directory_uri() . '/assets/imgs/logo.webp'
                ),
                'url'      => false,
                'required' => [ $prefix_id.'header_mobile_mode', '=', 'default' ],
            ),
        );    
    }

	public static function hero_options( $args = [] ){
        $args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );

        extract( $args );

		$mode_options = [
			'default'   => __('Default', 'mytheme'),
			'builder'   => __('Builder', 'mytheme'),
			'hide'      => __('Hide', 'mytheme'),
		];

		if( $scope === 'private' ) {
			unset($mode_options['default']);
			$mode_options = ['inherit' => __('Inherit', 'mytheme')] + $mode_options;
		}

		$final_options = array(
			array(
				'id' => $prefix_id.'_hero_heading',
				'title' => __('Hero', 'mytheme'),
				'type'  => 'section',
				'indent' => true,
			),
	        array(
	            'id'      => $prefix_id.'_hero_mode',
	            'type'    => 'button_set',
	            'title'   => __( 'Hero Mode', 'mytheme' ),
	            'options' => $mode_options, 
                'default' => ($scope === 'private') ? 'inherit' : 'default',
	        ),
	        array(
	            'id'       => $prefix_id.'_hero_layout',
	            'type'     => 'select',
	            'title'    => __('Hero Layout', 'mytheme'),
	            'desc'     => sprintf(__('Please create your layout before choosing. %sClick Here%s','mytheme'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
	            'options'  => self::get_templates_by_type( 'hero', 'page' ),
	            'required' => [ $prefix_id.'_hero_mode', '=', 'builder' ],
				'select2'  => [ 'allowClear' => false ],
	        ),
	    ); 
		return $final_options;
	}

    public static function footer_options( $args = [] ){
        $args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );

        extract( $args );

		$mode_options = [
			'default' => __('Default', 'mytheme'),
			'builder' => __('Builder', 'mytheme'),
			'hide'    => __('Hide', 'mytheme'),
		];

		if ($scope === 'private') {
			unset($mode_options['default']);
			$mode_options = ['inherit' => __('Inherit', 'mytheme')] + $mode_options;
		}

		$final_options = [
            array(
                'id' => $prefix_id.'footer_heading',
                'title' => __('Footer', 'mytheme'),
                'type'  => 'section',
                'indent' => true,
            ),
			array(
	            'id'      => $prefix_id.'footer_mode',
	            'type'    => 'button_set',
	            'title'   => __( 'Footer Mode', 'mytheme' ),
	            'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default'
	        ),
	        array(
				'id'      => $prefix_id.'footer_layout',
				'type'    => 'select',
				'title'   => __('Footer Layout', 'mytheme'),
				'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mytheme'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
				'options' => self::get_templates_by_type('footer'),
				'default' => 0,
				'select2'  => [ 'allowClear' => false ],
				'required' => ['footer_mode', '=', 'builder'],
	        ),
		];
		return $final_options;
	}
}