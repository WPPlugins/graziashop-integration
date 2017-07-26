jQuery( document ).ready(function() {

    jQuery('#post').submit(function() {

    	// Check that:
    	// - the post type is "product"
    	// - the checkbox "Sell on Graziashop" is checked
    	// - the SKU field isn't empty or filled with whitespaces
    	if( typenow === 'product' && jQuery('.acf-field[data-name="product-graziashop"] input:checkbox').attr('checked') === 'checked' && jQuery.trim( jQuery('#_sku').val() ) === '' ) {

    		// Remove previous error messages
    		jQuery('.gi_invalid_field_message').remove();

    		// Append error message
    		jQuery('.woocommerce_options_panel .options_group:first-child').append('<p class="gi_invalid_field_message">The SKU field is mandatory for the integration with Graziashop and must be filled with a unique identifier of your choice. Please provide one.</p>');

    		// Add informational class
    		jQuery('._sku_field').addClass('gi_invalid_field');

    		// Scroll to the invalid field
    		jQuery('html, body').animate({
		        scrollTop: jQuery('._sku_field').offset().top - jQuery( window ).height() / 2
		    }, 300);

    		// Remove informational class when clicking on the field
    		jQuery('._sku_field').on('click', function() {
    			// Remove informational class
    			jQuery( this ).removeClass('gi_invalid_field');
    			// Remove error messages
    			jQuery('.gi_invalid_field_message').remove();
    		});

    		// Prevent the product from being saved
        	return false;

    	}
        // Or let the saving process continue
        else {
    		return true;
    	}

    });


    jQuery('#gi-brand-field-name').on('change', function( e ) {
        if( jQuery( this ).val() === 'custom-brand' ) {
            jQuery('#gi-input-text').addClass('active');
        } else {
            jQuery('#gi-input-text').removeClass('active');
        }
    });

});