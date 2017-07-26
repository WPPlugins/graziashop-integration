<?php

/**
 * @since             1.2.5
 * @package           graziashop-integration
 *
 * @wordpress-plugin
 * Plugin Name:       Graziashop Integration by Republik Media
 * Description:       E-Commerce integration plugin for Graziashop online platform.
 * Version:           1.2.5
 * Author:            Republik Media
 * Author URI:        http://republik-media.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       graziashop-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined('WPINC') ) {
	die;
}

// Plugin path
define( 'GI_DIR', plugin_dir_path( __FILE__ ) );

// Actions on activation of the plugin
function activate_graziashop_integration() {

	require_once GI_DIR . 'includes/class-activator.php';
	GI_Activator::activate();

} register_activation_hook( __FILE__, 'activate_graziashop_integration' );

// Actions on deactivation of the plugin
function deactivate_graziashop_integration() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	GI_Deactivator::deactivate();

} register_deactivation_hook( __FILE__, 'deactivate_graziashop_integration' );

// Get the main class of the plugin
require GI_DIR . 'includes/class-main.php';

// Run the plugin
function run_graziashop_integration() {

	$plugin = new GI_Main();

} run_graziashop_integration();