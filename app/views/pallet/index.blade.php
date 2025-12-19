@extends('layouts.master')

@section('title', 'pallet Listing')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">pallet Listing
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('pallet') }}"><i class="fa fa-refresh"></i></a>
                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 5, 'AND'))
                        <a class="btn btn-primary" href="{{ url('pallet/create') }}">Add New Pallet     <i class="fa fa-plus"></i></a>
                    @endif
                </span>

                 <span class="pull-center">
                    <a class="btn btn-success" href="{{ url('pallet/stocki') }}">STOCK IN</a>
                </span>

                 <span class="pull-center">
                    <a class="btn btn-danger" href="{{ url('pallet/stocko') }}">STOCK OUT</a>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Pallet Details</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTableBrandListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1"> ID</th>
                                    <th class="col-sm-2"> Pallet Code</th>
                                
                                    <th class="col-sm-1"> Pallet Description </th>
                   
                                    <th class="text-center col-sm-1">Pallet Price </th>
                                    <th class="text-center col-sm-1" >Stock IN</th>

                                       <th class="text-center col-sm-1" >Supplier</th>
                             
                                       <th class="text-center col-sm-1">Deb Stock</th>
                                         <th class="text-center col-sm-1" style="display:none;">Deb Stock</th>
                                          <th class="text-center col-sm-1" style="display:none;">status</th>
                              
                                       <th class="text-center col-sm-2">Action</th>
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
    'order': [[0,'desc']],
    'ajax': '{{ URL::to('pallet/pallets') }}',
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0'},
        {'data': '1'},
        {'data': '2'},
        {'data': '3'},
        {'data': '4'},
        {'data': '5'},
        {'data': '6'},
        {'data': '7', "visible" : false },
        {'data': '8', "visible" : false },
  
        {'data': '9', 'orderable': false, 'searchable': false, 'className': 'text-center'},
       
    ]
});


@stop