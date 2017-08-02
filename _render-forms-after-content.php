<?php
/*
 * Render the default contact form using the 'the_content' filter
 * 
 */
function wp_swift_render_contact_form_after_content( $content ) {
	if( !get_field('use_shortcode') ) :
		$form = '';
		$form_builder = null;

		if( class_exists('acf') ) {
			// This page ID
			$this_page_id = get_the_ID();
			// The preset default contact page ID (Saved via ACF options page)
			$contact_page_id = get_field('contact_form_page', 'option');

			// Form IDs added with the repeater on the same options page
			$form_pages = array();

			if( have_rows('additional_forms', 'option') ):
			    while ( have_rows('additional_forms', 'option') ) : the_row();
			        $form_pages[] = get_sub_field('page');
			    endwhile;
			endif;


			if ($this_page_id === $contact_page_id) {
				$form_builder = wp_swift_get_contact_form();
			}
			elseif (in_array($this_page_id, $form_pages )) {
				$form_position = get_field('form_position', $this_page_id);
				if( $form_position !== 'shortcode' ) {
					$form_builder = wp_swift_get_generic_form($this_page_id);
				}
			}


			if ($form_builder !== null ) {
				ob_start();
		        ?><div class="contact-form-container"><?php 
					if ($form_builder != null ) {
			            if(isset($_POST[ $form_builder->get_submit_button_name() ])){ //check if form was submitted
			                $form_builder->process_form(); 
			            }
				        $form_builder->acf_build_form();
				    } 
				?></div><?php
		        $form = ob_get_contents();
		        ob_end_clean();
		    }

		}
	    return $content.$form;	    
	endif;
	return $content;	

}
add_filter( 'the_content', 'wp_swift_render_contact_form_after_content' );