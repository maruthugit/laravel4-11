@extends('layouts.master')
@section('title') Logistic @stop
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Transaction Management 
            	<span class="pull-right">
            		<a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic"><i class="fa fa-refresh"></i></a>
            	</span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
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
                    <h3 class="panel-title"><i class="fa fa-search"></i> Advanced Search</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="/jlogistic">
                        <div class="row">
                            <div class="col-lg-4">
                            <label for="from_date">From Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' class="form-control" name="from_date" value="<?php echo (Input::get('from_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="to_date">To Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' class="form-control" name="to_date" value="<?php echo (Input::get('to_date')); ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="to_date">SKU</label>
                                <div class="form-group">
                                    <input type="text" name="sku" class="form-control" value="<?php echo (Input::get('sku')); ?>"/>
                                    <span class="help-block">Multiple products separated by comma.</span>
                                </div>
                            </div>        
                        </div>
                        <div class="row">
                                <div class="form-group">
                                    <div class="form-group @if ($errors->has('status')) has-error @endif">
                                        {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label', 'id'=>'checkbox')) }}
                                        <div class="col-lg-10">
                                                                                                      
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='0'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('0', $_POST['status'])) echo 'checked="checked"'; ?>> Pending
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='1'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('1', $_POST['status'])) echo 'checked="checked"'; ?>> Undelivered
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='2'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('2', $_POST['status'])) echo 'checked="checked"'; ?>> Partial Sent
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='3'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('3', $_POST['status'])) echo 'checked="checked"'; ?>> Returned
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                               <label class="checkbox-inline">
                                                   <input type='checkbox' name='status[]' value='4'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('4', $_POST['status'])) echo 'checked="checked"'; ?>> Sending
                                               </label>
                                            </div>  
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='5'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('5', $_POST['status'])) echo 'checked="checked"'; ?>> Sent
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='6'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('6', $_POST['status'])) echo 'checked="checked"'; ?>> Cancelled
                                                </label>
                                            </div>
 
                                        </div>
                                    </div>
                                </div>                          
                        </div>
                        {{ Form::submit('Search', ['class' => 'btn btn-primary']) }}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Logistic Transaction Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-logistic">
                            <thead>
                                <tr>
                                    <th class="col-md-1">Logistic ID</th>
                                    <th class="col-md-1">Tran. ID</th>
                                    <th >Product name</th>
                                    <th class="col-lg-1">Tran. Date</th>
                                    <th class="col-lg-1">Do No</th>
                                    <th class="col-lg-1">Special Message</th>
                                    <th class="col-lg-1">Delivery Name</th>
                                    <th class="col-lg-2">Delivery Address</th>
                                    <th class="col-lg-1">Delivery City</th>
                                    <th class="col-lg-1">Status</th>
                                    <th class="col-lg-1">Action</th>
                                </tr>
                            </thead>
                            <tbody>                             
                            </tbody>
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
    $('#dataTables-logistic').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('jlogistic/listing?'.http_build_query(Input::all())) }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "id"},
        { "data" : "transaction_id"},
        { "data" : "name"},
        { "data" : "transaction_date"},
        { "data" : "do_no" },
        { "data" : "special_msg" },
        { "data" : "delivery_name" },
        { "data" : "address" },
        { "data" : "delivery_city" },
        { "data" : "status" },
        { "data" : "Action", "orderable" : false, "searchable" : false, "className" : "text-center" }
        ]
    });

    $(function () {
                $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD'
        });
            });
      $(function () {
                $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD'
        });
            });
@stop