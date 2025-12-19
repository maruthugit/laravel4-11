@extends('layouts.master')

@section('title', 'Push Notification')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Push Notification<span class="pull-right"><a class="btn btn-primary" title="" data-toggle="tooltip" href="push/create"><i class="fa fa-plus"></i></a></span></h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Notification List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTablePushListing">
                            <thead>
                                <tr>
                                    <th class="col-md-1" style="max-width: 10px !Important;">ID</th>
                                    <th class="col-sm-1">Push Type</th>
                                    <th class="col-sm-1">Sub Type</th>
                                    <th class="col-sm-2">Title</th>
                                    <th class="col-sm-3">Message</th>
                                    <th class="col-sm-1">Link ID</th>
                                    <th class="col-sm-1">Status</th>
                                    <th class="col-sm-2">Created Date</th>
                                    <th class="text-center col-sm-1">Target</th>
                                    <th class="text-center col-sm-1">Device</th>
                                    <th class="col-sm-1">Image</th>
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
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this notification?
            </div>
            <div class="modal-footer">
                {{ Form::open(['url' => 'push/delete']) }}
                    {{ Form::hidden('id', '', ['id' => 'deleteId']) }}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="stopModal" tabindex="-1" role="dialog" aria-labelledby="stopModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="stopModalLabel">Stop Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to stop this notification?
            </div>
            <div class="modal-footer">
                {{ Form::open(['url' => 'push/stop']) }}
                    {{ Form::hidden('id', '', ['id' => 'stopId']) }}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Stop</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="resumeModal" tabindex="-1" role="dialog" aria-labelledby="resumeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url' => 'push/resume')) }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="resumeModalLabel">Resume Date/Time</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group" id="datetimepicker">
                        {{ Form::text('begin', Input::old('begin', date('Y-m-d H:i:s')), array('class' => 'form-control')) }}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                        </span>
                    </div>
                    {{ $errors->first('begin', '<p class="help-block">:message</p>') }}
                </div>
                <div class="modal-footer">
                    {{ Form::open(['url' => 'push/resume']) }}
                        {{ Form::hidden('id', '', ['id' => 'resumeId']) }}
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Resume</button>
                    {{ Form::close() }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('script')
$('#dataTablePushListing').dataTable({
    'autoWidth': false,
    'processing': true,
    'serverSide': true,
    'ajax': '{{ URL::to('push/datanotification') }}',
    'order': [[0, 'asc']],
    'columnDefs': [{
        'targets': '_all',
        'defaultContent': ''
    }],
    'columns': [
        {'data': '0', 'searchable': false},
        {'data': '3'},
        {'data': '4'},
        {'data': '1'},
        {'data': '2'},
        {'data': '5'},  
        {'data': '6'},
        {'data': '7'},
        {'data': '8', 'orderable': false, 'searchable': false, 'className': 'text-center'},
        {'data':  function ( row, type, val, meta ) {
                if(row[9] == 1){ return '<span class="label label-default">Android</span>'};
                if(row[9] == 2){ return '<span class="label label-default">iOS</span>'};
                if(row[9] == 0){ return '<span class="label label-default">All</span>'};
        },
            className: "center",
            orderable: false,
        },
        {'data':  function ( row, type, val, meta ) {
            if(row[10] != '' && row[10] !== null){
                return '<img style="width:120px; height:90px;" src="/images/push/'+row[10]+'" class="img-rounded" >';
            }
            return '';
        },
            className: "center",
            orderable: false,
        },
        {'data': '11', 'orderable': false, 'searchable': false, 'className': 'text-center'},
    ]
});

$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#deleteId').val(button.data('value'));
});

$('#stopModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#stopId').val(button.data('value'));
});

$('#resumeModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#resumeId').val(button.data('value'));
});
@stop
