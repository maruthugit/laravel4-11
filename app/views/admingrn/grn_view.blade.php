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

$file = (Config::get('constants.GRN_PDF_FILE_PATH') . '/' . urlencode($grn['grn_no']) . '.pdf')."#".($grn['grn_id']).'#'.$grn['grn_no'];
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
                <font size="3"><strong>GOODS RECEIVED NOTE</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container">                
                <span style="font-weight:600">{{ $issuer['issuer_name'] }}</span><br />
                {{ $issuer['issuer_address_1'] }}<br />
                <?php if ($issuer['issuer_address_2'] != "") echo $issuer['issuer_address_2']."<br />"; ?>
                {{ $issuer['issuer_address_3'] }}<br />
                {{ $issuer['issuer_address_4'] }}<br />
                {{ $issuer['issuer_tel'] }}<br />
                <br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                GRN No: {{ $grn['grn_no'] }}<br />
                GRN Date: <?php echo date_format(date_create($grn['grn_date']), 'd/m/Y'); ?><br />
                DO No: {{ $grn['seller_do_no'] }}<br />
                PO No: {{ $grn['po_no'] }}<br />
                Delivery Person/Driver: {{ $grn['seller_driver_name'] }}<br />
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
                <span style="font-weight: bold; font-style: italic;"><u>Receive At:</u></span><br />
                <b>{{ $warehouse['name'] }}<br />
                {{ $warehouse['address_1'] }}<br />
                <?php if ($warehouse['address_2'] != "") echo $warehouse['address_2']."<br />"; ?>
                {{ $warehouse['postcode'] }} {{ $warehouse['city'] }}<br />
                {{ $warehouse['state'] }}<br />
                </b>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                <br />
                Attn: {{ $seller['attn'] }}<br />
                Tel: {{ $seller['tel'] }}
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
                <th style="width: 5%;" class="address_container">No</th>
                <th style="width: 25%;" class="address_container">Description</th>
                <th style="width: 20%;" class="address_container">Label/SKU</th>
                <th style="width: 10%;" class="address_container">Quantity</th>
                <th style="width: 10%;" class="address_container">UOM</th>
                <th style="width: 10%;" class="address_container">FOC Quantity</th>
                <th style="width: 10%;" class="address_container">FOC UOM</th>
                <th style="width: 10%;" class="address_container">REMARKS</th>
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


        $total_gst += $gst_seller;
        $total += $product->total;

        ?>
            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td align="center" class="address_container" style="width: 5%;">{{ $loopCount }}</td>
                <td align="center" class="address_container" style="width: 25%;">{{ $product->product_name }}</td>
                <td align="center" class="address_container" style="width: 20%;">
                    <?php if ($product->price_label != "") { ?>{{$product->price_label}} <?php } else {?>{{$product->sku}} <?php } ?>
                </td>
                <td align="center" class="address_container" style="width: 10%;">{{$product->quantity}}</td>
                <td align="center" class="address_container" style="width: 10%;">{{$product->uom}}</td>
                <td align="center" class="address_container" style="width: 10%;">{{$product->foc_qty}}</td>
                <td align="center" class="address_container" style="width: 10%;">{{$product->foc_uom}}</td>
                <td align="center" class="address_container" style="width: 10%;">{{$product->remarks}}</td>

            </tr>       
        @endforeach
        <!-- </tfoot> -->
        
    </table>
    
    <br /><br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td>
                <span style="font-size: 20"><b><u>Remarks / Note</u></b></span>
                <br>
                <span>{{ $grn['remarks'] }}</span>
            </td>
        </tr>
    </table>

    <br><br><br>
    <table>
        <tr><td>The undersigned acknowledges receipt of the goods which are described on the purchase order</td></tr>
    </table>
    <br><br><br>

    <br />
    <table style="width: 100%;" class="address_container">
        <tr>
            <td style="width: 20%;">
                <i>Received By</i>
                <br><br><br><br>
            </td>
            <td style="width: 10%;">&nbsp;</td>
            <td style="width: 20%;">
                <i>Delivered By</i>
                <br><br><br><br>
            </td>
            <td style="width: 10%;">&nbsp;</td>
            <td style="width: 20%;">
                <i>Delivered By</i>
                <br><br><br><br>
            </td>
            <td style="width: 10%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
        </tr>
        <tr>
            <td><b>{{ $grn['received_by'] }}</b></td>
            <td></td>
            <td><b>{{ $grn['delivered_by'] }}</b></td>
            <td></td>
            <td><b>{{ $grn['verified_by'] }}</b></td>
        </tr>

    </table>

    <br />

    

@stop

<a href="<?php echo '/admingrn/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
