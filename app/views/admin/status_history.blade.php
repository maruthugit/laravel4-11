@extends('layouts.master')

@section('title') History @stop

@section('content')

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <h1 class="page-header">History<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}transaction/history"><i class="fa fa-refresh"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Status Changes Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none;">
                <table class="table table-bordered table-striped table-hover" id="dataTables-result">
         
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Logistic ID</th>    
                            <th>Batch ID</th>
                            <th>Old Status</th>    
                            <th>New Status</th>
                            <th>Type</th>
                            <th>Modify By</th>  
                            <th>Modify Date</th>  
                        </tr>
                    </thead>
         
                </table>
            </div>
        </div>
    </div>
    <!-- @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
    <a href="/sysadmin/user/create" class="btn btn-success">Add User</a>
    @endif -->
</div>
@stop
@section('script')
    $('#dataTables-result').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('transaction/historylisting') }}",
       
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "trans_id"},
            { "data" : "logistic_id"},
            { "data" : "batch_id"},
            { data: function ( row, type, val, meta ) {
                if(row.old_status==0){
                    return 'pending';
                }
                if(row.old_status==1){
                    return 'sending';
                }
                if(row.old_status==2){
                    return 'returned';
                }
                if(row.old_status==3){
                    return 'undelivered';
                }
                if(row.old_status==4){
                    return 'sent';
                }
                if(row.old_status==5){
                    return 'cancelled';
                }
                else{
                    return row.old_status;
                }
            },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                if(row.status==0){
                    return 'pending';
                }
                if(row.status==1){
                    return 'sending';
                }
                if(row.status==2){
                    return 'returned';
                }
                if(row.status==3){
                    return 'undelivered';
                }
                if(row.status==4){
                    return 'sent';
                }
                if(row.status==5){
                    return 'cancelled';
                }
                else{
                    return row.status;
                }
            },
                className: "center"
            },
            { "data" : "type"},
            { "data" : "modify_by"},
            { "data" : "modify_date"},
        ],
    });
 
@stop