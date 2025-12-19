<?php

class CountryController extends BaseController {

	protected $country;

	public function __construct(Country $country) {
		$this->country = $country;
	}
	
	/**
     * Display the country page.
     *
     * @return Page
     */
	public function anyIndex() {
		return View::make('country.index');
	}

	/**
     * Display a listing of the country resource.
     *
     * @return Response
     */
	public function anyCountries() {		
		$countries = $this->country->select(array(
										'jocom_countries.id', 
										'jocom_countries.name',
									))
									->where('jocom_countries.status', '=', '1');
		return Datatables::of($countries)
									// ->edit_column('full_name', ucwords({{$full_name}}))
									->add_column('Action', '<a id="addState" class="btn btn-success" href="/country/state/{{$id}}" title="States" data-toggle="tooltip"><i class="fa fa-location-arrow"></i> States</a>
                                            ')
									->make();
	}

	/**
     * Display a listing of the state resource.
     *
     * @return Response
     */
	public function anyStates($country_id) {
		$states = DB::table('jocom_country_states')
				        ->select('id', 'name')
//				        ->where('status', '=', '1')
				        ->where('country_id', '=', $country_id);

		return Datatables::of($states)
							->add_column('Action', '<a id="addCity" class="btn btn-success" href="/country/city/{{$id}}" title="Cities" data-toggle="tooltip"><i class="fa fa-location-arrow"></i> Cities</a>')
							->make();
	}

	/**
     * Display a listing of the city resource.
     *
     * @return Response
     */
	public function anyCities($state_id) {
		$cities = DB::table('jocom_cities')
				        ->select('id', 'name')
				        //->where('status', '=', '1')
				        ->where('state_id', '=', $state_id);

		return Datatables::of($cities)
							->make();
	}

	/**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
	public function anyCreate() {
		if ( Permission::CheckAccessLevel(Session::get('role_id'), 1, 5, 'AND'))
			return View::make('country.create');
		else
			return View::make('home.denied', array('module' => 'Shipping > Add Country'));
	}

	/**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	public function anyStore() {
		$input = Input::all();

		if( !$this->country->fill($input)->isValid() ) {
			return Redirect::back()->withInput()->withErrors($this->country->errors);
		} else {
			// Insert Country Table
			$id = DB::table('jocom_countries')->insertGetId(array(
							'name' => trim(Input::get('name')),
							'insert_by' => Session::get('username'),
							'modify_by' => Session::get('username'),
			    			'insert_date' => date('Y-m-d H:i:s'),
			    			'modify_date' => date('Y-m-d H:i:s'))
			);

	        if($id) {
	            Session::flash('message', 'Successfully updated.');
	        } else {
	            Session::flash('message', 'Error. Unknown error occured.');
	        }
	        return Redirect::to('country');
	    }	
	}

	/**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	public function anyStatestore($country_id) {
		$input = Input::all();

		if( !$this->country->fill($input)->isValid() ) {
			return Redirect::back()->withInput()->withErrors($this->country->errors);
		} else {
			// Insert Country Table
			$id = DB::table('jocom_country_states')->insertGetId(array(
							'name' => trim(Input::get('name')),
							'country_id' => $country_id,
							'insert_by' => Session::get('username'),
							'modify_by' => Session::get('username'),
			    			'insert_date' => date('Y-m-d H:i:s'),
			    			'modify_date' => date('Y-m-d H:i:s'))
			);

	        if($id) {
	            Session::flash('message', 'Successfully updated.');
	        } else {
	            Session::flash('message', 'Error. Unknown error occured.');
	        }
	        return Redirect::to('country');
	    }	
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyEdit($country_id) {
    	$country = $this->country->select(array(
										'jocom_countries.id', 
										'jocom_countries.name'
									))
									->where('jocom_countries.status', '=', '1')
									->where('jocom_countries.id', '=', $country_id)
									->first();
	        
        return View::make('country.edit')->with(array('country' => $country));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyStateedit($state_id) {

    	$state = DB::table('jocom_country_states')
				        ->select('id', 'name')
				        ->where('status', '=', '1')
				        ->where('id', '=', $state_id)
						->first();
	        
        return View::make('country.stateedit')->with(array('state' => $state));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyState($country_id) {
    	$country = $this->country->select(array(
										'jocom_countries.id', 
										'jocom_countries.name'
									))
									->where('jocom_countries.status', '=', '1')
									->where('jocom_countries.id', '=', $country_id)
									->first();
	        
        return View::make('country.state')->with(array('country' => $country));
    }
    
    
 	public function anyCity($state_id) {
    	$state = DB::table('jocom_country_states')->select(array(
										'id', 
										'name'
									))
									->where('id', '=', $state_id)
									->first();
	        
        return View::make('country.city')->with(array('state' => $state));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyUpdate($country_id) {

    	$input = Input::all();

    	if( !$this->country->fill($input)->isValid() ) {
			return Redirect::back()->withInput()->withErrors($this->country->errors);
		}
		
		// UPDATE COUNTRY TABLE
		$country = $this->country->find($country_id);
		$country->modify_by = Session::get('username');
        $country->name = trim(Input::get('name'));
        $country->save();
        
		Session::flash('message', 'Successfully updated.');
		return Redirect::to('country');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyStateupdate($state_id) {

    	$input = Input::all();

    	if( !$this->country->fill($input)->isValid() ) {
			return Redirect::back()->withInput()->withErrors($this->country->errors);
		}
		
		// UPDATE State TABLE
		DB::table('jocom_country_states')				        
			        ->where('id', '=', $state_id)
			        ->update(['name' => trim(Input::get('name')), 'modify_by' => Session::get('username'), 'modify_date' => date('Y-m-d H:i:s')]);
        
		Session::flash('message', 'Successfully updated.');
		return Redirect::to('country');
    }

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $country_id
     * @return Response
     */
    public function anyDelete($country_id) {

        $country = $this->country->find($country_id);
        $country->status = 0;
        $country->modify_by = Session::get('username');
        $country->modify_date = date('Y-m-d H:i:s');       
        $country->save();

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('country');
    }

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $state_id
     * @return Response
     */
    public function anyStatedelete($state_id) {

        DB::table('jocom_country_states')				        
			        ->where('id', '=', $state_id)
			        ->update(['status' => 0, 'modify_by' => Session::get('username')]);

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('country');
    }
}