<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/sb-admin-2.css') }}
        {{ HTML::style('font-awesome/css/font-awesome.min.css') }}
       <style>
        table.item, th.item, td.item {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th.item,td.item{
            padding: 6px 15px;
            text-align: left;
        }
        </style>
    </head>
    <body>
        <div class="col-lg-12">
            <div class="panel panel-default" align="center">
                <div class="panel-heading">
                    <img moz-do-not-send="true" src="http://tmgrocer.com/email/img/logo.png" alt="tmGrocer" width="222" height="106">
                </div>
                <div class="panel-body" align="left">
                    <span align="left">Dear {{$company_name}},</span><br>
                </div>
                <br><br>
                <div class="panel-body" align="left">
                    <table class="item">
                        <tr>
                            <th class="item"><b>SKU</b></th>
                            <th class="item"><b>Item Name</b></th>
                            <th class="item"><b>Label</b></th>
                            <th class="item"><b>QTY</b></th>
                        </tr>
                        <?php foreach ($product as $key => $value) {  ?>
                        <tr>
                            <td class="item" ><?=$value['sku'];?></td>
                            <td class="item" ><?=$value['name'];?></td>
                            <td class="item" ><?=$value['price_label']; ?></td>
                            <td class="item" ><?=$value['unit']; ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <br><br>
                <div class="panel-body" align="left">
                    <span align="left">The transaction ID will be {{$transaction_id}} for references.</span><br>
                </div>
                <br><br>
                <div class="panel-footer" align="left">
                    <style type="text/css">a.link{margin:0;padding:0;border:none;text-decoration:none;}</style> 
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <table id="sig" width="670" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="font-family: Times; widows: 1; width: 670px; margin: 0px; padding: 10px" class="">
    <tbody class="">
        <tr class="">
            <td width="166" style="width: 166px; margin: 0px; padding: 0px" class="">
                <a href="http://www.tmgrocer.com/" title="tmGrocer" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/20-01.png" alt="tmGrocer" style="border: none" class="">
                </a>
            </td>
            <td width="10" style="width: 10px; min-width: 10px; max-width: 10px; margin: 0px; padding: 0px" class="">&nbsp;</td>
            <td style="margin: 0px; padding: 0px" class="">
                <table id="sig2" cellspacing="0" cellpadding="0" style="padding: 0px; margin: 0px; font-family: 'Lucida Grande', sans-serif; font-size: 10px; line-height: 10px; color: rgb(176, 176, 176); border-collapse: collapse; min-width: 560px" class="">
                    <tbody class="">
                        <tr style="margin: 0px; padding: 0px" class="">
                            <td style="margin: 0px; padding: 0px; white-space: nowrap" class="">
                                
                                    <a href="mailto:accounts@tmgrocer.com" style="border: none; text-decoration: none; color: rgb(4, 156, 219)" class="" onclick="return rcmail.command('compose','maruthu@tmgrocer.com',this)" rel="noreferrer">
                                    <span style="color: rgb(0, 0, 0); font-size: small" class="">Accounts Department of tmGrocer</span></a>
                                

                                <div style="width: 23px; margin-top: 7px; border-bottom-width: 1.5px !important; border-bottom-style: solid !important; border-bottom-color: rgb(158, 158, 158) !important" class=""></div>
                            </td>
                        </tr>
                        <tr class="">
                            <td height="5" style="height: 5px; font-size: 5px; line-height: 5px" class="">&nbsp;</td>
                        </tr>
                        <tr style="margin: 0px; padding: 0px" class="">
                            <td style="margin: 0px; padding: 0px; white-space: nowrap" class="">
                                <a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" title="map" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/1-01.png" alt="Map" style="border: none; vertical-align: middle" class="">&nbsp;</a>&nbsp;&nbsp;&nbsp;<a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" style="border: none; text-decoration: none; color: rgb(176, 176, 176)" class="" target="_blank" rel="noreferrer"><span style="color: rgb(0, 0, 0)" class="">10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Selangor, Malaysia.</span></a>
                            </td>
                        </tr>
                        <tr class="">
                            <td height="5" style="height: 5px; font-size: 5px; line-height: 5px" class="">&nbsp;</td>
                        </tr>
                        <tr style="margin: 0px; padding: 0px" class="">
                            <td style="margin: 0px; padding: 0px" class=""><img src="http://tmgrocer.com/sig/Signature_icon/2-01.png" alt="Phone No" style="border: none; vertical-align: middle" class="">&nbsp;&nbsp;&nbsp;<a href="tel:+60367348744" title="Call Us" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><span style="color: rgb(0, 0, 0)" class="">60367348744</span>&nbsp;</a>&nbsp;&nbsp;&nbsp;
                                <a href="http://www.tmgrocer.com/" title="tmGrocer" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/4-01.png" alt="tmGrocer" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none">&nbsp;</a>
                                <a href="mailto:accounts@tmgrocer.com" title="Email Me" style="border: none; text-decoration: none" class="" onclick="return rcmail.command('compose','maruthu@tmgrocer.com',this)" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/5-01.png" alt="Email" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none">&nbsp;</a>
                                <a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" title="Map" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/6-01.png" alt="Map" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none">&nbsp;</a>
                                <a href="http://www.facebook.com/tmgrocer" title="tmGrocer on Facebook" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/7-01.png" alt="Facebook" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none">&nbsp;</a>
                                <a href="http://www.twitter.com/tmgrocer" title="tmGrocer on Twitter" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/8-01.png" alt="Twitter" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none">&nbsp;</a>
                                </a>
                                <a href="https://play.google.com/store/apps/details?id=com.jocomit.twenty37" title="Wordpress" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/android-app.png" alt="Wordpress" class="img-allign" style="margin-left: 5px; margin-bottom: -4px; border: none; float: right; margin-top: -10px">
                                </a>
                            </td>
                        </tr>
                        <tr class="">
                            <td height="5" style="height: 5px; font-size: 5px; line-height: 5px" class="">
                                <div class="border-top" style="width: 441px; margin-bottom: 7px; border-bottom-width: 1.5px !important; border-bottom-style: solid !important; border-bottom-color: rgb(121, 85, 72) !important"></div>
                            </td>
                        </tr>
                        <tr style="margin: 0px; padding: 0px" class="">
                            <td style="margin: 0px; padding: 0px" class=""><img src="http://tmgrocer.com/sig/Signature_icon/10-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/11-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/12-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/13-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/14-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/15-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/16-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/17-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/18-01.png" alt="" style="border: none" class="">&nbsp;<img src="http://tmgrocer.com/sig/Signature_icon/19-01.png" alt="" style="border: none" class="">&nbsp;&nbsp;&nbsp;
                                <a href="https://itunes.apple.com/my/app/jocom/id945002198" title="map" style="border: none; text-decoration: none" class="" target="_blank" rel="noreferrer"><img src="http://tmgrocer.com/sig/Signature_icon/apple-app2.png" alt="Map" style="border: none; float: right; margin-top: -11px" class="">
                                </a>&nbsp;&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>  
        </div>
    </body>
</html>