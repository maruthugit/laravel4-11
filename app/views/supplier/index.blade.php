@extends('layouts.master')

@section('title', 'Supplier Listing')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Supplier Listing
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('supplier') }}"><i class="fa fa-refresh"></i></a>
                        <a class="btn btn-primary" href="{{ url('supplier/create') }}"><i class="fa fa-plus"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">&times;</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Supplier Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTableBrandListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Supplier</th>
                                
                                    <th class="col-sm-1">Supplier Code</th>
                   
                                    <th class="text-center col-sm-1">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
$('#dataTableBrandListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('supplier/suppliers') }}',
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0'},
        {'data': '1'},
        {'data': '2'},
        {'data': '3', 'orderable': false, 'searchable': false, 'className': 'text-center'},
    ]
});


@stop