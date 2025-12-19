@extends('layouts.master')
@section('title') Warehouse @stop
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>
<style type="text/css">
	/* END SORTABLETABLE STYLES */
/* BEGIN SPARKLINE STYLES */
.sparkline {
  min-width: 50px;
  border-right: 1px solid #DCDCDC;
  box-shadow: 1px 0 0 0 #FFFFFF;
  float: left;
  margin-right: 12px;
  padding: 10px 14px 0px 4px;
  line-height: 52px;
}
/* END SPARKLINE STYLES */
.stats_box {
  display: inline-block;
  list-style: none outside none;
  margin-left: 0;
  margin-top: 20px;
  padding: 0;
}
.stats_box li {
  background: #EEEEEE;
  box-shadow: 0 0 0 1px #F8F8F8 inset, 0 0 0 1px #CCCCCC;
  display: inline-block;
  line-height: 18px;
  margin: 0 10px 10px;
  padding: 0 10px;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
  float: left;
}
.stats_box .stat_text {
  float: left;
  font-size: 12px;
  padding: 9px 10px 7px 0;
  text-align: left;
  min-width: 150px;
  position: relative;
}
.stats_box .stat_text strong {
  display: block;
  font-size: 16px;
}
.stats_box .stat_text .percent {
  color: #444;
  float: right;
  font-size: 20px;
  font-weight: bold;
  position: absolute;
  right: 0;
  top: 17px;
}
.stats_box .stat_text .percent.up {
  color: #46a546;
}
.stats_box .stat_text .percent.down {
  color: #C52F61;
}
::-webkit-scrollbar {
  width: 12px;
  height: 12px;
}
::-webkit-scrollbar-thumb {
  border-radius: 1em;
}
::-webkit-scrollbar-thumb:hover {
  background-color: #999;
}
::-webkit-scrollbar-track {
  border-radius: 1em;
  background: transparent;
}
::-webkit-scrollbar-track:hover {
  background: rgba(110, 110, 110, 0.25);
}

input[readonly].lockinput{
  background-color:transparent;
  border: 0;
  font-size: 1em;
}

/* BEGIN FULLCALENDAR STYLES */

