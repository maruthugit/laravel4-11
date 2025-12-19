@extends('layouts.master')
@section('title', 'Report Template')
@section('content')

<?php
$tempcount = 1;

?>
<!-- For datepicker in create new report -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>


<script src="js/amcharts.js"></script>
<script src="js/pie.js"></script>
<script src="js/serial.js"></script>
<script src="js/export.js"></script>
<script src="js/export.min.js"></script>

<script src="js/vfs_fonts.js"></script>
<script src="js/xlsx.min.js"></script>
<script src="js/jszip.min.js"></script>
<script src="js/FileSaver.min.js"></script>

<script src="js/fabric.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js.map"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<link rel="stylesheet" href="css/export.css" type="text/css" media="all" />
<script src="js/light.js"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

<link href="https://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css">

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });

     $(function() {
        $( "#datepicker3" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker4" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>


<style>
#platformchart {
  width: 100%;
  height: 450px;
}

#statuschart {
  width: 100%;
  height: 450px;
}

#dailychart {
  width: 100%;
  height: 450px;
}

#statechart {
  width: 100%;
  height: 450px;
}

#zipcodechart {
  width: 100%;
  height: 450px;
}

#productschart {
  width: 100%;
  height: 450px;
}

#monthlychart {
  width: 100%;
  height: 450px;
}

#quaterlychart {
  width: 100%;
  height: 450px;
}

#regionchart {
  width: 100%;
  height: 450px;
}

#chartdiv {
  width: 100%;
  height: 500px;
}
</style>

<style>
     .loading {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999999;
        /*background: #3d464d;*/
        background: #FFF;
        opacity: 1.00;
        display: none;
        }
    .loading #load-message {
        width: 40px;
        height: 40px;
        position: absolute;
        left: 50%;
        right: 50%;
        bottom: 50%;
        top: 50%;
        margin: -20px;
    }
</style>

<div class="loading"><span id="load-message"></span></div>

