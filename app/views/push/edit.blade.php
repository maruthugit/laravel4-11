@extends('layouts.master')

@section('title', 'Push Notification')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Push Notification</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Notification</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'push/update/' . $notification->id, 'class' => 'form-horizontal')) }}
                            <div class="form-group @if ($errors->has('message')) has-error @endif">
                                {{ Form::label('message', 'Message *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <textarea id="message" rows="2" maxlength="34" name="message" autofocus="autofocus" class="form-control">{{ $notification->message }}</textarea>
                                    <span class="help-block">{{ $errors->first('message') }}</span>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('begin')) has-error @endif">
                                {{ Form::label('begin', 'Send Date/Time', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <div class="input-group" id="datetimepicker">
                                        {{ Form::text('begin', date('Y-m-d H:i:s'), array('class' => 'form-control')) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                                    <p class="help-block">Date / time has been resetted, update if necessary.</p>
                                    {{ $errors->first('begin', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('recipient')) has-error @endif">
                                {{ Form::label(null, 'Recipient *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="recipient" id="recipient-all" value="all" @if (Input::old('recipient') == 'all') checked @else @if (Input::old('recipient') != 'specify') checked @endif @endif> All
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="recipient" id="recipient-specify" value="specify" @if (Input::old('recipient') == 'specify') checked @endif> Specify Users
                                    </label>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('recipient-usernames')) has-error @endif">
                                {{ Form::label('recipient-usernames', 'Recipient Usernames', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <textarea id="recipient-usernames" rows="5" name="recipient-usernames" autofocus="autofocus" class="form-control">{{ Input::old('recipient-usernames') }}</textarea>
                                    <span class="help-block">Multiple recipients separated by comma.</span>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <a class="btn btn-default" href="/push"><i class="fa fa-reply"></i> Cancel</a>
                                    <button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
if ($('#recipient-all').attr('checked')) {
    $('#recipient-usernames').attr('readonly', 'readonly');
}

if ($('#recipient-specify').attr('checked')) {
    $('#recipient-usernames').removeAttr('readonly');
}

$('#recipient-all').click(function () {
    $('#recipient-usernames').val('').attr('readonly', 'readonly');
});

$('#recipient-specify').click(function () {
    $('#recipient-usernames').removeAttr('readonly');
});
@stop
