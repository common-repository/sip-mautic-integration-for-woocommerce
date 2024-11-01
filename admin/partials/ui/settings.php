<?php
if (! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
} ?>

<div class="settings-warp sip-tab-content" style="margin-top:10px;">
	<form method="post" action="options.php">
		<?php settings_fields( 'sip-miwc-register-settings' ); ?>
		<?php do_settings_sections( 'sip-miwc-register-settings' ); ?>
		<table class="sip-miwc-100">
			<tr>
				<td>
					<label for="url_of_mautic_server"><b><?php _e('URL of Mautic server : ' , 'sip-mautic-integration');?></label></b><br>
					<input class="sip-miwc-100" placeholder="http://example.com" id="url_of_mautic_server" type="text" name="url_of_mautic_server" value="<?php echo get_option('url_of_mautic_server'); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="public_key_of_mautic_server"><b><?php _e('Public Key : ' , 'sip-mautic-integration');?></label></b><br>
					<input class="sip-miwc-100" placeholder="3xid67ltj82sscsc408c08o0kkg4w40kwkg88gkk4ggco4okkc" id="public_key_of_mautic_server" type="text" name="public_key_of_mautic_server" value="<?php echo get_option('public_key_of_mautic_server'); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="secret_key_of_mautic_server"><b><?php _e('Secret Key : ' , 'sip-mautic-integration');?></label></b><br>
					<input class="sip-miwc-100" placeholder="23ecfhgt13xcg0cgosogck80gogs4cgcsco4gw40w0gg4co4oo" id="secret_key_of_mautic_server" type="text" name="secret_key_of_mautic_server" value="<?php echo get_option('secret_key_of_mautic_server'); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<label for="product_name_as_tag"><b><?php _e('Add product name as contact tag : ' , 'sip-mautic-integration');?></b></label>
					<input class="sip-miwc-100" placeholder="" id="product_name_as_tag" type="checkbox" <?php echo ( (get_option('product_name_as_tag') == true) ? 'checked' : '' )?> name="product_name_as_tag" value="true" />
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<label for="sku_as_tag"><b><?php _e('Add product SKU as contact tag : ' , 'sip-mautic-integration');?></label></b> 
					<input class="sip-miwc-100" placeholder="" id="sku_as_tag" type="checkbox" <?php echo ( (get_option('sku_as_tag') == true) ? 'checked' : '' )?> name="sku_as_tag" value="true" />
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<label for="add_order_status_as_tag"><b><?php _e('Add order status as a tag : ' , 'sip-mautic-integration');?></label></b> 
					<input class="sip-miwc-100" placeholder="" id="add_order_status_as_tag" type="checkbox" <?php echo ( (get_option('add_order_status_as_tag') == true) ? 'checked' : '' )?> name="add_order_status_as_tag" value="true" />
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<label for="additional_tags_to_add"><b><?php _e('Additional tags to add : ' , 'sip-mautic-integration');?></label></b><br>
					<input class="sip-miwc-100" placeholder="WordPress, WooCommerce" id="additional_tags_to_add" type="text" name="additional_tags_to_add" value="<?php echo get_option('additional_tags_to_add'); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<br>
					<label for="mautic_owner_id"><b><?php _e('Owner : ' , 'sip-mautic-integration');?></label></b><br>
					<input class="sip-miwc-100" placeholder="1" id="mautic_owner_id" type="number" name="mautic_owner_id" value="<?php echo get_option('mautic_owner_id'); ?>" />
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
	
	<?php $mautic_auth_connect_disconnect = get_option('sip_miwc_mautic_auth_connect'); ?>

	<hr>
	<table>
		<tr>
			<td><b><?php _e('Mautic Connection : ' , 'sip-mautic-integration'); ?></b></td>
			<?php if ( $mautic_auth_connect_disconnect == false ) { ?>
			<td>
				<?php
					echo "<form action='admin.php?page=sip-mautic-integration-settings' method='post'>";
						// this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
						wp_nonce_field('Mautic_Connection_Connect_Nonce');
						echo '<input type="hidden" value="true" name="mautic_connection_connect" />';
						submit_button('Connect');
					echo '</form>';
				?>
			</td>
			<?php } else { ?>
			<td>
				<?php
					echo "<form action='admin.php?page=sip-mautic-integration-settings' method='post'>";

						// this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
						wp_nonce_field('Mautic_Connection_DisConnect_Nonce');
						echo '<input type="hidden" value="true" name="mautic_connection_dis_connect" />';
						submit_button('Disconnect');
					echo '</form>';
				?>
			</td>
			<?php } ?>
		</tr>
	</table>
	<hr>
	<?php if ( $mautic_auth_connect_disconnect == true ) { ?>
	<table>
		<tr>
			<td><b><?php _e('Prepare Mautic Sync : ' , 'sip-mautic-integration'); ?></b></td>
			<td>
				<?php
					echo "<form action='admin.php?page=sip-mautic-integration-settings' method='post'>";

						// this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
						wp_nonce_field('Prepare_Mautic_old_order_sync_Nonce');
						echo '<input type="hidden" value="true" name="prepare_mautic_old_order_sync" />';
						submit_button('Prepare Sync data');
					echo '</form>';
				?>
			</td>
		</tr>
		<tr>
			<td><b><?php _e('Mautic Sync : ' , 'sip-mautic-integration'); ?></b></td>
			<td>
				<?php
					echo "<form id='sync_old_order_form' action='admin.php?page=sip-mautic-integration-settings' method='post'>";

						// this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
						wp_nonce_field('Mautic_old_order_sync_Nonce');
						echo '<input type="hidden" value="true" name="mautic_old_order_sync" />';
						submit_button('Sync old order');
					echo '</form>';
				?>
			</td>
		</tr>
	</table>
	<hr>
	<?php } ?>
</div>