
	<style media="screen">
	.portfolio{
		/* user-select: auto; */
		border: 1px solid;
		border-radius: 5px;
		border: none;
		box-shadow: 0px 0px 4px 0px;
		/* background: gold; */
		background-color: #f54707;
		background-image: linear-gradient(43deg, #93A5CF 15%, #E4EfE9 75%);
		/* background-image: linear-gradient(43deg, #A9F1DF 15%, #FFBBBB 75%); */
		/* background-image: linear-gradient(43deg, #FF564E 15%, #FAD126 75%, #00FF00 100%); */

		width: 20% !important;
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

	.center {
        text-align: center;
        /* border: 1px solid #000; */
	}
	/* .table-condensed thead tr:nth-child(2),
	.table-condensed tbody {
	display: none
	} */
	#load{
    width:100%;
    height:100%;
    position:fixed;
    z-index:9999;
    background-color:white;
    background:url("https://uat.all.jocom.com.my/img/spdfload.gif") no-repeat center center rgba(0,0,0,0.25)
}
.loader {
    background-color: rgb(54 50 50 / 95%);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 1000;
    margin-top: 0px;
    top: 0px;
}
.loader-centered {
    position: absolute;
    left: 50%;
    top: 50%;
    height: 200px;
    width: 200px;
    margin-top: -100px;
    margin-left: -132px;
}
.object {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0);
    margin-right: auto;
    margin-left: auto;
    border: 4px solid #fff;
    left: 73px;
    top: 73px;
    position: absolute;
}

.square-one {
    -webkit-animation: first_object_animate 1s infinite ease-in-out;
    animation: first_object_animate 1s infinite ease-in-out;
}
.square-two {
    -webkit-animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
    animation: second_object 1s forwards, second_object_animate 1s infinite ease-in-out;
}
.square-three {
    -webkit-animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
    animation: third_object 1s forwards, third_object_animate 1s infinite ease-in-out;
}

@-webkit-keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@keyframes second_object {
    100% {
        width: 100px;
        height: 100px;
        left: 48px;
        top: 48px;
    }
}
@-webkit-keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}
@keyframes third_object {
    100% {
        width: 150px;
        height: 150px;
        left: 23px;
        top: 23px;
    }
}

