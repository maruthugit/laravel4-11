@extends('layouts.master')
@section('title') Coupon @stop
@section('content')

<?php 
$tempcount = 1;

$currency = Config::get('constants.CURRENCY');
?>

<!-- For datepicker in edit coupon -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>


<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
        $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" }).val();

    });

    $(function() {
        $('#type').on('change', function(e) {
            console.log(e);
            var type = e.target.value;

            if(type == 'all'){
                $('#display_company').hide();
                $('#display_company_list').hide();
                $('#display_product').hide();
                $('#display_product_list').hide();
                $('#display_package').hide();
                $('#display_package_list').hide();
                $('#display_customer').hide();
                $('#display_customer_list').hide();
                $('#display_category').hide();
                $('#display_category_list').hide();
            }

            if(type == 'seller'){
                $('#display_company').show();
                $('#display_company_list').show();
                $('#display_product').hide();
                $('#display_product_list').hide();
                $('#display_package').hide();
                $('#display_package_list').hide();
                $('#display_customer').hide();
                $('#display_customer_list').hide();
                $('#display_category').hide();
                $('#display_category_list').hide();
            }

            if(type == 'item'){
                $('#display_product').show();
                $('#display_product_list').show();
                $('#display_company').hide();
                $('#display_company_list').hide();
                $('#display_package').hide();
                $('#display_package_list').hide();
                $('#display_customer').hide();
                $('#display_customer_list').hide();
                $('#display_category').hide();
                $('#display_category_list').hide();
            }

            if(type == 'package'){
                $('#display_package').show();
                $('#display_package_list').show();
                $('#display_company').hide();
                $('#display_company_list').hide();
                $('#display_product').hide();
                $('#display_product_list').hide();
                $('#display_customer').hide();
                $('#display_customer_list').hide();
                $('#display_category').hide();
                $('#display_category_list').hide();
            }

            if(type == 'customer'){                
                $('#display_customer').show();
                $('#display_customer_list').show();
                $('#display_product').hide();
                $('#display_product_list').hide();
                $('#display_package').hide();
                $('#display_package_list').hide();
                $('#display_company').hide();
                $('#display_company_list').hide();
                $('#display_category').hide();
                $('#display_category_list').hide();
            }

            if(type == 'category'){                
                $('#display_category').show();
                $('#display_category_list').show();
                $('#display_product').hide();
                $('#display_product_list').hide();
                $('#display_package').hide();
                $('#display_package_list').hide();
                $('#display_company').hide();
                $('#display_company_list').hide();
                $('#display_customer').hide();
                $('#display_customer_list').hide();
            }
        });
    });

    function selectCompany() {
        window.open("<?php echo asset('/') ?>coupon/selectseller", "", "width=600, height=800, scrollbars");
    }

    function getUserFromChild(id) {
        $("#related_com_id").val(id);
    }

    function selectProduct() {
        window.open("<?php echo asset('/') ?>coupon/selectitem", "", "width=600, height=800, scrollbars");
    }

    function getUserFromChild2(id) {
        $("#related_item_id").val(id);
    }

    function selectCustomer() {
        window.open("<?php echo asset('/') ?>coupon/selectcustomer", "", "width=600, height=800, scrollbars");
    }

    function getUserFromChild3(id) {
        $("#related_customer_id").val(id);
    }

    function selectPackage() {
        window.open("<?php echo asset('/') ?>coupon/selectpackage", "", "width=600, height=800, scrollbars");
    }

    function getUserFromChild4(id) {
        $("#related_package_id").val(id);
    }

    function selectCategory() {
        window.open("<?php echo asset('/') ?>coupon/selectcategory", "", "width=600, height=800, scrollbars");
    }

    function getUserFromChild5(id) {
        $("#related_category_id").val(id);
    }


</script>
<script>
  $( function() {

    var availableTags = [<?php foreach ($customers as $key => $value) {
        echo '"' .$value->username."(".$value->full_name.")".'"'.",";
    } ?>];
    
    $( "#tags" ).autocomplete({
      source: availableTags
    });
  } );
  </script>
