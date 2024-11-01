<?php
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://shopitpress.com/
 * @since      1.0.0
 *
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/admin
 */

define( 'SIP_MIWC_UTM_CAMPAIGN', 'sip-mautic-integration' );
define( 'SIP_MIWC_ADMIN_VERSION' , '1.0.4' );

if ( ! defined( 'SIP_SPWC_PLUGIN' ) )
	define( 'SIP_SPWC_PLUGIN',  'SIP Social Proof for WooCommerce' );

if ( ! defined( 'SIP_SPWC_PLUGIN_URL' ) )
	define( 'SIP_SPWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-social-proof-woocommerce/' );

if ( ! defined( 'SIP_FEBWC_PLUGIN' ) )
	define( 'SIP_FEBWC_PLUGIN', 'SIP Front End Bundler for WooCommerce' );

if ( ! defined( 'SIP_FEBWC_PLUGIN_URL' ) )
	define( 'SIP_FEBWC_PLUGIN_URL', 'https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/' );

if ( ! defined( 'SIP_RSWC_PLUGIN' ) )
	define( 'SIP_RSWC_PLUGIN',  'SIP Reviews Shortcode for WooCommerce' );

if ( ! defined( 'SIP_RSWC_PLUGIN_URL' ) )
	define( 'SIP_RSWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-reviews-shortcode-woocommerce/' );

if ( ! defined( 'SIP_AENWC_PLUGIN' ) )
	define( 'SIP_AENWC_PLUGIN',  'SIP Advanced Email Notification For Woocommerce' );

if ( ! defined( 'SIP_AENWC_PLUGIN_URL' ) )
	define( 'SIP_AENWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-advanced-email-notifications-for-woocommerce/' );

if ( ! defined( 'SIP_COSWC_PLUGIN' ) )
	define( 'SIP_COSWC_PLUGIN',  'SIP Custom Order Satus for WooCommerce' );

if ( ! defined( 'SIP_COSWC_PLUGIN_URL' ) )
	define( 'SIP_COSWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-custom-order-satus-woocommerce/' );

if ( ! defined( 'SIP_CCWC_PLUGIN' ) )
	define( 'SIP_CCWC_PLUGIN',  'SIP Cookie Check for WooCommerce' );

if ( ! defined( 'SIP_CCWC_PLUGIN_URL' ) )
	define( 'SIP_CCWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-cookie-check-woocommerce/' );

if ( ! defined( 'SIP_CAWC_PLUGIN' ) )
	define( 'SIP_CAWC_PLUGIN',  'SIP Cart Ajax Refresh' );

if ( ! defined( 'SIP_CAWC_PLUGIN_URL' ) )
	define( 'SIP_CAWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-cart-ajax-refresh/' );

if ( ! defined( 'SIP_MIWC_PLUGIN' ) )
	define( 'SIP_MIWC_PLUGIN',  'SIP Mautic integration for WooCommerce' );

if ( ! defined( 'SIP_MIWC_PLUGIN_URL' ) )
	define( 'SIP_MIWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-mautic-integration-wooCommerce/' );

if ( ! defined( 'SIP_WPGUMBY_THEME' ) )
	define( 'SIP_WPGUMBY_THEME','WPGumby' );

if ( ! defined( 'SIP_WPGUMBY_THEME_URL' ) )
	define( 'SIP_WPGUMBY_THEME_URL','https://shopitpress.com/themes/wpgumby/' );

function sip_mautic_integration_wc_version( ) {

	$get_optio_version = get_option( 'sip_version_value' );
	if( $get_optio_version == "" ) {
		add_option( 'sip_version_value', SIP_MIWC_ADMIN_VERSION );
	} else if ( version_compare( SIP_MIWC_ADMIN_VERSION , $get_optio_version , ">=" ) ) {
		update_option( 'sip_version_value', SIP_MIWC_ADMIN_VERSION );
	}
}
add_action( 'init', 'sip_mautic_integration_wc_version' );

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/admin
 * @author     Shopitpress <hello@shopitpress.com>
 */
