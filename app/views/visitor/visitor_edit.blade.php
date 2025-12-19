@extends('layouts.master')

@section('title') Visitor Management @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Visitor Management
            <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}visitor/edit/<?php echo $visitor->id; ?>"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-default" data-toggle="tooltip" href="{{asset('/')}}visitor"><i class="fa fa-reply"></i></a>
            </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url'=>'visitor/update/'.$visitor->id , 'class' => 'form-horizontal', 'files' => true)) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                {{ Form::label('visitor_name', 'Visitor Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('visitor_name', $visitor->name, array('class' => 'form-control') ) }}
                    </div>
                </div> 
                <div class='form-group'>
                {{ Form::label('visitor_ic', 'Visitor NRIC No./Email', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('visitor_ic', $visitor->ic, array('class' => 'form-control') ) }}
                    </div>
                </div> 
                <div class='form-group'>
                {{ Form::label('visitor_datetime', 'Visitor Date & Time', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                        <div class="input-group" id="datetimepicker_from">
                            {{ Form::text('visitor_datetime', $visitor->visitor_datetime, ['id' => 'visitor_datetime', 'class' => 'valid_from form-control', 'tabindex' => 1]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div> 

                <div class='form-group'>
                {{ Form::label('visit_purpose', 'Visit Purpose', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::textarea('visit_purpose', $visitor->visitor_purpose, array('class'=> 'form-control')) }}
                    </div>
                </div>


                <hr/>
                
                <div class='form-group'>
                {{ Form::label('status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                        <select class="form-control" name="status">
                            <?php
                                foreach ($status as $key => $valstat){
                                                                
                                    echo '<option value="' . htmlspecialchars( $key) .'"';
                                    if( $key == $visitor->status){
                                        echo ' selected="selected"';
                                    }
                                    echo '>' . htmlspecialchars( $valstat) . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>  
                <hr/>
            </div>

            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

    

</div>
<script>
    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    });

      $('body').on('click', '.remove-seller', function() {
       
        if($("#multipleSellerTable").children().length <= 1){
            alert('Must has minimum 1 Product');
        }else{
             var seller_id = $(this).attr("seller-id");
             $(".seller-cost-"+seller_id).remove();
            console.log($(this).parent().parent().remove());
        }
        
    });
   
   $("#add-seller-btn").click(function(){
       
        console.log($("#seller_name2").val());
       
        var seller_id = $("#seller_name2").val();
        var str = '<tr><td>'+seller_id+'<input name="product_sku[]" value="'+seller_id+'" type="hidden"></td> <td><input type="text" name="product_price[]"></td><td><input type="text" name="product_quantity[]"></td> <td><a seller-id="'+seller_id+'" class="btn btn-danger btn-xs remove-seller"><i class="fa fa-trash-o"></i> Remove</a></td></tr>';
                   $("#multipleSellerTable").append(str);

   });


</script>

@stop

@section('script')
    localStorage.clear();
    $('#addProdBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function calSubTotal() {
        var subTotal    = 0;
        var subPromo    = 0;
        var qty         = 0;
        var prodprice   = 0;
        var promoprice  = 0;

        $('tr.product').each(function(){
            var cur     = '#'+this.id;
            prodprice   = $(cur + ' td.p_price').text();
            promoprice  = $(cur + ' td.promo_price').text();
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;

            if(promoprice > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
            }

            subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(currencyFormat(subTotal));

        });
    }

    function calculateTotal() {
        var grandTotal = 0;
        var grandQty = 0;

        $('tr.product td.subtotal').each(function(){
            subtotal = $(this).text();
            grandTotal += parseFloat(subtotal.replace(/,/g,''));
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));;
        });
        
        $('.grand_total').html(currencyFormat(grandTotal));
        $('.grand_qty').html(grandQty);

    }

    $(document).on("click", "#deleteProduct", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        if(!$('.disc_product').length) {
            $('#emptyproduct').show();
        }
    });
@stop