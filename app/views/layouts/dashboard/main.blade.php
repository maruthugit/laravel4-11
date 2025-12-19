<!DOCTYPE html>
<html lang='en'>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">
    <!-- Jocom Favicon -->
    <link href="https://api.tmgrocer.com/images/favicon.png" rel="icon" type="image/png">
    <!-- Bootstrap CSS-->
    {{ HTML::style('jdboard-v2/vendor/bootstrap/css/bootstrap.min.css') }}
    <!-- Font Awesome CSS-->
    {{ HTML::style('jdboard-v2/vendor/font-awesome/css/font-awesome.min.css') }}
    <!-- Fontastic Custom icon font-->
    {{ HTML::style('jdboard-v2/css/fontastic.css') }}
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    {{ HTML::style('jdboard-v2/css/style.default.css') }}
    <!-- Custom stylesheet - for your changes-->
    {{ HTML::style('jdboard-v2/css/custom.css') }}
    @yield('additional-scripts-on-top')
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
    </head>
    <body onload="startTime()">
    <div class="page">
        <div class="page-content d-flex align-items-stretch">
        <div class="content-inner">
            <!-- Page Header-->
            <header class="page-header">
                <div class="container-fluid">
                <h2 class="no-margin-bottom"><span class="date-time-label">Date:</span><span class="dateTxt">{{ date("F j, Y") }}</span><span id="timeTxt"></span></h2>
                </div>
            </header>
            @yield('content')
            <!-- Page Footer-->
            <footer class="main-footer">
                <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                    <p>tmGrocer &copy; {{ date('Y') }}</p>
                    </div>
                </div>
                </div>
            </footer>
        </div>
        </div>
    </div>
    <!-- JavaScript files-->
    @yield('scripts')
    </body>
</html>
