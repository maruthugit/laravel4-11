<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>AJAX Sellers</title>
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
                    <h3 class="page-header">Purchase Order Management - Select Seller</h1>
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
                            <h3 class="panel-title"><i class="fa fa-list"></i> Seller Listing</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-sellers">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th class="col-sm-3">Username</th>
                                            <th class="col-sm-2">Full Name</th>
                                            <th class="col-sm-3">Email</th>
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
            $('#dataTables-sellers').dataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : "{{ URL::to('purchase-order/fetch-seller') }}",
                "order" : [[0,'asc']],
                "lengthMenu" : [ 10 ],
                "bLengthChange" : false,
                "columnDefs" : [{
                    "targets" : "_all",
                    "defaultContent" : ""
                }],
                "columns" : [
                    { "data" : "0", "orderable" : false, "searchable" : false, "className" : "col-sm-1" },
                    { "data" : "1" },
                    { "data" : "2" },
                    { "data" : "3" },
                    // { "data" : "4" },
                    { "data" : "4", "orderable" : false, "searchable" : false, "className" : "text-center"},                  
                ],
                "createdRow" : function (row, data, rowIndex) {
                    $.each($('td', row), function (colIndex) {
                        $(this).attr('data-passdata', data[colIndex]);
                        $(this).attr('id', data[0]);
                    });
                }
            });

            $(document).on("click", "#selectSeller", function() {
                var $row = $(this).closest("tr"),
                    $tds = $row.find("td");

                var c = 0;
                $.each($tds, function() {
                    localStorage.setItem("data" + c, $(this).text())
                    c++;
                });
                parent.$('input#seller').val(localStorage.data2);
                parent.$('input#seller_id').val(localStorage.data0);
                parent.$('input#seller_name').val(localStorage.data2);

                parent.$.fn.colorbox.close();
                return false;                
            });            
        });
    </script>   
</body>
</html>