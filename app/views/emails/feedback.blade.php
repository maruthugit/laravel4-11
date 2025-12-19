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
        th.item, td.item {
            padding: 6px 15px;
        }
        </style>
    </head>
    <body>
        <div class="col-lg-12">
            <div class="panel panel-default" align="center">
                <div class="panel-body" align="left">
                    FeedBack Notication,<br><br>
                     Title: {{ $title }}.<br><br>Name: {{ $name }}.<br><br> Email:{{ $email }}.<br><br>Type:{{ $type }}.<br><br>Remarks {{ $remarks }}.<br><br> Phone Number {{ $phonenumber }}.<br><br>
                </div>
              
            </div>  
        </div>
    </body>
</html>