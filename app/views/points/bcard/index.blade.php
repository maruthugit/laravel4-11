@extends('layouts.master')

@section('title', 'BCard Rewards')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">BCard Rewards
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('points/bcard') }}"><i class="fa fa-refresh"></i></a>
                    <a class="btn btn-primary" href="{{ url('points/bcard/create') }}"><i class="fa fa-plus"></i></a>
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
            @if (Session::has('error'))
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> {{ Session::get('error') }}<button data-dismiss="alert" class="close" type="button">&times;</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> BCard Reward List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTablePointListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Transaction / Conversion ID</th>
                                    <th class="col-sm-1">Reward ID</th>
                                    <th class="col-sm-2">Card</th>
                                    <th class="col-sm-1">Transaction Type</th>
                                    <th class="col-sm-1">Point</th>
                                    <th class="col-sm-2">Date / Time</th>
                                    <th class="col-sm-1">Status</th>
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
<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="showModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="showModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="void" tabindex="-1" role="dialog" aria-labelledby="showModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="showModalLabel"></h4>
            </div>
            <div class="modal-body">Are you sure you want to void this reward?</div>
            <div class="modal-footer">
                <form method="post" action="{{ url('points/bcard/void') }}">
                    <input type="hidden" name="void-id">
                    <button class="btn btn-danger" type="submit">Void</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
$('#dataTablePointListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('points/bcard/datatables') }}',
    'order': [[0, 'desc']],
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0'},
        {'data': '1', 'orderable': false},
        {'data': '2', 'orderable': false},
        {'data': '3'},
        {'data': '4'},
        {'data': '5', 'searchable': false},
        {'data': '6'},
        {'data': '7'},
        {'data': '8', 'orderable': false, 'searchable': false, 'className': 'text-center'},
    ]
});

$('#show').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var modal = $(this);

    modal.find('.modal-title').text('ID: ' + id + ' - BCard Transaction Details');
    modal.find('.modal-body').html('');

    $.ajax({
        url: '{{ URL::to('points/bcard/request-response?id=') }}' + id,
        success: function(data) {
            modal.find('.modal-body').html(data);
        }
    });
});

$('#void').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var modal = $(this);

    modal.find('.modal-title').text('ID: ' + id + ' - Void Transaction');
    modal.find('form input[name="void-id"]').attr('value', id);
});
@stop
