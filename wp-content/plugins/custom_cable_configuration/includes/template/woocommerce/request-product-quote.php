<!DOCTYPE html>
<html dir="<?php echo $dir; ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />
		<title><?php echo $title; ?></title>
		<style type="text/css" rel="stylesheet">
				#wrapper {
					background-color: <?php echo $bg_color; ?>;
					margin: 0;
					padding: 70px 0 70px 0;
					-webkit-text-size-adjust: none !important;
					width: 100%;
				}

				#template_container {
					box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
					background-color: <?php echo $template_container_bg_color; ?>;
					border: 1px solid <?php echo $template_container_border_color; ?>;
					border-radius: 3px !important;
				}

				#template_header {
					background-color: <?php echo $template_header_bg_color; ?>;
					border-radius: 3px 3px 0 0 !important;
					color: <?php echo $template_header_color; ?>;
					border-bottom: 0;
					font-weight: bold;
					line-height: 100%;
					vertical-align: middle;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				}

				#template_header h1,
				#template_header h1 a {
					color: <?php echo $template_header_h1_color; ?>;
				}

				#template_footer td {
					padding: 0;
					-webkit-border-radius: 6px;
				}

				#template_footer #credit {
					border:0;
					color: <?php echo $template_footer_color; ?>;
					font-family: Arial;
					font-size:12px;
					line-height:125%;
					text-align:center;
					padding: 0 48px 48px 48px;
				}

				#body_content {
					background-color: <?php echo $body_content_bg_color; ?>;
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
					color: <?php echo $body_content_inner_color; ?>;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 14px;
					line-height: 150%;
					text-align: <?php echo $textAlign; ?>;
				}

				.td {
					color: <?php echo $td_color; ?>;
					border: 1px solid <?php echo $td_border_color; ?>;
				}

				.text {
					color: <?php echo $text_color; ?>;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				}

				.link {
					color: <?php echo $link_color; ?>;
				}

				#header_wrapper {
					padding: 36px 48px;
					display: block;
				}

				h1 {
					color: <?php echo $h1_color; ?>;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 30px;
					font-weight: 300;
					line-height: 150%;
					margin: 0;
					text-align: <?php echo $textAlign; ?>;
					text-shadow: 0 1px 0 <?php echo $h1_text_shadow_color; ?>;
					-webkit-font-smoothing: antialiased;
				}

				h2 {
					color: <?php echo $h2_color; ?>;
					display: block;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 18px;
					font-weight: bold;
					line-height: 130%;
					margin: 16px 0 8px;
					text-align: <?php echo $textAlign; ?>;
				}

				h3 {
					color: <?php echo $h3_color; ?>;
					display: block;
					font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
					font-size: 16px;
					font-weight: bold;
					line-height: 130%;
					margin: 16px 0 8px;
					text-align: <?php echo $textAlign; ?>;
				}

				a {
					color: <?php echo $a_color; ?>;
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
	<body <?php echo $bodyMarginLabel; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<div id="wrapper" dir="<?php echo $dir; ?>">
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
												<h1><?php echo $email_heading; ?></h1>
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
																	<h2>Requested by: <?php echo $user_name; ?>  <?php echo $user_email; ?>  <?php echo $user_phone; ?></h2>
																	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;" border="1">
																		<tbody>
																			<tr><td colspan="2"><b>Configuration Details</b></td></tr>
																			<tr><td class="inner contents">Product Part Number</td><td class="inner contents"><?php echo $part_number; ?></td></tr><?php echo $confData; ?></tbody>
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
														<td colspan="2" valign="middle" id="credit"><?php echo $footer_text; ?></td>
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