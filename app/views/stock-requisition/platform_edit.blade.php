@extends('layouts.master')

@section('title') Stock Requisition Platform @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Platform
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    @if(isset($list))
{{ Form::open(array('url'=>'stock-requisition/platformupdate/'.$list->id , 'class' => 'form-horizontal form-submit')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                 <div class='form-group'>
                {{ Form::label('platform_title', 'Platform Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <input type="text" id="platform_name" name="platform_name" class="form-control" required ='required' value="{{$list->platform_name}}">
                </div> 
                </div>
             
                 <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Status *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <div class="input-group">
                              <select name='status' class="form-control" required><option value='0' <?php if($list->status==0){ echo 'selected="selected"';} ?>>Inactive</option><option value='1' <?php if($list->status==1){ echo 'selected="selected"';} ?>>Active</option></select>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                </div> 
                <hr/>
                <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
            </div>

        </div>
    </div>
    {{ Form::close() }}
@endif


</div>
@stop
@section('script')
    localStorage.clear();
      $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

@stop