@extends('layouts.master')

@section('title', 'Agent Listing')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Agent Listing
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('agents') }}"><i class="fa fa-refresh"></i></a>
                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 5, 'AND'))
                        <a class="btn btn-primary" href="{{ url('agents/create') }}"><i class="fa fa-plus"></i></a>
                    @endif
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Agent Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTableAgentListing">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Username</th>
                                    <th class="col-sm-2">Full Name</th>
                                    <th class="col-sm-1">Agent Code</th>
                                    <th class="col-sm-2">Email</th>
                                    <th class="col-sm-2">Contact No.</th>
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
<div class="modal fade" id="deactivate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Deactivate Agent</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Deactivate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('script')
$('#dataTableAgentListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('agents/datatables') }}',
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
        {'data': '7', 'orderable': false, 'searchable': false, 'className': 'text-center'},
    ]
});

$('#deactivate').on('show.bs.modal', function (event) {
    var button    = $(event.relatedTarget);
    var agentId   = button.data('agent-id');
    var agentName = button.data('agent-name');
    var modal = $(this);

    $('#deactivate form').attr('action', 'agents/' + agentId);
    modal.find('.modal-body').text('Are you sure you want to deactivate ' + agentName + '?');
});
@stop
