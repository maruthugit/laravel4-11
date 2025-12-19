@extends('layouts.master')

@section('title') DO Sorter @stop
@section('content')
<style>
    
    .suggestion-opt-box{
        position: absolute;
    background-color: #fff;
    z-index: 100;
    margin-right: 15px !important;
    margin-left: 15px !important;
    font-size: 13px;
    margin-top: 35px;
    }
    
    .sel-product-base{
        margin-bottom: 5px;
    }
    
    .attach-base{
/*        padding-left: 15px;
        padding-right: 15px;*/
    }
    
    .suggestion-opt{
        border: solid 1px #ddd;padding: 10px;top:-1px;border-top: 0px;
    }
    
    .suggestion-opt:hover{
        background-color: #f3f3f3;
        cursor: pointer;
    }
    
    
    #manual-process{
        display: none;
    }
    
</style>
<div id="page-wrapper" >
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Delivery Order Sorter
            <span class="pull-right">
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Delivery Order Sorter</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        <form class="form-horizontal">
                            <div class="form-group">
                                {{ Form::label('', 'Region', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select class="form-control" id="region" name="region">
                                        <?php foreach ($regionList as $key => $value) { ?>
                                        <option value="<?php echo $value->id; ?>"><?php echo $value->region; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('', 'From ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" id="from_id" name="from_id" placeholder="" aria-describedby="basic-addon2" style="text-transform: uppercase;" value="">
                                </div>
                                {{ Form::label('', 'To ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input type="text" class="form-control" id="to_id" name="to_id" placeholder="" aria-describedby="basic-addon2" style="text-transform: uppercase;" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('', 'Selected ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-8">
                                    <textarea class="form-control" id="list_transaction" name="list_transaction" rows="5"></textarea>
                                    <h6>Please enter transaction id with correct format. EX : 29108,19273,22334</h6>
                                </div>
                            </div>
                            <div class="form-group" style="display: none;">
                                {{ Form::label('', 'Create Separator', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3 " style="padding: 7px;">
                                    <input id="create_separator" type="checkbox" value="0" style="margin-top:5px;"> Yes
                                </div>
<!--                                {{ Form::label('', 'Include Failed DO', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3" style="padding: 7px;">
                                    <input id="is_include_failed" type="checkbox" value="0"> Yes
                                </div>-->
                            </div>
<!--                            <div class="form-group">
                                {{ Form::label('', 'Failed DO Only', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3 " style="padding: 7px;">
                                    <input id="failed_do_only" name="failed_do_only" type="checkbox" value="0" style="margin-top:5px;"> Yes
                                </div>
                            </div>-->

                            <hr/>
                            <div class="form-group">
                            {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-10">                                
                                    <button class="btn btn-primary btn" type="button" id="gen-btn" >Generate </button>
                                </div>
                            </div>
                       </form>
                    </div>                     
                </div>
                <!-- /.panel-body -->
            </div>
        
        <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i></h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs"> 
                         <button class="btn btn-default pull-right print-combine"><i class="fa fa-print" aria-hidden="true"></i> Print Selected Batch</button>
                        <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                            <li role="presentation" class="active">
                                <a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Batch</a>
                            </li> 
                            <li role="presentation" class="">
                                <a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile" aria-expanded="false">Failed <span class="badge alert-danger"><?php echo $totalFailed; ?></span></a>
                            </li> 
                             <li role="presentation" class="">
                                <a href="#combine" role="tab" id="combine-tab" data-toggle="tab" aria-controls="combine" aria-expanded="false">Combine Batch</a>
                            </li> 
                        </ul> 
                        <div class="tab-content" id="myTabContent" > 
                            <div class="tab-pane fade active in" role="tabpanel" id="home" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="batch-list"> 
                                   <thead> 
                                       <tr> 
                                            <th>#</th>
                                           <th>Batch ID</th> 
                                           <th>File</th> 
                                           <th>Total Transactions</th>
                                           <th>Total Success</th>
                                           <th>Total Duplicate</th>
                                           <th>Total Failed</th>
                                           <th>Total Regenerated</th>
                                           <th>Total Failed Remaining</th>
                                           <th>Generate At</th> 
                                           <th>Generate By</th> 
                                           <th>Action</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody>  
                                   </tbody> 
                               </table>
                            
                            </div> 
                            <div class="tab-pane fade in" role="tabpanel" id="profile" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
<!--                                <div class="col-md-12" style="padding: 0px;margin-bottom: 10px;border-bottom: solid 1px #ddd;padding-bottom: 10px;">
                                    <button class="btn btn-primary">Generate Failed DO</button>
                                </div>
                                <hr>-->
                                <table class="table table-striped"  id="failed-list"> 
                                   <thead> 
                                       <tr> 
                                           <th>Transaction ID</th> 
                                           <th>Batch No</th> 
                                           <th>Created At</th>
                                           <th>Created By</th>
                                           <th>Remarks</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody>  
                                   </tbody> 
                               </table>
                            
                            </div> 
                             <div class="tab-pane fade in" role="tabpanel" id="combine" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="combine-list"> 
                                   <thead> 
                                       <tr> 
                                            <th>ID</th> 
                                            <th>File</th> 
                                            <th>Batch No</th> 
                                            <th>Generate At</th> 
                                            <th>Generate By</th> 
                                            <!--<th>Action</th>--> 
                                       </tr> 
                                   </thead> 
                                   <tbody>  
                                   </tbody> 
                               </table>
                            
                            </div> 
                        </div> 
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
           
        </div>
        <!-- /.col-lg-12 -->
    </div>
    
    <!-- Modal -->
    <div id="printModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Print All success and approved DO</h4>
          </div>
          <div class="modal-body">
              <p>Do you want to create separator too ?</p> <input type="checkbox" id="prt-sprt" value="1"> YES 
          </div>
          <div class="modal-footer">
            <button type="button" id="printNow" data-sort-id="" class="btn btn-default" >Generate Now!</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
    
    <!-- Large modal -->
    <div class="modal fade bs-example-modal-lg" id="list-object" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="min-width:70%;" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">List of Transaction</h4>
        </div>
        <div class="modal-body" style="height: calc(100vh - 210px);overflow-y: auto;">
        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs"> 
                        <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                            <li role="presentation" class="active">
                                <a href="#home2" id="home-tab" role="tab" data-toggle="tab" aria-controls="home2" aria-expanded="true">Generated</a>
                            </li> 
                            <li role="presentation" class="">
                                <a href="#Duplicate" role="tab" id="Duplicate-tab" data-toggle="tab" aria-controls="Duplicate" aria-expanded="false">Duplicate</a>
                            </li> 
                            <li role="presentation" class="">
                                <a href="#profile2" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile2" aria-expanded="false">Failed</a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#specialapprove" role="tab" id="profile-tab" data-toggle="tab" aria-controls="specialapprove" aria-expanded="false">Approved</a>
                            </li>
                            <li role="presentation" class="">
                                <a href="#purchaseSheet" role="tab" id="purchaseSheet-tab" data-toggle="tab" aria-controls="purchaseSheet" aria-expanded="false">Purchase List</a>
                            </li>
                        </ul> 
                        <div class="tab-content" id="myTabContent" > 
                            <div class="tab-pane fade active in" role="tabpanel" id="home2" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="batch-list2"> 
                                   <thead> 
                                       <tr> 
                                           <th>Transaction ID</th> 
                                           <th>DO No</th> 
                                           <th>Created At</th>
                                           <th>Created By</th>
                                           <th>Remarks</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody id="success-lists">  
                                   </tbody> 
                               </table>
                            </div> 
                            <div class="tab-pane fade in" role="tabpanel" id="Duplicate" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="duplicate-list2"> 
                                   <thead> 
                                       <tr> 
                                           <th>Transaction ID</th> 
                                           <th>DO No</th> 
                                           <th>Created At</th>
                                           <th>Created By</th>
                                           <th>Remarks</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody id="duplicate-list">  
                                   </tbody> 
                               </table>
                            </div>
                            <div class="tab-pane fade in" role="tabpanel" id="profile2" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="failed-list2"> 
                                   <thead> 
                                       <tr> 
                                           <th>#</th> 
                                           <th>Transaction ID</th> 
                                           <th>DO No</th> 
                                           <th>Created At</th>
                                           <th>Created By</th>
                                           <th>Product Items</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody id="failed-lists">  
                                   </tbody> 
                               </table>
                            </div> 
                            <div class="tab-pane fade in" role="tabpanel" id="specialapprove" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <table class="table table-striped"  id="special-list2"> 
                                   <thead> 
                                       <tr> 
                                           <th>Transaction ID</th> 
                                           <th>DO No</th> 
                                           <th>Created At</th>
                                           <th>Created By</th>
                                           <th>Product Items</th> 
                                       </tr> 
                                   </thead> 
                                   <tbody id="special-lists"></tbody> 
                                </table>
                            </div> 
                            <div class="tab-pane fade in" role="tabpanel" id="purchaseSheet" aria-labelledby="home-tab" style="margin-top: 20px;padding: 15px;"> 
                                <div style="margin-bottom:10px;float: right;"><a id="export-purchaselist" class="btn btn-primary" href="">Export List</a></div>
                                <table class="table table-condensed"  id="failed-list2s"> 
                                    <thead style="background-color: #75d085;"> 
                                        <tr> 
                                            <th></th> 
                                            <th width="20%">Product</th> 
                                            <th style="min-width:150px;">SKU</th>
                                            <th width="20%">Product Option</th> 
                                            <th width="20%">Vendor</th> 
                                            <th  style="text-align:center;">Total Transactions</th>
                                            <th  style="text-align:center;">Total Set</th>
                                            <th  style="text-align:center;">Total Required</th>
                                            <th  style="text-align:right;">Highest Unit Price (RM)</th>
                                        </tr> 
                                    </thead>  
                                    <tbody id="purchase-list">  
                                    </tbody> 
                               </table>
                            </div> 
                        </div> 
                    </div>
      </div>
      <div class="modal-footer">
            <button type="button" id="submit-remove-product" class="btn btn-primary" sort-id-remove="" data-dismiss="">Remove Product</button>
            <button type="button" id="submit-app-do" class="btn btn-primary" sort-id-approve="" data-dismiss="">Approve DO</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade bs-example-modal-lg" id="loader" tabindex="-1" role="dialog" style="margin-top:15%;">
    <div class="modal-dialog" style="" role="document">
        <div class="modal-content">
            <div class="modal-body" style="overflow-y: auto;">
                 <h5>Please Wait .. While system generate your DO's <i class="fa fa-coffee" aria-hidden="true"></i></h5>
                 <div class="progress" >
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                      <span class="sr-only">45% Complete</span>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    
    </div>


@stop
@section('inputjs')


<script>

    
$(function(){
    
    $('[data-toggle="tooltip"]').tooltip(); 
    
    function getBatchList(){
        
        $('#batch-list').dataTable({
        "autoWidth" : false,
        "processing": true,
        "ordering": false,
        "serverSide": true,
        "ajax": "{{ URL::to('transaction/sortlist') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
             { data: function ( row, type, val, meta ) {
                    var checkBox = '';
                    
                    checkBox = '<input class="checkBoxSelecter" type="checkbox" value="'+row.id+'">';
                    console.log(checkBox);
                    return checkBox;
                }
            },
            { data: 'batch_no', name: 'batch_no' },
//            { data: 'filename', name: 'filename' },
            { data: function ( row, type, val, meta ) {
                    
                    var buttonFiles = '';
                    $.each( row.files, function( key, value ) {
                        console.log(value);
                        if(value.filename != '' && value.filename != null){
                            buttonFiles = buttonFiles + '<button class="btn btn-default download-file" style="margin-bottom:5px;" data-id="'+value.id+'">'+value.filename+'</button><br>';
                        }
                    });
                    
                    return buttonFiles;
                    
                    
                },
                className: "center"
            },
            { data: 'total_transactions' , name: 'total_transactions'  },
            { data: 'total_success', name: 'total_success' },
            { data: 'total_duplicate', name: 'total_duplicate' },
            { data: 'total_failed', name: 'total_failed' },
            { data: 'total_regenerated', name: 'total_regenerated' },
            { data: 'total_remaining_failed', name: 'total_remaining_failed' },
            { data: 'created_at', name: 'created_at' },
            { data: 'created_by', name: 'created_by' },
            { data: function ( row, type, val, meta ) {
                    var disabled = "";
                    if(row.total_remaining_failed <= 0){
                        var disabled = "disabled";
                    }
                    var button = 
                                //'<button class="btn btn-default" style="margin-left:5px;"><i class="fa fa-print"></i></button>'+
                                '<button class="btn btn-default view-list" data-id="'+row.id+'" style="margin-left:5px;" data-toggle="tooltip" title="Summary"><i class="fa fa-navicon"></i></button>';
                                //+'<button class="btn btn-default generate-failed '+disabled+'" data-id="'+row.id+'" style="margin-left:5px;" title="Generated Failed DO"><i class="fa fa-retweet" aria-hidden="true"></i></button>'
                               // +'<button class="btn btn-default print-do"  data-id="'+row.id+'" style="margin-left:5px;" title="Print DO"><i class="fa fa-print" aria-hidden="true"></i> </button>';
                    return button;
                },
                className: "center"
            },
            
            ]
        }); 
    }
    
    function getCombineBatchList(){
        
        $('#combine-list').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('transaction/combinesort') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: function ( row, type, val, meta ) {
                    
                    var buttonFiles = '';
                    if(row.filename != '' && row.filename != null){
                        buttonFiles = buttonFiles + '<button class="btn btn-default download-file-combine" style="margin-bottom:5px;" data-id="'+row.id+'">'+row.filename+'</button><br>';
                    }
                    return buttonFiles;
                    
                },
                className: "center"
            },
            { data: 'batchNo', name: 'batchNo' },
            { data: 'created_at', name: 'created_at' },
            { data: 'created_by', name: 'created_by' },
//            { data: 'status', name: 'status' },
            ]
        }); 
    }
    
    getCombineBatchList();
    
    function getFailedBatchList(){
        
        $('#failed-list').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('transaction/failedsortlist') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'batch_no', name: 'batch_no' },
            { data: 'created_at', name: 'created_at' },
            { data: 'created_by', name: 'created_by' },
            { data: 'remarks', name: 'remarks' }
            
            ]
        }); 
    }
    
    
    $('body').on('click', '#printNow', function(){
        
        //alert('print_now');
        console.log($("#prt-sprt").val());
        var separator = 0
        var ischecked= $("#prt-sprt").is(':checked');
        if(ischecked){
            separator = 1;
        }else{
            separator = 0;
        }
        
        var sort_id = $(this).attr('data-sort-id');
        
        $.ajax({
            method: "POST",
            url: "/transaction/printsuccessdo",
            data: {
                "separator":separator,
                "sort_id":sort_id
            },
            beforeSend: function(){
            $('#loader').modal("show");
                console.log('Migrating..');
            },
            success: function(data) {
                $('#loader').modal("hide");
                console.log(data);
                if(data.response == 1){
                    alert("File "+data.filename +" has been generated");
                    location.reload();
                }else{
                     alert("Generate failed!");
                }
//               

            }
        })
        
    });
    
     var selectedCombine = [];
    
    $('body').on('click', '.checkBoxSelecter', function(){
    
        var ischecked= $(this).is(':checked');
        if(ischecked){
            // remove from listt
//                alert('checked ' + $(this).val());
            selectedCombine.push($(this).val());
        }else{
            // add to list
//                alert('unchecked ' + $(this).val());
            var index = selectedCombine.indexOf($(this).val());
            if (index > -1) {
                selectedCombine.splice(index, 1);
            }
        }    
       
       console.log(selectedCombine);
        
    });
    
    $('body').on('click', '.print-combine', function(){
        
        if(selectedCombine.length > 0){
            
            $.ajax({
                method: "POST",
                url: "/transaction/combinebatchprint",
                data: {
                    "sort_id":selectedCombine
                },
                beforeSend: function(){
                $('#loader').modal("show");
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                //   location.reload();
                }
            })
            
        }else{
            alert("No batch selected!");
        }
        
    });
    
    
    $('body').on('click', '.download-file', function(){
    
        var id = $(this).attr("data-id");
        if(id > 0 ){
            window.location = "/transaction/downloadsorter?id="+id
        }else{
            alert("Failed to download !")
         }
        
    });
    
    $('body').on('click', '.download-file-combine', function(){
    
        var id = $(this).attr("data-id");
        if(id > 0 ){
            window.location = "/transaction/downloadcombinesorter?id="+id
        }else{
            alert("Failed to download !")
         }
        
    });
    
    $('body').on('click', '#failed_do_only', function(){
        
        if($('#failed_do_only').is(':checked') ){
            $("#from_id").val('');
            $("#to_id").val('');
        }
    
    });
    
    $('body').on('click', '.print-do', function(){
        
        var sortid = $(this).attr("data-id");
        $("#printNow").attr("data-sort-id",sortid);
    
        $("#printModal").modal("show");
        
    
    });
    
    
    $('body').on('click', '.generate-failed', function(){
        
        var sortid = $(this).attr("data-id");
        
        console.log(sortid);
        
        $.ajax({
            method: "POST",
            url: "/transaction/generatefailed",
            data: {
                "sort_id":sortid
            },
            beforeSend: function(){
            $('#loader').modal("show");
                console.log('Migrating..');
            },
            success: function(data) {
                console.log(data);
               //location.reload();

            }
        })
        
        
        
    });
    
    $('body').on('click', '.view-list', function(){
        
        var sortid = $(this).attr("data-id");
        
         $("#submit-app-do").attr("sort-id-approve",sortid);
         $("#submit-remove-product").attr("sort-id-remove",sortid);
        
        $.ajax({
                method: "POST",
                url: "/transaction/sorttransactions",
                data: {
                    "sortid":sortid
                },
                beforeSend: function(){
               
                },
                success: function(data) {
                    
                    
                    
                    var successDOM = "";
                    var failedDOM = "";
                    var duplicateDOM = "";
                    var purchaseListing = "";
                    var approvedDOM = "";
//                     <th>Transaction ID</th> 
//                                           <th>Batch No</th> 
//                                           <th>Created At</th>
//                                           <th>Created By</th>
//                                           <th>Remarks</th> 
                    // Update List
                    if((data.successList).length > 0){
                        $.each( data.successList, function( key, value ) {
                            successDOM = successDOM + '<tr><td>'+value.transaction_id+'</td><td>'+value.do_no+'</td><td>'+value.created_at+'</td><td>'+value.created_by+'</td><td>'+value.remarks+'</td></tr>';
                        });
                    }else{
                        successDOM = '<tr><td colspan="5" style="text-align:center;">No Records</td></tr>';
                    }
                    
                    // Update List
                    if((data.failedList).length > 0){
                       
                        $.each( data.failedList, function( key, value ) {
                            var loopDetails = '';
                            $.each( value.productCollection, function( keyD, valueD ) {
                                loopDetails = loopDetails + '<div style="margin-bottom:10px;">';
                                loopDetails = loopDetails + valueD.name + " <br><strong>SKU :"+valueD.sku+"</strong><br>Quantity : "+valueD.order_quantity+"<p>";
                                if(valueD.is_failed == 2){
                                    loopDetails = loopDetails + '<span class="label label-danger">Not Enough Stock</span><br>';
                                }
                                loopDetails = loopDetails + '</div>'
                            });
                            failedDOM = failedDOM + '<tr><td><input type="checkbox" class="add-approve" value="'+value.transaction_id+'"></td><td>'+value.transaction_id+'</td><td>'+value.do_no+'</td><td>'+value.created_at+'</td><td>'+value.created_by+'</td><td>'+loopDetails+'</td></tr>';
                        });
                    }else{
                        failedDOM = '<tr><td colspan="6" style="text-align:center;">No Records</td></tr>';
                    }
                    
                    // Update List
                    if((data.approvedList).length > 0){
                       
                        $.each( data.approvedList, function( key, value ) {
                            var loopDetails = '';
                            $.each( value.productCollection, function( keyD, valueD ) {
                                loopDetails = loopDetails + valueD.name + " <br><strong>SKU :"+valueD.sku+"</strong><br>Quantity : "+valueD.order_quantity+"<p>";
                                if(valueD.is_failed == 2){
                                    loopDetails = loopDetails + '<span class="label label-danger">Not Enough Stock</span><br>';
                                }
                            });
                            approvedDOM = approvedDOM + '<tr><td>'+value.transaction_id+'</td><td>'+value.do_no+'</td><td>'+value.created_at+'</td><td>'+value.created_by+'</td><td>'+loopDetails+'</td></tr>';
                        });
                    }else{
                        approvedDOM = '<tr><td colspan="5" style="text-align:center;">No Records</td></tr>';
                    }
                    
                    // Update List
                    if((data.DuplicateList).length > 0){
                        $.each( data.DuplicateList, function( key, value ) {
                            duplicateDOM = duplicateDOM + '<tr><td>'+value.transaction_id+'</td><td>'+value.do_no+'</td><td>'+value.created_at+'</td><td>'+value.created_by+'</td><td>'+value.remarks+'</td></tr>';
                        });
                    }else{
                        duplicateDOM = '<tr><td colspan="5" style="text-align:center;">No Records</td></tr>';
                    }
                    
                    // Update List
                    if((data.purchaseList).length > 0){
                        $.each( data.purchaseList, function( key, value ) {
                            if((value.base_product).length > 0){
                                var totalBaseRequired = '';
                                var seperator = '';
                            }else{
                                var totalBaseRequired = value.req_qty;
                                var seperator = 'border-bottom: solid 2px #bbbbbb;';
                            }
                            var totalBase = (value.base_product).length ;
                            var loopCounter = 0;
                            purchaseListing = purchaseListing + '<tr style="background-color:#ddd;'+seperator+'"><td><input class="add-remove" type="checkbox" value="'+value.product_id+'"></td><td>'+value.product_name+'</td><td>'+value.product_sku+'</td><td>'+value.product_label+'</td><td>'+value.company_name+'</td><td  style="text-align:center;">'+value.total_order+'</td><td  style="text-align:center;">'+value.req_qty+'</td><td style="text-align:center;">'+totalBaseRequired+'</td><td style="text-align:center;"></td></tr>';//value.unit_price
                            if((value.base_product).length > 0){
                                $.each( value.base_product, function( keyBase, valueBase ) {
                                    loopCounter++;
                                    if(loopCounter == totalBase){
                                        var baseSeperator = 'border-bottom: solid 2px #bbbbbb;';
                                    }else{
                                        var baseSeperator = '';
                                    }
                                    purchaseListing = purchaseListing + '<tr style="background-color:#f7f7f7;'+baseSeperator+'"><td><input class="add-remove" type="checkbox" value="'+valueBase.product_id+'"></td></td><td>'+valueBase.product_name+'</td><td>'+valueBase.product_sku+'</td><td>'+valueBase.product_label+'</td><td>'+valueBase.company_name+'</td><td></td><td></td><td style="text-align:center;">'+valueBase.totalQuantity+'</td><td style="text-align:center;"></td></tr>'; //valueBase.unit_price
                                
                                    
                                });
                            }
                        
                        });
                    }else{
                        purchaseListing = '<tr><td colspan="6" style="text-align:center;">No Records</td></tr>';
                    }
                    
                    
                    //id="export-purchaselist"
                    $("#export-purchaselist").attr("href","/transaction/exportpurchaselist?sort_id="+sortid);
                    $("#success-lists").html(successDOM);
                    $("#failed-lists").html(failedDOM);
                    $("#duplicate-list").html(duplicateDOM);
                    $("#purchase-list").html(purchaseListing);
                    $("#special-lists").html(approvedDOM);
                    $("#list-object").modal('show');
                   
                }
            })
//    
       
        
    });
    
    //
    var selectedApprove = [];
    
    $('body').on('click', '.add-approve', function(){
    
        var ischecked= $(this).is(':checked');
        if(ischecked){
            // remove from listt
//                alert('checked ' + $(this).val());
            selectedApprove.push($(this).val());
        }else{
            // add to list
//                alert('unchecked ' + $(this).val());
            var index = selectedApprove.indexOf($(this).val());
            if (index > -1) {
                selectedApprove.splice(index, 1);
            }
        }    
       
       console.log(selectedApprove);
    
    });
    
    var selectedRemove = [];
    
    $('body').on('click', '.add-remove', function(){
    
        var ischecked= $(this).is(':checked');
        if(ischecked){
            // remove from listt
//                alert('checked ' + $(this).val());
            selectedRemove.push($(this).val());
        }else{
            // add to list
//                alert('unchecked ' + $(this).val());
            var index = selectedRemove.indexOf($(this).val());
            if (index > -1) {
                selectedRemove.splice(index, 1);
            }
        }    
       
       console.log(selectedRemove);
    
    });
    
    $('body').on('click', '#submit-remove-product', function(){
    console.log(selectedRemove);
        var sortid = $(this).attr("sort-id-remove");
        if(selectedRemove.length > 0){
            // submit 
            removeProduct(selectedRemove,sortid);
        }else{
            alert('No Product selected');
        }
    });
    
    function removeProduct(selectedRemove,sortid){
        
        $.ajax({
            method: "POST",
            url: "/transaction/removeitempurchase",
            data: {
                "sort_id":sortid,
                "product_id":selectedRemove,
            },
            beforeSend: function(){
            $('#loader').modal("show");
                console.log('Migrating..');
            },
            success: function(data) {
                console.log(data);
                $('#loader').modal("hide");
                if(data.response == 1){
                    alert(data.totalProductRemove + " Product has/have been removed");
                    location.reload();
                }else{
                    alert("Remove product failed!");
                }
//               
                
            }
        })
    }
    
    function approveDO(selectedApprove,sortid){
        
        $.ajax({
            method: "POST",
            url: "/transaction/passdo",
            data: {
                "sort_id":sortid,
                "transaction_id":selectedApprove,
            },
            beforeSend: function(){
            $('#loader').modal("show");
                console.log('Migrating..');
            },
            success: function(data) {
                console.log(data);
                $('#loader').modal("hide");
                if(data.response == 1){
                    alert(data.result.totalApproved + " DO has/have been approved \n"+data.result.totalFailed + " DO failed to approved!");
                    //location.reload();
                }else{
                    alert("Approve DO failed!");
                }
//               
                
            }
        })
    }
    
    $('body').on('click', '#submit-app-do', function(){
    console.log(selectedApprove);
        var sortid = $(this).attr("sort-id-approve");
        if(selectedApprove.length > 0){
            // submit 
            approveDO(selectedApprove,sortid);
        }else{
            alert('No Transaction selected');
        }
    });
    
    
    
    
    $('body').on('click', '#gen-btn', function(){
        
        var checkForm = false;
        var region  = $("#region").val();
        var from_id = $("#from_id").val();
        var to_id   = $("#to_id").val();
        var list_transaction   = $("#list_transaction").val();
        var create_separator  = $("#create_separator").val();
        var is_include_failed  = $("#is_include_failed").val();
        var failed_do_only  = $("#failed_do_only").val();
         
        if($('#create_separator').is(':checked') ){
            create_separator = 1;
        }
        
        if($('#is_include_failed').is(':checked') ){
            is_include_failed = 1;
        }
        
        if($('#failed_do_only').is(':checked') ){
            failed_do_only = 1;
        }
        
        checkForm = validateForm();
        
        if(checkForm){
            
            $.ajax({
                method: "POST",
                url: "/transaction/sorter",
                data: {
                    "region":region,
                    "from_id":from_id,
                    "to_id":to_id,
                    "list_transaction":list_transaction,
                    "create_separator":create_separator,
                    "is_include_failed":is_include_failed,
                    "failed_do_only":failed_do_only
                },
                beforeSend: function(){
                $('#loader').modal("show");
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                    $('#loader').modal("hide");
                    alert("New batch "+data.data.batch_no+" created."+"\nTotal Success: "+data.data.total_success+"\nTotal Failed: "+data.data.total_failed+"\nTotal Duplicate: "+data.data.total_duplicate);
                    location.reload();

                }
            })
            
        }

        
        
    });
    
    function validateForm(){
        return true;
    }
    
    getBatchList();
    getFailedBatchList();
    
   
    
    
});
</script>

@stop





