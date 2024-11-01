<?php
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

function sip_miwc_woocommerce_order_status_changed( $id, $old_status, $new_status ){

	sip_miwc_mautic_order_sync( "new", $id, $old_status, $new_status );
}

function _create_query( $order ) {

	$order_item = $order->get_items();
	$product_name_as_tag = get_option('product_name_as_tag');
	$add_order_status_as_tag = get_option('add_order_status_as_tag');
	$sku_as_tag = get_option('sku_as_tag');
	$additional_tags_to_add = get_option('additional_tags_to_add');

	foreach( $order_item as $product ) {


		if ( $product_name_as_tag == true ) {
			$prodct_name[] = $product['name'];
		}

		if ( $sku_as_tag == true ) {
			$product_variation_id = $product['variation_id'];
			// Check if product has variation.
			
			if ($product_variation_id) { 
				$product_ = new WC_Product_Variation ($product['variation_id']);
			} else {
				$product_ = new WC_Product($product['product_id']);
			}

			// Get SKU
			$prodct_name[] = $product_->get_sku();
		}

	}

	if ( $add_order_status_as_tag != "" ) {
		$order_status = $order->post->post_status;
		$order_status = str_replace("wc-", "", $order_status);
		$prodct_name[] = $order_status;
	}

	if ( $additional_tags_to_add != "" ) {
		$prodct_name[] = $additional_tags_to_add;
	}

	if (!empty($prodct_name)) {
		$product_list = implode( ',', $prodct_name );
	} else {
		$product_list = "";
	}

	$billing_address_1 	= method_exists( $order, 'get_billing_address_1' ) ? $order->get_billing_address_1() : $order->billing_address_1;
	$billing_address_2 	= method_exists( $order, 'get_billing_address_2' ) ? $order->get_billing_address_2() : $order->billing_address_2;
	$billing_city		= method_exists( $order, 'get_billing_city' ) ? $order->get_billing_city() : $order->billing_city;
	$billing_company	= method_exists( $order, 'get_billing_company' ) ? $order->get_billing_company() : $order->billing_company;
	$billing_country	= method_exists( $order, 'get_billing_country' ) ? $order->get_billing_country() : $order->billing_country;
	$billing_email		= method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : $order->billing_email;
	$billing_first_name	= method_exists( $order, 'get_billing_first_name' ) ? $order->get_billing_first_name() : $order->billing_first_name;
	$billing_last_name	= method_exists( $order, 'get_billing_last_name' ) ? $order->get_billing_last_name() : $order->billing_last_name;
	$billing_phone		= method_exists( $order, 'get_billing_phone' ) ? $order->get_billing_phone() : $order->billing_phone;
	$billing_postcode	= method_exists( $order, 'get_billing_postcode' ) ? $order->get_billing_postcode() : $order->billing_postcode;
	$billing_state		= method_exists( $order, 'get_billing_state' ) ? $order->get_billing_state() : $order->billing_state;
	$customer_ip_address = method_exists( $order, 'get_customer_ip_address' ) ? $order->get_customer_ip_address() : $order->customer_ip_address;
	$billing_id			= method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	

	$query = array(
		'address1' => $billing_address_1,
		'address2' => $billing_address_2,
		'city' => $billing_city,
		'company' => $billing_company,
		'country' => _get_country_name( $billing_country ),
		'email' => $billing_email,
		'firstname' => $billing_first_name,
		'lastname' => $billing_last_name,
		'phone' => $billing_phone,
		'zipcode' => $billing_postcode,
		'state' => "",//_get_states_name( $billing_country, $billing_state ),
		'order_id' => $billing_id,
		'tag' => $product_list,
		'ip_address' => $customer_ip_address
	);

	return $query;
}

function _get_states_name( $country_code, $state_code ) {
	$states = wp_remote_get( path_join( SIP_MIWC_URL, 'admin/assets/json/states.json' ) );
	try {
		if ( is_wp_error( $states ) ) {
			throw new Exception( 'invalid json data.' );
		}
		$json = json_decode( $states['body'], true );
		if ( ! isset( $json[ $country_code ][ $state_code ] ) ) {
			throw new Exception( "Country[{$country_code}] is not found." );
		}
		$state_name = $json[ $country_code ][ $state_code ];
		return $state_name;
	} catch ( Exception $e ) {
		$msg = 'Mauticommerce state Error:' . $e->getMessage();
		error_log( $msg );
		$wc_country = new WC_Countries();

		$state = "";
		if (isset($wc_country->get_states( $country_code )[ $state_code ])) {
		 	$state = $wc_country->get_states( $country_code )[ $state_code ];
		// 	$state = $wc_country->get_states( $country_code );
		} else {
			$state = $state_code;
		}

		return $state;
	}
}


