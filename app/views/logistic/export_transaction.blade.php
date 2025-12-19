@extends('layouts.master')

@section('title') Export @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Export Transaction Details
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/export"><i class="fa fa-refresh"></i></a>
                </span>
            </h1>
        </div>
    </div>

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

    {{ Form::open(['role' => 'form', 'class' => 'form-horizontal', 'id'=>'cform']) }}
  
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-download"></i> Export transaction </h3>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group">
                    {{ Form::label('Date', 'By Date', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <div class="input-group" id="datetimepicker_from">
                            {{ Form::text('from_date', '', ['id' => 'from_date', 'class' => 'from_date form-control', 'tabindex' => 1]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_to">
                            {{ Form::text('to_date', '', ['id' => 'to_date', 'class' => 'to_date form-control', 'tabindex' => 2]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('transaction ID', 'By Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-7">
                        <textarea name="transID" class="transID form-control"></textarea>
                        <span class="help-block">Multiple ID separated by comma.</span>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('product_sku', 'By Product SKU', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-7">
                        <textarea name="product_sku" class="product_sku form-control"></textarea>
                        <span class="help-block">Multiple SKU separated by comma.</span>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('invoice_no', 'By Invoice No', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-7">
                        <textarea name="invoice_no" class="invoice_no form-control"></textarea>
                        <span class="help-block">Multiple Invoice No separated by comma.</span>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('do_no', 'By DO No', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-7">
                        <textarea name="do_no" class="do_no form-control"></textarea>
                        <span class="help-block">Multiple DO No separated by comma.</span>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('courier', 'By Courier', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='courier form-control' name='courier' id='courier'>
                            <option></option>
                            <option value="0">Jocom</option>
                            @foreach ($courier as $couriers)
                            <option value='{{ $couriers->id }}'>{{ $couriers->courier_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('status', 'By Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select multiple="multiple" searchable="Search here.." class='status form-control' name='status' id='status'>
                            <!--<option value=""></option>-->
                            @foreach ($statusList as $value=>$status)
                            <option value='{{$value}}'>{{$status}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('state', 'By State', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select multiple="multiple" class='state form-control' name='state' id='state'>
                            <!--<option value=""></option>-->
                            @foreach ($states as $value=>$state)
                            <option value='{{$state->id}}'>{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('', '', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <button type="button" class="export btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Form::close() }}

</div>

@stop

@section('inputjs')
 <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
        {{ HTML::style('css/bootstrap-multiselect.css') }}
        {{ HTML::style('css/multiselect.css') }}
        {{ HTML::script('js/bootstrap-multiselect.js') }}
        
<script>
$(document).ready(function() {
    
    $('#status').multiselect({
          nonSelectedText: 'Search here..',
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          buttonWidth:'400px'
         });

     $('#state').multiselect({
          nonSelectedText: 'Search here..',
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          buttonWidth:'400px'
         });
         
    $('.export').on('click', function(){

        var transID = $('.transID').val();
        var from_date = $('.from_date').val();
        var to_date = $('.to_date').val();
        var product_sku = $('.product_sku').val();
        var courier = $('.courier').val();
        var status = $('.status').val();
        var invoice_no = $('.invoice_no').val();
        var do_no = $('.do_no').val();
        var state = $('.state').val();
            
        window.location = "/jlogistic/exportdetails?from_date="+from_date+"&to_date="+to_date+"&product_sku="+product_sku+"&courier="+courier+"&status="+status+"&transID="+transID+"&invoice_no="+invoice_no+"&do_no="+do_no+"&state="+state;

        document.getElementById("cform").reset();
    });

});

$(function() {
    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });
});
</script>

@stop