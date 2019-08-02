<?php
@session_start();
require('../../../../wp-load.php');

$config_id = $_POST['config_id'];
foreach($_SESSION['part_number'] as $key=>$part_number)
{
	if($key != 0)
	{
		$main_term = get_term_by('id', $key, 'configuration');
		$sub_term = get_term_by('id', $part_number, 'configuration');
		$subterm_meta = get_option("taxonomy_term_$part_number");        
		if($subterm_meta['presenter_id'] == "changable")
		{
			$name = $main_term->name . " : " . $_SESSION['value_input'];
		} 
		else 
		{
			$name = $main_term->name . " : " . $sub_term->name;
		}
		$data .= "<tr><td class='inner contents'>".$name."</td></tr>";
	}  
	//echo "<pre>"; print_r($data);
}

$current_user = wp_get_current_user();
$from_name = $current_user->user_login;
$from_email = $current_user->user_email;


//$to = "pushppreet@graycelltech.com";
$to = "gurpreet@graycelltech.com";
// $to = get_option('admin_email');
$subject = 'New Product Configuration Request Recevied.';
$body = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!--[if !mso]><!-->
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<!--<![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title></title>		
		<!--[if (gte mso 9)|(IE)]>
		<style type="text/css">
			table {border-collapse: collapse;}
			body {
				margin: 0 !important;
				padding: 0;
				background-color: #ffffff;
			}
			table {
				border-spacing: 0;
				font-family: sans-serif;
				color: #333333;
			}
			td {
				padding: 0;
			}
			img {
				border: 0;
			}
			div[style*="margin: 16px 0"] { 
				margin:0 !important;
			}
			.wrapper {
				width: 100%;
				table-layout: fixed;
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			}
			.webkit {
				max-width: 600px;
				margin: 0 auto;
			}
			.inner {
				padding: 10px;
			}
			p {
				Margin: 0;
			}
			a {
				color: #ee6a56;
				text-decoration: underline;
			}
			.h1 {
				font-size: 21px;
				font-weight: bold;
				Margin-bottom: 18px;
			}
			.h2 {
				font-size: 18px;
				font-weight: bold;
				Margin-bottom: 12px;
			}
			 
			/* One column layout */
			.one-column .contents {
				text-align: left;
			}
			.one-column p {
				font-size: 14px;
				Margin-bottom: 10px;
			}
		</style>
		<![endif]-->
	</head>
	<body>
		<center class="wrapper">
			<div class="webkit">
				<!--[if (gte mso 9)|(IE)]>
				<table width="600" align="left">
					<tr>
						<td>
							<![endif]-->
							<table class="outer" align="left">
								<tr>You have received a new configuration request from user : '.$from_name.'('.$from_email.')</tr>'.$data.'
							</table>
							<!--[if (gte mso 9)|(IE)]>
						</td>
					</tr>
				</table>
				<![endif]-->
			</div>
		</center>
	</body>
	</html>';
$headers[] = 'Content-Type: text/html; charset=UTF-8';
$headers[] = 'From:'.$from_email;

 
wp_mail( $to, $subject, $body, $headers );
if(wp_mail)
{
	echo json_encode(array('final_pass'=>yes,'msg'=>'Your Request has been sent to Admin.'));
} 
else 
{
	echo json_encode(array('final_pass'=>no,'msg'=>'Try Again! Error sending mail.'));
}




?>
