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
                            <span class="pull-right"><a class="btn btn-default" title="" href="../ajaxproduct/"><i class="fa fa-reply"></i> Back</a></span>
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
                                            <th class="col-sm-5">Label</th>
                                            <th class="col-sm-1">Price</th>
                                            <th class="col-sm-1">Promo Price</th>
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
                "ajax" : "{{ URL::to('product/pricesajax/'.$id) }}",
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
                    { "data" : "2", "className" : "text-right"},
                    { "data" : "3", "className" : "text-right"},
                    { "data" : "4", "orderable" : false, "searchable" : false, "className" : "text-center"},                  
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

                

                parent.$('#emptyproduct').hide();
                parent.$('#grandTotal').remove();

                var rowTd = $('<tr id="'+lid+'" class="product">\
                                    <input type="hidden" id="lid[]" name="lid[]" value="'+lid+'">\
                                    <td> <b> {{ $name }}</b> <br><i class="fa fa-tag"></i> {{$sku}} <br>'+ localStorage.data0 +'</td>\
                                    <td class="hidden-xs hidden-sm">'+localStorage.data1+'</td>\
                                    <td class="hidden-xs hidden-sm col-xs-1 text-right p_price">'+localStorage.data2+'</td>\
                                    <td class="hidden-xs hidden-sm col-xs-1 text-right promo_price">'+localStorage.data3+'</td>\
                                    <td class="hidden-xs hidden-sm col-xs-1"><input type="text" class="form-control col-xs-2" name="qty[]" value="1"></td>\
                                    <td class="text-center col-xs-1">\
                                        <div class="btn-group">\
                                            <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                        </div>\
                                    </td>\
                                </tr>');
               

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
                
                if (exists == 0) {
                    parent.$('#ptb').append(rowTd);

                    localStorage.setItem("trid"+ rowCount, lid);
                    
                    //alert('[B] Total ROW: '+rowCount+'\nLID: '+lid+'\nStorage [trid'+rowCount+']: ' + localStorage.getItem("trid"+ rowCount));
         
                    var rowTotal = $('<tr id="grandTotal"><td colspan="2"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_price_total"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_promo_total"></td><td colspan="2"></td></tr>');

                    parent.$('#ptb').append(rowTotal);

                    parent.$('input#product').val(localStorage.data3);
                    parent.$('input#product_id').val(localStorage.data0);
                    parent.$.fn.colorbox.close();
                    return false;
               }
                
            });
        });

       
    </script>   
</body>
</html>