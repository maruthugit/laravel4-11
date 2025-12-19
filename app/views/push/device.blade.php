@extends('layouts.master')

@section('title', 'Push Notification')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Push Notification</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Device List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none; word-break: break-all;">
                        <table class="table table-striped table-bordered table-hover" id="dataTablePushListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">OS</th>
                                    <th class="col-sm-2">Token</th>
                                    <th class="col-sm-1">User</th>
                                    <th class="col-sm-1">Enable Push</th>
                                    <th class="col-sm-1">Date</th>
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
$('#dataTablePushListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('push/datadevice') }}',
    'order': [[5, 'desc']], // Order by date, descending
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0', 'orderable': false, 'searchable': false},
        {'data': '1'},
        {'data': '2'},
        {'data': '3'},
        {'data': '4'},
        {'data': '5'},
    ]
});
@stop
