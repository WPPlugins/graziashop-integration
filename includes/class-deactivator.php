<?php

/**
 * Fired during plugin deactivation
 *
 * @since      1.2.5
 *
 * @package    graziashop-integration
 * @subpackage graziashop-integration/includes
 */

class GI_Deactivator {

	// Actions on deactivation of the plugin
	public static function deactivate() {

		// Trash the page that generates the CSV file containing products data
		global $wpdb;

		$the_page_title = get_option('graziashop_integration_page_title');
		$the_page_name  = get_option('graziashop_integration_page_name');
		$the_page_id 	= get_option('graziashop_integration_page_id');

		if( $the_page_id ) {
		    wp_delete_post( $the_page_id ); // this will trash, not delete
		}

		// Delete saved options
		delete_option('graziashop_integration_page_title');
		delete_option('graziashop_integration_page_name');
		delete_option('graziashop_integration_page_id');

	}

}
