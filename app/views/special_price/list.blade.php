@extends('layouts.master')

@section('title') Refund @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">Special Pricing Management<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}special_price"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/special_price/add"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
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
        <div class="panel-heading">
            <h3 class="panel-title">Special Price Listing </h3>
        </div>

        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-refund">
                    <thead>
                        <tr>
                            <th class="col-sm-1">SP ID</th>
                            <th class="text-center col-sm-1">SP Group Name</th>
                            <th class="text-center col-sm-1">Created Date</th>       
                            <th class="text-center col-sm-1">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
@section('script')
    $('#dataTables-refund').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('special_price/listing') }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false },
            { "data" : "1", "class" : "text-left", "orderable" : false, "searchable" : false },
            { "data" : "2", "class" : "text-center", "orderable" : false },
            { "data" : "3", "class" : "text-center" },
        ]
    });

    $(document).on("click", "#deleteRefund", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this refund - ID: " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete refund id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop