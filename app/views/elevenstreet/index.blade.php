@extends('layouts.master')
@section('title') 11Street @stop
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
           <h1 class="page-header">PrestoMall Confirmed Orders</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group " >
                                <input type="text" class="form-control" placeholder="Order Number" id="migrate-order-number">
                                <span class="input-group-btn">
                                  <button class="btn btn-primary" id="migrate-single" type="button">Migrate</button>
                                </span>
                            </div><!-- /input-group -->
                        </div>  
                        <div class="col-md-2">
                            <button type="button" id="migrate-orders" class="btn btn-primary pull-right" style="width:100%;"><i class="fa fa-arrow-circle-down"></i> Migrate Latest Order</button>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="btn-group btn-group pull-right" role="group" aria-label="Small button group"> 
                            <input type="hidden" id="acc-type-hold" value="1" >
                            <button type="button" class="btn btn-default active btn-acc" acc-type="1" data-toggle="popover" data-trigger="hover" data-placement="top" title="11Street Account" data-content="(elevenstreet@jocom.my)">PrestoMall Acc 1</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="2" data-toggle="popover" data-trigger="hover" data-placement="top" title="11Street Account" data-content="(elevenstreet2@jocom.my)">PrestoMall Acc 2</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="3" data-toggle="popover" data-trigger="hover" data-placement="top" title="11Street Account" data-content="(elevenstreetfn@jocom.my)">PrestoMall F&N</button>
                            <button type="button" class="btn btn-default btn-acc" acc-type="4" data-toggle="popover" data-trigger="hover" data-placement="top" title="Coca Cola" data-content="(elevenstreetfn@jocom.my)">Coca Cola</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="5" data-toggle="popover" data-trigger="hover" data-placement="top" title="Spritzer" data-content="(spritzer@jocom.my)">Spritzer</button>
                            <button type="button" class="btn btn-default btn-acc" acc-type="6" data-toggle="popover" data-trigger="hover" data-placement="top" title="Cactus" data-content="(cactus@jocom.my)">Cactus</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="7" data-toggle="popover" data-trigger="hover" data-placement="top" title="F&N Creamer" data-content="(fnncreameries@jocom.my)">F&N Creamer</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="8" data-toggle="popover" data-trigger="hover" data-placement="top" title="Starbuck" data-content="(starbuck@jocom.my)">Starbuck</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="9" data-toggle="popover" data-trigger="hover" data-placement="top" title="POKKA" data-content="(pokka@jocom.my)">POKKA</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="10" data-toggle="popover" data-trigger="hover" data-placement="top" title="YEOS" data-content="(yeos@jocom.my)">Yeos</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="11" data-toggle="popover" data-trigger="hover" data-placement="top" title="Oriental" data-content="(oriental@jocom.my)">Oriental</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="12" data-toggle="popover" data-trigger="hover" data-placement="top" title="Kawan Food" data-content="(kawanfood@jocom.my)">Kawanfood</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="13" data-toggle="popover" data-trigger="hover" data-placement="top" title="Nikudo" data-content="(nikudo@jocom.my)">Nikudo</button> 
                            <button type="button" class="btn btn-default btn-acc" acc-type="14" data-toggle="popover" data-trigger="hover" data-placement="top" title="Etika" data-content="(etika@jocom.my)">Etika</button> 
        
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
                            <th width="10%">Recepient</th>
                            <!--<th width="25%">Email</th>-->
                            <th style="text-align: center;">Status</th>
                            <th width="10%">Transaction ID</th>
                            <th ></th>
                        </tr>
                    </thead>
         
                </table>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal-upload-csv" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Import order by CSV</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="modal-attention" role="alert">
                    <div style="margin-top:10px;"><strong><i class="fa fa-exclamation-triangle"></i> Instruction :</strong></div>  
                    <p>
                    <ul>
                        <li>Please use only excel sheet downloaded from 11Street Purchase Confirmed list only</li>
                        <li>Remove title row as in the red box and set column header as 1st row as save as .csv file </li>
                        <li>Browse the file and press upload file</li>
                    </ul>
                    <div>
                        {{ HTML::image('images/elevenGuide1.png', 'guide1', array('class' => 'img-rounded','style'=>'height:500px;width:100%;margin-top:10px;')) }}
                    </div>
                    
                </div>
                <div class="alert alert-success" id="upload-success-container" role="alert" style="display:none;">
                    <span id="upload-success-msg-totalCSV"></span></br>
                    <span id="upload-success-msg-totalExisting"></span></br>
                    <span id="upload-success-msg-totalRecord"></span></br>
                </div>
                <div>
                    <form class="form-horizontal" id="form-upload-csv" action="/eleven/orders/import" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <div class="input-group" id="browse-input">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" id="browse-launcher" type="button"><i class="fa fa-folder-open-o"></i> Browse</button>
                                </span>
                                <input type="text" id="selected-file" class="form-control" readonly="">
                                <input type="file"  name = "fileCSV" id='fileCSV' style="display:none;">
                            </div> 
                              
                            <div id='loading-upload' style="display: none;">
                                <div class="progress" style="">
                                    <div class="progress-bar progress-bar-success  progress-bar-striped active small" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%; ">
                                        <span style="font-size:12px;">uploading .. </span>
                                    </div>
                                </div>
                            </div>
                          </div>
                        </div>
                      </form>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="upload-close" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" id="upload-submit" class="btn btn-primary">Upload File</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
        
        $("#migrate-csv").click(function(){
            document.getElementById("form-upload-csv").reset();
            $("#modal-attention").show();
            $("#upload-success-container").hide();
        });
        
        $("#browse-launcher").click(function(){
            $("#fileCSV").click();
         });
         
        $("#migrate-single").click(function(){
            var order_number = $("#migrate-order-number").val();
            var account_type = $("#acc-type-hold").val();
            
            $.ajax({
                method: "POST",
                url: "/eleven/migratesingle",
                data: {
                    'order_number':order_number,
                    'account_type':account_type
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                    $('.loading').hide();
                    if(data.RespStatus == 1){
                        alert(data.message);
                    }
                }
          })
          
        });
         
        $("#fileCSV").change(function(){
            $("#selected-file").val($('#fileCSV')[0].files[0].name);
        });
        
        $("#upload-close").click(function(){
            location.reload();
        })
        
        $("#upload-submit").click(function(){
            
            $("#browse-input").hide();
            $("#loading-upload").show();
            $.ajax({
                    url:'/eleven/orders/import',
                    data:new FormData($("#form-upload-csv")[0]),
                    dataType:'json',
                    async:false,
                    type:'post',
                    processData: false,
                    contentType: false,
                    success:function(response){
                      setTimeout(function(){ 
                          $("#loading-upload").hide(); 
                          $("#browse-input").show();
                          if(response.response == '1'){
                          $("#upload-success-msg-totalRecord").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalRecord+ ' order(s) imported');
                          $("#upload-success-msg-totalCSV").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalCSVRecord+ ' record(s) detected from CSV file');
                          $("#upload-success-msg-totalExisting").html('<i class="fa fa-check-square-o"></i> ' + response.data.totalExisting+ ' record(s) already exist');
                          $("#modal-attention").hide();
                          $("#upload-success-container").show();
                          //alert(response.data.totalRecords+ ' record(s) updated successfully');
                      }
                      }
                      , 2000);
                      
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
        "ajax": "{{ URL::to('eleven/orders') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'order_number', name: 'order_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data : "recepient"},
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
               var TransferUrl = '/transaction/add/1/' +row.id ;
               var createCustomerURL = '/customer/create/1/'+row.id;
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
            url: "/eleven/revert",
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

/*  function migrate(){
        $.ajax({
            method: "GET",
            url: "/eleven/migrate/2",
            data: {},
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
                        //location.reload();
                    }
                    
                }
            }
          })
    } */

    function migrate(){
    
    
        var account_type = $("#acc-type-hold").val();
    
        $.ajax({
            method: "POST",
            url: "/eleven/migrate",
            data: {
                "list_type":2,
                "account_type":account_type
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
                        //location.reload();
    }

                }
            }
          })
    }


@stop