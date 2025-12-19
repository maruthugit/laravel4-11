@extends('layouts.master')

@section('title', 'Online Campaign User ')

@section('content')

<?php

$currency = Config::get('constants.CURRENCY');
?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();

    });
</script>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Active/Deactive Online Campaign User</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-gift"></i> User Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => "onlinecampaign/updatecampaign/{$user->id}", 'method'=> 'PATCH', 'class' => 'form-horizontal']) }}
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">ID</label>
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{ $user->id }}</p>
                                    {{Form::input('hidden', 'c_id', $user->id)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Name</label>
                                <div class="col-lg-3">
                                    {{ $user->name }}
                                    {{Form::input('hidden', 'c_name', $user->name)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Email</label>
                                <div class="col-lg-3">
                                    {{ $user->email }}
                                    {{Form::input('hidden', 'c_email', $user->email)}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Contact No.</label>
                                <div class="col-lg-3">
                                    {{ $user->contact_no }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Coupon Status</label>
                                <div class="col-lg-3">
                                    <p class="form-control-static"><?php if($user->is_coupon_used ==1){echo 'Yes';} else {echo 'No';} ?></p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                                <label class="col-lg-2 control-label">User Status</label>
                                <div class="col-lg-3">
                                    {{ Form::select('status', $statusOptions, $user->status_activation, ['class' => 'form-control']) }}
                                    {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <?php if ($user->status_activation == 0) { ?>

                            <hr>
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
<!-- 
                             <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), '0', ['class'=>'form-control'])}}
                                </div>
                            </div>
 -->
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
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                            <? }?>

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

