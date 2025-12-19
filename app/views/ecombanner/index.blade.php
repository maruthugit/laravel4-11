@extends('layouts.master')

@section('title') Banners @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">eCommunity Banner Management<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}ecombanner"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/ecombanner/create"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Banner Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-banner">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-8">Image</th>
                            <th class="col-sm-2">QR Code</th>
                            <!-- <th class="text-center col-sm-1">Sequence</th> -->
                            <th class="text-center col-sm-1">Action</th>
                        </tr>
                    </thead>
         
                </table>
            </div>
        </div>
    </div>
   <!--  @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
    <a href="/banner/create" class="btn btn-large btn-success">Add Banner</a>
    @endif  -->   
</div>

@stop
@section('script')
    $('#dataTables-banner').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('ecombanner/banners') }}",
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false, "searchable" : false },
            { "data" : "1", "orderable" : false, "searchable" : false },
            { "data" : "2", "orderable" : false, "searchable" : true },
            { "data" : "3", "orderable" : false, "searchable" : false },
            
        ]
    });

    $(document).on("click", "#deleteBan", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this banner - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete banner id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop