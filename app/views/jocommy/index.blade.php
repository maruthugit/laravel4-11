@extends('layouts.master')

@section('title') Banner List @stop

@section('content')
<style>
    #dataTables-products_wrapper td p{margin-bottom: 0px;}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Banner
                 <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jocommy/index"><i class="fa fa-refresh"></i></a>
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
                    <a class="pull-right"><a class="btn btn-primary" title="" data-toggle="tooltip" href="/jocommy/create"><i class="fa fa-plus"></i></a>
                    @endif
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="banner_type">Banner Type</label>
                                    {{ Form::select('banner_type', array_merge(['any' => 'Any'], $banner_type), Input::get('banner_type'), ['id' => 'banner_type', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    {{ Form::select('status', ['any' => 'Any', 1 => 'Active only', 0 => 'Inactive only'], Input::get('status'), ['id' => 'status', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lang">Language</label>
                                    {{ Form::select('lang', array_merge(['any' => 'Any'], $lang), Input::get('lang'), ['id' => 'lang', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="region">Region</label>
                                    {{ Form::select('region', array_merge(['0' => 'Any'], $region), Input::get('region'), ['id' => 'region', 'class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 5]) }}
                    </form>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Banner List</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th class="col-md-1">ID</th>
                                    <th class="col-md-1">Type</th>
                                    <th class="col-md-1">Image</th>
                                    <th class="col-md-1">Title</th>
                                    <th class="col-md-1">Content</th>
                                    <th class="col-md-1">Position</th>
                                    <th class="col-md-1">Duration</th>
                                    <th class="col-md-1">Status</th>
                                    <th class="col-md-1">Language</th>
                                    <th class="col-md-1">Region</th>
                                    <th class="col-md-1">Last Action</th>
                                    <th class="col-md-1">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    $('#dataTables-products').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "{{ URL::to('jocommy/index?' . http_build_query(Input::all())) }}",
            "type": "POST"
        },
        "order" : [[ 0, 'desc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "className" : "text-center" },
            { "data" : "1"},
            { "data" : "3"},
            { "data" : "4"},
            { "data" : "2"},
            { "data" : "6", "className": "text-right" },
            { "data" : "7", "searchable" : false },
            { "data" : "5", "className" : "text-center" },
            { "data" : "8" },
            { "data" : "9", "className" : "text-center" },
            { "data" : "10", "orderable" : false, "searchable" : false, "className" : "text-center" },
            { "data" : "11", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });

    $(document).on("click", "#deleteItem", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete product id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            }
        });
    });
@stop
