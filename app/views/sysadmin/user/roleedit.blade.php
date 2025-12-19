@extends('layouts.master')

@section('title') Role @stop

@section('content')

<div id="page-wrapper">
	@if ($errors->has())
		@foreach ($errors->all() as $error)
			<div class='bg-danger alert'>{{ $error }}</div>
		@endforeach
	@endif

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class='fa fa-gear'></i> System Administration</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

	{{ Form::open(array('url' => array('user/roleupdate/' . $role->id ) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> Edit Role </h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12">
				<div class='form-group'>
					{{ Form::label('role_name', 'Role', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-10">
						{{ Form::text('role_name', $role->role_name, ['placeholder' => 'Full Name', 'class' => 'form-control']) }}
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