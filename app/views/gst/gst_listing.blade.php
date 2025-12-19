@extends('layouts.master')
@section('content')

<?php

$tempcount = 1;

?>


<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">GST Report </h1>
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
                    <h3 class="panel-title"><i class="fa fa-search"></i> Search Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'gstreport/search', 'class' => 'form-horizontal')) }}
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
                                {{Form::text('report_month', $row['report_month'], array('required'=>'required', 'placeholder' => 'e.g. 01 OR blank for whole year', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('report_month')}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{ Form::button('Search', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                            </div>
                        </div>

                        <hr />

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
                                                        <th>Modified Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($row)>0)
                                                    {
                                                    ?>
                                                    @foreach($row['file'] as $trans_details)
                                                    <?php
                                                        $temp = explode("/", $trans_details);

                                                        $tempurl = urlencode(base64_encode($trans_details));
                                                        // base64_decode(urldecode($loc));
                                                        
                                                        $tempdate = date("Y-m-d H:i:s",filectime($trans_details));
                                                    ?>
                                                    <tr>
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$temp[3]}}</td>
                                                        <td>{{$tempdate}}</td>
                                                        <td><a class="btn btn-primary" title="" data-toggle="tooltip" href="/gstreport/files/{{$tempurl}}" target='_blank'><i class="fa fa-download"></i></a> <a class="btn btn-danger" title="" data-toggle="tooltip" href="/gstreport/remove?url={{$tempurl}}&report_year={{$row['report_year']}}&report_month={{$row['report_month']}}" onclick="return confirm('Are you sure to delete this file?');"><i class="fa fa-remove"></i></a></td>
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

