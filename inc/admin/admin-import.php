<?php
/**
* The Mytheme_Admin_Import class
*/

if( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Mytheme_Admin_Import extends Mytheme_Admin_Page {
	protected $id = null;
	protected $page_title = null;
	protected $menu_title = null;
	public $parent = null;
	public function __construct() {

		$this->id = 'pxlart-import-demos';
		$this->page_title = esc_html__( 'Import Demos', 'mytheme' );
		$this->menu_title = esc_html__( 'Import Demos', 'mytheme' );
		$this->parent = 'pxlart';
		//$this->position = '10';

		parent::__construct();
	}

	public function display() {
		mytheme_get_template( 'inc/admin/views/admin-demos' );
	}


	public function save() {

	}
}
new Mytheme_Admin_Import;