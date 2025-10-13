<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 * @author     Your Name <email@example.com>
 */
class Flip_Menu_Deactivator {

	/**
	 * Clean up on plugin deactivation.
	 *
	 * Note: Tables are NOT dropped on deactivation, only on uninstall.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear any cached data if needed
		wp_cache_flush();
	}

}
