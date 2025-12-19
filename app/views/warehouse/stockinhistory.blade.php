@extends('layouts.master')
@section('title') Warehouse @stop
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
            <h1 class="page-header">Warehouse Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}warehouse"><i class="fa fa-refresh"></i></a>
                    
                </span>
            </h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Inventory Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">Product ID</th>
                                    <th class="col-sm-1">SKU</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2">Label</th>
                                    <th class="col-sm-2">Last Stock In Date</th>
                                    <th class="col-sm-2">Action</th>
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

    <div class="modal fade" id="stockin_log" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {{-- <div class="modal-content" style="margin-left:auto; margin-left:auto; width:500px;"> --}}
              <div class="modal-content modal-lg" style="width: 700px;margin: auto;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title stock-size-title">Stock In Log </h4>
                </div>
                <div class="modal-body stock-size">
                  <!--<a class="btn btn-primary addSize" title="" data-toggle="tooltip" href="#" style="margin-bottom: 5px;"><i class="fa fa-plus"></i></a>-->
                  <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-list"></i> Stock In Log Listing</h3>                    
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive" style="overflow-x: none">
                            <table class="table table-striped table-bordered table-hover" id="dataTables-log">
                                <thead>
                                    <tr>
                                        <th class="">Quantity</th>
                                        <th class="">Remarks</th>
                                        <th class="">Stock In By</th>
                                        <th class="">Stock In Date</th>
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
    </div>

</div>
@stop
@section('inputjs')
<script>

    $(document).ready(function(){

      $('body').on('click', '.stockLog', function() {
        $("#stockin_log").modal('show');
        var product_id = $(this).attr('data-id');
        $('#dataTables-log').dataTable({
          "destroy": true,
          "autoWidth" : false,
          "ajax": '/warehouse/stockinproductlog/' + product_id,
          "order" : [[ 3, 'desc' ]],
          "columns" : [
              { "data" : "0", "className" : "text-center" },
              { "data" : "4" },
              { "data" : "2" },
              { "data" : "3" },

          ]
          
        });
      });

			$('#dataTables-products').dataTable({
          "autoWidth" : false,
          "processing": true,
          "serverSide": true,
          "ajax": "{{ URL::to('warehouse/history') }}",
          "order" : [[ 0, 'desc' ]],
          "columnDefs" : [{
              "targets" : "_all",
              "defaultContent" : "",
              "orderable": false, "targets": [2,3],
          }],
          "columns" : [
              { "data" : "0", "className" : "text-center", "width": "10%" },
              { "data" : "1" },
              { "data" : "2" },
              { "data" : "3" },
              { "data" : "4" },
              { "data" : "5", 
                "render": function (val, type, row) {
	                    return "<a  href=# class='btn btn-primary popoverButton stockLog' data-id="+row[0]+"><i class='fa fa-eye'></i></a>";
	                },
                "className" : "text-center",
               
              },

          ]
          
      });

	});
</script>

@stop