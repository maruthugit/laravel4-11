@extends('layouts.master')

@section('title') Edit Zone @stop

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
            @if (Session::has('message'))
                <div class="alert alert-success">
                    <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            @if (Session::has('warning'))
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation"></i> {{ Session::get('warning') }}<button data-dismiss="alert" class="close" type="button">×</button>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Last Updated Info</h3>

                </div>
                <div class="panel-body">
                        <div class="col-lg-12 form-horizontal">
                            <div class="form-group @if ($errors->has('name')) has-error @endif"">
                                 {{ Form::label('Created By', 'Created By', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">:
                                
                                    {{ $zone->insert_by }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('name')) has-error @endif"">
                                 {{ Form::label('Created At', 'Created At', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">:
                                    {{ $zone->insert_date }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('name')) has-error @endif"">
                                 {{ Form::label('Last Updated By', 'Last Updated By', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">:
                                    <?php if($zone->modify_by != '') { ?>
                                        {{ $zone->modify_by }}
                                    <?php 
                                    } ?>       
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('name')) has-error @endif"">
                                 {{ Form::label('Last Updated Date', 'Last Updated Date', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">:
                                    <?php if($zone->modify_by != '') { ?>
                                        {{ $zone->modify_date }}
                                    <?php 
                                    } ?> 
                                    
                                   
                                </div>
                            </div>


                        </div>
                    </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Zone</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => array('zone/update', $zone->id), 'method'=> 'PUT', 'class' => 'form-horizontal')) }}

                        <div class="form-group @if ($errors->has('name')) has-error @endif">
                        {{ Form::label('name', 'Zone Name *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('name', $zone->name, array('id' => 'name', 'class'=> 'form-control')) }}
                                {{ $errors->first('name', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        
                        <!-- <div class="form-group @if ($errors->has('weight')) has-error @endif">
                        {{ Form::label('weight', 'Maximum Delivery Weight (gram)', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('weight', $zone->weight, array('id' => 'weight', 'class'=> 'form-control')) }}
                                {{ $errors->first('weight', '<p class="help-block">:message</p>') }}
                            </div>
                        </div> -->

                        <div class="form-group @if ($errors->has('country')) has-error @endif">
                        {{ Form::label('country', 'Country *', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('country', $zone->country, array('id' => 'country', 'class'=> 'form-control', 'readonly' => 'readonly')) }}
                                {{ $errors->first('country', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('status', 'Active Status', ['class'=> 'col-lg-2 control-label','style'=>'padding-bottom:25px; padding-top:0px;']) }}
                            <div class="col-lg-3">
                                <input type="checkbox" name="status" value="1" <?php if($zone->status == 1) echo "checked"; ?> >
                            </div>
                        </div>

                        <hr>
                        
                        <div class="form-group">
                        {{ Form::label('init_weight', 'Initial Weight', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2 @if ($errors->has('init_weight')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon">Gram</span>
                                   {{ Form::text('init_weight', $zone->init_weight, array('id' => 'init_weight', 'placeholder' => 'Weight', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('init_weight', '<p class="help-block">:message</p>') }}
                            </div>

                            <div class="col-lg-2 @if ($errors->has('init_price')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo $zone->business_currency; ?></span>
                                   {{ Form::text('init_price', $zone->init_price, array('id' => 'init_price', 'placeholder' => 'Price', 'class'=> 'form-control')) }}                                   
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
                                   {{ Form::text('add_weight', $zone->add_weight, array('id' => 'add_weight', 'placeholder' => 'Weight', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('add_weight', '<p class="help-block">:message</p>') }}
                            </div>

                            <div class="col-lg-2 @if ($errors->has('add_price')) has-error @endif">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo $zone->business_currency; ?></span>
                                   {{ Form::text('add_price', $zone->add_price, array('id' => 'add_price', 'placeholder' => 'Price', 'class'=> 'form-control')) }}                                   
                                </div>
                                {{ $errors->first('add_price', '<p class="help-block">:message</p>') }}
                            </div>
                            <p class="help-block">Blank will make the maximum charges equal to Initial Weight Price!</p>
                        </div>

                        <hr>

                        
                        <?php
                            foreach($selected_states as $states) {
                                $checked_states[] =  $states->states_id;
                            }

                            foreach($selected_cities as $cities) {
                                $checked_cities[] = $cities->city_id;
                            }
                        ?>

                        <div class="form-group @if ($errors->has('city')) has-error @endif">
                            {{ Form::label('states', 'States / Cities *', array('class'=> 'col-lg-2 control-label')) }}
                            <div  class="row">
                                <div class="col-lg-9">
                                    <div id ="state">
                                    @foreach ($state_options as $state)
                                    <?php
                                        $count = 0;
                                        $checked_state = false ;
                                        if(in_array($state->id, $checked_states)) $checked_state = true ; 
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                        <h3 class="panel-title">
                                            {{ Form::checkbox('state[]', $state->id, $checked_state, ['class' => 'stateOption', 'id' => $state->id]) }} {{ $state->name }}
                                        </h3>
                                        </div>
                                        <div id="C_{{ $state->id }}" class="panel-body">
                                        @foreach ($city_options as $city)
                                        <?php
                                            
                                            $checked_city = false ;
                                            if(in_array($city->id, $checked_cities) && $checked_state == true) $checked_city = true ; 
                                        ?>
                                        @if ($city->state_id == $state->id)
                                            <?php $count++?>
                                            <div class="col-md-3">
                                            {{ Form::checkbox('city_'.$state->id.'[]', $state->id .'_'. $city->id, $checked_city, ['class' => cityOption, 'id' => $state->id.'_'.$count]) }} {{ $city->name }}
                                             </div>
                                        @endif
                                        @endforeach
                                        </div>
                                    </div>
                                    @endforeach 
                                    </div>
                                    {{ $errors->first('city', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <!-- <a class="btn btn-default" href="/zone"><i class="fa fa-reply"></i> Cancel</a> -->
                                {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu'), true ) ) {  ?>
                                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 3, 'AND'))
                                <button type="submit" value="Save" class="btn btn-primary"> Save</button>
                                @endif
                                <?php } ?>
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
@stop