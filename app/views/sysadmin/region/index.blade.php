@extends('layouts.master')

@section('title') Region @stop

@section('content')

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Region<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/region/create"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Region Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none;">
                <table class="table table-bordered table-striped table-hover" id="dataTables-region">
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-1">Region</th>
                            <th class="col-sm-3">Region Code</th>
                            <th class="col-sm-2">Country</th>
                            <th class="col-sm-3">States</th>
                            <th class="col-sm-1">Status</th>
                            <th class="col-sm-1" >Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
    <a href="/sysadmin/user/create" class="btn btn-success">Add User</a>
    @endif -->
</div>
@stop
@section('inputjs')
<script>
$('#dataTables-region').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('region/list') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id', name: 'id' },
            { data: 'region', name: 'region' },
            { data: 'region_code', name: 'region_code' },
            { data: 'name', name: 'name' },
            { data: function ( row, type, val, meta ) {
                    
                    var str = '';
                    $.each(row.states, function (index, value) {
                       
                        str = str + '<div><i class="fa fa-caret-right" aria-hidden="true"></i> '+value.name+' </div>'
                    })
                    
                    return str;
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   
                    if( row.activation == 1){ return '<span class="label label-success">Active</span>' };
                    if( row.activation == 0){ return '<span class="label label-danger">Inactive</span>' };
                },
                className: "center"
            },
            { data: function ( row, type, val, meta ) {
                   
                    return '<a class="btn btn-default" href="region/edit/'+row.id+'"><i class="fa fa-pencil" aria-hidden="true"></i></a';
                },
                className: "center"
            },
            ]
    });
    
</script>
@stop
