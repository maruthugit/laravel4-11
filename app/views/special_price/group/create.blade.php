@extends('layouts.master')

@section('title') Create SP Group @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Special Price Group</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(['role' => 'form', 'url' => '/special_price/group/store', 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-lock"></i> Special Price Group</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('group_name', 'Group name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('group_name', null, ['placeholder' => 'Group name', 'class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('min_purchase', 'Minimum Purchase ('.Config::get("constants.CURRENCY").')', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('min_purchase', null, ['placeholder' => 'Minimum Purchase', 'class' => 'form-control']) }}
                    </div>
                </div>   
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('min_purchase_qty', 'Minimum Purchase QTY ', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('min_qty_purchase', $group->min_qty_purchase, ['placeholder' => 'Minimum Purchase QTY', 'class' => 'form-control']) }}
                    </div>
                </div>  
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('is_free_delivery', 'Free Delivery  ', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-4">
                        <input type="checkbox" value="1" name="is_free_delivery_min_qty" id="is_free_delivery_min_qty" > (Free delivery if exceed min purchase quantity)
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
