@extends('layouts.master')

@section('title', 'Reward Points')

@section('content')
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Reward Points
                 <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('points') }}"><i class="fa fa-refresh"></i></a>
                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
                    	<a class="btn btn-primary" href="{{ url('points/create') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </span>
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			@if (Session::has('success'))
				<div class="alert alert-success" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}
				</div>
			@endif
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list"></i> Reward Point List</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="overflow-x: none">
						<table class="table table-striped table-bordered table-hover" id="dataTablePointListing">
							<thead>
								<tr>
									<th class="col-sm-1">ID</th>
									<th class="col-sm-4">Point</th>
									<th class="col-sm-2">Earn Rate</th>
									<th class="col-sm-2">Redeem Rate</th>
									<th class="col-sm-2">Status</th>
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
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			{{ Form::open(['method'=> 'DELETE', 'id' => 'action']) }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="deleteModalLabel"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<input type="hidden" name="point_type_id" id="point-type">
					<button type="submit" class="btn btn-danger">Confirm</button>
				</div>
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop

@section('script')
$('#dataTablePointListing').dataTable({
	'autoWidth': false,
	'processing': true,
	'serverSide': true,
	'ajax': '{{ URL::to('points/datatables') }}',
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
		{'data': '5', 'orderable': false, 'searchable': false, 'className': 'text-center'},
	]
});

$('#delete').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var pointType = button.data('type');
	var pointTypeId = button.data('type-id');
	var modal = $(this);

	modal.find('.modal-title').text('Delete ' + pointType);
	modal.find('.modal-body').text('Are you sure you want to delete ' + pointType + '?');
	$('#action').prop('action', '{{ url('points') }}/' + pointTypeId);
	$('#point-type').val(pointTypeId);
});
@stop
