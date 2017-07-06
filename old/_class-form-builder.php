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
        class WP_Swift_Form_Builder_Booking_Form extends WP_Swift_Form_Builder_Plugin {

            private $attributes = null;
            private $option = '';

            /*
             * Initializes the plugin.
             */
            public function __construct() { 
                $args = $this->get_form_args();
                parent::__construct( false, $this->get_form_data(), get_the_id(), $args );
                if (isset($args["option"])) {
                    $this->option = $args["option"];
                }
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
            //         add_shortcode( 'wp-swift-booking-form', array( $this, 'render_booking_form' ) ); 
            //         /*
            //          * Inputs
            //          */
            //         add_action( 'admin_menu', array($this, 'wp_swift_form_builder_booking_form_add_admin_menu'), 21 );
            //         add_action( 'admin_init', array($this, 'wp_swift_form_builder_booking_form_settings_init') );
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
                parent::validate_form();
                if ($this->get_error_count()==0) {
                    echo $this->process_form_after_default_passed();
                }
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
            private function process_form_after_default_passed() {
                /*
                 * Variables
                 */
                $send_email=false;//Debug variable. If false, emails will not be sent
                $date = ' - '.date("Y-m-d H:i:s").' GMT';
                $post_id_or_acf_option= '';//We can specify if it is an option field or use a post_id (https://www.advancedcustomfields.com/add-ons/options-page/)
                $headers = array('Content-Type: text/html; charset=UTF-8');

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
                $auto_response_message = 'Thank you very much for your enquiry. A representative will be bookinging you shortly.';
                // Set the response that is set back to the browser
                $browser_output_header = 'Hold Tight, We\'ll Get Back To You';
                // The auto-response subject
                $auto_response_subject='Auto-response (no-reply)';

                /*
                 * Now, we can override the default settings if they are set
                 */
                // If a to_email is set in ACF, send the email there instead of the admin email
                if (get_field('to_email', $this->option)) {
                    $to = get_field('to_email', $this->option); 
                }
                // Set reponse subject for email
                if (get_field('response_subject', $this->option)) {
                    $response_subject = get_field('response_subject', $this->option).$date; 
                }
                // Start the reponse message for the email
                if (get_field('response_message', $this->option)) {
                    $response_message = get_field('response_message', $this->option);
                }
                //Set auto_response_message
                if (get_field('auto_response_message', $this->option)) {
                    $auto_response_message = get_field('auto_response_message', $this->option);
                }
                // Set the response that is set back to the browser
                if (get_field('browser_output_header', $this->option)) {
                    $browser_output_header = get_field('browser_output_header', $this->option);
                } 
                // The auto-response subject
                if( get_field('auto_response_subject') ) {
                    $auto_response_subject = get_field('auto_response_subject');
                }

                // Start making the string that will be sent in the email
                $email_string = $response_message;
                $key_value_table = $this->build_key_value_table();
                // Add the table of values to the string
                $email_string .= $key_value_table;

                /*
                 * Send the email to the admin/office
                 */
                if ($send_email) {
                    $status = wp_mail($to, $response_subject.' - '.date("D j M Y, H:i"). ' GMT',  $email_string, $headers);//wrap_email($email_string)
                }
                /*
                 * If the user has requested it, send an email acknowledgement
                 */
                $user_output_footer = '';
                if($this->get_show_mail_receipt()) {
                    $user_email_string = $auto_response_message.'<p>A copy of your enquiry is shown below.</p>'.$key_value_table;
                    if ($send_email) {
                        if (isset($this->form_inputs['form-email']['clean'])) {
                            $status = wp_mail($this->form_inputs['form-email']['clean'], $auto_response_subject, $user_email_string, $headers);// wrap_email($user_response_msg)
                        }
                        
                    }
                }
                $user_output_footer = '<p>A confirmation email has been sent to you including these details.</p>';
                // $this->estimate_table();
                /*
                 * Return the html
                 */              
                return $this->build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table, $user_output_footer);
            }

            private function estimate() {

                $form_leaders_male = (integer) $this->form_inputs['form-leaders-male']['value'];
                $form_leaders_female = (integer) $this->form_inputs['form-leaders-female']['value'];
                $form_youths_male = (integer) $this->form_inputs['form-youths-male']['value'];
                $form_youths_female = (integer) $this->form_inputs['form-youths-female']['value'];

                echo "<pre>form-leaders-male -- ";var_dump( $form_leaders_male );echo "</pre>";
                echo "<pre>form-leaders-female -- ";var_dump( $form_leaders_female );echo "</pre>";
                echo "<p>Total Leaders: -- "; echo $form_leaders_male + $form_leaders_female; echo "</p><br>";

                echo "<pre>form-youths-male -- ";var_dump( $form_youths_male );echo "</pre>";
                echo "<pre>form-youths-female -- ";var_dump( $form_youths_female );echo "</pre>";
                echo "<p>Total Youths: -- "; echo $form_youths_male + $form_youths_female; echo "</p><br>";

                $form_dates_of_stay_start = $this->form_inputs['form-dates-of-stay-start']['value'];
                // echo "<pre>fform_dates_of_stay_start -- ";var_dump( $form_dates_of_stay_start );echo "</pre>";

                $form_dates_of_stay_end = $this->form_inputs['form-dates-of-stay-end']['value'];
                // echo "<pre>fform_dates_of_stay_end -- ";var_dump( $form_dates_of_stay_end );echo "</pre>";

//                 $dateTime = date_create_from_format('F d, Y', $form_dates_of_stay_start);
// echo date_format($dateTime, 'Y-m-d');
                $form_dates_of_stay_start = date('Y-m-d', strtotime($form_dates_of_stay_start));
                $form_dates_of_stay_end = date('Y-m-d', strtotime($form_dates_of_stay_end));
                // echo "<pre>";var_dump($form_dates_of_stay_start);echo "</pre>";
                // echo "<pre>";var_dump($form_dates_of_stay_end);echo "</pre>";

                $datetime1 = new DateTime($form_dates_of_stay_start);
                $datetime2 = new DateTime($form_dates_of_stay_end);
                $interval = $datetime1->diff($datetime2);
                echo 'Duration: '.$interval->format('%R%a days');
                echo 'Duration: '.$interval->format('%a');

                //form-types-of-stay

                 $form_types_of_stay = $this->form_inputs['form-types-of-stay']['options'];
                 foreach ($form_types_of_stay as $key => $type_of_stay) {
                     # code...
                    echo "<pre>";var_dump($type_of_stay);echo "</pre>";
                 }
                 // echo "<pre>";var_dump($form_types_of_stay);echo "</pre>";
            }

            private function estimate_table() {

                $form_leaders_male = (integer) $this->form_inputs['form-leaders-male']['value'];
                $form_leaders_female = (integer) $this->form_inputs['form-leaders-female']['value'];
                $form_youths_male = (integer) $this->form_inputs['form-youths-male']['value'];
                $form_youths_female = (integer) $this->form_inputs['form-youths-female']['value'];

                $form_dates_of_stay_start = $this->form_inputs['form-dates-of-stay-start']['value'];
                $form_dates_of_stay_end = $this->form_inputs['form-dates-of-stay-end']['value'];
                $form_dates_of_stay_start = date('Y-m-d', strtotime($form_dates_of_stay_start));
                $form_dates_of_stay_end = date('Y-m-d', strtotime($form_dates_of_stay_end));
                $datetime1 = new DateTime($form_dates_of_stay_start);
                $datetime2 = new DateTime($form_dates_of_stay_end);
                $interval = $datetime1->diff($datetime2);
                $duration = (int) $interval->format('%a');

                $form_types_of_stay = $this->form_inputs['form-types-of-stay']['options'];
                 foreach ($form_types_of_stay as $key => $type_of_stay) {
                     # code...
                    echo "<pre>";var_dump($type_of_stay);echo "</pre>";
                 }
                ob_start(); 
                ?><div class="callout success">
                    <table class="form-feedback" style="width:100%">
                        <thead>
                            <tr>
                                <th colspan="2"><h5>Numbers</h5></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width:30%; text-align:left">Leaders Male</th>
                                <td><?php echo $form_leaders_male ?></td>                                  
                            </tr>
                            <tr>
                                <th style="width:30%; text-align:left">Leaders Female</th>
                                <td><?php echo $form_leaders_female ?></td>                                  
                            </tr>  
                            <tr>
                                <th style="width:30%; text-align:left">Total Leaders</th>
                                <td><?php echo $form_leaders_male + $form_leaders_female ?></td>                                  
                            </tr>                                
                        </tbody>
                    </table>
                    
                    <hr>

                    <table class="form-feedback" style="width:100%">
                        <thead>
                            <tr>
                                <th colspan="2"><h5>Dates</h5></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width:30%; text-align:left">Arrive Date</th>
                                <td><?php echo $form_dates_of_stay_start ?></td>                                  
                            </tr>
                            <tr>
                                <th style="width:30%; text-align:left">Leave Date</th>
                                <td><?php echo $form_dates_of_stay_end ?></td>                                  
                            </tr>  
                            <tr>
                                <th style="width:30%; text-align:left">Total Duration</th>
                                <td><?php echo $interval->format('%R%a days') ?></td>                                  
                            </tr>                                
                        </tbody>
                    </table>
                </div>
                    
                    <?php
                $html = ob_get_contents();
                ob_end_clean();
                echo $html;
                return $html;
            }

            private function build_key_value_table() {

                ob_start(); 
                ?><table class="form-feedback" id="print-table" style="width:100%">
                        <tbody>
                            <?php foreach ($this->form_inputs as $key => $value): ?>
                                <?php                 
                                    // $required = $value['required'];
                                    // if (isset($value['required'])) {
                                    //     $required = $value['required'];
                                    // }
                                    // else {
                                    //     echo "<pre>";var_dump($value);echo "</pre>";
                                    // }
                                    // if () {
                                        
                                    // }
                                    // $type = $value['data_type'];
                                ?>
                                <?php if (isset($value['data_type'])): $type = $value['data_type'];?>
                                    <tr>
           
                                        <?php if ($type=='section'): ?>
                                            <th colspan="2" style="width:100%; text-align:center">
                                                <h3><?php $this->table_cell_header($key) ?></h3>
                                            </th>
                                        <?php else: ?>
                                            <?php if ($value['clean'] !== ''): ?>
                                                <th style="width:30%; text-align:left"><?php $this->table_cell_header($key) ?></th>
                                                <td><?php 
                                                    if ($value['type']=='select') {
                                                        echo ucwords(str_replace('-', ' ',$value['clean']));
                                                    } else {
                                                        echo $value['clean'];
                                                    }
                                                ?></td>                                      
                                            <?php endif ?>                                 
                                        <?php endif ?>
                                    </tr>


                                <?php endif ?>
      
                    
                                
              <?php 
              /*
                            <tr>
                                    <td colspan="2" style="width:100%; text-align:left">
                                    <?php if (isset($value["label"])): ?>
                                        <?php 
                                        echo "<pre>";var_dump($key);echo "</pre>"; 
                                        echo "<pre>";var_dump($value["label"]);echo "</pre>"; 

                                        ?>
                                    <?php endif ?>
                                        
                             
                                    </td>
                                </tr>

               <tr>
                                    <td colspan="2" style="width:100%; text-align:left">
                                    <?php if (isset($value["label"])): ?>
                                        <?php echo "<pre>";var_dump($value["label"]);echo "</pre>"; ?>
                                    <?php endif ?>
                                        
                             
                                    </td>
                                </tr>
                             <tr>
                                    <td colspan="2" style="width:100%; text-align:left">
                                        <?php echo "<pre>";var_dump($type);echo "</pre>"; ?>
                                        <?php if ($type == 'input_combo'): ?>
                                            <?php echo "<pre>";var_dump($value);echo "</pre>"; ?>   
                                        <?php endif ?>
                                    </td>
                                </tr>

                  <tr>
                                    <td colspan="2" style="width:100%; text-align:left">
                                        <?php echo "<pre>";var_dump($value);echo "</pre>"; ?>
                                    </td>
                                </tr>
              */ ?>
                               
                            <?php endforeach ?>   
                        </tbody>
                    </table>
                    <!-- <a href="#" class="print button small warning float-right"><i class="fa fa-print" aria-hidden="true"></i> Print Details</a> -->
<!--                     <div class="clearfix"></div>    
                    <div class="float-right">
                        <small><i>You may also save PDF</i></small>
                    </div> -->

                    
                    <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;
            }

            private function table_cell_header($key) {
                $header = ucwords(str_replace('-', ' ',substr($key, 5)));
                // $header = str_replace($header, ' of ', );
                $header = str_replace(' Of ', ' of ', $header);
                echo $header;
            }

            private function build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table, $user_output_footer) {

                $framework = '';
$options = get_option( 'wp_swift_form_builder_settings' );
    if (isset($options['wp_swift_form_builder_select_css_framework'])) {
        $framework = $options['wp_swift_form_builder_select_css_framework'];
    }

                ob_start(); ?>
                    <?php if ($use_callout):
                        // <!-- <div id="booking-thank-you">
                        //     <div class="callout secondary" data-closable="slide-out-right">  --> 

                            if ($framework === "zurb_foundation"): ?>
                                <div id="booking-thank-you">
                                    <div class="callout secondary" data-closable="slide-out-right">            
                            <?php elseif ($framework === "bootstrap"): ?>
                                <div class="panel panel-success" id="form-success-panel">
                                    <div class="panel-heading">
                                        <!-- <span class="pull-right clickable" data-effect="remove"><i class="fa fa-times"></i></span> -->
                                        <button type="button" class="close" data-target="#form-success-panel" data-dismiss="alert">
                                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                        </button>
                                        <h3><?php echo $browser_output_header; ?></h3>
                                        
                                    </div>
                                    <div class="panel-body">              
                            <?php endif; ?>     
                    <?php endif ?>

                            <?php if ($framework === "zurb_foundation"): ?>
                                <h3><?php echo $browser_output_header; ?></h3>
                            <?php endif; ?>                             
                            <p><?php echo $auto_response_message; ?></p>
                            <p>A copy of your enquiry is shown below.</p>
                            <?php echo $key_value_table; ?>
                            <?php echo $user_output_footer; ?>

                    <?php if ($use_callout): 
                        //         <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                        //             <span aria-hidden="true">&times;</span>
                        //         </button>
                        //     </div>
                        // </div>   
                            if ($framework === "zurb_foundation"): ?>
                                        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>        
                            <?php elseif ($framework === "bootstrap"): ?>
                                     </div>
                                </div>                
                            <?php endif; ?>
                    <?php endif;



                 
                



                $html = ob_get_contents();
                ob_end_clean();
                return $html;
            }

            private function get_form_data() {
                $form_data = array (
                  'section-count' => 5,
                  'form-contact-details' => 
                  array (
                    'passed' => true,
                    'section' => 1,
                    'section_header' => 'Contact Details',
                    'section_content' => '',
                    'type' => 'section',
                    'data_type' => 'section',
                  ),
                  'form-group-name' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 1,
                    'required' => 'required',
                    'type' => 'text',
                    'data_type' => 'text',
                    'placeholder' => '',
                    'label' => 'Group Name',
                    'help' => '',
                  ),
                  'form-group-leaders-name' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 1,
                    'required' => 'required',
                    'type' => 'text',
                    'data_type' => 'text',
                    'placeholder' => '',
                    'label' => 'Group Leaders Name',
                    'help' => '',
                  ),
                  'form-postal-address' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 1,
                    'required' => 'required',
                    'type' => 'textarea',
                    'data_type' => 'textarea',
                    'placeholder' => '',
                    'label' => 'Postal Address',
                    'help' => '',
                  ),
                  'form-phone-number' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 1,
                    'required' => 'required',
                    'type' => 'text',
                    'data_type' => 'text',
                    'placeholder' => '',
                    'label' => 'Phone Number',
                    'help' => '',
                  ),
                  'form-email' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 1,
                    'required' => 'required',
                    'type' => 'email',
                    'data_type' => 'email',
                    'placeholder' => '',
                    'label' => 'Email',
                    'help' => '',
                  ),
                  'form-group-details' => 
                  array (
                    'passed' => true,
                    'section' => 2,
                    'section_header' => 'Group Details',
                    'section_content' => '',
                    'type' => 'section',
                    'data_type' => 'section',
                  ),
                  'form-leaders-male' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => '',
                    'type' => 'number',
                    'data_type' => 'input_combo',
                    'placeholder' => '',
                    'label' => 'Male',
                    'help' => '',
                    'order' => 0,
                    'parent_label' => 'Number of Leaders',
                  ),
                  'form-leaders-female' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => '',
                    'type' => 'number',
                    'data_type' => 'input_combo',
                    'placeholder' => '',
                    'label' => 'Female',
                    'help' => '',
                    'order' => 1,
                  ),
                  'form-youths-male' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => '',
                    'type' => 'number',
                    'data_type' => 'input_combo',
                    'placeholder' => '',
                    'label' => 'Male',
                    'help' => '',
                    'order' => 0,
                    'parent_label' => 'Number of Youths',
                  ),
                  'form-youths-female' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => '',
                    'type' => 'number',
                    'data_type' => 'input_combo',
                    'placeholder' => '',
                    'label' => 'Female',
                    'help' => '',
                    'order' => 1,
                  ),
                  'form-youth-category' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => 'required',
                    'type' => 'select',
                    'data_type' => 'select',
                    'placeholder' => '',
                    'label' => 'Youth Category',
                    'options' => 
                    array (
                      0 => 
                      array (
                        'option' => 'Beavers',
                        'option_value' => 'beavers',
                      ),
                      1 => 
                      array (
                        'option' => 'Cubs/Macaoimh',
                        'option_value' => 'cubs',
                      ),
                      2 => 
                      array (
                        'option' => 'Scouts',
                        'option_value' => 'scouts',
                      ),
                      3 => 
                      array (
                        'option' => 'Venture Scouts',
                        'option_value' => 'venture',
                      ),
                      4 => 
                      array (
                        'option' => 'Other',
                        'option_value' => 'other',
                      ),
                    ),
                    'selected_option' => '',
                    'help' => '',
                  ),
                  'form-other-youth-categories' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 2,
                    'required' => '',
                    'type' => 'text',
                    'data_type' => 'text',
                    'placeholder' => 'Please specify if other',
                    'label' => 'Other',
                    'help' => '',
                  ),
                  'form-dates' => 
                  array (
                    'passed' => true,
                    'section' => 3,
                    'section_header' => 'Dates',
                    'section_content' => '',
                    'type' => 'section',
                    'data_type' => 'section',
                  ),
                  'form-dates-of-stay-start' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 3,
                    'required' => '',
                    'type' => 'text',
                    'data_type' => 'date_range',
                    'label' => 'Date From',
                    'help' => '',
                    'order' => 0,
                    'parent_label' => 'Dates of Stay',
                  ),
                  'form-dates-of-stay-end' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 3,
                    'required' => '',
                    'type' => 'text',
                    'data_type' => 'date_range',
                    'label' => 'Date To',
                    'help' => '',
                    'order' => 1,
                    'parent_label' => 'Dates of Stay',
                  ),
                  'form-accommodation' => 
                  array (
                    'passed' => true,
                    'section' => 4,
                    'section_header' => 'Accommodation',
                    'section_content' => '',
                    'type' => 'section',
                    'data_type' => 'section',
                  ),
                  'form-type-of-stay' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 4,
                    'required' => 'required',
                    'type' => 'select',
                    'data_type' => 'select',
                    'placeholder' => '',
                    'label' => 'Type of Stay',
                    'options' => 
                    array (
                      0 => 
                      array (
                        'option' => 'Full Board',
                        'option_value' => 'full-board',
                      ),
                      1 => 
                      array (
                        'option' => 'Self Catering',
                        'option_value' => 'self-catering',
                      ),
                      2 => 
                      array (
                        'option' => 'Camping',
                        'option_value' => 'camping',
                      ),
                      3 => 
                      array (
                        'option' => 'Camping with Use of Self Catering Kitchen',
                        'option_value' => 'camping-with-use-of-self-catering-kitchen',
                      ),
                      4 => 
                      array (
                        'option' => 'Camping with Meals',
                        'option_value' => 'camping-with-meals',
                      ),
                      5 => 
                      array (
                        'option' => 'Other',
                        'option_value' => 'other-types-stay',
                      ),
                    ),
                    'selected_option' => '',
                    'help' => '',
                  ),
                  'form-other-types-of-stay' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 4,
                    'required' => '',
                    'type' => 'text',
                    'data_type' => 'text',
                    'placeholder' => '',
                    'label' => 'Other Types of Stay',
                    'help' => '',
                  ),
                  'form-additional-information-section' => 
                  array (
                    'passed' => true,
                    'section' => 5,
                    'section_header' => 'Additional Information',
                    'section_content' => '',
                    'type' => 'section',
                    'data_type' => 'section',
                  ),
                  'form-special-dietary-requirements' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 5,
                    'required' => '',
                    'type' => 'textarea',
                    'data_type' => 'textarea',
                    'placeholder' => '',
                    'label' => 'Special Dietary Requirements',
                    'help' => '',
                  ),
                  'form-allergies' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 5,
                    'required' => '',
                    'type' => 'textarea',
                    'data_type' => 'textarea',
                    'placeholder' => '',
                    'label' => 'Allergies',
                    'help' => '',
                  ),
                  'form-other-special-requests' => 
                  array (
                    'passed' => false,
                    'clean' => '',
                    'value' => '',
                    'section' => 5,
                    'required' => '',
                    'type' => 'textarea',
                    'data_type' => 'textarea',
                    'placeholder' => '',
                    'label' => 'Other Special Requests',
                    'help' => '',
                  ),
                );
                // $form_data = get_book_inputs();
                return $form_data;
            }

            /*
             * Get the form settings
             */
            private function get_form_args() {
                $form_builder_args = array("show_mail_receipt"=>false, "option" => "");
                return $form_builder_args;
            }
        }

    }
}