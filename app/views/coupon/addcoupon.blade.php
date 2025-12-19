@extends('layouts.master')
@section('title') FreeCoupon @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>

`
<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Free Coupon Item Management</h1>
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
                        {{ Form::open(array('url'=>'coupon/addfreeitem', 'class' => 'form-horizontal')) }}
                            <div class="form-group @if ($errors->has('coupon_code')) has-error @endif">
                            {{ Form::label('coupon_code', 'Coupon Code', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('coupon_code', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('coupon_code')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                            {{ Form::label('name', 'Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('name', null, array('class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('name')}}</p>
                                </div>
                            </div>
                            

                             <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), '0', ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('start_from')) has-error @endif">
                                {{ Form::label('start_from', 'Start Date', array('class'=> 'col-lg-2 control-label')) }}
                            
                                <div class="col-lg-2">
                                        <div class="input-group required" id="datetimepicker_from">
                                            {{ Form::text('start_from','', ['required'=>'required', 'placeholder' => 'From (yyyy-mm-dd)', 'class' => 'form-control','tabindex' => 1]) }}
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>

                                        </div>

                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('end_to')) has-error @endif">
                                {{ Form::label('end_to', 'End Date', array('class'=> 'col-lg-2 control-label')) }}
                             
                                 <div class="col-lg-2">
                                    
                                        
                                        <div class="input-group" id="datetimepicker_to">
                                            {{ Form::text('end_to','', ['id' => 'end_to', 'placeholder' => 'To (yyyy-mm-dd)', 'class' => 'form-control','required'=>'required', 'tabindex' => 2]) }}
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                </div>
                            </div>
                            <div class="form-group">
                            {{ Form::label('q_limit', 'Set Quantity Limit', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('q_limit', array('No' => 'No', 'Yes' => 'Yes'), 'Yes', ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('qty')) has-error @endif">
                            {{ Form::label('qty', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('qty', 1, array('class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('qty')}}</p>
                                </div>                                
                            </div>
                            <div class="form-group">
                            {{ Form::label('c_limit', 'Per Customer Limit', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('cqty', 1, array('class'=> 'form-control', 'readonly' => 'true')) }}
                                </div>
                            </div>
                             <hr /> 
                            <div class="form-group">
                                {{ Form::label('seller_flg', 'Set Seller', array('class'=> 'col-lg-2 control-label')) }}
                                     <div class="col-lg-2">
                                        {{Form::select('seller_flg', array('0' => 'No', '1' => 'Yes'), '0', ['id' => 'seller_flg', 'class'=>'form-control'])}}
                                    </div>                                
                             </div>
                             <div class="form-group @if ($errors->has('seller')) has-error @endif">
                            {{ Form::label('seller_name', 'Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::select('seller', ['0' => 'All'] + $sellersOptions, "all", ['id' => 'seller', 'class' => 'form-control']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                                </div>

                            </div>
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','nadzri'), true ) ) {  ?>
                              <hr /> 
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
                                    {{Form::input('hidden', 'add_check', 'true')}}
                                    {{Form::input('hidden', 'type', 'all')}}
                                </div>
                            </div>
                              <?php } ?>
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
@section('inputjs')
<script>

$(document).ready(function() {
   
   $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
    $('#seller').on('change', function(e) { 
        var seller_id = $('#seller').val();

        if(seller_id != 0){

            $('#seller_flg').val(1);
        }
        else if(seller_id == 0){
            $('#seller_flg').val(0);
        }  

   });

});

</script>
@stop
