<?php
/*
 * Render the default contact form using the 'the_content' filter
 * 
 */
function wp_swift_render_contact_form_after_content( $content ) {
	$form = '';
	$form_builder = null;

	if( class_exists('acf') ) {
		// $contact_page_id = 138;
		// This page ID
		$this_page_id = get_the_ID();
		// The preset default contact page ID (Saved via ACF options page)
		$contact_page_id = get_field('contact_form_page', 'option');
		
		// The booking page ID (Saved via ACF options page)
		$booking_page_id = get_field('booking_form_page', 'option');
		// Form IDs added with the repeater on the same options page
		$form_pages = array();

		if( have_rows('additional_forms', 'option') ):
		    while ( have_rows('additional_forms', 'option') ) : the_row();
		        $form_pages[] = get_sub_field('page');
		    endwhile;
		endif;


		if ($this_page_id === $contact_page_id) {
			$form_builder = get_contact_form();
		}
		elseif ($this_page_id === $booking_page_id) {
			$form_builder = get_booking_form();
		}
		elseif (in_array($this_page_id, $form_pages )) {
			$form_builder = get_generic_form($this_page_id);
		}


		if ($form_builder !== null ) {
			ob_start();
			// //check if form was submitted
	        if(isset($_POST[ $form_builder->get_submit_button_name() ])){ 
	            $form_builder->process_form(); 
	        }
	        $form_builder->acf_build_form();
	        $form = ob_get_contents();
	        ob_end_clean();
	    }

	}
    return $content.$form;
}
add_filter( 'the_content', 'wp_swift_render_contact_form_after_content' );