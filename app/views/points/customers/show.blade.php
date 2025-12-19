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
			@if (Session::has('success'))
				<div class="alert alert-success" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}
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
					<h3 class="panel-title"><i class="fa fa-user"></i> Customer Basic Details</h3>
				</div>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class="col-lg-2 control-label">User ID</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $user->id }}</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Username</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $user->username }}</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Full Name</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $user->full_name }}</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">NRIC/Passport</label>
							<div class="col-lg-10">
								<p class="form-control-static">{{ $user->ic_no }}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			@foreach ($points as $point)
				<?php $status = ($point->status == 0) ? 'suspended' : 'active'; ?>
				<?php $action = ($point->status == 1) ? 'suspend' : 'unsuspend'; ?>
				<div class="panel panel-default @if ($point->status == 0) panel-danger @else panel-success @endif">
					<div class="panel-heading">
						<h3 class="panel-title">
							<i class="fa fa-gift"></i> {{ $point->type }} {{ ($point->expiry > 0) ? ' (expired in '.date('j M Y', strtotime($point->expiry)).')' : '' }}
							<span class="pull-right">{{ $point->point }} Points</span>
						</h3>
					</div>
					<table class="table table-center table-hover">
						<thead>
							<tr>
								<th class="col-md-2">Date / Time</th>
								<th class="col-md-2">Transaction ID</th>
								<th class="col-md-2">Action</th>
								<th class="col-md-2">Point</th>
								<th class="col-md-2">Balance</th>
								<th class="col-md-2">Remark</th>
							</tr>
						</thead>
						<tbody>
							<?php $pointTransactions = $transactions[$point->id]; ?>
							@forelse ($pointTransactions as $transaction)
								<?php $valid = ($transaction->code == hash('sha256', "{$transaction->point_user_id}|{$transaction->created_at}|{$transaction->point}|{$transaction->balance}")); ?>
								<tr @if ( ! $valid) class="danger" @endif>
									<td>{{ $transaction->created_at }}</td>
									<td>
										@if ($transaction->transaction_id)
											#{{ link_to('transaction/edit/'.$transaction->transaction_id, $transaction->transaction_id) }}
										@else - @endif
									</td>
									<td>{{ $transaction->action }}</td>
									<td>{{ $transaction->point }}</td>
									<td>{{ $transaction->balance }}</td>
									<td data-toggle="modal" data-target="#remark" data-remark="{{ $transaction->remark }}" data-transaction-id="{{ $transaction->id }}" data-point-id="{{ $point->point_id }}" class="clickable">{{ (strlen($transaction->remark) > 0) ? $transaction->remark : '-' }}</td>
								</tr>
							@empty
								<tr class="text-center">
									<td colspan="6">No transaction history</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					<div class="panel-footer">
						<span>Current status</span>
						<button class="point-status btn btn-{{ ($point->status == 0) ? 'danger' : 'success' }} btn-xs" data-target="#change-status" data-toggle="modal" data-action="{{ $action }}" data-type="{{ $point->type }}" data-point-id="{{ $point->id }}">
							{{ ucfirst($status) }} <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
<div class="modal fade" id="change-status" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="POST" action="{{ url("points/customers/{$user->id}") }}">
				<input type="hidden" name="_method" value="PATCH">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="statusModalLabel"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<input type="hidden" name="point_id" id="point-id">
					<input type="hidden" name="action_id" id="action">
					<button type="submit" class="btn btn-primary">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="remark" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="POST" action="{{ url("points/customers/{$user->id}") }}">
				<input type="hidden" name="_method" value="PATCH">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="remarkModalLabel">Remark</h4>
				</div>
				<div class="modal-body">
					<textarea class="form-control" rows="5" name="remark"></textarea>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="transaction_id" id="transaction-id">
					<button type="submit" class="btn btn-primary">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>
@stop

@section('script')
$('#change-status').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var action = button.data('action');
	var pointType = button.data('type');
	var pointId = button.data('point-id');
	var modal = $(this);

	switch (action) {
		case 'suspend':
			var actionId = 0;
			modal.find('.modal-title').text(action.charAt(0).toUpperCase() + action.slice(1) + ' ' + pointType);
			modal.find('.modal-body').text("Are you sure you want to " + action + " {{ $user->full_name }}'s " + pointType + '?');
			modal.find('.modal-footer').show();
			$('#action').val(actionId);
			$('#point-id').val(pointId);
			break;
		case 'unsuspend':
			var actionId = 1;
			modal.find('.modal-title').text('');
			modal.find('.modal-body').text('');
			modal.find('.modal-footer').hide();
			$.ajax({
				url: '{{ url('points/customers/active-check?user_id='.$user->id) }}&point_type=' + pointType,
				success: function (result) {
					if (result) {
						modal.find('.modal-title').text('(Error) ' + action.charAt(0).toUpperCase() + action.slice(1) + ' ' + pointType);
						modal.find('.modal-body').text('You cannot have more than one active point with same type at once.');
					} else {
						modal.find('.modal-title').text(action.charAt(0).toUpperCase() + action.slice(1) + ' ' + pointType);
						modal.find('.modal-body').text("Are you sure you want to " + action + " {{ $user->full_name }}'s " + pointType + '?');
						modal.find('.modal-footer').show();
						$('#action').val(actionId);
						$('#point-id').val(pointId);
					}
				}
			});
			break;
		case 'delete':
			var actionId = 2;
			modal.find('.modal-title').text(action.charAt(0).toUpperCase() + action.slice(1) + ' ' + pointType);
			modal.find('.modal-body').text("Are you sure you want to " + action + " {{ $user->full_name }}'s " + pointType + '?');
			modal.find('.modal-footer').show();
			$('#action').val(actionId);
			$('#point-id').val(pointId);
			break;
	}
});

$('#remark').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var remark = button.data('remark');
	var pointId = button.data('point-id');
	var transactionId = button.data('transaction-id');
	var modal = $(this);

	modal.find('.modal-body textarea').text(remark);
	$('#transaction-id').val(transactionId);
});
@stop
