@extends('layouts.master')

@section('title') Individual Permission @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Individual Permission<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}sysadmin/indvPermission"><i class="fa fa-refresh"></i></a>
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/sysadmin/indvPermission/create"><i class="fa fa-plus"></i></a>
                @endif
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Individual Permission Listing</h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
            <table class="table table-bordered table-striped table-hover" id="dataTables-role">
 
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Module</th>
                    {{-- <th>Sub Module</th> --}}
                    <th>Action</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>
</div>
<!-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
<div class="form-group">
    <a href="/sysadmin/role/create" class="btn btn-large btn-success">Add Role</a>
</div>
@endif -->
@stop

@section('script')
   $('#dataTables-role').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('sysadmin/indvPermission/permission') }}",
        "order" : [[0,'asc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false, "searchable" : false },
            { "data" : "1" },
            { "data" : "2" },
            {{-- { "data" : "3" }, --}}
            { "data" : "3", "orderable" : false, "searchable" : false },
           
        ]
    });

    {{-- $(document).on("click", "#deleteRole", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this user - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete user id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    });  --}}
    
@stop