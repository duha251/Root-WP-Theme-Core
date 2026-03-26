<?php
/**
 * Merlin WP
 * Better WordPress Theme Onboarding
 *
 * The following code is a derivative work from the
 * Envato WordPress Theme Setup Wizard by David Baker.
 *
 * @package   Merlin WP
 * @version   1.0.0
 * @link      https://merlinwp.com/
 * @author    Rich Tabor, from ThemeBeans.com & the team at ProteusThemes.com
 * @copyright Copyright (c) 2018, Merlin WP of Inventionn LLC
 * @license   Licensed GPLv3 for Open Source Use
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Merlin.
 */
class Merlin {
	/**
	 * Current theme.
	 *
	 * @var object WP_Theme
	 */
	protected $theme;

	/**
	 * Current step.
	 *
	 * @var string
	 */
	protected $step = '';

	/**
	 * Steps.
	 *
	 * @var    array
	 */
	protected $steps = array();

	/**
	 * TGMPA instance.
	 *
	 * @var    object
	 */
	protected $tgmpa;

	/**
	 * Importer.
	 *
	 * @var    array
	 */
	protected $importer;

	/**
	 * WP Hook class.
	 *
	 * @var Merlin_Hooks
	 */
	protected $hooks;

	/**
	 * Holds the verified import files.
	 *
	 * @var array
	 */
	public $import_files;

	/**
	 * The base import file name.
	 *
	 * @var string
	 */
	public $import_file_base_name;

	/**
	 * Helper.
	 *
	 * @var    array
	 */
	protected $helper;

	/**
	 * Updater.
	 *
	 * @var    array
	 */
	protected $updater;

	/**
	 * The text string array.
	 *
	 * @var array $strings
	 */
	protected $strings = null;

	/**
	 * The base path where Merlin is located.
	 *
	 * @var array $strings
	 */
	protected $base_path = null;

	/**
	 * The base url where Merlin is located.
	 *
	 * @var array $strings
	 */
	protected $base_url = null;

	/**
	 * The location where Merlin is located within the theme or plugin.
	 *
	 * @var string $directory
	 */
	protected $directory = null;

	/**
	 * Top level admin page.
	 *
	 * @var string $merlin_url
	 */
	protected $merlin_url = null;

	/**
	 * The wp-admin parent page slug for the admin menu item.
	 *
	 * @var string $parent_slug
	 */
	protected $parent_slug = null;

	/**
	 * The capability required for this menu to be displayed to the user.
	 *
	 * @var string $capability
	 */
	protected $capability = null;

	/**
	 * The URL for the "Learn more about child themes" link.
	 *
	 * @var string $child_action_btn_url
	 */
	protected $child_action_btn_url = null;

	/**
	 * The flag, to mark, if the theme license step should be enabled.
	 *
	 * @var boolean $license_step_enabled
	 */
	protected $license_step_enabled = false;

	/**
	 * The URL for the "Where can I find the license key?" link.
	 *
	 * @var string $theme_license_help_url
	 */
	protected $theme_license_help_url = null;

	/**
	 * Remove the "Skip" button, if required.
	 *
	 * @var string $license_required
	 */
	protected $license_required = null;

	/**
	 * The item name of the EDD product (this theme).
	 *
	 * @var string $edd_item_name
	 */
	protected $edd_item_name = null;

	/**
	 * The theme slug of the EDD product (this theme).
	 *
	 * @var string $edd_theme_slug
	 */
	protected $edd_theme_slug = null;

	/**
	 * The remote_api_url of the EDD shop.
	 *
	 * @var string $edd_remote_api_url
	 */
	protected $edd_remote_api_url = null;

	/**
	 * Turn on dev mode if you're developing.
	 *
	 * @var string $dev_mode
	 */
	protected $dev_mode = false;

	/**
	 * Ignore.
	 *
	 * @var string $ignore
	 */
	public $ignore = null;

	public $ready_big_button_url = '';
	public $slug = '';
	public $hook_suffix = '';

