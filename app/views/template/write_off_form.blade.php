<html>
<?php
// echo "<pre>";
// print_r($data);
// echo "</pre>";
?>
<style type="text/css">
    
    body{
        max-width: 800px;
        border: solid 1px #888080;
        padding: 10px;
        margin: auto;
        background-color: #fff;
        margin-bottom: 10px;
        margin-top: 10px;
        }
        
    html{
        background-color: #f3f3f3;
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

    <body>
        <table style="width:100%;">
            <tr>
                <td style="text-align:right;font-size: 10px;width: 80%;"></td>
                <td style="text-align:right;font-size: 10px;"></td>
            </tr>
            <tr>
                <td style="text-align:right;font-size: 10px;width: 80%;"></td>
                <td style="text-align:right;font-size: 10px;"></td>
            </tr>
        </table>
        <table style="width:100%;">
            <tr>
                 <td style="text-align: left;font-weight: bold;font-size: 25px;"><img width="100px;" src="/img/invoice_po_logo.jpg"></td>
                 <td style="text-align:right;">
                     <strong>STOCK WRITE OFF FORM</strong>
                 </td>
            </tr>
        </table>
        <table style="width:100%;">
            <tr>
                <td style="text-align:left;font-size: 12px;">
                    Jocom eThirtySeven Sdn. Bhd
                    <br>WareHouse
                    <br>No. 36A, Jalan Ipoh Batu 8 1/2
                    <br>Kompleks Selayang
                    <br>68100 Batu Caves
                    <br>Selangor
                </td>
                <td width="200px;" valign="top" style="text-align:left;font-size: 12px;">SW No. : <?php echo $data['write_off_info']->doc_no; ?>  <br>SW Date: <?php echo $data['write_off_info']->doc_date; ?></td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 12px;">Tel: 03-2241 6637   Fax: 03-2242 3837</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 12px;">PH : 019 226 6982</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 15px;"><strong>Account Department</strong> </td>
            </tr>

            
        </table>
        <table style="width:100%; padding: 0px;margin-top:30px;" border="1" cellpadding="0" cellspacing="0">
            <tr style="font-size:12px;text-align: center;">
                <td style="padding: 10px;">NO. </td>
                <td style="padding: 10px;">SKU </td>
                <td style="padding: 10px;">ITEM NAME</td>
                <td style="padding: 10px;">DESCRIPTION LABEL</td>
                <td style="padding: 10px;">EXPIRY DATE</td>
                <td style="padding: 10px;">QUANTITY</td>
            </tr>
            <?php 
            $index = 0;
            $grandTotal = 0;
            foreach ($data['write_off_details'] as $key => $value) { 
                $index++;
                ?>
                <tr style="font-size:12px;text-align: left;padding-left:5px;">
                    <td style="padding: 10px;text-align:center;"><?php echo $index; ?> </td>
                    <td style="padding: 10px;"><?php echo $value->sku; ?></td>
                    <td style="padding: 10px;"><?php echo $value->name; ?></td>
                    <td style="padding: 10px;"><?php echo $value->label; ?></td>
                    <td style="padding: 10px;text-align:center;"><?php echo $value->expired_date; ?></td>
                    <td style="padding: 10px;text-align: center;"><?php echo $value->quantity; ?></td>
                </tr>
            <?php $grandTotal += $value->quantity; } ?>
            <tr style="font-size:12px;text-align: left;">
                <td style="padding: 10px;text-align:center;" colspan="5"><strong>GRAND TOTAL</strong></td>
                <td style="padding: 10px;text-align:center;"><strong> <?php echo $grandTotal ; ?></strong></td>
            </tr>
            
        </table>
        <table style="width:100%; padding: 0px;margin-top:30px;font-size:12px" border="0" cellpadding="0" cellspacing="0">
            <tr><td><strong>Reason for goods write off :</strong></td></tr>
            <tr><td><?php echo $data['write_off_info']->remarks; ?></td></tr>
            <tr><td style="text-align: left;padding-top:10px;">This is a computer generated document. No signature required. Goods sold are not refundable, returnable or exchangeable. </td></tr>
        </table>

        <table style="width:100%; padding: 0px;margin-top:30px;font-size:12px" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td><strong>Prepared By :</strong></td>
                <td><strong>Approved (Operation) By :</strong></td>
                <td><strong>Approved (Account) By :</strong></td>
                <td><strong>Approved (Management) By :</strong></td>
                
            </tr>
            <tr>
                <td style="padding-top:50px;" > <strong>----------------------------------------------</strong></td>
                <td style="padding-top:50px;"  ><strong>----------------------------------------------</strong></td>
                <td style="padding-top:50px;"  ><strong>----------------------------------------------</strong></td>
                <td style="padding-top:50px;"  ><strong>----------------------------------------------</strong></td>
            </tr>
            <tr >
                <td style="padding-bottom:100px;">
                    Name : <?php echo $data['write_off_info']->NamePrepare; ?>
                    <br> Date : <?php echo date("d-m-Y",strtotime($data['write_off_info']->created_at)); ?>
                </td>
                <td style="padding-bottom:100px;">
                    Name : 
                    <br> Date : 
                </td>
                <td style="padding-bottom:100px;">
                    Name : 
                    <br> Date : 
                </td>
                <td style="padding-bottom:100px;">
                    Name : 
                    <br> Date : 
                </td>
            </tr>
        </table>
    </body>
</html>