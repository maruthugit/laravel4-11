@extends('layouts.master')
@section('title') FreeCoupon @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
$tempcount = 1;
?>

`
<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Free Coupon Item Management
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href='{{asset('/')}}coupon'}}><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Coupon</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'coupon/editfreecoupon/'.$display_coupon->id, 'class' => 'form-horizontal')) }}
                            <div class="form-group">
                            {{ Form::label('id', 'Coupon ID', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{$display_coupon->id}}</p>{{Form::input('hidden', 'id', $display_coupon->id)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('coupon_code', 'Coupon Code', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">{{$display_coupon->coupon_code}}</p>{{Form::input('hidden', 'coupon_code', $display_coupon->coupon_code)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('name', 'Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('name', $display_coupon->name, array('class'=> 'form-control')) }}
                                </div>                                
                            </div>
                            

                            <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), $display_coupon->status, ['class'=>'form-control'])}}
                                </div>
                            </div>


                            <hr />
                            <div class="form-group @if ($errors->has('start_from')) has-error @endif">
                                {{ Form::label('start_from', 'Start Date', array('class'=> 'col-lg-2 control-label')) }}
                            
                                <div class="col-lg-2">
                                        <div class="input-group required" id="datetimepicker_from">
                                            {{ Form::text('start_from',$display_coupon->valid_from, ['required'=>'required', 'placeholder' => 'From (yyyy-mm-dd)', 'class' => 'form-control','tabindex' => 1]) }}
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>

                                        </div>

                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('end_to')) has-error @endif">
                                {{ Form::label('end_to', 'End Date', array('class'=> 'col-lg-2 control-label')) }}
                             
                                 <div class="col-lg-2">
                                    
                                        
                                        <div class="input-group" id="datetimepicker_to">
                                            {{ Form::text('end_to',$display_coupon->valid_to, ['id' => 'end_to', 'placeholder' => 'To (yyyy-mm-dd)', 'class' => 'form-control','required'=>'required', 'tabindex' => 2]) }}
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                </div>
                            </div>
                            <div class="form-group">
                            {{ Form::label('q_limit', 'Set Quantity Limit', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('q_limit', array('No' => 'No', 'Yes' => 'Yes'), $display_coupon->q_limit, ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('qty')) has-error @endif">
                            {{ Form::label('qty', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('qty', $display_coupon->qty, array('class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('qty')}}</p>
                                </div>                                
                            </div>

                            <div class="form-group">
                            {{ Form::label('c_limit', 'Per Customer Limit', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('cqty', 1, array('class'=> 'form-control', 'readonly' => 'true')) }}
                                </div>
                            </div>

        
                            
                            <hr />                         
                            <div class="form-group">
                                {{ Form::label('seller_flg', 'Set Seller', array('class'=> 'col-lg-2 control-label')) }}
                                     <div class="col-lg-2">
                                        {{Form::select('seller_flg', array('0' => 'No', '1' => 'Yes'), $display_coupon->is_seller, ['id' => 'seller_flg', 'class'=>'form-control'])}}
                                    </div>                                
                            </div>
                            
                            <div class="form-group @if ($errors->has('seller')) has-error @endif">
                            {{ Form::label('seller_name', 'Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::select('seller', ['0' => 'All'] + $sellersOptions, $display_coupon->seller_id, ['id' => 'seller', 'class' => 'form-control']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                                </div>

                            </div>
                            
                            <hr /> 
                            
                            <!-- Product -->
                            <div class="form-group" id="display_product">
                                {{ Form::label('related_product', 'Product', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_item_id', null, array('class'=> 'form-control', 'id'=>'related_item_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectProduct();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>                         

                            <div class="form-group" id="display_product_list">
                                {{ Form::label('related_id', 'List of Product', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">                              
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>SKU</th>
                                                    <th>Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($display_item as $coupon_item)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$coupon_item->id}}</td>
                                                        <td>{{$coupon_item->sku}}</td>
                                                        <td>{{$coupon_item->name}}</td>
                                                        <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type({{$coupon_item->id;}});"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->        
                                </div>
                            </div>

                            


                                               


                           
                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','joshua01'), true ) ) {  ?>

                            <hr />
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}} 
                                </div>
                            </div>
                             <?php } ?>
                        {{ Form::close() }}

                        <hr />                        
                      
                    </div>
                                
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

{{ Form::open(array('url'=>'coupon/removefreeitem', 'id'=>'remove_frm')) }}
{{Form::input('hidden', 'remove_type_id', null, ['id'=>'remove_type_id'])}}
{{Form::input('hidden', 'couponID', $display_coupon->id, ['id'=>'couponID'])}}
{{ Form::close() }}

@stop
@section('inputjs')
<script>

$(document).ready(function() {
   
   $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
    $('#seller').on('change', function(e) { 
        var seller_id = $('#seller').val();

        if(seller_id != 0){

            $('#seller_flg').val(1);
        }
        else if(seller_id == 0){
            $('#seller_flg').val(0);
        }

   });

});

function getUserFromChild(id) {
        $("#related_com_id").val(id);
    }


function getUserFromChild2(id) {
        $("#related_item_id").val(id);
    }

function selectCategory() {
        window.open("<?php echo asset('/') ?>coupon/selectfreecategory", "", "width=600, height=800, scrollbars");
    }

function selectProduct() {
        window.open("<?php echo asset('/') ?>coupon/selectfreeitem", "", "width=600, height=800, scrollbars");
    }

function delete_type(id) {
    if(confirm("Are you sure to delete?")) {
        

        var tempid = document.getElementById("remove_type_id");
        tempid.value = id;

        var tempform = document.getElementById("remove_frm");
        tempform.submit();
        
    }
    
}

</script>
@stop
