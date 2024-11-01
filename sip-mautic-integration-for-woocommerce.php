<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://shopitpress.com/
 * @since             1.0.0
 * @package           Sip_Mautic_Integration_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       SIP Mautic integration for WooCommerce
 * Plugin URI:        https://shopitpress.com/plugins/sip-mautic-integration-wooCommerce
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.2
 * Author:            Shopitpress
 * Author URI:        https://shopitpress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sip-mautic-integration-for-woocommerce
 * Domain Path:       /languages
 * WC requires at least: 2.6.0
 * WC tested up to: 3.6.5
 * Last updated on: 10 Jul, 2019
 */

// If this file is called directly, abort.
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

define( 'SIP_MIWC_NAME', 'SIP Mautic integration for WooCommerce' );
define( 'SIP_MIWC_VERSION', '1.0.2' );
define( 'SIP_MIWC_BASENAME', plugin_basename( __FILE__ ) );
define( 'SIP_MIWC_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SIP_MIWC_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SIP_MIWC_INCLUDES', SIP_MIWC_DIR . trailingslashit( 'includes' ) );
define( 'SIP_MIWC_PUBLIC', SIP_MIWC_DIR . trailingslashit( 'public' ) );
define( 'SIP_MIWC_PLUGIN_PURCHASE_URL', 'https://shopitpress.com/plugins/sip-mautic-integration-woocommerce/' );
add_action( 'woocommerce_checkout_update_order_meta', 'sip_miwc_subscribe_to_mautic' );
add_action( 'woocommerce_order_status_changed', 'sip_miwc_woocommerce_order_status_changed', 5, 3);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sip-mautic-integration-for-woocommerce-activator.php
 */
function activate_sip_mautic_integration_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sip-mautic-integration-for-woocommerce-activator.php';
	Sip_Mautic_Integration_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sip-mautic-integration-for-woocommerce-deactivator.php
 */
function deactivate_sip_mautic_integration_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sip-mautic-integration-for-woocommerce-deactivator.php';
	Sip_Mautic_Integration_For_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sip_mautic_integration_for_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_sip_mautic_integration_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sip-mautic-integration-for-woocommerce.php';

function sip_inc_mautic_lib() {

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
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sip_mautic_integration_for_woocommerce() {

	$plugin = new Sip_Mautic_Integration_For_Woocommerce();
	$plugin->run();

}
run_sip_mautic_integration_for_woocommerce();