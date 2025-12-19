@extends('layouts.master')
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>


<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Fees Setup</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Fees</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'fees/edit/'.$display_fees->id, 'class' => 'form-horizontal')) }}
                        <div class="form-group @if ($errors->has('process_fees')) has-error @endif">
                        {{ Form::label('process_fees', "Process Fees ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('process_fees', $display_fees->process_fees, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('process_fees')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_charges')) has-error @endif">
                        {{ Form::label('delivery_charges', "Delivery Charges ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_charges', $display_fees->delivery_charges, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('delivery_charges')}}</p>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group @if ($errors->has('gst')) has-error @endif">
                        {{ Form::label('gst', 'GST (%)', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('gst', $display_fees->gst, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('gst')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('gst_status', 'GST Status', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{Form::select('gst_status', array('0' => 'Inactive', '1' => 'Active'), $display_fees->gst_status, ['class'=>'form-control'])}}
                            </div>
                        </div>

                        <hr />

                        <div class="form-group @if ($errors->has('currency')) has-error @endif">
                        {{ Form::label('currency', 'Currency', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('currency', $display_fees->currency, array('required'=>'required', 'placeholder' => 'e.g. RM', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('currency')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('currency_code')) has-error @endif">
                        {{ Form::label('currency_code', 'Currency Code (ISO)', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('currency_code', $display_fees->currency_code, array('required'=>'required', 'placeholder' => 'e.g. MYR', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('currency_code')}}</p>
                            </div>
                        </div>

                        <hr />
                        <div class="form-group @if ($errors->has('email_activation')) has-error @endif">
                        {{ Form::label('Customer Email Activation', 'Customer Email Activation', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-3" style="padding-top: 8px;">
                                <input type="radio" name="email_activation" value="1" <?php echo  $display_fees->email_activation == 1 ? "checked": ""; ?>> On
                                <input type="radio" name="email_activation" value="0" <?php echo  $display_fees->email_activation == 0 ? "checked": ""; ?>> Off
                                <p class="help-block" for="inputError">{{$errors->first('email_activation')}}</p>
                            </div>
                        </div>

                        <hr />
                       @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 3, 'AND'))
                        <div class="form-group">
                            <div class="col-lg-12">
                                {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
                                {{Form::input('hidden', 'id', $display_fees->id)}}
                            </div>
                        </div>
                        @endif

                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>
 
@stop

