<?php 

echo "<pre>";
        print_r($data);
        echo "</pre>";

?>

   
        <style>
            body{
        max-width: 900px;
        border: solid 1px #888080;
        padding: 10px;
        font-size:12px;
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
       
        

       
        </style>
@extends('checkout.pdf_header')
@section('content')

<page_header>
    </page_header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;" valign="top">
                    <div style="font-size:18px;"><strong><?php echo strtoupper($data['supplierInfo']['company_name']); ?></strong></div>
                    <div><?php echo $data['supplierInfo']['address1']; ?></div>
                    <div><?php echo $data['supplierInfo']['address2']; ?></div>
                    <div><?php echo $data['supplierInfo']['postcode']; ?>, <?php echo $data['supplierInfo']['state']; ?></div>
                    <div><?php echo $data['supplierInfo']['country']; ?></div>
                </td>
                <td style="width: 40%;" valign="top">
                    <div style="font-size:22px;margin-bottom: 15px;text-align: center;"><strong>INVOICE</strong></div>
                    <div>
                        <table>
                            <tr>
                                <td  width="50%">Date</td>
                                <td  width="50%">: <?php echo $data['invoice_date']; ?></td>
                            </tr>
                            <tr>
                                <td  width="50%">Invoice No</td>
                                <td  width="50%">: <?php echo $data['invoice_number']; ?></td>
                            </tr>
                            <tr>
                                <td  width="50%">P.O.</td>
                                <td  width="50%">: <?php echo $data['invoice_po']; ?></td>
                            </tr>
                            <tr>
                                <td  width="50%">Terms</td>
                                <td  width="50%">: <?php echo $data['invoice_term']; ?></td>
                            </tr>
                        </table>
                    </div>
                  
                </td>
            </tr>
        </table>
        
        <table style="width: 100%;margin-top: 10px;">
            <tr>
                <td style="width:10%;" valign="top">
                    <div style="margin-bottom: 15px;text-align: left;font-weight: bold;">Bill To :</div>
                </td>
                <td style="width:90%;" valign="top" style="text-align: left;">
                    <div style="font-weight: bold;">Jocom Ethirtyseven Sdn. Bhd.</div>
                    <div>Unit 9-1, Level 9,</div>
                    <div>Tower 3, Avenue 3, Bangsar South</div>
                    <div>No 8, Jalan Kerinchi</div>
                    <div>59200, Kuala Lumpur</div>
                    <div>Malaysia</div>
                    <div>Tel: 03-2241 6637 Fax: 03-2242 3837</div>
                </td>
            </tr>
        </table>
        <table style="width: 100%;margin-top: 10px;">
            <tr>
                <td style="width: 100%;border-top: double;"></td>
            </tr>
        </table>
        <table style="width: 100%;margin-top: 10px;border-bottom: 0px;border-left: 0px;border-right: 0px;" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%; padding: 5px; background-color: #ddd;color: #000;text-align: center;font-size: 12px;">Description</td>
                <td style="width: 10%; padding: 5px; background-color: #ddd;color: #000;text-align: center;font-size: 12px;">Quantity</td>
                <td style="width: 20%; padding: 5px; background-color: #ddd;color: #000;text-align: center;font-size: 12px;">Unit Price (RM)</td>
                <td style="width: 20%; padding: 5px; background-color: #ddd;color: #000;text-align: center;border-right: solid 1px #000;font-size: 12px;">Total (RM)</td>
            </tr>
            <?php foreach ($data['subItems'] as $key => $value) { ?>
            <tr>
                <td style="width: 50%; padding: 5px; color: #000;text-align: left;font-size: 12px;"><?php echo $value['item']; ?></td>
                <td style="width: 10%; padding: 5px; color: #000;text-align: center;font-size: 12px;"><?php echo $value['quantity']; ?></td>
                <td style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;"><?php echo number_format($value['unitPrice'],2); ?></td>
                <td style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;border-right: solid 1px #000;"><?php echo number_format($value['total'],2); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;"></td>
                <td style="width: 20%;  padding: 5px; color: #000;text-align: right;font-size: 12px;">SUBTOTAL</td>
                <td style="width: 20%;  padding: 5px; color: #000;text-align: right;font-size: 12px;border-right: solid 1px #000;"><?php echo number_format($data['subTotal'],2); ?></td>
            </tr>
            <tr >
                <td  colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;border-top: 0px;"></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;">GST 6%</td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;border-right: solid 1px #000;"><?php echo $data['gst']; ?></td>
            </tr>
            <tr >
                <td colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;border-top: 0px;"></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;">Rounding</td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;border-left: 0px;"><?php echo $data['rounding']; ?></td>
            </tr>
            <tr >
                <td  colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;border-top: 0px;"></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;"><strong>TOTAL</strong></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;border-right: solid 1px #000;border-right: solid 1px #000;"><?php echo number_format($data['total'],2); ?></td>
            </tr>
            <tr >
                <td colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;border-top: 0px;"></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;"><strong>PAID</strong></td>
                <td  style="width: 20%; padding: 5px; color: #000;text-align: right;font-size: 12px;border-right: solid 1px #000;"><?php echo $data['totalPaid']; ?></td>
            </tr>
            <tr >
                <td colspan="2" style="width: 50%; padding: 5px; color: #000;text-align: left;border-bottom: 0px;border-left: 0px;border-top: 0px;"></td>
                <td style="width: 20%; padding: 5px; color: #000;text-align: right;border-bottom: solid 1px #000;font-size: 12px;"><strong>TOTAL DUE</strong></td>
                <td style="width: 20%; padding: 5px; color: #000;text-align: right;border-bottom: solid 1px #000;font-size: 12px;border-right: solid 1px #000;"><?php echo $data['totalDue']; ?></td>
            </tr>
        </table>
        
        <table style="width: 100%;margin-top: 30px;">
            <tr>
                <td style="width: 100%;"  >This is a computer generated document. No signature required. </td>
            </tr>
        </table>
        
        <table style="width: 100%;margin-top: 20px;">
            <tr>
                <td  style="width: 100%;border:solid 1px #000;padding: 3px;" >THANK YOU FOR YOUR BUSINESS! </td>
            </tr>
        </table>
@stop