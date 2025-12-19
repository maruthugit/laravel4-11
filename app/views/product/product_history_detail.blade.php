@extends('layouts.master')
@section('title', 'Product History')
@section('content')



<?php 
// $tempKey = 'aDmIn';
// Crypt::setKey($tempKey);

$encrypted = Crypt::encrypt($po->id);
$encrypted = urlencode(base64_encode($encrypted));

$g_po_inv = true;
$tempcoupon = 0;
$tempamount = 0;
$tempcount = 1;
$tempid = $po->id;

$currency = Config::get('constants.CURRENCY');
$newinvdate = Config::get('constants.NEW_INVOICE_START_DATE');
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product History Detail
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href='{{asset('/')}}transaction'}}><i class="fa fa-reply"></i></a></span> -->
            </h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Compare Product History</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <div class="col-lg-12">
                        {{ Form::open(array('class' => 'form-horizontal')) }}

                            <div class="form-group">
                            {{ Form::label('product_id', 'Product ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->product_id}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('sku', 'SKU', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->sku}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('type', 'Type', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->type}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('name', 'Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->name}}</p>
                                </div>
                            {{ Form::label('old_name', 'Old Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->name}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->prd_status}}</p>
                                </div>
                            {{ Form::label('old_status', 'Old Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->prd_status}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('gst_status', 'GST Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">
                                        @if ($history->gst_status == 0)
                                            Exempted
                                        @elseif ($history->gst_status == 1)
                                            Zero Rated
                                        @else
                                            Taxable
                                        @endif
                                     </p>
                                </div>
                            {{ Form::label('old_gst_status', 'Old GST Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">
                                        @if ($previous_history->gst_status == 0)
                                            Exempted
                                        @elseif ($history->gst_status == 1)
                                            Zero Rated
                                        @else
                                            Taxable
                                        @endif
                                     </p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('delivery_fee', 'Delivery Fee', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->delivery_fee}}</p>
                                </div>
                            {{ Form::label('old_delivery_fee', 'Old Delivery Fee', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->delivery_fee}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                            {{ Form::label('price_id', 'Price ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->price_id}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('label', 'Label', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->label}}</p>
                                </div>
                            {{ Form::label('old_label', 'Old Label', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->label}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('price', 'Actual Price', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->price}}</p>
                                </div>
                            {{ Form::label('old_price', 'Old Actual Label', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->price}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('price_promo', 'Promo Price', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->price_promo}}</p>
                                </div>
                            {{ Form::label('old_price_promo', 'Old Promo Label', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->price_promo}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('price_status', 'Price Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->pri_status}}</p>
                                </div>
                            {{ Form::label('old_price_status', 'Old Price Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->pri_status}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('quantity', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->qty}}</p>
                                </div>
                            {{ Form::label('old_quantity', 'Old Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->qty}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('actual_stock', 'Actual Stock', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->stock}}</p>
                                </div>
                            {{ Form::label('old_actual_stock', 'Old Actual Stock', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->stock}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                            {{ Form::label('seller_id', 'Seller ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->seller_id}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('cost', 'Cost', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->cost}}</p>
                                </div>
                            {{ Form::label('old_cost', 'Old Cost', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->cost}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                            {{ Form::label('base_product_id', 'Base Product ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->base_id}}</p>
                                </div>
                            {{ Form::label('old_base_product_id', 'Old Base Product ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$previous_history->base_id}}</p>
                                </div>
                            </div>
                            <hr>
                            @if ($history->type == 'New Product')
                            <div class="form-group">
                            {{ Form::label('created_by', 'Created By', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->created_by}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('created_at', 'Created At', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->created_at}}</p>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                            {{ Form::label('updated_by', 'Updated By', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->updated_by}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('updated_at', 'Updated At', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-4">
                                     <p class="form-control-static">{{$history->updated_at}}</p>
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
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    History Lists
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-history">
                            <thead>
                               <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">Type</th>
                                    <th class="col-sm-1">Product ID</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2">Product Status</th>
                                    <th class="col-sm-1 text-center">Price ID</th>
                                    <th class="col-sm-1 text-center">Label</th>
                                    <th class="col-sm-1 text-center">Price </th>
                                    <th class="col-sm-1 text-center">Promo Price</th>
                                    <th class="col-sm-1 text-center">Price Status</th>
                                    <th class="col-sm-1 text-center">Seller ID</th>
                                    <th class="col-sm-1 text-center">Cost</th>
                                    <th class="col-sm-1 text-center">Created By</th>
                                    <th class="col-sm-1 text-center">Created At</th>
                                    <th class="col-sm-1 text-center">Updated By</th>
                                    <th class="col-sm-1 text-center">Updated At</th>
                                    <th class="col-sm-1 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div> 
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    </div>
</div>

@stop

@section('script')

$('#dataTables-history').dataTable({
    "autoWidth" : false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('product/historydetaillist/'.$history->id) }}",
    "order" : [[0,'desc']],
    "columns" : [
        { "data" : "0", "className" : "text-center" },
        { "data" : "1" },
        { "data" : "2" },
        { "data" : "3" },
        { "data" : "4", "className" : "text-center" },
        { "data" : "5", "className" : "text-center" },
        { "data" : "6", "className" : "text-center" },
        { "data" : "7", "className" : "text-center" },
        { "data" : "8", "className" : "text-center" },
        { "data" : "9", "className" : "text-center" },
        { "data" : "10", "className" : "text-center" },
        { "data" : "11", "className" : "text-center" },
        { "data" : "12", "className" : "text-center" },
        { "data" : "13", "className" : "text-center" },
        { "data" : "14", "className" : "text-center" },
        { "data" : "15", "className" : "text-center" },
        { "data" : "16", "className" : "text-center" }

    ]
});

@stop