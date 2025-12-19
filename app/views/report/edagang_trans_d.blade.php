<html>
    <?php $keylist = (isset($data['trans'][0]) ? array_keys($data['trans'][0]) : []); ?>
    <table>
        <!-- TABLE HEADER -->
        <tr>
            <td style="font-weight: bold;" align="center">transaction_ID</td>
            <td style="font-weight: bold;" align="center">transaction_date</td>
            <td style="font-weight: bold;" align="center">invoice_date</td>
            <?= (count(array_intersect($keylist, ['order_number_eleven', 'order_number_shopee', 'order_number_lazada', 'order_number_qoo10'])) > 0 ? '<td style="font-weight: bold;" align="center">Order Number</td>' : '') ?>
            <td style="font-weight: bold;" align="center">customer_username</td>
            <td style="font-weight: bold;" align="center">coupon_code</td>
            <td style="font-weight: bold;" align="center">coupon_amount</td>
            <td style="font-weight: bold;" align="center">merchant_ID</td>
            <td style="font-weight: bold;" align="center">merchant_username</td>
            <td style="font-weight: bold;" align="center">company_name</td>
            <td style="font-weight: bold;" align="center">shipping_country</td>
            <td style="font-weight: bold;" align="center">shipping_state</td>
            <td style="font-weight: bold;" align="center">product_id</td>
            <td style="font-weight: bold;" align="center">product_name</td>
            <td style="font-weight: bold;" align="center">product_total</td>
            <td style="font-weight: bold;" align="center">product_gross_total</td>
            <td style="font-weight: bold;" align="center">delivery_charges</td>
            <td style="font-weight: bold;" align="center">foreign_delivery_charges</td>
            <td style="font-weight: bold;" align="center">trans_total</td>
        </tr>
        
        <!-- DATA ROW -->
        <?php foreach ($data['trans'] as $v){ ?>
        <tr>
            <td><?= $v['trans_id'] ?></td>
            <td><?= $v['trans_date'] ?></td>
            <td><?= $v['inv_date'] ?></td>
            <?php
                if(count(array_intersect($keylist, ['order_number_eleven', 'order_number_shopee', 'order_number_lazada', 'order_number_qoo10'])) > 0){
                    echo '<td>';
                    echo ($v['order_number_eleven'] ? $v['order_number_eleven'] : ($v['order_number_shopee'] ? $v['order_number_shopee'] : ($v['order_number_lazada'] ? $v['order_number_lazada'] : ($v['order_number_qoo10'] ? $v['order_number_qoo10'] : ''))));
                    echo '</td>';
                }
            ?>
            <td><?= $v['customer_username'] ?></td>
            <td><?= $v['coupon_code'] ?></td>
            <td><?= $v['coupon_amount'] ? number_format($v['coupon_amount'], 2) : '' ?></td>
            <td><?= $v['merchant_ID'] ?></td>
            <td><?= $v['merchant_username'] ?></td>
            <td><?= (isset($data['format'][$v['product_id']][$v['merchant_ID']]) ? $data['format'][$v['product_id']][$v['merchant_ID']] : (isset($data['format']['seller'][$v['merchant_ID']]) ? $data['format']['seller'][$v['merchant_ID']] : $v['company_name'])) ?></td>
            <td><?= ($data['c_code'][$v['shipping_country']] ? $data['c_code'][$v['shipping_country']] : $v['shipping_country']) ?></td>
            <td><?= str_replace('WP-', 'WP ', $v['shipping_state']) ?></td>
            <td><?= $v['product_id'] ?></td>
            <td><?= $v['product_name'] ?></td>
            <td><?= $v['pro_total'] ?></td>
            <td><?= $v['pro_gross'] ?></td>
            <td><?= $v['delivery_fees'] ?></td>
            <td><?= $v['foreign_delivery_charges'] ?></td>
            <td><?= $v['trans_total'] ?></td>
        </tr>
        <?php } ?>
        <!-- DATA ROW -->
    </table>
</html>