function _get_country_name( $country_code ) {

	if ($country_code != "") {
		$country = WC()->countries->countries[ $country_code ];
		$country = str_replace("United Kingdom (UK)", "United Kingdom", $country);
		$country = str_replace("United States (US)", "United States", $country);
		$country = str_replace("Faroe Islands", "Faroes", $country);
		$country = str_replace("Republic of Ireland", "Ireland", $country);
		return $country;
	} else {
		return "";
	}
}

function sip_miwc_subscribe_to_mautic( $order_id ) {
	$order = sip_miwc_mautic_order_sync( "new", $order_id );
}

function sip_miwc_mautic_order_sync( $order_sync = "", $order_id = 0, $old_status = "" , $new_status = "" ) {

	global $wpdb;
	$table_name = $wpdb->prefix . "sip_mautic_contacts_info"; 
	$uri = get_option('url_of_mautic_server');
    $public = get_option('public_key_of_mautic_server');
    $secret = get_option('secret_key_of_mautic_server');
    $ownerId = get_option('mautic_owner_id');
    $add_order_status_as_tag = get_option('add_order_status_as_tag');

    $settings = array(
        'baseUrl'           => $uri,
        'clientKey'         => $public,
        'clientSecret'      => $secret,
        'callback'          => admin_url('admin.php?page=sip-mautic-integration-settings'),
        'version'           => 'OAuth1a'
    );

	if (($info = get_option('sip_miwc_mautic_auth_info'))) {
		$settings['accessToken']        = $info['accessToken'] ;
		$settings['accessTokenSecret']  = $info['accessTokenSecret'];
		$settings['accessTokenExpires'] = $info['accessTokenExpires'];
	}

	sip_inc_mautic_lib();

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

	$contactApi = \Mautic\MauticApi::getContext(
	    'contacts',
	    $auth,
	    $uri . '/api/'
	);


	if ( $order_sync == "old" ) {

		// 
	} else {

		$order = wc_get_order( $order_id );
		$query = _create_query( $order );

		$ip_address = "is:anonymous ip:".$query["ip_address"];
		$response = $contactApi->getList($ip_address, 0, 1, "id");
		$ip_exist_response = $response["contacts"];

		$ip_exist = "";
		foreach ($ip_exist_response as $key => $value) {
			$ip_exist = $key;
		}

		$email = "email:".$query["email"];
		$response = $contactApi->getList($email, 0, 1, "id");
		$email_exist_response = $response["contacts"];

		$email_exist = "";
		foreach ($email_exist_response as $key => $value) {
			$email_exist = $key;
		}

		if (empty($email_exist) && empty($ip_exist)) {

			$prodct_name[] = $query["tag"];

			if ( $add_order_status_as_tag == true ) {

				$prodct_name[] = $new_status;
				$product_tag = implode( ',', $prodct_name );
				$product_tag = str_replace($old_status, "", $product_tag);
			} else {
				$product_tag = $query["tag"];
			}

			// Insert New Data
			$data = array(
				'firstname'	=> $query["firstname"],
				'lastname'	=> $query["lastname"],
				'email'		=> $query["email"],
				'address1'	=> $query["address1"],
				'company'	=> $query["company"],
				'address2'	=> $query["address2"],
				'city'		=> $query["city"],
				'country'	=> $query["country"],
				'phone'		=> $query["phone"],
				'tags'		=> $product_tag,
				'zipcode'	=> $query["zipcode"],
				'ipAddress'	=> $query["ip_address"],
				'owner'     => $ownerId
			);

			$contact = $contactApi->create($data);
			$contactID = $contact["contact"]["fields"]["all"]["id"];
			$contactEmail = $contact["contact"]["fields"]["all"]["email"];

			$wpdb->insert($table_name, array(
					'email'		=> $contactEmail,
					'contactId'	=> $contactID
				),array(
					'%s',
					'%d'
				)
			);
			
		} elseif (!empty($email_exist)) {

			$response = $contactApi->get($email_exist);

			// $getData = $response["contact"]["fields"]["all"];
			$getData = $response["contact"]["tags"];

			foreach ($getData as $key => $value) {
				$prodct_name[] = $value["tag"];
			}

			$prodct_name[] = $query["tag"];

			if ( $add_order_status_as_tag == true ) {

				$prodct_name[] = $new_status;
				$product_tag = implode( ',', $prodct_name );
				$product_tag = str_replace($old_status, "", $product_tag);
			} else {
				$product_tag = $query["tag"];
			}

			$updatedData = array(
				'firstname'	=> $query["firstname"],
				'lastname'	=> $query["lastname"],
				'email'		=> $query["email"],
				'address1'	=> $query["address1"],
				'address2'	=> $query["address2"],
				// 'state'		=> $query["state"],
				'city'		=> $query["city"],
				'country'	=> $query["country"],
				'phone'		=> $query["phone"],
				'tags'		=> $product_tag,
				'zipcode'	=> $query["zipcode"],
				'ipAddress'	=> $query["ip_address"],
				'owner'     => $ownerId
			);

			$response = $contactApi->edit($email_exist, $updatedData);
		} elseif (!empty($ip_exist)) {

			$response = $contactApi->get($ip_exist);

			// $getData = $response["contact"]["fields"]["all"];
			$getData = $response["contact"]["tags"];

			foreach ($getData as $key => $value) {
				$prodct_name[] = $value["tag"];
			}

			$prodct_name[] = $query["tag"];

			if ( $add_order_status_as_tag == true ) {

				$prodct_name[] = $new_status;
				$product_tag = implode( ',', $prodct_name );
				$product_tag = str_replace($old_status, "", $product_tag);
			} else {
				$product_tag = $query["tag"];
			}

			$updatedData = array(
				'firstname'	=> $query["firstname"],
				'lastname'	=> $query["lastname"],
				'email'		=> $query["email"],
				'address1'	=> $query["address1"],
				'address2'	=> $query["address2"],
				// 'state'		=> $query["state"],
				'city'		=> $query["city"],
				'country'	=> $query["country"],
				'phone'		=> $query["phone"],
				'tags'		=> $product_tag,
				'zipcode'	=> $query["zipcode"],
				'ipAddress'	=> $query["ip_address"],
				'owner'     => $ownerId
			);

			$response = $contactApi->edit($ip_exist, $updatedData);
		}
	}
}


