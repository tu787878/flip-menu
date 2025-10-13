<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and handles all admin functionality
 * including menu management, file uploads, and settings.
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/admin
 * @author     Your Name <email@example.com>
 */
class Flip_Menu_Admin {

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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/flip-menu-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/flip-menu-admin.js', array( 'jquery' ), $this->version, false );

		// Pass AJAX URL to JavaScript
		wp_localize_script( $this->plugin_name, 'flipMenuAdmin', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'flip_menu_nonce' )
		));

	}

	/**
	 * Add admin menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		add_menu_page(
			__( 'Flip Menu', 'flip-menu' ),
			__( 'Flip Menu', 'flip-menu' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' ),
			'dashicons-book',
			30
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'All Shops', 'flip-menu' ),
			__( 'All Shops', 'flip-menu' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Add New Shop', 'flip-menu' ),
			__( 'Add New Shop', 'flip-menu' ),
			'manage_options',
			$this->plugin_name . '-new',
			array( $this, 'display_add_shop_page' )
		);

		add_submenu_page(
			$this->plugin_name,
			__( 'Manage Menus', 'flip-menu' ),
			__( 'Manage Menus', 'flip-menu' ),
			'manage_options',
			$this->plugin_name . '-menus',
			array( $this, 'display_manage_menus_page' )
		);

	}

	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		// Can add settings here if needed
	}

	/**
	 * Display the main admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once( 'partials/flip-menu-admin-display.php' );
	}

	/**
	 * Display the add shop page.
	 *
	 * @since    1.0.0
	 */
	public function display_add_shop_page() {
		$this->handle_shop_form();
		include_once( 'partials/flip-menu-admin-add-shop.php' );
	}

	/**
	 * Display the manage menus page.
	 *
	 * @since    1.0.0
	 */
	public function display_manage_menus_page() {
		include_once( 'partials/flip-menu-admin-manage-menus.php' );
	}

	/**
	 * Handle shop form submission.
	 *
	 * @since    1.0.0
	 */
	private function handle_shop_form() {
		global $wpdb;

		if ( ! isset( $_POST['flip_menu_shop_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['flip_menu_shop_nonce'], 'flip_menu_shop_action' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$shop_name = sanitize_text_field( $_POST['shop_name'] );
		$shop_description = sanitize_textarea_field( $_POST['shop_description'] );

		$table_name = $wpdb->prefix . 'flip_menu_shops';

		$result = $wpdb->insert(
			$table_name,
			array(
				'name' => $shop_name,
				'description' => $shop_description
			),
			array( '%s', '%s' )
		);

		if ( $result ) {
			add_action( 'admin_notices', array( $this, 'shop_saved_notice' ) );
		}
	}

	/**
	 * Display admin notice for successful shop save.
	 *
	 * @since    1.0.0
	 */
	public function shop_saved_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Shop saved successfully!', 'flip-menu' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Handle PDF upload via AJAX.
	 *
	 * @since    1.0.0
	 */
	public function handle_pdf_upload() {
		check_ajax_referer( 'flip_menu_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'flip-menu' ) ) );
		}

		if ( ! isset( $_FILES['pdf_file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file uploaded', 'flip-menu' ) ) );
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$file = $_FILES['pdf_file'];
		$shop_id = intval( $_POST['shop_id'] );

		// Upload the PDF file
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $file, $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'flip_menu_items';

			// Note: PDF to image conversion would require additional libraries (like Imagick)
			// For now, we'll store the PDF URL and handle conversion separately
			$wpdb->insert(
				$table_name,
				array(
					'shop_id' => $shop_id,
					'title' => sanitize_text_field( $_POST['title'] ),
					'source_type' => 'pdf',
					'source_url' => $movefile['url'],
					'page_order' => 0
				),
				array( '%d', '%s', '%s', '%s', '%d' )
			);

			wp_send_json_success( array(
				'message' => __( 'PDF uploaded successfully', 'flip-menu' ),
				'url' => $movefile['url']
			) );
		} else {
			wp_send_json_error( array( 'message' => $movefile['error'] ) );
		}
	}

	/**
	 * Handle image upload via AJAX.
	 *
	 * @since    1.0.0
	 */
	public function handle_image_upload() {
		check_ajax_referer( 'flip_menu_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'flip-menu' ) ) );
		}

		if ( ! isset( $_FILES['image_file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file uploaded', 'flip-menu' ) ) );
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$file = $_FILES['image_file'];
		$shop_id = intval( $_POST['shop_id'] );
		$page_order = intval( $_POST['page_order'] );

		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $file, $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'flip_menu_items';

			$wpdb->insert(
				$table_name,
				array(
					'shop_id' => $shop_id,
					'title' => sanitize_text_field( $_POST['title'] ),
					'source_type' => 'image',
					'source_url' => $movefile['url'],
					'page_order' => $page_order
				),
				array( '%d', '%s', '%s', '%s', '%d' )
			);

			wp_send_json_success( array(
				'message' => __( 'Image uploaded successfully', 'flip-menu' ),
				'url' => $movefile['url'],
				'id' => $wpdb->insert_id
			) );
		} else {
			wp_send_json_error( array( 'message' => $movefile['error'] ) );
		}
	}

	/**
	 * Handle menu item deletion via AJAX.
	 *
	 * @since    1.0.0
	 */
	public function handle_delete_item() {
		check_ajax_referer( 'flip_menu_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'flip-menu' ) ) );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'flip_menu_items';
		$item_id = intval( $_POST['item_id'] );

		$deleted = $wpdb->delete( $table_name, array( 'id' => $item_id ), array( '%d' ) );

		if ( $deleted ) {
			wp_send_json_success( array( 'message' => __( 'Item deleted successfully', 'flip-menu' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to delete item', 'flip-menu' ) ) );
		}
	}

}
