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
            <h1 class="page-header">Coupon Management</h1>
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
                        {{ Form::open(array('url'=>'coupon/add', 'class' => 'form-horizontal')) }}
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
                            <div class="form-group">
                            {{ Form::label('razerpay_payment', 'Shopee Pay Payment', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('razerpay_payment', array('0' => 'No', '1' => 'Yes'), '0', ['class'=>'form-control'])}}
                                </div>                                
                            </div>
                            <hr /> 
                            <div class="form-group">
                            {{ Form::label('is_jpoint', 'JPoint Restriction', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                   <input type="checkbox" value="1" name="is_jpoint" checked>
                                </div> 
                            </div>
                            <hr /> 
                             <div class="form-group">
                            {{ Form::label('is_preferred_member', 'Preferred Member', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                   <input type="checkbox" value="1" name="is_preferred_member">
                                </div> 
                            </div>
                            <hr /> 
                             <div class="form-group @if ($errors->has('zone_id')) has-error @endif">
							{{ Form::label('region', 'Region', array('class'=> 'col-lg-2 control-label')) }}
							<div class="col-lg-2">
								<select id="delivery_fee" class="form-control">
									<option value="0">Select Region</option>
									@foreach ($zoneOptions as $zone)
										<option value="{{ $zone->id }}">{{ $zone->name }}</option>
									@endforeach
								</select>
								{{ $errors->first('zone_id', '<p class="help-block">The delivery zone is required</p>') }}
							</div>
							<div class="col-lg-8">
								<button type="button" id="add_zone" class="btn btn-primary" disabled><i class="fa fa-plus"></i> Add Zone</button>
							</div>
						</div>
						<div id="zone_div"></div>
                            <hr />   
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','nadzri','boobalan'), true ) ) {  ?>
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
@section('script')
    // Select zone
$('select#delivery_fee').change(function () {
	var zone_id		= $('select#delivery_fee option').filter(':selected').val();

	if (zone_id > 0) {
		$('#add_zone').prop('disabled', false);
	} else {
		$('#add_zone').prop('disabled', true);
	}
});

// Add zone
var zone_index	= {{ $zoneCounter + 1 }};

$('#add_zone').click(function () {
	var zone_id		= $('select#delivery_fee option').filter(':selected').val();
	var zone_name	= $('select#delivery_fee option').filter(':selected').text();

	if (zone_id > 0) {
		$('#zone_div').append('<div id="zone_row_' + zone_index + '" class="form-group"><div class="col-lg-10 col-lg-offset-2"><input type="hidden" value="' + zone_id + '" name="zone_id[' + zone_index + ']"><div class="row"><div class="col-lg-3"><div class="input-group"><span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span><input type="text" name="zone_name[' + zone_index + ']" class="form-control" value="' + zone_name + '" disabled></div></div><div class="col-lg-2"><div class="col-lg-2"><button type="button" class="btn btn-danger delete-zone" data-zone="' + zone_index + '"><i class="fa fa-minus"></i> Remove Zone</button></div></div></div></div>');
		$('select#delivery_fee option[value="' + zone_id + '"]').remove();
		$('#add_zone').prop('disabled', true);
		$('select#delivery_fee').val(0);
	}

	zone_index++;
});

// Delete zone
$(document).on('click', '.delete-zone', function() {
	var zone_index	= $(this).data('zone');
	var zone_id		= $('input[name="zone_id[' + zone_index + ']"]').val();
	var zone_name	= $('input[name="zone_name[' + zone_index + ']"]').val();

	$('#delivery_fee').append('<option value="' + zone_id + '">' + zone_name + '</option>');
	$('#zone_row_' + zone_index).remove();
});
@stop