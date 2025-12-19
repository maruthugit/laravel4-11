@extends('layouts.master')

@section('title') Dashboard @stop

@section('content')
<style>
    
    .stat-box{
        border: solid 1px #dedede;
        background-color: #ffffff;
        height: 130px;
        padding: 10px;
        /* border-left: solid 4px #59c3a8; */
    }
    
    .stat-total{
        font-size: 30px;
        font-weight: 100;
        padding: 10px 0px 10px 0px;t
        color: #59c37b;
    }
    
    .stat-title
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

.hidefirst{
    /*display:none;*/
}

.badge-warning {
  background-color: #f89406;
}
   

/* CRICLE */

/* CIRCLE */

/* STAT COMPARE */

.left-pan-navi{

	width:5%;
 	cursor: pointer;

}

.mid-pan-navi{

	width:90%;
	text-align: center;
	
}

.right-pan-navi{

	width:5%;
	text-align: right;
	cursor: pointer;
	
}

.top-nav-stat{
	position: relative;
	display: inline-block;
	vertical-align: middle;
	width: 100%;
}

.stat-com-box{
	padding-top:25px;
	padding-bottom:25px;
}

.left-con-stat{
	text-align: right ;padding: 20px;font-weight: bolder;font-size: 15px;
}

.desc-left{
	font-size:15px;
	color: #777777;
}

.desc-right{
	text-align: left;padding: 20px;font-weight: bolder;    font-size: 30px;border-left:solid 1px #ddd;
}

.chart-stat-box{
	display: block; width: 500px; height: 250px;
}


/* STAT COMPARE */
    
</style>

<div id="page-wrapper" style="background-color: rgb(241, 241, 241);padding-top: 20px;" >
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif
    <!-- Success-Messages -->
    @if ($message = Session::get('success'))
        <!--<div class="alert alert-success alert-dismissable">-->
        <!--    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
        <!--    {{ $message }} Your <b>last login</b> was <b>{{ $last_login }}</b> .-->
        <!--</div>-->
    @endif

    <!--<div class="row">       -->
    <!--    <div class="col-lg-12">-->
    <!--        <h1 class="page-header"><i class="fa fa-home fa-fw"></i> Dashboard</h1>-->
    <!--    </div>-->
        <!-- /.col-lg-12 -->
    <!--</div>-->

<?php if (in_array(Session::get('username'), array('joshua', 'joshua01'), true ) ) {  ?>

<div class="row" style="margin-bottom:0px;">
    
        <div class="col-md-3">
            <div class="stat-box">
            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
            <div class="stat-total">{{Config::get("constants.CURRENCY")}} 65,233,284.72</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-users"></i> Customer Total Database</div>
            <div class="stat-total"><?php echo '3,076,987'; ?></div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-users"></i> Total Customer</div>
            <div class="stat-total"><?php echo '31,456'; ?></div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-cubes"></i> Total Product</div>
            <div class="stat-total">30,283</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        
 
 </div>
<?php } ?>

<?php if (in_array(Session::get('username'), array('agnes', 'maruthu', 'deddie','boobalan'), true ) ) {  ?>

<div class="row" style="margin-bottom:0px;">
    
        <div class="col-md-3">
            <div class="stat-box">
            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
            <div class="stat-total">{{Config::get("constants.CURRENCY")}} {{ $total_value }}</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-users"></i> Total Customer</div>
            <div class="stat-total"><?php echo number_format($total_cust); ?></div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box">
            <div class="stat-title"><i class="fa fa-cubes"></i> Total Product</div>
            <div class="stat-total">{{ number_format($total_products) }}</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
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
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
 
 </div>
<?php } ?>

