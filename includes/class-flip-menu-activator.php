<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 * @author     Your Name <email@example.com>
 */
class Flip_Menu_Activator {

	/**
	 * Create database tables for shops and menus.
	 *
	 * Creates the necessary database tables to store shop information
	 * and their associated flip menus.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Table for shops
		$table_shops = $wpdb->prefix . 'flip_menu_shops';
		$sql_shops = "CREATE TABLE $table_shops (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			description text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// Table for menus
		$table_menus = $wpdb->prefix . 'flip_menu_items';
		$sql_menus = "CREATE TABLE $table_menus (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			shop_id mediumint(9) NOT NULL,
			title varchar(255) NOT NULL,
			source_type varchar(20) NOT NULL,
			source_url text NOT NULL,
			page_order mediumint(9) NOT NULL DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY shop_id (shop_id),
			KEY page_order (page_order)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql_shops );
		dbDelta( $sql_menus );

		// Add version option
		add_option( 'flip_menu_version', FLIP_MENU_VERSION );
	}

}
