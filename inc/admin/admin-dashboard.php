<?php
/**
* The Mytheme_Admin_Dashboard base class
*/

if( !defined( 'ABSPATH' ) )
	exit; 

class Mytheme_Admin_Dashboard extends Mytheme_Admin_Page {
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
new Mytheme_Admin_Dashboard;