<?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'deddie', 'maruthu', 'adminjohor', 'william', 'kean','quenny','ryanloh','joshua01','gerald','boobalan'), true ) ) {  ?>
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
                        <span id="selected-date-range">Year</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
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
                        <li><a class="date-range" data-type="1"> Year </a></li>
                        <li><a class="date-range" data-type="2"> Month </a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body" style="padding: 0px;padding-top: 10px;">
                <div class="col-md-12 d-year-title" style=""><?php echo date("Y")?></div>
                <div class="col-md-12" style="padding-left:25px;">
                    <table>
                        <tr>
                            <td><div style=" width: 10px;height: 10px;background-color: #428bca;margin-right:10px;"></div></td>
                            <td>Sales Amount</td>
                            <td class="hidefirst"><div style=" width: 10px;height: 10px;background-color: #9c27b0;margin-left:10px;margin-right:10px;"></div></td>
                            <td class="hidefirst">GMV Sales Amount</td>
                        </tr>
                        <tr>
                            <td><div style=" width: 10px;height: 10px;background-color: #1CAF9A;margin-right:10px;"></div></td>
                            <td>GST Amount</td>
                            <td class="hidefirst"><div style=" width: 10px;height: 10px;background-color: #ffc107;margin-left:10px;margin-right:10px;"></div></td>
                            <td class="hidefirst">GMV GST Amount</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12" >
                    <div id="chart1" style="height: 350px;"></div>
                </div>
            </div>
    </div>
    </div>
</div>

<?php if (in_array(Session::get('username'), array('agnes', 'maruthu', 'wira', 'owen', 'annix','tammy','ira','boobalan'), true ) ) {  ?>
<div class="row" style="margin-bottom:20px;">
<div class="col-md-12" >
        <div class="col-md-12" style=" margin-bottom: 15px;background-color: #fff;padding-top: 20px;box-shadow: 0 8px 6px -6px #afafaf;padding-bottom: 20px;">
            <h4 style="margin-bottom: 0"></h4>
            <div id="demoprogressbar5" style="background-color: rgb(255, 255, 255);">
                <div style="margin-left:calc(<?php echo $percentageAchieved; ?>% - 25px) ;">
                    <img src="/images/asset/icon/pin.png" style="width:50px;"> 
                </div>
                <div class="progressbar" style="width: 100%; background-color: rgb(222, 222, 222); border-radius: 10px;box-shadow: 0 8px 6px -6px #afafaf;">
                    <div class="proggress" style="background-color: rgb(26, 188, 156); height: 20px;border-radius: 10px;width: <?php echo $percentageAchieved; ?>%;text-align: center;font-weight: bolder;color: #fff;">
                        <span style=" color: #ffffff;
    min-width: 100px !important;
    position: absolute;
    text-align: left;">MYR <?php echo number_format($CurrentSalesAmount,2,".",","); ?></span></div>
                    <div class="percentCount"><?php echo date("Y"); ?> (MYR <?php echo number_format($NextTargetAmount->amount,2,".",","); ?>)</div>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <div class="row" style="margin-bottom:20px;">
    <div class="col-md-12" >
        <div class="col-md-12" style=" margin-bottom: 15px;background-color: #fff;padding-top: 20px;box-shadow: 0 8px 6px -6px #afafaf;padding-bottom: 20px;">
            <h4 style="margin-bottom: 0"></h4>
            <div id="demoprogressbar5" style="background-color: rgb(255, 255, 255);">
                <div style="margin-left:calc(<?php echo $percentageAchieved2019; ?>% - 25px) ;">
                    <img src="/images/asset/icon/pin.png" style="width:50px;"> 
                </div>
                <div class="progressbar" style="width: 100%; background-color: rgb(222, 222, 222); border-radius: 10px;box-shadow: 0 8px 6px -6px #afafaf;">
                    <div class="proggress" style="background-color: rgb(26, 188, 156); height: 20px;border-radius: 10px;width: <?php echo $percentageAchieved2019; ?>%;text-align: center;font-weight: bolder;color: #fff;">
                        <span style=" color: #ffffff;
    min-width: 100px !important;
    position: absolute;
    text-align: left;">MYR <?php echo number_format($CurrentSalesAmount2019,2,".",","); ?></span></div>
                    <div class="percentCount"><?php echo '2024'; ?> (MYR <?php echo number_format(10000000.00,2,".",","); ?>)</div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php } ?>
    <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','joshua01', 'deddie', 'kean','quenny','gerald','ryanloh','boobalan'), true ) ) {  ?>
    <div class="row">
       
            <div class="col-md-4 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="ui segment" id="box-1"> <div class="ui active inverted dimmer"><div class="ui text loader large"></div></div></div>
                        <div class="top-nav-stat" style="">
                            <div class="pull-left left-pan-navi" id="day-left-pan-navi" day-sel-date= "" day-nav-type="left-1" style=""><i class="fa fa-caret-left "></i></div>
                            <div class="pull-left mid-pan-navi" id="day-selected-date-display"  day-sel-date="<?php echo date("Y-m-d"); ?>" ><?php echo date("d, F Y"); ?></div>
                            <div class="pull-right right-pan-navi" id="day-right-pan-navi" day-sel-date= "" day-nav-type="right-1" style=""><i class="fa fa-caret-right "></i></div>
                        </div>
                        <div class="stat-com-box row" style="">
                            <div class="col-md-5 col-xs-5 left-con-stat" style=" ">
                                <div id="day-previous-amount"> 0.00 </div>
                                <div class="desc-left" style=""><small><i class="fa fa-bullseye"></i> <span id="day-previous-date"><?php echo date("d, F Y",strtotime("-1 year")); ?></span></small></div>
                            </div>
                            <div class="col-md-7 col-xs-7 desc-right" id="day-current-amount"> 0.00 </div>
                        </div>
                        <div id="canvas-1">
                            <canvas width="500" height="250" id="myChart" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                        <hr>
                        <div id="canvas-1-status">
                        <canvas width="500" height="250" id="myChartStatus" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="ui segment" id="box-2"> <div class="ui active inverted dimmer"><div class="ui text loader large"></div></div></div>
                        <div class="top-nav-stat" style="">
                            <div class="pull-left left-pan-navi" id="month-left-pan-navi" month-sel-date= "" month-nav-type="left-1" style=""><i class="fa fa-caret-left "></i></div>
                            <div class="pull-left mid-pan-navi" id="month-selected-date-display"  month-sel-date="<?php echo date("Y-m-d"); ?>" ><?php echo date("d, F Y"); ?></div>
                            <div class="pull-right right-pan-navi" id="month-right-pan-navi" month-sel-date= "" month-nav-type="right-1" style=""><i class="fa fa-caret-right "></i></div>
                        </div>
                        <div class="stat-com-box row" style="">
                            <div class="col-md-5 col-xs-5 left-con-stat" style=" ">
                                <div id="month-previous-amount"> 0.00 </div>
                                <div class="desc-left" style=""><small><i class="fa fa-bullseye"></i> <span id="month-previous-date"><?php echo date("F Y",strtotime("-1 year")); ?></span></small></div>
                            </div>
                            <div class="col-md-7 col-xs-7 desc-right" id="month-current-amount"> 0.00 </div>
                        </div>
                        <div id="canvas-2">
                            <canvas width="500" height="250" id="myChart2" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                        <hr>
                        <div id="canvas-2-status">
                            <canvas width="500" height="250" id="myChart2Status" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="ui segment" id="box-3"> <div class="ui active inverted dimmer"><div class="ui text loader large"></div></div></div>
                        <div class="top-nav-stat" style="">
                            <div class="pull-left left-pan-navi" id="year-left-pan-navi" year-sel-date= "" year-nav-type="left-1" style=""><i class="fa fa-caret-left "></i></div>
                            <div class="pull-left mid-pan-navi" id="year-selected-date-display"  year-sel-date="<?php echo date("Y-m-d"); ?>" ><?php echo date("Y"); ?></div>
                            <div class="pull-right right-pan-navi" id="year-right-pan-navi" year-sel-date= "" year-nav-type="right-1" style=""><i class="fa fa-caret-right "></i></div>
                        </div>
                        <div class="stat-com-box row" style="">
                            <div class="col-md-5 col-xs-5 left-con-stat" style=" ">
                                <div id="year-previous-amount"> 0.00 </div>
                                <div class="desc-left" style=""><small><i class="fa fa-bullseye"></i> <span id="year-previous-date"><?php echo date("Y",strtotime("-1 year")); ?></span></small></div>
                            </div>
                            <div class="col-md-7 col-xs-7 desc-right" id="year-current-amount"> 0.00 </div>
                        </div>
                        <div id="canvas-3">
                            <canvas width="500" height="250" id="myChart3" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                        <hr>
                        <div id="canvas-3-status">
                            <canvas width="500" height="250" id="myChartStatus3" class="chartjs-render-monitor" style=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>
    

 <?php }  ?>
