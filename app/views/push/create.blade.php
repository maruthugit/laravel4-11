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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> New Notification</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'push/store', 'class' => 'form-horizontal','enctype'=>'multipart/form-data')) }}
                        
                             <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('paltform', 'Platform', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input type="radio" name="platform" value ="" checked="checked"  > ALL
                                    <input type="radio" name="platform" value ="1" style="margin-left:10px;"> Android
                                    <input type="radio" name="platform" value ="2" style="margin-left:10px;"> iOS

                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('main_category', 'Main Category *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select id="main_category" name="category" class="form-control" >
                                        <?php foreach ($parentCategory as $key => $value) { ?>
                                            <option id="<?php echo $value->id; ?>" value="<?php echo $value->id; ?>"><?php echo $value->description; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('sub_category', 'Sub Category *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select name="sub_category" id="sub_category" class="form-control" >
                                        <option id='0' value=''> - </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('title', 'Title *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" value="{{ Input::old('title') }}" name="title">
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('message')) has-error @endif">
                                {{ Form::label('message', 'Message *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <textarea id="message" rows="2"  name="message" autofocus="autofocus" class="form-control">{{ Input::old('message') }}</textarea>
                                    <span class="help-block">{{ $errors->first('message') }}</span>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('link_id', 'Link ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input class="form-control" type="text" value="{{ Input::old('title') }}" name="link_id">
                                    <p class="help-block">Link ID is related id number . Ex: Product ID , Category ID</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('title')) has-error @endif">
                                {{ Form::label('notification_sound', 'Notification Sound', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select class="form-control" name="sound_type">
                                        <?php foreach ($sound as $key => $valueSound) { ?>
                                            <option id="<?php echo $valueSound->voice_code; ?>" value="<?php echo $valueSound->voice_code; ?>"> <?php echo $valueSound->voice_code." - ".$valueSound->description; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('filename_image')) has-error @endif">
                                {{ Form::label('Image', 'Image', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input class="" id="imgInp" type="file" name="filename_image">
                                    <p class="help-block">Image size : Width : 120px Height : 60px;</p>
                                    <img  class="img-thumbnail" id="blah" style="margin-top: 10px;display: none;" src="" alt="" >
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('begin')) has-error @endif">
                                {{ Form::label('begin', 'Send Date/Time', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <div class="input-group" id="datetimepicker">
                                        {{ Form::text('begin', Input::old('begin', date('Y-m-d H:i:s')), array('class' => 'form-control')) }}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
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

$("#main_category").change(function(){

    var main_category_id = $(this).val();

    $.ajax({
        method: "POST",
        url: "/push/subcategory",
        data: {
            "main_category_id":main_category_id
        },
        beforeSend: function(){
        $('.loading').show();
            console.log('Migrating..');
        },
        success: function(data) {
        
        var option_str = '';
        
        if(data.length > 0){
        $.each(data, function (index, value) {
                option_str = option_str + '<option id="'+value.id+'" value="'+value.id+'">'+value.description+'</option>';
            })
           
        }else{
            option_str = '<option id="0" value="0"> - </option>';
        }
        
         $("#sub_category").html(option_str);
            
            
        }
    })
});
  



function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
      $("#blah").show();
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#imgInp").change(function() {
  readURL(this);
});

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
