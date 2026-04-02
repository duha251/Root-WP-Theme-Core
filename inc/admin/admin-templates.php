<?php

if( !defined( 'ABSPATH' ) )
	exit; 

class Mytheme_Admin_Templates {

	public function __construct() {
		add_action( 'admin_menu', [$this, 'register_page'], 20 );
	}
 
	public function register_page() {
		add_submenu_page(
			'pxlart',
		    esc_html__( 'Templates', 'mytheme' ),
		    esc_html__( 'Templates', 'mytheme' ),
		    'manage_options',
		    'edit.php?post_type=pxl-template',
		    false
		);
	}
}
new Mytheme_Admin_Templates;
