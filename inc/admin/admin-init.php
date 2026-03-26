<?php
/**
* The MyTheme_Admin initiate the theme admin
*/

if( !defined( 'ABSPATH' ) )
	exit; 

require_once get_template_directory() . '/inc/admin/libs/tgmpa/class-tgm-plugin-activation.php' ; 
require_once get_template_directory() . '/inc/admin/admin-require-plugins.php'; 
class MyTheme_Admin {

	public function __construct() {
		add_action( 'init', [$this, 'init'], 7 ); 
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue'], 99 );
		add_action( 'admin_init', [$this, 'save_plugins'] );
		add_action( 'admin_menu', [$this, 'fix_parent_menu'], 999 ); 
	}

	public function init() {
		 
		require_once get_template_directory() . '/inc/admin/libs/merlin/class-merlin.php';
		require_once get_template_directory() . '/inc/admin/libs/merlin/merlin-config.php';

		require_once get_template_directory() . '/inc/admin/updater/register-admin.php';
		require_once get_template_directory() . '/inc/admin/admin-page.php';
		require_once get_template_directory() . '/inc/admin/admin-dashboard.php';
		require_once get_template_directory() . '/inc/admin/admin-plugins.php' ;
		require_once get_template_directory() . '/inc/admin/admin-user.php' ;


		require_once get_template_directory() . '/inc/admin/admin-import.php';
		if( class_exists('Pxltheme_Core'))
			require_once get_template_directory() . '/inc/admin/admin-templates.php' ;
	}
 	
 	public function enqueue() {
		$pxl_server_info = apply_filters( 'pxl_server_info', ['api_url' => ''] ) ;
		wp_enqueue_style( 'pxlart-dashboard', get_template_directory_uri() . '/inc/admin/assets/css/dashboard.css' );

		if ( ! did_action( 'wp_enqueue_media' ) ) {
	        wp_enqueue_media();
	    }
		wp_enqueue_script( 'pxlart-admin', get_template_directory_uri() . '/inc/admin/assets/js/admin.js', array( 'jquery'), false, true );
		wp_localize_script( 'pxlart-admin', 'pxlart_admin', array(
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'wpnonce'        => wp_create_nonce( 'merlin_nonce' ),
			'api_url' 		 => $pxl_server_info['api_url'],
			'theme_slug'     => mytheme()->get_theme_slug(),
		));
	}

	  
	public function save_plugins() {

        if ( !current_user_can( 'edit_theme_options' ) ) {
            return;
        }

		// Deactivate Plugin
        if ( isset( $_GET['pxl-deactivate'] ) && 'deactivate-plugin' == sanitize_text_field($_GET['pxl-deactivate']) ) {

			check_admin_referer( 'pxl-deactivate', 'pxl-deactivate-nonce' );

			$plugins = TGM_Plugin_Activation::$instance->plugins;

			foreach( $plugins as $plugin ) {
				if ( $plugin['slug'] == sanitize_text_field($_GET['plugin']) ) {

					deactivate_plugins( $plugin['file_path'] );

                    wp_redirect( admin_url( 'admin.php?page=' . sanitize_text_field($_GET['page']) ) );
					exit;
				}
			}
		}

		// Activate plugin
		if ( isset( $_GET['pxl-activate'] ) && 'activate-plugin' == sanitize_text_field($_GET['pxl-activate']) ) {

			check_admin_referer( 'pxl-activate', 'pxl-activate-nonce' );

			$plugins = TGM_Plugin_Activation::$instance->plugins;

			foreach( $plugins as $plugin ) {
				if ( $plugin['slug'] == sanitize_text_field($_GET['plugin']) ) {

					activate_plugin( $plugin['file_path'] );

					wp_redirect( admin_url( 'admin.php?page=' . sanitize_text_field($_GET['page']) ) );
					exit;
				}
			}
		}
    }

    public function fix_parent_menu() {

        if ( !current_user_can( 'edit_theme_options' ) ) {
            return;
        }
		 
		global $submenu;
 
		$submenu['pxlart'][0][0] = esc_html__( 'Dashboard', 'mytheme' );

		//remove_submenu_page( 'themes.php', 'tgmpa-install-plugins' );
		remove_submenu_page( 'tools.php', 'redux-about' );
	}
}
 
new MyTheme_Admin;

