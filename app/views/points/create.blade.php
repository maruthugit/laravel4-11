@extends('layouts.master')

@section('title', 'Reward Points')

@section('content')
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Reward Points</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-gift"></i> Point Details</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => "points", 'method'=> 'POST', 'class' => 'form-horizontal']) }}
                        <div class="form-horizontal">
                            <div class="form-group @if ($errors->has('type')) has-error @endif">
                                <label class="col-lg-2 control-label">Point</label>
                                <div class="col-lg-3">
                                    {{ Form::text('type', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('type', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('earn_rate')) has-error @endif">
                                <label class="col-lg-2 control-label">Earn Rate</label>
                                <div class="col-lg-3">
                                    {{ Form::text('earn_rate', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('earn_rate', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('redeem_rate')) has-error @endif">
                                <label class="col-lg-2 control-label">Redeem Rate</label>
                                <div class="col-lg-3">
                                    {{ Form::text('redeem_rate', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('redeem_rate', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                                <label class="col-lg-2 control-label">Status</label>
                                <div class="col-lg-3">
                                    {{ Form::select('status', $statusOptions, null, ['class' => 'form-control']) }}
                                    {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('deactivate')) has-error @endif">
                                <label class="col-lg-2 control-label">Deactivate Usernames</label>
                                <div class="col-lg-3">
                                    <textarea id="deactivate" rows="5" name="deactivate" class="form-control"></textarea>
                                    <span class="help-block">Multiple usernames separated by comma.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
    								<input class="btn btn-default" type="reset" value="Reset">
    								@if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
    									<button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
    								@endif
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
