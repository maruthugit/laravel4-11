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
            font-size:12px
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
// print_r($display_trans);

$file = (Config::get('constants.PURCHASE_PDF_FILE_PATH') . '/' . urlencode($display_details['invoice_no']) . '.pdf')."#".($display_details['transaction_id']).'#'.$display_details['invoice_no'];
// $file = ($display_details['transaction_id'])."#".$path;
$encrypted = Crypt::encrypt($file);
$encrypted = urlencode(base64_encode($encrypted));
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
                            Tien Ming Distribution Sdn Bhd (1537285-T)<br />                 
                            10, Jalan Str 1, Saujana Teknologi Park, <br />
                            48000 Rawang,<br /> Selangor, Malaysia.<br />
                            
                            <br />
                        </td>
                        <td class="address_container">
                            Tel: 03-6734 8744<br />
                            Email: enquiries@tmgrocer.com<br>
                            
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
                <font size="4"><strong>P/L Report</strong></font><br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;" class="address_container">
                <!-- {{$display_details['buyer_name']}}<br /> -->
                {{$display_details['delivery_name']}}<br />
                {{$display_details['delivery_address_1']}}<br />
                <?php if ($display_details['delivery_address_2'] != "") echo $display_details['delivery_address_2']."<br />"; ?>
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_address_4']}}<br />
                <!-- {{$display_details['buyer_email']}}<br /><br />  -->
                Date: {{$display_details['transaction_date']}}<br />
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                Invoice No: {{$display_details['invoice_no']}}<br />
                Invoice Date: {{$display_details['invoice_date']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}
            </td>
        </tr>

        <tr>
            <td style="width: 50%;" class="address_container">
                <!-- Special instructions:<br />
                {{$display_details['special_instruction']}}<br /> -->
            </td>
            <td style="width: 20%;" class="address_container">&nbsp;</td>
            <td style="width: 30%;" class="address_container">
                
            </td>
        </tr>
    </table>

    <br /><br />

    <table  style="width: 100%;border-top: 1px solid #000;border-bottom: 1px solid #000;" class="address_container">
        <thead>
            <tr>
                <td colspan="7" style="width: 100%;border-top: 0.5px "></td>
            </tr>
            <tr>
                <th style="width: 16%; text-align: left;" valign="top" lass="address_container">Date</th>
                <th style="width: 24%; text-align: left;" valign="top" class="address_container">Receipt Nos</th>
                <th style="width: 30%; text-align: left;" valign="top" class="address_container">Supplier Name</th>
                <th style="width: 10%;" valign="top" class="address_container">Purchase Price<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" valign="top" class="address_container">Selling Price.<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" valign="top" class="address_container">Selling Total Amount<br />({{Config::get("constants.CURRENCY")}})</th>
                <th style="width: 10%;" valign="top" class="address_container">Purchase Total Amount<br />({{Config::get("constants.CURRENCY")}})</th>
                
            </tr>
            <tr>
                <td colspan="7" style="width: 100%;border: 0.5px " class="address_container"></td>
            </tr>
        </thead>

        <?php
        $costprice = 0;
        $total_amount = 0;
        $total_selling = 0;
        $subtotal_excl_gst = 0;
        $subtotal_gst = 0;
        $subtotal_incl_gst = 0;



        ?>

        @foreach($display_product as $product)
        <?php

        // Coupon product discount carry to e37

       

        if ($product->seller_company != '')
        {
           
            // $subtotal = $subtotal + $product->total;

            $total_amount = $product->amount;

            $total_amount += $total_amount;

            $total_selling = $product->total;

            $total_selling += $total_selling;
           

            // New Invoice Start 
            $unit_price = 0;
            $promo_price = 0;


            // New Invoice End

        ?>
            <tr>
                <td style="width: 16%;">{{$product->date_of_purchase}}</td> 
                <td style="width: 24%;" class="address_container">{{$product->refno}}</td>
                <td style="width: 30%;" class="address_container">{{$product->seller_company}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($product->costprice, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($product->Sellingprice, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($product->total, 2, ".", "")}}</td>
                <td style="width: 10%;" align="center" class="address_container">{{number_format($product->amount, 2, ".", "")}}</td>
               
            </tr>


        <?php
        }
        ?>
          <tr>
                <td colspan="7" style="height: 5px;" class="address_container">&nbsp;</td>
          </tr>   
        @endforeach

       
            <tr>
                <td colspan="7" style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>
   

           
           
            <tr>
                <td colspan="7"  style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>   
            <tr>
                <td  colspan="4" style="border-top: 0px;" class="address_container">Sub-Total</td>
                <td align="center" class="address_container">
                </td>
                <th align="center" class="address_container">
                   {{number_format($total_selling, 2, ".", "")}}
                </th>
                <th align="center" class="address_container">
                   
                    {{number_format($total_amount, 2, ".", "")}}

                </th>
            </tr>





            <tr>
                <td colspan="7"  style="width: 100%;border-bottom: 0.5px " class="address_container"></td>
            </tr>
    </table>
     <br />
    <table style="width: 100%;" class="address_container">
        <tr>
            
                <br /><br />
                This is a computer generated document. No signature required.
                <br />
                Goods sold are not refundable, returnable or exchangeable.
            </td>
            
        </tr>
    </table>
   

@stop

<a href="<?php echo '/transaction/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>