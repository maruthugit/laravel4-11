<?php

class MapsController extends BaseController
{
    private $method, $google_map_api_key;

    public function __construct() {
        $this->google_map_api_key = "AIzaSyDcTN4TPOfZRUmCF_7S_4w3sFlxaGEr3f4"; //"AIzaSyDUFmbwJMBHU_paeMfVO7oqPC1IJEtbJUU";
    }

    public function getDriverGpsView()
    {
        return View::make('maps.drivers-gps');
    }

    public function getDrivers()
    {
        $query = Input::get('query');
        $data = [];
        $data = DB::table('logistic_driver')->where('status', '=', 1)->get(['id', 'name', 'contact_no', 'type', 'status']);
        return Response::json($data);
    }

    public function getDriverLocationsData() {
        $id = Input::get('user');
        $selected_date = Input::get('s_date');

        $requested_date_start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $selected_date.' 00:00:00')->toDateTimeString();
        $requested_date_end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $selected_date.' 23:59:59')->toDateTimeString();

        $dataCollection = DB::table('jocom_trackingsignal')
            ->where('driverid', '=', $id)
            ->where('created_date', '>=', $requested_date_start)
            ->where('created_date', '<=', $requested_date_end)
            // ->take(25)
            ->orderBy('created_date', 'asc')
            ->get(['id', 'driverid', 'latitude as lat', 'longitude as lng', 'created_date']);

        $driverData = DB::table('logistic_driver')->where('id', '=', $id)->first(['name', 'contact_no', 'type', 'status']);

        $markData = [];

        foreach ($dataCollection as $k => $v) {
            $markData[$k]['id'] = $v->id;
            $markData[$k]['driver_id'] = $v->driverid;
            $markData[$k]['updated_at'] = $v->created_date;
            $markData[$k]['position'] = (object) array('lat' => (double)$v->lat, 'lng' => (double)$v->lng);
            $markData[$k]['location_info'] = $this->locationDetails((double)$v->lat, (double)$v->lng);
        }

        $pathData = [];
        foreach ($markData as $k => $value) {
            $pathData[$k] = $value['position'];
        }

        $data = [];
        $data['diverInfo'] = $driverData;
        $data['markers'] = $markData;
        $data['paths'] = $pathData;

        return Response::json($data);
    }

    private function locationDetails($lat, $lng) {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=true&key=".$this->google_map_api_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $manage = json_decode($response, true);

        foreach ($manage['results'] as $key => $value) {
            return $value;
        }
    }
    
    public function routePlanner()
    {
        return View::make('maps.route-planner');
    }

    public function getRoute($driver_id) 
    {

        $warehouse_lat = 3.2504489;
        $warehouse_lng = 101.649567;

        $warehouse_coordinate = ['lat' => $warehouse_lat, 'lng' => $warehouse_lng];

        $data = [];

        $batchs = DB::table('logistic_batch')
                    ->join('logistic_transaction', 'logistic_batch.logistic_id', '=', 'logistic_transaction.id')
                    ->where('driver_id', $driver_id)
                    ->where(function($query) {
                        $query->where('logistic_batch.status', '=', 0)
                              ->orWhere('logistic_batch.status', '=', 1);
                    })
                    ->orderBy('logistic_batch.id')
                    ->get();

        $driverData = DB::table('logistic_driver')->where('id', '=', $driver_id)->first(['name', 'contact_no', 'type', 'status']);

        if (count($batchs) == 0) {
            $data['diverInfo'] = $driverData;
            $data['markers'] = [];
            $data['paths'] = [];

            return Response::json($data);
        }

        $markers = [];
        $waypoints = [];

        foreach ($batchs as $index => $batch) {

            if ($batch->gps_latitude != null && $batch->gps_longitude != null) {

                array_push($waypoints, ['lat' => $batch->gps_latitude, 'lng' => $batch->gps_longitude]);

            } else {
                $street = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$batch->delivery_addr_1));
                $route = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$batch->delivery_addr_2));
                $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_city));
                $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_state));
                $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_country));
                $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_postcode));

                $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$street.",+".$route.",+".$city."+".$postcode.",".$country."&key=".$this->google_map_api_key;
                // print $URL;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL,$URL );
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $output = curl_exec($ch);
                curl_close($ch);
              
                $Mapvalue = json_decode($output);


                if($Mapvalue->status=="ZERO_RESULTS" || $Mapvalue == null){

                    $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$city."+".$postcode.",".$country."&key=".$this->google_map_api_key;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_URL,$URL );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $output = curl_exec($ch);
                    curl_close($ch);
                }            

                $ArrayMap = json_decode($output) ;

                $latitude = $ArrayMap->results[0]->geometry->location->lat;
                $longitute = $ArrayMap->results[0]->geometry->location->lng;

                LogisticTransaction::where('id', $batch->logistic_id)
                                   ->update(['gps_latitude' => $latitude, 'gps_longitude' => $longitute]);

                array_push($waypoints, ['lat' => $latitude, 'lng' => $longitude]);                
            }
            
        }


        $optimized_waypoints = $this->optimizeWaypoint($warehouse_coordinate, $warehouse_coordinate, $waypoints);
        if (!$optimized_waypoints) {
            return Response::json(['error' => 1]);
        }

        $waypoint_string = "";

        foreach ($optimized_waypoints as $index => $waypoint) {
            $markers[$index]['position'] = (object) array('lat' => (double)$waypoint['lat'], 'lng' => (double)$waypoint['lng']);
            $markers[$index]['location_info'] = $this->locationDetails((double)$waypoint['lat'], (double)$waypoint['lng']);
            $waypoint_string .= "&waypoint{$index}={$waypoint['lat']},{$waypoint['lng']}";
        }

        $url = "https://route.ls.hereapi.com/routing/7.2/calculateroute.json?apiKey=mRg2CeeQXI9w2lLLqaJ4xeMG0cImjUCAB4qfLMzITAM&mode=fastest;car{$waypoint_string}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);

        $legs = $decoded['response']['route'][0]['leg'];

        $paths = [];

        foreach ($legs as $index => $leg) {
            $markers[$index + 1]['distance'] = number_format((double)($leg['length']) / 1000, 2);
            $markers[$index + 1]['duration'] = $this->secondsToTime($leg['travelTime']);

            $maneuvers = $leg['maneuver'];
            foreach ($maneuvers as $maneuver) {
                array_push($paths, (object) array('lat' => (double)$maneuver['position']['latitude'], 
                                                  'lng' => (double)$maneuver['position']['longitude']));
            }
        }

        $summary = $decoded['response']['route'][0]['summary'];
        $total_distance = $summary['distance'];
        $total_duration = $summary['baseTime'];
        $total_duration_with_traffic = $summary['trafficTime'];

        $data['diverInfo'] = $driverData;
        $data['markers'] = $markers;
        $data['paths'] = $paths;
        $data['totalDistance'] = number_format((double)$total_distance / 1000, 1);
        $data['totalDuration'] = $this->secondsToTime($total_duration);
        $data['totalDurationWithTraffic'] = $this->secondsToTime($total_duration_with_traffic);

        return Response::json($data);
    }

    public function routePlannerDriver()
    {   
        if (Input::has('driver_id')) {
            return View::make('maps.route-planner-driver')->with(['driver_id' => Input::get('driver_id')]);
        }

        $drivers = DB::table('logistic_driver')->where('status', '=', 1)->get(['id', 'name', 'contact_no', 'type', 'status']);
        return View::make('maps.route-planner-driver')->with(['drivers' => $drivers]);
        
    }

    public function getRouteDriver() 
    {

        $driver_id = Input::get('driver_id');
        $current_lat = Input::get('lat');
        $current_lng = Input::get('lng');

        $warehouse_lat = 3.2504489;
        $warehouse_lng = 101.649567;

        $start_coordinate = ['lat' => $current_lat, 'lng' => $current_lng];
        $warehouse_coordinate = ['lat' => $warehouse_lat, 'lng' => $warehouse_lng];

        $data = [];

        $batchs = DB::table('logistic_batch')
                    ->join('logistic_transaction', 'logistic_batch.logistic_id', '=', 'logistic_transaction.id')
                    ->where('driver_id', $driver_id)
                    ->where(function($query) {
                        $query->where('logistic_batch.status', '=', 0)
                              ->orWhere('logistic_batch.status', '=', 1);
                    })
                    ->orderBy('logistic_batch.id')
                    ->get();

        $driverData = DB::table('logistic_driver')->where('id', '=', $driver_id)->first(['name', 'contact_no', 'type', 'status']);

        if (count($batchs) == 0) {
            $data['diverInfo'] = $driverData;
            $data['markers'] = [];
            $data['paths'] = [];

            return Response::json($data);
        }

        $markers = [];
        $waypoints = [];

        foreach ($batchs as $index => $batch) {

            if ($batch->gps_latitude != null && $batch->gps_longitude != null) {

                array_push($waypoints, ['lat' => $batch->gps_latitude, 'lng' => $batch->gps_longitude]);

            } else {
                $street = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$batch->delivery_addr_1));
                $route = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$batch->delivery_addr_2));
                $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_city));
                $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_state));
                $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_country));
                $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$batch->delivery_postcode));

                $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$street.",+".$route.",+".$city."+".$postcode.",".$country."&key=".$this->google_map_api_key;
                // print $URL;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL,$URL );
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $output = curl_exec($ch);
                curl_close($ch);
              
                $Mapvalue = json_decode($output);


                if($Mapvalue->status=="ZERO_RESULTS" || $Mapvalue == null){

                    $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$city."+".$postcode.",".$country."&key=".$this->google_map_api_key;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_URL,$URL );
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $output = curl_exec($ch);
                    curl_close($ch);
                }            

                $ArrayMap = json_decode($output) ;

                $latitude = $ArrayMap->results[0]->geometry->location->lat;
                $longitute = $ArrayMap->results[0]->geometry->location->lng;

                LogisticTransaction::where('id', $batch->logistic_id)
                                   ->update(['gps_latitude' => $latitude, 'gps_longitude' => $longitute]);

                array_push($waypoints, ['lat' => $latitude, 'lng' => $longitude]);                
            }
            
        }


        $optimized_waypoints = $this->optimizeWaypoint($start_coordinate, $warehouse_coordinate, $waypoints);
        if (!$optimized_waypoints) {
            return Response::json(['error' => 1]);
        }

        $waypoint_string = "";

        foreach ($optimized_waypoints as $index => $waypoint) {
            $markers[$index]['position'] = (object) array('lat' => (double)$waypoint['lat'], 'lng' => (double)$waypoint['lng']);
            $markers[$index]['location_info'] = $this->locationDetails((double)$waypoint['lat'], (double)$waypoint['lng']);
            $waypoint_string .= "&waypoint{$index}={$waypoint['lat']},{$waypoint['lng']}";
        }

        $url = "https://route.ls.hereapi.com/routing/7.2/calculateroute.json?apiKey=mRg2CeeQXI9w2lLLqaJ4xeMG0cImjUCAB4qfLMzITAM&mode=fastest;car{$waypoint_string}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);

        $legs = $decoded['response']['route'][0]['leg'];

        $paths = [];

        foreach ($legs as $index => $leg) {
            $markers[$index + 1]['distance'] = number_format((double)($leg['length']) / 1000, 2);
            $markers[$index + 1]['duration'] = $this->secondsToTime($leg['travelTime']);

            $maneuvers = $leg['maneuver'];
            foreach ($maneuvers as $maneuver) {
                array_push($paths, (object) array('lat' => (double)$maneuver['position']['latitude'], 
                                                  'lng' => (double)$maneuver['position']['longitude']));
            }
        }

        $summary = $decoded['response']['route'][0]['summary'];
        $total_distance = $summary['distance'];
        $total_duration = $summary['baseTime'];
        $total_duration_with_traffic = $summary['trafficTime'];

        $data['diverInfo'] = $driverData;
        $data['markers'] = $markers;
        $data['paths'] = $paths;
        $data['totalDistance'] = number_format((double)$total_distance / 1000, 1);
        $data['totalDuration'] = $this->secondsToTime($total_duration);
        $data['totalDurationWithTraffic'] = $this->secondsToTime($total_duration_with_traffic);

        return Response::json($data);
    }

    private function secondsToTime($inputSeconds) {
        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // Extract days
        $days = floor($inputSeconds / $secondsInADay);

        // Extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // Extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // Extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            // 'second' => (int)$seconds,
        ];

        foreach ($sections as $name => $value){
            if ($value > 0){
                $timeParts[] = $value. ' '.$name.($value == 1 ? '' : 's');
            }
        }

        return implode(' ', $timeParts);
    }

    private function optimizeWaypoint($start, $end, $waypoints) 
    {
        $waypoint_string = '';
        $count = 1;

        foreach ($waypoints as $waypoint) {
            $waypoint_string .= "destination{$count}={$waypoint['lat']},{$waypoint['lng']}&";
            $count++;
        }

        $url = "https://wse.ls.hereapi.com/2/findsequence.json?apiKey=mRg2CeeQXI9w2lLLqaJ4xeMG0cImjUCAB4qfLMzITAM&mode=fastest;car&start={$start['lat']},{$start['lng']}&{$waypoint_string}end={$end['lat']},{$end['lng']}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);
        
        if ($decoded['errors']) {
            return false;
        }

        $decoded_waypoints = $decoded['results'][0]['waypoints'];

        $optimized_waypoints = [];
        foreach ($decoded_waypoints as $waypoint) {
            array_push($optimized_waypoints, [
                'lat' => $waypoint['lat'],
                'lng' => $waypoint['lng']
            ]);
        }

        return $optimized_waypoints;
    }
}
