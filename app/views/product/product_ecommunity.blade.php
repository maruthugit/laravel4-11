@extends('layouts.master')

@section('title') eCommunity @stop

@section('content')
 <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->

 {{ HTML::style('css/jquery-ui.css') }}

<style>
    h3 {
        font-size: 24px;
    }
    .text-center{
        text-align: center;
    }
    
    #datatables_goto{
        display: none !important;
    }
    
    #datatables_goto_button{
        display: none !important;
    }
    .ui-widget {
    font-family: Arial,Helvetica,sans-serif;
    font-size: .9em;
    }
    .ui-accordion .ui-accordion-content {
        padding: 1em 1em;
        border-top: 0;
        overflow: auto;
    }

    /*.panel-body {
        padding: 5px;
    }*/

    .ui-accordion .ui-accordion-header {
        display: block;
        cursor: pointer;
        position: relative;
        margin: 2px 0 0 0;
        /* padding: .5em .5em .5em .7em; */
        font-size: 1.5em;
        color: #ffffff;
        background: #007fff;

    }

    .ui-accordion .ui-accordion-icons {
        padding-left: 2.2em;
         color: #ffffff;
    }
    .ui-accordion .ui-accordion-header {
        display: block;
        cursor: pointer;
        position: relative;
        margin: 2px 0 0 0;
        /*padding: .5em .5em .5em .7em;*/
        min-height: 0;
        font-size: 1.5em;
    }
   

    /* Spinner */
    .loader {
        border: 3px solid #f3f3f3; /* Light grey */
        border-top: 3px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    /* Spinner */
  
</style>



<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">eCommunity Store
                 <span class="pull-right">
                   
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div id="accordion">
            <h3>BEVERAGES</h3>
            <div class="panel panel-default ui-widget-content">
                <div lass="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[BEVERAGES]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="1"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                <!--                            <tbody  id="product-campaign-list">
                                <?php //foreach ($products as $key => $value) { ?>
                                <tr>
                                    <td><?php// echo $value->product_id; ?></td>
                                    <td><?php //echo $value->sku; ?></td>
                                    <td><?php //echo $value->name; ?></td>
                                    <td class="text-center"  style="width:70px;">
                                        <a class="btn btn-default btn-sm up-product" title="" data-toggle="tooltip" ><i class="fa fa-arrow-up"></i></a>
                                        <a class="btn btn-default btn-sm down-product" title="" data-toggle="tooltip" ><i class="fa fa-arrow-down"></i></a>
                                    </td>
                                    <td class="text-center" ><button class="btn btn-danger remove-product" data-id="<?php //echo $value->productCampaignID; ?>"><i class="fa fa-trash-o"></i> <span  style="font-weight: lighter;">Remove</span></button></td>
                                </tr>
                                <?php  //} ?>
                            </tbody>-->
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>

            <h3>CHILLED & FROZEN</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[CHILLED & FROZEN]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="2"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>FOOD CUPBOARD</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[FOOD CUPBOARD]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_3">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_3" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="3"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            

            <h3>FRESH MARKET</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[FRESH MARKET]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_4">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_4" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="4"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>HOUSEHOLD</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[HOUSEHOLD]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_5">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_5" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="5"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>JOCOM WELLNESS</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[JOCOM WELLNESS]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_6">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_6" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="6"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>ORGANIC MARKET</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[ORGANIC MARKET]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_7">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_7" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="7"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>PANTRY SUPPLIES</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[PANTRY SUPPLIES]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_8">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_8" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="8"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
            <h3>STATIONERY</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List <span class="text-primary">[STATIONERY]</span></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products_new_9">
                            <thead>
                                <tr>
                                    <th width="5%">SKU</th>
                                    <th width="20%">Product Name</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <th width="2%" class="text-center" >Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="overflow-x: none;">
                            <table class="table  table-striped table-bordered table-hover" id="dataTables-products2_new_9" style=" margin-top: 25px !important;">
                            <thead>
                                <tr>
                                    <th style="width:150px;">SKU</th>
                                    <th>Product</th>
                                    <th>Seq <button class="save-seq" campaignid="9"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    
                </div>
            </div>
          </div>
        </div>
    </div>
@stop
 
@section('inputjs')

    <script>
    $(document).ready(function() {
    
    var icons = {
      header: "ui-icon-circle-arrow-e",
      activeHeader: "ui-icon-circle-arrow-s"
    };
    $( "#accordion" ).accordion({
      heightStyle: "content",
      icons: icons,
      collapsible: true
    });
       
        var timer;
    //    $.fn.DataTable.ext.pager.numbers_length = 4;
        // $('#category').keydown(function () {
        //     clearTimeout(timer);
        //     timer = setTimeout(function () {
        //         if ($('#category').val()) {
        //             $.ajax({
        //                 url: '{{ url('api/categorysearch?keyword=') }}' + $('#category').val() + '&limit=5',
        //                 success: function (result) {
        //                     if ($('#category').is(":focus")) {
        //                         var candidates = $.parseJSON(result);
        //                         var size = 0;

        //                         $('#categoryAutoComplete').html('');

        //                         $.each(candidates, function (i, candidate) {
        //                             var categoryName = candidate.category_name;

        //                             if (categoryName.search($('#category').val())) {
        //                                 var clickAction = "$('#categoryAutoComplete').html(''); $('#category').val('" + candidate.category_name + "'); return false;";

        //                                 $('#categoryAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.category_name + '</a>');
        //                                 size++;
        //                             }
        //                         });

        //                         if ($('#category').is(':focus') && size > 0) {
        //                             $('#categoryAutoComplete').show();
        //                         }
        //                     }
        //                 }
        //             });
        //         } else {
        //             $('#categoryAutoComplete').html('');
        //         }
        //     }, 200);
        // });

        // $('html').click(function () {
        //     $('#categoryAutoComplete').html('').hide();
        // });

        var example_table = $('#dataTables-products2').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 1;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table_01 = $('#dataTables-products2_new').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 2;
            },          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table2 = $('#dataTables-products').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        var example_table3 = $('#dataTables-products_new').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAddAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.remove-product02', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // FOOD CUPBOARD
        var example_table_03 = $('#dataTables-products2_new_3').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 3;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_3 = $('#dataTables-products_new_3').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd03" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd03', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 3;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product03', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // FOOD CUPBOARD

        // FRESH MARKET
        var example_table_04 = $('#dataTables-products2_new_4').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 4;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_4 = $('#dataTables-products_new_4').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd04" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd04', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 4;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product04', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // FRESH MARKET

        // HOUSEHOLD
        var example_table_05 = $('#dataTables-products2_new_5').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 5;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_5 = $('#dataTables-products_new_5').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd05" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd05', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 5;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product05', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // HOUSEHOLD

         // JOCOM WELLNESS
        var example_table_06 = $('#dataTables-products2_new_6').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 6;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_6 = $('#dataTables-products_new_6').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd06" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd06', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 6;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product06', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // JOCOM WELLNESS

        // ORGANIC MARKET
        var example_table_07 = $('#dataTables-products2_new_7').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 7;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_7 = $('#dataTables-products_new_7').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd07" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd07', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 7;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product07', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // ORGANIC MARKET

        // PANTRY SUPPLIES
        var example_table_08 = $('#dataTables-products2_new_8').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 8;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_8 = $('#dataTables-products_new_8').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd08" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd08', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 8;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product08', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // PANTRY SUPPLIES

        // PANTRY SUPPLIES
        var example_table_09 = $('#dataTables-products2_new_9').DataTable({
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/ecommunity/product',
            "data"   : function( d ) {
              d.campaign_id= 9;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
          ]
        });

        var example_table3_9 = $('#dataTables-products_new_9').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('product/products?'.http_build_query(Input::all())) }}",
            "order" : [[ 0, 'desc' ]],
            "columnDefs" : [{
                "targets" : "_all",
                "defaultContent" : "",
                "orderable": false, "targets": [2,3],
            }],
            "columns" : [
    //            { "data" : "0", "className" : "text-center" },
                { "data" : "1" },
                { "data" : "3" },
                { "data" : "6", "className" : "text-center" },
                { data: function ( row, type, val, meta ) {
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd09" data-transaction-id="'+row[0]+'" type="button" title="Add to campaign">Add to campaign <i class="fa fa-angle-double-right"></i> </button>';
                    }
                }
            ]
        });

        $('body').on('click', '.triggerAdd09', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 9;
            addCampaignProductNew(product_id,campaign_id);
            
        });

        $('body').on('click', '.remove-product09', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        // PANTRY SUPPLIES


        $('body').on('click', '.triggerAdd', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 1;
            addCampaignProduct(product_id,campaign_id);
            

        });

        $('body').on('click', '.triggerAddAdd', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 2;
            addCampaignProductNew(product_id,campaign_id);
            
        });    

        $('body').on('click', '.remove-product', function(){ 

            var campaign_product_id = $(this).data('id');
            removeCampaignProduct(campaign_product_id);
            

        });

        $('body').on('click', '.move-up', function(){ 

            var campaign_product_id = $(this).data('id');
            var action = 1;
            moveProductPosition(campaign_product_id,action);
            

        });

        $('body').on('click', '.move-down', function(){ 

            var campaign_product_id = $(this).data('id');
            var action = 2;
            moveProductPosition(campaign_product_id,action);

        });
        
        $(".dataTables_wrapper > .row >  .col-sm-6 > .dataTables_paginate").parent().removeClass("col-sm-6").addClass("col-sm-8");
        $(".dataTables_wrapper > .row >  .col-sm-6 > .dataTables_info").parent().removeClass("col-sm-6").addClass("col-sm-4");
        
    });

    function moveProductPosition(product_campaign_id,action){
        
        var campaign_id = campaign_id;
        $.ajax({
            method: "POST",
            url: "/ecommunity/move",
            dataType:'json',
            data: {
                'product_campaign_id':product_campaign_id,
                'action':action
            },
            beforeSend: function(){
            },
            success: function(data) {
                if(data.RespStatus == 1){
                    if(data.error == 0){
                        if(data.campaign_id == 1){
                            $('#dataTables-products2').DataTable().ajax.reload();
                        } else if(data.campaign_id == 2){
                            $('#dataTables-products2_new').DataTable().ajax.reload();
                         } else if(data.campaign_id == 3){
                                $('#dataTables-products2_new_3').DataTable().ajax.reload();
                            } else if(data.campaign_id == 4){
                                    $('#dataTables-products2_new_4').DataTable().ajax.reload();
                                 } else if(data.campaign_id == 5){
                                        $('#dataTables-products2_new_5').DataTable().ajax.reload();
                                     } else if(data.campaign_id == 6){
                                            $('#dataTables-products2_new_6').DataTable().ajax.reload();
                                         } else if(data.campaign_id == 7){
                                                $('#dataTables-products2_new_7').DataTable().ajax.reload();
                                             } else if(data.campaign_id == 8){
                                                    $('#dataTables-products2_new_8').DataTable().ajax.reload();
                                                 } else if(data.campaign_id == 9){
                                                    $('#dataTables-products2_new_9').DataTable().ajax.reload();
                                                  }
                        
                        
                    }else{
                        if(data.errorCode == 10){
                            alert(data.message);
                            
                        }else{
                            alert('Failed to add!');
                        }
                    }
                }
            }
        })
    }
    
    function addCampaignProduct(product_id,campaign_id){
        
        var campaign_id = campaign_id;
        $.ajax({
            method: "POST",
            url: "/ecommunity/addproduct",
            dataType:'json',
            data: {
                'campaign_id':campaign_id,
                'product_id':product_id
            },
            beforeSend: function(){
                
            },
            success: function(data) {
                if(data.RespStatus == 1){
                    if(data.error == 0){
                        alert('Added successfully!');
                        $('#dataTables-products2').DataTable().ajax.reload();
                    }else{
                        if(data.errorCode == 10){
                            alert(data.message);
                            
                        }else{
                            alert('Failed to add!');
                        }
                    }
                }
            }
        });
    }

    function addCampaignProductNew(product_id,campaign_id){
        
        var campaign_id = campaign_id;
        $.ajax({
            method: "POST",
            url: "/ecommunity/addproduct",
            dataType:'json',
            data: {
                'campaign_id':campaign_id,
                'product_id':product_id
            },
            beforeSend: function(){
                
            },
            success: function(data) {
                if(data.RespStatus == 1){
                    if(data.error == 0){
                        alert('Added successfully!');
                        if(data.campaign_id == 1){
                            $('#dataTables-products2').DataTable().ajax.reload();
                        } else if(data.campaign_id == 2){
                            $('#dataTables-products2_new').DataTable().ajax.reload();
                         } else if(data.campaign_id == 3){
                                $('#dataTables-products2_new_3').DataTable().ajax.reload();
                            } else if(data.campaign_id == 4){
                                    $('#dataTables-products2_new_4').DataTable().ajax.reload();
                                 } else if(data.campaign_id == 5){
                                        $('#dataTables-products2_new_5').DataTable().ajax.reload();
                                     } else if(data.campaign_id == 6){
                                            $('#dataTables-products2_new_6').DataTable().ajax.reload();
                                         } else if(data.campaign_id == 7){
                                                $('#dataTables-products2_new_7').DataTable().ajax.reload();
                                             } else if(data.campaign_id == 8){
                                                    $('#dataTables-products2_new_8').DataTable().ajax.reload();
                                                 } else if(data.campaign_id == 9){
                                                    $('#dataTables-products2_new_9').DataTable().ajax.reload();
                                                  }
                        
                        
                    }else{
                        if(data.errorCode == 10){
                            alert(data.message);
                            
                        }else{
                            alert('Failed to add!');
                        }
                    }
                }
            }
        });
    }
    
    function removeCampaignProduct(product_campaign_id){
        
        var product_campaign_id = product_campaign_id;
        $.ajax({
            method: "POST",
            url: "/ecommunity/removeproduct",
            dataType:'json',
            data: {
                'product_campaign_id':product_campaign_id
            },
            beforeSend: function(){
                
            },
            success: function(data) {
                if(data.RespStatus == 1){
                    if(data.error == 0){
                        alert('Remove successfully!');
                         if(data.campaign_id == 1){
                            $('#dataTables-products2').DataTable().ajax.reload();
                        } else if(data.campaign_id == 2){
                            $('#dataTables-products2_new').DataTable().ajax.reload();
                         } else if(data.campaign_id == 3){
                                $('#dataTables-products2_new_3').DataTable().ajax.reload();
                            } else if(data.campaign_id == 4){
                                    $('#dataTables-products2_new_4').DataTable().ajax.reload();
                                 } else if(data.campaign_id == 5){
                                        $('#dataTables-products2_new_5').DataTable().ajax.reload();
                                     } else if(data.campaign_id == 6){
                                            $('#dataTables-products2_new_6').DataTable().ajax.reload();
                                         } else if(data.campaign_id == 7){
                                                $('#dataTables-products2_new_7').DataTable().ajax.reload();
                                             } else if(data.campaign_id == 8){
                                                    $('#dataTables-products2_new_8').DataTable().ajax.reload();
                                                 } else if(data.campaign_id == 9){
                                                    $('#dataTables-products2_new_9').DataTable().ajax.reload();
                                                  }
//                        location.reload();
                    }else{
                        if(data.errorCode == 10){
                            alert(data.message);
                        }else{
                            alert('Failed to remove!');
                        }
                    }
                }
            }
        });
    }
    
    $(document).on("click", ".upt-position", function(e) {
            console.log($(this).parent().parent());
            var ParentDOM = $(this).parent().parent();
            console.log(ParentDOM.find('input').val());

            var product_campaign_id = ParentDOM.find('input').attr('data-id');
            console.log('Price Amount'+ParentDOM.find('input').val());
            console.log('Price Option'+product_campaign_id);
            if(ParentDOM.find('input').val() >= 0 ){
                ParentDOM.append('<div class="ui segment"><div class="ui active loader"></div></div>');
                // Update Price

                $.ajax({
                    type: 'POST',
                    url: '/ecommunity/campaignupdate',
                    data: {
                        "product_campaign_id" : product_campaign_id,
                        "order_position" : ParentDOM.find('input').val()
                    },
                    dataType: "json",
                    success: function(data) { 
                        ParentDOM.find('.segment').remove();
                        if(data.RespStatus == 1){
                        if(data.error == 0){
                            // alert('Remove successfully!');
                             if(data.campaign_id == 1){
                                $('#dataTables-products2').DataTable().ajax.reload();
                            } else if(data.campaign_id == 2){
                                $('#dataTables-products2_new').DataTable().ajax.reload();
                             } else if(data.campaign_id == 3){
                                    $('#dataTables-products2_new_3').DataTable().ajax.reload();
                                } else if(data.campaign_id == 4){
                                        $('#dataTables-products2_new_4').DataTable().ajax.reload();
                                     } else if(data.campaign_id == 5){
                                            $('#dataTables-products2_new_5').DataTable().ajax.reload();
                                         } else if(data.campaign_id == 6){
                                                $('#dataTables-products2_new_6').DataTable().ajax.reload();
                                             } else if(data.campaign_id == 7){
                                                    $('#dataTables-products2_new_7').DataTable().ajax.reload();
                                                 } else if(data.campaign_id == 8){
                                                        $('#dataTables-products2_new_8').DataTable().ajax.reload();
                                                     } else if(data.campaign_id == 9){
                                                        $('#dataTables-products2_new_9').DataTable().ajax.reload();
                                                      }
    //                        location.reload();
                        }else{
                            if(data.errorCode == 10){
                                alert(data.message);
                            }else{
                                alert('Failed to remove!');
                            }
                        }
                    }
                        
                        // $('#dataTables-products2').DataTable().ajax.reload();

                        // if(resultData.status == 1){
                        //     alert('Failed to save');
                        // }
                    }
                });
            }else{
                alert('Please enter valid Key');
            }
    });

    $('.save-seq').on('click', function() {

        var campaignId = $(this).attr('campaignid');
        var seqText = '.order-position-txt-' + campaignId;

        if ($(seqText).length == 0) {
            return;
        }
        
        var campaignProductId = [];
        var position = [];

        $(seqText).each(function() {
            campaignProductId.push($(this).attr('data-id'));
            position.push(this.value);

        });

        if (duplicateExists(position)) {
            alert('There is a duplicate seq. Please check');
            return;
        }

        $(this).prop("disabled", true);
        $('.save-seq').find('span').text('');
        $('.loader').show();

        $.ajax({
            type: 'POST',
            url: '/ecommunity/saveseq',
            data: {
                "campaign_id" : campaignId,
                "campaign_product_ids" : campaignProductId,
                "order_positions" : position
            },
            dataType: "json",
            success: function(resultData) { 
                $('.save-seq').prop("disabled", false);
                $('.save-seq').find('span').text('Save Seq');
                $('.loader').hide();
                if(resultData.error == 1){
                    console.log(resultData);
                    alert(resultData.message);
                } else {
                    alert('Seq updated successfully');
                    $('#dataTables-products2').DataTable().ajax.reload();
                }
                
            }
        });
    });

    function duplicateExists(w){
        return new Set(w).size !== w.length 
    }
    
    </script>
@stop
