<?php $tempcount = 0; ?>
<div class="panel panel-default">
	<div class="panel-heading clearfix">
		<div style="float: right"><div class="btn btn-default" onclick="ShrinkExpand(this)">Shrink</div><div class="btn btn-success" onclick="duplicatecoupon(this)">Duplicate</div><div class="btn btn-danger" onclick="deletecoupon(this)">Delete</div></div>
		<h3 class="panel-title" style="line-height: 36px;"><i class="fa fa-pencil"></i> Coupon {{ (int)$k + 1 }}<input type="hidden" name="cp_idx[]" value="{{ $k }}"></h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			{{ Form::label('cp_shortname[]', 'Coupon Short Name', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-5">
				{{ Form::text('cp_shortname[]', $v['shortname'], ['class'=> 'form-control']) }}
				<p class="form-control-static" style="color: red">*No special character and spacing allow at "Coupon Short Name"</p>
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_dis_name[]', 'Coupon Display Name', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_dis_name[]', $v['dis_name'], ['class'=> 'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_dis_desc[]', 'Coupon Display Description', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_dis_desc[]', $v['dis_desc'], ['class'=> 'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_dis_color[]', 'Coupon Display Text Color', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3"><input type="color" name="cp_dis_color[]" class="form-control"{{ ($v['dis_color'] ? 'value="' . $v['dis_color'] . '"' : 'value="#ffffff"') }}/></div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_dis_bg[]', 'Coupon Display Pie Background Color', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3"><input type="color" name="cp_dis_bg[]" class="form-control"{{ ($v['dis_bg'] ? 'value="' . $v['dis_bg'] . '"' : 'value="#004746"') }}/></div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_name[]', 'Name', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_name[]', $v['name'], ['class'=> 'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_amount[]', "Amount (" . Config::get('constants.CURRENCY') . ")", ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::text('cp_amount[]', $v['amount'], ['required'=>'required', 'class'=> 'form-control']) }}</div>
			<div class="col-lg-2">{{ Form::select('cp_amount_type[]', ['%' => '%', 'Nett' => 'Nett'], $v['amount_type'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_status[]', 'Status', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_status[]', [0 => 'Inactive', 1 => 'Active'], (int)$v['status'], ['class' => 'form-control']) }}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_min_purchase[]', "Minimum Purchases (" . Config::get('constants.CURRENCY') . ")", ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_min_purchase[]', $v['min_purchase'], ['class'=> 'form-control', 'placeholder' => 'amount inclusive GST']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_max_purchase[]', "Maximum Purchases (" . Config::get('constants.CURRENCY') . ")", ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_max_purchase[]', ($v['max_purchase'] ? $v['max_purchase'] : ''), ['class'=> 'form-control', 'placeholder' => ''])}}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_valid_from[]', 'Start Date', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_valid_from[]', $v['valid_from'], ['placeholder' => 'yyyy-mm-dd', 'class'=>'form-control datepicker-field']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_valid_to[]', 'End Date', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_valid_to[]', $v['valid_to'], ['placeholder' => 'yyyy-mm-dd', 'class'=>'form-control datepicker-field']) }}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_q_limit[]', 'Set Quantity Limit', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_q_limit[]', ['No' => 'No', 'Yes' => 'Yes'], $v['q_limit'], ['class'=>'form-control']) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_qty[]', 'Quantity', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::text('cp_qty[]', $v['qty'], ['class'=> 'form-control']) }}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_c_limit[]', 'Set Limit Per Customer', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_c_limit[]', ['No' => 'No', 'Yes' => 'Yes'], $v['c_limit'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_cqty[]', 'Quantity', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::text('cp_cqty[]', $v['cqty'], ['class'=> 'form-control']) }}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_free_delivery[]', 'Free Delivery Charges', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_free_delivery[]', [0 => 'No', 1 => 'Yes'], (int)$v['free_delivery'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_free_process[]', 'Free Process Fees', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_free_process[]', [0 => 'No', 1 => 'Yes'], (int)$v['free_process'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_delivery_discount[]', "Delivery Discount (" . Config::get('constants.CURRENCY') . ")", ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('cp_delivery_discount[]', $v['delivery_discount'], ['class'=> 'form-control', 'placeholder' => '0 equal to No discount']) }}</div>
		</div>

		<hr />

		<div class="form-group">
			{{ Form::label('cp_boost_payment[]', 'Boost Payment', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_boost_payment[]', [0 => 'No', 1 => 'Yes'], $v['boost_payment'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_razerpay_payment[]', 'RazerPay Payment', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_razerpay_payment[]', [0 => 'No', 1 => 'Yes'], $v['razerpay_payment'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_tng_payment[]', 'TnG Payment', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_tng_payment[]', [0 => 'No', 1 => 'Yes'], $v['tng_payment'], ['class'=>'form-control']) }}</div>
		</div>
		<div class="form-group">
			{{ Form::label('cp_is_jpoint[]', 'JPoint Restriction', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">{{ Form::select('cp_is_jpoint[]', [0 => 'No', 1 => 'Yes'], $v['is_jpoint'], ['class'=>'form-control']) }}</div>
		</div>
		
		<hr />

		<div class="form-group">
			{{ Form::label('cp_type[]', 'Type', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-2">
				{{ Form::select('cp_type[]', ['all' => 'All', 'seller' => 'Seller', 'item' => 'Item', 'package' => 'Package', 'customer' => 'Customer', 'category' => 'Category'], $v['type'], ['class'=>'form-control']) }}
				{{ Form::input('hidden', 'cp_ori_type[]', $v['type']) }}
			</div>
		</div>

		<div class="form-group display_company" {{ $v['type'] != 'seller' ? 'style="display:none"' : '' }}>
			{{ Form::label('related_seller[]', 'Seller', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('related_seller[]', null, ['class'=> 'form-control', 'readonly'=>'readonly']) }}</div>
			<div class="col-lg-5"><a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectData(event, this, 'seller')">Select</a> {{ Form::button('Insert', ['class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()']) }}</div>
		</div>
		<div class="form-group display_company_list" {{ $v['type'] != 'seller' ? 'style="display:none"' : '' }}>
			{{ Form::label('related_id', 'List of Seller', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-10">
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-details">
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>Username</th>
							<th>Seller Name</th>
							<th></th>
						</tr>
						@if(count($v['coupon_type_data']['Seller']))
							@foreach($v['coupon_type_data']['Seller'] as $coupon_seller)
							<tr class="odd gradeX">
								<td>{{ $tempcount++ }}</td>
								<td>{{ $coupon_seller->id }}</td>
								<td>{{ $coupon_seller->username }}</td>
								<td>{{ $coupon_seller->company_name }}</td>
								<td><a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type(this, {{ $coupon_seller->id }})"><i class="fa fa-remove"></i></a></td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>      
			</div>
		</div>
		<div class="form-group display_product" {{ $v['type'] != 'item' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_item[]', 'Product', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('related_item[]', null, ['class'=> 'form-control', 'readonly'=>'readonly']) }}</div>
			<div class="col-lg-5"><a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectData(event, this, 'product')">Select</a> {{ Form::button('Insert', ['class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()']) }}</div>
		</div>
		<div class="form-group display_product_list" {{ $v['type'] != 'item' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_id', 'List of Product', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-10">                              
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-details">
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>SKU</th>
							<th>Name</th>
							<th></th>
						</tr>
						@if(count($v['coupon_type_data']['Item']))
							@foreach($v['coupon_type_data']['Item'] as $coupon_item)
							<tr class="odd gradeX">
								<td>{{ $tempcount++ }}</td>
								<td>{{ $coupon_item->id }}</td>
								<td>{{ $coupon_item->sku }}</td>
								<td>{{ $coupon_item->name }}</td>
								<td><a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type(this, {{ $coupon_item->id }})"><i class="fa fa-remove"></i></a></td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>    
			</div>
		</div>
		<div class="form-group display_package" {{ $v['type'] != 'package' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_package[]', 'Package', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('related_package[]', null, ['class'=> 'form-control', 'readonly'=>'readonly']) }}</div>
			<div class="col-lg-5"><a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectData(event, this, 'package');">Select</a> {{ Form::button('Insert', ['class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()']) }}</div>
		</div>
		<div class="form-group display_package_list" {{ $v['type'] != 'package' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_id', 'List of Package', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-10">                              
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-details">
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>SKU</th>
							<th>Name</th>
							<th></th>
						</tr>
						@if(count($v['coupon_type_data']['PItem']))
							@foreach($v['coupon_type_data']['PItem'] as $coupon_package)
							<tr class="odd gradeX">
								<td>{{ $tempcount++ }}</td>
								<td>{{ $coupon_package->id }}</td>
								<td>{{ $coupon_package->sku }}</td>
								<td>{{ $coupon_package->name }}</td>
								<td><a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type(this, {{ $coupon_package->id }})"><i class="fa fa-remove"></i></a></td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
		<div class="form-group display_customer" {{ $v['type'] != 'customer' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_customer[]', 'Customer', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('related_customer[]', null, ['class' => 'form-control', 'readonly' => 'readonly']) }}</div>
			<div class="col-lg-5"><a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectData(event, this, 'customer')">Select</a> {{ Form::button('Insert', ['class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()']) }}</div>
		</div>
		<div class="form-group display_customer_list" {{ $v['type'] != 'customer' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_id', 'List of Customer', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-10">
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-details">
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>Username</th>
							<th>Full Name</th>
							<th></th>
						</tr>
						@if(count($v['coupon_type_data']['Customer']))
							@foreach($v['coupon_type_data']['Customer'] as $coupon_customer)
							<tr class="odd gradeX">
								<td>{{ $tempcount++ }}</td>
								<td>{{ $coupon_customer->id }}</td>
								<td>{{ $coupon_customer->username }}</td>
								<td>{{ $coupon_customer->full_name }}</td>
								<td><a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type(this, {{ $coupon_customer->id }})"><i class="fa fa-remove"></i></a></td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
		<div class="form-group display_category" {{ $v['type'] != 'category' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_category[]', 'Category', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-3">{{ Form::text('related_category[]', null, ['class'=> 'form-control', 'readonly'=>'readonly']) }}</div>
			<div class="col-lg-5"><a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectData(event, this, 'category')">Select</a> {{ Form::button('Insert', ['class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()']) }}</div>
		</div>
		<div class="form-group display_category_list" {{ $v['type'] != 'category' ? 'style="display:none"' : '' }} >
			{{ Form::label('related_id', 'List of Category', ['class'=> 'col-lg-2 control-label']) }}
			<div class="col-lg-10">
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-details">
						<tr>
							<th>#</th>
							<th>ID</th>
							<th>Category Name</th>
							<th></th>
						</tr>
						@if(count($v['coupon_type_data']['Category']))
							@foreach($v['coupon_type_data']['Category'] as $coupon_category)
							<tr class="odd gradeX">
								<td>{{ $tempcount++ }}</td>
								<td>{{ $coupon_category->id }}</td>
								<td>{{ $coupon_category->category_name }}</td>
								<td><a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type(this, {{ $coupon_category->id }})"><i class="fa fa-remove"></i></a></td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
		<input type="hidden" name="type_related[]" value="{{ (isset($v['related_id']) && $v['related_id'] ? $v['type'] . '|' . implode(',', $v['related_id']) : '') }}">
	</div>
</div>