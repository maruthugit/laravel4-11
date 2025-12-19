<?php

class LeaderboardController extends BaseController
{
    
    public function index(){
        
        return View::make('leaderboard.index');
        
    }
    
    public function submission(){


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            $is_verified = false;
            
            $order_number = Input::get('order_number');
            $tracking_number = Input::get('tracking_number');
            
            $Leaderboard = Leaderboard::where("order_number",$order_number)->first();
            
            if(count($Leaderboard) > 0 ){
                // order number has been submitted before
                $is_verified = false;
                $data = array(
                        "isVerified" => 0,
                        "message" => 'We are sorry . Your order number has been submitted before'
                    );
                 
            }else{
                
                if($tracking_number === null){
                    $id = substr($tracking_number,4);        
                    $checkString = substr($tracking_number,0,4);

                    if(is_numeric($checkString))
                    {
                        $id = $tracking_number;
                    }
                }

                switch (Input::get('platform')) {
                    case 1: // JOCOM APP

                        $order = Transaction::where("id",$order_number)
                            ->where("status","completed")
                             ->whereNotIn("buyer_username","<>",['11street','lazada','shopee','qoo10'])->first();

                        if(count($order) > 0 ){
                            $is_verified = true;
                            $transaction_id = $order_number;
                        }else{

                            $orderTracking = Transaction::where("id",$id)
                            ->where("status","completed")->first();
                            if(count($orderTracking) > 0 ){
                                $is_verified = true;
                                $transaction_id = $id;
                            }
                        }

                        break;
                    case 2: // 11STREET APP

                        $ElevenStreetOrder = ElevenStreetOrder::where("order_number",$order_number)->first();
                        $record = $ElevenStreetOrder;
                        if(count($ElevenStreetOrder) > 0 ){

                            $order = Transaction::where("id",$ElevenStreetOrder->transaction_id)->where("status","completed")->first();
                            
                            if(count($order) > 0 ){
                                $is_verified = true;
                                $transaction_id = $order->id;
                               
                            }

                        }else{

                            $orderTracking = Transaction::where("id",$id)
                            ->where("status","completed")->first();
                            if(count($orderTracking) > 0 ){
                                $is_verified = true;
                                $transaction_id = $id;
                            }

                        }

                        break;
                    case 3: // LAZADA APP

                        $LazadaOrder = LazadaOrder::where("order_number",$order_number)->first();

                        if(count($LazadaOrder) > 0 ){

                            $order = Transaction::where("id",$LazadaOrder->transaction_id)
                            ->where("status","completed")->first();
                                
                            if(count($order) > 0 ){
                                $is_verified = true;
                                $transaction_id = $order->id;
                            }

                        }else{

                            $orderTracking = Transaction::where("id",$id)
                            ->where("status","completed")->first();
                            if(count($orderTracking) > 0 ){
                                $is_verified = true;
                                $transaction_id = $id;
                            }

                        }

                        break;
                    case 4: // QOO10 APP

                        $QootenOrder = QootenOrder::where("packNo",$order_number)->first();

                        if(count($QootenOrder) > 0 ){

                            $order = Transaction::where("id",$QootenOrder->transaction_id)
                            ->where("status","completed")->first();

                            if(count($order) > 0 ){
                                $is_verified = true;
                                $transaction_id = $order->id;
                            }

                        }else{

                            $orderTracking = Transaction::where("id",$id)
                            ->where("status","completed")->first();
                            if(count($orderTracking) > 0 ){
                                $is_verified = true;
                                $transaction_id = $id;
                            }

                        }

                        break;

                    case 5: // SHOPEE APP

                        $ShopeeOrder = ShopeeOrder::where("ordersn",$order_number)->first();

                        if(count($ShopeeOrder) > 0 ){

                            $order = Transaction::where("id",$ShopeeOrder->transaction_id)
                            ->where("status","completed")->first();

                            if(count($order) > 0 ){
                                $is_verified = true;
                                $transaction_id = $order->id;
                            }

                        }else{

                            $orderTracking = Transaction::where("id",$id)
                            ->where("status","completed")->first();
                            if(count($orderTracking) > 0 ){
                                $is_verified = true;
                                $transaction_id = $id;
                            }

                        }

                        break;

                    default:
                        break;
                }

                if($is_verified){

                    
                    $orderTransaction = Transaction::where("id",$transaction_id)->first();

                    $totalAmount = $orderTransaction->total_amount + $orderTransaction->gst_total;

                    $Leaderboard = new Leaderboard();
                    $Leaderboard->email = Input::get('email');
                    $Leaderboard->name = Input::get('name');
                    $Leaderboard->tracking_number = $tracking_number;
                    $Leaderboard->platform = Input::get('platform');
                    $Leaderboard->order_number = $order_number;
                    $Leaderboard->transaction_id = $transaction_id;
                    $Leaderboard->amount = $totalAmount;
                    $Leaderboard->is_approved = 1;
                    $Leaderboard->status = 1;
                    $Leaderboard->save();
                    
                    $id = $Leaderboard->id;
                    
                     
                    
                    $ranked = DB::select( DB::raw("SELECT * FROM (SELECT jocom_leaderboard.*, @rank := @rank + 1 rank FROM jocom_leaderboard CROSS JOIN (SELECT @rank := 0) init ORDER BY amount DESC, created_at ASC) AS A WHERE A.id = ".$id) );
                    
                    
                    $data = array(
                        "isVerified" => 1,
                        "message" => 'congratulation',
                        "ranked" => $ranked[0]->rank,
                    );

                }else{
                    
                    // DO something
                    
                    $data = array(
                        "isVerified" => 0,
                        "message" => 'We are sorry . Your submission cannot be verified , Please enter with correct information',
                        "record" => $record
                    );


                    

                }
            
            }
            
            

        } catch (Exception $ex) {
            $is_error = true;     
            $message = $ex->getMessage();
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        
        return $response;

    
        
        
        
    }
    
   
    public function getBoardlist() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            DB::beginTransaction();
            
            $Leaderboard = Leaderboard::where("is_approved",1)
                    ->orderBy('amount', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
            $data = $Leaderboard;
        
        } catch (Exception $ex) {
            
            $is_error = true;
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $Leaderboard);
        return $response;

    
    }
    
    public function approve() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            DB::beginTransaction();
            
            $submissionID  = Input::get('id');
            
            $Leaderboard = Leaderboard::find($submissionID);
            
            if(count($Leaderboard) > 0){
                
                
                
            }else{
                
            }
            
            
        
        } catch (Exception $ex) {
            
            $is_error = true;
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function application() {
        
        // Get Orders
        
        $cards = DB::table('jocom_leaderboard')->select(array(
                        'jocom_leaderboard.id',
                        'jocom_leaderboard.email',
                        'jocom_leaderboard.name',
                        'jocom_leaderboard.tracking_number',
                        'jocom_leaderboard.platform',
                        'jocom_leaderboard.order_number',
                        'jocom_leaderboard.transaction_id',
                        'jocom_leaderboard.amount',
                        'jocom_leaderboard.is_approved',
                        'jocom_leaderboard.created_at'
                        ))
                    ->orderBy('jocom_leaderboard.id','asc');
        return Datatables::of($cards)->make(true);
        
    }
    
    
    
    
    
    
    
}