class Sip_Mautic_Integration_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Build the custom admin page for managing addons, themes and licenses.
		add_action( 'admin_init', array( $this, 'sip_mautic_connection_connect_settings' ) );
		add_action( 'admin_menu', array( $this, 'sip_miwc_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'sip_miwc_config_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'sip_spwc_sip_extras_admin_menu' ), 2000 );
		add_filter( 'plugin_action_links_' . SIP_MIWC_BASENAME, array( $this, 'sip_miwc_action_links' ) );
		add_action( 'admin_init', array( $this, 'sip_miwc_register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'sip_miwc_admin_enqueue') );

	}

	public function sip_miwc_mautic_connection_connect( ) {

		if (!session_id()) {
			session_start();
		}

		require_once SIP_MIWC_DIR . 'admin/mautic/Psr/Log/LoggerAwareInterface.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Psr/Log/LoggerInterface.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Psr/Log/LogLevel.php';

		require_once SIP_MIWC_DIR . 'admin/mautic/Psr/Log/AbstractLogger.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Psr/Log/NullLogger.php';

		require_once SIP_MIWC_DIR . 'admin/mautic/QueryBuilder/QueryBuilder.php';

		require_once SIP_MIWC_DIR . 'admin/mautic/Auth/AuthInterface.php';    
		require_once SIP_MIWC_DIR . 'admin/mautic/Auth/ApiAuth.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Auth/AbstractAuth.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Auth/OAuth.php';

		require_once SIP_MIWC_DIR . 'admin/mautic/Api/Api.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Api/Contacts.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Api/Stages.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/Api/Segments.php';
		require_once SIP_MIWC_DIR . 'admin/mautic/MauticApi.php';

		$uri = get_option('url_of_mautic_server');
		$public = get_option('public_key_of_mautic_server');
		$secret = get_option('secret_key_of_mautic_server');

		$settings = array(
			'baseUrl'		=> $uri,
			'clientKey'		=> $public,
			'clientSecret'	=> $secret,
			'callback'		=> admin_url('admin.php?page=sip-mautic-integration-settings'),
			'version'		=> 'OAuth1a'
		);

		if (($info = get_option('sip_miwc_mautic_auth_info'))) {
			$settings['accessToken']		= $info['accessToken'] ;
			$settings['accessTokenSecret']	= $info['accessTokenSecret'];
			$settings['accessTokenExpires']	= $info['accessTokenExpires'];
		}

		$auth = \Mautic\Auth\ApiAuth::initiate($settings);
		
		try {
			if ($auth->validateAccessToken()) {

				if ($auth->accessTokenUpdated()) {
					$accessTokenData = $auth->getAccessTokenData();
					// //store access token data however you want
		
					$auth = array(
						'accessToken' 		 => $accessTokenData['access_token'],
						'accessTokenSecret'  => $accessTokenData['access_token_secret'],
						'accessTokenExpires' => $accessTokenData['expires']
					);

					update_option('sip_miwc_mautic_auth_info', $auth);
					update_option('sip_miwc_mautic_auth_connect', true);

					echo '<div class="updated notice">
						<p><b>Mautic connect successfully</b></p>
					</div>';
				}
			}
		} catch (Exception $e) {
			// Do Error handling
			echo "Error : " . $e->message( );
		}
		session_destroy();
	}

	public function sip_mautic_connection_connect_settings( ) {

		// // Check whether the button has been pressed AND also check the nonce
		if ( ( isset($_POST['mautic_connection_connect']) && check_admin_referer('Mautic_Connection_Connect_Nonce') ) || ( isset($_GET['oauth_token']) ) )  {
			// the button has been pressed AND we've passed the security check
			$this->sip_miwc_mautic_connection_connect();
		}

		// // Check whether the button has been pressed AND also check the nonce
		if ( isset($_POST['mautic_connection_dis_connect']) && check_admin_referer('Mautic_Connection_DisConnect_Nonce') )  {
			// the button has been pressed AND we've passed the security check
			delete_option('sip_miwc_mautic_auth_info');
			update_option('sip_miwc_mautic_auth_connect', false);
			echo '<div class="updated notice">
				<p><b>Mautic disconnect successfully</b></p>
			</div>';
		}

		// // Check whether the button has been pressed AND also check the nonce
		if ( isset($_POST['prepare_mautic_old_order_sync']) && check_admin_referer('Prepare_Mautic_old_order_sync_Nonce') )  {
			// the button has been pressed AND we've passed the security check
			
			global $wpdb;
			$table_name = $wpdb->prefix . "sip_mautic_contacts_info";

			$querystr = "SELECT p.ID ID FROM $wpdb->posts p JOIN $wpdb->postmeta email ON p.ID = email.post_id WHERE p.post_type = 'shop_order' AND email.meta_key = '_billing_email' AND email.meta_value <> '' AND email.meta_value <> '-' AND email.meta_value NOT IN( SELECT email FROM $table_name ) GROUP by email.meta_value ORDER BY p.ID ASC";

			$pageposts = $wpdb->get_results($querystr, OBJECT);
			$pageposts = str_replace("'", " ", $pageposts );
			$json_encode = json_encode($pageposts);


			$fp = fopen( 'results.json', 'w');
			fwrite($fp, json_encode($json_encode));
			fclose($fp);

			$querystr = "SELECT p.ID ID FROM $wpdb->posts p JOIN $wpdb->postmeta email ON p.ID = email.post_id WHERE p.post_type = 'shop_order' AND email.meta_key = '_billing_email' AND email.meta_value <> '' AND email.meta_value <> '-' AND email.meta_value IN( SELECT email FROM $table_name ) GROUP by email.meta_value ORDER BY p.ID ASC";

			$pageposts = $wpdb->get_results($querystr, OBJECT);
			$pageposts = str_replace("'", " ", $pageposts );
			$json_encode = json_encode($pageposts);

			$fp = fopen( 'results1.json', 'w');
			$fwrite = fwrite($fp, json_encode($json_encode));
			fclose($fp);
		}
	}


	public function sip_miwc_register_settings() { // whitelist options
		register_setting( 'sip-miwc-register-settings', 'public_key_of_mautic_server' );
		register_setting( 'sip-miwc-register-settings', 'secret_key_of_mautic_server' );
		register_setting( 'sip-miwc-register-settings', 'add_order_status_as_tag' );
		register_setting( 'sip-miwc-register-settings', 'additional_tags_to_add' );
		register_setting( 'sip-miwc-register-settings', 'url_of_mautic_server' );
		register_setting( 'sip-miwc-register-settings', 'product_name_as_tag' );
		register_setting( 'sip-miwc-register-settings', 'mautic_owner_id' );
		register_setting( 'sip-miwc-register-settings', 'sku_as_tag' );
	}

	/**
	* Plugin admin enqueue.
	*
	* @since 1.0.0
	*/
	public function sip_miwc_admin_enqueue( ) {

		wp_enqueue_script( 'sip_miwc_admin_script', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' , array('jquery'), '1.0.0', true );
	}


	/**
	* Plugin page menus.
	*
	* @since 1.0.0
	*/
	public function sip_miwc_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=sip-mautic-integration-settings' ) . '">' . __( 'Settings', 'sip-mautic-integration' ) . '</a>'
			);
		$plugin_links[] = '<a target="_blank" href="https://shopitpress.com/docs/sip-mautic-integration-woocommerce/?utm_source=wordpress.org&utm_medium=SIP-panel&utm_content=v'. SIP_MIWC_VERSION .'&utm_campaign='.SIP_MIWC_UTM_CAMPAIGN.'">' . __( 'Docs', 'sip-mautic-integration' ) . '</a>';

		return array_merge( $links, $plugin_links );
	}

	/**
	* Registers the admin menu for managing the ShopitPress options.
	*
	* @since 1.0.0
	*/
	public function sip_miwc_admin_menu() {
		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIHZpZXdCb3g9IjAgNTAgNzI1IDQ3MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNzI1IDQ3MCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTY0MC4zMjEsNDguNTk4YzI4LjU0LDAsNDMuNzI5LDI5Ljc5MiwzMi4xNzIsNTUuMTU4bC03Ni40MTYsMTY2Ljk1NGMtMTIuMDMyLTMyLjM0Ni01MC41NjUtNTUuNzU3LTg3LjktNjkuMTczYy00OC44NjItMTcuNjAyLTEyNy44NDMtMjEuODE5LTE5MC4wOTQtMzAuMzc5Yy0zNC4zMjEtNC42NjEtMTEwLjExOC0xMi43NS05Ny43OC01My4xMTVjMTMuMjM5LTQzLjA3NCw5Ni40ODEtNDcuNTkxLDEzMy44OC00Ny41OTFjODYuMTI5LDAsMTYwLjk1NCwxOS43NzEsMTYwLjk1NCw4My44NjZoOTkuNzQxVjQ4LjU5OEg2NDAuMzIxeiBNNTQzLjc5NiwxMDUuNTk0Yy03LjEwNS0yNy40NTgtMzIuMjc3LTQ4LjcxNy01OS4xNjktNTYuOTk3aDgyLjc3NkM1NjYuMjgxLDY2LjYxMyw1NTUuNDQ4LDk0LjE4MSw1NDMuNzk2LDEwNS41OTRMNTQzLjc5NiwxMDUuNTk0eiBNNTUwLjY0MSwzNzAuMTIzbC0xMy42MTEsMjkuNzIzYy02LjAzOCwxMy4yNzktMTkuMzI3LDIxLjYzNS0zMy45MjcsMjEuNjM1SDIyMS45NjljLTE0LjY2NiwwLTI3Ljk1NS04LjM1NS0zNC4wMDMtMjEuNjM1bC0xNS44NDQtMzQuNzIzYzEwLjkxMiwxNC43NDgsMjkuMzMxLDIzLjA4LDQ5LjA5OCwyOC4yODFDMzEzLjE1LDQxNy43MzIsNDY4LjUzNSw0MjEuNDgsNTUwLjY0MSwzNzAuMTIzTDU1MC42NDEsMzcwLjEyM3ogTTE2My43NjEsMzQ2Ljk5bC01OC4xNi0xMjcuMjQzYzE0LjY0MSwxNS42NTUsMzcuNjAxLDI3LjM2LDY2LjcyNCwzNi4yOTdjODUuNDA5LDI2LjI0MiwyMTMuODI1LDIyLjIyOSwyOTYuMjU0LDM1LjExN2M0MS45NDksNi41NjEsNDMuODU3LDQ3LjA4OCwxMy4yODksNjEuOTQ3Yy01Mi4zMzQsMjUuNTA2LTEzNS4yNDUsMjUuMzU5LTE5NC45NTcsMTEuNjk1QzIzNy4yMTksMjg1LjI1LDE1NS44MTksMzA0LjQ5LDE2My43NjEsMzQ2Ljk5TDE2My43NjEsMzQ2Ljk5eiBNODUuODY4LDE3Ni42OTJsLTMzLjM0Ni03Mi45MzdDNDAuOTQ5LDc4LjM5LDU2LjEzMSw0OC41OTgsODQuNjY5LDQ4LjU5OGgxMzYuOTY2QzE1OS43NTEsNjYuMTU0LDc3LjEwNSwxMTAuNjcsODUuODY4LDE3Ni42OTJMODUuODY4LDE3Ni42OTJ6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTM2Mi41MywwLjA4NmgyNzcuNzkyYzYzLjk2NiwwLDEwMi4xODUsNjYuNzk1LDc2LjEzNSwxMjMuNzI2TDU4MS4wMzEsNDE5Ljk4NEM1NjcuMTQ3LDQ1MC4yODEsNTM2LjQzNSw0NzAsNTAzLjEwMyw0NzBIMzYyLjUzSDIyMS44OTJjLTMzLjM0NSwwLTY0LjA0My0xOS43MTktNzcuOTE3LTUwLjAxNkw4LjUzNSwxMjMuODEyQy0xNy40OTMsNjYuODgyLDIwLjY5MywwLjA4Niw4NC42NjksMC4wODZIMzYyLjUzeiBNMzYyLjUzLDIzLjk0Mkg4NC42NjljLTQ2LjIxOCwwLTczLjU2OCw0OC4yNjYtNTQuNDMsOTAuMDExbDEzNS4zNjIsMjk2LjA3OGMxMC4wNzIsMjEuOTYxLDMyLjIyNSwzNi4xMDUsNTYuMjkxLDM2LjEwNUgzNjIuNTNoMTQwLjU3M2MyNC4wNjcsMCw0Ni4yMTktMTQuMTQ1LDU2LjI3Ny0zNi4xMDVsMTM1LjM4Ni0yOTYuMDc4YzE5LjE0LTQxLjc0NS04LjIyNi05MC4wMTEtNTQuNDQ0LTkwLjAxMUgzNjIuNTN6Ii8+PC9nPjwvc3ZnPg==';
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		$this->hook = add_menu_page(
			__( 'SIP Plugin Panel', 'sip-mautic-integration' ),
			__( 'SIP Plugins', 'sip-mautic-integration' ),
			'manage_options',
			'sip_plugin_panel',
			NULL,
			$icon_svg,
			62.25
			);

		// Load global assets if the hook is successful.
		if ( $this->hook ) {
		// Enqueue custom styles and scripts.
			add_action( 'admin_enqueue_scripts',  array( $this, 'sip_miwc_admin_tab_style' ) );
		}
	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function sip_miwc_admin_tab_style() {
		wp_register_style( 'sip_miwc_custom_css', esc_url( SIP_MIWC_URL .   '/admin/assets/css/custom.css', false, '1.0.0' ) );
		wp_enqueue_style( 'sip_miwc_custom_css' );
	}


	/**
	* Loads assets for the settings page.
	*
	* @since 1.0.0
	*/
	public function sip_miwc_admin_assets() {

		wp_register_style( 'sip_miwc_layout', esc_url( SIP_MIWC_URL .   '/admin/assets/css/layout.css', false, '1.0.0' ) );
		wp_enqueue_style( 'sip_miwc_layout' );
	}

	public function sip_miwc_remove_duplicate_submenu() {
		/* === Duplicate Items Hack === */
		remove_submenu_page( 'sip_plugin_panel', 'sip_plugin_panel' );
	}

	public function sip_miwc_config_menu() {
		global $parent;
		$args = array(
			'create_menu_page' => true,
			'parent_slug'   => '',
			'page_title'    => __( 'Mautic', 'sip-mautic-integration' ),
			'menu_title'    => __( 'Mautic', 'sip-mautic-integration' ),
			'capability'    => 'manage_options',
			'parent'        => '',
			'parent_page'   => 'sip_plugin_panel',
			'page'          => 'sip_plugin_panel',
			);

		$parent = $args['parent_page'];

		if ( ! empty( $parent ) ) {
			add_submenu_page( $parent , 'Mautic', 'Mautic', 'manage_options', 'sip-mautic-integration-settings', array( $this, 'sip_miwc_settings_page' ) );
		} else {
			add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['page'], array( $this, 'sip_miwc_admin_menu_ui' ), NULL , 62.25 );
		}
		/* === Duplicate Items Hack === */
		$this->sip_miwc_remove_duplicate_submenu();
	}

	/**
	* To avoide the duplication of ShopitPress Extras menue and run the latest sip panel
	*
	* @since 1.0.1
	*/
	public function sip_spwc_sip_extras_admin_menu() {
		global $parent;
		$get_optio_version = get_option( 'sip_version_value' );

		if ( version_compare( $get_optio_version , SIP_MIWC_ADMIN_VERSION , "<=" ) ) {

			if ( ! defined( 'SIP_PANEL_EXTRAS' ) ) {
				define( 'SIP_PANEL_EXTRAS' , TRUE);
				add_submenu_page( $parent , 'ShopitPress Extras', '<span style="color:#FF8080 ">ShopitPress Extras</span>', 'manage_options', 'sip-extras', array( $this, 'sip_miwc_admin_menu_ui' ) );
				add_action( 'admin_enqueue_scripts',  array( $this, 'sip_miwc_admin_assets' ) );
			}
		}
	}

	/**
	* On deactivation of plugin null the sip_versin_value
	*
	* @since 1.0.1
	*/
	public function sip_miwc_deactivate() {
		delete_option( 'sip_version_value' );
	}

	/**
	* Outputs the main UI for handling and managing addons, themes and licenses.
	*
	* @since 1.0.0
	*/
	public function sip_miwc_admin_menu_ui() { ?>
		<div class="wrap">
			<h2><?php _e('Shopitpress extras','sip-mautic-integration'); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-extras"><?php _e( 'Plugins', 'sip-mautic-integration' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'themes' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-extras&amp;action=themes"><?php _e( 'Themes', 'sip-mautic-integration' ); ?></a>
			</h2>
			<?php
			if ( ! isset( $_GET['action'] ) ) {
				include("partials/ui/plugin.php");
			} elseif ( 'themes' == $_GET['action'] ) {
				include("partials/ui/themes.php");
			}
			?>
		</div>
		<?php
	} // END menu_ui()

	/**
	* After loding this function global page show the admin panel
	*
	* @since     1.0.0
	*/
	public function sip_miwc_settings_page() { ?>

		<div class="sip-panel-wrapper wrap">
			<h2><?php _e('SIP Mautic integration for WooCommerce','sip-mautic-integration'); ?></h2>
			<div class="sip-container">
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-mautic-integration-settings"><?php _e( 'Settings', 'sip-mautic-integration' ); ?></a>
					<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'help' == $_GET['action'] ) echo ' nav-tab-active'; ?>" target="_blank" href="https://shopitpress.com/docs/sip-mautic-integration-woocommerce"><?php _e( 'Help', 'sip-mautic-integration' ); ?></a>
				</h2>
				<?php
				if ( ! isset( $_GET['action'] ) ) {
					include("partials/ui/settings.php");
				} elseif ( 'help' == $_GET['action'] ) {
					include("partials/ui/help.php");
				}
				?>
			</div><!-- .container -->
		</div>
		<?php
	}
}