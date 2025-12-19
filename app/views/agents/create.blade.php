@extends('layouts.master')

@section('title', 'Add Agent')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add Agent
                <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('agents') }}"><i class="fa fa-reply"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-user"></i> Agent Basic Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'agents', 'method' => 'post']) }}
                        <div class="form-horizontal">
                            <div class="form-group required {{ $errors->first('username', 'has-error') }}">
                                <label class="col-lg-2 control-label">Username</label>
                                <div class="col-lg-4">
                                    {{ Form::text('username', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('username', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->first('full_name', 'has-error') }}">
                                <label class="col-lg-2 control-label">Full Name</label>
                                <div class="col-lg-4">
                                    {{ Form::text('full_name', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('full_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group required {{ $errors->first('agent_code', 'has-error') }}">
                                <label class="col-lg-2 control-label">Agent Code</label>
                                <div class="col-lg-4">
                                    {{ Form::text('agent_code', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('agent_code', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group required {{ $errors->first('email', 'has-error') }}">
                                <label class="col-lg-2 control-label">Email</label>
                                <div class="col-lg-4">
                                    {{ Form::text('email', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group {{ $errors->first('contact_no', 'has-error') }}">
                                <label class="col-lg-2 control-label">Contact No.</label>
                                <div class="col-lg-4">
                                    {{ Form::text('contact_no', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('contact_no', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 3, 'AND'))
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
@stop
