<html>
    <table>
        <!-- TABLE HEADER -->
        <tr>
            <td style="font-weight: bold;" align="center">merchant_ID</td>
            <td style="font-weight: bold;" align="center">onboarding_date</td>
            <td style="font-weight: bold;" align="center">new/existing</td>
            <td style="font-weight: bold;" align="center">com/ind_name</td>
            <td style="font-weight: bold;" align="center">biz_type</td>
            <td style="font-weight: bold;" align="center">biz_NEW_brn</td>
            <td style="font-weight: bold;" align="center">ind_IC_num</td>
            <td style="font-weight: bold;" align="center">state</td>
            <td style="font-weight: bold;" align="center">postcode</td>
            <td style="font-weight: bold;" align="center">ind_gender</td>
            <td style="font-weight: bold;" align="center">biz_sector</td>
            <td style="font-weight: bold;" align="center">email</td>
            <td style="font-weight: bold;" align="center">mobile</td>
            <td style="font-weight: bold;" align="center">business_owned_women</td>
            <td style="font-weight: bold;" align="center">tourism_focus</td>
            <td style="font-weight: bold;" align="center">halal/agro_focus</td>
            <td style="font-weight: bold;" align="center">social/craft_focus</td>
            <td style="font-weight: bold;" align="center">MiM_focus</td>
            <td style="font-weight: bold;" align="center">retail_focus</td>
            <td style="font-weight: bold;" align="center">services_focus</td>
            <td style="font-weight: bold;" align="center">attended_training</td>
        </tr>

        <?php foreach ($data['seller'] as $v){ ?>
        <tr>
            <?php $l_cname = strtolower($v['company_name']);  ?>
            <td><?= $v['merchant_ID'] ?></td>
            <td><?= date('l, F d, Y', strtotime($v['onboard_date'])) ?></td>
            <td><?= (strtotime($v['onboard_date']) >= 1640966400000 ? 'New' : 'Existing') ?></td>
            <td><?= $v['company_name'] ?></td>
            <td><?= (preg_match('/(sdn[\.]? bhd[\.]?)/', $l_cname) ? 'Sdn Bhd' : (preg_match('/(enterprise)/', $l_cname) ? 'Enterprise' : (preg_match('/(llc|pte ltd)/', $l_cname) ? 'Limited Liability Company' : ''))) ?></td>
            <td><?= $v['reg_num'] ?></td>
            <td><?= $v['ic_no'] ?></td>
            <td><?= str_replace('WP-', 'WP ', $data['state_list'][$v['state']]) ?></td>
            <td><?= $v['postcode'] ?></td>
            <td></td>
            <td></td>
            <td><?= $v['email'] ?></td>
            <td><?= $v['mobile'] ?></td>
            <td>no</td>
            <td>no</td>
            <td>yes</td>
            <td>no</td>
            <td>no</td>
            <td>no</td>
            <td>no</td>
            <td>no</td>
        </tr>
        <?php } ?>
    </table>
</html>