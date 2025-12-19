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
            font-size:11px;
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
        .tb{
            float: right;
        }
        
        }

        .w3-badge,.w3-tag{
            background-color:#000;
            color:#fff;
            display:inline-block;
            padding-left:8px;
            padding-right:8px;
            font-weight:bold;
            text-align:center;

        }
        /*.tb{
            float: right;
        }*/

        .w3-badge{border-radius:50%}

        .w3-red,.w3-hover-red:hover{color:#fff!important;background-color:#f44336!important}

        .w3-black,.w3-hover-black:hover{color:#fff!important;background-color:#000!important}
        .w3-border-black,.w3-hover-border-black:hover{border-color:#000!important}

        .w3-table-all{border:1px solid #ccc}
</style>




<?php }else{?>
<style>
.tb{

    position: relative;
    float:right;

}
</style>
<?php }?>
@extends('checkout.pdf_header')
@section('content')
<?php 

$file = (Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($display_details['do_no']) . '.pdf')."#".($display_details['transaction_id']);
$encrypted = Crypt::encrypt($file);
$encrypted = urlencode(base64_encode($encrypted));

//  print_r($display_details);

?> 
    <page_header>
    </page_header>
    <?php if ($htmlview == false) { ?>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['do_no']}})
                </td>
            </tr>
        </table>
    </page_footer>
    <?php } ?>
    <table style="width: 100%;">
    <?php 
    if ($display_details['qr_code']!='') {

    ?>
        <tr >
            <td><br><?php echo $display_details['barcode']; ?></td>
            <td>&nbsp;</td>
            <td>
                <?php if ($htmlview == false) { ?>
                    <img width="80px;" style="float: right;padding: 5px;" src="images/qrcode/{{$display_details['qr_code']}}" />
                <?php }else{ ?>
                 <img width="80px;" style="float: right;padding: 5px;" src="/images/qrcode/{{$display_details['qr_code']}}" />
                 <?php } ?>
            </td>                   
        </tr>
<?php } ?>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td valign="right" style="text-align: right;">
              <input type="hidden" name="idPrintcount" id="idPrintcount" value="<?php echo $display_details['doprint_count']; ?>">
              <input type="hidden" name="idTransaction" id="idTransaction" value="<?php echo $display_details['transaction_id']; ?>">
                <table cellpadding="1"  cellspacing="0" style="text-align: center;solid 1px #000000;width: 30px; float: right;margin-right: 8px;"  >
                    <tr>
                    <?php
                     if ($display_details['doprint_count'] != 0){
                    ?>
                        <td style="text-align: center;border: 1px solid;width: 30px;padding: 2px;">
                        <?php echo $display_details['doprint_count']; ?>                 
                           <input type="hidden" name="idPrintcount" id="idPrintcount" value="<?php echo $display_details['doprint_count']; ?>">
                        </td>
                    <?php } ?>
                        <td style="text-align: center;border: 1px solid;width: 30px;padding: 2px;">
                            <?php
                                if ($display_details['delivery_time'] == '24 hours') {
                                    $time = $display_details['transaction_date'] + 1;
                                    $date = explode("-",$display_details['transaction_date']);
                                       
                                        if ($time > '31') {

                                            switch ($time) {
                                                case 32:
                                                    $time = '1';
                                                    break;
                                                case 33:
                                                    $time = '2';
                                                    break;
                                                case 34:
                                                    $time = '3';
                                                    break;
                                                case 35:
                                                    $time = '4';
                                                    break;
                                                case 36:
                                                    $time = '5';
                                                    break;
                                                case 37:
                                                    $time = '6';
                                                    break;
                                                case 38:
                                                    $time = '7';
                                                    break;
                                                case 39:
                                                    $time = '8';
                                                    break;
                                                case 40:
                                                    $time = '9';
                                                    break;
                                                case 41:
                                                    $time = '10';
                                                    break;
                                                case 42:
                                                    $time = '11';
                                                    break;
                                                case 43:
                                                    $time = '12';
                                                    break;
                                                default:
                                                    break;
                                            }

                                        echo $time;

                                    }
                                    else{
                                        echo $time;
                                    }

                                }
                                else{

                                    $time = $display_details['delivery_time'] + $display_details['transaction_date'];
                                    // $time2 = explode("-",$display_details['delivery_time']);
                                    $date = explode("-",$display_details['transaction_date']);
                                    // echo $display_details['transaction_date'];
                                    // $from = $time2[0] + $date[0];
                                    // $to = $time2[1] + $date[0];
                                    if ($time > '31') {

                                        switch ($time) {
                                            case 32:
                                                $time = '1';
                                                break;
                                            case 33:
                                                $time = '2';
                                                break;
                                            case 34:
                                                $time = '3';
                                                break;
                                            case 35:
                                                $time = '4';
                                                break;
                                            case 36:
                                                $time = '5';
                                                break;
                                            case 37:
                                                $time = '6';
                                                break;
                                            case 38:
                                                $time = '7';
                                                break;
                                            case 39:
                                                $time = '8';
                                                break;
                                            case 40:
                                                $time = '9';
                                                break;
                                            case 41:
                                                $time = '10';
                                                break;
                                            case 42:
                                                $time = '11';
                                                break;
                                            case 43:
                                                $time = '12';
                                                break;
                                            default:
                                                break;
                                        }

                                        echo $time;

                                    }
                                    else{
                                        echo $time;
                                    }
                                }
                            ?>    
                        </td>
                        <td style="text-align: center;border: 1px solid;width: 30px;padding: 2px;"><strong><?php 
                        
                        if($display_details['delivery_area_type'] == "house"){
                            echo "H";
                        }elseif($display_details['delivery_area_type'] == "office"){
                            echo "O";
                        }else{
                            echo "";
                        }
                        
                        ?></strong>
                        </td>
                    </tr>
                </table>
              
            </td> 
            
            
        </tr>

        <tr>
            <td style="width: 50%;">
                <?php if ($htmlview == false) { ?>
                    <img width="100px;" src="img/invoice_po_logo.jpg" /><br />
                <?php }else{ ?>
                 <img width="100px;" src="/img/invoice_po_logo.jpg" /><br />
                 <?php } ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>DELIVERY ORDER</strong></font>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="right">
                <?php if ($delivery_type == 'express') { ?>
                <img width="125px;" src="{{asset('img/express.jpg')}}" />
                <?php }else{ ?>
                <!--<img width="125px;" src="{{asset('/img/standard.jpg')}}" />-->
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;" class="address_container" valign="middle">
                Tien Ming Distribution Sdn Bhd (1537285-T)<br />                 
                10, Jalan Str 1, Saujana Teknologi Park, <br />
                48000 Rawang, Selangor, Malaysia.<br />
                Tel: 03-6734 8744<br />
                Email: enquiries@tmgrocer.com<br>
                <br />
            </td>
            <td style="width: 20%;" class="address_container" >&nbsp;</td>
            <td style="width: 30%;" class="address_container"  valign="middle">
                DO No: {{$display_details['do_no']}}<br />
                DO Date: {{$display_details['do_date']}}<br />
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
                Payment ID: {{$display_details['payment_id']}}<br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;" class="address_container" >
                <br />
                {{$display_details['delivery_name']}}<br />
                {{$display_details['delivery_address_1']}}<br />
                <?php if ($display_details['delivery_address_2'] != "") echo $display_details['delivery_address_2']."<br />"; ?>
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_address_4']}}<br />
                {{$display_details['delivery_contact_no']}}<br />
                Date {{$display_details['transaction_date']}} <br />
            </td>
            <td style="width: 20%;" class="address_container" >&nbsp;</td>
            <td style="width: 30%;" class="address_container"  valign="middle">
                Special instructions:<br />
                {{$display_details['special_instruction']}}<br />
            </td>
        </tr>
    </table>
    
    <br /><br />
    
    <table border="1" scellpadding="1"  cellspacing="0" style="width: 100%; border:solid 1px #000000;" >
        <thead>         
            <tr>
                <th style="width: 22%;" class="address_container" >Item Code</th>
                <th style="width: 35%;" class="address_container" >Description</th>
                <th style="width: 30%;" class="address_container" >Label</th>
                <th style="width: 10%;" class="address_container" >Quantity</th>
                <th style="width: 3%;" class="address_container" ></th>
            </tr>
        </thead>

        <?php
        
        if($deliveryservice){
            foreach ($display_delivery_service_items as $skey => $svalue) {
            
        ?>
            <tr>
                <td class="address_container"  style="width: 22%;">{{$svalue->item_sku}}</td>
                <td class="address_container"  style="width: 35%;">
                    {{$svalue->item_description}}
                </td>
                <td class="address_container"  style="width: 30%;">{{$svalue->item_label}}</td>
                <td class="address_container"  style="width: 10%;" align="center">{{$svalue->quantity}}</td>
                <?php if ($htmlview == false) { ?>
                <td class="address_container"  style="width: 3%;" align="center"><img src="img/checkboxpic.png" /></td>
                <?php }else{ ?>
                <td class="address_container"  style="width: 3%;" align="center"><img src="/img/checkboxpic.png" /></td>
                <?php } ?>
            </tr>
        <?php  } 
        }else{
         
        $subtotal = 0;
        $group_product_price = array();
        ?>
        <?php 
        
        $list_sku = array(); 
        // GROUPING SKU
        foreach($display_product as $product){
            if( array_key_exists($product->sku, $list_sku) ){
                $list_sku[$product->sku] = $list_sku[$product->sku] +1;
            }else{
               $list_sku[$product->sku] = 1;
            }
        }
        
        
          $repeater_sku = array(); 
        ?>

        @foreach($display_product as $product)
        <?php       
        
        // GROUPING SKU
        if( array_key_exists($product->sku, $repeater_sku) ){
            $repeater_sku[$product->sku] = $list_sku[$product->sku] +1;
        }else{
           $repeater_sku[$product->sku] = 1;
        }
     
        
        // for package product
        if ($product->product_group != '')
        {
            if(!isset($group_product_price[$product->product_group]))
            {
                $group_product_price[$product->product_group] = 0;
            }
            $group_product_price[$product->product_group] += $product->total;           
        }
        else // for normal product
        {
            $subtotal = $subtotal + $product->total;
        ?>  
            <tr>
                <?php if($list_sku[$product->sku] >= 1 && $repeater_sku[$product->sku] == 1){ ?>
                
                    <td class="address_container" rowspan="<?php echo $list_sku[$product->sku]; ?>" style="width: 22%;padding:5px;">{{$product->sku}}</td>
                    <td class="address_container" rowspan="<?php echo $list_sku[$product->sku]; ?>" style="width: 35%;padding:5px;">{{$product->name}}</td>
                    
                <?php } ?>
                
                 <?php

                $label = $product->price_label;

                $arrayOfItems = explode(',', $label);
                $trimmed_array=array_filter(array_map('trim',$arrayOfItems));



   ?>
                <td class="address_container"  style="width: 30%;"><?php  foreach($trimmed_array as $item) {
                     echo " $item<br>";
                 } ?>  </td>
                
                 
                    
                
                <td class="address_container" style="width: 10%;padding:5px;" align="center"><?php if( is_numeric( $product->unit ) && floor( $product->unit ) != $product->unit)
{  echo $product->unit; } else { echo (integer)$product->unit; } ?></td>
                <?php if ($htmlview == false) { ?>
                <td class="address_container" style="width: 3%padding:5px;;" align="center"><img src="img/checkboxpic.png" /></td>
                <?php }else{ ?>
                <td class="address_container" style="width: 3%;padding:5px;" align="center"><img src="/img/checkboxpic.png" /></td>
                <?php } ?>
            </tr>
        
        <?php
        }
        ?>          
        @endforeach

        @foreach($display_group as $group)
            <tr>
                <td class="address_container"  style="width: 22%;">{{$group->sku}}</td>
                <td class="address_container"  style="width: 35%;">
                    {{$group->name}}
                </td>
                <td class="address_container" style="width: 30%;">-</td>
                <td class="address_container" style="width: 10%;" align="center">{{$group->unit}}</td>
                <?php if ($htmlview == false) { ?>
                <td class="address_container" style="width: 3%;" align="center"><img src="img/checkboxpic.png" /></td>
                <?php }else{ ?>
                <td class="address_container" style="width: 3%;" align="center"><img src="/img/checkboxpic.png" /></td>
                <?php } ?>
            </tr>
        <?php
            $subtotal = $subtotal + $group_product_price[$group->sku];
        ?>
      
        @endforeach
       <?php   }  ?> 
    </table>  
    
    
    <br /><br />
    
    <strong>Delivery Terms and Conditions:</strong>
    <ul>
        <?php if($display_details['delivery_state_id'] == 458013 || $display_details['delivery_state_id'] == 458004){ 
            if(date("Y-m-d") >= '2018-08-10'){ 
        ?>
        
        <?php } } ?>
        <li style="font-size:10px;">Goods sold are not refundable, returnable or exchangeable.</li>
        <li style="font-size:10px;">Signature will be required upon delivery, and you are responsible for ensuring you are able to accept delivery on behalf of actual buyer.</li>
        <li style="font-size:10px;">A photo need to be taken for a proof that delivery has occurred and items are in good condition.</li>
        <li style="font-size:10px;">We reserve the right to change, modify or discontinue any delivery options at our absolute discretion</li>
    </ul>
    
    <br /><br />
    <br /><br />
    <br /><br />
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;" valign="bottom">
                <?php
                if (isset($display_details['signature_file']) && $display_details['signature_file'] != "" && file_exists(Config::get('constants.LOGISTIC_SIG_PATH') . '/' . urlencode($display_details['signature_file'])))
                {
                ?>
                <img width="200px;" src="{{Config::get('constants.LOGISTIC_SIG_PATH')}}/{{$display_details['signature_file']}}" /><br />
                <!-- <img width="100px;" src="{{Config::get('constants.LOGISTIC_SIG_PATH')}}/{{$display_details['signature_file']}}" /><br /> -->
                <?php
                }
                ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="bottom">
            </td>
        </tr>
        <tr>
            <td class="address_container"  style="width: 50%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
                Received by Customer<br />
                Name: {{$display_details['sign_name']}}<br />
                IC: {{$display_details['sign_ic']}}<br />
                Phone Number: <br />
                Date: {{$display_details['sign_date']}}<br />
                Time: {{$display_details['sign_time']}}<br />
            </td>
            <td class="address_container" style="width: 20%;">&nbsp;</td>
            <td class="address_container" style="width: 30%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
                Send by tmGrocer<br />
                Name: {{$display_details['driver_name']}}<br />
                Date: {{$display_details['sign_date']}}<br />
                Time: {{$display_details['sign_time']}}<br />
            </td>
        </tr>
    </table>
<!--    <br /><br />
    <strong>Term and Condition:</strong>
    <ul>
        <li style="font-size:10px;">Please produce IC for signatory of buyer or if it is another person sign on behalf please fill in their IC and phone number.</li>
        <li style="font-size:10px;">JOCOM have the rights to not give this item to the buyer if it is not the actual buyer based from IC except called and allowing second person to collect on behalf.</li>
        <li style="font-size:10px;">Photos are to be taken on the products delivered to the buyer for proof of delivery and also items are no broken..</li>
    </ul>-->
    
    

@stop
<!-- {{ HTML::link('transaction/download/'.$encrypted, Download, array('target'=>'blank')) }} -->
<a href="<?php echo '/transaction/download/'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" nclick="window.print()"/>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>

<script>

$(document).ready(function(){
   $(document).bind("contextmenu",function(e){
      return false;
   });
});



</script>
<script>

   $(document).ready(function() { 


        $("#printpagebutton").click(function(){
    //   alert('In');
            var idPrint = parseFloat($('#idPrintcount').val())+1;
            var transID = $('#idTransaction').val();
            $('#LabelidPrintcount').addClass('w3-tag w3-red w3-border-black w3-table-all');
            $('#LabelidPrintcount').html(idPrint);
            $('#idPrintcount').val(idPrint);

            // alert(idPrint);
            $.ajax({
                method: "GET",
                url: "/transaction/updatedoprinting",
                dataType:'json',
                data: {
                    'transactionID':transID,
                    'printcount':idPrint
                },
                beforeSend: function(){
                    
                },
                success: function(data) {
                //   alert(data);
                    if(data ==1){
                         window.print();
                    }
                }
            });

        
           

        });


   });

</script>