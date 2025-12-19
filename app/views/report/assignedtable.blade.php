
<html>

<!-- HEADER -->
    <table>
        <tr>
            <td style="width: 10% !important;">Driver : <?php echo "<strong>".$data['driver_name']."</strong>"; ?></td>
        </tr>
        <tr>
            <td style="width: 10% !important;">Date : <?php echo $data['transaction_from']; ?> - <?php echo $data['transaction_to']; ?></td>
        </tr>
    </table>

    <table style="border: 2px solid  #000000 !important;" border="2">
        <tr>
            <th style="border: solid 1px #fff;border-bottom: solid 1px #000000;">&nbsp;</th>
            <th>&nbsp;</th>
            <th><center><strong>Stock Out</strong></center></th>
            <th>&nbsp;</th>
            <th>&nbsp;<center><strong>Stock Return</strong></center></th>
        </tr>
        <tr>
            <th style="border: 2px solid  #000000;background-color: #ddd;">Product</th>
            <th style="border: 2px solid  #000000;">Total</th>
            <th style="border: 2px solid  #000000;">Check by Store</th>
            <th style="border: 2px solid  #000000;">Check by Driver</th>
            <th style="border: 2px solid  #000000;">Check by Store</th>
            <th style="border: 2px solid  #000000;">Check by Driver</th>
        </tr>
            <!-- start loop -->
            <?php 
            if ($data['logistic'] !='') {
                foreach ($data['logistic'] as $key => $value) { ?>
            <tr>
                <td style="border: 2px solid  #000000;">{{$value->name}} <?php if($value->label!=''){ echo '('.$value->label.')';} ?></td>
                <td style="border: 2px solid  #000000;">{{$value->qty_assign}}</td>
                <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
                <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
                <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
                <td style="border: 3px solid  #000000 !important;border-bottom: solid 1px #fff;" >&nbsp;</td>
            </tr>
            <tr>
                <td style="border: 3px solid  #000000 !important;"></td>
                <td style="border: 3px solid  #000000 !important;"></td>
                <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
                <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
                <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
                <td style="border: 3px solid  #000000 !important;border-top: solid 1px #fff;"></td>
            </tr>
            <!-- end loop -->
            <?php } } ?>
    </table>
</html>




