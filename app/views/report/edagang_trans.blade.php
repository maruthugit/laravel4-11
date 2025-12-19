<html>
    <table>
        <tr>
            <td style="font-weight: bold;" align="center">merchant_ID</td>
            <td style="font-weight: bold;" align="center">shipping_state</td>
            <td style="font-weight: bold;" align="center">shipping_country</td>
            <td style="font-weight: bold;" align="center">total_sales</td>
            <td style="font-weight: bold;" align="center">number_of_transactions</td>
            <td style="font-weight: bold;" align="center">number_of_consumers</td>
            <td style="font-weight: bold;" align="center">disbursed_amount</td>
            <td style="font-weight: bold;" align="center">matching_amount</td>
            <td style="font-weight: bold;" align="center">budget_bucket</td>
        </tr>

        @if(count($data) > 0)
        <?php foreach ($data as $merchant_ID => $v){ ?>
            <?php foreach ($v as $shipping_country => $v2){ ?>
                <?php foreach ($v2 as $shipping_state => $val){ ?>
                    <tr>
                        <td><?= $merchant_ID ?></td>
                        <td><?= str_replace('WP-', 'WP ', $shipping_state) ?></td>
                        <td><?= $shipping_country ?></td>
                        <td><?= number_format($val['total_sales'], 2, ".", "") ?></td>
                        <td><?= $val['number_of_transactions'] ?></td>
                        <td><?= $val['number_of_consumers'] ?></td>
                        <td><?= number_format($val['disbursed_amount'], 2, ".", "") ?></td>
                        <td><?= number_format($val['matching_amount'], 2, ".", "") ?></td>
                        <td><?= $val['budget_bucket'] ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        @endif
    </table>
</html>