@extends('layouts.master')
@section('title') Leader Board @stop
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
           <h1 class="page-header">Leader Board</h1>
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
                            <th width="2%">ID</th>
                            <th width="5%">email</th> 
                            <th width="5%">Name</th> 
                            <th width="5%">Tracking Number</th>
                            <th width="5%">Platform</th>
                            <th width="5%">Order Number</th>
                            <th width="5%">Transaction ID</th>
                            <th width="5%">Amount</th>
                            <th width="5%" style="text-align: center;">Status</th>
                            <th width="3%" style="text-align: center;">Created At</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
    <div id="update-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Mail Tracking</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                      <label for="">Delivery Tracking Number</label>
                      <input type="text" class="form-control" id="tracking_number" placeholder="Tracking Number">
                    </div>
                    <div class="form-group">
                      <input type="hidden" class="form-control" id="card_id" value="" placeholder="">
                      <input type="hidden" class="form-control" id="card_status" value="" placeholder="">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary upt-mail-tracking">Save changes</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
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
        "ajax": "{{ URL::to('/boardlist') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'email', name: 'email' },
            { data: 'name', name: 'name' },
            { data: 'tracking_number', name: 'tracking_number' },
            {data: function ( row, type, val, meta ) {
                    if(row.platform == 1){ 
                        var content =  'Jocom APP';
                        return content;
                    }
                    if(row.platform == 2){ 
                        var content =  '11 Street';
                        return content;
                    }
                    if(row.platform == 3){ 
                        var content =  'LAZADA';
                        return content;
                    }
                    if(row.platform == 4){ 
                        var content =  'Qoo10';
                        return content;
                    }
                    if(row.platform == 5){ 
                        var content =  'Shopee';
                        return content;
                    }
                    
                    
                },
                className: "center"
            },
            { data: 'order_number', name: 'order_number' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'amount', name: 'amount' },
            { data: 'is_approved', name: 'is_approved' },
            { data: 'created_at', name: 'created_at' }
            ]
    });
    
    $('body').on('click', '.upt-mail', function(){ 
        
        var card_id = $(this).attr('data-id');
        var card_status = $(this).attr('data-card-status');
        $("#card_id").val(card_id);
        $("#card_status").val(card_status);
        $("#update-modal").modal("show");

    });
    
    
    $('body').on('click', '.upt-mail-tracking', function(){ 
            
        var tracking_number = $("#tracking_number").val();
        var card_id = $("#card_id").val();
        var card_status = $("#card_status").val();
        
        $.ajax({
            method: "POST",
            url: "/mpay/update/mail",
            datatype: "json",
            data: {
                'card_id':card_id,
                'tracking_number':tracking_number,
                'card_status':card_status
            },
            beforeSend: function(){
            },
            success: function(data) {
                
                console.log(data);
                
            }
        })

    });
    
    
        });
</script>
@stop