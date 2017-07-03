<?php
/*
 * Check if WP_Swift_Form_Builder_Plugin exists.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'wp-swift-form-builder/form-builder.php' ) )  {

    include_once( plugin_dir_path( __DIR__ ) . 'wp-swift-form-builder/form-builder.php' );

    if(class_exists('WP_Swift_Form_Builder_Plugin')) {

        /*
         * Declare a new class that extends the form builder
         * 
         * @class       WP_Swift_Form_Builder_Login_Form
         * @extends     WP_Swift_Form_Builder_Plugin
         *
         */
        class WP_Swift_Form_Builder_Generic_Form extends WP_Swift_Form_Builder_Plugin {

            private $attributes = null;

            /*
             * Initializes the plugin.
             */
            public function __construct() { 
                $args = $this->get_form_args();
                parent::__construct( false, $this->get_form_data(), get_the_id(), $args );
            }    
 
            /*
             * Get the form settings
             *
             * @return array    form args
             */
            private function get_form_args() {
                $form_builder_args = array("show_mail_receipt"=>false, "option" => "");
                return $form_builder_args;
            }
            /*
             * Get the form settings array
             *
             * @return array    form data array
             */
            private function get_form_data() {
                $form_data = get_book_inputs(get_the_id());
                return $form_data;
            }
        }
    }
}