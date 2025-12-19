@extends('layouts.master')

@section('title') Add Manual Data @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Help Center </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Manual Data</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'helpcenter/store', 'class' => 'form-horizontal','files'=>true)) }}

                            <div class="form-group @if ($errors->has('username')) has-error @endif">
                                {{ Form::label('username', 'Username *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('username', Input::old('username'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                                    {{ $errors->first('user', '<p class="help-block">:message</p>') }}
                                </div>
                                <div class="col-xs-1">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="selectUserBtn" class="btn btn-primary selectUserBtn" href="/helpcenter/ajaxcustomer"><i class="fa fa-plus"></i> Select Customer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('order_id')) has-error @endif">
                                {{ Form::label('order_id', 'Order ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('order_id', Input::old('order_id'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('order_id', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  

                            <hr />
                           <div class="form-group @if ($errors->has('query_topic')) has-error @endif">
                                {{ Form::label('query_topic', 'Topic *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <select name="query_topic" class='form-control' autofocus='autofocus'>
                                        <option value="Order Related">Order Related</option>
                                        <option value="Cancellation">Cancellation</option>
                                        <option value="Delivery Status">Delivery Status</option>
                                        <option value="Incomplete items">Incomplete items</option>
                                        <option value="Refund">Refund</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    {{ $errors->first('query_topic', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  

                            <hr />
                            <div class="form-group @if ($errors->has('description')) has-error @endif">
                            {{ Form::label('description', 'Description *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('description', Input::old('description'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('description', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                                {{ Form::label('email', 'Email ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::email('email', Input::old('email'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  


                            <hr />
                            <div class="form-group @if ($errors->has('contact_number')) has-error @endif">
                                {{ Form::label('contact_number', 'Contact Number*', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('contact_number', Input::old('contact_number'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('contact_number', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  


                            <hr />
                            <div class="form-group @if ($errors->has('image')) has-error @endif">
                                {{ Form::label('image', 'Image Upload', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::file('image', array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  


                            <hr />
                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                                {{ Form::label('status', 'Status *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <select name="status" class='form-control' autofocus='autofocus'>
                                        <option value="1">Pending</option>
                                        <option value="2">Completed</option>
                                        
                                    </select>
                                    {{ $errors->first('query_topic', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  

                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 5, 'AND'))
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
    

@section('script')
    
    $('#datepicker').datepicker({ dateFormat: "yy-mm-dd" }).val();

    $('#addProdBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#selectUserBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onClosed: function() {
            localStorage.clear();
        }
    });
@stop