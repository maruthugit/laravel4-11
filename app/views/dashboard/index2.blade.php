@extends('layouts.master')

@section('title') Dashboard @stop

@section('content')

<style>
    
    .stat-box{
        border: solid 1px #f5f5f5;
        background-color: #ffffff;
        height: 130px;
        padding: 10px;
        border-left: solid 4px #59c3a8;
    }
    
    .stat-total{
        font-size: 30px;
        font-weight: 100;
        padding: 10px 0px 10px 0px;
        color: #59c37b;
    }
    
    .stat-title{
        font-weight: 400;
    }
    
    .d-panel{
        border-radius: 0px !important;
    }
    
    .d-panel-heading{
        border-radius: 0px !important;
        border:solid 0px;
        /*background-color: #fff !important;*/
         background-color:  #f5f5f5 !important;
       
        min-height: 50px;
    }
    
    .d-panel-title{
        text-transform: uppercase;
        font-size: 15px;
        font-weight: 400;
    }
    
    .d-year-title{
        font-size: 30px;
    font-weight: 100;
    /* text-align: right; */
    padding-left: 25px;
}
.chart-stat{
    height: 100px;
    border-right:solid 1px #ddd;
    margin-top: 10px;
}

.chart-stat-last{
    height: 100px;
     margin-top: 10px;
}

/* CRICLE */

/* CIRCLE */
    
</style>

<div id="page-wrapper" style="background-color: rgb(241, 241, 241);">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif
    <!-- Success-Messages -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ $message }} Your <b>last login</b> was <b>{{ $last_login }}</b> .
        </div>
    @endif

    <div class="row">       
        <div class="col-lg-12">
            <h1 class="page-header"><i class="fa fa-home fa-fw"></i> Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

