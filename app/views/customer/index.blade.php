@extends('layouts.master')

@section('title', 'Customers')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Customer Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}customer"><i class="fa fa-refresh"></i></a>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
                        <a class="btn btn-primary" title="" data-toggle="tooltip" href="/customer/create"><i class="fa fa-plus"></i></a>
                    @endif
                </span>
            </h1>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
        </div>
        <div class="panel-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="username">Username</label>
                            {{ Form::text('username', Input::get('username'), ['id' => 'username', 'class' => 'form-control', 'tabindex' => 1]) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="username">Full Name</label>
                            {{ Form::text('full_name', Input::get('full_name'), ['id' => 'full_name', 'class' => 'form-control', 'tabindex' => 2]) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="username">NRIC/Passport</label>
                            {{ Form::text('ic_passport', Input::get('ic_passport'), ['id' => 'ic_passport', 'class' => 'form-control', 'tabindex' => 3]) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="username">Mobile Phone</label>
                            {{ Form::text('mobile_no', Input::get('mobile_no'), ['id' => 'mobile_no', 'class' => 'form-control', 'tabindex' => 4]) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            {{ Form::select('status', ['any' => 'Any', 'active' => 'Active only', 'inactive' => 'Inactive only'], Input::get('status'), ['id' => 'status', 'class' => 'form-control', 'tabindex' => 5]) }}
                        </div>
                    </div>
                </div>
                {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 6]) }}
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Customer Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
                <table class="table table-bordered table-striped table-hover" id="dataTables-customer">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-lg-2">Username</th>
                            <th class="col-lg-2">Fullname</th>
                            <th class="col-sm-1">NRIC/Passport</th>
                            <th class="col-sm-1">Email</th>
                            <th class="col-sm-1">Mobile Phone</th>
                            <th class="col-sm-1">Status</th>
                            <th class="col-lg-1">Platform</th>
                             <th class="col-lg-1">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
        <a href="/customer/create" class="btn btn-large btn-success">Add Customer</a>
    @endif -->
</div>
<?php //die('In'); ?>

@stop

@section('script')
$('#dataTables-customer').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('customer/customers?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data": "0", "searchable" : false },
        { "data": "1" },
        { "data": "2" },
        { "data": "3" },
        { "data": "4", "visible": false, "orderable" : false},
        { "data": "5", "orderable" : false, "searchable" : false },
        { "data": "6" },
        { "data": "7" },
        { "data": "8" }
     
    ]
});

$(document).on("click", "#deleteCust", function(e) {
    var link = $(this).attr("href");
    e.preventDefault();
    bootbox.confirm({
        title: "Delete entry",
        message: "Are you sure to delete this customer - " + $(this).attr("data-value") + " ?",
        callback: function (result) {
            if (result === true) {
                console.log("Delete customer id");
                window.location = link;
            } else {
                console.log("IGNORE");
            }
        }
    });
});
@stop
