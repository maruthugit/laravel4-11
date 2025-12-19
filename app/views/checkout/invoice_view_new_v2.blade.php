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

// $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($display_details['invoice_no']) . '.pdf')."#".($display_details['transaction_id']).'#'.$display_details['invoice_no'];
// // $file = ($display_details['transaction_id'])."#".$path;
// $encrypted = Crypt::encrypt($file);
// $encrypted = urlencode(base64_encode($encrypted));
$total_point = 0;
?>


    <page_header>
    </page_header>
    <?php if ($htmlview == false) { ?>
     <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['invoice_no']}})
                </td>
            </tr>
        </table>
    </page_footer>
    <?php } ?>

    <table style="width: 100%;">
       
         <tr>
            <td colspan="3" lign="left" style="width: 100%">
                 <table border="0" style="width: 100%;" cellspacing="3">
                    <tr>
                        <td style="width: 16%">
                            <?php if ($htmlview == false) { ?>
                            <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br />
                            <?php }else{ ?>
                            <img width="100px;" src="/img/invoice_po_logo.jpg" /><br /><br />
                            <?php } ?>
                        </td>
                        <td style="width: 54%" class="address_container">
                            {{$display_issuer['issuer_name']}}<br />
                            {{$display_issuer['issuer_address_1']}}<br />
                            <?php if ($display_issuer['issuer_address_2'] != "") echo $display_issuer['issuer_address_2']."<br />"; ?>
                            {{$display_issuer['issuer_address_3']}}<br />
                            {{$display_issuer['issuer_address_4']}}<br />
                            <?php if ($display_issuer['issuer_gst'] != "") echo "(GST Reg No: ".$display_issuer['issuer_gst'].")<br />"; ?>
                        </td>
                        <td class="address_container">
                            Tel : 03-6261 1272<br />
                            Email : enquiries@tmgrocer.com<br>
                            
                        </td>
                        <td>&nbsp;</td>
                      
                      

                    </tr>
                 </table>
               

            </td>
            
        </tr>
        <tr>
          <td colspan="3" style="width: 100%" class="address_container"><hr></td>
        </tr>
         <tr>
            <td style="width: 50%;">
              <!-- <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br /> -->
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%; text-align: left; padding-bottom: 5px;">
                <font size="4"><strong>TAX INVOICE</strong></font><br />
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                {{$display_details['buyer_name']}}<br />
                {{$display_details['delivery_address_1']}}<br />
                <?php if ($display_details['delivery_address_2'] != "") echo $display_details['delivery_address_2']."<br />"; ?>
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_address_4']}}<br />
                {{$display_details['buyer_email']}}<br /><br /> 
                Date: {{$display_details['transaction_date']}}<br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                Invoice No: {{$display_details['invoice_no']}}<br />
                Invoice Date: {{$display_details['invoice_date']}}<br />
                Reference No: {{$display_details['elevenstr_order_no']}}<br /> 
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
                Payment ID: {{$display_details['payment_id']}}<br />
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                Special instructions:<br />
                {{$display_details['special_instruction']}}<br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                
            </td>
        </tr>
    </table>

    <br /><br />

    <table style="width: 100%;border-top: 1px solid #000;border-bottom: 1px solid #000;" class="address_container">
        <thead>
            <tr>
                <td colspan="8" style="width: 100%;border-top: 0.5px "></td>
            </tr>
            <tr>
                <!-- <th style="width: 16%;" valign="top">Item Code</th> -->
                <th style="width: 28%;" valign="top" class="address_container">Description</th>
                <th style="width: 8%;" valign="top" class="address_container">Quantity</th>
                <th style="width: 12%;" valign="top" class="address_container">Unit Price <?php echo $invoice_bussines_currency; ?><br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
                <th style="width: 9%;" valign="top" class="address_container">Promo Price.<br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
                <th style="width: 12%;" valign="top" class="address_container">Total Value<br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
                <th style="width: 12%;" valign="top" class="address_container">Total Excl. GST<br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
                <th style="width: 9%;" valign="top" class="address_container">GST @ {{$display_trans->gst_rate}}%<br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
                <th style="width: 10%;" valign="top" class="address_container">Total Incl. GST<br />({{ ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null &&  $invoice_bussines_currency !== '') ? $invoice_bussines_currency : Config::get("constants.CURRENCY") }})</th>
            </tr>
            <tr>
                <td colspan="8" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>
        </thead>

        <?php
        $subtotal = 0;
        $coupon_amount = 0;
        $total_excl_gst = 0;
        $total_incl_gst = 0;
        $subtotal_excl_gst = 0;
        $subtotal_gst = 0;
        $subtotal_incl_gst = 0;

        if (isset($display_coupon['coupon_code']))
        {
            $coupon_amount = $display_coupon['coupon_amount'];
        }

        $group_product_price = array();
        $group_product_gst = array();
        ?>

        @foreach($display_product as $product)
        <?php

        // Coupon product discount carry to e37
        $product_disc = $product->disc;

        if ($toCustomer == 1)
        {
            // $product_disc = $product->disc;
            $gst_amount = $product->gst_amount;
        }
        else
        {
            // $product_disc = 0;
            $gst_amount = $product->parent_gst_amount;
        }

        // for package product
        if ($product->product_group != '')
        {
            if(!isset($group_product_price[$product->product_group]))
            {
                $group_product_price[$product->product_group] = 0;
            }
            $group_product_price[$product->product_group] += $product->total;

            if(!isset($group_product_disc[$product->product_group]))
            {
                $group_product_disc[$product->product_group] = 0;
            }
            $group_product_disc[$product->product_group] += $product_disc;

            if(!isset($group_product_gst[$product->product_group]))
            {
                $group_product_gst[$product->product_group] = 0;
            }
            $group_product_gst[$product->product_group] += $gst_amount;
        }
        else // for normal product
        {
            // $subtotal = $subtotal + $product->total;

            $total_excl_gst = $product->total - $product_disc;
            $total_incl_gst = $product->total - $product_disc + $gst_amount;

            $subtotal_excl_gst += $total_excl_gst;
            $subtotal_gst += $gst_amount;
            $subtotal_incl_gst += $total_incl_gst;

            // New Invoice Start 
            $unit_price = 0;
            $promo_price = 0;

            if(isset($product->original_price) && $product->original_price > 0)
            {
                $unit_price  = $product->original_price;
                $promo_price = $product->price;
            }
            else 
            {
                $unit_price = $product->price;
            }

            // New Invoice End
            

        ?>
            <tr>
                 <!--<td style="width: 16%;">*<?php //if($product->gst_amount > 0) echo '*'; ?><!-- {{$product->sku}}</td> -->
                <td style="width: 24%;" class="address_container">
                    <?php if($product->product_name != ''){?> {{$product->product_name}} <?php } elseif($product->description != '') { ?>>{{$product->description}} <?php } else { ?>{{$product->name}}<?php } ?><br />
                    {{$product->price_label}}<br />
                    *<?php if($product->gst == 2) echo '*'; ?>{{$product->sku}}
                </td>
                <td style="width: 6%;" align="center" class="address_container">{{$product->unit}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($unit_price, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($promo_price, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container"><?php   echo number_format($promo_price > 0 ? $promo_price * $product->unit : $unit_price * $product->unit, 2, ".", ","); ?></td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($total_excl_gst, 2, ".", ",")}}</td>
                <td style="width: 7%;" align="center" class="address_container">{{number_format($gst_amount, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($total_incl_gst, 2, ".", ",")}}</td>
            </tr>


        <?php
        }
        ?>
          <tr>
                <td colspan="8" style="height: 5px;" class="address_container">&nbsp;</td>
          </tr>   
        @endforeach

        @foreach($display_group as $group)
            <?php
                $total_excl_gst = $group_product_price[$group->sku]-$group_product_disc[$group->sku];
                $total_incl_gst = $group_product_price[$group->sku]-$group_product_disc[$group->sku]+$group_product_gst[$group->sku];

                $subtotal_excl_gst += $total_excl_gst;
                $subtotal_gst += $group_product_gst[$group->sku];
                $subtotal_incl_gst += $total_incl_gst;
            ?>
            
            <tr>
               <!-- <td style="width: 16%;">*<?php //if($group_product_gst[$group->sku] > 0) echo '*'; ?>{{$group->sku}}</td> -->
                <td style="width: 28%;" class="address_container">
                    {{$group->name}} <br>
                    *<?php if($group_product_gst[$group->sku] > 0) echo '*'; ?>{{$group->sku}}
                </td>
                <td style="width: 8%;" align="center" class="address_container">{{$group->unit}}</td>
                <td style="width: 12%;" align="center" class="address_container">{{number_format($group_product_price[$group->sku]/$group->unit, 2, ".", "")}}</td>
                <td style="width: 12%;" align="center" class="address_container">{{number_format($group_product_price[$group->sku], 2, ".", "")}}</td>
                <!-- <td style="width: 9%;" align="center" class="address_container">{{number_format($group_product_disc[$group->sku], 2, ".", "")}}</td> -->
                <td style="width: 12%;" align="center" class="address_container">{{number_format($total_excl_gst, 2, ".", "")}}</td>
                <td style="width: 9%;" align="center" class="address_container">{{number_format($group_product_gst[$group->sku], 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($total_incl_gst, 2, ".", "")}}</td>
            </tr>
        <?php
            // $subtotal = $subtotal + $group_product_price[$group->sku];
        ?>
        
        @endforeach
            <tr>
                <td colspan="8" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>
      <!--   <tfoot> -->

            <?php

            $delivery_charges = $display_trans->delivery_charges;
            $gst_delivery = $display_trans->gst_delivery;

            $process_fees = $display_trans->process_fees;
            $gst_process = $display_trans->gst_process;

            // to customer with delivery charges and processing fee
            if ($toCustomer == 1)
            {
                $gst_total = $display_trans->gst_total;
            ?>
            <tr>
                <td colspan="5" style="border-bottom: 0px;" class="address_container">
                    Sub-Total
                </td>
                <td align="center" style="border-bottom: 0px;" class="address_container">
                    {{number_format($subtotal_excl_gst, 2, ".", ",")}}
                </td>
                <td align="center" style="border-bottom: 0px;" class="address_container">
                    {{number_format($subtotal_gst, 2, ".", "")}}
                </td>
                <td align="center" style="border-bottom: 0px;" class="address_container">
                    {{number_format($subtotal_incl_gst, 2, ".", ",")}}
                </td>
            </tr>

            <tr>
                <td colspan="5" style="border-top: 0px; border-bottom: 0px;" class="address_container">
                    **Delivery Charges
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;" class="address_container">
                    {{number_format($display_trans->delivery_charges, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;" class="address_container">
                    {{number_format($display_trans->gst_delivery, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;" class="address_container">
                    {{number_format($display_trans->delivery_charges+$display_trans->gst_delivery, 2, ".", "")}}
                </td>
            </tr>

            <tr>
                <td colspan="5" style="border-top: 0px;" class="address_container">
                    **Processing Fee
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format($display_trans->process_fees, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format($display_trans->gst_process, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format($display_trans->process_fees+$display_trans->gst_process, 2, ".", "")}}
                </td>
            </tr>
            <?php
            // if (isset($display_coupon['coupon_code']))
            // {
                ?>
           <!-- <tr>
                <td colspan="5" style="border-top: 0px;" class="address_container">
                    Coupon Code ({{$display_coupon['coupon_code']}}) 
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format(0, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format(0, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                    {{number_format($display_coupon['coupon_amount'], 2, ".", "")}}
                </td>
            </tr> -->

            <?php
             // }
            }
            else
            {
                // to e37 no delivery charges and processing fee
                $gst_total = $subtotal_gst;
                $delivery_charges = 0;
                $process_fees = 0;
            }
             if($display_coupon>0){
                $subtotal_excl_gst += $delivery_charges + $process_fees;
             }
             else{
                $subtotal_excl_gst += $delivery_charges + $process_fees-$display_coupon['coupon_amount'];
             }
             
           // $subtotal_excl_gst += $delivery_charges + $process_fees-$display_coupon['coupon_amount'];
    
            ?>
           
            <tr>
                <td colspan="8" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>    
            <tr>
                <td colspan="5" style="border-top: 0px;" class="address_container"><?php if ($display_points->count() || $display_coupon>0){ echo "Total";}else{ echo "Grand Total"; }?></td>
                <!--<td colspan="5" style="border-top: 0px;" class="address_container">@if ($display_points->count()) Total @else Grand Total @endif</td> -->
                <td align="center" class="address_container">
                    {{number_format($subtotal_excl_gst, 2, ".", ",")}}
                </td>
                <td align="center" class="address_container">
                    <!-- {{number_format($subtotal_gst + $display_trans->gst_delivery + $display_trans->gst_process, 2, ".", "")}} -->
                    {{number_format($gst_total, 2, ".", "")}}
                   
                </td>
                <td align="center" class="address_container">
                    <!-- {{number_format($subtotal_incl_gst + $display_trans->delivery_charges+$display_trans->gst_delivery + $display_trans->process_fees+$display_trans->gst_process, 2, ".", "")}} -->
                    {{number_format($subtotal_excl_gst + $gst_total, 2, ".", ",")}}
                </td>
            </tr>

            <?php
            if (isset($display_coupon['coupon_code']))
            {
            ?>
            <tr>
                <td colspan="5" style="border-top: 0px;" class="address_container">
                    Coupon Code ({{$display_coupon['coupon_code']}}) 
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                  <!--  {{number_format(0, 2, ".", "")}} -->
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                  <!--  {{number_format(0, 2, ".", "")}} -->
                </td>
                <td align="center" style="border-top: 0px;" class="address_container">
                 -{{number_format($display_coupon['coupon_amount'], 2, ".", "")}}
                </td>
            </tr>

            <?php
             }
             ?>


            @foreach ($display_points as $point)
                @if ($point->point > 0)
                
                <?php $total_point = $total_point + number_format($point->amount, 2, '.', '')?>
                <tr>
                    <td colspan="7" style="border-top: 0px;" class="address_container">{{ $point->type }} ({{ $point->point }} points)</td>
                    <td align="center" class="address_container">
                        -{{ number_format($point->amount, 2, '.', '') }}
                    </td>
                </tr>
                @endif
            @endforeach

<!--            @if ($display_points->count())
                <tr>
                    <td colspan="7" style="border-top: 0px;" class="address_container">Grand Total</td>
                    <td align="center" class="address_container">
                        <?php //$totalAmountDue = $subtotal_excl_gst + $gst_total - $point->amount; ?>
                        {{ number_format(abs($totalAmountDue), 2, '.', '') }}
                    </td>
                </tr>
            @endif-->

             <?php
            if (isset($display_coupon['coupon_code']) || $display_points->count() > 0 )
            {
            ?>
                <tr>
                <td colspan="8" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
               </tr>
                <tr>
                    <td colspan="7" style="border-top: 0px;" class="address_container">Grand Total</td>
                    <td align="center" class="address_container">
                        <?php
                      
                        $totalAmountDue = ($subtotal_excl_gst + $gst_total) - $total_point - $display_coupon['coupon_amount']; ?>
                        {{ number_format(abs($totalAmountDue), 2, '.', '') }}
                    </td>
                </tr>
            <? }?>

            <tr>
                <td colspan="8" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>
    </table>

    <br />
    <?php if($buyer_type == 'public'){ ?>
    <table style="width: 100%;" class="address_container"> 
    
        @foreach ($display_earns as $earn)
            @if ($earn->point > 0)
                <tr>
                    <td>You have earned {{ $earn->point }} {{ $earn->type }}.</td>
                </tr>
            @endif
        @endforeach
    </table>
    <?php } ?>
    <br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td>
                Remark:<br />
                <?php
                    if (isset($display_coupon['coupon_code']))
                    {
                        ?>
                        Coupon Code ({{$display_coupon['coupon_code']}}) - {{Config::get("constants.CURRENCY")}}{{number_format($display_coupon['coupon_amount'], 2, ".", "")}}<br />

                        <?php
                    }
                ?>
                * GST 0%<br />
                ** GST {{$display_trans->gst_rate}}%
                <?php
                    if ($invoice_bussines_currency !== 'MYR' &&  $invoice_bussines_currency !== null)
                    {
                        ?>
                       <br /> Exchange Rate (<?php echo $invoice_bussines_currency." ".$invoice_bussines_currency_rate ." = ". $standard_currency." ".$standard_currency_rate; ?>)<br />

                        <?php
                    }
                ?>

                <br /><br />
                This is a computer generated document. No signature required.
                <br />
                Goods sold are not refundable, returnable or exchangeable.
            </td>
        </tr>
    </table>

@stop

<a href="<?php echo '/transaction/download/'.$downloadLink?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>