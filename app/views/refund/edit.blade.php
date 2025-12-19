@extends('layouts.master')
@section('title', 'Refund')
@section('content')
<style>
    .specialadjust.form-group{marginx; margin-bottom: 15px;}
    .specialadjust.form-group .max-width-adj{max-width: 500px;}
    .inlineblock-middle{display: inline-block; vertical-align: top;}
    #clone-base{display: none;}
</style>

<?php

$currency = Config::get('constants.CURRENCY');
?>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Refund Management</h1>
			{{-- <h1 class="page-header">This page took {{ (microtime(true) - LARAVEL_START) }} seconds to render</h1> --}}
		</div>
	</div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/refund/update/'.$refund->id, 'class' => 'form-horizontal', 'files' => true)) }}
            <div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title"><i class="fa fa-pencil"></i> Refund Details </h2>
		        </div>
				<div class="panel-body">
					<div class="col-lg-12 ">
                        <div class='form-group'>
                        {{ Form::label('status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            @if($refund->status == "approved" || $refund->status == "confirmed")
                                {{ Form::text('status', strtoupper($refund->status), array('class' => 'form-control', 'disabled', 'style' => 'color:green') ) }}
                            @else 
                                {{ Form::text('status', strtoupper($refund->status), array('class' => 'form-control', 'disabled', 'style' => 'color:red') ) }}
                            @endif
                            </div>
                        </div>
						<div class='form-group'>
						{{ Form::label('id', 'Refund ID', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							{{ Form::text('id', $refund->id, array('class' => 'form-control', 'disabled') ) }}
							</div>
						</div>
						<div class='form-group'>
						{{ Form::label('created_date', 'Created at', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							{{ Form::text('created_date', $refund->created_date, array('class' => 'form-control', 'disabled') ) }}
							</div>
						</div>
						<div class='form-group'>
						{{ Form::label('created_by', 'Created by', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							{{ Form::text('created_by', $refund->created_by, array('class' => 'form-control', 'disabled') ) }}
							</div>
						</div>
					</div>

				</div>
			</div>
            <div class="panel @if ($errors->has('trans_id')) panel-danger @else panel-default @endif ">
                {{ $errors->first('trans_id', '<p class="text-danger">&nbsp; :message</p>') }}
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Transaction Details</h3>
                </div>
                <div class="panel-body ">
                    <div class="col-lg-12">
                        <div class="form-group" id="addTransBtn">
                            <div class="col-lg-10 col-lg-offset-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="selectTransBtn" class="btn btn-primary selectTransBtn" href="/refund/ajaxtrans"><i class="fa fa-plus"></i> Select Transaction</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('trans_id', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_id', $refund->trans_id, array('class'=> 'trans-id form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('trans_date', 'Transaction Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_date', $refund->transaction_date, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        {{-- <div class="form-group @if ($errors->has('trans_amount')) has-error @endif">
                            {{ Form::label('trans_amount', "Transaction Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_amount', number_format($refund->total_amount, 2), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div> --}}

                        <input type="hidden" name="buyer_id" value="{{ $buyer_details->id }}">
						<div class="form-group @if ($errors->has('buyer')) has-error @endif">
                            {{ Form::label('buyer', 'Buyer', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('buyer', $refund->customer_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('buyer', $refund->customer_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('email', "Email address", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('email', $refund->email, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('email', $refund->email, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('address')) has-error @endif">
                            {{ Form::label('address', "Address", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('address', $refund->address, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{  Form::text('address', $refund->address, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled'))  }}
                                @endif
                            </div>
                            {{ Form::label('postcode', "Postcode", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('postcode', $refund->postcode, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('postcode', $refund->postcode, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('ic_no')) has-error @endif">
                            {{ Form::label('ic_no', "I/C Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('ic_no', $refund->ic_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('ic_no', $refund->ic_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                            {{ Form::label('hp_no', "Phone Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('hp_no', $refund->hp_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('hp_no', $refund->hp_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('bank_name')) has-error @endif">
                            {{ Form::label('bank_name', "Bank name", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('bank_name', $refund->bank_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('bank_name', $refund->bank_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                            {{ Form::label('bank_acc_no', "Bank account no", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('bank_acc_no', $refund->bank_account_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('bank_acc_no', $refund->bank_account_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('bank_name')) has-error @endif">
                            {{ Form::label('order_no', "Order Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{ Form::text('order_no', $refund->order_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                @else
                                    {{ Form::text('order_no', $refund->order_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                            {{ Form::label('platform_store', "Platform Store", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                                    {{-- {{ Form::text('platform_store', $refund->platform_store, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }} --}}
                                    {{ Form::select('platform_store', [$refund->platform_store => $refund->platform_store, 'Jocom' => 'Jocom', 'Lazada Express' => 'Lazada Express', 
                                            'L-Starbucks' => 'L-Starbucks', 'L-Jocom' => 'L-Jocom','L-Everbest' => 'L-Everbest', 'L-Redbull' => 'L-Redbull',
                                            'L-EB' => 'L-EB', 'L-Pokka' => 'L-Pokka', 'L-Yeos' => 'L-Yeos', 'L-Etika' => 'L-Etika', 
                                            'S-Starbucks' => 'S-Starbucks', 'S-Jocom' => 'S-Jocom', 'S-Everbest' => 'S-Everbest',
                                            'S-EB' => 'S-EB', 'S-Pokka' => 'S-Pokka', 'S-Yeos' => 'S-Yeos', 'S-Etika' => 'S-Etika', 'S-Redbull' => 'S-Redbull', 'TikTok' => 'TikTok',
                                            'PG Mall' => 'PG Mall', 'SRC' => 'SRC'], 
                                            $refund->platform_store, ['class' => 'form-control', 'tabindex' => 4]) }}       
                                @else
                                    {{ Form::text('platform_store', $refund->platform_store, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'disabled')) }}
                                @endif
                            </div>
                        </div>


                        @if ($refund->status == "pending" && Refund::permission(Session::get('username'), '0,1,2,3,4') )
                        <div class="specialadjust form-group">
                            {{-- {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }} --}}
                            <label class="col-lg-2 control-label" for="remark_doc[]">Support Document</label>
                            <div class="col-lg-6">
                                <div class="max-width-adj inlineblock-middle">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="hidden"><input type="file" name="remark_doc[]">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                                <div class="input-group-btn inlineblock-middle">
                                    <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <!-- /.panel-body -->
                
            </div>
            <!-- /.panel -->

            <div class="panel @if ($errors->has('grand_total')) panel-danger @else panel-default @endif">
                {{ $errors->first('grand_total', '<p class="text-danger">&nbsp; :message</p>') }}
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Refund Item</h2>
                </div>
                
                <div class="panel-body">
                    <div class="form-group">
                        <?php $count = 1; ?>
                        <label class="col-lg-2 control-label" for="product_option">Products</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    @if($refund->status == "pending")
                                        @if (Refund::permission(Session::get('username'), '0,4'))
                                            <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Product</span>
                                            <div id="product_btn"></div>
                                        @endif
                                    @endif  
                                </div>
                            </div>
                            <br />
                            <div class="clearfix"></div>
                            <table class="table table-bordered table-hover" id="table">
                                <thead style="background:#EEEEEE">
                                    <tr>
                                        <th class="col-sm-2 text-center">Product Name &amp; SKU</th>
                                        {{-- <th class="col-sm-2 text-center">Item Name</th> --}}
                                        <th class="col-sm-2 text-center">Item Name & Label</th>
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">Price ({{$currency}})</th>
                                        {{-- <th class="hidden-xs hidden-sm col-sm-1 text-center">GST (%)</th>
                                        <th class="cell-small col-sm-1 text-center">Quantity</th>
                                        <th class="cell-small col-sm-1 text-center">Discount ({{$currency}})</th>
                                        <th class="cell-small col-sm-1 text-center">Sub-total ({{$currency}})</th> --}}
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">Refund Quantity</th>
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">Refund Price ({{$currency}})</th>
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">Total Refund ({{$currency}})</th>
                                        @if($refund->status == "pending" && Refund::permission(Session::get('username'), '0,4'))
                                            <th class="cell-small col-sm-1 text-center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="ptb">
                                	@if(count($products) > 0)
                                		@foreach ($products as $product) 
                            			<tr class="product">
                                            <input type="hidden" id="productID" name="product[][id]" value="{{ $product->id }}" class="product-id">
                                            {{-- <input type="hidden" id="sku" name="sku[][id]" value="{{ $product->sku }}" class="item-id"> --}}
                                            <input type="hidden" id="refund_oriPrice_request" name="refund_oriPrice_request[][oriPrice]" value="{{ $product->oriPrice }}" class="item-id">
                                            <input type="hidden" id="refund_quantity_request" name="refund_quantity_request[][quantity]" value="{{ $product->unit }}" class="item-id">
                                            <input type="hidden" id="refund_price_request" name="refund_price_request[][price]" value="{{ $product->price }}" class="item-id">
                            				<td name="product[]['product_id']" value="{{ $product->product_id }}">
                                                <b>{{ $product->product_name }}</b><br>
                                                <i class="fa fa-tag"></i> {{ $product->sku }}
                                            </td>
                            				<td class="text-center">
                                                {{ $product->name }} <br> {{ $product->label }} 
                                            </td>
                            				<td class="text-center">{{ number_format($product->oriPrice,2) }}</td>
                            				{{-- <td class="text-center">{{ $product->oriGST }}</td>
                                            <td class="text-center">{{ $product->oriUnit }}</td>
                            				<td class="text-center">{{ number_format($product->oriDisc, 2) }}</td>
                            				<td class="text-center subtotal">{{ number_format($product->oriTotal,2) }}</td> --}}
                                            <td class="text-center refund_quantity">
                                                {{ number_format($product->unit, 2) }}
                                            </td>
                            				<td class="text-center">
                                                {{ number_format($product->price, 2) }}
                                            </td>
                            				<td class="text-center total_refund">
                                                {{ number_format($product->total, 2) }}
                                            @if($refund->status == "pending" && Refund::permission(Session::get('username'), '0,4'))
                                                <td class="text-center"> 
                                                    <a id="delete_product_option" class="btn btn-primary btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            @endif
                                        </tr>
                                    	@endforeach
                                    @else
                                    <tr id="emptyproduct">
                                        <td colspan="11">No product added.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div> 
                        <label class="col-lg-2 control-label" for="other_option">Others</label>
                        <div class="col-sm-10">
                            @if($refund->status == "pending" && Refund::permission(Session::get('username'), '0,4'))
                                <div id="addOthersBtn" class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addOtherBtn" name="addOtherBtn" class="btn btn-primary addOtherBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Other</span>
                                    </div>
                                </div>
                            @endif
                            <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                            <table class="table table-bordered table-hover">
                                <thead style="background:#EEEEEE">
                                    <tr>
                                        <th class="col-sm-7 text-center">Item Name</th>
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">Amount ({{$currency}})</th>
                                        <th class="hidden-xs hidden-sm col-sm-1 text-center">GST (%)</th>
                                        <th class="cell-small col-sm-1 text-center">Quantity</th>
                                        <th class="cell-small col-sm-1 text-center">Sub-total ({{$currency}})</th>
                                        @if($refund->status == "pending" && Refund::permission(Session::get('username'), '0,4'))
                                            <th class="cell-small col-sm-1 text-center">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="otb">
                                	@if(count($others) > 0)
                                        @foreach ($others as $other)
                                        <tr class="other">
                                            <input type="hidden" id="otherID" name="other_request[][id]" value="{{ $other->id }}">
                                            <td>{{ $other->product_name }}</td>
                                            <td class="text-right">{{ number_format($other->price,2) }}</td>
                                            <td class="text-center">{{ $other->gst_rate }}</td>
                                            {{-- <td class="text-center">{{ $other->unit }}</td>
                                            <td class="text-right subtotal">{{ number_format($other->total,2) }}</td> --}}
                                            <td class="text-center">
                                                {{ $other->unit }}
                                            </td>
                            				<td class="text-right subtotal">
                                                {{ number_format($other->total,2) }}
                                            </td>
                                            @if($refund->status == "pending" && Refund::permission(Session::get('username'), '0,4'))
                                                 <td class="text-center"> 
                                                    <a id="delete_product_option" class="btn btn-primary btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @else
                                	<tr id="emptyother">
                                        <td colspan="6">No other item added.</td>
                                    </tr>
                                	@endif
                                </tbody>
                            </table>
                        </div>
                        <label class="col-lg-2 control-label" for=""></label>
                        <div class="col-sm-10">
                            <table class="table table-bordered">
                                <tbody id="ttb">
                                    {{-- <tr id="emptytotal">
                                        <input type="hidden" name="grand_total">
                                        <td class="text-right col-sm-8"><b>TOTAL ({{Config::get("constants.CURRENCY")}}):</b></td>
                                        <td id="grandTotal" name="grandTotal" class="text-right col-sm-1 grand_total"></td>
                                    </tr> --}}
                                    <tr>
                                        <input type="hidden" name="grand_total_refund">
                                        <td class="text-right col-sm-5"><b>TOTAL REFUND ({{Config::get("constants.CURRENCY")}}):</b></td>
                                        <td id="totalRefund" name="totalRefund" class="text-right col-sm-1 grand_total_refund"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <label class="col-lg-2 control-label" for="">Remark</label>
						<div class="col-sm-10">
							<div style="margin-bottom: 10px;">
                                @if ($refund->status == "pending")
                                    {{ Form::textarea('remark', $refund->remarks, ['class' => 'form-control', 'rows' => '5']) }}
                                @else
                                    {{ Form::textarea('remark', $refund->remarks, ['class' => 'form-control', 'rows' => '5', 'disabled']) }}
                                @endif
                                <?php //if (in_array(Session::get('username'), array('nadzri','kean', 'cocoyeo', 'melissa'), true ) && $refund->status == "pending") {  ?>
                                    {{-- {{ Form::textarea('remark', $refund->remarks, ['class' => 'form-control', 'rows' => '5']) }} --}}
                                <?php //} else { ?>
                                    {{-- {{ Form::textarea('remark', $refund->remarks, ['class' => 'form-control', 'rows' => '5', 'disabled']) }} --}}
                                <?php // }  ?>
                            </div>
						</div>	
                        <label class="col-lg-2 control-label" for="">Supporting Documents</label>
                        <div class="col-sm-10">
                            <?php
                            
                                if (
                                    isset($refund_supp_docs) && 
                                    is_array($refund_supp_docs) &&
                                    count($refund_supp_docs) >= 1
                                ){
                                    foreach ($refund_supp_docs as $key => $value) {
                                        $ext = strtolower(pathinfo($value->supp_docs, PATHINFO_EXTENSION));

                                        if ($ext == 'csv'){
                                            $csv    = Config::get('constants.CSV_UPLOAD_PATH') . $value->supp_docs;
                                            if(file_exists($csv)){
                                                $encrypted = Crypt::encrypt($csv);
                                                $encrypted = urlencode(base64_encode($encrypted));
                                            }

                                            echo '<p>' . HTML::link('refund/files/'. $encrypted, 'Refund CSV File ' . ($key + 1), array('target' => '_blank', 'class' => 'btn btn-primary')) . '</p>';
                                        } else if ($ext == 'pdf') {
                                            $pdf    = Config::get('constants.ATTACHMENT_PDF') . $value->supp_docs;
                                            if(file_exists($pdf)){
                                                $encrypted = Crypt::encrypt($pdf);
                                                $encrypted = urlencode(base64_encode($encrypted));
                                            }

                                            echo '<p>' . HTML::link('refund/files/'. $encrypted, 'Refund Support Doc ' . ($key + 1), array('target' => '_blank', 'class' => 'btn btn-primary')) . '</p>';
                                        } else {
                                            $images = Config::get('constants.ATTACHMENT_IMAGE') . $value->supp_docs;
                                            if(file_exists($images)){
                                                $encrypted = Crypt::encrypt($images);
                                                $encrypted = urlencode(base64_encode($encrypted));
                                            }

                                            echo '<p>' . HTML::link('refund/files/'. $encrypted, 'Refund Support Doc ' . ($key + 1), array('target' => '_blank', 'class' => 'btn btn-primary')) . '</p>';                                    
                                        }
                                    }
                                } else {
                                    echo "No supporting documents upload";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            @if ($refund->status == "approved" || $refund->status == "confirmed")
            <div class="panel @if ($errors->has('type')) panel-danger @else panel-default @endif">
                 {{ $errors->first('type', '<p class="text-danger">&nbsp; :message</p>') }}
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Refund Type</h2>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="refund_option">Refund Type</label>
                        <div class="col-sm-6">
                            @if($refund->status == "approved")
                            <div id="addRefundsBtn" class="input-group">
                                @if ( Refund::permission(Session::get('username'), '0,5'))
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addRefundBtn" name="addRefundBtn" class="btn btn-primary addRefundBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Type</span>
                                    </div>
                                @endif
                            </div>
                            @endif
                            <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                            <table class="table table-bordered table-hover">
                                <thead style="background:#EEEEEE">
                                    <tr class="types">
                                        <th class="col-sm-4 text-center">Refund Type</th>
                                        <th class="hidden-xs hidden-sm col-sm-2 text-center">Amount</th>
                                        <th class="hidden-xs hidden-sm col-sm-2 text-center">Cash Value</th>
                                        <th class="hidden-xs hidden-sm col-sm-2 text-center">Coupon Code</th>
                                        @if($refund->status == "approved")
                                        <th class="hidden-xs hidden-sm col-sm-2 text-center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="typetb">
                                	<?php //echo "<pre>"; //var_dump($types); //echo "</pre>"; ?>
                                    @if (count($types) > 0)
                                        @foreach ($types as $type)
                                        <tr>
                                            <input type="hidden" name="type[][id]" value={{ $type['id'] }}>
                                            <input type="hidden" name="type[][amount]" value={{ $type['amount'] }}>
                                            <input type="hidden" name="type[][coupon_code]" value={{ $type['coupon_code'] }}>
                                            <input type="hidden" name="type[][cash_value]" value={{ $type['cash_value'] }}>
                                            <td name="type[][type]">{{ $type['refund_type'] }}</td>
                                            <td class="text-center">
                                            @if($type['refund_type'] == "Coupon") 
                                                -
                                            @else
                                                {{ $type['amount_type'] }} {{ $type['amount_n_type'] }}
                                            @endif
                                            </td>
                                            <td name="type[][refund_type]" class="text-center">
                                            <?php $amount_type = ($type['amount_type'] == "deduct") ? "-" : "+"; ?>

                                            @if($type['cash_value'] == "")
                                                -
                                            @else
                                            	{{ $type['amount_type'] }} {{$currency}} {{ number_format($type['cash_value'], 2) }}
                                            @endif
                                            </td>

                                            <td name="type[][refund_type]" class="text-center">
                                            @if($type['coupon_code'] == "") 
                                                -
                                            @else
                                                {{ $type['coupon_code'] }}
                                            @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr id="emptyrefund">
                                        <td colspan="6">No refund type added.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                        <div class="form-group">
                            {{ Form::label('remarks', 'Remarks', ['class' => 'col-lg-2 control-label']) }}
                            <div class="col-lg-10">
                                <div class="input-group">
                                    @if ( Refund::permission(Session::get('username'), '0,5'))
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addRemarkBtn" name="addRemarkBtn" class="btn btn-primary addRemarkBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Remark</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                        @if(count($remarks) > 0)
                            @foreach($remarks as $remark)
                            <div class="col-lg-8 col-lg-offset-2">
                                <i class="fa fa-comment"></i><label>&nbsp;&nbsp; {{ $remark->created_by }} @ {{ $remark->created_date }} </label>
                            </div>
                            <div class="col-lg-8 col-lg-offset-2">
                                {{ Form::textarea('remark', $remark->remark, ['class' => 'form-control', 'rows' => '3', 'disabled']) }}
                            </div>
                            @endforeach
                           
                        @endif
                    </div>
                    <div id="remark_div">
                    </div>
                </div>
            </div>

            @endif
            @if($refund->status == "confirmed")
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-pencil"></i> Credit Note Details (Optional)</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12 ">
                        <input type="hidden" id="cn_no" value="{{$cn_no}}">
                        <?php  
                            // echo "<br><b> = = = BUYER DETAILS = = = </b><br>";
                            // var_dump($buyer_details);
                        ?>
                        @if ($cn_no != "")
                        <div class="form-group">
                            {{ Form::label('cn_no', 'Credit Note No.', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <?php
                                if (isset($cn_no) && $cn_no != "" && file_exists(Config::get('constants.CN_PDF_FILE_PATH') . '/' . urlencode($cn_no) . '.pdf'))
                                    {
                                        $file = Config::get('constants.CN_PDF_FILE_PATH') . '/' . urlencode($cn_no) . '.pdf';
                                        $encrypted = Crypt::encrypt($file);
                                        $encrypted = urlencode(base64_encode($encrypted));
                                        ?>
                                        {{ HTML::link('refund/files/'.$encrypted, $cn_no, array('target' => '_blank')) }}
                                        <?php
                                    }
                                    else
                                    {
                                        echo "-";
                                    }

                                ?>
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            {{ Form::label('full_name', 'Buyer Full Name / Company Name', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('full_name', ucwords($buyer_details->full_name), array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('attn', 'Attention To', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('attn', ucwords($buyer_details->attn), array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('address1', 'Address', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('address1', ucwords($buyer_details->address1), array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                        <div class='form-group'>
                        {{ Form::label('address2', 'Address 2', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('address2', ucwords($buyer_details->address2), array('class' => 'form-control buyer') ) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('postcode', 'Postcode', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('postcode', $buyer_details->postcode, array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('city', 'City', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('city', $buyer_details->city, array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                        <div class='form-group'>
                        {{ Form::label('state', 'State', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('state', ucwords($buyer_details->state), array('class' => 'form-control buyer') ) }}
                            </div>
                        </div>
                        <div class='form-group'>
                        {{ Form::label('country', 'Country', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('country', $buyer_details->country, array('class' => 'form-control buyer') ) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('gst_no', 'Buyer GST No', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('gst_no', $buyer_details->gst_no, array('class'=> 'form-control buyer')) }}
                            </div>
                        </div>
                         <div class='form-group'>
                        {{ Form::label('reason', 'Reason', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                            {{ Form::textarea('reason', ucfirst($buyer_details->reason), array('class' => 'form-control buyer', 'rows' => '3') ) }}
                            </div>
                        </div>
                        <div class='form-group'>
                            @if($refund->status == "confirmed" && $cn_no == "")
                            <div class="col-lg-10 col-lg-offset-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        @if ( Refund::permission(Session::get('username'), '0,5'))
                                            <span class="pull-left"><button name="generateCN" id="generateCN" class="btn btn-primary generateCNBtn" value="{{$refund->id}}"><i class="fa fa-plus"></i> Generate Credit Note</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
	{{-- @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND')) --}}
	<div class='form-group'>
		<div class="col-lg-10">
			{{-- {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }} --}}
            {{-- @if ( Refund::permission(Session::get('username'), 'edit'))
                {{ Form::submit('TEST', ['class' => 'btn btn-large btn-primary', 'name' => 'TEST']) }}
            @endif --}}
            @if($refund->status == "pending")
                @if ( Refund::permission(Session::get('username'), '0,1,2,3'))
                    {{ Form::submit('Save Edit', ['class' => 'btn btn-large btn-primary', 'name' => 'save_edit']) }}
                @endif
                @if ( Refund::permission(Session::get('username'), '0,4'))
                    {{ Form::submit('Reject', ['class' => 'btn btn-large btn-danger', 'name' => 'reject']) }}
                    {{ Form::submit('Approve', ['class' => 'btn btn-large btn-primary', 'name' => 'approve']) }}
                @endif
            @elseif ($refund->status == "approved")
                @if ( Refund::permission(Session::get('username'), '0,5'))
                    {{ Form::submit('Reject', ['class' => 'btn btn-large btn-danger', 'name' => 'reject']) }}
                    {{ Form::submit('Confirm', ['class' => 'btn btn-large btn-primary', 'name' => 'confirm']) }}
                @endif
            @elseif ($refund->status == "confirmed")
            @if ( Refund::permission(Session::get('username'), '0,5'))
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary', 'name' => 'save']) }}
                @endif
            @endif
		</div>
	</div>
	{{-- @endif --}}
	{{ Form::close() }}
</div>
	
<div id="clone-base">
    <div class="specialadjust form-group">
        <label class="col-lg-2 control-label" for="remark_doc[]"></label>
        <div class="col-lg-10">
            <div class="max-width-adj inlineblock-middle">
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                    <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">Select file</span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden"><input type="file" name="remark_doc[]">
                    </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
            <div class="input-group-btn inlineblock-middle">
                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')


{{-- Add supporting document --}}
$(document).on('click', ".btn-success", function(){ 
    var target = $(this).parents('.specialadjust.form-group');
    var html = $('#clone-base').html();
    target.after(html);
});

$(document).on('click', ".btn-danger", function(){ 
    var target = $(this).parents('.specialadjust.form-group');
    target.remove();
});

localStorage.clear(); //clear data inside local

if(localStorage.data0) {
    enableBtn();
}

hideButton();
calculateTotal(); 
var cn_no = $('#cn_no').val();
if(cn_no != "") {
    $('.buyer').prop('disabled', true);
}

//Add product
$('#addProdBtn').click(function(e) {
    e.preventDefault();
    var trans_id = $(".trans-id").val();

    $.colorbox({
        iframe:true, 
        width:"90%", 
        height:"90%",
        onClosed:function(){
            calculateTotal();
        },
        href:"/refund/ajaxproduct/" + trans_id,
    });
});

function enableBtn() {
    $('#addProdBtn').prop('disabled', false);
    $('#addOtherBtn').prop('disabled', false);
}

$('#addRemarkBtn').click(function (event) {
    event.preventDefault();
    $('#remark_div').append('<div class="form-group" id="new_remark">\
                                <div class="col-lg-8 col-lg-offset-2">\
                                    <textarea name="new_remark" class="form-control" rows="3"></textarea>\
                                </div>\
                                <div class="col-lg-offset-2">\
                                    <button type="button" class="btn btn-danger delete-remark"><i class="fa fa-minus"></i> Delete Remark</button>\
                                </div>\
                            </div>');
    $('#addRemarkBtn').prop('disabled', true);
});


$(document).on('click', '.delete-remark', function() {
    $('#new_remark').remove();
    $('#addRemarkBtn').prop('disabled', false);
});

$('#addOtherBtn').click(function(e) {
    e.preventDefault();
    $('#emptyother').hide();

    bootbox.dialog({
        title: "Add Refund Item",
        message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' + 
                    '<form class="form-horizontal"> ' +
                        '<div class="form-group">' +
                            '<label class="col-md-3 control-label" for="name">Item name *</label>' +
                            '<div class="col-md-8">' +
                                '<input id="name" type="text" name="name" class="form-control" placeholder="Item name">' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="col-md-3 control-label" for="amount">Unit Amount ({{$currency}})*<br>(exclude GST)</label>' +
                            '<div class="col-md-6">' +
                                '<input id="amount" type="text" name="amount" class="form-control" placeholder="Amount">' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="col-md-3 control-label" for="gst_rate">GST Rate(%)</label>' +
                            '<div class="col-md-6">' +
                                '<input id="gst_rate" type="text" name="gst_rate" class="form-control" placeholder="GST" value="0">' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="col-md-3 control-label" for="qty">Quantity</label>' +
                            '<div class="col-md-6">' +
                                '<input id="qty" type="text" name="qty" class="form-control" value="1">' +
                            '</div>' +
                        '</div>' +
                    '</form></div></div>',
        size: "large",
        buttons: {
            success: {
                label: "Add",
                className: "btn-success add-other",
                callback: function() {
                    var name        = $('#name').val();
                    var amount      = parseFloat($('#amount').val()).toFixed(2);
                    var gst_rate    = parseFloat($('#gst_rate').val());
                    var qty         = $('#qty').val();
                    var total;
                    //var total       = parseFloat($('#total').val()).toFixed(2);

                    if (gst_rate > 0) {
                        var gst_value = amount * gst_rate / 100;
                        total       = parseFloat(amount) + parseFloat(gst_value);
                        subtotal    = parseFloat(total) * qty;
                        //alert('[gst_rate: '+ gst_rate +'] [gst_value: '+ gst_value +'] [qty: '+ qty +'] [total: ' + total +'] [subtotal: ' + subtotal +']');
                    } else {
                        subtotal    = amount;
                    }

                    var rowTd   = '<input type="hidden" name="other[][name]" value="'+ name +'">\
                                    <input type="hidden" name="other[][price]" value="'+ amount +'">\
                                    <input type="hidden" name="other[][gst_rate]" value="'+ gst_rate +'">\
                                    <input type="hidden" name="other[][unit]" value="'+ qty +'">\
                                    <input type="hidden" name="other[][total]" value="'+ parseFloat(subtotal).toFixed(2) +'">\
                                    <td>' + name + '</td><td class="text-right col-xs-1">' + amount + '</td><td class="text-center col-xs-1">'+ gst_rate + '</td>\
                                    <td class="text-center col-xs-1">'+ qty + '</td><td class="text-right col-xs-1 subtotal">'+ parseFloat(subtotal).toFixed(2) +'</td>\
                                    <td class="text-center col-xs-1"><div class="btn-group">\
                                        {{-- <a class="btn btn-xs btn-danger" id="deleteOther" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\ --}}
                                        <a class="btn btn-primary btn-danger" id="deleteOther" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>\
                                    </div></td>';

                    parent.$('#otb').append('<tr id="other" class="other">'+rowTd+'</tr>');
                    calculateTotal();
                }
            }
        }
    });

});

$('#addRefundBtn').click(function(e) {
        e.preventDefault();
        $('#emptyrefund').hide();
            bootbox.dialog({
            title: "Add Refund Type",
            message: '<div class="row">  ' +
                        '<div class="col-md-12"> ' + 
                        '<form class="form-horizontal"> ' +
                            '<div class="form-group">' +
                                '<div class="col-md-2 col-md-offset-1">' +
                                    '<div class="radio">&nbsp;&nbsp;&nbsp;' +
                                        '<input type="radio" name="name" value="Cash"><b>Cash:</b>' +
                                    '</div>' +
                                '</div>' + 
                                '<label class="col-md-1 control-label" for="amount">{{$currency}}</label>' + 
                                '<div class="col-md-4">' +
                                    '<input id="amount" type="text" name="amount" class="form-control" placeholder="Amount">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<div class="col-md-2 col-md-offset-1">' +
                                    '<div class="radio">&nbsp;&nbsp;&nbsp;' +
                                        '<input type="radio" name="name" value="JoPoint"><b>JoPoint:</b>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="col-md-2">' +
                                    '<select class="form-control" name="amount_type" id="amount_type">' +
                                        '<option value="+" selected>Add</option>' +
                                        '<option value="-">Deduct</option>' +
                                    '</select>' +
                                '</div>' +
                                '<div class="col-md-3">' +
                                    '<input id="point" type="text" name="point" class="form-control" placeholder="Point">' +
                                '</div>' +
                                
                                '<label class="col-md-1 control-label" for="point">{{$currency}}</label>' + 
                                '<div class="col-md-3">' +
                                    '<input id="cash_value" type="text" name="cash_value" class="form-control" placeholder="Cash Value">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<div class="col-md-2 col-md-offset-1">' +
                                    '<div class="radio">&nbsp;&nbsp;&nbsp;' +
                                        '<input type="radio" name="name" value="Coupon"><b>Coupon:</b>' +
                                    '</div>' +
                                '</div>' + 
                                '<div class="col-md-4">' +
                                    '<input id="coupon_code" type="text" name="coupon_code" class="form-control" placeholder="Coupon Code">' +
                                '</div>' +
                            '</div>' +
                        '</form></div></div>',
            size: "large",
            buttons: {
                success: {
                    label: "Add",
                    className: "btn-success add-type",
                    callback: function() {
                        var name            = $('input[name="name"]:checked').val();
                        var amount          = $('#amount').val();
                        var amount_type     = "";
                        var point           = $('#point').val();
                        var cash_value      = "";
                        var coupon_code     = "-";
                        var amount_n_type   = "-";
                        var cash_value_type = "-";
                        
                        if(!$('input[name="name"]:checked').val()) {
                            bootbox.alert('Please select the refund type.');
                            return false;
                        }

                        if(name == "Cash")
                            amount_n_type  = parseFloat($('#amount').val()).toFixed(2);

                        if(name == "JoPoint") {
                            amount          = point;
                            amount_type     = $('#amount_type').val();
                            amount_n_type   = amount_type + amount + ' points';
                            cash_value      = $('#cash_value').val();
                            cash_value_type = amount_type + ' ' + parseFloat(cash_value).toFixed(2);
                        }

                        if(name == "Coupon") {
                            coupon_code     = $('#coupon_code').val();
                        }

						//var rowCV 	= '<input type="hidden" name="type[][cash_value]" value="'+ cash_value +'">';
						
                        var rowTd   = '<input type="hidden" name="type[][name]" value="'+ name +'">\
                                        <input type="hidden" name="type[][amount]" value="'+ amount +'">\
                                        <input type="hidden" name="type[][amount_type]" value="'+ amount_type +'">\
                                        <input type="hidden" name="type[][coupon_code]" value="'+ coupon_code +'">\
                                        <input type="hidden" name="type[][cash_value]" value="'+ cash_value +'">\
                                        <td>' + name + 
                                        '</td><td class="text-center col-xs-1">' + amount_n_type + '</td><td class="text-center col-xs-1">'+ cash_value_type + '</td><td class="text-center col-xs-1">'+ 
                                        coupon_code + '</td>\
                                        <td class="text-center col-xs-1"><div class="btn-group">\
                                        <a class="btn btn-xs btn-danger" id="deleteType" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                        </div></td>';

                        var rowTotal    = parent.$('#typetb tr').length;
                        var $row        = parent.$("input[name='type[][name]']");
                        var exist       = 0;

                        $row.each(function() {
                            var type = $(this).val();
                            if (type == name) {
                                exist = 1;
                                bootbox.alert('Refund Type already EXISTS! - ' + name);
                            }
                            
                        });

                        if (exist == 0) {
                        	//if (name == "JoPoint")
                        		//parent.$('#typetb').append('<tr id="type" class="type">'+rowCV + rowTd+'</tr>');
                        	//else
                            	parent.$('#typetb').append('<tr id="type" class="type">'+rowTd+'</tr>');
                            //localStorage.setItem("rtype" + rowCount, name);
                        }
                    }
                }
            }
        });

    });

function hideButton() {
    $('#addTransBtn').hide(); 
    $('#addProductBtn').hide(); 
    //$('#addOthersBtn').hide(); 
    //$('#addRefundsBtn').hide(); 
}

function currencyFormat(num) {
    return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function calculateTotal() {
    var grandTotal              = 0;
    var productTotal            = 0;
    var otherTotal              = 0;
    var totalRefund             = 0;
    var productTotalRefund      = 0;
    var transTotal;

    var totalTest               = 0;

    {{-- $('tr.product').each(function(){
        var refund_quantity = $(this).find('input[name="refund_quantity"]');
        var refund_quantity_val = $(this).find('input[name="refund_quantity"]').val();
        var refund_price = $(this).find('input[name="refund_price"]');
        var refund_price_val = $(this).find('input[name="refund_price"]').val();
        var total_refund = $(this).find('input[name="total_refund"]');
        var total_refund_val = $(this).find('input[name="total_refund"]').val();
        console.log('refund_quantity_val: ' + refund_quantity_val);
        console.log('refund_price_val: ' + refund_price_val);
        console.log('total_refund_val: ' + total_refund_val);

        refund_quantity.change(function(){
            refund_quantity_val = $(this).val();
            totalTest = refund_quantity_val * refund_price_val
            console.log('refund_quantity_val: ' + refund_quantity_val);
            console.log('totalTestEditQuantity: ' + totalTest);
        });
        refund_price.change(function(){
            refund_price_val = $(this).val();
            totalTest = refund_price_val * refund_quantity_val
            console.log('refund_price_val: ' + refund_price_val);
            console.log('totalTestEditPrice: ' + totalTest);
        });
    }); --}}

    transTotal = $('input[name="trans_amount"]').val();

    if(transTotal == "") {
        transTotal      = parseFloat(0).toFixed(2);
    }

    $('tr.product td.subtotal').each(function(){
        p_subtotal = $(this).text();
        productTotal += parseFloat(p_subtotal.replace(/,/g,''));
    });
    
    $('tr.product td.total_refund').each(function(){
        p_subtotal_refund = $(this).text();
        productTotalRefund += parseFloat(p_subtotal_refund.replace(/,/g,''));
    });

    // console.log('Product Total: ' + productTotal);

    $('tr.other td.subtotal').each(function(){
        o_subtotal = $(this).text();
        otherTotal += parseFloat(o_subtotal.replace(/,/g,''));
    });

    //alert('Other Total: ' + otherTotal);

    grandTotal = productTotal + otherTotal;
    grandTotal = currencyFormat(grandTotal);
    totalRefund = productTotalRefund + otherTotal;
    totalRefund = currencyFormat(totalRefund);

    $('.grand_total').html(grandTotal);
    $('input[name="grand_total"]').val(grandTotal);
    $('.grand_total_refund').html(totalRefund);
    $('input[name="grand_total_refund"]').val(totalRefund);

    {{-- if (totalRefund > parseFloat(transTotal)) {
        bootbox.alert({
            title: "Warning!",
            message: "The <b>Refund Amount ( " + totalRefund + ")</b> is more than the <b>Transaction Amount ( " + transTotal +")</b>."
        });
    } --}}
}


$("input:checkbox").on('click', function() {
    // in the handler, 'this' refers to the box clicked on
    var $box = $(this);

    if ($box.is(":checked")) {
        alert('A');
        var group = "input:checkbox[name='" + $box.attr("name") + "']";

        $(group).prop("checked", false);
        $box.prop("checked", true);
    } else {
        alert('B');

        $box.prop("checked", false);
    }
});

$(document).on("click", "#deleteOther", function(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
    calculateTotal();
    if(!$('.other').length) {
        $('#emptyother').show();
        //$('#grandTotal').remove();
    }
});

$(document).on("click", "#deleteType", function(e) {
    e.preventDefault();
    $(this).closest("tr").remove();
    if(!$('.other').length) {
        $('#emptytype').show();
    }
});

// Delete product option
$(document).on('click', '#delete_product_option', function(e) {
    e.preventDefault();
    localStorage.clear(); //clear data inside local
	if ($('.product').length != 0) {
		$(this).closest('tr').remove();

		$('#ptb tr').each(function (index) {
			$(this).children().first().html(index + 1);
		});

		if (!$('input[name="price[][default]"]:checked').val()) {
			$('input[name="price[][default]"]:nth(0)').prop('checked', true);
		}
	} else {
		bootbox.alert({
			title: 'Error',
			message: 'Please insert at least one (1) price option.',
		});

		$('input[name="price[][default]"]:nth(0)').prop('checked', true);
	}
    
    calculateTotal();
});

@stop

<!-- 21/03/2022 - Change codes (add product's details direct to refund_details table) -->
<!-- 04/03/2022 - Add drop down selection for platform_store) -->