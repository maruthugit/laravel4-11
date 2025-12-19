@extends('layouts.master')
@section('title') Shopee @stop
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
           <h1 class="page-header">Shopee Confirmed Orders</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
                    <div class="col-md-4">
                         <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','asif','joel'), true ) ) {  ?>
                        <button type="button" id="migrate-orders" class="btn btn-primary pull-right" style="width:100%;"><i class="fa fa-arrow-circle-down"></i> Migrate Latest Order</button>
                        <?php } ?>
                    </div>
                    <div class="col-md-8 ">
                        <div class="btn-group btn-group pull-right" role="group" aria-label="Small button group"> 
                            <input type="hidden" id="acc-type-hold" value="1" >
                            <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee@jocom.my)">Shopee Jocom</button> 
                            <!--<button type="button" class="btn btn-default btn-acc" acc-type="2" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee@jocom.my)">Shopee Coca-Cola</button>-->
                            <button type="button" class="btn btn-default btn-acc" acc-type="3" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee@jocom.my)">Shopee Yeo Hiap Seng</button> 
                            <!--<button type="button" class="btn btn-default btn-acc" acc-type="4" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee@jocom.my)">Shopee F&N </button>-->
                            <!--<button type="button" class="btn btn-default btn-acc" acc-type="5" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee.orientalfood@jocom.my)">Shopee OrientalFoodMY </button> -->
                            <!--<button type="button" class="btn btn-default btn-acc" acc-type="6" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee.nikudo@jocom.my)">Shopee NikudoSeafood </button> -->
                            <button type="button" class="btn btn-default btn-acc" acc-type="7" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee.starbucks@jocom.my)">Shopee Starbucks.OS </button> 
                            <!--<button type="button" class="btn btn-default btn-acc" acc-type="8" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee.kawanfood@jocom.my)">Shopee KawanFood</button> -->
                            <button type="button" class="btn btn-default btn-acc" acc-type="9" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(shopee.pokka@jocom.my)">Shopee Pokka</button>
                            <button type="button" class="btn btn-default btn-acc" acc-type="10" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(etika@jocom.my)">Shopee Etika</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="11" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(ebfrozen@jocom.my)">Shopee Ebfrozen</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="12" data-toggle="popover" data-trigger="hover" data-placement="top" title="Shopee Account" data-content="(everbest@jocom.my)">Shopee Everbest</button> 
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
                <table class="table table-bordered table-striped table-hover" id="dataTables-driver">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Order Number</th>            
                            <th width="25%">Customer</th>
                            <th width="10%">Phone</th>
                            <th style="text-align: center;">Status</th>
                            <th width="10%">Transaction ID</th>
                            <th ></th>
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
        
//        $('[data-toggle="popover"]').popover(); 
        
        $(".btn-acc").click(function(){
            $(".btn-acc").removeClass('active');
            $(this).addClass('active');
            console.log($(this).attr('acc-type'));
            $("#acc-type-hold").val($(this).attr('acc-type'));
        });
        
    });
</script>
@stop
@section('script')

    $('#dataTables-driver').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/shopee/orderslisting') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'ordersn', name: 'order_number' },
            { data: 'name', name: 'customer_name' },
            { data : "phone"},
            <!--{ data: 'customer_email', name: 'customer_email' },-->
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
            //{ data: 'transaction_id', name: 'transaction_id' },
            {
           
               data: function ( row, type, val, meta ) {
               var TransferUrl = '/transaction/add/4/' +row.id ;
               var createCustomerURL = '/customer/create/4/'+row.id;
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
    
    
    $("#migrate-orders").click(function(){
        migrate();
    })
    
    
    $('body').on('click', '.revert-order', function(){ 
            
        var order_id = $(this).attr('data-order');
        $.ajax({
            method: "POST",
            url: "/shopee/revert",
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
    
    function migrate(){
    
    
        var account_type = $("#acc-type-hold").val();
        console.log(account_type);
        $.ajax({
            method: "POST",
            url: "/shopee/migrate",
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
                if(data.response == 0){
                    alert(data.totalRecords+ ' record(s) migrated');
                    if(data.totalRecords > 0 ){
                        location.reload();
                    }
                    
                }
            }
          })
    }


@stop