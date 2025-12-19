@extends('layouts.master')

@section('title') Purchase Order @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Purchase Order<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}purchase-order"><i class="fa fa-refresh"></i></a>
                 @if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/purchase-order/create"><i class="fa fa-plus"></i></a>
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
            <h3 class="panel-title"><i class="fa fa-list"></i> Purchase Order Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTables-po">
         
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>PO Number</th>
                            <th>PO Date</th>
                            <th>Total</th>
                            <th>Seller Company</th>
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
     <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Signed PO</h4>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data"  action="/purchase-order/uploadsignpo">
            <div class="form-group">
           <input type="file" name="signedpo[]" class="form-control" accept=".pdf" multiple required>
           <input type="hidden" name="signpoid" value="" id="signpoid">

       </div>
       
      </div>

      <div class="modal-footer">
       <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
      </div> 

        </form>
    </div>

  </div>
</div>  
</div>
</div>
@stop

@section('script')
    $('#dataTables-po').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('purchase-order/orders') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0" },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "5" },
            { "data" : "3" },
            { "data" : "4" },
            { "data" : "6" },
        ]
        
    });
   $(document).on("click", "#deletePO", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Cancel Order",
            message: "Are you sure to Cancel this purchase order - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
 
   $(document).on("click", "#signedpo", function(e) {
        var link = $(this).attr("href");
        var status=$(this).attr("data-value");
        e.preventDefault();
        $('#signpoid').val(status);


    });
    $(document).on("click", "#generateEInv", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to generate eInvoice this purchase order - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
    
@stop