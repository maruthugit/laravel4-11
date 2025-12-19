@extends('checkout.pdf_header')
@section('content')



    <page_header>
    </page_header>
     <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['invoice_no']}})
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
                <font size="3"><strong>TAX INVOICE</strong></font>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
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
                Invoice No: {{$display_details['invoice_no']}}<br />
                Invoice Date: {{$display_details['invoice_date']}}<br />
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
                Payment ID: {{$display_details['payment_id']}}<br />
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
                <br />
                {{$display_details['buyer_name']}}<br />
                {{$display_details['delivery_address_1']}}<br />
                <?php if ($display_details['delivery_address_2'] != "") echo $display_details['delivery_address_2']."<br />"; ?>
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_address_4']}}<br />
                {{$display_details['buyer_email']}}<br />
                Date: {{$display_details['transaction_date']}}<br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <br />
                Special instructions:<br />
                {{$display_details['special_instruction']}}<br />
            </td>
        </tr>
    </table>

    <br /><br />

    <table border="1" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 16%;" valign="top">Item Code</th>
                <th style="width: 24%;" valign="top">Description</th>
                <th style="width: 6%;" valign="top">Quantity</th>
                <th style="width: 10%;" valign="top">Unit Price<br />(RM)</th>
                <th style="width: 10%;" valign="top">Gross Value<br />(RM)</th>
                <th style="width: 7%;" valign="top">Disc.<br />(RM)</th>
                <th style="width: 10%;" valign="top">Total Excl. GST<br />(RM)</th>
                <th style="width: 7%;" valign="top">GST @ {{$display_trans->gst_rate}}%<br />(RM)</th>
                <th style="width: 10%;" valign="top">Total Incl. GST<br />(RM)</th>
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
            $group_product_disc[$product->product_group] += ($product->disc);

            if(!isset($group_product_gst[$product->product_group]))
            {
                $group_product_gst[$product->product_group] = 0;
            }
            $group_product_gst[$product->product_group] += ($product->gst_amount);
        }
        else // for normal product
        {
            // $subtotal = $subtotal + $product->total;

            $total_excl_gst = $product->total - $product->disc;
            $total_incl_gst = $product->total - $product->disc + $product->gst_amount;

            $subtotal_excl_gst += $total_excl_gst;
            $subtotal_gst += $product->gst_amount;
            $subtotal_incl_gst += $total_incl_gst;

        ?>
            <tr>
                <td style="width: 16%;">*<?php if($product->gst_amount > 0) echo '*'; ?>{{$product->sku}}</td>
                <td style="width: 24%;">
                    {{$product->name}}<br />
                    {{$product->price_label}}
                </td>
                <td style="width: 6%;" align="center">{{$product->unit}}</td>
                <td style="width: 10%;" align="center">{{number_format($product->price, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($product->total, 2, ".", "")}}</td>
                <td style="width: 7%;" align="center">-{{number_format($product->disc, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($total_excl_gst, 2, ".", "")}}</td>
                <td style="width: 7%;" align="center">{{number_format($product->gst_amount, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($total_incl_gst, 2, ".", "")}}</td>
            </tr>

        <?php
        }
        ?>
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
                <td style="width: 16%;">*<?php if($group_product_gst[$group->sku] > 0) echo '*'; ?>{{$group->sku}}</td>
                <td style="width: 24%;">
                    {{$group->name}}
                </td>
                <td style="width: 6%;" align="center">{{$group->unit}}</td>
                <td style="width: 10%;" align="center">{{number_format($group_product_price[$group->sku]/$group->unit, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($group_product_price[$group->sku], 2, ".", "")}}</td>
                <td style="width: 7%;" align="center">-{{number_format($group_product_disc[$group->sku], 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($total_excl_gst, 2, ".", "")}}</td>
                <td style="width: 7%;" align="center">{{number_format($group_product_gst[$group->sku], 2, ".", "")}}</td>
                <td style="width: 10%;" align="center">{{number_format($total_incl_gst, 2, ".", "")}}</td>
            </tr>
        <?php
            // $subtotal = $subtotal + $group_product_price[$group->sku];
        ?>
        @endforeach

      <!--   <tfoot> -->
            <tr>
                <td colspan="6" style="border-bottom: 0px;">
                    Sub-Total
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($subtotal_excl_gst, 2, ".", "")}}
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($subtotal_gst, 2, ".", "")}}
                </td>
                <td align="center" style="border-bottom: 0px;">
                    {{number_format($subtotal_incl_gst, 2, ".", "")}}
                </td>
            </tr>

            <tr>
                <td colspan="6" style="border-top: 0px; border-bottom: 0px;">
                    **Delivery Charges
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;">
                    {{number_format($display_trans->delivery_charges, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;">
                    {{number_format($display_trans->gst_delivery, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px; border-bottom: 0px;">
                    {{number_format($display_trans->delivery_charges+$display_trans->gst_delivery, 2, ".", "")}}
                </td>
            </tr>

            <tr>
                <td colspan="6" style="border-top: 0px;">
                    **Processing Fee
                </td>
                <td align="center" style="border-top: 0px;">
                    {{number_format($display_trans->process_fees, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;">
                    {{number_format($display_trans->gst_process, 2, ".", "")}}
                </td>
                <td align="center" style="border-top: 0px;">
                    {{number_format($display_trans->process_fees+$display_trans->gst_process, 2, ".", "")}}
                </td>
            </tr>

            <tr>
                <td colspan="6" style="border-top: 0px;">@if ($display_points->count()) Total @else Grand Total @endif</td>
                <td align="center">
                    {{number_format($subtotal_excl_gst + $display_trans->delivery_charges + $display_trans->process_fees, 2, ".", "")}}
                </td>
                <td align="center">
                    <!-- {{number_format($subtotal_gst + $display_trans->gst_delivery + $display_trans->gst_process, 2, ".", "")}} -->
                    {{number_format($display_trans->gst_total, 2, ".", "")}}
                </td>
                <td align="center">
                    <!-- {{number_format($subtotal_incl_gst + $display_trans->delivery_charges+$display_trans->gst_delivery + $display_trans->process_fees+$display_trans->gst_process, 2, ".", "")}} -->
                    {{number_format($display_trans->total_amount - $coupon_amount + $display_trans->gst_total, 2, ".", "")}}
                </td>
            </tr>

            @foreach ($display_points as $point)
                <tr>
                    <td colspan="8" style="border-top: 0px;">{{ $point->type }} ({{ $point->point }} points)</td>
                    <td align="center">
                        -{{ number_format($point->amount, 2, '.', '') }}
                    </td>
                </tr>
            @endforeach

            @if ($display_points->count())
                <tr>
                    <td colspan="8" style="border-top: 0px;">Grand Total</td>
                    <td align="center">
                        <?php $totalAmountDue = $display_trans->total_amount - $coupon_amount + $display_trans->gst_total - $point->amount; ?>
                        {{ number_format(abs($totalAmountDue), 2, '.', '') }}
                    </td>
                </tr>
            @endif

    </table>

    <br />
    <table style="width: 100%;">
        @foreach ($display_earns as $earn)
            @if ($earn->point > 0)
                <tr>
                    <td>You have earned {{ $earn->point }} {{ $earn->type }}.</td>
                </tr>
            @endif
        @endforeach
    </table>

    <br />
    <table style="width: 100%;">
        <tr>
            <td>
                Remark:<br />
                <?php
                    if (isset($display_coupon['coupon_code']))
                    {
                        ?>
                        Coupon Code ({{$display_coupon['coupon_code']}}) - RM{{number_format($display_coupon['coupon_amount'], 2, ".", "")}}<br />

                        <?php
                    }
                ?>
                * GST 0%<br />
                ** GST {{$display_trans->gst_rate}}%

                <br /><br />
                This is a computer generated document. No signature required.
                <br />
                Goods sold are not refundable, returnable or exchangeable.
            </td>
        </tr>
    </table>

@stop
