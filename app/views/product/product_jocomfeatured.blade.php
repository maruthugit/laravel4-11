@extends('layouts.master')

@section('title') Campaign @stop

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
            <h1 class="page-header">Jocom Featured Products
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

            <!-- <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>Panel Status</h3>
                </div>
                <div class="panel-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Panel Status</label>
                                   <div id="categoryAutoComplete" class="list-group autocomplete"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                   
                                    <div id="categoryAutoComplete" class="list-group autocomplete"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 5]) }}
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>

             </div> -->
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
                                    <th>Seq<br><button class="save-seq"><span>Save Seq</span><div class="loader" style="display: none"></div></button></th>
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
          "processing": true,
          "serverSide": true,
          'ajax': {
            "type"   : "POST",
            "url"    : '/jocomfeatured/product',
            "data"   : function( d ) {
              d.campaign_id= 1;
            },
          },
          "columnDefs": [
            { "orderable": true, "targets": [2,3,4] }
          ],
          'columns': [
            {"data" : "0"},
            {"data" : "1"},
            {"data" : "2"},
            {"data" : "3"},
            {"data" : "4"},
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
                    return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to Boost">Add to Jocom 11.11 <i class="fa fa-angle-double-right"></i> </button>';
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
            url: "/jocomfeatured/move",
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
            url: "/jocomfeatured/addproduct",
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
            url: "/jocomfeatured/removeproduct",
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
                    url: '/jocomfeatured/campaignupdate',
                    data: {
                        "product_campaign_id" : product_campaign_id,
                        "order_position" : ParentDOM.find('input').val()
                    },
                    dataType: "json",
                    success: function(resultData) { 
                        ParentDOM.find('.segment').remove();
                        $('#dataTables-products2').DataTable().ajax.reload();

                        if(resultData.error == 1){
                            alert(resultData.message);
                        }
                    }
                });
            }else{
                alert('Please enter valid Key');
            }
    }); 
    
    $('.save-seq').on('click', function() {

        if ($('.order-position-txt').length == 0) {
            return;
        }

        var campaignProductId = [];
        var position = [];

        $('.order-position-txt').each(function() {
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
            url: '/jocomfeatured/saveseq',
            data: {
                "campaign_product_ids" : campaignProductId,
                "order_positions" : position
            },
            dataType: "json",
            success: function(resultData) { 
                $('.save-seq').prop("disabled", false);
                $('.save-seq').find('span').text('Save Seq');
                $('.loader').hide();
                if(resultData.error == 1){
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
