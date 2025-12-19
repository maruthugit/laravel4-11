@extends('layouts.master')
@section('title', 'Manager')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add Manager</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/manager/store', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('name', 'has-error') }}">
                            {{ Form::label('name', 'Name ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('name', $warehouse->name, ['placeholder' => 'Manager Name', 'class' => 'form-control']) }}
                                {{ $errors->first('name', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
    <div class='form-group' >
        <div class="col-lg-10" style="padding-bottom:10px;">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
            <!--Under Upgrading .. Wait for a moment.-->
        </div>
    </div>
    @endif
    {{ Form::close() }}
</div>
    
@stop
