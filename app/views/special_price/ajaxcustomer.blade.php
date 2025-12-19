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
                    <h1 class="page-header">Customers</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">            
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-list"></i> Customer List</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-customers">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1 text-center">ID</th>
                                            <th class="col-sm-2">Username</th>
                                            <th class="col-sm-2">First Name</th>
                                            <th class="col-sm-2">Last Name</th>
                                            <th class="col-sm-3">Email</th>
                                            <th class="text-center col-sm-2">Action</th>                                            
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
            $('#dataTables-customers').dataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : "{{ URL::to('special_price/customersajax') }}",
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

            $(document).on("click", "#selectCust", function() {
                var $row = $(this).closest("tr"),
                    $tds = $row.find("td");

                var c = 0;
                $.each($tds, function() {
                    localStorage.setItem("data" + c, $(this).text())
                    c++;
                });

                var id = localStorage.data0;

                parent.$('#emptycustomer').hide();

                var rowTd = $('<tr class="customer">\
                                    <input type="hidden" id="cid[]" name="cid[]" value="'+localStorage.data0+'">\
                                    <td class="text-center">'+localStorage.data0+'</td>\
                                    <td class="hidden-xs hidden-sm">'+localStorage.data1+'<p class="help-block"></td>\
                                    <td class="hidden-xs hidden-sm col-xs-1 text-center">'+localStorage.data2+'</td>\
                                    <td class="hidden-xs hidden-sm col-xs-1 text-center">'+localStorage.data3+'</td>\
                                    <td class="hidden-xs hidden-sm col-xs-1">'+localStorage.data4+'</td>\
                                    <td class="text-center col-xs-1">\
                                        <div class="btn-group">\
                                            <a class="btn btn-xs btn-danger" id="deleteCust" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                        </div>\
                                    </td>\
                                </tr>');
                
                var rowCount    = parent.$('#ctb tr').length;
                var rowCountE   = parent.$('#ectb tr').length;
                var exists      = 0;
                // alert('Length: '+rowCountE);

                if(rowCount > 1 || rowCountE > 0) {
                    bootbox.alert("Only one(1) customer is allowed.", function(e){
                        exists = 1;
                        parent.$.fn.colorbox.close();
                    });
                    return false;
                } 

                // if(exists == 0) {
                //     parent.$('#ctb').append(rowTd);
                //     parent.$('#ectb').append(rowTd);
                //     localStorage.setItem("cid"+ rowCount, id);
                //     parent.$.fn.colorbox.close();
                //     return false;
                // }

                var rowCount = parent.$('#gctb tr').length;
                var exists  = 0;
                // alert('Length: '+rowCount);

                if(rowCount > 0) {
                    for(var k = 0; k < rowCount; k++) {
                        for (var j = 0; j < localStorage.length; j++){

                            // alert('[Total row: '+rowCount+'][k: '+k+'] [j: '+j+'] Storage ['+localStorage.key(j)+ '] '+localStorage.getItem(localStorage.key(j)));
                            var key = "cid" + k;

                            if (localStorage.key(j) == key) {
                                //alert('[A] Total ROW: '+rowCount+'\n[j: '+j+'] LID: '+lid+'\nStorage [trid'+k+']['+localStorage.key(j)+']: ' + localStorage.getItem(localStorage.key(j)));
                                var item = localStorage.getItem(localStorage.key(j));

                                if(item == id){
                                    exists = 1;
                                    bootbox.alert("The customer is already EXISTS! Please select another customer.", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                                    return false;
                                }
                            }
                        }
                    }
                } 

                if(exists == 0) {
                    parent.$('#ctb').append(rowTd);
                    parent.$('#ectb').append(rowTd);
                    parent.$('#gctb').append(rowTd);
                    localStorage.setItem("cid"+ rowCount, id);
                    parent.$.fn.colorbox.close();
                    return false;
                }

            });

        });

        
    </script>   
</body>
</html>