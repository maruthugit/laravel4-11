<?php 
//echo "<pre>";
//print_r($data);
//echo "</pre>";
//die();
?>

<html>
    <!-- Headings -->
    <table>
    <tr>

    </tr>
    <tr>
        <td colspan="11"><h1><?php echo $data['productName']; ?></h1></td>
    </tr>
    <tr>

    </tr>
    <tr>
        <td><strong>SKU CODE :</strong></td>
        <td style="text-align: left;"><?php echo $data['productSKU']; ?></td>
    </tr>
    <tr>
        <td><strong>JC CODE :</strong></td>
        <td style="text-align: left;"><?php echo $data['productJcode']; ?></td>
    </tr>
    <tr>
        <td><strong>UNIT OF MEASURE :</strong></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>
        <td><strong>UNIT PRICE :</strong></td>
        <td style="text-align: left;"></td>
    </tr>
    <tr>

    </tr>
    <tr>

    </tr>
    
    
    
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>DATE</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>DESCRIPTION</b></td>
        <td style="border-top: 2px solid #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;"></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;" ><strong>IN</strong></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; border-right:2px solid  #000000; text-align: center;background-color: #ddd;"></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;"></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;" ><strong>OUT</strong></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000;  border-right:2px solid  #000000; text-align: center;background-color: #ddd;"></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;"></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;" ><strong>BALANCE</strong></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000;  border-right:2px solid  #000000; text-align: center;background-color: #ddd;"></td>
    <!-- Italic -->
    </tr>
    <tr>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>UNIT</b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>RM</b></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>UNIT</b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>RM</b></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>UNIT</b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"><b>RM</b></td>
    </tr>
    <tr>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"><b style="border: 2px solid  #000000;color:red;">Opening Balance</b></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;text-align: center;color:red;"><b></b></td> <!-- OPENING BALANCE QUANTITY -->
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;color:red;"><b></b></td> <!-- OPENING BALANCE TOTAL -->
    </tr>
    <!-- DATA ROW -->
    <?php foreach ($data['transaction'] as $key => $value)  {
        ?>
        
   
    <tr>
        <td style="border: 2px solid  #000000;"><?php echo $value->invoice_date; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->id; ?></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"><?php echo $value->productBaseQty*$value->qty_order;  ?></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td>
    </tr>
    <?php } ?>
    <!-- DATA ROW -->
    <!-- FOOTER -->
    <tr>
        <td style="border: 2px solid  #000000;" colspan="2"><b>TOTAL</b></td>
        <td style="border: 2px solid  #000000;"><b></b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"><b></b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
    </tr>
    <tr>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"><b>Closing Stock</b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"><b></b></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
    </tr>
    
    </table>

</html>