@extends('layouts.master')

@section('title', 'Creat Pallet')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock In
                <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('pallet') }}"><i class="fa fa-reply"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-xing"></i> Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => 'pallet/stockin', 'method' => 'post','files' => 'true','enctype'=>'multipart/form-data']) }}
                        <div class="form-horizontal">
                           

               


                            <div class="form-group {{ $errors->first('expired_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label"> Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker3'>
                                      
                                        <input type='text' class="form-control" name="date" value="<?php echo (Input::get('date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>


                            <div class="form-group required {{ $errors->first('pallet_Description', 'has-error') }}">
                                <label class="col-lg-2 control-label">Quantity </label>
                                <div class="col-lg-3">
                                    {{ Form::text('quantity', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('quantity', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            
                         <div class="form-group required">
                                <label class="col-lg-2 control-label">Pallet </label>
                                <div class="col-lg-3">
                 {{ Form::select('pallet_id', $pallets, null, ['class' => 'form-control']) }}                                       
                                </div>
                            </div>

                               <div class="form-group required">
                                <label class="col-lg-2 control-label">Supplier </label>
                                <div class="col-lg-3">
                         {{ Form::select('supplier_name', $suppliers, null, ['class' => 'form-control']) }}                                    
                                </div>
                            </div>


                    

                         <div class="form-group required {{ $errors->first('pallet_Description', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks </label>
                                <div class="col-lg-3">
                                    {{ Form::textarea('remarks', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                        <div class="form-group @if ($errors->has('file_name')) has-error @endif">
                                {{ Form::label('file', 'signature', array('class'=> 'col-lg-2 control-label' )) }}
                                <div class="col-lg-5">
                                   
                                   
                                    <input id="newfile" type="file" name="file_name" class="form-control"  required = 'required'>
                                   
                                    {{ $errors->first('file_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                   
  <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                 
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                  
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
<script src="../../../js/fileinput.min.js"></script>    

@section('script')



  
 $("#newfile").fileinput({
      
        allowedFileExtensions: ["jpg", "png","pdf"]
  
    });

 $(document).ready(function() {
    


    $('#datetimepicker3').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});

 $("#newfile").fileinput({
      
        allowedFileExtensions: ["jpg", "png","pdf"]
  
    });
@stop
