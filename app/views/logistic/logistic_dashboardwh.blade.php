@extends('layouts.master2')
@section('title') Logistic @stop
@section('content')
<style>
    .panel-box{
        border : 0px;
    }
    
    .stat-box{
        /*min-height: 100px;*/
        /*background-color: #f7f7f7;*/
        /*padding: 10px;*/
        /*border-right: solid 1px #dcdcdc;*/
        background-color: #f5f5f5;
        padding: 10px;
        border-right: solid 5px #fff;
        -webkit-box-shadow: 0 8px 6px -6px #c5bcbc;
        -moz-box-shadow: 0 8px 6px -6px #c5bcbc;
        box-shadow: 0 8px 6px -6px #c5bcbc;
        
    }
    
    .stat-box:last-child{
        background-color: #f5f5f5;
        padding: 10px;
        border-right: solid 5px #fff;
        -webkit-box-shadow: 0 8px 6px -6px #c5bcbc;
        -moz-box-shadow: 0 8px 6px -6px #c5bcbc;
        box-shadow: 0 8px 6px -6px #c5bcbc;
        
    }
    
    .stat-info{
        /*padding-top: 10px;*/
    }
    
    .stat-delay{
        text-align: left;
        color: #cc7781;
        padding: 3px;
        border-bottom: solid 1px #e8e8e8;
        margin-bottom: 5px;
        font-weight: 400;
    }
    
    .stat-total{
        font-size: 25px;
        font-weight: bold;
        color: #8c9ab5;
    }
    
    .stat-month, .stat-month-region{
        font-size: 12px;
        color: #989898;
    }
    
    .stat-status{
        font-weight: inherit;
        color: #9a9a9a;
        font-size: 12px;
        font-weight: 500;
    }
    
    .icon-box{
        min-width: 50px;
        padding: 5px;
        padding-top: 12px;
    }
    
    .icon-circle{
        border:solid 3px #aac2d8;
        padding: 7px;
        border-radius: 50px;
        background-color: #f5f5f5;
        width: 50px;
        height: 50px;
    }
    
    .tile-box{
        height: 150px;
        background-color: #ddd;
    }
    
    .panel-stat .stat-label {
    text-transform: uppercase;
    font-size: 11px;
    opacity: 0.75;
    display: block;
    line-height: normal;
    margin-bottom: 2px;
}
    
    
    
    .mb15 {
    margin-bottom: 15px;
}

.panel-stat .stat {
    color: #fff;
    max-width: 250px;
}
    
    .panel-success .panel-heading {
    background-color: #1CAF9A !important;
}
    
    .rTableRow { display: table-row; }
    .rTableCell, .rTableHead { display: table-cell; }
    .modal-body {
    padding-top: 0 !important;
}