<?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'kaijie'), true ) ) {  ?>
<div class="row">
    <div class="col-md-12 col-xs-12">
    <div class="panel panel-default d-panel" style="margin-top:20px;">
            <div class="panel-heading d-panel-heading" style="height:40px;">
                <div class="pull-left d-panel-title">
    <!--                <i class="fa fa-bullseye" aria-hidden="true"></i> Sales -->
                </div>
                <div class="btn-group pull-right" role="group" aria-label="..." >
                    <button type="button" class="btn btn-default btn-sm" id="nav-left"><i class="fa fa-arrow-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm disabled "><i class="fa fa-calendar-o"></i> <span id="selected-date">2017</span></button>
                    <button type="button" class="btn btn-default btn-sm" id="nav-right"><i class="fa fa-arrow-right"></i></button>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
                        <button type="button" class="btn btn-default active btn-cumulative" cum-type="1">Cumulative</button> 
                        <button type="button" class="btn btn-default btn-cumulative" cum-type="2">Not-Cumulative</button> 
                    </div>
                    <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="selected-date-range">Year - Monthly</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                        <input type="hidden" id="start-date-select" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="to-date-select" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="type-select" value="<?php echo 1;?>">

                        <input type="hidden" id="start-today" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="start-weekly-date" value="">
                        <input type="hidden" id="end-weekly-date" value="">
                        <input type="hidden" id="start-month-date" value="">
                        <input type="hidden" id="end-month-date" value="">
                        <input type="hidden" id="cumulative" value="1">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="date-range" data-type="3"> Yearly </a></li>
                        <li><a class="date-range" data-type="1"> Year - Monthly </a></li>
                        <li><a class="date-range" data-type="2"> Month - Weekly</a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body" style="padding: 0px;padding-top: 10px;">
                <div class="col-md-12 d-year-title" style=""><?php echo date("Y")?></div>
                <div class="col-md-12" style="padding-left:25px;">
                    <table>
                        <tr>
                            <td><div style=" width: 10px;height: 10px;background-color: #428bca;margin-right:10px;"></div></td>
                            <td>GMV Sales Amount</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td><div style=" width: 10px;height: 10px;background-color: #1CAF9A;margin-right:10px;"></div></td>-->
                        <!--    <td>GST Amount</td>-->
                        <!--</tr>-->
                    </table>
                </div>
                <div class="col-md-12" >
                    <div id="chart1" style="height: 350px;"></div>
                </div>
<!--                <div class="col-md-12" >
                    <div class="row">
                        <div class="col-md-3 chart-stat" style="height:220px;" >
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                            <div id="donut-example" style="max-height: 170px;"></div>
                                <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat-last">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                    </div>
                </div>-->
            </div>
    </div>
    </div>
</div>
<div class="row" style="margin-bottom:20px;">
        <div class="col-md-6">
            <div class="stat-box">
            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
            <div class="stat-total" style="font-weight:bolder;">{{Config::get("constants.CURRENCY")}} {{ $total_value }}</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        
        <div class="col-md-6 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-bars"></i>  Total Transaction</div>
            <div class="row" style="height: 45px">
                    <div class="col-xs-6">
                        <div class="stat-title">Completed</div>
                        <div style="color: #59c37b;">{{ number_format($total_completed_transaction) }}</div>
                    </div>
                    <div class="col-xs-6">
                        <div class="stat-title">Pending</div>
                        <div style="color: #59c37b;">{{ number_format($total_pending_transaction) }}</div>
                    </div>
                </div>
                <div class="row" style="height: 45px">
                    <div class="col-xs-6">
                        <div class="stat-title">Cancelled</div>
                        <div style="color: #59c37b;">{{ number_format($total_cancelled_transaction) }}</div>
                    </div>
                    <div class="col-xs-6">
                        <div class="stat-title">Refund</div>
                        <div style="color: #59c37b;">{{ number_format($total_refund_transaction) }}</div>
                    </div>
                </div>
            
            </div>
        </div>
        
 
 </div>
 
<div class="row" style="margin-bottom:20px;">
    <div class="col-md-12"  style="background-color:#fffff;">
         <div class="panel panel-default d-panel" style="margin-top:20px;">
            <div class="panel-body" style="padding: 0px;padding-top: 10px;">
                <div class="col-md-12" style="padding-left:25px;">
                    <table>
                        <tr>
                            <td><div style=" width: 10px;height: 10px;background-color: #428bca;margin-right:10px;"></div></td>
                            <td>GMV Sales Amount</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td><div style=" width: 10px;height: 10px;background-color: #1CAF9A;margin-right:10px;"></div></td>-->
                        <!--    <td>GST Amount</td>-->
                        <!--</tr>-->
                    </table>
                </div>
                <div class="col-md-12" >
                    <div id="chart2" style="height: 350px;"></div>
                </div>
<!--                <div class="col-md-12" >
                    <div class="row">
                        <div class="col-md-3 chart-stat" style="height:220px;" >
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                            <div id="donut-example" style="max-height: 170px;"></div>
                                <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                        <div class="col-md-3 chart-stat-last">
                            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
                                <div class="stat-total">RM 4,293,293.10</div>
                        </div>
                    </div>
                </div>-->
            </div>
    </div>
    </div>
</div>
<!-- Panel 2 -->

<?php if (in_array(Session::get('username'), array('maruthu', 'kaijie'), true ) ) {  ?>
<!-- Logistic Driver -->
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-default d-panel" style="margin-bottom: 0">
           <div class="panel-heading">Driver Statistic</div>
            
            <div class="panel-body" style="padding: 10px;">
                <div class="btn-group pull-right" role="group" aria-label="..." >
                    <button type="button" class="btn btn-default" id="nav-left-driver"><i class="fa fa-arrow-left"></i></button>
                    <button type="button" class="btn btn-default disabled"><i class="fa fa-calendar-o"></i> <span id="selected-date-driver"> <?php echo date("d M Y")?></span></button>
                    <button type="button" class="btn btn-default" id="nav-right-driver"><i class="fa fa-arrow-right"></i></button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="selected-date-range-driver">Weekly</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                        <input type="hidden" id="start-date-select-driver" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="to-date-select-driver" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="type-select-driver" value="<?php echo 2;?>">
                        
                        <input type="hidden" id="start-today-driver" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="start-weekly-date-driver" value="">
                        <input type="hidden" id="end-weekly-date-driver" value="">
                        <input type="hidden" id="start-month-date-driver" value="">
                        <input type="hidden" id="end-month-date-driver" value="">
                    </button>
                    <ul class="dropdown-menu">
                        <li ><a class="date-range-driver" data-type-driver="1"> Daily </a></li>
                        <li ><a class="date-range-driver" data-type-driver="2"> Weekly </a></li>
                        <li ><a class="date-range-driver" data-type-driver="3"> Monthly </a></li>
                    </ul>
                </div>
                <table id="logistic_driver" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Delivered</th>
                            <th>Returned</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                </table>
                <div id="driverChart" style=""></div>
            </div>
        </div>
    </div>
</div>
<!-- Logistic Driver -->

 <?php }} else{ ?>
    <div class="row text-center">        
        <div class="col-lg-12 col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row text-center">
                       <div><h2>Welcome to your dashboard, {{ Session::get('username') }} !</h2></div>    
                    </div>
                </div>
            </div>
        </div>
    </div>

 <?php } ?>