add_action( 'wp_ajax_contacts_sync', 'contacts_sync' );
add_action( 'wp_ajax_nopriv_contacts_sync', 'contacts_sync' );

function contacts_sync() {
	global $wpdb; // this is how you get access to the database

	$order_id = intval( $_POST['ID'] );
	$order = wc_get_order( $order_id );
	$query = _create_query( $order );

	$table_name = $wpdb->prefix . "sip_mautic_contacts_info"; 
	$uri = get_option('url_of_mautic_server');
    $public = get_option('public_key_of_mautic_server');
    $secret = get_option('secret_key_of_mautic_server');
    $ownerId = get_option('mautic_owner_id');
    $add_order_status_as_tag = get_option('add_order_status_as_tag');

    $settings = array(
        'baseUrl'           => $uri,
        'clientKey'         => $public,
        'clientSecret'      => $secret,
        'callback'          => admin_url('admin.php?page=sip-mautic-integration-settings'),
        'version'           => 'OAuth1a'
    );

	if (($info = get_option('sip_miwc_mautic_auth_info'))) {
		$settings['accessToken']        = $info['accessToken'] ;
		$settings['accessTokenSecret']  = $info['accessTokenSecret'];
		$settings['accessTokenExpires'] = $info['accessTokenExpires'];
	}

	sip_inc_mautic_lib();

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

	$contactApi = \Mautic\MauticApi::getContext(
	    'contacts',
	    $auth,
	    $uri . '/api/'
	);

	$ip = "is:anonymous ip:".$query["ip_address"];
	$response = $contactApi->getList($ip, 0, 1, "id");
	$contacts = $response["contacts"];

	$contact_id = "";
	foreach ($contacts as $key => $value) {
		$contact_id = $key;
	}

	$data = array(
		'firstname'	=> $query["firstname"],
		'lastname'	=> $query["lastname"],
		'email'		=> $query["email"],
		'address1'	=> $query["address1"],
		'company'	=> $query["company"],
		'address2'	=> $query["address2"],
		'city'		=> $query["city"],
		'country'	=> $query["country"],
		'phone'		=> $query["phone"],
		'tags'		=> $query["tag"],
		'zipcode'	=> $query["zipcode"],
		'ipAddress'	=> $query["ip_address"],
		'owner'     => $ownerId
	);

	if (empty($contact_id)) {

		$contact = $contactApi->create($data);
		if ( isset( $contact["error"]["details"]["country"][0] ) && ( $contact["error"]["details"]["country"][0] == "This value is not valid." ) ) {

			$data = array(
				'firstname'	=> $query["firstname"],
				'lastname'	=> $query["lastname"],
				'email'		=> $query["email"],
				'address1'	=> $query["address1"],
				'company'	=> $query["company"],
				'address2'	=> $query["address2"],
				'city'		=> $query["city"],
				'phone'		=> $query["phone"],
				'tags'		=> $query["tag"],
				'zipcode'	=> $query["zipcode"],
				'ipAddress'	=> $query["ip_address"],
				'owner'     => $ownerId
			);
			$contact = $contactApi->create($data);
		}

		if (isset($contact["contact"]["fields"]["all"]["id"])) {

			$contactID = $contact["contact"]["fields"]["all"]["id"];
			$contactEmail = $query["email"];

			if ( $contactID != "" ) {
				$wpdb->insert($table_name, array(
						'email'		=> $contactEmail,
						'contactId'	=> $contactID
					),array(
						'%s',
						'%d'
					)
				);
			}
		}

		echo "Insert " . $contact["contact"]["fields"]["all"]["id"];
	} else {
		
		$response = $contactApi->edit($contact_id, $data);
		echo "Update " . $response["contact"]["fields"]["all"]["id"];
	}
	
	wp_die(); // this is required to terminate immediately and return a proper response
}


