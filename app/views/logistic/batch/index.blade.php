@extends('layouts.master')

@section('title') Logistic Batch @stop

@section('content')
<div id="page-wrapper">
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
           <h1 class="page-header"> Logistic Batch Management 
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}batch"><i class="fa fa-refresh"></i></a>
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
 
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Batch Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-batch">
                    <thead>
                        <tr>
                           <th class="col-lg-1">ID</th>
                            <th class="col-lg-1">Batch Date</th>
                            <th class="col-lg-1">Transaction ID</th>
                            <th class="col-lg-1">Driver Username</th>
                            <th class="col-lg-2">Delivery Address</th>
                            <th class="col-lg-3">Special Message</th>
                            <th class="col-lg-1">Do No</th>
                            <th class="col-lg-1">Status</th>
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
    $('#dataTables-batch').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('batch/batch') }}",
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
            { "data" : "12" },  
            { "data" : "4" },     
            { "data" : "5" },   
            { "data" : "6" },
            { "data" : "13" },    ]
    });

    $(document).on("click", "#deleteBatch", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this batch- " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete batch id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
    
@stop