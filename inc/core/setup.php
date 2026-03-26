<?php
 namespace MyTheme\Inc\Core;

/**
 * Handles core theme setup and registrations.
 *
 * @package    MyTheme
 * @subpackage Inc\Setup
 */

class Setup {

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'theme_setup' ] );
		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
		add_action( 'init', [ $this, 'blocks_init' ] );
	}

	/**
	 * Main theme setup functionalities. Hooked to 'after_setup_theme'.
	 */
	public function theme_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'mytheme', get_template_directory() . '/languages' );

		// Custom Header
		add_theme_support( 'custom-header' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// set_post_thumbnail_size( 1170, 710 );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus([
			'primary'=> __( 'Primary', 'mytheme' ),
			'primary-mobile' => __( 'Primary Mobile', 'mytheme' ),
		]);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for core custom logo.
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		// add_theme_support( 'post-formats', array (
		//     '',
		// ) );

		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		remove_theme_support('widgets-block-editor'); 

		// Enable default style for block in editor + frontend
		add_theme_support( 'wp-block-styles' );

		// Helps videos, iframes, embed YouTube… automatically expand and contract with the screen.
		add_theme_support( 'responsive-embeds' );

		// Enable HTML5 markup for form, search, comment, gallery...
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		] );

		// Allow user to change background color / image in “Customize”.
		add_theme_support( 'custom-background', [
			'default-color' => 'ffffff',
			'default-image' => '',
		] );

		// Allow block in Gutenberg has option wide and full-width.
		add_theme_support( 'align-wide' );

		// Synchronize styles between editor and frontend
		add_editor_style( 'assets/css/editor-style.css' );
		
	}

	/**
	 * Registers sidebars. Hooked to 'widgets_init'.
	 */
	public function widgets_init() {
		register_sidebar( array(
			'name'          => esc_html__( 'Blog Sidebar', 'mytheme' ),
			'id'            => 'sidebar-blog',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title heading-title title-underline-highlight">',
			'after_title'   => '</h3>',
		) );
		
		if ( class_exists( 'Woocommerce' ) ) {
			register_sidebar( array(
				'name'          => esc_html__( 'Shop Sidebar', 'mytheme' ),
				'id'            => 'sidebar-shop',
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title heading-title title-underline-highlight">',
				'after_title'   => '</h3>',
			) );
		}
	}

	/**
	 * Registers block styles and patterns. Hooked to 'init'.
	 */
	public function blocks_init() {
		// Custom style for Gutenberg block.
		register_block_style( 'core/quote', [
			'name'  => 'fancy-quote',
			'label' => __( 'Fancy Quote', 'mytheme' ),
		] );
		// Register block pattern
		register_block_pattern( 'mytheme/hero-section', [
			'title'       => __( 'Hero Section', 'mytheme' ),
			'description' => __( 'A full-width hero banner with text.', 'mytheme' ),
			'content'     => "<!-- wp:cover --> ... <!-- /wp:cover -->",
		] );
	}


	/**
	 * 
	 */
}