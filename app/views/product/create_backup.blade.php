@extends('layouts.master')

@section('title', 'Product')
<script src="/js/angular_modified.js"></script>
<style>

    .suggestion-opt-box{
        //display: none;
        position: absolute;
        background-color: #fff;
        z-index: 100;
        margin-right: 15px !important;
        margin-left: 0px  !important;
        font-size: 13px;
    }
    
    .sel-product-base{
        margin-bottom: 5px;
    }
    
    .attach-base{
/*        padding-left: 15px;
        padding-right: 15px;*/
    }
    
    .suggestion-opt{
        border: solid 1px #ddd;padding: 10px;top:-1px;border-top: 0px;
    }
    
    .suggestion-opt:hover{
        background-color: #f3f3f3;
        cursor: pointer;
    }
    
</style>
@section('content')
<div id="page-wrapper" ng-app="jocom" ng-controller="jocomProduct">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Products</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-pencil"></i> Add Product</h3>
				</div>
				<div class="panel-body">
					{{ Form::open(['url' => 'product/store', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true]) }}
<!--						<div class="form-group @if ($errors->has('seller_name')) has-error @endif">
							{{ Form::label('seller_name', 'Seller Name', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::select('seller_name', ['' => 'Select Seller Name'] + $sellersOptions, null, ['class' => 'form-control']) }}
								{{ $errors->first('seller_name', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<hr>-->
                                                <div class="form-group">
							{{ Form::label('product_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								<select class="form-control" id="region_country_id" name="region_country_id">
                                                                <?php foreach ($countries as $key => $value) { ?>
                                                                    <option value="<?php echo $value->id; ?>" <?php if($product->region_country_id == $value->id){ echo "selected";} ?>><?php echo $value->name;?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                                                                <?php if(Session::get('branch_access') != 1){?>
                                                                <option value="">All region</option>
                                                                <?php } ?>
                                                                <?php foreach ($regions as $key => $value)  { ?>
                                                                    <option value="<?php echo $value->id; ?>" <?php if($value->id == $product->region_id) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                                                <?php  } ?>
                                                            </select>
							</div>
                                                        <div class="col-lg-3">
								
							</div>
						</div>
                                                <hr>
                                                <div class="form-group">
							{{ Form::label('base_product', 'Stock Product', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
                                                            <input type="checkbox" name="is_base_product" id="is_base_product" value="1" <?php echo $product->is_base_product == 1 ? 'checked':''; ?>>
							</div>
						</div>
                                                <hr>
                                                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
							{{ Form::label('seller_name', 'Seller Name', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
                                                            <select class="form-control" id="seller_name2" name="seller_name2">
                                                                <option value="">Select Seller Name</option>
                                                                <?php foreach ($sellersOptions as $key => $value) { ?>
                                                                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                                                <?php } ?>
                                                            </select>
							</div>
                                                        <div class="col-lg-1">
                                                            <a class="btn btn-primary" id="add-seller-btn" ><i class="fa fa-plus"></i> Add Seller</a>
                                                        </div>
                                                        <div class="col-lg-5 col-lg-offset-2">
                                                            <table class="table table-striped table-bordered" style="margin-top: 10px;"> 
                                                                <thead> 
                                                                    <tr> 
                                                                        <th class="col-md-8">Seller Name</th> 
                                                                        <th class="col-md-3">State</th> 
                                                                        <th class="col-md-2"></th> 
                                                                    </tr> </thead> 
                                                                <tbody id="multipleSellerTable"> 
                                                                
                                                                </tbody> 
                                                            </table>
                                                        </div>
						</div>
						<hr>
                                                

						<div class="form-group @if ($errors->has('product_name')) has-error @endif">
							{{ Form::label('product_name', 'Product Name *', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::text('product_name', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_name', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('product_name')) has-error @endif">
							{{ Form::label('product_shortname', 'Product Short Name *', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::text('product_shortname', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_shortname', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('product_name_cn')) has-error @endif">
							{{ Form::label('product_name_cn', 'Product Name (Chinese)', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::text('product_name_cn', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_name_cn', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('product_name_my')) has-error @endif">
							{{ Form::label('product_name_my', 'Product Name (Bahasa)', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::text('product_name_my', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_name_my', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<hr>
						<div class="form-group @if ($errors->has('product_desc')) has-error @endif">
							{{ Form::label('product_desc', 'Description', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-10">
								{{ Form::textarea('product_desc', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_desc', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('product_desc_cn')) has-error @endif">
							{{ Form::label('product_desc_cn', 'Description (Chinese)', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-10">
								{{ Form::textarea('product_desc_cn', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_desc_cn', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('product_desc_my')) has-error @endif">
							{{ Form::label('product_desc_my', 'Description (Bahasa)', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-10">
								{{ Form::textarea('product_desc_my', null, ['class' => 'form-control']) }}
								{{ $errors->first('product_desc_my', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<hr>
						<div <?php if($isFixedOption){ echo 'style="display:none;"'; } ?> class="form-group @if ($errors->has('main_category')) has-error @endif">
							{{ Form::label('main_category', 'Primary Category', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-4">
								<ul id="main_category" class="form-control categories-container">
									@foreach ($categoriesOptions as $category)
									@if ( $category['status']  == 1 )
										<li id="main_{{ $category['id'] }}" @if($category['status'] == 0) class="inactive" @endif>
										@if ( ! empty($category['category_name']))
											{{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
										@endif
										</li>
									@endif
									@endforeach
								</ul>
							</div>
						</div>
						<div class="form-group @if ($errors->has('categories')) has-error @endif">
						    <?php if($isFixedOption){ ?>
                                {{ Form::label('', '', ['class' => 'col-lg-2 control-label ']) }}
                                <div class="col-lg-4" style="display:none;">
								<label class="label-category">Available Category</label>
								<ul id="available_category" class="form-control categories-container">
									@foreach ($categoriesOptions as $category)
										<li id="available_{{ $category['id'] }}" @if($category['status'] == 0) class="inactive" @endif>
										@if ( ! empty($category['category_name']))
											{{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
										@endif
										</li>
									@endforeach
								</ul>
							</div>
                            <?php } else{ ?>
							{{ Form::label('categories', 'Secondary Category', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-4">
								<label class="label-category">Available Category</label>
								<ul id="available_category" class="form-control categories-container">
									@foreach ($categoriesOptions as $category)
										<li id="available_{{ $category['id'] }}" @if($category['status'] == 0) class="inactive" @endif>
										@if ( ! empty($category['category_name']))
											{{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
										@endif
										</li>
									@endforeach
								</ul>
							</div>
							<div class="col-lg-1 img-switch"></div>
							 <?php } ?>
							<div class="col-lg-4">
								<label class="label-category">Selected Category</label>
								<ul id="selected_category" class="form-control categories-container">
									@foreach ($categoriesOptions as $category)
										<li id="selected_{{ $category['id'] }}" class="hide @if($category['status'] == 0) inactive @endif">
										@if ( ! empty($category['category_name']))
											{{ $category['category_name'] }} @if ($category['permission']) **[Private] @endif [ID: {{ $category['id'] }}]
										@endif
										</li>
									@endforeach
								</ul>
								<div id="categories">
									@if (Session::has('_old_input') && ! empty(Input::old('categories')))
										<input id="old-main-category" type="hidden" value="{{ Input::old('main_category') }}">
										@foreach (Input::old('categories') as $category)
											<input class="old-categories" type="hidden" value="{{ $category }}">
										@endforeach
									@endif
									{{-- Hidden inputs will be generated on submit by JavaScript --}}
								</div>
							</div>
							<div class="col-lg-10 col-lg-offset-2">
								{{ $errors->first('categories', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<hr>
						<?php
							$count		= 0;
							$has_error	= false;

							for ($i = 0; $i < Session::get('errorsPriceOptions'); $i++)
							{
								$priceArray = [
									'label',
									'price',
									'qty',
									'p_referral_fees',
									'p_referral_fees_type',
									'default',
								];

								foreach ($priceArray as $price) {
									if ($errors->has("price.{$count}.{$price}")) {
										$has_error  = true;
									}
								}

								$count++;
							}
						?>
						<div class="form-group @if ($has_error == true) has-error @endif">
							{{ Form::label('add_price_option', 'Price Option', ['class' => 'col-lg-2 control-label']) }}
							<div class="col-lg-10">
								<button type="button" ng-click="AddOptionPrice()" id="add_price_option" class="btn btn-primary"><i class="fa fa-plus"></i> Add Price Option</button>
								@if ($has_error == true) <p class="help-block">Label, Price, Quantity, Referral Fees and Default price label are required.</p> @endif
								<div class="clearfix"><br></div>
								<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>No.</th>
												<th>Label</th>
												<th>Price</th>
												<th>Other Details</th>
												<th>Default</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="prices">
											<tr class="price_option">
												<td class="col-xs-1">1</td>
												<td class="col-xs-3">
													<div class="form-group">
														{{ Form::label('enPriceLabel', 'EN:', ['class'=> 'col-sm-2 help-block']) }}
														<div class="col-sm-10">
															<input id="enPriceLabel" type="text" class="form-control" name="price[][label]" placeholder="English">
															{{ $errors->first('price_labe', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
														{{ Form::label('cnPriceLabel', 'CN:', ['class'=> 'col-sm-2 help-block']) }}
														<div class="col-sm-10">
															<input id="cnPriceLabel" type="text" class="form-control" name="price[][label_cn]" placeholder="Chinese">
															{{ $errors->first('price_labe', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
														{{ Form::label('myPriceLabel', 'MY:', ['class'=> 'col-sm-2 help-block']) }}
														<div class="col-sm-10">
															<input id="myPriceLabel" type="text" class="form-control" name="price[][label_my]" placeholder="Bahasa">
															{{ $errors->first('price_labe', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
															{{ Form::label('alternativePriceLabel', 'WH:', ['class'=> 'col-sm-2 help-block']) }}
															<div class="col-sm-10">
																<input id="alPriceLabel" type="text" class="form-control" name="price[][alternative_label_name]" placeholder="Warehouse Label" value="{{ $price->alternative_label_name }}">
																{{ $errors->first('price_labe', '<p class="help-block">:message</p>') }}
															</div>
													</div>
												</td>
												<td class="col-xs-3">
													<div class="form-group">
														{{ Form::label('price', 'Actual:', ['class'=> 'col-sm-4 help-block']) }}
														<div class="col-sm-8">
															<input id="price" type="text" class="form-control text-right" name="price[][price]" placeholder="Actual Price">
															{{ $errors->first('price', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
														{{ Form::label('price_promo', 'Promotion:', ['class'=> 'col-sm-4 help-block']) }}
														<div class="col-sm-8">
															<input id="price_promo" type="text" class="form-control text-right" name="price[][price_promo]" placeholder="Promotion">
															{{ $errors->first('price_promo', '<p class="help-block">:message</p>') }}
														</div>
													</div>
                                                                                                        <div class="col-md-12 " style="text-align: center; border-top: solid 1px #ececec;border-bottom: solid 1px #ececec;padding: 10px;margin-bottom: 5px;background-color: #d9534f;
                                                                                                        color: #ffffff;">Cost Price</div>
                                                                                                        <div class="list-seller-cost">
                                                                                                            
                                                                                                        </div>
												</td>
												<td class="col-xs-3">
													<div class="form-group">
														{{ Form::label('seller_sku', 'Seller SKU:', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="seller_sku" type="text" class="form-control" name="price[][seller_sku]" placeholder="Seller SKU">
															{{ $errors->first('seller_sku', '<p class="help-block">:message</p>') }}
														</div>
													</div>
		                                                                                        <div class="form-group">
														{{ Form::label('barcode', 'Barcode:', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="barcode" type="text" class="form-control" name="price[][barcode]" placeholder="Barcode">
															{{ $errors->first('barcode', '<p class="help-block">:message</p>') }}
														</div>
													</div>											          
													<div class="form-group">
														{{ Form::label('qty', 'Quantity:', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="qty" type="text" class="form-control" name="price[][qty]" placeholder="Quantity">
															{{ $errors->first('qty', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
														{{ Form::label('stock', 'Actual Stock:', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="stock" type="text" class="form-control" name="price[][stock]" placeholder="Actual Stock">
															{{ $errors->first('stock', '<p class="help-block">:message</p>') }}
														</div>
													</div>
													<div class="form-group">
                                                                                                                    {{ Form::label('stock_unit', 'Stock Unit:', ['class'=> 'col-sm-5 help-block']) }}
                                                                                                                    <div class="col-sm-7">
                                                                                                                        <input id="stock_unit" type="text" class="form-control" name="price[][stock_unit]" placeholder="Unit Measurement" value="{{ $price->stock_unit }}">
                                                                                                                        {{ $errors->first('stock_unit', '<p class="help-block">:message</p>') }}
                                                                                                                    </div>
                                                                                                                </div>
													<div class="form-group">
														{{ Form::label('p_referral_fees', 'Referral Fees:', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="p_referral_fees" type="text" class="form-control" name="price[][p_referral_fees]" placeholder="Referral Fees">
															{{ $errors->first('p_referral_fees', '<p class="help-block">:message</p>') }}
														</div>
														<div class="col-sm-7" style="margin-top: 15px;">
															<select name="price[][p_referral_fees_type]" id="price[][p_referral_fees_type]" class="form-control">
																<option value="%">%</option>
																<option value="N">Nett</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														{{ Form::label('p_weight', 'Product Weight (gram):', ['class'=> 'col-sm-5 help-block']) }}
														<div class="col-sm-7">
															<input id="p_weight" type="text" class="form-control" name="price[][p_weight]" placeholder="Weight (gram)">
															{{ $errors->first('p_weight', '<p class="help-block">:message</p>') }}
														</div>
													</div>
                                                                                                    <h4 class="ui horizontal divider header base-box" >
                                                                                                        <i class="tag icon"></i>
                                                                                                        Base Product
                                                                                                    </h4>
                                                                                                    <div class="row base-box" style="position:relative;padding-left: 15px;padding-right: 15px;">
                                                                                                        <div class="input-group">
                                                                                                            <input type="text" class="form-control input-search-base"  id="search-base-{{ $index + 1 }}" ng-keyup="PFindProduct('search-base-{{ $index + 1 }}','suggestion-opt-box-{{ $index + 1 }}','{{ $index + 1 }}')" placeholder="search base product..">
                                                                                                            <span class="input-group-addon">x</span>
                                                                                                          </div>
                                                                                                        <div class="row suggestion-opt-box" id="suggestion-opt-box-{{ $index + 1 }}" style="">
<!--                                                                                                                    <div ng-repeat="x in suggestion_opt" class="col-md-12 suggestion-opt" >
                                                                                                                <div style="float:right;"><a class="btn btn-default" ng-click="AddProductBase(x.sku)"><i class="fa fa-plus"></i></a></div>
                                                                                                                <b>[[x.sku]]</b>
                                                                                                                <br>[[x.name]] 
                                                                                                            </div>-->
                                                                                                        </div>

                                                                                                        <hr>
                                                                                                        <div class="form-group attach-base" id="attach-base-{{ $index + 1 }}">

                                                                                                        <?php 
                                                                                                        if(isset($optionBaseProduct[$price->id])){
                                                                                                        foreach ($optionBaseProduct[$price->id] as $key => $value) { ?>
                                                                                                        <div class="col-md-12 sel-product-base"><div class="input-group"><span class="input-group-addon" id="basic-addon1" style="text-align:left;">
                                                                                                                <b><?php echo $value['sku'];?></b>
                                                                                                                <div><?php echo $value['name'];?></div></span>
                                                                                                                <span class="input-group-addon" id="basic-addon1" style="width: 10px;"> 
                                                                                                                <input class="form-control" style="width: 50px;min-width:50px;float: right;" name="productbase[option-{{ $index + 1 }}][sku][<?php echo $value['sku'];?>]" type="text" value="<?php echo $value['qty'];?>"></span>
                                                                                                                <span class="input-group-addon remove-product" id="basic-addon1" style="background-color:#d9ddea;cursor: pointer;"><i class="fa fa-trash-o"></i></span> 
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <?php }} ?>   
                                                                                                        </div>
                                                                                                    </div>
												</td>
												<input type="hidden" name="price[][id]" value="{{ $price->id }}">
												<td class="col-xs-1">
													<input type="radio" name="price[][default]" value="1" checked>
												</td>
												<td class="col-xs-1">
													<button type="button" id="delete_price_option" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Remove</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group @if ($errors->has('gst')) has-error @endif">
							{{ Form::label('gst', 'GST Status', ['class'=> 'col-lg-2 control-label']) }}
							<div class="col-lg-10">
							    <?php if(!$isFixedOption){ ?>
								<label class="radio-inline">
									<input type="radio" value="2" name="gst"> Taxable
								</label
								<?php } ?>  
								
								<label class="radio-inline">
									<input type="radio" value="1" name="gst" checked> Zero Rated
								</label>
								
								<?php if(!$isFixedOption){ ?>
								<label class="radio-inline">
									<input type="radio" value="0" name="gst"> Exempted
								</label>
								<?php } ?>   
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('gst_value', 'GST Value', ['class'=> 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								<p class="form-control-static">{{$gst}} %</p>
								<input type="hidden" name="gst_value" value="{{$gst}}">
							</div>
						</div>
						<hr>
						<div class="form-group @if ($errors->has('delivery_time')) has-error @endif">
							{{ Form::label('delivery_time', 'Delivery Time', ['class'=> 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								{{ Form::select('delivery_time', [
									''					=> 'Select Delivery Time',
									'24 hours'			=> '24 hours',
									'1-2 business days'	=> '1-2 business days',
									'2-3 business days'	=> '2-3 business days',
									'3-7 business days'	=> '3-7 business days',
									'14 business days'	=> '14 business days',
								], null, ['class' => 'form-control']) }}
								{{ $errors->first('delivery_time', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group @if ($errors->has('zone_id')) has-error @endif">
							{{ Form::label('delivery_fee', 'Delivery Fee', array('class'=> 'col-lg-2 control-label')) }}
							<div class="col-lg-2">
								<select id="delivery_fee" class="form-control">
									<option value="0">Select Zone</option>
									@foreach ($zoneOptions as $zone)
										<option value="{{ $zone->id }}">{{ $zone->name }}</option>
									@endforeach
								</select>
								{{ $errors->first('zone_id', '<p class="help-block">The delivery zone is required</p>') }}
							</div>
							<div class="col-lg-8">
								<button type="button" id="add_zone" class="btn btn-primary" disabled><i class="fa fa-plus"></i> Add Zone</button>
							</div>
						</div>
						<div id="zone_div"></div>
						<hr>
						<div class="form-group @if ($errors->has('related_product')) has-error @endif">
						{{ Form::label('related_product', 'Related Products (QR Code)', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-6">
							{{ Form::text('related_product', null, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
							{{ $errors->first('related_product', '<p class="help-block">:message</p>') }}
							</div>
						</div>

						<div class="form-group @if ($errors->has('related_product')) has-error @endif">
						{{ Form::label('tag', 'Product Tags', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-6">
							{{ Form::text('tag', null, array('placeholder' => 'e.g. food, fresh', 'class' => 'form-control') ) }}
							{{ $errors->first('tag', '<p class="help-block">:message</p>') }}
							</div>
						</div>

						<div class="form-group @if ($errors->has('do_cat')) has-error @endif" style="display: none;">
						{{ Form::label('do_cat', 'DO Category', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-6">
							{{ Form::text('do_cat', null, array('placeholder' => 'e.g. MEAT', 'class' => 'form-control') ) }}
							{{ $errors->first('do_cat', '<p class="help-block">:message</p>') }}
							</div>
						</div>

						<hr>
						<div class="form-group">
							{{ Form::label('bulk', 'Bulk', ['class'=> 'col-lg-2 control-label']) }}
							<div class="col-lg-3">
								<input type="checkbox" name="bulk" value="1">
							</div>
						</div>
						<div class="form-group @if ($errors->has('tag')) has-error @endif">
						{{ Form::label('freshness', 'Product Freshness', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-6">
							{{ Form::textarea('freshness', null, array('placeholder' => 'e.g. Within 24 hours', 'class' => 'form-control', 'maxlength' => '255', 'style' => 'width: 100%; height: 50px;') ) }}
							{{ $errors->first('freshness', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group">
						{{ Form::label('freshness_days', 'Freshness Days', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-1">
							    <input type="text" class="form-control" name="freshness_days" id="freshness_days" placeholder="0"></input>
							</div>
						</div>
						<div class="form-group @if ($errors->has('status')) has-error @endif">
							{{ Form::label('status', 'Product Status', array('class'=> 'col-lg-2 control-label')) }}
							<div class="col-lg-3">
								{{ Form::select('status', ['0' => 'Inactive', '1' => 'Active'], null, ['class'=> 'form-control']) }}
								{{ $errors->first('status', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<hr>

						<div class="form-group @if ($errors->has('permission')) has-error @endif">
							{{ Form::label('weight', 'Product Sorting', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-3">
								{{ Form::text('weight', 0, array('class'=> 'form-control')) }}
								{{ $errors->first('weight', '<p class="help-block">:message</p>') }}
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('halal', 'Halal', ['class'=> 'col-lg-2 control-label','style'=>'padding-bottom:25px; padding-top:0px;']) }}
							<div class="col-lg-3">
								<input type="checkbox" name="halal" value="1">
							</div>
						</div>                 
						<hr>

						<div class="form-group">
							{{ Form::label('min_qty', 'Min Quantity', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-1">
								<input type="number" name="min_qty" class="form-control" min="0" placeholder="0">
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('max_qty', 'Max Quantity', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-1">
								<input type="number" name="max_qty" class="form-control" placeholder="0" min="0">
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
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('inputjs')
{{ HTML::script('js/fileinput.min.js') }}
<script>
/* 
 */
var app = angular.module('jocom', []);
    
app.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

app.controller("jocomProduct", function($scope,$http,$timeout,$filter,$compile) {
    
    $scope.suggestion_opt = [];
    
    $scope.PFindProduct = function(targetSearch,suggestionBox,position){
        
        var suggestionOpt = '';
        $scope.suggestion_opt = [];
        //console.log("Search Value: " + $("#"+targetSearch).val());
        var product = $("#"+targetSearch).val();
        //console.log(product);
        if(product.length > 1){
           var request = $http({
                method: "post",
                url: "/api/searchbaseproduct",
                data: {keyword:product }
            }).then(function successCallback(response) {
                console.log(response.data);
                $scope.suggestion_opt = response.data.search;
                console.log("#"+suggestionBox);
                
                angular.forEach(response.data.search, function(value, key) {
                   // console.log(value);
                    suggestionOpt = suggestionOpt + '<div class="col-md-12 suggestion-opt" ><div style="float:right;"><a class="btn btn-default" ng-click="AddProductBase('+"'"+value.sku+"','"+value.name.replace("'",'')+"','"+position+"'"+')"><i class="fa fa-plus"></i></a></div><b>'+value.sku+'</b><br>'+value.name+'</div>';
    
                });
                
                //"+"'"+value.sku+"','"+value.name+"','"+suggestionBox+"'
                var temp = $compile(suggestionOpt)($scope);
                $("#"+suggestionBox).html(temp)
                $("#"+suggestionBox).show()
               // $compile(suggestionOpt)($scope);
                
                //console.log(suggestionOpt);
                
                // this callback will be called asynchronously
                // when the response is available
            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
           
       }else{
            $scope.suggestion_opt = [];
            $(".suggestion-opt-box").css("display", "none")
       }
         

    }
    
    $scope.AddProductBase= function(sku,name,position){
       
        var template = '<div class="col-md-12 sel-product-base" ><div class="input-group"><span class="input-group-addon" id="basic-addon1" style="text-align:left;">\n\
            <b>'+sku+'</b>\n\
            <div>'+name+'</div></span>\n\
            <span class="input-group-addon" id="basic-addon1" style="width: 10px;"> \n\
            <input class="form-control" style="width: 50px;min-width:50px;float: right;" name="productbase[option-'+position+'][sku]['+sku+']" type="text" value="1"></span>\n\
            <span class="input-group-addon remove-product" id="basic-addon1" style="background-color:#d9ddea;cursor: pointer;" ><i class="fa fa-trash-o"></i></span> \n\
            </div></div>';
        
        $("#attach-base-"+position).append(template);
        
        $(".suggestion-opt").hide();
       
    }
    
    $scope.AddOptionPrice= function(){
        
        $scope.suggestion_opt = [];
        
	var trLast = $('#prices').find('tr:last');
	var row = 0;
        var next_row = $('#prices').children().length + 1;
        

	$('#prices tr').each(function (index) {
		if ($('input[name="price[][default]"]:nth(' + index + ')').is(':checked')) {
			row = index;
		}
	});

	var trNew = trLast.clone();
        //
        trNew.find('.attach-base').html('');
        trNew.find('.attach-base').attr('id','attach-base-'+next_row);
        trNew.find('.attach-base').html('');
        trNew.find('.suggestion-opt-box').attr('id','suggestion-opt-box-'+next_row); 
        trNew.find('.input-search-base').attr('id','search-base-'+next_row); 
        trNew.find('.input-search-base').attr('ng-keyup',"PFindProduct('search-base-"+next_row+"','suggestion-opt-box-"+next_row+"','"+next_row+"')"); 
        
        trNew.find('input').val('').end().appendTo(trLast);

	$('#prices tr').each(function (index) {
		$(this).children().first().html(index + 1);
	});
        var temp = $compile(trNew)($scope);
	trLast.after(temp);
	$('input[name="price[][default]"]:nth(' + row + ')').prop('checked', true);

        
    }
    
    
     
});

$( document ).ready(function() {
    
    $('body').on('click', '#is_base_product', function() {
        
//        alert($("#prices").children().length);
        
        if($("#prices").children().length > 1 && $("#is_base_product").is(':checked')){
            $("#is_base_product").prop("checked", false);
            alert('Sorry. this product cannot be stock base as it have more than 1 price option')
        }else if($("#is_base_product").is(':checked') == false){
            $("#is_base_product").prop("checked", false);
            $("#add_price_option").show();
            $(".base-box").show();
            
        }else{
            $("#is_base_product").prop("checked", true);
            $("#add_price_option").hide();
            $(".base-box").hide();
            
        }
       
    });
    
    $('body').on('change', '#region_country_id', function() {
        loadOptionRegion($(this).val());
    });

    function loadOptionRegion(countryID){
        
        $.ajax({
                method: "POST",
                url: "/region/country",
                dataType:'json',
                data: {
                    'country_id':countryID
                },
                beforeSend: function(){
                },
                success: function(data) {
                    console.log(data.data.region);
                    var regionList = data.data.region;
                    var str = '<option value="0">All Region</option>';
                    $.each(regionList, function (index, value) {
                        str = str + "<option value='"+value.id+"'>"+value.region+"</option>";
                       console.log(str);
                    });
                    $("#region_id").html(str);
                    
                }
          })
        
    }
    
   $('body').on('click', '.remove-seller', function() {
       
        if($("#multipleSellerTable").children().length <= 1){
            alert('Must has minimum 1 Seller');
        }else{
             var seller_id = $(this).attr("seller-id");
             $(".seller-cost-"+seller_id).remove();
            console.log($(this).parent().parent().remove());
        }
        
    });
   
   $("#add-seller-btn").click(function(){
       
        console.log($("#seller_name2").val());
       
        var seller_id = $("#seller_name2").val();
        $.ajax({
                method: "POST",
                url: "/product/sellerinfo",
                data: {
                    'seller_id':seller_id
                },
                beforeSend: function(){
                },
                success: function(data) {
                   var str = '<tr><td>'+data.seller_name+'<input name="seller_multiple[]" value="'+data.seller_id+'" type="hidden"></td> <td>'+data.seller_state+'</td> <td><a seller-id="'+data.seller_id+'" class="btn btn-danger btn-xs remove-seller"><i class="fa fa-trash-o"></i> Remove</a></td></tr>';
                   $("#multipleSellerTable").append(str);
                   
                   var strSeller = " <div class='form-group seller-cost-"+data.seller_id+"' > \
                        <div class='col-sm-12'><i class='fa fa-caret-right help-block'></i> "+data.seller_name+"</div> \
                        <label for='cost_price' class='col-sm-4 help-block'>Cost</label> \
                        <div class='col-sm-8'><input id='price' type='text' class='form-control text-right' name='price[][cost_price]["+data.seller_id+"]' placeholder='Actual Price' value='0'></div> \
                    </div>";
    
                    $(".list-seller-cost").append(strSeller);
                }
          })
          
        // console.log($(this).parent().parent().remove());
   });
                
    
    
});

</script>
@stop

@section('script')
// Update selected category on submit bounced back
if ($('.old-categories').length > 0) {
	$('#selected_category li').addClass('hide');
	$('.old-categories').each(function () {
		$('#available_' + $(this).val()).addClass('hide');

		if ($(this).val() == $('#old-main-category').val()) {
			$('#main_' + $(this).val()).addClass('hide');
			$('#selected_' + $(this).val()).removeClass('hide').addClass('main');
		} else {
			$('#selected_' + $(this).val()).removeClass('hide');
		}
	});
}

// Main category
$('#main_category li').click(function () {
	var prefix = 'main_';
	var id = this.id.substr(prefix.length);
	$('#available_' + id).addClass('hide');

	$('#main_category li').each(function () {
		$(this).removeClass('hide');
	});

	$('#selected_category li.main').each(function () {
		$(this).addClass('hide').removeClass('main');
		$('#available_' + this.id.substr(('selected_').length)).removeClass('hide');
	});

	$('#main_' + id).addClass('hide');
	$('#selected_' + id).removeClass('hide').addClass('main');
});

<?php if($isFixedOption){ ?>
    $('#main_948').click();
    $('#selected_949').removeClass("hide");
    $('#selected_949').addClass("main");
    $('#selected_948').removeClass("main");
    
    
    $('#is_base_product').click();
    $('#is_base_product').attr("disabled","");
<?php } ?>

// Available category
$('#available_category li').click(function () {
	var prefix = 'available_';
	var id = this.id.substr(prefix.length);

	$(this).addClass('hide');
	$('#selected_' + id).removeClass('hide');
});

// Selected category
$('#selected_category li').click(function () {
	var prefix = 'selected_';
	var id = this.id.substr(prefix.length);

	if ( ! $('#selected_' + id).hasClass('main'))
	{
		$(this).addClass('hide');
		$('#available_' + id).removeClass('hide');
	}
});


// Delete price option
$(document).on('click', '#delete_price_option', function() {
	if ($('.price_option').length != 1) {
		$(this).closest('tr').remove();

		$('#prices tr').each(function (index) {
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
});

// Select zone
$('select#delivery_fee').change(function () {
	var zone_id		= $('select#delivery_fee option').filter(':selected').val();

	if (zone_id > 0) {
		$('#add_zone').prop('disabled', false);
	} else {
		$('#add_zone').prop('disabled', true);
	}
});

// Add zone
var zone_index	= {{ $zoneCounter + 1 }};

$('#add_zone').click(function () {
	var zone_id		= $('select#delivery_fee option').filter(':selected').val();
	var zone_name	= $('select#delivery_fee option').filter(':selected').text();

	if (zone_id > 0) {
		$('#zone_div').append('<div id="zone_row_' + zone_index + '" class="form-group"><div class="col-lg-10 col-lg-offset-2"><input type="hidden" value="' + zone_id + '" name="zone_id[' + zone_index + ']"><div class="row"><div class="col-lg-3"><div class="input-group"><span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span><input type="text" name="zone_name[' + zone_index + ']" class="form-control" value="' + zone_name + '" disabled></div></div><div class="col-lg-2"><div class="input-group"><span class="input-group-addon">{{Config::get("constants.CURRENCY")}}</span><input type="text" name="zone_price[' + zone_index + ']" class="form-control text-right" placeholder="Delivery Fee"></div></div><div class="col-lg-2"><button type="button" class="btn btn-danger delete-zone" data-zone="' + zone_index + '"><i class="fa fa-minus"></i> Remove Zone</button></div></div></div></div>');
		$('select#delivery_fee option[value="' + zone_id + '"]').remove();
		$('#add_zone').prop('disabled', true);
		$('select#delivery_fee').val(0);
	}

	zone_index++;
});

// Delete zone
$(document).on('click', '.delete-zone', function() {
	var zone_index	= $(this).data('zone');
	var zone_id		= $('input[name="zone_id[' + zone_index + ']"]').val();
	var zone_name	= $('input[name="zone_name[' + zone_index + ']"]').val();

	$('#delivery_fee').append('<option value="' + zone_id + '">' + zone_name + '</option>');
	$('#zone_row_' + zone_index).remove();
});

// jQuery plugin to prevent double submission of forms
// URL: http://technoesis.net/prevent-double-form-submission-using-jquery/
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};

$('form').preventDoubleSubmission();

// Submit
$('#add').submit(function(event) {
	$('#selected_category li.main').not('.hide').each(function () {
		var prefix = 'selected_';
		var id = this.id.substr(prefix.length);
		$('#categories').append('<input type="hidden" name="main_category" value="' + id + '">');
	});

	$('#selected_category li').not('.hide').each(function () {
		var prefix = 'selected_';
		var id = this.id.substr(prefix.length);

		$('#categories').append('<input type="hidden" name="categories[]" value="' + id + '">');
	});
});
@stop