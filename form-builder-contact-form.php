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
    private $attributes = null;
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
            $form_data = $this->get_form_data();
            $form_builder_args = array(
                "form_name" => "request-form",
                "submit_button_name" => $submit_button_name,
                "button_text" => "Submit Query",
                "show_mail_receipt" => true,
            );
            $Form_Builder = new WP_Swift_Form_Builder_Plugin($attributes, $form_data, get_the_ID(), $form_builder_args, $attributes, false);
        }
        else {
            return __( '<h4>Please install plugin WP Swift: Form Builder</h4>', 'wp-swift-form-builder-contact-form' );
        }
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
        do_action( 'custom_contact_before_' . $template_name );//To do

        // If POST
        if(isset($_POST['submit-request-form'])){ //check if the form was submitted

            $Form_Builder->validate_form();
            if ($Form_Builder->get_error_count()==0) {
                echo $this->process_successful_form($Form_Builder, $_POST);
            }
        }   

        /*
         * Look for form
         */
        if ($Form_Builder != null ) {
            $Form_Builder->acf_build_form();
        }
     
        do_action( 'custom_contact_after_' . $template_name );
     
        $html = ob_get_contents();
        ob_end_clean();
     
        return $html;
    }

/*
 * The form has been validated so we can send the emails.
 * This will get the to and from email recipients, build the html message and send the emails.
 * It then returns a html string that tells the user what has happened.
 *
 * @param class $Form_Builder   The name of the template to render (without .php)
 * @param array  $post          The global $_POST variable cast as a the local $post variable
 *
 * @return string               The html success message
 */
private function process_successful_form($Form_Builder, $post, $post_id=false) {
    /*
     * Variables
     */
    $send_email=true;//Debug variable. If false, emails will not be sent
    $date = ' - '.date("Y-m-d H:i:s").' GMT';
    $post_id_or_acf_option= '';//We can specify if it is an option field or use a post_id (https://www.advancedcustomfields.com/add-ons/options-page/)
    $mail_receipt=false;//auto-reponse flag
    if(isset($post['mail-receipt'])){
        $mail_receipt=true;//Send an auto-response to user
    }

    /*
     * These are the default form settings
     */
    // If a debug email is set in ACF, send the email there instead of the admin email
    $to = get_option('admin_email');
    // Set reponse subject for email
    $response_subject = "New Enquiry".$date;
    // Start the reponse message for the email
    $response_message = '<p>A website user has made the following enquiry.</p>';
    //Set auto_response_message
    $auto_response_message = 'Thank you very much for your enquiry. A representative will be contacting you shortly.';
    // Set the response that is set back to the browser
    $browser_output_header = 'Hold Tight, We\'ll Get Back To You';
    // The auto-response subject
    $auto_response_subject='Auto-response (no-reply)';

    /*
     * Now, we can override the default settings if they are set
     */
    // If a debug email is set in ACF, send the email there instead of the admin email
    if (get_field('debug_email', $post_id_or_acf_option)) {
        $to = get_field('debug_email', $post_id_or_acf_option); 
    }
    // Set reponse subject for email
    if (get_field('response_subject', $post_id_or_acf_option)) {
        $response_subject = get_field('response_subject', $post_id_or_acf_option).$date; 
    }
    // Start the reponse message for the email
    if (get_field('response_message', $post_id_or_acf_option)) {
        $response_message = '<p>'.get_field('response_message', $post_id_or_acf_option).'.</p>';
    }
    //Set auto_response_message
    if (get_field('auto_response_message', $post_id_or_acf_option)) {
        $auto_response_message = get_field('auto_response_message', $post_id_or_acf_option);
    }
    // Set the response that is set back to the browser
    if (get_field('browser_output_header', $post_id_or_acf_option)) {
        $browser_output_header = get_field('browser_output_header', $post_id_or_acf_option);
    } 
    // The auto-response subject
    if( get_field('auto_response_subject') ) {
        $auto_response_subject = get_field('auto_response_subject');
    }

    // Start making the string that will be sent in the email
    $email_string = $response_message;
    $key_value_table = $this->build_key_value_table($Form_Builder);
    // Add the table of values to the string
    $email_string .= $key_value_table;

    /*
     * Send the email to the admin/office
     */
    if ($send_email) {
        $status = wp_mail($to, $response_subject.' - '.date("D j M Y, H:i"). ' GMT',  $email_string);//wrap_email($email_string)
    }
    /*
     * If the user has requested it, send an email acknowledgement
     */
    if($mail_receipt) {
        $user_email_string = $auto_response_message.'<p>A copy of your enquiry is shown below.</p>'.$key_value_table;
        if ($send_email) {
            $status = wp_mail($Form_Builder->form_settings["form_data"]['form-email']['clean'], $auto_response_subject, $user_email_string);// wrap_email($user_response_msg)
        }
    }
    /*
     * Return the html
     */              
    return $this->build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table);
}

private function build_key_value_table($Form_Builder) {
ob_start(); 
?>
    <table style="width:100%">
        <tbody>
            <?php foreach ($Form_Builder->form_settings["form_data"] as $key => $value): ?>
                <?php                 
                    $required = $value['required'];
                    $type = $value['type'];
                ?>
                <tr>
                    <th style="width:30%"><?php echo ucwords(str_replace('-', ' ',substr($key, 5))) ?></th>
                    <td>
                        <?php 
                            if ($value['type']=='select') {
                                echo ucwords(str_replace('-', ' ',$value['clean']));
                            } else {
                                echo $value['clean'];
                            }
                        ?>
                    </td>
                </tr>
               
            <?php endforeach ?>   
        </tbody>
    </table>      
<?php
$html = ob_get_contents();
ob_end_clean();
return $html;
}

private function build_confirmation_output($use_callout=true, $browser_output_header, $auto_response_message, $key_value_table) {
ob_start(); ?>
    <?php if ($use_callout): ?>
        <div id="contact-thank-you">
            <div class="callout secondary" data-closable="slide-out-right">        
    <?php endif ?>

            <h3><?php echo $browser_output_header; ?></h3>
            <p><?php echo $auto_response_message; ?></p>
            <p>A copy of your enquiry is shown below.</p>
            <?php echo $key_value_table; ?>

    <?php if ($use_callout): ?>
                <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>        
    <?php endif;
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