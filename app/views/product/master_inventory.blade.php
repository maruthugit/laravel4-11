@extends('layouts.master')

@section('title') Master Product Inventory @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Master Product Inventory
<!--                 <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product">Favorite List</a>
                </span>-->
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th width="2%" >Product ID</th>
                                    <th width="5%" >SKU</th>
                                    <!--<th width="5%" >QRCode</th>-->
                                    <th width="10%" class="">Product Name</th>
                                    <th width="10%" class="">Product Label</th>
                                    <!--<th width="5%" class="text-center">Price</th>-->
                                    <!--<th width="5%" class="text-center">Promo Price</th>-->
                                    <th width="5%" class="text-center">Quantity</th>
                                    <th width="5%" class="text-center">Actual Stock</th>
                                    <th width="5%" class="text-center">Unit</th>
                                    <th width="1%" class="text-center">Add Qty</th>
                                    <th width="1%" class="text-center">Add Stock</th>
                                    <th width="5%" class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('inputjs')
<script>
    $( document ).ready(function() {
    
    var timer;
   
    
    $('body').on( "click", ".update-btn", function() {
        console.log('UPDATE');
        
        var htmlElement = $(this);
        var addActualStock = htmlElement.parent().parent().find('.actual_stock').val();
        var addQty = htmlElement.parent().parent().find('.actual_qty').val();
        var priceOptionID = htmlElement.attr("prc-option-id");
        var productID = htmlElement.attr("prd-id");
        
        console.log(priceOptionID);
        console.log(productID);
        
        // UPDATE //
        $.ajax({
                method: "POST",
                url: "/inventory/updatestock",
                data: {
                    'actualStock':addActualStock,
                    'qtyStock':addQty,
                    'priceOptionID':priceOptionID,
                    'productID':productID,
                },
                beforeSend: function(){
                $('.loading').show();
                    console.log('Migrating..');
                },
                success: function(data) {
                    console.log(data);
                    
                    var stockHistory = data.data.inventoryResult.stock_history;
                    var newActualStock = stockHistory.stock;
                    $("#actual_stock_"+stockHistory.priceopt).html(newActualStock);
                    $("#qty_stock_"+stockHistory.priceopt).html(data.data.inventoryResult.new_total_qty);
                    
                    $("#input_actual_"+stockHistory.priceopt).val('');
                    $("#input_qty_"+stockHistory.priceopt).val('');
                    
                    alert("Product Updated!")
                    
                    
                }
          })

    });
    
    
    
    

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

    $('#dataTables-products').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('product/products2?'.http_build_query(Input::all())) }}",
        "order" : [[ 0, 'desc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "className" : "text-center" },
            { "data" : "1" },
        
            { "data" : "3", "className": "" },
            { "data" : "4", "className" : "" },
//            { "data" : "5", "className" : "text-center" },
//            { "data" : "6", "className" : "text-center" },
//            { "data" : "7", "className" : "text-center" },
            { data: function ( row, type, val, meta ) {
                    var price_option_id = row[10];
                    return '<span id=qty_stock_'+price_option_id+'>'+row[7]+'</span>'
            },  
                className: "center"
            },
//            { "data" : "8", "className" : "text-center" },
            { data: function ( row, type, val, meta ) {
                    var price_option_id = row[10];
                    return '<span id=actual_stock_'+price_option_id+'>'+row[8]+'</span>'
            },  
                className: "center"
            },
            { "data" : "9", "className" : "text-center" },
            { data: function ( row, type, val, meta ) {
                    var price_option_id = row[10];
                    return '<input type="text" style="width:100px;" class="form-control  actual_qty" id="input_qty_'+price_option_id+'">'
            },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                    
                    var price_option_id = row[10];
                    return '<input type="text" style="width:100px;" class="form-control actual_stock" id="input_actual_'+price_option_id+'">'
            },
                className: "center"
            },  
            { data: function ( row, type, val, meta ) {
                    var product_id = row[0];
                    var price_option_id = row[10];
//                    console.log(row);

//<button style="width:100%;margin-top:5px;" class="btn btn-default" ><i class="fa fa-plus"></i> Add to Favorite</button>
                    return '<button class="btn btn-primary update-btn" style="width:100%" prd-id="'+product_id+'" prc-option-id="'+price_option_id+'" >Update</button>'
            },
                className: "center"
            },  
            
        ]
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
    
       });
</script>
@stop

   

