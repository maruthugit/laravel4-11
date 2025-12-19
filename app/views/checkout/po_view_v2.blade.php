<?php if ($htmlview == true) { ?>

<style type="text/css">
    
    body{
        max-width: 900px;
        border: solid 1px #888080;
        padding: 10px;
        margin: auto;
        background-color: #fff;
        }
    @media print{
        
        #printpagebutton { 
            display:none; 
        }

        .address_container{
            font-size:12px
        }

        body{
            border: solid 1px white;
            max-width: 100%;
            font-size: 12px;
            font-family: "Arial", Georgia, Serif;

        }
        #download{
            display: none;
        } 
    }
</style>

<?php } ?>
@extends('checkout.pdf_header')
@section('content')
<?php 

$file = (Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($display_details['po_no']) . '.pdf')."#".($display_details['transaction_id']).'#'.$display_details['po_no'];
$encrypted = Crypt::encrypt($file);
$encrypted = urlencode(base64_encode($encrypted));

?>
    <page_header>
    </page_header>
    <?php if ($htmlview == false) { ?>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['po_no']}})
                </td>
            </tr>
        </table>
    </page_footer>
    <?php } ?>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <?php if ($htmlview == false) { ?>
                <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br />
                <?php }else{ ?>
                <img width="100px;" src="/img/invoice_po_logo.jpg" /><br /><br />
                <?php } ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>PURCHASE ORDER</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container">                
                <!-- <span style="font-weight:bold">Jocom IT Consulting Sdn. Bhd.</span><br /> -->
                {{$display_issuer['issuer_name']}}<br />
                {{$display_issuer['issuer_address_1']}}<br />
                <?php if ($display_issuer['issuer_address_2'] != "") echo $display_issuer['issuer_address_2']."<br />"; ?>
                {{$display_issuer['issuer_address_3']}}<br />
                {{$display_issuer['issuer_address_4']}}<br />
                {{$display_issuer['issuer_tel']}}<br />
                 Email : enquiries@tmgrocer.com <br />
                <?php if ($display_issuer['issuer_gst'] != "") echo "(GST Reg No: ".$display_issuer['issuer_gst'].")<br />"; ?>
                <br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                PO No: {{$display_details['po_no']}}<br />
                PO Date: {{$display_details['po_date']}}<br />
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;" class="address_container">
                <br />
                {{$display_seller['seller_name']}}<br />
                {{$display_seller['seller_address_1']}}<br />
                <?php if ($display_seller['seller_address_2'] != "") echo $display_seller['seller_address_2']."<br />"; ?>
                {{$display_seller['seller_address_3']}}<br />
                {{$display_seller['seller_address_4']}}<br />
                {{$display_seller['seller_email']}}<br />
                <?php if ($display_seller['seller_gst'] != "") echo "(GST Reg No: ".$display_seller['seller_gst'].")<br />"; ?>
                <br />
                Attn: {{$display_seller['attn_name']}}<br />
                Contact No: {{$display_seller['contact_no']}}
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                Delivery name:<br />
                {{$display_details['delivery_name']}}<br /><br />
                Delivery contact:<br />
                {{$display_details['delivery_contact_no']}}<br /><br />
                Delivery address:<br />
                {{$display_details['delivery_address_1']}}<br />
                {{$display_details['delivery_address_2']}}<br />
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_address_4']}}<br />
            </td>
        </tr>
    </table>
    
    <br /><br />
    
    <table border="1" style="width: 100%;" class="address_container">
        <thead>
            <tr>
                <th style="width: 16%;" class="address_container">Item Code</th>
                <th style="width: 20%;" class="address_container">Description</th>
                <th style="width: 20%;" class="address_container">Label / SKU</th>
                <th style="width: 6%;" class="address_container">Quantity</th>
                <th style="width: 9%;" class="address_container">Unit Price<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" class="address_container">Total Excl. GST<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 9%;" class="address_container">GST @ {{$display_trans->gst_rate}}%<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" class="address_container">Total Incl. GST<br />({{Config::get("constants.CURRENCY")}})</th>
            </tr>
        </thead>
        
        
        <?php 
        $loopCount = 0;
        $total = 0;
        $total_gst = 0;
        $coupon_amt = 0;
        $value_with_gst = 0;
        ?>
        @foreach($display_product as $product)
        <?php 
        $loopCount++;

        // Parent Seller no Referral Fees
        if ($endSeller == 1)
        {
            if($product->gst_rate_item > 0){
                $productPrice = round(($product->price / (100 + $product->gst_rate_item) ) * 100, 2);
            }else{
                $productPrice = round($product->price, 2);
            }
            $unit_prices = $productPrice - (isset($product->p_referral_fees) ? (isset($product->p_referral_fees_type) && $product->p_referral_fees_type == 'N' ? $product->p_referral_fees : ($product->p_referral_fees * ($productPrice) / 100)) : 0);
            $gst_seller = $product->gst_seller;
        } 
        else
        {
            if($product->gst_rate_item > 0){
                $unit_prices = round(($product->price / (100 + $product->gst_rate_item) ) * 100, 2);
            }else{
                $unit_prices = round($product->price, 2);
            }
            $gst_seller = $product->parent_gst_amount;
        }

        $value = round($unit_prices, 2) * $product->unit;
        $value_with_gst = $value + $gst_seller ;
        $total_gst += $gst_seller;
        $total += $value;

        ?>
            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td style="width: 16%;" class="address_container">{{$product->sku}} <?php if ($product->product_group != "") { ?><br />({{$product->product_group}}) <?php } ?></td>
                <td style="width: 20%;" class="address_container">
                    <?php if ($product->pname != "") { ?>{{$product->pname}} <?php } else {?>{{$product->name}} <?php } ?><br />
                </td>
                <td style="width: 20%;" class="address_container">
                    {{$product->price_label}} <?php if ($product->seller_sku != "") { ?> / {{$product->seller_sku}} <?php } ?><br />
                </td>
                <td style="width: 6%;" align="center" class="address_container">{{$product->unit}}</td>
                <td style="width: 9%;" align="center" class="address_container">{{number_format($unit_prices, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($value, 2, ".", "")}}</td>
                <td style="width: 9%;" align="center" class="address_container">{{number_format($gst_seller, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($value_with_gst, 2, ".", "")}}</td>
            </tr>       
        @endforeach
        
<!--        <?php if($display_trans['delivery_charges'] > 0) {?>
        <tr class="no-top">
            <td>&nbsp;</td>
            <td>
                Delivery Charges
            </td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">{{$display_trans['delivery_charges']}}</td>
        </tr>
        <?php }?> -->
        <!-- <tfoot> -->
            <!-- <tr>
                <td colspan="5" style="border-bottom: 0px;">
                    Sub-Total
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($total, 2, ".", "")}}
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($total_gst, 2, ".", "")}}
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($total+$total_gst, 2, ".", "")}}
                </td>
            </tr> -->
            <!-- {{$display_trans['coupon_code']}} -->
            <tr>
                <td colspan="5" style="border-top: 0px;" class="address_container">Grand Total</td>
                <td align="center" class="address_container">
                    {{number_format($total, 2, ".", "")}}
                    <!-- {{number_format($total-$coupon_amt, 2, ".", "")}} -->
                </td>
                <td align="center" class="address_container">
                    {{number_format($total_gst, 2, ".", "")}}
                </td>
                <td align="center" class="address_container">
                    {{number_format($total+$total_gst, 2, ".", "")}}
                </td>
            </tr>
        <!-- </tfoot> -->
        
    </table>
    
    
    
    <br /><br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td>
               This is a computer generated document. No signature required.
            </td>
        </tr>    
    </table>

    

@stop

<a href="<?php echo '/transaction/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
