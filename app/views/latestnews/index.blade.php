@extends('layouts.master')

@section('title') Latest News @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Latest News Management<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}latestnews"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/latestnews/create"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>


    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Latest News Listing </h3>
    </div>
    <div class="panel-body">

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="dataTables-latestnews">
             <thead>
                <tr>
                    <th class="col-sm-1">ID</th>
                    <th class="col-sm-2">Image</th>
                    <th class="col-sm-3">QR Code</th>
                    <th class="text-center col-sm-1">Sequence</th>
                    <th class="text-center col-sm-1">Action</th>
                </tr>
            </thead>
 
     </table>
    </div>

   </div>
   </div>

    <!-- <a href="/latestnews/create" class="btn btn-large btn-success">Add Latest News</a> -->

</div>

@stop

@section('script')
    $('#dataTables-latestnews').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('latestnews/latestnews') }}",
        "order" : [[3,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false, "searchable" : false },
            { "data" : "1", "orderable" : false, "searchable" : false },
            { "data" : "2", "orderable" : false, "searchable" : false },
            { "data" : "3" },
            { "data" : "4", "orderable" : false, "searchable" : false },
            
        ]
    });

    $(document).on("click", "#deleteNews", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this latest news - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete latest news id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop