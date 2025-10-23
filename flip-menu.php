<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://shop.tcg-marketing.de/
 * @since             1.0.1
 * @package           Flip_Menu
 *
 * @wordpress-plugin
 * Plugin Name:       Flip Menu
 * Plugin URI:        https://shop.tcg-marketing.de/
 * Description:       Create interactive flip menus using Turn.js. Upload PDFs or images to create beautiful flip-through menus for different shops.
 * Version:           1.0.1
 * Author:            TCG Web & Marketing
 * Author URI:        https://shop.tcg-marketing.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       flip-menu
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FLIP_MENU_VERSION', '1.0.0' );
define( 'FLIP_MENU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLIP_MENU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-flip-menu-activator.php
 */
function activate_flip_menu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flip-menu-activator.php';
	Flip_Menu_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-flip-menu-deactivator.php
 */
function deactivate_flip_menu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flip-menu-deactivator.php';
	Flip_Menu_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_flip_menu' );
register_deactivation_hook( __FILE__, 'deactivate_flip_menu' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-flip-menu.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_flip_menu() {

	$plugin = new Flip_Menu();
	$plugin->run();

}
run_flip_menu();
