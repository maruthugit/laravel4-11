@extends('layouts.master')

@section('title') Products @stop

@section('content')

<style>
	
/* Ensure that the demo table scrolls */
    th, td { white-space: nowrap; }
    div.dataTables_wrapper {
  
        margin: 0 auto;
    }

    .dataTables_scrollBody{overflow-y: scroll;}


.row-mdf{
    /* position: absolute; */
    background-color: white;
}

table{
    margin-top: 0px !important;
}
  
  .td-1st{
      background-color: #fffff;
  }

  .zui-sticky-col{
    background-color: #fff !important;
  }

  .DTFC_RightBodyWrapper{
    z-index: 10;
    border-left: solid 1px #ddd;
    box-shadow: -10px 0px 6px -6px #f5f5f5;
  }

.DTFC_LeftBodyWrapper{
    z-index: 10;
    border-right: solid 1px #ddd;
    box-shadow: 10px 0px 6px -6px #f5f5f5;
  }

  .canedit{
    min-width:70px;
    background-color: #f7f7f7;
  }
	

</style> 
    


<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Products
                 <span class="pull-right">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a href="/product" type="button" class="btn btn-secondary btn-default ">Normal Version</a>
                        <a href="/product/productinline" type="button" class="btn btn-secondary btn-default active">Quick Edit</a>
                    </div>
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product"><i class="fa fa-refresh"></i></a>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
                    <a class="pull-right"><a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/create"><i class="fa fa-plus"></i></a>
                    @endif
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
            <div class="panel panel-default">
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
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="category">Category</label>-->
                            <!--        {{ Form::text('category', Input::get('category'), ['autocomplete' => 'off', 'id' => 'category', 'class' => 'form-control', 'id' => 'category', 'placeholder' => 'Category name or ID', 'tabindex' => 3]) }}-->
                            <!--        <div id="categoryAutoComplete" class="list-group autocomplete"></div>-->
                            <!--    </div>-->
                            <!--</div>-->
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
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table " id="dataTables-products" style="" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="col-sm-1 zui-sticky-col">ID</th>
                                    <th class="col-sm-1 zui-sticky-col">SKU</th>
                                    <th class="col-sm-1 text-center">Image</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2 text-center">Price Option ID</th>
                                    <th class="col-sm-2 text-center">Label</th>
                                    <th class="col-sm-2 text-center">UOM</th>
                                    <th class="col-sm-2 text-center">Quantity</th>
                                    <th class="col-sm-2 text-center">Stock</th>
                                    <th class="col-sm-2 text-center">Weight (Gram)</th>
                                    @if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'tammy', 'mint') ) )
                                    <th class="col-sm-2 text-center zui-sticky-col">Schedule</th>
                                    <th class="col-sm-2 text-center zui-sticky-col">Cost</th>
                                    <input type="hidden" id="cost-access" value="true">
                                    @else
                                    <input type="hidden" id="cost-access" value="false">
                                    @endif
                                    <th class="col-sm-2 text-center zui-sticky-col" style="width:200px;">Actual Price</th>
                                    <th class="col-sm-2 text-center zui-sticky-col">Promo Price</th>
                                    <th class="col-sm-2 text-center">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('datatable_script')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script> 
@stop

