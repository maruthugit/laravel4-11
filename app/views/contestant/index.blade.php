@extends('layouts.master')

@section('title') Contestants @stop

@section('content')
<div id="page-wrapper">
    
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Contestant<span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}contestant"><i class="fa fa-refresh"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif

     <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Contestants Listing </h3>
        </div>
        <div class="panel-body">
            <div class="dataTable_wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTables">
         
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contest</th>
                            <th>Name</th>
                            <th>Invoice Image</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Action</th>
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
    $('#dataTables').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/contestant/contestantslist') }}",
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
            { "data" : "4" },
            { "data" : "5" },
            { "data" : "6" },
        ]
        
    });

    
@stop