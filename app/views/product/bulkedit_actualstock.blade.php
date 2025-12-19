@extends('layouts.master')
@section('title') Product @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>
<style type="text/css">
	/* END SORTABLETABLE STYLES */
/* BEGIN SPARKLINE STYLES */
.sparkline {
  min-width: 50px;
  border-right: 1px solid #DCDCDC;
  box-shadow: 1px 0 0 0 #FFFFFF;
  float: left;
  margin-right: 12px;
  padding: 10px 14px 0px 4px;
  line-height: 52px;
}
/* END SPARKLINE STYLES */
.stats_box {
  display: inline-block;
  list-style: none outside none;
  margin-left: 0;
  margin-top: 20px;
  padding: 0;
}
.stats_box li {
  background: #EEEEEE;
  box-shadow: 0 0 0 1px #F8F8F8 inset, 0 0 0 1px #CCCCCC;
  display: inline-block;
  line-height: 18px;
  margin: 0 10px 10px;
  padding: 0 10px;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
  float: left;
}
.stats_box .stat_text {
  float: left;
  font-size: 12px;
  padding: 9px 10px 7px 0;
  text-align: left;
  min-width: 150px;
  position: relative;
}
.stats_box .stat_text strong {
  display: block;
  font-size: 16px;
}
.stats_box .stat_text .percent {
  color: #444;
  float: right;
  font-size: 20px;
  font-weight: bold;
  position: absolute;
  right: 0;
  top: 17px;
}
.stats_box .stat_text .percent.up {
  color: #46a546;
}
.stats_box .stat_text .percent.down {
  color: #C52F61;
}
::-webkit-scrollbar {
  width: 12px;
  height: 12px;
}
::-webkit-scrollbar-thumb {
  border-radius: 1em;
}
::-webkit-scrollbar-thumb:hover {
  background-color: #999;
}
::-webkit-scrollbar-track {
  border-radius: 1em;
  background: transparent;
}
::-webkit-scrollbar-track:hover {
  background: rgba(110, 110, 110, 0.25);
}

input[readonly].lockinput{
  background-color:transparent;
  border: 0;
  font-size: 1em;
}

.center {
  padding: 20px 0 !important;
}
/* BEGIN FULLCALENDAR STYLES */

</style>
<div id="page-wrapper">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Actual Stock
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product/bulkeditactualstock"><i class="fa fa-refresh"></i></a>
                    
                </span>
            </h1>
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
            <div class="col-md-10">
                <!-- <button type="button" id="csv-file-button" class="btn btn-default" style="width:100%;">Choose File</button>  -->
                <input id="csv-file" type="file" accept=".csv" />
                <br>
                <p style="color: red">Please upload file with .csv extension</p>
            </div>
            <!-- <div class="col-md-8">
                <label id="csv-file-label"></label>
            </div> -->
            <div class="col-md-2">
                <button type="button" id="upload" class="btn btn-primary pull-right" style="width:100%;" disabled="true"><i class="fa fa-arrow-circle-down"></i> Upload CSV File</button>
            </div>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12">
        	 @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Actual Stock Out Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">Product ID</th>
                                    <th class="col-sm-3">Product Name</th>
                                    <th class="col-sm-1">Price ID</th>
                                    <th class="col-sm-2">Price Label</th>
                                    <th class="col-sm-1 text-center">Quantity</th>
                                    <th class="col-sm-1">Update By</th>
                                    <th class="col-sm-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>

        </div>
    </div>
</div>
@stop
@section('inputjs')
<script>

    $(document).ready(function(){


      $('#csv-file').change(enableUploadButton);

      function enableUploadButton() {
          let file = $(this)[0].files[0];

          if (file) {
              document.getElementById('upload').disabled = false;
          }
      }

      $('#upload').click(function() {
        $(".loading").show();
        document.getElementById('upload').disabled = true;
        var formData = new FormData()
        formData.append('csv_file', $('#csv-file')[0].files[0]);

        $.ajax({
          url:'/product/uploadactualstockexcel',
          data:formData,
          type:'post',
          processData: false,
          contentType: false,
          success:function(response) {
              $(".loading").hide();
              if (response.status == 200) {
                  $('#dataTables').DataTable().ajax.reload();
                  alert('Upload Success');
              } else {
                console.log(response);
                  alert('Error uploading order.');
              }
          },
          complete:function() {
            document.getElementById('upload').disabled = false;
          }
        });
      });

			$('#dataTables').dataTable({
          "autoWidth" : false,
          "processing": true,
          "serverSide": true,
          "ajax": "{{ URL::to('product/actualstockoutlist') }}",
          "order" : [[ 0, 'desc' ]],
          "columns" : [
              { "data" : "0", "className" : "text-center" },
              { "data" : "1" },
              { "data" : "2" },
              { "data" : "3" },
              { "data" : "4" },
              { "data" : "5", "className" : "text-center" },
              { "data" : "6" },
              { "data" : "7" },
          ],
          
      });

	});

</script>

@stop