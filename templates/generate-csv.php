<?php
/**
 * Template Name: Graziashop Integration Plugin - Generate CSV Template
 *
 */

	global $post;
	global $_wp_additional_image_sizes;

	header( 'Content-Type: text/html; charset=UTF-8' );

	if( function_exists('get_field') ) {

		// Check the template is applied on the right page
		if( $post->post_name === get_option('graziashop_integration_page_name') ) {

			$storeData = new stdClass();
			$storeName = get_option( 'blogname' );

			// Define CSV file name
			// Lower case the brand's name
		    $CSVfileName  = strtolower( $storeName );
		    // Make alphanumeric (removes all other characters)
		    $CSVfileName  = preg_replace( '/[^a-z0-9_\s-]/', '', $CSVfileName );
		    // CSVfileName up multiple dashes or whitespaces
		    $string 	  = preg_replace( '/[\s-]+/', ' ', $CSVfileName );
		    // Convert whitespaces and underscore to dash
		    $CSVfileName  = preg_replace( '/[\s_]/', '-', $CSVfileName );
		    // Add a string at the end of the name
			$CSVfileName .= '_products-data';

			$storeData->CSVfileName = $CSVfileName; // Name of the final CSV file generated by this script
			$storeData->language    = 'EN'; // Language
			$storeData->gender 		= 'female';
			$storeData->email       = get_option('admin_email'); // Email address that will receive notifications regarding the file creation status - Doesn't send any notification if the email is invalid

			// Set file version
			$fileVersion = 1;

			// Querry the database to get all products data
			$products = new WP_Query(array(
		        'posts_per_page' => -1,
		        'post_type' 	 => 'product',
		        'orderby'   	 => 'ID'
		    ));

			// Create list object with headers already entered
			$list = array(
				array(
					'version',
					$fileVersion,
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					''
				),
				array(
					'product_code',
					'group_code',
					'barcode',
					'mpn',
					'brand',
					'category',
					'title',
					'description',
					'material',
					'pattern',
					'care',
					'image_link',
					'additional_image_links',
					'gender',
					'age_group',
					'product_type',
					'availability',
					'sale_price',
					'sale_price_effective_date',
					'condition',
					'shipping_weight',
					'colour',
					'size',
					'fit',
					'price',
					'currency',
					'stock',
					'season',
					'language'
				)
			);

			// Create the object that will be used to create products lines
		    $data = new stdClass();

		    // Function to retrieve products metas (better support than the ACF "get_field" function)
		    function gi_get_field( $fieldName, $id ) {
		    	$productMeta = get_post_meta( $id, $fieldName );
		    	$productMeta = array_filter( $productMeta );

		    	if( ! empty( $productMeta ) ) {
		    		return $productMeta[0];
		    	} else {
		    		return false;
		    	}
		    }

			// Function to add a line in the array used to construct the CSV file
			function addLine() {

				// Get global variables
				global $list, $data;

				// Check if the data are in the right format
				if( is_object( $data ) ) {

					$productLine = array(
						$data->product_code,
						$data->group_code,
						$data->barcode,
						$data->mpn,
						$data->brand,
						$data->category,
						$data->title,
						$data->description,
						$data->material,
						$data->pattern,
						$data->care,
						$data->image_link,
						$data->additional_image_links,
						$data->gender,
						$data->age_group,
						$data->product_type,
						$data->availability,
						$data->sale_price,
						$data->sale_price_effective_date,
						$data->condition,
						$data->shipping_weight,
						$data->colour,
						$data->size,
						$data->fit,
						$data->price,
						$data->currency,
						$data->stock,
						$data->season,
						$data->language
					);

					// Push the line inside the array
					array_push( $list, $productLine );

				} else {

					die( 'An error occured, data type ($data variable) is incorrect: ' . gettype( $data ) );

				}

			}

			// Define the brand's name of a product
			function define_product_brand_name( $id = false ) {

				global $storeName;

				// Define the product ID if none has been defined
				if( ! $id ) {
					$id = get_the_ID();
				}

				// 1 -
				if( get_option('gi_brand_field_name') && get_option('gi_brand_field_name') === 'custom-brand' && get_option('gi_custom_brand_name') ) {
					return get_option('gi_custom_brand_name');
				}
				// 2 - Check if a brand's name has already been defined
				else if( gi_get_field( 'product-brand-name', $id ) ) {
					return gi_get_field( 'product-brand-name', $id );
				}
				// 3 - Check if a custom field has been defined
				else if( get_option('gi_brand_field_name') && gi_get_field( get_option('gi_brand_field_name'), $id ) && gettype( gi_get_field( get_option('gi_brand_field_name'), $id ) ) === 'string' && gi_get_field( get_option('gi_brand_field_name'), $id ) !== 'default' ) {
					return $brandNameValue;
				}
				// 4 - Else use the store's name as the brand's name
				else {
					return $storeName;
				}

			}

			// Loop through the products
			while( $products->have_posts() ) {

				// Set current product as post
				$products->the_post();

				// Check the products is published, so viewable for normal users
				if( $product->post->post_status === 'publish' ) {

					// Reset $data object
					$data->product_code 			 = '';
					$data->group_code 				 = '';
					$data->barcode 					 = '';
					$data->mpn 						 = '';
					$data->brand 					 = '';
					$data->category 				 = '';
					$data->title 					 = '';
					$data->description 				 = '';
					$data->material 				 = '';
					$data->pattern 					 = '';
					$data->care 					 = '';
					$data->image_link 				 = '';
					$data->additional_image_links    = '';
					$data->gender 					 = $storeData->gender;
					$data->age_group 				 = '';
					$data->product_type 			 = '';
					$data->availability 			 = '';
					$data->sale_price 				 = '';
					$data->sale_price_effective_date = '';
					$data->condition 				 = '';
					$data->shipping_weight 			 = '';
					$data->colour 					 = '';
					$data->size 					 = '';
					$data->fit 						 = '';
					$data->price 					 = '';
					$data->currency 				 = '';
					$data->stock 					 = '';
					$data->season 					 = '';
					$data->language 				 = $storeData->language;

					// Create product ID with 6 digits
					$productID = sprintf( '%06d', get_the_ID() );

				    $data->title = get_the_title();

					// Currency
					$data->currency = get_woocommerce_currency();

				    // Custom fields

				    // Optional custom data
					if( gi_get_field( 'product-fit', get_the_ID() ) )
						$data->fit = gi_get_field( 'product-fit', get_the_ID() );

				    if( gi_get_field( 'product-barcode', get_the_ID() ) )
				    	$data->barcode = gi_get_field( 'product-barcode', get_the_ID() );

					if( gi_get_field( 'product-material', get_the_ID() ) )
						$data->material = gi_get_field( 'product-material', get_the_ID() );

					if( gi_get_field( 'product-season', get_the_ID() ) )
						$data->season = get_field( 'product-season', get_the_ID() );

					if( gi_get_field( 'product-care', get_the_ID() ) )
						$data->care = gi_get_field( 'product-care', get_the_ID() );

					// Mandatory data
					// Note: the brand's name is defined as the last data as we need all product's data for it

					// Define category
					if( ! gi_get_field( 'product-category', get_the_ID() ) ) {
						$data->category = 'Clothing & Accessories';
					} else {
						$data->category = gi_get_field( 'product-category', get_the_ID() );
					}

					// Define colour
					if( ! gi_get_field( 'product-colour', get_the_ID() ) ) {
						$data->colour 	   = 'See in title';
						$descriptionColour = '';
					} else {
						$data->colour 	   = gi_get_field( 'product-colour', get_the_ID() );
						$descriptionColour = 'in ' . $data->colour;
					}

					// Define description
					if( ! gi_get_field( 'product-description', get_the_ID() ) ) {
						$descriptionBase   = $data->title . ' by ' . $data->brand . $descriptionColour;
						$data->description = $descriptionBase;
					} else {
						$descriptionBase   = gi_get_field( 'product-description', get_the_ID() );
						$data->description = $descriptionBase;
					}

					// Define brand's name
					$data->brand = define_product_brand_name( get_the_ID() );
					// define_product_brand_name( get_the_ID() );

					// Get the product image
					$data->image_link = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );

					// Loop through additional images and get their URLs
					$i = 0;

					$additional_image_links = '';

					foreach( $product->get_gallery_attachment_ids() as $imageID ) {
						$imageArray = wp_get_attachment_image_src( $imageID, 'full' );

						// Make sure not to include the primary image in the additional images by checking URLs
						if( $imageArray[0] !== $data->image_link ) {

							if( $i === 0 ) {
								$additional_image_links  = $imageArray[0];
							} else {
								$additional_image_links .= ',' . $imageArray[0];
							}

							$i++;

						}
					}

					$data->additional_image_links = $additional_image_links;

					// Test if the product has variations
				    if( method_exists( $product, 'get_available_variations' ) ) {

				    	$productVariations = $product->get_available_variations();

				        foreach( $productVariations as $productVariation ) {

				        	$variationObject = new WC_Product_Variation( $productVariation['variation_id'] );

				        	$data->price  	     			 = $variationObject->regular_price;
							$data->sale_price 	 			 = $variationObject->sale_price;
							$data->sale_price_effective_date = $variationObject->sale_price_dates_from;

					        $data->group_code = strtoupper( substr( get_the_title(), 0, 2 ) ) . $productID;

					        // Check attribute_pa_size exists and is set to TRUE
					        if( isset( $productVariation['attributes']['attribute_pa_size'] ) && $productVariation['attributes']['attribute_pa_size'] ) {

					        	$data->size = $productVariation['attributes']['attribute_pa_size'];

								// Add the size data to the description
					        	if( ! gi_get_field( 'product-description', get_the_ID() ) || gi_get_field( 'product-description', get_the_ID() ) === '' ) {
					        		$data->description = $descriptionBase . ', sized ' . $data->size;
					        	}

					        } else {
					        	$data->size = 'OS';
					        }

					        // Define product code
					        $data->product_code = $data->group_code . '_' . $data->size;

				        	if( gi_get_field( 'product-graziashop', get_the_ID() ) ) {
				        	// if( gi_get_field( 'product-graziashop', get_the_ID() ) && ctype_digit( $productVariation['max_qty'] ) ) {
					        	$data->stock = $productVariation['max_qty'];
					        } else {
					        	$data->stock = 0;
					        }

					        // Add the line to the array
					        addLine();

				        }

				    } else {

				    	$data->price = get_post_meta( get_the_ID(), '_regular_price', true );

				    	// Test if a sale price exists
				    	$compareSalePrice = get_post_meta( get_the_ID(), '_price', true );

				    	if( $compareSalePrice !== $data->price ) {
				    		$data->sale_price 				 = get_post_meta( get_the_ID(), '_price', true );
							$data->sale_price_effective_date = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
				    	}

			        	$data->product_code = strtoupper( substr( get_the_title(), 0, 2 ) ) . $productID;
				        $data->size	        = 'OS';

			        	if( gi_get_field( 'product-graziashop', get_the_ID() ) && ctype_digit( gi_get_field( '_stock', get_the_ID() ) ) ) {
			        		$data->stock = gi_get_field( '_stock', get_the_ID() );
			        	} else {
							$data->stock = 0;
			        	}

			        	// Add the line to the array
			        	addLine();

				    }

				}

			}

			// Create an empty CSV file
			$fileStream = fopen( $storeData->CSVfileName . '.csv', 'w' );

			// Fill the CSV file with each line
			foreach( $list as $line ) {

				// fputcsv( $fileStream, explode( ',', $line ) );
				fputcsv( $fileStream, $line );

				// echo implode( ',', $line );
				// echo '<br/><br/>';

			}

			// Close CSV file
			fclose( $fileStream );

			// FOR LATER USE

			// // Connect to the FTP server
			// $fileStream = fopen( $storeData->CSVfileName . '.csv', 'w' );

			// $ftp_server     = 'ftp_server_address';
			// $ftp_connection = ftp_connect( $ftp_server )
			// 	or die( 'Could not connect to ' . $ftp_server );

			// $ftp_login      = ftp_login( $ftp_connection, 'username', 'password' );

			// // Enable passive way
			// ftp_pasv( $ftp_connection, true );

			// // Upload the CSV file
			// ftp_put( $ftp_connection, '/public_html/csv/' . $storeData->CSVfileName . '.csv', $storeData->CSVfileName . '.csv', FTP_ASCII );

			// // Close the FTP connection
			// ftp_close( $ftp_connection );

			// END FOR LATER USE

			// // Check the email address for notifications is correct
			// if( filter_var( $storeData->email, FILTER_VALIDATE_EMAIL ) ) {

			// 	// Send notification e-mail
			// 	$email_message = 'CSV file successfully generated at ' . date( 'h:i:s - d/m/Y' );
			// 	$email_message = wordwrap( $email_message, 70 );

			// 	mail( $storeData->email, get_option( 'blogname' ) . ' - CSV File successfully generated', $email_message );

			// }

			echo 'File successfully generated on the server.
				</br>You can find it at: ' . get_site_url() . '/'. $storeData->CSVfileName . '.csv
				</br>Or <a href="' . get_site_url() . '/'. $storeData->CSVfileName . '.csv">click here</a> to download it.';

			exit;

		}

	} else {

		die('The plugin "Advanced Custom Fields" doesn\'t seem to be installed on your website. You need to install it in order to use the plugin "Graziashop Integration": <a href="https://wordpress.org/plugins/advanced-custom-fields/installation/" target="_blank">Advanced Custom Fields</a>.');

	}

?>