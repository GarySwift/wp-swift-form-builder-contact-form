<?php 
/*
 * Get the booking form settings array
 *
 * @return array    form data array
 */
function get_contact_form_data() {
    $options = get_option( 'wp_swift_form_builder_contact_form_settings' );
    $form_first_and_last_name = false;
    $form_phone = false;
    if (isset($options['wp_swift_form_builder_contact_form_checkbox_first_last_name'])) {
        $form_first_and_last_name = true;
    }
    if (isset($options['wp_swift_form_builder_contact_form_checkbox_phone'])) {
        $form_phone = true;
    }

    $combine_name_fields = false;
    $show_telephone_input = false;
    $show_company_input = false;
    $form_data = array();

    if( class_exists('acf') ) {
        if( get_field('contact_form_page', 'option') ) {
            $contact_form_page = get_field('contact_form_page', 'option');
            $location[] = form_builder_location_array( $contact_form_page );
            if( get_field('combine_name_fields', $contact_form_page) ) {
                $combine_name_fields = get_field('combine_name_fields', $contact_form_page);
            }
            if( get_field('show_telephone_input', $contact_form_page) ) {
                $show_telephone_input = get_field('show_telephone_input', $contact_form_page);
            }
            if( get_field('show_company_input', $contact_form_page) ) {
                $show_company_input = get_field('show_company_input', $contact_form_page);
            }
        }
    }

    if (!$combine_name_fields) {
        $form_data['form-first-name'] = array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => 'required',
            'type' => 'text',
            'data_type' => 'text',
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
            'data_type' => 'text',
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
            'data_type' => 'text',
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
        'data_type' => 'email',
        'placeholder' => '',
        'label' => 'Email',
        'help' => '',
    );


    
    if ($show_telephone_input) {
        $form_data['form-phone'] = array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => '',
            'type' => 'text',
            'data_type' => 'text',
            'placeholder' => '',
            'label' => 'Telephone',
            'help' => '',
        );
    }

    if ($show_company_input) {
        $form_data['form-company'] = array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => '',
            'type' => 'text',
            'data_type' => 'text',
            'placeholder' => '',
            'label' => 'Company',
            'help' => '',
        );
    }

    $form_data['form-question'] =array (
        'passed' => false,
        'clean' => '',
        'value' => '',
        'section' => 0,
        'required' => 'required',
        'type' => 'textarea',
        'data_type' => 'textarea',
        'placeholder' => '',
        'label' => 'Question',
        'help' => '',
    );

    return $form_data;
}

/*
 * Get the booking form settings array
 *
 * @return array    form data array
 */
function get_booking_form_data() {
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
    // $form_data = get_book_inputs(152);
    return $form_data;
}