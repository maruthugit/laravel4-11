@extends('layouts.master')
@section('title', 'Transaction')
@section('content')

<style type="text/css">
/* Spinner */
.loadin {
    position: fixed;
    top: 0; right: 0;
    bottom: 0; left: 0;
    background: #fff;
}
.loader {
    left: 50%;
    margin-left: -4em;
    font-size: 10px;
    border: .8em solid rgba(218, 219, 223, 1);
    border-left: .8em solid rgba(58, 166, 165, 1);
    animation: spin 1.1s infinite linear;
}
.loader, .loader:after {
    border-radius: 50%;
    width: 8em;
    height: 8em;
    display: block;
    position: absolute;
    top: 50%;
    margin-top: -4.05em;
}

@keyframes spin {
  0% {
    transform: rotate(360deg);
  }
  100% {
    transform: rotate(0deg);
  }
}
/* Spinner */
</style>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Transaction</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            
            <form method="" action="" class="form-horizontal" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-pencil"></i> Transaction Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <input type="hidden" id="devicetype" name="devicetype" value="manual">

                            <div class="form-group">
                                <label for="csv_file" class="col-lg-2 control-label">File input</label>
                                <div class="col-lg-3">
                                <input type="file" id="csv-file" name="csv-file" class="form-control" tabindex="1" accept=".csv">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group @if ($errors->has('lid')) has-error @endif">
                                <?php $count = 1; ?>
                                <label class="col-lg-2 control-label" for="price_option">Products * </label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/transaction/ajaxproduct/1"><i class="fa fa-plus"></i> Add Product</span>
                                        </div>
                                    </div>
                                    <br />
                                <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-2">Product Name &amp; SKU</th>
                                                <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Actual Price</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Promotion Price</th>
                                                <th class="cell-small col-sm-1">Quantity</th>
                                                <th class="cell-small col-sm-1">Sub-total</th>
                                                <th class="cell-small text-center col-sm-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('gst')) has-error @endif">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="with_total" checked> With Total
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="2" name="with_total"> Without Total
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="loadin" style="display: none">
                            <div class="loader"></div>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->

        </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
    <div class='form-group bottom' >
        <div class="col-lg-10" style="padding-bottom:10px;">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            <button type="button" id="upload-orders" class="btn btn-large btn-primary">Save</button>
            <!--Under Upgrading .. Wait for a moment.-->
        </div>
    </div>
    @endif
    </form>
    
</div>
    
@stop

@section('script')

    $('#upload-orders').on('click', function() {
        

        if ($('#csv-file').get(0).files.length === 0) {
            console.log("No file selected.");
            alert("No file selected.");
            return;
        }

        var productCount = $('input[name="qrcode[]"]').length;
        if (productCount < 1) {
            console.log("No product selected.");
            alert("No product selected.");
            return;
        }

        var qrcodes = [];
        var qtys = [];
        var priceopts = [];

        for (var i = 0; i < productCount; i++) {
            qrcodes.push($('input[name="qrcode[]"]')[i].value);
            qtys.push($('input[name="qty[]"]')[i].value);
            priceopts.push($('input[name="priceopt[]"]')[i].value);
        }

        var formData = new FormData()
        formData.append('csv_file', $('#csv-file')[0].files[0]);
        formData.append('qrcode', qrcodes);
        formData.append('priceopt', priceopts);
        formData.append('qty', qtys);
        formData.append('with_total', $("input[name='with_total']:checked").val());

        $(".loadin").show();
        $('.bottom').hide();

        $.ajax({
                url:'/transaction/bulkstore',
                data:formData,
                type:'post',
                processData: false,
                contentType: false,
                success:function(response) {
                    $(".loadin").hide();
                    $('.bottom').show();
                    if (response.status == 200) {
                        
                        alert('Order Upload Success');
                        location.reload();
                    } else if (response.status == 409) {
                        let duplicateOrders = response.duplicateOrder;
                        var orderList = '';
                        for (let index = 0; index < duplicateOrders.length; index++) {
                            orderList = orderList + '\n' + duplicateOrders[index].order_number;
                        }
                        alert('Duplicate order number ' + orderList);
                    } else if (response.status == 404) {
                        alert('No new order number found.')
                    } else {
                        alert('Error uploading order.');
                    }


                },
            });
    });

    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })

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
//           prodprice   = $(cur + ' td.p_price').text();
//            promoprice  = $(cur + ' td.promo_price').text();
            
            
            prodprice   = $(cur + ' #price_local').val();
            promoprice  = $(cur + ' #price_promo_local').val();
            
            prodForeignPrice   = $(cur + ' #price_foreign').val();
            promoForeignPrice  = $(cur + ' #price_promo_foreign').val();
            
            
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;
            var priceForeign   = prodForeignPrice;
            
            if(parseFloat(promoprice.replace(/,/g,''))  > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
                priceForeign   = promoForeignPrice;
            }
            var selected_type = $("#selected_type").val();
            if(selected_type == 2 ){
                subTotal  = '<div id="amt-value-main" amt-value-1="'+currencyFormat((qty * parseFloat(priceForeign.replace(/,/g,''))))+'" > USD '+currencyFormat((qty * parseFloat(priceForeign.replace(/,/g,'')))) + '</div><div id="amt-value-option" amt-value-2="'+currencyFormat(qty * parseFloat(price.replace(/,/g,'')))+'" style="font-size:10px;"> MYR '+ currencyFormat(qty * parseFloat(price.replace(/,/g,'')))+'</div>';
            }else{
                subTotal  = '<div id="amt-value-main" amt-value-1="'+currencyFormat((qty * parseFloat(price.replace(/,/g,''))))+'" > MYR '+currencyFormat((qty * parseFloat(price.replace(/,/g,'')))) + '</div>';
            }
            
            //subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(subTotal);
            //$(cur + ' .subtotal').html(currencyFormat(subTotal));

        });
    }
    
    function calculateTotal() {
        var grandTotal1 = 0;
        var grandTotal2 = 0;
        var grandQty = 0;

        $('tr.product td#subtotal ').each(function(){
            subtotal1 = $(this).find( "#amt-value-main" ).attr("amt-value-1");
            subtotal2 = $(this).find( "#amt-value-option" ).attr("amt-value-2");
            
            grandTotal1 += parseFloat(subtotal1.replace(/,/g,''));;
            if(selected_type == 2 ){
                grandTotal2 += parseFloat(subtotal2.replace(/,/g,''));;
            }
            
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));
        });
        
        var selected_type = $("#selected_type").val();
        if(selected_type == 2 ){
            $('.grand_total').html('USD ' +currencyFormat(grandTotal1)+'<br> <div style="font-size:10px;">MYR '+ currencyFormat(grandTotal2))+'</div>';
        }else{
            $('.grand_total').html('MYR ' +currencyFormat(grandTotal1));
        }
        
        $('.grand_qty').html(grandQty);

    }

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.product').length) {
            $('#emptyproduct').show();
            $('#grandTotal').remove();
        }
    });

@stop