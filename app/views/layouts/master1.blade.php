<!DOCTYPE html>
<html lang='en'>
	<head>
		<meta name='viewport' content='width=device-width, initial-scal=1'>
		<title>@yield('title') | User Admin </title>

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
		<div class='container-fluid'>
			<div class='row'>
				@yield('content')
			</div>
		</div>

		<footer class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
	      @include('includes.footer')
	    </footer>
	
	    <!-- Scripts are placed here -->
	    {{ HTML::script('js/jquery.js') }}
	    {{ HTML::script('js/bootstrap.min.js') }}
	    {{ HTML::script('js/sb-admin-2.js') }}

	    <!-- Morris Charts JavaScript -->
	    {{ HTML::script('js/plugins/morris/raphael.min.js') }}
	    {{ HTML::script('js/plugins/morris/morris.min.js') }}

	    <!-- Metis Menu Plugin JavaScript -->
	    {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}
  
	</body>
</html>