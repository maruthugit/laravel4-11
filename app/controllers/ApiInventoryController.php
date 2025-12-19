<?php

class ApiInventoryController extends BaseController {
    public function __construct(){


    }

    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyLogin()
    {
        $data       = array();
        $data['enc']= 'UTF-8';
        $data['lang']= 'EN';

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $get['username'] = trim(Input::get('user'));
        $get['password'] = trim(Input::get('pass'));
        $get['ip']       = Request::getClientIp();
        $get['date']     = date('Y-m-d H:i:s');

        $userdata = array('xml_data' =>  User::verify_login($get));

        $data   = array_merge($data, $userdata);

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');

    }

    public function anyUpdate()
    {
        $api        = new ApiInventory;
        $data       = array();
        $get        = array();
        $data['enc']= 'UTF-8';

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }
        // $api->MemberForgot(Input::all());
        $data = array_merge($data, $api->UpdateQuantity(Input::all()));
        // var_dump($data);

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');

    }

    public function anyHistory()
    {
        $api        = new ApiInventory;
        $limit      = Input::get('count', 50);
        $offset     = Input::get('from', 0);
        $data       = array();
        $get        = array();
        $data['enc']= 'UTF-8';

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }
        // $api->MemberForgot(Input::all());
        $data = array_merge($data, $api->getHistory($limit, $offset, Input::all()));
        // var_dump($data);

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');

    }
}
?>
