@extends('layouts.master')

@section('title') State @stop

@section('content')
<div id="page-wrapper">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Country : {{ $country->name }} 
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href="/country"><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> State Listing</h3>                    
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive" style="overflow-x: none">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-states">
                        <thead>
                            <tr>
                                <th class="col-sm-1">ID</th>
                                <th class="col-sm-5">State</th>
                                <th class="text-center col-sm-1">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>                            
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
    <!-- /.col-lg-12 -->
</div>
    <!-- /.row -->	
@stop

@section('script')
    $('#dataTables-states').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to("country/states/$country->id") }}",
        "order" : [[ 0, 'asc' ]],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false, "searchable" : false },
            { "data" : "1" },
            { "data" : "2", "orderable" : false, "searchable" : false, "className" : "text-center" },
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
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
@stop