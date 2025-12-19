@extends('layouts.master')
@section('title', 'Warehouse Location')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Warehouse Location Management</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/warehouse-location/update/'.$warehouse->id, 'class' => 'form-horizontal')) }}

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Warehouse Address</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('name', 'has-error') }}">
                            {{ Form::label('name', 'Name ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('name', $warehouse->name, ['placeholder' => 'Warehouse Name', 'class' => 'form-control']) }}
                            {{ $errors->first('name', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('address_1', 'has-error') }}">
                            {{ Form::label('address_1', 'Street Address ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('address_1', $warehouse->address_1, ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                            {{ $errors->first('address_1', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('address_2', 'Street Address 2 ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('address_2', $warehouse->address_2, ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                            {{ $errors->first('address_2', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('postcode', 'has-error') }}">
                            {{ Form::label('postcode', 'Postcode ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                            {{ Form::text('postcode', $warehouse->postcode, ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                            {{ $errors->first('postcode', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('country', 'has-error') }}">
                            {{ Form::label('country', 'Country ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='country' id='country'>
                                    @foreach ($countries as $country)
                                        <option value='{{ $country->id }}' <?php if ($country->id == $warehouse->country_id) echo 'selected' ?>>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('country', '<p class="help-block">:message</p>') }}
                                <input type="hidden" id="country_name" name="country_name">
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('state', 'has-error') }}">
                            {{ Form::label('state', 'State ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='state' id='state'>
                                    @foreach ($states as $state)
                                    <option value='{{ $state->id }}' <?php if ($state->id == $warehouse->state_id) echo 'selected'?>>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('state', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->first('city', 'has-error') }}">
                            {{ Form::label('city', 'City/Town ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='city' id='city'>
                                    @foreach ($cities as $city)
                                    <option value='{{ $city->id }}' <?php if ($city->id == $warehouse->city_id) echo 'selected'?>>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('city', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group required {{ $errors->first('city', 'has-error') }}">
                            {{ Form::label('status', 'Status ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='status' id='status'>
                                    @foreach ($statuses as $key => $value)
                                    <option value='{{ $key }}' <?php if ($key == $warehouse->status) echo 'selected'?>>{{ $value }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('city', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Person In Charge</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('pic_name', 'has-error') }}">
                            {{ Form::label('pic_name', 'Name ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('pic_name', $warehouse->pic_name, ['placeholder' => 'Person In Charge Name', 'class' => 'form-control']) }}
                            {{ $errors->first('pic_name', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('pic_contact', 'has-error') }}">
                            {{ Form::label('pic_contact', 'Contact No ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('pic_contact', $warehouse->pic_contact, ['placeholder' => 'Mobile No', 'class' => 'form-control', 'maxlength'=>"12"]) }}
                            {{ $errors->first('pic_contact', '<p class="help-block" >:message</p>') }}
                            </div>
                        </div>                  
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Warehouse Contacts</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('tel', 'has-error') }}">
                            {{ Form::label('tel', 'Telephone No ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('tel', $warehouse->tel, ['placeholder' => 'Telephone No', 'class' => 'form-control']) }}
                            {{ $errors->first('tel', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('fax', 'has-error') }}">
                            {{ Form::label('fax', 'Fax No ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('fax', $warehouse->fax, ['placeholder' => 'Fax No', 'class' => 'form-control', 'maxlength'=>"12"]) }}
                            {{ $errors->first('fax', '<p class="help-block" >:message</p>') }}
                            </div>
                        </div>                  
                    </div>
                </div>
            </div>
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

    $('#country').change(function() {
        const selectedCountry = $("#country option:selected").text();
        $('#country_name').val(selectedCountry);
        
    });
                    
@stop