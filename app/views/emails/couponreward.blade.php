<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		{{ HTML::style('css/bootstrap.min.css') }}
		{{ HTML::style('css/sb-admin-2.css') }}
		{{ HTML::style('font-awesome/css/font-awesome.min.css') }}
                <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	</head>
        <body style="background-color: #e6e6e6;">
            <div class="col-lg-12" style=" padding: 40px;">
                    <div class="row" align="center" style="width:580px;text-align: center;margin-top: 20px;margin: 0 auto;-webkit-box-shadow: 0px -1px 21px 0px rgba(179,179,179,1);
-moz-box-shadow: 0px -1px 21px 0px rgba(179,179,179,1);
box-shadow: 0px -1px 21px 0px rgba(179,179,179,1);">
<!--                        <div class="col-md-12 col-xs-12" style="height: 200px;background-color: #6bd295;">
                            <div style="    color: #fff;font-size: 25px;padding-top: 20px;">
                                <i class="fas fa-gift fa-3x"></i>
                                <br>A reward for being awesome customer !!</div>
                        </div>-->
                        <div class="col-md-12 col-xs-12" style="height: 200px;background-color: #6bd295;padding: 0px;">
                            <img class="deviceWidth" src="https://www.tmgrocer.com/images/asset/reward.png" alt="" border="0" style="display: block; border-radius: 4px;">
                        </div>
                        
                       
                        <div class="col-md-12 col-xs-12" style="min-height: 400px;background-color: #f3f3f3;">
                            <div style="padding:10px;">
                                <div style="text-align:center;color: #717171;">
                                    <h2>Congratulation!</h2>
                                    <h2><?php echo $wording_text; ?></h2>
                                </div>
                                <?php if (isset($coupon_code)) { ?>       
                                <div style="background-color: #dcdcdc;font-size: 20px;padding: 10px;border: dashed 2px #b7b9b8;margin-top: 50px;">
                                    VOUCHER CODE : <?php echo $coupon_code; ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12" style="height: 100px;
    background-color: #ffffff;
    border-bottom: solid 3px #6bd295;
    border-top: solid 1px #e2e2e2;padding: 10px;">
                            <table>
                                <tr>
                                    <td><img src="http://tmgrocer.com/images/logo-jocom.png"></td>
                                    <td style="text-align: left;padding-left:  10px;">Tien Ming Distribution Sdn Bhd<br>10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Selangor, Malaysia..<br><a href="https://www.tmgrocer.com/">www.tmgrocer.com</a></td></tr>
                            </table>
                        </div>
                    </div>	
		</div>
	</body>
</html>