<!-- Panel 2 -->
<?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu', 'deddie', 'kean','quenny','joshua01','gerald','ryanloh','boobalan'), true ) ) {  ?>
<div class="row">
    <div class="col-md-5">
        <div class="panel panel-default d-panel">
            <div class="panel-heading d-panel-heading">Logistic Status Summary</div>
            <div class="panel-body">
                <div class="col-md-12" id="">
                    <div id="donut_single" style="width:100%;height:100%;"></div>
                </div>
            </div>
        </div>
        <div class="panel panel-default d-panel">
            <div class="panel-heading d-panel-heading">Top 10 Pending Product</div>
            <div class="panel-body">
                <div class="col-md-12" id="">
                    <div id="donut_single2" style="width:100%;height:100%;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="panel panel-default d-panel">
            <div class="panel-heading d-panel-heading">Latest Transactions</div>
            <div class="panel-body">
                <table class="table table-striped"> 
                    <thead> 
                        <tr> 
                            <th>Date</th>
                            <th>Transaction ID</th> 
                            <th>Total Amount (RM)</th> 
                            <th>Buyer</th> 
                            <th>City</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php foreach ($latestTransaction as $key => $value) { ?>
                            
                        
                        <tr> 
                            <th scope="row"><?php echo date_format(date_create($value->transaction_date),"d/m/Y H:i A");?></th> 
                            <td><?php echo $value->id;?></td> 
                            <td><?php echo number_format($value->total_amount, 2, '.', ''); ?></td> 
                            <td><?php echo $value->buyer_username;?></td> 
                            <td><?php echo $value->delivery_city;?></td> 
                        </tr> 
                        
                        <?php  }?>
                       
                    
                    </tbody> 
                </table>
            </div>
            <div class="panel-footer">
                <a href="/transaction" class="btn btn-default">view all</a>
            </div>
        </div>
    </div>
   
