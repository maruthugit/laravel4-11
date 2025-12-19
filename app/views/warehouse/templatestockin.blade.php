<html>
    <!-- Headings -->
    <table>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="10"><h1>Inventory Stock</h1></td>
    </tr>
    <tr>
         <td>&nbsp;</td>
    </tr>
    </table>

    @foreach ($company_list as $seller => $details)
    <table>
        <tr>
            <td colspan="5"><h3>{{$seller}}</h3></td>
        </tr>
        <tr>
            <td style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;">SKU</td>
            <td style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;">Product Name</td>
            <td style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;">Stock In Hand</td>
            <td style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;">Expiry Date</td>
            <td style="border: 2px solid  #000000;background-color: #ddd; vertical-align: middle;">Last Stock In Date</td>
        </tr>
        @foreach ($details as $sku => $detail)

        <tr>
            <?php 
                $td_style = 'border: 2px solid  #000000;';
                if ($detail['expired'] == 1) {
                    $td_style = $td_style . 'background-color:#ffff00';
                } else if ($detail['expired'] == 2) {
                    $td_style = $td_style . 'background-color:#ff0000';
                }
            ?>
            <td style="<?php echo $td_style; ?>">{{$sku}}</td>
            <td style="<?php echo $td_style; ?>">{{$detail['name']}}</td>
            <td style="<?php echo $td_style; ?>">{{$detail['stockin_hand']}}</td>
            <td style="<?php echo $td_style; ?>">{{$detail['expiry_date']}}</td>
            <td style="<?php echo $td_style; ?>">{{$detail['last_stockin']}}</td>
        </tr>
        @endforeach
    </table>
    @endforeach

</html>