@-webkit-keyframes first_object_animate {
    0% {
        -webkit-transform: perspective(100px);
    }
    50% {
        -webkit-transform: perspective(100px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(100px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes first_object_animate {
    0% {
        transform: perspective(100px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(100px) rotateX(-180deg) rotateY(-180deg);
    }
}

@-webkit-keyframes second_object_animate {
    0% {
        -webkit-transform: perspective(200px);
    }
    50% {
        -webkit-transform: perspective(200px) rotateY(180deg);
    }
    100% {
        -webkit-transform: perspective(200px) rotateY(180deg) rotateX(180deg);
    }
}

@keyframes second_object_animate {
    0% {
        transform: perspective(200px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(200px) rotateX(180deg) rotateY(0deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(200px) rotateX(180deg) rotateY(180deg);
        -webkit-transform: perspective(200px) rotateX(180deg) rotateY(180deg);
    }
}

@-webkit-keyframes third_object_animate {
    0% {
        -webkit-transform: perspective(300px);
    }
    50% {
        -webkit-transform: perspective(300px) rotateY(-180deg);
    }
    100% {
        -webkit-transform: perspective(300px) rotateY(-180deg) rotateX(-180deg);
    }
}

@keyframes third_object_animate {
    0% {
        transform: perspective(300px) rotateX(0deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(0deg) rotateY(0deg);
    }
    50% {
        transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(0deg);
    }
    100% {
        transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
        -webkit-transform: perspective(300px) rotateX(-180deg) rotateY(-180deg);
    }
}
	</style>
	
	@extends('layouts.master3')
    

	@section('title') Purchase Order Dashboard @stop

	@section('content')
<div class="loader" style="display: none;">

    <div class="loader-centered">
    <h1 style="color:white;margin-top: 200px !important;width: 127%;">Please Wait!!!</h1>

        <div class="object square-one"></div>
        <div class="object square-two"></div>
        <div class="object square-three"></div>
    </div>
</div>	<div id="page-wrapper">
    
		<div class="row">
			<div class="col-lg-12">
				<h3 class="page-header">Purchase Order Dashboard <span class="pull-right">
					<a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}po"><i class="fa fa-refresh"></i></a>
					@if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
						<a class="btn btn-primary" title="" data-toggle="tooltip" href="/po/create"><i class="fa fa-plus"></i></a>
					@endif
					
				</span></h3>
			</div>
		</div>
    
		<div class="row">
			<div class="col-lg-12">
				@if (Session::has('success'))
					<div class="alert alert-success">
						<i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
					</div>
				@endif
				@if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-list"></i> Purchase Order Note  </h3>
					</div>
					<div class="panel-body">
						<div class="pull-right">
							<table>
								<tr>
									<td id="datetimepicker_from" style="display:none;">
										<input type="text" name="dates_from" id="po_date_from" value="" class="form-control" autocomplete="off" placeholder="From" style="display:none;">
									</td>
									<td id="datetimepicker_to" style="display:none;">
										<input type="text" name="dates_to" id="po_date_to" value="" class="form-control" autocomplete="off" placeholder="To" style="display:none;">
									</td>
									<td>
										<div class="input-group" >
											<input size="0" type="text" name="dates_from" id="po_date_from" value="" class="form-control" autocomplete="off" style="display:none;">
											<input type="text" name="dates_to" id="po_date_to" value="" class="form-control" autocomplete="off" style="display:none;">
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
					 <div><div class="pull-right" style="margin-bottom: -18px;margin-right:15px;">
									<button type="button" class="btn btn-success" onclick="javascript:spdf_download()">Export Signed PDF</button>
								</div><br></div>
					<div class="panel-body text-center">
						
						{{-- <div class="col-md-12"> --}}
							
						<div class="box-con portfolio" style="display: inline-block;background-color: #f0ad4e !important;color: white;padding: 15px;background-image: none !important;">
							
							<br>&nbsp; 
							<br>&nbsp;
							<strong>Total Seller</strong></br>
							<b id="seller_total"></b> 
							<br>&nbsp; 
							<br>&nbsp; 
							<br>&nbsp; 


<!-- 						<strong><p id="seller_percentage" sty></p></strong>
 -->	
							</div>
							<div class="box-con portfolio" style="display: inline-block;background-color: #5cb85c !important;color: white;padding: 10px;background-image: none !important;">
								<strong>No of PO</strong><br>
								<b id="po_total"></b>
								<br>
								<strong>No of Qty.</strong></br>
								<b id="po_quantity"></b><br>
								<strong>Total PO Amount</strong></br>
								<b id="po_amount"></b>
								<!--<strong><p id="total_percentage"></p></strong>-->
							</div>
							<div class="box-con portfolio" style="display: inline-block;background-color: #d9534f !important;color: white;padding: 10px; background-image: none !important;">
								<strong>No of Cancelled PO</strong><br>
								<b id="po_status_cancelled"></b>
								<br>
								<strong>No of Cancelled Qty</strong></br>
								<b id="po_cancel_quantity"></b><br>
								<strong>Cancelled PO Amount</strong></br>
								<b id="po_cancel_amount"></b>
								<!--<strong><p id="total_percentage"></p></strong>-->
							</div>
							<div class="box-con portfolio" style="display: inline-block;background-color: #428bca;color: white;padding: 10px;background-image: none !important;">
								<strong>No of Revised PO</strong><br>
								<b id="po_revised"></b>
								<br>
								<strong>No of Revised Qty</strong></br>
								<b id="po_revised_quantity"></b><br>
								<strong>Revised PO Amount</strong></br>
								<b id="po_revised_amount"></b>
								<!--<strong><p id="total_percentage"></p></strong>-->
							</div>
							<div class="box-con portfolio" style="display:none">
								<strong>No of Mistake PO</strong><br>
								<b id="po_mistake"></b>
								<br>
								<strong>No of Mistake Qty</strong></br>
								<b id="po_mistake_quantity"></b><br>
								<strong>Mistake PO Amount</strong></br>
								<b id="po_mistake_amount"></b>
							</div>
							
							
						{{-- </div> --}}
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
										<th class="col-sm-1">PO NO</th>
										<th class="col-sm-1">DATE</th>
										<th>SELLER</th>
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
									<th style="width:30%;">PO NO</th>
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
							<li>PO NO</li>
							<li>SELLER NAME</li>
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
							<li>STATUS</li>
						</ul>
					</div>
					<div class="">
						<p>If your current view includes filters, the export will include the filtered records.</p>
					</div>
					<b>Format:</b>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="po_export" id="pdf" value="pdf">
						<label class="form-check-label" for="pdf" style="font-weight: unset;">PDF</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="po_export" id="excel" value="excel">
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
							<li>PO NO</li>
							<li>SELLER NAME</li>
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
						</ul>
					</div>
					<div class="">
						<p>If your current view includes filters, the export will include the filtered records.</p>
					</div>
					<b>Format:</b>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="po_dashboard_export" id="grn_dashboard_pdf" value="pdf">
						<label class="form-check-label" for="pdf" style="font-weight: unset;">PDF</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" name="po_dashboard_export" id="grn_dashboard_excel" value="excel">
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
	
	{{-- Whane page is open --}}
	$(document).ready(function(){
		daterange_picker();

		var start_date = $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
		var end_date = $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD');

		grn_count_percentange(start_date, end_date, null);

		product_details_datatable('Daily');

		add_accordion(start_date,end_date,'Daily');

	});

	{{-- For custom date. When button filter is clicked.  --}}
	$('#filter').on('click', function () { 
		var time_period = $('#time_period').val();
		var start_date = $('#po_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD');
		var end_date = $('#po_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD');
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
	}

	function grn_count_percentange(start_date, end_date, time_period){
		// console.log("DATA = ",start_date, end_date, time_period);
		$.ajax({
			url:'po-data',
			type:'GET',
			data:{'start_date':start_date,'end_date':end_date,'time_period':time_period},
			dataType:'json',
			success:function(data){
				if(data['success']) {
					// console.log("COUNTTTTTTTTTT = ",data);
					$("#po_total").text(data.po_total);
					$("#seller_total").text(data.seller_total);
					$("#po_quantity").text(data.po_quantity);
					$("#po_amount").text(data.po_amount);
					$("#po_status_cancelled").text(data.po_cancelled);
                    $("#po_revised").text(data.po_revised);
                    $("#po_mistake").text(data.po_mistake);
                    $("#po_cancel_quantity").text(data.po_cancelled_raw_quantity);
                    $("#po_cancel_amount").text(data.po_cancelled_raw_amount);
                    $("#po_revised_quantity").text(data.po_revised_raw_quantity);
                    $("#po_revised_amount").text(data.po_revised_raw_amount);
                     $("#po_mistake_quantity").text(data.po_mistake_raw_quantity);
                    $("#po_mistake_amount").text(data.po_mistake_raw_amount);



					$("#po_amount").prepend("RM");
					$("#po_cancel_amount").prepend("RM");
					$("#po_revised_amount").prepend("RM");
					$("#po_mistake_amount").prepend("RM");

					if(data.total_percentage > 0){
						$("#total_percentage").text('+'+data.total_percentage+'%');
						$("#total_percentage").css('color', 'green');
					} else{
						$("#total_percentage").text(data.total_percentage+'%');
						$("#total_percentage").css('color', 'red');
					}

					if(data.quantity_percentage > 0){
						$("#quantity_percentage").text('+'+data.quantity_percentage+'%');
						$("#quantity_percentage").css('color', 'green');
					} else{
						$("#quantity_percentage").text(data.quantity_percentage+'%');
						$("#quantity_percentage").css('color', 'red');
					}

					if(data.amount_percentage > 0){
						$("#amount_percentage").text('+'+data.amount_percentage+'%');
						$("#amount_percentage").css('color', 'green');
					} else{
						$("#amount_percentage").text(data.amount_percentage+'%');
						$("#amount_percentage").css('color', 'red');
					}

					if(data.seller_percentage > 0){
						$("#seller_percentage").text('+'+data.seller_percentage+'%');
						$("#seller_percentage").css('color', 'green');
					} else{
						$("#seller_percentage").text(data.seller_percentage+'%');
						$("#seller_percentage").css('color', 'red');
					}

				}
			}
		});
	}

	function product_details_datatable(type){
	console.log("M A S U K 1 !");
		$('#dataTables-daily').dataTable({
			"bFilter": false,
			"paging": false,
			"info": false,
			"autoWidth" : false,
			"processing": true,
			"serverSide": true,
			"ajax": { "method":"GET","url": "{{ URL::to('purchase-order/po-report') }}",
				data: function ( d ) {
							d.time_period = $('#time_period').val();
							if (d.time_period == "Custom"){
								d.start_date = $('#po_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD') ,
								d.end_date = $('#po_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
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
			    { "data" : "8" },


			],
        columnDefs : [
        { targets : [9],
          render : function (data, type, row) {
            switch(data) {
               case '1' : return 'active'; break;
               case '2' : return '<b style="color:red">cancelled<b style="color:red">'; break;
               case '4' : return '<b style="color:#428bca">revised</b>'; break;
               default  : return 'active';
            }
          }
        }
   ],

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
			$('#po_date_from').hide();
			$('#po_date_to').hide();
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
			$('#po_date_from').hide();
			$('#po_date_to').hide();
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
			$('#po_date_from').hide();
			$('#po_date_to').hide();
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
			$('#po_date_from').show();
			$('#po_date_to').show();
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
		var po_export = $('input[name="po_export"]:checked').val();
		if(po_export === undefined){
			alert("Please select the format of export.");
			return false;
		}
		var query;
		var	time_period_excel = $('#time_period').val();
		console.log("TIME_PERIOD_EXPORT = " + time_period_excel);

		if (time_period_excel == "Custom"){
			query = {
				start_date: $('#po_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#po_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		} else {
			query = {
				start_date: $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		}

	if(po_export == 'excel'){
			urls = "{{URL::to('purchase-order/download-excel/xls')}}?" + $.param(query);
		}
		else {
			urls = "{{URL::to('purchase-order/download-pdf')}}?" + $.param(query);
		}
		
		loader(urls);

		$("#exportModal").modal('toggle');
	}
	function spdf_download(){
		var url;
		var po_export ='spdf';
		var query;
		var	time_period_excel = $('#time_period').val();
		console.log("TIME_PERIOD_EXPORT = " + time_period_excel);

		if (time_period_excel == "Custom"){
			query = {
				start_date: $('#po_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#po_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		} else {
			query = {
				start_date: $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		}

		
		if(po_export == 'spdf'){
			  
			urls = "{{URL::to('purchase-order/spdf-download')}}?" + $.param(query);
			
		}
		 loader(urls);
	}

	function export_grn_dashboard(){
		var url;
		var po_export = $('input[name="po_dashboard_export"]:checked').val();
		if(po_export === undefined){
			alert("Please select the format of export.");
			return false;
		}
		var query;
		var	time_period_excel = $('#time_period').val();
		console.log("TIME_PERIOD_EXPORT = " + time_period_excel);

		if (time_period_excel == "Custom"){
			query = {
				start_date: $('#po_date_from').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#po_date_to').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		} else {
			query = {
				start_date: $('#grn_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
				end_date: $('#grn_date').data('daterangepicker').endDate.format('YYYY-MM-DD'),
				time_period: $('#time_period').val(),
				export_type: po_export,
			}
		}

		if(po_export == 'excel'){
			urls = "{{URL::to('purchase-order/download-po-dash-excel/xls')}}?" + $.param(query);
		}
		else {
			urls = "{{URL::to('purchase-order/download-po-dash-pdf')}}?" + $.param(query);
		}
		loader(urls);

		$("#exportGrnDashboardModal").modal('toggle');
	}

	function add_accordion(start_date,end_date,time_period){
		// console.log("TIME PERIOD : ",time_period);
		// console.log("START : ",start_date);
		// console.log("END : ",end_date);

		$.ajax({
			url:'po-dashboard-data',
			type:'GET',
			data:{'start_date':start_date,'end_date':end_date,'time_period':time_period},
			dataType:'json',
			success:function(pos){
				load_accordion(pos);
			}
		});
	}

	function load_accordion(pos){
		document.getElementById("accordion").innerHTML = "";
		var status="";
		for (const property in pos) {
           if(pos[property]['po_status']=='1'){
           	status="Active";
           }
          if(pos[property]['po_status']=='4')
          {
            status="<b style='color:#428bca'>Revised</b>";
          }
          if(pos[property]['po_status']=='2')
          {
            status="<b style='color:red'>Cancelled</b>";
          }
			var div = document.createElement('tr');
			div.className = '';
			div.innerHTML =
				`
					<tr>
					<td>
						<h4 class="panel-title">
						<a href="#/" class="accordion-toggle collapsed" data-target="#collapsedata`+pos[property]['id']+`" data-toggle="collapse" data-parent="#accordion"><span class="glyphicon"></span>
						`+pos[property]['po_no']+`
						</a>
						</h4>
						
					</td>
					<td>`+pos[property]['company_name']+`</td>
					<td>`+pos[property]['po_quantity']+`</td>
					<td>`+pos[property]['po_amount']+`</td>
					<td>`+status+`</td>
					</tr>
				`;
				var div2 = document.createElement('div');
			div2.className = '';
			div2.innerHTML =
				`
					
						
						<div class="panel-collapse collapse" id="collapsedata`+pos[property]['id']+`" style="width:195%">
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
								</tr>
							</thead>
							<tbody id="child_accordion`+pos[property]['id']+`">

							</tbody>
						</table>
						</div>
						</div>
				`;

			document.getElementById('accordion').appendChild(div);
						document.getElementById('accordion').appendChild(div2);

			append_subchild(pos[property]['id'], pos[property]['po_no']);
		}

		function append_subchild(id,po_no){
			$.ajax({
				url:'po-dashboard-child-data',
				type:'GET',
				data:{'po_no':po_no,'id':id},
				dataType:'json',
				success:function(po_details){
					load_child_accordion(po_details,id);
				}
			});
		}
			
		function load_child_accordion(po_details,id){

			for (const property in po_details) {
				var div = document.createElement('tr');
				div.className = '';
				div.innerHTML =
					`
						<tr>
							<td>`+po_details[property]['sku']+`</td>
							<td>`+po_details[property]['product_name']+`</td>
							<td>`+po_details[property]['price_label']+`</td>
							<td>`+po_details[property]['uom']+`</td>
							<td>`+po_details[property]['price']+`</td>
							<td>`+po_details[property]['quantity']+`</td>
							<td>`+po_details[property]['total']+`</td>
						</tr>
					`;
				document.getElementById('child_accordion'+po_details[property]['id']).appendChild(div);
			}

		}

	}
   function loader(urls){
		$.ajax({
  url: urls,
  beforeSend: function() {
  	window.location = urls;
   $(".loader").show();  
  },          
  complete: function() {
  $(".loader").hide();
  }
 
});
	}
	</script>
	
