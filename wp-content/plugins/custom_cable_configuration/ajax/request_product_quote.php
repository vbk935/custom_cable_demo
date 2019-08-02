<?php
@session_start();
require('../../../../wp-load.php');

//$user_name = 'Guest';
//$user_email = '';
// Fetching the logged-in user details
$current_user = wp_get_current_user();
if ( 0 != $current_user->ID ) {
    // Logged in.
		$user_name = $current_user->user_login;
		$user_email = "(". $current_user->user_email .")";
		$user_phone = get_user_meta( $current_user->ID, 'phone_number', true );
} else {
	// If user is not logged in
	$user_name = $_POST['contact_name'];
	$user_email = $_POST['contact_email'];
	$user_phone = $_POST['contact_phno'];
}

$data = "<tr><td colspan='2'>Product Quote Requested</td></tr>";
$data .= "<tr><td colspan='2'>Requested by: ". $user_name ." | ". $user_email . " | " . $user_phone ." </td></tr>";
$data .= "<tr><td colspan='2'></td></tr>";
$data .= "<tr><td colspan='2'></td></tr>";

$data .= "<tr><td colspan='2'>Requested Configuration</td></tr>";
$data .= "<tr><td class='inner contents'>Product part Number</td><td class='inner contents'>".$_POST['partNumber']."</td></tr>";

$product_data = $_POST['productData'];

$confData = '';
foreach($product_data as $key=>$value)
{
		$confData .= "<tr>";
		$confData .= "<td>".$value['configName']."</td>";
		$confData .= "<td>".$value['cguiComponentName']."</td>";
		$confData .= "</tr>";
}

$from_name = $current_user->user_login;
$from_email = $current_user->user_email;


$to = get_bloginfo('admin_email');
// $to = "shivali@graycelltech.com";
// $to = get_bloginfo('admin_email');
//$to = "pushppreet@graycelltech.com";

$subject = 'New Product Configuration Quote Request Recevied.';
$email_heading = 'Product Quote Requested';

// Load colours
$bg              = get_option( 'woocommerce_email_background_color' );
$body            = get_option( 'woocommerce_email_body_background_color' );
$base            = get_option( 'woocommerce_email_base_color' );
$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'woocommerce_email_text_color' );

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );

$dir = is_rtl() ? "rtl" : "ltr";
$textAlign = is_rtl() ? "right" : "left";
$bodyMarginLabel = is_rtl() ? "rightmargin" : "leftmargin";

$charset = bloginfo('charset');
$title = get_bloginfo( 'name', 'display' );
$bg_color = esc_attr( $bg );
$template_container_bg_color = esc_attr($body);
$template_container_border_color = esc_attr($bg_darker_10);
$template_header_bg_color = esc_attr($base);
$template_header_color = esc_attr($base_text);
$template_header_h1_color = esc_attr($base_text);
$template_footer_color = esc_attr($base_lighter_40);
$body_content_bg_color = esc_attr($body);
$body_content_inner_color = esc_attr( $text_lighter_20 );
$td_color = esc_attr( $text_lighter_20 );
$td_border_color = esc_attr($body_darker_10);
$text_color = esc_attr( $text );
$link_color = esc_attr( $base );
$h1_color = esc_attr( $base );
$h1_text_shadow_color = esc_attr( $base_lighter_20 );
$h2_color = esc_attr( $base );
$h3_color = esc_attr( $base );
$a_color = esc_attr( $base );
$part_number = $_POST['partNumber'];
$footer_text = wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) );