	/**
	 * Setup plugin version.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function version() {

		if ( ! defined( 'MERLIN_VERSION' ) ) {
			define( 'MERLIN_VERSION', '1.0.0' );
		}
	}

	/**
	 * Class Constructor.
	 *
	 * @param array $config Package-specific configuration args.
	 * @param array $strings Text for the different elements.
	 */
	function __construct( $config = array(), $strings = array() ) {

		$this->version();

		$config = wp_parse_args(
			$config, array(
				'base_path'            => get_parent_theme_file_path() . '/inc/admin/libs/',
				'base_url'             => get_parent_theme_file_uri() . '/inc/admin/libs/',
				'directory'            => 'merlin',
				'merlin_url'           => 'merlin',
				'parent_slug'          => 'admin.php',
				'capability'           => 'manage_options',
				'child_action_btn_url' => '',
				'dev_mode'             => '',
				'ready_big_button_url' => home_url( '/' ),
			)
		);

		// Set config arguments.
		$this->base_path              = $config['base_path'];
		$this->base_url               = $config['base_url'];
		$this->directory              = $config['directory'];
		$this->merlin_url             = $config['merlin_url'];
		$this->parent_slug            = $config['parent_slug'];
		$this->capability             = $config['capability'];
		$this->child_action_btn_url   = $config['child_action_btn_url'];
		$this->license_step_enabled   = $config['license_step'];
		$this->theme_license_help_url = $config['license_help_url'];
		$this->license_required       = $config['license_required'];
		$this->edd_item_name          = $config['edd_item_name'];
		$this->edd_theme_slug         = $config['edd_theme_slug'];
		$this->edd_remote_api_url     = $config['edd_remote_api_url'];
		$this->dev_mode               = $config['dev_mode'];
		$this->ready_big_button_url   = $config['ready_big_button_url'];

		// Strings passed in from the config file.
		$this->strings = $strings;

		// Retrieve a WP_Theme object.
		$this->theme = wp_get_theme();
		$this->slug  = strtolower( preg_replace( '#[^a-zA-Z]#', '', $this->theme->template ) );

		// Set the ignore option.
		$this->ignore = $this->slug . '_ignore';

		// Is Dev Mode turned on?
		if ( true !== $this->dev_mode ) {

			// Has this theme been setup yet?
			$already_setup = get_option( 'merlin_' . $this->slug . '_completed' );
			// Return if Merlin has already completed it's setup.
			if ( $already_setup ) {
				return;
			}
		}

		$pxl_import_demo_id = get_option('pxl_import_demo_id',false);
		if( !empty($pxl_import_demo_id) ){
			return;
		}

		// Get TGMPA.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();
		}

