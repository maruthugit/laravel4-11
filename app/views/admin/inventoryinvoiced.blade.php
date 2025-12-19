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
        <td colspan="11"><h1></h1></td>
    </tr>
    <tr>

    </tr>
    <tr>
        <td><strong>From Date :</strong></td>
        <td style="text-align: left;"><?php echo $data['from_date']; ?></td>
        <td><strong>To Date :</strong></td>
        <td style="text-align: left;"><?php echo $data['to_date']; ?></td>
    </tr>
    <tr>

    </tr>
    <tr>

    </tr>
    
    
    
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Invoice Date</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Transaction ID</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Invoice No</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>DO No</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Product Code</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Product</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Label</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Quantity/Unit</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Logistic Status</b></td>
        <?php foreach ($data['baseStockListColumn'] as $keyCP => $valueCP) {?>
        <td style="border: 2px solid  #000000;background-color: #e4aaaa;" align="center"><b><?php echo $valueCP['columnName']; ?></b></td>
        <?php } ?>
    </tr>
    
    <!-- DATA ROW -->
    <?php 
    foreach ($data['transaction'] as $key => $value) 
    { ?>
    <tr>
        <td style="border: 2px solid  #000000;"><?php echo $value['invoice_date']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['id']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['invoice_no']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['do_no']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['sku']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['name']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['label']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['qty_order']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['LogisticStatus']; ?></td>
        
        <?php 
        $mapperJC = array();
        if(isset($data['baseStockListColumnQty'][$value['product_price_id']])){
            foreach ($data['baseStockListColumnQty'][$value['product_price_id']] as $keyMP => $valueMP) {
                $mapperJC[$valueMP['jcode']] = $valueMP['quantity'];
            }
        }
        
        foreach ($data['baseStockListColumn'] as $keyMP => $valueMP) {
            if(isset($mapperJC[$keyMP])){
                $quantity = $mapperJC[$keyMP];
            }else{
                $quantity = '';
            }
            
            ?>
            
            <td style="border: 2px solid  #000000;"><?php echo $quantity*$value['qty_order'] > 0 ? $quantity*$value['qty_order']:'' ?></td>
            
        <?php } ?>
    </tr>
    <?php } ?>
    <!-- DATA ROW -->
    <!-- FOOTER -->
    
    </table>

</html>