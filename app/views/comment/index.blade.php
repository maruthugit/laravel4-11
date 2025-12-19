@extends('layouts.master')

@section('title') Comment @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Comments Management 
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 5, 'AND'))
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}comment"><i class="fa fa-refresh"></i></a>
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="/comment/create"><i class="fa fa-plus"></i></a></span>
            @endif
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Comment Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-comments">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th class="col-sm-3">Date</th>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Comment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th class="text-center col-sm-1">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
@stop

@section('script')
    $('#dataTables-comments').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('comment/comments') }}",
        "order" : [[ 1, 'desc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "visible" : false, "orderable" : false, "searchable" : false },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "3" },
            { "data" : "4", "orderable" : false },
            { "data" : "5" },
            { "data" : "6" },
            { "data" : "7", "orderable" : false, "searchable" : false, "className" : "text-center" },
        ]
    });

    $(document).on("click", "#deleteItem", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete product id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop