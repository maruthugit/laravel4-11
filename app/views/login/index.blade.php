@extends('layouts.default')

@section('title') Login @stop

@section('content')

<div class='col-md-4 col-md-offset-4'>
	<div class='login-panel panel panel-default'>
         @if ($errors->has())
            @foreach ($errors->all() as $error)
                <div class="panel panel-danger">
                    <div class="panel-heading"> {{ $error }} </div>
                </div>
            @endforeach
        @endif

        <!-- Success-Messages -->
        @if ($message = Session::get('success'))
            <div class="panel panel-success">
                <div class="panel-heading"> {{ $message }} </div>
            </div>
        @endif
            
		<div class="panel-heading">
			<h1><i class='fa fa-lock'></i> Login</h1>
		</div>
		<div class="panel-body">
            {{ Form::open(['role' => 'form']) }}
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" placeholder="Username" name="username" type="text" autofocus>
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                    </div>
                    <!-- <div class="checkbox">
                        <label>
                            <input name="remember" type="checkbox" value="Remember Me">Remember Me
                        </label>
                    </div> -->
                    <!-- Change this to a button or input when using this as a form -->
                    {{ Form::submit('Login', ['class' => 'btn btn-lg btn-info btn-block']) }}
                    <!-- <a href="index.html" class="btn btn-lg btn-primary btn-block">Login</a> -->
                </fieldset>
           	{{ Form::close() }}
        </div>
        <div class="text-center">    @include('includes.footer') 
	</div>
</div>

@stop