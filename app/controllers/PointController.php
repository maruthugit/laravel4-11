<?php

class PointController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function index()
    {
        return View::make('points.index');
    }

    public function show($id)
    {
        $point           = PointType::findOrFail($id);
        $deactivateUsers = DB::table('point_deactivate_users')
            ->join('jocom_user', 'point_deactivate_users.user_id', '=', 'jocom_user.id')
            ->where('point_type_id', '=', $id)
            ->lists('username');

        return View::make('points.show', [
            'point'         => $point,
            'statusOptions' => [
                0 => 'Inactive',
                1 => 'Active',
            ],
            'deactivate'    => implode(', ', $deactivateUsers),
        ]);
    }

    public function create()
    {
        return View::make('points.create', [
            'statusOptions' => [
                0 => 'Inactive',
                1 => 'Active',
            ],
        ]);
    }

    public function store()
    {
        $input      = Input::except('_token', 'deactivate');
        $deactivate = Input::get('deactivate');
        $usernames  = explode(',', trim(str_replace(' ', '', $deactivate)));
        $validator  = Validator::make($input, PointType::rules());

        if ($validator->fails()) {
            $errors = $validator->messages();

            return Redirect::back()->withInput()->withErrors($errors);
        }

        $point              = new PointType;
        $point->type        = array_get($input, 'type');
        $point->earn_rate   = array_get($input, 'earn_rate');
        $point->redeem_rate = array_get($input, 'redeem_rate');
        $point->status      = array_get($input, 'status');
        $point->save();

        $userIds = array_pluck(Customer::whereIn('username', $usernames)->get()->toArray(), 'id');

        foreach ($userIds as $userId) {
            DB::table('point_deactivate_users')
                ->insert([
                    'point_type_id' => $point->id,
                    'user_id'       => $userId,
                ]);
        }

        return Redirect::to('points')->withSuccess('Successfully updated.');
    }

    public function update($id)
    {
        $point      = PointType::findOrFail($id);
        $input      = Input::except('_method', '_token', 'deactivate');
        $deactivate = Input::get('deactivate');
        $usernames  = explode(',', trim(str_replace(' ', '', $deactivate)));
        $validator  = Validator::make($input, PointType::rules());

        if ($validator->fails()) {
            $errors = $validator->messages();

            return Redirect::back()->withInput()->withErrors($errors);
        }

        $point->type        = array_get($input, 'type');
        $point->earn_rate   = array_get($input, 'earn_rate');
        $point->redeem_rate = array_get($input, 'redeem_rate');
        $point->status      = array_get($input, 'status');
        $point->save();

        $userIds         = array_pluck(Customer::whereIn('username', $usernames)->get()->toArray(), 'id');
        $deactivateUsers = DB::table('point_deactivate_users')
            ->join('jocom_user', 'point_deactivate_users.user_id', '=', 'jocom_user.id')
            ->where('point_type_id', '=', $id)
            ->lists('user_id');

        $insertIds = array_diff($userIds, $deactivateUsers);
        $deleteIds = array_diff($deactivateUsers, $userIds);

        foreach ($insertIds as $insertId) {
            DB::table('point_deactivate_users')
                ->insert([
                    'point_type_id' => $id,
                    'user_id'       => $insertId,
                ]);
        }

        foreach ($deleteIds as $deleteId) {
            DB::table('point_deactivate_users')
                ->where('point_type_id', '=', $id)
                ->where('user_id', '=', $deleteId)
                ->delete();
        }

        return Redirect::to('points')->withSuccess('Successfully updated.');
    }

    public function destroy($id)
    {
        $point         = PointType::findOrFail($id);
        $point->status = 2;
        $point->save();

        return Redirect::back()->withSuccess('Successfully deleted.');
    }

    public function bcard()
    {
        return View::make('points.bcard.index');
    }

    public function bcardcreate()
    {
        return View::make('points.bcard.create');
    }

    public function bcardstore()
    {
        $input = Input::except('_token');

        $validator = Validator::make($input, [
            'bcard'          => 'required|digits:16',
            'type'           => 'required',
            'transaction_id' => 'required_if:type,transaction',
            'customer'       => 'required_if:type,conversion',
            'jpoint'         => 'required_if:type,conversion',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $bcard         = Input::get('bcard');
        $type          = Input::get('type');
        $transactionId = Input::get('transaction_id');
        $customer      = Input::get('customer');
        $jpoint        = Input::get('jpoint');

        switch ($type) {
            case 'transaction':
                $response = $this->bcardRewardTransaction($bcard, $transactionId);
                break;
            case 'conversion':
                $response = $this->bcardRewardConversion($bcard, $customer, $jpoint);
                break;
        }

        return Redirect::to('points/bcard');
    }

    protected function bcardRewardTransaction($bcard, $transactionId)
    {
        $billNo = 'T'.$transactionId;
        $query  = BcardTransaction::where('bill_no', '=', $billNo)
            ->where('action', '=', 'Earn')
            ->where('api', '=', 'Reward')
            ->whereNotNull('reward_id');

        $reward = $query->first();

        if ($reward) {
            Session::flash('error', 'Transaction is already rewarded with Reward ID '.$reward->reward_id.'.');
            return false;
        }

        $transaction = Transaction::find($transactionId);

        if ( ! $transaction) {
            Session::flash('error', 'Transaction not found.');
            return false;
        }

        $coupon = TCoupon::where('transaction_id', '=', $transactionId)->first();
        $point  = TPoint::where('transaction_id', '=', $transactionId)
            ->where('status', '=', 1)
            ->first();

        $grandTotal  = $transaction->total_amount + $transaction->gst_total - $coupon->coupon_amount - $point->amount;
        $earnRate    = object_get(PointType::find(PointType::BCARD), 'earn_rate', 1);
        
        // BCARD PROMOTION //
        
            
        $multiply = 1;
        /*
        
        $multiply_three = array("JC7293", "JC7292", "JC7188", "JC7187","JC7305", "JC7304", "JC7303", "JC6579", "JC6562", "JC6561","JC3611");
        $multiply_two = array("JC6577", "JC6576", "JC6575", "JC6126", "JC6569", "JC3200");
        
        */
        
        $multiply_three = array();
        if(DATE("Y-m-d h:i:s") >= '2018-08-01 00:00:00' && DATE("Y-m-d h:i:s") <= '2018-08-18 23:59:59' ) {
             $multiply_two = array("JC15119");
        }else{
             $multiply_two = array();
        }

        $TDetails = TDetails::where("transaction_id",$transactionId)->get();

        foreach ($TDetails as $key => $value) {
            $Product = Product::where("sku",$value->sku)->first();
            if(in_array($Product->qrcode, $multiply_two)){
                $multiply = 2;
            }
        }

        foreach ($TDetails as $key => $value) {

            $Product = Product::where("sku",$value->sku)->first();
            if(in_array($Product->qrcode, $multiply_three)){
                $multiply = 3;
            }
        }
      
        
        
        $bpoint      = floor($grandTotal * $earnRate * $multiply);
        // BCARD PROMOTION //
        $bcardConfig = array_merge(Config::get('points.bcard'), [
            'Card'        => $bcard,
            'TranxDate'   => date('Y-m-d\TH:i:s', strtotime($transaction->transaction_date)),
            'BillNo'      => 'T'.$transactionId,
            'TotalAmount' => $grandTotal,
            'TotalPoint'  => $bpoint,
        ]);

        $response = Bcard::api('Reward', $bcardConfig);

        if (object_get($response, 'status', 0) > 0) {
            $bcardTransaction            = new BcardTransaction;
            $bcardTransaction->action    = 'Earn';
            $bcardTransaction->api       = 'Reward';
            $bcardTransaction->point     = $bpoint;
            $bcardTransaction->request   = json_encode($bcardConfig);
            $bcardTransaction->response  = json_encode($response);
            $bcardTransaction->reward_id = object_get($response, 'RewardID');
            $bcardTransaction->bill_no   = 'T'.$transactionId;
            $bcardTransaction->save();

            Session::flash('success', 'Rewarded successfully.');

            return true;
        } else {
            Session::flash('error', 'Reward failed.');
            return false;
        }
    }

    protected function bcardRewardConversion($bcard, $customerUsername, $jpoint)
    {
        $customer = Customer::where('username', '=', $customerUsername)->first();

        if ( ! $customer) {
            Session::flash('error', 'Customer not found.');
            return false;
        }

        $customerJpoint = PointUser::getPoint($customer->id, PointType::JOPOINT, true);

        if ($customerJpoint->point < $jpoint) {
            Session::flash('error', 'Insufficient point.');
            return false;
        }

        $response = PointConversion::outbound($customerUsername, false, 'bcard', $bcard, $jpoint);

        if (array_get($response, 'message') == '#1901') {
            Session::flash('success', 'BPoint rewarded successfully.');
            return true;
        } else {
            Session::flash('error', 'Reward failed.');
            return false;
        }
    }

    public function datatables()
    {
        return Datatables::of(PointType::getDatatables())
            ->edit_column('status', function ($point) {
                switch ($point->status) {
                    case 0:
                        return 'Inactive';
                        break;
                    case 1:
                        return 'Active';
                        break;
                }
            })
            ->add_column('action', function ($point) {
                return '<a class="btn btn-primary btn-sm" href="'.url("points/{$point->id}").'"><i class="fa fa-pencil"></i></a>
                    <button class="btn btn-danger btn-sm" data-target="#delete" data-toggle="modal" data-type="'.$point->type.'" data-type-id="'.$point->id.'"><i class="fa fa-close"></i></button>';
            })
            ->make();
    }

    public function bcardDatatables()
    {
        return Datatables::of(BcardTransaction::getDatatables())
            ->edit_column('request', function ($bcard) {
                return object_get(json_decode($bcard->request), 'Card');
            })
            ->remove_column('response')
            ->edit_column('transaction_type', function ($bcard) {
                return $bcard->transaction_type." ({$bcard->api})";
            })
            ->remove_column('api')
            ->add_column('bill_no', function ($bcard) {
                $request = json_decode(array_get($bcard->toArray(), 'request'));
                $billNo  = object_get($request, 'BillNo');
                $prefix  = '';

                if (is_numeric($billNo)) {
                    switch ($bcard->transaction_type) {
                        case 'Earn':
                            $prefix = 'T';
                            break;
                        case 'Convert':
                            $prefix = 'C';
                            break;
                    }
                } else {
                    if (is_numeric(substr($billNo, 1))) {
                        $prefix = substr($billNo, 0, 1);
                        $billNo = substr($billNo, 1);
                    }
                }

                if ($prefix == 'T') {
                    return link_to('transaction/edit/'.$billNo, $prefix.$billNo);
                }

                return $prefix.$billNo;
            }, 1)
            ->add_column('reward_id', function ($bcard) {
                $response = json_decode(array_get($bcard->toArray(), 'response'));

                return object_get($response, 'RewardID');
            }, 2)
            ->add_column('action', function ($bcard) {
                return '<button class="btn btn-primary btn-sm" data-target="#show" data-toggle="modal" data-id="'.$bcard->id.'"><i class="fa fa-eye"></i></button>
                    <button class="btn btn-danger btn-sm" data-target="#void" data-toggle="modal" data-id="'.$bcard->id.'"><i class="fa fa-remove"></i></button>';
            })
            ->edit_column('status', function ($bcard) {
                $status = ($bcard->status + 1) % 2;

                if ($status) {
                    return '<span class="label label-success">Rewarded</span>';
                } else {
                    return '<span class="label label-danger">Voided</span>';
                }
            })
            ->make();
    }

    public function requestResponse()
    {
        $id          = Input::get('id');
        $transaction = BcardTransaction::find($id);

        if ($transaction) {
            $request  = json_decode($transaction->request);
            $response = json_decode($transaction->response);
            $html     = '<h4>Request</h4>';
            $html .= '<table class="table" style="width: 100%;">';

            foreach ($request as $key => $value) {
                $html .= "<tr><td class=\"col-xs-6\">{$key}</td><td class=\"col-xs-6\">{$value}</td></tr>";
            }

            $html .= '</table>';
            $html .= '<h4>Response</h4>';
            $html .= '<table class="table" style="width: 100%;">';

            foreach ($response as $key => $value) {
                $html .= "<tr><td class=\"col-xs-6\">{$key}</td><td class=\"col-xs-6\">{$value}</td></tr>";
            }

            $html .= '</table>';

            return $html;
        }
    }

    public function void()
    {
        $id               = Input::get('void-id');
        $bcardTransaction = BcardTransaction::find($id);

        if ( ! $bcardTransaction) {
            Session::flash('error', 'Transaction not found.');
            return Redirect::to('points/bcard');
        }

        $request = json_decode(object_get($bcardTransaction, 'request'));
        $config  = array_merge(Config::get('points.bcard'), [
            'Card'        => $request->Card,
            'TranxDate'   => $request->TranxDate,
            'BillNo'      => $request->BillNo,
            'TotalAmount' => $request->TotalAmount,
            'TotalPoint'  => $request->TotalPoint,
        ]);
        $response = Bcard::api('VoidReward', $config);

        if (object_get($response, 'status', 0) > 0) {
            DB::table('bcard_voids')->insert([
                'action'     => 'Void',
                'api'        => 'VoidReward',
                'point'      => $request->TotalPoint,
                'request'    => object_get($bcardTransaction, 'request'),
                'response'   => json_encode($response),
                'created_at' => date('Y-m-d H:i:s'),
                'reward_id'  => object_get(json_decode(object_get($bcardTransaction, 'response')), 'RewardID'),
                'bill_no'    => $request->BillNo,
            ]);

            Session::flash('success', 'Reward voided successfully.');
        } else {
            Session::flash('error', 'Void reward failed.');
        }

        return Redirect::to('points/bcard');
    }
    
    
    public function rewardDouble($transaction_id,$userid){
        
        // Only For JPOINT
        $PointUser = PointUser::where('user_id',$userid)
        ->where('point_type_id',1)->first();

        $PointTransaction = PointTransaction::where('point_user_id',$PointUser->id)
            ->where('transaction_id',$transaction_id)
            ->first();
            

        if($PointTransaction){
            
            $pointsBase = $PointTransaction->point;
            $PointTransaction->point = $pointsBase * 2;
            $PointTransaction->balance = $PointTransaction->balance + $pointsBase;
            $PointTransaction->save();
            
            // DB::table('jocom_points_history')
            //     ->where('user_id', $userid)
            //     ->where('point_type', 1) // Only For JPOINT
            //     ->where('transaction_id', $transaction_id) 
            //     ->update(['points' => $PointTransaction->point]);

            $PointUser->point = $PointUser->point + $pointsBase;
            $PointUser->save();

        }

    }
    
    
    public function rewardBigPoint($transaction_id,$userid,$totalPoint,$amount){
        
        // Only For JPOINT
        $PointUser = PointUser::where('user_id',$userid)
        ->where('point_type_id',1)->first();

        $Transaction = Transaction::find($transaction_id);

        $PointTransaction = PointTransaction::where('point_user_id',$PointUser->id)
            ->where('transaction_id',$transaction_id)
            ->first();

        if($PointTransaction){
            
            if($Transaction->total_amount >= $amount){

                $pointsBase = $PointTransaction->point;
                $PointTransaction->point = $pointsBase + $totalPoint;
                $PointTransaction->balance = $PointTransaction->balance + $totalPoint;
                $PointTransaction->save();
            
                $PointUser->point = $PointUser->point + $totalPoint;
                $PointUser->save();

            }
            

        }

    }

}
