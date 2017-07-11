<?php
$contact_form_page = false;
$location = array();

if( class_exists('acf') ):

	if( get_field('contact_form_page', 'option') ):
	    $contact_form_page = get_field('contact_form_page', 'option');
	    $location[] = form_builder_location_array( $contact_form_page );
	endif;

endif;

if( $contact_form_page && function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_595771b58d12c',
	'title' => 'Contact Form',
	'fields' => array (
		array (
			'key' => 'field_5957740876bba',
			'label' => 'Form Notes',
			'name' => '',
			'type' => 'message',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'This page has been assigned as the default contact page. The default form includes first and last name inputs, along with an email input and a question textarea.',
			'new_lines' => '',
			'esc_html' => 0,
		),
		array (
			'key' => 'field_59577633fb276',
			'label' => 'Form Adjustments',
			'name' => '',
			'type' => 'message',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'The following adjustments are available on the default contact page.',
			'new_lines' => '',
			'esc_html' => 0,
		),
		array (
			'key' => 'field_595771c51e07a',
			'label' => 'Combine Name Fields',
			'name' => 'combine_name_fields',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array (
			'key' => 'field_595773241e07b',
			'label' => 'Show Telephone Input',
			'name' => 'show_telephone_input',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array (
			'key' => 'field_5957739c1e07c',
			'label' => 'Show Company Input',
			'name' => 'show_company_input',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array (
			'key' => 'field_5964b570af257',
			'label' => 'Location',
			'name' => '',
			'type' => 'message',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => 'This form will be automatically be shown after the main content. However, you can override this default behaviour by activating the switch below and using the shortcode [contact-form]. You should put the shortcode into the WYSIWYG editor on this page. You can also enclose content like this [shorcode]content[/shorcode].',
			'new_lines' => 'wpautop',
			'esc_html' => 0,
		),
		array (
			'key' => 'field_5964b4138c8fe',
			'label' => 'Use Shortcode',
			'name' => 'use_shortcode',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
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

endif;