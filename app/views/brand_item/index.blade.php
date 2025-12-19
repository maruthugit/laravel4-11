@extends('layouts.master')

@section('title') Brands @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Brands Management<span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}brands"><i class="fa fa-refresh"></i></a>
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 5, 'AND'))
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="/brands/create"><i class="fa fa-plus"></i></a>
            @endif
            </span></h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Brand Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-brands">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Image</th>
                                    <th class="col-sm-3">QR Code</th>
                                    <th class="col-sm-1">Region</th> 
                                    <th class="text-center col-sm-1">Position</th>
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
    $('#dataTables-brands').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('brands/branditems') }}",        
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false, "searchable" : false },
            { "data" : "1", "orderable" : false },
            { "data" : "2", "orderable" : false },
            { "data" : "3", "orderable" : false, "className" : "text-center" },
            { "data" : "4", "orderable" : false, "searchable" : false, "className" : "text-center" },
            { "data" : "5", "orderable" : false, "searchable" : false, "className" : "text-center" },
        ]
    });

    $(document).on("click", "#deleteItem", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete? ID- " + $(this).attr("data-value") + " ?",
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