<?php 

class TrackingController extends BaseController
{

	/**
     * Default index for apps feed
     */

	public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }


    public function anySignal()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::createSignal(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public static function createSignal($input=array()){

    	$data = array();

    	$api 				= new Tracking;
		$api->driverid 		= Input::get('driverid');
		$api->latitude 		= Input::get('latitude');
		$api->longitude 	= Input::get('longitude');

		if($api->save()){
			

			$data  = array('driverid' 		=> $api->driverid,
						   'status_ins'		=> '1',	
				);
		} 
		else 
		{
			$data  = array('status_ins' 	=> '0',
					);	
		}
		return array('xml_data' => $data);


    }

    
    public function anyDevicevalidator(){

        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::checkDevice(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }


     public static function checkDevice($input=array()){

        $valid      = '0';
        $message    = 'Device ID does not match';

        $username=Input::get('username');
        $deviceid=Input::get('deviceid');


        $chkExists=DB::table('logistic_driver_device')
                ->where('username','=',$username)
                ->where('device_id','=',$deviceid)
                ->first();

        if(count($chkExists)>0)
        {
                $valid = '1'; 
                $message = 'Successfully Validated';
                

        }
        //  $valid = '1'; 
        $data = array('status'      => $valid, 
                      'status_msg'  => $message.count($chkExists),
                    );


       return array('xml_data' => $data);

    }






}

?>