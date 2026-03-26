<?php
namespace MyTheme\Inc\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Option {

    /**
     * Cache options theme
     * @var array
     */
    private static $options = [];

    /**
     * Name option in database.
     * @var string
     */
    private $option_name = 'pxl_theme_options';

    public function __construct() {
    }

    public function get_option_name() {
        if ( isset($_POST['opt_name']) && !empty($_POST['opt_name']) ) {
            return $_POST['opt_name'];
        }
        if ( defined('ICL_LANGUAGE_CODE') ) {
            if ( ICL_LANGUAGE_CODE != 'all' && !empty(ICL_LANGUAGE_CODE) ) {
                return $this->option_name . '_' . ICL_LANGUAGE_CODE;
            }
        }
        return $this->option_name;
    }

    public function set_option_name( $option_name ) {
        $this->option_name = $option_name;
        return $this;
    }

    public function get_theme_option( $setting = null, $default = false, $subset = false ) {
		if (is_null($setting) || empty($setting)) {
			return '';
		}

		if (empty(self::$options)) {
			self::$options = $this->get_options();
		}
		if (empty(self::$options) || ! isset( self::$options[ $setting ] ) || self::$options[ $setting ] === ''){
			if ( $subset && !empty($subset)) 
				return $default[$subset];
			else
				return $default;
		}

		if(is_array(self::$options[$setting])) {
			if( is_array($default) ){
				foreach (self::$options[$setting] as $key => $value){
					if(empty(self::$options[$setting][$key]) && isset($default[$key]))
						self::$options[$setting][$key] = $default[$key];
				}
			}else{
				foreach (self::$options[$setting] as $key => $value){
					if(empty(self::$options[$setting][$key]) && isset($default))
						self::$options[$setting][$key] = $default;
						
				}
			}
		} 
		if (!$subset || empty($subset)) {
			return self::$options[$setting];
		}
		if (isset(self::$options[$setting][$subset])) {
			return self::$options[$setting][$subset];
		}

		return self::$options;
    }

    public function get_page_option( $setting = null, $default = false, $subset = false ) {
		if (is_null($setting) || empty($setting)) {
			return '';
		}

		$id = get_the_ID();

		if(function_exists('is_shop') && is_shop()){
			$real_page = get_post(wc_get_page_id('shop'));
		}else{
			$real_page =  get_queried_object();
		}

		if($real_page instanceof WP_Post){
			$id = $real_page->ID;
		}
		
		$options = !empty($id) && ('' !== get_post_meta($id, $setting, true)) ? get_post_meta($id, $setting, true) : $default;
		if( !empty($id) && ('' !== get_post_meta($id, $setting, true)) ){
			$options = get_post_meta($id, $setting, true);
			if(is_array($options)) {
				if( is_array($default) ){
					foreach ($options as $key => $value){
						if(empty($options[$key]) && isset($default[$key]))
							$options[$key] = $default[$key];
					}
				}else{
					foreach ($options as $key => $value){
						if(empty($options[$key]) && isset($default))
							$options[$key] = $default;
							
					}
				}
			}
		}else{
			$options = $default;
		}
		

		if ($subset && !empty($subset)) {  
			if (isset($options[$subset])) {
				$options = $options[$subset];
			}
		} 
		return $options;
    }

    public function get_option( $setting = null, $default = false, $subset = false ) {
		if (is_null($setting) || empty($setting)) {
			return '';
		}
			
		$theme_opt = $this->get_theme_option($setting, $default);
		$page_opt  = $this->get_page_option($setting, $theme_opt);
		if( $page_opt !== NULL && $page_opt !== '' && $page_opt !== '-1'){
			if(is_array($page_opt) && is_array($theme_opt)){
				foreach ($page_opt as $key => $value) {
					if(empty($page_opt[$key]) || $page_opt[$key] === 'px'){
						if (isset($theme_opt[$key])) {
							$page_opt[$key] = $theme_opt[$key];
						}
					}
				}
			}
			$theme_opt = $page_opt;
		}

		if ($subset && !empty($subset)) {  
			if (isset($theme_opt[$subset])) {
				$theme_opt = $theme_opt[$subset];
			}
		}
		return $theme_opt;
    }

    public function set_options( $setting, $value ) {
        if ( empty(self::$options) ) {
            self::$options = $this->get_options();
        }

        $options = self::$options;
        $options[$setting] = $value;

        update_option($this->get_option_name(), $options);

        self::$options = $options;

        return $this;
    }

    public function get_options() {
        $options = get_option($this->get_option_name(), []);
        $options = apply_filters('case/setting/options', $options);
        return $options;
    }
}