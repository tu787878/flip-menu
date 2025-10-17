<?php

/**
 * Provide a admin area view for managing menus
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin/partials
 */

global $wpdb;
$shops_table = $wpdb->prefix . 'flip_menu_shops';
$items_table = $wpdb->prefix . 'flip_menu_items';

$shops = $wpdb->get_results( "SELECT * FROM $shops_table ORDER BY name ASC" );
$selected_shop = isset( $_GET['shop_id'] ) ? intval( $_GET['shop_id'] ) : ( ! empty( $shops ) ? $shops[0]->id : 0 );

$menu_items = array();
if ( $selected_shop ) {
	$menu_items = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM $items_table WHERE shop_id = %d ORDER BY page_order ASC",
		$selected_shop
	) );
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( empty( $shops ) ) : ?>
		<div class="notice notice-warning">
			<p><?php _e( 'Please create a shop first before adding menu items.', 'flip-menu' ); ?></p>
		</div>
	<?php else : ?>

		<div style="margin: 20px 0;">
			<label for="shop-selector"><strong><?php _e( 'Select Shop:', 'flip-menu' ); ?></strong></label>
			<select id="shop-selector" onchange="window.location.href='?page=flip-menu-menus&shop_id=' + this.value;">
				<?php foreach ( $shops as $shop ) : ?>
					<option value="<?php echo esc_attr( $shop->id ); ?>" <?php selected( $selected_shop, $shop->id ); ?>>
						<?php echo esc_html( $shop->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="card" style="max-width: 800px;">
			<h2><?php _e( 'Upload Menu Items', 'flip-menu' ); ?></h2>

			<div style="margin: 20px 0;">
				<h3><?php _e( 'Upload PDF', 'flip-menu' ); ?></h3>
				<p class="description"><?php _e( 'Upload a PDF file containing your menu (Note: requires Imagick for page conversion)', 'flip-menu' ); ?></p>
				<form id="pdf-upload-form" enctype="multipart/form-data">
					<input type="hidden" name="shop_id" value="<?php echo esc_attr( $selected_shop ); ?>" />
					<table class="form-table">
						<tr>
							<th><label for="pdf-file"><?php _e( 'PDF File', 'flip-menu' ); ?></label></th>
							<td><input type="file" id="pdf-file" name="pdf_file" accept=".pdf" required /></td>
						</tr>
					</table>
					<button type="submit" class="button button-primary"><?php _e( 'Upload PDF', 'flip-menu' ); ?></button>
					<span class="spinner" style="float: none;"></span>
				</form>
			</div>

			<hr />

			<div style="margin: 20px 0;">
				<h3><?php _e( 'Upload Images', 'flip-menu' ); ?></h3>
				<p class="description"><?php _e( 'Upload individual menu page images', 'flip-menu' ); ?></p>
				<form id="image-upload-form" enctype="multipart/form-data">
					<input type="hidden" name="shop_id" value="<?php echo esc_attr( $selected_shop ); ?>" />
					<table class="form-table">
						<tr>
							<th><label for="page-order"><?php _e( 'Page Order', 'flip-menu' ); ?></label></th>
							<td><input type="number" id="page-order" name="page_order" value="0" min="0" /></td>
						</tr>
						<tr>
							<th><label for="image-file"><?php _e( 'Image File', 'flip-menu' ); ?></label></th>
							<td><input type="file" id="image-file" name="image_file[]" accept="image/*" required multiple/></td>
						</tr>
					</table>
					<button type="submit" class="button button-primary"><?php _e( 'Upload Image', 'flip-menu' ); ?></button>
					<span class="spinner" style="float: none;"></span>
				</form>
			</div>
		</div>

		<div style="margin-top: 30px;">
			<h2><?php _e( 'Current Menu Items', 'flip-menu' ); ?></h2>
			<button id="delete-all-items" class="button button-danger" style="margin-bottom:10px;">
				<?php _e( 'Delete All', 'flip-menu' ); ?>
			</button>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e( 'ID', 'flip-menu' ); ?></th>
						<th><?php _e( 'Type', 'flip-menu' ); ?></th>
						<th><?php _e( 'Page Order', 'flip-menu' ); ?></th>
						<th><?php _e( 'Preview', 'flip-menu' ); ?></th>
						<th><?php _e( 'Actions', 'flip-menu' ); ?></th>
					</tr>
				</thead>
				<tbody id="menu-items-list">
					<?php if ( ! empty( $menu_items ) ) : ?>
						<?php foreach ( $menu_items as $item ) : ?>
							<tr data-item-id="<?php echo esc_attr( $item->id ); ?>">
								<td><?php echo esc_html( $item->id ); ?></td>
								<td><?php echo esc_html( strtoupper( $item->source_type ) ); ?></td>
								<td>
									<input type="number" class="inline-order" value="<?php echo esc_attr( $item->page_order ); ?>" min="0" style="width:60px;" />
								</td>
								<td>
									<?php if ( $item->source_type === 'image' ) : ?>
										<img src="<?php echo esc_url( $item->source_url ); ?>" style="max-width: 100px; height: auto;" />
									<?php else : ?>
										<a href="<?php echo esc_url( $item->source_url ); ?>" target="_blank"><?php _e( 'View PDF', 'flip-menu' ); ?></a>
									<?php endif; ?>
								</td>
								<td>
									<button class="button button-small delete-item" data-item-id="<?php echo esc_attr( $item->id ); ?>">
										<?php _e( 'Delete', 'flip-menu' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="5"><?php _e( 'No menu items found. Upload your first menu!', 'flip-menu' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

	<?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
	// PDF Upload
	$('#pdf-upload-form').on('submit', function(e) {
		e.preventDefault();
		var formData = new FormData(this);
		formData.append('action', 'flip_menu_upload_pdf');
		formData.append('nonce', flipMenuAdmin.nonce);

		var $spinner = $(this).find('.spinner');
		$spinner.addClass('is-active');

		$.ajax({
			url: flipMenuAdmin.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				$spinner.removeClass('is-active');
				if (response.success) {
					alert(response.data.message);
					location.reload();
				} else {
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				$spinner.removeClass('is-active');
				alert('<?php _e( 'An error occurred during upload', 'flip-menu' ); ?>');
			}
		});
	});

	// Image Upload
	$('#image-upload-form').on('submit', function(e) {
		e.preventDefault();
		var formData = new FormData(this);
		formData.append('action', 'flip_menu_upload_image');
		formData.append('nonce', flipMenuAdmin.nonce);

		var $spinner = $(this).find('.spinner');
		$spinner.addClass('is-active');

		$.ajax({
			url: flipMenuAdmin.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				$spinner.removeClass('is-active');
				if (response.success) {
					alert(response.data.message);
					location.reload();
				} else {
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				$spinner.removeClass('is-active');
				alert('<?php _e( 'An error occurred during upload', 'flip-menu' ); ?>');
			}
		});
	});

	// Delete Item
	$('.delete-item').on('click', function() {

		var itemId = $(this).data('item-id');
		var $row = $(this).closest('tr');

		$.ajax({
			url: flipMenuAdmin.ajax_url,
			type: 'POST',
			data: {
				action: 'flip_menu_delete_item',
				nonce: flipMenuAdmin.nonce,
				item_id: itemId
			},
			success: function(response) {
				if (response.success) {
					$row.fadeOut(function() {
						$(this).remove();
					});
				} else {
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				alert('<?php _e( 'An error occurred during deletion', 'flip-menu' ); ?>');
			}
		});
	});

	// Inline page order editing
	$('.inline-order').on('change', function() {
		var $row = $(this).closest('tr');
		var itemId = $row.data('item-id');
		var newOrder = $(this).val();

		$.ajax({
			url: flipMenuAdmin.ajax_url,
			type: 'POST',
			data: {
				action: 'flip_menu_update_order',
				nonce: flipMenuAdmin.nonce,
				item_id: itemId,
				page_order: newOrder
			},
			success: function(response) {
				if (!response.success) {
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				alert('<?php _e( 'An error occurred while updating order', 'flip-menu' ); ?>');
			}
		});
	});

	// Delete all items
	$('#delete-all-items').on('click', function() {
		console.log("delete all clicked");
		$.ajax({
			url: flipMenuAdmin.ajax_url,
			type: 'POST',
			data: {
				action: 'flip_menu_delete_all_items',
				nonce: flipMenuAdmin.nonce,
				shop_id: <?php echo intval($selected_shop); ?>
			},
			success: function(response) {
				console.log(response);
				if (response.success) {
					$('#menu-items-list').empty().append('<tr><td colspan="5"><?php _e( 'No menu items found. Upload your first menu!', 'flip-menu' ); ?></td></tr>');
				} else {
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				alert('<?php _e( 'An error occurred during deletion', 'flip-menu' ); ?>');
			}
		});
	});
});
</script>
