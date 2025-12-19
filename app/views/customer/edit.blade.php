@extends('layouts.master')

@section('title') Edit Customer @stop

@section('content')

<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Customer Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => array('customer/update/' . $cust->id) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Login Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('username', $cust->username, ['placeholder' => 'Username', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password', 'Password', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('password_confirmation', 'Confirm Password', array('class' => 'col-lg-2 control-label')) }}
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
                    {{ Form::label('fistname', 'First name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('firstname', $cust->firstname, ['placeholder' => 'First Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('lastname', 'Last name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('lastname', $cust->lastname, ['placeholder' => 'Last Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('ic_no', 'NRIC/Passport', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('ic_no', $cust->ic_no, ['placeholder' => 'NRIC/Passport', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('dob', 'Date of Birth', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('dob', $cust->dob, ['placeholder' => '', 'class' => 'form-control', 'id' => 'datepicker']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email', 'Email', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email', $cust->email, ['placeholder' => 'Email', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('mobile_no', 'Mobile No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('mobile_no', $cust->mobile_no, ['placeholder' => 'Mobile No', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('bcard', 'BCard No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('bcard', $card->bcard, ['placeholder' => 'e.g. 6298430000005477', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('agent_id', 'Agent', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::select('agent_id', $agents, $cust->agent_id, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group">
                    {{ Form::label('usr_agree', 'User agreement & PDPA', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    <div class="checkbox">
                        <label>
                            <input id="usr_agree" type="checkbox" name="usr_agree" value="1" <?php if($cust->usr_agree == 1) echo 'checked="checked"'; ?> >User Agreement
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="pdpa" type="checkbox" name="pdpa" value="1" <?php if($cust->pdpa == 1) echo 'checked="checked"'; ?>>Personal Data Protection Act (PDPA)
                        </label>
                    </div>
                </div>
            </div>
                
                <div class='form-group'>
                    {{ Form::label('active_status', 'Account Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <select class='form-control' name='active_status' id='active_status'>
                            <option value='0' <?php echo $cust->active_status == 0 ? "selected":"" ?> >Inactive</option>
                                <option value='1' <?php echo $cust->active_status == 1 ? "selected":"" ?>  >Active</option>
                        </select>
                        <div>
                            <?php  
                                if ($cust->active_status == 0)
                                {
                                   echo '<br><a type="submit" class="btn btn-large btn-primary" id="email_activation">Resend Activation</a>';                            
                                }
                            ?>
                        </div>
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
                    {{ Form::text('address1', $cust->address1, ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('address2', 'Street Address 2', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('address2', $cust->address2, ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('postcode', 'Postcode', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('postcode', $cust->postcode, ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('country', 'Country', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='country_id' id='country'>
                            @foreach ($countries as $country)
                                <option value='{{ $country->id }}' <?php if ($country->id == $cust->country_id) echo 'selected="selected"'?> >{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('state', 'State', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        {{ Form::text('state', $cust->state, ['placeholder' => 'State', 'class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('city', 'City', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        {{ Form::text('city', $cust->city, ['placeholder' => 'City/Town', 'class' => 'form-control']) }}
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
                            @foreach ($types as $type)
                                <option value='{{ $type }}' <?php if ($type == $cust->type) echo 'selected="selected"'?> >{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 3, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    @endif
    
    {{ Form::close() }}

    <br>

    {{ Form::open(array('url' => array('customer/update/' . $cust->id) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Favourite Address</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('delivername', 'Delivery Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        {{ Form::select('favaddr', array('' => "- - - - - Select Delivery Name - - - - - ") + $favaddr, null, ['class' => 'form-control', 'id' => 'favaddr']) }}
                    </div>
                </div>
            </div>
            @foreach($add as $adds)
            <div class="col-lg-12" id="display_add{{$adds->id}}" style="display:none">
                <div class='form-group'>
                    {{ Form::label('add_id', 'ID', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->id}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('delivercontactno', 'Contact No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->delivercontactno}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliveradd1', 'Delivery Address', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->deliveradd1}}</p>
                        <p class="form-control-static">{{$adds->deliveradd2}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliverpostcode', 'Delivery Postcode', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->deliverpostcode}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliverpostcode', 'Delivery City', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->city_name}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliverpostcode', 'Delivery State', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->state_name}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliverpostcode', 'Delivery Country', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->country_name}}</p>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('deliverpostcode', 'Special Message', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <p class="form-control-static">{{$adds->specialmsg}}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{ Form::close() }}

</div>

@stop

@section('script')
    
    $(function() {
        $('#favaddr').on('change', function(e) {
            console.log(e);
            var favaddr_id = e.target.value;

            $('[id^=display_add]').hide();
            
            $('#display_add'+favaddr_id).show();
        });
     });
     
    $('#email_activation').on('click', function(){ 

        var data = {
            username: $("#username").val()
        };
        
        $.ajax({
            method: "POST",
            url: "/api/user/resendactivation",
            datatype: "json",
            data: data,
            beforeSend: function(){
            },
            success: function(data) {
                 alert('Email Activation Sent!');
            }
       })

    }); 
@stop