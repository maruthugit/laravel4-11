@extends('layouts.master')
@section('title', 'Transaction')
@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Transaction Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction"><i class="fa fa-refresh"></i></a>
                    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction/add"><i class="fa fa-plus"></i></a>
                    @endif -->
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="transaction_from">Transaction From</label>
                                    <div class="input-group" id="datetimepicker_from">
                                        {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'class' => 'form-control', 'tabindex' => 1]) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="transaction_to">Transaction To</label>
                                    <div class="input-group" id="datetimepicker_to">
                                        {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'class' => 'form-control', 'tabindex' => 2]) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    {{ Form::text('username', Input::get('username'), ['id' => 'username', 'class' => 'form-control', 'tabindex' => 3]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    {{ Form::select('status', [
                                        'any' => 'Any',
                                        'pending' => 'Pending',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                        'refund' => 'Refund',
                                    ], Input::get('status'), ['id' => 'status', 'class' => 'form-control', 'tabindex' => 4]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_from">Amount From</label>
                                    {{ Form::text('amount_from', Input::get('amount_from'), ['id' => 'amount_from', 'class' => 'form-control', 'tabindex' => 5]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Amount To</label>
                                    {{ Form::text('amount_to', Input::get('amount_to'), ['id' => 'amount_to', 'class' => 'form-control', 'tabindex' => 6]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Label Name</label>
                                    {{ Form::text('product_name', Input::get('product_name'), ['id' => 'product_name', 'class' => 'form-control', 'tabindex' => 7]) }}
                                    <span class="help-block">Multiple products separated by comma.</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Product SKU</label>
                                    {{ Form::text('product_sku', Input::get('product_sku'), ['id' => 'product_sku', 'class' => 'form-control', 'tabindex' => 8]) }}
                                    <span class="help-block">Multiple products separated by comma.</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Coupon Code</label>
                                    {{ Form::text('coupon', Input::get('coupon'), ['id' => 'coupon', 'class' => 'form-control', 'tabindex' => 9]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Agent Code</label>
                                    {{ Form::text('agent', Input::get('agent'), ['id' => 'agent', 'class' => 'form-control', 'tabindex' => 10]) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="payment">Payment</label>
                                    <select class="form-control" name="payment_option">
                                        <option name="default"></option>
                                        <option name="Cash" value="Cash"<? if(@$_POST['payment_option'] == 'Cash') { echo 'selected = \"selected\"'; } ?>>Cash</option>
                                        <option name="MOLPay" value="MOLPay"<? if(@$_POST['payment_option'] == 'MOLPay') { echo 'selected = \"selected\"'; } ?>>MOLPay</option>
                                        <option name="mPAY" value="mPAY"<? if(@$_POST['payment_option'] == 'mPAY') { echo 'selected = \"selected\"'; } ?>>mPAY</option>
                                        <option name="Revpay" value="Revpay"<? if(@$_POST['payment_option'] == 'Revpay') { echo 'selected = \"selected\"'; } ?>>Revpay</option>
                                        <option name="Boost" value="Boost"<? if(@$_POST['payment_option'] == 'Boost') { echo 'selected = \"selected\"'; } ?>>Boost</option>
                                        <option name="GrabPay" value="GrabPay"<? if(@$_POST['payment_option'] == 'GrabPay') { echo 'selected = \"selected\"'; } ?>>GrabPay</option>
                                        <option name="FavePay" value="FavePay"<? if(@$_POST['payment_option'] == 'FavePay') { echo 'selected = \"selected\"'; } ?>>FavePay</option>
                                        <option name="PayPal" value="PayPal"<? if(@$_POST['payment_option'] == 'PayPal') { echo 'selected = \"selected\"'; } ?>>PayPal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Reference Number</label>
                                    {{ Form::text('reference_number', Input::get('reference_number'), ['id' => 'reference_number', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Region</label>
                                    <select name="region_id" class="form-control">
                                        <?php if(count($region) > 1) { ?><option value="">All</option><?php } ?>
                                        <?php foreach ($region as $key => $value) { ?>
                                        <option value="<?php echo $value->id; ?>"><?php echo $value->region; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="amount_to">Delivery Address Type</label>
                                    <select name="delivery_address_type" class="form-control">
                                        <option value="">All</option>
                                        <option value="house">House</option>
                                        <option value="office">Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="invoice_number">Invoice Number</label>
                                    {{ Form::text('invoice_number', Input::get('invoice_number'), ['id' => 'invoice_number', 'class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 11]) }}
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Transaction Listing</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-transaction">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Inv. Number</th>
                                    <th>Transaction Date</th>
                                    <th>Username</th>
                                    <th>Total Amount</th>
                                    <th>Order Number</th>
                                    <th>Address Type</th>
                                    <th>State</th>
                                    <th>Payment Gateway</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="remove_frm" name="remove_frm" action="{{asset('/')}}transaction/remove" method="post">
    <input type="hidden" name="remove_transaction_id" id="remove_transaction_id" value="">
</form>

<script type="text/javascript">
function delete_transaction(transaction_id) {
    if (confirm("Are you sure to delete this transaction")) {
        var tempid = document.getElementById("remove_transaction_id");
        tempid.value = transaction_id;

        // var nameValue = document.getElementById("remove_transaction_id").value;
        // alert(nameValue);

        var tempform = document.getElementById("remove_frm");
        tempform.submit();
    }
}
</script>
@stop

@section('script')
$('#dataTables-transaction').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('transaction/listing?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
    // { "data" : "0", "searchable" : false},
    // { "data" : "1", "orderable" : false, "searchable" : false}
    { "data" : "id"},
    { "data" : "invoice_no", orderable: false, searchable: true, 'className' : 'text-center'},
    { "data" : "transaction_date"},
    { "data" : "buyer_username" },
    { "data" : "total" },
    { "data" : "order_number" },
    { "data" : "delivery_area_type", orderable: true, searchable: true, 'className' : 'text-center'},
    { "data" : "delivery_state" },
    { "data" : "paymentgateway" },
    { "data" : "status" },
    { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
    ]
});

$(function() {
    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
@stop