		add_action( 'admin_init', array( $this, 'admin_handler' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_transient' ), 30 ); //knight
		add_action( 'admin_init', array( $this, 'redirect' ), 30 );
		add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
		add_action( 'admin_init', array( $this, 'steps' ), 30, 0 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_page' ), 30, 0 );
		add_action( 'admin_init', array( $this, 'ignore' ), 5 );
		add_filter( 'tgmpa_load', array( $this, 'load_tgmpa' ), 10, 1 );
		add_action( 'wp_ajax_merlin_plugins', array( $this, '_ajax_plugins' ), 10, 0 );
	}

	/**
	 * Require necessary classes.
	 */
	function admin_handler() {
		error_reporting(0); // Hide errors
	}

	/**
	 * Set redirection transient on theme switch.
	 */
	public function switch_theme() {
		if ( ! is_child_theme() ) {
			set_transient( $this->theme->template . '_merlin_redirect', 1 );
		}
	}

	public function maybe_redirect_transient() {
		delete_transient( 'elementor_activation_redirect' );
		delete_transient( '_wc_activation_redirect' );
	}

	/**
	 * Redirection transient.
	 */
	public function redirect() {

		if ( ! get_transient( $this->theme->template . '_merlin_redirect' ) ) {
			return;
		}

		delete_transient( $this->theme->template . '_merlin_redirect' );

		//wp_safe_redirect( menu_page_url( $this->merlin_url ) );
		wp_safe_redirect( admin_url( 'admin.php?page='.$this->merlin_url )); 

		exit;
	}

	

	/**
	 * Give the user the ability to ignore Merlin WP.
	 */
	public function ignore() {

		// Bail out if not on correct page.
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'merlinwp-ignore-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->ignore ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		update_option( 'merlin_' . $this->slug . '_completed', 'ignored' );
	}

	/**
	 * Conditionally load TGMPA
	 *
	 * @param string $status User's manage capabilities.
	 */
	public function load_tgmpa( $status ) {
		return is_admin() || current_user_can( 'install_themes' );
	}


	/**
	 * Add the admin menu item, under Appearance.
	 */
	public function add_admin_menu() {

		// Strings passed in from the config file.
		$strings = $this->strings;
		$this->hook_suffix = add_submenu_page(
			esc_html( $this->parent_slug ), esc_html( $strings['admin-menu'] ), esc_html( $strings['admin-menu'] ), sanitize_key( $this->capability ), sanitize_key( $this->merlin_url ), array( $this, 'admin_page' )
		);
	}

	/**
	 * Add the admin page.
	 */
	public function admin_page() {

		// Strings passed in from the config file.
		$strings = $this->strings;
		$pxl_server_info = apply_filters( 'pxl_server_info', ['api_url' => ''] ) ;
		$theme_slug = apply_filters( 'pxl_demo_item_download', get_option( 'template' ) );
		// Do not proceed, if we're not on the right page.
		if ( empty( $_GET['page'] ) || $this->merlin_url !== $_GET['page'] ) {
			return;
		}

		if ( ob_get_length() ) {
			ob_end_clean();
		}

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		// Use minified libraries if dev mode is turned on.
		$suffix = ( ( true === $this->dev_mode ) ) ? '' : '.min';

		// Enqueue styles.
		wp_enqueue_style( 'merlin', trailingslashit( $this->base_url ) . $this->directory . '/assets/css/merlin' . $suffix . '.css', array( 'wp-admin' ), MERLIN_VERSION );
		wp_enqueue_style( 'pxlart-dashboard', get_template_directory_uri() . '/inc/admin/assets/css/dashboard.css' );
		// Enqueue javascript.
		wp_enqueue_script( 'merlin', trailingslashit( $this->base_url ) . $this->directory . '/assets/js/merlin' . $suffix . '.js', array( 'jquery-core' ), MERLIN_VERSION );
		 
		wp_enqueue_script( 'pxlart-admin', get_template_directory_uri() . '/inc/admin/assets/js/admin.js', array( 'jquery'), false, true );

		$texts = array(
			'something_went_wrong' => esc_html__( 'Something went wrong. Please refresh the page and try again!', 'mytheme' ),
		);

		// Localize the javascript.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			// Check first if TMGPA is included.
			wp_localize_script(
				'merlin', 'merlin_params', array(
					'tgm_plugin_nonce' => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'tgm_bulk_url'     => $this->tgmpa->get_tgmpa_url(),
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'wpnonce'          => wp_create_nonce( 'merlin_nonce' ),
					'api_url' 		 => $pxl_server_info['api_url'],
					'theme_slug'     => $theme_slug,
					'texts'            => $texts,
				)
			);
		} else {
			// If TMGPA is not included.
			wp_localize_script(
				'merlin', 'merlin_params', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'wpnonce' => wp_create_nonce( 'merlin_nonce' ),
					'api_url' 		 => $pxl_server_info['api_url'],
					'theme_slug'     => $theme_slug,
					'texts'   => $texts,
				)
			);
		}

		ob_start();

		/**
		 * Start the actual page content.
		 */
		$this->header(); ?>

		<div class="merlin__wrapper">

			<div class="merlin__content merlin__content--<?php echo esc_attr( strtolower( $this->steps[ $this->step ]['name'] ) ); ?>">

				<?php $this->step_output(); ?>

				<?php
				// Content Handlers.
				$show_content = true;

				if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
					$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
				}

				if ( $show_content ) {
					$this->body();
				}
				?>
 

			</div>

			<?php echo sprintf( '<a class="return-to-dashboard" href="%s">%s</a>', esc_url( admin_url( '/' ) ), esc_html( $strings['return-to-dashboard'] ) ); ?>

