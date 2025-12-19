<?php 
// echo "<pre>";
// print_r($data);
// print_r($data['Stockrep']);
// foreach ($data['Stockrep'] as $key => $value){
//     echo $value['ProductName'].'<br>';

// }
// echo "</pre>";


// die();
?>
<html>
    <!-- Headings -->
    <table>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="10"><h1><?php echo  "Pallet History"; ?></h1></td>
    </tr>
    <tr>
         <td>&nbsp;</td>
    </tr>
  
    
   

    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
       
      
       <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Date</b></td>
       
 
        <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Pallet Code</b></td>
          <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Supplier</b></td>
                <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Type</b></td>
        <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Pallet Description</b></td>
          <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Pallet Price</b></td>
          
              <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; color:red " align="center"><b>Stock Out</b></td>
                <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Stock In</b></td>
                  <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>To Date Balance </b></td>
                    <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>To Date Debt Stock</b></td>
         
                 
                                <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Remarks</b></td>
                                 <td  style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Modify By </b></td>
                         
        <!-- Stock in Hand Report -->
        
        
    </tr>
 
    <!-- DATA ROW -->
    <?php foreach ($data['pallet'] as $key => $value){


        ?>
        
   
    <tr>
       
      <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->date; ?></td>
      <td style="border: 2px solid  #000000;"><?php echo $value->pallet_code; ?></td>
       <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->supplier_name; ?></td>
      <td style="border: 2px solid  #000000;"><?php echo $value->type; ?></td>
       <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->pallet_Description; ?></td>
      <td style="border: 2px solid  #000000;"><?php echo $value->pallet_price; ?></td>
       <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->stockout; ?></td>
      <td style="border: 2px solid  #000000;"><?php echo $value->stockin; ?></td>
       <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->debtstock; ?></td>
        <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->debtstock; ?></td>
       <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->remarks; ?></td>
      <td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value->modify_by; ?></td>
        
         <!-- <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td> -->
    </tr>
    <?php } //die();?>
    <!-- DATA ROW -->

    
    
    </table>

    
    
     

</html>