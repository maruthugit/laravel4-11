@extends('layouts.master')
@section('title') Courier @stop
@section('content')
<style>
    .center{
        text-align: center;
    }
    .loading {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999999;
        /*background: #3d464d;*/
        background: #FFF;
        opacity: 1.00;
        display: none;
        }
    .loading #load-message {
        width: 40px;
        height: 40px;
        position: absolute;
        left: 50%;
        right: 50%;
        bottom: 50%;
        top: 50%;
        margin: -20px;
    }
</style>
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Couriers</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Orders </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-driver">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="7%">Transaction ID</th> 
                            <th width="5%">Batch ID</th> 
                            <th width="7%">Reference Number</th> 
                            <th width="18%">Product</th>
                            <th width="3%">Qty</th>
                            <th width="3%">Courier</th>
                            <th width="7%">Tracking Number</th>
                            <th width="5%" style="text-align: center;">Logistic Status</th>
                            <th width="5%" style="text-align: center;">Batch Status</th>
                            <th width="5%" style="text-align: center;">Courier Status</th>
                            <th width="3%" style="text-align: center;">Slip </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
@section('inputjs')
<script>
 $( document ).ready(function() {
     
     //   'id','courier_id','batch_id','transaction_item_logistic_id','tracking_no','product_id','quantity','remarks','api_post'
 $('#dataTables-driver').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/courier/list') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            //{ data: 'transaction_id', name: 'transaction_id' },
            { data: function ( row, type, val, meta ) {
                    return '<a target="_blank" href="transaction/edit/'+row.transaction_id+'">'+row.transaction_id+'</a>';
                 }
            },
            //{ data: 'batch_id', name: 'batch_id' },
            { data: function ( row, type, val, meta ) {
                    return '<a target="_blank" href="/batch/edit/'+row.batch_id+'">'+row.batch_id+'</a>';
                 }
            },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'product_name', name: 'product_name' },
            { data: 'quantity', name: 'quantity' },
            { data: 'courier_name', name: 'courier_name' },
            { data: 'tracking_no', name: 'tracking_no' },
            { data: function ( row, type, val, meta ) {
                    if(row.LogisticStatus==0){
                        return '<span class="label label-warning">Pending</span>';
                    }
                    else if(row.LogisticStatus==1){
                        return '<span class="label label-danger">Undelivered</span>';
                    }
                    else if(row.LogisticStatus==2){
                        return '<span class="label label-info">Partial Sent</span>';
                    }
                    else if(row.LogisticStatus==3){
                        return '<span class="label label-danger">Returned</span>';
                    }
                    else if(row.LogisticStatus==4){
                        return '<span class="label label-info">Sending</span>';
                    }
                    else if(row.LogisticStatus==5){
                        return '<span class="label label-success">Sent</span>';
                    }
                    else{
                        return '<span class="label label-danger">Cancelled</span>';
                    }
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                    if(row.BatchStatus==0){
                        return '<span class="label label-warning">Pending</span>';
                    }
                    else if(row.BatchStatus==1){
                        return '<span class="label label-info">Sending</span>';
                    }
                    else if(row.BatchStatus==2){
                        return '<span class="label label-danger">Returned</span>';
                    }
                    else if(row.BatchStatus==3){
                        return '<span class="label label-danger">Undelivered</span>';
                    }
                    else if(row.BatchStatus==4){
                        return '<span class="label label-success">Sent</span>';
                    }
                    else{
                        return '<span class="label label-danger">Cancelled</span>';
                    }
                },
                className: "center"
            },
            { data: 'courier_status', name: 'courier_status' },
            { data: function ( row, type, val, meta ) {
                    
                    if(row.courier_id == 1){
                        return "<a class='btn btn-default'  target='_blank' href='/courier/slip/"+row.id+"'><i class='fa fa-file-pdf-o'></i></a";
                    }else if(row.courier_id == 4){
                        return "<a class='btn btn-default'  target='_blank' href='/jlogistic/macrolinkdomesticlabel/"+row.id+"'><i class='fa fa-file-pdf-o'></i></a";
                    }else{
                        return '';
                    }
                    
                },
                className: "center"
            },
           
            ]
    });
        });
</script>
@stop