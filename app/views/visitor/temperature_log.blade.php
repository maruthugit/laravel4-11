@extends('layouts.master')

@section('title') Visitor Management @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">MCO QR Code Scan Temperature Log
              <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}visitor/temperaturelog"><i class="fa fa-refresh"></i></a>
              </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
        </div>
        <div class="panel-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="text" name="date" id="datetimepicker" class="form-control" value="{{Input::get('date')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Type</label>
                            {{ Form::select('type', ['any' => 'Any', 'staff' => 'Staff', 'visitor' => 'Visitor'], Input::get('type'), ['id' => 'type', 'class' => 'form-control', 'tabindex' => 4]) }}
                        </div>
                    </div>
                </div>
                {{ Form::submit('Search', ['class' => 'btn btn-primary', 'tabindex' => 5]) }}
                <!-- <button class="btn btn-default" id="export">Export</button> -->
                <a href="{{ URL::to('visitor/exporttemperaturelog?'.http_build_query(Input::all())) }}" class="btn btn-default" id="export">Export</a>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Visitor Listing </h2>
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none">
                <table class="table table-striped table-bordered table-hover" id="dataTables-vistor" >
                    <thead>
                        <tr>
                            <th class="col-sm-1">ID</th>
                            <th class="col-sm-3">Name</th>
                            <th class="col-sm-2 text-center">Temperature</th>
                            <th class="col-sm-2 text-center">Phone</th>
                            <th class="col-sm-1 text-center">Type</th>
                            <th class="col-sm-3 text-center">Logged At</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>


</div>

<script>
    $(function() {
        $('#datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });


</script>

@stop

@section('script')

$('#dataTables-vistor').dataTable({
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('visitor/temperaturelog?'.http_build_query(Input::all())) }}",
    "order": [[0,'desc']],
    "columnDefs": [{
        "targets": "_all",
        "defaultContent": ""
    }],
    "columns": [
        { "data": "0", "searchable" : false },
        { "data": "1" },
        { "data": "2" },
        { "data": "3" },
        { "data": "4" },
        { "data": "5", "orderable" : false, "searchable" : false },
        { "data": "6" }
        
    ]
});

$('#1export').on('click', function(e) {
    e.preventDefault();
    $.ajax({
        'url': "{{ URL::to('visitor/exporttemperaturelog?'.http_build_query(Input::all())) }}",
        'success': function(result) {
            if (result.message) {
                alert(result.message);
            }
        }
    })
})


@stop