@extends('layouts.master')

@section('title') SP Group @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
             <h1 class="page-header">Special Price Groups<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}special_price/group"><i class="fa fa-refresh"></i></a>
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 13, 5, 'AND'))
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/special_price/group/create"><i class="fa fa-plus"></i></a>
                @endif
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Group Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
                <table class="table table-bordered table-striped table-hover" id="dataTables-group">
                    <thead>
                        <tr>
                            <th style="width:20px;">ID</th>
                            <th>Group Name</th>
                            <th>Seller Name</th>            
                            <th class="col-sm-2">Created at</th>
                            <th class="col-sm-2 text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        <!--</div>-->
        </div>
    </div>
    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
    <a href="/special_price/group/create" class="btn btn-large btn-success">Add Group</a>
    @endif -->
</div>

@stop

@section('script')
    $('#dataTables-group').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('special_price/group/groups') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "searchable" : false },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "3" , "className" : "text-center" },
           // { "data" : "4", "visible": false, "orderable" : false},
           // { "data" : "5", "orderable" : false, "searchable" : false },
           // { "data" : "6" },
            //{ "data" : "7" },
            { "data" : "4" , "className" : "text-center" },
        ]
    });

    $(document).on("click", "#deleteGroup", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this group - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete group id ");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
    
@stop