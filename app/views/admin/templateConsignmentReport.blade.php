<html>
    <!-- Headings -->
    <table>
        <?php
            // Check date
            $c_date_format = [
                1 => 'transaction date',
                2 => 'insert date',
                3 => 'invoice date',
            ];
            $created_date_method = (isset($input['created']) && $input['created'] ? $input['created'] : 0);
            $created_date_from = (isset($input['created_from']) && $input['created_from'] && $created_date_method ? $input['created_from'] . ' 00:00:00' : 0);
            $created_date_to = (isset($input['created_to']) && $input['created_to'] && $created_date_method ? $input['created_to'] . ' 23:59:59' : 0);
            // GRN check date
            $grn_d_format = [
                1 => 'po_date',
                2 => 'delivery_date',
                3 => 'created_at',
                4 => 'grn_date',
            ];
            $platform = array_keys($data['platform']);
        ?>
        <tr>
            <td><strong>VENDOR</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ $seller_name }}</td>
        </tr>
        <tr>
            <td><strong>DATE CHECK METHOD</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ ($created_date_method ? $c_date_format[$created_date_method] : '-') }}</td>
        </tr>
        <tr>
            <td><strong>FROM DATE</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ ($created_date_from ? $created_date_from : '-') }}</td>
        </tr>
        <tr>
            <td><strong>TO DATE</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ ($created_date_to ? $created_date_to : '-') }}</td>
        </tr>
        <tr>
            <td><strong>PREPARED BY</strong></td>
            <td></td>
            <td style="text-align: left;">: CMS SYSTEM</td>
        </tr>
        <tr>
            <td><strong>GRN DATE BY</strong></td>
            <td></td>
            <td style="text-align: left;">{{ $grn_d_format[(int)$input['grn_date']] }}</td>
        </tr>
        <tr>
            <td><strong>GRN FROM DATE</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ ($created_date_from ? date('Y-m-d', strtotime("-1 month", strtotime($created_date_from))) : '-') }}</td>
        </tr>
        <tr>
            <td><strong>GRN TO DATE</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ ($created_date_to ? date('Y-m-d', strtotime("-1 month", strtotime($created_date_to))) : '-') }}</td>
        </tr>
        <tr>
            <td><strong>Total GRN Stock IN</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ (int)$data['grn_count'][1] + (int)$data['grn_count'][1] }}</td>
        </tr>
        <tr>
            <td><strong>Total GRB GS</strong></td>
            <td></td>
            <td style="text-align: left;">: {{ $data['grn_count'][3] }}</td>
        </tr>
        <tr></tr>
        <tr></tr>
    </table>
    
    <table style="border: 2px solid  #000000;" border="1">
        <!-- START - COUNT BASE DATA -->
            <tr>
                <td></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Item</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Product Name</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Label</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Lazada</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Shopee</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>JOCOM</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Cost / RM</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Total Qty</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Total Cost / RM</strong></td>
            </tr>
            <?php $count_index = 1; ?>
            @if(count($data['base_count']) > 0)
            @foreach ($data['base_count'] as $value)
            <tr>
                <td></td>
                <td style="text-align: center;">{{ $count_index }}</td>
                <td style="text-align: center;">{{ $value->product_name }}</td>
                <td style="text-align: center;">{{ strip_tags($value->option_name) }}</td>
                <?php
                    $trans_idiv_salescount = explode(',', $value->trans_idiv_info);
                    $trans_idiv_platform = explode(',', $value->trans_idiv_platform);
                    $p = ['lazada' => 0, 'shopee' => 0, 'jocom' => 0];
                    foreach($trans_idiv_salescount AS $k => $info){
                        if(in_array($trans_idiv_platform[$k], ['lazada', 'shopee'])){
                            $p[$trans_idiv_platform[$k]] += (int)$info;
                        }else{
                            $p['jocom'] += (int)$info;
                        }
                    }
                ?>
                <td>{{ $p['lazada'] }}</td>
                <td>{{ $p['shopee'] }}</td>
                <td>{{ $p['jocom'] }}</td>
                <td>{{ $value->cost_price }}</td>
                <td style="text-align: center;">{{ (int)$value->total_sell }}</td>
                <td>{{ (int)$value->total_sell * $value->cost_price }}</td>
            </tr>
            <?php $count_index++; ?>
            @endforeach
            @endif
            @if(count($data['pack_count']) > 0)
            @foreach ($data['pack_count'] as $value)
            <tr>
                <td></td>
                <td style="text-align: center;">{{ $count_index }}</td>
                <td style="text-align: center;">{{ $value->product_name }}</td>
                <td style="text-align: center;">{{ strip_tags($value->option_name) }}</td>
                <?php
                    $trans_idiv_salescount = explode(',', $value->trans_idiv_info);
                    $trans_idiv_platform = explode(',', $value->trans_idiv_platform);
                    $p = ['lazada' => 0, 'shopee' => 0, 'jocom' => 0];
                    foreach($trans_idiv_salescount AS $k => $info){
                        if(in_array($trans_idiv_platform[$k], ['lazada', 'shopee'])){
                            $p[$trans_idiv_platform[$k]] += (int)$info * (int)$value->base_qty;
                        }else{
                            $p['jocom'] += (int)$info * (int)$value->base_qty;
                        }
                    }
                ?>
                <td>{{ $p['lazada'] }}</td>
                <td>{{ $p['shopee'] }}</td>
                <td>{{ $p['jocom'] }}</td>
                <td>{{ $value->cost_price }}</td>
                <td style="text-align: center;">{{ (int)$value->total_sell * (int)$value->base_qty }}</td>
                <td>{{ (int)$value->total_sell * $value->cost_price }}</td>
            </tr>
            <?php $count_index++; ?>
            @endforeach
            @endif
            <tr></tr>
        <!-- END - COUNT BASE DATA -->

        <!-- START - DATA ROW -->
            <tr>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Trans ID</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Invoice No</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Invoice Date</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Product Name</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Product Label</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Order Qty</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Logistic ID</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Logistic Status</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Logistic Item Status</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Transaction Status</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Deliver Date</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Platform</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Base Product Name</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Base Product Label</strong></td>
                <td style="text-align: center; background-color: #000; color: #fff;"><strong>Base Product Qty</strong></td>
            </tr>
            <?php if(count($data['base']) > 0){ ?>
            <?php foreach ($data['base'] as $value) { ?>
            <tr>
                <td style="text-align: center;">{{ $value->transaction_id }}</td>
                <td style="text-align: center;">{{ $value->invoice_no }}</td>
                <td style="text-align: center;">{{ $value->invoice_date }}</td>
                <td style="text-align: center;">{{ $value->product_name }}</td>
                <td style="text-align: center;">{{ strip_tags($value->option_name) }}</td>
                <td style="text-align: center;">{{ (int)$value->unit }}</td>
                <td style="text-align: center;">{{ $value->logistic_id }}</td>
                <td style="text-align: center;">{{ LogisticTransaction::get_status($value->logistic_status) }}</td>
                <td style="text-align: center;">{{ LogisticTransaction::get_status($value->logistic_item_status) }}</td>
                <td style="text-align: center;">{{ $value->transaction_status }}</td>
                <td style="text-align: center;">{{ $value->delivery_time }}</td>
                <td style="text-align: center;">{{ in_array($value->buyer_username, $platform) ? $data['platform'][$value->buyer_username] : 'JOCOM' }}</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
            </tr>
            <?php $count_index++; ?>
            <?php } ?>
            <?php } ?>
            <?php if(count($data['pack']) > 0){ ?>
            <?php foreach ($data['pack'] as $value) {  ?>
            <tr>
                <td style="text-align: center;">{{ $value->transaction_id }}</td>
                <td style="text-align: center;">{{ $value->invoice_no }}</td>
                <td style="text-align: center;">{{ $value->invoice_date }}</td>
                <td style="text-align: center;">{{ $value->product_name }}</td>
                <td style="text-align: center;">{{ strip_tags($value->option_name) }}</td>
                <td style="text-align: center;">{{ (int)$value->unit }}</td>
                <td style="text-align: center;">{{ $value->logistic_id }}</td>
                <td style="text-align: center;">{{ LogisticTransaction::get_status($value->logistic_status) }}</td>
                <td style="text-align: center;">{{ LogisticTransaction::get_status($value->logistic_item_status) }}</td>
                <td style="text-align: center;">{{ $value->transaction_status }}</td>
                <td style="text-align: center;">{{ $value->delivery_time }}</td>
                <td style="text-align: center;">{{ in_array($value->buyer_username, $platform) ? $data['platform'][$value->buyer_username] : 'JOCOM' }}</td>
                <td style="text-align: center;">{{ $value->base_product_name }}</td>
                <td style="text-align: center;">{{ strip_tags($value->base_option_name) }}</td>
                <td style="text-align: center;">{{ $value->base_qty }}</td>
            </tr>
            <?php $count_index++; ?>
            <?php } ?>
            <?php } ?>
        <!-- END - DATA ROW -->
    </table>

</html>