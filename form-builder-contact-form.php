<?php
/*
Plugin Name:       WP Swift: Form Builder Contact Form Add-On
Description:       Generate a contact form. Requires plugin 'WP Swift: Form Builder' to be installed.
Version:           1.0.0
Author:            Gary Swift
License:           GPL-2.0+
Text Domain:       wp-swift-form-builder-contact-form
*/
class WP_Swift_Form_Builder_Contact_Form_Plugin {
     // private $Form_Builder = null;
    /*
     * Initializes the plugin.
     */
    public function __construct() {
                # A shortcode for rendering the contact form.
        add_shortcode( 'custom-contact-form', array( $this, 'render_contact_form' ) ); 
        // add_action( 'wp_enqueue_scripts', array($this, 'enqueue_javascript') );
    }
 
    public function enqueue_javascript () {
        wp_enqueue_script( $handle='wp-swift-form-builder-contact-form', $src=plugins_url( '/assets/javascript/wp-swift-form-builder-contact-form.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
    }
    /**
     * A shortcode for rendering the contact form.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_contact_form( $attributes, $content = null ) {
        $Form_Builder = null;
        if ( class_exists('WP_Swift_Form_Builder_Plugin')) {
            $submit_button_name = "submit-request-form";
            $form_data = $this->get_account_form();
            $form_builder_args = array(
                "form_name" => "request-form",
                "submit_button_name" => $submit_button_name,
                "button_text" => "Submit Query",
            );
            // return __( 'Page ID: '.get_the_ID(), 'wp-swift-form-builder-contact-form' );
            $Form_Builder = new WP_Swift_Form_Builder_Plugin($form_data, get_the_ID(), $form_builder_args, false);
            if ($Form_Builder != null ) {
                $Form_Builder->set_form_data($form_data, get_the_ID(), $form_builder_args, false); //"option"
            }
        }
        else {
            return __( '<h4>Please install plugin WP Swift: Form Builder</h4>', 'wp-swift-form-builder-contact-form' );
        }
        // Parse shortcode attributes
        // $default_attributes = array( 'show_title' => false);//524 //, 'redirect' => '524' 
        // $attributes = shortcode_atts( $default_attributes, $attributes );
        // $show_title = $attributes['show_title'];
     
         
        // Error messages
        // $errors = array();
        // if ( isset( $_REQUEST['contact'] ) ) {
        //     $error_codes = explode( ',', $_REQUEST['contact'] );
         
        //     foreach ( $error_codes as $code ) {
        //         $errors []= $this->get_error_message( $code );
        //     }
        // }
        // $attributes['errors'] = $errors;   

        // Check if user just logged out
        // $attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;


        
        // Render the contact form using an external template
        return $this->get_template_html( 'contact_form', $attributes, $Form_Builder );
    }

    /**
     * Renders the contents of the given template to a string and returns it.
     *
     * @param string $template_name The name of the template to render (without .php)
     * @param array  $attributes    The PHP variables for the template
     *
     * @return string               The contents of the template.
     */
    private function get_template_html( $template_name, $attributes = null, $Form_Builder = null) {
        if ( ! $attributes ) {
            $attributes = array();
        }
     
        ob_start();
     
     // echo "<pre>"; echo $this->form_builder->add(2, 2); echo "</pre>";
        do_action( 'custom_contact_before_' . $template_name );

// echo "<pre>"; var_dump($Form_Builder->form_settings); echo "</pre>";
        // If POST
if(isset($_POST['submit-request-form'])){ //check if form was submitted

  $Form_Builder->set_form_pristine(false);
    // echo "<pre>"; var_dump($_POST); echo "</pre><hr>";
    // $form_settings = process_form($form_settings, $_POST);


        //      $mail_receipt=false;//auto-reponse flag
        // if(isset($post['mail-receipt'])){
        //     $mail_receipt=true;//Send an auto-response to user
        // }
        // // include('_email-template.php');
    // echo "<pre>"; var_dump($Form_Builder->form_settings); echo "</pre><hr>";
        //Loop through the POST and validate. Store the values in $form_data
        foreach ($_POST as $key => $value) {
            if (($key!='submit-request-form') && ($key!='mail-receipt') && ($key!='form-file-upload') && ($key!='g-recaptcha-response')) { //Skip the button and mail-receipt checkbox
                // $Form_Builder->form_settings[$key] = check_input($Form_Builder->form_settings[$key], $value);//Validate input  

                // check_input($data, $value)

                // $Form_Builder->form_settings[$key] = $Form_Builder->check_input($Form_Builder->form_settings[$key], $value);//Validate input    
                $Form_Builder->check_input($key, $value);//Validate input    
                // echo '<pre>'.$key.': ';var_dump($Form_Builder->form_settings["form_data"][$key]); echo '</pre><hr>';
            }
        }
         // echo "<pre>"; var_dump($Form_Builder->form_settings); echo "</pre><hr>";
}   

     




        // // Loop through form1_data and increase form1_num_error_found count for each error
        // foreach ($form_settings["form_data"] as $key => $value) {
        //     if(!$form_settings["form_data"][$key]['passed']) {
        //         //An error has been found in this input so increase the count
        //         $form_settings["form_num_error_found"]++;
        //     }
        // }

        // if($form_settings["form_num_error_found"]) {
        //     // Error has been found in user input
        //     $form_settings["response_msg"] = "We're sorry, there has been an error with the form input.<br>Please rectify the ".$form_num_error_found." errors below and resubmit.";
        //     $form_settings["error_class"] = 'error';
        //     // echo "<pre>"; var_dump($form_settings); echo "</pre>";
        // }




        // require( 'templates/' . $template_name . '.php');
        if ($Form_Builder != null ) {
            $Form_Builder->acf_build_form();
        }

     
        do_action( 'custom_contact_after_' . $template_name );
     
        $html = ob_get_contents();
        ob_end_clean();
     
        return $html;
    }

    private function get_form_data() {
        return array (
        'form-name' => 
          array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => 'required',
            'type' => 'text',
            'placeholder' => '',
            'label' => 'Name',
            'help' => '',
          ),
          // 'form-first-name' => 
          // array (
          //   'passed' => false,
          //   'clean' => '',
          //   'value' => '',
          //   'section' => 0,
          //   'required' => 'required',
          //   'type' => 'text',
          //   'placeholder' => '',
          //   'label' => 'First Name',
          //   'help' => '',
          // ),
          // 'form-last-name' => 
          // array (
          //   'passed' => false,
          //   'clean' => '',
          //   'value' => '',
          //   'section' => 0,
          //   'required' => 'required',
          //   'type' => 'text',
          //   'placeholder' => '',
          //   'label' => 'Last Name',
          //   'help' => '',
          // ),
          'form-email' => 
          array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => 'required',
            'type' => 'email',
            'placeholder' => '',
            'label' => 'Email',
            'help' => '',
          ),
          'form-question' => 
          array (
            'passed' => false,
            'clean' => '',
            'value' => '',
            'section' => 0,
            'required' => '',
            'type' => 'textarea',
            'placeholder' => '',
            'label' => 'Question',
            'help' => '',
          ),  
        );
    }

