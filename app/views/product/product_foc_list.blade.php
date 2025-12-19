@extends('layouts.master')

@section('title') FOC Product List @stop

@section('content')
<style>
.center{
    text-align:center;
}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">FOC Campaign
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}product/createfoc"><i class="fa fa-plus"></i> Add New FOC</a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> FOC Campaign List</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-bordered table-hover" id="dataTables-history">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-1">Start Date</th>
                                    <th class="col-sm-1">End Date</th>
                                    <th class="col-sm-2">Product FOC</th>
                                    <th class="col-sm-1 center">Qty</th>
                                    <th class="col-sm-1">Allocation</th>
                                    <th class="col-sm-1 text-center">Balance</th>
                                    <th class="col-sm-1 text-center">Rule Code</th>
                                    <th class="col-sm-2 text-center">Region</th>
                                    <th class="col-sm-1 text-center">Status</th>
                                    <th class="col-sm-1 text-center" style="min-width:100px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>


@stop


@section('script')
   $('#dataTables-history').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('product/focinfo?'.http_build_query(Input::all())) }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id'},
            { data: 'start_date'},
            { data: 'end_date'},
            { data: 'ProductNameFOC'},
            { data: function ( row, type, val, meta ) {
                   return row.reward_quantity;
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   return row.quantity;
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   return row.balance_quantity;
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   return row.rule;
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                var states = '';
                for (var key in row.region) {
                    var obj = row.region[key];
                    states = states + '<li>' + obj.name + '</li>';
                }
                return states;
                },
                className: ""
            },
            { data: function ( row, type, val, meta ) {
                    if(row.activation == 1){ return ' <span class="label label-success">Active</span>'};
                    if(row.activation == 0){ return ' <span class="label label-danger">Inactive</span>'};
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   return ' <a href="/product/editfoc/'+row.id+'" class="btn btn-default" href=><i class="fa fa-pencil"></i></a>';
                },
                className: "center"
            },
            
        ]
    });
@stop



