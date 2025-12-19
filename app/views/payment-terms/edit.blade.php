@extends('layouts.master')
@section('title', 'Payment Terms')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i>Payment Terms</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/payment-terms/update/' . $payment->id, 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i>Payment Terms Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('period', 'has-error') }}">
                            {{ Form::label('period', 'Period ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('period', $payment->period, ['placeholder' => '', 'class' => 'form-control']) }}
                            {{ $errors->first('period', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('status', 'has-error') }}">
                            {{ Form::label('status', 'Status ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='status' id='status'>
                                    @foreach ($statuses as $key => $value)
                                    <option value='{{ $key }}' <?php if ($key == $payment->status) echo 'selected'?>>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
    <div class='form-group' >
        <div class="col-lg-10" style="padding-bottom:10px;">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
            <!--Under Upgrading .. Wait for a moment.-->
        </div>
    </div>
    @endif
    {{ Form::close() }}
</div>
    
@stop

@section('script')
@stop