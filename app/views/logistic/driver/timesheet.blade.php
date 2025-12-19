@extends('layouts.master')
@section('title') Driver Time Sheet @stop
@section('content')
<style>
    .center{
        text-align: center;
    }
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
           <h1 class="page-header">Driver Time Sheet</h1>
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">
            <div class="col-md-2">
                <button type="button" id="generate" class="btn btn-primary pull-right" style="width:100%;"> Generate</button>
            </div>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Driver Time Sheet Listing </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: hidden;" >
                <table class="table table-bordered table-striped table-hover" id="dataTables-driver">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>File Name</th>         
                            <th>Action</th>
                        </tr>
                    </thead>
         
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')

    $('#dataTables-driver').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('jlogistic/drivertimesheetajax') }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { data: 'id' },
            { data : 'created_at' },
            { data : 'file_name' },
            { data : 'Action' },
        ]
    });

    $('#generate').on('click', function() {
        if (confirm('Generate new time sheet?')) {
            $.ajax({
                type: 'POST',
                url: '/generatedrivertimesheet',
                dataType: "json",
                success: function(resultData) { 
                    alert('New Time Sheet Generated.');
                    $('#dataTables-driver').DataTable().ajax.reload();
                }
            });
        }
    });

@stop