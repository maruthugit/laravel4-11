@extends('layouts.master')

@section('title', 'Point Conversions')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Point Conversions</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Point Conversion List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTablePointConversionListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">Username</th>
                                    <th class="col-sm-1">From Type</th>
                                    <th class="col-sm-1">From Point</th>
                                    <th class="col-sm-1">To Type</th>
                                    <th class="col-sm-1">To Point</th>
                                    <th class="col-sm-1">Rate</th>
                                    <th class="col-sm-1">Charges</th>
                                    <th class="col-sm-1">Status</th>
                                    <th class="col-sm-1">Created Date</th>
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
$('#dataTablePointConversionListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('points/conversions/datatables') }}',
    'order': [[0, 'desc']],
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
        {'data': '7'},
        {'data': '8'},
        {'data': '9'},
    ]
});
@stop
