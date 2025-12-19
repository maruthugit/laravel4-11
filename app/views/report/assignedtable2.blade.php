<html>
    <table>
        <tr>
            <td>Driver : <?php echo $data['driver_name']; ?></td>
            <td><p></p></td>
        </tr>
        <tr>
            <td><p>Lorry : </p></td>
            <td><p></p></td>
        </tr>
    </table>
    <table style="border: 2px solid  #000000 !important;">
        <tr>
            <th style="border: 3px solid  #000000;">Date Assign:  <?php echo $data['transaction_from']; ?> - <?php echo $data['transaction_to']; ?></th>
            
                <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max->id_count;

                    while($i < $id)
                    {
                        echo '<th style="border-top: 3px solid  #000000; "></th>';
                        $i++;
                    }
                   
                ?>
            <th style="border-top: 3px solid  #000000; ">&nbsp;</th>
            <th style="border: 3px solid  #000000;border-right: 0px solid  #ddd; "><center><strong>STOCK OUT Check By</strong></center></th>
            <th style="border: 3px solid  #000000;border-left: 0px solid  #ddd; ;">&nbsp;</th>
            <th>&nbsp;</th>
        </tr>

        <tr>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Product</th>
            
                <?php

                    $i = 0;
                    $max = reset($data['logistic2']);
                    $id = $max->id_count;

                    while($i < $id)
                    {
                        echo "<th style='border: 2px solid  #000000;background-color: #ddd;'>DO</th>";
                        $i++;
                    }
                   
                ?>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Total</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Check by Store </th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Check by Driver</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Stock Return</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Check by Store</th>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Check by Driver</th>
        </tr>
        <?php 
            if ($data['logistic2'] !='') {

                $numbers = array();
                foreach ($data['logistic2'] as $key => $value) { 

                    $numbers[] = $value->id_count;
                    $trans = $value->transaction_id;
                    $translist = explode(",", $trans);
                    
                    $qty = $value->qty_assign;
                    $qtylist = explode(",", $qty);
        ?>
        <tr>
            <td style="border: 2px solid  #000000;">&nbsp;</td>
                <?php foreach ($translist as $valuetrans) { ?>
                <td style="border: 2px solid  #000000; background-color: #ddd;">
                    {{$valuetrans}}
                </td>
                <?php } ?>
                <?php $max = max($numbers);
                        $min = $max - $value->id_count; 
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
            <td style="border: 2px solid  #000000;">
            <?php if ($value->base_product!='') {
                echo $value->base_product . " (".$value->label_name.")";
            }else{
                echo $value->shortname. " (".$value->label_name.")";
                }?>
            </td>
            
            <?php 
            $Maintotal = 0;
            foreach ($qtylist as $valueqty) { ?>
            <td style="border: 2px solid  #000000;"> 
                <?php 
                if ($value->base_product!='') {
                    echo $valueqty * $value->base_quantity;
                    
                    $Maintotal = $Maintotal + ($valueqty * $value->base_quantity);
                }else{
                    $Maintotal = $Maintotal + ($valueqty);
                    echo $valueqty;
                 }?>
            </td>
            <?php } ?>
            <?php $max = max($numbers);
                    $min = $max - $value->id_count; 
                for ($i=0; $i <$min  ; $i++) {
                   echo "<td style='border: 2px solid  #000000;'></td>";
                }
            ?>
            <td style="border: 3px solid  #000000;"><strong><?php echo $Maintotal; ?></strong></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
        </tr>
        <?php } }?>
    </table>
</html>




