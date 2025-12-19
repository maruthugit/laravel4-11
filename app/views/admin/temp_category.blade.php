@extends('layouts.master')

@section('title', 'Temp Move Category')

@section('content')
<?php 
$tempcount = 1;
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Temp Move Category</h1>
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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Move Category Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'temp_category/store', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}                        
                        <div class="form-group @if ($errors->has('csv')) has-error @endif">
                            {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="csv" id="csv">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                                {{ $errors->first('csv', '<p class="help-block">The file is required</p>') }}
                                <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('template', 'Template File', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static">{{ HTML::link(asset('/').'temp_category/files/template_category.csv', "template_category.csv", array('target'=>'_blank')) }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <!-- {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }} -->
                                <input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
                                @if (Session::get('role_id') == '1')
                                    {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }}
                                    <!-- <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button> -->
                                @endif
                            </div>
                        </div>
                    {{ Form::close() }}
                        <div <?php if(count($job) <= 0) { ?>style="display:none"<?php } ?> >
                            <hr>
                            <div class="form-group">
                                {{ Form::label('list_job', 'List of Job in Queue', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>Uploaded File</th>
                                                    <th>Queuing File</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $fullpath = "files/".$file_log;
                                                echo " <a href=$fullpath>$logfile</a> ";
                                                ?>
                                                @foreach($job as $jobrow)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$jobrow->id}}</td>
                                                        <td>{{ HTML::link(asset('/').'temp_category/files/'.$jobrow->in_file, $jobrow->in_file, array('target'=>'_blank')) }}</td>
                                                        <td>{{ HTML::link(asset('/').'temp_category/files/original_'.$jobrow->in_file, 'original_'.$jobrow->in_file, array('target'=>'_blank')) }}</td>
                                                        <td>{{$jobrow->remark}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->           
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    @if (Session::get('role_id') == '1')
                                    {{ Form::open(['url' => 'temp_category/movecategory', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true,  'target' => '_blank']) }}
                                    {{ Form::submit('Process Job in Queue', ['class' => 'btn btn-large btn-primary']) }}
                                    {{ Form::close() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@stop


