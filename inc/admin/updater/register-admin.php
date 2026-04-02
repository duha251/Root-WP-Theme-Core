<?php
/**
 * The Mytheme_Register initiate the theme engine
 */

if( !defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly

class Mytheme_Register {

	/**
	 * Variables required for the theme updater
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $remote_api_url = null;
	protected $theme_slug = null;
	protected $theme_name = null;
	protected $version = null;
	protected $author = null;
	protected $renew_url = null;
	protected $strings = null;
 
	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $config = array(), $strings = array() ) {
		$config = wp_parse_args( $config, array(
			'remote_api_url' => 'http://api.casethemes.net/',
			'theme_slug'     => mytheme()->get_theme_slug(),
			'theme_name'     => mytheme()->get_theme_name(),
			'version'        => '',
			'author'         => 'Pixelart team',
			'renew_url'      => ''
		) );

		// Set config arguments
		$this->remote_api_url = $config['remote_api_url'];
		$this->theme_slug     = sanitize_key( $config['theme_slug'] );
		$this->theme_name     = $config['theme_name'];
		$this->version        = $config['version'];
		$this->author         = $config['author'];
		$this->renew_url      = $config['renew_url'];

		// Populate version fallback
		if ( '' == $config['version'] ) {
			$theme = wp_get_theme( $this->theme_slug );
			$this->version = $theme->get( 'Version' );
		}

		// Strings passed in from the updater config
		$this->strings = $strings;
 
		add_action( 'admin_init', array( $this, 'register_option' ), 12 );
		add_action( 'admin_init', array( $this, 'remove_key' ), 13);
		add_action( 'admin_init', array( $this, 'updater' ), 14);
		add_action( 'admin_init', array( $this, 'pxl_notice' ), 15);
		add_filter( 'http_request_args', array( $this, 'disable_wporg_request' ), 5, 2 );

	}

	 
	/**
	 * Creates the updater class.
	 *
	 * since 1.0.0
	 */
	function updater() {

		/* If there is no valid license key status, don't allow updates. */
		if ( get_option( $this->theme_slug . '_purchase_code_status', false ) != 'valid' ) {
			remove_action( 'admin_notices', array( TGM_Plugin_Activation::$instance, 'notices' ) );  
			return;
		}

		if ( !class_exists( 'Mytheme_Updater' ) ) {
			// Load our custom theme updater
			mytheme_get_template( 'inc/admin/updater/updater-class' );
		}

		new Mytheme_Updater(
			array(
				'remote_api_url' => $this->remote_api_url,
				'version' 		 => $this->version,
				'license'  => trim( get_option( $this->theme_slug . '_purchase_code' ) ),
			),
			$this->strings
		);
	}
	
	/**
	 * [init_hooks description]
	 * @method init_hooks
	 * @return [type]     [description]
	 */
	public function pxl_notice() {
 		$dev_mode = (defined('DEV_MODE') && DEV_MODE);
 		if( $dev_mode === true) return;
        if ( 'valid' != get_option( $this->theme_slug . '_purchase_code_status', false ) ) {

            if ( ( ! isset( $_GET['page'] ) || 'pxlart' != sanitize_text_field($_GET['page']) ) ) {
                add_action( 'admin_notices', array( $this, 'admin_error' ) );
            } else {
                add_action( 'admin_notices', array( $this, 'admin_notice' ) );

            }
        }
	}
	
	function admin_error() {
		echo '<div class="error"><p>' . sprintf( wp_kses_post( esc_html__( 'The %s theme needs to be registered. %sRegister Now%s', 'mytheme' ) ), mytheme()->get_theme_name(), '<a href="' . admin_url( 'admin.php?page=pxlart') . '">' , '</a>' ) . '</p></div>';
	}
	
	function admin_notice() {
		echo '<div class="notice"><p>'.esc_html__( 'Purchase code is invalid. Need a license for activation', 'mytheme' ).'</p></div>';
	}
	

	function messages($merlin = false){
		$purchase_code = trim( get_option( $this->theme_slug . '_purchase_code' ) );  
		if ( ! $purchase_code ){
			?>
			<div class="pxl-dsb-box-head-inner">
				<h6><?php echo esc_html__( 'Register License', 'mytheme' ) ?></h6>
			</div>
			<?php 
			$this->form();
			?>
			<div class="pxl-dsb-box-foot">
				<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e( 'Can’t find your purchase code?', 'mytheme' ); ?></a>
			</div>
			<?php 
		}else{  
			$this->check_license($merlin);
		}
	} 

	function check_license($merlin) {
		$pxl_server_info = apply_filters( 'pxl_server_info', ['docs_url' => 'https://doc.casethemes.net/', 'support_url' => 'https://casethemes.ticksy.com/'] ) ; 
		$purchase_code = trim( get_option( $this->theme_slug . '_purchase_code' ) ); 
		$api_params = array(
			'action' => 'check_license',
			'license'       => $purchase_code,
			'item_name'  	=> $this->theme_name,
			'url'           => rawurlencode(home_url())
		);
		  
		$license_data = $this->get_api_response( $api_params );
  
		if ( false === $license_data->success ) {
			switch ( $license_data->error ) {
				case 'missing':
					$message = esc_html__( 'This appears to be an invalid license key. Please try again or contact support.', 'mytheme' );
				break;
				case 'item_name_mismatch':
					$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'mytheme' ), $this->theme_name );
				break;
				case 'license_exists':
					$message = esc_html__( 'Your license is not active for this URL.', 'mytheme' );
				break;
				default:
					$message = esc_html__( 'An error occurred, please try again.', 'mytheme' );
				break;
			}
			?>
			<div class="pxl-dsb-confirmation fail">
				<h6><?php echo esc_html__( 'Active false', 'mytheme' ) ?></h6>
				<p><?php echo wp_kses_post( $message ) ?> <a href="<?php echo esc_url($pxl_server_info['docs_url']) ?>" target="_blank"><?php echo esc_html__( 'our help center', 'mytheme' ) ?></a> or <a href="<?php echo esc_url($pxl_server_info['support_url']) ?>" target="_blank"><?php echo esc_html__( 'submit a ticket', 'mytheme' ) ?></a></p>
			</div>
			<?php $this->form(); ?>
			<div class="pxl-dsb-box-foot">
				<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e( 'Can’t find your purchase code?', 'mytheme' ); ?></a>
			</div>
			<?php 
		}else{
			if ( 'valid' === $license_data->license ) {
				update_option( $this->theme_slug . '_purchase_code_status', $license_data->license );
				?>
				<div class="pxl-dsb-box-head"> 
					<div class="pxl-dsb-confirmation success">
						<h6><?php echo esc_html__( 'Thanks for the verification!', 'mytheme' ) ?></h6>
						<p><?php echo esc_html__( 'You can now enjoy and build great websites. Looking for help? Visit', 'mytheme' ) ?> <a href="<?php echo esc_url($pxl_server_info['support_url']) ?>" target="_blank"><?php echo esc_html__( 'submit a ticket', 'mytheme' ) ?></a>.</p>
					</div> 

					<div class="pxl-dsb-deactive">
						<form method="POST" action="<?php echo admin_url( 'admin.php?page=pxlart' )?>">
							<input type="hidden" name="action" value="removekey"/>
							<button class="btn button" type="submit"><?php esc_html_e( 'Remove Purchase Code', 'mytheme' ) ?></button>
						</form>
					</div> 
				</div> 
				<?php 
				if($merlin)
					wp_redirect(admin_url('admin.php?page=pxlart-setup&step=plugins'));
			}
			else {
		        $message = esc_html__( 'Response return null.', 'mytheme' ); ?>
		        <div class="pxl-dsb-confirmation fail">
		          <h6><?php echo esc_html__( 'Active false', 'mytheme' ) ?></h6>
		          <p><?php echo wp_kses_post( $message ) ?> <a href="<?php echo esc_url($pxl_server_info['docs_url']) ?>" target="_blank"><?php echo esc_html__( 'our help center', 'mytheme' ) ?></a> or <a href="<?php echo esc_url($pxl_server_info['support_url']) ?>" target="_blank"><?php echo esc_html__( 'submit a ticket', 'mytheme' ) ?></a></p>
		        </div>
		        <?php $this->form(); ?>
		    <?php }
		}

	}
	   
	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	function form() {

		$strings = $this->strings;

		$license = trim( get_option( $this->theme_slug . '_purchase_code' ) );
		$status = get_option( $this->theme_slug . '_purchase_code_status', false );

		?>
		<form action="options.php" method="post" class="pxl-dsb-register-form">
			<?php settings_fields( $this->theme_slug . '-license' ); ?>
			<input id="<?php echo esc_attr($this->theme_slug)?>_purchase_code" name="<?php echo esc_attr($this->theme_slug)?>_purchase_code" type="text" value="<?php echo esc_attr( $license ); ?>" placeholder="<?php esc_attr_e( 'Enter your purchase code', 'mytheme' ); ?>">
			<input type="submit" class="res-purchase-code" value="<?php esc_attr_e( 'Register your purchase code', 'mytheme' ) ?>">
		</form>
		<?php
	}
	
	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * since 1.0.0
	 */
	function register_option() {
		register_setting(
			$this->theme_slug . '-license',
			$this->theme_slug . '_purchase_code',
			array( $this, 'sanitize_license' )
		);
	}
	 
	function sanitize_license( $new ) {

		$old = get_option( $this->theme_slug .'_purchase_code' );

		if ( $old && $old != $new ) {
			// New license has been entered, so must reactivate
			delete_option( $this->theme_slug . '_purchase_code_status' );
		}

		return $new;
	}

	function remove_key(){
		if(isset($_POST['action']) && sanitize_text_field($_POST['action'] === 'removekey')){
			$purchase_code = trim( get_option( $this->theme_slug . '_purchase_code' ) ); 
			$api_params = array(
				'action' => 'remove_license',
				'license'       => $purchase_code,
				'url'           => rawurlencode(home_url())
			);
			  
			$license_data = $this->get_api_response( $api_params );
			 
			if ( true === $license_data->success ) {
				delete_option( $this->theme_slug . '_purchase_code' );
				delete_option( $this->theme_slug . '_purchase_code_status' );
			} 
			 
		}
	}
 
	
	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 * @return array $response decoded JSON response.
	 */
	 function get_api_response( $api_params ) {

		// Call the custom API.
		 
		$response = wp_remote_get(
			add_query_arg( $api_params, $this->remote_api_url ),
			array( 'timeout' => 15, 'sslverify' => false )
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response;
	 }
	 
	 

	/**
	 * Disable requests to wp.org repository for this theme.
	 *
	 * @since 1.0.0
	 */
	function disable_wporg_request( $r, $url ) {

		// If it's not a theme update request, bail.
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
 			return $r;
 		}

 		// Decode the JSON response
 		$themes = json_decode( $r['body']['themes'] );

 		// Remove the active parent and child themes from the check
 		$parent = get_option( 'template' );
 		$child = get_option( 'stylesheet' );
 		unset( $themes->themes->$parent );
 		unset( $themes->themes->$child );

 		// Encode the updated JSON response
 		$r['body']['themes'] = json_encode( $themes );

 		return $r;
	}
	
}

new Mytheme_Register;