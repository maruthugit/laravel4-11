@extends('layouts.master')
@section('title') Batch Reset @stop
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Batch Reset
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic"><i class="fa fa-refresh"></i></a>
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
                    <form method="POST" action="/batch/unassign">
                        <div class="row">
                            <div class="col-lg-3">
                            <label for="from_date">From Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input type='text' class="form-control" onkeydown="return false" name="from_date" value="<?php echo (Input::get('from_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="to_date">To Date</label>
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker2'>
                                        <input type='text' class="form-control" onkeydown="return false" name="to_date" value="<?php echo (Input::get('to_date')); ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                            <label for="from_date">Drivers</label>
                                <div class="form-group">
                                    <select class="driver form-control" name="driver" id="driver">
                                      <option class="default">  </option>
                                      <?php foreach ($driver as $key => $value) { ?>
                                      <option value="<?php echo $value->id; ?>" <?php if(Input::get('driver')==$value->id){ echo "selected";} ?> data-name="<?php echo $value->name;?>"><?php echo $value->name;?></option>
                                      <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    {{ Form::select('status', [
                                        'default' => '',
                                        '0' => 'Pending',
                                        '1' => 'Sending',
                                    ], Input::get('status'), ['id' => 'status', 'class' => 'form-control', 'tabindex' => 4, 'name'=>'status']) }}
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
            <div class="row">
                <div class="col-lg-4">
                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND'))
                    <a class="btn btn-primary" id="batch_unassign" data-status="2" data-user="<?php echo Session::get("username"); ?>">Reset</a> 
                @endif 
                </div>
            </div>  
            <br>        
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Batch Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-data">
                            <thead>
                                <tr>
                                    <!-- <th>
                                        <label class="checkbox-inline">
                                          <input type="checkbox" class="checkbox select-all">
                                        </label>
                                    </th> -->
                                    <th>Assign Date</th>
                                    <th>Batch ID</th>
                                    <th>Logistic ID</th>
                                    <th>Delivery City</th>
                                    <th>Delivery state</th>
                                    <th>Driver Name</th>
                                    <th>Status</th>
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
    $('#dataTables-data').dataTable({
        "autoWidth" : false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('batch/unassignbatch?'.http_build_query(Input::all())) }}",
        "order" : [[0,'desc']],
        "columnDefs" : [{
        "targets" : "_all",
        "defaultContent" : ""
        }],
        "columns" : [
        { "data" : "assign_date"},
        { "data" : "id"},
        { "data" : "logistic_id"},
        { "data" : "delivery_city"},
        { "data" : "delivery_state"},
        { "data" : "name"},
        { "data" : "status" },  
        ]
    });


    $('body').on('click', '#batch_unassign', function(){

        var from_date = $("input[name=from_date]").val();
        var to_date = $("input[name=to_date]").val();
        var driver = $( "#driver option:selected" ).val();
        var name = $( "#driver option:selected" ).attr('data-name');
        var status = $( "#status option:selected" ).val();

        if (confirm('Are you sure you want to unassign ' + name + '?')) {
            $.ajax({
                method: "POST",
                url: "/batch/unassignupdate",
                data: {
                    'from_date':from_date, 
                    'to_date':to_date,
                    'driver':driver,  
                    'status':status, 
                },
                beforeSend: function() {
                  
                },
                success : function(data){
                alert("Successfully Reset!");
                $('#dataTables-data').DataTable().ajax.reload();
                    
                },          
            })

       }
        
    }); 

    $(function () {
                $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
            });
      $(function () {
                $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD  HH:mm:ss'
        });
            });
@stop

@section('inputjs')
<script>
  //checkbox
  $(document).on('change', '.select-all', function() {
    if ($(this).is(':checked')) {
      $('[name="check[]"]').each(function() {
        $(this).prop('checked', true);
      });
    } else {
      $('[name="check[]"]').each(function() {
        $(this).prop('checked', false);
      });
    }
  });

</script>

@stop

