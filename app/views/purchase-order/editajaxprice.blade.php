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
                        <h3 class="page-header">Product name : {{$name}} (SKU: {{$sku}})
                            <span class="pull-right"><a class="btn btn-default" title="" href="/purchase-order/pbx/editproducts"><i class="fa fa-reply"></i> Back</a></span>
                        </h3>
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
                            <h3 class="panel-title"><i class="fa fa-list"></i> Price List</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="col-lg-12 table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1 text-center">ID</th>
                                            <th class="col-sm-3">Label</th>
                                            <th class="col-md-2">Price (MYR)</th>
                                            <th class="col-md-1">Promo Price (MYR)</th>
                                            <!-- <th class="col-md-1">Price (USD)</th>
                                            <th class="col-md-1">Promo Price (USD)</th> -->
                                            <th class="col-md-1">Base Unit</th>
                                            <th class="col-md-1">Packing Factor</th>
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
                "ajax" : "{{ URL::to('purchase-order/pbx/editpriceajax/'.$id) }}",
                "order" : [[ 0, 'desc' ]],
                "lengthMenu" : [ 5 ],
                "bLengthChange" : false,
                "columnDefs" : [{
                    "targets" : "_all",
                    "defaultContent" : ""
                }],
                "columns" : [
                    { "data" : "0", "className" : "text-center" },
                    { "data" : "1" },
                    { "data" : "2", "className" : "text-right" , "orderable" : false},
                    { "data" : "3", "className" : "text-right", "orderable" : false},
                    // { "data" : "4", "visible" : true, "orderable" : false  },
                    // { "data" : "5", "visible" : true, "orderable" : false  },
                    { "data" : "4", "orderable" : false, "searchable" : false, "className" : "text-center" },
                    { "data" : "5", "orderable" : false, "searchable" : false, "className" : "text-center" },
                    { "data" : "6", "orderable" : false, "searchable" : false, "className" : "text-center" },                  
                ],
                "createdRow" : function (row, data, rowIndex) {
                    $.each($('td', row), function (colIndex) {
                        $(this).attr('data-passdata', data[colIndex]);
                        $(this).attr('id', data[0]);
                    });
                }
            });

            $(document).on("click", "#selectItem", function() {
                var qty = prompt("Product: {{$name}}"+"\nQuantity: ", "1");

                var $row = $(this).closest("tr"),
                    $tds = $row.find("td");


                var c = 0;
                var lid;

                $.each($tds, function() {
                    if(c < 1) {
                        lid = $(this).text(),
                        localStorage.setItem("data" + c, "")
                    } else if (c == 3) {
                        localStorage.setItem("data" + c, $(this).find(":input").val());
                    } else if (c == 4) {
                        localStorage.setItem("data" + c, $(this).find(":selected").val());
                        localStorage.setItem("data" + c + "text", $(this).find(":selected").text());
                    } else if (c == 5) {
                        localStorage.setItem("data" + c, $(this).find(":input").val());
                    } else {
                        localStorage.setItem("data" + c, $(this).text());
                    }
                    
                    // alert('c: '+c+' value: '+ $(this).text());
                    c++;
                });


                // <tr class="odd gradeX product" id="{{$tempcount}}">
                //                                                 <td>{{$tempcount}}</td>
                //                                                 <td>{{$trans_details->product_name}}</td>
                //                                                 <td>{{$trans_details->price_label}}</td>                     
                //                                                 <td>{{$trans_details->sku}}<br>{{$trans_details->seller_sku}}</td>
                //                                                 <td>{{$trans_details->base_unit}}</td>
                //                                                 <td>{{$trans_details->packing_factor}}</td>
                //                                                 <td><input type="number" name="quantity[]" id="quantity" value="{{$trans_details->quantity}}"></td>
                //                                                 <td><input type="number" name="price[]" class="price" id="price" value="{{number_format($trans_details->price,2)}}"></td>
                //                                                 <td class="subtotal">{{number_format($trans_details->total,2)}}</td>
                //                                                 <input type="hidden" name="subtotal[]" value="{{number_format($trans_details->total,2)}}">
                //                                                 <input type="hidden" name="detail_id[]" value="{{$trans_details->id}}">
                //                                                 <td class="text-center col-xs-1">
                //                                                     <div class="btn-group">
                //                                                         <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                //                                                     </div>
                //                                                 </td>
                //                                             </tr>

                var subTotal = parseFloat(qty * localStorage.data3).toFixed(2);

                var rowTd = $('<tr class="odd gradeX product" row-type="add">\
                                <input type="hidden" id="addpriceopt[]" name="priceopt[]" value="'+lid+'">\
                                <input type="hidden" id="addqrcode[]" name="qrcode[]" value="{{$qrcode}}">\
                                <input type="hidden" id="add_quantity[]" name="add_quantity[]" value="'+qty+'">\\n\
                                <input type="hidden" id="addprice_local" name="price_local[]" value="'+localStorage.data2+'">\\n\
                                <input type="hidden" id="add_price" name="add_price[]" value="'+localStorage.data3+'">\\n\
                                <input type="hidden" id="addbase_unit" name="base_unit[]" value="'+localStorage.data4+'">\\n\
                                <input type="hidden" id="addpacking_factor" name="packing_factor[]" value="'+localStorage.data5+'">\\n\
                                <input type="hidden" class="subtotal" name="subtotal[]" value="'+subTotal+'">\
                                <input type="hidden" name="add_total[]" value="'+subTotal+'" />\
                                <input type="hidden" name="add_sst[]" value="0" />\
                                <td>{{$name}}</td>\
                                <td class="hidden-xs hidden-sm">'+localStorage.data1+'</td>\
                                <td>{{$sku}}</td>\
                                <td class="hidden-xs hidden-sm col-xs-1">'+localStorage.data4text+'</td>\
                                <td class="hidden-xs hidden-sm col-xs-1">'+localStorage.data5+'</td>\
                                <td class="hidden-xs hidden-sm col-xs-1 text-right quantity"><input type="number" name="quantity[]" class="quantity" id="quantity" value="'+parseFloat(qty).toFixed(0)+'"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 text-right price"><input type="number" name="price[]" class="price" id="price" value="'+localStorage.data3+'"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 subtotal"></td>\
                                <td><input type="number" name="sst[]" class="sst" id="sst" value="0"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 subtotal-sst"></td>\
                                <td class="text-center col-xs-1">\
                                    <div class="btn-group">\
                                        <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                    </div>\
                                </td>\
                            </tr>');

                    
                // var rowTd1 = $('<tr id="'+lid+'" class="product">\
                //                 <input type="hidden" id="priceopt[]" name="priceopt[]" value="'+lid+'">\
                //                 <input type="hidden" id="qrcode[]" name="qrcode[]" value="{{$qrcode}}">\
                //                 <input type="hidden" id="qty[]" name="qty[]" value="'+qty+'">\\n\
                //                 <input type="hidden" id="price_local" name="price_local[]" value="'+localStorage.data2+'">\\n\
                //                 <input type="hidden" id="price_promo_local" name="price_promo_local[]" value="'+localStorage.data3+'">\\n\
                //                 <input type="hidden" id="base_unit" name="base_unit[]" value="'+localStorage.data4+'">\\n\
                //                 <input type="hidden" id="packing_factor" name="packing_factor[]" value="'+localStorage.data5+'">\\n\
                //                 <td> <b> {{ $name }}</b> <br><i class="fa fa-tag"></i> {{$sku}} <br>'+ localStorage.data0 +'</td>\
                //                 <td class="hidden-xs hidden-sm">'+localStorage.data1+'</td>\
                //                 <td class="hidden-xs hidden-sm col-xs-1 text-right p_price">MYR '+localStorage.data2+'</div></td>\
                //                 <td class="hidden-xs hidden-sm col-xs-1 text-right promo_price">MYR '+localStorage.data3+'</td>\
                //                 <td class="hidden-xs hidden-sm col-xs-1 text-center">'+localStorage.data4text+'</td>\
                //                 <td class="hidden-xs hidden-sm col-xs-1 text-right qty">'+qty+'</td>\
                //                 <td id="subtotal" class="hidden-xs hidden-sm col-xs-1 text-right subtotal"></td>\
                //                 <td class="text-center col-xs-1">\
                //                     <div class="btn-group">\
                //                         <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                //                     </div>\
                //                 </td>\
                //             </tr>');
                    
                
               

                var i       = 0;
                var exists  = 0;
                var rowCount = parent.$('#ptb tr').length;
                var str     = "Item already exists! Please select another item.";

                if(rowCount > 0) {
                    for(var k = 0; k < rowCount; k++) {
                        for (var j = 0; j < localStorage.length; j++){
                            var key = "trid" + k;

                            if (localStorage.key(j) == key) {
                                //alert('[A] Total ROW: '+rowCount+'\n[j: '+j+'] LID: '+lid+'\nStorage [trid'+k+']['+localStorage.key(j)+']: ' + localStorage.getItem(localStorage.key(j)));
                                var item = localStorage.getItem(localStorage.key(j));

                                if(item == lid){
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
                
                if (exists == 0 && qty != null) {
                    parent.$('#ptb').append(rowTd);

                    localStorage.setItem("trid"+ rowCount, lid);
                    
                    //alert('[B] Total ROW: '+rowCount+'\nLID: '+lid+'\nStorage [trid'+rowCount+']: ' + localStorage.getItem("trid"+ rowCount));
                    
                    var subTotal = $('<td id=subTotal></td>');
                    
                    var rowTotal = $('<tr id="grandTotal"><td class="text-right" colspan="5"><b>Total:</b></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_qty"></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_total"></td><td></td></tr>');

                    parent.$('#subtotal').append(subTotal);
                    // parent.$('#ptb').append(rowTotal);

                    // parent.$('input#product').val(localStorage.data3);
                    // parent.$('input#product_id').val(localStorage.data0);
                    parent.$.fn.colorbox.close();
                    return false;
               }
                
            });
        });

        function enablePacking(id) {
            var selectedBase = $('#base_unit' + id + " option:selected").val();
            if (selectedBase == 'P') {
                $('#packing_factor' + id).removeAttr('disabled');
            } else {
                $('#packing_factor' + id).val('');
                $('#packing_factor' + id).attr('disabled','disabled');
            }
            

        }
       
    </script>   
</body>
</html>