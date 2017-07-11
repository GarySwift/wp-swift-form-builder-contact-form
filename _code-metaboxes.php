<?php
// Add help text to right of screen in a metabox
function metabox_form_builder_form_data_array() {
	global $post;
	if(!empty($post)) {
        $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);

        // if($pageTemplate == 'page-templates/page-book-now.php' ) {
        if($post->ID == $booking_form_page ) {
		    add_meta_box( 
				'metabox-form-builder-form-data-array',//$id
				'Form Builder: Form Data Array',// $title
				'metabox_form_builder_form_data_array_content',//$callback
				'page',// $screen
				'normal',// $context
				'low');// $priority 
        }
    }    
}
// callback function to populate metabox
function metabox_form_builder_form_data_array_content() { 
	$form_data = get_page_inputs();
	?><p>Developers can copy and paste the code below to save on page loading times in the front end.</p>
	<textarea readonly class="copy" onclick="this.focus();this.select()" onfocus="this.focus();this.select();"><?php var_export($form_data) ?></textarea><?php 
}
add_action( 'add_meta_boxes', 'metabox_form_builder_form_data_array' );