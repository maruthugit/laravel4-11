<?php

class PointCheckoutController extends BaseController
{
    public function anyIndex()
    {
        if ( ! Input::has('txn_id') &&  ! Input::has('txn_type') && Input::has('user')) {
            $input = [
                'user'         => Input::get('user'),
                'pass'         => Input::get('pass'),
                'qrcode'       => Input::get('qrcode'),
                'price_option' => Input::get('priceopt'),
                'qty'          => Input::get('qty'),
                'devicetype'   => Input::get('devicetype'),
                'lang'         => Input::get('lang', 'EN'),
                'ip_address'   => Input::get('ip', Request::getClientIp()),
                'location'     => Input::get('location', ''),
            ];

            Session::put('lang', array_get($input, 'lang'));
            Session::put('devicetype', array_get($input, 'devicetype'));

            $signalCheck = base64_encode(serialize($input));

            $data = [];

            if (Session::get('checkout_signal_check') && Session::get('checkout_transaction_id')) {
                if (Session::get('checkout_signal_check') == $signalCheck) {
                    $data = [
                        'transaction_id' => Session::get('checkout_transaction_id'),
                        'status'         => 'success',
                        'message'        => 'valid',
                    ];
                } else {
                    Session::forget('checkout_signal_check');
                }
            }

            if ( ! isset($data['transaction_id'])) {
                $data = $this->checkout($input);
            }

            $transactionId = array_get($data, 'transaction_id');

            if (array_get($data, 'status') == 'success' &&  ! empty($transactionId)) {
                Session::put('checkout_signal_check', $signal_check);
                Session::put('checkout_transaction_id', array_get($data, 'transaction_id'));
                Session::put('lang', array_get($data, 'lang'));
                Session::put('devicetype', array_get($data, 'devicetype'));
                Session::put('android_orderid', array_get($data, 'transaction_id'));

                $additionalCheckoutData = $this->getAdditionalCheckoutData($transactionId);
                $data                   = array_merge($data, $additionalCheckoutData);

                if (array_get($data, 'trans_query')) {
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.point_checkout', $data);
                } else {
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view', ['message' => array_get($data, 'message')]);
                }
            } else {
                Session::forget('checkout_signal_check');
                Session::forget('checkout_transaction_id');

                if ( ! array_get($data, 'message')) {
                    $data['message'] = '101';
                }

                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view', ['message' => array_get($data, 'message')]);
            }
        }
    }

    protected function checkout(array $input)
    {
        $qrCodes      = array_get($input, 'qrcode');
        $priceOptions = array_get($input, 'price_option');
        $quantities   = array_get($input, 'qty');

        if ( ! is_array($qrCodes)) {
            return ['status' => 'error', 'message' => '101'];
        }

        $username = array_get($input, 'user');
        $password = array_get($input, 'pass');
        $loggedIn = Customer::check_login($username, $password);

        if ($loggedIn == 'yes') {
            $customer = Customer::where('username', '=', $username)->first();

            if ( ! $customer) {
                return ['status' => 'error', 'message' => '102'];
            }
        } else {
            return ['status' => 'error', 'message' => '102'];
        }

        $data = [
            'transaction_date'    => date('Y-m-d H:i:s'),
            'status'              => 'pending',
            'buyer_id'            => $customer->id,
            'buyer_username'      => $customer->username,
            'delivery_name'       => '',
            'delivery_contact_no' => '',
            'special_msg'         => '',
            'buyer_email'         => $customer->email,
            'delivery_addr_1'     => '',
            'delivery_addr_2'     => '',
            'delivery_postcode'   => '',
            'delivery_city'       => '',
            'delivery_city_id'    => '',
            'delivery_state'      => '',
            'delivery_country'    => '',
            'delivery_charges'    => '',
            'delivery_condition'  => '',
            'process_fees'        => 0,
            'total_amount'        => 0,
            'gst_rate'            => 0,
            'gst_process'         => 0,
            'gst_delivery'        => 0,
            'gst_total'           => 0,
            'insert_by'           => $customer->username,
            'insert_date'         => date('Y-m-d H:i:s'),
            'modify_by'           => $customer->username,
            'modify_date'         => date('Y-m-d H:i:s'),
            'lang'                => array_get($input, 'lang'),
            'ip_address'          => array_get($input, 'ip_address'),
            'location'            => array_get($input, 'location'),
            'no_shipping'         => 1,
        ];
        $transactionDetails = [];

        foreach ($qrCodes as $index => $qrCode) {
            $priceOption = array_get($priceOptions, $index);
            $quantity    = array_get($quantities, $index);

            if ( ! empty($qrCode) && is_numeric($quantity) && $quantity > 0) {
                $product = DB::table('jocom_product_and_package')
                    ->where('qrcode', '=', $qrCode)
                    ->first();

                if ( ! $product) {
                    return ['status' => 'error', 'message' => '106'];
                }

                $pointTypeId = array_get(Config::get('constants.POINTS'), $product->id);

                if ( ! $pointTypeId) {
                    return ['status' => 'error', 'message' => '109'];
                }

                $pointType      = PointType::find($pointTypeId);
                $conversionRate = PointConversionRate::from(PointType::CASH)->to($pointType->id)->active()->first();

                if ((int) $quantity < $conversionRate->minimum) {
                    return ['status' => 'error', 'message' => '113'];
                }

                $resources = $this->addTransactionDetail($product, $priceOption, $quantity, $data, $transactionDetails);

                if (array_get($resources, 'status') == 'error') {
                    return ['status' => 'error', 'message' => '110'];
                    break;
                }

                $data               = array_get($resources, 'data');
                $transactionDetails = array_get($resources, 'transaction_detail');
            }
        }

        $transactionId = DB::table('jocom_transaction')->insertGetId($data);

        foreach ($transactionDetails as $transactionDetail) {
            $transactionDetail['transaction_id'] = $transactionId;

            DB::table('jocom_transaction_details')->insert($transactionDetail);
        }

        return [
            'transaction_id' => $transactionId,
            'status'         => 'success',
            'message'        => 'valid',
            'devicetype'     => array_get($input, 'devicetype'),
            'lang'           => array_get($input, 'lang'),
        ];
    }

