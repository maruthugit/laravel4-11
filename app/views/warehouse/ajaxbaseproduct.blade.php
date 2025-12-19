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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    {{ HTML::script('js/jquery.js') }}

    <!-- Bootstrap Core JavaScript -->
    {{ HTML::script('js/bootstrap.min.js') }}

    <!-- Metis Menu Plugin JavaScript -->
    {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}

    <!-- Morris Charts JavaScript -->
    <!-- {{ HTML::script('js/plugins/morris/raphael.min.js') }}
    {{ HTML::script('js/plugins/morris/morris.min.js') }}
    {{ HTML::script('js/plugins/morris/morris-data.js') }} -->

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
                    <h1 class="page-header">Products</h1>
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
                            <h3 class="panel-title"><i class="fa fa-list"></i> Product List</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1 text-center">ID</th>
                                            <th class="col-sm-1">SKU</th>
                                            <th class="col-sm-3">Seller Name</th>
                                            <th class="col-sm-4">Product Name</th>
                                            <th class="col-sm-2">Category</th>
                                            <!-- <th class="col-sm-1 text-center">Price&nbsp;</th> -->
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

    <php die(); ?>
    <!-- /#wrapper -->

    <script type="text/javascript">
        $(document).ready(function() {

            $('#dataTables-products').dataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : "{{ URL::to('warehouse/productsbaseajax?id='.$productID )}}",

                "order" : [[ 0, 'desc' ]],
                "lengthMenu" : [ 8 ],
                "bLengthChange" : false,
                "columnDefs" : [{
                    "targets" : "_all",
                    "defaultContent" : ""
                }],
                "columns" : [
                    { "data" : "0", "className" : "text-center" },
                    { "data" : "1" },
                    { "data" : "2" },
                    { "data" : "3" },
                    { "data" : "4" },
                    //{ "data" : "5", "className" : "text-right" },
                    // { "data" : "5", "className" : "text-right" },
                    { "data" : "5", "orderable" : false, "searchable" : false, "className" : "text-center"},                  
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
                var lid;

                $.each($tds, function() {
                    if(c < 1) {
                        lid = $(this).text(),
                        localStorage.setItem("data" + c, "")
                    } else {

                        localStorage.setItem("data" + c, $(this).text())
                    }
                    
                    // alert('c: '+c+' value: '+ $(this).text());
                    c++;
                });

                var pid = "{{$productID}}";

                console.log(localStorage);

                parent.$('#emptyproduct').hide();

                if(pid == lid){
                    exists = 1;
                    bootbox.alert("The item is base product! Please select another item.", function(e){
                        parent.$.fn.colorbox.close();
                    });
                    return false;
                }


                var rowTd = $('<tr id="'+lid+'" class="product">\
                                    <input type="hidden" id="base_productID[]" name="base_productID[]" value="'+lid+'">\
                                    <input id="base_productName[]" name="base_productName[]" value="'+localStorage.data3+'" type="hidden">\
                                    <td> '+ lid +'</td>\
                                    <td class="hidden-xs hidden-sm">'+localStorage.data3+'</td>\
                                    <td class="col-xs-1">\
                                        <div class="btn-group">\
                                            <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                        </div>\
                                    </td>\
                                </tr>');
               
               // <td class="hidden-xs hidden-sm col-xs-1 text-center r_length">'+parent.$('#multipleNodeProductsTable tr').length+'</td>\   - Removed row Length;




             


               
                var i       = 0;
                var exists  = 0;
                var rowCount = parent.$('#multipleBaseProductsTable tr').length;
                var str     = "Item already exists! Please select another item.";
                // alert(rowCount);
                if(rowCount > 0) {


                    for(var k = 0; k < rowCount; k++) {
                        for (var j = 0; j < localStorage.length; j++){
                            var key = "trid" + k;

                            if (localStorage.key(j) == key) {
                                // alert('[A] Total ROW: '+rowCount+'\n[j: '+j+'] LID: '+lid+'\nStorage [trid'+k+']['+localStorage.key(j)+']: ' + localStorage.getItem(localStorage.key(j)));
                                var item = localStorage.getItem(localStorage.key(j));

                                console.log(item+'log');

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


                

               

                
                
                if (exists == 0) {
                    parent.$('#multipleBaseProductsTable').append(rowTd);

                    localStorage.setItem("trid"+ rowCount, lid);
                    
                    var rowTotal = $('<tr id="grandTotal"><td colspan="2"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_price_total"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_promo_total"></td><td colspan="2"></td></tr>');

                    // parent.$('#multipleNodeProductsTable').append(rowTotal);
                    // var rowCount = $('#multipleNodeProductsTable').rowCount();
                    //  var totalRowCount = $("#multipleNodeProductsTable tr").length;
                    // parent.$('#add-lnkproduct-btn').addClass('disabled');  //Edited on 03 Aug 2017

                    // alert(rowCount);

                    parent.$('input#product').val(localStorage.data3);
                    parent.$('input#product_id').val(localStorage.data0);
                    parent.$.fn.colorbox.close();
                    return false;
               }
                
            });


            
            $(document).on("click", "#selectItem1", function() {

                $("#myModalAll").modal('show');

                alert()

                alert('Ok');
                // return false;
                // $.ajax({
                //     method: "POST",
                //     url: "/warehouse/productvariant",
                //     dataType:'json',
                //     data: {
                //         'product_id':pid
                //     },
                //     beforeSend: function(){
                //     },
                //     success: function(data) {

                      
                //        return false;
                //     }
                // });

                $('#dataTables-productsvariation').dataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : "{{ URL::to('warehouse/productsajax?id='.$productID )}}",

                "order" : [[ 0, 'desc' ]],
                "lengthMenu" : [ 8 ],
                "bLengthChange" : false,
                "columnDefs" : [{
                    "targets" : "_all",
                    "defaultContent" : ""
                }],
                "columns" : [
                    { "data" : "0", "className" : "text-center" },
                    { "data" : "1" },
                    { "data" : "2" },
                    { "data" : "3" },
                    { "data" : "4" },
                    //{ "data" : "5", "className" : "text-right" },
                    //{ "data" : "6", "className" : "text-right" },
                    { "data" : "5", "orderable" : false, "searchable" : false, "className" : "text-center"},                  
                ],
                "createdRow" : function (row, data, rowIndex) {
                    $.each($('td', row), function (colIndex) {
                        $(this).attr('data-passdata', data[colIndex]);
                        $(this).attr('id', data[0]);
                    });
                }
            });



                return false;
               
            });



        });
    </script>   
</body>
</html>