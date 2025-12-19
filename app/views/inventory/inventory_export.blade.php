@extends('layouts.master')

@section('title') Inventory @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Inventory Report
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
                        {{ Form::open(array('url'=>'inventory/report', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                      

                        <div class="form-group">
                            {{ Form::label('', 'Report Type', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                               <select class="form-control" id="report_type" name="report_type">
                                   <option value="1" selected="selected">By Invoice Transaction</option>
                                   <option style="display:none;" value="2">By Stock Product</option>
                               </select>
                            </div>
                        </div>
                        <div class="form-group" style="display:none;">
                            {{ Form::label('', 'Product JC Code', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                <textarea class="form-control col-lg-12" name="jcode" rows="5" ></textarea>
                                <p class="help-block">For more than 1 product use ',' to separate the product . (Ex: JC1762,JC1040,JC2319)</p>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', 'Invoice Range Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_from">
                                    {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_to">
                                    {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'class' => 'form-control', 'tabindex' => 1]) }}
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
    
});

</script>
@stop




