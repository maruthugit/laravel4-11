<?php

class ZoneController extends BaseController {

    protected $zone;
    protected $country;

    public function __construct(Zone $zone, Country $country) {
        $this->zone = $zone;
        $this->country = $country;
    }
    
    /**
     * Display the zone page.
     *
     * @return Page
     */
    public function anyIndex() {
        return View::make('zone.index');
    }

    /**
     * Display a listing of the zone resource.
     *
     * @return Response
     */
    public function anyZones() {     
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
               
        $SysAdminRegion = DB::table('jocom_sys_admin_region AS JSAR' )
                ->leftJoin('jocom_region AS JR', 'JR.id', '=', 'JSAR.region_id')
                ->select('JSAR.*','JR.*')
                ->where("JSAR.status",1)
                ->where("JSAR.sys_admin_id",$sysAdminInfo->id)
                ->first();
        
        $countryID = $SysAdminRegion->country_id;
        
       
                                    
         if($SysAdminRegion->region_id != 0){
                 $zones = $this->zone->select(array(
                                        'jocom_zones.id', 
                                        'jocom_zones.name',
                                        'jocom_countries.name as country',
                                        'jocom_zones.status'
                                    ))
                                    ->leftJoin('jocom_countries', 'jocom_zones.country_id', '=', 'jocom_countries.id')
                                    ->where('jocom_zones.status', '!=', '2')
                                    ->where('jocom_zones.country_id', '=', $countryID);

            }else{
                 $zones = $this->zone->select(array(
                                        'jocom_zones.id', 
                                        'jocom_zones.name',
                                        'jocom_countries.name as country',
                                        'jocom_zones.status'
                                    ))
                                    ->leftJoin('jocom_countries', 'jocom_zones.country_id', '=', 'jocom_countries.id')
                                    ->where('jocom_zones.status', '!=', '2');
            }
                                    
                                    
        return Datatables::of($zones)
                                    // ->edit_column('full_name', ucwords({{$full_name}}))
                                    
                                   //Added by Maruthu
                                    ->edit_column('status',' 
                                        @if($status == 1)
                                            <p class="text-success">Active</p>
                                        @else
                                            <p class="text-danger">Inactive</p>
                                        @endif
                                    ') 
                                    ->add_column('Action', '<a class="btn btn-primary" title="Edit" data-toggle="tooltip" href="/zone/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                        @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 1, 9, \'AND\'))
                                            <a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" href="/zone/delete/{{$id}}"><i class="fa fa-remove"></i></a>
                                        @endif
                                      ')
                                    ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function anyCreate() {
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
               
        $SysAdminRegion = DB::table('jocom_sys_admin_region AS JSAR' )
      			->leftJoin('jocom_region AS JR', 'JR.id', '=', 'JSAR.region_id')
      			->select('JSAR.*','JR.*')
      			->where("JSAR.status",1)
                        ->where("JSAR.sys_admin_id",$sysAdminInfo->id)
                        ->first();
                
        if($SysAdminRegion->region_id != 0){
                $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
                $country_options = Country::where("id",$SysAdminRegion->country_id)->get();

            }else{
                $regions = Region::where("country_id",$country_id)
                    ->where("activation",1)->get();
                $country_options = Country::getActiveCountry();
            }
                
//        $country_options    = $this->country->where('status', '=', 1)->orderBy('id', 'asc')->get();
        
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND'))
            return View::make('zone.create', ['country_options' => $country_options]);
        else
            return View::make('home.denied', array('module' => 'Shipping > Add Zone'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $input      = Input::all();
        $states     = array();
        $cities     = array();
        $arr_city   = array();
        $arr_state  = array();

        if( !$this->zone->fill($input)->isValid() ) {
            return Redirect::back()->withInput()->withErrors($this->zone->errors);
        }
        else
        {
            if(Input::has('state')) {
                $states = Input::get('state');

                foreach ($states as $state) {
                    if(Input::has('city_'.$state)) {
                        $cities      = Input::get('city_'.$state);
                        $arr_state[] = $state;

                        foreach ($cities as $city) {
                            $tmp = explode('_', $city);
                            $arr_city[] = $tmp[1];
                        }
                    } 
                    else {
                        // echo "<br>NO City for State - ".$state;
                        Session::flash('err_message', 'Each State must consists of at least one(1) City.');
                        return Redirect::to('zone/create');
                    }

                }
            }

            if (count($arr_state) > 0) {
                $data['name']         = trim(Input::get('name'));
                $data['weight']       = trim(Input::get('weight'));
                $data['init_weight']  = null != Input::get('init_weight') ? trim(Input::get('init_weight')) : NULL;
                $data['init_price']   = null != Input::get('init_price') ? trim(Input::get('init_price')) : NULL;
                $data['add_weight']   = null != Input::get('add_weight') ? trim(Input::get('add_weight')) : NULL;
                $data['add_price']    = null != Input::get('add_price') ? trim(Input::get('add_price')) : NULL;
                $data['country_id']   = trim(Input::get('country'));
                $data['insert_by']    = Session::get('username');
                $data['insert_date']  = date('Y-m-d H:i:s');

                $zone_id = Zone::insert_zone($data);

                $sdata = array();
                foreach ($arr_state as $sid) {
                    $sdata['states_id']     = $sid;
                    $sdata['zone_id']   = $zone_id;
                    $sdata['insert_by'] = Session::get('username');
                    $sdata['insert_date']= date('Y-m-d H:i:s');

                    Zone::insert_zone_state($sdata);
                }
            }

            if (count($arr_city) > 0) {
                $cdata = array();
                foreach ($arr_city as $cid) {
                    $cdata['city_id']   = $cid;
                    $cdata['zone_id']   = $zone_id;
                    $cdata['insert_by'] = Session::get('username');
                    $cdata['insert_date']= date('Y-m-d H:i:s');

                    Zone::insert_zone_city($cdata);
                }   
            }
            // Insert Country Table
//            $id = DB::table('jocom_zones')->insertGetId(array(
//                            'name' => trim(Input::get('name')),
//                            'country_id' => trim(Input::get('country')),
//                            'insert_by' => Session::get('username'),
//                            'modify_by' => Session::get('username'),
//                            'insert_date' => date('Y-m-d H:i:s'),
//                            'modify_date' => date('Y-m-d H:i:s'))
//            );

            if($zone_id) {
                Session::flash('message', 'Successfully updated.');
            } 
//            else {
//                Session::flash('message', 'Error. Unknown error occured.');
//            }
            return Redirect::to('zone');
        }   
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $zone_id
     * @return Response
     */
    public function anyEdit($zone_id) {
        $zone = $this->zone->select(array(
                                        'jocom_zones.id', 
                                        'jocom_zones.name',
                                        'jocom_zones.weight',
                                        'jocom_zones.init_weight',
                                        'jocom_zones.init_price',
                                        'jocom_zones.add_weight',
                                        'jocom_zones.add_price',
                                        'jocom_zones.country_id',
                                        'jocom_zones.status', // Added by Maruthu
                                        'jocom_zones.insert_by',
                                        'jocom_zones.insert_date', 
                                        'jocom_zones.modify_by',
                                        'jocom_zones.modify_date',
                                        'jocom_countries.name as country',
                                        'jocom_countries.currency as currency',
                                        'jocom_countries.business_currency as business_currency',
                                        'jocom_country_states.name as states'
                                    ))
                                    ->leftJoin('jocom_countries', 'jocom_zones.country_id', '=', 'jocom_countries.id')
                                    ->leftJoin('jocom_country_states', 'jocom_zones.country_id', '=', 'jocom_country_states.country_id')
//                                    ->where('jocom_zones.status', '=', '1')
                                    ->where('jocom_zones.id', '=', $zone_id)
                                    ->first();

        $state_options = DB::table('jocom_country_states')
                        ->select('jocom_country_states.id', 'jocom_country_states.name')
//                        ->leftJoin('jocom_zones', 'jocom_country_states.country_id', '=', 'jocom_zones.country_id')
                        // ->where('jocom_country_states.status', '=', '1')
//                        ->where('jocom_zones.id', '=', $zone_id)
                        ->where('jocom_country_states.country_id', '=', $zone->country_id)
                        ->orderBy('jocom_country_states.name')                        
                        ->get();

        $city_options   = DB::table('jocom_cities as city')
                            ->select('city.id', 'city.name', 'state.id as state_id')
                            ->leftjoin('jocom_country_states as state', 'state.id', '=', 'city.state_id')
                            ->where('state.country_id', '=', $zone->country_id)
                            ->get();
                            
        $selected_states = DB::table('jocom_zone_states')
                        ->select('states_id')
                        ->leftJoin('jocom_zones', 'jocom_zone_states.zone_id', '=', 'jocom_zones.id')
                        ->where('jocom_zone_states.zone_id', '=', $zone_id)
                        ->get();

        $selected_cities = DB::table('jocom_zone_cities')
                                ->select('city_id')
                                ->leftjoin('jocom_cities', 'jocom_cities.id', '=', 'jocom_zone_cities.city_id')
                                ->where('jocom_zone_cities.zone_id', '=', $zone_id)
                                ->get();

        if (count($selected_cities) == 0){
            $selected_cities = DB::table('jocom_cities as city')
                                ->select('city.id as city_id')
                                ->leftjoin('jocom_country_states as state', 'state.id', '=', 'city.state_id')
                                ->where('state.country_id', '=', $zone->country_id)
                                ->get();
        }
        
//        return View::make('zone.edit')->with(array('zone' => $zone, 'state_options' => $state_options, 'selected_options' => $selected_options));
        return View::make('zone.edit')->with(array(
                            'zone'              => $zone, 
                            'state_options'     => $state_options, 
                            'city_options'      => $city_options,
                            'selected_states'   => $selected_states,
                            'selected_cities'   => $selected_cities,
                        ));     
    }    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $zone_id
     * @return Response
     */
    public function anyUpdate($zone_id) {

        $input = Input::all();

        if( !$this->zone->fill($input)->isValid() ) {
            return Redirect::back()->withInput()->withErrors($this->zone->errors);
        }
        
        // UPDATE ZONE TABLE
        $zone               = $this->zone->find($zone_id);
        $zone->name         = trim(Input::get('name'));
        $zone->weight       = trim(Input::get('weight'));
        $zone->status       = trim(Input::get('status')); // Added by Maruthu
        $zone->init_weight  = null != Input::get('init_weight') ? trim(Input::get('init_weight')) : NULL;
        $zone->init_price   = null != Input::get('init_price') ? trim(Input::get('init_price')) : NULL;
        $zone->add_weight   = null != Input::get('add_weight') ? trim(Input::get('add_weight')) : NULL;
        $zone->add_price    = null != Input::get('add_price') ? trim(Input::get('add_price')) : NULL;
        $zone->modify_by    = Session::get('username');
        $zone->modify_date  = date('Y-m-d H:i:s');
        $zone->save();

        if(Input::has('state')) {
            $states = Input::get('state');

            foreach ($states as $state) {
                if(Input::has('city_'.$state)) {
                    $cities      = Input::get('city_'.$state);
                    $arr_state[] = $state;

                    foreach ($cities as $city) {
                        $tmp = explode('_', $city);
                        $arr_city[] = $tmp[1];
                    }
                } 
                else {
                    // echo "<br>NO City for State - ".$state;
                    Session::flash('warning', 'Each State must consists of at least one(1) City.');
                    return Redirect::to('zone/edit/'.$zone_id);
                }

            }
        }

        // UPDATE ZONE_STATES TABLE
        if (count($arr_state) > 0) {
            DB::table('jocom_zone_states')->where('zone_id', '=', $zone_id)->delete();

            $sdata = array();
            foreach ($arr_state as $sid) {
                $sdata['states_id']     = $sid;
                $sdata['zone_id']   = $zone_id;
                $sdata['insert_by'] = Session::get('username');
                $sdata['insert_date']= date('Y-m-d H:i:s');

                Zone::insert_zone_state($sdata);
            }
        }

        // UPDATE ZONE_CITIES TABLE
        if (count($arr_city) > 0) {
            DB::table('jocom_zone_cities')->where('zone_id', '=', $zone_id)->delete();
            
            $cdata = array();
            foreach ($arr_city as $cid) {
                $cdata['city_id']   = $cid;
                $cdata['zone_id']   = $zone_id;
                $cdata['insert_by'] = Session::get('username');
                $cdata['insert_date']= date('Y-m-d H:i:s');

                Zone::insert_zone_city($cdata);
            }   
        }
        
//        DB::table('jocom_zone_states')->where('zone_id', '=', $zone_id)->delete();
//
//        if (Input::has('state_id')) {
//            foreach(Input::get('state_id') as $key => $value) {
//                DB::table('jocom_zone_states')->insertGetId(array(
//                        'states_id' => trim(Input::get("state_id.$key")), 
//                        'zone_id' => ($zone_id),
//                        'insert_by' => Session::get('username'),
//                        'modify_by' => Session::get('username'),
//                        'insert_date' => date('Y-m-d H:i:s'),
//                        'modify_date' => date('Y-m-d H:i:s'))
//                );
//            }
//        }
//        
//        // UPDATE ZONE TABLE
//        $zone = $this->zone->find($zone_id);
//        $zone->name = trim(Input::get('name'));
//        $zone->modify_by = Session::get('username');
//        $zone->save();
        
        Session::flash('message', 'Successfully updated.');
        return Redirect::to('zone/edit/'.$zone_id);
    }

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $zone_id
     * @return Response
     */
    public function anyDelete($zone_id) {

        $zone               = $this->zone->find($zone_id);
        $zone->modify_by    = Session::get('username');
        $zone->modify_date  = date('Y-m-d H:i:s');
        $zone->status       = 2;
        $zone->save();

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('zone');
    }
}