@section('script')
  
    var timer;

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

    $("#dataTableID_paginate").on("click", "a", function() { 
        
        $('.DTFC_LeftBodyLiner thead tr').css({visibility:'collapse'});
                $('.dataTables_scrollBody thead tr').css({visibility:'collapse'}); 

                $('.DTFC_LeftHeadWrapper thead th').css({'background-color':'white'}); 
    
    });

   
    var columns = [];
    var rightColumn = 5;
    var costAccess = $('#cost-access').val();
    if (costAccess == 'false') {
        rightColumn = 3;
            columns = [
            { data: 'id', name: 'id' },
            { data: 'sku', name: 'sku' },
            { data: 'img_1', name: 'img_1' },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<input type="text" value="'+row.name+'">';
                    return row.name;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<input type="text" value="'+row.PriceID+'">';
                    return row.PriceID;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text" data-up-type="LBL" data-id="'+row.PriceID+'">'+row.label+'</div>';
                    return row.label;
                },
            },
            {
               data: function ( row, type, val, meta ) {
                   if(row.stock_unit == ''){
                        return ' -- ';
                   }else{
                        return row.stock_unit;
                   }
                },
                className: "dt-center "
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text" data-up-type="QTY" data-id="'+row.PriceID+'">'+row.quantity+'</div>';
                    return content;
                },
                className: "canedit"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text"  data-up-type="STK" data-id="'+row.PriceID+'">'+row.stock+'</div>';
                    return content;
                },
                className: "canedit"
            },
            
            {
               data: function ( row, type, val, meta ) {

                    var content =  '<div class="editable" data-type="text"  data-up-type="WGT" data-id="'+row.PriceID+'">'+row.weight+'</div>';
                    return content;
                },
                className: "canedit"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div style="width:100px;" class="input-group"><input style="width:100px;" data-id="'+row.PriceID+'" type="text" class="form-control " value="'+row.price+'" ><span class="input-group-btn"><button type="button" class="btn btn-default upt-price"><i class="fa fa-save"></i></button></span></div>';
                    return content;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div style="width:100px;" class="input-group"><input style="width:100px;" data-id="'+row.PriceID+'" type="text" class="form-control " value="'+row.price_promo+'" ><span class="input-group-btn"><button type="button" class="btn btn-default upt-promo-price"><i class="fa fa-save"></i></button></span></div>';
                    return content;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    if(row.status == 1){
                        var content =  '<span class="label label-success">Active</span>';
                    }else{
                        var content =  '<span class="label label-warning">Inactive</span>';
                    }
                   
                    return content;
                },
                className: "zui-sticky-col"
            }
        ];
    } else {
        columns = [
            { data: 'id', name: 'id' },
            { data: 'sku', name: 'sku' },
            { data: 'img_1', name: 'img_1' },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<input type="text" value="'+row.name+'">';
                    return row.name;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<input type="text" value="'+row.PriceID+'">';
                    return row.PriceID;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text" data-up-type="LBL" data-id="'+row.PriceID+'">'+row.label+'</div>';
                    return row.label;
                },
            },
            {
               data: function ( row, type, val, meta ) {
                   if(row.stock_unit == ''){
                        return ' -- ';
                   }else{
                        return row.stock_unit;
                   }
                },
                className: "dt-center "
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text" data-up-type="QTY" data-id="'+row.PriceID+'">'+row.quantity+'</div>';
                    return content;
                },
                className: "canedit"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div class="editable" data-type="text"  data-up-type="STK" data-id="'+row.PriceID+'">'+row.stock+'</div>';
                    return content;
                },
                className: "canedit"
            },
            
            {
               data: function ( row, type, val, meta ) {

                    var content =  '<div class="editable" data-type="text"  data-up-type="WGT" data-id="'+row.PriceID+'">'+row.weight+'</div>';
                    return content;
                },
                className: "canedit"
            },
            { 
                data: function ( row, type, val, meta ) {
                    var content = '<div class="input-group-button"><button class="btn btn-primary upt-schedule" type="button" href="/product/scheduleajax?price_id='+row.PriceID+'">Schedule</button></div>';
                    return content;
                }, 
                className: "zui-sticky-col" 
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div style="width:100px;" class="input-group"><input style="width:100px;" data-id="'+row.PriceID+'" type="text" class="form-control " value="'+row.cost_price+'" ><span class="input-group-btn"><button type="button" class="btn btn-default upt-cost"><i class="fa fa-save"></i></button></span></div>';
                    return content;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div style="width:100px;" class="input-group"><input style="width:100px;" data-id="'+row.PriceID+'" type="text" class="form-control " value="'+row.price+'" ><span class="input-group-btn"><button type="button" class="btn btn-default upt-price"><i class="fa fa-save"></i></button></span></div>';
                    return content;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    var content =  '<div style="width:100px;" class="input-group"><input style="width:100px;" data-id="'+row.PriceID+'" type="text" class="form-control " value="'+row.price_promo+'" ><span class="input-group-btn"><button type="button" class="btn btn-default upt-promo-price"><i class="fa fa-save"></i></button></span></div>';
                    return content;
                },
                className: "zui-sticky-col"
            },
            {
               data: function ( row, type, val, meta ) {
                    if(row.status == 1){
                        var content =  '<span class="label label-success">Active</span>';
                    }else{
                        var content =  '<span class="label label-warning">Inactive</span>';
                    }
                   
                    return content;
                },
                className: "zui-sticky-col"
            }
        ];
    }
    
    var table =  $('#dataTables-products').DataTable({
      
        autoWidth : false,
        processing: true,
        serverSide: true,
        ajax: "{{ URL::to('product/products2?'.http_build_query(Input::all())) }}",
        initComplete: function(settings, json) {
             
            },
        drawCallback: function( settings ) {
            setTimeout(function(){ 
              
            }, 100);
        },
        
        fixedColumns: true,
        scrollX:        true,
        scrollCollapse: true,
        ordering: false,
        pageLength:10,
        paging:         true,
        createdRow: function( row, data, dataIndex ) {

           
        },
        fixedColumns:   {
            leftColumns: 2,
            rightColumns: rightColumn
        },
        columns : columns
    });

    

    	
    $('.DTFC_LeftBodyLiner thead tr').css({visibility:'collapse'});
    $('.dataTables_scrollBody thead tr').css({visibility:'collapse'});

    $(this).closest("table").prepend("<thead></thead>").children("thead").append($(this).remove());

    function format ( d ) {
    // `d` is the original data object for the row
    return '<table class="table table-striped table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<thead><th>Price Option</th> <th style="width:500px;"> Label</th><th>Actual Price</th><th>Promo Price</th><th></th> </thead>'+
        '<tbody><tr><td>83202</td><td>Party Pack @ RM 60:-  1) F&N Magnolia Justice League Batman  </td> <td><input type="text" class="form-control" value="100.99"></td><td><input type="text" class="form-control" value="100.99"></td><td><button class="btn btn-default"><i class="fa fa-save"></button></td></tr><tr><td>83202</td><td>325Ml </td><td><input type="text" class="form-control" value="100.99"></td><td><input type="text" class="form-control" value="100.99"></td><td><button class="btn btn-default"><i class="fa fa-save"></button></td></tr> </tbody>'+
        '</table>';
}


   

    $(document).on("dblclick", ".editable", function(e) {
        var ParentDOM = $(this).parent();
        var Content = $(this).html();
        var Id = $(this).attr('data-id');
        var UpdateType = $(this).attr('data-up-type');
        console.log(ParentDOM);
        ParentDOM.find('.editable').hide();
        ParentDOM.append('<input type="text" data-id="'+Id+'" data-up-type="'+UpdateType+'" class="edit-value form-control" value="'+Content+'" autofocus >');

    });


    $(document).on("keypress", ".edit-value", function(e) {

        var ParentDOM = $(this).parent();
        console.log(ParentDOM);
        console.log($(this).val());
        var value = $(this).val();
        var price_option_id = $(this).attr('data-id');
        var price_option_type = $(this).attr('data-up-type');
        if(e.which == 13) {
            ParentDOM.append('<div class="ui segment"><div class="ui active loader"></div></div>');
            $.ajax({
                type: 'POST',
                url: '/product/updatepriceoption',
                data: {
                    "price_option_id" : price_option_id,
                    "price_option_type" : price_option_type,
                    "value" : value
                },
                dataType: "json",
                success: function(resultData) { 
                    ParentDOM.find('.segment').remove();
                    ParentDOM.html('<div class="editable" data-type="text" data-id="'+price_option_id+'">'+value+'</div>')
                    if(resultData.status === 1){
                        alert('Failed to save');
                    }
                }
            });
          
        }
    });

    $(document).on("click", "#deleteItem", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete product id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            }
        });
    });

    $(document).on("click", ".upt-cost", function(e) {
            console.log($(this).parent().parent());
            var ParentDOM = $(this).parent().parent();
            console.log(ParentDOM.find('input').val());

            var price_option_id = ParentDOM.find('input').attr('data-id');
            console.log('Price Amount'+ParentDOM.find('input').val());
            console.log('Price Option'+price_option_id);
            if(ParentDOM.find('input').val() >= 0 ){
                ParentDOM.append('<div class="ui segment"><div class="ui active loader"></div></div>');
                // Update Cost

                var formData = new FormData();
                formData.append('price_option_id', price_option_id);
                formData.append('cost_price', ParentDOM.find('input').val());

                $.ajax({
                        type: 'POST',
                        url: '/product/updatesellercost',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(resultData) { 
                            ParentDOM.find('.segment').remove();
                            if(resultData.status == 1){
                                alert('Failed to save');
                            }
                        }
                });
            }else{
                alert('Please enter valid amount');
            }
    });

    $(document).on("click", ".upt-promo-price", function(e) {
            console.log($(this).parent().parent());
            var ParentDOM = $(this).parent().parent();
            console.log(ParentDOM.find('input').val());

            var price_option_id = ParentDOM.find('input').attr('data-id');
            console.log('Price Amount'+ParentDOM.find('input').val());
            console.log('Price Option'+price_option_id);
            if(ParentDOM.find('input').val() >= 0 ){
                ParentDOM.append('<div class="ui segment"><div class="ui active loader"></div></div>');
                // Update Price

                $.ajax({
                        type: 'POST',
                        url: '/product/updatepriceoption',
                        data: {
                            "price_option_id" : price_option_id,
                            "price_option_type" : 2,
                            "price_amount" : ParentDOM.find('input').val()
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            ParentDOM.find('.segment').remove();
                            if(resultData.status == 1){
                                alert('Failed to save');
                            }
                        }
                });
            }else{
                alert('Please enter valid amount');
            }
    }); 


    $(document).on("click", ".upt-price", function(e) {
            console.log($(this).parent().parent());
            var ParentDOM = $(this).parent().parent();
            console.log(ParentDOM.find('input').val());

            var price_option_id = ParentDOM.find('input').attr('data-id');
            console.log('Price Amount'+ParentDOM.find('input').val());
            console.log('Price Option'+price_option_id);
            if(ParentDOM.find('input').val() >= 0 ){
                ParentDOM.append('<div class="ui segment"><div class="ui active loader"></div></div>');
                // Update Price

                $.ajax({
                    type: 'POST',
                    url: '/product/updatepriceoption',
                    data: {
                        "price_option_id" : price_option_id,
                        "price_option_type" : 1,
                        "price_amount" : ParentDOM.find('input').val()
                    },
                    dataType: "json",
                    success: function(resultData) { 
                        ParentDOM.find('.segment').remove();
                        if(resultData.status == 1){
                            alert('Failed to save');
                        }
                    }
                });
            }else{
                alert('Please enter valid amount');
            }
    }); 
    
    $('body').on('click', ".upt-schedule", function(){
        $('.upt-schedule').colorbox({
            iframe:true, width:"90%", height:"90%",
            onClosed: function() {
                localStorage.clear();
            }
        });
    });

@stop
