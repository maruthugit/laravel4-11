<?php if ($htmlview == true) { ?>

<style type="text/css">
    
    body{
        max-width: 900px;
        border: solid 1px #888080;
        padding: 10px;
        margin: auto;
        background-color: #fff;
        }
    span {
        display: inline-block;
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

$file = (Config::get('constants.STOCK_TRANSFER_PDF_FILE_PATH') . '/' . urlencode($st['st_no']) . '.pdf')."#".($st['st_id']).'#'.$st['st_no'];
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
            <td style="width: 10%;">&nbsp;</td>
            <td style="width: 40%;">
                <font size="3"><strong>STOCK TRANSFER</strong></font>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                <span style="font-weight:600">JOCOM MSHOPPING SDN BHD</span><br />
                Unit 9-1, Level 9,<br />
                Tower 3, Avenue 3, Bangsar South,<br />
                No.8, Jalan Kerinchi, 59200 Kuala Lumpur<br />
                <u>operation@jocom.my</u><br />
                Tel: 03-22416637 <span>&nbsp</span> Fax: 03-22423837<br />
                <br />
            </td>
            <td style="width: 10%;" class="address_container">&nbsp;</td>
            <td style="width: 40%;" class="address_container">
                Date: {{$st['delivery_date']}}<br />
                Stock Transfer No: {{$st['st_no']}}<br />
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container">
                <br />
                <u><b>SHIP FROM</b></u><br />
                <b>{{ $warehouse['name'] }}</b><br />
                {{ $warehouse['address_1'] }}<br />
                <?php if ($warehouse['address_2'] != "") echo $warehouse['address_2']."<br />"; ?>
                {{ $warehouse['postcode'] }} {{ $warehouse['city'] }}, {{ $warehouse['state'] }} <br />
                <br />
                ATTN: {{$warehouse['pic_name']}}
                <br />
                Phone: {{$warehouse['pic_contact']}}
                <br />
                <br />
                <br />
            </td>
            <td style="width: 10%;" class="address_container">&nbsp;</td>
            <td style="width: 40%;" class="address_container">
                <br />
                <u><b>SHIP TO</b></u><br />
                <b>{{ $seller['company_name'] }}</b><br />
                {{ $seller['address_1'] }}<br />
                <?php if ($seller['address_2'] != "") echo $seller['address_2']."<br />"; ?>
                {{ $seller['postcode'] }} {{ $seller['city'] }}, {{ $seller['state'] }}<br />
                <br />
                ATTN: {{$seller['attn']}}
                <br />
                Phone: {{$seller['tel']}}
                <br />
                <br />
                <br />
            </td>
        </tr>

    </table>
    
    <br />
    
    <table border="1" cellpadding="5" style="width: 100%;" class="address_container">
        <thead>
            <tr>
                <th style="width: 5%;" class="address_container">No</th>
                <th style="width: 35%;" class="address_container">Description</th>
                <th style="width: 35%;" class="address_container">Label / SKU</th>
                <th style="width: 15%;" class="address_container">Quantity</th>
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
        $total += $product->quantity;
        ?>
            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td align="center" class="address_container" style="width: 6%;">{{ $loopCount }}</td>
                <td align="left" class="address_container" style="width: 23%;">{{ $product->name }}</td>
                <td align="center" class="address_container" style="width: 23%;">{{$product->sku}}</td>
                <td align="center" class="address_container" style="width: 7%;">{{$product->quantity}}</td>
            </tr>       
        @endforeach
        <tr>
            <td colspan="3" style="border-top: 0px;text-align: left;" class="address_container"><b>Grand Total</b></td>
            <td align="center" class="address_container"><b>{{$total}}</b></td>
        </tr>
        
    </table>
    
    <br /><br />
    <table style="width: 90%;" class="address_container">

        <tr>
            <td><br /><b>Remarks:</b><br />
             {{$st['remark']}}

            </td>
        </tr>
        <tr>
            <td><span style="word-break:break-all;">{{$gdf['reason']}}</span></td>
        </tr>
     
    </table>

    <br /><br /><br />
    <table style="width: 90%;" class="address_container">
        <tr>
            <td style="width: 20%;">
                <i>Prepared By</i>
                <br><br><br><br>
            </td>
            <td style="width: 20%;"></td>
            
            <td style="width: 20%;">
                <i>Collected By</i>
                <br><br><br><br>
            </td>
            <td style="width: 20%;"></td>
            <td style="width: 20%;">Received By</td>

        </tr>
        <tr>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
            <td></td>
            <td style="border-bottom:1pt solid black;"></td>
        </tr>
        <tr>
            <td><b>{{ ucfirst($st['created_by']) }}</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><br />Date: <b><?php echo date_format(date_create($st['st_date']), 'd/m/Y'); ?></b></td>
            <td></td>
            <td><br />Date</td>
            <td></td>
            <td><br />Date</td>
        </tr>

    </table>

    <br />

    

@stop

<a href="<?php echo '/stock-transfer/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
