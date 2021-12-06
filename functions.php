<?php
 
add_action( 'wp_enqueue_scripts', 'enqueue_parent_theme_style');
function enqueue_parent_theme_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
} 
 
//add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
//function my_theme_enqueue_styles() {
//
//    $parent_style = 'parent-style'; // define parent style here.
// 
//    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
//    wp_enqueue_style( 'child-style',
//        get_stylesheet_directory_uri() . '/style.css',
//        array( $parent_style ),
//        wp_get_theme()->get('Version')
//    );
//}

wp_enqueue_script( 'ionicons5', 'https://unpkg.com/ionicons@5.0.0/dist/ionicons.js', array('javascript'), '5.0.0', true );

add_filter( 'upload_mimes', 'my_myme_types', 1, 1 );
function my_myme_types( $mime_types ) {
  $mime_types['svg'] = 'image/svg+xml';     // Adding .svg extension
  $mime_types['pdf'] = 'application/json'; // Adding .pdf extension
  $mime_types['eps'] = 'application/json'; // Adding .eps extension
  $mime_types['ai'] = 'application/json'; // Adding .ai extension
  $mime_types['zip'] = 'application/json'; // Adding .zip extension
  
  return $mime_types;
}

// Add to list of permitted mime types
function my_prefix_pewc_get_permitted_mimes( $permitted_mimes ) {
 // Add PDF to the list of permitted mime types
 $permitted_mimes['pdf'] = "application/pdf";
$permitted_mimes['eps'] = "application/eps";
$permitted_mimes['ai'] = "application/ai";
$permitted_mimes['svg'] = "application/svg";
$permitted_mimes['zip'] = "application/zip";
 // Remove a mime type - uncomment the line below if you wish to prevent JPGs from being uploaded
 unset( $permitted_mimes['gif'] );
 return $permitted_mimes;
}
add_filter( 'pewc_permitted_mimes', 'my_prefix_pewc_get_permitted_mimes' );

// fix price overrides for p
if(!function_exists('woo_discount_rules_has_price_override_method')){
    function woo_discount_rules_has_price_override_method($has_price_override, $product, $on_apply_discount){
        if($on_apply_discount == 'on_apply_discount') $has_price_override = true;
        return $has_price_override;
    }
}
add_filter('woo_discount_rules_has_price_override', 'woo_discount_rules_has_price_override_method', 10, 3);

function woo_discount_rules_price_rule_final_amount_applied_method($discountedPrice, $price, $discount, $additionalDetails, $product, $product_page){
    if($discountedPrice < 0) $discountedPrice = 0;
    $total_price = $product->get_price();
    $addon_price = 0;
    if($price != $total_price){
        $addon_price = $total_price - $price;
    }
    $discountedPrice = $discountedPrice + $addon_price;

    return $discountedPrice;
}

add_filter('woo_discount_rules_price_rule_final_amount_applied', 'woo_discount_rules_price_rule_final_amount_applied_method', 10, 6);

remove_action( 'woocommerce_before_calculate_totals', 'pewc_wc_calculate_total', 10, 1 );
remove_filter( 'woocommerce_cart_item_price', 'pewc_minicart_item_price', 10, 3 );
remove_action( 'woocommerce_cart_calculate_fees', 'pewc_cart_calculate_fees', 10 );
function prefix_remove_actions() {
	remove_action( 'woocommerce_before_calculate_totals', 'pewc_wc_calculate_total', 10, 1 );
	remove_filter( 'woocommerce_cart_item_price', 'pewc_minicart_item_price', 10, 3 );
	remove_action( 'woocommerce_cart_calculate_fees', 'pewc_cart_calculate_fees', 10 );
}
add_action( 'init', 'prefix_remove_actions' );