<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		{{ HTML::style('css/bootstrap.min.css') }}
		{{ HTML::style('css/sb-admin-2.css') }}
		{{ HTML::style('font-awesome/css/font-awesome.min.css') }}
	</head>
	<body>
		<div class="col-lg-12">
			<div class="panel panel-default" align="center">
				<div class="panel-heading">
					<h1 class="page-header text-primary"></i>Bulk Import Transaction Report : <?php echo $acc_name; ?>  </h1>
				</div>
				<div class="panel-body">
                                    <div>Hi, this might need your attention</div>
                                    
                                    <div>Execution Datetime: <?php echo $execution_datetime; ?></div>
                                    <div>Total of Records: <?php echo $total_records ?></div>
                                    <p></p>
                                    <p></p>
                                    <p></p>
                                    <div></div>
                                    <p>
                                    <p>
                                    <table>
                                        <tr>
                                            <td style="padding:10px;" valign="top">
                                                 <table border="1">
                                                    <tr>
                                                        <td colspan="2"><strong>Failed Transactions </strong></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px;"><strong>Customer Name</strong></td>
                                                    </tr>
                                                    <?php foreach ($manual_order_list as $key => $value) { ?>
                                                    <tr>
                                                        <td style="padding:5px;"><?php echo $value['buyername']; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </table>
                                            </td>
                                            <td style="padding:10px;" valign="top">
                                                <table border="1">
                                                    <tr>
                                                        <td colspan="3"><strong>Successful Transactions </strong></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px;"><strong>Customer Name</strong></td>
                                                        <td style="padding:5px;"><strong>Transaction ID</strong></td>
                                                    </tr>
                                                    <?php foreach ($transfered_orders as $keyT => $valueT) { ?>
                                                    <tr>
                                                        <td style="padding:5px;"><?php echo $valueT['buyername']; ?></td>
                                                        <td style="padding:5px;"><?php echo $valueT['transactionID']; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                   
                                    </p>
                                    <p><br>Thank you!</p><br>
				</div>
				<div class="panel-footer">
					<style type="text/css">a.link{margin:0;padding:0;border:none;text-decoration:none;}</style> 
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="format-detection" content="telephone=no"> <br /><br /> 
					<table id="sig" width='670' cellspacing='0' cellpadding='0' border-spacing='0' style="width:670px;margin:0;padding:10;" bgcolor='#f5f5f5'>
						<tr>
							<td width="166" style="width:166px;margin:0;padding:0;">
								<a href='http://www.tmgrocer.com' title="Jocom" style="border:none;text-decoration:none;">
									<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/logo_166.png" alt="Jocom" style="border:none;border-right:solid #00ab46;width:166px;height:79px;">
								</a>
							</td> 
							<td width="10" style="width:10px;min-width:10px;max-width:10px;margin:0;padding:0;">&nbsp;</td> 
							<td style="margin:0;padding:0;">
								<table id="sig2" cellspacing='0' cellpadding='0' border-spacing='0' style="padding:0;margin:0;font-family:'Lucida Grande',sans-serif;font-size:10px;line-height:10px;color:#b0b0b0;border-collapse:collapse;-webkit-text-size-adjust:none;">
									<tr style="margin:0;padding:0;">
										<td style="margin:0;padding:0;font-family:'Lucida Grande',sans-serif;white-space:nowrap;">
											<strong>
												<a href="mailto:accounts@tmgrocer.com" style="border:none;text-decoration:none;color:#049cdb;">
												<span style="color:#000">tmGrocer</span>
												</a>
											</strong>
											<!-- <span style="color:#00ab46">
												<strong> &#124; </strong>
											</span>
											<span style="color:#000">Senior Web Developer</span> -->
										</td>
									</tr> 
									<tr>
										<td height='5' style="height:5px;font-size:5px;mso-line-height-rule:exactly;line-height:5px;">&nbsp;</td>
									</tr> 
									<tr style="margin:0;padding:0;">
										<td style="margin:0;padding:0;font-family:'Lucida Grande',sans-serif;white-space:nowrap;">
											<a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" title="map" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_map.png" alt="Map" style="border:none;width:16px;height:16px;vertical-align:middle;" />
											</a>&nbsp;&nbsp;
											<span style="color:#000">tmGrocer </span>
											<span style="color:#00ab46"><strong> &#124; </strong></span>
											<a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" style="border:none;text-decoration:none;color:#b0b0b0;">
												<span style="color:#000">10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Selangor, Malaysia.</span>
											</a>
										</td>
									</tr>
									<tr>
										<td height='5' style="height:5px;font-size:5px;mso-line-height-rule:exactly;line-height:5px;">&nbsp;</td>
									</tr>
									<tr style="margin:0;padding:0;">
										<td style="margin:0;padding:0;font-family:'Lucida Grande',sans-serif;white-space:nowrap;">
											<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_tel.png" alt="Phone No" style="border:none;width:16px;height:16px;vertical-align:middle;">&nbsp;&nbsp;
											<a href='tel:+60367348744' title="Call Us" style="border:none;text-decoration:none;">
												<span style="color:#000">603 6734 8744</span>
											</a>&nbsp;&nbsp;
											<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_fax.png" alt="Fax No" style="border:none;width:16px;height:16px;vertical-align:middle;">&nbsp;&nbsp;
											<span style="color:#000">603 6734 8744</span>
										</td>
									</tr>
									<tr>
										<td height='5' style="height:5px;font-size:5px;mso-line-height-rule:exactly;line-height:5px;">&nbsp;</td>
									</tr>
									<tr style="margin:0;padding:0;">
										<td style="margin:0;padding:0;font-family:'Lucida Grande',sans-serif;white-space:nowrap;">
											<a href='http://www.tmgrocer.com/' title="tmGrocer" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_web_limegreen.png" alt="Facebook" style="border:none;width:77px;height:16px;">
											</a>&nbsp;&nbsp;
											<a href='mailto:accounts@tmgrocer.com' title="Email Me" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_email_limegreen.png" alt="Email" style="border:none;width:77px;height:16px;">
											</a>&nbsp;&nbsp;
											<a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" title="map" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/but_map_limegreen.png" alt="Map" style="border:none;width:55px;height:16px;">
											</a>&nbsp;&nbsp;
											<a href='http://www.facebook.com/tmGrocer' title="tmGrocer on Facebook" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/facebook16.png" alt="Facebook" style="border:none;width:16px;height:16px;">
											</a>&nbsp;&nbsp;
											<a href='http://www.twitter.com/tmGrocer' title="tmGrocer on Twitter" style="border:none;text-decoration:none;">
												<img moz-do-not-send="true" src="http://tmgrocer.com/email/img/twitter16.png" alt="Twitter" style="border:none;width:16px;height:16px;">
											</a>
										</td>
									</tr>
								</table>
							</td>
						<tr>
					</table><br />&nbsp;
				</div>
			</div>	
		</div>
	</body>
</html>