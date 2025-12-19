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
    <input type="hidden" id="price_id" value="{{$price_id}}">
    <div class="wrapper">
        <div id="page-wrapper" style="margin: 0">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Price Schedule</h1>
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
                            <h3 class="panel-title"><i class="fa fa-list"></i> Schedule List</h3>                    
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" style="overflow-x: none">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-1 text-center">ID</th>
                                            <th class="col-sm-1 text-right">Price</th>
                                            <th class="col-sm-1 text-right">Price Promo</th>
                                            <th class="col-sm-1 text-right">Cost</th>
                                            <th class="col-sm-1 text-right">Date</th>
                                            <th class="text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list">
                                        @foreach ($price_schedule as $schedule)
                                            <tr id="{{$schedule->id}}">
                                                <td class="col-sm-1 text-center">{{$schedule->id}}</td>
                                                <td class="text-right"><input type="text" class="form-control text-right price" value="{{ ($schedule->price != null) ? number_format($schedule->price, 2) : '' }}"></td>
                                                <td class="text-right"><input type="text" class="form-control text-right price_promo" value="{{ ($schedule->price_promo != null) ? number_format($schedule->price_promo, 2) : '' }}"></td>
                                                <td class="text-right"><input type="text" class="form-control text-right cost" value="{{ ($schedule->cost != null) ? number_format($schedule->cost, 2) : '' }}"></td>
                                                <td class="text-right">
                                                    <div class="input-group datepicker">
                                                        <input class="form-control text-right date" tabindex="1" type="text" value="{{ date_format(date_create($schedule->date), 'Y-m-d') }}">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-primary saveSchedule" data-toggle="tooltip" data-value="{{$schedule->id}}">Save</a>
                                                    <a class="btn btn-primary deleteSchedule" title="" data-toggle="tooltip" data-value="{{$schedule->id}}">Delete</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>                            
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-list"></i> Add Schedule</h3>                    
                        </div>

                        <div class="panel-body">
                            <div class="col-sm-12">
                                <!-- <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/purchase-order/pbx/products"><i class="fa fa-plus"></i> Add Schedule</span>
                                    </div>
                                </div> -->
                                <br />
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Price Promo</th>
                                            <th class="text-right">Cost</th>
                                            <th class="text-right">Date</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <td><input type="text" class="form-control text-right" id="new_price" value=""></td>
                                        <td><input type="text" class="form-control text-right" id="new_price_promo" value=""></td>
                                        <td><input type="text" class="form-control text-right" id="new_cost" value=""></td>
                                        <td><div class="input-group datepicker" id="datetimepicker_from">
                                                        <input id="new_date" class="form-control text-right" tabindex="1" type="text" value="">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                        </span>
                                                    </div></td>
                                        <td class="text-center"><a id="saveNewSchedule" class="btn btn-primary" title="" data-toggle="tooltip" data-value="'.$p->id.'">Save</a></td>
                                    </tbody>
                                </table>
                            </div>                         
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('body').on('click', ".datepicker", function(){
                $(this).datetimepicker({
                  format: 'YYYY-MM-DD'
              });
            });

            $('#saveNewSchedule').on('click', function() {
                var priceId = $('#price_id').val();
                var price = $('#new_price').val();
                var pricePromo = $('#new_price_promo').val();
                var cost = $('#new_cost').val();
                var date = $('#new_date').val();

                if (price == '' && pricePromo == '' && cost == '') {
                    alert('Please insert Price, Price Promo or Cost.')
                } else if (date == '') {
                    alert('Please insert date.')
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/product/createschedule',
                        data: {
                            "price_id" : priceId,
                            "price" : price,
                            "price_promo" : pricePromo,
                            "cost" : cost,
                            "date" : date
                        },
                        dataType: "json",
                        success: function(resultData) {

                            var id = resultData.id;
                            if (id != undefined) {
                                $('#new_price').val('');
                                $('#new_price_promo').val('');
                                $('#new_cost').val('');
                                $('#new_date').val('');
                                var newRow = '<tr id="'+id+'">\
                                                <td class="col-sm-1 text-center">'+id+'</td>\
                                                <td class="text-right"><input type="text" class="form-control text-right price" value="'+price+'"></td>\
                                                <td class="text-right"><input type="text" class="form-control text-right price_promo" value="'+pricePromo+'"></td>\
                                                <td class="text-right"><input type="text" class="form-control text-right cost" value="'+cost+'"></td>\
                                                <td class="text-right">\
                                                    <div class="input-group datepicker">\
                                                        <input class="form-control text-right date" tabindex="1" type="text" value="'+date+'">\
                                                        <span class="input-group-btn">\
                                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>\
                                                        </span>\
                                                    </div>\
                                                </td>\
                                                <td class="text-center">\
                                                    <a class="btn btn-primary saveSchedule" data-toggle="tooltip" data-value="'+id+'">Save</a>\
                                                    <a class="btn btn-primary deleteSchedule" title="" data-toggle="tooltip" data-value="'+id+'" >Delete</a>\
                                                </td>\
                                            </tr>';
                                var tbody = $('tbody#list').append(newRow);
                            }
                            
                            alert(resultData.message);
                        }
                    });
                }
            });

            $('body').on('click', ".saveSchedule", function(){
                var id = $(this).attr('data-value');
                var tr = $('#' + id);
                var price = tr.find('td input.price').val();
                var pricePromo = tr.find('td input.price_promo').val();
                var cost = tr.find('td input.cost').val();
                var date = tr.find('td input.date').val();

                if (price == '' && pricePromo == '' && cost == '') {
                    alert('Please insert Price, Price Promo or Cost.')
                } else if (date == '') {
                    alert('Please insert date.')
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/product/updateschedule',
                        data: {
                            "id" : id,
                            "price" : price,
                            "price_promo" : pricePromo,
                            "cost" : cost,
                            "date" : date
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                        }
                    });
                }
            });

            $('body').on('click', ".deleteSchedule", function(){
                var id = $(this).attr('data-value');
                var tr = $('#' + id);

                if (confirm('Delete Schedule ID - ' + id + '?')) {
                    $.ajax({
                        type: 'POST',
                        url: '/product/deleteschedule',
                        data: {
                            "id" : id
                        },
                        dataType: "json",
                        success: function(resultData) { 
                            alert(resultData.message);
                            tr.remove();
                        }
                    });
                }
                
            });
        });
    </script>   
</body>
</html>