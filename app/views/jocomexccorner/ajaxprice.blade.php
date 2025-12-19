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
                            <span class="pull-right"><a class="btn btn-default" title="" href="/flashsale/ajaxproduct/"><i class="fa fa-reply"></i> Back</a></span>
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
                "ajax" : "{{ URL::to('flashsale/pricesajax/'.$id) }}",
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
                ],
                "createdRow" : function (row, data, rowIndex) {
                    $(row).attr('id', data[0]);
                    // $.each($('td', row), function (colIndex) {
                    //     $(this).attr('data-passdata', data[colIndex]);
                    // });
                    // alert('data[4] : ' + data[4]);
                }

            });

            $(document).on("click", "#selectItem", function(e) {
                parent.$('#emptyproduct').hide();

                var exists          = 0;
                var id              = $(this).attr("href");
                var label           = $('#' + id + ' > :nth-child(2)').text();
                var actual_price    = $('#' + id + ' > :nth-child(3)').text();
                var eliminate=actual_price.replaceAll(",","");
                var promo_price     = $('#' + id + ' > :nth-child(4)').text();
                var disc_amount     = $('#amount').val();
                var disc_type       = $('#type').val();
                var disc;

                switch(disc_type) {
                    case '%'    : disc = disc_amount + ' ' + disc_type;
                            break;

                    case 'N'    : disc = '' + currencyFormat(parseFloat(disc_amount));
                            break;
                }

                // alert('[Disc amt: ' + disc_amount +'] [Disc type: ' + disc_type +'] [DISCOUNT: ' + disc +']');
                // return false;

                var rowTd   = '<input type="hidden" name="label_id[]" value="' + id + '">\
                                <input type="hidden" name="product_id[]" value="{{$id}}">\
                                <input type="hidden" name="label[]" value="' + label + '">\
                                <input type="hidden" name="price[]" value="'+eliminate+'">\
                                <td><b>{{$name}}</b><br><i class="fa fa-tag"></i> {{$sku}} ' + '</td><td>' + label + '</td>\
                                <td class="text-center col-xs-1">'+eliminate+'</td>\
                                <td class="text-center col-xs-1"><input type="text" name="promo_price[]" class="form-control"></td>\
                                <td class="text-center col-xs-1"><input type="text" name="qty[]" class="form-control"></td>\
                                <td class="text-center col-xs-1"><input type="text" name="seq[]" class="form-control seq" required></td>\
                                <td class="text-center col-xs-1"><div class="btn-group">\
                                <a class="btn btn-xs btn-danger" id="deleteProduct" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                </div></td>';

                parent.$('#ptb tr').each(function() {
                    if (this.id != 'emptyproduct' && this.id == id) {
                        exists = 1;
                    }
                });

                // alert('EXISTS [1] - - > ' + exists);
                if (exists == 0) {
                    parent.$('#ptb').append('<tr id="'+ id +'" class="disc_product">'+rowTd+'</tr>');
                    parent.$.fn.colorbox.close();
                    return false;
                    
                } else {
                    e.preventDefault();
                    bootbox.alert("The item already EXISTS! Please select another item.", function(e){
                        parent.$.fn.colorbox.close();
                    });
                    return false;
                }
            });
        });

        function currencyFormat(num) {
            return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        }       
    </script>   
</body>
</html>