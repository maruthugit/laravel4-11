<?php

class TestController extends BaseController
{
    private $callback;

    private $class;

    private $method;

    public function __construct()
    {
        if (App::environment('local')) {
            $this->class    = Input::get('class');
            $this->method   = Input::get('method');
            $this->callback = [$this->class, $this->method];
        } else {
            App::abort(404);
        }
    }

    public function anyBcard()
    {
        $api = Input::get('api');

        switch ($api) {
            case 'PointInquiry':
                $result = Bcard::api($api, [
                    'WSKey'         => '531272477',
                    'WSCompanyCode' => 'B0115',
                    'WSBranchCode'  => 'J01',
                    'Card'          => '6298430000005477',
                    'Password'      => '111111',
                ]);
                break;
            case 'Reward':
            case 'VoidReward':
                $amount = Input::get('amount', 10.05);
                $billNo = Input::get('billno',
                    'TEST'.str_pad(rand(1, 999999), '0'));
                $result = Bcard::api($api, [
                    'WSKey'         => '531272477',
                    'WSCompanyCode' => 'B0115',
                    'WSBranchCode'  => 'J01',
                    'Card'          => '6298430000005477',
                    'POSID'         => '',
                    'TranxDate'     => date('Y-m-d\TH:i:s'),
                    'BillNo'        => $billNo,
                    'TotalAmount'   => $amount,
                    'TotalPoint'    => floor($amount),
                ]);
                break;
            case 'Redemption':
                $point  = Input::get('point', 5);
                $result = Bcard::api($api, [
                    'WSKey'         => '531272477',
                    'WSCompanyCode' => 'B0115',
                    'WSBranchCode'  => 'J01',
                    'Card'          => '6298430000005477',
                    'Password'      => '111111',
                    'POSID'         => '',
                    'TranxDate'     => date('Y-m-d\TH:i:s'),
                    'Point'         => $point,
                ]);
                break;
            case 'ReverseRedemption':
            case 'VoidRedemption':
                $point  = Input::get('point', 5);
                $result = Bcard::api($api, [
                    'WSKey'         => '531272477',
                    'WSCompanyCode' => 'B0115',
                    'WSBranchCode'  => 'J01',
                    'Card'          => '6298430000005477',
                    'POSID'         => '',
                    'TranxDate'     => date('Y-m-d\TH:i:s'),
                    'Point'         => $point,
                ]);
                break;
        }

        if (isset($result)) {
            $object = json_encode($result);

            if ($object != false) {
                echo $object; // Success
            } else {
                echo $object; // Failed
            }
        }
    }

    public function anyIndex()
    {
        $params = Input::except('class', 'method');

        var_dump(call_user_func_array($this->callback, $params));
    }

    public function anyPoint()
    {
        $userId     = Input::get('user_id');
        $pointType  = Input::get('point_type', PointType::JOPOINT);
        $activeOnly = Input::get('active_only', false);
        $params     = Input::except('class', 'method', 'user_id', 'point_type', 'active_only');
        $pointUser  = PointUser::getOrCreate($userId, $pointType, $activeOnly);
        $object     = new $this->class($pointUser);

        var_dump(call_user_func_array([$object, $this->method], $params));
    }

    public function anyPointuser()
    {
        $userId    = Input::get('user_id');
        $pointType = Input::get('point_type', PointType::JOPOINT);

        var_dump(PointUser::getPoint($userId, $pointType));
    }

    public function getPhpinfo()
    {
        echo phpinfo();
    }

    public function getEmailprogress()
    {
        return View::make('emails.progress');
    }
}
