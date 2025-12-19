@extends('layouts.master')

@section('title') Product Update @stop

@section('extra-css')
<style>
    td {
        text-align: center;
    }
</style>

@stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Cost/Selling Price - Import Product Details</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    @if (Session::has('message'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
    </div>
    @endif
        
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa fa-thumbs-up"></i> {{ $message }}
    </div>
    @endif

    {{ Form::open(['role' => 'form', 'url' => '/product-update/import', 'class' => 'form-horizontal', 'files' => true]) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Upload Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">

                <div class='form-group'>
                    {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Select file</span>
                                <span class="fileinput-exists">Change</span>
                                <input type="file" name="csv" id="csv">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>
                        <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
                    </div>
                </div>

                 <div class='form-group'>
                    <input type="hidden" name="import" id="import">
                    <div class="col-lg-10 col-lg-offset-2">
                        {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Import CSV File Guides</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <h3><u>Editing CSV file</u></h3>

                <!-- <p class="text-warning">* Yellow highlights: For referrence purpose.</p>
                <p class="text-danger">* Red highlights&nbsp;&nbsp;&nbsp; : Important columns, CANNOT edit.</p>
                <p class="text-success">* Green highlights: CAN edit</p> -->
                <p class="text-danger">* Please follow the following csv file template when importing.</p>
                <div>
                    {{ HTML::image('media/product_import_template.png', 'Template', array('class' => 'img-responsive')) }}
                </div>
                <br>
                <p class="text-default">* <b>NOTE: </b><br>If there are multiple similar price_id please set the actual price and promo price for the first occurence. <br>Actual price and promo price for subsequent similar price_id have to set -1 as shown above.</p>
                <br>
                <h3><u>Saving CSV file</u></h3>

                <p class="text-info"><b>For Windows: </b>  Save as <b>"CSV - Comma Delimited (.csv)"</b> before import the CSV.</p>
                <p class="text-info"><b>For Mac &nbsp;&nbsp;&nbsp;&nbsp;: </b> Save as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
            </div>  
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Import Listing</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
               <table class="table table-bordered" id="ptb">
                    <thead>
                        <tr>
                            <th class="col-sm-1 text-center">ID</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">File name</th>
                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Imported By</th>
                            <th class="hidden-xs hidden-sm col-sm-2 text-center">Imported At</th>
                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
</div>
@stop

@section('script')
$('#ptb').dataTable({
    "autoWidth" : false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('product-update/importlist') }}",
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
    ]
    
});
@stop