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
        class WP_Swift_Form_Builder_Contact_Form_Plugin extends WP_Swift_Form_Builder_Plugin {

            private $attributes = null;

            /*
             * Initializes the plugin.
             */
            public function __construct() { 
                parent::__construct( false, $this->get_form_data(), get_the_id(), $this->get_form_args() );
            }    
            /*
             * Initializes the plugin.
             */
            // public function __construct() {
            //     /*
            //      * Check if WP_Swift_Form_Builder_Plugin exists. Abort if not.
            //      */
            //     include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            //     if ( is_plugin_active( 'wp-swift-form-builder/form-builder.php' ) )  {
            //         add_shortcode( 'wp-swift-contact-form', array( $this, 'render_contact_form' ) ); 
            //         /*
            //          * Inputs
            //          */
            //         add_action( 'admin_menu', array($this, 'wp_swift_form_builder_contact_form_add_admin_menu'), 21 );
            //         add_action( 'admin_init', array($this, 'wp_swift_form_builder_contact_form_settings_init') );
            //         // add_action( 'admin_menu', array($this, 'wp_swift_form_builder_add_admin_menu') );
            //         // add_action( 'admin_init', array($this, 'wp_swift_form_builder_settings_init') );
            //     }
            //     else {
            //         add_action( 'admin_init', array($this, 'plugin_deactivate') );
            //         add_action( 'admin_notices', array($this, 'plugin_activate_fail_admin_notice') );
            //     }
            // }

            /**
             * Plugin activation hook.
             *
             * Creates the WordPress page needed by the plugin.
             */
            public function plugin_activated() {
                /*
                 * This will create the WordPress page needed to use this plugin
                 * Shortcode will also be added to content 
                 */
                $page_definitions = array(
                    'contact-page' => array(
                        'title' => __( 'Contact Us', 'wp-swift-form-builder-contact-form' ),
                        'content' => '<p>Please use the form below to submit any questions you may have.</p>[wp-swift-contact-form]'
                    ),
                );
                $post_ids = array();
                /*
                 * Create a new page for each page in $page_definitions
                 */
                foreach ( $page_definitions as $slug => $page ) {
                    // Check that the page doesn't exist already
                    $query = new WP_Query( 'pagename=' . $slug );
                    if ( ! $query->have_posts() ) {
                        // Add the page using the data from the array above
                        $post_ids[] = wp_insert_post(
                            array(
                                'post_content'   => $page['content'],
                                'post_name'      => $slug,
                                'post_title'     => $page['title'],
                                'post_status'    => 'publish',
                                'post_type'      => 'page',
                                'ping_status'    => 'closed',
                                'comment_status' => 'closed',
                            )
                        );
                    }
                }
                # Create transient data so we can show admin notice
                set_transient( 'contact-form-plugin-activate-notice', $post_ids, 5 );
            } 

            /*
             * Plugin can deactivate itself
             */
            public function plugin_deactivate() {
                deactivate_plugins( plugin_basename( __FILE__ ) );
            }
            /*
             * An install failed admin notice
             */
            public function plugin_activate_fail_admin_notice() {
                $class = 'notice notice-error';
                $message = __( '<strong>WP Swift: Form Builder Contact Form Add-On</strong> requires the plugin <strong>WP Swift: Form Builder</strong> to be installed. Please download this plugin from <a href="https://github.com/GarySwift/wp-swift-form-builder" target="_blank"><b>here</b></a>.', 'wp-swift-form-builder-contact-form' );
                printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
                if ( isset( $_GET['activate'] ) ){
                    unset( $_GET['activate'] );
                } 
            }
            /*
             * Enqueue the Javascript
             */
            public function enqueue_javascript () {
                wp_enqueue_script( $handle='wp-swift-form-builder-contact-form', $src=plugins_url( '/assets/javascript/wp-swift-form-builder-contact-form.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
            }
            /**
             * A shortcode for rendering the contact form.
             *
             * @param  array   $attributes  Shortcode attributes.
             * @param  string  $content     The text content for shortcode. Not used.
             *
             * @return string  The shortcode output
             */
            public function render_contact_form( $attributes, $content = null ) {
                $Form_Builder = null;
                if ( class_exists('WP_Swift_Form_Builder_Plugin')) {
                    $submit_button_name = "submit-request-form";
                    $form_data = $this->get_form_data();
                    $form_builder_args = array(
                        "form_name" => "request-form",
                        "submit_button_name" => $submit_button_name,
                        "button_text" => "Submit Query",
                        "show_mail_receipt" => true,
                    );
                    $Form_Builder = new WP_Swift_Form_Builder_Plugin($attributes, $form_data, get_the_ID(), $form_builder_args, $attributes, false);
                }
                else {
                    // return __( '<h4>Please install plugin WP Swift: Form Builder</h4>', 'wp-swift-form-builder-contact-form' );
                    add_action( 'admin_init', array($this, 'plugin_deactivate') );
                    add_action( 'admin_notices', array($this, 'plugin_activate_fail_admin_notice') );
                    return null;
                }
                // Render the contact form using an external template
                return $this->get_template_html( 'contact_form', $attributes, $Form_Builder );
            }

            /**
             * Renders the contents of the given template to a string and returns it.
             *
             * @param string $template_name The name of the template to render (without .php)
             * @param array  $attributes    The PHP variables for the template
             *
             * @return string               The contents of the template.
             */
            private function get_template_html( $template_name, $attributes = null, $Form_Builder = null) {
                if ( ! $attributes ) {
                    $attributes = array();
                }
             
                ob_start();
                do_action( 'custom_contact_before_' . $template_name );//To do

                // If POST
                if(isset($_POST['submit-request-form'])){ //check if the form was submitted

                    $Form_Builder->validate_form();
                    if ($Form_Builder->get_error_count()==0) {
                        echo $this->process_successful_form($Form_Builder, $_POST);
                    }
                }   

                /*
                 * Look for form
                 */
                if ($Form_Builder != null ) {
                    $Form_Builder->acf_build_form();
                }
             
                do_action( 'custom_contact_after_' . $template_name );
             
                $html = ob_get_contents();
                ob_end_clean();
             
                return $html;
            }

        /*
         * The form has been validated so we can send the emails.
         * This will get the to and from email recipients, build the html message and send the emails.
         * It then returns a html string that tells the user what has happened.
         *
         * @param class $Form_Builder   The name of the template to render (without .php)
         * @param array  $post          The global $_POST variable cast as a the local $post variable
         *
         * @return string               The html success message
         */
        private function process_successful_form($Form_Builder, $post, $post_id=false) {
            /*
             * Variables
             */
            $send_email=true;//Debug variable. If false, emails will not be sent
            $date = ' - '.date("Y-m-d H:i:s").' GMT';
            $post_id_or_acf_option= '';//We can specify if it is an option field or use a post_id (https://www.advancedcustomfields.com/add-ons/options-page/)
            $mail_receipt=false;//auto-reponse flag
            if(isset($post['mail-receipt'])){
                $mail_receipt=true;//Send an auto-response to user
            }

            /*
             * These are the default form settings
             */
            // If a debug email is set in ACF, send the email there instead of the admin email
            $to = get_option('admin_email');
            // Set reponse subject for email
            $response_subject = "New Enquiry".$date;
            // Start the reponse message for the email
            $response_message = '<p>A website user has made the following enquiry.</p>';
            //Set auto_response_message
            $auto_response_message = 'Thank you very much for your enquiry. A representative will be contacting you shortly.';
            // Set the response that is set back to the browser
            $browser_output_header = 'Hold Tight, We\'ll Get Back To You';
            // The auto-response subject
            $auto_response_subject='Auto-response (no-reply)';

            /*
             * Now, we can override the default settings if they are set
             */
            // If a debug email is set in ACF, send the email there instead of the admin email
            if (get_field('debug_email', $post_id_or_acf_option)) {
                $to = get_field('debug_email', $post_id_or_acf_option); 
            }
            // Set reponse subject for email
            if (get_field('response_subject', $post_id_or_acf_option)) {
                $response_subject = get_field('response_subject', $post_id_or_acf_option).$date; 
            }
            // Start the reponse message for the email
            if (get_field('response_message', $post_id_or_acf_option)) {
                $response_message = '<p>'.get_field('response_message', $post_id_or_acf_option).'.</p>';
            }
            //Set auto_response_message
            if (get_field('auto_response_message', $post_id_or_acf_option)) {
                $auto_response_message = get_field('auto_response_message', $post_id_or_acf_option);
            }
            // Set the response that is set back to the browser
            if (get_field('browser_output_header', $post_id_or_acf_option)) {
                $browser_output_header = get_field('browser_output_header', $post_id_or_acf_option);
            } 
            // The auto-response subject
            if( get_field('auto_response_subject') ) {
                $auto_response_subject = get_field('auto_response_subject');
            }

            // Start making the string that will be sent in the email
            $email_string = $response_message;
            $key_value_table = $this->build_key_value_table($Form_Builder);
            // Add the table of values to the string
            $email_string .= $key_value_table;

            /*
             * Send the email to the admin/office
             */
            if ($send_email) {
                $status = wp_mail($to, $response_subject.' - '.date("D j M Y, H:i"). ' GMT',  $email_string);//wrap_email($email_string)
            }
            /*
             * If the user has requested it, send an email acknowledgement
             */
            if($mail_receipt) {
                $user_email_string = $auto_response_message.'<p>A copy of your enquiry is shown below.</p>'.$key_value_table;
                if ($send_email) {
                    $status = wp_mail($Form_Builder->form_settings["form_data"]['form-email']['clean'], $auto_response_subject, $user_email_string);// wrap_email($user_response_msg)
                }
            }
            /*
             * Return the html
             */              
            return $this->build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table);
        }

        private function build_key_value_table($Form_Builder) {
        ob_start(); 
        ?>
            <table style="width:100%">
                <tbody>
                    <?php foreach ($Form_Builder->form_settings["form_data"] as $key => $value): ?>
                        <?php                 
                            $required = $value['required'];
                            $type = $value['type'];
                        ?>
                        <tr>
                            <th style="width:30%"><?php echo ucwords(str_replace('-', ' ',substr($key, 5))) ?></th>
                            <td>
                                <?php 
                                    if ($value['type']=='select') {
                                        echo ucwords(str_replace('-', ' ',$value['clean']));
                                    } else {
                                        echo $value['clean'];
                                    }
                                ?>
                            </td>
                        </tr>
                       
                    <?php endforeach ?>   
                </tbody>
            </table>      
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
        }

        private function build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table) {
        ob_start(); ?>
            <?php if ($use_callout): ?>
                <div id="contact-thank-you">
                    <div class="callout secondary" data-closable="slide-out-right">        
            <?php endif ?>

                    <h3><?php echo $browser_output_header; ?></h3>
                    <p><?php echo $auto_response_message; ?></p>
                    <p>A copy of your enquiry is shown below.</p>
                    <?php echo $key_value_table; ?>

            <?php if ($use_callout): ?>
                        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>        
            <?php endif;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
        }
            private function get_form_data() {
                $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
                $form_first_and_last_name = false;
                $form_phone = false;
                // echo "<pre>2 wp-swift-form-builder-style</pre>";
                // echo "<pre>"; var_dump($options); echo "</pre>";
                // echo "<pre>"; var_dump(!isset($options['wp_swift_form_builder_checkbox_css'])); echo "</pre>";
                // echo "<pre>"; var_dump(isset($options['wp_swift_form_builder_checkbox_css'])); echo "</pre>";
                if (isset($options['wp_swift_form_builder_contact_form_checkbox_first_last_name'])) {
                    $form_first_and_last_name = true;
                }
                if (isset($options['wp_swift_form_builder_contact_form_checkbox_phone'])) {
                    $form_phone = true;
                }

                $form_data = array();
                // $form_data = array (
                // // 'form-name' => 
                // //   array (
                // //     'passed' => false,
                // //     'clean' => '',
                // //     'value' => '',
                // //     'section' => 0,
                // //     'required' => 'required',
                // //     'type' => 'text',
                // //     'placeholder' => '',
                // //     'label' => 'Name',
                // //     'help' => '',
                // //   ),
                //   'form-email' => 
                //   array (
                //     'passed' => false,
                //     'clean' => '',
                //     'value' => '',
                //     'section' => 0,
                //     'required' => 'required',
                //     'type' => 'email',
                //     'placeholder' => '',
                //     'label' => 'Email',
                //     'help' => '',
                //   ),
                //   'form-question' => 
                //   array (
                //     'passed' => false,
                //     'clean' => '',
                //     'value' => '',
                //     'section' => 0,
                //     'required' => '',
                //     'type' => 'textarea',
                //     'placeholder' => '',
                //     'label' => 'Question',
                //     'help' => '',
                //   ),  
                // );


                if ($form_first_and_last_name) {
                    $form_data['form-first-name'] = array (
                        'passed' => false,
                        'clean' => '',
                        'value' => '',
                        'section' => 0,
                        'required' => 'required',
                        'type' => 'text',
                        'placeholder' => '',
                        'label' => 'First Name',
                        'help' => '',
                      );
                    $form_data['form-last-name'] = array (
                        'passed' => false,
                        'clean' => '',
                        'value' => '',
                        'section' => 0,
                        'required' => 'required',
                        'type' => 'text',
                        'placeholder' => '',
                        'label' => 'Last Name',
                        'help' => '',
                      );
                }
                else {
                    $form_data['form-name'] = array (
                        'passed' => false,
                        'clean' => '',
                        'value' => '',
                        'section' => 0,
                        'required' => 'required',
                        'type' => 'text',
                        'placeholder' => '',
                        'label' => 'Name',
                        'help' => '',
                      );
                }

                $form_data['form-email'] = array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 0,
                    'required' => 'required',
                    'type' => 'email',
                    'placeholder' => '',
                    'label' => 'Email',
                    'help' => '',
                );


                
                if ($form_phone) {
                    $form_data['form-phone'] = array (
                        'passed' => false,
                        'clean' => '',
                        'value' => '',
                        'section' => 0,
                        'required' => '',
                        'type' => 'text',
                        'placeholder' => '',
                        'label' => 'Telephone',
                        'help' => '',
                      );
                }

                $form_data['form-question'] =array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 0,
                    'required' => '',
                    'type' => 'textarea',
                    'placeholder' => '',
                    'label' => 'Question',
                    'help' => '',
                );

                return $form_data;
            }



            /*
             * Get the form settings
             */
            private function get_form_args() {
                $form_builder_args = array(
                );
                return $form_builder_args;
            }


            public function wp_swift_form_builder_contact_form_add_admin_menu(  ) { 

                // add_submenu_page( 'tools.php',
                //  'WP Swift Form Builder Contact Form',
                //  'WP Swift Form Builder Contact Form',
                //  'manage_options',
                //  'wp_swift_form_builder_contact_form',
                //  'wp_swift_form_builder_contact_form_options_page' );

                if ( empty ( $GLOBALS['admin_page_hooks']['wp-swift-brightlight-main-menu'] ) ) {
                    $options_page = add_options_page( 
                        'Form Builder Contact Page Configuration',
                        'Form Builder: Contact Page',
                        'manage_options',
                        'wp-swift-form-builder-contact-form-settings-menu',
                        array($this, 'wp_swift_form_builder_contact_form_options_page') 
                    );  
                }
                else {
                    // Create a sub-menu under the top-level menu
                    $options_page = add_submenu_page( 'wp-swift-brightlight-main-menu',
                       'Form Builder Contact Page Configuration', 
                       'Form Builder: Contact Page',
                       'manage_options', 
                       'wp-swift-form-builder-contact-form-settings-menu',
                       array($this, 'wp_swift_form_builder_contact_form_options_page') );       
                }

            }

        /*
        */
            public function wp_swift_form_builder_contact_form_settings_init(  ) { 

                register_setting( 'wp_swift_form_builder_contact_form_plugin_page', 'wp_swift_form_builder_contact_form_settings' );

                add_settings_section(
                    'wp_swift_form_builder_contact_form_plugin_page_section', 
                    __( 'Set your preferences for the Contact Form here', 'wp-swift-form-builder-contact-form' ), 
                    array($this, 'wp_swift_form_builder_contact_form_settings_section_callback'), 
                    'wp_swift_form_builder_contact_form_plugin_page'
                );

                add_settings_field( 
                    'wp_swift_form_builder_contact_form_checkbox_first_last_name', 
                    __( 'Use first and last names', 'wp-swift-form-builder-contact-form' ), 
                    array($this, 'wp_swift_form_builder_contact_form_checkbox_first_last_name_render'), 
                    'wp_swift_form_builder_contact_form_plugin_page', 
                    'wp_swift_form_builder_contact_form_plugin_page_section' 
                );

                add_settings_field( 
                    'wp_swift_form_builder_contact_form_checkbox_phone', 
                    __( 'Use telephone', 'wp-swift-form-builder-contact-form' ), 
                    array($this, 'wp_swift_form_builder_contact_form_checkbox_phone_render'), 
                    'wp_swift_form_builder_contact_form_plugin_page', 
                    'wp_swift_form_builder_contact_form_plugin_page_section' 
                );
            }


            public function wp_swift_form_builder_contact_form_checkbox_first_last_name_render(  ) { 

                $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
                ?>
                <input type='checkbox' name='wp_swift_form_builder_contact_form_settings[wp_swift_form_builder_contact_form_checkbox_first_last_name]' <?php checked( $options['wp_swift_form_builder_contact_form_checkbox_first_last_name'], 1 ); ?> value='1'>
                <?php

            }


            public function wp_swift_form_builder_contact_form_checkbox_phone_render(  ) { 

                $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
                ?>
                <input type='checkbox' name='wp_swift_form_builder_contact_form_settings[wp_swift_form_builder_contact_form_checkbox_phone]' <?php checked( $options['wp_swift_form_builder_contact_form_checkbox_phone'], 1 ); ?> value='1'>
                <?php

            }

            public function wp_swift_form_builder_contact_form_settings_section_callback(  ) { 

                echo __( 'This section description', 'wp-swift-form-builder-contact-form' );

            }


            public function wp_swift_form_builder_contact_form_options_page(  ) { 
                if ( get_option( 'wp_swift_form_builder_contact_form_settings' )) {
                    $wp_swift_form_builder_contact_form_settings = get_option( 'wp_swift_form_builder_contact_form_settings' );
                    echo "<pre>"; var_dump($wp_swift_form_builder_contact_form_settings); echo "</pre>";
                }
                ?>
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

            }    

        }

    }
}