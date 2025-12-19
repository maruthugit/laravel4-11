<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta name='viewport' content='width=device-width, initial-scal=1'>
        <title>@yield('title') | CMS </title>
        <link href="<?=url('images/favicon.png');?>" rel="icon" type="image/png">

        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/sb-admin-2.css') }}
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

        <style>
            body {
                margin-top: 5%;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <!-- nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0"> -->            
                @yield('content')
            <!-- </nav> -->
        </div>  

        <!-- Scripts are placed here -->
        {{ HTML::script('js/jquery.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}

        <!-- Custom Theme JavaScript -->
        {{ HTML::script('js/sb-admin-2.js') }}

        <!-- Morris Charts JavaScript -->
        {{ HTML::script('js/plugins/morris/raphael.min.js') }}
        {{ HTML::script('js/plugins/morris/morris.min.js') }}
        {{ HTML::script('js/plugins/morris/morris-data.js') }}

        <!-- Metis Menu Plugin JavaScript -->
        {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}
        
          
    </body>
</html>