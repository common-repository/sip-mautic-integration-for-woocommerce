<?php
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

/**
 * Fired during plugin deactivation
 *
 * @link       https://shopitpress.com/
 * @since      1.0.0
 *
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sip_Mautic_Integration_For_Woocommerce
 * @subpackage Sip_Mautic_Integration_For_Woocommerce/includes
 * @author     Shopitpress <hello@shopitpress.com>
 */
class Sip_Mautic_Integration_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// update_option('sip_miwc_mautic_auth_connect', false);
	}

}
