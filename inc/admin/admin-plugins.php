<?php
/**
* The dashbaord class
*/

if( !defined( 'ABSPATH' ) )
	exit; 

class MyTheme_Admin_Plugins extends MyTheme_Admin_Page {
	protected $id = null;
	protected $page_title = null;
	protected $menu_title = null;
	public $parent = null;
	
	public function __construct() {

		$this->id = 'pxlart-plugins';
		$this->page_title = esc_html__( 'Install Plugins', 'mytheme' );
		$this->menu_title = esc_html__( 'Install Plugins', 'mytheme' );
		$this->parent = 'pxlart';

		parent::__construct();
	}

	public function display() {
		$admin_controller = $this; 
		mytheme_get_template( 'inc/admin/views/admin-plugins', [ 'admin_controller' => $admin_controller ] );
	}

	public function tgmpa_plugin_action( $plugin, $status ) {

		$btn_class = $btn_text = $nonce_url = '';
		$page = admin_url( 'admin.php?page=' . sanitize_text_field($_GET['page']) );

		$sts_cls = 'pxl-plugin-inst';
		switch( $status ) {
			case 'not-installed':
				$btn_class = 'white';
				$btn_text = esc_html_x( 'Install', 'Plugin installation page.', 'mytheme' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'tgmpa-install' => 'install-plugin',
							'return_url' => sanitize_text_field($_GET['page'])
						),
						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-install',
					'tgmpa-nonce'
				);
				$sts_cls .=' not-installed'; 
				break;

			case 'installed':
				$btn_class = 'success';
				$btn_text = esc_html_x( 'Activate', 'Plugin installation page.', 'mytheme' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'pxl-activate' => 'activate-plugin'
						),
						$page
					),
					'pxl-activate',
					'pxl-activate-nonce'
				);
				$sts_cls .=' installed';
				break;

			case 'active':
				$btn_class = 'danger';
				$btn_text = esc_html_x( 'Deactivate', 'Plugin installation page.', 'mytheme' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'pxl-deactivate' => 'deactivate-plugin'
						),
						$page
					),
					'pxl-deactivate',
					'pxl-deactivate-nonce'
				);
				$sts_cls .=' active';
				break;
		}

		$nonce_url_d = wp_nonce_url(
			add_query_arg(
				array(
					'plugin' => urlencode( $plugin['slug'] ),
					'pxl-deactivate' => 'deactivate-plugin'
				),
				$page
			),
			'pxl-deactivate',
			'pxl-deactivate-nonce'
		);
		$btn_text_active = esc_html_x( 'Deactivate', 'Plugin installation page.', 'mytheme' );

		printf(
			'<a class="pxl-button '.$sts_cls.'" href="%4$s" title="%2$s %1$s" data-deactive-url="%5$s" data-text-active="%6$s"><span>%2$s</span></a>',
			$plugin['name'], $btn_text, $btn_class, esc_url( $nonce_url ), esc_url( $nonce_url_d ), $btn_text_active
		);
	}
}
new MyTheme_Admin_Plugins;