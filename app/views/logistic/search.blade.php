@extends('layouts.master')
@section('title', 'Search') @stop
@section('content')

<!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>


<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Search
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}search"><i class="fa fa-refresh"></i></a>
                </span>
            </h1>
        </div>
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
                    <form method="POST" action="/jlogistic/search">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="Date">Date</label>
                                    <select class="form-control" name="date_option">
                                        <option name="batch_date" value="batch_date"<? if(@$_POST['date_option'] == 'batch_date') { echo 'selected = \"selected\"'; } ?>>Batch Date</option>
                                        <option name="accept_date" value="accept_date"<? if(@$_POST['date_option'] == 'accept_date') { echo 'selected = \"selected\"'; } ?>>Accept Date</option>
                                    </select>
                                </div>
                            </div>
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
                                        <input type='text' class="form-control" name="to_date" value="<?php echo (Input::get('to_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="Date">Driver</label>
                                    <select class="form-control" name="drivers">
                                    <option name="default"></option>
                                    <?php 
                                    $driver = DB::table('logistic_driver')
                                            ->select('id', 'username')->get();
                                            foreach ($driver as $key => $value) {                               
                                    ?>

                                   <option name="names" value="<?php echo $value->id; ?>" <? if(@$_POST['drivers'] == $value->id) { echo 'selected = \"selected\"'; }?>><?php echo ucwords($value->username); ?></option>
                                   <?php } ?>
                                    </select>
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
                                                   <input type='checkbox' name='status[]' value='4'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('4', $_POST['status'])) echo 'checked="checked"'; ?>checked> Sent
					                           </label>
					                        </div>                                                           
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='0'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('0', $_POST['status'])) echo 'checked="checked"'; ?>> Pending
                                                </label>
                                            </div>
				                            <div class="col-lg-2">
				                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='1'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('1', $_POST['status'])) echo 'checked="checked"'; ?>> Sending
				                                </label>
				                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='2'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('2', $_POST['status'])) echo 'checked="checked"'; ?>> Returned
                                                </label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='3'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('3', $_POST['status'])) echo 'checked="checked"'; ?>> Undelivered
                                                </label>
                                            </div> 
				                            <div class="col-lg-2">
				                                <label class="checkbox-inline">
                                                    <input type='checkbox' name='status[]' value='5'<?php if(isset($_POST['status']) && is_array($_POST['status']) && in_array('5', $_POST['status'])) echo 'checked="checked"'; ?>> Cancelled
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Search Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="search">
                            <thead>
                                <tr>
                                    <th class="col-lg-1">ID</th>
                                    <th class="col-lg-1">Logistic ID</th>
                                    <th class="col-lg-1">SKU</th>
                                    <th class="col-lg-1">Name</th>
                                    <th class="col-lg-1">QTY order</th>
                                    <th class="col-lg-1">QTY to assign</th>
                                    <th class="col-lg-1">QTY to send</th>
                                    <th class="col-lg-1">Status</th>
                                    <th class="col-lg-1">DO NO</th>
                                    <th class="col-lg-1">Driver</th>
                                </tr>
                            </thead>
                            <tbody>                           
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
      
        </div>
    </div>
</div>
@stop

@section('script')
    $('#search').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('jlogistic/searchlisting?'.http_build_query(Input::all())) }}",
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
            { "data" : "7" },         
            { "data"  : "10" },         
            { "data"  : "9", "className" : "text-capitalize" },
            
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