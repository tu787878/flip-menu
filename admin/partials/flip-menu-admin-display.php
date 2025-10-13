<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin/partials
 */

global $wpdb;
$table_name = $wpdb->prefix . 'flip_menu_shops';
$shops = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<p><?php _e( 'Manage your flip menus for different shops. Use shortcode [flip_menu shop_id="X"] to display a flip menu.', 'flip-menu' ); ?></p>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php _e( 'ID', 'flip-menu' ); ?></th>
				<th><?php _e( 'Shop Name', 'flip-menu' ); ?></th>
				<th><?php _e( 'Description', 'flip-menu' ); ?></th>
				<th><?php _e( 'Shortcode', 'flip-menu' ); ?></th>
				<th><?php _e( 'Created', 'flip-menu' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $shops ) ) : ?>
				<?php foreach ( $shops as $shop ) : ?>
					<tr>
						<td><?php echo esc_html( $shop->id ); ?></td>
						<td><strong><?php echo esc_html( $shop->name ); ?></strong></td>
						<td><?php echo esc_html( $shop->description ); ?></td>
						<td>
							<code>[flip_menu shop_id="<?php echo esc_attr( $shop->id ); ?>"]</code>
							<button class="button button-small copy-shortcode" data-shortcode='[flip_menu shop_id="<?php echo esc_attr( $shop->id ); ?>"]'>
								<?php _e( 'Copy', 'flip-menu' ); ?>
							</button>
						</td>
						<td><?php echo esc_html( $shop->created_at ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="5"><?php _e( 'No shops found. Create your first shop!', 'flip-menu' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<script>
	jQuery(document).ready(function($) {
		$('.copy-shortcode').on('click', function() {
			var shortcode = $(this).data('shortcode');
			navigator.clipboard.writeText(shortcode).then(function() {
				alert('<?php _e( 'Shortcode copied to clipboard!', 'flip-menu' ); ?>');
			});
		});
	});
	</script>
</div>
