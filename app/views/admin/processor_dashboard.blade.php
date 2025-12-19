@extends('layouts.master')

@section('title') Proccessor Dashboard @stop

@section('content')
<style>    
.title-cat {
    color: #2daf73;
}

.box-con {
    padding:20px;
    border: solid 1px #ddd;
    cursor: pointer;
}

.box-con:hover {
    padding:20px;
    background-color: #f3f3f3;
    border: solid 1px #ddd;
}

.child {
  display: inline-block;
}
    
a.overall, a.byDate, a.overallSendingPlaceRegion, a.overallstrAllProductSold {
    cursor: pointer;
    font-weight: bold;
    color: #989898;
}
</style>

<div><span id="load-message"></span></div>
<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <h1 class="page-header">Proccessor Dashboard<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}processorDashboard/processor"><i class="fa fa-refresh"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>      

    <div class="row text-center">
        <div class="col-lg-12 col-md-8">
            <div class="panel panel-default">
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Processor Dashboard List </h3>
        </div>
        <div class="panel-body">

            
            <div class="row">
                <div class="col-md-12 col-xs-12" style="margin-bottom:0px;padding-right: 0px;">
                    <div class="col-md-12">

                        <!-- CUSTOM DATE -->
                        {{-- <div class="col-lg-2" id="datetimepicker_from" style="display:none;">
                            <div class="input-group" id="datetimepicker_from2">
                                <input id="transaction_from" class="form-control" name="transaction_from" placeholder="YYYY/MM/DD" type="text"  required>
                                <span class="input-group-btn" >
                                    <button type="button" class="custom-date-from btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-2" id="datetimepicker_to" style="display:none;">
                            <div class="input-group" id="datetimepicker_to2">
                                <input id="transaction_to" class="form-control" name="transaction_to" placeholder="YYYY/MM/DD" type="text"  required>
                                <span class="input-group-btn">
                                    <button type="button" class="custom-date-2 btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-2" id="custom_search" style="display:none;">
                            <button class="btn btn-primary" value="Search" onclick="dateCheck()">Search</button>
                        </div> --}}

                        <div class="btn-group pull-right" role="group" aria-label="..." >
                            <table>
                                <tr>
                                    <td><!-- CUSTOM DATE  (FROM)-->
                                        <div id="datetimepicker_from" class="btn-group pull-right" role="group" aria-label="..." style="display:none;">
                                        <div class="input-group" id="datetimepicker_from2">
                                                <input id="transaction_from" class="form-control" name="transaction_from" placeholder="From" type="text" required>
                                                <span class="input-group-btn" >
                                                    <button type="button" class="custom-date-from btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><!-- CUSTOM DATE  (UNTIL)-->
                                        <div id="datetimepicker_to" class="btn-group pull-right" role="group" aria-label="..." style="display:none;">
                                            <div class="input-group" id="datetimepicker_to2">
                                                <input id="transaction_to" class="form-control" name="transaction_to" placeholder="Until" type="text"  required>
                                                <span class="input-group-btn">
                                                    <button type="button" class="custom-date-2 btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><!-- CUSTOM DATE  (BUTTON SEARCH)-->
                                        <button class="btn btn-primary" id="custom_search" value="Search" onclick="dateCheck()" style="display:none;">Search</button>
                                    </td>
                                    <td>
{{ Form::open(array('url'=>'processorDashboard/generateprocessorexcel', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }} 
                                        <div class="btn-group pull-right" role="group" aria-label="..." >

                                            <button type="button" class="btn btn-default" id="nav-left"><i class="fa fa-arrow-left"></i></button>
                                            <button type="button" id="date" class="btn btn-default disabled"><i class="fa fa-calendar-o"></i> 
                                                <span id="selected-date" name="selected-date"> 
                                                    <?php echo date("d M Y")?>
                                                    <input type="hidden" id="selected-date" name="selected-date" value="<?php echo date("d M Y")?>" />
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-default" id="nav-right"><i class="fa fa-arrow-right"></i></button>
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span id="selected-date-range">Daily</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                                                <input type="hidden" id="start-date-select" name="start-date-select" value="<?php echo date("Y-m-d")?>">
                                                <input type="hidden" id="to-date-select" name="to-date-select" value="<?php echo date("Y-m-d")?>">
                                                <input type="hidden" id="type-select" name="type-select" value="<?php echo 1;?>">
                                                
                                                <input type="hidden" id="start-today" name="start-today" value="<?php echo date("Y-m-d")?>">
                                                <input type="hidden" id="start-weekly-date" value="">
                                                <input type="hidden" id="end-weekly-date" value="">
                                                <input type="hidden" id="start-month-date" value="">
                                                <input type="hidden" id="end-month-date" value="">
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li ><a class="date-range" data-type="1"> Daily </a></li>
                                                <li ><a class="date-range" data-type="2"> Weekly </a></li>
                                                <li ><a class="date-range" data-type="3"> Monthly </a></li>
                                                <li ><a class="date-range" data-type="4"> Custom </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </table> 
                        </div>
                    </div>
                </div>
            </div>         
        </div>
            <hr>
        <div class="panel-body text-center">
                <div class="box-con" style="display: inline-block">
                    <div style="display: inline-block">
                        <div><i class="fa fa-bar-chart fa-2x"></i></div>
                    </div>
                    <div style="display: inline-block;padding-left: 10px;">
                        <div class="title-cat"><strong> Number of Orders </strong></div>
                        <div class="stat-total" id="total-order"></div>
                    </div>
                </div>
                <div class="box-con" style="display: inline-block">
                    <div style="display: inline-block">
                        <div><i class="fa fa-money fa-2x"></i></div>
                    </div>
                    <div style="display: inline-block;padding-left: 10px;">
                        <div class="title-cat"><strong> Total Revenue</strong></div>
                        <div class="stat-total" id="total-revenue"></div>
                    </div>
                </div>
        </div>
            <hr>
        <div class="panel-body">
            <div class="row">
                <div class="form-group">
                         <div class="col-md-12">                                
                            <button class="btn btn-primary pull-right" type="submit">Export</button>
                        </div>
                </div>
            </div> 
{{ Form::close() }}
            <hr>
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-bordered table-striped table-hover" id="dataTables-processing">
         
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Company Name</th>
                            <th>Product Name</th>   
                            <th>Label</th>   
                            <th>Unit Sold</th>    
                            <th>Revenue</th> 
                        </tr>
                    </thead>
                    <tbody id="list-all-product-sold"> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('inputjs')
<script>

    //custom date
    //after search button is clicked
    function dateCheck() { 
        var startDate = document.getElementById("transaction_from").value + " 00:00:00";
        var toDate = document.getElementById("transaction_to").value + " 23:23:59";
        var type = $('#type-select').val();
        getProcessor(0,type,startDate,toDate);            
        
    }
    
    $(document).ready(function() {        
        $("#datetimepicker_from2, #datetimepicker_to2").datetimepicker({
                format: "YYYY-MM-DD",
        });
    });

    var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    getProcessor(0,1,startDate,toDate);

    $(".date-range").click(function(){
        $("#selected-date-range").html($(this).html());
        $('#type-select').val($(this).attr("data-type"))
        
        //Daily
        if($('#type-select').val() == 1){

            $('#date').show();
            $('#nav-left').show();
            $('#nav-right').show();

            $('#custom_search').hide();
            $('#datetimepicker_from').hide();
            $('#datetimepicker_to').hide();

            var type = $('#type-select').val();
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            getProcessor(0,type,startDate,toDate);
        }
        
        //Weekly
        if($('#type-select').val() == 2){

            $('#date').show();
            $('#nav-left').show();
            $('#nav-right').show();

            $('#custom_search').hide();
            $('#datetimepicker_from').hide();
            $('#datetimepicker_to').hide();

            var type = $('#type-select').val();
            var startDate = $('#start-weekly-date').val();
            var toDate = $('#end-weekly-date').val();
            getProcessor(0,type,startDate,toDate);
        }
        
        //Monthly
        if($('#type-select').val() == 3){
            
            $('#date').show();
            $('#nav-left').show();
            $('#nav-right').show();

            $('#custom_search').hide();
            $('#datetimepicker_from').hide();
            $('#datetimepicker_to').hide();

            var type = $('#type-select').val();
            var startDate = $('#start-month-date').val();
            var toDate = $('#end-month-date').val();
            getProcessor(0,type,startDate,toDate);            
        }
        
        //Custom date
        if($('#type-select').val() == 4){
            $('#date').hide();
            $('#nav-left').hide();
            $('#nav-right').hide();

            $('#custom_search').show();
            $('#datetimepicker_from').show();
            $('#datetimepicker_to').show();
            
            var type = $('#type-select').val();
            var startDate = document.getElementById("transaction_from").value + " 00:00:00";
            var toDate = document.getElementById("transaction_to").value + " 23:23:59";
            getProcessor(0,type,startDate,toDate);   
            
        }
    });

    // Custom date
    // $(".custom-date-from").click(function(){

    //             console.log("datetimepicker_to");
    //             var type = $('#type-select').val();
    //             var startDate = $('#datetimepicker_from').val();
    //             var toDate = $('#datetimepicker_to').val();
    //             getProcessor(0,type,startDate,toDate);

    // });

    $("#nav-left").click(function(){
        console.log('LEFT');
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        getProcessor(1,type,startDate,toDate);
        //$("#selected-date-range").html($(this).html());
    });
    
    $("#nav-right").click(function(){
        console.log('RIGHT');
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        getProcessor(2,type,startDate,toDate);
        //$("#selected-date-range").html($(this).html());
    });

    function getProcessor(navigation,type,startDate,toDate){
        //Get data base on the selected date and type (Daily,Weekly,Montly)
        $.ajax({

            method: "POST",
            url: "/processorDashboard/dashboardprocessor",
            dataType:'json',
            data: {
                'navigation':navigation,
                'rangeType':type,
                'startDate':startDate,
                'toDate':toDate

            },
            success: function(data) {
                // console.log(data);
                
                var allProductSold = data.data.TotalProcessor.ProcessorDashboard.allProductSold;
                // console.log(allProductSold);

                var strAllProductSold = '';
                $.each( JSON.parse(allProductSold), function( key, value ) {
                    strAllProductSold = strAllProductSold + '<tr><td style="text-align:left;">'
                                            +value.sku+'</td><td style="text-align:left;">'
                                            +value.companyName+'</td><td style="text-align:left;">'
                                            +value.productName+'</td><td style="text-align:left;">'
                                            +value.label+'</td><td style="text-align:left;">'
                                            +value.unitsSold+'</td><td style="width:40px;">'
                                            +value.revenue+'</td></tr> ';
                });

                $("#list-all-product-sold").html(strAllProductSold);
                
                $("#total-order").html(data.data.TotalProcessor.TotalOrderAndRevenue.totalOrder);
                $("#total-revenue").html(data.data.TotalProcessor.TotalOrderAndRevenue.totalRevenue);

                $("#start-date-select").val(data.data.DateSelection.startDate);
                $("#to-date-select").val(data.data.DateSelection.toDate);

                if($('#type-select').val() == 1){
                    $("#selected-date").html(data.data.DateSelection.displayStartDate); 
                }else{
                    $("#selected-date").html(data.data.DateSelection.displayStartDate+ ' - '+ data.data.DateSelection.displayEndDate); 
                }

                $("#start-weekly-date").val(data.data.DateSelection.WeeklyStartDate);
                $("#end-weekly-date").val(data.data.DateSelection.WeeklyEndDate);
                $("#start-month-date").val(data.data.DateSelection.MonthStartDate);
                $("#end-month-date").val(data.data.DateSelection.MonthEndDate);
               
            }
        })
    }
</script>
@stop