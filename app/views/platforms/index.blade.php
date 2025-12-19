@extends('layouts.master')

@section('title') Platform Management @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Platform Management
              <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}platforms/create"><i class="fa fa-refresh"></i></a>
              </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
 @if (Session::has('success'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
@if (Session::has('message'))
                <div class="alert alert-danger">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
    {{ Form::open(array('url' => 'platforms/platforms' , 'class' => 'form-horizontal form-submit')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Create Platform</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                {{ Form::label('platform_title', 'Platform Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <input type="text" id="platform_name" name="platform_name" class="form-control" required ='required'>
                </div> 
                </div>
             <div class="form-group @if ($errors->has('platform_username')) has-error @endif">
                            {{ Form::label('platform_username', 'Platform Username *', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="platform_user_id" name="platform_user_id">
                            <div class="col-lg-4">
                                <div class="input-group">
                                <input type="text" id="platform_username" name="platform_username" class="form-control" placeholder="" readonly required ='required'>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/platforms/ajaxcustomer"><i class="fa fa-plus"></i> Select User</button>
                                </span>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                </div>
                 <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Status *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <div class="input-group">
                              <select name='status' class="form-control" required><option value='0'>Inactive</option><option value='1'>Active</option></select>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                </div> 
                <hr/>
            </div>

            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
    {{ Form::open(array('url' => 'platforms/store' , 'class' => 'form-horizontal form-submit')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Create Store</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                {{ Form::label('store_title', 'Store Name *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <input type="text" id="store_name" name="store_name" class="form-control" required ='required'>
                </div> 
                </div>
                <div class='form-group'>
                {{ Form::label('store_id', 'External Store ID *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <input type="text" id="store_id" name="store_id" class="form-control" required ='required'>
                </div> 
                </div>
             <div class="form-group">
                            {{ Form::label('platform', 'Store Platform*', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <div class="input-group">
                                <select name='platform_id' class="form-control" required>
                                    <option value=''>Select Platform</option>
                                    @if($platforms)
                                    @foreach($platforms as $value)
                                    <option value='{{$value->id}}'>{{$value->platform_name}}</option>
                                    @endforeach
                                    @endif
                                    </select>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                </div>
                 <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Status *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <div class="input-group">
                              <select name='status' class="form-control" required><option value='0'>Inactive</option><option value='1'>Active</option></select>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                </div> 
                <hr/>
            </div>

            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

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