<?php
/*
Plugin Name:       WP Swift: Form Builder Contact Form Add-On
Description:       Generate a contact form. Requires plugin 'WP Swift: Form Builder' to be installed.
Version:           1.0.1
Author:            Gary Swift
License:           GPL-2.0+
Text Domain:       wp-swift-form-builder-contact-form
*/

include "_class-contact-form.php";

class WP_Swift_Contact_Form_Plugin  {

    private $text_domain = 'wp-swift-form-builder-contact-form';

    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
        /*
         * Handle forms
         */

        # Shortcode for rendering the new user registration form
        add_shortcode( 'contact-form', array( $this, 'render_form' ) );

        # Handle POST request form login form
        add_action( 'init', array( $this, 'process_form' ) );
    }

    /*
     * Handles the user login (if successful)
     *
     * Test for a POST request from the login in form before the WordPress header is called
     * This will redirect if the login is successful. It won't do anything if failed.
     * Failed login will be handle in the render login function
     */
    public function process_form() {
        $form_builder = $this->get_form_builder();
        if( $form_builder!== null && isset($_POST[ $form_builder->get_submit_button_name() ]) ){ 
            $form_builder->process_form( );  
        }        
    }

    /**
     * Plugin activation hook.
     *
     * Creates all WordPress pages needed by the plugin.
     */
    public static function plugin_activated() {

    }


    /**
     * A shortcode for rendering the contact form.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_form( $attributes, $content = null ) {

            $form_builder = $this->get_form_builder();

            // echo "render_form<pre>"; var_dump($form_builder); echo "</pre>";

            if($form_builder!== null && isset($_POST[ $form_builder->get_submit_button_name() ])){ //check if form was submitted

                $form_builder->process_form(); 
                // echo "<pre>"; var_dump($_POST); echo "</pre>";
            }
            // Render the login form using an external template
            return $this->get_template_html( 'contact_form', $attributes, $form_builder );
    }  

    /**
     * Renders the contents of the given template to a string and returns it.
     *
     * @param string $template_name The name of the template to render (without .php)
     * @param array  $attributes    The PHP variables for the template
     *
     * @return string               The contents of the template.
     */
    private function get_template_html( $template_name, $attributes = null, $form_builder ) {
        if ( ! $attributes ) {
            $attributes = array();
        }
     
        ob_start();
     
        do_action( 'personalize_login_before_' . $template_name );
     
        require( 'templates/' . $template_name . '.php');
     
        do_action( 'personalize_login_after_' . $template_name );
     
        $html = ob_get_contents();
        ob_end_clean();
     
        return $html;
    } 
    
    private function get_form_builder( $attributes=array() )  {
        return get_contact_form( $attributes ); 
    }             
}

function get_contact_form( $attributes=array() ) {
    $form_builder = null;
    if (class_exists('WP_Swift_Form_Builder_Contact_Form')) {
        $form_builder = new WP_Swift_Form_Builder_Contact_Form($attributes);    
    }
    return $form_builder;        
}
// Initialize the plugin
$wp_swift_contact_form_plugin = new WP_Swift_Contact_Form_Plugin();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'WP_Swift_Contact_Form_Plugin', 'plugin_activated' ) );
