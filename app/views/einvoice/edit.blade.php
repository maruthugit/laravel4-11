@extends('layouts.master')
@section('title', 'eInvoice')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit New eInvoice</h1>
              <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
                  
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> eInvoice</h3>
                </div>
                <div class="panel-body">

                {{ Form::open(['url' => 'einvoice/update/'.$einv->id, 'method' => 'post']) }}
                 <div class="form-horizontal">
                    <div class="form-group">
                    {{ Form::label('no', 'eInvoice No', array('class'=> 'col-lg-2 control-label')) }}
                         <div class="col-lg-5">
                             <p class="form-control-static"><?php

                                $path = Config::get('constants.EINV_PDF_FILE_PATH') . '/' . urlencode($einv->einv_no) . '.pdf';
                                $file = ($einv->id)."#".($einv->einv_no)."#". $path;
                                $encrypted = Crypt::encrypt($file);
                                $encrypted = urlencode(base64_encode($encrypted));

                                ?>
                                {{ HTML::link('einvoice/files/'.$encrypted, $einv->einv_no, array('target'=>'_blank')) }}
                                <?php
                                                                
                            ?></p>

                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-lg-2 control-label">eInvoice Date</label>
                        <div class="col-lg-2">
                            {{ Form::text('po_no', $einv->einv_date, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('po_no', 'PO No', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-2">
                            {{ Form::text('po_no', $einv->po_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                        </div>
                    </div>


                        <div class="form-group">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products</label>
                            <div class="col-sm-10">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-3">Product Name &amp; SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-4">Label</th>
                                            <th class="cell-small col-sm-1 text-center">Price</th>
                                            <th class="cell-small col-sm-1 text-center">Quantity</th>
                                            <th class="cell-small col-sm-1 text-center">Sub-total</th>
                                            <th class="cell-small col-sm-1 text-center">SST (RM)</th>
                                            <th class="cell-small col-sm-1 text-center">Subtotal Incl. SST</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($einv_details as $trans_details)
                                            <tr class="odd gradeX">
                                                <td>{{$trans_details->product_name}}<br><i class="fa fa-tag"></i> {{$trans_details->sku}}</td>
                                                <td>{{$trans_details->price_label}}</td>  
                                                <td class="text-right">{{number_format($trans_details->price,2)}}</td>
                                                <td class="text-right">{{$trans_details->quantity}}</td>
                                                <td class="text-right">{{number_format($trans_details->total,2)}}</td>
                                                <td class="text-right">{{number_format($trans_details->sst,2)}}</td>
                                                <td class="text-right">{{number_format($trans_details->subtotal_sst,2)}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="4" class="text-right"><b>Total:</b></td>
                                            <td class="text-right">{{$subtotal}}</td>
                                            <td class="text-right">{{$sst_total}}</td>
                                            <td class="text-right">{{$total}}</td>
                                            <input type="hidden" id="total_amount" value="{{$total}}">
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                {{ Form::label('discpercent', 'Discount %', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::number('discpercent', $einv->discount_percent, ['class' => 'form-control', 'readonly']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('disctotal', 'Discounted Total', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('disctotal', $einv->discount_total, ['class' => 'form-control', 'readonly']) }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            {{ Form::label('whloc_id', 'Warehouse Location ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_id', $einv->warehouse_loc_id, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('whloc_name', 'Warehouse Location Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_name', $einv->loc_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('whloc_address1', 'Address 1', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address1', $einv->address_1, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('whloc_address2', 'Address 2', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address2', $einv->address_2, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('whloc_contact', 'Contact No.', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_contact', $einv->pic_contact, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('whloc_pic', 'Person In Charge', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_pic', $einv->pic_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>


                        <hr>
                        <div class="form-group">
                            {{ Form::label('seller_id', 'Supplier/Seller ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_id', $einv->seller_id, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(seller_name)) has-error @endif">
                            {{ Form::label('seller_name', 'Supplier/Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_name', $einv->company_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->first('remarks', 'has-error') }}">
                            <label class="col-lg-2 control-label">Remarks / Note</label>
                            <div class="col-lg-4">
                                {{ Form::textarea('remarks', $einv->remarks, ['class' => 'form-control']) }}
                                {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
                                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

                {{ Form::close() }}
</div>
    
@stop

@section('script')

$(document).ready(function() {
    
   $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});

@stop