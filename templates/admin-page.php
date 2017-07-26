<?php
	global $wpdb;

	// Get an array of names of the custom fields present for all products

	// Querry the database to get the first 30 products
	$products = get_posts(array(
        'posts_per_page' => 2,
        'post_type' 	 => 'product',
        'orderby'   	 => 'ID'
    ));

	// Initialise the array which will contain all custom fields names
    $custom_fields_names = [];

    if( $products ) {
    	// Loop through the products
	    foreach( $products as $product ) {

	    	if( empty( $custom_fields_names ) ) {
	    		$custom_fields_names = get_post_custom( $product->ID );
	    		$custom_fields_names = array_keys( $custom_fields_names );
	    	}
	    	// Else get the union of $current_custom_fields_names and $custom_fields_names
	    	else {

	    		$current_custom_fields_names = get_post_custom( $product->ID );
	    		$current_custom_fields_names = array_keys( $current_custom_fields_names );

	    		// Get the custom fields present in both arrays
	    		$custom_fields_names = array_intersect( $custom_fields_names, $current_custom_fields_names );

	    	}

		}
    }
?>

<div class="gi_csv wrap pbs">

	<header class="gi-admin-section">
		<h5 class="subtitle">by <a href="http://www.republik-media.com/" target="_blank">Republik Media</a></h5>
		<a id="gi-button-csv" href="<?php echo get_option('graziashop_integration_page_name'); ?>" target="_blank" class="button-primary">Generate CSV</a>
	</header>

	<div class="gi-text">
		<p><strong><i>Brand is a Mandatory Field for Graziashop and not a native field within WooCommerce. This Application allows Mono-Brand Merchants and Multi-Brand Merchants to set Brand information.</i></strong></p>
		<p><strong>Mono-Brand (Single Brand) Merchant</strong>, please select <strong>set custom brand</strong> from the drop-down menu and input your <strong>brands name</strong> and save changes.</p>
		<p><strong>Multi-Brand Merchant</strong>, please select the <strong>custom field</strong> that contains your brand information and save changes.</p>
	</div>

	<table class="widefat posts pbs-settings" cellspacing="0">
		<tbody>
			<tr valign="top">
				<td>
					<form name="frm" method="post" action="options.php">

						<?php settings_fields( 'baw-settings-group' ); ?>

						<table class="inner-setings">
							<tr>
								<td style="line-height: 27px;">Brand's custom field</td>
								<td>

									<?php if( count( $custom_fields_names ) < 1 ): ?>

									<span style="line-height: 27px;">No custom fields found.</span>

									<?php else: ?>

									<select id="gi-brand-field-name" class="brand_field_name" name="gi_brand_field_name">
										<option value="default">Graziashop Integration plugin custom field</option>
										<option value="custom-brand" <?php if( get_option('gi_brand_field_name') === 'custom-brand' ) { echo 'selected'; } ?>>Set custom brand</option>
										<?php foreach( $custom_fields_names as $custom_fields_name ): ?>
										<option value="<?php echo $custom_fields_name; ?>" <?php if( $custom_fields_name === get_option('gi_brand_field_name') ) { echo 'selected'; } ?>><?php echo $custom_fields_name; ?></option>
										<?php endforeach; ?>
									</select>

									<?php endif; ?>
								</td>
								<td style="line-height: 27px;"><span>Select the custom fields in the list to be used as the "Brand" field.</span></td>
								<td style="line-height: 27px;">
									<span class="submit">
										<input type="submit" class="button-primary" value="Save Changes">
									</span>
								</td>
							</tr>
						  	<tr>
						  		<td style="line-height: 27px;"></td>
								<td style="line-height: 27px;"><input id="gi-input-text" class="<?php if( get_option('gi_brand_field_name') === 'custom-brand' ) { echo 'active'; } ?>" type="text" placeholder="Brand's name" name="gi_custom_brand_name" value="<?php if( get_option('gi_custom_brand_name') ) { echo get_option('gi_custom_brand_name'); } ?>"></td>
							</tr>
						</table>

					</form>
				</td>
			</tr>
		</tbody>
	</table>

</div>