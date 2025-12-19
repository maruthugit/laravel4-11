@extends('layouts.master')

@section('title') General Report @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> General Report
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}inventory"><i class="fa fa-refresh"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>Inventory Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'warehouse/exportreport', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                      

                        <div class="form-group">
                            {{ Form::label('', 'Report Type', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                               <select class="form-control" id="report_type" name="report_type"> 
                                   <option value="1" selected="selected">By Stock Inventory</option>
                                   <option value="2">By Stock In</option>
                                   <option value="3">By Stock Out</option>
                                   <option value="4">By Return</option>
                               </select>
                            </div>
                        </div>
                       
                        <div class="form-group datetimepicker @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', 'Range Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_from">
                                   

                                    {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'required'=>'required', 'class' => 'form-control', 'tabindex' => 1]) }}

                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_to">
                                    {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'required'=>'required', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Export</button>
                            </div>
                        </div>

                        <hr />

                        @if ($query['filename'] != NULL)
                        <div class="form-group" id="refer">
                        {{ Form::label('filename', 'File Name', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['filename'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('emailto', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['email'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('Count', 'Total Record', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['count'] }}</p>
                            </div>
                        </div>

                        <div class="form-group" id="refer">
                        {{ Form::label('SQL', 'SQL Statement', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">
                                <p class="form-control-static">{{ $query['statement'] }}</p>
                            </div>
                        {{ Form::close() }}
                        </div>

                        <hr />
                        @endif

                     
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
@section('inputjs')

<script>

$(document).ready(function() {
    
   $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    var reporttype = $('#report_type').val();
        
    if(reporttype == 1){
      $('#transaction_from').prop('required', false);
      $('#transaction_to').prop('required', false);
      $('.datetimepicker').hide();

    }

    $('body').on('change', '#report_type', function() {

        var reptype = $('#report_type').val();

        $('#transaction_from').val('');
        $('#transaction_to').val('');
        
        if(reptype != 1){

            $('.datetimepicker').show();
            $('#transaction_from').prop('required', true);
            $('#transaction_to').prop('required', true);
        }
        else if(reptype == 1){
            $('#transaction_from').prop('required', false);
            $('#transaction_to').prop('required', false);
            $('.datetimepicker').hide();
        }


    });


    
});

</script>
@stop