</div>
 <?php } } else{ ?>
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
        <?php if (in_array(Session::get('username'), array('ira','asif','wendy','nuratiqah','toby','maruthu','grace','jamilah','kaijie','boobalan'), true ) ) {  ?>
        <div class="col-lg-12 col-md-8">
            <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default d-panel">
                    <div class="panel-heading d-panel-heading">Logistic Status Summary</div>
                    <div class="panel-body">
                        <div class="col-md-12" id="">
                            <div id="donut_single" style="width:100%;height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default d-panel">
                    <div class="panel-heading d-panel-heading">Top 5 Pending Product</div>
                    <div class="panel-body">
                        <div class="col-md-12" id="">
                            <div id="donut_single2" style="width:100%;height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php }  ?>
    </div>

 <?php } ?>
</div>


@stop
@section('inputjs')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
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
                        barChartCollection.push({ y: value.x_description, a: value.total_amount, b: value.gst_amount, c: value.total_amount2, d: value.gst_amount2 });
                        // barChartCollection.push({ y: value.x_description, a: value.total_amount, b: value.gst_amount});
                    });
                    
                    generateCumulativeBarchart(barChartCollection);
                    
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
            barSizeRatio:0.90,
            barColors: ['#428bca','#1CAF9A','#9c27b0','#ffc107'],
            data:barChartCollection,
            xkey: 'y',
            // ykeys: ['a', 'b'], 
            ykeys: ['a', 'b', 'c', 'd'],
            // labels: ['Total Sales', 'Gst'] 
             labels: ['Total Sales', 'Gst','GMV Total Sales', 'GMV Gst']
        });
                    
        var items = $("#chart1").find( "svg" ).find("rect");
        console.log(items.length);
        var item_cell = 2;
        var counter = 1;
        var pos = 0;
        
        $.each(items,function(index,v){
            console.log(index);
            console.log(pos);
            if(item_cell == counter){
                // var value = barChartCollection[pos].b
                            
//                            var value = (barChartCollection[pos].b).toFixed(2).replace(/./g, function(c, i, a) {
//                            return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
//                            });
                counter = 1;
                pos++;
            }else{
                
                // var value = barChartCollection[pos].a;
                
            //(barChartCollection[pos].a).toFixed(2).replace(/./g, function(c, i, a) {
              //              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
                //            });
                counter++;
            }
//            
            // if(value <= 0){
            //     value = "";
            // }else{
            //     value = "RM "+ Number(value).toLocaleString('en');
            // }
            
            var newY = parseFloat( $(this).attr('y') - 20 );
            var halfWidth = parseFloat( $(this).attr('width') / 2 );
            var newX = parseFloat( $(this).attr('x') ) +  halfWidth;
            var newX2 = parseFloat( $(this).attr('x') ) +  halfWidth + 20;
            // var output = '<text style="text-anchor: middle; font: 12px sans-serif;" x="'+newX+'" y="'+newY+'" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#ef6c6c" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)"><tspan dy="3.75">'+value+'</tspan></text>';
            // $("#chart1").find( "svg" ).append(parseSVG(output));

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
    
                ["<?php echo htmlspecialchars($valueP->name); ?>",    <?php echo $valueP->total_qty;  ?>],
                        
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


      });
