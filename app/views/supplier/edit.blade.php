@extends('layouts.master')

@section('title', 'Edit Brand')

@section('content')
<?php
// echo "<pre>";
// print_r($brand);
// echo "</pre>";
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Supplier
                <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('supplier') }}"><i class="fa fa-reply"></i></a>
                </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-user"></i> Supplier  Details</h3>
                </div>
                <div class="panel-body">
                 {{ Form::open(['url' => "supplier/{$supplier->id}", 'method' => 'patch','class' => 'form-horizontal']) }}
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">ID</label>
                                <div class="col-lg-4">
                                    {{ Form::text('id', $supplier ->id, ['class' => 'form-control', 'disabled' => 'disabled']) }}
                                </div>
                            </div>

                           
                            <div class="form-group required {{ $errors->first('supplier_name', 'has-error') }}">
                                <label class="col-lg-2 control-label">supplier</label>
                                <div class="col-lg-4">
                                    {{ Form::text('supplier_name', $supplier ->supplier_name, ['class' => 'form-control']) }}
                                    {{ $errors->first('supplier_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                        

                         

                            <div class="form-group required {{ $errors->first('supplier_code', 'has-error') }}">
                                <label class="col-lg-2 control-label">supplier Code</label>
                                <div class="col-lg-4">
                                    {{ Form::text('supplier_code', $supplier ->supplier_code, ['class' => 'form-control']) }}
                                    {{ $errors->first('supplier_code', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            


       <!--  <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Product Status', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::select('active_status', ['0' => 'Inactive', '1' => 'Active'], $brand->status, ['class'=> 'form-control']) }}
                                {{ $errors->first('active_status', '<p class="help-block">:message</p>') }}
                            </div>
                        </div> -->


                            
                            <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                    
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 21, 3, 'AND'))
                                    @endif
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
@stop
