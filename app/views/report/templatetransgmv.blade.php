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
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4"><h1>Transaction & GMV Report</h1></td>
    </tr>
    
    <tr>
         <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
         <td colspan="4" align="right"><b>Report Generated On</b>: <? echo date('d-m-yy'); ?> </td>
    </tr>
   
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; " align="center"><b>Transaction Date</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Transaction ID</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total Sale</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Total GMV</b></td>
        


    </tr>
    
    <!-- DATA ROW -->
    <?php 

    // print_r($data);
    // die();

    foreach ($data as $key => $value){
        ?>
    <tr>
        <td style="border: 2px solid  #000000;"><?php echo $value->Date; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->id; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value->TotalAmount;  ?></td>

        <td style="border: 2px solid  #000000; color:#FF0000"><?php echo $value->TotalGMV;  ?></td>
    </tr>
    <?php } //die();?>
    <!-- DATA ROW -->

    
    
    </table>

</html>