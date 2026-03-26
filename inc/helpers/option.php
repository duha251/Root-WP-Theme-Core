<?php
namespace MyTheme\Inc\Helpers;

class Option {

	public static function get_templates_by_type($template_type = 'df', $meta_type = null){
		$post_list = ['' => 'None'];

		$meta_query = [
			[
				'key'     => 'template_type',
				'value'   => $template_type,
				'compare' => '='
			]
		];

		if(!is_null($meta_type)) {
			switch ($template_type) {
				case 'header':
					$meta_query[] = [
						'key'     => 'header_type',
						'value'   => $meta_type,
						'compare' => '='
					];
					break;
				case 'hero-section' :
					$meta_query[] = [
						'key'     => 'hero_section_display_on',
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
            'default' => __('Default', 'mindverse'),
            'builder' => __('Builder', 'mindverse'),
            'hide'    => __('Hide', 'mindverse')
        ];
    
        if ( $scope === 'private' ) {
            unset($mode_options['default']);
            $mode_options = ['inherit' => __('Inherit', 'mindverse')] + $mode_options;
        }
    
        return array(
            array(
                'id'      => $prefix_id.'header_mode',
                'type'    => 'button_set',
                'title'   => __( 'Header Mode', 'mindverse' ),
                'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default'
            ),
            array(
                'id'      => $prefix_id.'header_layout',
                'type'    => 'select',
                'title'   => __('Header Layout', 'mindverse'),
                'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mindverse'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
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

	public static function hero_options( $args = [] ){
        $args = array_merge([
            'scope' => 'global',
            'prefix_id' => '', 
        ], $args );

        extract( $args );

		$mode_options = [
			'default'   => __('Default', 'mindverse'),
			'builder'   => __('Builder', 'mindverse'),
			'hide'      => __('Hide', 'mindverse'),
		];

		if( $scope === 'private' ) {
			unset($mode_options['default']);
			$mode_options = ['inherit' => __('Inherit', 'mindverse')] + $mode_options;
		}

		$final_options = array(
			array(
				'id' => $prefix_id.'_hero_heading',
				'title' => __('Hero', 'mindverse'),
				'type'  => 'section',
				'indent' => true,
			),
	        array(
	            'id'      => $prefix_id.'_hero_mode',
	            'type'    => 'button_set',
	            'title'   => __( 'Hero Mode', 'mindverse' ),
	            'options' => $mode_options, 
                'default' => ($scope === 'private') ? 'inherit' : 'default',
	        ),
	        array(
	            'id'       => $prefix_id.'_hero_layout',
	            'type'     => 'select',
	            'title'    => __('Hero Layout', 'mindverse'),
	            'desc'     => sprintf(__('Please create your layout before choosing. %sClick Here%s','mindverse'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
	            'options'  => self::get_templates_by_type( 'hero-section', 'page' ),
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
			'default' => __('Default', 'mindverse'),
			'builder' => __('Builder', 'mindverse'),
			'hide'    => __('Hide', 'mindverse'),
		];

		if ($scope === 'private') {
			unset($mode_options['default']);
			$mode_options = ['inherit' => __('Inherit', 'mindverse')] + $mode_options;
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
	            'title'   => __( 'Footer Mode', 'mindverse' ),
	            'options' => $mode_options, 
                'default' => $scope === 'private' ? 'inherit' : 'default'
	        ),
	        array(
				'id'      => $prefix_id.'footer_layout',
				'type'    => 'select',
				'title'   => __('Footer Layout', 'mindverse'),
				'desc'    => sprintf(__('Please create your layout before choosing. %sClick Here%s','mindverse'),'<a href="' . esc_url( admin_url( 'edit.php?post_type=pxl-template' ) ) . '">','</a>'),
				'options' => self::get_templates_by_type('footer'),
				'default' => 0,
				'select2'  => [ 'allowClear' => false ],
				'required' => ['footer_mode', '=', 'builder'],
	        ),
		];
		return $final_options;
	}
}