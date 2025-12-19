@extends('layouts.master')

@section('title') Create Zone @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Zones</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('err_message'))
            <div class="alert alert-danger">
                {{ Session::get('err_message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif      
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Zone</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'zone/store', 'class' => 'form-horizontal')) }}

                        <div class="form-group @if ($errors->has('name')) has-error @endif">
                        {{ Form::label('name', 'Zone Name *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('name', Input::old('name'), array('id' => 'name', 'class'=> 'form-control')) }}
                                {{ $errors->first('name', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        
                        <!-- <div class="form-group @if ($errors->has('weight')) has-error @endif">
                        {{ Form::label('weight', 'Maximum Delivery Weight (gram)', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('weight', Input::old('weight'), array('id' => 'weight', 'class'=> 'form-control')) }}
                                {{ $errors->first('weight', '<p class="help-block">:message</p>') }}
                            </div>
                        </div> -->

                        <div class="form-group @if ($errors->has('country')) has-error @endif">
                            {{ Form::label('country', 'Country *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select name="country" class="form-control" id="country">
                                    <option value="" selected="selected"> - Select Country - </option>
                                <?php foreach ($country_options as $key => $value) { ?>
                                    <option value="<?php echo $value->id; ?>" currency="<?php echo $value->business_currency; ?>"><?php echo $value->name; ?></option>
                                <?php } ?>
                                </select>

                            </div>
                        </div>

                        <hr>
                        
                        <div class="form-group">
                        {{ Form::label('init_weight', 'Initial Weight', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2 @if ($errors->has('init_weight')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon">Gram</span>
                                   {{ Form::text('init_weight', null, array('id' => 'init_weight', 'placeholder' => 'Weight', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('init_weight', '<p class="help-block">:message</p>') }}
                            </div>

                            <div class="col-lg-2 @if ($errors->has('init_price')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon currency-label">{{Config::get('constants.CURRENCY')}}</span>
                                   {{ Form::text('init_price', null, array('id' => 'init_price', 'placeholder' => 'Price', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('init_price', '<p class="help-block">:message</p>') }}
                            </div>
                            <p class="help-block">Blank will take the default in Fees Setup!</p>
                        </div>
                        
                        <div class="form-group">
                        {{ Form::label('add_weight', 'Additional Weight', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2 @if ($errors->has('add_weight')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon">Gram</span>
                                   {{ Form::text('add_weight', null, array('id' => 'add_weight', 'placeholder' => 'Weight', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('add_weight', '<p class="help-block">:message</p>') }}
                            </div>

                            <div class="col-lg-2 @if ($errors->has('add_price')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon currency-label">{{Config::get('constants.CURRENCY')}}</span>
                                   {{ Form::text('add_price', null, array('id' => 'add_price', 'placeholder' => 'Price', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('add_price', '<p class="help-block">:message</p>') }}
                            </div>
                            <p class="help-block">Blank will make the maximum charges equal to Initial Weight Price!</p>
                        </div>

                        <hr>

                        <div class="form-group @if ($errors->has('city')) has-error @endif">
                            <label class="col-lg-2 control-label" for="state">States / Cities *</label>
                            <div  class="row">
                                <div class="col-lg-9">
                                    <div id ="state">

                                    </div>
                                    {{ $errors->first('city', '<p class="help-block">:message</p>') }}
                                </div>

                            </div>
                            
                        </div>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <!-- <a class="btn btn-default" href="/zone"><i class="fa fa-reply"></i> Cancel</a> -->
                                {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                <button type="submit" value="Save" class="btn btn-primary">Save</button>
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

@section('script')
    $(document).on("click", ".stateOption", function(e) {
        var id = $(this).val();
        var city   = $("input[name='city_"+id+"[]']");
        var length = city.length;
        var count  = 0;
        //$("input[name='price[][default]']:nth("+$row+")").prop('checked', true); 

        if ($(this).is(':checked')) {
            city.each(function(index) {
                count = count + 1;
                $('#' + id + '_' + count).prop('checked', true); 
            });
        } else {
            city.each(function(index) {
                count = count + 1;
                $('#' + id + '_' + count).prop('checked', false); 
            });
        }
    });

    $(document).on("click", ".cityOption", function(e) {
        var id     = $(this).val();
        var city   = $("input[name='city_"+id+"[]']");
        var length = city.length;
        var count  = 0;
        var tmp    = id.split('_');
        var state  = tmp[0];

        if ($(this).is(':checked')) {
            $('#' + state).prop('checked', true); 
        } else {
            $('#city_' + id).prop('checked', false); 
        }
    });

    $(function() {
        $('#country').on('change', function(e) {
            console.log(e);
            var element = $(this);
            
            var country_id = e.target.value;
            
            $(".currency-label").html($('option:selected', this).attr('currency'));
            
            
            //ajax
            $.get('/places?country_id=' + country_id , function(data){
                $('#state').empty();

                var header;
                var body;
                var panel;
                var done;

                $.each(data, function(index, stateObj){
                    panel   = '<div class="panel panel-default">';
                    header  = '<div class="panel-heading"><h3 class="panel-title"><input name="state[]" id="' + stateObj.id + '" type="checkbox" value="' + stateObj.id + '" class="stateOption"></input> ' + stateObj.name +' </h3></div>';
                    header += '<div id="C_' + stateObj.id + '" class="panel-body">';

                    var state = $.get('/places?state_id=' + stateObj.id);
                    var count = 0;

                    state.done(function(data) {
                        $.each(data, function(index2, cityObj){
                            count = count + 1;
                            var hidden = '<input type="hidden" name="city_'+stateObj.id+'[]" value="'+ cityObj.id +'"/>';
                            $('#C_' + stateObj.id).append('<div class="col-md-3"><input id="' + stateObj.id + '_' + count + '" name="city_'+stateObj.id+'[]" type="checkbox" value="'+ stateObj.id +'_' + cityObj.id + '" class="cityOption"></input> '+ cityObj.name +' </div>');
                        });
                    });

                    $('#state').append(panel + header + '</div></div></div>');
                   
                });
            }); 
        });
    });

    
    

    


@stop