</style>
<div id="page-wrapper">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Warehouse Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}warehouse"><i class="fa fa-refresh"></i></a>
                    
                </span>
            </h1>
        </div>
	</div>
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
                    <h2 class="panel-title">Warehouse Management</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">

                    	<div class="text-center">
						    <ul class="stats_box">
							<li><a class="quick-btn" href="{{asset('/')}}warehouse/linkproduct">
							    <div class="sparkline"><i class="fa fa-link fa-2x" aria-hidden="true"></i></div>
							    <div class="stat_text">
								<strong>Link <br> Product</strong>
								<!-- <span class="percent down"> <i class="fa fa-caret-down"></i> -16%</span> -->
							    </div>
							    </a>
							</li>
							<li>
								<a class="quick-btn" href="#">
							    <div class="sparkline"><i class="fa fa-bars fa-2x" aria-hidden="true"></i></div>
							    <div class="stat_text">
								<strong>Reordering <br> Rules</strong>
								<!-- <span class="percent down"> <i class="fa fa-caret-down"></i> -16%</span> -->
							    </div>
							    </a>
							</li>
							<li>
								<a class="quick-btn" href="{{asset('/')}}warehouse/invadjustments">
							    <div class="sparkline"><i class="fa fa-adjust fa-2x" aria-hidden="true"></i></div>
							    <div class="stat_text">
								<strong>Inventory <br> Adjustments</strong>
								<!-- <span class="percent down"> <i class="fa fa-caret-down"></i> -16%</span> -->
							    </div>
							    </a>
							</li>
							<li>
								<a class="quick-btn" href="{{asset('/')}}warehouse/generalreport">
							    <div class="sparkline"><i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i></div>
							    <div class="stat_text">
								<strong>Export Stock <br>Report to Excel</strong>
								<!-- <span class="percent down"> <i class="fa fa-caret-down"></i> -16%</span> -->
							    </div>
							    </a>
							</li>
							<!-- <li>
							    <div class="sparkline line_day"></div>
							    <div class="stat_text">
								<strong>165</strong>Daily Visit
								<span class="percent up"> <i class="fa fa-caret-up"></i> +23%</span>
							    </div>
							</li> -->
							
						    </ul>
						</div>
                      


                    </div>  
                </div>

                


            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Inventory Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-products">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">Product ID</th>
                                    <th class="col-sm-1">SKU</th>
                                    <th class="col-sm-2">Product Name</th>
                                    <th class="col-sm-2">Label</th>
                                    <th class="col-sm-1 text-center">Stock in Hand</th>
                                    <th class="col-sm-1 text-center">Reserved Stock</th>
                                    <th class="col-sm-1 text-center">UOM</th>
                                    <th class="col-sm-1 text-center">Link(s) ID</th>
                                    <th class="col-sm-1 text-center">Root Base ID</th>
                                    <th class="col-sm-1 text-center">Stock in</th>
                                    <th class="col-sm-1 text-center">Stock Out</th>
                                    <th class="col-sm-1 text-center">Stock Return</th>
                                    <!-- <th class="col-sm-1 text-center">Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>

        </div>
    </div>


    <div class="modal fade" id="addStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Stock In</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('stn_product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('product_value', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('unit')) has-error @endif">
                            {{ Form::label('unit', 'Quantity', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('unit', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit')}}</p>
                                </div>
                            </div>

                            <!-- <div class="form-group @if ($errors->has('batchno')) has-error @endif">
                            {{ Form::label('batchno', 'Lot/Batch No', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('batchno', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('batchno')}}</p>
                                </div>
                            </div> -->


                            <div class="form-group">
                                {{ Form::label('valid_to', 'Expiry Date', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('valid_to', null, array('id'=>'datepicker','placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
                                </div>
                            </div>


                            <div class="form-group @if ($errors->has('unit_price')) has-error @endif">
                            {{ Form::label('unit_price', 'Cost Price', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('unit_price', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit_price')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('total_amount')) has-error @endif">
                            {{ Form::label('total_amount', "Total Amount ($currency)", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('total_amount', null, array('class'=>'form-control lockinput'))}}
                                    <p class="help-block" for="inputError">{{$errors->first('total_amount')}}</p>
                                </div>
                                
                            </div>
                            <div class="form-group @if ($errors->has('total_amount')) has-error @endif">
                            {{ Form::label('stockin_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('stockin_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('total_amount')}}</p>
                                </div>
                                
                            </div>
                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="submit" id="savestockin"  class="btn btn-primary pull-left">Save</button>
                                {{Form::input('hidden', 'stockin_productid', '')}}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="outStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Stock Out</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('stn_product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('product_value', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('unit')) has-error @endif">
                            {{ Form::label('stockinhand', 'Stock in Hand', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{ Form::label('stock_inhand', null, array('class'=> 'col-lg-5 pull-left')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('stockout_unit')) has-error @endif">
                            {{ Form::label('stockout_unit', 'Stock Out Unit', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('stockout_unit', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('stockout_unit')}}</p>
                                </div>
                            </div>

			                <div class="form-group @if ($errors->has('assignto')) has-error @endif">
			                    {{ Form::label('Assignto', 'Assign To', array('class' => 'col-lg-4 control-label')) }}
			                    <div class="col-lg-5">
			                        <select class='form-control' name='assignto' id='assignto'>
			                            <!-- <option value=""> - </option> -->
			                             @foreach($driverdetails as $driver)
                                      		  <option value="{{ $driver['id'] }}">{{ ucwords($driver['username']) }}</option>
                                   		 @endforeach
			                        </select>
			                         <p class="help-block" for="inputError">{{$errors->first('assignto')}}</p>
			                    </div>
			                </div>

                            <div class="form-group @if ($errors->has('stockout_remark')) has-error @endif">
                            {{ Form::label('stockout_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('stockout_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('stockout_remark')}}</p>
                                </div>
                                
                            </div>

                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="submit" id="savestockout" class="btn btn-primary pull-left">Save</button>
                                {{Form::input('hidden', 'stockout_productid', '')}}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="returnStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Stock Return</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('stret_product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('productret_value', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('stockreturn_unit')) has-error @endif">
                            {{ Form::label('stockreturn_unit', 'Stock Return Unit', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('stockreturn_unit', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('stockreturn_unit')}}</p>
                                </div>
                            </div>

			                <div class="form-group @if ($errors->has('returnfrom')) has-error @endif">
			                    {{ Form::label('Returnfrom', 'Return From', array('class' => 'col-lg-4 control-label')) }}
			                    <div class="col-lg-5">
			                        <select class='form-control' name='returnfrom' id='returnfrom'>
			                            <!-- <option value=""> - </option> -->
			                             @foreach($driverdetails as $driver)
                                      		  <option value="{{ $driver['id'] }}">{{ ucwords($driver['username']) }}</option>
                                   		 @endforeach
			                        </select>
			                         <p class="help-block" for="inputError">{{$errors->first('returnfrom')}}</p>
			                    </div>
			                </div>

                            <div class="form-group @if ($errors->has('returnfrom_remark')) has-error @endif">
                            {{ Form::label('returnfrom_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('returnfrom_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('returnfrom_remark')}}</p>
                                </div>
                                
                            </div>

                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="submit" id="savestockreturn" class="btn btn-primary pull-left">Save</button>
                                <!-- &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-primary pull-left" id="close-assign-mdl" data-dismiss="modal">Close</button> -->
                                {{Form::input('hidden', 'stockreturn_productid', '')}}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Recent Activities </h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('stret_product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('productret_value', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('stockreturn_unit')) has-error @endif">
                            {{ Form::label('stockreturn_unit', 'Stock Return Unit', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('stockreturn_unit', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('stockreturn_unit')}}</p>
                                </div>
                            </div>

			                <div class="form-group @if ($errors->has('returnfrom')) has-error @endif">
			                    {{ Form::label('Returnfrom', 'Return From', array('class' => 'col-lg-4 control-label')) }}
			                    <div class="col-lg-5">
			                        <select class='form-control' name='returnfrom' id='returnfrom'>
			                            <!-- <option value=""> - </option> -->
			                             @foreach($driverdetails as $driver)
                                      		  <option value="{{ $driver['id'] }}">{{ ucwords($driver['username']) }}</option>
                                   		 @endforeach
			                        </select>
			                         <p class="help-block" for="inputError">{{$errors->first('returnfrom')}}</p>
			                    </div>
			                </div>

                            <div class="form-group @if ($errors->has('returnfrom_remark')) has-error @endif">
                            {{ Form::label('returnfrom_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('returnfrom_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('returnfrom_remark')}}</p>
                                </div>
                                
                            </div>

                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="submit" id="savestockreturn" class="btn btn-primary pull-left">Save</button>
                                <!-- &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-primary pull-left" id="close-assign-mdl" data-dismiss="modal">Close</button> -->
                                {{Form::input('hidden', 'stockreturn_productid', '')}}
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

<script>

// $(document).ready(function(){  
//            $('.hoverss').popover({  
//                 title:'News',  
//                 html:true,  
//                 placement:'top'  
//            });  
//            // function fetchData(){  
//            //      var fetch_data = '';  
//            //      var element = $(this);  
//            //      var id = element.attr("id");  
//            //      $.ajax({  
//            //           url:"fetch.php",  
//            //           method:"POST",  
//            //           async:false,  
//            //           data:{id:id},  
//            //           success:function(data){  
//            //                fetch_data = data;  
//            //           }  
//            //      });  
//            //      return fetch_data;  
//            // }  
//       });  



    $(document).ready(function(){

    	$(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    	});

    	$('body').on('click', '.addStock', function(){ 
    		var productID =  $(this).attr('data-id');
    		 // $('#productvalue').val(productID);
    		 $("label[for='product_value']").text(productID);
    		 $("input[name='stockin_productid']").val(productID);

    		 $('#unit').val("");
    		 $('#unit_price').val("");
    		 $('#total_amount').val("");
    		 $('#stockin_remark').val("");
    		 $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val("");

            $("#addStockModal").modal('show');
            
    		 
            // alert(productID);
           
    	 });


    	$('body').on('click', '.stockReturn', function(){ 
    		var productID =  $(this).attr('data-id');
    		 // $('#productvalue').val(productID);
    		 $("label[for='productret_value']").text(productID);
    		 $("input[name='stockreturn_productid']").val(productID);

    		 $('#stockreturn_unit').val("");
    		 $('#returnfrom_remark').val("");
    		 $('select[name=returnfrom]').val(1);

             $("#returnStockModal").modal('show');           
    		 
            // alert(productID);
           
    	 });


    	$('#unit').on(
    			'change', function(){

    				if($('#unit_price').val() ==""){
    					$('#unit_price').val('0');
    				}

    				var tamount = parseFloat($('#unit').val()) * parseFloat($('#unit_price').val());
    				// alert(tamount);
    				if(tamount == '' || tamount == 'NaN' ){
    					tamount= '0';
    				}
    				$('#total_amount').val(parseFloat(tamount).toFixed(2));
    			}
    		);

    	$('#unit_price').on(
    			'change', function(){
    				var tamount = parseFloat($('#unit').val()) * parseFloat($('#unit_price').val());
    				if(tamount == '' || tamount == 'NaN' ){
    					tamount= '0';
    				}
    				$('#total_amount').val(parseFloat(tamount).toFixed(2));
    			}
    		);

    	// $('body').on('change', '#stn_unit', function(){

    	// 	$('#stn_total_amount').val($('#stn_unit_price').val() + $('#stn_unit_price').val());

    	// });

    	$('#savestockin').on('click', 
    				function() {

    		var unit 			= $('#unit').val();
    		var unitprice 		= $('#unit_price').val();
    		var totalamount 	= $('#total_amount').val();		
    		var remarkstockin	= $('#stockin_remark').val();
    		var expirydate 		= $('#datepicker').val();
    		var productID		= $("input[name='stockin_productid']").val();	

    		console.log(unit);
    		console.log(unitprice);
    		console.log(totalamount);
    		console.log(remarkstockin);


    		$.ajax({
                    method: "POST",
                    url: "/warehouse/savestockin",
                    dataType:'json',
                    data: {
                        'productID':productID,
                        'unit':unit,
                        'expirydate':expirydate,
                        'unitprice':unitprice,
                        'totalamount':totalamount,
                        'remarkstockin':remarkstockin
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response == 1){
                            
                            // bootbox.alert("Successfully Saved!.", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });  
                            
                            $("#savestockin").submit();

                           }
                           else
                           {
                            bootbox.alert("Please select Product ID", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                           }
                    }
                });



    	});


    	$('#savestockreturn').on('click', 
    				function() {

    		var stockReturn 	= $('#stockreturn_unit').val();
    		var returnfrom 	 	= $('select[name=returnfrom]').val();
    		var remarkreturn	= $('#returnfrom_remark').val();
    		var productID		= $("input[name='stockreturn_productid']").val();	

    		console.log(stockReturn);
    		console.log(returnfrom);
    		console.log(remarkreturn);
    		console.log(productID);


    		$.ajax({
                    method: "POST",
                    url: "/warehouse/savestockreturn",
                    dataType:'json',
                    data: {
                        'productID':productID,
                        'stockreturn':stockReturn,
                        'returnfrom':returnfrom,
                        'remarkreturn':remarkreturn
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response == 1){
                            
                            $("#savestockreturn").submit();

                           }
                           else
                           {
                            bootbox.alert("Please select Product ID", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                           }
                    }
                });



    	});



    	$('#savestockout').on('click', 
    				function() {
			
    		var productID		= $("input[name='stockout_productid']").val();			
    		var outstockunit 	= $('#stockout_unit').val();
			var assignto 	 	= $('select[name=assignto]').val();
			var outstockremark 	= $('#stockout_remark').val();			

			if(outstockunit != ''){

				$.ajax({
                    method: "POST",
                    url: "/warehouse/savestockout",
                    dataType:'json',
                    data: {
                        'productID':productID,
                        'outstockunit':outstockunit,
                        'assignto':assignto,
                        'outstockremark':outstockremark

                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response == 1){

                            $("#savestockout").submit();

                           }
                           else
                           {
                            bootbox.alert("Please select Product ID", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                           }
                    }
                });

			}
			else{
				$('#stockout_unit').focus();

				bootbox.alert("Please fill out this field", function(e){
                                        parent.$.fn.colorbox.close();
                                    });  
                
				return false;
			}


				




			});

    	


    	$('body').on('click', '.outStock', function(){ 
    		var productID =  $(this).attr('data-id');
    		 // $('#productvalue').val(productID);
    		 $("label[for='product_value']").text(productID);
    		 $("input[name='stockout_productid']").val(productID);

    		$('#stockout_unit').val('');
			$('select[name=assignto]').val(1);
			$('#stockout_remark').val('');
    		
    		if(productID != ""){

    			$.ajax({
                    method: "POST",
                    url: "/warehouse/stockinhand",
                    dataType:'json',
                    data: {
                        'productid':productID
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.productdetails){
                            // bootbox.alert("Tested ok", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });  

                            $("label[for='stock_inhand']").text(data.productdetails.stockin_hand);
                            
                            $("#outStockModal").modal('show');

                           }
                           else
                           {
                            bootbox.alert("Please select Product ID", function(e){
                                        parent.$.fn.colorbox.close();
                                    });
                           }
                    }
                });


    		}

            
            // alert(productID);
           
    	 });


    	$('body').on('click', '.popoverButton', function(){ 
    		// console.log('Test');
    		var productID = $(this).attr('data-id');


    		

    	});


    	

    	// $('[data-toggle="popover"]').popover().on("mouseover",
    	// 		function(){
    	// 			var dataid = $(this).attr('data-id');

    	// 			$('[data-toggle="popover"]').popover();
    	// 		}	 
    	// 	);

    	 // $('[data-toggle="popover"]').popover({
		    //     placement : 'top',
		    //     trigger : 'hover'
		    // });

    	 // $("[data-toggle='popover']").popover().on("mouseover", function(){
    	 // 	$(this).attr('data-id').title="Welcome to Jocom";	
    	 // });

    	  
    	  

			// $("tr[text-center]").popover({placement:"top",trigger:"hover"});
			

			$('#dataTables-products').dataTable({
		            "autoWidth" : false,
		            "processing": true,
		            "serverSide": true,
		            "ajax": "{{ URL::to('warehouse/listing?'.http_build_query(Input::all())) }}",
		            "order" : [[ 0, 'desc' ]],
		            "columnDefs" : [{
		                "targets" : "_all",
		                "defaultContent" : "",
		                "orderable": false, "targets": [2,3],
		            }],
		            "columns" : [
		                { "data" : "0", "className" : "text-center" },
		                { "data" : "1" },
		                { "data" : "2" },
		                { "data" : "3" },
		                { "data" : "4", 
			                "render": function (val, type, row) {
				                    return val < 0 ? "<a href=# class='popoverButton' data-id="+row[0]+"><font color='#FF0000'><b>"+val+"</b></font></a>" :  "<a href=# class='popoverButton'  data-id="+row[0]+">"+val+"</a>";
				                },
		                 "className" : "text-center",
		                 
		                  },
		                { "data" : "5", "className" : "text-center" },
		                { "data" : "6", "className" : "text-center" 
		            	},
		                { "data" : "7", "className" : "text-center" },
		                //{ "data" : "8", "className" : "text-center" },
		                {"data":"8",
                          "className":"left",
                          "render":function (val, type, row) {
                             return row[9] != ''? row[9] : row[8] ;
                          }
                         },
		                { "data" : "10", "className" : "text-center" },
		                { "data" : "11", "className" : "text-center" },
		                { "data" : "12", "className" : "text-center" }
		                // { data: function ( row, type, val, meta ) {
		                //     return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to Inventory">Add to Inventory <i class="fa fa-angle-double-right"></i> </button>';
		                //     }
		                // }

		                

		            ]
		            
		        });




	});
</script>

@stop