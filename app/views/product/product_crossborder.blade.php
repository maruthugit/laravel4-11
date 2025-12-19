@extends('layouts.master')

@section('title') Cross Border @stop

@section('content')
<style>
    .text-center{
        text-align: center;
    }
    
    #datatables_goto{
        display: none !important;
    }
    
    #datatables_goto_button{
        display: none !important;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Cross Border
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
<!--            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Product Name</label>
                                    {{ Form::text('name', Input::get('name'), ['id' => 'name', 'class' => 'form-control', 'placeholder' => 'Product Name', 'tabindex' => 1]) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="seller">Seller</label>
                                    {{ Form::select('seller', array_merge(['any' => 'Any'], $sellers), Input::get('seller'), ['id' => 'seller', 'class' => 'form-control', 'tabindex' => 2]) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    {{ Form::text('category', Input::get('category'), ['autocomplete' => 'off', 'id' => 'category', 'class' => 'form-control', 'id' => 'category', 'placeholder' => 'Category name or ID', 'tabindex' => 3]) }}
                                    <div id="categoryAutoComplete" class="list-group autocomplete"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    {{ Form::select('status', ['any' => 'Any', 'active' => 'Active only', 'inactive' => 'Inactive only'], Input::get('status'), ['id' => 'status', 'class' => 'form-control', 'tabindex' => 4]) }}
                                </div>
                            </div>
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 5]) }}
                    </form>
                </div>
            </div>-->
<!--            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> MyCyberSales Product List</h3>
                </div>-->
                <?php 
//                
//                echo "<pre>";
//                print_r($products);
//                echo "</pre>";
                ?>
<!--                <div class="panel-body">
                    
                    
                </div>
            </div>-->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-6" style="    border-right: dashed 1px;border-color: #e0d9d9;">
                        <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <!--<th width="2%">ID</th>-->
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
                                    <!--<th>ID</th>-->
                                    <th style="width:80px;">Position</th>
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
        </div>
    </div>
@stop
 
@section('inputjs')

    <script>
    $(document).ready(function() {
    
       
        var timer;
    //    $.fn.DataTable.ext.pager.numbers_length = 4;
        $('#category').keydown(function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                if ($('#category').val()) {
                    $.ajax({
                        url: '{{ url('api/categorysearch?keyword=') }}' + $('#category').val() + '&limit=5',
                        success: function (result) {
                            if ($('#category').is(":focus")) {
                                var candidates = $.parseJSON(result);
                                var size = 0;

                                $('#categoryAutoComplete').html('');

                                $.each(candidates, function (i, candidate) {
                                    var categoryName = candidate.category_name;

                                    if (categoryName.search($('#category').val())) {
                                        var clickAction = "$('#categoryAutoComplete').html(''); $('#category').val('" + candidate.category_name + "'); return false;";

                                        $('#categoryAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.category_name + '</a>');
                                        size++;
                                    }
                                });

                                if ($('#category').is(':focus') && size > 0) {
                                    $('#categoryAutoComplete').show();
                                }
                            }
                        }
                    });
                } else {
                    $('#categoryAutoComplete').html('');
                }
            }, 200);
        });

        $('html').click(function () {
            $('#categoryAutoComplete').html('').hide();
        });

        var example_table = $('#dataTables-products2').DataTable({

          'ajax': {
            "type"   : "POST",
            "processing": true,
                "serverSide": true,
            "url"    : '/crossborder/product',
            "data"   : function( d ) {
              d.campaign_id= 1;
            },
            "dataSrc": ""
          },
          "columnDefs": [
            { "orderable": false, "targets": [2,3] }
          ],
          'columns': [

            {"data" : "sku"},
            {"data" : "name"},
        //    {"data" : "order_position"},
            { data: function ( row, type, val, meta ) 
                {
                        return ' <a class="btn btn-default btn-sm up-product move-up" data-id="'+row.productCampaignID+'" title="" data-toggle="tooltip" ><i class="fa fa-arrow-up"></i></a><a class="btn btn-default btn-sm down-product move-down" data-id="'+row.productCampaignID+'" title="" style="margin-left:5px;" data-toggle="tooltip" ><i class="fa fa-arrow-down"></i></a>';
                }
            },
            { data: function ( row, type, val, meta ) 
                {
                        return '<button class="btn btn-danger remove-product" data-id="'+row.productCampaignID+'"><i class="fa fa-trash-o"></i> <span  style="font-weight: lighter;">Remove</span></button>';
                }
            }
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


        $('body').on('click', '.triggerAdd', function(){ 

            var product_id = $(this).data('transaction-id');
            var campaign_id = 1;
            addCampaignProduct(product_id,campaign_id);
            

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
            url: "/crossborder/move",
            dataType:'json',
            data: {
                'product_campaign_id':product_campaign_id,
                'action':action
            },
            beforeSend: function(){
            },
            success: function(data) {
                $('#dataTables-products2').DataTable().ajax.reload();
            }
        })
    }
    
    function addCampaignProduct(product_id,campaign_id){
        
        var campaign_id = campaign_id;
        $.ajax({
            method: "POST",
            url: "/crossborder/addproduct",
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
        })
    }
    
    function removeCampaignProduct(product_campaign_id){
        
        var product_campaign_id = product_campaign_id;
        $.ajax({
            method: "POST",
            url: "/crossborder/removeproduct",
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
                         $('#dataTables-products2').DataTable().ajax.reload();
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
        })
    }
    
    </script>
@stop