<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Report Template </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message') OR $message)
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }} {{ $message }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success') OR $success)
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }} {{ $success }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Filter Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}

                        <div class="form-group">
                            {{ Form::label('type_report', 'Filter', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <select class="form-control" id="type_report" name="type_report">
                                    <option value="1">Transaction Report</option>
                                    <option value="2">Product Report</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select class="form-control" id="type_period" name="type_period">
                                    <option value="1">Monthly</option>
                                    <option value="2">Quaterly / Yearly</option>
                                    <!-- <option value="3">Yearly</option> -->
                                </select>
                            </div>
                            <div class="col-lg-2">
                                {{Form::text('created_from', date('Y-m-d', strtotime("yesterday")), array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 {{Form::text('created_to', date('Y-m-d'), array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 <!-- <input type="checkbox" name="is_checked" id="checkbox" value="1" /> Compare -->
                                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#filter-panel">
                                    <span class="glyphicon glyphicon-cog"></span> Advanced
                                </button>

                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                            {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{Form::text('datepicker3', date('Y-m-d', strtotime("yesterday")), array('id'=>'datepicker3', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 {{Form::text('datepicker4', date('Y-m-d'), array('id'=>'datepicker4', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>

                        </div>
                    <div  class="">
                        <div class="form-group collapse filter-panel" id="filter-panel">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-2">
                                <select class="form-control" id="supplier" name="supplier">
                                    <option value=""  selected>Filter by Supplier</option>
                                    <?php foreach ($seller as $key => $value) { ?>
                                   <option value="<?php echo $value->id; ?>" ><?php echo $value->company_name; ?></option>
                                   <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select class="form-control" id="category" name="category">
                                    <option value=""  selected>Filter by Category</option>
                                    <?php foreach ($category as $key => $value) { ?>
                                   <option value="<?php echo $value->id; ?>" ><?php echo $value->category_name." [ID:".$value->id."]"; ?></option>
                                   <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-2" id="transColumn">
                                <input id="transID" type="text" class="form-control" name="transID" placeholder="Filter by Transaction ID">
                                <span class="help-block">Multiple ID separated by comma.</span>
                            </div>
                            <div class="col-lg-2">
                                <input id="product" type="text" class="form-control" name="product" placeholder="Filter by Product ID">
                                <span class="help-block">Multiple product separated by comma.</span>
                            </div>
                            
                        </div>
                        
                    </div>
                        <center>
                            <div class="col-lg-4">                                
                                <a class="btn btn-success" id="generate">Generate</a>
                            </div>
                        </center>
                        
                    </div>
                    {{ Form::close() }}
                    <hr />
                        
                </div>
            </div>
                <!-- /.panel-body -->
            <div class="panel panel-default" style="margin-top:20px;display: none;" id="main">
                <div></div><br/>
                <div class="col-lg-12">
                                 <!-- <input type="checkbox" name="is_checked" id="checkbox" value="1" /> Compare -->
                    <input type="button" value="Export charts to PDF" onclick="exportCharts();" class="btn btn-primary pull-right" />
                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div id="datetext"></div>
                            <div id="suppliertext"></div>
                            <div id="transIDtext"></div>
                            <div id="producttext"></div>
                            <div id="categorytext"></div>
                        </div>
                        <div id="date"></div>
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #9C27B0;padding: 10px;color: white;">Platform <button data-toggle="collapse" data-target="#tableplatformtoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i></button> </div><br>
                                    <div class="row">
                                        <div id="platformchart" class="chartdiv"></div>
                                    </div>
                                    <div id="tableplatformtoggle" class="collapse tableplatformtoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Platform</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tableplatform"></tbody>
                                       </table>
                                    </div>
                            </div>
                        </div>

                    <!-- status -->
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #4CAF50;padding: 10px;color: white;">Status 
                                    <button data-toggle="collapse" data-target="#tablestatustoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button> </div><br>
                                <div class="row">
                                    <div id="statuschart" class="chartdiv"></div>
                                </div>  
                                <div id="tablestatustoggle" class="collapse tablestatustoggle">
                                    <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Status</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tablestatus"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <!-- day-to-day -->
                        <div class="col-md-12" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #00BCD4;padding: 10px;color: white;"> Day-To-Day
                                    <button data-toggle="collapse" data-target="#tabledailytoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                <div class="row">
                                    <div id="dailychart" class="chartdiv"></div>    
                                </div> 
                                <div id="tabledailytoggle" class="collapse tabledailytoggle">
                                    <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Days</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tabledaily"></tbody>
                                    </table>
                                </div>                         
                            </div>
                        </div>

                    <!-- state -->
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #2196F3;padding: 10px;color: white;">State
                                    <button data-toggle="collapse" data-target="#tablestatetoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="statechart" class="chartdiv"></div>    
                                    </div> 
                                    <div id="tablestatetoggle" class="collapse tablestatetoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>State</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tablestate"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>

                    <!-- zipcode -->
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #009688;padding: 10px;color: white;">Zipcode
                                    <button data-toggle="collapse" data-target="#tablezipcodetoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="zipcodechart" class="chartdiv"></div>
                                    </div>
                                    <div id="tablezipcodetoggle" class="collapse tablezipcodetoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Zipcode</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tablezipcode"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>

                    <!-- products -->
                        <div class="col-md-12" style="margin-top: 15px;" id="productscolumn">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #673AB7;padding: 10px;color: white;">Products
                                    <button data-toggle="collapse" data-target="#tableproducttoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="productschart" class="chartdiv"></div>
                                    </div>
                                    <div id="tableproducttoggle" class="collapse tableproducttoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Product Name</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tableproduct"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>

                        <!-- monthly -->
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #2196F3;padding: 10px;color: white;">Monthly
                                    <button data-toggle="collapse" data-target="#tablemonthlytoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="monthlychart" class="chartdiv"></div>    
                                    </div> 
                                    <div id="tablemonthlytoggle" class="collapse tablemonthlytoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Month</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tablemonthly"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>

                        <!-- region -->
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #FF5722;padding: 10px;color: white;">Region
                                    <button data-toggle="collapse" data-target="#tableregiontoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="regionchart" class="chartdiv"></div>    
                                    </div> 
                                    <div id="tableregiontoggle" class="collapse tableregiontoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Region</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tableregion"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>

                        <!-- quaterly -->
                        <div class="col-md-12" style="margin-top: 15px;" id="quaterlycolumn">
                            <div class="col-md-12" style="border: 1px solid #cec9c9;border-radius: 7px;">
                                <div class="row" style="background: #2196F3;padding: 10px;color: white;">Quaterly
                                    <button data-toggle="collapse" data-target="#tablequaterlytoggle" class="btn btn-default pull-right"> View More <i class="fa fa-caret-down"></i> </button></div><br>
                                    <div class="row">
                                        <div id="quaterlychart" class="chartdiv"></div>    
                                    </div> 
                                    <div id="tablequaterlytoggle" class="collapse tablequaterlytoggle">
                                        <table class="table table-responsive table-striped">
                                              <thead>
                                                  <tr>
                                                     <th>Quater</th>
                                                     <th>Total Revenue</th>
                                                  </tr>
                                               </thead>
                                               <tbody id="tablequaterly"></tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
            <!-- /.panel -->
    </div>
        <!-- /.col-lg-12 -->
</div>
</div>

<script>
        $(function () {
        $('input[name="datepicker3"]').hide();
        $('input[name="datepicker4"]').hide();

        //show it when the checkbox is clicked
        $('input[name="is_checked"]').on('click', function () {
            if ($(this).prop('checked')) {
                $('input[name="datepicker3"]').fadeIn();
                $('input[name="datepicker4"]').fadeIn();
            } else {
                $('input[name="datepicker3"]').hide();
                $('input[name="datepicker4"]').hide();
            }
        });
    });

        $(function() {
            $('#transColumn').hide(); 
            $('#type_report').change(function(){
                if($('#type_report').val() == 1) {
                    $('#transColumn').hide(); 
                } else {
                    $('#transColumn').hide(); 
                } 
            });
        });


        $("#generate").click(function(){
        
        var datepicker = $('#datepicker').val();
        var datepicker2 = $('#datepicker2').val();

        var type_period = $('#type_period').val();

        var date1=new Date(datepicker);//Remember, months are 0 based in JS
        var date2=new Date(datepicker2);
        var year1=date1.getFullYear();
        var year2=date2.getFullYear();
        var month1=date1.getMonth();
        var month2=date2.getMonth();

        var numberOfMonths = (year2 - year1) * 12 + (month2 - month1);

        var supplier = $('#supplier').val();
        var type_report = $('#type_report').val();

        var type_period = $('#type_period').val();

        var category = $('#category').val();
        var transID = $('#transID').val();
        var product = $('#product').val();

        var is_checked = $("#checkbox").prop('checked') == true ? 1: 0; 

        console.log(numberOfMonths);

        if (type_period == 1) {

            if (numberOfMonths >= 4) {
              alert('Date cant be more than 3 months!');
            }else{
                getStatistic(datepicker,datepicker2, is_checked, supplier,type_report,category,transID,product, type_period);
                $('#quaterlycolumn').hide();
            }
            // $('#quaterlycolumn').hide(); 
        }else{

            if (numberOfMonths <= 2 ) {
              alert('This selection is not best suitable for quarterly/yearly report. Please choose monthly report instead.');
            }else{

              getStatistic(datepicker,datepicker2, is_checked, supplier,type_report,category,transID,product, type_period);
              $('#quaterlycolumn').show(); 
            }
            
        }

        if (type_report == 1) {
          $('#productscolumn').hide(); 
        }else{
          $('#productscolumn').show(); 
        }

    });

        function getStatistic(startDate,toDate,is_checked,supplier,type_report,category,transID,product,type_period){

        $.ajax({
            method: "POST",
            url: "/reporttemplate/generate",
            dataType:'json',
            data: {
                'type_report':type_report,
                'created_from':startDate,
                'created_to':toDate,
                'is_checked':is_checked,
                'supplier':supplier,
                'category':category,
                'transID':transID,
                'product':product,
                'type_period':type_period,
            },
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(data) {
                $('.loading').hide();
                // console.log(data.status);
                    loadDailyChart(data.daytoday);

                    loadPlatformChart(data.platform);

                    loadStatusChart(data.status);

                    loadStateChart(data.state);
               
                    loadPostcodeChart(data.postcode);

                    loadProductChart(data.products);

                    loadMonthlyChart(data.monthly);

                    loadRegionChart(data.region);

                    loadQuaterlyChart(data.quaterly);

                    $('#main').show();

            },
            error: function (error) {
                // location.reload();
            }
        })
    }

    // save the real makeChart function for later
    AmCharts.lazyLoadMakeChart = AmCharts.makeChart;

    // wH = $(window).height(),
    // override makeChart function
    AmCharts.makeChart = function(a, b, c) {

      // set scroll events
      jQuery(document).on('scroll load touchmove', handleScroll);
      jQuery(window).on('load', handleScroll);

      function handleScroll() {
        var $ = jQuery;
        if (true === b.lazyLoaded)
          return;
        var hT = $('#' + a).offset().top,
          hH = $('#' + a).outerHeight() / 2,
          wH = $(window).height(),
          wS = $(window).scrollTop();

          // console.log(wH);
        if (wS > (hT + hH - wH)) {
          b.lazyLoaded = true;
          AmCharts.lazyLoadMakeChart(a, b, c);
          return;
        }
      }

      // Return fake listener to avoid errors
      return {
        addListener: function() {}
      };
    };

    function loadDailyChart(TotalBatchSentGrouping){
        
      var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }
                        

        // TotalBatchSentGrouping.sort(function(a,b) {
        //     return b.total - a.total;
        // });

        // var topTen = TotalBatchSentGrouping.slice(0, 10);

        $("#dailychart").html('');

        var batch = [];
 
        $.each(TotalBatchSentGrouping, function( key, value ) {

            batch.push({  date: key, total: value });
            
        });

        batch.sort(function(a,b) {
            return a.date - b.date;
        });

        $('#tabledaily').text('');
         
                        $.each(batch, function(index, data) {
                            // console.log(data.date);
                            $('#tabledaily').append('<tr><td class="labeldaily"> Day '+ data.date +'</td><td class="valuedaily"> RM '+ data.total +'</td></tr>');
                           
                        });

        console.log(batch);

        var chart = AmCharts.makeChart( "dailychart", {
          "type": "serial",
          "theme": "light",
          "titles": [{
            "text": "Day To Day "+text,
            "margin": 20,
          }],
          "dataProvider":batch ,
          "startDuration": 5,
          "valueAxes": [ {
            "gridColor": "#FFFFFF",
            "gridAlpha": 0.2,
            "dashLength": 0
          } ],
          "gridAboveGraphs": true,
          "graphs": [ {
            "balloonText": "[[date]]: <b>[[total]]</b>",
            // "labelText": "[[total]]",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "type": "column",
            "valueField": "total"
          } ],
          "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
          },
          "categoryField": "date",
          "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20,
            "labelRotation": 40
          },
          "export": {
            "enabled": true
          }

        } );
        
        
    }

    function loadPlatformChart(TotalBatchSentGroupingtwo){
        var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

        $("#platformchart").html('');

        var batch = [];

        if(TotalBatchSentGroupingtwo.length > 0 ){
            var batch = [];

        }

        $.each( TotalBatchSentGroupingtwo, function( key, value ) {
            batch.push({platform: key , total_order: value });
            
        });
       
        var chart = AmCharts.makeChart( "platformchart", {
              "type": "pie",
              "theme": "light",
              "growSlices": true,
              "titles": [{
                "text": "Total "+text+" By Platform",
                "margin": 20,
              }],
              "dataProvider": batch,
              "valueField": "total_order",
              "titleField": "platform",
              "startDuration": 1,
               "balloon":{
               "fixedPosition":true
              },
              "export": {
                "enabled": true
              }
            } );

        batch.sort(function(a,b) {
            return b.total_order - a.total_order;
        });

        var totalgroup = batch;

        $('#tableplatform').text('');

                        $.each(totalgroup, function(index, data) {
                            // console.log(index);
                            
                            $('#tableplatform').append('<tr><td class="labelplatform">'+ data.platform + '</td><td class="valueplatform"> RM ' + data.total_order + '</td></tr>');
                          
                            });
                
            
    }

    function loadStatusChart(TotalBatchSentGroupingthree){
      var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

        $("#statuschart").html('');
        var batch = [];

        $.each( TotalBatchSentGroupingthree, function( key, value ) {
            
            batch.push({status: key , total_order: value });
            
        });

        // console.log(batch);

        var chart = AmCharts.makeChart( "statuschart", {
              "type": "pie",
              "theme": "light",
              "titles": [{
                "text": "Total Cancelled VS Delivered "+text,
                "margin": 20,
              }],
              "dataProvider": batch,
              "startDuration": 1,
              "valueField": "total_order",
              "titleField": "status",
               "balloon":{
               "fixedPosition":true
              },
              "export": {
                "enabled": true
              }
            } );

        batch.sort(function(a,b) {
            return b.total_order - a.total_order;
        });

        var totalgroup = batch;
        $('#tablestatus').text('');

                        $.each(totalgroup, function(index, data) {
                            // console.log(index);
                            // console.log(data);
                            $('#tablestatus').append('<tr><td class="labelstatus">' + data.status + '</td><td class="valuestatus"> RM '+ data.total_order +'</td></tr>');
 

                            });


            
    }

    function loadStateChart(TotalBatchSentGroupingfour){

        // $('#tablestate').text('');
         
        //                 $.each(TotalBatchSentGroupingfour, function(index, data) {
        //                     // console.log(data.date);
        //                 $('#tablestate').append('<tr><td class="labelstate">'+ data.delivery_state +'</td><td style="text-align:center;" class="valuestate"> RM '+ data.total_order +'</td></tr>');
                           
                            
        //                 });
                        
        var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

        TotalBatchSentGroupingfour.sort(function(a,b) {
            return b.total_order - a.total_order;
        });

        $('#tablestate').text('');
         
                        $.each(TotalBatchSentGroupingfour, function(index, data) {
                            // console.log(data.date);
                        $('#tablestate').append('<tr><td class="labelstate">'+ data.delivery_state +'</td><td style="text-align:center;" class="valuestate"> RM '+ data.total_order +'</td></tr>');
                           
                            
                        });

        var topTen = TotalBatchSentGroupingfour.slice(0, 10);

        var batch = [];

        $.each( topTen, function( key, value ) {

            batch.push({  delivery_state: value.delivery_state, total_order: value.total_order });
            
        });

        var chart = AmCharts.makeChart( "statechart", {
              "type": "pie",
              "theme": "light",
              "titles": [{
                "text": "Top 10 States In " +text,
                "margin": 20,
              }],
              "dataProvider": batch,
              "startDuration": 5,
              "valueField": "total_order",
              "titleField": "delivery_state",
               "balloon":{
               "fixedPosition":true
              },
              "export": {
                "enabled": true
              }
            } );
            
    }

    function loadPostcodeChart(TotalBatchSentGroupingfive){
     
        // $('#tablezipcode').text('');
         
        //                 $.each(TotalBatchSentGroupingfive, function(index, data) {
        //                     // console.log(data.date);
        //                     $('#tablezipcode').append('<tr><td class="labelzipcode">'+ data.delivery_postcode +'</td><td class="valuezipcode"> RM '+ data.total_order +'</td></tr>');
                           
        //                 });
                        
        var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

        TotalBatchSentGroupingfive.sort(function(a,b) {
            return b.total_order - a.total_order;
        });

        $('#tablezipcode').text('');
         
                        $.each(TotalBatchSentGroupingfive, function(index, data) {
                            // console.log(data.date);
                            $('#tablezipcode').append('<tr><td class="labelzipcode">'+ data.delivery_postcode +'</td><td class="valuezipcode"> RM '+ data.total_order +'</td></tr>');
                           
                        });

        var topTen = TotalBatchSentGroupingfive.slice(0, 10);

        var batch = [];

             $.each(topTen, function( key, value ) {

                        batch.push({  delivery_postcode: value.delivery_postcode, total_order: value.total_order });
                        
                    });

             var chart = AmCharts.makeChart( "zipcodechart", {
                  "type": "pie",
                  "theme": "light",
                  "titles": [{
                    "text": "Top 10 Zipcode In " +text,
                    "margin": 20,
                  }],
                  "dataProvider": batch,
                  "startDuration": 5,
                  "valueField": "total_order",
                  "titleField": "delivery_postcode",
                   "balloon":{
                   "fixedPosition":true
                  },
                  "export": {
                    "enabled": true
                  }
                } );

    }

    function loadProductChart(TotalBatchSentGroupingsix){
        
        // $('#tableproduct').text('');
         
        //                 $.each(TotalBatchSentGroupingsix, function(index, data) {
        //                     // console.log(data.date);
        //                     $('#tableproduct').append('<tr><td class="labelproduct">'+ data.name +'</td><td class="valueproduct">RM '+ data.total_order +'</td></tr>');
                           
        //                 });
                        
        TotalBatchSentGroupingsix.sort(function(a,b) {
            return b.total_order - a.total_order;
        });

        $('#tableproduct').text('');
         
                        $.each(TotalBatchSentGroupingsix, function(index, data) {
                            // console.log(data.date);
                            $('#tableproduct').append('<tr><td class="labelproduct">'+ data.name +'</td><td class="valueproduct">RM '+ data.total_order +'</td></tr>');
                           
                        });

        var topTen = TotalBatchSentGroupingsix.slice(0, 10);

        var batch = [];

             $.each( topTen, function( key, value ) {

                        batch.push({  name: value.name, total_order: value.total_order });
                        
                    });

        // console.log(batch);
             var chart = AmCharts.makeChart( "productschart", {
                      "type": "serial",
                      "rotate": true,
                      "theme": "light",
                      "titles": [{
                        "text": "Top 10 Products Ordered"
                      }],
                      "dataProvider":batch ,
                      "startDuration": 5,
                      "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                      } ],
                      "gridAboveGraphs": true,
                      "graphs": [ {
                        "balloonText": "[[name]]: <b>[[total_order]]</b>",
                        // "labelText": "[[total_order]]",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "total_order",
                        "balloonFunction": function(graphDataItem, graph) {
                            return AmCharts.formatNumber(graphDataItem.values.value, {precision: -1, decimalSeparator: '.', thousandsSeparator: ','}, -1);
                          }
                      } ],
                      "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                      },
                      "categoryField": "name",
                      "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20,
                        "labelRotation": 20
                      },
                      "export": {
                        "enabled": true
                      }

                    } );
        
    }

    function loadMonthlyChart(TotalBatchSentGroupingseven){
      var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

    //   TotalBatchSentGroupingseven.sort(function(a,b) {
    //         return b.total_order - a.total_order;
    //     });
      
    //     $('#tablemonthly').text('');
         
    //                     $.each(TotalBatchSentGroupingseven, function(index, data) {
    //                         // console.log(data.date);
    //                         $('#tablemonthly').append('<tr><td class="labelmonthly">'+ data.month_name +'</td><td class="valuemonthly"> RM '+ data.total_order +'</td></tr>');

    //                     });


    //     var batch = [];

    //          $.each(TotalBatchSentGroupingseven, function( key, value ) {

    //                     batch.push({  month_name: value.month_name, total_order: value.total_order });
                        
    //                 });
             // console.log(batch);
                    // console.log(batch);
             var chart = AmCharts.makeChart( "monthlychart", {
                      "type": "serial",
                      "theme": "light",
                      "titles": [{
                        "text": "Total "+text+" By Month"
                      }],
                      "dataProvider":TotalBatchSentGroupingseven ,
                      "startDuration": 5,
                      "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                      } ],
                      "gridAboveGraphs": true,
                      "graphs": [ {
                        "balloonText": "[[month_name]]: <b>[[total_order]]</b>",
                        // "labelText": "[[total_order]]",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "total_order",
                        "balloonFunction": function(graphDataItem, graph) {
                            return AmCharts.formatNumber(graphDataItem.values.value, {precision: -1, decimalSeparator: '.', thousandsSeparator: ','}, -1);
                          }
                      } ],
                      "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                      },
                      "categoryField": "month_name",
                      "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20
                      },
                      "export": {
                        "enabled": true
                      }

                    } );
                    
                    $('#tablemonthly').text('');
         
                        $.each(TotalBatchSentGroupingseven, function(index, data) {
                            // console.log(data.date);
                            $('#tablemonthly').append('<tr><td class="labelmonthly">'+ data.month_name +'</td><td class="valuemonthly"> RM '+ data.total_order +'</td></tr>');

                        });

    }

    function loadRegionChart(TotalBatchSentGroupingeight){
         
         var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }               

        var batch = [];

             $.each(TotalBatchSentGroupingeight, function( key, value ) {
                // console.log(value);
                        batch.push({  region_name: value.region_name, total_order: value.total_order });
                        

                    });
             // console.log(batch);
                    // console.log(batch);
             var chart = AmCharts.makeChart( "regionchart", {
                      "type": "serial",
                      "theme": "light",
                      "titles": [{
                        "text": "Total "+text+" By Region"
                      }],
                      "dataProvider":batch ,
                      "startDuration": 5,
                      "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                      } ],
                      "gridAboveGraphs": true,
                      "graphs": [ {
                        "balloonText": "[[region_name]]: <b>[[total_order]]</b>",
                        // "labelText": "[[total_order]]",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "total_order",
                        "balloonFunction": function(graphDataItem, graph) {
                            return AmCharts.formatNumber(graphDataItem.values.value, {precision: -1, decimalSeparator: '.', thousandsSeparator: ','}, -1);
                          }
                      } ],
                      "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                      },
                      "categoryField": "region_name",
                      "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20
                      },
                      "export": {
                        "enabled": true
                      }

                    } );

             batch.sort(function(a,b) {
                return b.total_order - a.total_order;
            });

             $('#tableregion').text('');

                        $.each(batch, function(index, data) {
                            // console.log(data.date);
                            $('#tableregion').append('<tr><td class="labelregion">'+ data.region_name +'</td><td class="valueregion"> RM '+ data.total_order +'</td></tr>');

                        });

    }


    function loadQuaterlyChart(TotalBatchSentGroupingnine){
      
      var type_report = $('#type_report').val();

        if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }

    //   TotalBatchSentGroupingnine.sort(function(a,b) {
    //         return b.total_order - a.total_order;
    //     });
      
    //     $('#tablequaterly').text('');
         
    //                     $.each(TotalBatchSentGroupingnine, function(index, data) {
    //                         // console.log(data.date);
    //                         $('#tablequaterly').append('<tr><td class="labelquaterly">'+ data.month_name +'</td><td class="valuequaterly"> RM '+ data.total_order +'</td></tr>');

    //                     });


    //     var batch = [];

    //          $.each(TotalBatchSentGroupingnine, function( key, value ) {

    //                     batch.push({  month_name: value.month_name, total_order: value.total_order });
                        
    //                 });
  
             var chart = AmCharts.makeChart( "quaterlychart", {
                      "type": "serial",
                      "theme": "light",
                      "titles": [{
                        "text": "Total "+text+" By Quater"
                      }],
                      "dataProvider":TotalBatchSentGroupingnine ,
                      "startDuration": 5,
                      "valueAxes": [ {
                        "gridColor": "#FFFFFF",
                        "gridAlpha": 0.2,
                        "dashLength": 0
                      } ],
                      "gridAboveGraphs": true,
                      "graphs": [ {
                        "balloonText": "[[month_name]]: <b>[[total_order]]</b>",
                        // "labelText": "[[total_order]]",
                        "fillAlphas": 0.8,
                        "lineAlpha": 0.2,
                        "type": "column",
                        "valueField": "total_order",
                        "balloonFunction": function(graphDataItem, graph) {
                            return AmCharts.formatNumber(graphDataItem.values.value, {precision: -1, decimalSeparator: '.', thousandsSeparator: ','}, -1);
                          }
                      } ],
                      "chartCursor": {
                        "categoryBalloonEnabled": false,
                        "cursorAlpha": 0,
                        "zoomable": false
                      },
                      "categoryField": "month_name",
                      "categoryAxis": {
                        "gridPosition": "start",
                        "gridAlpha": 0,
                        "tickPosition": "start",
                        "tickLength": 20
                      },
                      "export": {
                        "enabled": true
                      }

                    } );
                    
                    $('#tablequaterly').text('');
         
                        $.each(TotalBatchSentGroupingnine, function(index, data) {
                            // console.log(data.date);
                            $('#tablequaterly').append('<tr><td class="labelquaterly">'+ data.month_name +'</td><td class="valuequaterly"> RM '+ data.total_order +'</td></tr>');

                        });

    }

function exportCharts() {

  // So that we know export was started
  console.log("Starting export...");

  // Define IDs of the charts we want to include in the report
  var ids = ["dailychart","platformchart", "statuschart", "statechart","zipcodechart", "productschart","monthlychart", "regionchart","quaterlychart"];

  // Collect actual chart objects out of the AmCharts.charts array
  var charts = {},
    charts_remaining = ids.length;
  for (var i = 0; i < ids.length; i++) {
    for (var x = 0; x < AmCharts.charts.length; x++) {
      if (AmCharts.charts[x].div.id == ids[i])
        charts[ids[i]] = AmCharts.charts[x];
    }
  }

  // Trigger export of each chart
  for (var x in charts) {
    if (charts.hasOwnProperty(x)) {
      var chart = charts[x];
      chart["export"].capture({}, function() {
        this.toJPG({}, function(data) {

          // Save chart data into chart object itself
          this.setup.chart.exportedImage = data;

          // Reduce the remaining counter
          charts_remaining--;

          // Check if we got all of the charts
          if (charts_remaining == 0) {
            // Yup, we got all of them
            // Let's proceed to putting PDF together
            generatePDF();
          }

        });
      });
    }
  }

  function generatePDF() {

     var type_report = $('#type_report').val();

     if (type_report == 1) {
          var text = "Transaction";
        }else{
          var text = "Product";
        }
    // Log
    console.log("Generating PDF...");
    // console.log($('#datepicker').val());
    // Initiliaze a PDF layout
    var layout = {
      "content": []
    };

    if ($('#type_report').val() == 1) {
            // Let's add a custom title
      layout.content.push({
        "text": "Transaction Sales Report",
        "fontSize": 18,
        "bold": true,
        "marginBottom": 15,
        "alignment" : 'center',
      });
    }else{
      // Let's add a custom title
      layout.content.push({
        "text": "Product Sales Report",
        "fontSize": 18,
        "bold": true,
        "marginBottom": 15,
        "alignment" : 'center',
      });
    }

    // Let's add a custom title
    // layout.content.push({
    //   "text": "Transaction Sales Report",
    //   "fontSize": 15,
    //   "marginBottom": 15,
    // });

    // Now let's grab actual content from our <p> intro tag
    // layout.content.push({
    //   "text": $('#datepicker').val()
    // });

    layout.content.push({
      "columns": [{
        "width": "100%",
        "text": "Date: "+ $('#datepicker').val() + " To "+ $('#datepicker2').val(),
        "fit": [250, 300],
        "fontSize": 10,
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    if ($('#supplier option:selected').val() != '') {
      var supplier = $('#supplier option:selected').text();
    }else{
      var supplier = '';
    }

    if ($('#category option:selected').val() != '') {
      var category = $('#category option:selected').text();
    }else{
      var category = '';
    }

    layout.content.push({
      "columns": [{
        "width": "30%",
        "text": "Supplier: "+ supplier,
        "fit": [250, 300],
        "fontSize": 10,
        "marginBottom": 10,
      }, {
        "width": "*",
        "text": "Category: "+ category,
        "fit": [250, 300],
        "fontSize": 10,
        "marginBottom": 10,
      }, {
        "width": "*",
        "text": "Product ID: "+ $('#product').val(),
        "fit": [250, 300],
        "fontSize": 10,
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    var batchplatform = [];
    var labellistplatform = [];

    $("#tableplatform").find("td.valueplatform").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchplatform.push(total);            
            
        });

    $("#tableplatform").find("td.labelplatform").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistplatform.push(label);
            
        });

    var batchstatus = [];
    var labelliststatus = [];

    $("#tablestatus").find("td.valuestatus").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchstatus.push(total);            
            
        });

    $("#tablestatus").find("td.labelstatus").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labelliststatus.push(label);
            
        });

    // layout.content.push({
    //   "columns": [{
    //     "width": "50%",
    //     "text": "Top 10 Platform Transaction",
    //     "style": 'subheader',
    //     "marginBottom": 10,
    //   },{
    //     "width": "50%",
    //     "text": "Top 10 Status Transaction",
    //     "style": 'subheader',
    //     "marginBottom": 10,
    //   }]
    // });

    // console.log(labellist);
    // with table column
    // layout.content.push({
    //   "columns": [{
    //     "width": "70%",
    //     "image": charts["platform"].exportedImage,
    //     "fit": [250, 300]
    //   }, {
    //     "width": "30%",
    //     "table": {
    //         "body": [
    //             ["Platform", "Total Amount"],
    //             [labellist, batchplatform],
    //         ]
    //       },
    //     "fit": [500, 500]
    //   }],
    //   "columnGap": 10
    // });
    // Put two next charts side by side in columns
    layout.content.push({
      "columns": [{
        "width": "50%",
        "image": charts["platformchart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      }, {
        "width": "50%",
        "image": charts["statuschart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    // with table column
    layout.content.push({
      "columns": [{
        "width": "50%",
        "table": {
            "body": [
                ["Platform", "Total Revenue"],
                [labellistplatform, batchplatform],
            ],
            "widths": ["50%","*"],
          },
        "fit": [500, 500],
        "marginBottom": 10,
      },{
        "width": "50%",
        "table": {
            "body": [
                ["Status", "Total Revenue"],
                [labelliststatus, batchstatus],
            ],
            "widths": ["50%","*"],
          },
        "fit": [500, 500],
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    var batchdaily = [];
    var labellistdaily = [];

    $("#tabledaily").find("td.valuedaily").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchdaily.push(total);            
            
        });

    $("#tabledaily").find("td.labeldaily").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistdaily.push(label);
            
        });

    // Add bigger chart
    layout.content.push({
      "image": charts["dailychart"].exportedImage,
      "fit": [523, 300],
      "marginBottom": 10,
    });

    // with table column
    layout.content.push({
      "columns": [{
        "width": "100%",
        "table": {
            "body": [
                ["Days", "Total Revenue"],
                [labellistdaily,batchdaily],
            ],
            "widths": ["50%","*"],
          },
          "marginBottom": 10,
      }]
    });

    // var type_report = $('#type_report').val();

    layout.content.push({
      "columns": [{
        "width": "50%",
        "text": "Top 10 States In "+text,
        "fontSize": 14,
        "bold": true,
        "marginTop": 10,
        "marginBottom": 10,
      },{
        "width": "50%",
        "text": "Top 10 Zipcode In " +text,
        "fontSize": 14,
        "bold": true,
        "marginTop": 10,
        "marginBottom": 10,
      }], 
    });

    layout.content.push({
      "columns": [{
        "width": "50%",
        "image": charts["statechart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      }, {
        "width": "*",
        "image": charts["zipcodechart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      }],
      "columnGap": 10,
    });

    var batchstate = [];
    var labelliststate = [];

    $("#tablestate").find("td.valuestate").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchstate.push(total);            
            
        });

    $("#tablestate").find("td.labelstate").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labelliststate.push(label);
            
        });

    var batchzipcode = [];
    var labellistzipcode = [];

    $("#tablezipcode").find("td.valuezipcode").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchzipcode.push(total);            
            
        });

    $("#tablezipcode").find("td.labelzipcode").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistzipcode.push(label);
            
        });

    layout.content.push({
      "columns": [{
        "width": "50%",
        "table": {
            "body": [
                ["States", "Total Revenue"],
                [labelliststate, batchstate],
            ],
            "widths": ["50%","*"],
          },
          "marginBottom": 10,
      },{
        "width": "50%",
        "table": {
            "body": [
                ["Zipcode", "Total Revenue"],
                [labellistzipcode, batchzipcode],
            ],
            "widths": ["50%","*"],
          },

        "marginBottom": 10,
      }],
      "columnGap": 10,
      "pageBreak": 'after'
    });


    var batchproduct = [];
    var labellistproduct = [];

    $("#tableproduct").find("td.valueproduct").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchproduct.push(total);            
            
        });

    $("#tableproduct").find("td.labelproduct").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistproduct.push(label);
            
        });

    if (type_report == 2) {

      layout.content.push({
        "columns": [{
          "text": "Top 10 Products Ordered",
          "fontSize": 14,
          "bold": true,
          "marginBottom": 10,
          "alignment" : 'center',
        }], 
      });

      // Add bigger chart
      layout.content.push({
        "image": charts["productschart"].exportedImage,
        "fit": [523, 300],
        "marginBottom": 10,
      });

      // with table column
      layout.content.push({
        "columns": [{
          "width": "100%",
          "table": {
              "body": [
                  ["Product Name", "Total Revenue"],
                  [labellistproduct,batchproduct],
              ],
              "widths": ["80%","20%"],
            },
            "marginBottom": 10,
        }]
      });

  }
    // Let's add a table
    // layout.content.push({
    //   "table": {
    //     // headers are automatically repeated if the table spans over multiple pages
    //     // you can declare how many rows should be treated as headers
    //     "headerRows": 1,
    //     "widths": ["16%", "16%", "16%", "16%", "16%", "*"],
    //     "body": [
    //       ["USA", "UK", "Canada", "Japan", "France", "Brazil"],
    //       ["5000", "4500", "5100", "1500", "9600", "2500"],
    //       ["5000", "4500", "5100", "1500", "9600", "2500"],
    //       ["5000", "4500", "5100", "1500", "9600", "2500"]
    //     ]
    //   }
    // });

    
    // Add chart and text next to each other
    // layout.content.push({
    //   "columns": [{
    //     "width": "25%",
    //     "image": charts["chartdiv4"].exportedImage,
    //     "fit": [125, 300]
    //   }, {
    //     "width": "*",
    //     "stack": [
    //       document.getElementById("note1").innerHTML,
    //       "\n\n",
    //       document.getElementById("note2").innerHTML
    //     ]
    //   }],
    //   "columnGap": 10
    // });

    var batchmonthly = [];
    var labellistmonthly = [];

    $("#tablemonthly").find("td.valuemonthly").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchmonthly.push(total);            
            
        });

    $("#tablemonthly").find("td.labelmonthly").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistmonthly.push(label);
            
        });

    var batchregion = [];
    var labellistregion = [];

    $("#tableregion").find("td.valueregion").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchregion.push(total);            
            
        });

    $("#tableregion").find("td.labelregion").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistregion.push(label);
            
        });

    // Put two next charts side by side in columns
    layout.content.push({
      "columns": [{
        "width": "50%",
        "image": charts["monthlychart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      },{
        "width": "50%",
        "image": charts["regionchart"].exportedImage,
        "fit": [250, 300],
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    layout.content.push({
      "columns": [{
        "width": "50%",
        "table": {
            "body": [
                ["Month", "Total Revenue"],
                [labellistmonthly,batchmonthly],
            ],
            "widths": ["50%","*"],
          },
          "marginBottom": 10,
      },{
        "width": "50%",
        "table": {
            "body": [
                ["Region", "Total Revenue"],
                [labellistregion, batchregion],
            ],
            "widths": ["50%","*"],
          },
        "marginBottom": 10,
      }],
      "columnGap": 10
    });

    var batchquater = [];
    var labellistquater = [];

    $("#tablequaterly").find("td.valuequaterly").each(function() { //get all rows in table
            var total = $(this).text();//Refers to TD element
            batchquater.push(total);            
            
        });

    $("#tablequaterly").find("td.labelquaterly").each(function() { //get all rows in table
            var label = $(this).text();//Refers to TD element
            labellistquater.push(label);
            
        });

    var type_period = $('#type_period').val();

    if (type_period == 2) {

      // Put two next charts side by side in columns
      layout.content.push({
        "columns": [{
          "width": "100%",
          "image": charts["quaterlychart"].exportedImage,
          "fit": [523, 300],
          "marginBottom": 10,
        }],
        "columnGap": 10
      });

      layout.content.push({
        "columns": [{
          "width": "100%",
          "table": {
              "body": [
                  ["Quater", "Total Revenue"],
                  [labellistquater,batchquater],
              ],
              "widths": ["50%","*"],
            },
            "marginBottom": 10,
        }],
        "columnGap": 10
      });
    }
    // Trigger the generation and download of the PDF
    // We will use the first chart as a base to execute Export on
    chart["export"].toPDF(layout, function(data) {
      datedwnload = new Date().getTime();
      this.download(data, "application/pdf", "report_"+datedwnload+".pdf");
    });

  }
} 

</script>
@stop

