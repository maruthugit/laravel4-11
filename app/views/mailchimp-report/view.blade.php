@extends('layouts.master')
@section('title', 'Mailchimp Campaign Report')
@section('extra-css')

<style>
    #page-wrapper {
        font-family: "Graphik Web","Helvetica Neue",Helvetica,Arial,Verdana,sans-serif;
    }
    .h1 {
        font-family: "Cooper Light",Georgia,Times,"Times New Roman",serif;
    }
    .card-body {
        padding-left: 20px;
        /*border:1px solid #484848 !important;*/
    }
    .row-data {
        padding: 10px;
        margin-right: 0px;
        margin-left: -20px;
    }
    .report-font-title {
        margin-top: 10px;
        padding-top: 10px;
        margin-bottom: 10px;
        font-size: 23px;
    }
    .report-font {
        font-size: 18px;
    }
    .report-font-bold {
        font-size: 28px;
        margin-left: 20px !important;
    }
    .item {
        border:1px solid #ddd !important;
    }
    .link-clicks {
        padding-top: 10px;
        padding-right: 10px;
        padding-bottom: 10px;
        border-bottom:1px solid #ddd !important;
    }
</style>
@stop
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Mailchimp Campaign Report
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
                <h1 class="h1">{{$report->campaign_title}}</h2>
                <br>
                <h4>Subject: {{$report->subject_line}}</h4>
                <h4>Delivered: {{date_format(date_create($report->send_time), 'D, j F Y g:i A')}}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
                <div class="row row-data item">
                    <div class="col-lg-4">
                        <span class="report-font">Total deliveries</span>
                        <span class="report-font-bold">{{$report->emails_sent}}</span>
                    </div>
                    <div class="col-lg-4">
                        <span class="report-font">Total opens</span>
                        <span class="report-font-bold">{{$report->opens->opens_total}}</span>
                    </div>
                    <div class="col-lg-4">
                        <span class="report-font">Total unique opens</span>
                        <span class="report-font-bold">{{$report->opens->unique_opens}}</span>
                    </div>
                </div>
                <div class="row row-data">
                    <div class="col-lg-4">
                        <span class="report-font">Bounced</span>
                        <span class="report-font-bold">{{$report->bounces->hard_bounces + $report->bounces->soft_bounces + $report->bounces->syntax_errors}}</span>
                    </div>
                    <div class="col-lg-4">
                        <span class="report-font">Total Clicks</span>
                        <span class="report-font-bold">{{$report->clicks->clicks_total}}</span>
                    </div>
                    <div class="col-lg-4">
                        <span class="report-font">Total unsubscribed</span>
                        <span class="report-font-bold">{{$report->unsubscribed}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
              <div class="report-font-title">24-hour performance</div>
              <canvas id="lineChart" height="80" style="padding-right: 20px"></canvas>
            </div>
        </div>     
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
                <div class="report-font-title">Top 5 links clicked</div>
                <div class="top-5-clicks"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
                <div class="report-font-title">Top 5 subscribers opens the most</div>
                <div class="top-5-opens"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default card-body">
                <div class="report-font-title">Top 5 locations by opens</div>
                <div class="top-5-locations"></div>
            </div>
        </div>
    </div>
</div>

@stop

{{ HTML::script('jdboard-v2/vendor/chart.js/Chart.min.js') }}
@section('script')



$.ajax({
    url:'/mailchimp-report/timeseries/<?php echo $report->id ?>',
    type:'GET',
    dataType:'json',
    beforeSend: function(){
        $(".comparison-loader").show();
    },
    success:function(data){
        $(".comparison-loader").hide();


        var platform_comparisons = data;

        $('.platform-compare-lbl').html('Platform Comparison (' + platform_comparisons.month + ')');

        var times = data.map(function (el) { return el.timestamp; });

        var openCount = data.map(function (el) { return el.unique_opens; });
        var clickCount = data.map(function (el) { return el.recipients_clicks; });
                    

        var legendState = true;
        if ($(window).outerWidth() < 576) {
            legendState = false;
        }

        var LINECHART = $('#lineChart');
                        
        var myLineChart = new Chart(LINECHART, {
            type: 'line',
            options: {
                scales: {
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: "black",
                            //fontSize: 24
                        }
                    }],
                    yAxes: [{
                        stacked: false,
                        display: true,
                        gridLines: {
                            display: true,
                            // lineWidth: 50
                            //color: "rgba(0, 0, 0, 0.6)"
                        },
                        ticks: {
                            fontColor: "black",
                            //fontSize: 24,
                            min: 0
                        }
                    }]
                },
                legend: {
                    display: true,
                    align: 'end',
                    labels: {
                        //fontColor: "black",
                        //fontSize: 20,
                        //fontWeight: 600
                    }
                }
            },
            data: {
                labels: times,
                datasets: [
                    {
                        label: "Opens",
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#0054A8",
                        borderColor: "#0054A8",
                        pointHoverBackgroundColor: "#0054A8",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        //borderWidth: 1,
                        pointBorderColor: "#0054A8",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 4,
                        pointHoverRadius: 5,
                        pointHoverBorderColor: "#fff",
                        pointHoverBorderWidth: 4,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: openCount,
                        spanGaps: false
                    },
                    {
                        label: "Clicks",
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#E433FF",
                        borderColor: "#E433FF",
                        pointHoverBackgroundColor: "#E433FF",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        //borderWidth: 4,
                        pointBorderColor: "#E433FF",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 4,
                        pointHoverRadius: 5,
                        pointHoverBorderColor: "#fff",
                        pointHoverBorderWidth: 4,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: clickCount,
                        spanGaps: false
                    },
                ]
            }
        });
    }
});

$.ajax({
    url:'/mailchimp-report/clicks/<?php echo $report->id ?>',
    type:'GET',
    dataType:'json',
    beforeSend: function(){
        $(".comparison-loader").show();
    },
    success:function(data){
        console.log(data);

        for (var i = 0; i < data.length; i++) {
            $('.top-5-clicks').append('<div class="link-clicks"><a class="report-font" href="'+data[i].url+'" target="_blank">'+data[i].url+'</a><span class="report-font" style="float: right">'+data[i].unique_clicks+'</span></div>');
        }
        
    }
});

$.ajax({
    url:'/mailchimp-report/opens/<?php echo $report->id ?>',
    type:'GET',
    dataType:'json',
    beforeSend: function(){
        $(".comparison-loader").show();
    },
    success:function(data){
        console.log(data);

        for (var i = 0; i < data.length; i++) {
            $('.top-5-opens').append('<div class="link-clicks"><a class="report-font">'+data[i].email_address+'</a><span class="report-font" style="float: right">'+data[i].opens_count+'</span></div>');
        }
        
    }
});

$.ajax({
    url:'/mailchimp-report/locations/<?php echo $report->id ?>',
    type:'GET',
    dataType:'json',
    beforeSend: function(){
        $(".comparison-loader").show();
    },
    success:function(data){
        console.log(data);

        for (var i = 0; i < data.length; i++) {
            $('.top-5-locations').append('<div class="link-clicks"><a class="report-font">'+data[i].region_name+'</a><span class="report-font" style="float: right">'+data[i].opens+'</span></div>');
        }
        
    }
});


@stop