<?php

/**
 * Fired during plugin activation
 *
 * @since      1.2.5
 *
 * @package    graziashop-integration
 * @subpackage graziashop-integration/includes
 */

class GI_Activator {

	private $plugin_slug;

	// Define the core functionality of the plugin
	public function __construct() {

		$this->plugin_slug = 'graziashop-integration';

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

	// Actions executed on the activation of the plugin
	public static function activate() {

		global $wpdb;

	    $the_page_title = "Graziashop Integration - CSV File Creation";

	    // Update/save the page title in the databse
	    update_option( 'graziashop_integration_page_title', $the_page_title, '', 'yes' );

	    $the_page = get_page_by_title( $the_page_title );

	    // Define a static page name
    	$the_page_name = 'generate-products-csv';

	    if( ! $the_page ) {

	        // Create post object
	        $_p 				  = array();
	        $_p['post_title'] 	  = $the_page_title;
	        $_p['post_name']	  = $the_page_name;
	        $_p['post_content']   = '';
	        $_p['post_status'] 	  = 'publish'; // Make the page viewable for everybody (so Graziashop can call it)
	        // $_p['post_status'] 	  = 'private'; // Make the page hidden for normal visitors
	        $_p['post_type'] 	  = 'page';
	        $_p['post_category']  = array(1); // Default category "Uncategorised"
	        $_p['comment_status'] = 'closed';
	        $_p['ping_status'] 	  = 'closed';

	        // Insert the post into the database
	        $the_page_id = wp_insert_post( $_p );

	    } else {

	        // The plugin may have been previously active and the page may just be trashed
	        $the_page_id = $the_page->ID;

	        // Make sure the page is not trashed
	        $the_page->post_status = 'publish';
	        // $the_page->post_status = 'private';
	        $the_page_id 		   = wp_update_post( $the_page );

	    }

	    // Update template ("generate-csv.php")
        if( $the_page_id && ! is_wp_error( $the_page_id ) ) {
            update_post_meta( $the_page_id, '_wp_page_template', 'generate-csv.php' );
        }

		// Store/update page settings in the databse
    	update_option( 'graziashop_integration_page_name', $the_page_name, '', 'yes' );
	    update_option( 'graziashop_integration_page_id', $the_page_id );

	    // Querry the database to get all products
		$products = new WP_Query(array(
	        'posts_per_page' => -1,
	        'post_type' 	 => 'product',
	        'orderby'   	 => 'ID'
	    ));

	    // Loop through the products
		while( $products->have_posts() ) {

			// Set current product as post
			$products->the_post();

		    // Make all products sellable on Graziashop
		    update_post_meta( get_the_ID(), 'product-graziashop', 1 );

		}

	}

}