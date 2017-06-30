<?php
// function form_builder_location_array($id) {
// 	return array ( array (
// 			'param' => 'page',
// 			'operator' => '==',
// 			'value' => $id,
// 		),
// 	);
// }

$contact_form_page = false;
$booking_form_page = false;
$location = array();

if( function_exists('acf_add_local_field_group') ):

	if( get_field('contact_form_page', 'option') ) {
	    $contact_form_page = get_field('contact_form_page', 'option');
	    $location[] = form_builder_location_array( $contact_form_page );
	}
	if( get_field('booking_form_page', 'option') ) {
	    $booking_form_page = get_field('booking_form_page', 'option');
	    $location[] = form_builder_location_array( $booking_form_page );
	}

	if ( $contact_form_page || $booking_form_page ):
 
	$sitename = get_bloginfo('name');
	acf_add_local_field_group(array (
		'key' => 'group_5666f0cb4d504',
		'title' => 'Form Builder: Contact Form',
		'fields' => array (
			array (
				'key' => 'field_5695232e13e86',
				'label' => 'To Email',
				'name' => 'to_email',
				'type' => 'email',
				'instructions' => 'Leave blank to send email to the default admin email.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_5695245e13e87',
				'label' => 'Response Subject',
				'name' => 'response_subject',
				'type' => 'text',
				'instructions' => 'Email subject for the office',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'New Customer Enquiry',
				'placeholder' => 'New Customer Enquiry',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_5695273813e8b',
				'label' => 'Response Message',
				'name' => 'response_message',
				'type' => 'wysiwyg',
				'instructions' => 'Email message for the office (appears above user enquiry)',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '<h3>For the attention of '.$sitename.' Admin</h3>

	A website user has made the following enquiry:',
				'tabs' => 'all',
				'toolbar' => 'full',
				'media_upload' => 1,
				'delay' => 0,
			),
			array (
				'key' => 'field_56f3f15656ee2',
				'label' => 'Confirmation Header',
				'name' => 'browser_output_header',
				'type' => 'text',
				'instructions' => 'The heading in confirmation panel after a the form has been submitted',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Hold Tight, We\'ll Get Back To You',
				'placeholder' => 'Hold Tight, We\'ll Get Back To You',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_5695253913e88',
				'label' => 'Auto Response Subject',
				'name' => 'auto_response_subject',
				'type' => 'text',
				'instructions' => 'Email subject for the user',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => $sitename.' Auto-response (no-reply)',
				'placeholder' => 'Auto-response (no-reply)',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5695259613e89',
				'label' => 'Auto Response Message',
				'name' => 'auto_response_message',
				'type' => 'wysiwyg',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Thank you very much for your enquiry. A '.$sitename.' representative will be contacting you shortly.

	Kind regards,
	The '.$sitename.' team.',
				'tabs' => 'all',
				'toolbar' => 'basic',
				'media_upload' => 1,
				'delay' => 0,
			),
		),
		'location' => $location,	
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

	endif;//@end if ($contact_form_page || $booking_form_page)
endif;//@end if( function_exists('acf_add_local_field_group') )