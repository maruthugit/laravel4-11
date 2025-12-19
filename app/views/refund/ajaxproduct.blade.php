<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>AJAX Products</title>
    <!-- Bootstrap Core CSS -->
    {{-- HTML::style('//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css') --}}
    {{ HTML::style('css/bootstrap.min.css') }}

    <!-- Custom CSS -->
    {{ HTML::style('css/sb-admin-2.css') }}

    <!-- Custom Fonts -->
    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

    <!-- DataTables CSS -->
    {{ HTML::style('css/plugins/dataTables.bootstrap.css') }}

    <!-- Colorbox CSS -->
    {{ HTML::style('css/colorbox.css') }}

    <!-- jQuery -->
    {{ HTML::script('js/jquery.js') }}

    <!-- Bootstrap Core JavaScript -->
    {{ HTML::script('js/bootstrap.min.js') }}

    <!-- Metis Menu Plugin JavaScript -->
    {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}

    <!-- DataTables JavaScript -->
    {{ HTML::script('js/plugins/dataTables/jquery.dataTables.js') }}
    {{ HTML::script('js/plugins/dataTables/dataTables.bootstrap.js') }}

    <!-- Custom Theme JavaScript -->
    {{ HTML::script('js/sb-admin-2.js') }}

    <!-- bootbox code -->
    {{ HTML::script('js/bootbox.js') }}

    <!-- Colorbox JavaScript -->
    {{ HTML::script('js/jquery.colorbox.js') }}

