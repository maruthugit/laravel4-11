@extends('layouts.master')
@section('title') Coupon @stop
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Coupon Management
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}coupon"><i class="fa fa-refresh"></i></a> 
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND'))
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}coupon/add"><i class="fa fa-plus"></i></a>
            @endif
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Coupon Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-coupon">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Coupon Code</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>

<form id="remove_frm" name="remove_frm" action="{{asset('/')}}coupon/remove" method="post">
  <input type="hidden" name="remove_coupon_id" id="remove_coupon_id" value="" />
</form>


<script type="text/javascript">
function delete_coupon(coupon_id) {
    if(confirm("Are you sure to delete this coupon")) {
        

        var tempid = document.getElementById("remove_coupon_id");
        tempid.value = coupon_id;

        // var nameValue = document.getElementById("remove_transaction_id").value;
        // alert(nameValue);

        var tempform = document.getElementById("remove_frm");
        tempform.submit();
        
    }
    
}

</script>
@stop


@section('script')
    $('#dataTables-coupon').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('coupon/listing') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "id"},
        { "data" : "coupon_code"},
        { "data" : "name"},
        { "data" : "amount" },
        { "data" : "status" },
        { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });
@stop


