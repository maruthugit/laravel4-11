@extends('checkout.pdf_header')
@section('content')

    <page_header>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['po_no']}})
                </td>
            </tr>
        </table>
    </page_footer>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>PURCHASE ORDER</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;">                
                <!-- <span style="font-weight:bold">Jocom IT Consulting Sdn. Bhd.</span><br /> -->
                Jocom IT Consulting Sdn. Bhd.<br />                 
                Unit 9-1, Level 9,<br />
                Tower 3, Avenue 3, Bangsar South,<br />
                No. 8, Jalan Kerinchi,<br />
                59200 Kuala Lumpur.<br />
                Tel: 03-2241 6637 Fax: 03-2242 3837<br />
                (GST Reg No: 001641910272)<br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                PO No: {{$display_details['po_no']}}<br />
                PO Date: {{$display_details['po_date']}}<br />
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;">
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
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
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
    
    <table border="1" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 16%;">Item Code</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 20%;">Label / SKU</th>
                <th style="width: 6%;">Quantity</th>
                <th style="width: 9%;">Unit Price<br />(RM)</th>
                <th style="width: 10%;">Total Excl. GST<br />(RM)</th>
                <th style="width: 9%;">GST @ {{$display_trans->gst_rate}}%<br />(RM)</th>
                <th style="width: 10%;">Total Incl. GST<br />(RM)</th>
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

        $unit_prices = $product->price - (isset($product->p_referral_fees) ? (isset($product->p_referral_fees_type) && $product->p_referral_fees_type == 'N' ? $product->p_referral_fees : ($product->p_referral_fees * ($product->price) / 100)) : 0);
        $value = $unit_prices * $product->unit;
        $value_with_gst = $value + $product->gst_seller;

        $total_gst += $product->gst_seller;
        $total += $value;

        ?>
            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td style="width: 16%;">{{$product->sku}} <?php if ($product->product_group != "") { ?><br />({{$product->product_group}}) <?php } ?></td>
                <td style="width: 20%;">
                    <?php if ($product->pname != "") { ?>{{$product->pname}} <?php } else {?>{{$product->name}} <?php } ?><br />
                </td>
                <td style="width: 20%;">
                    {{$product->price_label}} <?php if ($product->seller_sku != "") { ?> / {{$product->seller_sku}} <?php } ?><br />
                </td>
                <td style="width: 6%;" align="center">{{$product->unit}}</td>
                <td style="width: 9%;" align="center">{{number_format($unit_prices, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($value, 2, ".", "")}}</td>
                <td style="width: 9%;" align="center">{{number_format($product->gst_seller, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($value_with_gst, 2, ".", "")}}</td>
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
                <td colspan="5" style="border-top: 0px;">Grand Total</td>
                <td align="center">
                    {{number_format($total, 2, ".", "")}}
                    <!-- {{number_format($total-$coupon_amt, 2, ".", "")}} -->
                </td>
                <td align="center">
                    {{number_format($total_gst, 2, ".", "")}}
                </td>
                <td align="center">
                    {{number_format($total+$total_gst, 2, ".", "")}}
                </td>
            </tr>
        <!-- </tfoot> -->
        
    </table>
    
    
    
    <br /><br />
    <table style="width: 100%;">
        <tr>
            <td>
               This is a computer generated document. No signature required.
            </td>
        </tr>    
    </table>

    

@stop


