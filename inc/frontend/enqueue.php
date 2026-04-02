<?php
/**
 * Handles front-end scripts and styles.
 *
 * @package    Mytheme
 * @subpackage Inc\Frontend
 * @author     Case Theme
 */

namespace Mytheme\Inc\Frontend;

use Mytheme\Inc\Core\Option;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the enqueuing of scripts and styles for the theme's front-end.
 */
class Enqueue {

    private $version;
	private $option;

    public function __construct( Option $option_instance, $theme_version) {
        $this->option = $option_instance;
		$this->version = $theme_version;
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_assets'] );
	}

    public function enqueue_assets(){
        $this->enqueue_styles();
		$this->enqueue_scripts();
    }

    public function enqueue_scripts() {
        // wp_enqueue_script('jquery'); 
		wp_enqueue_script_module('app', get_template_directory_uri() . '/assets/js/app.js', [], $this->version, []);
    }

    /**
	 * Enqueues theme stylesheets.
	 */
    public function enqueue_styles() {
        wp_enqueue_style('mytheme-style', get_template_directory_uri() . '/assets/css/style.min.css', [], $this->version);
        wp_add_inline_style( 'mytheme-style', $this->render_inline_styles() );
        wp_enqueue_style('mytheme-wp-block-style', get_template_directory_uri() . '/assets/css/wp-block.css', [], $this->version);

        // wp_enqueue_style('mytheme-custom-style', get_template_directory_uri() . '/assets/css/custom-style.css', $this->version);

        // // Enquence Google Font
        $google_font_url = $this->get_google_fonts_url();
        if ( ! empty( $google_font_url ) ) {
            wp_enqueue_style( 'mytheme-google-fonts', $google_font_url, [], $this->version );
        }
    }

    /**
	 * Generates the Google Fonts URL.
	 *
	 * @return string The final Google Fonts URL.
	 */
	public function get_google_fonts_url() {
		$fonts = [];
		if ( 'off' !== _x( 'on', 'DM Sans font: on or off', 'mytheme' ) ) {
			$fonts[] = 'DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000';
		}
		if ( empty( $fonts ) ) {
			return '';
		}
		$query_string     = implode( '&family=', $fonts );
		$google_fonts_url = 'https://fonts.googleapis.com/css2?family=' . $query_string . '&display=swap';

		return $google_fonts_url;
	}

	/**
	 * Generates the global inline CSS for :root variables.
	 *
	 * @return string The generated CSS.
	 */
	public function render_inline_styles() {
		$theme_colors     = $this->get_style_config( 'theme_colors' );
		$link_colors      = $this->get_style_config( 'link' );
		$linear_gradient_colors    = $this->get_style_config( 'linear_gradient' );
		$theme_typography = $this->get_style_config( 'theme_typography' );
		$spacing_block    = $this->get_style_config( 'spacing_block' );

		$breakpoints = [
			'xs' => '575px',
			'sm' => '767px',
			'md' => '991px',
			'lg' => '1199px',
			'xl' => '1399px',
		];

		$container = preg_match('/([\d.]+)([a-z%]*)/', $this->get_style_config( 'container' )['width'], $matches);
		$container_size = isset($matches[1]) ? (float) $matches[1] : 1300;
		$container_unit = $matches[2] ?? 'px';

		ob_start();
			echo ':root{';
				foreach ( $theme_colors as $color => $value ) {
					printf( '--mt-%1$s-color: %2$s;', esc_attr( $color ), esc_attr( $value ) );
				}
				foreach ( $link_colors as $color => $value ) {
					printf( '--mt-link-%1$s: %2$s;', esc_attr( $color ), esc_attr( $value ) );
				}
				foreach ( $linear_gradient_colors as $color => $value ) {
					printf( '--mt-linear-color-%1$s: %2$s;', esc_attr( $color ), esc_attr( $value ) );
				}
				foreach ( $theme_typography as $font => $value ) {
					$font_family = is_array( $value) ? $value['font-family'] : $value;
					printf( '--mt-%1$s-font: %2$s;', esc_attr( $font ), esc_attr( $font_family ) );
				}
				printf( '--mt-container: %1$s;', esc_attr( $container_size . $container_unit ) );
			echo '}';

			foreach ( $spacing_block as $breakpoint_key => $value ) {
				$pd_top = ( $value['padding-top'] ?? '' ) ;
				$pd_bottom = ( $value['padding-bottom'] ?? '')  ;
				if( !empty( $pd_top ) ) {
					if( !empty( $breakpoint_key ) ) {
						printf( 
							'@media screen and (max-width: %1$s) { #main .inner { padding-top: %2$s; } }', 
							esc_attr( $breakpoints[$breakpoint_key] ),
							esc_attr( $pd_top ),
						);
					}else {
						printf( '#main .inner { padding-top: %1$s; }', esc_attr( $pd_top ) );
					}
				}
				if( !empty( $pd_bottom ) ) {
					if( !empty( $breakpoint_key ) ) {
						printf( 
							'@media screen and (max-width: %1$s) { #main .inner { padding-bottom: %2$s; } }', 
							esc_attr( $breakpoints[$breakpoint_key] ),
							esc_attr( $pd_bottom )
						);
					}else {
						printf( '#main .inner { padding-bottom: %1$s; }', esc_attr( $pd_bottom ) );
					}
				}
			}

		return ob_get_clean();
	}

