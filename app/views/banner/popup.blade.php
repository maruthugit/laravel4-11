@extends('layouts.master')

@section('title') Popup Banner @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">Popup List<span class="pull-right">
                <a class="btn btn-default" title="" data-toggle="tooltip" href="/banner/popupcreate"><i class="fa fa-plus"></i> Create New</a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Popup Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table  table-striped table-hover" id="dataTables-banner">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-2">Title</th>
                            <th class="col-sm-2">QR Code</th>
                            <th class="col-sm-1">Category ID</th>
                            <th class="col-sm-1">From Date</th>
                            <th class="col-sm-1">To Date</th>
                            <th class="col-sm-1">Country</th>         
                            <th class="col-sm-1">Region</th>         
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
@section('inputjs')
<script>
$(document).ready(function(){

 var list = $('#dataTables-banner').dataTable({
            "autoWidth" : false,
            "processing": true,
            "language": {
             "loadingRecords": "Loading...",
             "processing":    '<div class="ui active inverted dimmer"> <div class="ui medium text loader"></div></div>'
            },
            "serverSide": true,
            "ajax": "/banner/getpopup",
            "order" : [[0,'desc']],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : ""
            },{ "sClass": "td-1st", "aTargets": [ 2 ] }
            ,{ "sClass": "td-2st", "aTargets": [ 3 ] }],
            "columns" : [
                
                { data: 'id', name: 'id' },
                { data: 'description', name: 'description' },
                { data: 'qr_code', name: 'qr_code' },
                { data: 'category_id', name: 'category_id' },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'country_name', name: 'country_name' },
                { data: 'region_name', name: 'region_name' },
                {
                    data: function ( row, type, val, meta ) {
                        var url = '/banner/popupedit/'+row.id;
                        var content =  '<a href="'+url+'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
                        return content;
                    },
                    className: "center"
                },
               
                ]
        });
});
        </script>
@stop


@stop