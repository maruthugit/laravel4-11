<?php
class ApiV2LocationController extends BaseController {
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
        $get['lang']= 'EN';
        $req        = trim(Input::get('req'));

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


        return Response::json($data);
    }

    public function anyState()
    {
        $get        = array();
        $get['lang']= 'EN';

        $count      = Input::get('count');
        $from       = Input::get('from');
        $get['code']= trim(Input::get('code'));

        $data   = ApiLocation::GetStates($count, $from, $get);

        return Response::json($data);
    }

    public function anyCity()
    {
        $get        = array();
        $get['lang']= 'EN';

        $count      = Input::get('count');
        $from       = Input::get('from');
        $get['code']= trim(Input::get('code'));

        $data   = ApiLocation::GetCities($count, $from, $get);

        return Response::json($data);
                    
    }
}

?>