@extends('layouts.master')

@section('title') Seller @stop

@section('content')

<script type="text/javascript">
function nongstSubmit() {

    if(!document.getElementById('gstCheck').checked && document.getElementById('gstnumCheck').value == "")
    {
        alert ('For non GST registered company, please check the checkbox.');
        document.getElementById('gstCheck').focus();
        return false;
    }
    else if(document.getElementById('gstnumCheck').value != "" && document.getElementById('gstCheck').checked)
    {
        alert ('For GST registered company, please uncheck the checkbox.');
        document.getElementById('gstCheck').focus();
        return false;
    }      
}
</script>

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Seller Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => array('seller/update/' . $seller->id) , 'class' => 'form-horizontal', 'method' => 'PUT', 'files' => true)) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Seller Login Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('id', 'ID', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('id', $seller->id, ['placeholder' => 'ID', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('createdtime', 'Created Time', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('createdtime', $seller->created_date, ['placeholder' => 'createdtime', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('username', 'Username', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('username', $seller->username, ['placeholder' => 'Username', 'class' => 'form-control', 'disabled']) }}
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
            <h2 class="panel-title"><i class="fa fa-user"></i> Company Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('company_name', 'Company Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('company_name', $seller->company_name, ['placeholder' => 'Company Name', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('company_reg_num', 'Company Registration No.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('company_reg_num', $seller->company_reg_num, ['placeholder' => 'Company Registration No', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('gst_reg_num', 'GST Registration No.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('gst_reg_num', $seller->gst_reg_num, ['placeholder' => 'GST Registration No', 'id' => 'gstnumCheck', 'class' => 'form-control']) }}
                        
                        <?php $checked = ''; ?>
                                    @if ($seller->non_gst == 1) 
                                    <?php $checked = 'checked'; ?>
                                    @endif
                        <input type="checkbox" value="1" id="gstCheck" name="non_gst" <?php echo $checked ?>> Non GST Registered
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('tel_num', 'Telephone No.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('tel_num', $seller->tel_num, ['placeholder' => 'Telephone No', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('address1', 'Street Address', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('address1', $seller->address1, ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('address2', 'Street Address 2', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('address2', $seller->address2, ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('postcode', 'Postcode', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                    {{ Form::text('postcode', $seller->postcode, ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('country', 'Country', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='country' id='country'>
                            @foreach ($countries as $country)
                                <option value='{{ $country->id }}' <?php if ($country->id == $seller->country) echo 'selected="selected"'?> >{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('state', 'State', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='state' id='state'>
                            @foreach ($states as $state)
                                <option value='{{ $state->id }}' <?php if ($state->id == $seller->state) echo 'selected="selected"'?> >{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('city', 'City', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <select class='form-control' name='city' id='city'>
                            @foreach ($cities as $city)
                                <option value='{{ $city->id }}' <?php if ($city->id == $seller->city) echo 'selected="selected"'?> >{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email', 'Email', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email', $seller->email, ['placeholder' => 'Email', 'class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('email2', 'Email-2', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email2', $seller->email1, ['placeholder' => 'Email - 2', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('email3', 'Email-3', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::email('email3', $seller->email2, ['placeholder' => 'Email - 3', 'class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('pic_full_name', 'PIC Full Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('pic_full_name', $seller->pic_full_name, array('class' => 'form-control') ) }}
                    </div>
                </div>
                
                <div class='form-group'>
                    {{ Form::label('mobile_no', 'PIC Mobile No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('mobile_no', $seller->mobile_no, array('class' => 'form-control') ) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('ic_no', 'NRIC/Passport', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('ic_no', $seller->ic_no, ['placeholder' => 'NRIC/Passport', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('status', 'Active Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-2">
                    {{ Form::select('status', array('1' => 'Active', '0' => 'Inactive'), $seller->active_status, array('class' => 'form-control') ) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('notification', 'Send Notification', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4"> 
                        <?php $checked = ''; ?>
                                    @if ($seller->notification == 1) 
                                    <?php $checked = 'checked'; ?>
                                    @endif
                        <input type="checkbox" value="1" id="notification" name="notification" <?php echo $checked ?>> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-bank"></i> Bank Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">

                <div class='form-group'>
                    {{ Form::label('bank_acc_no', 'Bank Account No', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('bank_acc_no', $seller->bank_acc_no, array('class' => 'form-control') ) }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('bank_type', 'Bank Type', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('bank_type', $seller->bank_type, array('class' => 'form-control') ) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Others </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">  
                <div class='form-group'>
                    {{ Form::label('credit_term', 'Credit Term', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4" style="padding-top: 5px;">
                        <input type="text" name="credit_term" class="form-control" value="<?php echo $seller->credit_term; ?>">
                        <small>Ex: 30 Days / 60 Days</small>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('description', 'Business Method', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4" style="padding-top: 5px;">
                        <input name="business_method" type="radio" value="1" id="is_cosignment" <?php echo $seller->business_method == 1 ? "checked" : ''; ?>> Buy Off
                        <input name="business_method" type="radio" value="2" id="is_cosignment" <?php echo $seller->business_method == 2 ? "checked" : ''; ?>> Consignment
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('description', 'Description', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <textarea class="form-control" name="description" id="description" rows="4"><?php echo $seller->description; ?></textarea>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('logo', 'Logo (Format - PEG, JPG, PNG, GIF)', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-10">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 320px; height: 320px;">
                                @if (isset($seller->file_name))
                                    @if (file_exists(Config::get('constants.SELLER_FILE_PATH') ."/". $seller->file_name))
                                        {{ HTML::image(Config::get('constants.SELLER_FILE_PATH') . "/" . $seller->file_name) }}
                                    @else
                                        {{ HTML::image('media/no_images.jpg') }}
                                    @endif
                                @else
                                    {{ HTML::image('media/no_images.jpg') }}
                                @endif
                            </div>
                            <!-- <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 320px; max-height: 320px; line-height: 20px;"></div> -->
                            <div>
                                <span class="btn btn-default btn-file">
                                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Select image</span>
                                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                    <input type="file" name="logo" id="logo" />
                                </span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                            </div>
                        </div>
                        <input type="hidden" name="file_name" id="file_name" value="<?php echo (isset($seller->file_name) AND $seller->file_name != '') ? $seller->file_name: ''?>"><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if( (Permission::CheckAccessLevel(Session::get('role_id'), 9, 3, 'AND')) || (in_array(Session::get('username'), array('nuratiqah', 'toby', 'ganesware'), true ) )) {?>
    <div class='form-group' >
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary', 'onclick'=> 'return nongstSubmit();'] ) }}
        </div>
    </div>
    <?php } ?>
    {{ Form::close() }}

</div>

@stop