@extends('layouts.master')

@section('title') Edit User @stop

@section('content')

<div id="page-wrapper">
	@if ($errors->has())
		@foreach ($errors->all() as $error)
			<div class='bg-danger alert'>{{ $error }}</div>
		@endforeach
	@endif

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class='fa fa-gears'></i> User Profile</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ $message }}
        </div>
    @endif
	
	{{ Form::open(array('url' => array('home/update/' . $user->id) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> User Details</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12">
				<div class='form-group'>
					{{ Form::label('username', 'Username', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::text('username', $user->username, ['placeholder' => ' UserName', 'class' => 'form-control', 'disabled']) }}
					</div>
				</div>

				<div class='form-group'>
					{{ Form::label('Fullname', 'Full Name', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::text('full_name', $user->full_name, ['placeholder' => 'Full Name', 'class' => 'form-control']) }}
					</div>
				</div>

				<div class='form-group'>
					{{ Form::label('email', 'Email', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::email('email', $user->email, ['placeholder' => 'Email', 'class' => 'form-control', 'disabled']) }}
					</div>
				</div>
			 
			 	<div class='form-group'>
					{{ Form::label('role', 'Role', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::text('role_name', $user->role_name, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
					</div>
				</div>

				<div class='form-group'>
					{{ Form::label('password', 'Password', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) }}
					</div>
				</div>

				<div class='form-group'>
					{{ Form::label('password_confirmation', 'Confirm Password', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-4">
					{{ Form::password('password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) }}
					</div>
				</div>

				<div class='form-group'>
					<div class="col-lg-10 col-lg-offset-2">
					{{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
					</div>
				</div>
				
			</div>
			
		</div>
	</div>
	{{ Form::close() }}
</div>

@stop