    protected function addTransactionDetail($product, $priceOption, $quantity, $data, $transactionDetail)
    {

        $price = DB::table('jocom_product_price')
            ->where('id', '=', $priceOption)
            ->where('product_id', '=', $product->id)
            ->first();

        if ( ! $price) {
            return ['status' => 'error', 'message' => '109'];
        }

        $pointTypeId = array_get(Config::get('constants.POINTS'), $product->id);

        if ( ! $pointTypeId) {
            return ['status' => 'error', 'message' => '109'];
        }

        $pointType      = PointType::find($pointTypeId);
        $conversionRate = PointConversionRate::from(PointType::CASH)->to($pointType->id)->active()->first();
        $unitPrice      = 1 / $conversionRate->rate;
        $totalAmount    = $quantity / $conversionRate->rate;

        $seller = DB::table('jocom_seller')
            ->where('id', '=', $product->sell_id)
            ->first();

        $data['total_amount'] += $totalAmount;
        $transactionDetail[] = [
            'product_id'           => $product->id,
            'sku'                  => $product->sku,
            'price_label'          => $price->label,
            'seller_sku'           => $price->seller_sku,
            'price'                => $unitPrice,
            'unit'                 => $quantity,
            'p_referral_fees'      => $price->p_referral_fees,
            'p_referral_fees_type' => $price->p_referral_fees_type,
            'delivery_time'        => '24 hours',
            'delivery_fees'        => 0,
            'seller_id'            => $seller->id,
            'seller_username'      => $seller->username,
            'gst_rate_item'        => 0,
            'gst_amount'           => 0,
            'gst_seller'           => 0,
            'total'                => $data['total_amount'],
            'transaction_id'       => '',
            'p_option_id'          => $price->id,
            'product_group'        => '',
            'sp_group_id'          => 0,
            'parent_seller'        => $seller->parent_seller,
            'parent_gst_amount'    => 0,
        ];

        return [
            'data'               => $data,
            'transaction_detail' => $transactionDetail,
        ];
    }

    protected function getAdditionalCheckoutData($transactionId)
    {
        $transaction = DB::table('jocom_transaction AS transaction')
            ->select('transaction.*', 'customer.full_name AS name')
            ->leftJoin('jocom_user AS customer', 'transaction.buyer_username', '=', 'customer.username')
            ->where('transaction.id', '=', $transactionId)
            ->first();

        switch ($transaction->lang) {
            case 'CN':
                $transactionDetails = DB::select('SELECT detail.*, (CASE WHEN product.name_cn IS NULL OR product.name_cn = "" THEN product.name ELSE product.name_cn END) AS product_name, product.qrcode AS qrcode FROM jocom_transaction_details AS detail LEFT JOIN jocom_products AS product ON detail.sku = product.sku WHERE detail.transaction_id = '.$transactionId);
                break;
            case 'MY':
                $transactionDetails = DB::select('SELECT detail.*, (CASE WHEN product.name_my IS NULL OR product.name_my = "" THEN product.name ELSE product.name_my END) AS product_name, product.qrcode AS qrcode FROM jocom_transaction_details AS detail LEFT JOIN jocom_products AS product ON detail.sku = product.sku WHERE detail.transaction_id = '.$transactionId);
                break;
            case 'EN':
            default:
                $transactionDetails = DB::table('jocom_transaction_details AS detail')
                    ->select('detail.*', 'product.name AS product_name', 'product.qrcode AS qrcode')
                    ->leftJoin('jocom_products AS product', 'detail.sku', '=', 'product.sku')
                    ->where('detail.transaction_id', '=', $transactionId)
                    ->get();
                break;
        }

        return array_merge(['trans_query' => $transaction, 'trans_detail_query' => $transactionDetails], MCheckout::molpay_conf(), MCheckout::mpay_conf());
    }
}
