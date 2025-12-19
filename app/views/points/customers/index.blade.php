@extends('layouts.master')

@section('title', 'Customer Reward Points')

@section('content')
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Customer Reward Points</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			@if (Session::has('message'))
				<div class="alert alert-success">
					<i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
				</div>
			@endif
			@if (Session::has('error'))
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<i class="fa fa-exclamation-triangle"></i> {{ Session::get('error') }}
				</div>
			@endif
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list"></i> Reward Point List</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive" style="overflow-x: none">
						<table class="table table-striped table-bordered table-hover" id="dataTablePointCustomerListing">
							<thead>
								<tr>
									<th class="col-sm-1">User ID</th>
									<th class="col-sm-2">Username</th>
									<th class="col-sm-3">Full name</th>
									<th class="col-sm-3">NRIC/Passport</th>
									<th class="col-sm-2">Points</th>
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
$('#dataTablePointCustomerListing').dataTable({
	'autoWidth': false,
	'processing': true,
	'serverSide': true,
	'ajax': '{{ URL::to('points/customers/datatables') }}',
	'columnDefs': [{
		'targets': '_all',
		'defaultContent': ''
	}],
	'columns': [
		{'data': '0'},
		{'data': '1'},
		{'data': '2'},
		{'data': '3'},
		{'data': '4', 'orderable': false, 'searchable': false},
		{'data': '5', 'orderable': false, 'searchable': false, 'className': 'text-center'},
	]
});
@stop
