<?php
/*
Plugin Name:       WP Swift: Form Builder Contact Form Add-On
Description:       Generate a contact form. Requires plugin 'WP Swift: Form Builder' to be installed.
Version:           1.0.1
Author:            Gary Swift
License:           GPL-2.0+
Text Domain:       wp-swift-form-builder-contact-form
*/

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
        class WP_Swift_Form_Builder_Contact_Form extends WP_Swift_Form_Builder_Plugin {

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

            /*
             * Process the form
             * 
             * Use the parent class to run the default validation on the form
             * If default is passed, we let the child do additional checks required by this form such as existing email
             * 
             * Eg. The parent will check if the email exists, is valid etc but the child only knows if it needs to check for duplicates
             * The parent does not know what to with a sccessful form, it just validates default settings
             * The parent will handle errors if default errors have been found
             */
            public function process_form() {
                // echo "process_form<pre>"; var_dump($_POST); echo "</pre>";
                parent::validate_form();
                if ($this->get_error_count()==0) {
                    $this->process_form_after_default_passed($_POST);
                }
                // else {
                //     $this->process_form_after_default_passed_login_failed();
                // }
            }


            /*
             * Default has passed so the child will continue processing
             */
            private function process_form_after_default_passed() {
                echo "<pre>"; var_dump('process_form_after_default_passed'); echo "</pre>";
                // echo "<pre>"; var_dump($_POST); echo "</pre>";

                               ob_start(); 
                ?>
                    <table style="width:100%">
                        <tbody>
                            <?php foreach ($this->form_inputs as $key => $value): ?>
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
                echo $html;
            }

            /*
             * Process the user after default error checking has passed but the login attempt has failed
             * We know it has failed because a successful login results in a redirect
             * The only reason we attempt wp_signon is to get the error codes
             */
            private function process_form_after_default_passed_login_failed() {
                echo "<pre>"; var_dump('process_form_after_default_passed_login_failed'); echo "</pre>";
                // echo "<pre>"; var_dump($_POST); echo "</pre>";
            }  

            /*
             * Default has passed so the child will continue processing
             *
             * The form has been validated so we can send the emails.
             * This will get the to and from email recipients, build the html message and send the emails.
             * It then returns a html string that tells the user what has happened.
             *
             * @param class $Form_Builder   The name of the template to render (without .php)
             * @param array  $post          The global $_POST variable cast as a the local $post variable
             *
             * @return string               The html success message
             */
            private function process_form_after_default_passed_($post, $post_id=false) {
                /*
                 * Variables
                 */
                $send_email=false;//Debug variable. If false, emails will not be sent
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
                if (isset($options['wp_swift_form_builder_contact_form_checkbox_first_last_name'])) {
                    $form_first_and_last_name = true;
                }
                if (isset($options['wp_swift_form_builder_contact_form_checkbox_phone'])) {
                    $form_phone = true;
                }

                $form_data = array();

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
        }

    }
}