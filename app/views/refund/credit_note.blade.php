@extends('refund.pdf_header')
@section('content')



    <page_header>
    </page_header>
     <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['cn_no']}})
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
                <font size="3"><strong>CREDIT NOTE</strong></font>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
                {{$display_issuer['issuer_name']}}<br />
                {{$display_issuer['issuer_address_1']}}<br />
                <?php if ($display_issuer['issuer_address_2'] != "") echo $display_issuer['issuer_address_2']."<br />"; ?>
                {{$display_issuer['issuer_address_3']}}<br />
                {{$display_issuer['issuer_address_4']}}<br />
                {{$display_issuer['issuer_tel']}}<br />
                <?php if ($display_issuer['issuer_gst'] != "") echo "(GST Reg No: ".$display_issuer['issuer_gst'].")<br />"; ?>
                <br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                CN No: {{$display_details['cn_no']}}<br />
                CN Date: {{$display_details['cn_date']}}<br />
                Invoice No: {{$display_details['invoice_no']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
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
                <?php if ($display_details['buyer_gst_no'] != "") echo "(GST Reg No: ".$display_details['buyer_gst_no'].")<br /><br />"; ?>
                <?php if ($display_details['attn'] != "") echo "<br />Attention To: ".$display_details['attn']."<br />"; ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <br />
                Reason:<br />
                {{$display_details['reason']}}<br />
            </td>
        </tr>
    </table>

    <br /><br />
    <?php 
        // var_dump($display_details);
        // var_dump($display_product);  
        // var_dump($display_other);
    ?>
    <table border="1" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 14%;" valign="top">Item Code</th>
                <th style="width: 25%;" valign="top">Description</th>
                <th style="width: 20%;" valign="top">Label / SKU</th>
                <th style="width: 6%;" valign="top">Quantity</th>
                <th style="width: 8%;" valign="top">Unit Price<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 9%;" valign="top">Total Excl. GST<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 8%;" valign="top">GST @ {{ $display_details['transaction_gst_rate'] }}%<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 9%;" valign="top">Total Incl. GST<br />({{Config::get("constants.CURRENCY")}})</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($display_product as $product)
            <?php
            	$unit_price 	= $product->price - $product->disc;
                $gst_price      = $unit_price * $product->gst_rate / 100;
                $gst_value      = $gst_price * $product->unit;
                $total_inc_gst  = ($unit_price + $gst_price) * $product->unit;
                $product_name 	= wordwrap($product->name, 43, "<br />\n");
                
            ?>
            <tr>
                <td>&nbsp;{{ $product->sku }}</td>
                <td>&nbsp;{{ $product_name }}</td>
                <td align="center">&nbsp; - </td>
                <td align="center">{{ $product->unit }}</td>
                <td align="center">{{ number_format($unit_price,2) }}</td>
                <td align="center">{{ number_format($unit_price * $product->unit, 2) }}</td>
                <td align="center">{{ number_format($gst_value, 2) }}</td>
                <td align="right">{{ number_format($total_inc_gst, 2) }}&nbsp;</td>
            </tr>
            @endforeach
            @foreach ($display_other as $other)
            <?php
                $gst_price      = $other->price * $other->gst_rate / 100;
                $gst_value      = $gst_price * $other->unit;
                $total_inc_gst  = ($other->price + $gst_price) * $other->unit;
                $other_name		= wordwrap($other->product_name, 45, "<br />\n");
            ?>
            <tr>
                <td align="center">-</td>
                <td>&nbsp;{{ $other_name }}</td>
                <td align="center">&nbsp; - </td>
                <td align="center">&nbsp;{{ $other->unit }}</td>
                <td align="center">{{ number_format($other->price,2) }}</td>
                <td align="center">{{ number_format($other->price * $other->unit, 2) }} </td>
                <td align="center">{{ number_format($gst_value, 2) }}</td>
                <td align="right">{{ number_format($total_inc_gst,2) }}&nbsp;</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="7" align="right"><b>Gand Total ({{Config::get("constants.CURRENCY")}})&nbsp;</b></td>
                <td align="right">{{ number_format($display_details['refund_total'],2) }}&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%;">
        <tr>
            <td>
                <br /><br />
                This is a computer generated document. No signature required.
            </td>
        </tr>
    </table>

@stop
