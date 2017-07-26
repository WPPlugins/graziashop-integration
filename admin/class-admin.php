<?php

/**
 * The admin-specific functionality of the plugin
 *
 * @since      1.2.5
 *
 * @package    graziashop-integration
 * @subpackage graziashop-integration/admin
 */

class GI_Admin {

	private $plugin_slug;
	private $version;

	protected $templates;

	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->version 	   = $version;

		$this->templates   = array(
			'generate-csv.php' => __( 'Graziashop Integration Plugin - Generate CSV Template', $this->plugin_slug )
		);

	}

	// Show the "Generate CSV" option on the plugins table
	public function add_plugin_table_link( $links ) {

		$generate_csv_link = '<a href="' . get_site_url() . '/' . get_option('graziashop_integration_page_name') . '">' . __( 'Graziashop Integration', 'graziashop-integration' ) . '</a>';

		array_unshift( $links, $generate_csv_link );

		return $links;
	}

	// Register settings
	public function register_admin_settings() {
		register_setting('baw-settings-group', 'gi_brand_field_name');
		register_setting('baw-settings-group', 'gi_custom_brand_name');
	}

	// Register the stylesheets for the admin area.
	public function register_admin_menu_link() {

		global $menu;

		add_menu_page('Graziashop Integration', 'Graziashop', 'read', 'gi_csv', array( $this, 'include_admin_page' ), 'none', 56);

		// Edit new menu link URL
		// $menu[56][2] = get_site_url() . '/' . get_option('graziashop_integration_page_name');

	}

	public function include_admin_page() {
		include( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/admin-page.php' );
	}

	public function register_project_templates( $attributes ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. If it doesn't exist, or it's empty prepare an array
		$templates = wp_cache_get( $cache_key, 'themes' );
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// Since we've updated the cache, we need to delete the old cache
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $attributes;

	}

	// Checks if the template is assigned to the page
	public function view_project_template( $template ) {

		global $post;

		// If no posts found, return to avoid "Trying to get property of non-object" error
		if ( !isset( $post ) ) {
			return $template;
		}

		if ( ! isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
			return $template;
		}

		$file = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . get_post_meta( $post->ID, '_wp_page_template', true );

		// Just to be safe, we check if the file exist first
		if( file_exists( $file ) ) {
			return $file;
		}

		return $template;

	}

	// Load custom CSS
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
	}

	// Load custom JS
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/admin.js', array(), $this->version, 'all' );
	}

    // Update and return ACF path
	public function acf_settings_path() {
	    $path = plugin_dir_url( dirname( __FILE__ ) ) . '/advanced-custom-fields-pro/';
	    return $path;
	}

	// Check through TGMPA class that the ACF plugin is installed and activated
	public function register_required_plugins() {
		$plugins = array(
			array(
				'name'      	   => 'Advanced Custom Fields',
				'slug'      	   => 'advanced-custom-fields',
				'required'  	   => true,
				'version'   	   => '4.3.9'
			)
		);

		// TGMPA configuration
		$config = array(
			'id'           => $this->plugin_slug . '_tgmpa', // Unique ID for TGMPA instance
			'dismissable'  => false, // Make the alerts not dismissable
			'is_automatic' => true // Automatically activate plugins after installation
		);

		tgmpa( $plugins, $config );
	}

	// Register custom fields through ACF/ACF Pro
	public function register_products_custom_fields() {

		if( function_exists("register_field_group") ) {
			register_field_group(array (
				'id' => 'acf_characteristics',
				'title' => 'Characteristics',
				'fields' => array (
					array (
						'key' => 'field_548869809d7cd',
						'label' => 'Sell on Graziashop',
						'name' => 'product-graziashop',
						'type' => 'true_false',
						'instructions' => 'Choose if you want to sell this product on Graziashop or if you prefer to sell it exclusively on your store.',
						'message' => '',
						'default_value' => 1,
					),
					array (
						'key' => 'field_55c9d51d0e640',
						'label' => 'Brand\'s name',
						'name' => 'product-brand-name',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_55c9d5600e642',
						'label' => 'Barcode EAN/UPC',
						'name' => 'product-barcode',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_543ba308c9848',
						'label' => 'Category',
						'name' => 'product-category',
						'type' => 'select',
						'required' => 1,
						'choices' => array (
							'Clothing & Accessories' => 'Clothing & Accessories',
							'Clothing & Accessories > Clothing > Activewear' => 'Clothing & Accessories > Clothing > Activewear',
							'Clothing & Accessories > Clothing > Beachwear' => 'Clothing & Accessories > Clothing > Beachwear',
							'Clothing & Accessories > Clothing > Dresses' => 'Clothing & Accessories > Clothing > Dresses',
							'Clothing & Accessories > Clothing > Knitwear' => 'Clothing & Accessories > Clothing > Knitwear',
							'Clothing & Accessories > Clothing > Sleepwear' => 'Clothing & Accessories > Clothing > Sleepwear',
							'Clothing & Accessories > Clothing > Jumpsuits' => 'Clothing & Accessories > Clothing > Jumpsuits',
							'Clothing & Accessories > Clothing > Outerwear > Coats' => 'Clothing & Accessories > Clothing > Outerwear > Coats',
							'Clothing & Accessories > Clothing > Outerwear > Jackets' => 'Clothing & Accessories > Clothing > Outerwear > Jackets',
							'Clothing & Accessories > Clothing > Tops' => 'Clothing & Accessories > Clothing > Tops',
							'Clothing & Accessories > Clothing > Shorts' => 'Clothing & Accessories > Clothing > Shorts',
							'Clothing & Accessories > Clothing > Skirts' => 'Clothing & Accessories > Clothing > Skirts',
							'Clothing & Accessories > Clothing > Suits' => 'Clothing & Accessories > Clothing > Suits',
							'Clothing & Accessories > Clothing > Jeans' => 'Clothing & Accessories > Clothing > Jeans',
							'Clothing & Accessories > Clothing > Trousers' => 'Clothing & Accessories > Clothing > Trousers',
							'Clothing & Accessories > Clothing > Lingerie' => 'Clothing & Accessories > Clothing > Lingerie',
							'Clothing & Accessories > Accessories' => 'Clothing & Accessories > Accessories',
							'Clothing & Accessories > Accessories > Bags' => 'Clothing & Accessories > Accessories > Bags',
							'Clothing & Accessories > Accessories > Eyewear' => 'Clothing & Accessories > Accessories > Eyewear',
							'Clothing & Accessories > Accessories > Jewellery' => 'Clothing & Accessories > Accessories > Jewellery',
							'Clothing & Accessories > Accessories > Watches' => 'Clothing & Accessories > Accessories > Watches',
							'Clothing & Accessories > Shoes' => 'Clothing & Accessories > Shoes',
							'Clothing & Accessories > Shoes > Boots' => 'Clothing & Accessories > Shoes > Boots',
							'Clothing & Accessories > Shoes > Flats' => 'Clothing & Accessories > Shoes > Flats',
							'Clothing & Accessories > Shoes > Outdoor' => 'Clothing & Accessories > Shoes > Outdoor',
							'Clothing & Accessories > Shoes > Pumps' => 'Clothing & Accessories > Shoes > Pumps',
							'Clothing & Accessories > Shoes > Sandals' => 'Clothing & Accessories > Shoes > Sandals',
							'Clothing & Accessories > Shoes > Trainers' => 'Clothing & Accessories > Shoes > Trainers',
						),
						'default_value' => 'Clothing & Accessories',
						'allow_null' => 0,
						'multiple' => 0,
					),
					array (
						'key' => 'field_543ba336c9849',
						'label' => 'Description',
						'name' => 'product-description',
						'type' => 'textarea',
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'formatting' => 'br',
					),
					array (
						'key' => 'field_543ba353c984a',
						'label' => 'Material',
						'name' => 'product-material',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_55c9d57c0e644',
						'label' => 'Care',
						'name' => 'product-care',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_55c9d56e0e643',
						'label' => 'Season',
						'name' => 'product-season',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_54e32182fb7a9',
						'label' => 'Fit',
						'name' => 'product-fit',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_543ba365c984b',
						'label' => 'Colour',
						'name' => 'product-colour',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'product',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'default',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}

	}

}