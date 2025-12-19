	<style media="screen">
	.portfolio{
		user-select: auto;
		border: 1px solid;
		border-radius: 5px;
		border: none;
		box-shadow: 0px 0px 4px 0px;
		/* background: gold; */
		background-color: #f54707;
		background-image: linear-gradient(43deg, #93A5CF 15%, #E4EfE9 75%);
		/* background-image: linear-gradient(43deg, #A9F1DF 15%, #FFBBBB 75%); */
		/* background-image: linear-gradient(43deg, #FF564E 15%, #FAD126 75%, #00FF00 100%); */

		width: 25% !important;
		margin: 10px;
	}
	.portfolio>b{
		font-size: 25px;
	}
	table{
		font-size: 12px;
	}

	/* accordion */
	.panel-title .accordion-toggle:after {
		font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
		content: "\2212";    /* adjust as needed, taken from bootstrap.css */
		float: left;        /* adjust as needed */
		color: grey;         /* adjust as needed */
	}
	.panel-title .accordion-toggle.collapsed:after {
		content: "\002b";
	}

	/* .table-condensed thead tr:nth-child(2),
	.table-condensed tbody {
	display: none
	} */
	</style>
	@extends('layouts.master3')

	@section('title') Admin Goods Received Note GRN @stop

	@section('content')
	<div id="page-wrapper">

		<div class="row">
			<div class="col-lg-12">
				<h3 class="page-header"> Admin Goods Received Dashboard
					<span class="pull-right">
						<a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}admingrn/dashboard"><i class="fa fa-refresh"></i></a>
					</span>
				</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				@if (Session::has('success'))
					<div class="alert alert-success">
						<i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
					</div>
				@endif

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-list"></i> Admin Goods Received Note </h3>
					</div>

					<div class="panel-body">
						<div class="pull-right">
							<table>
								<tr>
									<td id="datetimepicker_from" style="display:none;">
										<input type="text" name="dates_from" id="grn_date_from" value="" class="form-control" autocomplete="off" placeholder="From" style="display:none;">
									</td>
									<td id="datetimepicker_to" style="display:none;">
										<input type="text" name="dates_to" id="grn_date_to" value="" class="form-control" autocomplete="off" placeholder="To" style="display:none;">
									</td>
									<td>
										<div class="input-group" >
											<input size="0" type="text" name="dates_from" id="grn_date_from" value="" class="form-control" autocomplete="off" style="display:none;">
											<input type="text" name="dates_to" id="grn_date_to" value="" class="form-control" autocomplete="off" style="display:none;">
											<div class="input-group-btn" id="prev_date">
												<button type="button" class="btn btn-default prev-day" type="submit"><i class="glyphicon glyphicon-arrow-left" style="height:20px;"></i></button>
											</div><!-- /btn-group -->
											<input size="30" disabled type="text" name="dates" id="grn_date" value="" class="form-control" autocomplete="off">
											<div class="input-group-btn" id="next_date">
												<button type="button" class="btn btn-default next-day" type="submit"><i class="glyphicon glyphicon-arrow-right" style="height:20px;"></i></button>
											</div><!-- /input-group -->
											<select class="form-control time_period" name="time_period" id="time_period">
												<option value="Daily">Daily</option>
												<option value="Weekly">Weekly</option>
												<option value="Monthly">Monthly</option>
												<option value="Custom">Custom</option>
											</select>
											<div class="input-group-btn">
												<button type="button" class="btn btn-default" id="filter" style="display:none;"><i class="glyphicon glyphicon-search" style="height:20px;"></i>Filter</button>
											</div>
										</div><!-- /input-group -->
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="panel-body text-center">
					<div class="box-con portfolio" style="display: inline-block;background-color: #5cb85c !important;color: white;padding: 10px;background-image: none !important;">
							<strong>No of GRN</strong><br>
							<b id="grn_total"></b>
							<!--<strong><p id="total_percentage" style="color:#ffffff !important;"></p></strong>-->
						</div>
						<div class="box-con portfolio" style="display: inline-block;background-color: #f0ad4e !important;color: white;padding: 15px;background-image: none !important;">
							<strong>No of Qty.</strong></br>
							<b id="grn_quantity"></b>
								<!--<strong><p id="quantity_percentage" style="color:#ffffff !important;;"></p></strong>-->
						</div>
						<div class="box-con portfolio" style="display: inline-block;background-color: #d9534f !important;color: white;padding: 10px; background-image: none !important;">
							<strong>Total GRN Amount</strong></br>
							<b id="grn_amount"></b>
								<!--<strong><p id="amount_percentage" style="color:#ffffff !important;;"></p></strong>-->
						</div>
							<div class="box-con portfolio" style="display: inline-block;background-color: #428bca;color: white;padding: 10px;background-image: none !important;">
							<strong>Completed Orders</strong></br>
							<b id="completed"></b>
						</div>
