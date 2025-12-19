@extends('layouts.master')

@section('title') Driver @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
           <h1 class="page-header">Driver Management 
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}driver"><i class="fa fa-refresh"></i></a>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}driver/create"><i class="fa fa-plus"></i></a>
                    @endif
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Driver Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-driver">
                    <thead>
                        <tr>
                            <th style="width:20px;">ID</th>
                            <th >Name</th>            
                            <th >Contact No</th>
                            <th >Type</th>
                            <th >Status</th>
                            <th class="col-lg-1">Action</th>
                        </tr>
                    </thead>
         
                </table>
            </div>
        </div>
    </div>
    
     
</div>

@stop

@section('script')
    $('#dataTables-driver').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('driver/driver') }}",
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
            { "data" : "4" },     
            { "data" : "5" },       ]
    });

    $(document).on("click", "#deleteDriver", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this driver- " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete driver id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
    
@stop