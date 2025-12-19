@extends('layouts.master')

@section('title') Users @stop

@section('content')

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Charity User Management<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}charity/user"><i class="fa fa-refresh"></i></a>
                @if(Permission::CheckAccessLevel(Session::get('role_id'), 22, 5, 'AND'))
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/charity/user/create"><i class="fa fa-plus"></i></a>
                @endif
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> User Listing </h3>
                </div>
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <table class="table table-bordered table-striped table-hover" id="dataTables-user">         
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="col-sm-1">Username</th>
                                    <th>Fullname</th>
                                    <th>Charity Name</th>
                                    <th class="col-sm-1">Email</th>
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
        <!-- /.col-lg-12 -->
    </div>
</div>
@stop
@section('script')
    $('#dataTables-user').dataTable({
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('charity/user/users') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "searchable" : false },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "3" },
            { "data" : "4", "orderable" : false, "searchable" : false },
            { "data" : "5", "orderable" : false, "searchable" : false },
            { "data" : "6", "orderable" : false, "searchable" : false },
        ]
    });

    $(document).on("click", "#deleteUser", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Inactive entry",
            message: "Are you sure to inactive this user - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Inactive user id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop