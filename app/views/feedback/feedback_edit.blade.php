@extends('layouts.master')

@section('title') Feedback @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Feedback Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Comment Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => array('feedback/edit', $feedback->id), 'method'=> 'PUT', 'class' => 'form-horizontal')) }}

                            <div class="form-group @if ($errors->has('comment_date')) has-error @endif">
                            {{ Form::label('insert_date', 'Date & Time ', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('insert_date', $feedback->insert_date, array('id' => 'datepicker', 'class'=> 'form-control', 'placeholder' => 'YYYY-MM-DD HH:ii:ss' ,'readonly'=>'readonly')) }}
                                    {{ $errors->first('insert_date', '<p class="help-block">:message</p>') }}
                                    <p class="help-block">Format: YYYY-MM-DD HH:ii:ss</p>
                                </div>
                            </div>

                            <hr />
                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('id', 'Feedback ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <p class="form-control-static">{{$feedback->id}}</p>                                  
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('user', 'Name', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <p class="form-control-static">{{$feedback->full_name}}</p>                                    
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('email', 'Email', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <p class="form-control-static">{{$feedback->email}}</p>                                   
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('contact', 'Contact', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <p class="form-control-static">{{$feedback->mobile_no}}</p>   
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('attachment', 'Attachment', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                @foreach($images as $img)                                                              
                                    <a href="#" class="pop">
                                        <img id="preview" src="/images/feedback/<?php echo $img->attachment; ?>" height="100" width="100">
                                    </a>                                                                                                   
                                @endforeach
                                 </div>
                            </div>
    
                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('type', 'Type', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4" style="padding-top: 4px;">
                                    @if($feedback->type == 0) <span class="label label-danger">Issue</span> @else <span class="label label-success">Feedback</span> @endif
                                    {{ $errors->first('user', '<p class="help-block">:message</p>') }}                                   
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('comment')) has-error @endif">
                            {{ Form::label('comment', 'Comment', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <!--{{ Form::textarea('comment', $feedback->comment, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly'=>'readonly')) }}-->
                                    {{$feedback->comment}}
                                </div>
                            </div>

                            <hr />
                            <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">              
                                      <div class="modal-body">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <img src="" class="imagepreview" style="width: 100%;" >
                                      </div>
                                    </div>
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->  

@stop
@section('inputjs')
<script>

$(function() {
        $('.pop').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');   
        });     
});
</script>
@stop