@extends('layouts.master')

@section('title') Goods Received Note GRN @stop

@section('content')
<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-12">
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif

     <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Goods Received Note {{Input::get('type')}} Basis
              <span class="pull-right">
                  <a class="btn btn-primary btn-sm" title="" data-toggle="tooltip" href="{{asset('/')}}grn"><i class="fa fa-refresh"></i></a>
              </span>
            </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTables-po">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>GRN Number</th>
                            <th>GRN Date</th>
                            <th>Seller Company</th>
                        </tr>
                    </thead>
                </table>
            </div>
            </div>
        </div>
    </div>
    </div>
    </div>

</div>
@stop

@section('script')
  var period = '<?php echo $period; ?>';
  var type = '<?php echo $type; ?>';
  $( document ).ready(function() {
      console.log( "ready!" );
      console.log( period );
      console.log( "ready!" );
  });
    $('#dataTables-po').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": { "method":"POST","url": "{{ URL::to('grn/grn-report/view-all/any-grn-report-view-all-data') }}", "data": { "period": period, "type": type }},
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0" },
            { "data" : "1" },
            { "data" : "2" },
            { "data" : "3" },
        ]

    });

@stop
