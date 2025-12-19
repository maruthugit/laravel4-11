@extends('layouts.master')
@section('title') Warehouse @stop
@section('content')

<div id="page-wrapper">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Warehouse Management : Inventory Adjustments
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

            <!-- <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Inventory Adjustments</h2>
                </div>
                

                


            </div> -->

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
                                    <th class="col-sm-1 text-center">Old Stock</th>
                                    <th class="col-sm-1 text-center">New Stock</th>
                                    <th class="col-sm-1 text-center">Actual Stock</th>
                                    <th class="col-sm-1 text-center">Action</th>
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


    <div class="modal fade" id="addAdjustModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Inventory Adjustments</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(array('class' => 'form-horizontal')) }}
                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('product_value', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('stock_inhand')) has-error @endif">
                        	 {{ Form::label('stockinhand', 'Current Stock Unit', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::label('stock_inhand', '', array('class'=> 'col-lg-3 control-label prdLabel')) }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('newstock')) has-error @endif">
                            {{ Form::label('newstock', 'New Stock', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('newstock', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('newstock')}}</p>
                                </div>
                            </div>

                          
                            <div class="form-group @if ($errors->has('adjustments_remark')) has-error @endif">
                            {{ Form::label('adjustments_remark', "Remarks", array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                     {{ Form::textarea('adjustments_remark', null, ['class' => 'form-control', 'rows' => '3']) }}
                                    <p class="help-block" for="inputError">{{$errors->first('adjustments_remark')}}</p>
                                </div>
                                
                            </div>

                             
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="submit" id="saveadjust"  class="btn btn-primary pull-left">Save</button>
                                {{Form::input('hidden', 'stockadjust_productid', '')}}
                                {{Form::input('hidden', 'adjust_inhand', '')}}
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

    $(document).ready(function(){


    	$('body').on('click', '.Adjust', function(){ 
    		var productID =  $(this).attr('data-id');
    		 // $('#productvalue').val(productID);
    		 $("label[for='product_value']").text(productID);
    		 $("input[name='stockadjust_productid']").val(productID);


    		 $('#newstock').val("");
    		 $('#adjustments_remark').val("");
    


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
                            $("input[name='adjust_inhand']").val(data.productdetails.stockin_hand);
                            
                            $("#addAdjustModal").modal('show');

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



    	$('#saveadjust').on('click', 
    				function() {

    		var newstock 		= $('#newstock').val();
    		var stockinhand 	= $("input[name='adjust_inhand']").val();	
    		var remarkadjust	= $('#adjustments_remark').val();
    		var productID		= $("input[name='stockadjust_productid']").val();	


    	


    		$.ajax({
                    method: "POST",
                    url: "/warehouse/savestockadjust",
                    dataType:'json',
                    data: {
                        'productID':productID,
                        'newstock':newstock,
                        'stockinhand':stockinhand,
                        'remarkadjust':remarkadjust
                    },
                    beforeSend: function(){
                        
                    },
                    success: function(data) {
                           // alert(data.response);
                           if(data.response == 1){
                            
                            // bootbox.alert("Successfully Saved!.", function(e){
                            //             parent.$.fn.colorbox.close();
                            //         });  
                            
                            $("#saveadjust").submit();

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



    	$('#dataTables-products').dataTable({
		            "autoWidth" : false,
		            "processing": true,
		            "serverSide": true,
		            "ajax": "{{ URL::to('warehouse/adjustmentslisting?'.http_build_query(Input::all())) }}",
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
				                    return val < 0 ? "<font color='#FF0000'><b>"+val+"</b></font>" : val;
				                },
		                 "className" : "text-center" },
		                { "data" : "5" , "className" : "text-center" },
		                { "data" : "6", 
		                	"render": function (val, type, row) {
				                    return val < 0 ? "<font color='#FF0000'><b>"+val+"</b></font>" : val;
				                },
		                 "className" : "text-center" },
		                { "data" : "7", "className" : "text-center" }

		                // { data: function ( row, type, val, meta ) {
		                //     return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to Inventory">Add to Inventory <i class="fa fa-angle-double-right"></i> </button>';
		                //     }
		                // }
		            ]
		        });


    });

</script> 	

@stop