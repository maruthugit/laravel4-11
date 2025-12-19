<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		{{ HTML::style('css/bootstrap.min.css') }}
		<!--[if gte mso 7]><xml>
          <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
          </o:OfficeDocumentSettings>
        </xml><![endif]-->
	</head>
    <body style="background-color: #e6e6e6;">
        <div bgcolor="#FFFFFF" marginwidth="0" marginheight="0">
            <center style="width:100%;background-color:#ffffff;">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                        <tr>
                            <td style="padding:10px" align="center">
                                <table class="jcm-table" width="580" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td bgcolor="#fce30a" style="padding:0px; background-color: rgb(252,227,10);" align="center">
                                                <!--[if mso]>
                                                  <table width="50%"><tr><td><img width="200" src="{{ asset('campaigns/emails/images/voucher-winner-email3_01.jpg') }}" alt="ITEAMS" style="text-align: right; width: 207px; border: 0; text-decoration:none; vertical-align: baseline;"></td></tr></table>
                                                  <div style="display:none">
                                               <![endif]-->
                                                <img 
                                                src="{{ asset('campaigns/emails/images/voucher-winner-email3_01.jpg') }}" alt="" style="text-align: right; max-width: 580px; border: 0; text-decoration:none; vertical-align: baseline;" width="580" height="225" tabindex="0"
                                                >
                                                <!--[if mso]>
                                                    </div>
                                               <![endif]-->
                                               <!--[if mso]>
                                                  <table width="50%"><tr><td><img width="200" src="{{ asset('campaigns/emails/images/voucher-winner-email3_02.jpg') }}" alt="ITEAMS" style="text-align: right; width: 207px; border: 0; text-decoration:none; vertical-align: baseline;"></td></tr></table>
                                                  <div style="display:none">
                                               <![endif]-->
                                                <img src="{{ asset('campaigns/emails/images/voucher-winner-email3_02.jpg') }}" alt="" style="text-align: right; max-width: 580px; border: 0; text-decoration:none; vertical-align: baseline;" width="580" height="321" tabindex="0">
                                                <!--[if mso]>
                                                    </div>
                                               <![endif]-->
                                               <?php if (!empty($coupon_code)) { ?>
                                                <div style="background-color: #358a41; width: 380px; padding: 20px;border-radius: 15px;color: #cfd60e; font-size: 16px; text-decoration: none;">VOUCHER CODE : <strong>{{ $coupon_code }}</strong></div>
                                                <div style="color: red; font-style: italic; font-size: 15px; padding-top: 20px;padding-bottom:10px; font-weight: 500; text-decoration: none;">Valid Before: {{ $valid_to }}</div>
                                                <?php } ?>
                                                <!--[if mso]>
                                                  <table width="50%"><tr><td><img width="200" src="{{ asset('campaigns/emails/images/voucher-winner-email3_04.jpg') }}" alt="ITEAMS" style="text-align: right; width: 207px; border: 0; text-decoration:none; vertical-align: baseline;"></td></tr></table>
                                                  <div style="display:none">
                                               <![endif]-->
                                                <img src="{{ asset('campaigns/emails/images/voucher-winner-email3_04.jpg') }}" alt="" style="text-align: right; max-width: 580px; border: 0; text-decoration:none; vertical-align: baseline;" width="580" height="233" tabindex="0">
                                                <!--[if mso]>
                                                    </div>
                                               <![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="jcm-table" width="580" cellspacing="0" cellpadding="0" border="0" align="center">
                                  <tbody>
                                        <tr>
                                            <td>
                                                <table style="font-family:Helvetica" class="jcm-table" width="580" cellspacing="0" cellpadding="0" border="0">
                                                  <tbody>
                                                    <tr style="background:#000;color:#ffffff;font-family:Roboto,Arial,Helvetica,'sans-serif';font-size:12px">
                                                      <td style="text-align:center" align="center">
                                                        <p style="color:#fff;padding:20px 15px 5px;margin:0">For more information, visit us on <a href="https://www.tmgrocer.com" style="color:#fff" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.tmgrocer.com&amp;source=gmail&amp;ust=1558581004466000&amp;usg=AFQjCNEGBvEisvmKEGLj_ms_DbWRNw6Qmw">https://www.tmgrocer.com</a></p>
                                                        <p style="color:#fff;padding:0 15px;margin:0">or contact us at <a href="mailto:enquiries@tmgrocer.com" style="color:#fff" target="_blank">enquiries@tmgrocer.com</a></p>
                                                        <p style="color:#f0302e;padding:15px 15px 20px;margin:0">Terms &amp; Conditions apply</p>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                              </td>
                        </tr>
                        <tr style="background:#fff" align="center">
                            <td align="center"></td>
                        </tr>
                    </tbody>
                </table>
            </center>
        </div>
	</body>
</html>
