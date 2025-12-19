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
            <h1 class="page-header">User Administration</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    
    {{ Form::open(array('url' => array('sysadmin/user/update/' . $user->id) , 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true)) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> User Login Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('user_photo', 'User Photo', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                        <?php if(isset($user->user_photo)){
                            echo '<img src="/images/userprofile/'.$user->user_photo.'" class="avatar img-circle" alt="avatar" style="margin-bottom: 15px;">';
                        }else{
                            echo '<img src="http://via.placeholder.com/100x100" class="avatar img-circle" alt="avatar" style="margin-bottom: 15px;">';
                        } ?>
                        <input type="file" class="form-control" name="user_photo">
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('username', $user->username, ['placeholder' => ' UserName', 'class' => 'form-control']) }}
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
             
                <div class='form-group'>
                    {{ Form::label('role', 'Role', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::select('role', $roles, $user->role_id, array('class' => 'form-control') ) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('role', 'Access Region', array('class' => 'col-lg-3 control-label')) }}
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
    @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 3, 'AND'))
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