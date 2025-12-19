@extends('layouts.master')
@section('title') Seller @stop
@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">GS Vendor Edit</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'seller/storeeditgsseller/' , 'class' => 'form-horizontal', 'files' => true)) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-user"></i> GS Vendor Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                    {{ Form::label('vendor_name', 'Vendor Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <input type="hidden" name="id" value="<?php echo $vendor->id; ?>">
                    {{ Form::text('vendor_name',  $vendor->seller_name, ['placeholder' => 'Vendor Name', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('vendor_contact_number', 'Telephone No.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                    {{ Form::text('vendor_contact_number', $vendor->seller_phone_no, ['placeholder' => 'Telephone No', 'class' => 'form-control']) }}
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('address', 'Address.', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <textarea class="form-control"  name="address" rows="5" id="address"><?php echo $vendor->seller_address; ?></textarea>
                    </div>
                </div>
                <div class='form-group'>
                    {{ Form::label('status', 'Active Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-2">
                        <input type="radio" name="status" value="1" <?php if($vendor->activation == 1){ echo 'checked'; } ?> > Active 
                        <input type="radio" name="status" value="2" <?php if($vendor->activation == 2){ echo 'checked'; } ?>> Inactive
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary', 'onclick'=> 'return nongstSubmit();'] ) }}
        </div>
    </div>
    @endif
    {{ Form::close() }}

</div>
{{ HTML::script('js/jquery.js') }}
@stop