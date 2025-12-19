@extends('layouts.master')
@section('title') Help Center @stop
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Help Center Management
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}helpcenter"><i class="fa fa-refresh"></i></a> 
        
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Help Center Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-helpcenter">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Order ID</th>
                                    <th>Topic</th>
                                    <th>Description</th>
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

<form id="remove_frm" name="remove_frm" action="{{asset('/')}}helpcenter/remove" method="post">
  <input type="hidden" name="remove_helpcenter_id" id="remove_helpcenter_id" value="" />
</form>


<script type="text/javascript">
function delete_record(record_id) {
    if(confirm("Are you sure to delete this Ticket")) {
        

        var tempid = document.getElementById("remove_helpcenter_id");
        tempid.value = record_id;

        var tempform = document.getElementById("remove_frm");
        tempform.submit();
        
    }
    
}

</script>
@stop


@section('script')
    $('#dataTables-helpcenter').dataTable({
        "autoWidth" : true,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('helpcenter/listing') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "id"},
        { "data" : "username"},
        { "data" : "order_id" },
        { "data" : "query_topic"},
        { "data" : "description" },
        { "data" : "status" },
        { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });
@stop


