@extends('layouts.master')

@section('title') Sellers @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Seller Management<span class="pull-right">
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
            <h3 class="panel-title"><i class="fa fa-list"></i> Seller Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTables-seller">
         
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Created Time</th>
                            <th>Username</th>
                            <th>Company Name</th>            
                            <th>NRIC/Passport</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Mobile Phone</th>
                            <th>Credit Term</th>
                            <th>Business Method</th>
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
    <!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
    <a href="/seller/create" class="btn btn-large btn-success">Add Seller</a>
    @endif -->
     
</div>
@stop

@section('script')
    $('#dataTables-seller').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('seller/sellers') }}",
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
            { "data" : "4", "visible" : false, "orderable" : false  },
            { "data" : "5", "orderable" : false, "searchable" : false },
            { "data" : "6" },
            { "data" : "7" },
            { "data" : "8" },
            { "data" : "9" },
            { "data" : "10" },
            { "data" : "11" },
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