<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Coupon Management
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
                        {{ Form::open(array('url'=>'coupon/edit/'.$display_coupon->id, 'class' => 'form-horizontal')) }}
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
                            {{ Form::label('username', 'Username', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('username', $display_coupon->username, array('class'=> 'form-control','id'=>'tags')) }}
                                </div>                                
                            </div>
                            <div class="form-group @if ($errors->has('amount')) has-error @endif">
                            {{ Form::label('amount', "Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('amount', $display_coupon->amount, array('required'=>'required', 'class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('amount')}}</p>
                                </div>
                                <div class="col-lg-2">
                                    {{Form::select('amount_type', array('%' => '%', 'Nett' => 'Nett'), $display_coupon->amount_type, ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('status', array('0' => 'Inactive', '1' => 'Active'), $display_coupon->status, ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('min_purchase')) has-error @endif">
                                {{ Form::label('min_purchase', "Minimum Purchases ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('min_purchase', $display_coupon->min_purchase, array('class'=> 'form-control', 'placeholder' => 'amount inclusive GST'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('min_purchase')}}</p>
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('max_purchase')) has-error @endif">
                                {{ Form::label('max_purchase', "Maximum Purchases ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('max_purchase', $display_coupon->max_purchase, array('class'=> 'form-control', 'placeholder' => ''))}}
                                    <p class="help-block" for="inputError">{{$errors->first('max_purchase')}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                {{ Form::label('valid_from', 'Start Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_from', $display_coupon->valid_from, array('id'=>'datepicker', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('valid_to', 'End Date', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('valid_to', $display_coupon->valid_to, array('id'=>'datepicker2', 'placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>

                            <hr />

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

                            <hr />

                             <div class="form-group">
                            {{ Form::label('c_limit', 'Set Limit Per Customer', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('c_limit', array('No' => 'No', 'Yes' => 'Yes'), $display_coupon->c_limit, ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group @if ($errors->has('cqty')) has-error @endif">
                            {{ Form::label('cqty', 'Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('cqty', $display_coupon->cqty, array('class'=> 'form-control')) }}
                                    <p class="help-block" for="inputError">{{$errors->first('cqty')}}</p>
                                </div>                                
                            </div>

                            <hr />

                             <div class="form-group">
                            {{ Form::label('free_delivery', 'Free Delivery Charges', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('free_delivery', array('0' => 'No', '1' => 'Yes'), $display_coupon->free_delivery, ['class'=>'form-control'])}}
                                </div>
                            </div>
                            
                            <div class="form-group">
                            {{ Form::label('free_process', 'Free Process Fees', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('free_process', array('0' => 'No', '1' => 'Yes'), $display_coupon->free_process, ['class'=>'form-control'])}}
                                </div>                                
                            </div>
                            
                            <hr /> 

                            <div class="form-group">
                            {{ Form::label('boost_payment', 'Boost Payment', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('boost_payment', array('0' => 'No', '1' => 'Yes'), $display_coupon->boost_payment, ['class'=>'form-control'])}}
                                </div>                                
                            </div>
                            
                            <div class="form-group">
                            {{ Form::label('razerpay_payment', 'Shopee Pay Payment', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('razerpay_payment', array('0' => 'No', '1' => 'Yes'), $display_coupon->razerpay_payment, ['class'=>'form-control'])}}
                                </div>                                
                            </div>
                            
                            <hr />
                            <div class="form-group">
                            {{ Form::label('is_jpoint', 'JPoint Restriction', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                   <input type="checkbox" value="1" name="is_jpoint" @if($display_coupon->is_jpoint=="1") checked="checked" @endif>
                                </div>                                
                            </div>
                            <hr />
                             <div class="form-group">
                            {{ Form::label('is_preferred_member', 'Preferred Member', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                   <input type="checkbox" value="1" name="is_preferred_member" @if($display_coupon->is_preferred_member=="1") checked="checked" @endif>
                                </div>                                
                            </div>
                            <hr />
                            
                             <div class="form-group @if ($errors->has('zone_id')) has-error @endif">
							{{ Form::label('region', 'Region', array('class'=> 'col-lg-2 control-label')) }}
							<div class="col-lg-2">
								<select id="delivery_fee" class="form-control">
									<option value="0">Select Region</option>
									@foreach ($zoneOptions as $zone)
										@if ( ! in_array($zone->id, $deliveryZone))
											<option value="{{ $zone->id }}">{{ $zone->name }}</option>
										@endif
									@endforeach
								</select>
								{{ $errors->first('zone_id', '<p class="help-block">The delivery zone is required</p>') }}
							</div>
							<div class="col-lg-8">
								<button type="button" id="add_zone" class="btn btn-primary" disabled><i class="fa fa-plus"></i> Add Zone</button>
							</div>
						</div>
						<div id="zone_div">
							@foreach ($deliveryFees as $index => $fee)
								<div id="zone_row_{{ $index }}" class="form-group">
									<div class="col-lg-10 col-lg-offset-2">
										<input type="hidden" value="{{ $fee->zone_id }}" name="zone_id[{{ $index }}]">
										<div class="row">
											<div class="col-lg-3">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-globe fa-fw"></i>
													</span>
													<input type="text" name="zone_name[{{ $index }}]" class="form-control" value="{{ $fee->name }}" disabled>
												</div>
											</div>
											<div class="col-lg-2">
												<button type="button" class="btn btn-danger delete-zone" data-zone="{{ $index }}"><i class="fa fa-minus"></i> Remove Zone</button>
											</div>
										</div>
									</div>
								</div>
								<?php $zoneCounter = $index; ?>
							@endforeach
						</div>

                            <hr />  

                            <div class="form-group">
                            {{ Form::label('type', 'Type', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{Form::select('type', array('all' => 'All', 'seller' => 'Seller', 'item' => 'Item', 'package' => 'Package', 'customer' => 'Customer', 'category' => 'Category'), $display_coupon->type, ['class'=>'form-control'])}}
                                    {{Form::input('hidden', 'ori_type', $display_coupon->type)}}
                                </div>
                            </div>

                            <!-- Seller -->
                            <div class="form-group" id="display_company" <?php if($display_coupon->type != 'seller') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_company', 'Seller', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_com_id', null, array('class'=> 'form-control', 'id'=>'related_com_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectCompany();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>

                            <div class="form-group" id="display_company_list" <?php if($display_coupon->type != 'seller') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_id', 'List of Seller', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>Username</th>
                                                    <th>Seller Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($display_seller as $coupon_seller)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$coupon_seller->id}}</td>
                                                        <td>{{$coupon_seller->username}}</td>
                                                        <td>{{$coupon_seller->company_name}}</td>
                                                        <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type({{$coupon_seller->id;}});"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->           
                                </div>
                            </div>

                            <!-- Product -->
                            <div class="form-group" id="display_product" <?php if($display_coupon->type != 'item') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_product', 'Product', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_item_id', null, array('class'=> 'form-control', 'id'=>'related_item_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectProduct();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>                         

                            <div class="form-group" id="display_product_list"  <?php if($display_coupon->type != 'item') { ?>style="display:none"<?php } ?> >
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

                            <!-- Package -->
                            <div class="form-group" id="display_package" <?php if($display_coupon->type != 'package') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_package', 'Package', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_package_id', null, array('class'=> 'form-control', 'id'=>'related_package_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectPackage();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>                         

                            <div class="form-group" id="display_package_list"  <?php if($display_coupon->type != 'package') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_id', 'List of Package', array('class'=> 'col-lg-2 control-label')) }}
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
                                                @foreach($display_package as $coupon_package)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$coupon_package->id}}</td>
                                                        <td>{{$coupon_package->sku}}</td>
                                                        <td>{{$coupon_package->name}}</td>
                                                        <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type({{$coupon_package->id;}});"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->  

                     
                                </div>
                            </div>


                            <!-- User -->
                             <div class="form-group" id="display_customer" <?php if($display_coupon->type != 'customer') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_customer', 'Customer', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_customer_id', null, array('class'=> 'form-control', 'id'=>'related_customer_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectCustomer();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>                         

                            <div class="form-group" id="display_customer_list"  <?php if($display_coupon->type != 'customer') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_id', 'List of Customer', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>Username</th>
                                                    <th>Full Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($display_customer as $coupon_customer)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$coupon_customer->id}}</td>
                                                        <td>{{$coupon_customer->username}}</td>
                                                        <td>{{$coupon_customer->full_name}}</td>
                                                        <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type({{$coupon_customer->id;}});"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                            </div>

                            <!-- Category -->
                             <div class="form-group" id="display_category" <?php if($display_coupon->type != 'category') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_category', 'Category', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('related_category_id', null, array('class'=> 'form-control', 'id'=>'related_category_id', 'readonly'=>'readonly')) }}
                                </div>
                                <div class="col-lg-5">
                                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="selectCategory();return false;">Select</a> {{ Form::button('Insert', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                                  
                                </div>
                            </div>                         

                            <div class="form-group" id="display_category_list"  <?php if($display_coupon->type != 'category') { ?>style="display:none"<?php } ?> >
                                {{ Form::label('related_id', 'List of Category', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                            <thead>
                                               <tr>
                                                    <th>#</th>
                                                    <th>ID</th>
                                                    <th>Category Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($display_category as $coupon_category)
                                                    <tr class="odd gradeX">
                                                        <td>{{$tempcount++}}</td>
                                                        <td>{{$coupon_category->id}}</td>
                                                        <td>{{$coupon_category->category_name}}</td>
                                                        <td> <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_type({{$coupon_category->id;}});"><i class="fa fa-remove"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                            </div>

                            <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','quenny','nadzri','boobalan'), true ) ) {  ?>
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

            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND'))
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Duplicate Coupon</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'coupon/duplicate/'.$display_coupon->id, 'class' => 'form-horizontal')) }}
                            <div class="form-group">
                            {{ Form::label('duplicate', 'Duplicate Quantity', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-2">
                                    {{ Form::text('duplicate', null, array('class'=> 'form-control', 'placeholder' => 'Min:1, Max:100', 'required' => 'required')) }}
                                </div>
                                <div class="col-lg-2">
                                    <button class="btn btn-success" type="submit">Duplicate</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            @endif
        </div>
        <!-- /.col-lg-12 -->
    </div>
</div>

{{ Form::open(array('url'=>'coupon/remove', 'id'=>'remove_frm')) }}
{{Form::input('hidden', 'remove_type_id', null, ['id'=>'remove_type_id'])}}
{{Form::input('hidden', 'couponID', $display_coupon->id, ['id'=>'couponID'])}}
{{ Form::close() }}

<script type="text/javascript">
function delete_type(id) {
    if(confirm("Are you sure to delete?")) {
        

        var tempid = document.getElementById("remove_type_id");
        tempid.value = id;

        var tempform = document.getElementById("remove_frm");
        tempform.submit();
        
    }
    
}
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
		$('#zone_div').append('<div id="zone_row_' + zone_index + '" class="form-group"><div class="col-lg-10 col-lg-offset-2"><input type="hidden" value="' + zone_id + '" name="zone_id[' + zone_index + ']"><div class="row"><div class="col-lg-3"><div class="input-group"><span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span><input type="text" name="zone_name[' + zone_index + ']" class="form-control" value="' + zone_name + '" disabled></div></div><div class="col-lg-2"><button type="button" class="btn btn-danger delete-zone" data-zone="' + zone_index + '"><i class="fa fa-minus"></i> Remove Zone</button></div></div></div></div>');
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
</script>

 
@stop

