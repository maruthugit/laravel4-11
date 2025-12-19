@extends('layouts.master')

@section('title') Status Summary @stop

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
            <h1 class="page-header"><i class="fa fa-home fa-fw"></i> Status Summary</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

<?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'wira', 'maruthu', 'kaijie'), true ) ) {  ?>


<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-default d-panel" style="margin-bottom: 0">
            <div class="panel-heading">Transaction</div>
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2"><p>Total Transaction</p></div>
                    <div class="col-xs-2" id="total_transaction"></div>
                </div>
                
                <div class="row">
                    <div class="col-xs-2">Total Cancelled</div>
                    <div class="col-xs-2" id="total_cancelled"></div>
                    <div class="col-xs-2">Total Refund</div>
                    <div class="col-xs-2" id="total_refund"></div>
                    <div class="col-xs-2">Total Pending</div>
                    <div class="col-xs-2" id="total_pending"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-default d-panel" style="margin-bottom: 0">
            <div class="panel-heading">Summary</div>
            
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
                <table id="summary" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Platform</th>
                            <th>Cancellation</th>
                            <th>Refund</th>
                            <th>Pending</th>
                        </tr>
                    </thead>
                </table>
                <div id="chart" style=""></div>
            </div>
        </div>
    </div>
</div>



 <?php } else{ ?>
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
    
    // Change period type 
    var startDate = $('#start-today').val();
    var toDate = $('#start-today').val();
    loadTable(0, 2, '', ''); // default weekly


    // Change period type driver
    $(".date-range-driver").click(function(){

        $("#selected-date-range-driver").html($(this).html());
        $('#type-select-driver').val($(this).attr("data-type-driver"));
        
        if($('#type-select-driver').val() == 1){
            var type = $('#type-select-driver').val();
            var startDate = $('#start-today-driver').val();
            var toDate = $('#start-today-driver').val();
            loadTable(0,type,startDate,toDate);
        }
        
        if($('#type-select-driver').val() == 2){
            var type = $('#type-select-driver').val();
            var startDate = $('#start-weekly-date-driver').val();
            var toDate = $('#end-weekly-date-driver').val();
            loadTable(0,type,startDate,toDate);
        }
        
        if($('#type-select-driver').val() == 3){
            
            var type = $('#type-select-driver').val();
            var startDate = $('#start-month-date-driver').val();
            var toDate = $('#end-month-date-driver').val();
            loadTable(0,type,startDate,toDate);
        }
    });

    $("#nav-left-driver").click(function(){
        var type = $('#type-select-driver').val();
        var startDate = $('#start-date-select-driver').val();
        var toDate = $('#to-date-select-driver').val();
        loadTable(1,type,startDate,toDate);
    });
    
    $("#nav-right-driver").click(function(){
        var type = $('#type-select-driver').val();
        var startDate = $('#start-date-select-driver').val();
        var toDate = $('#to-date-select-driver').val();
        loadTable(2,type,startDate,toDate);
    });
    

    function loadTable(navigation,type,startDate,toDate){
        $.ajax({
                method: "POST",
                url: "/transaction/summarytable",
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
                    console.log(data);
                    console.log(data.summary);
                    $('#total_transaction').html('<strong>' + data.total_transaction + '</strong>');
                    $('#total_cancelled').html('<strong>' + data.total_cancelled + ' ' + data.total_cancelled_percent) + '</strong>';
                    $('#total_refund').html('<strong>' + data.total_refund + ' ' + data.total_refund_percent + '</strong>');
                    $('#total_pending').html('<strong>' + data.total_pending + ' ' + data.total_pending_percent + '</strong>');

                    $("#chart").html('');
                    $("tbody").remove();
                    var table = document.getElementById('summary');

                    var chartData = [];
                    for (let [key, value] of Object.entries(data.summary)) {
                        //console.log(key);
                        //console.log(value.total);
                        var tbody = document.createElement('tbody');
                        var tr = document.createElement('tr');
                        var tdplatform = document.createElement('td');
                        var platform = document.createTextNode(key);
                        var tdcancelled = document.createElement('td');
                        var cancelled = document.createTextNode(value.cancelled + ' / ' + value.total + ' (' + value.cancelled_percent + '%)');
                        var tdrefund = document.createElement('td');
                        var refund = document.createTextNode(value.refund + ' / ' + value.total + ' (' + value.refund_percent + '%)');
                        var tdpending = document.createElement('td');
                        var pending = document.createTextNode(value.pending + ' / ' + value.total + ' (' + value.pending_percent + '%)');

                        tdplatform.appendChild(platform);
                        tdcancelled.appendChild(cancelled);
                        tdrefund.appendChild(refund);
                        tdpending.appendChild(pending);
                        tr.appendChild(tdplatform);
                        tr.appendChild(tdcancelled);
                        tr.appendChild(tdrefund);
                        tr.appendChild(tdpending);
                        tbody.appendChild(tr);
                        table.appendChild(tbody);
                        
                        chartData.push({  y: key, a: value.cancelled_percent, b: value.refund_percent, c: value.pending_percent  });
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

                    Morris.Bar({
                      element: 'chart',
                      data: chartData,
                      barGap: 4,
                      barColors: ['#428bca','#1CAF9A','#D9534F'],
                      xkey: 'y',
                      ykeys: ['a', 'b', 'c'],
                      labels: ['Cancelled Percent','Refund Percent','Pending Percent'],
                      horizontal: true,
                    });
            }
        })
        
    }


      });
</script>
@stop