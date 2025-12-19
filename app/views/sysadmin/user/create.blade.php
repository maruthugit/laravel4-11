@extends('layouts.master')

@section('title') Create User @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">System Administration</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/sysadmin/user/store', 'class' => 'form-horizontal', 'files' => true, 'enctype' => "multipart/form-data"]) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> User Login Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('user_photo', 'User Photo', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <img src="http://via.placeholder.com/100x100" class="avatar img-circle" alt="avatar" style="margin-bottom: 15px;">
                        <input type="file" class="form-control" name="user_photo">
                    </div>
                </div> 
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('username', null, ['placeholder' => ' UserName', 'class' => 'form-control']) }}
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
            </div>
        </div>

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> User Profile </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('full_name', 'Full Name *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('full_name', null, ['placeholder' => 'Full Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email', 'Email *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email', null, ['placeholder' => 'Email', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('contact_no', 'Contact No.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('contact_no', null, ['placeholder' => 'Contact No.', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('role', 'Role', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::select('role', $roles, null, array('class' => 'form-control') ) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('role', 'Access Region', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <select name="region_access" id="region_access" class="form-control">
                                <option value="0">All region</option>
                            <?php foreach ($regions as $key => $value)  { ?>
                                <option value="<?php echo $value->id; ?>" <?php if($value->id == $user_region) { echo "selected";} ?>><?php echo $value->region; ?></option>
                            <?php  } ?>
                        </select>
            </div>
        </div>
    </div>
        </div>
    </div>
    @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
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