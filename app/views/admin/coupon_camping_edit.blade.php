@extends('layouts.master')
@section('title') Coupon Camping @stop
@section('content')
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script>
		$(function() {
			$(".datepicker-field").datepicker({ dateFormat: "yy-mm-dd" }).val();

			$(document).on('change', '[name="cp_type[]"]', function(e) {
				var type = e.target.value;
				var t_parent = $(e.target).parents('.panel-body')[0];

				$(t_parent).find('.display_company').hide();
				$(t_parent).find('.display_company_list').hide();
				$(t_parent).find('.display_product').hide();
				$(t_parent).find('.display_product_list').hide();
				$(t_parent).find('.display_package').hide();
				$(t_parent).find('.display_package_list').hide();
				$(t_parent).find('.display_customer').hide();
				$(t_parent).find('.display_customer_list').hide();
				$(t_parent).find('.display_category').hide();
				$(t_parent).find('.display_category_list').hide();

				if(type == 'seller'){
					$(t_parent).find('.display_company').show();
					$(t_parent).find('.display_company_list').show();
				}

				if(type == 'item'){
					$(t_parent).find('.display_product').show();
					$(t_parent).find('.display_product_list').show();
				}

				if(type == 'package'){
					$(t_parent).find('.display_package').show();
					$(t_parent).find('.display_package_list').show();
				}

				if(type == 'customer'){                
					$(t_parent).find('.display_customer').show();
					$(t_parent).find('.display_customer_list').show();
				}

				if(type == 'category'){                
					$(t_parent).find('.display_category').show();
					$(t_parent).find('.display_category_list').show();
				}
			});
		});

		function delete_type(target, id) {
			if(confirm("Are you sure delete promo type from coupon?")) {
				var p_base = $(target).parents('.panel.panel-default')[0];
				var t_base = $(p_base).find('input[name="type_related[]"]')[0];
				var t = t_base.value.split("|");
				if(t[1]){
					var type = t[1].split(",");
					var r = type.filter(function(element){
						return parseInt(element) != parseInt(id);
					});
					t[1] = r.join(",");
					t_base.value = t.join("|");
				}
				
				var p = $(target).parents('.gradeX')[0];
				$(p).remove();

				$('form[action="{{ url('/') }}/coupon/campingupdate/{{ ($camping['id'] ? $camping['id'] : 0) }}"]').submit();
			}
		}

		function ShrinkExpand(target){
			var p = $(target).parents('.panel.panel-default')[0];
			var t = $(p).find('.panel-body');
			var text = '';
			if($(t).is(":visible")){
				$(t).hide();
				text = 'Expand';
			}else{
				$(t).show();
				text = 'Shrink';
			}
			$(target).text(text);
		}

		function updateViewData(idx, x_target, i_target){
			var m_target = $(i_target).parents('.panel-title')[0];
			$(m_target).html('<i class="fa fa-pencil"></i> Coupon ' + (parseInt(idx) + 1) + '<input type="hidden" name="cp_idx[]" value="' + idx + '">');
			x_target.setAttribute('onclick', 'addcoupon(this, ' + (parseInt(idx) + 1) + ')');
		}

		function formatCoupon(){
			$('input[name="cp_idx[]"]').each(function(index, value){
				this.value = index;
				updateViewData(index, $('.btn.btn-primary[onclick^="addcoupon"]')[0], this);
			});
		}

		function duplicatecoupon(target){
			var html = $(target).parents('.panel.panel-default')[0].outerHTML;
			$(html).insertBefore('#submitgroup');
			var n_dp = $("#submitgroup").prev().find(".datepicker-field");
			n_dp.each(function(index, value){
				this.className = this.className.replace(/hasDatepicker/gm, '');
			});
			formatCoupon();
			$(".datepicker-field").datepicker({ dateFormat: "yy-mm-dd" }).val();
		}

		function deletecoupon(target){
			if(confirm("Are you sure delete coupon from camping?")) {
				var p_target = $(target).parents('.panel.panel-default')[0];
				$(p_target).remove();
				formatCoupon();
			}
		}

		function addcoupon(target, idx){
			var view = '{{ str_replace(['"', '\''], ['\\"', '\\\''], preg_replace('/([\t])/i', ' ', preg_replace('/(\n|[ \t]{2,})/i', '', View::make(Config::get('constants.ADMIN_FOLDER') . '.coupon_camping_editdata')->with('k', -1)->with('v', [])))) }}';
			$(view).insertBefore('#submitgroup');
			formatCoupon();
			$(".datepicker-field").datepicker({ dateFormat: "yy-mm-dd" }).val();
		}

		function selectData(event, target, type){
			event.preventDefault();
			var t_parent = $(target).parents('.form-group')[0];
			$(t_parent).addClass('select_data');
			if(type === 'seller') window.open("{{ asset('/') }}coupon/selectseller", "", "width=600, height=800, scrollbars");
			if(type === 'product') window.open("{{ asset('/') }}coupon/selectitem", "", "width=600, height=800, scrollbars");
			if(type === 'customer') window.open("{{ asset('/') }}coupon/selectcustomer", "", "width=600, height=800, scrollbars");
			if(type === 'package') window.open("{{ asset('/') }}coupon/selectpackage", "", "width=600, height=800, scrollbars");
			if(type === 'category') window.open("{{ asset('/') }}coupon/selectcategory", "", "width=600, height=800, scrollbars");
		}

		function getUserFromChild(id) {
			insertData(id);
		}
		function getUserFromChild2(id) {
			insertData(id);
		}
		function getUserFromChild3(id) {
			insertData(id);
		}
		function getUserFromChild4(id) {
			insertData(id);
		}
		function getUserFromChild5(id) {
			insertData(id);
		}

		function insertData(id){
			$('.form-group.select_data input').val(id);
			$('.form-group.select_data').removeClass('select_data');
		}
	</script>
	<div id="page-wrapper">
		<h1 class="page-header">Coupon Camping Management</h1>
		@if (Session::has('message') || Session::has('success'))
			<div class="alert alert-{{ Session::has('message') ? 'danger' : 'success' }}"><i class="fa fa-exclamation"></i> {{ Session::get(Session::has('message') ? 'message' : 'success') }}<button data-dismiss="alert" class="close" type="button">×</button></div>
		@endif

		{{ Form::open(['url' => 'coupon/campingupdate/' . ($camping['id'] ? $camping['id'] : 0), 'class' => 'form-horizontal']) }}
			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-pencil"></i> Coupon Camping</h3></div>

				<div class="panel-body">
					<div class="form-group">
						{{ Form::label('id', 'Camping ID', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-5"><p class="form-control-static">{{ $camping['id'] }}</p>{{ Form::input('hidden', 'id', $camping['id']) }}</div>
					</div>

					<div class="form-group">
						{{ Form::label('name', 'Name', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-3">{{ Form::text('name', $camping['name'], ['class'=> 'form-control']) }}</div>
					</div>

					<div class="form-group">
						{{ Form::label('status', 'Status', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-2">{{ Form::select('status', ['0' => 'Inactive', '1' => 'Active'], $camping['status'], ['class'=>'form-control']) }}</div>
					</div>

					<div class="form-group">
						{{ Form::label('start_at', 'Start Date', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-3">{{Form::text('start_at', $camping['start_at'], ['placeholder' => 'yyyy-mm-dd', 'class'=>'form-control datepicker-field'])}}</div>
					</div>

					<div class="form-group">
						{{ Form::label('end_at', 'End Date', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-3">{{ Form::text('end_at', $camping['end_at'], ['placeholder' => 'yyyy-mm-dd', 'class'=>'form-control datepicker-field'])}}</div>
					</div>

					<div class="form-group">
						{{ Form::label('format', 'Camping Format', ['class'=> 'col-lg-2 control-label']) }}
						<div class="col-lg-3">{{ Form::select('format', ['wheel_equal_2' => 'Spin Wheel: Display wheel follow avaliable coupon and display it twice', 'wheel_equal' => 'Spin Wheel: Display wheel follow avaliable coupon', 'wheel_accurate' => 'Spin Wheel: Display wheel follow actual remain coupon'], $camping['format'], ['class'=>'form-control'])}}</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading clearfix">
					@if (Permission::CheckAccessLevel(Session::get('role_id'), 12, 5, 'AND'))
						<div style="float: right;"><div class="btn btn-primary" onclick="addcoupon(this, {{ count($camping['coupon_data']) }})">Add Coupon</div></div>
					@endif
					<h3 class="panel-title" style="line-height: 36px;"><i class="fa fa-pencil"></i> Coupon</h3>
				</div>
				<div class="panel-body">
					@if(isset($camping['coupon_data']) && count($camping['coupon_data']) > 0)
						@foreach($camping['coupon_data'] as $k => $v)
							@include('admin.coupon_camping_editdata', ['k' => $k, 'v' => $v])
						@endforeach
					@endif

					<div class="form-group" id="submitgroup">
						<div class="col-lg-12">
							{{ Form::reset('Reset', ['class' => 'btn btn-default', 'data-toggle' => 'tooltip']) }}
							{{ Form::button('Save', ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'onclick' => 'submit()'])}} 
						</div>
					</div>
				</div>
			</div>
		{{ Form::close() }}
	</div>
@stop