</div>


@stop
@section('inputjs')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script src="{{asset('js/horizontal-chart.js')}}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
$(document).ready(function(){
    
    $("#nav-left").click(function(){
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        var cumumulative_type = $('#cumulative').val();
        loadChart(1,type,startDate,toDate,cumumulative_type);
    });
    
    $("#nav-right").click(function(){
  
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        var cumumulative_type = $('#cumulative').val();
        loadChart(2,type,startDate,toDate,cumumulative_type);
    });
    
    $(".btn-cumulative").click(function(){
        $(".btn-cumulative").removeClass('active');
        $(this).addClass('active');
        var cumumulative_type = $(this).attr('cum-type');
        $("#cumulative").val(cumumulative_type);
        
        var cumumulative_type = $('#cumulative').val();
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        var cumumulative_type = $('#cumulative').val();
        loadChart(0,type,startDate,toDate,cumumulative_type);
        
    });
    
    
    
    
    // Change period type 
    $(".date-range").click(function(){
        $("#selected-date-range").html($(this).html());
        $('#type-select').val($(this).attr("data-type"))
        
        if($('#type-select').val() == 1){
            var type = $('#type-select').val();
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            var cumumulative_type = $('#cumulative').val();
            loadChart(0,type,startDate,toDate,cumumulative_type);
        }
        
        if($('#type-select').val() == 2){
            var type = $('#type-select').val();
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            var cumumulative_type = $('#cumulative').val();
            loadChart(0,type,startDate,toDate,cumumulative_type);
        }
        
        if($('#type-select').val() == 3){
            
            var type = $('#type-select').val();
            var startDate = $('#start-month-date').val();
            var toDate = $('#end-month-date').val();
            var cumumulative_type = $('#cumulative').val();
            loadChart(0,type,startDate,toDate,cumumulative_type);
            
        }
    });
    // Change period type 
    var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    var cumumulative_type = $('#cumulative').val();
    loadChart(0,1,startDate,toDate,cumumulative_type);
    loadDriverTable(0,2,'',''); // default weekly


    // Change period type driver
    $(".date-range-driver").click(function(){

        $("#selected-date-range-driver").html($(this).html());
        $('#type-select-driver').val($(this).attr("data-type-driver"));
        
        if($('#type-select-driver').val() == 1){
            var type = $('#type-select-driver').val();
            var startDate = $('#start-today-driver').val();
            var toDate = $('#start-today-driver').val();
            loadDriverTable(0,type,startDate,toDate);
        }
        
        if($('#type-select-driver').val() == 2){
            var type = $('#type-select-driver').val();
            var startDate = $('#start-weekly-date-driver').val();
            var toDate = $('#end-weekly-date-driver').val();
            loadDriverTable(0,type,startDate,toDate);
        }
        
        if($('#type-select-driver').val() == 3){
            
            var type = $('#type-select-driver').val();
            var startDate = $('#start-month-date-driver').val();
            var toDate = $('#end-month-date-driver').val();
            loadDriverTable(0,type,startDate,toDate);
        }
    });

    $("#nav-left-driver").click(function(){
        var type = $('#type-select-driver').val();
        var startDate = $('#start-date-select-driver').val();
        var toDate = $('#to-date-select-driver').val();
        loadDriverTable(1,type,startDate,toDate);
    });
    
    $("#nav-right-driver").click(function(){
        var type = $('#type-select-driver').val();
        var startDate = $('#start-date-select-driver').val();
        var toDate = $('#to-date-select-driver').val();
        loadDriverTable(2,type,startDate,toDate);
    });
    
    function parseSVG(s) {
        var div= document.createElementNS('http://www.w3.org/1999/xhtml', 'div');
        div.innerHTML= '<svg xmlns="http://www.w3.org/2000/svg">'+s+'</svg>';
        var frag= document.createDocumentFragment();
        while (div.firstChild.firstChild)
            frag.appendChild(div.firstChild.firstChild);
        return frag;
    }
    
    function loadChart(navigation,type,startDate,toDate,cumumulative_type){
        
        $.ajax({
                method: "POST",
                url: "/home/dashboarddata",
                data: {
                    'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate,
                    'cumumulative_type':cumumulative_type
                },
                beforeSend: function(){
                $('.loading').show();
                },
                success: function(data) {
                    
                    var barChartCollection =  [];
                    var barChart = data.data.BarChartData;
                    var chartTitle = data.data.DateSelection.displayChartTitle;
                    $.each(barChart, function (index,value) {
                        barChartCollection.push({ y: value.x_description, a: value.total_amount2 + value.gst_amount2, b: value.gst_amount2 });
                    });
                    
                    generateCumulativeBarchart(barChartCollection);
                    
                    $(".d-year-title").html(chartTitle);
                    $("#start-date-select").val(data.data.DateSelection.startDate);
                    $("#to-date-select").val(data.data.DateSelection.toDate);
                    $("#selected-date").html(data.data.DateSelection.displayStartDate+' - '+data.data.DateSelection.displayEndDate);
                    
                    
                    
                    
            }
        })
        
    }
    
    loadChart2('',3,'2015-01-01','2018-12-31',1);
    
    function loadChart2(navigation,type,startDate,toDate,cumumulative_type){
        
        $.ajax({
                method: "POST",
                url: "/home/dashboarddata",
                data: {
                    'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate,
                    'cumumulative_type':cumumulative_type
                },
                beforeSend: function(){
                $('.loading').show();
                },
                success: function(data) {
                    
                    var barChartCollection =  [];
                    var barChart = data.data.BarChartData;
                    var chartTitle = data.data.DateSelection.displayChartTitle;
                    $.each(barChart, function (index,value) {
                        barChartCollection.push({ y: value.x_description, a: value.total_amount2 + value.gst_amount2, b: value.gst_amount2 });
                    });
                    
                    generateCumulativeBarchart2(barChartCollection);
                    
                    $(".d-year-title").html(chartTitle);
                    $("#start-date-select").val(data.data.DateSelection.startDate);
                    $("#to-date-select").val(data.data.DateSelection.toDate);
                    $("#selected-date").html(data.data.DateSelection.displayStartDate+' - '+data.data.DateSelection.displayEndDate);
                    
                    
                    
                    
            }
        })
        
    }
    
    
    
    function generateCumulativeBarchart(barChartCollection){
        
    $("#chart1").html('')
    console.log(barChartCollection);
        Morris.Bar({
            element: 'chart1',
            barGap:2,
            barSizeRatio:0.40,
            barColors: ['#428bca'],
            data:barChartCollection,
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Total Sales']
        });
                    
        var items = $("#chart1").find( "svg" ).find("rect");
        console.log(items.length);
        var item_cell = 2;
        var counter = 1;
        var pos = 0;
         console.log(items);
        $.each(items,function(index,v){
            console.log(index);
            console.log(pos);
//             if(item_cell == counter){
//                 var value = barChartCollection[pos].b
                            
// //                            var value = (barChartCollection[pos].b).toFixed(2).replace(/./g, function(c, i, a) {
// //                            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
// //                            });
//                 counter = 1;
//                 pos++;
//             }else{
                
//                 var value = barChartCollection[pos].a;
                
//             //(barChartCollection[pos].a).toFixed(2).replace(/./g, function(c, i, a) {
//               //              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
//                 //            });
//                 counter++;
//             }
            
            var value = barChartCollection[pos].a;
            console.log(value);
            
              pos++;
//            
            if(value <= 0){
                value = "";
            }else{
                value = "RM "+ Number(value).toLocaleString('en');
            }
            
            var newY = parseFloat( $(this).attr('y') - 20 );
            var halfWidth = parseFloat( $(this).attr('width') / 2 );
            var newX = parseFloat( $(this).attr('x') ) +  halfWidth;
            var newX2 = parseFloat( $(this).attr('x') ) +  halfWidth + 20;
            var output = '<text style="text-anchor: middle; font: 12px sans-serif;" x="'+newX+'" y="'+newY+'" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#ef6c6c" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan dy="3.75">'+value+'</tspan></text>';
            $("#chart1").find( "svg" ).append(parseSVG(output));

        });
    }
    
    function generateCumulativeBarchart2(barChartCollection){
        
    $("#chart2").html('')
    console.log(barChartCollection);
        Morris.Bar({
            element: 'chart2',
            barGap:2,
            barSizeRatio:0.40,
            barColors: ['#428bca'],
            data:barChartCollection,
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Total Sales']
        });
                    
        var items = $("#chart2").find( "svg" ).find("rect");
        console.log(items.length);
        var item_cell = 2;
        var counter = 1;
        var pos = 0;
         console.log(items);
        $.each(items,function(index,v){
            console.log(index);
            console.log(pos);
//             if(item_cell == counter){
//                 var value = barChartCollection[pos].b
                            
// //                            var value = (barChartCollection[pos].b).toFixed(2).replace(/./g, function(c, i, a) {
// //                            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
// //                            });
//                 counter = 1;
//                 pos++;
//             }else{
                
//                 var value = barChartCollection[pos].a;
                
//             //(barChartCollection[pos].a).toFixed(2).replace(/./g, function(c, i, a) {
//               //              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
//                 //            });
//                 counter++;
//             }
            
            var value = barChartCollection[pos].a;
            console.log(value);
            
              pos++;
//            
            if(value <= 0){
                value = "";
            }else{
                value = "RM "+ Number(value).toLocaleString('en');
            }
            
            var newY = parseFloat( $(this).attr('y') - 20 );
            var halfWidth = parseFloat( $(this).attr('width') / 2 );
            var newX = parseFloat( $(this).attr('x') ) +  halfWidth;
            var newX2 = parseFloat( $(this).attr('x') ) +  halfWidth + 20;
            var output = '<text style="text-anchor: middle; font: 12px sans-serif;" x="'+newX+'" y="'+newY+'" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#ef6c6c" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan dy="3.75">'+value+'</tspan></text>';
            $("#chart2").find( "svg" ).append(parseSVG(output));

        });
    }
    
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
            
            var data = google.visualization.arrayToDataTable([
              ['Task', 'Hours per Day'],
              ["Pending (<?php echo $summaryLogisticStatus['TotalPendingAll'] ?>)",    <?php echo $summaryLogisticStatus['TotalPendingAll'] ?>],
              ["Sending (<?php echo $summaryLogisticStatus['TotalSendingAll'] ?>)",      <?php echo $summaryLogisticStatus['TotalSendingAll'] ?>],
              ["Returned (<?php echo $summaryLogisticStatus['TotalReturnedAll'] ?>)",  <?php echo $summaryLogisticStatus['TotalReturnedAll'] ?>],
              ["Undelivered (<?php echo $summaryLogisticStatus['TotalUndeliveredAll'] ?>)", <?php echo $summaryLogisticStatus['TotalUndeliveredAll'] ?>],
            //  ['Sent (<?php echo $summaryLogisticStatus['TotalSentAll'] ?>)",    <?php //echo $summaryLogisticStatus['TotalSentAll'] ?>],
              ["Partial Sent (<?php echo $summaryLogisticStatus['TotalPartialAll'] ?>)",    <?php echo $summaryLogisticStatus['TotalPartialAll'] ?>]
            ]);

            var options = {
//              title: 'Logistic Status',
              pieHole: 0.3,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
            chart.draw(data, options);
        
        
            var data = google.visualization.arrayToDataTable([
              ['Task', 'Hours per Day'],
            <?php foreach ($totalProductQty as $keyP => $valueP) { ?>
    
                ["<?php echo $valueP->name; ?>",    <?php echo $valueP->total_qty;  ?>],
                        
            <?php } ?>
            ]);

            var options = {
//              title: 'Top 5 Pending Product',
              pieHole: 0.3,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single2'));
            chart.draw(data, options);
//            var chart2 = new google.visualization.PieChart(document.getElementById('donut_single2'));
//            chart2.draw(data, options);
      }

    function loadDriverTable(navigation,type,startDate,toDate){console.log(type)
        $.ajax({
                method: "POST",
                url: "/home/dashboarddriver",
                data: {
                    'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate
                },
                beforeSend: function(){
                $('.loading').show();
                },
                success: function(data) {
                    $("#driverChart").html('')
                    $("tbody").remove();
                    var table = document.getElementById('logistic_driver');

                    for (const val of data.driver) {
                        console.log(val.total);
                        var tbody = document.createElement('tbody');
                        var tr = document.createElement('tr');
                        var tdname = document.createElement('td');
                        var name = document.createTextNode(val.name);
                        var tdsent = document.createElement('td');
                        var sent = document.createTextNode(val.sent);
                        var tdreturn = document.createElement('td');
                        var returned = document.createTextNode(val.return);
                        var tdtotal = document.createElement('td');
                        var total = document.createTextNode(val.total);

                        tdname.appendChild(name);
                        tdsent.appendChild(sent);
                        tdreturn.appendChild(returned);
                        tdtotal.appendChild(total);
                        tr.appendChild(tdname);
                        tr.appendChild(tdsent);
                        tr.appendChild(tdreturn);
                        tr.appendChild(tdtotal);
                        tbody.appendChild(tr);
                        table.appendChild(tbody);
                    }

                    if($('#type-select-driver').val() == 1){
                       $("#selected-date-driver").html(data.start_date_display); 
                    }else{
                        $("#selected-date-driver").html(data.start_date_display+ ' - '+ data.end_date_display); 
                    }

                    $("#start-date-select-driver").val(data.start_date);
                    $("#to-date-select-driver").val(data.end_date);
                    $("#start-weekly-date-driver").val(data.weekly_start_date);
                    $("#end-weekly-date-driver").val(data.weekly_end_date);
                    $("#start-month-date-driver").val(data.monthly_start_date);
                    $("#end-month-date-driver").val(data.monthly_end_date);
                    
                    var batch = [];
                    $.each(data.driver, function( key, value ) {
                        batch.push({  y: value.name, a: value.sent_percent, b: value.return_percent, c: value.total_percent  });
                    });

                    Morris.Bar({
                      element: 'driverChart',
                      data: batch,
                      barGap: 4,
                      barColors: ['#428bca','#1CAF9A','#D9534F'],
                      xkey: 'y',
                      ykeys: ['a', 'b', 'c'],
                      labels: ['Delivered Percent','Returned Percent','Total Percent'],
                      horizontal: true,
                    });
            }
        })
        
    }

      });
</script>
@stop