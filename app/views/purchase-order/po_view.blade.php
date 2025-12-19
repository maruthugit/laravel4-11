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

$file = (Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($po['po_no']) . '.pdf')."#".($po['po_id']).'#'.$po['po_no'];
$encrypted = Crypt::encrypt($file);
$encrypted = urlencode(base64_encode($encrypted));

?>
    <page_header>
    </page_header>
    <?php if ($htmlview == false) { ?>
    <!-- <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{ $display_details['po_no'] }})
                </td>
            </tr>
        </table>
    </page_footer> -->
    <?php } ?>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <?php if ($htmlview == false) { ?>
                <img width="100px;" src="{{public_path('/img/invoice_po_logo.jpg')}}" /><br /><br />
                <?php }else{ ?>
                <img width="100px;" src="/img/invoice_po_logo.jpg" /><br /><br />
                <?php } ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>{{$po['po_type']}}</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container">                
                <span style="font-weight:600"><strong>{{ $issuer['issuer_name'] }}</strong></span><br />
                {{ $issuer['issuer_address_1'] }}<br />
                <?php if ($issuer['issuer_address_2'] != "") echo $issuer['issuer_address_2']."<br />"; ?>
                {{ $issuer['issuer_address_3'] }}<br />
                {{ $issuer['issuer_address_4'] }}<br />
                {{ $issuer['issuer_tel'] }}<br />
                <br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                PO No: {{$po['po_no']}}<br />
                PO Date: <?php echo date_format(date_create($po['po_date']), 'Y-m-d'); ?><br />
                Payment terms: {{ $po['payment_terms'] }}<br />
                Delivery Date: <?php echo date_format(date_create($po['delivery_date']), 'Y-m-d'); ?><br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;" class="address_container">
                <br />
                <span style="font-weight:bold">{{ $seller['company_name'] }}</span><br />
                {{ $seller['address1'] }}<br />
                <?php if ($seller['address2'] != "") echo $seller['address2']."<br />"; ?>
                {{ $seller['postcode']}} {{$seller['city'] }}<br />
                {{ $seller['state']}}<br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                <br>
                <span style="font-weight: bold; font-style: italic;"><u>DELIVER TO:</u></span><br />
                {{ $warehouse['address_1'] }}<br />
                <?php if ($warehouse['address_2'] != "") echo $warehouse['address_2']."<br />"; ?>
                {{ $warehouse['postcode'] }} {{ $warehouse['city'] }}<br />
                {{ $warehouse['state'] }}<br />
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                <br />
                ATTN: {{ $seller['attn'] }}<br />
                TEL: {{ $seller['tel'] }}
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                <br />
                PIC: {{ $warehouse['pic_name'] }}<br />
                TEL: {{ $warehouse['pic_contact'] }}
            </td>
        </tr>
    </table>
    
    <br />
    
    <table border="1" cellpadding="5" style="width: 100%;" class="address_container">
        <thead>
            <tr>
                <th style="width: 6%;" class="address_container">No</th>
                <th style="width: 23%;" class="address_container">Description</th>
                <th style="width: 23%;" class="address_container">Label / SKU</th>
                <th style="width: 7%;" class="address_container">Quantity</th>
                <th style="width: 11%;" class="address_container">Unit Price / carton ({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" class="address_container">Total Excl. SST ({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" class="address_container">SST ({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" class="address_container">Total Incl. SST (({{Config::get("constants.CURRENCY")}})</th>
            </tr>
        </thead>
        
        
        <?php 
        $loopCount = 0;
        $total = 0;
        $total_gst = 0;
        $coupon_amt = 0;
        $value_with_gst = 0;

        ?>
        @foreach($products as $product)
        <?php 
        $loopCount++;


        $total += $product->total;
        $total_sst += $product->sst;
        $grand_total += $product->total + $product->sst;

        ?>
            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td align="center" class="address_container" style="width: 6%;">{{ $loopCount }}</td>
                <td align="left" class="address_container" style="width: 23%;">{{ $product->product_name }}</td>
                <td align="center" class="address_container" style="width: 23%;">
                    <?php if ($product->price_label != "") { ?>{{$product->price_label}} <?php } else {?>{{$product->sku}} <?php } ?>
                </td>
                <td align="center" class="address_container" style="width: 7%;">{{number_format($product->quantity, 2, ".", ",")}}</td>
                <td align="right" class="address_container" style="width: 11%;">{{number_format($product->price, 2, ".", ",")}}</td>
                <td align="right" class="address_container" style="width: 10%;">{{number_format($product->total, 2, ".", ",")}}</td>
                <td align="right" class="address_container" style="width: 10%;">{{number_format($product->sst, 2, ".", ",")}}</td>
                <td align="right" class="address_container" style="width: 10%;">{{number_format($product->total + $product->sst, 2, ".", ",")}}</td>
            </tr>       
        @endforeach
        <?php 
            $discounted_amount = number_format(($total * $po['discount_percent'])/100, 2, ".", ",");
            $discounted_sst = number_format(($total_sst * $po['discount_percent'])/100, 2, ".", ",");
            $discounted_amount_sst = number_format(($grand_total * $po['discount_percent'])/100, 2, ".", ",");
        ?>
        @if ($po['discount_percent'] != 0 && $po['discount_total'] != 0.00)
            <tr>
                <td colspan="5" style="border-top: 0px;text-align: left;" class="address_container"><b>Discount {{$po['discount_percent']}}%</b></td>
                <td align="right" class="address_container">{{$discounted_amount}}</td>
                <td align="right" class="address_container">{{$discounted_sst}}</td>
                <td align="right" class="address_container"><b>{{$discounted_amount_sst }}</b></td>
            </tr>
            <tr>
                <td colspan="5" style="border-top: 0px;text-align: left;" class="address_container"><b>Grand Total</b></td>
                <td align="right" class="address_container"><b>{{number_format($total-$discounted_amount, 2, ".", ",")}}</b></td>
                <td align="right">{{number_format($total_sst-$discounted_sst, 2, ".", ",")}}</td>
                <td align="right" class="address_container"><b>{{number_format($po['discount_total'], 2, ".", ",")}}</b></td>
            </tr>
        @else
            <tr>
                <td colspan="5" style="border-top: 0px;text-align: left;" class="address_container"><b>Grand Total</b></td>
                <td align="right" class="address_container"><b>{{number_format($total, 2, ".", ",")}}</b></td>
                <td align="right">{{number_format($total_sst, 2, ".", ",")}}</td>
                <td align="right" class="address_container"><b>{{number_format($total+$total_sst, 2, ".", ",")}}</b></td>
            </tr>
        @endif
            
        <!-- </tfoot> -->
        
    </table>
    
    <br /><br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td>
                <p><span>&#42;</span> Kindly Take Note</p>
                @if ($po['remark'] != '' || $po['remark'] != null)
                    <p>{{ $po['remark'] }}</p>
                @endif
            </td>
        </tr>
        <tr>
            <td>
                <h4><u>Delivery Details</u></h4>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width: 100%;" class="address_container">
                    <tr>
                        <td style="width: 30%;"><h4>Monday to Friday<br><br>Saturday</h4></td>
                        <td><h4>8 am - 12 pm<br>2 pm - 5 pm<br>8 am - 12 pm ( SMALL QUANTITY ONLY )</h4></td>
                    </tr>
                    
                </table>
            </td>
            
        </tr>
     
    </table>

    <br /><br /><br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td style="width: 25%;">
                <i>Prepared By</i>
                <br><br><br><br>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 25%;">
                <i>Checked By</i>
                <br><br><br><br>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 25%;">
                <i>Approved By</i>
                <br><br><br><br>
            </td>
            <td style="width: 15%;"></td>
        </tr>
        <tr>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
        </tr>
        <tr>
            <td><b>{{ ucfirst($po['created_by']) }}</b></td>
            <td></td>
            <td><b>Chean.S</b></td>
            <td></td>
            <td><b>Sew Wen Kuan <!-- {{ $po['manager'] }}--></b></td>
        </tr>

    </table>

    <br />

    

@stop

<a href="<?php echo '/purchase-order/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
