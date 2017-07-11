<?php
function wp_swift_get_contact_form( $attributes=array() ) {
    $form_builder = null;
    if (class_exists('WP_Swift_Form_Builder_Contact_Form')) {
        $form_builder = new WP_Swift_Form_Builder_Contact_Form( get_contact_form_data(), array("show_mail_receipt"=>true, "option" => "") );    
    }
    return $form_builder;        
}

function wp_swift_get_generic_form( $attributes=array() ) {
    $form_builder = null;
    if (class_exists('WP_Swift_Form_Builder_Contact_Form')) {
        $form_builder = new WP_Swift_Form_Builder_Contact_Form( get_page_inputs(get_the_id()), array("show_mail_receipt"=>true, "option" => "") );
    }
    return $form_builder;        
}

function form_builder_location_array($id) {
    return array ( array (
            'param' => 'page',
            'operator' => '==',
            'value' => $id,
        ),
    );
}