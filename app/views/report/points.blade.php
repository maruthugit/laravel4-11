@extends('layouts.master')

@section('title', 'Reward Points Report')

@section('content')
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script>
    $(function () {
        $('#from_date').datepicker({ dateFormat: "yy-mm-dd" }).val();
        $('#to_date').datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Reward Points Report</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message') OR $message)
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation"></i> {{ Session::get('message') }} {{ $message }}<button data-dismiss="alert" class="close" type="button">&times;</button>
                </div>
            @endif
            @if (Session::has('success') OR $success)
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }} {{ $success }}<button data-dismiss="alert" class="close" type="button">&times;</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Generate Report</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'report/points', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('email', 'Email To', ['class'=> 'col-lg-2 control-label']) }}
                            <div class="col-lg-3">
                                {{ Form::text('email', '', ['required' => 'required', 'placeholder' => 'abc@jocom.my, 123@jocom.my', 'class' => 'form-control']) }}
                                <p class="help-block">{{ $errors->first('email') }}</p>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('point_type', 'Point Type', ['class'=> 'col-lg-2 control-label']) }}
                            <div class="col-lg-4">
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('jpoint', 1, true) }} {{ JPoint }}
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('bcard', 1, true) }} {{ BCard }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('action_type', 'Action Type', ['class'=> 'col-lg-2 control-label']) }}
                            <div class="col-lg-4">
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 1, true) }} Earn
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 2, true) }} Redeem
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 3, true) }} Convert
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 4, true) }} Cash Buy
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 5, true) }} Cash Out
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 6, true) }} Reversal
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 7, true) }} Refund
                                    </label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="checkbox-inline">
                                        {{ Form::checkbox('action_type[]', 8, true) }} Void
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group @if ($errors->has('amount')) has-error @endif">
                            {{ Form::label('amount', '[Optional] Amount Range', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-2">
                                {{ Form::select('amount', ['0' => 'Not Applicable', '1' => 'From Point to Point'], '0', ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{ Form::text('amount_from', '0', ['required' => 'required', 'placeholder' => '0', 'class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-1">
                                {{ Form::text('amount_to', '100', ['required' => 'required', 'placeholder' => '100', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                            {{ Form::label('created', '[Optional] Date', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-2">
                                {{ Form::select('created', ['0' => 'Not Applicable', '1' => 'From Date to Date Ordered'], '0', ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                {{ Form::text('created_from', date('Y-m-d', strtotime('yesterday')), ['id' => 'from_date', 'placeholder' => 'yyyy-mm-dd', 'class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                {{ Form::text('created_to', date('Y-m-d'), ['id' => 'to_date', 'placeholder' => 'yyyy-mm-dd', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('', '', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-2">
                                <button class="btn btn-primary" type="submit">Generate</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
            @if (count($queue))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-list-alt"></i> Report Listing</h3>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>File Name</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queue as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ object_get($record, 'in_file') }}</td>
                                    <td>
                                        @if (object_get($record, 'status') == 0) In Queue @endif
                                        @if (object_get($record, 'status') == 1) In Process @endif
                                    </td>
                                    <td>{{ object_get($record, 'request_at') }}</td>
                                    <td>
                                        <a class="btn btn-primary" href="{{ url('process/report/'.object_get($record, 'id').'?type=point') }}">Process Now</a>
                                        <a class="btn btn-danger" href="{{ url('process/cancel/'.object_get($record, 'id').'?type=point') }}">Cancel</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