	/**
	 * Private helper to get style configurations.
	 * This is the equivalent of the old mytheme_global_style_config().
	 *
	 * @param string $key The configuration key to retrieve.
	 * @return array The configuration array.
	 */
	public function get_style_config( $key ) {
		$configs = [
			'theme_colors'     => [
				'primary'   => $this->option->get_option( 'primary_color', '#CDF683' ),
				'secondary' => $this->option->get_option( 'secondary_color', '#000' ),
				'third'     => $this->option->get_option( 'third_color', '#EDDD5E' ),
				'body-background'   => $this->option->get_option( 'body_bg_color', '#FFF' ),
				'heading'   => $this->option->get_option( 'heading_color', '#060F16' ),
			],
			'link'             => [
				'color'       => $this->option->get_option( 'link_color', [ 'regular' => '#000' ] )['regular'],
				'hover-color' => $this->option->get_option( 'link_color', [ 'hover' => '#FFF' ] )['hover'],
			],
			'linear_gradient' => $this->option->get_option( 'linear_gradient_color', [ 'from' => '#000', 'to' => '#FFF' ] ),
			'theme_typography' => [
				'primary'   => $this->option->get_option( 'primary_font', 'DM Sans' ),
                'secondary' => $this->option->get_option( 'secondary_font', 'DM Sans' ),
                'third'     => $this->option->get_option( 'third_font', 'DM Sans' ),
                'heading'   => $this->option->get_option( 'heading_font', 'DM Sans' ),
			],
			'spacing_block' => [
				''          =>  $this->option->get_theme_option( 'spacing_block'   , [ 'padding-top' => '', 'padding-bottom' => '' ] ),
				'xl'        =>  $this->option->get_theme_option( 'spacing_block_xl', [ 'padding-top' => '', 'padding-bottom' => '' ] ),
				'lg'        =>  $this->option->get_theme_option( 'spacing_block_lg', [ 'padding-top' => '', 'padding-bottom' => '' ] ),
				'md'        =>  $this->option->get_theme_option( 'spacing_block_md', [ 'padding-top' => '', 'padding-bottom' => '' ] ),
				'sm'        =>  $this->option->get_theme_option( 'spacing_block_sm', [ 'padding-top' => '', 'padding-bottom' => '' ] ),
				'xs'        =>  $this->option->get_theme_option( 'spacing_block_xs', [ 'padding-top' => '', 'padding-bottom' => '' ] ),
			],
			'container' => $this->option->get_theme_option( 'container_width', ['width' => '', 'units' => 'px'] ),
		];
		return isset( $configs[ $key ] ) ? $configs[ $key ] : [];
	}
}