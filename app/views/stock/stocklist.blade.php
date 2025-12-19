
@extends('layouts.master')

@section('title') Stock Transfer @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stock Transfer</h1>
             <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Stock Transfer View</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">   
                                     
                 
                 {{ Form::open(['url' => "stock/{$stock->id}", 'method' => 'patch','class' => 'form-horizontal']) }}


                            <div class="form-group">
                            {{ Form::label('id', 'ST ', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                    <p class="form-control-static">{{$stock->id}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group {{ $errors->first('st_no', 'has-error') }}">
                            {{ Form::label('st_no', 'ST Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                
                                     <p class="form-control-static">{{$stock->st_no}}</p>
                                </div>
                            </div>

                              <div class="form-group {{ $errors->first('expired_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Expiry Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker3'>
                                      
                                      <p class="form-control-static">{{$stock->expired_date}}</p>
                                                                                

                                      
                                 </div>
                                </div>
                            </div>

                             <div class="form-group {{ $errors->first('transfer_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Transfer Date Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker2'>
                                      
                                          <p class="form-control-static">{{$stock->transfer_date}}</p>
                                 </div>
                                </div>
                            </div>

                             <div class="form-group {{ $errors->first('st_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">ST Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker1'>
                                      
                                        <p class="form-control-static">{{$stock->st_date}}</p>
                                 </div>
                                </div>
                            </div>

                            <hr />

                        

                         

                            <div class="form-group @if ($errors->has('deliver_from')) has-error @endif">
                            {{ Form::label('deliver_from', 'Deliver From', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                     <p class="form-control-static">{{$stock->deliver_from}}</p>
                                </div>
                            </div>

                        

                            <div class="form-group @if ($errors->has('deliver_to')) has-error @endif">
                            {{ Form::label('deliver_to', 'Description *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                     <p class="form-control-static">{{$stock->deliver_to}}</p>
                                </div>
                            </div>

                           <hr/>

                           <div class="form-group {{ $errors->first('purposeoftransfer', 'has-error') }}">
                                <label class="col-lg-2 control-label">Purpose of Transfer</label>
                                <div class="col-lg-4">
                                    <p class="form-control-static">{{$stock->purposeoftransfer}}</p>
                                </div>
                            </div>


                            <hr />

                            <div class="form-group @if ($errors->has('lid')) has-error @endif">
                                <label class="col-lg-2 control-label" for="price_option">Stock Products</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                       
                                    </div>
                                    <br />
                                    <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                    <table class="table table-bordered">
                                        <thead>
                                              <tr>
                                                <th class="col-sm-2">Product &amp; SKU</th>
                                                <th class="cell-small col-sm-1">Quantity </th>
                                            
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                                            @if (($product_stock) && sizeof($product_stock) > 0)

                                            @foreach ($product_stock as $pskg)
                                          <tr class="product">

                                                <input type="hidden" value="{{$pskg->product_id}}" name="lid[]" id="lid[]">
                                                <td><b>{{ $pskg->name }}</b><br><i class="fa fa-tag"></i> {{$pskg->sku}}</td>
                                            <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->qty}}</p></td>
                                               
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr id="emptyproduct">
                                                <td colspan="6">No product added.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                    

                         
                            <hr/>
                          <div class="form-group {{ $errors->first('sendby', 'has-error') }}">
                            {{ Form::label('sendby', 'Send By', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-2">
                                
                                   <p class="form-control-static">{{$stock->sendby}}</p>
                                </div>
                            </div>

                              <div class="form-group {{ $errors->first('receivedby', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Recieved By</label>
                                     <div class="col-lg-2">
                                
                                      
                                       <p class="form-control-static">{{$stock->receivedby}}</p>
                                                                                

                                      
                                
                                </div>
                            </div>

                             <div class="form-group {{ $errors->first('approvedby', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Approved By</label>
                                     <div class="col-lg-2">
                                   
                                      
                                       <p class="form-control-static">{{$stock->approvedby}}</p>
                                      
                           
                                </div>
                            </div>

                           

                            <hr />

                        

                    
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                          
                                 
                                   
                                    

                                       <a class="btn btn-primary" href="{{ url('stock') }}"><i class="fa fa-times">close</i></a>
                           
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop

@section('inputjs')
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>
@stop

@section('script')
    $(document).ready(function(){
        localStorage.clear()
        var rowCount = $('#ptb tr').length - 1;
        var label = document.getElementsByName("label");

      //alert('rowCount: '+rowCount + ' [label] ' + label.length);
        for (var i=0; i < label.length; i++) {
            localStorage.setItem("trid"+ i, label[i].value);
            //alert('rowCount: '+i + ' [label] ' + label[i].value);

        }
    });

    var rowTotal = $('<tr id="grandTotal"></tr>');
    $('#ptb').append(rowTotal);
    calculateTotal();

    $('#emptyproduct').hide();

    $('#addProdBtn').colorbox({
        iframe:true, width:"50%", height:"60%",
        onClosed:function(){
            calculateTotal();
        }
    });

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function calculateTotal() {
        var grandTotal = 0;
        var grandPromo = 0;
        $('tr.product td.p_price').each(function(){
            prodprice = $(this).text();
            grandTotal += parseFloat(prodprice.replace(/,/g,''));
        });

        $('tr.product td.promo_price').each(function(){
            promoprice = $(this).text();
            grandPromo += parseFloat(promoprice.replace(/,/g,''));
        });

        $('.p_price_total').html(currencyFormat(grandTotal));
        $('.p_promo_total').html(currencyFormat(grandPromo));
    }

    $(document).on("click", "#deleteItem", function(e) {
        var emptyRow = $('<tr id="emptyproduct"><td colspan="6">No product added.</td></tr>');
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if($('.product').length == '0') {
            $('#ptb').append(emptyRow);
            $('#grandTotal').remove();
        }
    });

        jQuery('#cboxOverlay').remove();

    $(document).ready(function() {
    
   $('#datetimepicker3').datetimepicker({
        format: 'YYYY-MM-DD '
    });

   $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD '
    });

     $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD '
    });
    
});

   
@stop
