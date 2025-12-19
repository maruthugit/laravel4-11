<html>
    <table>
        <tr></tr>
        <tr> <td style="font-size:25px !important; font-weight:bold !important; font:Calibri;">Warehouse Checklist</td></tr>
        <tr></tr>
       <?php if($data['drivers_counts']=="all"){?>
      <tr>
         <b>
            <td style="font-size:12px !important; font-weight:bold !important; font:Calibri;color:#FF0000">Drivers :</td>
         </b>
         <td>
            <p></p>
         </td>
      </tr>
      <tr>
         <td>ALL</td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      </tr>
      <?php } ?>  
      <?php if($data['drivers_counts']=="one"){?>
      <tr>
         <b>
            <td style="font-size:12px !important; font-weight:bold !important; font:Calibri;color:#FF0000">Driver :
            </td>
         </b>
         <td>
            <p></p>
         </td>
      </tr>
      <tr>
         <b>
            <td ><?php 
               foreach($data['driver_name'] as $driver){
               echo  $driver->username;?>
               <?php } ?> 
            </td>
         </b>
         <td>
            <p></p>
         </td>
      </tr>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      </tr>
      <?php }?>
      <?php if($data['drivers_counts']=="multiple"){?>         
      <tr>
         <b>
            <td style="font-size:12px !important; font-weight:bold !important; font:Calibri;color:#FF0000">Drivers :</td>
         </b>
         <td>
            <p></p>
         </td>
      </tr>
      <?php 
         foreach($data['driver_name'] as $driver){ 
         ?>
      <tr>
         <td>
            <?php  echo  $driver->username; ?>  
         </td>
      </tr>
      <?php }?>
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      <?php }?>
      <tr>
         <b>
            <td>
               <p style="font-size:12px !important; font-weight:bold !important; font:Calibri;color:#FF0000">Generated From : <?php echo ucfirst($data['generatedfrom']); ?> </p>
            </td>
         </b>
         <td>
            <p></p>
         </td>
      </tr>
        <tr>
            <td><p>LORRY : </p></td>
            <td><p></p></td>
        </tr>
    </table>
    <table style="">
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">EQUIPMENT</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">QTY</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">TAKEN</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">RETURN</td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">H/Phone</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;">Power Bank</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;"></td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;">T&G</td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
        </tr>
        <tr>
         <th></th>
         <th></th>
         <th></th>
         <?php
            $i = 0;
            $max = reset($data['logistic2']);
            $id = $max['id_count'] + 2;
            
            while($i < $id)
            {
                echo "<th></th>";
                $i++;
            }
            
            ?>
         <td style="border: 3px solid  #000000 !important;">DO</td>
         <td style="border: 3px solid  #000000 !important;"></td>
         <td style="border: 3px solid  #000000 !important;"></td>
         <td style="border: 3px solid  #000000 !important;"></td>
      </tr>
      <tr>
         <th></th>
         <th></th>
         <th></th>
         <?php
            $i = 0;
            $max = reset($data['logistic2']);
            $id = $max['id_count'] + 2;
            
            while($i < $id)
            {
                echo "<th></th>";
                $i++;
            }
            
            ?>
         <td style="border: 3px solid  #000000 !important;">Load</td>
         <td style="border: 3px solid  #000000 !important;"></td>
         <td style="border: 3px solid  #000000 !important;"></td>
         <td style="border: 3px solid  #000000 !important;"></td>
      </tr>
        <tr>
        <th></th>
        <th></th>
        <th></th>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;">U-Pack 0.5</td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max['id_count'] + 2;

                    while($i < $id)
                    {
                        echo "<th></th>";
                        $i++;
                    }
                   
                ?>
            <td style="border: 3px solid  #000000 !important;">U-Pack 2.2</td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
            <td style="border: 3px solid  #000000 !important;"></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th style="border: 3px solid  #000000;">Date Range:  <?php echo $data['transaction_from']; ?> - <?php echo $data['transaction_to']; ?></th>
            
                <?php
                
                    $numbers = array();
                    foreach ($data['logistic2'] as $key => $value) { 
                        $numbers[] = $value['id_count'];
                    }
                    
                    $max = max($numbers);

                    $i = 0;
                

                    while($i < $max)
                    {
                        echo '<th style="border-top: 3px solid  #000000; "></th>';
                        $i++;
                    }
                   
                ?>
            <th style="border-top: 3px solid  #000000; ">&nbsp;</th>
            <th style="border-top: 3px solid  #000000; ">&nbsp;</th>
            <th style="border-top: 3px solid  #000000; ">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-right: 0px solid  #ddd; "><center><strong>STOCK OUT: CHECK</strong></center></th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd; ;">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-right: 0px solid  #ddd; "><center><strong>STOCK RETURN: CHECK</strong></center></th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd;border-right: 0px solid  #ddd; ;">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd; ;">&nbsp;</th>
        </tr>
      <tr>
         <b>
            <td style="border: 2px solid  #000000;background-color: #ddd;">
               <h1 style="font-size:28px !important; font-weight:bold !important; font:Calibri;">Dry Items</h1>
            </td>
         </b>
         <?php

                    $i = 0;
                    while($i < $max)
                    {
                        echo "<th style='border: 2px solid  #000000;background-color: #ddd;'></th>";
                        $i++;
                    }
                   
                ?>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
        <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
      </tr>

        <tr>
            <th style="border: 2px solid  #000000;background-color: #ddd;">SKU</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Product</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Label</th>
            
                <?php

                    $i = 0;
                    while($i < $max)
                    {
                        echo "<th style='border: 2px solid  #000000;background-color: #ddd;'>DO</th>";
                        $i++;
                    }
                   
                ?>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Total</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Store </th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Driver</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Quantity</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Store</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Driver</th>
        </tr>
        <?php
         if ($data['logistic2'] !='') { 
             $count=0;
          foreach ($data['logistic2'] as $key=>$values) {
            if($values['cat_type']=="Dry"){
             $count=1;
            }
          }
         }
         if($count!=1){
         ?>
      <tr>
         <b>
            <td style="text-align:center;color:#FF0000;">No Items Found</td>
         </b>
      </tr>
      <?php }?>
        <?php 
            if ($data['logistic2'] !='') {
             
                foreach ($data['logistic2'] as $key => $value) { 

                   if($value['cat_type'] == "Dry"){
                    
                    $trans = $value['transaction_id'];
                    $translist = explode(",", $trans);
                    
                    $qty = $value['qty_assign'];
                    $qtylist = explode(",", $qty);
        ?>
        <tr>
            <td style="border: 2px solid  #000000;">&nbsp;</td>
            <td style="border: 2px solid  #000000;">&nbsp;</td>
            <td style="border: 2px solid  #000000;">&nbsp;</td>

                <?php foreach ($translist as $valuetrans) { ?>
                <td style="border: 2px solid  #000000; background-color: #ddd;font-size:12px !important;">
                    {{$valuetrans}}
                </td>
                <?php } ?>
                <?php $max = max($numbers);
                        $min = $max - $value['id_count']; 
                    for ($i=0; $i<$min  ; $i++) {
                       echo "<td style='border: 2px solid  #000000;background-color: #ddd;'></td>";
                    }
                ?>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
        </tr>
        <tr>
            <td style="border: 2px solid  #000000;font-size:12px !important;">
            <?php echo $value['sku'];?>
         </td>
             <td style="border: 2px solid  #000000;font-size:12px !important;">
            <?php if ($value['base_product']!='') {
                echo $value['base_product'];
            }else{
                echo $value['shortname'];
                }?>
            </td>
            <td style="border: 2px solid  #000000;font-size:12px !important;">
             <?php echo $value['label_name'];?>
            </td>
            <?php 
            $Maintotal = 0;
            foreach ($qtylist as $valueqty) { ?>
            <td style="border: 2px solid  #000000;font-size:12px !important;"> 
                <?php 
                if ($value['base_product']!='') {
                    echo $valueqty ;
                    
                    $Maintotal = $Maintotal + ($valueqty);
                }else{
                    $Maintotal = $Maintotal + ($valueqty);
                    echo $valueqty;
                 }?>
            </td>
            <?php } ?>
            <?php $max = max($numbers);
                    $min = $max - $value['id_count']; 
                for ($i=0; $i <$min  ; $i++) {
                   echo "<td style='border: 2px solid  #000000;'></td>";
                }
            ?>
            <td style="border: 3px solid  #000000;font-size:12px !important;"><strong><?php echo $Maintotal; ?></strong></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
        </tr>
        <?php } } }?>
        <tr></tr>
        <tr>
         <b>
            <td style="border: 2px solid  #000000;background-color: #ddd;">
               <h1 style="font-size:28px !important; font-weight:bold !important; font:Calibri;">Frozen Items</h1>
            </td>
         </b>
         <?php

                    $i = 0;
                    while($i < $max)
                    {
                        echo "<th style='border: 2px solid  #000000;background-color: #ddd;'></th>";
                        $i++;
                    }
                   
                ?>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
         <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
        <th style="border: 2px solid  #000000;background-color: #ddd;"></th>
      </tr>

        <tr>
            <th style="border: 2px solid  #000000;background-color: #ddd;">SKU</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Product</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Label</th>
            
                <?php

                    $i = 0;
                    while($i < $max)
                    {
                        echo "<th style='border: 2px solid  #000000;background-color: #ddd;'>DO</th>";
                        $i++;
                    }
                   
                ?>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Total</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Store </th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Driver</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Quantity</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Store</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Driver</th>
        </tr>
        <?php
         if ($data['logistic2'] !='') { 
             $count=0;
          foreach ($data['logistic2'] as $key=>$values) {
            if($values['cat_type']=="Frozen"){
             $count=1;
            }
          }
         }
         if($count!=1){
         ?>
      <tr>
         <b>
            <td style="text-align:center;color:#FF0000;">No Items Found</td>
         </b>
      </tr>
      <?php }?>
        <?php 
            if ($data['logistic2'] !='') {
             
                foreach ($data['logistic2'] as $key => $value) { 

                   if($value['cat_type'] == "Frozen"){
                    
                    $trans = $value['transaction_id'];
                    $translist = explode(",", $trans);
                    
                    $qty = $value['qty_assign'];
                    $qtylist = explode(",", $qty);
        ?>
        <tr>
            <td style="border: 2px solid  #000000;">&nbsp;</td>
            <td style="border: 2px solid  #000000;">&nbsp;</td>
            <td style="border: 2px solid  #000000;">&nbsp;</td>

                <?php foreach ($translist as $valuetrans) { ?>
                <td style="border: 2px solid  #000000; background-color: #ddd;font-size:12px !important;">
                    {{$valuetrans}}
                </td>
                <?php } ?>
                <?php $max = max($numbers);
                        $min = $max - $value['id_count']; 
                    for ($i=0; $i<$min  ; $i++) {
                       echo "<td style='border: 2px solid  #000000;background-color: #ddd;'></td>";
                    }
                ?>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
            <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;border-right: 3px solid #000000;" >&nbsp;</td>
        </tr>
        <tr>
            <td style="border: 2px solid  #000000;font-size:12px !important;">
            <?php echo $value['sku'];?>
         </td>
             <td style="border: 2px solid  #000000;font-size:12px !important;">
            <?php if ($value['base_product']!='') {
                echo $value['base_product'];
            }else{
                echo $value['shortname'];
                }?>
            </td>
            <td style="border: 2px solid  #000000;font-size:12px !important;">
             <?php echo $value['label_name'];?>
            </td>
            <?php 
            $Maintotal = 0;
            foreach ($qtylist as $valueqty) { ?>
            <td style="border: 2px solid  #000000;font-size:12px !important;"> 
                <?php 
                if ($value['base_product']!='') {
                    echo $valueqty ;
                    
                    $Maintotal = $Maintotal + ($valueqty);
                }else{
                    $Maintotal = $Maintotal + ($valueqty);
                    echo $valueqty;
                 }?>
            </td>
            <?php } ?>
            <?php $max = max($numbers);
                    $min = $max - $value['id_count']; 
                for ($i=0; $i <$min  ; $i++) {
                   echo "<td style='border: 2px solid  #000000;'></td>";
                }
            ?>
            <td style="border: 3px solid  #000000;font-size:12px !important;"><strong><?php echo $Maintotal; ?></strong></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
        </tr>
        <?php } } }?>
    </table>
</html>