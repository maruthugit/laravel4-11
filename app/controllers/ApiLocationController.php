<?php
class ApiLocationController extends BaseController {
    public function __construct(){
    }

    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyCountry()
    {
    	$data       = array();
        $get        = array();
        $data['enc']= 'UTF-8';
        $get['lang']= 'EN';
        $req    	= trim(Input::get('req'));

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

       switch($req) {
            case 'register':
                $data   = array_merge($data, ApiLocation::AllCountries());
                break;
            
            case 'delivery':
                $data   = array_merge($data, ApiLocation::DeliveryCountries(Input::all()));
                break;
            
            default:
                $tmpdata = array(
                    'status'    =>'0',
                    'status_msg'=>'#805'
                    // 'status_msg'=>'['.$req.']'.' Invalid request.'
                );
                $data = array_merge($data, array('xml_data' => $tmpdata));
                break;
        }


        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');
    }

    public function anyState()
    {
    	$data       = array();
        $get        = array();
        $data['enc']= 'UTF-8';
        $get['lang']= 'EN';

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $count  	= Input::get('count');
        $from   	= Input::get('from');
        $get['code']= trim(Input::get('code'));

        $data   = array_merge($data, ApiLocation::GetStates($count, $from, $get));

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');
    }

    public function anyCity()
    {
    	$data       = array();
        $get        = array();
        $data['enc']= 'UTF-8';
        $get['lang']= 'EN';

        if(Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $count  	= Input::get('count');
        $from   	= Input::get('from');
        $get['code']= trim(Input::get('code'));

        $data   = array_merge($data, ApiLocation::GetCities($count, $from, $get));

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');
    }
}

?>