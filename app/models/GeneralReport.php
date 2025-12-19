<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class GeneralReport extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;

    public static function get_product($data = null) 
    {

        $products = DB::table('jocom_products AS a')
                ->select(DB::raw("a.sku AS sku, a.name AS name, b.label AS label, c.company_name AS seller, group_concat(distinct e.category_name separator ', ') AS category, group_concat(distinct e.id separator ', ') AS categoryID, b.qty AS qty, b.stock AS stock, b.price AS price, b.price_promo AS promo, b.p_referral_fees AS referral_fees, b.p_referral_fees_type AS referral_fees_type, a.insert_date AS insert_date, a.modify_date AS modify_date, jpps.cost_price, 
                    (CASE 
                    WHEN a.status=0 THEN 'Inactive' 
                    WHEN a.status=1 THEN 'Active' 
                    ELSE 'Delete' 
                    END) AS status"))
                ->leftJoin('jocom_product_price AS b', 'a.id', '=', 'b.product_id')
                ->leftJoin('jocom_seller AS c', 'a.sell_id', '=', 'c.id')
                ->leftJoin('jocom_categories AS d', 'a.id', '=', 'd.product_id')
                ->leftjoin('jocom_product_price_seller AS jpps','jpps.product_price_id','=','b.id')
                ->leftJoin('jocom_products_category AS e', 'd.category_id', '=', 'e.id')
                ->whereIn('a.status', $data['status'])
                ->where('b.status', '=', '1')
                ->where('jpps.activation', '=', '1')
                ->where(function($query) use ($data)
                    {
                        if ($data['seller'] == NULL OR $data['seller'] == 'all')
                            $nothing;
                        else
                            $query->where('c.id', '=',  $data['seller']);
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['categories'] == NULL OR $data['categories'] == 'all')
                            $nothing;
                        else
                            $query->whereIn('e.id', $data['categories']);
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['quantity'] == '0')
                            $nothing;
                        else
                            $query->whereBetween('b.qty', array($data['quantity_from'],$data['quantity_to']));
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['stock'] == '0')
                            $nothing;
                        else
                            $query->whereBetween('b.stock', array($data['stock_from'],$data['stock_to']));
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['price'] == '0')
                            $nothing;
                        else
                            $query->whereBetween('b.price', array($data['price_from'],$data['price_to']));
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['referral'] == '0')
                            $nothing;
                        elseif ($data['referral'] == '1')
                            $query->whereBetween('b.p_referral_fees', array($data['referral_from'],$data['referral_to']))
                                  ->where('b.p_referral_fees_type', '=', 'N');
                        else
                            $query->whereBetween('b.p_referral_fees', array($data['referral_from'],$data['referral_to']))
                                  ->where('b.p_referral_fees_type', '=', '%');
                    })
                ->where(function($query) use ($data)
                    {
                        if ($data['created'] == '0')
                            $nothing;
                        elseif ($data['created'] == '1')
                            $query->whereBetween('a.insert_date', array($data['created_from'],$data['created_to']." 23:59:59"));
                        else
                            $query->whereBetween('a.modify_date', array($data['created_from'],$data['created_to']." 23:59:59"));
                    })
                ->groupBy('a.id');
                // ->groupBy('b.label')
                // ->get();


        if ($data['group_label'] == '1')
            $products = $products->groupBy('b.label');

        $products = $products->get();

        return $products;
        
    }

    public static function get_transaction($data = null)
    {
        try{
            
        
        
        $amountFrom = array_get($data, 'amount_from');
        $amountTo   = array_get($data, 'amount_to');
        $pointAction1 = PointAction::EARN;
        $pointAction2 = PointAction::REVERSAL;
        $gateway        = array_get($data, 'gateway');
        $payment_status = '';
        
        if (array_get($data, 'amount') == '0') {
            $having = 'actual_total IS NOT NULL';
        } else {
            $having = "actual_total BETWEEN {$amountFrom} AND {$amountTo}";
        }

        $regionid = array_get($data, 'region_id');
        $stateName = array();
        $State = State::getStateByRegion($regionid);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }


        if (array_get($data, 'breakdown') == '0') {
            $transactions = DB::table('jocom_transaction AS transactions')
                ->select(
                    'transactions.id',
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m-%d") AS date'),
                    'transactions.invoice_no AS invoice_no',
                    'transactions.invoice_date AS invoice_date',
                    'transactions.do_no AS do_no',
                    'parent.parent_inv AS parent_invoice',
                    'transactions.buyer_username AS buyer_username',
                    'transactions.delivery_name AS delivery_name',
                    'transactions.delivery_contact_no AS delivery_contact_no',
                    'transactions.delivery_state AS delivery_state',
                    'transactions.status AS status',
                    'transactions.external_ref_number AS external_ref_number',
                    'eleven.order_number AS order_number_eleven', //11Street Order No.
                    'shopee.ordersn AS order_number_shopee', //11Street Order No.
                    'lazada.order_number AS order_number_lazada', //11Street Order No.
                    'qoo10.packNo AS order_number_qoo10', //11Street Order No.
                    'transactions.delivery_charges AS delivery_charges',
                    'quo.delivery_charges AS quo_delivery_charges',
                    'transactions.process_fees AS process_fees',
                    'transactions.gst_delivery AS gst_delivery',
                    'quo.gst_delivery AS quo_gst_delivery',
                    'transactions.gst_process AS gst_process',
                    'transactions.gst_total AS gst_total',
                    'quo.gst_total AS quo_gst_total',
                    'transactions.total_amount AS total_amount',
                    'quo.total_amount AS quo_total_amount',
                    'transactions.special_msg AS special_msg',
                    'agents.agent_code AS agent_code',
                    'coupons.coupon_code AS coupon_code',
                    'mpay.id AS mpayid',
                    'molpay.id AS molpayid',
                    'paypal.id AS paypalid',
                    'boost.id AS boostid',
                    'revpay.id AS revpayid',
                    'grabpay.id AS grabpayid',
                    DB::raw("
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point)
                            ELSE 0
                        END) AS point_earned,
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point * points.rate)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point * points.rate)
                            ELSE 0
                        END) AS point_earned_value,
                        (CASE
                            WHEN coupons.coupon_amount IS NULL THEN 0
                            ELSE ROUND(coupons.coupon_amount, 2)
                        END) AS coupon_amount,
                        (CASE
                            WHEN c.free_delivery THEN '10'
                            ELSE '0'
                        END) AS coupon_delivery_fee,
                        (CASE
                            WHEN c.free_process THEN '5'
                            ELSE '0'
                        END) AS coupon_processing_fee,
                        (CASE
                            WHEN transaction_point.point IS NULL THEN 0
                            ELSE transaction_point.point
                        END) AS point_redeemed,
                        (CASE
                            WHEN transaction_point.amount IS NULL THEN 0
                            ELSE transaction_point.amount
                        END) AS point_amount,
                        ROUND((
                            transactions.total_amount -
                            (CASE WHEN coupons.coupon_amount IS NULL THEN 0 ELSE ROUND(coupons.coupon_amount, 2) END) -
                            (CASE WHEN transaction_point.amount IS NULL THEN 0 ELSE transaction_point.amount END) +
                            transactions.gst_total
                        ), 2) AS actual_total
                    ")
                )
                ->leftJoin('jocom_agents AS agents', 'transactions.agent_id', '=', 'agents.id')
                ->leftJoin('jocom_transaction_coupon AS coupons', 'transactions.id', '=', 'coupons.transaction_id')
                ->leftJoin('jocom_transaction_qoo10 AS quo', 'quo.id', '=', 'transactions.id') // FOR QUO10
                ->leftJoin('jocom_coupon AS c', 'c.coupon_code', '=', 'coupons.coupon_code')                
                ->leftJoin('jocom_transaction_point AS transaction_point', 'transactions.id', '=', 'transaction_point.transaction_id')
                ->leftjoin('jocom_elevenstreet_order AS eleven', 'transactions.id', '=', 'eleven.transaction_id')  //11street Order Number
                ->leftjoin('jocom_shopee_order AS shopee', 'transactions.id', '=', 'shopee.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_qoo10_order AS qoo10', 'transactions.id', '=', 'qoo10.transaction_id')  //qoo10 Order Number
                ->leftjoin('jocom_lazada_order AS lazada', 'transactions.id', '=', 'lazada.transaction_id')  //lazada Order Number
                ->leftJoin('point_transactions AS points', function ($join) use ($pointAction1, $pointAction2) {
                    $join->on(DB::raw('(points.transaction_id = transactions.id AND (points.point_action_id = '.PointAction::EARN.' OR points.point_action_id = '.PointAction::REVERSAL.'))'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_transaction_parent_invoice AS parent', 'transactions.id', '=', 'parent.transaction_id')
                // ->leftJoin('jocom_mpay_transaction AS mpay', 'transactions.id', '=', 'mpay.transaction_id')
                // ->leftJoin('jocom_molpay_transaction AS molpay', 'transactions.id', '=', 'molpay.transaction_id')
                // ->leftJoin('jocom_paypal_transaction AS paypal', 'transactions.id', '=', 'paypal.transaction_id')
                ->leftJoin('jocom_revpay_transaction AS revpay', function ($join) use ($data) {
                    $join->on(DB::raw('(revpay.transaction_id = transactions.id AND revpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_mpay_transaction AS mpay', function ($join) use ($data) {
                    $join->on(DB::raw('(mpay.transaction_id = transactions.id AND mpay.payment_status = "0")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_molpay_transaction AS molpay', function ($join) use ($data) {
                    $join->on(DB::raw('(molpay.transaction_id = transactions.id AND molpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_paypal_transaction AS paypal', function ($join) use ($data) {
                    $join->on(DB::raw('(paypal.transaction_id = transactions.id AND paypal.payment_status = "Completed")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_boost_transaction AS boost', function ($join) use ($data) {
                    $join->on(DB::raw('(boost.transaction_id = transactions.id AND boost.transaction_status = "completed")'), DB::raw(''), DB::raw(''));
                })
               
                 ->leftJoin('jocom_grabpay_transaction AS grabpay', function ($join) use ($data) {
                    $join->on(DB::raw('(grabpay.transaction_id = transactions.id AND grabpay.status = "success")'), DB::raw(''), DB::raw(''));
                });
                // if (array_get($data, 'gateway') != '0') {
                //     switch (array_get($data, 'gateway')) {
                //         case '1': $gateway = 'mpay';
                //             break;
                        
                //         case '2': $gateway = 'molpay';
                //             break;

                //         case '3': $gateway = 'paypal';
                //             break;
                //     }

                //     $transactions = $transactions->leftJoin('jocom_'.$gateway.'_transaction as '.$gateway, $gateway.'.transaction_id', '=', 'transactions.id');
                // }
                if(count($stateName) > 0){
                    $transactions = $transactions->whereIn('transactions.delivery_state', $stateName);
                }
                
                $transactions = $transactions->whereIn('transactions.status', array_get($data, 'status'))
                ->where(function ($query) use ($data) {
                    $platform = array_get($data, 'buyer_username');
                    
                    if (array_get($data, 'customer') == NULL || array_get($data, 'customer') == 'all') {
                        if (array_search('jocom', $platform) === FALSE) {
                            $query->whereIn('transactions.buyer_username', array_get($data, 'buyer_username'));
                        } else {
                            $not_in = array_diff(['prestomall', 'lazada', 'Qoo10', 'shopee', 'Astro Go Shop'], $platform);
                            $query->whereNotIn('transactions.buyer_username', $not_in);
                        }
                    } else {
                        $query->where('transactions.buyer_username', '=', array_get($data, 'customer'));
                    }

                })

                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'company') == 1) {
                        $query->where('transactions.buyer_username', '!=', 'kitkat');
                        
                    } else{
                        // DO NOTHING
                    }
                })
                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'agent') == NULL || array_get($data, 'agent') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.agent_id', '=', array_get($data, 'agent'));
                    }
                })
                ->where(function ($query) use ($data) {
                    switch (array_get($data, 'created')) {
                        case '1':
                            $query->whereBetween('transactions.transaction_date', [array_get($data, 'created_from').' 00:00:00', array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '2':
                            $query->whereBetween('transactions.insert_date', [array_get($data, 'created_from').' 00:00:00', array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '3':
                            $query->whereBetween('transactions.invoice_date', [array_get($data, 'created_from'), array_get($data, 'created_to')]);
                            break;
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'special_msg') != '0') {
                        $query->where('transactions.special_msg', '!=', '');
                    }
                });
                // ->where(function ($query) use ($data) {
                //     if(array_get($data, 'gateway') != '0') {
                //         switch (array_get($data, 'gateway')) {
                //             case '1':   $gateway = 'mpay';
                //                         $payment_status = '0';
                //                         break;

                //             case '2':   $gateway = 'molpay';
                //                         $payment_status = '00';
                //                         break;

                //             case '3':   $gateway = 'paypal';
                //                         $payment_status = 'Completed';
                //                         break;
                //         }

                //         $query->where($gateway.'.payment_status', '=', $payment_status);
                //     }
                // });
                // ->where('mpay.payment_status', '=', '0')
                // ->where('molpay.payment_status', '=', '00')
                // ->where('paypal.payment_status', '=', 'Completed');

                if ((array_get($data, 'created') == 0) || (array_get($data, 'created') == 1)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.transaction_date')
                    ->orderBy('transactions.id')
                    ->get();
                }
                if ((array_get($data, 'created') == 2)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.insert_date')
                    ->orderBy('transactions.id')
                    ->get();
                }
                if ((array_get($data, 'created') == 3)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.invoice_date')
                    ->orderBy('transactions.id')
                    ->get();
                }
                // $transactions = $transactions->havingRaw($having)
                // ->groupBy('transactions.id')
                // ->orderBy('transactions.transaction_date')
                // ->orderBy('transactions.id')
                // ->get();
                // Log::info(DB::getQueryLog());
        } else {
            // echo "INN";
            $products     = explode(',', array_get($data, 'product'));
            // print_r($products);
            $transactions = DB::table('jocom_transaction AS transactions')
                ->select(
                    'transactions.id',
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m-%d") AS date'),
                    'transactions.invoice_no AS invoice_no',
                    'transactions.invoice_date AS invoice_date',
                    'transactions.do_no AS do_no',
                    'parent.parent_inv AS parent_invoice',
                    'transactions.buyer_username AS buyer_username',
                    'transactions.delivery_name AS delivery_name',
                    'transactions.delivery_contact_no AS delivery_contact_no',
                    'transactions.delivery_state AS delivery_state',
                    'JSL.company_name AS seller_name',
                    'transactions.status AS status',
                    'transactions.external_ref_number AS external_ref_number',
                    'eleven.order_number AS order_number_eleven', //11Street Order No.
                    'shopee.ordersn AS order_number_shopee', //11Street Order No.
                    'lazada.order_number AS order_number_lazada', //11Street Order No.
                    'qoo10.packNo AS order_number_qoo10', //11Street Order No.
                    'transactions.delivery_charges AS delivery_charges',
                    'quo.delivery_charges AS quo_delivery_charges',
                    'transactions.process_fees AS process_fees',
                    'transactions.gst_delivery AS gst_delivery',
                    'quo.gst_delivery AS quo_gst_delivery',
                    'transactions.gst_process AS gst_process',
                    'transactions.gst_total AS gst_total',
                    'quo.gst_total AS quo_gst_total',
                    'transactions.total_amount AS total_amount',
                    'quo.total_amount AS quo_total_amount',
                    'transactions.special_msg AS special_msg',
                    'agents.agent_code AS agent_code',
                    'coupons.coupon_code AS coupon_code',
                    'mpay.id AS mpayid',
                    'molpay.id AS molpayid',
                    'paypal.id AS paypalid',
                    'boost.id AS boostid',
                    'revpay.id AS revpayid',
                    'grabpay.id AS grabpayid',
                    'transaction_details.sku AS sku',
                    'product_details.name AS product_name',
                    'transaction_details.price_label AS price_label',
                    'JPB.product_base_id AS base_product_id', // Base Product ID
                    DB::raw("(CASE WHEN JPB.product_id IS NOT NULL THEN (select distinct JPN.name from jocom_products JPN where JPN.id = JPB.product_base_id) END) AS base_product_name"), // Base Product Name
                    DB::raw("(CASE WHEN transaction_details.original_price > 0 THEN transaction_details.original_price ELSE transaction_details.price END) AS actual_price"),
                    'transaction_details.price AS price', 
                    'quodtl.price AS quo_price', 
                    'transaction_details.actual_total_amount AS actual_total_amount', 
                    'transaction_details.actual_price_gst_amount AS actual_price_gst_amount', 
                    // DB::raw('SUM(transaction_details.unit) AS unit'),// 'transaction_details.unit AS unit',
                    // DB::raw('SUM(transaction_details.price * transaction_details.unit) AS total_item_amount'),
                    'transaction_details.unit AS unit',// 'transaction_details.unit AS unit',
                    'transaction_details.cost_unit_amount AS cost_unit_amount',
                    'transaction_details.cost_amount AS cost_amount',
                    'transaction_details.price * transaction_details.unit AS total_item_amount',
                    'transaction_details.p_referral_fees AS p_referral_fees',
                    'transaction_details.p_referral_fees_type AS p_referral_fees_type',
                    'transaction_details.seller_username AS seller_username',
                    'transaction_details.disc AS disc',
                    'transaction_details.gst_rate_item AS gst_rate_item',
                    'transaction_details.gst_amount AS gst_amount',//'transaction_details.gst_amount AS gst_amount',
                    'quodtl.gst_amount AS quo_gst_amount', //
                    'transaction_details.gst_seller AS gst_seller',//'transaction_details.gst_seller AS gst_seller',
                    'transaction_details.parent_po AS parent_po',
                    'transaction_details.po_no AS po_no',
                    'transaction_details.platform_original_price AS platform_original_price',
                    DB::raw("
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point)
                            ELSE 0
                        END) AS point_earned,
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point * points.rate)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point * points.rate)
                            ELSE 0
                        END) AS point_earned_value,
                        (CASE
                            WHEN coupons.coupon_amount IS NULL THEN 0
                            ELSE ROUND(coupons.coupon_amount, 2)
                        END) AS coupon_amount,
                        (CASE
                            WHEN c.free_delivery THEN '10'
                            ELSE '0'
                        END) AS coupon_delivery_fee,
                        (CASE
                            WHEN c.free_process THEN '5'
                            ELSE '0'
                        END) AS coupon_processing_fee,
                        (CASE
                            WHEN transaction_point.point IS NULL THEN 0
                            ELSE transaction_point.point
                        END) AS point_redeemed,
                        (CASE
                            WHEN transaction_point.amount IS NULL THEN 0
                            ELSE transaction_point.amount
                        END) AS point_amount,
                        ROUND((
                            transactions.total_amount -
                            (CASE WHEN coupons.coupon_amount IS NULL THEN 0 ELSE ROUND(coupons.coupon_amount, 2) END) -
                            (CASE WHEN transaction_point.amount IS NULL THEN 0 ELSE transaction_point.amount END) +
                            transactions.gst_total
                        ), 2) AS actual_total
                    ")
                )
                ->leftJoin('jocom_agents AS agents', 'transactions.agent_id', '=', 'agents.id')
                ->leftJoin('jocom_transaction_coupon AS coupons', 'transactions.id', '=', 'coupons.transaction_id')
                ->leftJoin('jocom_transaction_qoo10 AS quo', 'quo.id', '=', 'transactions.id') // FOR QUO10
                ->leftJoin('jocom_coupon AS c', 'c.coupon_code', '=', 'coupons.coupon_code')                
                ->leftJoin('jocom_transaction_point AS transaction_point', 'transactions.id', '=', 'transaction_point.transaction_id')
                ->leftjoin('jocom_elevenstreet_order AS eleven', 'transactions.id', '=', 'eleven.transaction_id')  //11street Order Number
                ->leftjoin('jocom_shopee_order AS shopee', 'transactions.id', '=', 'shopee.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_qoo10_order AS qoo10', 'transactions.id', '=', 'qoo10.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_lazada_order AS lazada', 'transactions.id', '=', 'lazada.transaction_id')  //Shopee Order Number
                ->leftJoin('point_transactions AS points', function ($join) use ($pointAction1, $pointAction2) {
                    $join->on(DB::raw('(points.transaction_id = transactions.id AND (points.point_action_id = '.PointAction::EARN.' OR points.point_action_id = '.PointAction::REVERSAL.'))'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_transaction_details AS transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->leftJoin('jocom_seller AS JSL', 'JSL.username', '=', 'transaction_details.seller_username')
                ->leftJoin('jocom_transaction_parent_invoice AS parent', 'transactions.id', '=', 'parent.transaction_id')
                ->leftJoin('jocom_products AS product_details', 'product_details.id', '=', 'transaction_details.product_id')
                ->leftjoin('jocom_product_base_item AS JPB','JPB.product_id','=','product_details.id')
                ->leftJoin('jocom_transaction_details_qoo10 as quodtl', function($join){
                    $join->on('quodtl.product_id', '=', 'transaction_details.product_id');
                    $join->on('quodtl.transaction_id', '=', 'transactions.id');
                })
                 // ->leftJoin('jocom_mpay_transaction AS mpay', 'transactions.id', '=', 'mpay.transaction_id')
                // ->leftJoin('jocom_molpay_transaction AS molpay', 'transactions.id', '=', 'molpay.transaction_id')
                // ->leftJoin('jocom_paypal_transaction AS paypal', 'transactions.id', '=', 'paypal.transaction_id')
                ->leftJoin('jocom_revpay_transaction AS revpay', function ($join) use ($data) {
                    $join->on(DB::raw('(revpay.transaction_id = transactions.id AND revpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_mpay_transaction AS mpay', function ($join) use ($data) {
                    $join->on(DB::raw('(mpay.transaction_id = transactions.id AND mpay.payment_status = "0")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_molpay_transaction AS molpay', function ($join) use ($data) {
                    $join->on(DB::raw('(molpay.transaction_id = transactions.id AND molpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_paypal_transaction AS paypal', function ($join) use ($data) {
                    $join->on(DB::raw('(paypal.transaction_id = transactions.id AND paypal.payment_status = "Completed")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_boost_transaction AS boost', function ($join) use ($data) {
                    $join->on(DB::raw('(boost.transaction_id = transactions.id AND boost.transaction_status = "completed")'), DB::raw(''), DB::raw(''));
                })
                 ->leftJoin('jocom_grabpay_transaction AS grabpay', function ($join) use ($data) {
                    $join->on(DB::raw('(grabpay.transaction_id = transactions.id AND grabpay.status = "success")'), DB::raw(''), DB::raw(''));
                });
                // if (array_get($data, 'gateway') != '0') {
                //     switch (array_get($data, 'gateway')) {
                //         case '1': $gateway = 'mpay';
                //             break;
                        
                //         case '2': $gateway = 'molpay';
                //             break;

                //         case '3': $gateway = 'paypal';
                //             break;
                //     }
                //     $transactions = $transactions->leftJoin('jocom_'.$gateway.'_transaction as '.$gateway, $gateway.'.transaction_id', '=', 'transactions.id');
                // }
                if(count($stateName) > 0){
                    $transactions = $transactions->whereIn('transactions.delivery_state', $stateName);
                }
                $transactions = $transactions->whereIn('transactions.status', array_get($data, 'status'))
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'customer') == NULL || array_get($data, 'customer') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.buyer_username', '=', array_get($data, 'customer'));
                    }
                })
                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'company') == 1) {
                        $query->where('transactions.buyer_username', '!=', 'kitkat');
                        
                    } else{
                        // DO NOTHING
                    }
                })
                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'seller') == NULL || array_get($data, 'seller') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transaction_details.seller_username', '=', array_get($data, 'seller'));
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'agent') == NULL || array_get($data, 'agent') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.agent_id', '=', array_get($data, 'agent'));
                    }
                })
                ->where(function ($query) use ($data) {
                    switch (array_get($data, 'created')) {
                        case '1':
                            $query->whereBetween('transactions.transaction_date', [array_get($data, 'created_from'), array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '2':
                            $query->whereBetween('transactions.insert_date', [array_get($data, 'created_from'), array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '3':
                            $query->whereBetween('transactions.invoice_date', [array_get($data, 'created_from'), array_get($data, 'created_to')]);
                            break;
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'special_msg') != '0') {
                        $query->where('transactions.special_msg', '!=', '');
                    }
                })
                ->where(function ($query) use ($products) {
                    if (array_get($products, '0') != 'all') {
                        $query->whereIn('transaction_details.sku', $products);
                    }
                })
                // ->where(function ($query) use ($data) {
                //     if(array_get($data, 'gateway') != '0') {
                //         switch (array_get($data, 'gateway')) {
                //             case '1':   $gateway = 'mpay';
                //                         $payment_status = '0';
                //                         break;

                //             case '2':   $gateway = 'molpay';
                //                         $payment_status = '00';
                //                         break;

                //             case '3':   $gateway = 'paypal';
                //                         $payment_status = 'Completed';
                //                         break;
                //         }
                //         $query->where($gateway.'.payment_status', '=', $payment_status);
                //     }
                // })
                // ->where('mpay.payment_status', '=', '0')
                // ->where('molpay.payment_status', '=', '00')
                // ->where('paypal.payment_status', '=', 'Completed')
                ->havingRaw($having)
                // ->groupBy('transactions.id', 'transaction_details.p_option_id','JPB.product_id')
                ->groupBy('transactions.id', 'transaction_details.id')
                ->orderBy('transactions.transaction_date')
                ->orderBy('transactions.id')
                ->orderBy('transaction_details.p_option_id')
                ->get();
                
                //echo $transactions;
                
        }
    
        //  echo '<pre>';
        //         print_r($transaction); 
        //         echo '</pre>';
        return $transactions;
        
        }catch (exception $ex){
            echo $ex->getMessage();
            //  echo $ex->getLine();
        }
    }
    
    public static function get_transactionORI($data = null)
    {
        $amountFrom = array_get($data, 'amount_from');
        $amountTo   = array_get($data, 'amount_to');
        $pointAction1 = PointAction::EARN;
        $pointAction2 = PointAction::REVERSAL;
        $gateway        = array_get($data, 'gateway');
        $payment_status = '';
        
        if (array_get($data, 'amount') == '0') {
            $having = 'actual_total IS NOT NULL';
        } else {
            $having = "actual_total BETWEEN {$amountFrom} AND {$amountTo}";
        }
        
        $regionid = array_get($data, 'region_id');
        $stateName = array();
        $State = State::getStateByRegion($regionid);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }

        if (array_get($data, 'breakdown') == '0') {
            $transactions = DB::table('jocom_transaction AS transactions')
                ->select(
                    'transactions.id',
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m-%d") AS date'),
                    'transactions.invoice_no AS invoice_no',
                    'transactions.invoice_date AS invoice_date',
                    'parent.parent_inv AS parent_invoice',
                    'transactions.buyer_username AS buyer_username',
                    'transactions.status AS status',
                    'eleven.order_number AS order_number_eleven', //11Street Order No.
                    'shopee.ordersn AS order_number_shopee', //11Street Order No.
                    'lazada.order_number AS order_number_lazada', //11Street Order No.
                    'qoo10.packNo AS order_number_qoo10', //11Street Order No.
                    'transactions.delivery_charges AS delivery_charges',
                    'transactions.process_fees AS process_fees',
                    'transactions.gst_delivery AS gst_delivery',
                    'transactions.gst_process AS gst_process',
                    'transactions.gst_total AS gst_total',
                    'transactions.total_amount AS total_amount',
                    'transactions.special_msg AS special_msg',
                    'agents.agent_code AS agent_code',
                    'mpay.id AS mpayid',
                    'molpay.id AS molpayid',
                    'paypal.id AS paypalid',
                    DB::raw("
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point)
                            ELSE 0
                        END) AS point_earned,
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point * points.rate)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point * points.rate)
                            ELSE 0
                        END) AS point_earned_value,
                        (CASE
                            WHEN coupons.coupon_amount IS NULL THEN 0
                            ELSE ROUND(coupons.coupon_amount, 2)
                        END) AS coupon_amount,
                        (CASE
                            WHEN c.free_delivery THEN '10'
                            ELSE '0'
                        END) AS coupon_delivery_fee,
                        (CASE
                            WHEN c.free_process THEN '5'
                            ELSE '0'
                        END) AS coupon_processing_fee,
                        (CASE
                            WHEN transaction_point.point IS NULL THEN 0
                            ELSE transaction_point.point
                        END) AS point_redeemed,
                        (CASE
                            WHEN transaction_point.amount IS NULL THEN 0
                            ELSE transaction_point.amount
                        END) AS point_amount,
                        ROUND((
                            transactions.total_amount -
                            (CASE WHEN coupons.coupon_amount IS NULL THEN 0 ELSE ROUND(coupons.coupon_amount, 2) END) -
                            (CASE WHEN transaction_point.amount IS NULL THEN 0 ELSE transaction_point.amount END) +
                            transactions.gst_total
                        ), 2) AS actual_total
                    ")
                )
                ->leftJoin('jocom_agents AS agents', 'transactions.agent_id', '=', 'agents.id')
                ->leftJoin('jocom_transaction_coupon AS coupons', 'transactions.id', '=', 'coupons.transaction_id')
                ->leftJoin('jocom_coupon AS c', 'c.coupon_code', '=', 'coupons.coupon_code')                
                ->leftJoin('jocom_transaction_point AS transaction_point', 'transactions.id', '=', 'transaction_point.transaction_id')
                ->leftjoin('jocom_elevenstreet_order AS eleven', 'transactions.id', '=', 'eleven.transaction_id')  //11street Order Number
                ->leftjoin('jocom_shopee_order AS shopee', 'transactions.id', '=', 'shopee.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_qoo10_order AS qoo10', 'transactions.id', '=', 'qoo10.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_lazada_order AS lazada', 'transactions.id', '=', 'lazada.transaction_id')  //Shopee Order Number
                ->leftJoin('point_transactions AS points', function ($join) use ($pointAction1, $pointAction2) {
                    $join->on(DB::raw('(points.transaction_id = transactions.id AND (points.point_action_id = '.PointAction::EARN.' OR points.point_action_id = '.PointAction::REVERSAL.'))'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_transaction_parent_invoice AS parent', 'transactions.id', '=', 'parent.transaction_id')
                // ->leftJoin('jocom_mpay_transaction AS mpay', 'transactions.id', '=', 'mpay.transaction_id')
                // ->leftJoin('jocom_molpay_transaction AS molpay', 'transactions.id', '=', 'molpay.transaction_id')
                // ->leftJoin('jocom_paypal_transaction AS paypal', 'transactions.id', '=', 'paypal.transaction_id')
                ->leftJoin('jocom_mpay_transaction AS mpay', function ($join) use ($data) {
                    $join->on(DB::raw('(mpay.transaction_id = transactions.id AND mpay.payment_status = "0")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_molpay_transaction AS molpay', function ($join) use ($data) {
                    $join->on(DB::raw('(molpay.transaction_id = transactions.id AND molpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_paypal_transaction AS paypal', function ($join) use ($data) {
                    $join->on(DB::raw('(paypal.transaction_id = transactions.id AND paypal.payment_status = "Completed")'), DB::raw(''), DB::raw(''));
                });
                
                // if (array_get($data, 'gateway') != '0') {
                //     switch (array_get($data, 'gateway')) {
                //         case '1': $gateway = 'mpay';
                //             break;
                        
                //         case '2': $gateway = 'molpay';
                //             break;

                //         case '3': $gateway = 'paypal';
                //             break;
                //     }

                //     $transactions = $transactions->leftJoin('jocom_'.$gateway.'_transaction as '.$gateway, $gateway.'.transaction_id', '=', 'transactions.id');
                // }
                if(count($stateName) > 0){
                    $transactions = $transactions->whereIn('transactions.delivery_state', $stateName);
                }
                $transactions = $transactions->whereIn('transactions.status', array_get($data, 'status'))
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'customer') == NULL || array_get($data, 'customer') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.buyer_username', '=', array_get($data, 'customer'));
                    }
                })
                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'company') == 1) {
                        $query->where('transactions.buyer_username', '!=', 'kitkat');
                        
                    } else{
                        // DO NOTHING
                    }
                })
                // KIT KAT CHECKING 
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'agent') == NULL || array_get($data, 'agent') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.agent_id', '=', array_get($data, 'agent'));
                    }
                })
                ->where(function ($query) use ($data) {
                    switch (array_get($data, 'created')) {
                        case '1':
                            $query->whereBetween('transactions.transaction_date', [array_get($data, 'created_from').' 00:00:00', array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '2':
                            $query->whereBetween('transactions.insert_date', [array_get($data, 'created_from').' 00:00:00', array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '3':
                            $query->whereBetween('transactions.invoice_date', [array_get($data, 'created_from'), array_get($data, 'created_to')]);
                            break;
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'special_msg') != '0') {
                        $query->where('transactions.special_msg', '!=', '');
                    }
                });
                // ->where(function ($query) use ($data) {
                //     if(array_get($data, 'gateway') != '0') {
                //         switch (array_get($data, 'gateway')) {
                //             case '1':   $gateway = 'mpay';
                //                         $payment_status = '0';
                //                         break;

                //             case '2':   $gateway = 'molpay';
                //                         $payment_status = '00';
                //                         break;

                //             case '3':   $gateway = 'paypal';
                //                         $payment_status = 'Completed';
                //                         break;
                //         }

                //         $query->where($gateway.'.payment_status', '=', $payment_status);
                //     }
                // });
                // ->where('mpay.payment_status', '=', '0')
                // ->where('molpay.payment_status', '=', '00')
                // ->where('paypal.payment_status', '=', 'Completed');
                
                if ((array_get($data, 'created') == 0) || (array_get($data, 'created') == 1)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.transaction_date')
                    ->orderBy('transactions.id')
                    ->get();
                }
                if ((array_get($data, 'created') == 2)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.insert_date')
                    ->orderBy('transactions.id')
                    ->get();
                }
                if ((array_get($data, 'created') == 3)) {

                    $transactions = $transactions->havingRaw($having)
                    ->groupBy('transactions.id')
                    ->orderBy('transactions.invoice_date')
                    ->orderBy('transactions.id')
                    ->get();
                }

                //$transactions = $transactions->havingRaw($having)
                //->groupBy('transactions.id')
                //->orderBy('transactions.transaction_date')
                //->orderBy('transactions.id')
                //->get();
                // Log::info(DB::getQueryLog());
        } else {
            $products     = explode(',', array_get($data, 'product'));
            /*
            
            (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point)
                            ELSE 0
                        END) AS point_earned,
                        (CASE
                            WHEN points.point_action_id = {$pointAction1} THEN SUM(points.point * points.rate)
                            WHEN points.point_action_id = {$pointAction2} THEN SUM(points.point * points.rate)
                            ELSE 0
                        END) AS point_earned_value,
            */
            
            $transactions = DB::table('jocom_transaction AS transactions')
                ->select(
                    'transactions.id',
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m-%d") AS date'),
                    'transactions.invoice_no AS invoice_no',
                    'transactions.invoice_date AS invoice_date',
                    'parent.parent_inv AS parent_invoice',
                    'transactions.buyer_username AS buyer_username',
                    'transactions.status AS status',
                    'eleven.order_number AS order_number_eleven', //11Street Order No.
                    'shopee.ordersn AS order_number_shopee', //11Street Order No.
                    'lazada.order_number AS order_number_lazada', //11Street Order No.
                    'qoo10.packNo AS order_number_qoo10', //11Street Order No.
                    'transactions.delivery_charges AS delivery_charges',
                    'transactions.process_fees AS process_fees',
                    'transactions.gst_delivery AS gst_delivery',
                    'transactions.gst_process AS gst_process',
                    'transactions.gst_total AS gst_total',
                    'transactions.total_amount AS total_amount',
                    'transactions.special_msg AS special_msg',
                    'agents.agent_code AS agent_code',
                    'mpay.id AS mpayid',
                    'molpay.id AS molpayid',
                    'paypal.id AS paypalid',
                    'transaction_details.sku AS sku',
                    'product_details.name AS product_name',
                    'transaction_details.price_label AS price_label',
                    DB::raw("(CASE WHEN transaction_details.original_price > 0 THEN transaction_details.original_price ELSE transaction_details.price END) AS actual_price"),
                    'transaction_details.price AS price',
                   // DB::raw('SUM(transaction_details.unit) AS unit'),// 'transaction_details.unit AS unit',
                   // DB::raw('SUM(transaction_details.price * transaction_details.unit) AS total_item_amount'),
                    'transaction_details.unit AS unit',// 'transaction_details.unit AS unit',
                    'transaction_details.price * transaction_details.unit AS total_item_amount',
                    'transaction_details.p_referral_fees AS p_referral_fees',
                    'transaction_details.p_referral_fees_type AS p_referral_fees_type',
                    'transaction_details.seller_username AS seller_username',
                    'transaction_details.disc AS disc',
                    'transaction_details.gst_rate_item AS gst_rate_item',
                    'transaction_details.gst_amount AS gst_amount',//'transaction_details.gst_amount AS gst_amount',
                    'transaction_details.gst_seller AS gst_seller',//'transaction_details.gst_seller AS gst_seller',
                    'transaction_details.parent_po AS parent_po',
                    'transaction_details.po_no AS po_no',
                    DB::raw("
                        (CASE
                            WHEN coupons.coupon_amount IS NULL THEN 0
                            ELSE ROUND(coupons.coupon_amount, 2)
                        END) AS coupon_amount,
                        (CASE
                            WHEN c.free_delivery THEN '10'
                            ELSE '0'
                        END) AS coupon_delivery_fee,
                        (CASE
                            WHEN c.free_process THEN '5'
                            ELSE '0'
                        END) AS coupon_processing_fee,
                        (CASE
                            WHEN transaction_point.point IS NULL THEN 0
                            ELSE transaction_point.point
                        END) AS point_redeemed,
                        (CASE
                            WHEN transaction_point.amount IS NULL THEN 0
                            ELSE transaction_point.amount
                        END) AS point_amount,
                        ROUND((
                            transactions.total_amount -
                            (CASE WHEN coupons.coupon_amount IS NULL THEN 0 ELSE ROUND(coupons.coupon_amount, 2) END) -
                            (CASE WHEN transaction_point.amount IS NULL THEN 0 ELSE transaction_point.amount END) +
                            transactions.gst_total
                        ), 2) AS actual_total
                    ")
                )
                ->leftJoin('jocom_agents AS agents', 'transactions.agent_id', '=', 'agents.id')
                ->leftJoin('jocom_transaction_coupon AS coupons', 'transactions.id', '=', 'coupons.transaction_id')
                ->leftJoin('jocom_coupon AS c', 'c.coupon_code', '=', 'coupons.coupon_code')                
                ->leftJoin('jocom_transaction_point AS transaction_point', 'transactions.id', '=', 'transaction_point.transaction_id')
                ->leftjoin('jocom_elevenstreet_order AS eleven', 'transactions.id', '=', 'eleven.transaction_id')  //11street Order Number
                ->leftjoin('jocom_shopee_order AS shopee', 'transactions.id', '=', 'shopee.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_qoo10_order AS qoo10', 'transactions.id', '=', 'qoo10.transaction_id')  //Shopee Order Number
                ->leftjoin('jocom_lazada_order AS lazada', 'transactions.id', '=', 'lazada.transaction_id')  //Shopee Order Number
                ->leftJoin('point_transactions AS points', function ($join) use ($pointAction1, $pointAction2) {
                    $join->on(DB::raw('(points.transaction_id = transactions.id AND (points.point_action_id = '.PointAction::EARN.' OR points.point_action_id = '.PointAction::REVERSAL.'))'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_transaction_details AS transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->leftJoin('jocom_transaction_parent_invoice AS parent', 'transactions.id', '=', 'parent.transaction_id')
                ->leftJoin('jocom_products AS product_details', 'product_details.id', '=', 'transaction_details.product_id')
                 // ->leftJoin('jocom_mpay_transaction AS mpay', 'transactions.id', '=', 'mpay.transaction_id')
                // ->leftJoin('jocom_molpay_transaction AS molpay', 'transactions.id', '=', 'molpay.transaction_id')
                // ->leftJoin('jocom_paypal_transaction AS paypal', 'transactions.id', '=', 'paypal.transaction_id')
                ->leftJoin('jocom_mpay_transaction AS mpay', function ($join) use ($data) {
                    $join->on(DB::raw('(mpay.transaction_id = transactions.id AND mpay.payment_status = "0")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_molpay_transaction AS molpay', function ($join) use ($data) {
                    $join->on(DB::raw('(molpay.transaction_id = transactions.id AND molpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
                })
                ->leftJoin('jocom_paypal_transaction AS paypal', function ($join) use ($data) {
                    $join->on(DB::raw('(paypal.transaction_id = transactions.id AND paypal.payment_status = "Completed")'), DB::raw(''), DB::raw(''));
                });

                // if (array_get($data, 'gateway') != '0') {
                //     switch (array_get($data, 'gateway')) {
                //         case '1': $gateway = 'mpay';
                //             break;
                        
                //         case '2': $gateway = 'molpay';
                //             break;

                //         case '3': $gateway = 'paypal';
                //             break;
                //     }
                //     $transactions = $transactions->leftJoin('jocom_'.$gateway.'_transaction as '.$gateway, $gateway.'.transaction_id', '=', 'transactions.id');
                // }
                if(count($stateName) > 0){
                    $transactions = $transactions->whereIn('transactions.delivery_state', $stateName);
                }
                $transactions = $transactions->whereIn('transactions.status', array_get($data, 'status'))
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'customer') == NULL || array_get($data, 'customer') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.buyer_username', '=', array_get($data, 'customer'));
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'seller') == NULL || array_get($data, 'seller') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transaction_details.seller_username', '=', array_get($data, 'seller'));
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'agent') == NULL || array_get($data, 'agent') == 'all') {
                        // DO NOTHING
                    } else {
                        $query->where('transactions.agent_id', '=', array_get($data, 'agent'));
                    }
                })
                ->where(function ($query) use ($data) {
                    switch (array_get($data, 'created')) {
                        case '1':
                            $query->whereBetween('transactions.transaction_date', [array_get($data, 'created_from'), array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '2':
                            $query->whereBetween('transactions.insert_date', [array_get($data, 'created_from'), array_get($data, 'created_to').' 23:59:59']);
                            break;
                        case '3':
                            $query->whereBetween('transactions.invoice_date', [array_get($data, 'created_from'), array_get($data, 'created_to')]);
                            break;
                    }
                })
                ->where(function ($query) use ($data) {
                    if (array_get($data, 'special_msg') != '0') {
                        $query->where('transactions.special_msg', '!=', '');
                    }
                })
                ->where(function ($query) use ($products) {
                    if (array_get($products, '0') != 'all') {
                        $query->whereIn('transaction_details.sku', $products);
                    }
                })
                // ->where(function ($query) use ($data) {
                //     if(array_get($data, 'gateway') != '0') {
                //         switch (array_get($data, 'gateway')) {
                //             case '1':   $gateway = 'mpay';
                //                         $payment_status = '0';
                //                         break;

                //             case '2':   $gateway = 'molpay';
                //                         $payment_status = '00';
                //                         break;

                //             case '3':   $gateway = 'paypal';
                //                         $payment_status = 'Completed';
                //                         break;
                //         }
                //         $query->where($gateway.'.payment_status', '=', $payment_status);
                //     }
                // })
                // ->where('mpay.payment_status', '=', '0')
                // ->where('molpay.payment_status', '=', '00')
                // ->where('paypal.payment_status', '=', 'Completed')
                ->havingRaw($having)
                ->groupBy('transactions.id', 'transaction_details.p_option_id') 
                ->orderBy('transactions.transaction_date') 
                ->orderBy('transactions.id')
                ->orderBy('transaction_details.p_option_id')
                ->get();
              
        }

        return $transactions;
    }

    public static function get_top($data = null) 
    {
        if ($data['sort_type'] == '0')
        {
            $order = "total";
            $by = "desc";
        }
        else
        {
            $order = "unit";
            $by = "desc";
        }

        $transaction = DB::table('jocom_transaction AS a')
                ->select(DB::raw("b.product_id AS product_id, c.name AS product_name, b.p_option_id AS label_id, b.price_label AS product_label, group_concat(distinct e.category_name separator ', ') AS category, ROUND( SUM( b.unit )/count(e.category_name), 0) AS unit, b.price AS price, ROUND( SUM( b.total )/count(e.category_name), 2) AS total"))
                ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                ->leftJoin('jocom_categories AS d', 'c.id', '=', 'd.product_id')
                ->leftJoin('jocom_products_category AS e', 'd.category_id', '=', 'e.id')
                ->where('a.status', '=', 'completed')
                ->where('b.product_id', '<>', '0')
                ->whereRaw('c.name IS NOT NULL')
                ->where(function($query) use ($data)
                    {
                        if ($data['customer'] == NULL OR $data['customer'] == 'all')
                            $nothing;
                        else
                            $query->where('a.buyer_username', '=',  $data['customer']);
                    })              
                ->where(function($query) use ($data)
                    {
                        if ($data['created'] == '0')
                            $nothing;
                        else
                            $query->whereBetween('a.transaction_date', array($data['created_from'],$data['created_to']." 23:59:59"));
                    })
                ->groupBy('b.p_option_id')
                ->orderBy($order, $by)
                ->take($data['topcount'])
                ->get();

        return $transaction;
        
    }
    
    public static function get_dailytransaction($data = null)
    {
        $date_from = $data['date_from'];
        $date_to = $data['date_to'];

        $regionid = array_get($data, 'region_id');
        $stateName = array();
        $State = State::getStateByRegion($regionid);
        foreach ($State as $keyS => $valueS) {
            $stateName[] = $valueS->name;
        }

        $products =  DB::table('jocom_transaction as t')
                        ->join('jocom_transaction_details as d', 't.id', '=', 'd.transaction_id')
                        ->join('jocom_product_base_item as b', 'd.p_option_id', '=', 'b.price_option_id')
                        ->join('jocom_products as p', 'b.product_base_id', '=', 'p.id')
                        ->join('jocom_product_price as o', 'p.id', '=', 'o.product_id')
                        ->join('jocom_seller as s', 'p.sell_id', '=', 's.id')
                        ->where('transaction_date', '>', $date_from)
                        ->where('transaction_date', '<=', $date_to)
                        ->where('t.status', 'completed')
                        ->where('b.status', 1)
                        ->where('o.status', 1)
                        ->groupBy('p.sku')
                        ->select(DB::raw('p.sku, p.name, o.label, sum(d.unit),b.quantity, sum(unit * quantity) as total_required, count(product_base_id) as transaction_count, s.company_name, t.buyer_username'));

        if(count($stateName) > 0){
            $products = $products->whereIn('transactions.delivery_state', $stateName);
        }
        return $products->get();
    }
    
    public static function consignment_base_Q($query){
        $transactions_query = (isset($query['trans']) ? $query['trans'] : '( SELECT * FROM `jocom_transaction` WHERE `status` = "completed" )');
        $seller_query = (isset($query['seller']) ? $query['seller'] : '`jocom_seller`');
        $is_base_query = (isset($query['is_base']) ? $query['is_base'] : '`product_details`.`is_base_product` = 1');
        // $trans_logi_sent_query = '`logi_trans`.`status` = ' . LogisticTransaction::get_status_int('Sent');
        
        return "
            SELECT
                `transactions`.`id` AS `transaction_id`,
                `transactions`.`invoice_no` AS `invoice_no`, 
                `transactions`.`invoice_date` AS `invoice_date`,
                `transactions`.`buyer_username` AS `buyer_username`,
                `transactions`.`status` AS `transaction_status`,
                `transaction_details`.`product_name` AS `product_name`,
                `transaction_details`.`product_id` AS `product_id`,
                `transaction_details`.`po_no` AS `customer_po`,
                `product_details`.`is_base_product` AS `is_base`,
                `p_cost`.`cost_price`,
                `seller`.`id` AS `seller_id`,
                `seller`.`company_name` AS `seller`,
                `transaction_details`.`price_label` AS `option_name`,
                `transaction_details`.`unit` AS `unit`,
                `logi_trans`.`status` AS `logistic_status`,
                `logi_tran_items`.`delivery_time` AS `delivery_time`,
                -- `logi_tran_items`.`status` AS `logistic_item_status`,
                `logi_trans`.`status` AS `logistic_item_status`,
                -- `logi_tran_items`.`qty_order` AS `logistic_order_qty`,
                -- `logi_tran_items`.`qty_to_assign` AS `logistic_assign_qty`,
                -- `logi_tran_items`.`qty_to_send` AS `logistic_send_qty`,
                `logi_trans`.`id` AS `logistic_id`
            FROM $transactions_query AS `transactions` 
            JOIN `jocom_transaction_details` AS `transaction_details` ON `transactions`.`id` = `transaction_details`.`transaction_id` 
            JOIN `jocom_products` AS `product_details` ON `product_details`.`id` = `transaction_details`.`product_id` AND $is_base_query
            LEFT JOIN (SELECT `id`, `product_price_id`, `seller_id`, `cost_price` FROM `jocom_product_price_seller` WHERE activation = 1) AS `p_cost` ON `p_cost`.`product_price_id` = `transaction_details`.`p_option_id`
            LEFT JOIN $seller_query AS `seller` ON `product_details`.`sell_id` = `seller`.`id` 
            LEFT JOIN `logistic_transaction` AS `logi_trans` ON `transactions`.`id` = `logi_trans`.`transaction_id` -- AND $trans_logi_sent_query
            LEFT JOIN `logistic_transaction_item` AS `logi_tran_items` ON `logi_trans`.`id` = `logi_tran_items`.`logistic_id` AND `transaction_details`.`product_id` = `logi_tran_items`.`product_id`
            WHERE 
                `transaction_details`.`id` IS NOT NULL AND
                `product_details`.`id` IS NOT NULL AND
                `logi_trans`.`id` IS NOT NULL AND
                `seller`.`id` IS NOT NULL 
            ORDER BY 
                `transactions`.`id` ASC
        ";
    }

    public static function consignment_pack_Q($query){
        $transactions_query = (isset($query['trans']) ? $query['trans'] : '( SELECT * FROM `jocom_transaction` WHERE `status` = "completed" )');
        $seller_query = (isset($query['seller']) ? $query['seller'] : '`jocom_seller`');
        // $trans_logi_sent_query = '`logi_trans`.`status` = ' . LogisticTransaction::get_status_int('Sent');
        
        return "
            SELECT 
                `transactions`.`id` AS `transaction_id`,
                `transactions`.`invoice_no` AS `invoice_no`, 
                `transactions`.`invoice_date` AS `invoice_date`,
                `transactions`.`buyer_username` AS `buyer_username`,
                `transactions`.`status` AS `transaction_status`,
                `transaction_details`.`product_name` AS `product_name`,
                `transaction_details`.`price_label` AS `option_name`,
                `transaction_details`.`po_no` AS `customer_po`,
                `seller`.`company_name` AS `seller`,
                `transaction_details`.`unit` AS `unit`,
                `logi_trans`.`status` AS `logistic_status`,
                `logi_tran_items`.`delivery_time` AS `delivery_time`,
                -- `logi_tran_items`.`status` AS `logistic_item_status`,
                `logi_trans`.`status` AS `logistic_item_status`,
                `logi_trans`.`id` AS `logistic_id`,
                `p_cost`.`cost_price`,
                `seller`.`id` AS `seller_id`,
                `transaction_details`.`product_id` AS `product_id`,
                `product_base`.`product_base_id` AS `base_id`,
                `product_base`.`price_option_id` AS `option_id`,
                `product_base`.`quantity` AS `base_qty`,
                `base_details`.`name` AS `base_product_name`,
                `price_option`.`label` AS `base_option_name`
            FROM $transactions_query AS `transactions` 
            JOIN `jocom_transaction_details` AS `transaction_details` ON `transactions`.`id` = `transaction_details`.`transaction_id`
            LEFT JOIN (
                SELECT * 
                FROM `jocom_product_base_item` AS `base` 
                WHERE `base`.`status` = 1
            ) AS `product_base` ON `product_base`.`product_id` = `transaction_details`.`product_id`
            LEFT JOIN `jocom_products` AS `base_details` ON `base_details`.`id` = `product_base`.`product_base_id`
            LEFT JOIN (
                SELECT *
                FROM `jocom_product_price`
                WHERE `status` = 1
            ) AS `price_option` ON `product_base`.`price_option_id` = `price_option`.`id`
            LEFT JOIN (SELECT `id`, `product_price_id`, `seller_id`, `cost_price` FROM `jocom_product_price_seller` WHERE activation = 1) AS `p_cost` ON `p_cost`.`product_price_id` = `product_base`.`price_option_id`
            LEFT JOIN $seller_query AS `seller` ON `base_details`.`sell_id` = `seller`.`id`
            LEFT JOIN `logistic_transaction` AS `logi_trans` ON `transactions`.`id` = `logi_trans`.`transaction_id`
            LEFT JOIN `logistic_transaction_item` AS `logi_tran_items` ON `logi_trans`.`id` = `logi_tran_items`.`logistic_id` AND `transaction_details`.`product_id` = `logi_tran_items`.`product_id`
            WHERE 
                `transaction_details`.`id` IS NOT NULL AND
                `product_base`.`id` IS NOT NULL AND
                `base_details`.`id` IS NOT NULL AND
                `logi_trans`.`id` IS NOT NULL AND
                `seller`.`id` IS NOT NULL 
            ORDER BY 
                `transactions`.`id` ASC
        ";
    }

    public static function consignment_base_count_Q($query){
        $transactions_query = (isset($query['trans']) ? $query['trans'] : '( SELECT * FROM `jocom_transaction` WHERE `status` = "completed" )');
        $seller_query = (isset($query['seller']) ? $query['seller'] : '`jocom_seller`');
        $is_base_query = (isset($query['is_base']) ? $query['is_base'] : '`product_details`.`is_base_product` = 1');
        // $trans_logi_sent_query = '`logi_trans`.`status` = ' . LogisticTransaction::get_status_int('Sent');

        return "
            SELECT
                `transaction_details`.`product_id` AS `product_id`,
                `transaction_details`.`p_option_id` AS `option_id`,
                `transaction_details`.`product_name` AS `product_name`,
                `transaction_details`.`price_label` AS `option_name`,
                `p_cost`.`cost_price`,
                GROUP_CONCAT(`transaction_details`.`unit`) AS `trans_idiv_info`,
                GROUP_CONCAT(`transactions`.`buyer_username`) AS `trans_idiv_platform`,
                SUM(`transaction_details`.`unit`) AS total_sell
            FROM $transactions_query AS `transactions` 
            JOIN `jocom_transaction_details` AS `transaction_details` ON `transactions`.`id` = `transaction_details`.`transaction_id` 
            JOIN `jocom_products` AS `product_details` ON `product_details`.`id` = `transaction_details`.`product_id` AND $is_base_query
            LEFT JOIN (SELECT `id`, `product_price_id`, `seller_id`, `cost_price` FROM `jocom_product_price_seller` WHERE activation = 1) AS `p_cost` ON `p_cost`.`product_price_id` = `transaction_details`.`p_option_id`
            LEFT JOIN $seller_query AS `seller` ON `product_details`.`sell_id` = `seller`.`id` 
            LEFT JOIN `logistic_transaction` AS `logi_trans` ON `transactions`.`id` = `logi_trans`.`transaction_id` -- AND $trans_logi_sent_query 
            LEFT JOIN `logistic_transaction_item` AS `logi_tran_items` ON `logi_trans`.`id` = `logi_tran_items`.`logistic_id` AND `transaction_details`.`product_id` = `logi_tran_items`.`product_id`
            WHERE 
                `transaction_details`.`id` IS NOT NULL AND
                `product_details`.`id` IS NOT NULL AND
                `logi_trans`.`id` IS NOT NULL AND
                `seller`.`id` IS NOT NULL 
            GROUP BY
                `transaction_details`.`product_id`, `transaction_details`.`p_option_id`
            ORDER BY 
                `transactions`.`id` ASC
        ";
    }

    public static function consignment_pack_count_Q($query){
        $transactions_query = (isset($query['trans']) ? $query['trans'] : '( SELECT * FROM `jocom_transaction` WHERE `status` = "completed" )');
        $seller_query = (isset($query['seller']) ? $query['seller'] : '`jocom_seller`');
        // $trans_logi_sent_query = '`logi_trans`.`status` = ' . LogisticTransaction::get_status_int('Sent');

        // Pointless to check is base cuz all item is cheked on product base item table. 
        // It only pick the package item it have base item, if that product without base item record that product will be ignore

        return "
            SELECT
                `product_base`.`product_base_id` AS `product_id`,
                `product_base`.`price_option_id` AS `option_id`,
                `base_details`.`name` AS `product_name`,
                `price_option`.`label` AS `option_name`,
                `p_cost`.`cost_price`,
                `product_base`.`quantity` AS `base_qty`,
                GROUP_CONCAT(`transaction_details`.`unit`) AS `trans_idiv_info`,
                GROUP_CONCAT(`transactions`.`buyer_username`) AS `trans_idiv_platform`,
                SUM(`transaction_details`.`unit`) AS total_sell
            FROM $transactions_query AS `transactions` 
            JOIN `jocom_transaction_details` AS `transaction_details` ON `transactions`.`id` = `transaction_details`.`transaction_id`
            LEFT JOIN (
                SELECT * 
                FROM `jocom_product_base_item` AS `base` 
                WHERE `base`.`status` = 1
            ) AS `product_base` ON `product_base`.`product_id` = `transaction_details`.`product_id`
            LEFT JOIN (SELECT `id`, `product_price_id`, `seller_id`, `cost_price` FROM `jocom_product_price_seller` WHERE activation = 1) AS `p_cost` ON `p_cost`.`product_price_id` = `product_base`.`price_option_id`
            LEFT JOIN `jocom_products` AS `base_details` ON `base_details`.`id` = `product_base`.`product_base_id`
            LEFT JOIN (
                SELECT *
                FROM `jocom_product_price`
                WHERE `status` = 1
            ) AS `price_option` ON `product_base`.`price_option_id` = `price_option`.`id`
            LEFT JOIN $seller_query AS `seller` ON `base_details`.`sell_id` = `seller`.`id`
            LEFT JOIN `logistic_transaction` AS `logi_trans` ON `transactions`.`id` = `logi_trans`.`transaction_id` -- AND $trans_logi_sent_query 
            LEFT JOIN `logistic_transaction_item` AS `logi_tran_items` ON `logi_trans`.`id` = `logi_tran_items`.`logistic_id` AND `transaction_details`.`product_id` = `logi_tran_items`.`product_id`
            WHERE 
                `transaction_details`.`id` IS NOT NULL AND
                `product_base`.`id` IS NOT NULL AND
                `base_details`.`id` IS NOT NULL AND
                `logi_trans`.`id` IS NOT NULL AND
                `seller`.`id` IS NOT NULL 
            GROUP BY
                `product_base`.`product_base_id`, `product_base`.`price_option_id`
            ORDER BY 
                `transactions`.`id` ASC
        ";
    }

    // YH: no using.
    public static function consignment_platform_count($result){
        $count_p = [];
        foreach ($result['platform'] as $p => $p_name) {
            $count_p[$p] = 0;
            if(count($result['base'])){
                foreach ($result['base'] as $i => $v) {
                    if($p === 'jocom' || $v->buyer_username === $p){
                        $count_p[$p] += (int)$v->unit;
                        unset($result['base'][$i]); // remove current idx avoid next time use back 
                    }
                }
            }
            if(count($result['pack'])){
                foreach ($result['pack'] as $i => $v) {
                    if($p === 'jocom' || $v->buyer_username === $p){
                        $count_p[$p] += (int)$v->unit * (int)$v->base_qty;
                        unset($result['pack'][$i]); // remove current idx avoid next time use back 
                    }
                }
            }
        }
        return $count_p;
    }

    public static function consignment_grn($parm){
        if(!count($parm['base']) && !count($parm['pack'])) return []; // both dont have data pointless continue render return empty instead
        $d_range = [
            1 => '`po_date`',
            2 => '`delivery_date`',
            3 => '`created_at`',
            4 => '`grn_date`',
        ];
        if($parm['seller_id']){ // User input is seller id
            $input = 'seller_id = ' . $parm['seller_id'];
        }else{ // User input is not seller id, proceed hardest way grasp seller id from past result
            $base = (count($parm['base']) ? json_decode(json_encode($parm['base']), true) : []);
            $pack = (count($parm['pack']) ? json_decode(json_encode($parm['pack']), true) : []);
            $seller_ids = (count($base) && count($pack) ? array_unique(array_merge(array_filter(array_column($base, 'seller_id')), array_filter(array_column($pack, 'seller_id')))) : (count($base) ? array_unique(array_column($base, 'seller_id')) : (count($pack) ? array_unique(array_column($pack, 'seller_id')) : [])));
            $input = (count($seller_ids) ? 'seller_id IN (' . implode(',', $seller_ids) . ')' : ''); // find seller id on result it may take time to proceed
        }
        
        $input .= (in_array((int)$parm['date_method'], [1, 2, 3]) ? ($input ? ' AND ' : '') . $d_range[(int)$parm['date_method']] . ' BETWEEN "' . $parm['date_from'] . '" AND "' . $parm['date_to'] . '"' : '');
        $purchase_order_query = (
            $input ? 
            '( SELECT * FROM `jocom_purchase_order` WHERE ' . $input . ' AND `status` = 1 )' : 
            '( SELECT * FROM `jocom_purchase_order` WHERE `status` = 1 )'
        );
        $input = (in_array((int)$parm['date_method'], [4]) ? $d_range[(int)$parm['date_method']] . ' BETWEEN "' . $parm['date_from'] . '" AND "' . $parm['date_to'] . '"' : '');
        $warehouse_grn = (
            $input ? 
            '( SELECT * FROM `jocom_warehouse_grn` WHERE ' . $input . ' AND `status` = 1 )' : 
            '( SELECT * FROM `jocom_warehouse_grn` WHERE `status` = 1 )'
        );

        return DB::select("
            SELECT po.type, po.po_no, po.po_date, po.delivery_date, w_d_grn.sku, w_d_grn.quantity
            FROM $purchase_order_query AS po
            JOIN $warehouse_grn AS w_grn ON w_grn.po_id = po.id
            JOIN jocom_warehouse_grn_details AS w_d_grn ON w_d_grn.grn_id = w_grn.id AND w_d_grn.status = 1
        ");
    }

    public static function consignment_grn_count($result_grn){
        $grn_count = [1 => 0, 2 => 0, 3 => 0];
        if(!count($result_grn)) return [1 => 0, 2 => 0, 3 => 0];
        // 1 PURCHASE ORDER
        // 2 PURCHASE REQUISITION FORM
        // 3 PURCHASE ORDER GS
        foreach ($result_grn as $k => $v) $grn_count[$v->type] += (int)$v->quantity;
        return $grn_count;
    }

    public static function Consignment($parm){
        try{
            // --------------------------------------------
            // Start - Set the input query
            // -------------------------------------------- 
                // --------------------------------------------
                // Start - Transaction Query for get Base Product on transcation
                // Region ID
                $regionid = (isset($parm['region_id']) && (int)$parm['region_id'] ? (int)$parm['region_id'] : 0);
                $input = (
                    $regionid 
                    ? '`delivery_state` IN ("' . implode('", "', DB::table('jocom_country_states AS JCS')->where('JCS.region_id', $regionid)->lists('name')) . '")'
                    : ''
                );

                // Check date
                $created_date_method = (isset($parm['created']) && $parm['created'] ? $parm['created'] : 0);
                $created_date_from = (isset($parm['created_from']) && $parm['created_from'] ? $parm['created_from'] . ' 00:00:00' : date('Y-m-d', strtotime("yesterday")) . ' 00:00:00');
                $created_date_to = (isset($parm['created_to']) && $parm['created_to'] ? $parm['created_to'] . ' 23:59:59' : date('Y-m-d') . ' 23:59:59');
                $input .= (
                    $created_date_method ?
                        ($input ? ' AND ' : '') . 
                        ($created_date_method == 1 ? '`transaction_date` BETWEEN "' . $created_date_from . '" AND "' . $created_date_to . '"' : '') .
                        ($created_date_method == 2 ? '`insert_date` BETWEEN "' . $created_date_from . '" AND "' . $created_date_to . '"' : '') .
                        ($created_date_method == 3 ? '`invoice_date` BETWEEN "' . $created_date_from . '" AND "' . $created_date_to . '"' : '')
                    :
                    ''
                );
                $transactions_query = (
                    $input ? 
                    '( SELECT * FROM `jocom_transaction` WHERE ' . $input . ' AND `status` = "completed" )' : 
                    '( SELECT * FROM `jocom_transaction` WHERE `status` = "completed" )'
                );
                // End - Transaction Query for get Base Product on transcation
                // --------------------------------------------

                // --------------------------------------------
                // Start - Seller Query for get Base Product on transcation
                // check seller is select
                $input = (
                    isset($parm['seller']) && !empty($parm['seller']) ?
                        (is_numeric($parm['seller']) ? ' id = ' . $parm['seller'] : 'username = "' . $parm['seller'] . '"')
                    :
                    ''
                ); // ' LOCATE("' . $parm['seller'] . '", username) > 0'
                $seller_query = (
                    $input ? 
                    '( SELECT * FROM `jocom_seller` WHERE ' . $input . ' )' : 
                    '`jocom_seller`'
                );
                // End - Seller Query for get Base Product on transcation
                // --------------------------------------------
            // --------------------------------------------
            // End - Set the input query
            // --------------------------------------------

            // --------------------------------------------
            // Start - Get the Base Product By Seller Query
            // --------------------------------------------
                $result_base = DB::select(self::consignment_base_Q([ 'trans' => $transactions_query, 'is_base' => '`product_details`.`is_base_product` = 1', 'seller' => $seller_query]));
            // --------------------------------------------
            // End - Get the Base Product By Seller Query
            // --------------------------------------------

            // --------------------------------------------
            // Start - Get the Package Product By Seller Query
            // --------------------------------------------
                $result_pack = DB::select(self::consignment_pack_Q([ 'trans' => $transactions_query, 'seller' => $seller_query]));
            // --------------------------------------------
            // End - Get the Package Product By Seller Query
            // --------------------------------------------

            // --------------------------------------------
            // Start - Group & Count Base Product By Seller Query
            // --------------------------------------------
                $result_base_count = DB::select(self::consignment_base_count_Q([ 'trans' => $transactions_query, 'is_base' => '`product_details`.`is_base_product` = 1', 'seller' => $seller_query]));
            // --------------------------------------------
            // End - Group & Count Base Product By Seller Query
            // --------------------------------------------

            // --------------------------------------------
            // Start - Group & Count Package Product By Seller Query
            // --------------------------------------------
                $result_pack_count = DB::select(self::consignment_pack_count_Q([ 'trans' => $transactions_query, 'seller' => $seller_query]));
            // --------------------------------------------
            // End - Group & Count Package Product By Seller Query
            // --------------------------------------------

            // // --------------------------------------------
            // // Start - Get the Issue Product By Seller Query
            // // --------------------------------------------
            //     $platform_count = self::consignment_platform_count([
            //         'base' => $result_base,
            //         'pack' => $result_pack,
            //         'platform' => $parm['platform'],
            //     ]);
            // // --------------------------------------------
            // // End - Get the Issue Product By Seller Query
            // // --------------------------------------------

            // --------------------------------------------
            // Start - Get the GRN
            // --------------------------------------------
                $grn = self::consignment_grn([
                    'base' => $result_base,
                    'pack' => $result_pack,
                    'seller_id' => (is_numeric($parm['seller']) ? (int)$parm['seller'] : 0),
                    'date_method' => (isset($parm['grn_date']) && in_array((int)$parm['grn_date'], [1, 2, 3, 4]) ? $parm['grn_date'] : 1),
                    'date_from' => date('Y-m-d', strtotime("-1 month", strtotime($created_date_from))) . ' 00:00:00',
                    'date_to' => date('Y-m-d', strtotime("-1 month", strtotime($created_date_to))) . ' 23:59:59'
                ]);
            // --------------------------------------------
            // End - Get the GRN
            // --------------------------------------------

            // --------------------------------------------
            // Start - GRN total
            // --------------------------------------------
                $grn_count = self::consignment_grn_count($grn);
            // --------------------------------------------
            // End - GRN total
            // --------------------------------------------

            return [
                'base' => $result_base,
                'pack' => $result_pack,
                'base_count' => $result_base_count,
                'pack_count' => $result_pack_count,
                'platform' => $parm['platform'],
                // 'platform_count' => $platform_count,
                'grn' => $grn,
                'grn_count' => $grn_count,
            ];

        }catch (exception $ex){
            echo $ex->getMessage();
        }
    }
    
  
    
    
}