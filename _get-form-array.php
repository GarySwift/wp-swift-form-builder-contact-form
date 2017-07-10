<?php 
function _acf_form_array($form_data) {
    global $post;
        $name =  get_sub_field('name');
        // echo "<pre>name ";var_dump($name);echo "</pre>";
        $id = sanitize_title_with_dashes( get_sub_field('name') );
        $type = get_sub_field('type');
        $data_type = get_sub_field('type');
        if($type==='date' || $type==='date_range' || $type==='input_combo') {
            $type='text';
        }
        $label = get_sub_field('label');
        $help = get_sub_field('help');
        $placeholder = get_sub_field('placeholder');
        $required = get_sub_field('required');
        $select_options='';
        $instructions = get_sub_field('instructions');

        if( get_sub_field('select_options') ) {
            $select_options = get_sub_field('select_options');
            if ($data_type==='checkbox' || $data_type==='select') {
                foreach ($select_options as $key => $value) {
                    // echo "<pre>";var_dump($value);echo "</pre>";
                    $value['checked'] = false;
                    if ( $value['option_value'] === '') {
                        // $value['option_value'] = sanitize_title_with_dashes( $value['option'] );
                        $select_options[$key]['option_value'] = sanitize_title_with_dashes( $value['option'] );
                    }
                    if ($data_type==='checkbox') {
                        $select_options[$key]['checked'] = false;
                    }
                    
                    // echo "<pre>";var_dump($value);echo "</pre><hr>";
                    
                }
            }
            // if ($data_type==='select') {
            //     // echo "<pre>";var_dump($select_options);echo "</pre>";
            //     foreach ($select_options as $key => $value) {
            //         // echo "<hr><pre>";var_dump($value);echo "</pre><hr>";
            //         if ( $value['option_value'] === '') {
            //             // $value['option_value'] = sanitize_title_with_dashes( $value['option'] );
            //             $select_options[$key]['option_value'] = sanitize_title_with_dashes( $value['option'] );
            //         }
                    
            //     }
            //     // echo "<hr><pre>";var_dump($select_options);echo "</pre>";
            // }            
        }

        if($required) {
            $required = 'required';
        }
        else {
            $required = '';
        }
        if(!$label) {
            $label = $name;
        }

        // echo "<pre>";var_dump($data_type);echo "</pre>";
        switch ($data_type) {
            // case "section
            //     $section++;
            //     $form_data['form-'.$id] = array("section"=>$section, "type"=>$type, "name"=>get_sub_field('name'));
            //     break;            
            case "text":
            case "url":
            case "email":
            case "number":
                $form_data['form-'.$id] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$label, "help"=>$help, "instructions" => $instructions);
                break;
            case "textarea":
                $form_data['form-'.$id] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$label, "help"=>$help, "instructions" => $instructions);
                break; 
            case "select":
                $form_data['form-'.$id] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$label, "options"=>$select_options, "selected_option"=>"", "help"=>$help, "instructions" => $instructions);
                break;
            case "multi_select":
            case "checkbox":
                $form_data['form-'.$id] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$label, "options"=>$select_options, "selected_option"=>"", "help"=>$help, "instructions" => $instructions);
                break;    
            case "file":
                $enctype = 'enctype="multipart/form-data"';
                $form_class = 'js-check-form-file';
                $form_data['form-'.$id] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$label, "accept"=>"pdf", "help"=>$help, "instructions" => $instructions);
                break;
            case "input_combo":

            	$input_one =  get_sub_field('input_one');
            	$input_two =  get_sub_field('input_two');
            	$id_one = sanitize_title_with_dashes( $input_one );
            	$id_two = sanitize_title_with_dashes( $input_two );
            	$combo_input_type =  get_sub_field('combo_input_type');
                $form_data['form-'.$id.'-'.$id_one] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$combo_input_type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$input_one, "help"=>$help, "instructions" => $instructions, 'order'=>0, 'parent_label'=>$label);
                $form_data['form-'.$id.'-'.$id_two] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$combo_input_type, "data_type"=>$data_type,  "placeholder"=>$placeholder, "label"=>$input_two, "help"=>$help, "instructions" => $instructions, 'order'=>1);
                break;                
            case "date_range":
                $form_data['form-'.$id.'-start'] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type, "label"=>"Date From", "help"=>$help, "instructions" => $instructions, 'order'=>0, 'parent_label'=>$label);
                $form_data['form-'.$id.'-end'] = array("passed"=>false, "clean"=>"", "value"=>"", "section"=>get_section($form_data), "required"=>$required, "type"=>$type, "data_type"=>$data_type, "label"=>"Date To", "help"=>$help, "instructions" => $instructions, 'order'=>1, 'parent_label'=>$label);
                break; 
            case "section":
            	if (isset($form_data['section-count'])) {
            		$form_data['section-count']++;
            	}
            	else {
            		$form_data['section-count']=1;
            	}
                if( get_sub_field('section_header') ) {
                    $section_header = get_sub_field('section_header');
                }
                else {
                    $section_header='';
                }
                if( get_sub_field('section_content') ) {
                    $section_content = get_sub_field('section_content');
                }
                else {
                    $section_content='';
                }
                $form_data['form-'.$id] = array("passed"=>true, "section"=>get_section($form_data), "section_header"=>$section_header, "section_content"=>$section_content, "type"=>$type, "data_type"=>$type);
                break;  
            case "section_close":
                $form_data['form-'.$id] = array("passed"=>true, "section"=>get_section($form_data), "type"=>$type, "data_type"=>$type);
                break;                                                    
        }
    return $form_data;       
}

function get_section($form_data) {
	if (isset($form_data['section-count'])) {
		return $form_data['section-count'];
	}
	else {
		return 0;
	}
}

// echo "<pre>";var_dump(get_the_id());echo "</pre>";
function get_book_inputs($page_id) {
    // echo "<pre>page_id "; var_dump($page_id); echo "</pre>";
	$form_data = array();
	// Construct the array that makes the form
	if ( have_rows('form_inputs', $page_id) ) :
        // echo "<pre>have_rows('form_inputs</pre>"; 
	    // $form_data = array();

	    // $section=0;
	    // $section_count=0;
	    // while( have_rows('form_inputs') ) : the_row(); // Loop through the repeater for form inputs
	    //     if(get_sub_field('type')==='section') {
	    //         // $section=1;
	    //         $section_count++;
	    //     }
	    // endwhile;// End the AFC loop  

	    while( have_rows('form_inputs', $page_id) ) : the_row(); // Loop through the repeater for form inputs        
	         $form_data =  _acf_form_array($form_data);  
	    endwhile;// End the AFC loop  
	endif;
    // echo "<pre>"; var_dump($form_data); echo "</pre>";
	return $form_data;
}