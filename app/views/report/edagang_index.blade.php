@extends('layouts.master')
@section('title', 'Report')
@section('content')
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <style>
        #refer { display: none; } 
    </style>

    <div id="page-wrapper">
        <div class="col-lg-12">
            <h1 class="page-header">EDAGANG Weekly Seller Report</h1>
        </div>
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
                            {{ Form::open(['url'=>'report/edagang', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
                            <div class="form-group">
                                {{ Form::label('format', 'Seller Name Format', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-8"><input type="file" name="format" id="format" class="form-control" accept=".txt"></div>
                                <div class="col-lg-2" style="text-align: right;"><button class="btn btn-primary" type="apply">Apply Format</button></div>
                            </div>

                            @if(file_exists($filepath))
                            <div class="form-group">
                                {{ Form::label('format', 'Current Seller Name Format', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2"><button class="btn btn-primary" type="download">Download Format</button></div>
                                <div class="col-lg-2"><button class="btn btn-primary" type="clear">Remove Format</button></div>
                            </div>
                            @endif
                            
                            <div class="form-group">
                                {{ Form::label('plateform', 'Platform', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <select class="form-control" name="plateform">
                                        <option value="all">All Plateform</option>
                                        @foreach($plateform as $input => $name)
                                        <option value="{{ $input }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('created')) has-error @endif">
                                {{ Form::label('created', 'Date', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                    {{Form::text('time_s', date('Y-m-d', strtotime("yesterday")), ['id' => 'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control', 'onfocus' => 'this.oldvalue = this.value;'])}}
                                </div>
                                <div class="col-lg-2">
                                    {{Form::text('time_e', date('Y-m-d'), ['id' => 'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control', 'onfocus' => 'this.oldvalue = this.value;'])}}
                                </div>
                                <p class="help-block">Date Inserted: Date input to system.</p>
                            </div>
                            
                            <div class="form-group">
                                {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10"><button class="btn btn-primary" type="submit">Generate</button></div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    $(function() {
        $("#datepicker").datepicker({ dateFormat: "yy-mm-dd" }).val();
        $("#datepicker2").datepicker({ dateFormat: "yy-mm-dd" }).val();
        $(document).on('click', 'button[type="submit"]', function(e){
            var d1 = Date.parse($("#datepicker").val());
            var d2 = Date.parse($("#datepicker2").val());
            var d = d2 - d1;
            if(d < 0 || d > 604800000){
                alert('Maximum date select range is one week only');
                e.preventDefault();
            }
        });
    });

    $(document).on('click', 'button[type="apply"], button[type="clear"], button[type="download"]', function(e){
        $('#add').append('<input type="hidden" name="' + $(this).attr('type') + '" value="true">');
        $('button[type="submit"]').submit();
    });
@stop