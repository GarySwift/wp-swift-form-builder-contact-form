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

        # Shortcode for rendering the new user registration form
        add_shortcode( 'contact-form', array( $this, 'render_form' ) );

        # Handle POST request form login form
        // add_action( 'init', array( $this, 'process_form' ) );

        add_action( 'admin_menu', array($this, 'wp_swift_contact_form_admin_menu') );

        # Register ACF field groups that will appear on the options pages
        add_action( 'admin_menu', array($this, 'acf_add_local_field_group_contact_form') );

        # Register the inputs
        add_action( 'admin_init', array($this, 'wp_swift_form_builder_contact_form_settings_init') );   

    }


    /*
     * The ACF field group for 'Contact Form'
     */ 
    public function acf_add_local_field_group_contact_form() {
        include "acf-field-groups/_acf-field-group-contact-form.php";
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
            'title' => 'Contact Form',
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




    public function wp_swift_form_builder_contact_form_add_admin_menu(  ) { 

        $show_form_builder = class_exists('WP_Swift_Admin_Menu');
        if (!$show_form_builder) {
            $options_page = add_options_page( 
                'Form Builder Contact Page Configuration', 
                'Form Builder: Contact Page',
                'manage_options',
                'wp-swift-form-builder-contact-form-settings-menu',
                array($this, 'wp_swift_form_builder_contact_form_options_page') 
            );  
        }

    }
    
    public function wp_swift_form_builder_contact_form_settings_init(  ) { 

        register_setting( 'contact-form', 'wp_swift_form_builder_contact_form_settings' );

        add_settings_section(
            'wp_swift_form_builder_contact_form_plugin_page_section', 
            __( 'Set your preferences for the Contact Form here', 'wp-swift-form-builder-contact-form' ), 
            array($this, 'wp_swift_form_builder_contact_form_settings_section_callback'), 
            'contact-form'
        );

        add_settings_field( 
            'wp_swift_form_builder_contact_form_checkbox_first_last_name', 
            __( 'Use first and last names', 'wp-swift-form-builder-contact-form' ), 
            array($this, 'wp_swift_form_builder_contact_form_checkbox_first_last_name_render'), 
            'contact-form', 
            'wp_swift_form_builder_contact_form_plugin_page_section' 
        );

        add_settings_field( 
            'wp_swift_form_builder_contact_form_checkbox_phone', 
            __( 'Use telephone', 'wp-swift-form-builder-contact-form' ), 
            array($this, 'wp_swift_form_builder_contact_form_checkbox_phone_render'), 
            'contact-form', 
            'wp_swift_form_builder_contact_form_plugin_page_section' 
        );
    }


    public function wp_swift_form_builder_contact_form_checkbox_first_last_name_render(  ) { 

        $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
        ?>
        <input type="checkbox" name="wp_swift_form_builder_contact_form_settings[wp_swift_form_builder_contact_form_checkbox_first_last_name]" value="1" <?php 
        if (isset($options['wp_swift_form_builder_contact_form_checkbox_first_last_name'])) {
             checked( $options['wp_swift_form_builder_contact_form_checkbox_first_last_name'], 1 );
         } ?>>
        <?php

    }


    public function wp_swift_form_builder_contact_form_checkbox_phone_render(  ) { 

        $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
        ?>
        <input type="checkbox" name="wp_swift_form_builder_contact_form_settings[wp_swift_form_builder_contact_form_checkbox_phone]" value="1" <?php 
        if (isset($options['wp_swift_form_builder_contact_form_checkbox_phone'])) {
             checked( $options['wp_swift_form_builder_contact_form_checkbox_phone'], 1 );
         } ?>>
        <?php

    }

    public function wp_swift_form_builder_contact_form_settings_section_callback(  ) { 

        echo __( 'Available options:', 'wp-swift-form-builder-contact-form' );

    }

    public function wp_swift_form_builder_contact_form_options_page(  ) { 
        $show_form_builder = class_exists('WP_Swift_Admin_Menu');
        if (!$show_form_builder): ?>
            <div id="form-builder-wrap" class="wrap">
                <h2>WP Swift Form Builder Contact Form</h2>
                <form action='options.php' method='post'>

                    <?php
                        settings_fields( 'wp_swift_form_builder_contact_form_plugin_page' );
                        do_settings_sections( 'wp_swift_form_builder_contact_form_plugin_page' );
                        submit_button();
                    ?>

                </form>
            </div>
        <?php 
        endif;
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
