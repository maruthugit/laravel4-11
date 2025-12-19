<?php 
//echo "<pre>";
//print_r($data); 
//echo "</pre>";
//
//die();?>

<html>
    <!-- Headings -->
    <table>
    <tr>

    </tr>
    <tr>
        <td colspan="11"><h1>Proccessor Report</h1></td>
    </tr>
    <tr>

    </tr>
    <tr>
        <td><strong>From Date :</strong></td>
        <td style="text-align: left;"><?php echo $data['startDate']; ?></td>
    </tr>
    <tr>
        <td><strong>To Date :</strong></td>
        <td style="text-align: left;"><?php echo $data['toDate']; ?></td> 
    </tr>
    <tr>

    </tr>
    <tr>

    </tr>
    
    
    
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        <!-- <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>JC No</b></td> -->
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>SKU</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Company Name</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Product Name</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Label</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Unit Sold</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Revenue</b></td>
    </tr>
    
    <!-- DATA ROW -->
    <?php 
    foreach ($data['totalProductSold'] as $key => $value) { 
        ?>
    <tr>
        <!-- <td style="border: 2px solid  #000000;"><?php echo $value->jcCode?></td> -->
        <td style="border: 2px solid  #000000;"><?php echo $value->sku?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->companyName?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->productName?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->label?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->unitsSold?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->revenue?></td>
    </tr>
    <?php } ?>
    <!-- DATA ROW -->

    <!-- FOOTER -->
    <tr>
        <td colspan="3"></td>
        <td style="border: 2px solid  #000000;"><b>TOTAL</b></td>
        <td style="border: 2px solid  #000000;"><?php echo $data['sumQuantity']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $data['sumPrice']; ?></td>
    </tr>
    <!-- FOOTER -->
    
    </table>

</html>