/*
$body = <<<EOD
<!DOCTYPE html>
<html dir="{$dir}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
		<title>{$title}</title>
		<style type="text/css" rel="stylesheet">
				#wrapper {
					background-color: {$bg_color};
					margin: 0;
					padding: 70px 0 70px 0;
					-webkit-text-size-adjust: none !important;
					width: 100%;
				}

				#template_container {
					box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
					background-color: {$template_container_bg_color};
					border: 1px solid {$template_container_border_color};
					border-radius: 3px !important;
				}

				#template_header {
					background-color: {$template_header_bg_color};
					border-radius: 3px 3px 0 0 !important;
					color: {$template_header_color};
					border-bottom: 0;
					font-weight: bold;
					line-height: 100%;
					vertical-align: middle;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				}

				#template_header h1,
				#template_header h1 a {
					color: {$template_header_h1_color};
				}

				#template_footer td {
					padding: 0;
					-webkit-border-radius: 6px;
				}

				#template_footer #credit {
					border:0;
					color: {$template_footer_color};
					font-family: Arial;
					font-size:12px;
					line-height:125%;
					text-align:center;
					padding: 0 48px 48px 48px;
				}

				#body_content {
					background-color: {$body_content_bg_color};
				}

				#body_content table td {
					padding: 48px;
				}

				#body_content table td td {
					padding: 12px;
				}

				#body_content table td th {
					padding: 12px;
				}

				#body_content p {
					margin: 0 0 16px;
				}

				#body_content_inner {
					color: {$body_content_inner_color};
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 14px;
					line-height: 150%;
					text-align: {$textAlign};
				}

				.td {
					color: {$td_color};
					border: 1px solid {$td_border_color};
				}

				.text {
					color: {$text_color};
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				}

				.link {
					color: {$link_color};
				}

				#header_wrapper {
					padding: 36px 48px;
					display: block;
				}

				h1 {
					color: {$h1_color};
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 30px;
					font-weight: 300;
					line-height: 150%;
					margin: 0;
					text-align: {$textAlign};
					text-shadow: 0 1px 0 {$h1_text_shadow_color};
					-webkit-font-smoothing: antialiased;
				}

				h2 {
					color: {$h2_color};
					display: block;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 18px;
					font-weight: bold;
					line-height: 130%;
					margin: 16px 0 8px;
					text-align: {$textAlign};
				}

				h3 {
					color: {$h3_color};
					display: block;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 16px;
					font-weight: bold;
					line-height: 130%;
					margin: 16px 0 8px;
					text-align: {$textAlign};
				}

				a {
					color: {$a_color};
					font-weight: normal;
					text-decoration: underline;
				}

				img {
					border: none;
					display: inline;
					font-size: 14px;
					font-weight: bold;
					height: auto;
					line-height: 100%;
					outline: none;
					text-decoration: none;
					text-transform: capitalize;
				}
		</style>
	</head>
	<body {$bodyMarginLabel}="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<div id="wrapper" dir="{$dir}">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
							<tr>
								<td align="center" valign="top">
									<!-- Header -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header">
										<tr>
											<td id="header_wrapper">
												<h1>{$email_heading}</h1>
											</td>
										</tr>
									</table>
									<!-- End Header -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Body -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
										<tr>
											<td valign="top" id="body_content">
												<!-- Content -->
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top">
															<div id="body_content_inner">
																	<h2>Requested by: {$user_name}  {$user_email}  {$user_phone}</h2>
																	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;" border="1">
																		<tbody>
																			<tr><td colspan="2"><b>Configuration Details</b></td></tr>
																			<tr><td class="inner contents">Product Part Number</td><td class="inner contents">{$part_number}</td></tr>{$confData}</tbody>
																	</table>
															</div>
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Footer -->
									<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
										<tr>
											<td valign="top">
												<table border="0" cellpadding="10" cellspacing="0" width="100%">
													<tr>
														<td colspan="2" valign="middle" id="credit">{$footer_text}</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- End Footer -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
EOD;*/

ob_start();
$template_path = ABSPATH ."wp-content/plugins/custom_cable_configuration/includes/template/woocommerce/request-product-quote.php";
include($template_path);
$body = ob_get_clean();

$headers[] = 'Content-Type: text/html; charset=UTF-8';
$headers[] = 'From:'.$from_email;

if(wp_mail( $to, $subject, $body, $headers )) {
	$response_data = array(
			'status'=>1,
			'msg'=>'Thank you, a representative will reach out to you shortly.', 
			'sent_to'=> $to, 
			'phone_number'=> $user_phone 
		);
}
else {
	$response_data = array(
			'status'  =>0,
			'msg'     => 'Something went wrong! Please try again later', 
			'sent_to' => $to, 
			'phone_number'=> $user_phone
		);
}
ob_get_clean();
wp_send_json($response_data);