a.overall, a.byDate, a.overallSendingPlaceRegion, a.overallPendingPlaceRegion {
    cursor: pointer;
    font-weight: bold;
    color: #989898;
}
    
    
</style>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.min.js"></script>-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<!--<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>-->
<!--<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css">-->
<div d="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div lass="col-md-12" >
        <?php if($region_id==0) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">Overall Statistics</div>
                <div class="panel-body" style="padding:25px;">
                    <div class="panel panel-default panel-box row" >
                    <?php  //for ($x= 0; $x<6; $x++){ ?>
                    <div class="col-md-6  col-xs-6 stat-box" style="margin-bottom:10px;">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-pending-date-place">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallPendingPlace"><input type="hidden" class="status" value="0">Pending Place</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                        
                        <div class="col-md-6  col-xs-6 stat-box" style="margin-bottom:10px;">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sending-date-place">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallSendingPlace"><input type="hidden" class="status" value="0">Sending Place</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-pending-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="0">Pending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sending-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="4">Sending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-flip-horizontal fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-undelivered-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="1"> Undelivered</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-exclamation-triangle fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-partial-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="2"> Partial Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-puzzle-piece fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-returned-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="3"> Returned</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sent-date">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overall"><input type="hidden" class="status" value="5"> Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-check-square-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>                     
                   
                    <?php  // } ?>
                </div>
                    <div class="row">
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Pending</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-pending"></div>
                                <span class="stat-month" data-status="0" data-month="1"><i class="fa fa-caret-right"></i><a class="overall"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-pending">0</div>
                                <span class="stat-month" data-status="0" data-month="2"><i class="fa fa-caret-right"></i><a class="overall"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-pending">0</div>
                                <span class="stat-month" data-status="0" data-month="3"><i class="fa fa-caret-right"></i><a class="overall"> Over 1 month</a></span></div>
                        </div>
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Sending</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-sending">0</div>
                                <span class="stat-month" data-status="4" data-month="1"><i class="fa fa-caret-right"></i><a class="overall"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-sending">0</div>
                                <span class="stat-month" data-status="4" data-month="2"><i class="fa fa-caret-right"></i><a class="overall"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-sending">0</div>
                                <span class="stat-month" data-status="4" data-month="3"><i class="fa fa-caret-right"></i><a class="overall"> Over 1 month</a></span></div>
                        </div>
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Return</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-return">0</div>
                                <span class="stat-month" data-status="3" data-month="1"><i class="fa fa-caret-right"></i><a class="overall"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-return">0</div>
                                <span class="stat-month" data-status="3" data-month="2"><i class="fa fa-caret-right"></i><a class="overall"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-return">0</div>
                                <span class="stat-month" data-status="3" data-month="3"><i class="fa fa-caret-right"></i><a class="overall"><a class="overall"> Over 1 month</a></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="row" style="padding-top:10px;">
        </div>
        <div class="row">
            <div class="col-md-12 col-xs-12" style="margin-bottom:20px;padding-right: 0px;">
                <div class="col-md-12">
                <div class="btn-group pull-right" role="group" aria-label="..." >
                    <button type="button" class="btn btn-default" id="nav-left"><i class="fa fa-arrow-left"></i></button>
                    <button type="button" class="btn btn-default disabled"><i class="fa fa-calendar-o"></i> <span id="selected-date"> <?php echo date("d M Y")?></span></button>
                    <button type="button" class="btn btn-default" id="nav-right"><i class="fa fa-arrow-right"></i></button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="selected-date-range">Daily</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                        <input type="hidden" id="start-date-select" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="to-date-select" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="type-select" value="<?php echo 1;?>">
                        
                        <input type="hidden" id="start-today" value="<?php echo date("Y-m-d")?>">
                        <input type="hidden" id="start-weekly-date" value="">
                        <input type="hidden" id="end-weekly-date" value="">
                        <input type="hidden" id="start-month-date" value="">
                        <input type="hidden" id="end-month-date" value="">
                    </button>
                    <ul class="dropdown-menu">
                        <li ><a class="date-range" data-type="1"> Daily </a></li>
                        <li ><a class="date-range" data-type="2"> Weekly </a></li>
                        <li ><a class="date-range" data-type="3"> Monthly </a></li>
                    </ul>
                </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-xs-12">
            <div class="panel panel-default panel-box">
                <?php  //for ($x= 0; $x<6; $x++){ ?>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-pending">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDate"><input type="hidden" class="status" value="0">Pending</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-sending2">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDate"><input type="hidden" class="status" value="4"> Sending</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-truck fa-flip-horizontal fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-undelivered">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDate"><input type="hidden" class="status" value="1"> Undelivered</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-exclamation-triangle fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-partial">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="byDate"><input type="hidden" class="status" value="2"> Partial Sent</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-puzzle-piece fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-returned">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDate"><input type="hidden" class="status" value="3"> Returned</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-truck fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
                <div class="col-md-2  col-xs-6 stat-box">
                    <div class="pull-left stat-info">
                        <div class="stat-total" id="total-logistic-sent">0</div>
                        <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDate"><input type="hidden" class="status" value="5"> Sent</input></a></div>
                    </div>
                    <div class="pull-right icon-box">
                        <!--<div class="icon-circle">-->
                            <i class="fa fa-check-square-o fa-2x" style="color:#e68787"></i>
                        <!--</div>-->
                    </div>
                </div>
               
                <?php  // } ?>
            </div>
                </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row" style="margin-top:20px;">
                <div class="col-md-4">
                    <div class="panel panel-success panel-stat" style="">
                        <div class="panel-heading">

                          <div class="stat">
                            <div class="row">
                              <div class="col-xs-4">
                                  <i class="fa fa-truck fa-flip-horizontal fa-4x"></i>
                              </div>
                              <div class="col-xs-8">
                                <h1 style="margin-top: 5px;" id="total-batch-sending">0</h1>
                                <small class="stat-label">Total Batch Sending</small>
                              </div>
                            </div><!-- row -->

                            <div class="mb15"></div>
                          </div><!-- stat -->
                        </div><!-- panel-heading -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel  panel-stat"  style="background-color: #D9534F !important; ">
                        <div class="panel-heading">

                          <div class="stat">
                            <div class="row">
                              <div class="col-xs-4">
                                  <i class="fa fa-send-o fa-4x"></i>
                              </div>
                              <div class="col-xs-8">
                                <h1 style="margin-top: 5px;" id="total-batch-processing">0</h1>
                                <small class="stat-label">Total Batch Processing</small>
                              </div>
                            </div><!-- row -->

                            <div class="mb15"></div>
                          </div><!-- stat -->
                        </div><!-- panel-heading -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel  panel-stat" style="    background-color: #428bca !important;">
                        <div class="panel-heading">

                          <div class="stat">
                            <div class="row">
                              <div class="col-xs-4">
                                  <i class="fa fa-check-square-o fa-4x"></i>
                              </div>
                              <div class="col-xs-8">
                                <h1 style="margin-top: 5px;" id="total-batch-completed">0</h1>
                                 <small class="stat-label">Total Batch Completed</small>
                              </div>
                            </div><!-- row -->

                            <div class="mb15"></div>
                          </div><!-- stat -->
                        </div><!-- panel-heading -->
                    </div>
                </div>
        </div>
        
    <?php } else { ?>

    <!-- Region -->
            <div class="panel panel-default" id="targetRegion" style="margin-top: 15px;">
                <div class="panel-heading">{{$region_name}} Statistics</div>
                <div class="panel-body" style="padding:25px;">
                    <div class="panel panel-default panel-box row" >
                    <?php  //for ($x= 0; $x<6; $x++){ ?>
                    <div class="col-md-6  col-xs-6 stat-box" style="margin-bottom:10px;">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-pending-date-place-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallPendingPlaceRegion"><input type="hidden" class="statusRegion" value="0">Pending Place</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                        
                        <div class="col-md-6  col-xs-6 stat-box" style="margin-bottom:10px;">
                            <div class="pull-left stat-info">
                                <div class="stat-total" id="total-logistic-sending-date-place-region">0</div>
                                <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallSendingPlaceRegion"><input type="hidden" class="statusRegion" value="0">Sending Place</input></a></div>
                            </div>
                            <div class="pull-right icon-box">
                                <!--<div class="icon-circle">-->
                                    <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                                <!--</div>-->
                            </div>
                        </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-pending-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="0">Pending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sending-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="4">Sending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-flip-horizontal fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-undelivered-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="1"> Undelivered</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-exclamation-triangle fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-partial-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="2"> Partial Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-puzzle-piece fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-returned-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="3"> Returned</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sent-date-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="overallRegion"><input type="hidden" class="statusRegion" value="5"> Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-check-square-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                      
                   
                    <?php  // } ?>
                </div>
                    <div class="row">
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Pending</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-pending-region">0</div>
                                <span class="stat-month-region" data-status="0" data-month="1"><i class="fa fa-caret-right"></i><a class="overallRegion"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-pending-region">0</div>
                                <span class="stat-month-region" data-status="0" data-month="2"><i class="fa fa-caret-right"></i><a class="overallRegion"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-pending-region">0</div>
                                <span class="stat-month-region" data-status="0" data-month="3"><i class="fa fa-caret-right"></i><a class="overallRegion"><a class="overallRegion"> Over 1 month</a></span></div>
                        </div>
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Sending</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-sending-region">0</div>
                                <span class="stat-month-region" data-status="4" data-month="1"><i class="fa fa-caret-right"></i><a class="overallRegion"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-sending-region">0</div>
                                <span class="stat-month-region" data-status="4" data-month="2"><i class="fa fa-caret-right"></i><a class="overallRegion"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-sending-region">0</div>
                                <span class="stat-month-region" data-status="4" data-month="3"><i class="fa fa-caret-right"></i><a class="overallRegion"><a class="overallRegion"> Over 1 month</a></span></div>
                        </div>
                        <div class="col-md-4  col-xs-6 stat-box">
                            <div class="col-md-12 stat-delay" ><i class="fa fa-exclamation-circle"></i> Return</div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-1month-return-region">0</div>
                                <span class="stat-month-region" data-status="3" data-month="1"><i class="fa fa-caret-right"></i><a class="overallRegion"> 1 week</a></span></div>
                            <div class="col-md-4" style="border-right: solid 1px #e4e4e4;">
                                <div class="stat-total" id="t-d-2month-return-region">0</div>
                                <span class="stat-month-region" data-status="3" data-month="2"><i class="fa fa-caret-right"></i><a class="overallRegion"> 3 weeks</a></span></div>
                            <div class="col-md-4" >
                                <div class="stat-total" id="t-d-3month-return-region">0</div>
                                <span class="stat-month-region" data-status="3" data-month="3"><i class="fa fa-caret-right"></i><a class="overallRegion"><a class="overallRegion"> Over 1 month</a></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12" style="margin-bottom:20px;padding-right: 0px;">
                    <div class="col-md-12">
                    <div class="btn-group pull-right" role="group" aria-label="..." >
                        <button type="button" class="btn btn-default" id="nav-left"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-default disabled"><i class="fa fa-calendar-o"></i> <span id="selected-date"> <?php echo date("d M Y")?></span></button>
                        <button type="button" class="btn btn-default" id="nav-right"><i class="fa fa-arrow-right"></i></button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span id="selected-date-range">Daily</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                            <input type="hidden" id="start-date-select" value="<?php echo date("Y-m-d")?>">
                            <input type="hidden" id="to-date-select" value="<?php echo date("Y-m-d")?>">
                            <input type="hidden" id="type-select" value="<?php echo 1;?>">
                            
                            <input type="hidden" id="start-today" value="<?php echo date("Y-m-d")?>">
                            <input type="hidden" id="start-weekly-date" value="">
                            <input type="hidden" id="end-weekly-date" value="">
                            <input type="hidden" id="start-month-date" value="">
                            <input type="hidden" id="end-month-date" value="">
                        </button>
                        <ul class="dropdown-menu">
                            <li ><a class="date-range" data-type="1"> Daily </a></li>
                            <li ><a class="date-range" data-type="2"> Weekly </a></li>
                            <li ><a class="date-range" data-type="3"> Monthly </a></li>
                        </ul>
                    </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                <div class="panel panel-default panel-box">
                    <?php  //for ($x= 0; $x<6; $x++){ ?>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-pending-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="0">Pending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-calendar-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sending2-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="4"> Sending</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-flip-horizontal fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-undelivered-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="1"> Undelivered</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-exclamation-triangle fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-partial-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i><a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="2"> Partial Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-puzzle-piece fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-returned-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="3"> Returned</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-truck fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="col-md-2  col-xs-6 stat-box">
                        <div class="pull-left stat-info">
                            <div class="stat-total" id="total-logistic-sent-region">0</div>
                            <div class="stat-status"> <i class="fa fa-caret-right"></i> <a class="byDateRegion"><input type="hidden" class="statusRegionDate" value="5"> Sent</input></a></div>
                        </div>
                        <div class="pull-right icon-box">
                            <!--<div class="icon-circle">-->
                                <i class="fa fa-check-square-o fa-2x" style="color:#e68787"></i>
                            <!--</div>-->
                        </div>
                    </div>
                   
                    <?php  // } ?>
                </div>
                    </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row" style="margin-top:20px;">
                    <div class="col-md-4">
                        <div class="panel panel-success panel-stat" style="">
                            <div class="panel-heading">

                              <div class="stat">
                                <div class="row">
                                  <div class="col-xs-4">
                                      <i class="fa fa-truck fa-flip-horizontal fa-4x"></i>
                                  </div>
                                  <div class="col-xs-8">
                                    <h1 style="margin-top: 5px;" id="total-batch-sending-region">0</h1>
                                    <small class="stat-label">Total Batch Sending</small>
                                  </div>
                                </div><!-- row -->

                                <div class="mb15"></div>
                              </div><!-- stat -->
                            </div><!-- panel-heading -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel  panel-stat"  style="background-color: #D9534F !important; ">
                            <div class="panel-heading">

                              <div class="stat">
                                <div class="row">
                                  <div class="col-xs-4">
                                      <i class="fa fa-send-o fa-4x"></i>
                                  </div>
                                  <div class="col-xs-8">
                                    <h1 style="margin-top: 5px;" id="total-batch-processing-region">0</h1>
                                    <small class="stat-label">Total Batch Processing</small>
                                  </div>
                                </div><!-- row -->

                                <div class="mb15"></div>
                              </div><!-- stat -->
                            </div><!-- panel-heading -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel  panel-stat" style="    background-color: #428bca !important;">
                            <div class="panel-heading">

                              <div class="stat">
                                <div class="row">
                                  <div class="col-xs-4">
                                      <i class="fa fa-check-square-o fa-4x"></i>
                                  </div>
                                  <div class="col-xs-8">
                                    <h1 style="margin-top: 5px;" id="total-batch-completed-region">0</h1>
                                     <small class="stat-label">Total Batch Completed</small>
                                  </div>
                                </div><!-- row -->

                                <div class="mb15"></div>
                              </div><!-- stat -->
                            </div><!-- panel-heading -->
                        </div>
                    </div>
            </div>
    
           
    <?php } ?>
    <!-- End of Region -->

    <div id="myModal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed">
                           <tr>
                               <th style='text-align:center;'>Transaction ID</th>
                               <th>Insert Date</th>
                               <th >Delivery Name</th>
                               <th >City</th>
                               <th >State</th>
                           </tr> 
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="download btn btn-default" data-status="" data-startDate="" data-endDate="" data-month="">Download</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>

    <div id="myModalRegion" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed">
                           <tr>
                               <th style='text-align:center;'>Transaction ID</th>
                               <th>Insert Date</th>
                               <th >Delivery Name</th>
                               <th >City</th>
                               <th >State</th>
                           </tr> 
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="downloadRegion btn btn-default" id="downloadRegion" data-status="" data-startDate="" data-endDate="" data-month="">Download</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>
    
    <div id="myModalPendingPlace" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed" >
                           <thead>
                                <th style="width:400px;">Location</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Total</th>
                            </thead>
                            <tbody id="list-pending-all-place">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>

    <div id="myModalPendingPlaceRegion" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed" >
                           <thead>
                                <th style="width:400px;">Location</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Total</th>
                            </thead>
                            <tbody id="list-pending-all-place-region">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>
    
    <div id="myModalSendingPlace" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed" >
                            <thead>
                            <th style="width:400px;">Location</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Total</th>
                            </thead>
                            <tbody id="list-sending-all-place">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>

    <div id="myModalSendingPlaceRegion" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Transaction</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(100vh - 210px);overflow-y: auto;"> 
                        <!-- <p>Loading...</p> -->
                        <table class="tb table-striped table table-condensed" >
                            <thead>
                            <th style="width:400px;">Location</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Total</th>
                            </thead>
                            <tbody id="list-sending-all-place-region">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
                    </div>
            </div>
        </div>
    </div>

    

    </div>

