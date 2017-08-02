<?php
class WP_Swift_Contact_Form_Plugin_Admin_Interface  {

    private $text_domain = 'wp-swift-form-builder-contact-form-admin-interface';

    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {

        # Shortcode for rendering the new user registration form
        // add_shortcode( 'booking-form', array( $this, 'render_booking_form' ) );

        # Handle POST request form login form
        // add_action( 'init', array( $this, 'process_form' ) );

        # Register the acf_add_options_sub_page    
        add_action( 'admin_menu', array($this, 'wp_swift_contact_form_admin_menu') );

        # Register ACF field groups that will appear on the options pages
        add_action( 'init', array($this, 'acf_add_local_field_group_contact_form') );

        # Register the inputs
        add_action( 'admin_init', array($this, 'wp_swift_form_builder_contact_form_settings_init') );   

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

// Initialize the class
$wp_swift_contact_form_plugin_admin_interface = new WP_Swift_Contact_Form_Plugin_Admin_Interface();