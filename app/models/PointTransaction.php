<?php

class PointTransaction extends Eloquent
{
    protected $guarded = array('id');

    private $pointUser;

    public function __construct(PointUser $pointUser = null)
    {
        $this->pointUser = $pointUser;
    }

    public function setUpdatedAtAttribute($value)
    {
        // Disable Eloquent default `updated_at` column in DB
    }

    public function history($order = 'desc')
    {
        return DB::table('point_transactions')
            ->select('point_transactions.*', 'point_actions.*', 'point_transactions.id AS id')
            ->join('point_actions', 'point_transactions.point_action_id', '=', 'point_actions.id')
            ->where('point_transactions.point_user_id', '=', $this->pointUser->id)
            ->orderBy('point_transactions.created_at', $order)
            ->orderBy('point_transactions.id', $order)
            ->get();
    }

    public function purchase($transactionId, $paypal = false)
    {
        $pointType        = PointType::find($this->pointUser->point_type_id);
        $transactionPoint = TPoint::where('point_type_id', '=', $this->pointUser->point_type_id)
            ->where('transaction_id', '=', $transactionId)
            ->first();
        
        $Tdcnt = 0;    
        $TDetails = TDetails::where("transaction_id",$transactionId)->where("sku","JC-0000000029181")->first();
        if(count($TDetails)>0){
            $Tdcnt = 1;    
        }

        if ($transactionPoint) {
            // Update transaction status to indicate actual point reduction
            $transactionPoint->status = 1;
            $transactionPoint->save();

            $user = Customer::find($this->pointUser->user_id);
            General::audit_trail('PointTransaction.php', 'purchase()', 'Update redeem point status', $user->username, 'CMS');

            if ($paypal) {
                $isRedeemed = (bool) $this->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->where('point_transactions.transaction_id', '=', $transactionId)
                    ->where('point_users.point_type_id', '=', $this->pointUser->point_type_id)
                    ->where('point_transactions.point_action_id', '=', PointAction::REDEEM)
                    ->count();
            } else {
                $isRedeemed = false;
            }

            $TPRedeemed = (bool) TPoint::where('transaction_id', '=', $transactionId)
                ->where('point_type_id', '=', $this->pointUser->point_type_id)
                ->where('status', '=', 0)
                ->count();

            if ($TPRedeemed == false) {
                if ($isRedeemed == false) {
                    $redeemPoint = $transactionPoint->point;

                    // Deduct points
                    $this->pointUser->point -= $redeemPoint;
                    $this->pointUser->save();

                    $user = Customer::find($this->pointUser->user_id);
                    General::audit_trail('PointTransaction.php', 'purchase()', 'Update user point balance', $user->username, 'CMS');

                    // Log transaction
                    DB::table('point_transactions')
                        ->insert([
                            'point_user_id'   => $this->pointUser->id,
                            'point'           => -($redeemPoint),
                            'rate'            => $pointType->redeem_rate,
                            'balance'         => $this->pointUser->point,
                            'point_action_id' => PointAction::REDEEM,
                            'code'            => hash('sha256', "{$this->pointUser->id}|" . date('Y-m-d H:i:s') . "|-{$redeemPoint}|{$this->pointUser->point}"),
                            'transaction_id'  => $transactionId,
                            'created_at'      => date('Y-m-d H:i:s'),
                        ]);

                    $user = Customer::find($this->pointUser->user_id);
                    General::audit_trail('PointTransaction.php', 'purchase()', 'Insert point transaction', $user->username, 'CMS');
                }
            }
        }

        $earnedId     = [];
        $earnedPoints = $this->where('point_transactions.transaction_id', '=', $transactionId)
            ->where('point_transactions.point_user_id', '=', $this->pointUser->id)
            ->where('point_transactions.point_action_id', '=', PointAction::EARN)
            ->get();

        foreach ($earnedPoints as $earnedPoint) {
            $earnedId[] = $earnedPoint->id;
        }

        $reversedId     = [];
        $reversedPoints = $this->where('point_transactions.transaction_id', '=', $transactionId)
            ->where('point_transactions.point_user_id', '=', $this->pointUser->id)
            ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
            ->get();

        foreach ($reversedPoints as $reversedPoint) {
            $reversedId[] = $reversedPoint->reversal;
        }

        $isRewarded = (bool) $this->where('point_transactions.transaction_id', '=', $transactionId)
            ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))
            ->count();

        if ($this->pointUser->status == PointUser::ACTIVE && $isRewarded == false) {
            $earnPoint = $this->getTransactionPoint($transactionId);
            if($Tdcnt == 0){
                if ($earnPoint > 0) {
                    // Accumulate points
                    $this->pointUser->point += $earnPoint;
                    $this->pointUser->save();
    
                    // Log transaction
                    DB::table('point_transactions')
                        ->insert([
                            'point_user_id'   => $this->pointUser->id,
                            'point'           => $earnPoint,
                            'rate'            => $pointType->redeem_rate,
                            'balance'         => $this->pointUser->point,
                            'point_action_id' => PointAction::EARN,
                            'code'            => hash('sha256', "{$this->pointUser->id}|" . date('Y-m-d H:i:s') . "|{$earnPoint}|{$this->pointUser->point}"),
                            'transaction_id'  => $transactionId,
                            'created_at'      => date('Y-m-d H:i:s'),
                        ]);
                }
            }
        }
    }

    public function redeem($transactionId, $redeemPoint)
    {
        if ($redeemPoint <= 0) {
            return;
        }

        $pointTypeId  = $this->pointUser->point_type_id;
        $pointType    = PointType::active()->find($pointTypeId);
        $redeemAmount = $redeemPoint * $pointType->redeem_rate;
        $transaction  = TPoint::where('transaction_id', '=', $transactionId)
            ->where('point_type_id', '=', $this->pointUser->point_type_id)
            ->first();

        if ($transaction) {
            $transaction->point += $redeemPoint;
            $transaction->amount += $redeemAmount;
            $transaction->save();
        } else {
            $transaction                 = new TPoint;
            $transaction->transaction_id = $transactionId;
            $transaction->point          = $redeemPoint;
            $transaction->amount         = $redeemAmount;
            $transaction->point_type_id  = $this->pointUser->point_type_id;
            $transaction->save();
        }

        $user = Customer::find($this->pointUser->user_id);
        General::audit_trail('PointTransaction.php', 'redeem()', 'Redeem point', $user->username, 'CMS');
    }

    /**
     * @param $transactionId (int)     Transaction ID
     * @param $point         (int)     Points
     * @param $amount        (decimal) Cash amount
     * @param $remark        (string)  Remark
     */
    public function refund($transactionId, $point, $amount, $remark = '')
    {
        // If both point and amount are positive or negative
        if (($point >= 0 && $amount >= 0) || ($point <= 0 && $amount <= 0)) {
            $rate = $amount / $point;

            if ($rate > 0) {
                $this->pointUser->point += $point;
                $this->pointUser->save();

                DB::table('point_transactions')
                    ->insert([
                        'point_user_id'   => $this->pointUser->id,
                        'point'           => $point,
                        'rate'            => $rate,
                        'balance'         => $this->pointUser->point,
                        'remark'          => $remark,
                        'point_action_id' => PointAction::REFUND,
                        'code'            => hash('sha256', "{$this->pointUser->id}|" . date('Y-m-d H:i:s') . "|{$point}|{$this->pointUser->point}"),
                        'transaction_id'  => $transactionId,
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);

                $user = Customer::find($this->pointUser->user_id);
                General::audit_trail('PointTransaction.php', 'refund()', 'Refund', $user->username, 'CMS');

                return true;
            }
        }

        Log::warning('Invalid refund.', ['transactionId' => $transactionId, 'point' => $point, 'amount' => $amount, 'remark' => $remark]);
        return false;
    }

    public function reversal($transactionId)
    {
        $redeemTransactions = DB::table('point_transactions')
            ->where('point_transactions.transaction_id', '=', $transactionId)
            ->where('point_transactions.point_action_id', '=', PointAction::REDEEM)
            ->orderBy('created_at', 'desc')
            ->groupBy('point_user_id')
            ->get();

        foreach ($redeemTransactions as $redeemTransaction) {
            if ($redeemTransaction && $redeemTransaction->point_user_id == $this->pointUser->id) {
                $this->pointUser->point -= $redeemTransaction->point;
                $this->pointUser->save();

                // Log transaction
                DB::table('point_transactions')
                    ->insert([
                        'point_user_id'   => $this->pointUser->id,
                        'point'           => -($redeemTransaction->point),
                        'rate'            => $redeemTransaction->rate,
                        'balance'         => $this->pointUser->point,
                        'point_action_id' => PointAction::REVERSAL,
                        'code'            => hash('sha256', "{$this->pointUser->id}|" . date('Y-m-d H:i:s') . "|" . -($redeemTransaction->point) . "|{$this->pointUser->point}"),
                        'transaction_id'  => $transactionId,
                        'reversal'        => $redeemTransaction->id,
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);

                $user = Customer::find($this->pointUser->user_id);
                General::audit_trail('PointTransaction.php', 'reversal()', 'Reversal', $user->username, 'CMS');
            }
        }

        $earnedId     = [];
        $earnedPoints = DB::table('point_transactions')
            ->where('point_transactions.transaction_id', '=', $transactionId)
            ->where('point_transactions.point_action_id', '=', PointAction::EARN)
            ->get();

        foreach ($earnedPoints as $earnedPoint) {
            $earnedId[] = $earnedPoint->id;
        }

        $reversedId     = [];
        $reversedPoints = DB::table('point_transactions')
            ->where('point_transactions.transaction_id', '=', $transactionId)
            ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
            ->get();

        foreach ($reversedPoints as $reversedPoint) {
            $reversedId[] = $reversedPoint->reversal;
        }

        $earnTransactions = DB::table('point_transactions')
            ->whereIn('id', array_diff($earnedId, $reversedId))
            ->get();

        foreach ($earnTransactions as $earnTransaction) {
            if ($earnTransaction && $earnTransaction->point_user_id == $this->pointUser->id) {
                // Deduct point
                $this->pointUser->point -= $earnTransaction->point;
                $this->pointUser->save();

                // Log transaction
                DB::table('point_transactions')
                    ->insert([
                        'point_user_id'   => $this->pointUser->id,
                        'point'           => -($earnTransaction->point),
                        'rate'            => $earnTransaction->rate,
                        'balance'         => $this->pointUser->point,
                        'point_action_id' => PointAction::REVERSAL,
                        'code'            => hash('sha256', "{$this->pointUser->id}|" . date('Y-m-d H:i:s') . "|-{$earnTransaction->point}|{$this->pointUser->point}"),
                        'transaction_id'  => $transactionId,
                        'reversal'        => $earnTransaction->id,
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);

                $user = Customer::find($this->pointUser->user_id);
                General::audit_trail('PointTransaction.php', 'reversal()', 'Reversal', $user->username, 'CMS');
            }
        }

        $transactionPoints = TPoint::where('transaction_id', '=', $transactionId)->get();

        foreach ($transactionPoints as $transactionPoint) {
            $transactionPoint->status = 0;
            $transactionPoint->save();
        }
    }

    public function getTransactionPoint($transactionId)
    {
        $transaction       = Transaction::find($transactionId);
        $transactionCoupon = TCoupon::where('transaction_id', '=', $transactionId)->first();
        $transactionPoint  = TPoint::where('transaction_id', '=', $transactionId)->first();
        $pointType         = PointType::find($this->pointUser->point_type_id);
        $splskupoints = 0;
        
        
        // BCARD PROMOTION //
        if($pointType->type == 'BCard' ){
            
            $multiply = 1;
            
            // $multiply_three = array("JC7293", "JC7292", "JC7188", "JC7187","JC7305", "JC7304", "JC7303", "JC6579", "JC6562", "JC6561","JC3611");
            // $multiply_two = array("JC6577", "JC6576", "JC6575", "JC6126", "JC6569", "JC3200");
            
            // $TDetails = TDetails::where("transaction_id",$transactionId)->get();
            
            // foreach ($TDetails as $key => $value) {
            //     $Product = Product::where("sku",$value->sku)->first();
            //     if(in_array($Product->qrcode, $multiply_two)){
            //         $multiply = 2;
            //     }
            // }
            
            // foreach ($TDetails as $key => $value) {
                
            //     $Product = Product::where("sku",$value->sku)->first();
            //     if(in_array($Product->qrcode, $multiply_three)){
            //         $multiply = 3;
            //     }
            // }
            
            $date = $transaction->transaction_date;
            $startdate = date('2018-08-16 00:00:00');
            $enddate = date('2018-09-24 23:59:59');

            if (strtotime($date) >= strtotime($startdate) && strtotime($date) <= strtotime($enddate)) {

                $multiply_five = array("JC8295" ,
                                            "JC15061" 
                                            );
                $multiply_three = array();
                $multiply_two = array();
                
                
                $TDetails = TDetails::where("transaction_id",$transactionId)->get();

                foreach ($TDetails as $key => $value) {
                    $Product = Product::where("sku",$value->sku)->first();
                    if(in_array($Product->qrcode, $multiply_five)){
                        $multiply = 5;
                        $status = "true";
                    }else{
                        $status = "false";
                    }
    
                }

                if($status=="false"){
                    
                    foreach ($TDetails as $key => $value) {
                        $Product = Product::where("sku",$value->sku)->first();
                            if(in_array($Product->qrcode, $multiply_three)){
                                $multiply = 3;
                                $status = "true";
                            }else{
                                $status = "false";
                            }
    
                    }
                    
                    if($status=="false"){
                    
                        foreach ($TDetails as $key => $value) {
                            $Product = Product::where("sku",$value->sku)->first();
                                if(in_array($Product->qrcode, $multiply_two)){
                                    $multiply = 2;
                                    $status = "true";
                                }else{
                                    $status = "false";
                                }
        
                            }
                        }
                    
                    
                    // if ($transaction->total_amount + $transaction->total_gst >= "60.00") {
                    //     $multiply = 3;
                    // }else{
                    //     $multiply = 2;
                    // }
                }
                
            }      
               
            // Noob campaign 8 nov - 14 nov 2018
            if(date("Y-m-d h:i:s") >= '2018-11-08 00:00:00' && date("Y-m-d h:i:s") <= '2018-11-14 23:59:59'){
                $multiply = 3;
            }
            // BCARD PROMOTION //
        
        }else{
            $multiply = 1;
        }
        
        $result = DB::table('jocom_transaction_details AS JTD')
                    ->leftjoin('jocom_transaction as JP','JP.id','=','JTD.transaction_id')
                    // ->select('JTD.transaction_id')
                    ->whereIn("JTD.sku",['JC-0000000029635','JC-0000000029637','JC-0000000029636','JC-0000000029757','JC-0000000029885','JC-0000000029886','JC-0000000031266','JC-0000000031972'])  
                    ->where('JP.id','=',$transactionId)
                    ->get();
        
        if(count($result)>0){
            foreach ($result as $key => $value) {
               
               if($value->product_id == 29635){
                    $splskupoints = $splskupoints + 35000;  
                   
               }
               else if($value->product_id == 29636){
                    $splskupoints = $splskupoints + 100000;  
                   
               }
               else if($value->product_id == 29637){
                    $splskupoints = $splskupoints + 60000;  
                   
               }
               else if($value->product_id == 29757){
                    $splskupoints = $splskupoints + 105000;  
                   
               }
               else if($value->product_id == 29885){
                    $splskupoints = $splskupoints + 63000;  
                   
               }
               else if($value->product_id == 29886){
                    $splskupoints = $splskupoints + 37000;  
                   
               }
               else if($value->product_id == 31266){
                    $splskupoints = $splskupoints + 12000;  
                   
               }
               else if($value->product_id == 31972){
                    $splskupoints = $splskupoints + 35000;  
                   
               }
                
            }
                
           
        }
        
        if($splskupoints == 0){

        //$points = ($transaction->total_amount + $transaction->gst_total - $transactionCoupon->coupon_amount - $transactionPoint->amount) * $pointType->earn_rate * $multiply;
        $points = ($transaction->total_amount - $transaction->delivery_charges - $transactionCoupon->coupon_amount - $transactionPoint->amount) * $pointType->earn_rate * $multiply;
        }
        else {
            $points = $splskupoints;
        }
        
        if ($points - floor($points) > 0.99) {
            return floor($points) + 1;
        } else {
            return floor($points);
        }
    }
}
