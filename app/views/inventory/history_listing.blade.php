@extends('layouts.master')

@section('title') Inventory @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Inventory Management
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Inventory History Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-history">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">QR Code</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2">Label</th>
                                    <th class="col-sm-1 text-center">Type</th>
                                    <th class="col-sm-1 text-center">Quantity</th>
                                    <th class="col-sm-1 text-center">Old Stock</th>
                                    <th class="col-sm-1 text-center">New Stock</th>
                                    <th class="col-sm-1 text-center">Measurement</th>
                                    <th class="col-sm-1 text-center">Username</th>
                                    <th class="col-sm-1 text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    $('#dataTables-history').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('inventory/listing?'.http_build_query(Input::all())) }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "id"},
        { "data" : "qrcode", "className": "text-center"},
        { "data" : "name"},
        { "data" : "label"},
        { "data" : "type", "className": "text-center" },
        { "data" : "qty", "className": "text-right" },
        { "data" : "pre_stock", "className": "text-right" },
        { "data" : "stock", "className": "text-right" },
        { "data" : "stock_unit", "className": "text-right" },
        { "data" : "username", "className": "text-center" },
        { "data" : "update_date", "className": "text-center" }
        ]
    });
@stop



