@extends('layouts.master')

@section('title') Goods Defect Form @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Goods Defect Form Report
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}gdf/report"><i class="fa fa-refresh"></i></a>
            </span></h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>Goods Defect Form Report</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'gdf/reportdownload', 'id' => 'add', 'class' => 'form-horizontal','method'=>post,'files' => true)) }}
                      

          <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('type', 'Type ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='type' id='type'>
                                   <option value="All">All</option>
                                    <option value="Exchanged">Exchanged</option>
                                    <option value="Returned To Vendor">Returned To Vendor</option>
                                    <option value="Issue Credit Note">Issue Credit Note</option>
                                    <option value="Expired / Write Off">Expired / Write Off</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group @if ($errors->has('created')) has-error @endif">
                        {{ Form::label('created', 'GDF Range Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_from">
                                    {{ Form::text('transaction_from', Input::get('transaction_from'), ['id' => 'transaction_from', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="input-group" id="datetimepicker_to">
                                    {{ Form::text('transaction_to', Input::get('transaction_to'), ['id' => 'transaction_to', 'class' => 'form-control', 'tabindex' => 1]) }}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Export</button>
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
    </div>


@stop
@section('inputjs')
<script>



$(document).ready(function() {
    
   $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});

</script>
@stop




