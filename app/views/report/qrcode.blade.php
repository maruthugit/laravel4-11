@extends('layouts.master')
@section('title', 'Report')
@section('content')

<?php

$tempcount = 1;

?>

<!-- For datepicker in create new report -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>

<style>
#refer {
    display: none;
} 
</style>

<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">QRCode Listing </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message') OR $message)
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }} {{ $message }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success') OR $success)
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }} {{ $success }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Item Purchased Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/qrtransaction', 'id' => 'add2', 'class' => 'form-horizontal', 'files' => true, 'target' => '_blank', 'method' => 'get')) }}
                        <div class="form-group @if ($errors->has('customer')) has-error @endif">
                        {{ Form::label('customer_name', 'Customer', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-4">
                                {{ Form::text('customer', Input::get('customer'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'customer', 'placeholder' => 'Customer Name or ID']) }}
                                <div id="customerAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block">Blank for all</p>
                                <p class="help-block">Use ',' for multi-customer.</p>
                                <p class="help-block"> E.g. bumbudesaklcc,bumbudesaklia2</p>
                                <p class="help-block" for="inputError">{{$errors->first('customer')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">QrCoode Listing</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                        <!-- /.row -->
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

            <!-- /.panel-heading -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Product Listing By Seller</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/qrseller', 'id' => 'add3', 'class' => 'form-horizontal', 'files' => true, 'target' => '_blank', 'method' => 'get')) }}
                        <div class="form-group @if ($errors->has('seller')) has-error @endif">
                        {{ Form::label('seller_name', 'Seller', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-4">
                                {{ Form::select('seller', ['all' => 'All'] + $sellersOptions, "all", ['class' => 'form-control']) }}
                                <div id="customerAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">QrCode Listing</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                        <!-- /.row -->
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

             <!-- /.panel-heading -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Product Listing Category</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/qrcategory', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true, 'target' => '_blank', 'method' => 'get')) }}
                        <div class="form-group @if ($errors->has('categories')) has-error @endif">
                            {{ Form::label('categories', 'Category', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-4">
                                {{ Form::text('categories', Input::get('categories'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'categories', 'placeholder' => 'Category Name or ID']) }}
                                <div id="categoryAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block">Blank for all</p>
                                <p class="help-block">Use ';' for multi-categories.</p>
                                <p class="help-block"> E.g. Cleaners;Rice</p>
                                <p class="help-block" for="inputError">{{$errors->first('categories')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">QrCode Listing</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                        <!-- /.row -->
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

            
             <!-- /.panel-heading -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Product Listing by QRCode</h3>                    
        </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/qrbycode', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true, 'target' => '_blank', 'method' => 'post')) }}
                        <div class="form-group @if ($errors->has('categories')) has-error @endif">
                            {{ Form::label('QRCode', 'QRCode', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-4">
                                {{ Form::text('qrcode', Input::get('qrcode'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'qrcode', 'placeholder' => 'QRCode']) }}
                                <p class="help-block">Blank for all</p>
                                <p class="help-block">Use ',' for multi-QRCode.</p>
                                <p class="help-block"> E.g. JC3065,JC3074,JC3075</p>
                                <p class="help-block" for="inputError">{{$errors->first('categories')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">QrCode Listing</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                        <!-- /.row -->
                        
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
    var timer;

    $('#customer').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#customer').val()) {
                $.ajax({
                    url: '{{ url('report/customersearch?keyword=') }}' + $('#customer').val() + '&limit=10',
                    success: function (result) {
                        if ($('#customer').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#customerAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var customerName = candidate.full_name;

                                if (customerName.search($('#customer').val())) {
                                    var clickAction = "$('#customerAutoComplete').html(''); $('#customer').val('" + candidate.username + "'); return false;";

                                    $('#customerAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.full_name + ' ('  + candidate.username + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#customer').is(':focus') && size > 0) {
                                $('#customerAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#customerAutoComplete').html('');
            }
        }, 200);
    });

    $('#categories').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#categories').val()) {
                $.ajax({
                    url: '{{ url('report/categorysearch?keyword=') }}' + $('#categories').val() + '&limit=10',
                    success: function (result) {
                        if ($('#categories').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#categoryAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var categoryName = candidate.category_name;

                                if (categoryName.search($('#categories').val())) {
                                    var clickAction = "$('#categoryAutoComplete').html(''); $('#categories').val('" + candidate.category_name + "'); return false;";

                                    $('#categoryAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.category_name + ' ('  + candidate.id + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#categories').is(':focus') && size > 0) {
                                $('#categoryAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#categoryAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#customerAutoComplete').html('').hide();
    });

    $('html').click(function () {
        $('#categoryAutoComplete').html('').hide();
    });
    
@stop
 



