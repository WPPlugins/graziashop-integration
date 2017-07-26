<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.2.5
 *
 * @package    graziashop-integration
 * @subpackage graziashop-integration/includes
 */

class GI_Main {

	private $plugin_slug;
	private $version;

	// Define the core functionality of the plugin
	public function __construct() {

		$this->plugin_slug = 'graziashop-integration';
		$this->version     = '1.2.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	// Load the required dependencies for this plugin.
	private function load_dependencies() {

		// Languages
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php' );

		// Admin class
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php' );

	}

	// Define the locale for this plugin for internationalization.
	private function set_locale() {

		$plugin_i18n = new GI_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	// Register all of the hooks related to the admin area functionality
	private function define_admin_hooks() {

		$admin = new GI_Admin( $this->get_plugin_name(), $this->get_version() );

		// Load custom CSS
		add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_styles' ) );

		// Load custom JS
		add_action( 'admin_enqueue_scripts', array( $admin, 'enqueue_scripts' ) );

		// Register menu admin link
		add_action( 'admin_menu', array( $admin, 'register_admin_menu_link' ) );

		// Register settings
		add_action( 'admin_init', array( $admin, 'register_admin_settings' ) );

		// Show the "Generate CSV" link on the plugins table
		// NOT READY FOR RELEASE
		// add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'graziashop-integration.php' ), array( $admin, 'add_plugin_table_link'), 10, 2 );

		// Support for the template of the page that will generate the CSV file
		add_filter( 'page_attributes_dropdown_pages_args', array( $admin, 'register_project_templates' ) );
		add_filter( 'wp_insert_post_data', array( $admin, 'register_project_templates' ) );
		add_filter( 'template_include', array( $admin, 'view_project_template') );

		// Require TGMPA class
		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'resources/class-tgm-plugin-activation.php' );

		// TGM function: check that the plugin "Advanced Custom Fields" is intalled on the Wordpress site
		// If not, download, install and activate it
		add_action( 'tgmpa_register', array( $admin, 'register_required_plugins') );

		// Register custom fields for all WooCommerce products
		add_action( 'admin_init', array( $admin, 'register_products_custom_fields' ) );

	}

	// Public function to get different data
	public function get_plugin_name() {
		return $this->plugin_slug;
	}

	public function get_version() {
		return $this->version;
	}

}
