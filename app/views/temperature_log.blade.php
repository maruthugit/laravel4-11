
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>JOCOM</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
        </style>
    </head>
    <body>
    	<div class="container">
    		<h3 class="text-center mt-5 mb-5">Welcome To JOCOM</h3>

    		

    		<div class="content">
    			<h5 class="mb-3">Please fill in the following details.</h5>
	    		<form>
					<div class="form-group">
						<label for="name">Name</label>
						<input type="text" class="form-control" id="name" required />
					</div>
					<div class="form-group">
						<label for="phone">Phone Number</label>
						<input type="number" class="form-control" id="phone" required />
					</div>
					<div class="form-group">
						<label for="temperature">Temperature</label>
						<input type="number" class="form-control" id="temperature" step=".01" required />
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="type" id="staff" value="staff" checked>
						<label class="form-check-label" for="staff">Staff</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="type" id="visitor" value="visitor">
						<label class="form-check-label" for="visitor">Visitor</label>
					</div>
					<button type="submit" class="btn btn-primary btn-lg btn-block mt-4">Submit</button>
				</form>
    		</div>
    	</div>
    </body>


    <script type="text/javascript">
    	$(function() {
    		$('form').on('submit', function(e) {
    			e.preventDefault();

    			$('button[type="submit"]').prop('disabled', true);
    			$('button[type="submit"]').html(`<span class="spinner-border spinner-border" role="status" aria-hidden="true"></span>`);

    			var formData = new FormData()
		        formData.append('name', $('#name').val());
		        formData.append('phone', $('#phone').val());
		        formData.append('temperature', $('#temperature').val());
		        formData.append('type', $("input[name='type']:checked").val());

    			$.ajax({
	                url: '/temperature_log/store',
	                data: formData,
	                type: 'POST',
	                processData: false,
	                contentType: false,
	                success:function(response) {
	                    $('button[type="submit"]').prop('disabled', false);
	                    $('button[type="submit"]').html('Submit');

	                    console.log(response.safe);
	                    console.log(response.name);

	                    var message = '';
	                    if (response.safe) {
	                    	message = 'Your temperature is normal. You may now proceed.';
	                    } else {
	                    	message = 'Your temperature is abnormal. Please seek medical advice.'
	                    }

	                    $('.content').html(
		                    `<div class="jumbotron">
								<p class="lead">${response.name}</p>
								<p class="lead">Your temperature is ${response.temperature}\xB0C</p>
								<p class="lead">Check in at ${response.logged_at}</p>
								<hr class="my-4">
								<p class="font-weight-bold">${message}</p>
							</div>`);

	                },
	            });
    		});
    	});
    </script>
</html>