</head>
<body>

    <div class="wrapper">
        <div id="page-wrapper" style="margin: 0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <h3 class="page-header">Transaction ID: {{ $id }} <br>Transaction Date: {{ $date }} <br>Transaction Amount: {{Config::get("constants.CURRENCY")}} {{ number_format($amount, 2) }}</h3>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">            
                @if (Session::has('message'))
                    <div class="alert alert-success">
                        <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                    </div>
                @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-list"></i> Transaction Items</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="col-lg-13 table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1">SKU</th>
                                            <th class="col-sm-1 text-center">Item ID</th>
                                            <th class="col-sm-1 text-center">ID</th>
                                            <th class="col-sm-3">Product Name</th>
                                            <th class="col-sm-3">Item Name</th>
                                            <th class="col-sm-2">Label</th>
                                            {{-- <th class="col-sm-1">Quantity</th> --}}
                                            <th class="col-sm-3 text-center">Price</th>
                                            {{-- <th class="col-sm-1">Total Discount</th>
                                            <th class="col-sm-1">GST (%)</th>
                                            <th class="col-sm-2">Sub-Total</th> --}}
                                            <th class="col-sm-3 text-center">Refund Quantity</th>
                                            <th class="col-sm-3 text-center">Refund Price</th>
                                            <th class="text-center col-sm-1">Action</th>                                            
                                        </tr>
                                    </thead>
                                    
                                </table>
                            </div>                            
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables-products').dataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : "{{ URL::to('refund/productsajax/'.$id) }}",
                "order" : [[ 0, 'desc' ]],
                "lengthMenu" : [ 10 ],
                "bLengthChange" : false,
                "columnsDef" : [{
                    "targets" : "_all",
                    "defaultContent" : ""
                }],
                "columns" : [
                    { "data" : "0", "className" : "text-left"},
                    { "data" : "1", "className" : "text-center" },
                    { "data" : "2", "className" : "text-center" },
                    { "data" : "3", "orderable" : false },
                    { "data" : "4", "className" : "text-left"},
                    { "data" : "5", "className" : "text-left"},
                    // { "data" : "6", "className" : "text-right", "orderable" : false},
                    { "data" : "6", "className" : "text-right", "orderable" : false},
                    // { "data" : "8", "className" : "text-right", "orderable" : false},   
                    // { "data" : "9", "className" : "text-right", "orderable" : false},   
                    // { "data" : "10", "className" : "text-right", "orderable" : false},    
                    { "data" : "7", "className" : "text-right", "orderable" : false},    
                    { "data" : "8", "className" : "text-right", "orderable" : false},    
                    { "data" : "9", "className" : "text-center", "orderable" : false},               
                ],
                "createdRow" : function (row, data, rowIndex) {
                    $.each($('td', row), function (colIndex) {
                        $(this).attr('data-passdata', data[colIndex]);
                        $(this).attr('id', data[0]);
                    });
                }
            });
            $(document).on("click", "#selectItem", function() {
                var $row = $(this).closest("tr"),
                    $tds = $row.find("td");

                var c = 0;
                var sku;

                var i       	        = 0;
                var exists  	        = 0;
                var rowCount 	        = parent.$('#ptb tr').length;
                var price      	        = $row.find('input[name="price"]').val();
                var qty      	        = $row.find('input[name="qty"]').val();
                var refund_quantity     = $row.find('input[name="refund_quantity"]').val();
                var refund_price        = $row.find('input[name="refund_price"]').val();
                var total_refund        = refund_quantity * refund_price;
                var str     	        = "Item already exists! Please select another item.";

				// if (parseFloat(qty) == 0) {
				if (parseFloat(refund_quantity) == 0) {
                	bootbox.alert('Invalid Quantity entered!');
                	return false;
                }
                
                $.each($tds, function() {
                    if(c == 0) {
                        sku = $(this).text();
                    }
                    c++;
                });

                if(rowCount > 0) {
                    for(var k = 0; k < rowCount; k++) {
                        // alert('[rowCount: '+rowCount+'][k: '+ k +']');
                        for (var j = 0; j < localStorage.length; j++){
                            var key = "transId" + k;
                            if (localStorage.key(j) == key) {
                                var item = localStorage.getItem(localStorage.key(j));

                                if(item == sku){
                                    // alert('[Key: '+ key +'] '+ item + ' [got id: '+ trans_detail_id +']');

                                    exists = 1;
                                    bootbox.alert("The item already EXISTS! Please select another item.", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                                    return false;
                                }
                            }
                        }
                    }
                } 

                c = 0;

                if(exists == 0) {
                    var total_transId = 0;

                    $.each($tds, function() {
                        if(c == 0) {
                            trans_detail_id = $(this).text();

                            for(var j = 0; j < rowCount; j++) {
                                for (var k = 0; k < localStorage.length; k++) {
                                    var key = "transId" + j;

                                    if (localStorage.key(k) == key) {
                                        // alert('[length: '+localStorage.length+'] [j: '+j+'][k: '+k+'] [key: '+key+']');
                                        total_transId++;
                                    }
                                }
                            }

                            localStorage.setItem("transId" + total_transId, trans_detail_id);
                        } 
                        else{
                            localStorage.setItem("trans" + c, $(this).text().trim());
                        }
                        
                        // alert('c: '+c+' value: '+ $(this).text().trim());
                        c++;
                    });
                    
                    parent.$('#emptyproduct').hide();
                    
                    var gst_rate            = localStorage.trans9;
                    var org_qty             = parseFloat(localStorage.trans6);
                    //var qty         = $('input[name="qty"]').val().trim();
                    // var refund_quantity     = $('input[name="refund_quantity"]').val();
                    var org_price           = localStorage.trans6;
                    // var refund_price        = $('input[name="refund_price"]').val();
                    // var total_refund        = refund_quantity * refund_price;
                    
                    var disc;
                    var subtotal;
                
                

                    // if (parseFloat(qty) < org_qty) {
                    // if (parseFloat(refund_quantity) < org_qty) {
                    //     disc        = localStorage.trans8 / localStorage.trans6 * refund_quantity;
                    //     subtotal    = refund_quantity * refund_price - disc;
                    //     price       = localStorage.trans7 - disc;
                    //     alert('QTY changed! [SubTotal: '+ subtotal +']');
                    // } 
                    // else {
                    //     qty         = localStorage.trans7;
                    //     disc        = localStorage.trans9;
                    //     subtotal    = localStorage.trans11;
                    //     price       = localStorage.trans8;
                    // }

                    if (parseFloat(gst_rate) > 0) {
                        var gst_value   = parseFloat(price) * gst_rate / 100;
                        var total       = parseFloat(price).toFixed(2) + parseFloat(gst_value).toFixed(2);
                        subtotal        = parseFloat(total).toFixed(2) * refund_quantity + gst_value;
                        // alert('[gst_rate: ' + gst_rate + '] [gst_value: '+ gst_value +'] [price: ' + price +'] [disc: ' + disc+ '] [total: '+ total +'] [subtotal: '+ subtotal +']');
                    }

                    /*
                        <input type="hidden" id="priceopt[]" name="priceopt[]" value="'+lid+'">\
                        <input type="hidden" id="qrcode[]" name="qrcode[]" value="{{$qrcode}}">\
                    */
                   
                    // Add to refund product list after click select inside "Add Product"
                    var rowTd = $('<tr id="'+trans_detail_id+'" class="product">\
                                        <input type="hidden" id="trans[]" name="trans[]" value="'+localStorage.trans2+'">\
                                        <input type="hidden" id="refund_quantity[]" name="refund_quantity[]" value="'+refund_quantity+'">\
                                        <input type="hidden" id="refund_price[]" name="refund_price[]" value="'+refund_price+'"> \
                                        <input type="hidden" id="total_refund[]" name="total_refund[]" value="'+total_refund+'"> \
                                        <!-- <input type="hidden" id="disc[]" name="disc[]" value="'+ parseFloat(disc).toFixed(2) +'"> -->\
                                        <!-- <input type="hidden" id="total[]" name="total[]" value="'+parseFloat(subtotal).toFixed(2)+'"> -->\
                                        <input type="hidden" id="productID[]" name="productID[]" value="'+localStorage.trans1+'">\
                                        <input type="hidden" id="productName[]" name="productName[]" value="'+localStorage.trans3+'">\
                                        <input type="hidden" id="sku[]" name="sku[]" value="'+trans_detail_id+'">\
                                        <input type="hidden" id="itemName[]" name="itemName[]" value="'+localStorage.trans4+'">\
                                        <input type="hidden" id="label[]" name="label[]" value="'+localStorage.trans5+'">\
                                        <input type="hidden" id="price[]" name="price[]" value="'+price+'">\
                                        <td> <b> '+ localStorage.trans3 +'</b> <br><i class="fa fa-tag"></i> '+trans_detail_id+'</td>\
                                        <td class="hidden-xs hidden-sm col-xs-1 text-center">'+localStorage.trans4+'<br>'+localStorage.trans5+'</td>\
                                        <td class="hidden-xs hidden-sm col-xs-1 text-center price">'+price+'</td>\
                                        <!-- <td class="hidden-xs hidden-sm col-xs-1 text-center price">'+localStorage.trans6+'</td> -->\
                                        <!-- <td class="hidden-xs hidden-sm col-xs-1 text-center gst">'+localStorage.trans9+'</td> -->\
                                        <!-- <td class="hidden-xs hidden-sm col-xs-1 text-center qty">'+localStorage.trans6+'</td> -->\
                                        <!-- <td class="hidden-xs hidden-sm col-xs-1 text-center disc">'+localStorage.trans8+'</td> -->\
                                        <!-- <td id="subtotal" class="hidden-xs hidden-sm col-xs-1 text-center subtotal">'+localStorage.trans10+'</td> -->\
                                        <td id="refund_quantity" class="hidden-xs hidden-sm col-xs-1 text-center refund_quantity">'+refund_quantity+'</td>\
                                        <td id="refund_price" class="hidden-xs hidden-sm col-xs-1 text-center refund_price">'+parseFloat(refund_price).toFixed(2)+'</td>\
                                        <td id="total_refund" class="hidden-xs hidden-sm col-xs-1 text-center total_refund">'+parseFloat(total_refund).toFixed(2)+'</td>\
                                        <!-- <td  class="hidden-xs hidden-sm col-xs-1 text-right subtotal">'+ parseFloat(subtotal).toFixed(2) +'</td>-->\
                                        <!-- <td class="edit col-xs-1">\
                                            <?php if (!in_array(Session::get('username'), array('nadzri_account'), true )) {  ?>\
                                                {{  Form::text('refund_quantity[]', number_format($product->unit), ['class' => 'refund_quantity form-control text-center', 'id' => 'refund_quantity'])  }}\
                                            <?php } else { ?>\
                                                {{ Form::text('refund_quantity[]', $product->unit, ['class' => 'form-control text-center', 'disabled']) }}\
                                            <?php }  ?>\
                                        </td>\
                                        <td class="edit col-xs-1">\
                                            <?php if (!in_array(Session::get('username'), array('nadzri_account'), true )) {  ?>\
                                                {{  Form::text('refund_price[]', number_format($product->price), ['class' => 'refund_price form-control text-center', 'id' => 'refund_price'])  }}\
                                            <?php } else { ?>\
                                                {{ Form::text('refund_price[]', number_format($product->price), ['class' => 'refund_price form-control text-center', 'id' => 'refund_price', 'disabled']) }}\
                                            <?php }  ?>\
                                        </td>\
                                        <td class="edit col-xs-1">\
                                                {{ Form::text('total_refund[]', '', ['class' => 'total_refund form-control text-center', 'id' => 'total_refund', 'readonly']) }}\
                                        </td> -->\
                                        <td class="text-center col-xs-1">\
                                            <div class="btn-group">\
                                                <a id="delete_product_option" class="btn btn-primary btn-danger"><i class="fa fa-trash-o"></i></a>\
                                            </div>\
                                        </td>\
                                    </tr>');

                        parent.$('#ptb').append(rowTd);
                        parent.$.fn.colorbox.close();
                        return false;
                }
            });
        });
       
    </script>   
</body>
</html>