<?php

class RewardController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }
    
    public function anyIndex()
    {
        return View::make('rewardsetting.index');
    }
    
    /*
     * @Desc: Get buy more get more module information
     */
    public function anyInfo(){
        
        $isError = false;
        $message = '';
        
        try{
            
            $RewardModule = DB::table('jocom_reward_module AS JRM' )
                ->select('JRM.*')
                ->where("JRM.reward_type_code",'BMGM')
                ->first();
            
            $RewardBMGMSetting= DB::table('jocom_reward_bmgm_setting AS BMGM' )
                ->select('BMGM.*')
                ->where("BMGM.reward_code_type",'BMGM')
                ->first();
            
            $RewardFirstBMGMModule = DB::table('jocom_reward_bmgm_stage AS BMGMS' )
                ->select('BMGMS.*')
                ->where("BMGMS.code",'S1')
                ->first();
            
            $RewardSecondBMGMModule = DB::table('jocom_reward_bmgm_stage AS BMGMS' )
                ->select('BMGMS.*')
                ->where("BMGMS.code",'S2')
                ->first();
            
            $RewardThirdBMGMModule = DB::table('jocom_reward_bmgm_stage AS BMGMS' )
                ->select('BMGMS.*')
                ->where("BMGMS.code",'S3')
                ->first();
            
            
            $info = array(
                "activation" => $RewardBMGMSetting->activation,
                "is_send_notification" => $RewardBMGMSetting->is_send_notification,
                
                "first_stage" => array(
                    "amount" => $RewardFirstBMGMModule->amount,
                    "is_voucher" => $RewardFirstBMGMModule->is_voucher,
                    "voucher_amount" => $RewardFirstBMGMModule->voucher_amount,
                    "is_point" => $RewardFirstBMGMModule->is_point,
                    "point" => $RewardFirstBMGMModule->point,
                ),
                
                "second_stage" => array(
                    "amount" => $RewardSecondBMGMModule->amount,
                    "is_voucher" => $RewardSecondBMGMModule->is_voucher,
                    "voucher_amount" => $RewardSecondBMGMModule->voucher_amount,
                    "is_point" => $RewardSecondBMGMModule->is_point,
                    "point" => $RewardSecondBMGMModule->point,
                ),
                "third_stage" => array(
                    "amount" => $RewardThirdBMGMModule->amount,
                    "is_voucher" => $RewardThirdBMGMModule->is_voucher,
                    "voucher_amount" => $RewardThirdBMGMModule->voucher_amount,
                    "is_point" => $RewardThirdBMGMModule->is_point,
                    "point" => $RewardThirdBMGMModule->point,
                )
                
                
            );
            
            
        } catch (Exception $ex) {
            $isError = true;
            $message =  $ex->getMessage();

        } finally {
            
            return array(
                "isError" => $isError,
                "info" => $info
            );
            
        }
        
        
    }
    
    
    /*
     * @Desc : to save reward setting for but more get more feature
     */
    public function anySave(){
        
        
        $isError = false;
        
        
        try{
            
           
            
            $isActivate = Input::get("is_activate");
            
            $is1_stage_amount = Input::get("1_stage_amount");
            $is1_is_stage_voucher = Input::get("1_is_stage_voucher");
            $is1_stage_voucher_amount = Input::get("1_stage_voucher_amount");
            $is1_is_stage_point = Input::get("1_is_stage_point");
            $is1_stage_point = Input::get("1_stage_point");
            
            $is2_stage_amount = Input::get("2_stage_amount");
            $is2_is_stage_voucher = Input::get("2_is_stage_voucher");
            $is2_stage_voucher_amount = Input::get("2_stage_voucher_amount");
            $is2_is_stage_point = Input::get("2_is_stage_point");
            $is2_stage_point = Input::get("2_stage_point");
            
            $is3_stage_amount = Input::get("3_stage_amount");
            $is3_is_stage_voucher = Input::get("3_is_stage_voucher");
            $is3_stage_voucher_amount = Input::get("3_stage_voucher_amount");
            $is3_is_stage_point = Input::get("3_is_stage_point");
            $is3_stage_point = Input::get("3_stage_point");
            
            $isPush = Input::get("is_push_notification");
           
            
            $RewardBMGMSetting = RewardBMGMSetting::where("reward_code_type","BMGM")->first();
            
        
            $RewardBMGMSetting->is_send_notification = $isPush;
            $RewardBMGMSetting->activation = $isActivate;
            $RewardBMGMSetting->save();
            
            
            $RewardBMGMStage = RewardBMGMStage::where("code","S1")->first();
            $RewardBMGMStage->amount = $is1_stage_amount;
            $RewardBMGMStage->is_voucher = $is1_is_stage_voucher;
            $RewardBMGMStage->voucher_amount = $is1_stage_voucher_amount;
            $RewardBMGMStage->is_point = $is1_is_stage_point;
            $RewardBMGMStage->point = $is1_stage_point;
            $RewardBMGMStage->save();
            
            $RewardBMGMStage = RewardBMGMStage::where("code","S2")->first();
            $RewardBMGMStage->amount = $is2_stage_amount;
            $RewardBMGMStage->is_voucher = $is2_is_stage_voucher;
            $RewardBMGMStage->voucher_amount = $is2_stage_voucher_amount;
            $RewardBMGMStage->is_point = $is2_is_stage_point;
            $RewardBMGMStage->point = $is2_stage_point;
            $RewardBMGMStage->save();
            
            $RewardBMGMStage = RewardBMGMStage::where("code","S3")->first();
            $RewardBMGMStage->amount = $is3_stage_amount;
            $RewardBMGMStage->is_voucher = $is3_is_stage_voucher;
            $RewardBMGMStage->voucher_amount = $is3_stage_voucher_amount;
            $RewardBMGMStage->is_point = $is3_is_stage_point;
            $RewardBMGMStage->point = $is3_stage_point ;
            $RewardBMGMStage->save();
            
            
        } catch (Exception $ex) {
            
            echo $ex->getMessage();

        } finally {
            
            return array(
                "isError" => $isError,
                "response" => ''
            );
            
        }
        
        
    }
    
    public static function createAccountBMGM($user_id){
        
        try{
            $RewardBMGMTracking = RewardBMGMTracking::where('user_id', '=', $user_id)
                    ->where('is_completed', '=', 0)
                    ->first();
                
                if($RewardBMGMTracking == null ){
                    
                    // create tracking account
                    $RewardBMGMTracking = new RewardBMGMTracking();
                    $RewardBMGMTracking->user_id = $user_id;
                    $RewardBMGMTracking->stage_1 = 0;
                    $RewardBMGMTracking->stage_1_used = 0;
                    $RewardBMGMTracking->stage_2 = 0;
                    $RewardBMGMTracking->stage_2_used = 0;
                    $RewardBMGMTracking->stage_3 = 0;
                    $RewardBMGMTracking->stage_3_used = 0;
                    $RewardBMGMTracking->is_completed = 0;
                    $RewardBMGMTracking->activation = 1;
                    $RewardBMGMTracking->save();
                    
                    $rewardTrackingID = $RewardBMGMTracking->id;
                    
                    
                }else{
                    
                    $rewardTrackingID = $RewardBMGMTracking->id;
                }
        } catch (Exception $ex) {
            
            return false;

        }
        
        return $RewardBMGMTracking;
        
    }
    
    
    public static function rewardBMGM($user_id,$transaction_id){
        
        try{
            
            $code = '';
            
            $RewardTracking = RewardController::createAccountBMGM($user_id);

                $RewardTrackingID = $RewardTracking->id;
                
                $BMGMTransaction = DB::table('jocom_transaction')
                   ->where('id', '=', $transaction_id)
                   ->first();
                   
               $BMGMTransactionCoupon = DB::table('jocom_transaction_coupon AS JTC')
                   ->leftJoin('jocom_coupon AS JC','JC.coupon_code','=','JTC.coupon_code')
                   ->where('JTC.transaction_id', '=', $transaction_id)
                   ->select("JC.*")
                   ->first();
                   
                if($BMGMTransactionCoupon){
                    $coupon_id = $BMGMTransactionCoupon->id;
                    $Coupon = Coupon::find($coupon_id);
                    //$Coupon->status = 0 ;
                    // ->save();
                }else{
                    $coupon_id = null;
                }
             
               
                // Get total available for reward calculation
                $applicableAmount = $BMGMTransaction->gst_total + $BMGMTransaction->total_amount;
           
                $RewardBMGMTransaction = new RewardBMGMTransaction();
                $RewardBMGMTransaction->tracking_id = $RewardTrackingID;
                $RewardBMGMTransaction->transaction_id = $transaction_id;
                $RewardBMGMTransaction->amount = $applicableAmount;
                $RewardBMGMTransaction->save();
                
            
                
                //print_r($RewardBMGMTransaction);
                $totalSum = DB::table('jocom_reward_bmgm_transaction')
                    ->select(DB::raw('SUM(amount) as total_amount'))
                    ->where('tracking_id', '=', $RewardTrackingID)
                    ->first();
                
                $totalSumAmount = $totalSum->total_amount;
                print_r($totalSumAmount);
                
                $firstStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S1')
                   ->first();
                
                $SecondStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S2')
                   ->first();
                
                $thirdStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S3')
                   ->first();
                
                
                if($totalSumAmount >= $thirdStage->amount && $RewardTracking->stage_3_voucher_id == null){
                    
                    $code = 'S3';
                    
                }   elseif($totalSumAmount >= $SecondStage->amount && $RewardTracking->stage_2_voucher_id == null){
                    
                    $code = 'S2';
                    
                }   elseif($totalSumAmount >= $firstStage->amount && $RewardTracking->stage_1_voucher_id == null)  {
                    
                    $code = 'S1';
                    
                }
                //  echo "<pre>";
                //  print_r($code);
                //  echo "</pre>";
                
                if($RewardTracking->is_full_cycle == 0){
                
                    switch ($code) {
                        case 'S1':

                            $isVoucher = $firstStage->is_voucher;
                            $isPoint = $firstStage->is_point;

                            if($isVoucher == 1){
                                // reward customer with voucher
                                $voucherAmount = $firstStage->voucher_amount;
                                $CouponID = CouponController::rewardCoupon('BMGM', 'Nett', $voucherAmount, $user_id);
                                $RewardTracking = RewardBMGMTracking::find($RewardTrackingID);
                                $RewardTracking->stage_1 = 1;
                                $RewardTracking->stage_1_voucher_id = $CouponID;
                                $RewardTracking->save();

                            }

                            break;

                        case 'S2':

                            $isVoucher = $SecondStage->is_voucher;
                            $isPoint = $SecondStage->is_point;

                            if($isVoucher){
                                // reward customer with voucher
                                $voucherAmount = $SecondStage->voucher_amount;
                                $CouponID = CouponController::rewardCoupon('BMGM', 'Nett', $voucherAmount, $user_id);

                                $RewardTracking = RewardBMGMTracking::find($RewardTrackingID);
                                
                                // echo "<pre>";
                                // print_r($RewardTracking);
                                // echo "</pre>";

                                if($RewardTracking){
                                    $RewardTracking->stage_1 = 1;
                                    $RewardTracking->stage_1_used = 1;
                                    
                                    // Deactivate Coupon 
                                    if($RewardTracking->stage_1_voucher_id != null){
                                        $Coupon = Coupon::find($RewardTracking->stage_1_voucher_id);
                                        
                                        echo "<pre>";
                                        print_r($Coupon);
                                        echo "</pre>";
                                
                                        $Coupon->status = 0;
                                        $Coupon->save();
                                    }
                                    
                                }

                                $RewardTracking->stage_2 = 1;
                                $RewardTracking->stage_2_voucher_id = $CouponID;
                                $RewardTracking->save();
                            }

                            break;

                        case 'S3':

                            $isVoucher = $thirdStage->is_voucher;
                            $isPoint = $thirdStage->is_point;

                            if($isVoucher){
                                // reward customer with voucher
                                $voucherAmount = $thirdStage->voucher_amount;
                                $CouponID = CouponController::rewardCoupon('BMGM', 'Nett', $voucherAmount, $user_id);
                                
                                $RewardTracking = RewardBMGMTracking::find($RewardTrackingID);

                                if($RewardTracking){
                                    $RewardTracking->stage_1 = 1;
                                    $RewardTracking->stage_1_used = 1;
                                    
                                    $RewardTracking->stage_2 = 1;
                                    $RewardTracking->stage_2_used = 1;
                                    
                                    // Deactivate Coupon Stage 1
                                    if($RewardTracking->stage_1_voucher_id != null){
                                        $Coupon = Coupon::find($RewardTracking->stage_1_voucher_id);
                                        $Coupon->status = 0;
                                        $Coupon->save();
                                    }
                                    
                                    // Deactivate Coupon Stage 2
                                    if($RewardTracking->stage_2_voucher_id != null){
                                        $Coupon = Coupon::find($RewardTracking->stage_2_voucher_id);
                                        $Coupon->status = 0;
                                        $Coupon->save();
                                    }
                                }

                                $RewardTracking->stage_3 = 1;
                                $RewardTracking->is_full_cycle = 1;
                                $RewardTracking->stage_3_voucher_id = $CouponID;
                                $RewardTracking->save();
                            }

                            break;

                    }
                
                }
                
            
        } catch (Exception $ex) {
        
            return false;

        }
        
        return $RewardBMGMTracking;
        
    }
    
    public function anyEmail(){
        
        return View::make('emails.couponreward');
        
    }
    
    /*
     * @Desc: Referrer Reward module information
     */

     public function anyRefrinfo(){
        $isError = false;
        $message = '';

        
        try{

            

             $RewardModule = DB::table('jocom_reward_module AS JRM')
                ->select('JRM.*')
                ->where("JRM.reward_type_code",'RFRR')
                ->first();  

             $RewardSettings = DB::table('jocom_reward_referrer_setting AS RFRR')
                    ->select('RFRR.*')
                    ->where("RFRR.reward_code_type",'RFRR')
                    ->first();     

                   

            $info = array(
                    "activation" => $RewardModule->activation,
                    "point" => $RewardSettings->point,
                    "description" => $RewardSettings->description
                    );


        } catch (Exception $ex) {
            $isError = true;
            $message =  $ex->getMessage();

        } finally {
            
            return array(
                "isError" => $isError,
                "info" => $info
            );
        }


     }

     /*
     * @Desc : to save reward setting for Referrer
     */
    public function anyRfrrsave(){
        
        
        $isError = false;
        $response = 0;

        
        try{

            $isActivate = Input::get("is_activate");            
            $rfrrPoint = Input::get("rfrr_point");
            $description = Input::get("rfrr_description");

            $RewardModule = RewardModule::where("reward_type_code","RFRR")->first();
            $RewardModule->activation = $isActivate;
            $RewardModule->updated_by = Session::get('username');
            $RewardModule->save();



            $RewardRFRR = RewardRFRRSetting::where("reward_code_type","RFRR")->first();

            if(count($RewardRFRR)>0){
                $RewardRFRRSetting = RewardRFRRSetting::where("reward_code_type","RFRR")->first();
                $RewardRFRRSetting->point = $rfrrPoint;
                $RewardRFRRSetting->description = $description;
                $RewardRFRRSetting->activation = $isActivate;
                $RewardRFRRSetting->updated_by = Session::get('username');
                $RewardRFRRSetting->save();
                
                $response = 1;
            }
            else 
            {
                $RewardRFRRSetting = new RewardRFRRSetting();
                $RewardRFRRSetting->reward_code_type = 'RFRR';
                $RewardRFRRSetting->point            = $rfrrPoint;
                $RewardRFRRSetting->activation       = $isActivate;
                $RewardRFRRSetting->created_by = Session::get('username');
                $RewardRFRRSetting->updated_by = Session::get('username');
                $RewardRFRRSetting->save();

                $response = 1;
            }


        } catch (Exception $ex) {
            
            echo $ex->getMessage();

        } finally {
            
            return array(
                "isError" => $isError,
                "response" => $response
            );
            
        }
    
    }
    
    
    public function anyCode(){

        // echo 'ok';
        $Users  = DB::table('jocom_user')
                    ->whereNull("referrer_code")
                    ->get();  



            foreach ($Users as  $value) {
                $rndcode = '';
                $rndcode = RewardRFRRSetting::generateCode();

                DB::table('jocom_user')
                    ->where('id', $value->id)
                    ->update(['referrer_code' => $rndcode]);
                    
                echo $value->id.'-'.$value->username.'-'.RewardRFRRSetting::generateCode().'<br>';

            }

    }
    
     /*
     * @Desc : BIRTHDAY REWARD START
     */

    public function anyInfobrth(){
        
        $isError = false;
        $message = '';
        
        try{
            
            $RewardModule = DB::table('jocom_reward_module AS JRM' )
                ->select('JRM.*')
                ->where("JRM.reward_type_code",'BRTH')
                ->first();
            
            $RewardBRTHSetting= DB::table('jocom_reward_brth_setting AS BRTH' )
                ->select('BRTH.*')
                ->where("BRTH.reward_code_type",'BRTH')
                ->first();
            
            $RewardFirstBRTHModule = DB::table('jocom_reward_brth_stage AS BRTHS' )
                ->select('BRTHS.*')
                ->where("BRTHS.code",'S1')
                ->first();            
            
            $info = array(
                "activation" => $RewardBRTHSetting->activation,
                "is_send_notification" => $RewardBRTHSetting->is_send_notification,
                
                "first_stage" => array(
                    "amount" => $RewardFirstBRTHModule->amount,
                    "is_voucher" => $RewardFirstBRTHModule->is_voucher,
                    "voucher_amount" => $RewardFirstBRTHModule->voucher_amount,
                    "is_point" => $RewardFirstBRTHModule->is_point,
                    "point" => $RewardFirstBRTHModule->point,
                ),                
                
            );
            
            
        } catch (Exception $ex) {
            $isError = true;
            $message =  $ex->getMessage();

        } finally {
            
            return array(
                "isError" => $isError,
                "info" => $info
            );
            
        }
        
        
    }
    
    public function anySavebrth(){

        $isError = false;
        
        
        try{
            
            $isActivate = Input::get("is_activate");
            
            // $is1_stage_amount = Input::get("1_stage_amount");
            $is1_is_stage_voucher = Input::get("1_is_stage_voucher");
            $is1_stage_voucher_amount = Input::get("1_stage_voucher_amount");
            $is1_is_stage_point = Input::get("1_is_stage_point");
            $is1_stage_point = Input::get("1_stage_point");
                        
            $isPush = Input::get("is_push_notification");
           
            
            $RewardBRTHSetting = RewardBRTHSetting::where("reward_code_type","BRTH")->first();
            
        
            $RewardBRTHSetting->is_send_notification = $isPush;
            $RewardBRTHSetting->activation = $isActivate;
            $RewardBRTHSetting->save();
            
            
            $RewardBRTHStage = RewardBRTHStage::where("code","S1")->first();
            // $RewardBRTHStage->amount = $is1_stage_amount;
            $RewardBRTHStage->is_voucher = $is1_is_stage_voucher;
            $RewardBRTHStage->voucher_amount = $is1_stage_voucher_amount;
            $RewardBRTHStage->is_point = $is1_is_stage_point;
            $RewardBRTHStage->point = $is1_stage_point;
            $RewardBRTHStage->save();
                        
            
        } catch (Exception $ex) {
            
            echo $ex;

        } finally {
            
            return array(
                "isError" => $isError,
                "response" => ''
            );
            
        }      
        
    }

    public static function anyRewardbrth(){

        try{

            $user = DB::table('jocom_user')->where('dob','!=', '')->where('active_status',1)->where('username','=','maryanne')->get();
   
            $firstStage = DB::table('jocom_reward_brth_stage')
                   ->where('code', '=', 'S1')
                   ->first();

            foreach ($user as $key => $value) {
                $user_id = $value->id;

                $isVoucher = $firstStage->is_voucher;
                $isPoint = $firstStage->is_point;

                if($isVoucher == 1){
                    // reward customer with voucher
                    $voucherAmount = $firstStage->voucher_amount;
                    $CouponID = CouponController::rewardCoupon('BRTH', 'Nett', $voucherAmount, $user_id);  

                    if (isset($CouponID)) {
                        $RewardTracking = new RewardBRTHTracking();
                        $RewardTracking->stage_1 = 1;
                        $RewardTracking->user_id = $user_id;
                        $RewardTracking->stage_1_voucher_id = $CouponID;
                        $RewardTracking->save();   
                    }

                }

                if ($isPoint == 1) {
                    
                    $jpoint = $firstStage->point;
                    $PointID = PointCustomerController::rewardPoint('BRTH',$user_id,$jpoint);  

                    if (isset($PointID)) {
                        $RewardTracking = new RewardBRTHTracking();
                        $RewardTracking->stage_1 = 1;
                        $RewardTracking->user_id = $user_id;
                        $RewardTracking->stage_1_jpoint_id = $PointID;
                        $RewardTracking->save();   
                    }

                }

            }
                      
            
        } catch (Exception $ex) {
        
            return false;

        }
        
        return $RewardTracking;
        
    }  

    /*
     * @Desc : BIRTHDAY REWARD END
     */ 
  
}
