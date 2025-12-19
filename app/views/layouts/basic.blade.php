<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>CMS</title>
    <link href="<?=url('images/favicon.png');?>" rel="icon" type="image/png">
    <!-- Bootstrap Core CSS -->
    {{-- HTML::style('//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css') --}}
    {{ HTML::style('css/bootstrap.min.css') }}

    <!-- MetisMenu CSS -->
    {{ HTML::style('css/plugins/metisMenu/metisMenu.min.css') }}

    <!-- Timeline CSS -->
    {{ HTML::style('css/plugins/timeline.css') }}

    <!-- Custom CSS -->
    {{ HTML::style('css/sb-admin-2.css') }}

    <!-- Morris Charts CSS -->
    {{ HTML::style('css/plugins/morris.css') }}

    <!-- Custom Fonts -->
    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

    <!-- DataTables CSS -->
    {{ HTML::style('css/plugins/dataTables.bootstrap.css') }}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <div class="wrapper">

        <div id="page-wrapper">
            @yield('content')
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

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

    <!-- AutoComplete JavaScript -->
    {{ HTML::script('js/jquery.autocomplete.js') }}

    <script type="text/javascript">

        // $(document).ready(function() {
        //     $('#dataTables-transaction').dataTable({
        //         "bSort": false,
        //         "bServer": false,
        //         // "bFilter": false
        //     });
        // });

        $(document).ready(function() {
            @yield('script')
            // $('#dataTables-transaction').dataTable({
            //    "processing": true,
            //     "serverSide": true,
            //     "ajax": "{{ URL::to('admin/transaction/listing') }}",
            //     "order" : [[0,'desc']],
            //     "pageLength": 25,
            //     "columnDefs" : [{
            //     "targets" : "_all",
            //     "defaultContent" : ""
            //     }],
            //     "columns" : [
            //     // { "data" : "0", "searchable" : false},
            //     // { "data" : "1", "orderable" : false, "searchable" : false}
            //     { "data" : "id"},
            //     { "data" : "transaction_date"},
            //     { "data" : "buyer_username" },
            //     { "data" : "total" },
            //     { "data" : "status" },
            //     { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
            //     ]
            // });

            // $('#dataTables-coupon').dataTable({
            //    "processing": true,
            //     "serverSide": true,
            //     "ajax": "{{ URL::to('admin/coupon/listing') }}",
            //     "order" : [[0,'desc']],
            //     "pageLength": 25,
            //     "columnDefs" : [{
            //     "targets" : "_all",
            //     "defaultContent" : ""
            //     }],
            //     "columns" : [
            //     { "data" : "id"},
            //     { "data" : "coupon_code"},
            //     { "data" : "amount" },
            //     { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
            //     ]
            // });
        });

        $(function() {
                var date = $('#datepicker').datepicker({ dateFormat: 'yy-mm-dd'}).val(); 
            });
    </script>


<!-- For datepicker in create new transaction -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">  
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    
</body>
</html>
