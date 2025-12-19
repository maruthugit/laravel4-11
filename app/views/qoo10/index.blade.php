@extends('layouts.master')
@section('title') Qoo10 @stop
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
           <h1 class="page-header">Qoo10 Orders</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
            <div class="col-md-2">
                <button type="button" id="migrate-orders" class="btn btn-primary pull-right" style="width:100%;"><i class="fa fa-arrow-circle-down"></i> Migrate Order</button>
            </div>
            <div class="col-md-10 ">
                <input type="hidden" id="acc-type-hold" value="1" >
                <div class="btn-group btn-group pull-right" role="group" aria-label="Small button group"> 
                    <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="Qoo10 Account" data-content="(qoo10@jocom.my)">Qoo10 Jocom MY & SG</button> 
                    <!--<button type="button" class="btn btn-default  btn-acc" acc-type="2" data-toggle="popover" data-trigger="hover" data-placement="top" title="Qoo10 Account" data-content="(qoo10@jocom.my)">Qoo10 Singapore</button> -->
                    <button type="button" class="btn btn-default  btn-acc" acc-type="3" data-toggle="popover" data-trigger="hover" data-placement="top" title="Qoo10 F&N Account" data-content="(qoo10@jocom.my)">F&N</button> 
                </div>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Orders </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-orders">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Pack Number</th> 
                            <!-- <th width="10%">Order Number</th>  -->           
                            <th width="25%">Customer</th>
                            <th width="25%">Status</th>
                            <th width="10%">Transaction ID</th>
                            <th width="10%">Action</th>
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
        

        $(".btn-acc").click(function(){
            $(".btn-acc").removeClass('active');
            $(this).addClass('active');
            $("#acc-type-hold").val($(this).attr('acc-type'));
        });
         
        $("#migrate-orders").click(function(){
            migrate();
        })
        
        function migrate(){
            
            var account_type = $("#acc-type-hold").val();
            console.log(account_type);
            $.ajax({
                method: "Post",
                url: "/qoo10/migrate",
                data: { 
                    "list_type":account_type,
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                    $('.loading').hide();
                    alert(data.totalRecords+ ' record(s) migrated');
                    if(data.response == 0){
                        
                        if(data.totalRecords > 0 ){
                            location.reload();
                        }

                    }
                }
              })
        }
         
        // $("#fileCSV").change(function(){
        //     $("#selected-file").val($('#fileCSV')[0].files[0].name);
        // });
        
        // $("#upload-close").click(function(){
        //     location.reload();
        // })
        
        // $("#upload-submit").click(function(){
            
        //     $("#browse-input").hide();
        //     $("#loading-upload").show();
        //     $.ajax({
        //             url:'/eleven/orders/import',
        //             data:new FormData($("#form-upload-csv")[0]),
        //             dataType:'json',
        //             async:false,
        //             type:'post',
        //             processData: false,
        //             contentType: false,
        //             success:function(response){
        //               setTimeout(function(){ 
        //                   $("#loading-upload").hide(); 
        //                   $("#browse-input").show();
        //                   if(response.response == '1'){
        //                   $("#upload-success-msg-totalRecord").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalRecord+ ' order(s) imported');
        //                   $("#upload-success-msg-totalCSV").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalCSVRecord+ ' record(s) detected from CSV file');
        //                   $("#upload-success-msg-totalExisting").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalExisting+ ' record(s) already exist');
        //                   $("#modal-attention").hide();
        //                   $("#upload-success-container").show();
        //                   //alert(response.data.totalRecords+ ' record(s) updated successfully');
        //               }
        //               }
        //               , 2000);
                      
        //             },
        //         });
        // });
    });
</script>
@stop
@section('script')

    $('#dataTables-orders').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('qoo10/orderslisting') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id'},
            { data: 'packNo'},
            <!-- { data: 'orderNo'}, -->
            { data: 'buyer'},
            { data: function ( row, type, val, meta ) {
                    if(row.status == 1){ return '<span class="label label-warning">Pending</span>'};
                    if(row.status == 2){ return '<span class="label label-success">Transfered</span>'};
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                    if(row.transaction_id == 0){ return ' - '};
                    if(row.transaction_id != 0){ return '<span class="">'+row.transaction_id+'</span>'};
                },
                className: "center"
            },
            {
           
               data: function ( row, type, val, meta ) {
               var TransferUrl = '/transaction/add/3/' +row.id ;
               var createCustomerURL = '/customer/create/3/'+row.id;
               var editUrl = '/transaction/edit/'+row.transaction_id;
                    if(row.status == 1){ 
                    var content =  '<a href=" '+createCustomerURL+'"  target="_blank"><button type="button" class="btn btn-primary" style="margin-right:5px;"> Create Customer</button></a><a href=" '+TransferUrl+'"  target="_blank"><button type="button" class="btn btn-primary" style="margin-right:5px;"> Transfer</button></a>';
                        return content;
                    }
                    
                    if(row.status == 2){ 
                    var content =  '<a><button type="button" data-order="'+row.id+'" class="btn btn-primary revert-order" style="margin-right:5px;"> Revert</button></a><a href="'+editUrl+'"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="fa fa-pencil"></i></button></a>';
                        return content;
                    }
                },
                className: "center"
            }
            ]
    });
    
    $('body').on('click', '.revert-order', function(){ 
            
        var order_id = $(this).attr('data-order');
        console.log(order_id);
        $.ajax({
            method: "POST",
            url: "/qoo10/revert",
            datatype: "json",
            data: {
                'order_id':order_id
            },
            beforeSend: function(){
            },
            success: function(data) {
                console.log(data);
                
                if(data.response == 0){
                    alert('Order Reverted');
                    location.reload();
                }

            }
       })

    });
@stop