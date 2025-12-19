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

$file = (Config::get('constants.GDF_PDF_FILE_PATH') . '/' . urlencode($gdf['gdf_no']) . '.pdf')."#".($gdf['gdf_id']).'#'.$gdf['gdf_no'];
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
                <font size="3"><strong><?php echo strtoupper($gdf['type']); ?> <!-- GOODS DEFECT FORM -->
                
                </strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container">                
                <b>{{ $warehouse['name'] }}</b><br />
                {{ $warehouse['address_1'] }}<br />
                <?php if ($warehouse['address_2'] != "") echo $warehouse['address_2']."<br />"; ?>
                {{ $warehouse['postcode'] }} {{ $warehouse['city'] }}<br />
                {{ $warehouse['state'] }}<br />
                <br />
                <span style="width: 140px;">Tel: {{$warehouse->tel}}</span>  Fax: {{$warehouse->fax}}
                <br />
                Ph: {{$warehouse->pic_contact}}
                <br />
                <br />
                <br />
                <b>To :</b> <br />
                <?php if ($seller != NULL) {
                    echo '<b>'.$seller['company_name'].'</b><br />';
                    echo $seller['address_1'].'<br />';
                    echo ($seller['address_2'] != "") ? $seller['address_2']."<br />":'';
                    echo $seller['postcode'].' '.$seller['city'].'<br />';
                    echo $seller['state'];
                } ?>
                <br />
                <br />
                <b>Attn :</b> <br />
                {{ $seller['attn'] }}
                <br />
            </td>
            <td style="width: 10%;" class="address_container">&nbsp;</td>
            <td style="width: 40%;" class="address_container">
                <span style="width: 100px">GDF No:</span> {{$gdf['gdf_no']}}<br />
                <span style="width: 100px">GDF Date:</span> <?php echo date_format(date_create($po['po_date']), 'Y-m-d'); ?><br />
            </td>
        </tr>

    </table>
    
    <br />
    
    <table border="1" cellpadding="5" style="width: 100%;" class="address_container">
        <thead>
            <tr>
                <th style="width: 5%;" class="address_container">No</th>
                <th style="width: 35%;" class="address_container">Description</th>
                <th style="width: 20%;" class="address_container">Label / SKU</th>
                <th style="width: 15%;" class="address_container">Remark</th>
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
                <td align="center" class="address_container" style="width: 16%;">{{$product->sku}}</td>
                <td align="center" class="address_container" style="width: 7%;">{{$product->remark}}</td>
                <td align="center" class="address_container" style="width: 7%;">{{$product->quantity}}</td>
            </tr>       
        @endforeach
        <tr>
            <td colspan="4" style="border-top: 0px;text-align: left;" class="address_container"><b>Grand Total</b></td>
            <td align="center" class="address_container"><b>{{$total}}</b></td>
        </tr>
        
    </table>
    
    <br /><br />
    <table style="width: 90%;" class="address_container">
        <tr>
            <td>
                <p><b>&#42;&#42; {{$gdf['type']}}</b></p>
            </td>
        </tr>

        <tr>
            <td><br /><br /><b><i>Reason for goods defect:</i></b></td>
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
                <i>Approved By</i>
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
            <td><b>{{ ucfirst($gdf['created_by']) }}</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </table>

    <br />

    

@stop

<a href="<?php echo '/gdf/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
