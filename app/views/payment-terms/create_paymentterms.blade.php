@extends('layouts.master')
@section('title', 'Payment Terms')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add Payment Terms</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/payment-terms/store', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="form-group required {{ $errors->first('period', 'has-error') }}">
                            {{ Form::label('period', 'Payment Terms', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" aria-describedby="basic-addon2" name="period">
                                    <span class="input-group-addon" id="basic-addon2">Days</span>
                                </div>
                                {{ $errors->first('period', '<p class="help-block" style="color:red;">:message</p>') }}
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
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
<?php if(isset($orderMappedInfo['order_number'])){ ?>

    var rowTotal = $('<tr id="grandTotal"><td class="text-right" colspan="4"><b>Total:</b></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_qty"></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_total"></td><td></td></tr>');
    parent.$('#ptb').append(rowTotal);
    calSubTotal();
    calculateTotal();
    
<?php  } ?>

    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.product').length) {
            $('#emptyproduct').show();
            $('#grandTotal').remove();
        }
    });

@stop