<div class="box-con portfolio" style="display: inline-block;background-color: #975d2b;color: white;padding: 10px;background-image: none !important;">							<strong>Partial Orders</strong></br>
							<b id="partial"></b>
							
						</div>
					</div>
					<hr>
					<div class="panel-body">
						<div class="col-md-12">
							<div class="dataTable_wrapper">
								<div class="table-responsive">
									<p style="margin-bottom: -23px;"><b> Product Details </b></p>
									<div class="pull-right" style="margin-bottom: -23px;">
										<button type="button" class="btn btn-success" data-toggle="modal" data-target="#exportModal"> Export</button>
									</div>
									<table class="table table-bordered table-striped table-hover" id="dataTables-daily">
										<thead>
											<tr>
											<th class="col-sm-1">GRN NO</th>
											<th class="col-sm-1">DATE</th>
											<th>SKU</th>
											<th>PRODUCT NAME</th>
											<th>LABEL</th>
											<th>UOM</th>
											<th>PRICE</th>
											<th>QUANTITY</th>
											<th>STATUS</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="panel-body">
						<div class="form-group" id="accordion_table">
							<div class="pull-right">
								<button type="button" class="btn btn-success" data-toggle="modal" data-target="#exportGrnDashboardModal"> Export</button>
							</div>
							<table class="table table-bordered table-striped table-hover">
								<thead style="background:steelblue;">
									<tr>
										<th style="width:80%;">GRN NO</th>
										<th>Company</th>
										<th>Quantity</th>
										<th>Amount</th>
									    <th>Status</th>
									</tr>
								</thead>
									<tbody id="accordion">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exportModalLabel">EXPORT</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="">
						<ul>
							<li>GRN NO</li>
							<li>SKU</li>
							<li>PRODUCT NAME</li>
							<li>LABEL</li>
							<li>UOM</li>
							<li>BASE UNIT</li>
							<li>PACKING FACTOR</li>
							<li>PRICE</li>
							<li>TOTAL QUANTITY</li>
							<li>TOTAL AMOUNT</li>
							<li>FOC</li>
							<li>FOC QUANTITY</li>
							<li>FOC UOM</li>
							<li>COMPANY NAME</li>
							<li>STATUS</li>
						</ul>
					</div>
					<div class="">
						<p>If your current view includes filters, the export will include the filtered records.</p>
					</div>
					<b>Format:</b>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="grn_export" id="pdf" value="pdf">
						<label class="form-check-label" for="pdf" style="font-weight: unset;">PDF</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="grn_export" id="excel" value="excel">
						<label class="form-check-label" for="excel" style="font-weight: unset;">Excel</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="javascript:excel_product_details()" class="btn btn-sm btn-danger">Export</button>
					<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="exportGrnDashboardModal" tabindex="-1" role="dialog" aria-labelledby="exportGrnDashboardModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exportGrnDashboardModalLabel">EXPORT</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="">
						<ul>
							<li>GRN NO</li>
							<li>SKU</li>
							<li>PRODUCT NAME</li>
							<li>LABEL</li>
							<li>UOM</li>
							<li>BASE UNIT</li>
							<li>PACKING FACTOR</li>
							<li>PRICE</li>
							<li>TOTAL QUANTITY</li>
							<li>TOTAL AMOUNT</li>
							<li>FOC</li>
							<li>FOC QUANTITY</li>
							<li>FOC UOM</li>
							<li>COMPANY NAME</li>
							<li>STATUS</li>
						</ul>
					</div>
					<div class="">
						<p>If your current view includes filters, the export will include the filtered records.</p>
					</div>
					<b>Format:</b>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="grn_dashboard_export" id="grn_dashboard_pdf" value="pdf">
						<label class="form-check-label" for="pdf" style="font-weight: unset;">PDF</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="grn_dashboard_export" id="grn_dashboard_excel" value="excel">
						<label class="form-check-label" for="excel" style="font-weight: unset;">Excel</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="javascript:export_grn_dashboard()" class="btn btn-sm btn-danger">Export</button>
					<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	@stop

	@section('script')
	
	$(document).ready(function(){
	daterange_picker();

	var start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
	var end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD');

	grn_count_percentange(start_date, end_date, null);

	product_details_datatable('Daily');

	add_accordion(start_date,end_date,'Daily');

	});

	$('#filter').on('click', function () {
		var time_period = $('#time_period').val();
		var start_date = $('#grn_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD');
		var end_date = $('#grn_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD');
		grn_count_percentange(start_date, end_date, time_period);
		console.log("Filter :",start_date,end_date,time_period);

		$('#dataTables-daily').DataTable().ajax.reload();

		add_accordion(start_date,end_date,time_period);
	});

	$('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
		var time_period = $('#time_period').val();
		var start_date = picker.startDate.format('YYYY-MM-DD');
		var end_date = picker.endDate.format('YYYY-MM-DD');
		grn_count_percentange(start_date, end_date, time_period);

		$('#dataTables-daily').DataTable().ajax.reload();

		add_accordion(start_date,end_date,time_period);

	});

	function daterange_picker(){
	$('input[name="dates"]').daterangepicker(
		{
			autoApply: true,
			singleDatePicker: true,
			showDropdowns: true,
			changeMonth: true,
			changeYear: true,
			locale: {
				format: 'DD MMM YYYY'
			},
			startDate: new Date(),
		}
	);
	{{-- 
	$('input[name="dates_from"]').daterangepicker(
		{
		autoApply: true,
		singleDatePicker: true,
		showDropdowns: true,
		changeMonth: true,
		changeYear: true,
		locale: {
			format: 'DD MMM YYYY'
		},
		startDate: new Date(),
		}
	);

	$('input[name="dates_to"]').daterangepicker(
		{
		autoApply: true,
		singleDatePicker: true,
		showDropdowns: true,
		changeMonth: true,
		changeYear: true,
		locale: {
			format: 'DD MMM YYYY'
		},
		startDate: new Date(),
		}
	); --}}
	}

	function grn_count_percentange(start_date, end_date, time_period){
	// console.log("DATA = ",start_date, end_date, time_period);
	$.ajax({
		url:'grn-data',
		type:'GET',
		data:{'start_date':start_date,'end_date':end_date,'time_period':time_period},
		dataType:'json',
		success:function(data){
		if(data['success']) {
			// console.log("COUNTTTTTTTTTT = ",data);
			$("#grn_total").text(data.grn_total);
			$("#grn_quantity").text(data.grn_quantity);
			$("#grn_amount").text(data.grn_amount);
			$("#completed").text(data.completed);
			$("#partial").text(data.partial);
			$("#grn_amount").prepend("RM");

			if(data.total_percentage > 0){
				$("#total_percentage").text('+'+data.total_percentage+'%');
				$("#total_percentage").css('color', 'white');
			} else{
				$("#total_percentage").text(data.total_percentage+'%');
				$("#total_percentage").css('color', 'white');
			}

			if(data.quantity_percentage > 0){
				$("#quantity_percentage").text('+'+data.quantity_percentage+'%');
				$("#quantity_percentage").css('color', 'white');
			} else{
				$("#quantity_percentage").text(data.quantity_percentage+'%');
				$("#quantity_percentage").css('color', 'white');
			}

			if(data.amount_percentage > 0){
				$("#amount_percentage").text('+'+data.amount_percentage+'%');
				$("#amount_percentage").css('color', 'white');
			} else{
				$("#amount_percentage").text(data.amount_percentage+'%');
				$("#amount_percentage").css('color', 'white');
			}

		}
		}
	});
	}

	function product_details_datatable(type){

	$('#dataTables-daily').dataTable({
		"bFilter": false,
		"paging": false,
		"info": false,
		"autoWidth" : false,
		"processing": true,
		"serverSide": true,
		"ajax": { "method":"GET","url": "{{ URL::to('admingrn/grn-report') }}",
			data: function ( d ) {
						d.time_period = $('#time_period').val();
						if (d.time_period == "Custom"){
							d.start_date = $('#grn_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD') ,
							d.end_date = $('#grn_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
							console.log("START_DATE_CUSTOM = " + d.start_date);
							console.log("END_DATE_CUSTOM = " + d.end_date);
							console.log("TIME_PERIOD_CUSTOM = " + d.time_period);
						} else {
							d.start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD') ,
							d.end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
							console.log("START_DATE = " + d.start_date);
							console.log("END_DATE = " + d.end_date);
							console.log("TIME_PERIOD = " + d.time_period);
						};
					}
		},
		"order" : [[0,'desc']],
		"columnDefs" : [{
			"targets" : "_all",
			"defaultContent" : ""
		}],
		"columns" : [
			{ "data" : "0" },
			{ "data" : "1" },
			{ "data" : "2" },
			{ "data" : "3" },
			{ "data" : "4" },
			{ "data" : "5" },
			{ "data" : "6" },
			{ "data" : "7" },
			{ "data" : "9" },
		]

	});
	}
	
	$('#time_period').on('change', function() {

		switch (this.value) {
		case 'Daily':
			$('#prev_date').show();
			$('#next_date').show();
			$('#grn_date').show();

			$('#datetimepicker_from').hide();
			$('#datetimepicker_to').hide();
			$('#grn_date_from').hide();
			$('#grn_date_to').hide();
			$('#filter').hide();
			
			$('input[name="dates"]').daterangepicker(
			{
				autoApply: true,
				singleDatePicker: true,
				showDropdowns: true,
				changeMonth: true,
				changeYear: true,
				locale: {
					format: 'DD MMM YYYY'
				},
				startDate: new Date(),
			})
			break;
		case 'Weekly':
			$('#prev_date').show();
			$('#next_date').show();
			$('#grn_date').show();

			$('#datetimepicker_from').hide();
			$('#datetimepicker_to').hide();
			$('#grn_date_from').hide();
			$('#grn_date_to').hide();
			$('#filter').hide();
			
			var end_date = new Date();
			var start_date = new Date();
			start_date = new Date(start_date.setDate(start_date.getDate() -7));
			set_daterangepicker(start_date, end_date);
			break;
		case 'Monthly':
			$('#prev_date').show();
			$('#next_date').show();
			$('#grn_date').show();

			$('#datetimepicker_from').hide();
			$('#datetimepicker_to').hide();
			$('#grn_date_from').hide();
			$('#grn_date_to').hide();
			$('#filter').hide();
			
			$('input[name="dates"]').daterangepicker(
			{
				autoUpdateInput:true,
				autoApply: true,
				singleDatePicker: true,
				showDropdowns: true,
				changeMonth: true,
				changeYear: true,
				alwaysShowCalendars: false,
				locale: {
					format: 'MMMM YYYY'
				},
			})
			break;
		case 'Custom':
			$('#prev_date').hide();
			$('#next_date').hide();
			$('#grn_date').hide();
			
			$('#datetimepicker_from').show();
			$('#datetimepicker_to').show();
			$('#grn_date_from').show();
			$('#grn_date_to').show();
			$('#filter').show();
			
			{{-- $('input[name="dates"]').daterangepicker(
			{
				autoApply: true,
				showDropdowns: true,
				changeMonth: true,
				changeYear: true,
				locale: {
				format: 'DD MMM YYYY'
				},
			}
			) --}}

			$('input[name="dates_from"]').daterangepicker(
				{
					autoApply: true,
					singleDatePicker: true,
					showDropdowns: true,
					changeMonth: true,
					changeYear: true,
					locale: {
					format: 'DD MMM YYYY'
					},
				}
			);

			$('input[name="dates_to"]').daterangepicker(
				{
					autoApply: true,
					singleDatePicker: true,
					showDropdowns: true,
					changeMonth: true,
					changeYear: true,
					locale: {
					format: 'DD MMM YYYY'
					},
				}
			);
			break;

		default:
			$('#prev_date').show();
			$('#next_date').show();
			$('input[name="dates"]').daterangepicker(
				{
					autoApply: true,
					singleDatePicker: true,
					showDropdowns: true,
					changeMonth: true,
					changeYear: true,
					locale: {
					format: 'DD MMM YYYY'
					},
					startDate: new Date(),
				}
			)
			break;
		}
		var time_period = $('#time_period').val();
		var start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
		var end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD');

		grn_count_percentange(start_date, end_date, time_period);

		$('#dataTables-daily').DataTable().ajax.reload();

		add_accordion(start_date,end_date,time_period);
	});

	$('.prev-day').on('click', function () {
			set_date_range('prev');

			var time_period = $('#time_period').val();
			var start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
			var end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
			grn_count_percentange(start_date, end_date, time_period);

			$('#dataTables-daily').DataTable().ajax.reload();
			add_accordion(start_date,end_date,time_period);
	});

	$('.next-day').on('click', function () {
			set_date_range('next');

			var time_period = $('#time_period').val();
			var start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
			var end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
			grn_count_percentange(start_date, end_date, time_period);

			$('#dataTables-daily').DataTable().ajax.reload();
			add_accordion(start_date,end_date,time_period);
	});

	function set_date_range(button_clicked){
		var time_period = $('#time_period').val();

		var start_date = $('#grn_date').data('daterangepicker').startDate._d;
		var end_date = $('#grn_date').data('daterangepicker').endDate._d;

		switch (time_period) {
		case 'Daily':
			if(button_clicked == 'prev'){
				start_date.setDate(start_date.getDate() -1);
			}
			else{
				start_date.setDate(start_date.getDate() +1);
			}
			set_datepicker(start_date);
			break;
		case 'Weekly':
			if(button_clicked == 'prev'){
				start_date.setDate(start_date.getDate() -7);
				end_date.setDate(end_date.getDate() -7);
			}
			else{
				start_date.setDate(start_date.getDate() +7);
				end_date.setDate(end_date.getDate() +7);
			}

			set_daterangepicker(start_date,end_date);
			break;
		case 'Monthly':
			if(button_clicked == 'prev'){
				start_date.setMonth(start_date.getMonth() -1);
			}
			else{
				start_date.setMonth(start_date.getMonth() +1);
			}
			set_dmonthpicker(start_date);
			break;
		case 'Custom':
			var start_date = $('#grn_date').data('daterangepicker').startDate._d;
			var end_date = $('#grn_date').data('daterangepicker').endDate._d;
			const diffTime = Math.abs(end_date - start_date);
			const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
			if(button_clicked == 'prev'){
				start_date.setDate(start_date.getDate() - diffDays);
				end_date.setDate(end_date.getDate() - diffDays);
			}
			else{
				start_date.setDate(start_date.getDate() + diffDays);
				end_date.setDate(end_date.getDate() + diffDays);
			}

			set_daterangepicker(start_date,end_date);
			break;

		default:

			break;
		}
	}

	function set_daterangepicker(start_date,end_date){
		$('input[name="dates"]').daterangepicker(
			{
				autoApply: true,
				locale: {
				format: 'DD MMM YYYY'
				},
				startDate: start_date,
				endDate: end_date
			}
		)
	}

	function set_datepicker(start_date){
		$('input[name="dates"]').daterangepicker(
			{
				autoApply: true,
				singleDatePicker: true,
				showDropdowns: true,
				changeMonth: true,
				changeYear: true,
				locale: {
				format: 'DD MMM YYYY'
				},
				startDate: start_date,
			}
		)
	}
	function set_dmonthpicker(start_date){
		$('input[name="dates"]').daterangepicker(
			{
				autoUpdateInput:true,
				alwaysShowCalendars: false,
				autoApply: true,
				singleDatePicker: true,
				showDropdowns: true,
				changeMonth: true,
				changeYear: true,
				locale: {
				format: 'MMMM YYYY'
				},
				startDate: start_date,
			}
		)
	}


	@stop
	<script type="text/javascript">
	function excel_product_details(){
		var url;
		var grn_export = $('input[name="grn_export"]:checked').val();
		if(grn_export === undefined){
			alert("Please select the format of export.");
			return false;
		}
		var query;
		var	time_period_excel = $('#time_period').val();
		console.log("TIME_PERIOD_EXPORT = " + time_period_excel);

		if (time_period_excel == "Custom"){
			query = {
				start_date: $('#grn_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: grn_export,
			}
		} else {
			query = {
				start_date: $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: grn_export,
			}
		}

		if(grn_export == 'excel'){
			url = "{{URL::to('admingrn/downloadexcel/xls')}}?" + $.param(query);
		}
		else {
			url = "{{URL::to('admingrn/exportpdf')}}?" + $.param(query);
		}
		window.location = url;

		$("#exportModal").modal('toggle');
	}

	function export_grn_dashboard(){
		var url;
		var grn_export = $('input[name="grn_dashboard_export"]:checked').val();
		if(grn_export === undefined){
			alert("Please select the format of export.");
			return false;
		}
		var query;
		var	time_period_excel = $('#time_period').val();
		console.log("TIME_PERIOD_EXPORT = " + time_period_excel);

		if (time_period_excel == "Custom"){
			query = {
				start_date: $('#grn_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: grn_export,
			}
		} else {
			query = {
				start_date: $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: grn_export,
			}
		}

		if(grn_export == 'excel'){
			url = "{{URL::to('admingrn/downloadgrndashexcel/xls')}}?" + $.param(query);
		}
		else {
			url = "{{URL::to('admingrn/exportgrndashpdf')}}?" + $.param(query);
		}
		window.location = url;

		$("#exportGrnDashboardModal").modal('toggle');
	}

	function add_accordion(start_date,end_date,time_period){
		// console.log("TIME PERIOD : ",time_period);
		// console.log("START : ",start_date);
		// console.log("END : ",end_date);

		$.ajax({
			url:'grn-dashboard-data',
			type:'GET',
			data:{'start_date':start_date,'end_date':end_date,'time_period':time_period},
			dataType:'json',
			success:function(grns){
				load_accordion(grns);
			}
		});
	}

	function load_accordion(grns){
		document.getElementById("accordion").innerHTML = "";
		for (const property in grns) {
		
            if(grns[property]['status']=='1'){var sts="Completed";}else if(grns[property]['status']=='0'){var sts="Pending"}else{var sts="partial"} 
			var div = document.createElement('tr');
			div.className = '';
			div.innerHTML =
				`
					<tr>
						<td>
							<h4 class="panel-title">
								<a href="#/" class="accordion-toggle collapsed" data-target="#collapsedata`+grns[property]['id']+`" data-toggle="collapse" data-parent="#accordion"><span class="glyphicon"></span>
								`+grns[property]['grn_no']+`
								</a>
							</h4>
							<div class="panel-collapse collapse" id="collapsedata`+grns[property]['id']+`">
								<div class="panel-body">
									<table class="table table-bordered table-striped table-hover">
										<thead>
											<tr>
												<th>SKU</th>
												<th>PRODUCT NAME</th>
												<th>LABEL</th>
												<th>UOM</th>
												<th>PRICE</th>
												<th>QUANTITY</th>
												<th>TOTAL AMOUNT</th>
												<th>STATUS</th>
											</tr>
										</thead>
										<tbody id="child_accordion`+grns[property]['id']+`">

										</tbody>
									</table>
								</div>
							</div>
						</td>
						<td>`+grns[property]['company_name']+`</td>
						<td>`+grns[property]['grn_quantity']+`</td>
						<td>`+grns[property]['grn_amount']+`</td>
						<td>`+sts+`</td>
					</tr>
				`;
			document.getElementById('accordion').appendChild(div);
			append_subchild(grns[property]['id'], grns[property]['grn_no']);
		}

		function append_subchild(id,grn_no){
			$.ajax({
				url:'grn-dashboard-child-data',
				type:'GET',
				data:{'grn_no':grn_no,'id':id},
				dataType:'json',
				success:function(grn_details){
				load_child_accordion(grn_details,id);
				}
			});
		}
			
		function load_child_accordion(grn_details,id){

			for (const property in grn_details) {
			 if(grn_details[property]['status']=='1'){var sts="Completed";}else if(grns[property]['status']=='0'){var sts="Pending"}else{var sts="partial"} 
				var div = document.createElement('tr');
				div.className = '';
				div.innerHTML =
					`
						<tr>
							<td>`+grn_details[property]['sku']+`</td>
							<td>`+grn_details[property]['product_name']+`</td>
							<td>`+grn_details[property]['price_label']+`</td>
							<td>`+grn_details[property]['uom']+`</td>
							<td>`+grn_details[property]['price']+`</td>
							<td>`+grn_details[property]['quantity']+`</td>
							<td>`+grn_details[property]['total']+`</td>
							<td>`+sts+`</td>
						</tr>
					`;
				document.getElementById('child_accordion'+grn_details[property]['id']).appendChild(div);
			}

		}

	}

	</script>
