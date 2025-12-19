<html>
    <table>
        <tr></tr>
        <tr></tr>
        <tr>
            <td>DRIVER : <?php echo $data['driver_name']; ?></td>
            <td><p></p></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><p>LORRY : </p></td>
            <td><p></p></td>
        </tr>
    </table>
    <table style="">
        <tr>
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
            <th style="border: 3px solid  #000000;">Date Assign:  <?php echo $data['transaction_from']; ?> - <?php echo $data['transaction_to']; ?></th>
            
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
            <th style="border: 3px solid  #000000;border-right: 0px solid  #ddd; "><center><strong>STOCK OUT: CHECK</strong></center></th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd; ;">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-right: 0px solid  #ddd; "><center><strong>STOCK RETURN: CHECK</strong></center></th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd;border-right: 0px solid  #ddd; ;">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd; ;">&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        

        <tr>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Product</th>
            
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
                
                

                foreach ($data['logistic2'] as $key => $value) { 

                    // $numbers[] = $value->id_count;
                    // $trans = $value->transaction_id;
                    // $translist = explode(",", $trans);
                    
                    // $qty = $value->qty_assign;
                    // $qtylist = explode(",", $qty);
                    
                    
                    
                    $trans = $value['transaction_id'];
                    $translist = explode(",", $trans);
                    
                    $qty = $value['qty_assign'];
                    $qtylist = explode(",", $qty);
        ?>
        <tr>
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
            <?php if ($value['base_product']!='') {
                echo $value['base_product'] . " (".$value['label_name'].")";
            }else{
                echo $value['shortname']. " (".$value['label_name'].")";
                }?>
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
        <?php } }?>
    </table>
</html>




