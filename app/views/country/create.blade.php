@extends('layouts.master')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Countries and States 
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href="/country"><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Country</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'country/store', 'class' => 'form-horizontal')) }}

                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                            {{ Form::label('name', 'Country Name *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('name', Input::old('name'), array('id' => 'name', 'class'=> 'form-control')) }}
                                    {{ $errors->first('name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/country"><i class="fa fa-reply"></i> Cancel</a> -->
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND'))
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    @endif
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->  
@stop