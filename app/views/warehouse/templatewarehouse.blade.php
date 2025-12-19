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
        <td colspan="10"><h1><?php echo $data['reporttitle']; ?></h1></td>
    </tr>
    <tr>
         <td>&nbsp;</td>
    </tr>
    @if ($data['rtype'] != 1)
    <tr>
        <td><strong>Range Date </strong></td>
    
        <td colspan="2"><b>From :</b> <?php echo $data['fromdate']; ?></td>
        <td colspan="2"><b>To :</b> <?php echo $data['todate']; ?></td>
    
    </tr>
    
    <tr>
         <td>&nbsp;</td>
    </tr>
    @endif
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
    <!-- TABLE HEADER -->
    <tr>
        @if ($data['rtype'] != 1)
        <td rowspan="2" style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle; " align="center"><b>Date</b></td>
        @endif
          @if ($data['rtype'] == 1 || $data['rtype'] != 1 )
        <td <?php if($data['rtype'] != '1'){echo 'rowspan=2';}?> style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Product ID</b></td>
        @endif
        <td <?php if($data['rtype'] != '1'){echo 'rowspan=2';}?> style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Product Name</b></td>
        <td <?php if($data['rtype'] != '1'){echo 'rowspan=2';}?> style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;" align="center"><b>Label</b></td>
        
        <!-- Stock in Hand Report -->
        @if ($data['rtype'] == 1)
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;" ><strong>Stock In Hand</strong></td>
        <td style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd;" ><strong>Measurement</strong></td> @endif
        @if ($data['rtype'] != 1)
        <!-- Stock in Report -->
        <td <?php if($data['rtype'] == '2'){echo 'colspan=3';}else{echo 'colspan=2';}?> style="border-top: 2px solid  #000000; border-bottom: 2px solid #000000; text-align: center;background-color: #ddd; vertical-align: middle;" ><strong>Stock Details</strong></td>
        @endif
        @if ($data['rtype'] == 2)
        <td rowspan="2" style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Expirydate</b></td>
        @endif
        @if ($data['rtype'] >= 2)
         <td rowspan="2" style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Remark</b></td>
        @endif

        @if ($data['rtype'] == 1 || $data['rtype'] != 1 )
        <td <?php if($data['rtype'] != '1'){echo 'rowspan=2';}?> style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Created By</b></td>
        <td <?php if($data['rtype'] != '1'){echo 'rowspan=2';}?> style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Created At</b></td>@endif
        @if ($data['rtype'] == 1)
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Modified By</b></td>
        <td style="border: 2px solid  #000000;background-color: #ddd;" align="center"><b>Updated At</b></td>@endif

    <!-- Italic -->
    </tr>
    @if ($data['rtype'] != 1)
    <tr>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        @if ($data['rtype'] == 3)
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Stock OUT</b></td> @endif
        @if ($data['rtype'] == 4)
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Stock Return</b></td> @endif
        @if ($data['rtype'] == 3)
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Assign To</b></td>  @endif
        @if ($data['rtype'] == 4)
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Return From</b></td>  @endif
         @if ($data['rtype'] == 2)
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Stock IN</b></td>
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Unit Price</b></td>
        <td style="border: 2px solid  #000000;text-align: center; background-color: #ddd;"><b>Total</b></td> @endif

        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;"></td>
        
    </tr>
    @endif
    <!-- DATA ROW -->
    <?php foreach ($data['Stockrep'] as $key => $value){
        ?>
        
   
    <tr>
        @if ($data['rtype'] != 1)<td style="border: 2px solid  #000000;"><?php echo $value['CreatedAt']; ?></td>@endif
        @if ($data['rtype'] == 1 || $data['rtype'] != 1 )<td  style="border: 2px solid  #000000; text-align: center;"><?php echo $value['ProductID']; ?></td>@endif
        <td style="border: 2px solid  #000000;"><?php echo $value['ProductName']; ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Label']; ?></td>
        @if ($data['rtype'] == 1)
        <td style="border: 2px solid  #000000;"><?php echo $value['StockInHand'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Measurement'];  ?></td>@endif
        @if ($data['rtype'] == 2)
        <td style="border: 2px solid  #000000;"><?php echo $value['StockInQty'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['UnitPrice'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Total'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Expirydate'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Remark'];  ?></td>
        @endif
        @if ($data['rtype'] == 3)
        <td style="border: 2px solid  #000000;"><?php echo $value['StockOutUnit'];  ?></td>@endif
        @if ($data['rtype'] == 4)
        <td style="border: 2px solid  #000000;"><?php echo $value['StockReturnUnit'];  ?></td>@endif
        @if ($data['rtype'] > 2)
        <td style="border: 2px solid  #000000;"><?php echo $value['DriverName'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['Remark'];  ?></td>
        @endif
        <td style="border: 2px solid  #000000;"><?php echo $value['CreatedBy'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['CreatedAt'];  ?></td>
         @if ($data['rtype'] == 1)<td style="border: 2px solid  #000000;"><?php echo $value['ModifiedBy'];  ?></td>
        <td style="border: 2px solid  #000000;"><?php echo $value['UpdatedAt'];  ?></td>@endif
         <!-- <td style="border: 2px solid  #000000;text-align: center;"></td>
        <td style="border: 2px solid  #000000;"></td>
        <td style="border: 2px solid  #000000;text-align: center;"></td> -->
    </tr>
    <?php } //die();?>
    <!-- DATA ROW -->

    
    
    </table>

</html>