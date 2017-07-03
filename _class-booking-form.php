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

            /*
             * Initializes the plugin.
             */
            public function __construct() { 
                $args = $this->get_form_args();
                parent::__construct( false, $this->get_form_data(), get_the_id(), $args );
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

            /*
             * Get the form settings
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
            // private function get_form_data() {
            //     $form_data = array (
            //       'section-count' => 5,
            //       'form-contact-details' => 
            //       array (
            //         'passed' => true,
            //         'section' => 1,
            //         'section_header' => 'Contact Details',
            //         'section_content' => '',
            //         'type' => 'section',
            //         'data_type' => 'section',
            //       ),
            //       'form-group-name' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 1,
            //         'required' => 'required',
            //         'type' => 'text',
            //         'data_type' => 'text',
            //         'placeholder' => '',
            //         'label' => 'Group Name',
            //         'help' => '',
            //       ),
            //       'form-group-leaders-name' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 1,
            //         'required' => 'required',
            //         'type' => 'text',
            //         'data_type' => 'text',
            //         'placeholder' => '',
            //         'label' => 'Group Leaders Name',
            //         'help' => '',
            //       ),
            //       'form-postal-address' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 1,
            //         'required' => 'required',
            //         'type' => 'textarea',
            //         'data_type' => 'textarea',
            //         'placeholder' => '',
            //         'label' => 'Postal Address',
            //         'help' => '',
            //       ),
            //       'form-phone-number' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 1,
            //         'required' => 'required',
            //         'type' => 'text',
            //         'data_type' => 'text',
            //         'placeholder' => '',
            //         'label' => 'Phone Number',
            //         'help' => '',
            //       ),
            //       'form-email' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 1,
            //         'required' => 'required',
            //         'type' => 'email',
            //         'data_type' => 'email',
            //         'placeholder' => '',
            //         'label' => 'Email',
            //         'help' => '',
            //       ),
            //       'form-group-details' => 
            //       array (
            //         'passed' => true,
            //         'section' => 2,
            //         'section_header' => 'Group Details',
            //         'section_content' => '',
            //         'type' => 'section',
            //         'data_type' => 'section',
            //       ),
            //       'form-leaders-male' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => '',
            //         'type' => 'number',
            //         'data_type' => 'input_combo',
            //         'placeholder' => '',
            //         'label' => 'Male',
            //         'help' => '',
            //         'order' => 0,
            //         'parent_label' => 'Number of Leaders',
            //       ),
            //       'form-leaders-female' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => '',
            //         'type' => 'number',
            //         'data_type' => 'input_combo',
            //         'placeholder' => '',
            //         'label' => 'Female',
            //         'help' => '',
            //         'order' => 1,
            //       ),
            //       'form-youths-male' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => '',
            //         'type' => 'number',
            //         'data_type' => 'input_combo',
            //         'placeholder' => '',
            //         'label' => 'Male',
            //         'help' => '',
            //         'order' => 0,
            //         'parent_label' => 'Number of Youths',
            //       ),
            //       'form-youths-female' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => '',
            //         'type' => 'number',
            //         'data_type' => 'input_combo',
            //         'placeholder' => '',
            //         'label' => 'Female',
            //         'help' => '',
            //         'order' => 1,
            //       ),
            //       'form-youth-category' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => 'required',
            //         'type' => 'select',
            //         'data_type' => 'select',
            //         'placeholder' => '',
            //         'label' => 'Youth Category',
            //         'options' => 
            //         array (
            //           0 => 
            //           array (
            //             'option' => 'Beavers',
            //             'option_value' => 'beavers',
            //           ),
            //           1 => 
            //           array (
            //             'option' => 'Cubs/Macaoimh',
            //             'option_value' => 'cubs',
            //           ),
            //           2 => 
            //           array (
            //             'option' => 'Scouts',
            //             'option_value' => 'scouts',
            //           ),
            //           3 => 
            //           array (
            //             'option' => 'Venture Scouts',
            //             'option_value' => 'venture',
            //           ),
            //           4 => 
            //           array (
            //             'option' => 'Other',
            //             'option_value' => 'other',
            //           ),
            //         ),
            //         'selected_option' => '',
            //         'help' => '',
            //       ),
            //       'form-other-youth-categories' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 2,
            //         'required' => '',
            //         'type' => 'text',
            //         'data_type' => 'text',
            //         'placeholder' => 'Please specify if other',
            //         'label' => 'Other',
            //         'help' => '',
            //       ),
            //       'form-dates' => 
            //       array (
            //         'passed' => true,
            //         'section' => 3,
            //         'section_header' => 'Dates',
            //         'section_content' => '',
            //         'type' => 'section',
            //         'data_type' => 'section',
            //       ),
            //       'form-dates-of-stay-start' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 3,
            //         'required' => '',
            //         'type' => 'text',
            //         'data_type' => 'date_range',
            //         'label' => 'Date From',
            //         'help' => '',
            //         'order' => 0,
            //         'parent_label' => 'Dates of Stay',
            //       ),
            //       'form-dates-of-stay-end' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 3,
            //         'required' => '',
            //         'type' => 'text',
            //         'data_type' => 'date_range',
            //         'label' => 'Date To',
            //         'help' => '',
            //         'order' => 1,
            //         'parent_label' => 'Dates of Stay',
            //       ),
            //       'form-accommodation' => 
            //       array (
            //         'passed' => true,
            //         'section' => 4,
            //         'section_header' => 'Accommodation',
            //         'section_content' => '',
            //         'type' => 'section',
            //         'data_type' => 'section',
            //       ),
            //       'form-type-of-stay' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 4,
            //         'required' => 'required',
            //         'type' => 'select',
            //         'data_type' => 'select',
            //         'placeholder' => '',
            //         'label' => 'Type of Stay',
            //         'options' => 
            //         array (
            //           0 => 
            //           array (
            //             'option' => 'Full Board',
            //             'option_value' => 'full-board',
            //           ),
            //           1 => 
            //           array (
            //             'option' => 'Self Catering',
            //             'option_value' => 'self-catering',
            //           ),
            //           2 => 
            //           array (
            //             'option' => 'Camping',
            //             'option_value' => 'camping',
            //           ),
            //           3 => 
            //           array (
            //             'option' => 'Camping with Use of Self Catering Kitchen',
            //             'option_value' => 'camping-with-use-of-self-catering-kitchen',
            //           ),
            //           4 => 
            //           array (
            //             'option' => 'Camping with Meals',
            //             'option_value' => 'camping-with-meals',
            //           ),
            //           5 => 
            //           array (
            //             'option' => 'Other',
            //             'option_value' => 'other-types-stay',
            //           ),
            //         ),
            //         'selected_option' => '',
            //         'help' => '',
            //       ),
            //       'form-other-types-of-stay' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 4,
            //         'required' => '',
            //         'type' => 'text',
            //         'data_type' => 'text',
            //         'placeholder' => '',
            //         'label' => 'Other Types of Stay',
            //         'help' => '',
            //       ),
            //       'form-additional-information-section' => 
            //       array (
            //         'passed' => true,
            //         'section' => 5,
            //         'section_header' => 'Additional Information',
            //         'section_content' => '',
            //         'type' => 'section',
            //         'data_type' => 'section',
            //       ),
            //       'form-special-dietary-requirements' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 5,
            //         'required' => '',
            //         'type' => 'textarea',
            //         'data_type' => 'textarea',
            //         'placeholder' => '',
            //         'label' => 'Special Dietary Requirements',
            //         'help' => '',
            //       ),
            //       'form-allergies' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 5,
            //         'required' => '',
            //         'type' => 'textarea',
            //         'data_type' => 'textarea',
            //         'placeholder' => '',
            //         'label' => 'Allergies',
            //         'help' => '',
            //       ),
            //       'form-other-special-requests' => 
            //       array (
            //         'passed' => false,
            //         'clean' => '',
            //         'value' => '',
            //         'section' => 5,
            //         'required' => '',
            //         'type' => 'textarea',
            //         'data_type' => 'textarea',
            //         'placeholder' => '',
            //         'label' => 'Other Special Requests',
            //         'help' => '',
            //       ),
            //     );
            //     // $form_data = get_book_inputs(152);
            //     return $form_data;
            // }
        }
    }
}