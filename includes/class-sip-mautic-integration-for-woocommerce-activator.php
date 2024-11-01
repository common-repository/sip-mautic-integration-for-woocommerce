<?php
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

/**
 * Fired during plugin activation
 *
 * @link       https://shopitpress.com/
 * @since      1.0.0
 *
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/includes
 * @author     Shopitpress <hello@shopitpress.com>
 */
class Sip_Mautic_Integration_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		update_option('sip_miwc_mautic_auth_connect', false);
		delete_option('sip_miwc_mautic_auth_info');

		$table_name = $wpdb->prefix . "sip_mautic_contacts_info"; 

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			email varchar(100) NOT NULL,
			contactId bigint(20) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8; ";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}