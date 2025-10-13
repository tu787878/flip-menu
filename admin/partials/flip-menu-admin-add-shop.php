<?php

/**
 * Provide a admin area view for adding a new shop
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin/partials
 */
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="">
		<?php wp_nonce_field( 'flip_menu_shop_action', 'flip_menu_shop_nonce' ); ?>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="shop_name"><?php _e( 'Shop Name', 'flip-menu' ); ?> *</label>
				</th>
				<td>
					<input type="text" name="shop_name" id="shop_name" class="regular-text" required />
					<p class="description"><?php _e( 'Enter the name of your shop or restaurant', 'flip-menu' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="shop_description"><?php _e( 'Description', 'flip-menu' ); ?></label>
				</th>
				<td>
					<textarea name="shop_description" id="shop_description" rows="5" class="large-text"></textarea>
					<p class="description"><?php _e( 'Optional description of your shop', 'flip-menu' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Create Shop', 'flip-menu' ) ); ?>
	</form>
</div>