</script>
<script>
$(document).ready(function(){
    
    var myChart;
    
    $("#box-1").show();
    
    $("#day-left-pan-navi").click(function(){
        var currentDate = $(this).attr("day-sel-date");
        loadPanelDayStatistic('DAYCOM',currentDate,'NL');
    });
    
    $("#day-right-pan-navi").click(function(){
        var currentDate = $(this).attr("day-sel-date");
        loadPanelDayStatistic('DAYCOM',currentDate,'NR');
    });
    
    
    $("#month-left-pan-navi").click(function(){
        var currentDate = $(this).attr("month-sel-date");
        loadPanelMonthStatistic('MONTHCOM',currentDate,'NL');
    });
    
    $("#month-right-pan-navi").click(function(){
        var currentDate = $(this).attr("month-sel-date");
        loadPanelMonthStatistic('MONTHCOM',currentDate,'NR');
    });
    
    
    $("#year-left-pan-navi").click(function(){
        var currentDate = $(this).attr("year-sel-date");
        loadPanelYearStatistic('YEARCOM',currentDate,'NL');
    });
    
    $("#year-right-pan-navi").click(function(){
        var currentDate = $(this).attr("year-sel-date");
        loadPanelYearStatistic('YEARCOM',currentDate,'NR');
    });
    
    var ctx = document.getElementById("myChart").getContext('2d');
    var ctx2 = document.getElementById("myChart2").getContext('2d');
    var ctx3 = document.getElementById("myChart3").getContext('2d');
    
    var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
    gradientFill.addColorStop(0, "rgba(128, 182, 244, 0.6)");
    gradientFill.addColorStop(1, "rgba(244, 144, 128, 0.6)");
    
    function chartGenerate(targetChart,label,datasetsData){
        
        if (typeof myChart !== 'undefined') {
            myChart.clear();
        }
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: label,
                datasets:datasetsData 
            },
            options: {
                scales: {
                    yAxes: [{
                        gridLines: {
                            display:false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display:false
                        }
                    }]
                }
            }
        });
        
        
    }
    
    // Handle On Comparison panel //
    function loadPanelDayStatistic(typeData,currentDate,action){
        
        document.getElementById('myChart').remove();
        document.getElementById('myChartStatus').remove();
        $('#canvas-1').html('<canvas width="424" height="212" id="myChart" class="chartjs-render-monitor"></canvas>');
        $('#canvas-1-status').html('<canvas width="424" height="212" id="myChartStatus" class="chartjs-render-monitor"></canvas>');
        
        
        canvas = document.querySelector('#myChart');
        canvasStatus = document.querySelector('#myChartStatus');
        ctx = canvas.getContext('2d');
        ctxStatus = canvasStatus.getContext('2d');

        // var canvas = document.getElementById('myChart');
        // var ctx = canvas.getContext('2d');

        var typeData = typeData;
        var currentDate = currentDate;
        var action = action;
        
        $("#box-1").show();
        
        $.ajax({
            type: 'POST',
            url: '/home/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action
            },
            dataType: "json",
            success: function(resultData) { 
                
                var label = resultData.last5cycle.labelData[0];
                var datasetsData = [{
                    label: 'Current Year',
                    data: resultData.last5cycle.valueData[0],
                    backgroundColor: gradientFill,
                    borderColor: ['rgba(255,99,132,1)'],
                    borderWidth: 1,
                    animation: {easing: "easeInOutBack"}
                },
                {
                    label: 'Last Year',
                    data: resultData.last5cycle.valueData[1],
                    backgroundColor: ['rgba(26, 188, 156, 0.2)'],
                    borderColor: ['rgba(26, 188, 156, 0.5)'],
                    borderWidth: 1
                }];
                console.log(resultData.last5cycle.StatusCompare.completed.percentage);
                var datasetsDataStatus = [{
                    label: '% Percentage',
                    data: [resultData.last5cycle.StatusCompare.completed.percentage,resultData.last5cycle.StatusCompare.refund.percentage,resultData.last5cycle.StatusCompare.cancelled.percentage] ,
                    backgroundColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderWidth: 1,
                    animation: {easing: "easeInOutBack"},
                },
               ];
                    
                $("#day-current-amount").html(resultData.currentAmount);
                $("#day-previous-amount").html(resultData.previousAmount);
                $("#day-previous-date").html(resultData.previousDateDisplay);
                $("#day-selected-date-display").html(resultData.currentDateDisplay);
                $("#day-left-pan-navi").attr("day-sel-date",resultData.previousDate);
                $("#day-right-pan-navi").attr("day-sel-date",resultData.nextDate);
                $("#box-1").hide();

                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {labels: label,datasets:datasetsData },
                    options: {scales: {yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}]}
                    }
                });

                var LabelStatus = ['Completed','Refund','Cancelled'];
                console.log(datasetsDataStatus);

                myChartStatus = new Chart(ctxStatus, {
                    type: 'bar',
                    data: {labels: LabelStatus,datasets:datasetsDataStatus },
                    options: {
                        title: {
                            display: true,
                            text: '(%) Percentage Comparison'
                        },
                        legend: { display: false },
                        scales: {
                            yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}],}
                    }
                });
               
            }
        });
  
    }
    
    function loadPanelMonthStatistic(typeData,currentDate,action){
        
        document.getElementById('myChart2').remove();
        document.getElementById('myChart2Status').remove();
        $('#canvas-2').append('<canvas width="424" height="212" id="myChart2" class="chartjs-render-monitor"></canvas>');
        $('#canvas-2-status').html('<canvas width="424" height="212" id="myChart2Status" class="chartjs-render-monitor"></canvas>');

       
        canvasStatus2 = document.querySelector('#myChart2Status');
        ctxStatus2 = canvasStatus2.getContext('2d');

        canvas2 = document.querySelector('#myChart2');
        ctx2 = canvas2.getContext('2d');

        var typeData = typeData;
        var currentDate = currentDate;
        var action = action;
        
        $("#box-2").show();
        
        $.ajax({
            type: 'POST',
            url: '/home/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action
            },
            dataType: "json",
            success: function(resultData) { 
                
                var label = resultData.last5cycle.labelData[0];
                var datasetsData = [{
                    label: 'Current Year',
                    data: resultData.last5cycle.valueData[0],
                    backgroundColor: gradientFill,
                    borderColor: ['rgba(255,99,132,1)'],
                    borderWidth: 1,
                    animation: {easing: "easeInOutBack"}
                },
                {
                    label: 'Last Year',
                    data: resultData.last5cycle.valueData[1],
                    backgroundColor: ['rgba(26, 188, 156, 0.2)'],
                    borderColor: ['rgba(26, 188, 156, 0.5)'],
                    borderWidth: 1
                }];

                var datasetsDataStatus = [{
                     label: '% Percentage',
                    data: [resultData.last5cycle.StatusCompare.completed.percentage,resultData.last5cycle.StatusCompare.refund.percentage,resultData.last5cycle.StatusCompare.cancelled.percentage] ,
                    backgroundColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderWidth: 1,
                    animation: {easing: "easeInOutBack"},
                },
               ];
                    
                $("#month-current-amount").html(resultData.currentAmount);
                $("#month-previous-amount").html(resultData.previousAmount);
                $("#month-previous-date").html(resultData.previousDateDisplay);
                $("#month-selected-date-display").html(resultData.currentDateDisplay);
                $("#month-left-pan-navi").attr("month-sel-date",resultData.previousDate);
                $("#month-right-pan-navi").attr("month-sel-date",resultData.nextDate);
                $("#box-2").hide();
                
                myChart2 = new Chart(ctx2, {
                    type: 'line',
                    data: {labels: label,datasets:datasetsData },
                    options: {scales: {yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}]}
                    }
                });

                var LabelStatus2 = ['Completed','Refund','Cancelled'];
                console.log(datasetsDataStatus);

                myChart2Status = new Chart(ctxStatus2, {
                    type: 'bar',
                    data: {labels: LabelStatus2,datasets:datasetsDataStatus },
                    options: {
                        title: {
                            display: true,
                            text: '(%) Percentage Comparison'
                        },
                        legend: { display: false },
                        scales: {
                            yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}],}
                    }
                });
               
            }
        });
        
    }
    
    function loadPanelYearStatistic(typeData,currentDate,action){
        
        
        document.getElementById('myChart3').remove();
        document.getElementById('myChartStatus3').remove();

        $('#canvas-3').append('<canvas width="424" height="212" id="myChart3" class="chartjs-render-monitor"></canvas>');
        $('#canvas-3-status').append('<canvas width="424" height="212" id="myChartStatus3" class="chartjs-render-monitor"></canvas>');

        canvas = document.querySelector('#myChart3');
        ctx = canvas.getContext('2d');
        var canvas = document.getElementById('myChart3');
        var ctx = canvas.getContext('2d');

        var canvas3 = document.getElementById('myChartStatus3');
        var ctxStatus3 = canvas3.getContext('2d');


        var typeData = typeData;
        var currentDate = currentDate;
        var action = action;
        
        $("#box-3").show();
        
        $.ajax({
            type: 'POST',
            url: '/home/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action
            },
            dataType: "json",
            success: function(resultData) { 
                
                var label = resultData.last5cycle.labelData[0];
                var datasetsData = [{
                    label: 'Last 5 Years',
                    data: resultData.last5cycle.valueData[0],
                    backgroundColor: gradientFill,
                    borderColor: [
                        'rgba(255,99,132,1)'
                    ],
                    borderWidth: 1
                }];

                var datasetsDataStatus = [{
                    label: '% Percentage',
                    data: [resultData.last5cycle.StatusCompare.completed.percentage,resultData.last5cycle.StatusCompare.refund.percentage,resultData.last5cycle.StatusCompare.cancelled.percentage] ,
                    backgroundColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderColor: ['rgba(26, 188, 156, 0.2)','rgb(234, 222, 161)','rgb(255, 48, 86)'],
                    borderWidth: 1,
                    animation: {easing: "easeInOutBack"},
                },
               ];
                    
                $("#year-current-amount").html(resultData.currentAmount);
                $("#year-previous-amount").html(resultData.previousAmount);
                $("#year-previous-date").html(resultData.previousDateDisplay);
                $("#year-selected-date-display").html(resultData.currentDateDisplay);
                $("#year-left-pan-navi").attr("year-sel-date",resultData.previousDate);
                $("#year-right-pan-navi").attr("year-sel-date",resultData.nextDate);
                $("#box-3").hide();
                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {labels: label,datasets:datasetsData },
                    options: {scales: {yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}]}
                    }
                });

                var LabelStatus2 = ['Completed','Refund','Cancelled'];
                console.log(datasetsDataStatus);

                myChart3Status = new Chart(ctxStatus3, {
                    type: 'bar',
                    data: {labels: ['Completed','Refund','Cancelled'],datasets:datasetsDataStatus },
                    options: {
                        title: {
                            display: true,
                            text: '(%) Percentage Comparison'
                        },
                        legend: { display: false },
                        scales: {
                            yAxes: [{gridLines: {display:false}}],xAxes: [{gridLines: {display:false}}],}
                    }
                });
               
            }
        });

    }
    
    loadPanelDayStatistic('DAYCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN');
    loadPanelMonthStatistic('MONTHCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN');
    loadPanelYearStatistic('YEARCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN');
   
});
</script>
@stop