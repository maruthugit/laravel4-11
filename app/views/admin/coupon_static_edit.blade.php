@extends('layouts.master')
@section('title') Edit Coupon @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>

<!-- For datepicker in create new coupon -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.css" rel="stylesheet" type="text/css">
<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>
<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Coupon </h1>
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
                        {{ Form::open(array('url'=>'coupon/edits/'.$display_coupon->id, 'class' => 'form-horizontal')) }}
                            <input type="hidden" name="id" value="{{$display_coupon->id}}">

                            <div class="form-group @if ($errors->has('coupon_code')) has-error @endif">

{{ Form::label('coupon', 'Coupon*', array('class'=> 'col-lg-2 control-label')) }}
<input type="hidden" name="coupon_id" value="{{$display_coupon->coupon_id}}" id="coupon_id">                            <div class="col-lg-4">
                                <div class="input-group">
                                <input type="text" id="coupon" name="coupon_code" class="form-control" readonly value="{{$display_coupon->coupon_code}}">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectCouponBtn" id="selectCouponBtn"  type="button" href="/coupon/ajaxcoupon"><i class="fa fa-plus"></i> Select Coupon</button>
                                </span>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                            </div>

                            <div class="form-group @if ($errors->has('amount')) has-error @endif">
                            {{ Form::label('amount', "Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    <input type="text" name="amount"value="{{$display_coupon->coupon_amount}}" class="form-control" id="amount" readonly>
                                    <p class="help-block" for="inputError">{{$errors->first('amount')}}</p>
                                </div>
                                <div class="col-lg-2">
                                
                                <input type="text" name="amount_type"value="{{$display_coupon->coupon_amount_type}}" class="form-control" id="amount_type" readonly>

                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('description')) has-error @endif">
                            {{ Form::label('Description', 'Description(T&C)', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-6">
                                    {{Form::textarea('description',$display_coupon->description, array('class'=>'form-control summernote','required'=>'required'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('description')}}</p>
                                </div>
                            </div>
                           <div class="form-group @if ($errors->has('valid_from')) has-error @endif">
                                {{ Form::label('valid_from', 'Start Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_from', $display_coupon->from_date, array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control','required'=>'required'))}}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('valid_to')) has-error @endif">
                                {{ Form::label('valid_to', 'End Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_to',$display_coupon->to_date, array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control','required'=>'required'))}}
                                </div>
                            </div>
                            
                             <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    <select class="form-control" name="status">

                                        <option value="0" <?php if($display_coupon->status=="0"){?>selected="selected" <?php }?>>Inactive</option>
                                        <option value="1" <?php if($display_coupon->status=="1"){?>selected="selected" <?php }?>>Active</option>
                                        
                                    </select>
                                
                                </div>
                            </div>
                             <hr /> 
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
                                    
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>

@section('script')
$(document).ready(function() {
  $('.summernote').summernote({
  height: 100
});
});

 $('#selectCouponBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

@stop

