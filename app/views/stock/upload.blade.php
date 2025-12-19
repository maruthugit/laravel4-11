@extends('layouts.master')

@section('title') Stock @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock Transfer</h1>
              <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Stock Transfer Media</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                                           
 

                            {{ Form::open(array('url' => array('stock/file', $stock->id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files' => true)) }}        


                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('st_no', 'St_no', array('class'=> 'col-lg-2 control-label ')) }}
                                <div class="col-lg-5">
                                  
                                    <p class="clearfix"></p>
                                    <h4><span class="label label-danger">{{$stock->st_no }}</span></h4>
                                </div>
                            </div>
<br>
                           <div class="form-group "><br>
                                {{ Form::label('ID', 'Stock Transfer Form', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                      <?php
                                        
                                            // $file = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf';
                                            // $encrypted = Crypt::encrypt($file);
                                            // $encrypted = urlencode(base64_encode($encrypted));

                                            $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($stock->st_no) . '.pdf';
                                            $file = ($stock->id)."#".($stock->invoice_no).$path;
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));

                                       
  ?>
                                    <p class="clearfix"></p>
                           

<a class="btn btn-primary" title="" data-toggle="tooltip" href="/stock/forms{{$encrypted}}" target="_blank"><i class="fa fa-download"></i>click here to download as pdf</a>






                                </div>
                            </div>
                            <br>

                             <div class="form-group {{ $errors->first('receivedby', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Recieved By*</label>
                                     <div class="col-lg-2">
                                
                                      
                                        <input type='text' class="form-control" name="receivedby" value="<?php echo $stock->receivedby; ?>" required="required" required/>
                                                                                

                                      
                                
                                </div>
                            </div>
                            <br>
<br>
                            <div class="form-group @if ($errors->has('newfile')) has-error @endif">
                                {{ Form::label('file', 'signature', array('class'=> 'col-lg-2 control-label' )) }}
                                <div class="col-lg-5">
                                   
                                   
                                    <input id="newfile" type="file" name="file" class="form-control"  required = 'required'>
                                   
                                    {{ $errors->first('newfile', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <br>

                           


                        
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
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>    
@stop

@section('script')
  
   
 $("#newfile").fileinput({
      
        allowedFileExtensions: ["jpg", "png","pdf"]
  
    });
    


   
@stop