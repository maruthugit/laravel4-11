@extends('layouts.master')

@section('title') TransactionGMV @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Transaction & GMV Report 
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}inventory"><i class="fa fa-refresh"></i></a>
            </span></h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>Transaction & GMV Report </h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/generategmv', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                      

                        <div class="form-group class="form-group @if ($errors->has('region_id')) has-error @endif"">
                            {{ Form::label('transaction_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-3">
                                <select class="form-control" id="region_country_id" name="region_country_id">
                                <?php foreach ($countries as $key => $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($transaction->region_country_id == $value->id){ echo "selected";} ?>><?php echo $value->name;?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                                <?php if(Session::get('branch_access') != 1){?>
                                <option value="">All region</option>
                                <?php } ?>
                                <?php foreach ($regions as $key => $value)  { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($value->id == $product->region_id) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                <?php  } ?>
                            </select>
                            </div>
                                                        
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                        {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-10">
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('completed', '1', true) }} {{ Completed }}
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('cancelled', '1') }} {{ Cancelled }}
                                    </label>
                                </div>    
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('refund', '1') }} {{ Refund }}
                                    </label>
                                </div>
                                <div class="col-lg-2">
                                    <label class="checkbox-inline">
                                    {{ Form::checkbox('pending', '1') }} {{ Pending }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', 'Date Range', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('created', array('1' => 'From Date to Date Ordered', '2' => 'From Date to Date Inserted','3' => 'From Date to Date Invoiced'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <div class="input-group" id="datetimepicker_from">
                                        {{ Form::text('start_from', Input::get('start_from'), ['id' => 'start_from', 'placeholder' => 'From (yyyy-mm-dd)', 'class' => 'form-control','required'=>'required', 'tabindex' => 1]) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>

                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group">
                                    
                                    <div class="input-group" id="datetimepicker_to">
                                        {{ Form::text('end_to', Input::get('end_to'), ['id' => 'end_to', 'placeholder' => 'To (yyyy-mm-dd)', 'class' => 'form-control','required'=>'required', 'tabindex' => 2]) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="form-group">
                            {{ Form::label('', '[Optional] Product SKU', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                {{Form::text('product_sku', '', array('placeholder' => 'Product SKU', 'class'=>'form-control'))}}
                                <p class="help-block">Blank for all, or use ',' for multi-product SKU.</p>
                                <p class="help-block" for="inputError">{{$errors->first('product')}}</p>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('seller')) has-error @endif">
                        {{ Form::label('seller_name', '[Optional] Seller', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{ Form::select('seller', ['all' => 'All'] + $sellersOptions, "all", ['class' => 'form-control']) }}
                                <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                            </div>

                        </div>
                        <div class="form-group @if ($errors->has('customer')) has-error @endif">
                        {{ Form::label('customer_name', '[Optional] Customer', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-4">
                                {{ Form::text('customer', Input::get('customer'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'customer', 'placeholder' => 'Customer ID']) }}
                                <div id="customerAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block">Blank for all, or use ',' for Customer ID</p>
                                <p class="help-block" for="inputError">{{$errors->first('customer')}}</p>
                            </div>
                        </div>




                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Export</button>
                            </div>
                        </div>

                        <hr />

                        

                     
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


   document.getElementById("region_country_id").selectedIndex = "2";
    loadOptionRegion(458);


    $('body').on('change', '#region_country_id', function() {
        loadOptionRegion($(this).val());
    });

    function loadOptionRegion(countryID){
        
        $.ajax({
                method: "POST",
                url: "/region/country",
                dataType:'json',
                data: {
                    'country_id':countryID
                },
                beforeSend: function(){
                },
                success: function(data) {
                    console.log(data.data.region);
                    var regionList = data.data.region;
                    var str = '<option value="0">All Region</option>';
                    $.each(regionList, function (index, value) {
                        str = str + "<option value='"+value.id+"'>"+value.region+"</option>";
                       console.log(str);
                    });
                    $("#region_id").html(str);
                    
                }
          })
        
    }



    
});

</script>
@stop




