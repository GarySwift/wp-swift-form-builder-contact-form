<?php
/*
Plugin Name:       WP Swift: Form Builder Contact Form Add-On
Description:       Generate a contact form. Requires plugin 'WP Swift: Form Builder' to be installed.
Version:           1.0.1
Author:            Gary Swift
License:           GPL-2.0+
Text Domain:       wp-swift-form-builder-contact-form
*/

include "_form-data.php";
include "_class-contact-form.php";
include "_render-forms-after-content.php";
require_once '_build-form-array.php';
require_once '_functions.php';

class WP_Swift_Contact_Form_Plugin  {

    private $text_domain = 'wp-swift-form-builder-contact-form';

    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {

        # Shortcode for rendering the new user registration form
        add_shortcode( 'contact-form', array( $this, 'render_contact_form' ) );

        add_shortcode( 'form', array( $this, 'render_form' ) );

        # Handle POST request form login form
        // add_action( 'init', array( $this, 'process_form' ) );

        # Register the acf_add_options_sub_page    
        add_action( 'admin_menu', array($this, 'wp_swift_contact_form_admin_menu') );

        # Register ACF field groups that will appear on the options pages
        add_action( 'init', array($this, 'acf_add_local_field_group_contact_form') );

        # Register the inputs
        // add_action( 'admin_init', array($this, 'wp_swift_form_builder_contact_form_settings_init') );   

    }


    /*
     * The ACF field group for 'Contact Form'
     */ 
    public function acf_add_local_field_group_contact_form() {
        include "acf-field-groups/_acf-field-group-contact-form.php";
        include "acf-field-groups/_acf-field-group-form-inputs.php";
        include "acf-field-groups/_acf-field-group-options-page-settings.php";
        include "acf-field-groups/_acf-field-group-contact-page-input-settings.php";
    }

    /*
     * This determines the location the menu links
     * They are listed under Settings unless the other plugin 'wp_swift_admin_menu' is activated
     */
    public function get_parent_slug() {
        if ( get_option( 'wp_swift_admin_menu' ) ) {
            return get_option( 'wp_swift_admin_menu' );
        }
        else {
            return 'options-general.php';
        }
    }

    /*
     * 
     * Create the menu pages that show in the side bar.
     *
     * The top level page is uses the standard WordPress API for showing menus.
     * The submenus use Advanced Custom Fields API to register pages
     */
    public function wp_swift_contact_form_admin_menu() {
    
        acf_add_options_sub_page(array(
            'title' => 'Forms',
            'slug' => 'contact_form',
            'parent' => $this->get_parent_slug(),
        ));
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
            $form_builder->process_form();
        }        
    }

    /**
     * A shortcode for rendering the contact form.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_contact_form( $attributes = array(), $content = null ) {

        $form_builder = wp_swift_get_contact_form($attributes);
        // Render the login form using an external template
        return $this->get_template_html( 'contact_form', $attributes, $form_builder );

    }

    /**
     * A shortcode for rendering the contact form.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_form( $attributes = array(), $content = null ) {

        $form_builder = wp_swift_get_generic_form($attributes);
        // Render the login form using an external template
        return $this->get_template_html( 'contact_form', $attributes, $form_builder, $content );

    }      
 
    /**
     * Renders the contents of the given template to a string and returns it.
     *
     * @param string $template_name The name of the template to render (without .php)
     * @param array  $attributes    The PHP variables for the template
     *
     * @return string               The contents of the template.
     */
    private function get_template_html( $template_name, $attributes = null, $form_builder, $content = null ) {
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
                   
}
// Initialize the plugin
$wp_swift_contact_form_plugin = new WP_Swift_Contact_Form_Plugin();