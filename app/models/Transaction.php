<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class Transaction extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    protected $table = 'jocom_transaction';

    public static $rules = array(
        'buyer_username'=>'required',
        'transaction_date'=>'required|date',
        'delivery_name'=>'required',
        'delivery_contact_no'=>'required',
        'delivery_addr_1'=>'required',
        'delivery_postcode'=>'required',
        'delivery_city'=>'required',
        'delivery_state'=>'required',
        'delivery_country'=>'required',
        'total_amount'=>'required',
    );

    public static $message = array(
        'transaction_date.required'=>'The delivery date is required',
        'delivery_name.required'=>'The delivery name is required',
        'delivery_contact_no.required'=>'The delivery contact number is required',
        'delivery_addr_1.required'=>'The delivery address is required',
        'delivery_postcode.required'=>'The delivery postcode is required',
        'delivery_city.required'=>'The delivery city is required',
        'delivery_state.required'=>'The delivery state is required',
        'delivery_country.required'=>'The delivery country is required',
        //'total_amount.required'=>'The total amount is required',
    );

    /**
     * Listing for transaction
     * @return [type] [description]
     */
    public function scopeTransaction_listing()
    {

        //commented as using join table
        //$listing = Transaction::orderBy('id', 'Desc')->paginate(25);

        //reserve for search function
        $tempColumn = 'a.buyer_username';
        $tempOperator = 'LIKE';
        $tempSearch = '%%';

        $trans = DB::table('jocom_transaction AS a')
        ->select('a.*', 'b.coupon_code', 'b.coupon_amount')
        ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
        ->orderBy('a.id', 'Desc', 'b.transaction_date', 'ASC')
        ->where($tempColumn, $tempOperator, $tempSearch)->get();
        //->where($tempColumn, $tempOperator, $tempSearch)->paginate(25);

        //$this->layout->content = View::make('admin.transaction_listing')->with('display_listing', $listing);
        return $trans;
    }

    /**
     * Add transaction
     * @return [type] [description]
     */
    public function scopeAdd_transaction()
    {
        $trans = new Transaction;
        $trans->buyer_username = Input::get('buyer_username');
        $trans->status = Input::get('status');
        $trans->parcel_status = Input::get('parcel_status');
        $transaction_date = Input::get('transaction_date');
        if (Input::get('temptime')=='')
        {
            $temptime = date("H:i:s");
        }
        else
        {
            $temptime = Input::get('temptime');
        }
        $trans->transaction_date = $transaction_date." ".$temptime;
        $trans->delivery_name = trim(Input::get('delivery_name'));
        $trans->delivery_contact_no = trim(Input::get('delivery_contact_no'));
        $trans->delivery_addr_1 = trim(Input::get('delivery_addr_1'));
        $trans->delivery_addr_2 = trim(Input::get('delivery_addr_2'));
        $trans->delivery_postcode = trim(Input::get('delivery_postcode'));
        $trans->delivery_state = trim(Input::get('delivery_state'));
        $trans->delivery_country = trim(Input::get('delivery_country'));
        $trans->total_amount = trim(Input::get('total_amount'));
        //for testing on localhost
        $trans->insert_by = Session::get('username');
        // $trans->insert_by = "Admin Name";
        $trans->insert_date = date("Y-m-d h:i:sa");
        //for testing on localhost
        $trans->modify_by = Session::get('username');
        // $trans->modify_by = "Admin Name";
        $trans->modify_date = date("Y-m-d h:i:sa");
        $trans->save();

        return $insertedId = $trans->id;

    }

    /**
     * Save transaction
     * @return [type] [description]
     */
    public function scopeSave_transaction()
    {
        if (Input::has('id'))
        {
            $trans_id = Input::get('id');
            $trans = Transaction::find($trans_id);
            $trans->status = Input::get('status');
            $trans->parcel_status = Input::get('parcel_status');
            $ori_status = Input::get('ori_status');
            $new_status = Input::get('status');
            $trans->delivery_name = trim(Input::get('delivery_name'));
            $trans->delivery_contact_no = trim(Input::get('delivery_contact_no'));
            $trans->delivery_addr_1 = trim(Input::get('delivery_addr_1'));
            $trans->delivery_addr_2 = trim(Input::get('delivery_addr_2'));
            $trans->delivery_postcode = trim(Input::get('delivery_postcode'));
            $trans->delivery_city = trim(Input::get('delivery_city'));
            $trans->delivery_state = trim(Input::get('delivery_state'));
            $trans->delivery_country = trim(Input::get('delivery_country'));
            $trans->total_amount = Input::get('total_amount') + Input::get('coupon_amount') + Input::get('point_amount') - Input::get('gst_total');
            $trans->special_msg = trim(Input::get('special_msg'));
            $trans->external_ref_number = trim(Input::get('external_ref_number'));
            //for testing on localhost
            //$trans->modify_by = "Admin Name";
            $trans->modify_by = Session::get('username');
            $trans->modify_date = date("Y-m-d h:i:sa");
 
            if ($ori_status != $new_status) {
		        $type = 'Transaction';
                Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,Session::get('username'));          
            }

            //temporary not allow to amend input tax value
            // if (Input::has('gst_seller'))
            // {
            //     foreach (Input::get('gst_seller') as $key => $value) {
            //         // echo "key: " . $key . " || value: " . $value . "<br>";
            //         $tranD = TDetails::find($key);
            //         $tranD->gst_seller = $value == '' ? 0 : $value;
            //         $tranD->save();
            //     }
            // }

            // removed, as not allow to insert transaction details
            // if (Input::has('sku'))
            // {
            //     $sellerid = Seller::select('id')->where('username', '=', trim(Input::get('seller_username')))->first();

            //     if ($sellerid == null)
            //     {
            //         $sellerid = 0;
            //     }

            //     $itemID = Product::select('id')->where('sku', '=', trim(Input::get('sku')))->first();

            //     if ($itemID == null)
            //     {
            //         $itemID = 0;
            //     }

            //     $tranD = new TDetails;
            //     $tranD->product_id = $itemID;
            //     $tranD->sku = trim(Input::get('sku'));
            //     $tranD->price_label = trim(Input::get('price_label'));
            //     $tranD->seller_sku = trim(Input::get('seller_sku'));
            //     $tranD->price = Input::get('price') == '' ? 0 : Input::get('price');
            //     $tranD->delivery_fees = Input::get('delivery_fees')== '' ? 0 : Input::get('delivery_fees');
            //     $tranD->seller_id = $sellerid;
            //     $tranD->seller_username = trim(Input::get('seller_username'));
            //     $tranD->total = Input::get('total')== '' ? 0 : Input::get('total');
            //     $tranD->unit = Input::get('unit')== '' ? 0 : Input::get('unit');
            //     $tranD->delivery_time = trim(Input::get('delivery_time'));
            //     $tranD->p_option_id = 0;
            //     $tranD->transaction_id = $trans_id;
            //     $tranD->save();
            // }
            
            // Master Cancel
            if (strtolower($ori_status) != 'cancelled' && strtolower($new_status) == 'cancelled') {
                $logistic_transaction = LogisticTransaction::where('transaction_id', '=', $trans_id)->first();
                if ($logistic_transaction != null) {
                    $logistic_ori_status = $logistic_transaction->status;
                    $logistic_transaction->status = 6;
                    $logistic_transaction->modify_by = Session::get('username');
                    $logistic_transaction->save();
                    Transaction::statusHistory($logistic_ori_status,6, $trans_id, $logistic_transaction->id,null,'Logistic',Session::get('username'));

                    $latest_batch_id = LogisticBatch::where('logistic_id', '=', $logistic_transaction->id)->max('id');
                    if ($latest_batch_id != null) {
                        $logistic_batch = LogisticBatch::find($latest_batch_id);
                        $batch_ori_status = $logistic_batch->status;
                        $logistic_batch->status = 5;
                        $logistic_batch->modify_by = Session::get('username');
                        $logistic_batch->save();
                        Transaction::statusHistory($batch_ori_status,5, $trans_id, null,$logistic_batch->id,'Batch',Session::get('username'));
                    }
                }
            } else if (strtolower($ori_status) == 'cancelled' && strtolower($new_status) != 'cancelled') {
                $logistic_transaction = LogisticTransaction::where('transaction_id', '=', $trans_id)->first();
                if ($logistic_transaction != null) {
                    $logistic_ori_status = $logistic_transaction->status;
                    $logistic_transaction->status = 0;
                    $logistic_transaction->modify_by = Session::get('username');
                    $logistic_transaction->save();
                    Transaction::statusHistory($logistic_ori_status,0, $trans_id, $logistic_transaction->id,null,'Logistic',Session::get('username'));

                    $latest_batch_id = LogisticBatch::where('logistic_id', '=', $logistic_transaction->id)->max('id');
                    if ($latest_batch_id != null) {
                        $logistic_batch = LogisticBatch::find($latest_batch_id);
                        $batch_ori_status = $logistic_batch->status;
                        $logistic_batch->status = 0;
                        $logistic_batch->modify_by = Session::get('username');
                        $logistic_batch->save();
                        Transaction::statusHistory($batch_ori_status,0, $trans_id, null,$logistic_batch->id,'Batch',Session::get('username'));
                    }
                }
            }
            
            // Handle Return FOC to available 
            if(strtolower($new_status) == 'cancelled' && strtolower($new_status) == 'cancelled'){
                
                $TDetails = TDetails::where('transaction_id', '=', $trans_id)->get();
                foreach ($TDetails as $key => $details) {
                    if($details->action_type == 'FOC'){
                        $jocom_foc_reward_transaction = DB::table('jocom_foc_reward_transaction')->where("transaction_id",$trans_id)->first();
                        if(count($jocom_foc_reward_transaction)>0) {
                        $FocReward = FocReward::find($jocom_foc_reward_transaction->reward_id);
                        $FocReward->balance_quantity = $FocReward->balance_quantity + $details->unit;
                        $FocReward->save();
    
                        $FocRewardTransaction = DB::table('jocom_foc_reward_transaction')->insertGetId(
                            array(
                                "reward_id"=>$jocom_foc_reward_transaction->id,
                                "flow_type"=>'RTN',
                                "quantity"=>$details->unit,
                                "transaction_id"=>$trans_id,
                                "created_at"=>DATE("Y-m-d h:i:s"),
                            )
                        );
                        }    
                    }
                }
            }

            if ($ori_status == 'completed' && $new_status != 'completed')
            {
                $details = TDetails::where('transaction_id', '=', $trans_id)->get();

                foreach ($details as $detail)
                {
                    // purchases for charity
                    if ($trans->charity_id > 0)
                    {
                        $product = CharityProduct::where('charity_id', $trans->charity_id)->where('product_price_id', $detail->p_option_id)->first();

                        if (isset($product->id))
                        {
                            $product->qty += $detail->unit;
                            // $product->stock -= $detail->unit;
                            $product->save();
                        }
                    }
                    else
                    {
                        // normal order, not special pricing
                        if ($detail->sp_group_id == 0)
                        {
                            $product = Price::find($detail->p_option_id);

                            if (isset($product->id))
                            {
                                $product->qty += $detail->unit;
                                // $product->stock -= $detail->unit;
                                $product->save();
                            }
                        }
                    }            
                }

                $coupon = TCoupon::where('transaction_id', '=', $trans_id)->first();

                if (isset($coupon->coupon_code))
                {
                    $limit = Coupon::where('coupon_code', '=', $coupon->coupon_code)->first();
                    if(isset($limit->q_limit) && $limit->q_limit == 'Yes')
                    {
                        $limit->qty = $limit->qty + 1;
                        $limit->save();
                    }
                }

                $transaction = Transaction::findOrFail($trans_id);

                $userId     = $transaction->getUserId();
                $userPoints = PointUser::getPoints($userId);

                foreach ($userPoints as $userPoint)
                {
                    $pointTransaction = new PointTransaction($userPoint);
                    $pointTransaction->reversal($trans_id);
                }
            }
            elseif ($ori_status != 'completed' && $new_status == 'completed')
            {
                //$trans->invoice_date = date("Y-m-d");

                $details = TDetails::where('transaction_id', '=', $trans_id)->get();

                foreach ($details as $detail)
                {
                    // purchases for charity
                    if ($trans->charity_id > 0)
                    {
                        $product = CharityProduct::where('charity_id', $trans->charity_id)->where('product_price_id', $detail->p_option_id)->first();

                        if (isset($product->id))
                        {
                            $product->qty -= $detail->unit;
                            $product->stock -= $detail->unit;
                            $product->save();
                        }
                    }
                    else
                    {
                        // normal order, not special pricing
                        if ($detail->sp_group_id == 0)
                        {
                            $product = Price::find($detail->p_option_id);

                            if (isset($product->id))
                            {
                                $product->qty -= $detail->unit;
                                $product->stock -= $detail->unit;
                                $product->save();
                            }
                        }
                    }            
                }

                $coupon = TCoupon::where('transaction_id', '=', $trans_id)->first();

                if (isset($coupon->coupon_code))
                {
                    $limit = Coupon::where('coupon_code', '=', $coupon->coupon_code)->first();
                    if(isset($limit->q_limit) && $limit->q_limit == 'Yes')
                    {
                        $limit->qty = $limit->qty - 1;
                        $limit->save();
                    }
                }

                $transaction = Transaction::findOrFail($trans_id);
                $pointTypes  = PointType::getActive();
                $userId      = $transaction->getUserId();

                foreach ($pointTypes as $pointType)
                {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($trans_id);
                }

                // Google Analytics
                //open connection
                $ch = curl_init();
                $url = asset('/') . "analytics/google/" . $trans_id;

                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);

                // Timeout in seconds
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);

                //execute post
                $result = curl_exec($ch);

                //close connection
                curl_close($ch);
            }

            //$insert_audit = General::audit_trail('Insert into jocom_transaction', 'Transaction.php', 'save_transaction()', 'Save Transaction', Session::get('username'));

            $trans->save();

            return $trans;
        }
        else
        {
            return false;
        }

    }

    public static function dashboard_total()
    {
        
        
        
        

        // $total = Transaction::where('status', '=', 'completed')->sum('total_amount');
        // $gst_total = Transaction::where('status', '=', 'completed')->sum('gst_total');

        // $coupon = DB::table('jocom_transaction AS a')
        // ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
        // ->sum('b.coupon_amount');

        // $point = DB::table('jocom_transaction AS a')
        // ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
        // ->where('a.status', '=', 'completed')
        // ->where('b.status', '=', '1')
        // ->sum('b.amount');

        // $refund = DB::table('jocom_refund')
        //             ->where('status', '=', 'confirmed')
        //             ->sum('jocom_refund.amount');

        // $amount = $total - $coupon - $point + $gst_total - $refund;
        
        /*  Hide on 2023/05/29
        $a =  date('2015-01-01 00:00:00');
        $b =  date('2018-11-22 23:59:59');
        $currentAmount = 0;
        for ($x=2013; $x<= date("Y"); $x++){
            
            $a = date("Y-m-d H:i:s", strtotime($x.'-01-01 00:00:00')); //date('Y-01-01 00:00:00',strtotime($x.'-'.'01'));
            $b = date("Y-m-d H:i:s", strtotime($x.'-12-31 23:59:59')); //date('Y-12-t 23:59:59',strtotime($x.'-'.'12'));
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
            ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
            ->select(
                DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
            		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                ELSE 
            		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
            		ROUND(SUM(
                    CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    ) , 2)
                ELSE 
            		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                END AS total_sales"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=", $b)
            ->first();
            
                    
            
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=", $b)
            ->first();
            
             $currentAmount += (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
            
        }
        */
            
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed','cancelled'])
            ->first();
        
         $selectedDateSales =  DB::table('jocom_transaction AS JT')
            ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
            ->select(DB::raw("SUM(JTD.actual_total_amount) AS total_sales"))
            ->whereIn('JT.status', ['completed','cancelled'])
            ->first();
            
            // $currentAmount = (double)$selectedDateSales->total_sales;
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
        
     
        return $currentAmount;
    }

    public static function dashboard_latest_transaction()
    {

        $trans = Transaction::where('status', '=', 'completed')->orderBy('id', 'DESC')->first();

        $coupon = DB::table('jocom_transaction AS a')
        ->select('b.coupon_amount')
        ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
        ->where('a.id', '=', $trans->id)
        ->first();

        $point = DB::table('jocom_transaction AS a')
        ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
        ->where('a.id', '=', $trans->id)
        ->where('b.status', '=', '1')
        ->sum('b.amount');

        $total = $trans->total_amount - $coupon->coupon_amount - $point + $trans->gst_total;

        $transD = array();

        $transD['id'] = $trans->id;
        $transD['total_amount'] = $total;
        $transD['transaction_date'] = $trans->transaction_date;


        return $transD;
    }

    public static function get_transaction($period = null)
    {

        
          $transaction = Transaction::where('invoice_date', 'like', '%'.$period.'%')
                                ->where('status', '=', 'completed')
                                ->orderBy('transaction_date')
                                ->orderBy('id')
                                ->get();
//        $transaction = Transaction::where('transaction_date', 'like', '%'.$period.'%')
//                                ->where('status', '=', 'completed')
//                                ->orderBy('transaction_date')
//                                ->orderBy('id')
//                                ->get(); /* CHANGE ON 04-07-2016 */


        if(count($transaction)>0)
        {
            foreach ($transaction as $k => $v)
            {
                $tranD = array();
                $eInvoice = "";

                $parentInv  = DB::table('jocom_transaction_parent_invoice')->where('transaction_id', '=', $transaction[$k]->id)->first();
                if (count($parentInv)>0)
                    $eInvoice = $parentInv->parent_inv;

                $coupon = TCoupon::select('coupon_amount')->where('transaction_id', '=', $transaction[$k]->id)->first();
                $coupon_amount = 0;
                if (count($coupon)>0)
                {
                    $coupon_amount = $coupon->coupon_amount;
                }

                $point = DB::table('jocom_transaction AS a')
                ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
                ->where('a.id', '=', $transaction[$k]->id)
                ->where('b.status', '=', '1')
                ->sum('b.amount');

                $transaction_detail = TDetails::where('transaction_id', '=', $transaction[$k]->id)->get();

                foreach ($transaction_detail as $key => $value)
                {
                    $tranD[] = array(
                        'id' => $transaction_detail[$key]->id,
                        'disc' => $transaction_detail[$key]->disc,
                        'gst_rate_item' => $transaction_detail[$key]->gst_rate_item,
                        'gst_seller' => $transaction_detail[$key]->gst_seller,
                        'seller_username' => $transaction_detail[$key]->seller_username,
                        'total' => $transaction_detail[$key]->total,
                        'p_referral_fees' => $transaction_detail[$key]->p_referral_fees,
                        'p_referral_fees_type' => $transaction_detail[$key]->p_referral_fees_type,
                        'price' => $transaction_detail[$key]->price,
                        'unit' => $transaction_detail[$key]->unit,
                        'po_no' => $transaction_detail[$key]->po_no,
                        'parent_po' => $transaction_detail[$key]->parent_po
                    );
                }

                $tran[] = array(
                        'id' => $transaction[$k]->id,
                        'invoice_date' => $transaction[$k]->invoice_date,
                        'transaction_date' => date("Y-m-d", strtotime($transaction[$k]->transaction_date)),
                        'delivery_charges' => $transaction[$k]->delivery_charges,
                        'process_fees' => $transaction[$k]->process_fees,
                        'gst_rate' => $transaction[$k]->gst_rate,
                        'gst_total' => $transaction[$k]->gst_total,
                        'total_amount' => $transaction[$k]->total_amount,
                        'coupon_amount' => $coupon_amount,
                        'point' => $point,
                        'invoice_no' => $transaction[$k]->invoice_no,
                        'e_invoice' => $eInvoice,
                        'item' => $tranD,
                        'parent_po' => $transaction_detail[$key]->parent_po
                    );

                // $transaction_detail[$transaction[$k]->id] = TDetails::where('transaction_id', '=', $transaction[$k]->id)->get();
            }
        }

        return $tran;
    }

    public static function calculate_gst($record = null)
    {
        if (count($record) > 0)
        {
            foreach ($record as $row)
            {
                $total_gst_item[$row['id']] = 0;
                $total_gst_disc[$row['id']] = 0;

                $total_nongst_item[$row['id']] = 0;
                $total_nongst_disc[$row['id']] = 0;

                $total_gst_seller[$row['id']] = 0;
                $gst_seller[$row['id']] = 0;
                $total_nongst_seller[$row['id']] = 0;

                foreach ($row['item'] as $itemrow)
                {
                    if (count($itemrow) >= 0)
                    {
                        if($itemrow['gst_rate_item'] != 0)
                        {
                            $total_gst_item[$row['id']] += $itemrow['total'];
                            $total_gst_disc[$row['id']] += $itemrow['disc'];
                            $total_gst_seller[$row['id']] += ($itemrow['price']-(isset($itemrow['p_referral_fees']) ? (isset($itemrow['p_referral_fees_type']) && $itemrow['p_referral_fees_type'] == 'N' ? $itemrow['p_referral_fees'] : ($itemrow['p_referral_fees'] * $itemrow['price'] / 100)) : 0)) * $itemrow['unit'];
                            $gst_seller[$row['id']] += $itemrow['gst_seller'];
                        }
                        else
                        {
                            $total_nongst_item[$row['id']] += $itemrow['total'];
                            $total_nongst_disc[$row['id']] += $itemrow['disc'];
                            $total_nongst_seller[$row['id']] += ($itemrow['price']-(isset($itemrow['p_referral_fees']) ? (isset($itemrow['p_referral_fees_type']) && $itemrow['p_referral_fees_type'] == 'N' ? $itemrow['p_referral_fees'] : ($itemrow['p_referral_fees'] * $itemrow['price'] / 100)) : 0)) * $itemrow['unit'];
                        }
                    }

                }

                $report[] = array(
                        'id' => $row['id'],
                        'transaction_date' => $row['transaction_date'],
                        'invoice_date' => $row['invoice_date'],
                        'delivery_charges' => $row['delivery_charges'],
                        'process_fees' => $row['process_fees'],
                        'gst_rate' => $row['gst_rate'],
                        'gst_total' => $row['gst_total'],
                        'total_amount' => $row['total_amount'],
                        'coupon_amount' => $row['coupon_amount'],
                        'point' => $row['point'],
                        'invoice_no' => $row['invoice_no'],
                        'total_gst_item' => $total_gst_item[$row['id']],
                        'total_gst_disc' => $total_gst_disc[$row['id']],
                        'total_nongst_item' => $total_nongst_item[$row['id']],
                        'total_nongst_disc' => $total_nongst_disc[$row['id']],
                        'total_gst_seller' => $total_gst_seller[$row['id']],
                        'total_nongst_seller' => $total_nongst_seller[$row['id']],
                        'gst_seller' => $gst_seller[$row['id']],
                        'invoice_no' => $row['invoice_no'],
                        'e_invoice' => $row['e_invoice'],
                        'parent_po'=>$itemrow['parent_po'],
                    );
            }

            return $report;
        }
        else return null;

    }

    public static function calculate_gst_seller($record = null)
    {
        if (count($record) > 0)
        {
            foreach ($record as $row)
            {
                foreach ($row['item'] as $itemrow)
                {
                    if (count($itemrow) >= 0)
                    {
                        if ($total_gst_item[$row['id']][$itemrow['po_no']] == null)
                            $total_gst_item[$row['id']][$itemrow['po_no']] = 0;

                        if ($total_gst_disc[$row['id']][$itemrow['po_no']] == null)
                            $total_gst_disc[$row['id']][$itemrow['po_no']] = 0;

                        if ($total_gst_seller[$row['id']][$itemrow['po_no']] == null)
                            $total_gst_seller[$row['id']][$itemrow['po_no']] = 0;

                        if ($gst_seller[$row['id']][$itemrow['po_no']] == null)
                            $gst_seller[$row['id']][$itemrow['po_no']] = 0;

                        if ($total_nongst_item[$row['id']][$itemrow['po_no']] == null)
                            $total_nongst_item[$row['id']][$itemrow['po_no']] = 0;

                        if ($total_nongst_disc[$row['id']][$itemrow['po_no']] == null)
                            $total_nongst_disc[$row['id']][$itemrow['po_no']] = 0;

                        if ($total_nongst_seller[$row['id']][$itemrow['po_no']] == null)
                            $total_nongst_seller[$row['id']][$itemrow['po_no']] = 0;

                        if($itemrow['gst_rate_item'] != 0)
                        {
                            $total_gst_item[$row['id']][$itemrow['po_no']] += $itemrow['total'];
                            $total_gst_disc[$row['id']][$itemrow['po_no']] += $itemrow['disc'];
                            $total_gst_seller[$row['id']][$itemrow['po_no']] += ($itemrow['price']-(isset($itemrow['p_referral_fees']) ? (isset($itemrow['p_referral_fees_type']) && $itemrow['p_referral_fees_type'] == 'N' ? $itemrow['p_referral_fees'] : ($itemrow['p_referral_fees'] * $itemrow['price'] / 100)) : 0)) * $itemrow['unit'];
                            $gst_seller[$row['id']][$itemrow['po_no']] += $itemrow['gst_seller'];
                        }
                        else
                        {
                            $total_nongst_item[$row['id']][$itemrow['po_no']] += $itemrow['total'];
                            $total_nongst_disc[$row['id']][$itemrow['po_no']] += $itemrow['disc'];
                            $total_nongst_seller[$row['id']][$itemrow['po_no']] += ($itemrow['price']-(isset($itemrow['p_referral_fees']) ? (isset($itemrow['p_referral_fees_type']) && $itemrow['p_referral_fees_type'] == 'N' ? $itemrow['p_referral_fees'] : ($itemrow['p_referral_fees'] * $itemrow['price'] / 100)) : 0)) * $itemrow['unit'];
                        }
                    }

                }

                foreach ($total_gst_item[$row['id']] as $k => $v)
                {
                    $report[] = array(
                            'id' => $row['id'],
                            'transaction_date' => $row['transaction_date'],
                            'invoice_date' => $row['invoice_date'],
                            // 'delivery_charges' => $row['delivery_charges'],
                            // 'process_fees' => $row['process_fees'],
                            'gst_rate' => $row['gst_rate'],
                            'gst_total' => $row['gst_total'],
                            'total_amount' => $row['total_amount'],
                            // 'coupon_amount' => $row['coupon_amount'],
                            // 'invoice_no' => $row['invoice_no'],
                            'total_gst_item' => $total_gst_item[$row['id']][$k],
                            // 'total_gst_disc' => $total_gst_disc[$row['id']][$k],
                            // 'total_nongst_item' => $total_nongst_item[$row['id']][$k],
                            // 'total_nongst_disc' => $total_nongst_disc[$row['id']][$k],
                            'total_gst_seller' => $total_gst_seller[$row['id']][$k],
                            'total_nongst_seller' => $total_nongst_seller[$row['id']][$k],
                            'gst_seller' => $gst_seller[$row['id']][$k],
                            'po_no' => $k,
                            'parent_po'=>$itemrow['parent_po'],
                        );

                }
            }

            return $report;
        }
        else return null;

    }

    public static function gst_report($record = null, $year="", $month="", $tenure="", $type="")
    {
        $tempname = $year . "_" . $month;
        if($type != "")
            $tempname .= "_" . $tenure . $type;

        $haveFile = true;
        $file_name = "";
        $add = "";
        $count = 1;

        $path = Config::get('constants.GST_REPORT_PATH') . "/" . $year;

        if(!file_exists($path . "/" . $file_name))
            mkdir($path, 0755, true);

        while ($haveFile === true)
        {
            $fileName = 'report_gst_'.$tempname.$add.'_output.csv';

            if(Transaction::check_file($fileName, $path))
            {
                //file exist
                $haveFile = true;
                $add = "-" . $count;
                $count++;
            }
            else
                //no file
                $haveFile = false;
        }

        // $fileName = 'report_gst_'.$tempname.$add.'.csv';

        $file = fopen($path.'/'.$fileName, 'w');

        fputcsv($file, ['DATE','Invoice Date', 'Transaction ID', 'INVOICE NO', 'eInvoice','ePO NO', 'TAXABLE AMOUNT(excluding GST)', 'OUTPUT GST AMOUNT', 'ZERO RATED AMOUNT', 'TOTAL', 'DELIVERY CHARGES', 'PROCESS FEES', 'POINT REDEEMED']);
        // fputcsv($file, ['DATE', 'Transaction ID', 'INVOICE NO', 'DESCRIPTION', 'TAXABLE AMOUNT(excluding GST)', 'STANDARD RATE', 'OUTPUT GST AMOUNT', 'ZERO RATED AMOUNT', 'ZERO RATE', 'TOTAL']);

        foreach ($record as $row)
        {
            $gst_amount = $row['total_gst_item'] - $row['total_gst_disc'] + $row['delivery_charges'] + $row['process_fees'];
            $non_gst_amount = $row['total_nongst_item'] - $row['total_nongst_disc'];
            $total = $row['total_amount'] + $row['gst_total'] - $row['coupon_amount'] - $row['point'];

            fputcsv($file, [
                $row['transaction_date'],
                $row['invoice_date'],
                $row['id'],
                $row['invoice_no'],
                $row['e_invoice'],
                $row['parent_po'],
                // "",
                $gst_amount,
                // $row['gst_rate'],
                $row['gst_total'],
                $non_gst_amount,
                // "0",
                round($total, 2),
                $row['delivery_charges'],
                $row['process_fees'],
                $row['point']
            ]);
        }

        fclose($file);

        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test')
            $mail = ['maruthu@tmgrocer.com'];
        else
            $mail = ['lim@tmgrocer.com'];

        $subject = "GST Report: " . $fileName;
        $attach = $path . "/" . $fileName;

        $body = array('title' => 'GST');

        Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
            {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($mail, '')->subject($subject);
                $message->attach($attach);
            }
        );

        return "1";
    }

    public static function gst_seller_report($record = null, $year="", $month="", $tenure="", $type="")
    {
        $tempname = $year . "_" . $month;
        if($type != "")
            $tempname .= "_" . $tenure . $type;

        $haveFile = true;
        $file_name = "";
        $add = "";
        $count = 1;

        $path = Config::get('constants.GST_REPORT_PATH') . "/" . $year;

        if(!file_exists($path . "/" . $file_name))
            mkdir($path, 0755, true);

        while ($haveFile === true)
        {
            $fileName = 'report_gst_'.$tempname.$add.'_input.csv';

            if(Transaction::check_file($fileName, $path))
            {
                //file exist
                $haveFile = true;
                $add = "-" . $count;
                $count++;
            }
            else
                //no file
                $haveFile = false;
        }

        // $fileName = 'report_gst_'.$tempname.$add.'.csv';

        $file = fopen($path.'/'.$fileName, 'w');

        fputcsv($file, ['DATE','Invoice Date', 'Transaction ID', 'PO NO','ePO NO', 'TAXABLE AMOUNT(excluding GST)', 'INPUT GST AMOUNT', 'ZERO RATED AMOUNT', 'TOTAL']);

        foreach ($record as $row)
        {
            $gst_amount = $row['total_gst_seller'];
            $non_gst_amount = $row['total_nongst_seller'];
            $gst_seller = $row['gst_seller'];
            $total = $gst_amount + $non_gst_amount + $gst_seller;

            fputcsv($file, [
                $row['transaction_date'],
                $row['invoice_date'],
                $row['id'],
                $row['po_no'],
                $row['parent_po'],
                number_format($gst_amount, 2, ".", ""),
                number_format($gst_seller, 2, ".", ""),
                number_format($non_gst_amount, 2, ".", ""),
                number_format($total, 2, ".", "")
            ]);
        }

        fclose($file);

        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test')
            $mail = ['maruthu@tmgrocer.com'];
        else
            $mail = ['lim@tmgrocer.com'];

        $subject = "GST Report: " . $fileName;
        $attach = $path . "/" . $fileName;

        $body = array('title' => 'GST Seller');

        Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
            {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($mail, '')->subject($subject);
                $message->attach($attach);
            }
        );

        return "1";
    }

    public static function check_file($file_name = "", $path = "")
    {
        if(!file_exists($path . "/" . $file_name))
            $hasFile = 0;
        else
            $hasFile = 1;

        return $hasFile;
    }

    // temporary function to insert previous order of JIT
    public static function insert_jit($newfile, $insertfile, $field)
    {
        // create a copy of inserted product
        $inserted = fopen($insertfile, "w");
        fputcsv($inserted, $field);

        $fp = fopen($newfile, "r");

        while(! feof($fp))
        {
            $data = fgetcsv($fp);
            $num = count($data);

            if (! is_bool($data))
            {
                for ($i = 0; $i < $num; $i++)
                {
                    $insertInd = true;

                    if ($data[$i] == "Date")
                    {
                        $insertInd = false;
                        break;
                    }


                    switch ($i)
                    {
                        case 0:
                            $trans['transaction_date'] = date("Y-m-d h:i:s", strtotime($data[$i]));
                            $trans['invoice_date'] = date("Y-m-d", strtotime($data[$i]));
                            break;

                        case 1:
                            $trans['buyer_username'] = $data[$i];
                            $tempcust = Customer::select('*')->where('username', '=', $data[$i])->first();
                            $trans['buyer_id'] = $tempcust->id;

                            $trans['delivery_name'] = $tempcust->full_name;
                            $trans['delivery_contact_no'] = isset($tempcust->home_num) ? $tempcust->home_num : $tempcust->mobile_no;
                            $trans['buyer_email'] = isset($tempcust->email) ? $tempcust->email : 'sbv@abc.com';
                            $trans['delivery_addr_1'] = "Unit 9-1, Level 9,";
                            $trans['delivery_addr_2'] = "Tower 3, Avenue 3, Bangsar South,";
                            $trans['delivery_postcode'] = "59200";
                            $trans['delivery_city'] = "Kuala Lumpur";
                            $trans['delivery_city_id'] = "4580113";
                            $trans['delivery_state'] = "WP-Kuala Lumpur";
                            $trans['delivery_country'] = "Malaysia";
                            $trans['delivery_charges'] = "0";
                            $trans['delivery_condition'] = "Delivery fees is set in the item";
                            $trans['process_fees'] = "0";
                            $trans['gst_rate'] = "6";
                            $trans['gst_process'] = "0";
                            $trans['gst_delivery'] = "0";
                            $trans['gst_total'] = "0";


                            break;

                        case 2:
                            $details['product_id'] = $data[$i];
                            $tempproduct = Product::find($data[$i]);
                            $details['sku'] = $tempproduct->sku;
                            $details['seller_id'] = '40';
                            $details['seller_username'] = 'JOCOM';


                            $tempdetails = Price::select('*')->where('product_id', '=', $data[$i])->where('status', '=', '1')->where('default', '=', '1')->first();
                            $details['p_option_id'] = $tempdetails->id;
                            $details['p_referral_fees'] = '20';
                            $details['p_referral_fees_type'] = 'N';
                            $details['price_label'] = $tempdetails->label;
                            $details['seller_sku'] = $tempdetails->seller_sku;
                            $details['delivery_fees'] = '0';
                            $details['delivery_time'] = '24 hours';

                            $details['disc'] = '0';
                            $details['gst_rate_item'] = '0';
                            $details['gst_amount'] = '0';
                            $details['gst_seller'] = '0';
                            break;

                        case 3:
                            $trans['total_amount'] = $data[$i];
                            break;

                        case 4:
                            $details['unit'] = $data[$i];
                            break;

                        case 5:
                            $details['price'] = $data[$i];

                            $details['total'] = $details['price'] * $details['unit'];
                            break;

                        case 6:
                            $details['p_referral_fees'] = $data[$i];
                            break;

                        case 7:
                            $details['p_referral_fees_type'] = $data[$i];
                            break;

                        default:
                            # code...
                            break;
                    }
                }

                if ($insertInd == true)
                {
                    $tempdata = DB::table('jocom_running_jit')->select('*')->get();

                    foreach ($tempdata as $row)
                    {
                        $counter = $row->counter + 1;

                        switch ($row->value_key)
                        {
                            case 'invoice_no':
                                $tempdoc = Config::get('constants.INVOICE_PREFIX') . str_pad($counter, 5, "0", STR_PAD_LEFT);

                                $trans['invoice_no'] = $tempdoc;
                                // $sql = DB::table('jocom_running_jit')
                                //     ->where('value_key', 'invoice_no')
                                //     ->update(array('counter' => $counter));
                                break;

                            case 'po_no':
                                $tempdoc = "PO-" . str_pad($counter, 5, "0", STR_PAD_LEFT);

                                $details['po_no'] = $tempdoc;
                                // $sql = DB::table('jocom_running_jit')
                                //     ->where('value_key', 'po_no')
                                //     ->update(array('counter' => $counter));
                                break;

                            case 'do_no':
                                $tempdoc = "DO-" . str_pad($counter, 5, "0", STR_PAD_LEFT);

                                $trans['do_no'] = $tempdoc;
                                // $sql = DB::table('jocom_running_jit')
                                //     ->where('value_key', 'do_no')
                                //     ->update(array('counter' => $counter));
                                break;

                            case 'transaction_id':
                                $trans['id'] = $counter;
                                $sql = DB::table('jocom_running_jit')
                                    ->where('value_key', 'transaction_id')
                                    ->update(array('counter' => $counter));

                                // max transaction can go up to 4600 only
                                if ($trans['id'] > 4600)
                                    return;
                                break;

                            default:
                                # code...
                                break;
                        }
                    }

                    $trans['status'] = 'completed';
                    $trans['insert_by'] = Session::get('username');
                    $trans['insert_date'] = date("Y-m-d h:i:sa");
                    $trans['modify_by'] = Session::get('username');
                    $trans['modify_date'] = date("Y-m-d h:i:sa");
                    $trans['lang'] = "EN";
                    $trans['ip_address'] = Request::getClientIp();
                    $trans['tnc'] = "1";

                    $details['transaction_id'] = $trans['id'];

                    DB::transaction(function() use ($trans, $details)
                    {
                        $transID = DB::table('jocom_transaction')->insert($trans);
                        $detailsID = DB::table('jocom_transaction_details')->insert($details);

                        $tempInv = Transaction::generateInv($trans['id']);
                        $tempPO  = Transaction::generatePO($trans['id']);
                        $tempDO  = Transaction::generateDO($trans['id']);
                    });

                    fputcsv($inserted, $data, ",", "\"");
                }
            }
        }

        fclose($inserted);
        fclose($fp);

    }


    public static function diff_pending($job)
    {
        $path  = Config::get('constants.CSV_JIT_PATH');

        $no_pending = false;

        if (count($job) > 0)
        {

            foreach ($job as $key => $value)
            {
                $file_original = 'original_' . $job['in_file'];
                $file_inserted = 'inserted_' . $job['in_file'];
                $file_diff = 'diff_' . $job['in_file'];

                if(file_exists($path.$file_original))
                {
                    // generate diff file for pending product list
                    $output = shell_exec('diff -N --unchanged-line-format= --old-line-format= --new-line-format=\'%L\' '. $path.$file_inserted .' '. $path.$file_original .' > '. $path.$file_diff);

                    // -N, --new-file
                    //     treat absent files as empty

                    // -E, --ignore-tab-expansion
                    //     ignore changes due to tab expansion


                    unlink($path.$file_original);
                    rename($path.$file_diff, $path.$file_original); // rename diff file to original

                    if(file_exists($path.$file_inserted))
                        unlink($path.$file_inserted);


                    if(filesize($path.$file_original) == 0)
                    {
                        unlink($path.$file_original);

                        $temprow = DB::table('jocom_job_queue')->where('id', '=', $job['id'])->update(array('status' => 2));

                        $no_pending = true;
                    }
                    else
                        $no_pending = false;
                }
                else
                {
                    $temprow = DB::table('jocom_job_queue')->where('id', '=', $job['id'])->update(array('status' => 2)); // update to complete if no pending file
                    $no_pending = true;
                }
            }
        }

        return $no_pending;
    }

    // temporary function to move category
    public static function temp_category($newfile, $insertfile, $field)
    {
        // create a copy of inserted product
        $inserted = fopen($insertfile, "w");
        fputcsv($inserted, $field);

        $fp = fopen($newfile, "r");

        while(! feof($fp))
        {
            $data = fgetcsv($fp);
            $num = count($data);

            if (! is_bool($data))
            {
                for ($i = 0; $i < $num; $i++)
                {
                    $insertInd = true;

                    if ($data[$i] == "ProductID")
                    {
                        $insertInd = false;
                        break;
                    }
                    

                    switch ($i)
                    {
                        case 0:
                            $category['product_id'] = $data[$i];
                            break;

                        case 1:
                            $category['category_id'] = $data[$i];
                            break;

                        case 2:
                            $category['category_1'] = $data[$i];
                            break;

                        case 3:
                            $category['category_2'] = $data[$i];
                            break;

                        case 4:
                            $category['category_3'] = $data[$i];
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }

                if ($insertInd == true)
                {

                    DB::transaction(function() use ($category)
                    {
                        $full_cat = $category['category_id'];
                        $date = date("Y-m-d h:i:s");

                        DB::table('jocom_categories')->where('product_id', '=', $category['product_id'])->delete();

                        DB::table('jocom_categories')->insert(array('product_id' => $category['product_id'], 'category_id' => $category['category_id'], 'created_at' => $date, 'main' => '1'));

                        if ($category['category_1'] != '')
                        {
                            $full_cat = $full_cat . "," . $category['category_1'];

                            DB::table('jocom_categories')->insert(array('product_id' => $category['product_id'], 'category_id' => $category['category_1'], 'created_at' => $date));
                        }

                        if ($category['category_2'] != '')
                        {
                            $full_cat = $full_cat . "," . $category['category_2'];

                            DB::table('jocom_categories')->insert(array('product_id' => $category['product_id'], 'category_id' => $category['category_2'], 'created_at' => $date));
                        }

                        if ($category['category_3'] != '')
                        {
                            $full_cat = $full_cat . "," . $category['category_3'];

                            DB::table('jocom_categories')->insert(array('product_id' => $category['product_id'], 'category_id' => $category['category_3'], 'created_at' => $date));
                        }

                        DB::table('jocom_products')->where('id', '=', $category['product_id'])->update(array('category' => $full_cat));
                    });

                    fputcsv($inserted, $data, ",", "\"");
                }
            }
        }

        fclose($inserted);
        fclose($fp);
        
    }

    /**
     * Manually generate PO
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGeneratePO($query, $id, $manual = null)
    {
        // $gotPO = Transaction::checkfile($id, "PO");

        if ($gotPO == 'yes' && $manual != true)
        {
            return 'no';
            $id = null;
        }

        if ($id != null)
        {

            $trans = Transaction::find($id);
            //valid transaction
            if ($trans != null)
            {
                $general = array(
                        "po_no" => "",
                        "po_date" => date("Y-m-d", strtotime($trans->transaction_date)),
                        // "po_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                        "payment_terms" => "Cash/Credit Card",
                        "transaction_id" => $trans->id,
                        "delivery_name" => ($trans->delivery_name != '' ? $trans->delivery_name . "" : ''),
                        "delivery_contact_no" => ($trans->delivery_contact_no != '' ? $trans->delivery_contact_no . "" : ''),
                        "delivery_address_1" => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1 . "," : ''),
                        "delivery_address_2" => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2 . "," : ''),
                        "delivery_address_3" => ($trans->delivery_postcode != '' ? $trans->delivery_postcode . " " : '') . ($trans->delivery_city != '' ? $trans->delivery_city . "," : '') ,
                        "delivery_address_4" => ($trans->delivery_state != '' ? $trans->delivery_state . ", " : '') . $trans->delivery_country . ".",
                        "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                        );

                $involved_seller = DB::table('jocom_transaction_details')
                                    ->select('seller_username')
                                    ->where('transaction_id', '=', $trans->id)
                                    ->groupBy('seller_username')
                                    ->get();

                //each transaction details
                foreach($involved_seller as $sellerrow)
                {
                    $sellerTable = DB::table('jocom_seller')
                                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                                    ->where('username', '=', $sellerrow->seller_username)
                                    ->first();

                    $tempcountry = "";
                    $tempstate = "";
                    $tempcity = "";

                    $sellerCountry = Country::find($sellerTable->country);
                    if ($sellerCountry != null)
                    {
                        $tempcountry = $sellerCountry->name;
                    }

                    $sellerState = State::find($sellerTable->state);
                    if ($sellerState != null)
                    {
                       $tempstate = $sellerState->name .", ";
                    }

                    if (is_numeric($sellerTable->city))
                    {
                        $city_row = City::find($sellerTable->city);

                        if (count($city_row) > 0)
                            $tempcity = $city_row->name;
                    }
                    else
                        $tempcity = $sellerTable->city;

                    $seller = array(
                            "seller_name" => $sellerTable->company_name,
                            "seller_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1 . "," : ''),
                            "seller_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2 . "," : ''),
                            "seller_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode . " " : '') . ($tempcity != '' ? $tempcity . ", " : ''),
                            "seller_address_4" => $tempstate . $tempcountry . ".",
                            "seller_email" => $sellerTable->email,
                            "attn_name" => $sellerTable->company_name,
                            "contact_no" => $sellerTable->tel_num . ($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '') . $sellerTable->mobile_no,
                            "seller_gst" => $sellerTable->gst_reg_num,
                    );


                   // $product = DB::table('jocom_transaction_details AS a')
                   //              ->select('a.*', 'b.name')
                   //              ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                   //              ->where('a.transaction_id', '=', $trans->id)
                   //              ->where('a.seller_username', '=', $sellerTable->username)
                   //              ->get();

                    //with package product
                    $product = DB::table('jocom_transaction_details AS a')
                                ->select('a.*', 'b.name', 'c.name AS pname')
                                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                                ->leftJoin('jocom_product_package AS c', 'a.product_group', '=', 'c.sku')
                                ->where('a.transaction_id', '=', $trans->id)
                                ->where('a.seller_username', '=', $sellerTable->username)
                                ->get();


                    foreach($product as $prow)
                    {
                        $items[] = array(
                            "sku" => $prow->sku,
                            "price_label" => $prow->price_label,
                            "seller_sku" => $prow->seller_sku,
                            "description" => (isset($prow->name) ? $prow->name : ""),
                            "qty" => $prow->unit,
                            "u_price" => number_format($prow->price, 2, ".", ""),
                            "value" => number_format(($prow->price * $prow->unit), 2, ".", "")
                        );
                    }

                    $general = array_merge($general, array('items' => $items));

                    //loop until next empty PO number, to prevent error in jocom_running table
                    $haveFile = true;
                    while ($haveFile === true)
                    {
                        $po_counter = 0;

                        $running = DB::table('jocom_running_jit')
                                    ->select('*')
                                    ->where('value_key', '=', 'po_no')->first();

                        if ($running != null)
                        {
                            $po_counter = $running->counter + 1;
                            $sql = DB::table('jocom_running_jit')
                                        ->where('value_key', 'po_no')
                                        ->update(array('counter' => $po_counter));
                        }
                        else
                        {
                            $po_counter = 1;
                            $sql = DB::table('jocom_running_jit')->insert(array(
                                            array('value_key' => 'po_no', 'counter' => $po_counter),
                                        ));
                        }

                        $numPO = "PO-" . str_pad($po_counter, 5, "0", STR_PAD_LEFT);

                        $general['po_no'] = $numPO;

                        $file_name = urlencode($general['po_no']) . ".pdf";

                         if(!file_exists(Config::get('constants.PO_PDF_FILE_PATH') . "/" . $file_name))
                         {
                            $haveFile = false;
                         }
                    }


                    // update PO number to transaction details table
                    $sql = DB::table('jocom_transaction_details')
                                    ->where('seller_username', $sellerTable->username)
                                    ->where('transaction_id', $trans->id)
                                    ->update(array('po_no' => $numPO));

                    $doc_info = serialize($general);
                    // $doc_info .= serialize($trans);
                    $doc_info .= serialize($seller);
                    // $doc_info .= serialize($product);

                    $loop = true;
                    while($loop === true)
                    {
                        $tmp_str = "";
                        if(mb_strlen($doc_info, '8bit') > 65000) {
                            $tmp_str = substr($doc_info, 0, 60000);
                            $doc_info = substr($doc_info, 60000);

                        } else {
                            $loop = false;
                            $tmp_str = $doc_info;
                        }
                        $sql = DB::table('jocom_document_data')->insert(array(
                                array('doc_type' => 'seller_po', 'doc_no' => $general['po_no'], 'doc_info' => $tmp_str),
                            ));
                    }

                    if(!file_exists(Config::get('constants.PO_PDF_FILE_PATH') . "/" . $file_name))
                    {
                        include app_path('library/html2pdf/html2pdf.class.php');

                        $response = View::make('checkoutjit.po_view')
                                ->with('display_details', $general)
                                ->with('display_trans', $trans)
                                ->with('display_seller', $seller)
                                ->with('display_product', $product);

                        // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                        $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
                        $html2pdf->setDefaultFont('arialunicid0');
                        // $html2pdf->pdf->SetDisplayMode('fullpage');
                        // $html2pdf = new HTML2PDF('P','A4');
                        $html2pdf->WriteHTML($response);
                        //$html2pdf->Output("example.pdf");
                        $html2pdf->Output("./" . Config::get('constants.PO_PDF_FILE_PATH') . "/" . $file_name, 'F');

                    }
                } //end each transaction details
                return 'yes';
            } // end valid transaction
        }
        else
        {
            return 'no';
        }
    }


    /**
     * Manually generate Invoice
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGenerateInv($query, $id, $manual = null)
    {
        // $gotINV = Transaction::checkfile($id, "INV");

        if ($gotINV == 'yes' && $manual != true)
        {
            return 'no';
            $id = null;
        }


        if ($id != null)
        {

            $trans = Transaction::find($id);

            // valid transaction
            if ($trans != null)
            {
                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $coupon = TCoupon::where('transaction_id', '=', $id)->first();

                $payment_id = 0;

                if(count($paypal) > 0)
                {
                    $payment_id = $paypal->txn_id;
                }

                $general = array(
                        "invoice_no" => "",
                        "invoice_date" => $trans->invoice_date,
                        // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                        "payment_terms" => "cash/cc",
                        "transaction_id" => $trans->id,
                        "delivery_contact_no" => $trans->delivery_contact_no,
                        "payment_id" => $payment_id,
                        "transaction_date" => date("d-m-Y", strtotime($trans->transaction_date)),

                        "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
                        "buyer_email" => isset($buyer->email) ? $buyer->email : "",
                        "delivery_address_1" => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1 . "," : ''),
                        "delivery_address_2" => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2 . "," : ''),
                        "delivery_address_3" => ($trans->delivery_postcode != '' ? $trans->delivery_postcode . " " : '') . ($trans->delivery_city != '' ? $trans->delivery_city . "," : '') ,
                        "delivery_address_4" => ($trans->delivery_state != '' ? $trans->delivery_state . ", " : '') . $trans->delivery_country . ".",
                        "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                        "delivery_contact_no" => $trans->delivery_contact_no,
                    );


                $product = DB::table('jocom_transaction_details AS a')
                        ->select('a.*', 'b.name')
                        ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                        ->where('a.transaction_id', '=', $trans->id)
                        //->where('a.product_group', '!=', '')
                        ->get();

                foreach($product as $prow)
                {
                    $items[] = array(
                        "sku" => $prow->sku,
                        // to change to current
                        "price_label" => $prow->price_label,
                        "description" => (isset($prow->name) ? $prow->name : ""),
                        "qty" => $prow->unit,
                        "u_price" => number_format($prow->price, 2, ".", ""),
                        "value" => number_format(($prow->price * $prow->unit), 2, ".", "")
                    );
                }

                $group = DB::table('jocom_transaction_details_group AS a')
                        ->select('a.*', 'b.name')
                        ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                        ->where('a.transaction_id', '=', $trans->id)
                        ->get();

                foreach($group as $grow)
                {
                    $items[] = array(
                        "sku" => $grow->sku,
                        "price_label" => "-",
                        "description" => (isset($grow->product_name) ? $grow->product_name : ""),
                        "qty" => $grow->unit,
                        "u_price" => 0,
                        "value" => 0
                    );
                }

                $general = array_merge($general, array('items' => $items));

                // $results = DB::select('SELECT a.*, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ?', $trans->id);

                //var_dump( DB::getQueryLog() );

                $haveFile = true;
                while ($haveFile === true)
                {
                    $inv_counter = 0;

                    $running = DB::table('jocom_running_jit')
                                ->select('*')
                                ->where('value_key', '=', 'invoice_no')->first();

                    if ($running != null)
                    {
                        $inv_counter = $running->counter + 1;
                        $sql = DB::table('jocom_running_jit')
                                    ->where('value_key', 'invoice_no')
                                    ->update(array('counter' => $inv_counter));
                    }
                    else
                    {
                        $inv_counter = 1;
                        $sql = DB::table('jocom_running_jit')->insert(array(
                                        array('value_key' => 'invoice_no', 'counter' => $inv_counter),
                                    ));
                    }

                    $numINV = Config::get('constants.INVOICE_PREFIX') . str_pad($inv_counter, 5, "0", STR_PAD_LEFT);

                    $general['invoice_no'] = $numINV;

                    $file_name = urlencode($general['invoice_no']) . ".pdf";

                     if(!file_exists(Config::get('constants.INVOICE_PDF_FILE_PATH') . "/" . $file_name))
                     {
                        $haveFile = false;
                     }
                }

                // update Invoice number to transaction table
                $sql = DB::table('jocom_transaction')
                                ->where('id', $trans->id)
                                ->update(array('invoice_no' => $numINV,'invoice_date' => date("Y-m-d")));

                $doc_info = serialize($general);
                // $doc_info .= serialize($trans);
                // $doc_info .= serialize($paypal);
                // $doc_info .= serialize($coupon);
                // $doc_info .= serialize($product);
                // $doc_info .= serialize($group);

                $loop = true;
                while($loop === true)
                {
                    $tmp_str = "";
                    if(mb_strlen($doc_info, '8bit') > 65000) {
                        $tmp_str = substr($doc_info, 0, 60000);
                        $doc_info = substr($doc_info, 60000);

                    } else {
                        $loop = false;
                        $tmp_str = $doc_info;
                    }
                    $sql = DB::table('jocom_document_data')->insert(array(
                            array('doc_type' => 'buyer_inv', 'doc_no' => $general['invoice_no'], 'doc_info' => $tmp_str),
                        ));
                }

                $points = TPoint::transaction($id)->get();

                // Earned points
                $earnedPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)
                    ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                    ->get();

                $earnedId = [];

                foreach ($earnedPoints as $earnedPoint)
                {
                    $earnedId[] = $earnedPoint->id;
                }

                $reversalPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)
                    ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                    ->get();

                $reversedId = [];

                foreach ($reversalPoints as $reversalPoint)
                {
                    $reversedId[] = $reversalPoint->reversal;
                }

                $earnedPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))
                    ->get();

                if(!file_exists(Config::get('constants.INVOICE_PDF_FILE_PATH') . "/" . $file_name))
                {
                    include app_path('library/html2pdf/html2pdf.class.php');

                    $response = View::make('checkoutjit.invoice_view')
                            ->with('display_details', $general)
                            ->with('display_trans', $trans)
                            ->with('display_seller', $paypal)
                            ->with('display_coupon', $coupon)
                            ->with('display_product', $product)
                            ->with('display_group', $group)
                            ->with('display_points', $points)
                            ->with('display_earns', $earnedPoints);

                    // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                    $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
                    $html2pdf->setDefaultFont('arialunicid0');
                    // $html2pdf = new HTML2PDF('P','A4');
                    $html2pdf->WriteHTML($response);
                    //$html2pdf->Output("example.pdf");
                    $html2pdf->Output("./" . Config::get('constants.INVOICE_PDF_FILE_PATH') . "/" . $file_name, 'F');

                }
                return 'yes';
            } // end valid transaction

        }
        else
        {
            return 'no';
        }
    }

    /**
     * Manually generate DO
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGenerateDO($query, $id, $manual = null)
    {
        // $gotDO = Transaction::checkfile($id, "DO");
        //

        if ($gotDO == 'yes' && $manual != true)
        {
            return 'no';
            $id = null;
        }

        if ($id != null)
        {

            $trans = Transaction::find($id);

            // valid transaction
            if ($trans != null)
            {
                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $payment_id = 0;

                if ($paypal != null)
                {
                    $payment_id = $paypal->txn_id;
                }

                $general = array(
                        "do_no" => "",
                        "do_date" => date("Y-m-d", strtotime($trans->transaction_date)),
                        // "do_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                        "payment_terms" => "cash/cc",
                        "transaction_id" => $trans->id,
                        "delivery_contact_no" => $trans->delivery_contact_no,
                        "payment_id" => $payment_id,
                        "transaction_date" => date("d-m-Y", strtotime($trans->transaction_date)),

                        "delivery_name" => isset($trans->delivery_name) ? $trans->delivery_name : "",
                        // "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
                        // "buyer_email" => isset($buyer->email) ? $buyer->email : "",
                        "delivery_address_1" => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1 . "," : ''),
                        "delivery_address_2" => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2 . "," : ''),
                        "delivery_address_3" => ($trans->delivery_postcode != '' ? $trans->delivery_postcode . " " : '') . ($trans->delivery_city != '' ? $trans->delivery_city . "," : '') ,
                        "delivery_address_4" => ($trans->delivery_state != '' ? $trans->delivery_state . ", " : '') . $trans->delivery_country . ".",
                        "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                        "delivery_contact_no" => $trans->delivery_contact_no,
                    );

                $product = DB::table('jocom_transaction_details AS a')
                        ->select('a.*', 'b.name')
                        ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                        ->where('a.transaction_id', '=', $trans->id)
                        //->where('a.product_group', '!=', '')
                        ->get();

                foreach($product as $prow)
                {
                    $items[] = array(
                        "sku" => $prow->sku,
                        "price_label" => $prow->price_label,
                        "description" => (isset($prow->name) ? $prow->name : ""),
                        "qty" => $prow->unit,
                        "u_price" => number_format($prow->price, 2, ".", ""),
                        "value" => number_format(($prow->price * $prow->unit), 2, ".", "")
                    );
                }


                $group = DB::table('jocom_transaction_details_group AS a')
                        ->select('a.*', 'b.name')
                        ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                        ->where('a.transaction_id', '=', $trans->id)
                        ->get();

                foreach($group as $grow)
                {
                    $items[] = array(
                        "sku" => $grow->sku,
                        "price_label" => "-",
                        "description" => (isset($grow->product_name) ? $grow->product_name : ""),
                        "qty" => $grow->unit,
                        "u_price" => 0,
                        "value" => 0
                    );
                }

                $general = array_merge($general, array('items' => $items));

                // $results = DB::select('SELECT a.*, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ?', $trans->id);

                //var_dump( DB::getQueryLog() );

                $haveFile = true;
                while ($haveFile === true)
                {
                    $do_counter = 0;

                    $running = DB::table('jocom_running_jit')
                                ->select('*')
                                ->where('value_key', '=', 'do_no')->first();

                    if ($running != null)
                    {
                        $do_counter = $running->counter + 1;
                        $sql = DB::table('jocom_running_jit')
                                    ->where('value_key', 'do_no')
                                    ->update(array('counter' => $do_counter));
                    }
                    else
                    {
                        $do_counter = 1;
                        $sql = DB::table('jocom_running_jit')->insert(array(
                                        array('value_key' => 'do_no', 'counter' => $do_counter),
                                    ));
                    }

                    $numDO = "DO-" . str_pad($do_counter, 5, "0", STR_PAD_LEFT);

                    $general['do_no'] = $numDO;

                    $file_name = urlencode($general['do_no']) . ".pdf";

                     if(!file_exists(Config::get('constants.DO_PDF_FILE_PATH') . "/" . $file_name))
                     {
                        $haveFile = false;
                     }
                }

                // update Invoice number to transaction table
                $sql = DB::table('jocom_transaction')
                                ->where('id', $trans->id)
                                ->update(array('do_no' => $numDO));

                $doc_info = serialize($general);
                // $doc_info .= serialize($trans);
                // $doc_info .= serialize($paypal);
                // $doc_info .= serialize($product);
                // $doc_info .= serialize($group);

                $loop = true;
                while($loop === true)
                {
                    $tmp_str = "";
                    if(mb_strlen($doc_info, '8bit') > 65000) {
                        $tmp_str = substr($doc_info, 0, 60000);
                        $doc_info = substr($doc_info, 60000);

                    } else {
                        $loop = false;
                        $tmp_str = $doc_info;
                    }
                    $sql = DB::table('jocom_document_data')->insert(array(
                            array('doc_type' => 'buyer_do', 'doc_no' => $general['do_no'], 'doc_info' => $tmp_str),
                        ));
                }

                if(!file_exists(Config::get('constants.DO_PDF_FILE_PATH') . "/" . $file_name))
                {
                    include app_path('library/html2pdf/html2pdf.class.php');

                    $response = View::make('checkoutjit.do_view')
                            ->with('display_details', $general)
                            ->with('display_trans', $trans)
                            ->with('display_seller', $paypal)
                            ->with('display_product', $product)
                            ->with('display_group', $group);

                    $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                    $html2pdf->setDefaultFont('arialunicid0');
                    // $html2pdf = new HTML2PDF('P','A4');
                    $html2pdf->WriteHTML($response);
                    //$html2pdf->Output("example.pdf");
                    $html2pdf->Output("./" . Config::get('constants.DO_PDF_FILE_PATH') . "/" . $file_name, 'F');

                }
                return 'yes';
            } // end valid transaction

        }
        else
        {
            return 'no';
        }
    }

    public function scopeCheckfile($query, $id, $filetype = null)
    {
        if ($filetype == "INV")
        {
            $trans = Transaction::where('id', '=', $id)->where('invoice_no', '!=', '')->first();
            if ($trans != null)
            {
                return 'yes';
            }
            else
            {
                return 'no';
            }
        }
        else if ($filetype == "DO")
        {
            $trans = Transaction::where('id', '=', $id)->where('do_no', '!=', '')->first();
            if ($trans != null)
            {
                return 'yes';
            }
            else
            {
                return 'no';
            }
        }
        else if ($filetype == "PO")
        {
            $trans = TDetails::where('transaction_id', '=', $id)->where('po_no', '!=', '')->first();
            if ($trans != null)
            {
                return 'yes';
            }
            else
            {
                return 'no';
            }
        }
        else return 'no';

    }
    // end of temporary function to insert previous order of JIT


    public function coupon()
    {
        return $this->belongsToMany('Coupon', 'coupon_code');
    }

    public function tdetails()
    {
        return $this->hasMany('TDetails');
    }

    public function tpaypal()
    {
        return $this->hasOne('TPayPal');
    }

    public function tmolpay()
    {
        return $this->hasOne('TMolPay');
    }

    public function tmpay()
    {
        return $this->hasOne('TMPay');
    }



    // public function scopeGetTrans($query)
    // {
    //  return $query->where('id', '=', 320);

    // }

    public function getUserId()
    {
        if ($this->buyer_id > 0)
        {
            return $this->buyer_id;
        }
        else
        {
            $buyer = Customer::where('username', '=', $this->buyer_username)->firstOrFail();
            return $buyer->id;
        }
    }

    public function scopeIncomplete($query, $id)
    {
        return $query->where('status', '!=', 'completed')
            ->where('id', '=', $id);
    }

    /*
     * Get previous purchased item by username
     */
    public static function getPreviousPurchasedItem($username,$limit = 10){
        
        return DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details as JTD', 'JTD.transaction_id', '=', 'JT.id')
               // ->leftjoin('jocom_products as JP', 'JP.sku', '=', 'JTD.sku')
                ->select('JTD.sku','JTD.product_id' )
//                ->select('JP.id AS ProductID','JP.sku','JP.name','JP.name_my','JP.name_cn','JP.description','JP.description_cn','JP.description_my',
//                'JP.qrcode', 'JP.qrcode_file', 'JP.img_1' )
                ->where('JT.buyer_username', '=', $username)
                ->take($limit)->groupBy('JTD.product_id')->orderBy('JTD.id', 'DESC')->get();
}
    
    
    public static function getLastMonthTopPurchase($limit = 10){
        
        return DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details as JTD', 'JTD.transaction_id', '=', 'JT.id')
                ->leftjoin('jocom_products as JP', 'JP.sku', '=', 'JTD.sku')
                ->select('JP.id AS ProductID','JP.sku',
                        DB::raw("count(JTD.id) AS 'TotalPurchased'"),
                        DB::raw("MONTHNAME(STR_TO_DATE(MONTH(now() - INTERVAL 1 MONTH), '%m')) AS 'Month'"),
                        DB::raw("YEAR(JT.insert_date) AS 'Year'"),
                        'JP.name','JP.name_my','JP.name_cn','JP.description','JP.description_cn','JP.description_my',
                'JP.qrcode', 'JP.qrcode_file', 'JP.img_1' )
                ->whereRaw("YEAR(JT.insert_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(JT.insert_date) = MONTH(NOW() - INTERVAL 1 MONTH)")
                        
                ->take($limit)
                ->groupBy('JTD.sku')
                ->orderBy('TotalPurchased', 'DESC')->get();
        
    }
    
    // Added by Maruthu

    public function getPaymentGateway($transactionid,$status){
        $paytype="-";

        if(isset($status) && $status=='completed'){
            $molpay = DB::table('jocom_molpay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('payment_status','=','00')
                        ->first();

            $mpay = DB::table('jocom_mpay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('payment_status','=','0')
                        ->first();

            $paypal = DB::table('jocom_paypal_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('payment_status','=','Completed')
                        ->first();     
                        
            $boost = DB::table('jocom_boost_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('transaction_status','=','completed')
                        ->first();  
                        
            $revpay = DB::table('jocom_revpay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('payment_status','=','00')
                        ->first();
            
            $grabpay = DB::table('jocom_grabpay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('status','=','success')
                        ->first(); 
            $favepay = DB::table('jocom_favepay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('status','=','successful')
                        ->first(); 
                        
            $pacepay = DB::table('jocom_pacepay_transaction')
                        ->where('transaction_id','=',$transactionid)
                        ->where('status','=','successful')
                        ->first();
                        
            if(count($molpay)>0){
                $paytype="MOLPay";
            }
            else if(count($revpay)>0){
                $paytype="RevPay";
            }
            else if(count($mpay)>0){
                $paytype="mPAY";
            }
            else if(count($paypal)>0){
                $paytype="PayPal";
            }
            else if(count($boost)>0){
                $paytype="Boost";
            }
            else if(count($grabpay)>0){
                $paytype="GrabPay";
            }
            else if(count($favepay)>0){
                $paytype="FavePay";
            }
            else if(count($pacepay)>0){
                $paytype="PacePay";
            }
            else{
                $paytype = "Cash";
            }
        }
        return $paytype;

    }

    public function getXMLpostcode($postcode){

        $status = 0;
        $state   = "";

        $xmldata=simplexml_load_file(Config::get('constants.XML_FILE_PATH').'postcode.xml');

        if(count($xmldata))
        {

            foreach ($xmldata as $value) {
                 if($value->postcode==$postcode){
                    $status = 1;
                    $s_code = $value->state_code;
                    
                    $xmlstate=simplexml_load_file(Config::get('constants.XML_FILE_PATH').'state.xml');

                        foreach ($xmlstate as $val) {

                            $scode = $val->state_code; 
                            if((string)$scode==(string)$s_code){
                                $state = $val->state_name;
                            }
                        }

                        $array  = array('st_code'      => $value->state_code ,
                                         'post_office'  => $value->post_office,
                                         'state'        => $state,
                                         'status'       => $status,
                            );

                         break; 
                 }
            }
        }
        if($status==1)
        {
            return $array;
        }
        else
        {
            return array('st_code'     => '',
                         'post_office' => '',
                         'state'       => '',
                         'status'      => $status,
                        );
        }
        
    }

    public function getStateID($state = ""){

        return DB::table('jocom_country_states')
                        ->where('name','=',$state)
                        ->first();

    }
    
    public function getStateName($stateId) {
        return DB::table('jocom_country_states')
                ->where('id', '=', $stateId)
                ->select('name')
                ->first()->name;
    }

    public function getCityID($city = "",$stateid){
        return DB::table('jocom_cities')
                        ->select('jocom_cities.id','jocom_cities.name') 
                        ->leftJoin('jocom_country_states', 'jocom_cities.state_id', '=', 'jocom_country_states.id')
                        ->where('jocom_cities.name','=',$city)
                        ->where('jocom_cities.state_id','=',$stateid)
                        ->first();

    }
    
    public function getCityName($cityId, $stateId) {
        return DB::table('jocom_cities')
                    ->where('id', '=', $cityId)
                    ->where('state_id', '=', $stateId)
                    ->select('name')
                    ->first()->name;
    }
    
    public static function sumValueOld($startDate,$endDate){
        
    
       // $startDate = date("Y-m-d", strtotime($startDate));
        //$endDate = date("Y-m-d", strtotime($endDate));

        return Transaction::select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    ->where('status','completed')
                    ->where('invoice_no','<>','')
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();
                    //->where('invoice_date','>=',$startDate)
                    //->where('invoice_date','<=',$endDate)
                    
                    
                    
                   

        
    }
    
    public static function sumValue($startDate,$endDate,$regionID = array()){
        // echo $startDate;
        // echo $endDate;
       
        if(count($regionID) > 0){
            
            $searchValue = array();
            foreach ($regionID as $id) {
                
                // take all region 
                if(in_array($id, Config::get('constants.REGION_SPECIAL_CODE'))){
                    
                    $RegionCode = DB::table('jocom_region_refer')
                                ->where('code', $id)
                                ->where('status', 1)
                                ->first();
                  
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('country_id', $RegionCode->country_id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                        
                    
                }else{
                    
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('region_id', $id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                    
                }
            }
            
            $searchValue = array_unique($searchValue);
            
            $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('status', ['completed'])
                    ->whereIn('delivery_state', $searchValue)
                    //->where('status','completed')
                    ->where('invoice_no','<>','')
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();
            
        }else{
           $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                     // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('status', ['completed'])
                    ->where('invoice_no','<>','')
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();
                    
                    // echo "<pre>";
                    // print_r($resultQuery );
                    // echo "</pre>";
                    
                    // echo $startDate;
                    // echo $endDate;
        }
       
        return $resultQuery ;
        
    }
    
    public static function sumValueTwo($startDate,$endDate,$regionID = array()){
        
        if(count($regionID) > 0){
            
            $searchValue = array();
            foreach ($regionID as $id) {
                
                // take all region 
                if(in_array($id, Config::get('constants.REGION_SPECIAL_CODE'))){
                    
                    $RegionCode = DB::table('jocom_region_refer')
                                ->where('code', $id)
                                ->where('status', 1)
                                ->first();
                  
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('country_id', $RegionCode->country_id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                        
                    
                }else{
                    
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('region_id', $id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                    
                }
            }
            
            $searchValue = array_unique($searchValue);
            
            /*
            
            $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
                    END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ) + SUM(JT.gst_delivery), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->whereIn('JT.delivery_state', $searchValue)
                ->where('JT.invoice_no','<>','')
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();          
            
          */
            
            // $resultQuery = DB::table('jocom_transaction AS JT')
            //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
            //     ->select(
            //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
            //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ), 2)
            //         ELSE 
            //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
            //         END AS total_order"), 
            //                         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
            //     		ROUND(SUM(
            //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
            //             ), 2)
            //         ELSE 
            //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
            //         END AS gst_total"))
            //     ->whereIn('JT.status', ['completed'])
            //     ->whereIn('JT.delivery_state', $searchValue)
            //     ->where('JT.invoice_no','<>','')
            //     ->where('JT.transaction_date','>=',$startDate)
            //     ->where('JT.transaction_date','<=',$endDate)
            //     ->first();  
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->whereIn('JT.delivery_state', $searchValue)
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();   
                        
            $resultQuery =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                
            $dataRecord = array(
                    "total_order" => (double)($resultQuery->total_order ),
                    "gst_total" => (double)($resultQuery->gst_total )
                )   ;

      
      
        }else{
            
            /*
            $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
                    END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ) + SUM(JT.gst_delivery), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
                */
            
            $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();  
                        
                        
            // 
            
            $a =  date('2015-01-01 00:00:00');
            $b =  date('2018-11-22 23:59:59');
            
            $a =  $startDate;
            $b =  $endDate;
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
        ->select(
            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
            ELSE 
        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        		ROUND(SUM(
                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                ) , 2)
            ELSE 
        		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
            END AS total_sales"))
        ->whereIn('JT.status', ['completed'])
        ->where('JT.invoice_no','<>','')
        ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
        ->first();
        
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
            ->first();
            
            
             $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                        
                        
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;        
                
            $dataRecord = array(
                    "total_order" => (double)$selectedDateSales->total_order  , //$currentAmount ,
                    "gst_total" => (double)$selectedDateSales->gst_total  //0
                )  ;


        }
        
        return (object)$dataRecord ;
        
    }

    public static function statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,$modify_by = '0'){
        
       
        //print_r(session::all());
        
        if($modify_by === '0'){
             $modify_by = Session::get('username');
        }else{
             $modify_by =  $modify_by;
        }
        
       
        
        //$modify_by == null ? Session::get('username'): $modify_by;
        $modify_date = date('Y-m-d H:i:s');

        if ($type=='Transaction') {

            DB::table('status_history')->insert(array('status'=>$new_status, 'old_status'=>$ori_status, 'modify_by'=>$modify_by, 'modify_date'=>$modify_date, 'trans_id'=>$trans_id, 'type'=>$type));
        }
       
        if ($type=='Batch') {
            switch ($new_status) { 
                case '0':
                    $new_status = 'pending';
                    break;
                case '1':
                    $new_status = 'sending';
                    break;
                case '2':
                    $new_status = 'returned';
                    break;
                case '3':
                    $new_status = 'undelivered';
                    break;
                case '4':
                    $new_status = 'sent';
                    break;
                case '5':
                    $new_status = 'cancelled';
                    break;
                    default:
                        break;
            }
            switch ($ori_status) {
                case '0':
                    $ori_status = 'pending';
                    break;
                case '1':
                    $ori_status = 'sending';
                    break;
                case '2':
                    $ori_status = 'returned';
                    break;
                case '3':
                    $ori_status = 'undelivered';
                    break;
                case '4':
                    $ori_status = 'sent';
                    break;
                case '5':
                    $ori_status = 'cancelled';
                    break;
                default:
                    break;
            }
            DB::table('status_history')->insert(array('status'=>$new_status,'old_status'=>$ori_status, 'modify_by'=>$modify_by, 'modify_date'=>$modify_date, 'trans_id'=>$trans_id, 'batch_id'=>$batch_id, 'type'=>$type));
        }

        if ($type=='after_assign') {

            switch ($ori_status) {
                case 0:
                    $ori_status = 'pending';
                    break;
                case 1:
                    $ori_status = 'undelivered';
                    break;
                case 2:
                    $ori_status = 'partial sent';
                    break;
                case 3:
                    $ori_status = 'returned';
                    break;
                case 4:
                    $ori_status = 'sending';
                    break;
                case 5:
                    $ori_status = 'sent';
                    break;
                case 6:
                   $ori_status = 'cancelled';
                    break;
                default:
                    break;
            }

            DB::table('status_history')->insert(array('status'=>$new_status, 'old_status'=>$ori_status, 'modify_by'=>$modify_by, 'modify_date'=>$modify_date, 'trans_id'=>$trans_id,'logistic_id'=>$logistic_id, 'type'=>'Logistic'));
        }

        if ($type=='batchTrans') {    

            switch ($new_status) {
                case '3':
                    $new_status = 'returned';
                    break;
                case '1':
                    $new_status = 'undelivered';
                    break;
                case '4':
                    $new_status = 'sending';
                    break;
                case '2':
                    $new_status = 'partial send';
                    break;
                case '5':
                    $new_status = 'sent';
                    break;
                case '6':
                    $new_status = 'cancelled';
                    break;
                    default:
                        break;
            }
            switch ($ori_status) {
                case '0':
                    $ori_status = 'pending';
                    break;
                case '1':
                    $ori_status = 'undelivered';
                    break;
                case '2':
                    $ori_status = 'partial send';
                    break;
                case '3':
                    $ori_status = 'returned';
                    break;
                case '4':
                    $ori_status = 'sending';
                    break;
                case '5':
                    $ori_status = 'sent';
                    break;
                case '6':
                    $ori_status = 'cancelled';
                    break;
                default:
                    break;
            }

            DB::table('status_history')->insert(array('status'=>$new_status,'old_status'=>$ori_status, 'modify_by'=>$modify_by, 'modify_date'=>$modify_date, 'trans_id'=>$trans_id, 'logistic_id'=>$logistic_id, 'type'=>'Logistic'));
        }

        if ($type=='Logistic') {

            switch ($new_status) {
                case 0:
                    $new_status = 'pending';                       
                    break;
                case 1:
                    $new_status = 'undelivered';   
                    break;
                case 2:
                    $new_status = 'partial sent';
                    break;
                case 3:
                    $new_status = 'returned';
                    break;
                case 4:
                    $new_status = 'sending';
                    break;
                case 5:
                    $new_status = 'sent';
                    break;
                case 6:
                    $new_status = 'cancelled';
                    break;
                default:
                    break;
            }
            switch ($ori_status) {
                case 0:
                    $ori_status = 'pending';
                    break;
                case 1:
                    $ori_status = 'undelivered';
                    break;
                case 2:
                    $ori_status = 'partial sent';
                    break;
                case 3:
                    $ori_status = 'returned';
                    break;
                case 4:
                    $ori_status = 'sending';
                    break;
                case 5:
                    $ori_status = 'sent';
                    break;
                case 6:
                   $ori_status = 'cancelled';
                    break;
                default:
                    break;
            }

           DB::table('status_history')->insert(array('status'=>$new_status,'old_status'=>$ori_status,'modify_by'=>$modify_by, 'modify_date'=>$modify_date,'trans_id'=>$trans_id, 'logistic_id'=>$logistic_id, 'type'=>$type));
        }
        

    }

    public static function Elevenstreettransaction($orderno){
        $data = array();
        $result = DB::table('jocom_elevenstreet_order AS EO')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    ->select('EO.transaction_id','EO.created_at')
                    ->where('EO.order_number','=',$orderno)
                    ->where('JP.status','=','completed')
                    ->where('EO.activation','=',1)
                    ->get();

        if(count($result)>0){
            foreach ($result as  $value) {
                 $transarray = DB::table('jocom_transaction')
                                 ->where('id','=',$value->transaction_id)
                                 ->first();

                if(count($transarray)>0){

                    $temparray = array('transdate' =>  $transarray->transaction_date, //date("m/d/y",strtotime($transarray->transaction_date)),
                                       'transID'   =>  $transarray->id,
                                       'invNo'    =>  $transarray->invoice_no,
                                       'invDate'    =>  $transarray->invoice_date,//date("m/d/y",strtotime($transarray->invoice_date)),
                                       'buyerUser'    =>  $transarray->buyer_username,
                                       'status'    =>  $transarray->status,
                                       'gst'       =>  $transarray->gst_total,
                                       'totalamount'    =>  $transarray->total_amount,
                                       'created_at'    =>  $value->created_at,
                                        );
                    array_push($data, $temparray);
                }

            }
        }

        return $data;
    }
    
    public static function ElevenstreettransactionTransID($transID){
        $data = array();
        $result = DB::table('jocom_elevenstreet_order AS EO')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    ->select('EO.transaction_id','EO.created_at')
                    ->where('JP.id','=',$transID)
                    ->where('JP.status','=','completed')
                    ->where('activation','=',1)
                    ->first();

        if(count($result)>0){
            // foreach ($result as  $value) {
                 $transarray = DB::table('jocom_transaction')
                                 ->where('id','=',$result->transaction_id)
                                 ->first();

                if(count($transarray)>0){

                    $temparray = array('transdate' =>  $transarray->transaction_date ,//date("m/d/y",strtotime($transarray->transaction_date)),
                                       'transID'   =>  $transarray->id,
                                       'invNo'    =>  $transarray->invoice_no,
                                       'invDate'    => $transarray->invoice_date, //date("m/d/y",strtotime($transarray->invoice_date)),
                                       'buyerUser'    =>  $transarray->buyer_username,
                                       'status'    =>  $transarray->status,
                                       'gst'       =>  $transarray->gst_total,
                                       'totalamount'    =>  $transarray->total_amount,
                                       'created_at'    =>  $result->created_at,
                                        );
                    array_push($data, $temparray);
                }

            // }
        }

        return $data;
    }

    public static function Get11streettransaction($orderno){
        $flag = 0;
        $result = DB::table('jocom_elevenstreet_order AS EO')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    ->select('EO.transaction_id')
                    ->where('EO.order_number','=',$orderno)
                    ->where('JP.status','=','completed')
                    ->where('EO.activation','=',1)
                    ->first();

         DB::table('jocom_elevenstreet_order')
            ->where('order_number', $orderno)
            ->update(['api_payment_status' => 1]);       

        //  if(count($result)>0){

        //     $trans_id = $result->transaction_id;
        //     $transdetails = DB::table('jocom_transaction_details')
        //                         ->where('transaction_id','=',$trans_id)
        //                         ->get();
        //      $flag = 1;                   
        //  }   
        //  if($flag == 1){
        //     return $transdetails;
        //  }
                  

    }
    
    public static function Checklivestreamexists($transactionid){

        $codeexists = 0;
        $Transaction = Transaction::find($transactionid);  

        $username = $Transaction->buyer_username;
        $buycontact_no = $Transaction->delivery_contact_no;
        $buyeremail  = $Transaction->buyer_email;  
        $buyeraddr  = $Transaction->delivery_addr_1;  

        // echo $transactionid;

        $transactionall = DB::table('jocom_transaction')
                                 ->where('buyer_username','=',$username)
                                 ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons']) 
                                 ->where('status','=','completed')  
                                 ->get();
        // print_r($transactionall);

        if(count($transactionall)>0){

            foreach ($transactionall as $value) {
               $transid = $value->id;

               $coupondata = TCoupon::whereIn("coupon_code",['JOMSMO30'])  
                                    ->where('transaction_id','=',$transid)
                                    ->get();
                // print_r($coupondata);
               if(count($coupondata)>0){
                $codeexists = 1;
                break;
               } 
            }

            if($codeexists == 0){
                $transactionall_01 = DB::table('jocom_transaction')
                                 ->where('delivery_contact_no','=',$buycontact_no)
                                 ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons'])   
                                 ->where('status','=','completed')  
                                 ->get();
                if(count($transactionall_01)>0){

                    foreach ($transactionall_01 as $value_01) {
                      $transid_01 = $value_01->id;

                      $coupondata_01 = TCoupon::whereIn("coupon_code",['JOMSMO30'])  
                                            ->where('transaction_id','=',$transid_01)
                                            ->get();
                      if(count($coupondata_01)>0){
                        $codeexists = 1;
                        break;
                      } 
                    }

                }
            }

            if($codeexists == 0){
                $transactionall_02 = DB::table('jocom_transaction')
                                 ->where('buyer_email','=',$buyeremail)
                                 ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons'])   
                                 ->where('status','=','completed')  
                                 ->get();
                if(count($transactionall_02)>0){

                    foreach ($transactionall_02 as $value_02) {
                       $transid_02 = $value_02->id;

                       $coupondata_02 = TCoupon::whereIn("coupon_code",['JOMSMO30'])  
                                            ->where('transaction_id','=',$transid_02)
                                            ->get();
                       if(count($coupondata_02)>0){
                        $codeexists = 1;
                        break;
                       } 
                    }

                }
            }
            
            if($codeexists == 0){
                $transactionall_03 = DB::table('jocom_transaction')
                                 ->where('delivery_addr_1','=',$buyeraddr)
                                 ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons'])   
                                 ->where('status','=','completed')  
                                 ->get();
                if(count($transactionall_03)>0){

                    foreach ($transactionall_03 as $value_02) {
                       $transid_03 = $value_02->id;

                       $coupondata_03 = TCoupon::whereIn("coupon_code",['JOMSMO30'])  
                                            ->where('transaction_id','=',$transid_03)
                                            ->get();
                       if(count($coupondata_03)>0){
                        $codeexists = 1;
                        break;
                       } 
                    }

                }
            }



        }    
        

        return $codeexists;

    }
    
    public static function Checkpublicbinexists($coupon,$bin){

        $codeexists = 0;

        $coupondata = Coupon::where("coupon_code",$coupon)
                             ->whereIn('is_bank_code',[1,2,3,4,5])
                             ->get();

        if(count($coupondata)>0){
            
            $bindata = CouponPublicBin::where('bin_number','=',$bin)->get();

            if(count($bindata)>0){
                $codeexists = 0;
            }
            else {
                $codeexists = 1;

            }

        }
        else {

            $codeexists = 1;
        }

         return $codeexists;

    }
    
    public static function Checkpublicbinexists1($coupon,$bin){

        $codeexists = 0;
        // $array = array('1','2');
        $coupondata = Coupon::where("coupon_code",$coupon)
                             ->where('is_bank_code','=',1)
                             ->get();
        //   print_r($coupon);
        if(count($coupondata)>0){
            // echo 'In';
            
            $bindata = CouponPublicBin::where('bin_number','=',$bin)->get();

            if(count($bindata)>0){
                $codeexists = 0;
            }
            else {
                $codeexists = 1;

            }

        }
        
        else {
            $coupondata1 = Coupon::where("coupon_code",$coupon)
                             ->where('is_bank_code','=',2)
                             ->get();
                //   print_r($coupondata1);      
              if(count($coupondata1)>0){
                  $bindata = CouponPublicBin::where('bin_number','=',$bin)->get();

                    if(count($bindata)>0){
                        $codeexists = 0;
                    }
                    else {
                        $codeexists = 1;
        
                    }
                  
              }
              else{
            
            
            $codeexists = 1;
              }
        }

         return $codeexists;

    }
    
    public static function Checkproductexists($transactionid){
        $codeexists = 0;

        $result = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000029886'.'JC-0000000029885','JC-0000000029757','JC-0000000029637','JC-0000000029636','JC-0000000029635'])  
                    ->where('JP.id','=',$transactionid)
                    ->get();

        if(count($result)>0){
            $codeexists = 1;
        }
        

        return $codeexists;

    }
    
    public static function Checkboostproductexists($transactionid,$couponcode){
        $codeexists = 0;

        $result = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000031266'])  
                    ->where('JP.id','=',$transactionid)
                    ->get();

        if(count($result)>0){
            $codeexists = 1;
            $resultcoupon = DB::table('jocom_coupon')
                                ->where('name','=','Boost 12 days to Christmas -RM100 voucher')
                                ->where('coupon_code',$couponcode)
                                ->get();
             if(count($resultcoupon)>0){
                $codeexists = 0; 
             }                   
                                
        }
        

        return $codeexists;

    }
    
    public static function Checkcouponexists($coupon){
        $codeexists = 0;
        $couponname = 'Boost 12 days to Christmas -RM100 voucher';

        $result = DB::table('jocom_coupon AS JC')
                    ->select('JC.coupon_code')
                    ->where('JC.coupon_code','=',$coupon)
                    ->where('JC.name','=',$couponname)
                    ->get();

        if(count($result)>0){
            $codeexists = 1;
        }
        

        return $codeexists;

    }
    
    
    
    public static function Checkjpoints($transactionid){
        // $codeexists = 0;
        // $status='completed';

        // $result = DB::table('jocom_transaction_details AS JTD')
        //             ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
        //             ->select('JTD.transaction_id')
        //             ->whereIn("JTD.sku",['JC-0000000030237','JC-0000000030238','JC-0000000030239','JC-0000000030241','JC-0000000030242','JC-0000000030243','JC-0000000030244','JC-0000000030245','JC-0000000030246','JC-0000000030247','JC-0000000030248'])  
        //             ->where('JP.id','=',$transactionid)
        //             ->where('JP.status','=',$status)
        //             ->get();

        // if(count($result)>1){
        //     $codeexists = 1;
        // }
        $codeexists = 0;
        $buyername = '';
        $status='completed';
       
        $trans = DB::table('jocom_transaction')->where('id','=',$transactionid)->first();
        
        if(count($trans)>0){
            $buyername = $trans->buyer_username;
            
            $transdetails = DB::table('jocom_transaction_details AS JTD')
                                ->where('JTD.transaction_id','=',$transactionid)
                                ->get();
                                
            if(count($transdetails) > 0){
                foreach($transdetails as $value){
                    $productid = $value->product_id;
                    
                    $result = DB::table('jocom_transaction_details AS JTD')
                        ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                        ->select('*')
                        ->whereIn('JTD.sku',['JC-0000000030237','JC-0000000030238','JC-0000000030239','JC-0000000030241','JC-0000000030242','JC-0000000030243','JC-0000000030244','JC-0000000030245','JC-0000000030246','JC-0000000030247','JC-0000000030248'])  
                        ->where('JP.buyer_username','=',$buyername)
                        ->where('JP.status','=',$status)
                        ->where('JTD.product_id','=',$productid)
                        ->get();
                        
                    if(count($result)>0){
                        $codeexists = 1;
                        
                    }
        
                }
            }
            
        }

        return $codeexists;

    }
    
    public static function Checkjpointsnew($transactionid){
        $codeexists = 0;
        $buyername = '';
        $status='completed';
        
        print_r($transactionid);
        echo '<br>';
        
        try {
        
        $trans = DB::table('jocom_transaction')->where('id','=',$transactionid)->first();
        
        if(count($trans)>0){
            $buyername = $trans->buyer_username;
            
            $transdetails = DB::table('jocom_transaction_details AS JTD')
                                ->where('JTD.transaction_id','=',$transactionid)
                                ->get();
                                
            if(count($transdetails) > 0){
                foreach($transdetails as $value){
                    $productid = $value->product_id;
                    print_r($productid);
                    echo '<br>';
                    $result = DB::table('jocom_transaction_details AS JTD1')
                        ->leftjoin('jocom_transaction as JP','JP.id','=','JTD1.transaction_id')
                        ->select('JTD1.sku')
                        ->whereIn('JTD1.sku',['JC-0000000030237','JC-0000000030238','JC-0000000030239','JC-0000000030241','JC-0000000030242','JC-0000000030243','JC-0000000030244','JC-0000000030245','JC-0000000030246','JC-0000000030247','JC-0000000030248'])  
                        ->where('JP.buyer_username','=',$buyername)
                        ->where('JP.status','=',$status)
                        ->where('JTD1.product_id','=',$productid)
                        ->get();
                        
                    if(count($result)>0){
                        $codeexists = 1;
                        foreach ($result as  $value1) {
                            $existingSKU[] =  $value1->sku;  
                        }
                        
                    }
        
                }
            }
            print_r($existingSKU);
        }
        
        }catch (Exception $ex) {
            $isError = 1;
           $errorMessage = $ex->getMessage();
           echo $errorMessage;
     	}finally {

     	}
        

        return $codeexists;

    }
    
    public static function Checkproductpoints($transactionid){
        $splskupoints = 0;
        $status='completed';

        $result = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000029635111','JC-0000000029637111','JC-0000000029636111','JC-0000000029757111','JC-0000000043356'])  
                    ->where('JP.id','=',$transactionid)
                    ->where('JP.status','=',$status)
                    ->get();

        if(count($result)>0){
            $codeexists = 1;
        }
        
        
        

        return $splskupoints;

    }
    
    public static function Checkjcmeleven11($transactionid){
       
        $codeexists = 0;
        $buyername = '';
        $status='completed';
       
        $trans = DB::table('jocom_transaction')->where('id','=',$transactionid)->first();
        
        if(count($trans)>0){
            $buyername = $trans->buyer_username;
            
            $transdetails = DB::table('jocom_transaction_details AS JTD')
                                ->where('JTD.transaction_id','=',$transactionid)
                                ->get();
                                
            if(count($transdetails) > 0){
                foreach($transdetails as $value){
                    $productid = $value->product_id;
                    $result = DB::table('jocom_jeleven11 AS JE')
                                
                        ->select('*')
                        ->where('JE.product_id','=',$productid)
                        ->get();
                        
                    if(count($result)>0){
                        // $result_01 = DB::table('jocom_transaction_coupon AS a')
                        //     ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                        //     // ->where('b.buyer_username','=',$buyername)
                        //       ->where('a.transaction_id','=',$transactionid)
                        //     // ->where('a.coupon_code','=','JCMMES10')
                        //     ->first();
                        // echo '3';
                        // if(count($result_01)>0){
                            
                        //     if(strtoupper(trim($result_01->coupon_code)) =='SHARON50'){
                        //       $codeexists = 2;     
                        //     }
                        //      else {
                        //      $codeexists = 1;   
                        //     }
                        // }
                       
                        
                        $codeexists = 1; 
                        
                        
                        
                    }
        
                }
            }
            
        }

        return $codeexists;

    }
    
    
    public static function Checkminspendvalue($qrcode){
       
        $codeexists = 0;
        
        
        $result = DB::table('jocom_minspendvalue AS JE')
                        ->select('*')
                        ->where('JE.product_id','=',$qrcode)
                        ->first();
        if(count($result) > 0)
        {
            
            $codeexists = 1; 
        }
       
       

        return $codeexists;

    }
    
    public static function Checkminspendvaluetotal($qrcode){
        $qty = 0;
        
        $result = DB::table('jocom_minspendvalue AS JE')
                        ->select('*')
                        ->where('JE.product_id','=',$qrcode)
                        ->first();
        if(count($result) > 0)
        {
            
            $qty = (int)$result->minvalue;
        }
        
        return $qty;
    }
    
    public static function Checkcoupon($coupon){
        $codeexists = 2;
        
        $result_01 = DB::table('jocom_coupon AS a')
                          ->where('a.coupon_code','=',$coupon)
                         ->where('a.name','=','MCM RE GIC Code')
                         ->get();

        if(count($result_01)>0){
            $codeexists = 1;
        }

        return $codeexists;
        
        
    }
    
    public static function Checkbinfiniteexists($transactionid){
        $codeexists = 0;

        $result = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000037205','JC-0000000030722'])  
                    ->where('JP.id','=',$transactionid)
                    ->get();

        if(count($result)>0){
            $codeexists = 1;
        }

        return $codeexists;

    }
    
    public static function Checkproductrestrict($qrcode){
        $qty = 0;
        
        $result = DB::table('jocom_productrestrict AS JE')
                        ->select('*')
                        ->where('JE.product_id','=',$qrcode)
                        ->first();
        if(count($result) > 0)
        {
            
            $qty = (int)$result->qty;
        }
        
        return $qty;
    }
    
    public static function Checkpurchasevalidity($transactionid,$coupon){
        $codeexists = 0;

        $Transaction = Transaction::find($transactionid);  
        $username = $Transaction->buyer_username;


        $result = DB::table('jocom_transaction')
                        ->where('buyer_username','=',$username)
                        ->where('status','=','completed')
                        ->get();
        if(count($result) > 0)
        {
            $result_01 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_username','=',$username)
                            ->where('a.coupon_code','=',$coupon)
                            ->get();
            if(count($result_01)>0){
                $codeexists = 1;  
            }
            else
            {
                $codeexists = 0;  
            }
          
        }

        return $codeexists;

    }
    
    public static function Checkuserexists($transactionid){
        $userexists = 0; 

        $Transaction = Transaction::find($transactionid);  
        $username = $Transaction->buyer_username;

        $ExistsUser = DB::table('jocom_transaction')
                        ->where('buyer_username','=',$username)
                        ->where('status','=','completed')
                        ->get();

        if(count($ExistsUser) > 0)
        {

          $userexists = 1;  
        }

        return $userexists;                

    }
    
    public static function Checkuserpurchase($userid,$qrcode){
        $userexists = 0; 
        // $qrcode = 'JC48136';
        // $userid = 26284;
        // echo $userid.'-'.$qrcode;
        $Product = Product::where("qrcode",$qrcode)->first();
        $product_id = $Product->id;
        
        
        $result_01 = DB::table('jocom_transaction_details AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_id','=',$userid)
                            // ->where('a.product_id','=',$product_id)
                            ->whereIn('a.product_id',[48700,43991])  
                            ->where('b.status','=','completed')
                            ->get();
        
        if(count($result_01) > 0)
        {
          $userexists = 1;  
        }
        
        
    }
    
    public static function Checkuserblock($transactionid){
        $userexists = 0; 

        $Transaction = Transaction::find($transactionid);  
        $username = $Transaction->buyer_username;
        $delivery_contactno = $Transaction->delivery_contact_no;
        
        $ExistsUser = DB::table('jocom_transaction')
                ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                ->where('status','=','completed')
                ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons']) 
                ->get();
                

        // $ExistsUser = DB::table('jocom_transaction')
        //                 ->where('buyer_username','=',$username)
        //                 ->where('status','=','completed')
        //                 ->get();

        if(count($ExistsUser) > 0)
        {

          $userexists = 1;  
        }
        
        $ExistsUser1 = DB::table('jocom_transaction')
                ->where('delivery_addr_1', 'like', '%Batu 29%')
                ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                ->where('status','=','completed')
                ->get();
        
         if(count($ExistsUser1) > 0)
        {

          $userexists = 1;  
        }
        
        $ExistsUser2 = DB::table('jocom_transaction')
                ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                ->where('status','=','completed')
                ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons']) 
                ->get();
        
         if(count($ExistsUser2) > 4)
        {

          $userexists = 1;  
        }
        

        return $userexists;                

    }
    
    
    public static function Checkuserblockmobile($transactionid){
        
            $userexists = 0; 
            
            $Transaction = Transaction::find($transactionid);
            $delivery_contactno = trim($Transaction->delivery_contact_no);
            $delivery_contactno_1 = trim(substr($delivery_contactno,3));
           
            $result_02 = DB::table('jocom_restrict_mobile AS a')
                           
                            ->where('a.mobile', 'like', '%'.$delivery_contactno.'%')
                            ->get();
           

            if(count($result_02) > 0)
            {
                $userexists = 1; 
                    
            }
            // echo $delivery_contactno_1;
            $result_03 = DB::table('jocom_restrict_mobile AS a')
                           
                            ->where('a.mobile', 'like', '%'.$delivery_contactno_1.'%')
                            ->get();
        //   print($result_03);

            if(count($result_03) > 0)
            {
                $userexists = 1; 
                // echo 's';        
            }
            
            if($delivery_contactno =='0163422831'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='016-3422831'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='163422831'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='0122515745'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='0129766701'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='0178802841'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='0166184818'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='+60122829384'){
                $userexists = 1;
            }
            
            if($delivery_contactno =='0162555857'){
                $userexists = 1;
            }
            
             if($delivery_contactno =='0163959269'){
                $userexists = 1;
            }
            
            
             return $userexists;      
    }
    
    public static function Checkuserblockemail($transactionid){
        
            $userexists = 0; 
            
            $Transaction = Transaction::find($transactionid);
            $email = trim($Transaction->buyer_email);
            
            $result_02 = DB::table('jocom_restrict_email AS a')
                           
                            ->where('a.email', 'like', '%'.$email.'%')
                            ->get();

            if(count($result_02) > 0)
            {
                $userexists = 1; 
                    
            }
            
            if($email=='prince_csy113@hotmail.com'){
                 $userexists = 1; 
            }
            
            
             return $userexists;      
    }
    
    public static function Checkuserblockaddress($transactionid){
        
            $userexists = 0; 
            
            $Transaction = Transaction::find($transactionid);
            $addr = trim($Transaction->delivery_addr_1);
            
            $result_02 = DB::table('jocom_restrict_address AS a')
                           
                            ->where('a.address', 'like', '%'.$addr.'%')
                            ->get();

            if(count($result_02) > 0)
            {
                $userexists = 1; 
                    
            }
            
            if($addr=='No 1, Jalan Ahliman 3'){
                $userexists = 1;
            }
            
            
             return $userexists;      
    }
    
    public static function Checkuserblockcontact($transactionid){
        $userexists = 0; 

        $Transaction = Transaction::find($transactionid);  
        $username = trim($Transaction->buyer_username);
        $delivery_contactno = trim($Transaction->delivery_contact_no);
        $email = trim($Transaction->buyer_email);
        $addr = trim($Transaction->delivery_addr_1);
        $delname = trim($Transaction->delivery_name);
        
        // $ExistsUser = DB::table('jocom_transaction')
        //         ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
        //         ->where('status','=','completed')
        //         ->get();
                
        $result_01 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_username','=',$username)
                            // ->where('a.coupon_code','=','JCM16OFF')
                            ->whereIn('a.coupon_code',['JCM30OFFTEST','JCM30OFF'])  
                            ->where('b.status','=','completed')
                            ->get();
           
        if(count($result_01) > 0)
        {
            $userexists = 1; 
                
            
          
        }
        
        if(substr(strtoupper($username),0,4) == 'WLAN'){
           $userexists = 1; 
        }
        
        if(substr(strtoupper($username),1,4) == 'LANG'){
           $userexists = 1; 
        }
        
        if(substr(strtoupper($username),0,4) == 'DESD'){
             $userexists = 1; 
        }
        
        if(substr(strtoupper($username),0,4) == 'SUEY'){
           $userexists = 1;  
        }
        
        if(substr(strtoupper($username),0,4) == 'ELEN'){
           $userexists = 1;  
        }
        
        if(substr(strtoupper($username),0,5) == 'LOON'){
          $userexists = 1;  
        }
        
        
        if(substr(strtoupper($username),0,5) == 'WEILO'){
          $userexists = 1; 
        }
        
        if(substr(strtoupper($username),0,5) == 'ELENG'){
             $userexists = 1; 
        }
        
        if(strtoupper($delname) == 'ANG WEI LONG'){
            $userexists = 1;
        }
        
        if(strtoupper($addr) == 'LOT 682,'){
            $userexists = 1;
        }
        
        if(strtoupper($addr) == 'LOT 682'){
            $userexists = 1;
        }
        
        if(strtoupper($addr) == 'LOT 682, JALAN'){
            $userexists = 1;
        }
        
        
        
        if(strtoupper($addr) == 'LOT 682, JALAN TELOK'){
            $userexists = 1;
        }
        
        if(strtoupper($delname) == 'CHENG SAN YUN'){
            $userexists = 1;
        }
        
        if(trim($delivery_contactno) == '0163422831'){
            $userexists = 1;
        }
        
        if(trim($delivery_contactno) == '0166731368'){
            $userexists = 1;
        }
        
        if(substr(strtoupper($username),0,4) == 'CYAN'){
             $userexists = 1; 
        }
        
        
        
        
        
        $result_02 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','pgmall','tiktokshop','Lamboplace']) 
                            // ->where('a.coupon_code','=','JCM16OFF')
                            ->whereIn('a.coupon_code',['JCM30OFFTEST','JCM30OFF'])  
                            ->where('b.status','=','completed')
                            ->get();
           

        if(count($result_02) > 0)
        {
            $userexists = 1; 
                
            
          
        }
        
        $result_03 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_email', 'like', '%'.$email.'%')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','pgmall','tiktokshop','Lamboplace'])
                            // ->where('a.coupon_code','=','JCM30OFF')
                            ->whereIn('a.coupon_code',['JCM30OFFTEST','JCM30OFF'])  
                            ->where('b.status','=','completed')
                            ->get();
           

        if(count($result_03) > 0)
        {
            $userexists = 1; 
                

          
        }
        
        if($delivery_contactno == '0163422831') {
        
        $result_04 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.delivery_contact_no', 'like', '%0163422831%')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','pgmall','tiktokshop','Lamboplace']) 
                            // ->where('a.coupon_code','=','JCM16OFF')
                            ->whereIn('a.coupon_code',['JCM30OFFTEST','JCM30OFF'])  
                            ->where('b.status','=','completed')
                            ->get();
           

        if(count($result_04) > 0)
        {
            $userexists = 1; 
                
        
          
        }
        }
        
        $result_05 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.delivery_addr_1', 'like', '%'.$addr.'%')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','pgmall','tiktokshop','Lamboplace']) 
                            // ->where('a.coupon_code','=','JCM16OFF')
                            ->whereIn('a.coupon_code',['JCM30OFFTEST','JCM30OFF'])  
                            ->where('b.status','=','completed')
                            ->get();
           

        if(count($result_05) > 0)
        {
            $userexists = 1; 
                
            // echo 'yes';
          
        }
        

        return $userexists;                

    }
    
    public static function Checkvoucher($transactionid){
        $userexists = 0; 

        $Transaction = Transaction::find($transactionid);  
        $username = $Transaction->buyer_username;
        $delivery_contactno = $Transaction->delivery_contact_no;
       
       if(isset($username) && $username !=''){
           
           $result_01 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_username','=',$username)
                            // ->whereIn('a.coupon_code','=','DEFCON2022')
                            ->whereIn('a.coupon_code',['DEFCON2022','SUPER77','SUPER712'])  
                            // ->where('b.status','=','completed')
                            ->get();
           
        
            if(count($result_01) > 0)
            {
                $userexists = 1; 
                    
        
              
            }
       }
                
        
        
        

        return $userexists;                

    }
    
    public static function Checkuserblockcontactmoretimes($transactionid,$coupon){
        $userexists = 0; 

        $Transaction = Transaction::find($transactionid);  
        $username = $Transaction->buyer_username;
        $delivery_contactno = $Transaction->delivery_contact_no;
        $delivery_addr = $Transaction->delivery_addr_1;
        $special_msg = $Transaction->special_msg;
        
        $result_01 = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.buyer_username','=',$username)
                            ->where('a.coupon_code','=',$coupon)
                            ->where('b.status','=','completed')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons']) 
                            ->get();
                            
        if(count($result_01) > 3)
        {

          $userexists = 1;  
        }
        
        $ExistsUser = DB::table('jocom_transaction_coupon AS a')
                            ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
                            ->where('b.delivery_contact_no','=',$delivery_contactno)
                            ->where('a.coupon_code','=',$coupon)
                            ->where('b.status','=','completed')
                            ->whereNotIn('b.buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street','vettons']) 
                            ->get();
        // $ExistsUser = DB::table('jocom_transaction')
        //         ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
        //         ->where('status','=','completed')
        //         ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','Qoo10','11Street']) 
        //         ->get();
         
          if(count($ExistsUser) > 3)
            {
    
              $userexists = 1;  
            }
        
        if($delivery_addr == 'Batu 29.5' || $delivery_addr == 'batu 29 1/2' || $delivery_addr == 'BATU 29 1/2')  
        {
        
        $ExistsUser1 = DB::table('jocom_transaction')
                ->where('delivery_addr_1', 'like', '%Batu 29%')
                // ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                ->where('status','=','completed')
                ->get();
        
         if(count($ExistsUser1) > 0)
            {
    
              $userexists = 1;  
            }
            
        }    
        
        if($delivery_contactno == '102462468' || $delivery_contactno == '0102462468')  
        {
        
        $ExistsUser2 = DB::table('jocom_transaction')
                // ->where('delivery_addr_1', 'like', '%Batu 29%')
                ->where('delivery_contact_no', 'like', '%102462468%')
                // ->where('status','=','completed')
                ->get();
        
         if(count($ExistsUser2) > 0)
            {
    
              $userexists = 1;  
            }
        }
        
        // $ExistsUser4 = DB::table('jocom_transaction')
        //         ->where('delivery_addr_1', 'like', '%jalan perdana 7/1%')
        //         ->where('status','=','completed')
        //         ->get();
        
        //  if(count($ExistsUser4) > 0)
        //     {
    
        //       $userexists = 1;  
        //     }
        
        if (strpos($delivery_addr, 'jalan perdana 7/1') !== false) {
            $userexists = 1;  
        }
        
        
        
        if (strpos($special_msg, '19719584') !== false) {
            $userexists = 1;  
        }
        
        if($delivery_addr == 'JALAN TERATAI 2/7' || $delivery_addr == 'Jalan Teratai 2/7' || $delivery_addr == '16 taman cahaya') {
            $userexists = 1;  
        }
        
        
        $ExistsUser1 = DB::table('jocom_transaction')
                ->where('delivery_addr_1', '=', $delivery_addr)
                // ->where('delivery_contact_no', 'like', '%'.$delivery_contactno.'%')
                ->where('status','=','completed')
                ->get();
        
         if(count($ExistsUser1) > 0)
            {
    
              $userexists = 1;  
            }
            
        
        
        
         

        return $userexists;                

    }
    
    public static function Blackfriday($trans_id){
         $codeexists = 0;
            DB::table('jocom_minspendvalue')
    		->insert(array('product_id' => $trans_id, 'minvalue' => 10, 'status' => 1));
    		
             $sum_price    = TDetails::where('transaction_id', '=', $trans_id)->sum('total');
                  $sum_purchase = number_format($sum_price, 2);
                 if($sum_purchase>=70 && $sum_purchase<=149){
                     DB::table('jocom_minspendvalue')
    		            ->insert(array('product_id' => '11112', 'minvalue' => 10, 'status' => 1));
                    //   $temp_xml = MCheckout::insert_coupon_code($trans_id,'BLACKFRIDAY5');
                  $result = MCheckout::Couponcodetempweb($trans_id,'BLACKFRIDAY5');
                }else if($sum_purchase>=150){
                    //  $lscheck = MCheckout::Couponcodetemp($trans_id,'BLACKFRIDAY10');
                }
        return $result;
    }
    
    public static function Checkwondaproductexists($transactionid,$couponcode){
        $codeexists = 0;

        

        $couponname = 'Brand Sales Executive Wonda 3-in-1';
        
        $couponname_1 = 'Jocom x Amin Hayat Live #JCMGIVEAWAY(29-09-2021)';
        
        $couponname_2 = 'Jocom x Amin Hayat Live #JCMGIVEAWAY(14-10-2021)';

        $result = DB::table('jocom_coupon AS JC')
                    ->select('JC.coupon_code')
                    ->where('JC.coupon_code','=',$couponcode)
                    ->where('JC.name','=',$couponname)
                    ->first();
       
        if(count($result)>0){
            $codeexists = 1;
        }
        
        $result_1 = DB::table('jocom_coupon AS JC')
                    ->select('JC.coupon_code')
                    ->where('JC.coupon_code','=',$couponcode)
                    ->where('JC.name','=',$couponname_1)
                    ->first();
       
        if(count($result_1)>0){
            $codeexists = 1;
        }
        
        $result_2 = DB::table('jocom_coupon AS JC')
                    ->select('JC.coupon_code')
                    ->where('JC.coupon_code','=',$couponcode)
                    ->where('JC.name','=',$couponname_2)
                    ->first();
       
        if(count($result_2)>0){
            $codeexists = 1;
        }



        if($codeexists == 1){

            // $result_01 = DB::table('jocom_transaction_coupon AS a')
            //                 ->leftJoin('jocom_transaction AS b', 'a.transaction_id', '=', 'b.id')
            //                 ->where('b.id','=',$transactionid)
            //                 ->where('a.coupon_code','=',$couponcode)
            //                 ->get();
         $result_01 = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000024329','JC-0000000024328','JC-0000000024327','JC-0000000039831','JC-0000000040505'])  
                    ->where('JP.id','=',$transactionid)
                    ->get();

            if(count($result_01)>0){
                $transdetails = DB::table('jocom_transaction_details AS JTD')
                                ->where('JTD.transaction_id','=',$transactionid)
                                ->get();

                if(count($transdetails) > 1)
                {
                    $codeexists = 7;                
                }
                else{
                     $codeexists = 0;  
                }
           
            }
            else
            {
                $codeexists = 0;  
            }



            
        }
        




        return $codeexists;

    }
    
    
    
    public static function verorder(){

        $array = array(199176,
199177,
199178,
199179,
199180,
199181,
199182,
199183,
199184,
199185,
199186,
199187,
199188,
199079,
199080,
199081,
198778,
198779,
198780,
198781,
198782,
198783,
198784,
198785,
198786,
198787,
198788,
198789,
198790,
198791,
198792,
198793,
198794,
198795,
198796,
198797,
198798,
198660,
198661,
198662,
198663,
198664,
198665
);
        die();

        foreach ($array as $key => $value) {
                $tdet = DB::table('jocom_transaction_details')->where('transaction_id','=',$value)->first();
                           
                                        $TDetails = new TDetails;
                                        $TDetails->product_id = 28348;
                                        $TDetails->product_name = 'Pokka Houjicha 1.5L';
                                        $TDetails->sku = 'JC-0000000028348';
                                        $TDetails->price_label = '1.5L';
                                        $TDetails->price = 0.00;
                                        $TDetails->foreign_price = 0.00;
                                        $TDetails->p_referral_fees = 0.00;
                                        $TDetails->p_referral_fees_type = '%';
                                        $TDetails->unit = 1.000;
                                        $TDetails->delivery_fees = 0.00;
                                        $TDetails->delivery_time = '3-7 business days';
                                        $TDetails->seller_id = 159;
                                        $TDetails->seller_username = 'F&N';
                                        $TDetails->disc = 0.00;
                                        $TDetails->gst_rate_item = 0.00;
                                        $TDetails->gst_amount = 0.00;
                                        $TDetails->transaction_id = $value;
                                        $TDetails->p_option_id = 36839;
                                        $TDetails->parent_seller = 69;
                                        $TDetails->po_no = $tdet->po_no;
                                        $TDetails->parent_po = $tdet->parent_po;
                                        $TDetails->zone_id = 3;
                                        $TDetails->total_weight = 1500;
                                        $TDetails->original_price = 0.00;
                                        $TDetails->ori_price = 0.00;
                                        $TDetails->gst_ori = 0.00;
                                        $TDetails->actual_price = 0.00;
                                        $TDetails->actual_total_amount = 0.00;
                                        $TDetails->actual_price_gst_amount = 0.00;
                                        $TDetails->foreign_price= 0.00000;
                                        $TDetails->cost_unit_amount=0.00;
                                        $TDetails->cost_amount=0.00;
                                        $TDetails->category_1 = '789';
                                        $TDetails->category_2 = '790';
                                        $TDetails->category_3 = '789,790';
                                        $TDetails->action_type = 'FOC';
                                        $TDetails->save();
                                         $log_item_id = $TDetails->id;

                                            echo $TDetails->id .'<br>';
                
                                            $ldet = DB::table('logistic_transaction')->where('transaction_id','=',$value)->first();
                                            $logistic_id = $ldet->id; 
                
                                            
                
                                            $Ldetails = new LogisticTItem; 
                                            $Ldetails->logistic_id = $logistic_id; 
                                            $Ldetails->product_id = 28348;
                                            $Ldetails->product_price_id = 36839;
                                            $Ldetails->transaction_item_id = $log_item_id;
                                            $Ldetails->sku = 'JC-0000000028348';
                                            $Ldetails->name = 'Pokka Houjicha 1.5L';
                                            $Ldetails->label = '1.5L';
                                            $Ldetails->delivery_time = 3;
                                            $Ldetails->qty_order = 1;
                                            $Ldetails->qty_to_assign = 1;
                                            $Ldetails->qty_to_send = 1;
                                            $Ldetails->save();
                // echo 'Process Completed';
                                            // die();
        }
        echo 'Process Completed';
        }
    
    

}