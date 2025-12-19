@extends('layouts.master')

@section('title') SP Settings @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Special Pricing Settings</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-thumbs-up"></i> {{ $message }}
        </div>
    @endif

    {{ Form::open(array('url' => array('/special_price/updatesetting/') , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Settings </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('qty', 'Default Quantity', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('qty', $settings->default_qty, ['placeholder' => '', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('updated_by', 'Last updated by', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('updated_by', $settings->updated_by, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('updated_at', 'Last updated at', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('updated_at', $settings->updated_at, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
                @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 3, 'AND'))
                <div class='form-group'>
                    <div class="col-lg-10 col-lg-offset-2">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </div>
    {{ Form::close() }}
</div>

@stop