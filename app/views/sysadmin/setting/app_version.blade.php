@extends('layouts.master')

@section('title') Apps Version @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Apps Version</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ $message }}
        </div>
    @endif

    {{ Form::open(array('url' => array('sysadmin/app/update') , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Apps Version Setting</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('ios', 'iPhone Version', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('iphone', $app->iphone, ['placeholder' => '', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('ios', 'iPad Version', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('ipad', $app->ipad, ['placeholder' => '', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('android', 'Android Version', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('android', $app->android, ['placeholder' => '1.0.0', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('tablet', 'Tablet Version', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('tablet', $app->tablet, ['placeholder' => '1.0.0', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('updated_by', 'Last updated by', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('updated_by', $app->updated_by, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('updated_at', 'Last updated at', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('updated_at', $app->updated_at, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
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