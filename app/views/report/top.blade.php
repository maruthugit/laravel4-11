@extends('layouts.master')
@section('title', 'Report')
@section('content')

<?php

$tempcount = 1;

?>

<!-- For datepicker in create new report -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    });
</script>

<style>
#refer {
    display: none;
} 
</style>

<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Top Item Sold Report </h1>
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
                        {{ Form::open(array('url'=>'report/top', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                        {{ Form::label('email', 'Email To', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{Form::text('email', '', array('required'=>'required', 'placeholder' => 'abc@jocom.my, 123@jocom.my', 'class'=>'form-control'))}}
                                <p class="help-block" for="inputError">{{$errors->first('email')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('sort_type')) has-error @endif">
                        {{ Form::label('sort_type', 'By Type', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{ Form::select('sort_type', array('0' => 'Sort By Total', '1' => 'Sort By Unit'), "0", ['class' => 'form-control']) }}
                                <p class="help-block" for="inputError">{{$errors->first('sort_type')}}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group @if ($errors->has('customer')) has-error @endif">
                        {{ Form::label('customer_name', '[Optional] Customer', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-4">
                                {{ Form::text('customer', Input::get('customer'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'customer', 'placeholder' => 'Customer Name or ID']) }}
                                <div id="customerAutoComplete" class="list-group autocomplete"></div>
                                <p class="help-block">Blank for all</p>
                                <p class="help-block" for="inputError">{{$errors->first('customer')}}</p>
                            </div>
                        </div>

                         <div class="form-group @if ($errors->has('topcount')) has-error @endif">
                        {{ Form::label('topcount', '[Optional] Top', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-2">
                                {{Form::text('topcount', '', array('placeholder' => '20', 'class'=>'form-control'))}}
                                <p class="help-block">Blank for all</p>
                                <p class="help-block" for="inputError">{{$errors->first('topcount')}}</p>
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', '[Optional] Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                {{ Form::select('created', array('0' => 'Not Applicable', '1' => 'From Date to Date Ordered'), "0", ['class' => 'form-control']) }}
                            </div>
                            <div class="col-lg-2">
                                {{Form::text('created_from', date('Y-m-d', strtotime("yesterday")), array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                            </div>
                            <div class="col-lg-2">
                                 {{Form::text('created_to', date('Y-m-d'), array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
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
                                                            <a class="btn btn-large btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}process/report/{{$job->id}}/?type=top">Process Now</a>
                                                            <a class="btn btn-danger" title="" data-toggle="tooltip" href="{{asset('/')}}process/cancel/{{$job->id}}/?type=top">Cancel</a>
                                                            <!-- {{ Form::open(['url' => 'abc/def', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
                                                            {{ Form::submit('Process Now', ['class' => 'btn btn-large btn-primary']) }}
                                                            {{ Form::close() }} -->
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
    var timer;

    $('#customer').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#customer').val()) {
                $.ajax({
                    url: '{{ url('report/customersearch?keyword=') }}' + $('#customer').val() + '&limit=10',
                    success: function (result) {
                        if ($('#customer').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#customerAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var customerName = candidate.full_name;

                                if (customerName.search($('#customer').val())) {
                                    var clickAction = "$('#customerAutoComplete').html(''); $('#customer').val('" + candidate.username + "'); return false;";

                                    $('#customerAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.full_name + ' ('  + candidate.username + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#customer').is(':focus') && size > 0) {
                                $('#customerAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#customerAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#customerAutoComplete').html('').hide();
    });
@stop
 



