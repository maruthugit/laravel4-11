@extends('layouts.master')

@section('title', 'Creat Pallet')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Create Pallet
                <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('pallet') }}"><i class="fa fa-reply"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-xing"></i> Pallet  Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'pallet', 'method' => 'post']) }}
                        <div class="form-horizontal">
                            <div class="form-group required {{ $errors->first('pallet_code', 'has-error') }}">
                                <label class="col-lg-2 control-label">Pallet Code</label>
                                <div class="col-lg-4">
                                    {{ Form::text('pallet_code', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('pallet_code', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                         


                            <div class="form-group required {{ $errors->first('pallet_price', 'has-error') }}">
                                <label class="col-lg-2 control-label">Pallet Price</label>
                                <div class="col-lg-4">
                                    {{ Form::text('pallet_price', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('pallet_price', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>


                            <div class="form-group required {{ $errors->first('pallet_Description', 'has-error') }}">
                                <label class="col-lg-2 control-label">Pallet Description </label>
                                <div class="col-lg-4">
                                    {{ Form::textarea('pallet_Description', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('pallet_Description', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
  <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                    
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 3, 'AND'))
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