    public function get_account_form() {
      return array (
  'form-section-open-name-details' => 
  array (
    'passed' => true,
    'section' => 1,
    'section_header' => 'Name Details',
    'section_content' => 'Seamlessly benchmark magnetic initiatives whereas vertical e-tailers. Holisticly restore B2B web services via diverse synergy. Synergistically grow fully tested strategic theme areas whereas',
    'type' => 'section',
  ),
  'form-user-account-first-name' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 1,
    'required' => 'required',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'First Name',
    'help' => '',
  ),
  'form-user-account-last-name' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 1,
    'required' => 'required',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'Last Name',
    'help' => '',
  ),
  'form-user-account-nickname' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 1,
    'required' => 'required',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'Nickname',
    'help' => '',
  ),
  'form-section-close-name-details' => 
  array (
    'passed' => true,
    'section' => 1,
    'type' => 'section_close',
  ),
  'form-section-open-contact-info' => 
  array (
    'passed' => true,
    'section' => 2,
    'section_header' => 'Contact Info',
    'section_content' => 'Intrinsicly negotiate revolutionary channels after vertical value. Conveniently synthesize.',
    'type' => 'section',
  ),
  'form-user-account-email' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 2,
    'required' => 'required',
    'type' => 'email',
    'placeholder' => '',
    'label' => 'Email',
    'help' => '',
  ),
  'form-user-account-website' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 2,
    'required' => 'required',
    'type' => 'url',
    'placeholder' => '',
    'label' => 'Website',
    'help' => '',
  ),
  'form-user-account-tel' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 2,
    'required' => '',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'Telephone',
    'help' => '',
  ),
  'form-user-account-mobile' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 2,
    'required' => '',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'Mobile',
    'help' => '',
  ),
  'form-section-close-contact-info' => 
  array (
    'passed' => true,
    'section' => 2,
    'type' => 'section_close',
  ),
  'form-section-open-about-yourself' => 
  array (
    'passed' => true,
    'section' => 3,
    'section_header' => 'About Yourself',
    'section_content' => 'Holisticly recaptiualize market positioning paradigms and user-centric internal or.',
    'type' => 'section',
  ),
  'form-user-account-practice' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 3,
    'required' => '',
    'type' => 'text',
    'placeholder' => '',
    'label' => 'Legal Practice/Firm',
    'help' => '',
  ),
  'form-user-account-bio' => 
  array (
    'passed' => false,
    'clean' => '',
    'value' => '',
    'section' => 3,
    'required' => '',
    'type' => 'textarea',
    'placeholder' => '',
    'label' => 'Biographical Info',
    'help' => '',
  ),
  'form-section-close-about-yourself' => 
  array (
    'passed' => true,
    'section' => 3,
    'type' => 'section_close',
  ),
);
    }
}
// Initialize the plugin
$form_builder_contact_form_plugin = new WP_Swift_Form_Builder_Contact_Form_Plugin();