<?php

function UPCP_Product_Inquiry_Form() {
	$Inquiry_Plugin = get_option("UPCP_Inquiry_Plugin");
	if ($Inquiry_Plugin == 'WPForms') {UPCP_WPForms_Inquiry_Form();}
	else {UPCP_CF7_Inquiry_Form();}
}

function UPCP_CF7_Inquiry_Form() {
	$plugin = "contact-form-7/wp-contact-form-7.php";
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$CF_7_Installed = is_plugin_active($plugin);

	if ($CF_7_Installed) {
		$Admin_Email = get_option('admin_email');
		$Blogname = get_option('blogname');
		$Site_URL = get_bloginfo('siteurl');

		$UPCP_Contact_Form = get_page_by_path('upcp-product-inquiry-form', OBJECT, 'wpcf7_contact_form');

		if ($UPCP_Contact_Form) {
			$user_update = array("Message_Type" => "Upcate", "Message" => "Inquiry form already exists.");
			return $user_update;
		}

		$post = array(
			'post_name' => 'upcp-product-inquiry-form',
			'post_title' => 'UPCP Inquiry Form',
			'post_type' => 'wpcf7_contact_form',
			'post_content' => 
'<p>Your Name (required)<br />
    [text* your-name] </p>
				
<p>Your Email (required)<br />
    [email* your-email] </p>

<p>Inquiry Product Name<br />
    [text product-name "%PRODUCT_NAME%"] </p>

<p>Your Message<br />
    [textarea your-message] </p>

<p>[submit "Send"]</p>
Product Inquiry E-mail
[your-name] <' . $Admin_Email . '>
From: [your-name] <[your-email]>
Interested Product: [product-name]

Message Body:
[your-message]

--
This e-mail was sent from a contact form on ' . $Blogname . ' (' . $Site_URL . ')
' . $Admin_Email . '
Reply-To: [your-email]

0
0

[your-subject]
' . $Blogname . ' <' . $Admin_Email . '>
Message Body:
[your-message]

--
This e-mail was sent from a contact form on ' . $Blogname . ' (' . $Site_URL . ')
[your-email]
Reply-To: ' . $Admin_Email . '

0
0
Your message was sent successfully. Thanks.
Failed to send your message. Please try later or contact the administrator by another method.
Validation errors occurred. Please confirm the fields and submit it again.
Failed to send your message. Please try later or contact the administrator by another method.
Please accept the terms to proceed.
Please fill in the required field.
This input is too long.
This input is too short.
			');
		
		$insert_result = wp_insert_post( $post);

		if ($insert_result != 0) {
				$mail_array = array(
				'subject' => 'Product Inquiry E-mail',
				'sender' => $Blogname . ' <' . $Admin_Email . '>',
				'body' => 'From: [your-name] <[your-email]>
Interested Product: [product-name]

Message Body:
[your-message]

--
This e-mail was sent from a contact form on ' . $Blogname . ' (' . $Site_URL . ')',
				'recipient' => $Admin_Email,
				'additional_headers' => 'Reply-To: [your-email]',
				'attachments' => '',
				'use_html' => 0,
				'exclude_blank' => 0
			);

			add_post_meta($insert_result, "_mail", $mail_array);
			add_post_meta($insert_result, "_form", 
'<p>Your Name (required)<br />
    [text* your-name] </p>
				
<p>Your Email (required)<br />
    [email* your-email] </p>

<p>Inquiry Product Name<br />
    [text product-name "%PRODUCT_NAME%"] </p>

<p>Your Message<br />
    [textarea your-message] </p>

<p>[submit "Send"]</p>
			');
			add_post_meta($insert_result, "_mail_2", $mail_array);
			add_post_meta($insert_result, "_messages", array(
				"mail_sent_ok",
				"Your message was sent successfully. Thanks.",
				"mail_sent_ng",
				"Failed to send your message. Please try later or contact the administrator by another method.",
				"validation_error",
				"Validation errors occurred. Please confirm the fields and submit it again.",
				"spam",
				"Failed to send your message. Please try later or contact the administrator by another method.",
				"accept_terms",
				"Please accept the terms to proceed.",
				"invalid_required",
				"Please fill in the required field.",
				"invalid_too_long",
				"This input is too long.",
				"invalid_too_short",
				"This input is too short."
				)
			);

			add_post_meta($insert_result, "_additional_settings", '');
			add_post_meta($insert_result, "_locale", 'en_US');
		}

		if ($insert_result != 0) {$user_update = array("Message_Type" => "Update", "Message" => "Product inquiry form successfully created.");}
		else {$user_update = array("Message_Type" => "Error", "Message" => "Inquiry form could not be created.");}
	}
	else {
		$user_update = array("Message_Type" => "Error", "Message" => "Contact Form 7 must be activated.");
	}	

	return $user_update;
}

function UPCP_WPForms_Inquiry_Form() {
	$plugin = "wpforms/wpforms.php";
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$WPForms_Installed = is_plugin_active("wpforms/wpforms.php") ? true : is_plugin_active("wpforms-lite/wpforms.php");

	if ($WPForms_Installed) {

		$UPCP_Contact_Form = get_page_by_path('upcp-wp-forms-product-inquiry-form', OBJECT, 'wpforms');

		if ($UPCP_Contact_Form) {
			$user_update = array("Message_Type" => "Upcate", "Message" => "Inquiry form already exists.");
			return $user_update;
		}

		$post = array(
			'post_name' => 'upcp-wp-forms-product-inquiry-form',
			'post_title' => 'UPCP Inquiry Form',
			'post_type' => 'wpforms',
			'post_status' => 'publish',
			'post_content' => 'placeholder'
		);
		$insert_result = wp_insert_post($post);
		
		if ($insert_result != 0) {
			$update = array(
				'ID' => $insert_result,
				'post_content' => '{"id":"' . $insert_result . '","field_id":5,"fields":{"1":{"id":"1","type":"text","label":"Your Name","description":"","required":"1","size":"medium","placeholder":"","default_value":"","css":"","input_mask":""},"3":{"id":"3","type":"email","label":"Your Email","description":"","required":"1","size":"medium","placeholder":"","confirmation_placeholder":"","default_value":"","css":""},"2":{"id":"2","type":"text","label":"Inquiry Product Name","description":"","size":"medium","placeholder":"","default_value":"%PRODUCT_NAME%","css":"","input_mask":""},"4":{"id":"4","type":"textarea","label":"Your Message","description":"","size":"medium","placeholder":"","css":""}},"settings":{"form_title":"Product Inquiry E-mail","form_desc":"","form_class":"","submit_text":"Send","submit_text_processing":"Sending...","submit_class":"","honeypot":"1","notification_enable":"1","notifications":{"1":{"notification_name":"Default Notification","email":"{admin_email}","subject":"New Blank Form Entry","sender_name":"Demo Theme Test Setup","sender_address":"{admin_email}","replyto":"","message":"{all_fields}"}},"confirmation_type":"message","confirmation_message":"Thanks for inquiring! We will be in touch with you shortly.","confirmation_message_scroll":"1","confirmation_page":"11573","confirmation_redirect":""},"meta":{"template":"blank"}}'
			);
		}
		
		wp_update_post($update);

	}
}

function UPCP_Send_Inquiry_Submission_Emails($contact_form) {
	$Inquiry_Form_Email = get_option("UPCP_Inquiry_Form_Email");

	$Submission = WPCF7_Submission::get_instance();
	$Data = $Submission->get_posted_data();
	
	$UPCP_Contact_Form = get_page_by_path('upcp-product-inquiry-form', OBJECT, 'wpcf7_contact_form');

	$Form_ID = $Data['_wpcf7']; 
	if ($Form_ID != $UPCP_Contact_Form->ID or $Inquiry_Form_Email == 0) {return;}

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin = "ultimate-wp-mail/Main.php";
	$UWPM_Installed = is_plugin_active($plugin);

	if ($UWPM_Installed) {
		$Params = array(
			'Email_ID' => $Inquiry_Form_Email,
			'Email_Address' => $Data['your-email'],
			'Name' => $Data['your-name'],
			'Products' => $Data['product-name']
		);

		EWD_UWPM_Send_Email_To_Non_User($Params);
	}
}
add_action('wpcf7_mail_sent', 'UPCP_Send_Inquiry_Submission_Emails');

function EWD_UPCP_Add_UWPM_Element_Sections() {
	if (function_exists('uwpm_register_custom_element_section')) {
		uwpm_register_custom_element_section('ewd_upcp_uwpm_elements', array('label' => 'Product Catalog Tags'));
	}
}
add_action('uwpm_register_custom_element_section', 'EWD_UPCP_Add_UWPM_Element_Sections');

function EWD_UPCP_Add_UWPM_Elements() {
	if (function_exists('uwpm_register_custom_element')) {

		uwpm_register_custom_element('ewd_upcp_inquiry_name', 
			array(
				'label' => 'Inquiry Sender Name',
				'callback_function' => 'EWD_UPCP_UWPM_Inquiry_Name',
				'section' => 'ewd_upcp_uwpm_elements'
			)
		);
		uwpm_register_custom_element('ewd_upcp_inquiry_email', 
			array(
				'label' => 'Inquiry Sender Email',
				'callback_function' => 'EWD_UPCP_UWPM_Inquiry_Email',
				'section' => 'ewd_upcp_uwpm_elements'
			)
		);
		uwpm_register_custom_element('ewd_upcp_inquiry_products', 
			array(
				'label' => 'Inquiry Products',
				'callback_function' => 'EWD_UPCP_UWPM_Inquiry_Products',
				'section' => 'ewd_upcp_uwpm_elements'
			)
		);
	}
}
add_action('uwpm_register_custom_element', 'EWD_UPCP_Add_UWPM_Elements');

function EWD_UPCP_UWPM_Inquiry_Name($Params, $User) {
	if (!isset($Params['Name'])) {return;}

	return $Params['Name'];
}

function EWD_UPCP_UWPM_Inquiry_Email($Params, $User) {
	if (!isset($Params['Email_Address'])) {return;}

	return $Params['Email_Address'];
}

function EWD_UPCP_UWPM_Inquiry_Products($Params, $User) {
	if (!isset($Params['Products'])) {return;}

	return $Params['Products'];
}
?>