			<?php $ignore_url = wp_nonce_url( admin_url( '?' . $this->ignore . '=true' ), 'merlinwp-ignore-nounce' ); ?>

			<?php echo sprintf( '<a class="return-to-dashboard ignore" href="%s">%s</a>', esc_url( $ignore_url ), esc_html( $strings['ignore'] ) ); ?>

		</div>

		<?php $this->footer(); ?>

		<?php
		exit;
	}

	/**
	 * Output the header.
	 */
	protected function header() {

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Get the current step.
		$current_step = strtolower( $this->steps[ $this->step ]['name'] );
		?>

		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<?php printf( esc_html( $strings['title%s%s%s%s'] ), '<ti', 'tle>', esc_html( $this->theme->name ), '</title>' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php //do_action( 'admin_head' ); ?>
		</head>
		<body class="merlin__body merlin__body--<?php echo esc_attr( $current_step ); ?>">
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	protected function body() {
		isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
	}

	/**
	 * Output the footer.
	 */
	protected function footer() {
		?>
		</body>
		<?php do_action( 'admin_footer' ); ?>
		<?php do_action( 'admin_print_footer_scripts' ); ?>
		</html>
		<?php
	}
 
	/**
	 * Setup steps.
	 */
	public function steps() {

		$this->steps = array(
			'welcome' => array(
				'name'    => esc_html__( 'Welcome', 'mytheme' ),
				'view'    => array( $this, 'welcome' ),
				'handler' => array( $this, 'welcome_handler' ),
			),
		);

		if ( $this->license_step_enabled ) {
			$this->steps['license'] = array(
				'name' => esc_html__( 'License', 'mytheme' ),
				'view' => array( $this, 'license' ),
			);
		}

		// Show the plugin importer, only if TGMPA is included.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->steps['plugins'] = array(
				'name' => esc_html__( 'Plugins', 'mytheme' ),
				'view' => array( $this, 'plugins' ),
			);
		}

		// Show the content importer, only if there's demo content added.
		 
		$this->steps['content'] = array(
			'name' => esc_html__( 'Content', 'mytheme' ),
			'view' => array( $this, 'content' ),
		);

		$this->steps['ready'] = array(
			'name' => esc_html__( 'Ready', 'mytheme' ),
			'view' => array( $this, 'ready' ),
		);

		$this->steps = apply_filters( $this->theme->template . '_merlin_steps', $this->steps );
	}

	/**
	 * Output the steps
	 */
	protected function step_output() {
		$ouput_steps  = $this->steps;
		$array_keys   = array_keys( $this->steps );
		$current_step = array_search( $this->step, $array_keys, true );

		array_shift( $ouput_steps );

		$strings = $this->strings;
		?>

		<ul class="merlin-content-steps">

			<?php
			$i = 1;
			foreach ( $ouput_steps as $step_key => $step ) :

				$class_attr = '';
				$show_link  = false;

				if ( $step_key === $this->step ) {
					$class_attr = 'active';
				} elseif ( $current_step > array_search( $step_key, $array_keys, true ) ) {
					$class_attr = 'done';
					$show_link  = true;
				}
				?>

				<li class="<?php echo esc_attr( $class_attr ); ?>">
					<div class="step-num"><?php echo esc_html($i); ?></div>
					<div class="step-title"><?php echo esc_attr($step_key) === "content" ? $strings['import-header'] : $strings[$step_key.'-header'] ?></div>
					<a href="<?php echo esc_url( $this->step_link( $step_key ) ); ?>" title="<?php echo esc_attr( $step['name'] ); ?>"></a>
				</li>

			<?php $i++; endforeach; ?>

		</ul>

		<?php
	}

	/**
	 * Get the step URL.
	 *
	 * @param string $step Name of the step, appended to the URL.
	 */
	protected function step_link( $step ) {
		return add_query_arg( 'step', $step );
	}

	/**
	 * Get the next step link.
	 */
	protected function step_next_link() {
		$keys = array_keys( $this->steps );
		$step = array_search( $this->step, $keys, true ) + 1;

		return add_query_arg( 'step', $keys[ $step ] );
	}

	protected function step_previous_link() {
		$keys = array_keys( $this->steps );
		$step = array_search( $this->step, $keys, true ) - 1;

		return add_query_arg( 'step', $keys[ $step ] );
	}

	/**
	 * Introduction step
	 */
	protected function welcome() {

		// Has this theme been setup yet? Compare this to the option set when you get to the last panel.
		$already_setup = get_option( 'merlin_' . $this->slug . '_completed' );

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = ! $already_setup ? $strings['welcome-header%s'] : $strings['welcome-header-success%s'];
		$paragraph = ! $already_setup ? $strings['welcome%s'] : $strings['welcome-success%s'];
		$start     = $strings['btn-start'];
		$no        = $strings['btn-no'];
		?>

		<div class="merlin__content--transition">

			<h1><?php echo esc_html( sprintf( $header, $theme ) ); ?></h1>

			<p><?php echo esc_html( sprintf( $paragraph, $theme ) ); ?></p>

			<img src="<?php echo(esc_url( get_template_directory_uri(). '/inc/admin/assets/img/import-start.png' )) ?>" width="380" />

		</div>

		<footer class="merlin__content__footer">
			<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '/' ) ); ?>" class="merlin__button merlin__button--skip"><?php echo esc_html( $no ); ?></a>
			<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $start ); ?></a>
			<?php wp_nonce_field( 'merlin' ); ?>
		</footer>
	<?php
	}

	/**
	 * Handles save button from welcome page.
	 * This is to perform tasks when the setup wizard has already been run.
	 */
	protected function welcome_handler() {

		check_admin_referer( 'merlin' );

		return false;
	}

	/**
	 * Theme EDD license step.
	 */
	protected function license() {
		$is_theme_registered = $this->is_theme_registered();
		$action_url          = $this->theme_license_help_url;
		$required            = $this->license_required;

		$is_theme_registered_class = ( $is_theme_registered ) ? ' is-registered' : null;

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = ! $is_theme_registered ? $strings['license-header'] : $strings['license-header-success%s'];
		$header2    = ! $is_theme_registered ? $strings['license-header2'] : $strings['license-header-success%s']; 
		$action    = $strings['license-tooltip'];
		$label     = $strings['license-label'];
		$skip      = $strings['btn-license-skip'];
		$next      = $strings['btn-next']; 
		$paragraph = ! $is_theme_registered ? $strings['license%s'] : $strings['license-success%s'];
		$install   = $strings['btn-license-activate']; 
		$register = new MyTheme_Register;
		?>

		<?php 
			$theme_slug = get_template(); 
			if ( 'valid' == get_option( $theme_slug.'_purchase_code_status', false ) ) {
				wp_redirect(admin_url('admin.php?page=pxlart-setup&step=plugins'));
			}
		?>
		<div class="merlin__content--transition">

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( sprintf( $header2, $theme ) ); ?></h1>

			<p id="license-text"><?php echo esc_html( sprintf( $paragraph ) ); ?></p>

			<?php  if ( ! $is_theme_registered ) : ?>
				<div class="merlin__content--license-key">
					<div class="pxl-regis-box pxl-regis-box-solid pxl-regis-register-box">
						<div class="pxl-regis-box-head">
							<?php $register->messages(true); ?>
						</div> 
					</div> 
				</div>

			<?php endif; ?>

		</div>

		<footer class="merlin__content__footer <?php echo esc_attr( $is_theme_registered_class ); ?>">

			<?php if ( ! $is_theme_registered ) : ?>

				<?php if ( ! $required ) : ?>
					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
				<?php endif ?>
 
			<?php else : ?>
				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $next ); ?></a>
			<?php endif; ?>
			<?php wp_nonce_field( 'merlin' ); ?>
		</footer>
		<?php
	}


	/**
	 * Check, if the theme is currently registered.
	 *
	 * @return boolean
	 */
	private function is_theme_registered() {
		$dev_mode = apply_filters( 'pxl_set_dev_mode', (defined('DEV_MODE') && DEV_MODE)) ;
		if( $dev_mode === true) return true;
		$is_registered = get_option( $this->edd_theme_slug . '_license_key_status', false ) === 'valid';
		return apply_filters( 'merlin_is_theme_registered', $is_registered );
	}
 
	/**
	 * Theme plugins
	 */
	protected function plugins() {

		// Variables.
		$url    = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'merlin' );
		$method = '';
		$fields = array_keys( $_POST );
		$creds  = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields );

		tgmpa_load_bulk_installer();

		if ( false === $creds ) {
			return true;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
			return true;
		}

		// Are there plugins that need installing/activating?
		$plugins          = $this->get_tgmpa_plugins();
		$required_plugins = $recommended_plugins = array();
		$count            = count( $plugins['all'] );
		$class            = $count ? null : 'no-plugins';

		// Split the plugins into required and recommended.
		foreach ( $plugins['all'] as $slug => $plugin ) {
			if ( ! empty( $plugin['required'] ) ) {
				$required_plugins[ $slug ] = $plugin;
			} else {
				$recommended_plugins[ $slug ] = $plugin;
			}
		}

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $count ? $strings['plugins-header'] : $strings['plugins-header-success'];
		$paragraph = $count ? $strings['plugins'] : $strings['plugins-success%s'];
		$action    = $strings['plugins-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$install   = $strings['btn-plugins-install'];
		?>

		<div class="merlin__content--transition">

			<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>

			<h1><?php echo esc_html( $header ); ?></h1>

			<p><?php echo esc_html( $paragraph ); ?></p>

			<?php if ( $count ) { ?>
				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout"><span><?php echo esc_html( $action ); ?></span><span class="chevron"></span></a>
			<?php } ?>

		</div>

		<form action="" method="post">

			<?php if ( $count ) : ?>

				<ul class="merlin__drawer merlin__drawer--install-plugins">

				<?php if ( ! empty( $required_plugins ) ) : ?>
					<?php foreach ( $required_plugins as $slug => $plugin ) : ?>
						<li data-slug="<?php echo esc_attr( $slug ); ?>">
							<input type="checkbox" name="default_plugins[<?php echo esc_attr( $slug ); ?>]" class="checkbox" id="default_plugins_<?php echo esc_attr( $slug ); ?>" value="1" checked>

							<label for="default_plugins_<?php echo esc_attr( $slug ); ?>">
								<i></i>

								<span><?php echo esc_html( $plugin['name'] ); ?></span>

								<span class="badge">
									<span class="hint--top" aria-label="<?php esc_html_e( 'Required', 'mytheme' ); ?>">
										<?php esc_html_e( 'Required', 'mytheme' ); ?>
									</span>
								</span>
							</label>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $recommended_plugins ) ) : ?>
					<?php foreach ( $recommended_plugins as $slug => $plugin ) : ?>
						<li data-slug="<?php echo esc_attr( $slug ); ?>">
							<input type="checkbox" name="default_plugins[<?php echo esc_attr( $slug ); ?>]" class="checkbox" id="default_plugins_<?php echo esc_attr( $slug ); ?>" value="1" checked>

							<label for="default_plugins_<?php echo esc_attr( $slug ); ?>">
								<i></i><span><?php echo esc_html( $plugin['name'] ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>

				</ul>

			<?php endif; ?>

			<footer class="merlin__content__footer <?php echo esc_attr( $class ); ?>">
				<?php if ( $count ) : ?>
					<a id="close" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--closer merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
					<a id="skip" href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( $skip ); ?></a>
					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next button-next" data-callback="install_plugins">
						<span class="merlin__button--loading__text"><?php echo esc_html( $install ); ?></span>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange"><?php echo esc_html( $next ); ?></a>
				<?php endif; ?>
				<?php wp_nonce_field( 'merlin' ); ?>
			</footer>
		</form>

	<?php
	}

	/**
	 * Page setup
	 */
	protected function content() { 
		 
		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $strings['import-header'];
		$paragraph = $strings['import'];
		$action    = $strings['import-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$import    = $strings['btn-import'];
		?>

		<h1><?php echo esc_html( $header ); ?></h1>

		<p><?php echo esc_html( $paragraph ); ?></p>

		<?php mytheme_get_template( 'inc/admin/views/admin-demos' ); ?>
 
		<footer class="merlin__content__footer" style="margin-top: 1em">

				<a href="<?php echo esc_url( $this->step_previous_link() ); ?>" class="merlin__button merlin__button--skip merlin__button--proceed"><?php echo esc_html( 'Back to Install Plugins' ); ?></a>

				<a href="<?php echo esc_url( $this->step_next_link() ); ?>" class="merlin__button merlin__button--skip" >
					<?php echo esc_html( $skip ); ?>
				</a>

		</footer>
		  
	<?php
	}


	/**
	 * Final step
	 */
	protected function ready() {

		// Author name.
		$author = $this->theme->author;

		// Theme Name.
		$theme = ucfirst( $this->theme );

		// Remove "Child" from the current theme name, if it's installed.
		$theme = str_replace( ' Child', '', $theme );

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header    = $strings['ready-header'];
		$paragraph = $strings['ready%s'];
		$action    = $strings['ready-action-link'];
		$skip      = $strings['btn-skip'];
		$next      = $strings['btn-next'];
		$big_btn   = $strings['ready-big-button'];

		// Links.
		$links = array();

		for ( $i = 1; $i < 4; $i++ ) {
			if ( ! empty( $strings[ "ready-link-$i" ] ) ) {
				$links[] = $strings[ "ready-link-$i" ];
			}
		}

		$links_class = empty( $links ) ? 'merlin__content__footer--nolinks' : null;

		$allowed_html_array = array(
			'a' => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
		);

		update_option( 'merlin_' . $this->slug . '_completed', time() );
		?>

		<div class="merlin__content--transition">

			<h1><?php echo esc_html( sprintf( $header, $theme ) ); ?></h1>

			<p><?php wp_kses( printf( $paragraph, $author ), $allowed_html_array ); ?></p>

		</div>

		<footer class="merlin__content__footer merlin__content__footer--fullwidth <?php echo esc_attr( $links_class ); ?>">

			<a href="<?php echo esc_url( $this->ready_big_button_url ); ?>" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin"><?php echo esc_html( $big_btn ); ?></a>

			<?php if ( ! empty( $links ) ) : ?>
				<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout"><span><?php echo esc_html( $action ); ?></span><span class="chevron"></span></a>

				<ul class="merlin__drawer merlin__drawer--extras">

					<?php foreach ( $links as $link ) : ?>
						<li><?php echo wp_kses( $link, $allowed_html_array ); ?></li>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		</footer>

	<?php
	}

	/**
	 * Get registered TGMPA plugins
	 *
	 * @return    array
	 */
	protected function get_tgmpa_plugins() {
		$plugins = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $this->tgmpa->plugins as $slug => $plugin ) { 
			if ( $this->tgmpa->is_plugin_active( $slug ) && false === $this->tgmpa->does_plugin_have_update( $slug ) ) {
				continue;
			} else { 
				$plugins['all'][ $slug ] = $plugin;
				if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}
					if ( $this->tgmpa->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}
			}
		} 

		return $plugins;
	}
   
	/**
	 * Do plugins' AJAX
	 *
	 * @internal    Used as a calback.
	 */
	function _ajax_plugins() {
		
		if ( ! check_ajax_referer( 'merlin_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
			exit( 0 );
		}

		$json      = array();
		$tgmpa_url = $this->tgmpa->get_tgmpa_url();
		$plugins   = $this->get_tgmpa_plugins();
		 
		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( sanitize_title($_POST['slug']) === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'message'       => esc_html__( 'Activating', 'mytheme' ),
				);
				break;
			}
		}

		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( sanitize_title($_POST['slug']) === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'message'       => esc_html__( 'Updating', 'mytheme' ),
				);
				break;
			}
		}

		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( sanitize_title($_POST['slug']) === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'message'       => esc_html__( 'Installing', 'mytheme' ),
				);
				break;
			}
		}
 
		if ( $json ) {
			$json['hash']    = md5( serialize( $json ) );
			$json['message'] = esc_html__( 'Installing', 'mytheme' );
			wp_send_json( $json );
		} else {
			wp_send_json(
				array(
					'done'    => 1,
					'message' => esc_html__( 'Success', 'mytheme' ),
				)
			);
		}

		exit;
	}
     
}