add_action( 'wp_ajax_contacts_sync_update', 'contacts_sync_update' );
add_action( 'wp_ajax_nopriv_contacts_sync_update', 'contacts_sync_update' );

function contacts_sync_update() {
	global $wpdb; // this is how you get access to the database

	$order_id = intval( $_POST['ID'] );
	$order = wc_get_order( $order_id );
	$query = _create_query( $order );

	$table_name = $wpdb->prefix . "sip_mautic_contacts_info"; 
	$uri = get_option('url_of_mautic_server');
    $public = get_option('public_key_of_mautic_server');
    $secret = get_option('secret_key_of_mautic_server');
    $ownerId = get_option('mautic_owner_id');
    $add_order_status_as_tag = get_option('add_order_status_as_tag');

    $settings = array(
        'baseUrl'           => $uri,
        'clientKey'         => $public,
        'clientSecret'      => $secret,
        'callback'          => admin_url('admin.php?page=sip-mautic-integration-settings'),
        'version'           => 'OAuth1a'
    );

	if (($info = get_option('sip_miwc_mautic_auth_info'))) {
		$settings['accessToken']        = $info['accessToken'] ;
		$settings['accessTokenSecret']  = $info['accessTokenSecret'];
		$settings['accessTokenExpires'] = $info['accessTokenExpires'];
	}

	sip_inc_mautic_lib();

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

	$contactApi = \Mautic\MauticApi::getContext(
	    'contacts',
	    $auth,
	    $uri . '/api/'
	);


	$select_email_id = "SELECT contactId FROM $table_name WHERE email = '{$query["email"]}' ORDER BY email ASC limit 1";
	$select_contactID = $wpdb->get_results($select_email_id, OBJECT);

	$id = $select_contactID[0]->contactId;
	$response = $contactApi->get($id);
	$getData = $response["contact"]["tags"];

	foreach ($getData as $key => $value) {
		$prodct_name[] = $value["tag"];
	}

	$prodct_name[] = $query["tag"];
	$product_tag = implode( ',', $prodct_name );

	$updatedData = array(
		'firstname'	=> $query["firstname"],
		'lastname'	=> $query["lastname"],
		'email'		=> $query["email"],
		'address1'	=> $query["address1"],
		'company'	=> $query["company"],
		'address2'	=> $query["address2"],
		'city'		=> $query["city"],
		'country'	=> $query["country"],
		'phone'		=> $query["phone"],
		'tags'		=> $product_tag,
		'zipcode'	=> $query["zipcode"],
		'ipAddress'	=> $query["ip_address"],
		'owner'     => $ownerId
	);

	$response = $contactApi->edit($id, $updatedData);
	echo "Update " . $response["contact"]["fields"]["all"]["id"];

	wp_die(); // this is required to terminate immediately and return a proper response
}