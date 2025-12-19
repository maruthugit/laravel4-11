@extends('layouts.master')

@section('title') Sellers @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">GS Vendor<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}seller"><i class="fa fa-refresh"></i></a>
                 @if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/seller/create"><i class="fa fa-plus"></i></a>
                 @endif
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif

     <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> GS Vendor </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTables-seller">
         
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>GS Vendor Name</th>
                            <th>Address</th>        
                            <th>Phone Number</th>         
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</div>
@stop

@section('script')
    $('#dataTables-seller').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('seller/gsvendors') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "searchable" : false },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "3", "visible" : false, "orderable" : false  },
            { "data" : "4", "orderable" : false, "searchable" : false },
            { "data" : "5" },
        ]
        
    });

    $(document).on("click", "#deleteSeller", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this seller - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete seller id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
    
@stop