@extends('layouts.master')

@section('title') Create Driver @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            
            <h1 class="page-header">Driver Management </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/driver/store', 'class' => 'form-horizontal', 'files' => true]) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Login Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('username', 'Username *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('username', null, ['placeholder' => 'Username', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password', 'Password *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password_confirmation', 'Confirm Password *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> Personal Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('name', 'Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('contact_no', 'Contact No *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('contact_no', null, ['placeholder' => 'Contact No', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('device_id', 'Device ID *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('device_id', null, ['placeholder' => 'Device ID', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('type', 'Type', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::select('type', $type, null, array('class' => 'form-control') ) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::select('status', $status, null, array('class' => 'form-control') ) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('role', 'Access Region', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <select name="region_access" id="region_access" class="form-control">
                            <?php foreach ($regions as $key => $value)  { ?>
                                <option value="<?php echo $value->id; ?>" <?php if($value->id == $user_region) { echo "selected";} ?>><?php echo $value->region; ?></option>
                            <?php  } ?>
                        </select>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('logistic_dashboard', 'Logistic Dashboard', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <input type="checkbox" name="logistic_dashboard" id="logistic_dashboard" value="1" >
                    </div>
                </div>
                

            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> Prfile Details</h2>
        </div>
        <div class="panel-body">
            <div class='form-group'>
                    {{ Form::label('Profile Image', 'Profile Image (Format - PEG, JPG, PNG, GIF)', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-10">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 640; height: 640px;">
                                {{ HTML::image('media/no_images.jpg', '', array('width' => '640', 'height' => '640')) }}   
                            </div>
                            <div>
                                <span class="btn btn-default btn-file">
                                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Upload image</span>
                                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                    <input type="file" name="profileimg" id="profileimg" />
                                </span>                            
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                            </div>
                        </div>
                        <input type="hidden" name="filename" id="filename" value="<?php echo (isset($driver->filename) AND $driver->filename != '') ? $driver->filename: ''?>"><br>
                    </div>
                </div>
        </div>
        

    </div>

    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 5, 'OR'))
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    @endif

    {{ Form::close() }}

</div>

@stop