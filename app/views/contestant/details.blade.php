@extends('layouts.master')
@section('title', 'Contestant')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Contestant Details</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Contestant Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group">
                            {{ Form::label('contest', 'Contest', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$contest}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('name', 'Contestant Name', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$name}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', 'Email', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$email}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('contact', 'Contact No', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$contact}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('invoice_img', 'Invoice Image', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <img src="<?php echo '/images/contestant/'.$invoice_img ?>" width="300">
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- /.panel-body -->
            </div>
               <!-- /.panel -->

            @if ($survey1_answer != null && $survey1_why != null && $survey2_answer != null && $survey2_why != null)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Survey Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group">
                            {{ Form::label('survey1_answer', 'Question 1', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$survey1_answer}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('survey1_why', 'Question 1 Why', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$survey1_why}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('survey2_answer', 'Question 2', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$survey2_answer}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('survey2_why', 'Question 2 Why', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{$survey2_why}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- /.panel-body -->
            </div>
            @endif
        </div>
    </div>
    {{ Form::close() }}
</div>

@stop