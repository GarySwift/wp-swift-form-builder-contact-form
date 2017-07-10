<?php
/*
Plugin Name: 	WP Swift: Email Templates
Description: 	Send emails in a html wrapper
Version: 		1.0
Author: 		Gary Swift
Author URI: 	https://github.com/GarySwift
Text Domain:	wp-swift-email-templates
*/
 
function wp_swift_wrap_email($message) {
    $settings = array(
        'from_name'         => get_bloginfo('name'),
        'from_email'        => get_bloginfo('admin_email'),
        'template'          => 'boxed',
        'body_bg'           => '#e3e3e3',
        'body_size'         => '680',
        'footer_text'       => '&copy;'.date('Y').' ' .get_bloginfo('name'),
        'footer_aligment'   => 'center',
        'footer_bg'         => '#eee',
        'footer_text_size'  => '12',
        'footer_text_color' => '#777',
        'footer_powered_by' => 'off',
        'header_aligment'   => 'center',
        'header_bg'         => '#525050',
        'header_text_size'  => '30',
        'header_text_color' => '#f1f1f1',
        'email_body_bg'     => '#fafafa',
        'body_text_size'    => '14',
        'body_text_color'   => '#888',
    );
    ob_start();
    include( 'templates/partials/header.php' );
    echo $message;
    include( 'templates/partials/footer.php' );
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}