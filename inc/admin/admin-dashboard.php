<?php
/**
* The MyTheme_Admin_Dashboard base class
*/

if( !defined( 'ABSPATH' ) )
	exit; 

class MyTheme_Admin_Dashboard extends MyTheme_Admin_Page {
	protected $id = null;
	protected $page_title = null;
	protected $menu_title = null;
	public $position = null;
	public function __construct() {
		$this->id = 'pxlart';
		$this->page_title = mytheme()->get_theme_name();
		$this->menu_title = mytheme()->get_theme_name();
		$this->position = '50';

		parent::__construct();
	}

	public function display() {
		mytheme_get_template( 'inc/admin/views/admin-dashboard' );
	}
 
	public function save() {

	}
}
new MyTheme_Admin_Dashboard;
