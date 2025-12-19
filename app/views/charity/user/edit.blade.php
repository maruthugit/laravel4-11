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
            <h1 class="page-header">Charity User Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    
    {{ Form::open(array('url' => array('charity/user/update/' . $user->id) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> User Login Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('username', $user->username, ['placeholder' => 'Username', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password', 'Password', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password_confirmation', 'Confirm Password', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('Last login', 'Last Login', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('Last Login', $user->last_login, ['placeholder' => ' Last Login', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> User Profile</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('Fullname', 'Full Name', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('full_name', $user->full_name, ['placeholder' => 'Full Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('contact_no', 'Contact No.', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('contact_no', $user->contact_no, ['placeholder' => 'Contact No.', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email', 'Email', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email', $user->email, ['placeholder' => 'Email', 'class' => 'form-control']) }}
                    </div>
                </div>
             
                <div class="form-group">
                    {{ Form::label('charity_id', 'Charity Category', array('class'=> 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::select('charity_id', $category, $user->charity_id, array('class'=> 'form-control')) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('status', 'Status', array('class'=> 'col-lg-3 control-label')) }}
                     <div class="col-lg-4">
                        {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), $user->status, ['class'=>'form-control'])}}
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    @if(Permission::CheckAccessLevel(Session::get('role_id'), 22, 3, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
        {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
        {{ Form::submit(' Save ', ['class' => 'btn btn-primary']) }}
        </div>  
    </div>
    @endif
    {{ Form::close() }}
</div>

@stop