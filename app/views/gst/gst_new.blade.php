@extends('layouts.master')
@section('content')

<?php

$tempcount = 1;

?>


<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> GST Report </h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Generate Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'gstreport/newreport', 'class' => 'form-horizontal')) }}
                        <div class="form-group @if ($errors->has('report_year')) has-error @endif">
                        {{ Form::label('report_year', 'Year', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('report_year', $row['report_year'], array('required'=>'required', 'placeholder' => 'e.g. 2015', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('report_year')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('report_month')) has-error @endif">
                        {{ Form::label('report_month', 'Month', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('report_month', $row['report_month'], array('required'=>'required', 'placeholder' => 'e.g. 01', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('report_month')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('type', 'Type', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{Form::select('type', array('-' => 'Monthly', 'month' => 'Quarter'), $row['type'], ['class'=>'form-control'])}}
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{ Form::button('Generate', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                            </div>
                        </div>

                        
                        {{Form::input('hidden', 'generate_new', 'true')}}
                        {{ Form::close() }}
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

