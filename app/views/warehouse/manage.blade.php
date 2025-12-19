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

.center {
  padding: 20px 0 !important;
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

            @if (count($negative_stocks) > 0)
              <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Negative Stock Listing</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-negative">
                            <thead>
                                <tr>
                                    <th class="col-sm-1">Product ID</th>
                                    <th class="col-sm-1">SKU</th>
                                    <th class="col-sm-1">Name</th>
                                    <th class="col-sm-1">Actual Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            @endif

            <!-- <a class="btn btn-primary addStock">Stock In</a> -->
            <ul class="stats_box">
              <?php if (in_array(Session::get('username'), array('joshua', 'agnes', 'maruthu','asif','chua','nisahlini','amyng','quenny','joseph','joel','tracyyap','ramesh','william'), true ) ) {  ?>
              <li>
                <a class="quick-btn addStock">
                  <div class="sparkline"><i class="fa fa-upload fa-2x" aria-hidden="true"></i></div>
                  <div class="stat_text center">
                    <strong>Stock In</strong>
                <!-- <span class="percent down"> <i class="fa fa-caret-down"></i> -16%</span> -->
                  </div>
                </a>
              </li>
              <?php } ?>
              <!-- <li>
                <a class="quick-btn manageSize">
                  <div class="sparkline"><i class="fa fa-th-large fa-2x" aria-hidden="true"></i></div>
                  <div class="stat_text center">
                    <strong>Stock Size</strong>
                  </div>
                </a>
              </li> -->
              <li>
                <a class="quick-btn export" href="/warehouse/exportstockin">
                  <div class="sparkline"><i class="fa fa-list fa-2x" aria-hidden="true"></i></div>
                  <div class="stat_text center">
                    <strong>Export</strong>
                  </div>
                </a>
              </li>
            </ul>

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
                                    <th class="col-sm-1">Link(s) ID</th>
                                    <th class="col-sm-1">Root Base ID</th>
                                    <th class="col-sm-1 text-center">Stock in Hand</th>
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
                          <div class="form-group">
                            {{ Form::label('sku', 'SKU', array('class'=> 'col-lg-4 control-label')) }}
                            <div class="col-lg-5">
                              <input type="text" name="sku" id="sku" class="form-control" required>
                            </div>
                          </div>

                        	<div class="form-group @if ($errors->has('product_value')) has-error @endif">
                        	 {{ Form::label('stn_product_id', 'Product ID', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    <input type="text" name="product_id" id="product_id" class="form-control" disabled="true" required>
                                     <p class="help-block" for="inputError">{{$errors->first('unit')}}</p>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('product_name')) has-error @endif">
                        	 {{ Form::label('stn_product_name', 'Product Name', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-7">
                                    <input type="text" name="product_name" id="product_name" class="form-control" disabled="true" required>
                                     <p class="help-block" for="inputError">{{$errors->first('unit')}}</p>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('unit')) has-error @endif">
                            {{ Form::label('size', 'Size', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    <select class="form-control" id="size"></select>
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('unit')) has-error @endif">
                            {{ Form::label('unit', 'Quantity', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-3">
                                    {{Form::text('unit', null, array('required'=>'required', 'class'=>'form-control'))}}
                                     <p class="help-block" for="inputError">{{$errors->first('unit')}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('valid_to', 'Expiry Date', array('class'=> 'col-lg-4 control-label')) }}
                                 <div class="col-lg-5">
                                    {{Form::text('valid_to', null, array('required'=>'required', 'id'=>'datepicker','placeholder' => 'yyyy-mm-dd', 'class'=>'form-control'))}}
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
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sizeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title stock-size-title">Manage Stock Size </h4>
                </div>
                <div class="modal-body stock-size">
                  
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stockin_log" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title stock-size-title">Stock Log </h4>
                </div>
                <div class="modal-body stock-size">
                  <!--<a class="btn btn-primary addSize" title="" data-toggle="tooltip" href="#" style="margin-bottom: 5px;"><i class="fa fa-plus"></i></a>-->
                  <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-list"></i> Stock Log Listing</h3>                    
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive" style="overflow-x: none">
                            <table class="table table-striped table-bordered table-hover" id="dataTables-log">
                                <thead>
                                    <tr>
                                        <th class="">Quantity</th>
                                        <th class="">Remarks</th>
                                        <th class="">Expiry Date</th>
                                        <th class="">Insert By</th>
                                        <th class="">Insert At</th>
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
        </div>
    </div>

</div>
@stop
@section('inputjs')
<script>

    $(document).ready(function(){

    	$(function() {
        $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    	});

      $('#unit').on('change', function(){
        if($('#unit_price').val() ==""){
          $('#unit_price').val('0');
        }

        var tamount = parseFloat($('#unit').val()) * parseFloat($('#unit_price').val());
        // alert(tamount);
        if(tamount == '' || tamount == 'NaN' ){
          tamount= '0';
        }
        $('#total_amount').val(parseFloat(tamount).toFixed(2));
      });

      $('#unit_price').on('change', function(){
        var tamount = parseFloat($('#unit').val()) * parseFloat($('#unit_price').val());
        if(tamount == '' || tamount == 'NaN' ){
          tamount= '0';
        }
        $('#total_amount').val(parseFloat(tamount).toFixed(2));
      });

    	$('body').on('click', '.addStock', function(){ 

        $('#size option').remove();
        // $.ajax({
        //   type:'GET',
        //   url: '/warehouse/sizelist1',
        //   success:function(r){

        //     $.each(r, function() {
        //       $('#size').append($("<option />").val(this.quantity).text(this.quantity+' ('+this.label+')'));
        //     });
        //   },error:function(r) {
        //     alert('Error');
        //   }
        // });

        $('#sku').val("");
        $('#product_id').val("");
  		  $('#unit').val("");
  		  $('#unit_price').val("");
		    $('#total_amount').val("");
  		  $('#stockin_remark').val("");
  		  $( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val("");

        $("#addStockModal").modal('show');
           
    	 });

      $('body').on('click', '.manageSize', function(){ 

        $("#sizeModal").modal('show');

        $('.stock-size-title').html('Manage Stock Size ');
        $('.stock-size').html(`<a class="btn btn-primary addSize" title="" data-toggle="tooltip" href="#" style="margin-bottom: 5px;"><i class="fa fa-plus"></i></a>
                <div class="panel panel-default">
                  <div class="panel-heading">
                      <h3 class="panel-title"><i class="fa fa-list"></i> Stock Size Listing</h3>                    
                  </div>
                  <!-- /.panel-heading -->
                  <div class="panel-body">
                      <div class="table-responsive" style="overflow-x: none">
                          <table class="table table-striped table-bordered table-hover" id="dataTables-stocks">
                              <thead>
                                  <tr>
                                      <th class="">ID</th>
                                      <th class="">Label</th>
                                      <th class="">Quantity</th>
                                      <th class="">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                              </tbody>
                          </table>
                      </div>                            
                  </div>
                  <!-- /.panel-body -->
                </div>`);

        $('#dataTables-stocks').dataTable({
            "autoWidth" : false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('warehouse/sizelist') }}",
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

            ]
            
        }); 
           
      });

      $('body').on('click', '.addSize', function(){ 
        $('.stock-size-title').html('Add New Stock Size');
        $('.stock-size').html(`{{ Form::open(array('class' => 'form-horizontal')) }}
                          <div class="form-group">
                            {{ Form::label('label', 'Label', array('class'=> 'col-lg-4 control-label')) }}
                            <div class="col-lg-5">
                              <input type="text" name="new_label" id="new_label" class="form-control" required>
                            </div>
                          </div>

                          <div class="form-group">
                            {{ Form::label('quantity', 'Quantity', array('class'=> 'col-lg-4 control-label')) }}
                            <div class="col-lg-5">
                              <input type="text" name="new_quantity" id="new_quantity" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group">
                              <div class="col-sm-offset-4 col-sm-10">
                                  <button type="submit" id="storestocksize" class="btn btn-primary pull-left">Save</button>
                              </div>
                          </div>
                    {{ Form::close() }}`);
      });

      $('body').on('click', '.stockLog', function() {
        $("#stockin_log").modal('show');
        var product_id = $(this).attr('data-id');
        $('#dataTables-log').dataTable({
          "destroy": true,
          "autoWidth" : false,
          "ajax": '/warehouse/stockinproductlog/' + product_id,
          "order" : [[ 2, 'desc' ]],
          "columns" : [
              { "data" : "0", "className" : "text-center" },
              { "data" : "4" },
              { "data" : "1" },
              { "data" : "2" },
              { "data" : "3" },

          ]
          
        });
      });

      $('input[name=sku]').change(function() { 
        var sku = $(this).val();

        $.ajax({
          method: "GET",
          url: '/warehouse/productid/' + sku,
          success: function(data) {

            if (data.product_id != undefined) {
               
              $('#product_id').val(data.product_id);
              $('#product_name').val(data.product_name);
              $('#size').append($("<option />").val(1).text(1));
              if (data.quantity != null) {
                $('#size').append($("<option />").val(data.quantity).text(data.quantity));
              }
              
            } else {
              alert(data.error);
              $('#product_id').val('');
            }
          }
        });
      });

    	$('#savestockin').on('click', function() {

        var productID   = $("#product_id").val(); 
        var size = $('#size').find(":selected").val();
    		var quantity	= $('#unit').val() * size;
    		var unitprice 		= $('#unit_price').val();
    		var totalamount 	= $('#total_amount').val();		
    		var remarkstockin	= $('#stockin_remark').val();
    		var expirydate 		= $('#datepicker').val();
    		
        console.log(productID);
    		console.log(quantity);
    		console.log(unitprice);
    		console.log(totalamount);
    		console.log(remarkstockin);


        if (productID.length > 0 && quantity > 0 && expirydate.length > 0) {
          $.ajax({
            method: "POST",
            url: "/warehouse/savestockin",
            dataType:'json',
            data: {
                'productID':productID,
                'unit':quantity,
                'expirydate':expirydate,
                'unitprice':unitprice,
                'totalamount':totalamount,
                'remarkstockin':remarkstockin
            },
            beforeSend: function(){
                
            },
            error: function(data) {
              console.log(data);
              alert(data);
              return false;
            },
            success: function(data) {

              if(data.response == 1){
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
        } else {
          if (productID.length == 0) {
            alert('Please enter valid SKU');
            return false;
          } else if (quantity == 0 || quantity == NaN) {
            alert('Please enter quantity');
            return false
          } else if (expirydate.length == 0) {
            alert('Please enter expiry date');
            return false;
          }
        }
    	});

      $('body').on('click', '#storestocksize', function(){ 
        var label = $('#new_label').val();
        var quantity = $('#new_quantity').val();

        $.ajax({
          method: "POST",
          url: "/warehouse/savestocksize",
          dataType:'json',
          data: {
              'label':label,
              'quantity':quantity,
          },
          beforeSend: function(){
              
          },
          error: function(data) {
            console.log(data);
          },
          success: function(data) {
            if(data.response == 1){
              $("#storestocksize").submit();
            }
          },
        });
           
      });

			$('#dataTables-products').dataTable({
          "autoWidth" : false,
          "processing": true,
          "serverSide": true,
          "ajax": "{{ URL::to('warehouse/simplelist') }}",
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
              { "data" : "5", "className" : "text-center" },
              { "data" : "6", "className":"text-center",
                "render":function (val, type, row) {
                  return row[7] != ''? row[7] : row[6] ;
                }
              },
              { "data" : "4", 
                "render": function (val, type, row) {
	                    return val < 0 ? "<a href=# class='popoverButton stockLog' data-id="+row[0]+"><font color='#FF0000'><b>"+val+"</b></font></a>" :  "<a href=# class='popoverButton stockLog'  data-id="+row[0]+">"+val+"</a>";
	                },
               "className" : "text-center",
               
                },

          ]
          
      });

	});
	
	$('#dataTables-negative').dataTable({
    "autoWidth" : false,
    "processing": true,
    "serverSide": true,
    "ajax": "{{ URL::to('warehouse/negative') }}",
    "order" : [[ 0, 'desc' ]],
    "columns" : [
      { "data" : "0" },
      { "data" : "1" },
      { "data" : "3" },
      { "data" : "2" }
    ]
  });

  $('body').on('click', '#updatestocksize', function(){ 

    var sizeId = $('#size_id').text();
    var label = $('#label').val();
    var quantity = $('#quantity').val();

    $.ajax({
      method: "GET",
      url: '/warehouse/updatestocksize/',
      dataType: 'json',
      data: {
        'id': sizeId,
        'label': label,
        'quantity': quantity,
      },
      success: function(data) {
        if(data.response == 1){
          $("#updatestocksize").submit();
        }
      }
    });    
  });

  function updateSize(id) {
    $.ajax({
      method: "GET",
      url: '/warehouse/sizedetail/' + id,
      success: function(data) {

        $('.stock-size').html(`{{ Form::open(array('class' => 'form-horizontal')) }}
                      <div class="form-group">
                        {{ Form::label('size_id', 'Size ID', array('class'=> 'col-lg-4 control-label')) }}
                        <div class="col-lg-5">
                          <p class="form-control-static" id="size_id">`+data.size.id+`</p>
                        </div>
                      </div>

                      <div class="form-group">
                        {{ Form::label('label', 'Label', array('class'=> 'col-lg-4 control-label')) }}
                        <div class="col-lg-5">
                          <input type="text" name="label" id="label" class="form-control" value="`+data.size.label+`" required>
                        </div>
                      </div>

                      <div class="form-group">
                        {{ Form::label('quantity', 'Quantity', array('class'=> 'col-lg-4 control-label')) }}
                        <div class="col-lg-5">
                          <input type="text" name="quantity" id="quantity" class="form-control" value="`+data.size.quantity+`" required>
                        </div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-offset-4 col-sm-10">
                              <button type="submit" id="updatestocksize" class="btn btn-primary pull-left">Save</button>
                          </div>
                      </div>
                {{ Form::close() }}`);
        }
    });

    $('.stock-size-title').html('Update Stock Size');
    
  }

  function deleteSize(id) {
    if (confirm('Delete this stock size?')) {
      $.ajax({
        method: "POST",
        url: "/warehouse/deletestocksize",
        dataType:'json',
        data: {
            'id': id,
        },
        beforeSend: function(){
            
        },
        error: function(data) {
          console.log(data);
        },
        success: function(data) {
          alert('Stock size deleted.');
          $('#dataTables-stocks').DataTable().ajax.reload();
        },
      });
    }
  }
</script>

@stop