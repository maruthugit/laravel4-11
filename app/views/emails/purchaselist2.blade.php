<html>
<table >
    <tr>
        <td><strong>BATCH NO: </strong><?php echo $batchNo; ?></td>
    </tr>
    <tr>
        <td><strong>EXPORT DATE: </strong><?php echo $date; ?></td>
    </tr>
    <tr></tr>
    <tr></tr>
</table>

<table style="border:2px solid #000000 !important;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        <td style="border:2px solid #000000 !important;background-color: #ddd "><strong>Transaction ID</strong></td>
        <td style="border:2px solid #000000 !important;background-color: #ddd "><strong>PRODUCT</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd"><strong>PRODUCT SKU</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd"><strong>PRODUCT OPTION</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd"><strong>VENDOR</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd" align="center"><strong>TOTAL TRANSACTIONS</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd" align="center"><strong>TOTAL SET</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd" align="center"><strong>TOTAL REQUIRED</strong></td>
        <td style="border:2px solid #000000;background-color: #ddd" align="center"><strong>HIGHEST UNIT PRICE (RM)</strong></td>
    </tr>
    <!-- TABLE HEADER -->
<?php foreach ($data as $key => $value) { ?>
    <tr>
        <td style="border:2px solid #000000 !important;"><?php echo $value['transaction_id']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['product_name']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['product_sku']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['product_label']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['company_name']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['total_order']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['req_qty']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['req_qty']; ?></td>
        <td style="border:2px solid #000000 !important;"><?php echo $value['unit_price']; ?></td>
    </tr>
    <?php if(count($value['base_product']) > 0) { ?>
    <?php foreach ($value['base_product'] as $kBase => $vBase) { ?>
    <tr>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff; "><?php echo $vBase['product_name'] ;?></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"><?php echo $vBase['product_sku'] ;?></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"><?php echo $vBase['product_label'] ;?></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"><?php echo $vBase['company_name'] ;?></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"><?php echo $vBase['totalQuantity'] ;?></td>
        <td style="border:2px solid #000000  ; background-color:#e2e8ff;"><?php echo $vBase['unit_price'] ;?></td>
    </tr>
    <?php } ?>
    <?php } ?>
<?php } ?>
</table>
</html>

