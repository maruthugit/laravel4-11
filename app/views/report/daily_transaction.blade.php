@extends('layouts.master')
@section('title', 'Report')
@section('content')

<?php

$tempcount = 1;

?>

<style>
#refer {
    display: none;
} 
</style>

<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Daily Transaction Report </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message') OR $message)
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }} {{ $message }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success') OR $success)
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }} {{ $success }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Generate Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/dailytransaction', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}

                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                        {{ Form::label('email', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('email', '', array('required'=>'required', 'placeholder' => 'abc@jocom.my', 'class'=>'form-control'), 'required')}}
                                <p class="help-block" for="inputError">{{$errors->first('email')}}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                        {{ Form::label('from', 'Date From', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="date_from">
                                    {{Form::text('date_from', date('Y-m-d g:i A', strtotime("yesterday 12pm")), array('id'=>'date_from', 'class'=>'form-control', 'required'))}}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('to', 'Date To', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="date_to">
                                    {{Form::text('date_to', date('Y-m-d g:i A', strtotime("today 12pm")), array('id'=>'date_to', 'class'=>'form-control', 'required'))}}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Generate</button>
                            </div>
                        {{Form::input('hidden', 'generate', 'true')}}
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

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Report Listing
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>File Name</th>
                                                        <th>Status</th>
                                                        <th>Request Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($row)>0)
                                                    {
                                                    ?>
                                                    @foreach($row as $job)
                                                    <tr>
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$job->in_file}}</td>
                                                        <td>
                                                            @if($job->status == 0)
                                                                In Queue
                                                            @elseif($job->status == 1)
                                                                In Process
                                                            @endif
                                                        </td>
                                                        <td>{{$job->request_at}}</td>
                                                        <td>
                                                            @if($job->status == 0)
                                                            <a class="btn btn-large btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}process/report/{{$job->id}}/?type=dailytrans">Process Now</a>
                                                            <a class="btn btn-danger" title="" data-toggle="tooltip" href="{{asset('/')}}process/cancel/{{$job->id}}/?type=dailytrans">Cancel</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                                <!-- /.panel -->
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                        
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

    $(function() {

        $('#date_from, #date_to').datetimepicker({
            format: 'YYYY-MM-DD h:mm A'
        });
            
    });
    
@stop