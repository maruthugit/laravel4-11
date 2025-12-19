@extends('layouts.master')

@section('title') Create Customer @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Customer Management </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/customer/store', 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Login Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('username', 'Username *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('username', null, ['placeholder' => 'Username', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password', 'Password *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password_confirmation', 'Confirm Password *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> Personal Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('firstname', 'First name *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('firstname', $customerInfo['first_name'], ['placeholder' => 'First name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('lastname', 'Last name *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('lastname', $customerInfo['last_name'], ['placeholder' => 'Last name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('ic_passport', 'NRIC/Passport', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('ic_passport', null, ['placeholder' => 'NRIC/Passport', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('dob', 'Date of Birth', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-2">
                    {{ Form::text('dob', null, ['placeholder' => '', 'class' => 'form-control', 'id' => 'datepicker']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('mobile_no', 'Mobile No *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('mobile_no', $customerInfo['mobile_no'], ['placeholder' => 'Mobile No', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email', 'Email *', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email', $customerInfo['email'], ['placeholder' => 'Email', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('bcard', 'BCard No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('bcard', null, ['placeholder' => 'e.g. 6298430000005477', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('agent_id', 'Agent', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::select('agent_id', $agents, null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-truck"></i> Address</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('address1', 'Street Address', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('address_1', $customerInfo['street_address_1'], ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('address2', 'Street Address 2', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('address_2', $customerInfo['street_address_2'], ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('postcode', 'Postcode', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('postcode', $customerInfo['postcode'], ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('country', 'Country', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='country_id' id='country'>
                            <option value=""> - </option>
                            @foreach ($countries as $country)
                                <option value='{{ $country->id }}'>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('state', 'State', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-sm-3">
                        {{ Form::text('state', null, ['placeholder' => 'State', 'class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('city', 'City/Town', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-sm-3">
                        {{ Form::text('city', null, ['placeholder' => 'City/Town', 'class' => 'form-control']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Others</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('type', 'Customer Type', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='type' id='type'>
                            <option value=""> - </option>
                            @foreach ($types as $type)
                                <option value='{{ $type }}'>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    @endif

    {{ Form::close() }}

</div>

@stop