<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Add Product</title>
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/sb-admin-2.css') }}
        {{ HTML::style('font-awesome/css/font-awesome.min.css') }}
        {{ HTML::style('css/plugins/dataTables.bootstrap.css') }}
        {{ HTML::style('css/colorbox.css') }}
        {{ HTML::style('css/custom.css') }}
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{ HTML::script('js/jquery.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}
        {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}
        {{ HTML::script('js/plugins/dataTables/jquery.dataTables.js') }}
        {{ HTML::script('js/plugins/dataTables/dataTables.bootstrap.js') }}
        {{ HTML::script('js/sb-admin-2.js') }}
        {{ HTML::script('js/bootbox.js') }}
        {{ HTML::script('js/jquery.colorbox.js') }}
    </head>
    <body>
        <div class="wrapper">
            <div id="page-wrapper" style="margin: 0">
                <h3 class="page-header">Add Product</h3>
                <form method="get" action="{{ url('product/category/charityproduct/addoption/'.$charityId) }}">
                    <div class="table-responsive" style="overflow-x: none">
                        <table id="datatable-products" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SKU</th>
                                    <th>Seller</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('#datatable-products').dataTable({
                    'processing'   : true,
                    'serverSide'   : true,
                    'ajax'         : '{{ url('product/category/charityproduct/datatable') }}',
                    'order'        : [[ 0, 'desc' ]],
                    'lengthMenu'   : [ 10 ]
                });

                $('.selectProduct').each(function () {
                    var select = $(this);
                    console.log();
                });
            });
        </script>
    </body>
</html>