<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiLocation extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    protected $table = 'jocom_countries';


    public function AllCountries() 
    {
    	$countries = DB::table('jocom_countries')
                            ->select('*')
                            ->orderByRaw("FIELD(name, 'Malaysia') DESC")
                            ->orderBy('name', 'ASC')
                            ->get();
            
        $data['timestamp'] 	= $timestamp;
        $data['record'] 	= count($countries);
        $data['state'] 		= 'NO';
        $data['item'] 		= array();

        foreach ($countries as $country) {
            $data['item'][] = array(
                'id' 	=> $country->id,
                'name' 	=> $country->name,
            );
        }
        return array('xml_data' => $data);
    }


    public function DeliveryCountries($input=array())
    {
    	$countries = DB::table('jocom_countries')
    						->where('status', '=', '1')
                            ->orderByRaw("FIELD(name, 'Malaysia') DESC")
    						->orderBy('name', 'ASC')
    						->get();

    	$data['timestamp'] 	= $timestamp;
        $data['record'] 	= count($countries);
        $data['state'] 		= 'NO';
        $data['item'] 		= array();

        foreach ($countries as $country) {
        	$data['item'][] = array(
        			'id'	=> $country->id,
        			'name'	=> $country->name,
        	);
        }

        return array('xml_data' => $data);
	}

	public function GetStates($count='', $from='', $get=array())
	{
		$country_id = $get['code'];

		$states = DB::table('jocom_country_states as state')
						->select('state.id', 'state.name')
						->where('state.country_id', '=', $country_id)
						->where('state.status', '=', 1)
						->orderBy('state.name', 'ASC')
						->get();

    	$data['timestamp'] 	= $timestamp;
        $data['record'] 	= count($states);
        // $data['state'] 		= 'YES';
        $data['item'] 		= array();

        if (count($states) > 0) {
        	foreach ($states as $state) {
	        	$data['item'][] = array(
	        			'id'	=> $state->id,
	        			'name'	=> $state->name,
	        	);
	        }

        } else $data['status_msg'] = '#101';
        // else $data['error'] = 'Sorry. No data found!';  

        return array('xml_data' => $data);
	}

	public function GetCities($count='', $from='', $get=array())
	{
		$state_id = $get['code'];

		$cities = DB::table('jocom_cities as city')
						->select('city.id', 'city.name')
						->leftjoin('jocom_country_states as state', 'state.id', '=', 'city.state_id')
						->where('city.state_id', '=', $state_id)
						->orderBy('city.name', 'ASC')
						->get();

    	$data['timestamp'] 	= $timestamp;
        $data['record'] 	= count($cities);
        // $data['state'] 		= 'YES';
        $data['item'] 		= array();

        if (count($cities) > 0) {
        	foreach ($cities as $city) {
	        	$data['item'][] = array(
	        			'id'	=> $city->id,
	        			'name'	=> $city->name,
	        	);
	        }

        } else $data['status_msg'] = '#101';
        // else $data['error'] = 'Sorry. No data found!';  

        return array('xml_data' => $data);
	}

	
}
?>