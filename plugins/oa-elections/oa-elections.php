<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mckernan.in
 * @since             1.0.0
 * @package           OA_Elections
 *
 * @wordpress-plugin
 * Plugin Name:       Order of the Arrow - Elections
 * Plugin URI:        http://github.com/mckernanin/oa-elections
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Kevin McKernan
 * Author URI:        https://mckernan.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       oa-elections
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oae-activator.php
 */
function activate_oa_elections() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oae-activator.php';
	OAE_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oae-deactivator.php
 */
function deactivate_oa_elections() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oae-deactivator.php';
	OAE_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_oa_elections' );
register_deactivation_hook( __FILE__, 'deactivate_oa_elections' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-oae.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_oa_elections() {

	$plugin = new OAE();
	$plugin->run();

}
run_oa_elections();
