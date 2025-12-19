@extends('layouts.master')
@section('title') Taobao Shop @stop
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
           <h1 class="page-header">Taobao Orders</h1>
             
        </div>

        <!-- <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
            <div class="col-md-10">
                <input id="csv-file" type="file" accept=".csv" />
            </div>
            
            <div class="col-md-2">
                <button type="button" id="uploadorders" class="btn btn-primary pull-right" style="width:100%;" disabled="true"><i class="fa fa-arrow-circle-down"></i> Upload CSV File</button>
            </div>
        </div> -->
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
                            <th width="10%">Transaction ID</th>
                            <th width="15%">Transaction Date</th>            
                            <th width="30%">State</th>
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

            var formData = new FormData()
            formData.append('csv_file', $('#csv-file')[0].files[0]);

            $.ajax({
                    url:'/astrogo/upload',
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
        "ajax": "{{ URL::to('transaction/taobaolisting') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'delivery_state', name: 'delivery_state' },
            { data: 'status', name: 'status' }
           

            ]
    });

@stop