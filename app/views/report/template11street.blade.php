<?php 
//  echo "<pre>";
//  print_r($data);
// // print_r($data['Stockrep']);

//   echo "</pre>";

// foreach ($data['totdata'] as $key => $value){
//     echo $value['cmstransid'].'<br>';

// }

//  die();
?>
<html>
    <!-- Headings -->
    <table>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="10"><h1>11Street Sale Reconciliation</h1></td>
    </tr>
    <tr>
         <td> <b>Report Generated On</b>: <? echo date('d-m-yy'); ?> </td>
    </tr>
    <tr>
         <td>&nbsp;</td>
    </tr>
   
   
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>        
        <td colspan="12"  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; " align="center"><b>CMS Sale Details</b></td>
        <td colspan="13" style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; " align="center"><b>11Street Payment Transfer Detail </b> </td>
        
    
    </tr>
    <tr>
        <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; " align="center"><b>Date</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Transaction ID</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Invoice No</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Invoice Date</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Buyer</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Status</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>11Street Order No</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Sales GST</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total Sale Excluded GST</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total Sale included GST</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Marketing Cost</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total Sale</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Order No</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Product Number</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Transfer Amount</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Prepaid Shipping Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Return Shipping Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Transaction Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Payment Gateway Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Discount Coupon Usage Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Multiple Purchase Discount</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Point Usage Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b> Claim Fee</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total Sale</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Variance</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Payment Status</b></td>
        


    </tr>
    
    <!-- DATA ROW -->
    <?php foreach ($data['totdata'] as $key => $value){
        ?>
    <tr>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmstransdate']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmstransid']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmsinvno'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmsinvdate'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmsbuyser'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmsbstatus'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['estreetordno'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmsgst'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmstotalexc'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmstotalinc'];  ?></td>       
        <td style="border: 2px solid  #000000;"><?php echo $value['marketngcost'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['cmstotalsales'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['orderno'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['productnumber'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['transferamount'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['prepaidshippingamount'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['returnshippingamount'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['transactionfee'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['paymentgatewayfee'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['discountcouponfee'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['multipurchasedisc'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['pointusagefee'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['claimfee'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['eleventotalamount'];  ?></td>
        <td style="border: 2px solid  #000000;"><span style="color:#FF0000"><?php echo $value['variance'];  ?></span></td>
        <td style="border: 2px solid  #000000;"><span style="color:#FF0000"><?php echo $value['paymentstatus'];  ?></span></td>
    </tr>
    <?php } //die();?>
    <!-- DATA ROW -->

    
    
    </table>

</html>