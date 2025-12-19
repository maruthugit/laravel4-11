@extends('layouts.master')
@section('title') Coupon @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>

<!-- For datepicker in create new coupon -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>
<script>
  $( function() {

    var availableTags = [<?php foreach ($customers as $key => $value) {
        echo '"' .$value->username."(".$value->full_name.")".'"'.",";
    } ?>];
    
    $( "#tags" ).autocomplete({
      source: availableTags
    });
  } );
  </script>
<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Bulk Coupon Management</h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Coupon</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'coupon/bulkadd', 'class' => 'form-horizontal')) }}

                            <div class="form-group @if ($errors->has('prefix')) has-error @endif">
                            {{ Form::label('prefix', 'Coupon Prefix', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('prefix', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('prefix')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('code_length')) has-error @endif">
                            {{ Form::label('code_length', 'Code Length', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('code_length', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('code_length')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('gquantity')) has-error @endif">
                            {{ Form::label('gquantity', 'Generate Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('gquantity', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('gquantity')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                            {{ Form::label('name', 'Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('name', null, array('class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('name')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                            {{ Form::label('username', 'Username', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('username', null, array('class'=>'form-control','id'=>'tags'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('username')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('amount')) has-error @endif">
                            {{ Form::label('amount', "Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::text('amount', null, array('required'=>'required', 'class'=>'form-control'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('amount')}}</p>
                                </div>
                                <div class="col-lg-2">
                                    {{Form::select('amount_type', array('%' => '%', 'Nett' => 'Nett'), 'Nett', ['class'=>'form-control'])}}
                                </div>
                            </div>

                             <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), '0', ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('min_purchase')) has-error @endif">
                                {{ Form::label('min_purchase', "Minimum Purchases ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('min_purchase', 0, array('class'=> 'form-control', 'placeholder' => 'amount inclusive GST'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('min_purchase')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('max_purchase')) has-error @endif">
                                {{ Form::label('max_purchase', "Maximum Purchases ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('max_purchase', 0, array('class'=> 'form-control', 'placeholder' => ''))}}
                                    <p class="help-block" for="inputError">{{$errors->first('max_purchase')}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                {{ Form::label('valid_from', 'Start Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_from', null, array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('valid_to', 'End Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_to', null, array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>

                            <hr />

                             <div class="form-group">
                            {{ Form::label('q_limit', 'Set Quantity Limit', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('q_limit', array('No' => 'No', 'Yes' => 'Yes'), 'No', ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('qty')) has-error @endif">
                            {{ Form::label('qty', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('qty', null, array('class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('qty')}}</p>
                                </div>                                
                            </div>

                            <hr />

                             <div class="form-group">
                            {{ Form::label('c_limit', 'Set Limit Per Customer', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('c_limit', array('No' => 'No', 'Yes' => 'Yes'), 'No', ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('cqty')) has-error @endif">
                            {{ Form::label('cqty', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('cqty', null, array('class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('cqty')}}</p>
                                </div>                                
                            </div>

                            <hr />

                             <div class="form-group">
                            {{ Form::label('free_delivery', 'Free Delivery Charges', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('free_delivery', array('0' => 'No', '1' => 'Yes'), '0', ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group">
                            {{ Form::label('free_process', 'Free Process Fees', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('free_process', array('0' => 'No', '1' => 'Yes'), '0', ['class'=>'form-control'])}}
                                </div>                                
                            </div>

                            <hr /> 
                            <div class="form-group">
                            {{ Form::label('boost_payment', 'Boost Payment', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('boost_payment', array('0' => 'No', '1' => 'Yes'), '0', ['class'=>'form-control'])}}
                                </div>                                
                            </div>
                             <hr /> 
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
                                    {{Form::input('hidden', 'add_check', 'true')}}
                                    {{Form::input('hidden', 'type', 'all')}}
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
    </div>


 
@stop

