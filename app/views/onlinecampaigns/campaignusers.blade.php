@extends('layouts.master')

@section('title') Online Campaign User List @stop

@section('content')
<style>
.center{
    text-align:center;
}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Online Campaign Users
            <span class="pull-right">
            <!-- <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}onlinecampaign/createfoc"><i class="fa fa-plus"></i> Add New </a> -->
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Campaign Users List</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-bordered table-hover" id="dataTables-campaignusers">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">ID</th>
                                    <th class="col-sm-2">Campaign ID</th>
                                    <th class="col-sm-2">Name</th>
                                    <th class="col-sm-2">Email</th>
                                    <th class="col-sm-2">Register date</th>
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
   $('#dataTables-campaignusers').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('onlinecampaign/userlists?'.http_build_query(Input::all())) }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id'},
            { data: 'campaign_id'},
            { data: 'name'},
            { data: 'email'},
            { data: 'created_at'},
            { data: function ( row, type, val, meta ) {
                    if(row.status_activation == 1){ return ' <span class="label label-success">Active</span>'};
                    if(row.status_activation == 0){ return ' <span class="label label-danger">Inactive</span>'};
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                    var ids = row.id;
                   return ' <a href="/onlinecampaign/editcampaign/'+row.id+'" class="btn btn-primary"><i class="fa fa-pencil"></i></a> <button class="btn btn-danger" data-target="#delete" data-toggle="modal" data-type='+ids+' data-type-id='+ids+'> <i class="fa fa-close"></i></button>';

                },
                className: "center"
            },
            
        ]
    });
@stop






