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
.loader {
    background-color: rgb(54 50 50 / 95%);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 1000;
    margin-top: 0px;
    top: 0px;
}
.loader-centered {
    position: absolute;
    left: 50%;
    top: 50%;
    height: 200px;
    width: 200px;
    margin-top: -100px;
    margin-left: -132px;
}
.object {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0);
    margin-right: auto;
    margin-left: auto;
    border: 4px solid #fff;
    left: 73px;
    top: 73px;
    position: absolute;
}

.square-one {
    -webkit-animation: first_object_animate 1s infinite ease-in-out;
    animation: first_object_animate 1s infinite ease-in-out;
}
.square-two {
    -webkit-animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
    animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
}
.square-three {
    -webkit-animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
    animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
}

@-webkit-keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@-webkit-keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}
@keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}

@-webkit-keyframes first_object_animate {
    0% {
        -webkit-transform: perspective(100px);
    }
    50% {
        -webkit-transform: perspective(100px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(100px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes first_object_animate {
    0% {
        transform: perspective(100px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
    }
}

@-webkit-keyframes second_object_animate {
    0% {
        -webkit-transform: perspective(200px);
    }
    50% {
        -webkit-transform: perspective(200px) rotateY(180deg);
    }
    100% {
        -webkit-transform: perspective(200px) rotateY(180deg) rotateX(180deg);
    }
}

@keyframes second_object_animate {
    0% {
        transform: perspective(200px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(200px) rotateX(180deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(200px) rotateX(180deg) rotateY(180deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(180deg);
    }
}

@-webkit-keyframes third_object_animate {
    0% {
        -webkit-transform: perspective(300px);
    }
    50% {
        -webkit-transform: perspective(300px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(300px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes third_object_animate {
    0% {
        transform: perspective(300px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
    }
}

/* STAT COMPARE */
    
</style>
<div class="loader" style="display: none;" id="process">

    <div class="loader-centered">
    <h1 style="color:white;margin-top: 200px !important;width: 127%;">Loading......</h1>

        <div class="object square-one"></div>
        <div class="object square-two"></div>
        <div class="object square-three"></div>
    </div>
</div>
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
    
    <div class="row" style="<?php if($topstatic_privilage=='0' && $totalsale_privilage=='0' && $barchart_privilage=='0'&& $dmy_privilage=='0' && $logistics_previlage=='0' && $logistics_spcl=='0'){ ?>display:none<?php } ?>">
            <div class="pull-right" style="margin-bottom:10px;margin-top:8px;">
                <table>
                    <tbody>
                        <tr>
                            <td><h3 class="panel-title" style="margin-right:16px;"><i class="fa fa-filter"></i> Platform Filter</h3></td>
                            <td><select class="form-control platform pull-right" name="masterplatform" id="masterplatform" style="margin-right:6px;">
                                <option value="mshopping" data-id='1' data-value='0'>Over all</option>
                                @foreach($platformslist as $platform)
                                
												<option value="{{$platform->platform_username}}" data-id='{{$platform->id}}' data-value='{{$platform->id}}'>{{$platform->platform_name}}</option>
								@endforeach
											</select></td>
							<td><select class="form-control masterstorerange pull-right" name="masterstore" id="masterstore" style="margin-right:6px;">
												
												
											</select></td>
                        </tr>
                    </tbody>
                </table>
                </div>
        </div>
<div class="row text-center" style="<?php if($barchart_privilage=='1'){ ?>display:none<?php } ?>">        
        <div class="col-lg-12 col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row text-center">
                       <div><h2>Welcome to your dashboard, {{ Session::get('username') }} !</h2></div>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-8" style="<?php if($logistics_spcl=='0'){ ?>display:none<?php } ?>">
            <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default d-panel">
                    <div class="panel-heading d-panel-heading">Logistic Status Summary</div>
                    <div class="panel-body">
                        <div class="col-md-12" id="">
                            <div id="donut_single1" style="width:100%;height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default d-panel">
                    <div class="panel-heading d-panel-heading">Top 5 Pending Product</div>
                    <div class="panel-body">
                        <div class="col-md-12" id="">
                            <div id="donut_single12" style="width:100%;height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

<div class="row" style="margin-bottom:0px;<?php if($topstatic_privilage=='0'){ ?>display:none<?php } ?>">
    
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



<div class="row" style="margin-bottom:0px;<?php if($totalsale_privilage=='0'){ ?>display:none<?php } ?>">
    
        <div class="col-md-3">
            <div class="stat-box" style="background-color: #059d3a;color:#ffffff">
            <div class="stat-title"><i class="fa fa-bar-chart"></i> Total Sales</div>
            <div class="stat-total" id="master-total">{{Config::get("constants.CURRENCY")}} {{ $total_value }}</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box" style="background-color: #ab2b2b;color:#ffffff">
            <div class="stat-title"><i class="fa fa-users"></i> Total Customer</div>
            <div class="stat-total" id="master-customer"><?php echo number_format($total_cust); ?></div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3 ">
             <div class="stat-box" style="background-color: #059d96;color:#ffffff">
            <div class="stat-title"><i class="fa fa-cubes"></i> Total Product</div>
            <div class="stat-total" id="master-products">{{ number_format($total_products) }}</div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
        <div class="col-md-3" >
             <div class="stat-box" style="background-color: #06566e;color:#ffffff">
            <div class="stat-title"><i class="fa fa-bars"></i>  Total Transaction</div>
            <div class="row" style="height: 45px">
                    <div class="col-xs-6">
                        <div class="stat-title" style="color:#7fffd4">Completed</div>
                        <div style="color: #ffffff; font-weight: bold;" id="master-total-completed">{{ number_format($total_completed_transaction) }}</div>
                    </div>
                    <div class="col-xs-6">
                        <div class="stat-title" style="color:#7fffd4">Pending</div>
                        <div style="color: #ffffff; font-weight: bold;" id="master-pending-transaction">{{ number_format($total_pending_transaction) }}</div>
                    </div>
                </div>
                <div class="row" style="height: 45px">
                    <div class="col-xs-6">
                        <div class="stat-title" style="color:#7fffd4">Cancelled</div>
                        <div style="color: #ffffff;font-weight: bold;" id="master-cancelled-transaction">{{ number_format($total_cancelled_transaction) }}</div>
                    </div>
                    <div class="col-xs-6">
                        <div class="stat-title" style="color:#7fffd4">Refund</div>
                        <div style="color: #ffffff;font-weight: bold;" id="master-refund-transaction">{{ number_format($total_refund_transaction) }}</div>
                    </div>
                </div>
            <!--<div><i class="fa fa-caret-right" aria-hidden="true"></i> Increase 10%</div>-->
            </div>
        </div>
 
 </div>

<div class="row" style="<?php if($barchart_privilage=='0'){ ?>display:none<?php } ?>">
    <div class="col-md-12 col-xs-12">
    <div class="panel panel-default d-panel" style="margin-top:20px;">
            <div class="panel-heading d-panel-heading" style="height:40px;">
                <div class="pull-left d-panel-title">
    <!--                <i class="fa fa-bullseye" aria-hidden="true"></i> Sales -->
                </div>
                <input type="date" name="date" style="display:none" id="tuo">
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


<div class="row" style="margin-bottom:20px;<?php if($percentage_previlage=='0'){ ?> display:none <?php } ?>">
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
    
    <div class="row" style="margin-bottom:20px;<?php if($percentage_previlage=='0'){ ?> display:none <?php } ?>">
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
                    <div class="percentCount"><?php echo '2019'; ?> (MYR <?php echo number_format(10000000.00,2,".",","); ?>)</div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row" style="<?php if($dmy_privilage=='0'){ ?>display:none<?php } ?>">
       
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
    
<!-- Panel 2 -->
<div class="row" style="<?php if($logistics_previlage=='0'){ ?>display:none<?php } ?>">
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
                <table class="table table-striped table-bordered table-hover" id="dataTable-latesttransaction"> 
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
                    </tbody> 
                </table>
            </div>
            <div class="panel-footer">
                <a href="/transaction" class="btn btn-default">view all</a>
            </div>
        </div>
    </div>
    <div class="row">
            <div class="pull-right" style="margin-bottom:10px;margin-top:8px;">
                <table>
                    <tbody>
                        <tr>
                            <td><h3 class="panel-title" style="margin-right:16px;"><i class="fa fa-filter"></i> Filter</h3></td>
                            <td><select class="form-control platform pull-right" name="platform" id="platform" style="margin-right:6px;">
                                
                                @foreach($platformslist as $platform)
                                @if($platform->platform_username!='mshopping')
												<option value="{{$platform->platform_username}}" data-id='{{$platform->id}}'>{{$platform->platform_name}}</option>
								@endif
								@endforeach
											</select></td>
							<td><select class="form-control storerange pull-right" name="store" id="store" style="margin-right:6px;">
												
												
											</select></td>
						    <td><select class="form-control storerange pull-right" name="platformstatus" id="platformstatus" style="margin-right:6px;">
						                       <option value="">All</option>
												<option value="completed">Completed</option>
												<option value="pending">Pending</option>
											</select></td>
							<td><label for='date' style="margin-bottom:20px;"> From:<input type='date' name='fromdate' class='form-control pull-right' placeholder='from date' id='platfom-from-date'></label> </td>
								<td><label for='date' style="margin-bottom:20px;"> To:<input type='date' name='todate' class='form-control pull-right' placeholder='from date' id='platfom-to-date'></label> </td>
								<td> <button type="button" class="btn btn-primary" style="margin-right: 9px;margin-left: 10px;" id='filter'> Filter </button></td>
							<td><button type="button" class="btn btn-success" style="margin-right:18px;" data-toggle="modal" data-target="#exportModal"> Export</button></td>
                        </tr>
                    </tbody>
                </table>
                </div>
        </div>
        <div class="panel panel-default" style="text-align:left !important">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 3rd Party Transaction</h3>
                </div>
                	
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTable-transaction" style="font-size: 14px!important;">
                            <thead>
                                <tr>
                                    <th># ID</th>
                                    <th>Transaction Date</th>
                                    <th>External Order Number</th>
                                    <th>Buyer</th>
                                    <th>Total Amount</th>
                                    <th>Area Type</th>
                                    <th>State</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true" style="text-align:left !important">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exportModalLabel">EXPORT</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
					<div class="">
						<p>If your current view includes filters, the export will include the filtered records.</p>
					</div>
					<b>Format:</b>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="transaction_export" id="excel" value="excel">
						<label class="form-check-label" for="excel" style="font-weight: unset;">Excel</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="javascript:export_transaction()" class="btn btn-sm btn-danger">Export</button>
					<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
   
</div>
    
</div>


@stop
@section('inputjs')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script>
<?php if($topstatic_privilage!='0'||$totalsale_privilage!='0'||$barchart_privilage!='0'||$percentage_previlage!='0'||$dmy_privilage!='0'||$logistics_previlage!='0' || $logistics_spcl!='0' ){ ?>
function export_transaction(){
		var url;
		var query;
		var platform= $('#platform').val();
        var store= $('#store').val();
        var platformstatus= $('#platformstatus').val();
		var type=$("input[name='transaction_export']").is(":checked");
		var export_type = $('input[name="transaction_export"]:checked').val();
		var from_date=$('#platfom-from-date').val();
        var to_date=$('#platfom-to-date').val();
		if(store==''){
    alert('Please Select Store');
    return false;
         }
     if(from_date!='' && to_date==''|| to_date!='' && from_date==''){
    alert('Please fill both From Date and To Date');
    return false;
    }
		if (type){
			query = {
				type: export_type,
				platform: platform,
				store:store,
				platformstatus:platformstatus,
				from_date:from_date,
				to_date:to_date,
			}
		}else{
		  alert("Please Select Export Type");
			return false;  
		} 
		

		if(export_type == 'excel'){
			url = "{{URL::to('dashboard/downloadexcel/xls')}}?" + $.param(query);
		}
		else if(export_type=='pdf') {
			url = "{{URL::to('dashboard/exportpdf')}}?" + $.param(query);
		}
		window.location = url;

		$("#exportModal").modal('toggle');
	}
$(document).ready(function(){
    
    $("#nav-left").click(function(){
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        var cumumulative_type = $('#cumulative').val();
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadChart(1,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
    });
    
    $("#nav-right").click(function(){
  
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        var cumumulative_type = $('#cumulative').val();
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadChart(2,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
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
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadChart(0,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
        
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
            var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
            var platform= $('#masterplatform').val();
            var store= $('#masterstore').val();
            loadChart(0,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
        }
        
        if($('#type-select').val() == 2){
            var type = $('#type-select').val();
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            var cumumulative_type = $('#cumulative').val();
            var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
            var platform= $('#masterplatform').val();
            var store= $('#masterstore').val();
            loadChart(0,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
        }
        
        if($('#type-select').val() == 3){
            
            var type = $('#type-select').val();
            var startDate = $('#start-month-date').val();
            var toDate = $('#end-month-date').val();
            var cumumulative_type = $('#cumulative').val();
            var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
            var platform= $('#masterplatform').val();
            var store= $('#masterstore').val();
            loadChart(0,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
            
        }
    });
    // Change period type 
    var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    var cumumulative_type = $('#cumulative').val();
    var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
    var platform= $('#masterplatform').val();
    var store= $('#masterstore').val();
    
    function parseSVG(s) {
        var div= document.createElementNS('http://www.w3.org/1999/xhtml', 'div');
        div.innerHTML= '<svg xmlns="http://www.w3.org/2000/svg">'+s+'</svg>';
        var frag= document.createDocumentFragment();
        while (div.firstChild.firstChild)
            frag.appendChild(div.firstChild.firstChild);
        return frag;
    }
    
    function loadChart(navigation,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store){
        
        $.ajax({
                method: "POST",
                url: "/dashboard/dashboarddata",
                data: {
                    'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate,
                    'cumumulative_type':cumumulative_type,
                    'platform_id':masterplatforms,
                    'platform':platform,
                    'store_id' :store
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
        var item_cell = 2;
        var counter = 1;
        var pos = 0;
        
        $.each(items,function(index,v){
            
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
              is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
            chart.draw(data, options);
            var chart1 = new google.visualization.PieChart(document.getElementById('donut_single1'));
            chart1.draw(data, options);
        
        
            var data = google.visualization.arrayToDataTable([
              ['Task', 'Hours per Day'],
            <?php foreach ($totalProductQty as $keyP => $valueP) { ?>
    
                ["<?php echo htmlspecialchars($valueP->name); ?>",    <?php echo $valueP->total_qty;  ?>],
                        
            <?php } ?>
            ]);

            var options = {
//              title: 'Top 5 Pending Product',
              pieHole: 0.3,
              is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single2'));
            chart.draw(data, options);
            var chart2 = new google.visualization.PieChart(document.getElementById('donut_single12'));
            chart2.draw(data, options);
//            var chart2 = new google.visualization.PieChart(document.getElementById('donut_single2'));
//            chart2.draw(data, options);
      }
      $('#platform').on('change', function() {
        var  platform=$('#platform').find(':selected').data('id') 
       $.get('platforms/storelist?platform_id=' + platform, function(data) {
            $('#store').empty();
            var finaldata="";
            finaldata+="<option value='' selected='selected'>Select Store</option><option value='all'>All</option>";
                $.each(data, function(index, stateObj){
                    finaldata+='<option value="' + stateObj.external_store_id + '">' + stateObj.store_name + '</option>';
                });
             $('#store').html(finaldata);
        });
    });
$('#filter').on('click', function() {
var platform= $('#platform').val();
var store= $('#store').val();
var platformstatus= $('#platformstatus').val();
var from_date=$('#platfom-from-date').val();
var to_date=$('#platfom-to-date').val();
if(store==''){
    alert('Please Select Store');
    return false;
}
if(from_date!='' && to_date==''|| to_date!='' && from_date==''){
    alert('Please fill both From Date and To Date');
    return false;
}
var actionurl="{{ URL::to('dashboard/platformlisting?platform=')}}"+platform+"&store="+store+"&platformstatus="+platformstatus+"&from_date="+from_date+"&to_date="+to_date;
if(store!=''){
$('#dataTable-transaction').DataTable().ajax.url(actionurl).load();
}
});
var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
       $.get('platforms/storelist?platform_id=' + masterplatforms, function(data) {
            $('#masterstore').empty();
            var finaldatas='<option value="1">All</option>';
             $('#masterstore').html(finaldatas);
        });
 var  platforms=$('#platform').find(':selected').data('id'); 
       $.get('platforms/storelist?platform_id=' + platforms, function(data) {
            $('#store').empty();
            var finaldata="";
            finaldata+="<option value=''>Select Store</option><option value='all' selected='selected'>All</option>";
                $.each(data, function(index, stateObj){
                    finaldata+='<option value="' + stateObj.external_store_id + '">' + stateObj.store_name + '</option>';
                });
             $('#store').html(finaldata);
        });
        

var platform= $('#platform').val();
var store='all';
var platformstatus= $('#platformstatus').val();
var from_date=$('#platfom-from-date').val();
var to_date=$('#platfom-to-date').val();
var actionurl="{{ URL::to('dashboard/platformlisting?platform=')}}"+platform+"&store="+store+"&platformstatus="+platformstatus+"&from_date="+from_date+"&to_date="+to_date;
$('#dataTable-transaction').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax":actionurl,
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data" : "id", orderable: true, searchable: true, 'className' : 'text-center', "width": "8%"},
        { "data" : "transaction_date", 'className' : 'text-center'},
        { "data" : "order_number", 'className' : 'text-center'},
        { "data" : "buyer_username", 'className' : 'text-center'},
        { "data" : "total", 'className' : 'text-center'},
        { "data" : "delivery_area_type", orderable: false, searchable: true, 'className' : 'text-center'},
        { "data" : "delivery_state", 'className' : 'text-center'},
        { "data" : "status", 'className' : 'text-center'}
    ]
});
var  masterplatforms1=$('#masterplatform').find(':selected').data('id'); 
var platform1= $('#masterplatform').val();
var store1= $('#masterstore').val();
var actionurls="{{ URL::to('dashboard/dashboardlatesttransaction?platform_id=')}}"+masterplatforms1+"&store_id="+store1+"&platform="+platform1;
<?php if($logistics_previlage=='1'){?>
$('#dataTable-latesttransaction').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "paging": false,
    "bFilter": false,
    "bInfo": false, 
    "ajax":actionurls,
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data" : "0", 'className' : 'text-center'},
        { "data" : "1", 'className' : 'text-center'},
        { "data" : "2", 'className' : 'text-center'},
        { "data" : "3", 'className' : 'text-center'},
        { "data" : "4", 'className' : 'text-center'},
    ]
});
<?php } ?>
$('#masterplatform').on('change', function() {
        var  platform=$('#masterplatform').find(':selected').data('id');
         var addtionalid=$('#masterplatform').find(':selected').data('value')
       $.get('platforms/storelist?platform_id=' + platform, function(data) {
            $('#masterstore').empty();
            var finaldata="";
            if(platform==1)
            {
                if(addtionalid==0){
                   finaldata+="<option value='' selected='selected'>Select Store</option><option value='1'>All</option>"; 
                }else{
            finaldata+="<option value='' selected='selected'>Select Store</option>";
                }
            }else{
            finaldata+="<option value='' selected='selected'>Select Store</option><option value='all'>All</option>";  
            }
                $.each(data, function(index, stateObj){
                     if(addtionalid!=0){
                    finaldata+='<option value="' + stateObj.external_store_id + '">' + stateObj.store_name + '</option>';
                     }
                });
             $('#masterstore').html(finaldata);
        });
    });

}); 
<?php } ?>
</script>
<script>
<?php if($topstatic_privilage!='0'||$totalsale_privilage!='0'||$barchart_privilage!='0'||$percentage_previlage!='0'||$dmy_privilage!='0'||$logistics_previlage!='0' || $logistics_spcl!='0' ){ ?>
$(document).ready(function(){
    
    var myChart;
    
    $("#box-1").show();
    
    $("#day-left-pan-navi").click(function(){
        var currentDate = $(this).attr("day-sel-date");
var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
var platform= $('#masterplatform').val();
var store= $('#masterstore').val();
        loadPanelDayStatistic('DAYCOM',currentDate,'NL',masterplatforms,platform,store);
    });
    
    $("#day-right-pan-navi").click(function(){
        var currentDate = $(this).attr("day-sel-date");
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadPanelDayStatistic('DAYCOM',currentDate,'NR',masterplatforms,platform,store);
    });
    
    
    $("#month-left-pan-navi").click(function(){
        var currentDate = $(this).attr("month-sel-date");
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadPanelMonthStatistic('MONTHCOM',currentDate,'NL',masterplatforms,platform,store);
    });
    
    $("#month-right-pan-navi").click(function(){
        var currentDate = $(this).attr("month-sel-date");
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadPanelMonthStatistic('MONTHCOM',currentDate,'NR',masterplatforms,platform,store);
    });
    
    
    $("#year-left-pan-navi").click(function(){
        var currentDate = $(this).attr("year-sel-date");
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadPanelYearStatistic('YEARCOM',currentDate,'NL',masterplatforms,platform,store);
    });
    
    $("#year-right-pan-navi").click(function(){
        var currentDate = $(this).attr("year-sel-date");
        var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
        var platform= $('#masterplatform').val();
        var store= $('#masterstore').val();
        loadPanelYearStatistic('YEARCOM',currentDate,'NR',masterplatforms,platform,store);
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
    function loadPanelDayStatistic(typeData,currentDate,action,masterplatforms,platform,store){
        
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
        var masterplatforms=masterplatforms;
        var platform=platform;
        var store=store;
        
        $("#box-1").show();
        
        $.ajax({
            type: 'POST',
            url: '/dashboard/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action,
                "platform_id" :masterplatforms,
                "platform" :platform,
                "store_id" :store
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
    
    function loadPanelMonthStatistic(typeData,currentDate,action,masterplatforms,platform,store){
        
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
        var masterplatforms=masterplatforms;
        var platform=platform;
        var store=store;
        
        $("#box-2").show();
        
        $.ajax({
            type: 'POST',
            url: '/dashboard/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action,
                "platform_id" :masterplatforms,
                "platform" :platform,
                "store_id" :store
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
    
    function loadPanelYearStatistic(typeData,currentDate,action,masterplatforms,platform,store){
        
        
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
        var masterplatforms=masterplatforms;
        var platform=platform;
        var store=store;
        
        $("#box-3").show();
        
        $.ajax({
            type: 'POST',
            url: '/dashboard/daystatistic',
            data: {
                "typeData" :typeData,
                "currentDate" :currentDate,
                "action" :action,
                "platform_id":masterplatforms,
                "platform" :platform,
                "store_id" :store
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
        var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    var cumumulative_type = $('#cumulative').val();
    var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
    var platform= $('#masterplatform').val();
    var store= $('#masterstore').val();
    loadChart(0,1,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
    
    function parseSVG(s) {
        var div= document.createElementNS('http://www.w3.org/1999/xhtml', 'div');
        div.innerHTML= '<svg xmlns="http://www.w3.org/2000/svg">'+s+'</svg>';
        var frag= document.createDocumentFragment();
        while (div.firstChild.firstChild)
            frag.appendChild(div.firstChild.firstChild);
        return frag;
    }
    
    function loadChart(navigation,type,startDate,toDate,cumumulative_type,masterplatforms,platform,store){
        
        $.ajax({
                method: "POST",
                url: "/dashboard/dashboarddata",
                data: {
                    'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate,
                    'cumumulative_type':cumumulative_type,
                    'platform_id':masterplatforms,
                    'platform':platform,
                    'store_id' :store
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
        var item_cell = 2;
        var counter = 1;
        var pos = 0;
        
        $.each(items,function(index,v){
            
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
var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
var platform= $('#masterplatform').val();
var store= $('#masterstore').val();
<?php if($dmy_privilage=='1'){?>
    loadPanelDayStatistic('DAYCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
    loadPanelMonthStatistic('MONTHCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
    loadPanelYearStatistic('YEARCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
<?php  } ?>
$('.masterstorerange').on('change', function() {
$('#process').show();
var  masterplatforms=$('#masterplatform').find(':selected').data('id'); 
var platform= $('#masterplatform').val();
var store= $('#masterstore').val();
if(store==''){
    alert('Please select store!');
    return false;
}
var actionurls="{{ URL::to('dashboard/dashboardlatesttransaction?platform_id=')}}"+masterplatforms+"&store_id="+store+"&platform="+platform;
$.get('dashboard/dashboardtotal?platform_id=' + masterplatforms+'&store_id='+store+'&platform='+platform, function(data) {
            var mastertotal='{{Config::get("constants.CURRENCY")}} '+data['master_total'];
             var master_customer=data['total_customer'];
             var master_products=data['total_products'];
             var master_total_completed=data['total_completed_transaction'];
             var master_pending_transaction=data['total_pending_transaction'];
             var master_cancelled_transaction=data['total_cancelled_transaction'];
             var master_refund_transaction=data['total_refund_transaction'];
            $('#master-total').html(mastertotal);
            $('#master-customer').html(master_customer);
            $('#master-products').html(master_products);
            $('#master-total-completed').html(master_total_completed);
            $('#master-pending-transaction').html(master_pending_transaction);
            $('#master-cancelled-transaction').html(master_cancelled_transaction);
            $('#master-refund-transaction').html(master_refund_transaction);
            $('#process').hide();
            
        });
$('#dataTable-latesttransaction').DataTable().ajax.url(actionurls).load();
  $.get('dashboard/charttwo?platform_id=' + masterplatforms+'&store_id='+store+'&platform='+platform, function(datas) {
           
var arr = [['Task', 'Hours per Day']];
for (var i = 0; i < datas.length; i++) {
    arr.push([datas[i].name,parseInt(datas[i].total_qty)]);
}
            var data = google.visualization.arrayToDataTable(arr);
            var options = {
              pieHole: 0.3,
              is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single2'));
            chart.draw(data, options);
            var chart2 = new google.visualization.PieChart(document.getElementById('donut_single12'));
            chart2.draw(data, options);
      });
$.get('dashboard/chartone?platform_id=' + masterplatforms+'&store_id='+store+'&platform='+platform, function(datas) {
 var data = google.visualization.arrayToDataTable([
              ['Task', 'Hours per Day'],
              ["Pending ("+datas['TotalPendingAll']+")",datas['TotalPendingAll']],
              ["Sending ("+datas['TotalSendingAll']+")",datas['TotalSendingAll']],
              ["Returned ("+datas['TotalReturnedAll']+")",datas['TotalReturnedAll']],
              ["Undelivered ("+datas['TotalUndeliveredAll']+")",datas['TotalUndeliveredAll']],
              ["Partial Sent ("+datas['TotalPartialAll']+")",datas['TotalPartialAll']]
            ]);

            var options = {
//              title: 'Logistic Status',
              pieHole: 0.3,
              is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donut_single'));
            chart.draw(data, options);
            var chart1 = new google.visualization.PieChart(document.getElementById('donut_single1'));
            chart1.draw(data, options);
 }); 

    loadPanelDayStatistic('DAYCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
    loadPanelMonthStatistic('MONTHCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
    loadPanelYearStatistic('YEARCOM',<?php echo "'".date("Y-m-d")."'"; ?>,'NN',masterplatforms,platform,store);
    loadChart(0,1,startDate,toDate,cumumulative_type,masterplatforms,platform,store);
    
 
});
   
});
<?php } ?>
</script>
@stop