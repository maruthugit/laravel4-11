<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiCharity extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public static function getLogin($get=array())
    {
        $count      = ApiCharity::CheckFailedAttempt($get['username'], $get['ip']);

        // $queries = DB::getQueryLog();
        // $last_query         = end($queries);
        // $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
        // $query['statement'] = vsprintf($tempquery, $last_query['bindings']);

        // return $query['statement'];

        // return $count;

        if($count < 5 )
        {
            $valid = 0;

            $cust   = DB::table('jocom_charity_users')
                        ->where('username', '=', $get['username'])
                        ->first();

            if(count($cust)>0)
            {
                if(Hash::check($get['password'], $cust->password)) $valid = 1;
                if($cust->status != 1) $valid = 2;
            }

            if($valid == 1)
            {
                $last_login = date('Y-m-d H:i:s');

                $data['role_id'] = $cust->role_id;
                $data['username'] = $cust->username;
                $data['user_id'] = $cust->id;
                $data['charity_id'] = $cust->charity_id;
                $data['last_login'] = $last_login;

                // Session::put('role_id', $cust->role_id);
                // Session::put('username', $cust->username);
                // Session::put('user_id', $cust->id);
                // Session::put('charity_id', $cust->charity_id);
                // Session::put('last_login', $last_login);

                $cust2  = DB::table('jocom_charity_users')
                        ->where('username', '=', $get['username'])
                        ->update(array('last_login' => $last_login));

                $add = ApiCharity::add_attempt($cust->username, $get['ip'], $last_login, 1);
                $data['success'] = 'You have logged in successfully.';
            }
            elseif($valid == 2)
            {
                $data['message'] = 'You are not allowed to access!';
            }
            else
            {
                $add = ApiCharity::add_attempt($cust->username, $get['ip'], date('Y-m-d H:i:s'), 0);
                $data['message'] = 'Login attempt '. $count .': The username/password combo does not exist.';
            }            
        }
        else
        {
            $data['message'] = 'You have login for more than 5 times. Please try again after 30 minutes.';
        }

        return $data;
    }

    public function CheckFailedAttempt($username, $ip)
    {
        $count          = 0;
        $date           = new DateTime;
        $date->modify('-30 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $attempts   = DB::table('jocom_charity_attempts')
                        ->where('username', '=', $username)
                        ->where('ip_address', '=', $ip)
                        ->where('attempt_at' ,'>=', $formatted_date)
                        ->where('status', '=', '0')
                        ->get();

        $count = count($attempts);

        return $count;
    }

    public function add_attempt($username, $ip, $date, $status)
    {
        DB::table('jocom_charity_attempts')->insert(
            array('username' => $username, 'ip_address' => $ip, 'attempt_at' => $date, 'status' => $status)
        );

        return;

    }

    public static function getDashboard($get=array())
    {
        // total value
        $e          = strtotime("first day of this month");
        $e2         = strtotime("last day of this month");
        $start      = date('Y-m-d', $e);
        $end        = date('Y-m-d', $e2);

        $trans = DB::table('jocom_transaction')
            ->select(DB::raw('SUM(total_amount) AS total, SUM(delivery_charges) AS delivery, SUM(process_fees) AS process, SUM(gst_total) AS gst, SUM(gst_process) AS gst_process, SUM(gst_delivery) AS gst_delivery'))
            ->where('status', '=', 'completed')
            ->where('charity_id', $get['charity_id'])
            ->whereBetween('transaction_date', array($start, $end." 23:59:59"))
            ->first();

        if (count($trans)>0)
        {
            $total          = round($trans->total,2);
            $delivery       = $trans->delivery;
            $process        = $trans->process;
            $gst            = $trans->gst;
            $gst_delivery   = round($trans->gst_delivery,2);
            $gst_process    = round($trans->gst_process,2);
        }

        // echo "Total:".$total." Delivery:".$delivery." Process:".$process." GST:".$gst." GST Deliver:".$gst_delivery." GST Process:".$gst_process;

        $coupon = DB::table('jocom_transaction AS a')
        ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
        ->where('a.status', '=', 'completed')
        ->where('a.charity_id', $get['charity_id'])
        ->sum('b.coupon_amount');

        $point = DB::table('jocom_transaction AS a')
        ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
        ->where('a.status', '=', 'completed')
        ->where('b.status', '=', '1')
        ->where('a.charity_id', $get['charity_id'])
        ->sum('b.amount');

        $refund = DB::table('jocom_refund as a')
                    ->leftJoin('jocom_transaction AS b','b.id', '=', 'a.trans_id')
                    ->where('a.status', '=', 'confirmed')
                    ->where('b.charity_id', $get['charity_id'])
                    ->sum('a.amount');

        // $data['amount'] = round($total - $delivery - $process - $coupon - $point + $gst - $refund, 2);
        $data['amount'] = round($total - $delivery - $process - $gst_delivery - $gst_process - $coupon - $point + $gst - $refund, 2);


        // latest transaction
        $latest = Transaction::where('status', '=', 'completed')->where('charity_id', $get['charity_id'])->orderBy('id', 'DESC')->first();

        if (count($latest)>0)
        {
            $coupon = DB::table('jocom_transaction AS a')
            ->select('b.coupon_amount')
            ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
            ->where('a.id', '=', $latest->id)
            ->first();

            $point = DB::table('jocom_transaction AS a')
            ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
            ->where('a.id', '=', $latest->id)
            ->where('b.status', '=', '1')
            ->sum('b.amount');

            $data['latest'] = $latest->total_amount - $latest->delivery_charges - $latest->process_fees - $latest->gst_process - $latest->gst_delivery - $coupon->coupon_amount - $point + $latest->gst_total;
        }
        else
        {
            $data['latest'] = 0;
        }

        // total donor
        $donor = DB::table('jocom_transaction')
            ->select(DB::raw(" buyer_username "))
            ->where('status', '=', 'completed')
            ->where('charity_id', $get['charity_id'])
            ->groupBy('buyer_username')
            ->get();

        if (count($donor)>0)        
            $data['donor'] = count($donor);
        else
            $data['donor'] = 0;


        // total products received
        $product = DB::table('jocom_transaction as a')
            ->select(DB::raw("ROUND( SUM( b.unit ), 0) AS unit"))
            ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
            ->where('a.status', '=', 'completed')
            ->where('a.charity_id', $get['charity_id'])
            ->first();

        if (count($product)>0)        
            $data['product'] = $product->unit;
        else
            $data['product'] = 0;        

        return $data;
    }

    public static function getDonation($get=array())
    {
        $order = "unit";
        $by = "desc";

        if ($get['transaction_from'] == "")
        {
            $e          = strtotime("first day of this month");
            $e2         = strtotime("last day of this month");
            $start      = date('Y-m-d', $e);
            $end        = date('Y-m-d', $e2);

            $get['transaction_from']   = $start;
            $get['transaction_to']     = $end;
        }


        // $transaction = Transaction::where('charity_id', $get['charity_id'])->where('status', 'completed')->get();

        $transaction = DB::table('jocom_transaction AS a')
                ->select(DB::raw("b.sku as sku, b.product_id AS product_id, c.name AS product_name, b.p_option_id AS label_id, b.price_label AS product_label, ROUND( SUM( b.unit ), 0) AS unit, b.price AS price, ROUND( SUM( b.total ), 2) AS total, ROUND( SUM( b.gst_amount ), 2) AS gst"))
                ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                ->where('a.status', '=', 'completed')
                ->where('b.product_id', '<>', '0')
                ->whereRaw('c.name IS NOT NULL')
                ->where('a.charity_id', $get['charity_id'])
                ->whereBetween('a.transaction_date', array($get['transaction_from'],$get['transaction_to']." 23:59:59"))
                ->groupBy('b.p_option_id')
                ->orderBy($order, $by)
                ->get();

        if (count($transaction)>0)
        {
            foreach ($transaction as $trans)
            {
                $item['sku']            = $trans->sku;
                $item['product_name']   = $trans->product_name;
                $item['product_label']  = $trans->product_label;
                $item['unit']           = $trans->unit;
                $item['price']          = $trans->price;
                $item['gst']            = $trans->gst;
                $item['total']          = $trans->total+$trans->gst;

                $data['item'][] = $item;

                // $data['sku'][] = $trans->sku;
                // $data['product_name'][] = $trans->product_name;

            }
        }
        else
        {
            $data['message'] = 'No record';
        }

        return $data;
    }

    public static function getProduct($get=array())
    {
        $charityProducts = Product::select([
            'jocom_charity_product.id as id',
            'jocom_products.name as name',
            'jocom_products.sku as sku',
            'jocom_product_price.label as label',
            'jocom_charity_product.qty as qty',
            'jocom_charity_product.quota as quota',
        ])
            ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->leftJoin('jocom_charity_product', 'jocom_product_price.id', '=', 'jocom_charity_product.product_price_id')
            ->where('jocom_charity_product.charity_id', '=', $get['charity_id'])
            ->get();

        if (count($charityProducts)>0)
        {
            foreach ($charityProducts as $product)
            {
                $item['sku']            = $product->sku;
                $item['product_name']   = $product->name;
                $item['product_label']  = $product->label;
                $item['qty']            = $product->qty;
                $item['quota']          = $product->quota;
                $item['action']         = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal" data-id="'.$product->id.'" data-qty="'.$product->qty.'" data-quota="'.$product->quota.'" data-remark="" >Update</button>';
                // $item['action']         = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="product/edit/'.$product->id.'"><i class="fa fa-pencil"></i></a>';

                $data['item'][] = $item;

            }
        }
        else
        {
            $data['message'] = 'No record';
        }

        return $data;
    }

    public static function updateProduct($get=array())
    {
        $charityProductId = $get['charityProductId'];
        $charityProduct   = CharityProduct::find($charityProductId);
        $quantity         = $get['qty'];
        $quota            = $get['quota'];

        if ($quantity >= 0) {
            $charityProduct->qty = $quantity;
        }

        if ($quota > 0) {
            $charityProduct->quota = $quota;
        }

        $charityProduct->save();

        $data['message'] = '1';

        return $data;
    }

    public static function getProfile($get=array())
    {
        $user = CharityUser::where('username', $get['username'])->first();

        if (count($user)>0)
        {
            $temp['username']         = $user->username;
            $temp['email']            = $user->email;
            $temp['full_name']        = $user->full_name;
            $temp['contact_no']       = $user->contact_no;

            $data['user'] = $temp;            
        }
        else
        {
            $data['message'] = 'No record';
        }

        return $data;
    }


    public static function updateProfile($get=array())
    {
        $arr_validate   = array();
        $arr_input      = array();

        $user = CharityUser::where('username', $get['username'])->first();
        $id = $user->id;

        $temp['username']         = $user->username;
        $temp['email']            = $user->email;
        $temp['full_name']        = $user->full_name;
        $temp['contact_no']       = $user->contact_no;

        $data['user'] = $temp;

        Session::put('username', "[API] ".$get['username']);
        
        $arr_input_user['username']             = $get['username'];
        $arr_input_user['password']             = $get['password'];
        $arr_input_user['password_confirmation']= $get['password_confirmation'];
        $arr_input_user['full_name']            = $get['full_name'];
        $arr_input_user['email']                = $get['email'];
        $arr_input_user['contact_no']           = $get['contact_no'];

        $arr_validate   = CharityUser::getUpdateRules($arr_input_user);
        $arr_input      = CharityUser::getUpdateInputs($arr_input_user);
        $arr_udata      = CharityUser::getUpdateDbDetails($arr_input);
        $validator      = Validator::make($arr_input, $arr_validate);

        if ($validator->passes())
        {
            if(CharityUser::updateUser($id, $arr_udata))
            {
                $temp['username']         = $get['username'];
                $temp['email']            = $get['email'];
                $temp['full_name']        = $get['full_name'];
                $temp['contact_no']       = $get['contact_no'];

                $data['user'] = $temp;
                $data['message'] = '1';
            }
        }
        else
        {
            $data['message'] = '0';
        }

        return $data;
    }

}
?>