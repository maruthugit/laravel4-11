@extends('layouts.master')
@section('content')

<!-- For datepicker in create new transaction -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>


<script>
$(function() {
$( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
</script>

<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Transaction Management</h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Transaction</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                	<div class="col-lg-12">
                		{{ Form::open(array('url'=>'transaction/add', 'class' => 'form-horizontal')) }}

                		<div class="form-group @if ($errors->has('transaction_date')) has-error @endif">
                        	{{ Form::label('transaction_date', 'Transaction Date', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('transaction_date', null, array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('transaction_date')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        	{{ Form::label('temptime', 'Transaction Time', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::input('time', 'temptime', null, ['placeholder' => 'hh:mm:ss or leave blank for current time', 'class'=>'form-control'])}}
                               <!--  <span class="help-block">&lt;blank&gt; for current time</span> -->
                            </div>
                        </div>

                        <hr />

                        <div class="form-group">
                        	{{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::select('status', array('pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refund' => 'Refund'), 'pending', ['class'=>'form-control'])}}
                            </div>
                        </div>

                        <div class="form-group">
                        	{{ Form::label('buyer_username', 'Buyer', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                <select name="buyer_username" class="form-control">
								 	@foreach($display_cust as $full_name => $username)
										<option value="{{ $username }}"
									<?php if ($username == Input::old('buyer_username'))
										echo 'selected';
									?>
									>{{ ucwords($full_name) }}</option>
									@endforeach
								</select>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group @if ($errors->has('delivery_name')) has-error @endif">
                        	{{ Form::label('delivery_name', 'Delivery Name', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_name', null, array('required'=>'required', 'class'=>'form-control'))}} 
                                <p class="help-block" for="inputError">{{$errors->first('delivery_name')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_contact_no')) has-error @endif">
                        	{{ Form::label('delivery_contact_no', 'Delivery Contact', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_contact_no', null, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('delivery_contact_no')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_addr_1')) has-error @endif">
                        	{{ Form::label('delivery_addr_1', 'Delivery Address', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_addr_1', null, array('required'=>'required', 'class'=>'form-control'))}} 
                                <p class="help-block" for="inputError">{{$errors->first('delivery_addr_1')}}</p>
                                {{Form::text('delivery_addr_2', null, array('required'=>'required', 'class'=>'form-control'))}} 
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_postcode')) has-error @endif">
                        	{{ Form::label('delivery_postcode', 'Delivery Postcode', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_postcode', null, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('delivery_postcode')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_state')) has-error @endif">
                        	{{ Form::label('delivery_state', 'Delivery State', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_state', null, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('delivery_state')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('delivery_country')) has-error @endif">
                        	{{ Form::label('delivery_country', 'Delivery Country', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('delivery_country', null, array('required'=>'required', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('delivery_country')}}</p>
                            </div>
                        </div>


                        <hr />
                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
						   			{{Form::input('hidden', 'add_check', 'true')}}
                                    {{Form::input('hidden', 'total_amount', '0')}}
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