@stop
@section('inputjs')
 <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
<script>
$(document).ready(function() {
    
    var timeout = setTimeout("location.reload(true);",600000);
      function resetTimeout() {
        clearTimeout(timeout);
        timeout = setTimeout("location.reload(true);",600000);
      }

    $('.overall').on('click', function(){

        var status = $(this).closest('div').find(".status").val();
        var originalModal = $('#myModal').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlist",
            dataType: 'json',
            data: {
                'status':status,   
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModal').modal('show').find('.tb').append(tr);

                    $('.download').attr('data-status', status);

                    $('#myModal').on('hidden.bs.modal', function () {

                        $('#myModal').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });

    $('.overallRegion').on('click', function(){

        var status = $(this).closest('div').find(".statusRegion").val();
        var originalModal = $('#myModalRegion').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlistregion",
            dataType: 'json',
            data: {
                'status':status,   
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModalRegion').modal('show').find('.tb').append(tr);

                    $('#downloadRegion').attr('data-status', status);

                    $('#myModalRegion').on('hidden.bs.modal', function () {

                        $('#myModalRegion').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });

    $('.byDate').on('click', function(){

        var status = $(this).closest('div').find(".status").val();
        var startDate = $('#start-date-select').val();
        var endDate = $('#to-date-select').val();
        var originalModal = $('#myModal').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlistdate",
            dataType: 'json',
            data: {
                'status':status,   
                'startDate':startDate, 
                'endDate':endDate,
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModal').modal('show').find('.tb').append(tr);

                    $('.download').attr('data-status', status);
                    $('.download').attr('data-startDate', startDate);
                    $('.download').attr('data-endDate', endDate);

                    $('#myModal').on('hidden.bs.modal', function () {

                        $('#myModal').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });
    
    $('.byDateRegion').on('click', function(){

        var status = $(this).closest('div').find(".statusRegionDate").val();
        var startDate = $('#start-date-select').val();
        var endDate = $('#to-date-select').val();
        var originalModal = $('#myModalRegion').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlistdateregion",
            dataType: 'json',
            data: {
                'status':status,   
                'startDate':startDate, 
                'endDate':endDate,
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModalRegion').modal('show').find('.tb').append(tr);

                    $('.downloadRegion').attr('data-status', status);
                    $('.downloadRegion').attr('data-startDate', startDate);
                    $('.downloadRegion').attr('data-endDate', endDate);

                    $('#myModalRegion').on('hidden.bs.modal', function () {

                        $('#myModalRegion').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });
        
    
    $('.overallPendingPlace').on('click', function(){
        $("#myModalPendingPlace").modal('show');
    });

    $('.overallPendingPlaceRegion').on('click', function(){
        $("#myModalPendingPlaceRegion").modal('show');
    });

    $('.overallSendingPlace').on('click', function(){
        $("#myModalSendingPlace").modal('show');
    });

    $('.overallSendingPlaceRegion').on('click', function(){
        $("#myModalSendingPlaceRegion").modal('show');
    });
    
    $('.stat-month').on('click', function(){

        var status = $(this).data("status");
        var month = $(this).data("month");
        var originalModal = $('#myModal').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlistdelay",
            dataType: 'json',
            data: {
                'status':status,   
                'month':month
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModal').modal('show').find('.tb').append(tr);

                    $('.download').attr('data-status', status);
                    $('.download').attr('data-month', month);

                    $('#myModal').on('hidden.bs.modal', function () {

                        $('#myModal').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });

    $('.stat-month-region').on('click', function(){

        var status = $(this).data("status");
        var month = $(this).data("month");
        var originalModal = $('#myModalRegion').clone();
   
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardlistdelayregion",
            dataType: 'json',
            data: {
                'status':status,   
                'month':month
            },
            success : function(data){
                
                for (var i = 0; i < data.length; i++) {
                    tr = $('<tr/>');
                    tr.append("<td class='col-lg-2' style='text-align:center;'>" + data[i].transaction_id + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].insert_date + "</td>");
                    tr.append("<td class='col-lg-2' >" + data[i].delivery_name + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_city + "</td>");
                    tr.append("<td class='col-lg-2'>" + data[i].delivery_state + "</td>");

                    $('#myModalRegion').modal('show').find('.tb').append(tr);

                    $('.downloadRegion').attr('data-status', status);
                    $('.downloadRegion').attr('data-month', month);

                    $('#myModalRegion').on('hidden.bs.modal', function () {

                        $('#myModalRegion').remove();
                         var myClone = originalModal.clone();
                        $('body').append(myClone);
                        
                    })                
                }           
                
            },          
        })

    });

    $('body').on('click', '.download', function(){

        var status = $(this).attr('data-status');
        var endDate = $(this).attr('data-endDate');
        var startDate = $(this).attr('data-startDate');
        var month = $(this).attr('data-month');

        if (status!='') {
            window.location = "/jlogistic/dashboardcsv?status="+status;

        }

        if (status!='' && startDate!='' && endDate!='') {
             window.location = "/jlogistic/dashboardcsv?status="+status+"&startDate="+startDate+"&endDate="+endDate;
        }

        if (month!='') {
            window.location = "/jlogistic/dashboardcsv?status="+status+"&month="+month;
        }

    });

    $('body').on('click', '#downloadRegion', function(){

        var status = $(this).attr('data-status');
        var endDate = $(this).attr('data-endDate');
        var startDate = $(this).attr('data-startDate');
        var month = $(this).attr('data-month');

        if (status!='') {
            window.location = "/jlogistic/dashboardcsvregion?status="+status;

        }

        if (status!='' && startDate!='' && endDate!='') {
            window.location = "/jlogistic/dashboardcsvregion?status="+status+"&startDate="+startDate+"&endDate="+endDate;
        }

        if (month!='') {
            window.location = "/jlogistic/dashboardcsvregion?status="+status+"&month="+month;
        }

    });

});

</script>
<script>
    
    var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    getStatistic(0,1,startDate,toDate);
    
    
    $(".date-range").click(function(){
        $("#selected-date-range").html($(this).html());
        $('#type-select').val($(this).attr("data-type"))
        
        if($('#type-select').val() == 1){
            var type = $('#type-select').val();
            var startDate = $('#start-today').val();
            var toDate = $('#start-today').val();
            getStatistic(0,type,startDate,toDate);
        }
        
        if($('#type-select').val() == 2){
            var type = $('#type-select').val();
            var startDate = $('#start-weekly-date').val();
            var toDate = $('#end-weekly-date').val();
            getStatistic(0,type,startDate,toDate);
        }
        
        if($('#type-select').val() == 3){
            
            var type = $('#type-select').val();
            var startDate = $('#start-month-date').val();
            var toDate = $('#end-month-date').val();
            getStatistic(0,type,startDate,toDate);
            
        }
    });
    
    $("#nav-left").click(function(){
        console.log('LEFT');
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        getStatistic(1,type,startDate,toDate);
        //$("#selected-date-range").html($(this).html());
    });
    
    $("#nav-right").click(function(){
        console.log('RIGHT');
        console.log('LEFT');
        var type = $('#type-select').val();
        var startDate = $('#start-date-select').val();
        var toDate = $('#to-date-select').val();
        getStatistic(2,type,startDate,toDate);
        //$("#selected-date-range").html($(this).html());
    });
    
    function getStatistic(navigation,type,startDate,toDate){
        // alert('PL');
        var product_campaign_id = product_campaign_id;
        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardstatistic",
            dataType:'json',
            data: {
                'navigation':navigation,
                'rangeType':type,
                'startDate':startDate,
                'toDate':toDate
            },
            beforeSend: function(){
                
            },
            success: function(data) {
                
//                console.log($("#total-logistic-sending").html(data.data.TotalStatistic.TransactionLogistic.Sending));
                
                console.log(data.data.TotalStatistic.TransactionLogistic.Sending);
                
                $("#total-logistic-pending").html(data.data.TotalStatistic.TransactionLogistic.pending);
                $("#total-logistic-sending2").html(data.data.TotalStatistic.TransactionLogistic.sending);
                $("#total-logistic-undelivered").html(data.data.TotalStatistic.TransactionLogistic.Undelivered);
                $("#total-logistic-partial").html(data.data.TotalStatistic.TransactionLogistic.Partial);
                $("#total-logistic-returned").html(data.data.TotalStatistic.TransactionLogistic.Returned);
                $("#total-logistic-sent").html(data.data.TotalStatistic.TransactionLogistic.Sent);

                $("#total-logistic-pending-date").html(data.data.TotalStatistic.TransactionLogisticAll.pending);
                $("#total-logistic-sending-date").html(data.data.TotalStatistic.TransactionLogisticAll.sending);
                $("#total-logistic-undelivered-date").html(data.data.TotalStatistic.TransactionLogisticAll.Undelivered);
                $("#total-logistic-partial-date").html(data.data.TotalStatistic.TransactionLogisticAll.Partial);
                $("#total-logistic-returned-date").html(data.data.TotalStatistic.TransactionLogisticAll.Returned);
                $("#total-logistic-sent-date").html(data.data.TotalStatistic.TransactionLogisticAll.Sent);

                // Region

                $("#total-logistic-pending-region").html(data.data.TotalStatistic.TransactionLogisticRegion.pending);
                $("#total-logistic-sending2-region").html(data.data.TotalStatistic.TransactionLogisticRegion.sending);
                $("#total-logistic-undelivered-region").html(data.data.TotalStatistic.TransactionLogisticRegion.Undelivered);
                $("#total-logistic-partial-region").html(data.data.TotalStatistic.TransactionLogisticRegion.Partial);
                $("#total-logistic-returned-region").html(data.data.TotalStatistic.TransactionLogisticRegion.Returned);
                $("#total-logistic-sent-region").html(data.data.TotalStatistic.TransactionLogisticRegion.Sent);

                $("#total-logistic-pending-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.pending);
                $("#total-logistic-sending-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.sending);
                $("#total-logistic-undelivered-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Undelivered);
                $("#total-logistic-partial-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Partial);
                $("#total-logistic-returned-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Returned);
                $("#total-logistic-sent-date-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Sent);
                
                var pendingPlace = data.data.TotalStatistic.TransactionLogisticAll.pendingPlace;
                $("#total-logistic-pending-date-place").html(pendingPlace.length);
                var strPendingPlace = '';
                $.each( pendingPlace, function( key, value ) {
                    
                    strPendingPlace = strPendingPlace + '<tr><td style="text-align:left;">'+value.location+'</td><td style="text-align:left;">'+value.delivery_city+'</td><td style="text-align:left;">'+value.delivery_state+'</td><td style="width:40px;">'+value.total_order+'</td></tr> ';
                });

                $("#list-pending-all-place").html(strPendingPlace);

                var pendingPlaceRegion = data.data.TotalStatistic.TransactionLogisticAllRegion.pendingPlaceRegion;
                $("#total-logistic-pending-date-place-region").html(pendingPlaceRegion.length);
                var strPendingPlaceRegion = '';
                $.each( pendingPlaceRegion, function( key, value ) {
                    
                    strPendingPlaceRegion = strPendingPlaceRegion + '<tr><td style="text-align:left;">'+value.location+'</td><td style="text-align:left;">'+value.delivery_city+'</td><td style="text-align:left;">'+value.delivery_state+'</td><td style="width:40px;">'+value.total_order+'</td></tr> ';
                });
                $("#list-pending-all-place-region").html(strPendingPlaceRegion);
                
                var sendingPlace = data.data.TotalStatistic.TransactionLogisticAll.sendingPlace;
                $("#total-logistic-sending-date-place").html(sendingPlace.length);
                
                var strSendingPlace = '';
                $.each( sendingPlace, function( key1, value1 ) {
                    
                    strSendingPlace = strSendingPlace + '<tr><td style="text-align:left;">'+value1.location+'</td><td style="text-align:left;">'+value1.delivery_city+'</td><td style="text-align:left;">'+value1.delivery_state+'</td><td style="width:40px;">'+value1.total_order+'</td></tr> ';
                });
                
                $("#list-sending-all-place").html(strSendingPlace);


                var sendingPlaceRegion = data.data.TotalStatistic.TransactionLogisticAllRegion.sendingPlaceRegion;
                $("#total-logistic-sending-date-place-region").html(sendingPlaceRegion.length);
                
                var strSendingPlaceRegion = '';
                $.each( sendingPlaceRegion, function( key1, value1 ) {
                    
                    strSendingPlaceRegion = strSendingPlaceRegion + '<tr><td style="text-align:left;">'+value1.location+'</td><td style="text-align:left;">'+value1.delivery_city+'</td><td style="text-align:left;">'+value1.delivery_state+'</td><td style="width:40px;">'+value1.total_order+'</td></tr> ';
                });
                
                $("#list-sending-all-place-region").html(strSendingPlaceRegion);

                
                $("#total-batch-sending").html(data.data.TotalStatistic.batchLogistic.sending);
                $("#total-batch-processing").html(data.data.TotalStatistic.batchLogistic.processing);
                $("#total-batch-completed").html(data.data.TotalStatistic.batchLogistic.completed);

                //Region
                $("#total-batch-sending-region").html(data.data.TotalStatistic.batchLogisticRegion.sending);
                $("#total-batch-processing-region").html(data.data.TotalStatistic.batchLogisticRegion.processing);
                $("#total-batch-completed-region").html(data.data.TotalStatistic.batchLogisticRegion.completed);
                
                $("#start-date-select").val(data.data.DateSelection.startDate);
                $("#to-date-select").val(data.data.DateSelection.toDate);
                
                if($('#type-select').val() == 1){
                   $("#selected-date").html(data.data.DateSelection.displayStartDate); 
                }else{
                    $("#selected-date").html(data.data.DateSelection.displayStartDate+ ' - '+ data.data.DateSelection.displayEndDate); 
                }
                
                $("#t-d-1month-pending").html(data.data.TotalStatistic.TransactionLogisticAll.Pending1Month);
                $("#t-d-2month-pending").html(data.data.TotalStatistic.TransactionLogisticAll.Pending2Month);
                $("#t-d-3month-pending").html(data.data.TotalStatistic.TransactionLogisticAll.Pending3Month);
                
                $("#t-d-1month-sending").html(data.data.TotalStatistic.TransactionLogisticAll.Sending1Month);
                $("#t-d-2month-sending").html(data.data.TotalStatistic.TransactionLogisticAll.Sending2Month);
                $("#t-d-3month-sending").html(data.data.TotalStatistic.TransactionLogisticAll.Sending3Month);
                
                $("#t-d-1month-return").html(data.data.TotalStatistic.TransactionLogisticAll.Returned1Month);
                $("#t-d-2month-return").html(data.data.TotalStatistic.TransactionLogisticAll.Returned2Month);
                $("#t-d-3month-return").html(data.data.TotalStatistic.TransactionLogisticAll.Returned3Month);

                //Region
                $("#t-d-1month-pending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Pending1Month);
                $("#t-d-2month-pending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Pending2Month);
                $("#t-d-3month-pending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Pending3Month);
                
                $("#t-d-1month-sending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Sending1Month);
                $("#t-d-2month-sending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Sending2Month);
                $("#t-d-3month-sending-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Sending3Month);
                
                $("#t-d-1month-return-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Returned1Month);
                $("#t-d-2month-return-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Returned2Month);
                $("#t-d-3month-return-region").html(data.data.TotalStatistic.TransactionLogisticAllRegion.Returned3Month);
                
                $("#start-weekly-date").val(data.data.DateSelection.WeeklyStartDate);
                $("#end-weekly-date").val(data.data.DateSelection.WeeklyEndDate);
                $("#start-month-date").val(data.data.DateSelection.MonthStartDate);
                $("#end-month-date").val(data.data.DateSelection.MonthEndDate);
                
                <?php if($region_id==0){ ?>
                    loadDriverChart(data.data.TotalStatistic.driverBatch.TotalBatchSentGrouping);
                <?php } else{ ?>               
                    loadDriverChartRegion(data.data.TotalStatistic.driverBatchRegion.TotalBatchSentGroupingRegion);
                <?php } ?>
                
                /*
                 *   <input type="text" id="start-weekly-date" value="">
                    <input type="text" id="end-weekly-date" value="">
                    <input type="text" id="start-month-date" value="">
                    <input type="text" id="end-month-date" value="">
                 */
                
            },
            error: function (error) {
                location.reload();
            }
        })
    }
    
    function loadDriverChart(TotalBatchSentGrouping){
        $("#myChart").html('')
        var batch = [{  y: '-', a: 0,6: 0 }];
        if(TotalBatchSentGrouping.length > 0 ){
            var batch = [];
        }
        $.each( TotalBatchSentGrouping, function( key, value ) {
            batch.push({  y: value.driver, a: value.sent, b: value.assign, c: value.return  });
            
        });
        // Morris.Bar({
        //     element: 'myChart',
        //     data: batch,
        //     barGap:4,
        //     barSizeRatio:0.30,
        //     barColors: ['#428bca','#1CAF9A','#D9534F'],
        //     hideHover: 'auto',
        //     xkey: 'y',
        //     ykeys: ['a','b','c'],
        //     labels: ['Batch Sent','Batch Assigned','Batch Returned'],
        // });
        
    } 

    function loadDriverChartRegion(TotalBatchSentGroupingRegion){
        $("#myChartRegion").html('')
        var batchRegion = [{  y: '-', a: 0,6: 0 }];
        if(TotalBatchSentGroupingRegion.length > 0 ){
            var batchRegion = [];
        }
        $.each( TotalBatchSentGroupingRegion, function( key, value ) {
            batchRegion.push({  y: value.driver, a: value.sent, b: value.assign, c: value.return  });
            
        });
        // Morris.Bar({
        //     element: 'myChartRegion',
        //     data: batchRegion,
        //     barGap:4,
        //     barSizeRatio:0.30,
        //     barColors: ['#428bca','#1CAF9A','#D9534F'],
        //     hideHover: 'auto',
        //     xkey: 'y',
        //     ykeys: ['a','b','c'],
        //     labels: ['Batch Sent','Batch Assigned','Batch Returned'],
        // });
        
    } 
    

//var ctx = document.getElementById("myChart");
//var myChart = new Chart(ctx, {
//    type: 'bar',
//    data: {
//        labels: ["Amin", "Fakhrur", "Ahmad"],
//        datasets: [{
//            label: 'Total Sent',
//            fillColor: "#79D1CF",
//            strokeColor: "#79D1CF",
//            data: [[60,40], [60,40], [60,40]],
//           // data: [12, 19, 3, 5, 2, 3],
//            backgroundColor: 
//                'rgba(75, 192, 192, 0.2)'
//            ,
//            borderColor: 
//                'rgba(75, 192, 192, 1)'
//            ,
//            borderWidth: 1
//        }]
//    },
//    options: {
//        scales: {
//            yAxes: [{
//                ticks: {
//                    beginAtZero:true
//                }
//            }]
//        }
//    }
//});


function generateTotalBatchChart(labelListSet,dataListSet){
    
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Amin", "Fakhrur", "Ahmad"],
            datasets: [{
                label: 'Total Sent',
                fillColor: "#79D1CF",
                strokeColor: "#79D1CF",
                data: [[60,40], [60,40], [60,40]],
               // data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 
                    'rgba(75, 192, 192, 0.2)'
                ,
                borderColor: 
                    'rgba(75, 192, 192, 1)'
                ,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
}

function generateTotalBatchChartRegion(labelListSet,dataListSet){
    
    var ctx = document.getElementById("myChartRegion");

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Amin", "Fakhrur", "Ahmad"],
            datasets: [{
                label: 'Total Sent',
                fillColor: "#79D1CF",
                strokeColor: "#79D1CF",
                data: [[60,40], [60,40], [60,40]],
               // data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 
                    'rgba(75, 192, 192, 0.2)'
                ,
                borderColor: 
                    'rgba(75, 192, 192, 1)'
                ,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
}

</script>
@stop

