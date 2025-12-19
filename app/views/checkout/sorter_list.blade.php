<table style="width: 700px;border:solid 1px #000;" cellpadding="2" cellspacing="0" >
    <tr>
        <td style="width: 5%;border:solid 1px #000;padding: 5px;"><strong>No.</strong></td>
        <td style="width: 15%;border:solid 1px #000;padding: 5px;"><strong>Transaction ID</strong></td>
        <td style="width: 15%;border:solid 1px #000;padding: 5px;"><strong>Location</strong></td>
        <td style="width: 15%;border:solid 1px #000;padding: 5px;"><strong>Date Printed</strong></td>
        <td style="width: 20%;border:solid 1px #000;padding: 5px;"><strong>Received From</strong></td>
        <td style="width: 20%;border:solid 1px #000;padding: 5px;"><strong>Remarks</strong></td>
        <td style="width: 10%;border:solid 1px #000;padding: 5px;"><strong>Recd. Sign</strong></td>
    </tr>
    <?php 
    $counter = 0;
    foreach ($transaction as $keyList => $valueList) { 
        $counter++;
        ?>
  
    <tr>
        <td style="border:solid 1px #000;padding: 5px;"><?php echo $counter; ?></td>
        <td style="border:solid 1px #000;padding: 5px;"><?php echo $valueList['transaction_id']; ?></td>
        <td style="border:solid 1px #000;padding: 5px;"><?php echo $valueList['delivery_city']; ?></td>
        <td style="border:solid 1px #000;padding: 5px;"><?php echo date("Y-m-d h:i:s") ?></td>
        <td style="border:solid 1px #000;padding: 5px;"><?php echo $valueList['platform']; ?></td>
        <td style="border:solid 1px #000;padding: 5px;"></td>
        <td style="border:solid 1px #000;padding: 5px;"></td>
    </tr>
    <?php } ?>
</table>
<table style="width: 700px;margin-top:30px;" border="0" cellpadding="2" cellspacing="0" >
    <tr>
        <td style="padding: 5px;">Received</td>
        <td style="padding: 5px;width: 200px;"></td>
        <td style="padding: 5px;">Prepared By</td>
        <td style="padding: 5px;width: 200px;"></td>
      
    </tr>
    <tr>
        <td style="padding: 5px;">Date</td>
        <td style="padding: 5px;width: 200px;"></td>
        <td style="padding: 5px;">Date</td>
        <td style="padding: 5px;width: 200px;"></td>
    </tr>
</table>