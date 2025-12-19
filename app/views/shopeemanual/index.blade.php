@extends('layouts.master')
@section('title') Shopee Manual Orders @stop
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
           <h1 class="page-header">Shopee Manual Orders</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
            <div class="col-md-2">
                <!-- <button type="button" id="csv-file-button" class="btn btn-default" style="width:100%;">Choose File</button>  -->
                <input id="csv-file" type="file" accept=".csv" />
            </div>
            <div class="col-md-6">
                <div class="btn-group btn-group pull-right" role="group" aria-label="Small button group"> 

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
            <div class="col-md-2">
                <button type="button" id="upload-orders" class="btn btn-primary pull-right" style="width:100%;" disabled="true"><i class="fa fa-arrow-circle-down"></i> Upload CSV File</button>
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
                            <th width="10%">ID</th>
                            <th width="15%">Order Number</th>            
                            <th width="30%">Customer</th>
                            <th width="15%">Transaction ID</th>
                            <th width="15%">Status</th>
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

        // document.getElementById('csv-file-button').addEventListener('click', openDialog);

        // function openDialog() {
        //     document.getElementById('csv-file').click();
        // }

        $(".btn-acc").click(function(){
            $(".btn-acc").removeClass('active');
            $(this).addClass('active');
            console.log($(this).attr('acc-type'));
            $("#acc-type-hold").val($(this).attr('acc-type'));
        });


        $('#csv-file').change(enableUploadButton);

        function enableUploadButton() {
            let file = $(this)[0].files[0];

            if (file) {
                // document.getElementById('csv-file-label').innerHTML = file.name;
                document.getElementById('upload-orders').disabled = false;
            }
        }

        $('#upload-orders').click(function() {
            $(".loading").show();

            var account_type = $("#acc-type-hold").val();

            var formData = new FormData()
            formData.append('csv_file', $('#csv-file')[0].files[0]);
            formData.append('account_type', account_type);

            $.ajax({
                    url:'/shopeemanual/upload',
                    data:formData,
                    type:'post',
                    processData: false,
                    contentType: false,
                    success:function(response) {
                        $(".loading").hide();
                        if (response.status == 200) {
                            $('#dataTables-driver').DataTable().ajax.reload();
                            alert('Order Upload Success');
                        } else if (response.status == 409) {
                            let duplicateOrders = response.duplicateOrder;
                            var orderList = '';
                            for (let index = 0; index < duplicateOrders.length; index++) {
                                orderList = orderList + '\n' + duplicateOrders[index].order_number;
                            }
                            alert('Duplicate order number ' + orderList);
                        } else if (response.status == 404) {
                            alert('No new order number found.')
                        } else {
                            alert('Error uploading order.');
                        }
                    },
                });
        });

    });
</script>
@stop
@section('script')

    $('#dataTables-driver').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('shopeemanual/orders') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'ordersn', name: 'ordersn' },
            { data: 'name', name: 'name' },
            { data: function ( row, type, val, meta ) {
                    if(row.transaction_id == 0){ return ' - '};
                    if(row.transaction_id != 0){ return '<span class="">'+row.transaction_id+'</span>'};
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                    if(row.status == 0){ return '<span class="label label-warning">Pending</span>'};
                    if(row.status == 1){ return '<span class="label label-success">Transfered</span>'};
                },
                className: "center"
            }
            ]
    });

@stop