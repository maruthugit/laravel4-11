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
                <div class="panel-heading"><img moz-do-not-send="true" src="http://tmgrocer.com/email/img/logo.png" alt="tmGrocer" width="222" height="106"></div>
                <div class="panel-body" align="left"><span align="left">Dear {{ $userfullname }},</span><br></div>
                <br><br>
                <div class="panel-body" align="left"><span align="left">Here is your coupon code: {{ $coupon_code }}.</span></div>
            </div>  
        </div>
    </body>
</html>