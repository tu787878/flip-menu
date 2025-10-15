<?php

/**
 * Provide an admin area view for API and embed settings
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin/partials
 */

$api_enabled = get_option( 'flip_menu_api_enabled', false );
$api_key = get_option( 'flip_menu_api_key', '' );
$cors_enabled = get_option( 'flip_menu_api_cors_enabled', true );
$allowed_origins = get_option( 'flip_menu_api_allowed_origins', '*' );

$site_url = get_site_url();
$api_base_url = rest_url( 'flip-menu/v1' );
$widget_script_url = plugin_dir_url( dirname( __FILE__ ) ) . '../public/js/flip-menu-widget.js';
$turnjs_script_url = plugin_dir_url( dirname( __FILE__ ) ) . '../public/js/turn.min.js';

global $wpdb;
$shops_table = $wpdb->prefix . 'flip_menu_shops';
$shops = $wpdb->get_results( "SELECT * FROM $shops_table ORDER BY name ASC" );
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<p><?php _e( 'Configure API access and generate embed codes for external websites.', 'flip-menu' ); ?></p>

	<!-- API Settings Form -->
	<div class="card" style="max-width: 900px; margin-top: 20px;">
		<h2><?php _e( 'API Settings', 'flip-menu' ); ?></h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'flip_menu_api_settings_action', 'flip_menu_api_settings_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Enable API', 'flip-menu' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="api_enabled" value="1" <?php checked( $api_enabled, 1 ); ?> />
							<?php _e( 'Allow external access to shop data via REST API', 'flip-menu' ); ?>
						</label>
						<p class="description"><?php _e( 'Enable this to allow other websites to access your flip menus', 'flip-menu' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'API Key', 'flip-menu' ); ?></th>
					<td>
						<input type="text" name="api_key" id="api-key-field" class="regular-text" value="<?php echo esc_attr( $api_key ); ?>" readonly />
						<button type="submit" name="generate_new_key" class="button" onclick="return confirm('<?php _e( 'Generate a new API key? The old key will stop working.', 'flip-menu' ); ?>')">
							<?php _e( 'Generate New Key', 'flip-menu' ); ?>
						</button>
						<button type="button" class="button" onclick="copyToClipboard('api-key-field')">
							<?php _e( 'Copy', 'flip-menu' ); ?>
						</button>
						<p class="description"><?php _e( 'Use this key to authenticate API requests. Leave empty for public access.', 'flip-menu' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Enable CORS', 'flip-menu' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="cors_enabled" value="1" <?php checked( $cors_enabled, 1 ); ?> />
							<?php _e( 'Allow cross-origin requests', 'flip-menu' ); ?>
						</label>
						<p class="description"><?php _e( 'Required for external websites to access the API', 'flip-menu' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Allowed Origins', 'flip-menu' ); ?></th>
					<td>
						<input type="text" name="allowed_origins" class="regular-text" value="<?php echo esc_attr( $allowed_origins ); ?>" />
						<p class="description"><?php _e( 'Use * for all origins, or specify domain (e.g., https://example.com)', 'flip-menu' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save API Settings', 'flip-menu' ) ); ?>
		</form>
	</div>

	<!-- API Documentation -->
	<div class="card" style="max-width: 900px; margin-top: 20px;">
		<h2><?php _e( 'API Endpoints', 'flip-menu' ); ?></h2>

		<p><?php _e( 'Base URL:', 'flip-menu' ); ?> <code><?php echo esc_html( $api_base_url ); ?></code></p>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Endpoint', 'flip-menu' ); ?></th>
					<th><?php _e( 'Description', 'flip-menu' ); ?></th>
					<th><?php _e( 'Example', 'flip-menu' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>GET /shops</code></td>
					<td><?php _e( 'Get all shops', 'flip-menu' ); ?></td>
					<td><button class="button button-small" onclick="testEndpoint('/shops')"><?php _e( 'Test', 'flip-menu' ); ?></button></td>
				</tr>
				<tr>
					<td><code>GET /shops/{id}</code></td>
					<td><?php _e( 'Get single shop', 'flip-menu' ); ?></td>
					<td><button class="button button-small" onclick="testEndpoint('/shops/1')"><?php _e( 'Test', 'flip-menu' ); ?></button></td>
				</tr>
				<tr>
					<td><code>GET /shops/{id}/menu</code></td>
					<td><?php _e( 'Get shop menu items', 'flip-menu' ); ?></td>
					<td><button class="button button-small" onclick="testEndpoint('/shops/1/menu')"><?php _e( 'Test', 'flip-menu' ); ?></button></td>
				</tr>
				<tr>
					<td><code>GET /shops/{id}/complete</code></td>
					<td><?php _e( 'Get shop + menu items', 'flip-menu' ); ?></td>
					<td><button class="button button-small" onclick="testEndpoint('/shops/1/complete')"><?php _e( 'Test', 'flip-menu' ); ?></button></td>
				</tr>
			</tbody>
		</table>

		<div id="api-test-result" style="margin-top: 20px; display: none;">
			<h3><?php _e( 'API Response:', 'flip-menu' ); ?></h3>
			<pre id="api-response" style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; overflow: auto; max-height: 400px;"></pre>
		</div>
	</div>

	<!-- Embed Code Generator -->
	<div class="card" style="max-width: 900px; margin-top: 20px;">
		<h2><?php _e( 'Embed Widget Generator', 'flip-menu' ); ?></h2>

		<p><?php _e( 'Generate embed code for external websites to display your flip menus.', 'flip-menu' ); ?></p>

		<?php if ( empty( $shops ) ) : ?>
			<div class="notice notice-warning inline">
				<p><?php _e( 'Please create a shop first before generating embed codes.', 'flip-menu' ); ?></p>
			</div>
		<?php else : ?>

			<table class="form-table">
				<tr>
					<th scope="row"><label for="embed-shop-id"><?php _e( 'Select Shop', 'flip-menu' ); ?></label></th>
					<td>
						<select id="embed-shop-id" class="regular-text" onchange="generateEmbedCode()">
							<?php foreach ( $shops as $shop ) : ?>
								<option value="<?php echo esc_attr( $shop->id ); ?>">
									<?php echo esc_html( $shop->name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="embed-width"><?php _e( 'Width', 'flip-menu' ); ?></label></th>
					<td>
						<input type="number" id="embed-width" value="800" min="400" max="1600" onchange="generateEmbedCode()" />
						<span>px</span>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="embed-height"><?php _e( 'Height', 'flip-menu' ); ?></label></th>
					<td>
						<input type="number" id="embed-height" value="600" min="300" max="1200" onchange="generateEmbedCode()" />
						<span>px</span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Embed Code', 'flip-menu' ); ?></th>
					<td>
						<textarea id="embed-code" class="large-text code" rows="10" readonly></textarea>
						<br />
						<button type="button" class="button button-primary" onclick="copyToClipboard('embed-code')">
							<?php _e( 'Copy Embed Code', 'flip-menu' ); ?>
						</button>
						<p class="description"><?php _e( 'Copy this code and paste it into any website to display the flip menu.', 'flip-menu' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php _e( 'Preview', 'flip-menu' ); ?></h3>
			<div id="embed-preview" style="border: 2px dashed #ddd; padding: 20px; background: #f9f9f9;">
				<p style="text-align: center; color: #666;"><?php _e( 'Embed preview will appear here', 'flip-menu' ); ?></p>
			</div>

		<?php endif; ?>
	</div>

</div>

<script>
jQuery(document).ready(function($) {
	// Generate initial embed code
	generateEmbedCode();
});

function copyToClipboard(elementId) {
	var element = document.getElementById(elementId);
	element.select();
	element.setSelectionRange(0, 99999);
	document.execCommand('copy');
	alert('<?php _e( 'Copied to clipboard!', 'flip-menu' ); ?>');
}

function generateEmbedCode() {
	var shopId = document.getElementById('embed-shop-id').value;
	var width = document.getElementById('embed-width').value;
	var height = document.getElementById('embed-height').value;

	var apiUrl = '<?php echo esc_js( rest_url() ); ?>';
	var apiKey = '<?php echo esc_js( $api_key ); ?>';
	var widgetUrl = '<?php echo esc_js( $widget_script_url ); ?>';
	var turnjsUrl = '<?php echo esc_js( $turnjs_script_url ); ?>';


	var embedCode = '<!-- Flip Menu Widget -->\n';
	embedCode += '<div data-flip-menu-widget\n';
	embedCode += '     data-shop-id="' + shopId + '"\n';
	embedCode += '     data-api-url="' + apiUrl + '"\n';
	if (apiKey) {
		embedCode += '     data-api-key="' + apiKey + '"\n';
	}
	embedCode += '     data-width="' + width + '"\n';
	embedCode += '     data-height="' + height + '">\n';
	embedCode += '</div>\n';
	embedCode += '<script src="'+turnjsUrl+'"><\/script>';
	embedCode += '<script src="' + widgetUrl + '"><\/script>';
	document.getElementById('embed-code').value = embedCode;

	// Update preview
	var previewDiv = document.getElementById('embed-preview');
	previewDiv.innerHTML = '<div data-flip-menu-widget data-shop-id="' + shopId + '" data-api-url="' + apiUrl + '" data-api-key="' + apiKey + '" data-width="' + width + '" data-height="' + height + '"></div>';

	// Reinitialize widget if available
	if (typeof FlipMenuWidget !== 'undefined') {
		FlipMenuWidget.init();
	}
}

function testEndpoint(endpoint) {
	var baseUrl = '<?php echo esc_js( $api_base_url ); ?>';
	var apiKey = '<?php echo esc_js( $api_key ); ?>';
	var url = baseUrl + endpoint;

	if (apiKey) {
		url += '?api_key=' + encodeURIComponent(apiKey);
	}

	document.getElementById('api-test-result').style.display = 'block';
	document.getElementById('api-response').textContent = 'Loading...';

	fetch(url)
		.then(response => response.json())
		.then(data => {
			document.getElementById('api-response').textContent = JSON.stringify(data, null, 2);
		})
		.catch(error => {
			document.getElementById('api-response').textContent = 'Error: ' + error.message;
		});
}
</script>

<style>
.card {
	padding: 20px;
	background: white;
	border: 1px solid #ccd0d4;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.card h2 {
	margin-top: 0;
}
code {
	background: #f0f0f1;
	padding: 2px 6px;
	border-radius: 3px;
}
</style>
