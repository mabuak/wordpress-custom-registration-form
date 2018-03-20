<?php
/**
 * Plugin Name: Tailor made form
 * Description: This plugins for tailor made application development registration form
 * Version: 0.0.1
 * Author: Fachruzi Ramadhan
 * Author URI: http://dhanhost.com
 */

// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'taylor-form-ajax', 'TFAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// The function to add js in front page wordpress
function my_script() {
	// embed the javascript file that makes the AJAX request
	wp_enqueue_script( 'taylor-form-ajax', plugin_dir_url( __FILE__ ) . 'js/dt-taylor-form.js', array( 'jquery' ) );
}

add_action( 'wp_enqueue_scripts', 'my_script' );

// The function for display the form
function taylor_form( $name, $phone, $email, $company, $website, $sales_revenue, $message ) {
	echo '
	<style>
	.required {
		color: #ff7d7d;
	    position: relative;
	    top: -4px;
	}
	</style>
	';
	
	echo '
    <form class="taylor-form dt-taylor-form" action="' . $_SERVER['REQUEST_URI'] . '" method="post">
	<input type="hidden" name="send_message">
    <div class="form-fields">
	<label for="name">Full Name <span class="required">*</span></label>
    <input type="text" class="validate[required]" name="name" value="' . ( isset( $_POST['name'] ) ? $name : null ) . '">
	
	<label for="phone">Phone <span class="required">*</span></label>
    <input type="text" class="validate[required]" name="phone" value="' . ( isset( $_POST['phone'] ) ? $phone : null ) . '">
	
	<label for="email">Email <span class="required">*</span></label>
    <input type="text" class="validate[required,custom[email]]" name="email" value="' . ( isset( $_POST['email'] ) ? $email : null ) . '">
	
	<label for="company">Company Name</label>
    <input type="text" name="company" value="' . ( isset( $_POST['company'] ) ? $company : null ) . '">
	
	<label for="website">Website</label>
    <input type="text" name="website" value="' . ( isset( $_POST['website'] ) ? $website : null ) . '">
    
	<label for="sales_revenue">Monthly Sales Revenue</label>
    <input type="text" name="sales_revenue" value="' . ( isset( $_POST['sales_revenue'] ) ? $sales_revenue : null ) . '">
    
	<label for="message">Message <span class="required">*</span></label>
    <textarea name="message" class="validate[required]">' . ( isset( $_POST['message'] ) ? $message : null ) . '</textarea>
	
	</div>
	<input class="dt-btn dt-btn-m dt-btn-submit" style="margin-top: 1%" type="submit" name="submit" value="Learn More"/>
	</form>
    ';
}

// The function for send email
function taylor_send_email() {
	
	$name          = sanitize_user( $_POST['fields']['name'] );
	$phone         = esc_attr( $_POST['fields']['phone'] );
	$email         = sanitize_email( $_POST['fields']['email'] );
	$company       = esc_url( $_POST['fields']['company'] );
	$website       = sanitize_text_field( $_POST['fields']['website'] );
	$sales_revenue = sanitize_text_field( $_POST['fields']['sales_revenue'] );
	$message       = sanitize_text_field( $_POST['fields']['message'] );
	
	$to      = get_option( 'admin_email' );
	$subject = 'TAILOR MADE APPLICATION DEVELOPMENT FORM';
	$headers = "From: EOASOLUTIONS <" . $email . "> \r\n";
	
	$messageTemp = "Name : $name \nPhone : $phone \nEmail : $email \nCompany : $company \nWebsite : $website \nSales Revenue : $sales_revenue \nMessage : \n$message \n\n\n";
	
	//Send Email...
	wp_mail( $to, $subject, $messageTemp, $headers );
	
	//Return Back The JSON
	wp_send_json( array( 'success' => true, 'errors' => 'Thank you, We will contact you immediately.' ), 200 );
}

// The callback function that will replace [book]
function taylor_form_shortcode() {
	
	ob_start();
	
	taylor_form( $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['company'], $_POST['website'], $_POST['sales_revenue'], $_POST['message'] );
	
	return ob_get_clean();
}

// The callback for ajax handler
function taylor_ajax_handler() {
	
	if ( isset( $_POST["action"] ) ) {
		taylor_send_email();
		
		wp_die();
	}
}

add_action( 'wp_ajax_nopriv_taylor_ajax_handler', 'taylor_ajax_handler' );
add_action( 'wp_ajax_taylor_ajax_handler', 'taylor_ajax_handler' );

// Register a new shortcode: [cr_taylor_form]
add_shortcode( 'cr_taylor_form', 'taylor_form_shortcode' );