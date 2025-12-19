@extends('layouts.master')
@section('title') PGMall @stop
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
           <h1 class="page-header">PGMall Confirmed Orders</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
                    <div class="col-md-4">
                        <!-- <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','asif','joel','nadzri'), true ) ) {  ?>
                        <button type="button" id="migrate-orders" class="btn btn-primary pull-right" style="width:100%;"><i class="fa fa-arrow-circle-down"></i> Migrate Latest Order</button>
                        <?php } ?> -->
                    </div>
                    {{-- <div class="col-md-8 ">
                        <div class="btn-group btn-group pull-right" role="group" aria-label="Small button group"> 
                            <input type="hidden" id="acc-type-hold" value="1" >
                            <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="PGMall Account" data-content="(pgmall@jocom.my)">PGMall Jocom</button> 
                        </div>
                    </div> --}}
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
        </div>
        <div class="panel-body">
            <form method="POST">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="transaction_from">Transaction From</label>
                            <div class="input-group" id="datetimepicker_from">
                                {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'class' => 'form-control', 'tabindex' => 1]) }}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="transaction_to">Transaction To</label>
                            <div class="input-group" id="datetimepicker_to">
                                {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'class' => 'form-control', 'tabindex' => 2]) }}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-3">
                        <div class="form-group">
                            <div class="input-group" id="datetimepicker_to">
                                <span class="input-group-btn">
                                <input type="hidden" id="acc-type-hold" value="1" >
                                <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="PGMall Account" data-content="(pgmall@jocom.my)">PGMall Jocom</button> 
                                </span>
                            </div>
                        </div>
                    </div> --}}
                </div>
                
                <div class="row">
                    <div class="col-lg-3">
                        <div class="btn-group btn-group pull-left" role="group" aria-label="Small button group"> 
                            <input type="hidden" id="acc-type-hold" value="1" >
                            <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="PGMall Account" data-content="(pgmall@jocom.my)">PGMall Jocom</button> 
                        </div>
                    </div>
                </div>
                {{-- {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 11]) }} --}}
                <div class="row">
                    <div class="col-lg-12">
                        <br>
                        <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','asif','joel','nadzri'), true ) ) {  ?>
                            <button type="button" id="migrate-orders" class="btn btn-primary" style="width:30%;"><i class="fa fa-arrow-circle-down"></i> Migrate Latest Order</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
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
        "ajax": "{{ URL::to('/pgmall/orderslisting') }}",
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
                        var TransferUrl = '/transaction/add/5/' +row.id ;
                        var createCustomerURL = '/customer/create/5/'+row.id;
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
        if ($('#transaction_from').val() == '' || $('#transaction_to').val() == ''){
            alert('Please fill in the date');
        } else {
            migrate();
        }
    })
    
    
    $('body').on('click', '.revert-order', function(){ 
            
        var order_id = $(this).attr('data-order');
        $.ajax({
            method: "POST",
            url: "/pgmall/revert",
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
        var date_start = $("#transaction_from").val() + ' 00:00:00';
        var date_end = $("#transaction_to").val() + ' 23:59:59';
        {{-- console.log(account_type);
        console.log(date_start);
        console.log(date_end); --}}
        $.ajax({
            method: "POST",
            url: "/pgmall/migrate",
            data: {
                "list_type":account_type,
                "date_start":date_start,
                "date_end":date_end,
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

    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
@stop