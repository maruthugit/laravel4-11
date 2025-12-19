@extends('layouts.master')

@section('title') Inventory @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Update History
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product Update History</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
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
        "ajax": "{{ URL::to('product/producthistory?'.http_build_query(Input::all())) }}",
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



