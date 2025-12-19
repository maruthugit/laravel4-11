<?php

class WarehouseLocationController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function locations() {     
        $payments = WarehouseLocation::select('id', 'name', 'address_1', 'address_2', 'postcode', 'pic_name', 'pic_contact', 'status')
                        ->where('status', '<', '2');

        return Datatables::of($payments)
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/warehouse-location/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 9, 9, \'AND\'))
                                <a id="deleteWarehouseLocation" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/warehouse-location/delete/{{$id}}"><i class="fa fa-remove"></i></a>
                                @endif
                            ')
                        ->edit_column('status', '
                                @if($status == 1)
                                    <p class="text-success">Active</p>
                                @else
                                    <p class="text-danger">Inactive</p>
                                @endif
                                ')
                        ->make();
    }

    /**
     * Display a listing of the warehouse location.
     *
     * @return Response
     */
    public function index() {
        return View::make('warehouse-location.index');
    }


    /**
     * Show the form for creating a new warehouse location.
     *
     * @return Response
     */
    public function create() {   
        $countries = Country::getActiveCountry();
        return View::make('warehouse-location.create_warehouselocation')
                ->with([
                    'countries' => $countries,
                ]);
    }

     /**
     * Store a newly created warehouse location in storage.
     *
     * @return Response
     */
    public function store() {
        $payment_input   = Input::all();
        $validator = Validator::make(Input::all(), WarehouseLocation::$rules);

        if ($validator->passes()) {
            $stateId = Input::get('state');
            $stateName = Transaction::getStateName($stateId);
            $cityId = Input::get('city');
            $cityName = Transaction::getCityName($cityId, $stateId);

            $countryId = Input::get('country');
            $countryName = Input::get('country_name');

            $location = new WarehouseLocation;
            $location->name = Input::get('name');
            $location->address_1 = Input::get('address_1');
            $location->address_2 = Input::get('address_2');
            $location->postcode = Input::get('postcode');
            $location->city = $cityName;
            $location->city_id = $cityId;
            $location->state = $stateName;
            $location->state_id = $stateId;
            $location->country = $countryName;
            $location->country_id = $countryId;
            $location->pic_name = Input::get('pic_name');
            $location->pic_contact = Input::get('pic_contact');
            $location->tel = Input::get('tel');
            $location->fax = Input::get('fax');
            $location->status = 1;
            $location->created_by = Session::get('username');
            $location->updated_by = Session::get('username');

            if($location->save()) {
                General::audit_trail('WarehouseLocationController.php', 'store()', 'Add Warehouse Location', Session::get('username'), 'CMS');
                return Redirect::to('/warehouse-location')->with('success', 'Warehouse Location(ID: '.$location->id.') added successfully.');
            }
               
        } else {
            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
        
    }

    /**
     * Show the form for editing the specified warehouse location.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $warehouse = WarehouseLocation::find($id);
        $countries = Country::getActiveCountry();
        
        if ($warehouse == null) {
            return Redirect::to('/warehouse-location');
        }

        $states = DB::table('jocom_country_states')
                    ->select('id', 'name')
                    ->where('country_id', '=', $warehouse->country_id)
                    ->orderby('name')
                    ->get();

        $cities = DB::table('jocom_cities')
                    ->select('id', 'name')
                    ->where('state_id', '=', $warehouse->state_id)
                    ->orderby('name')
                    ->get();

        // $statuses = array("1" => "Active", "0" => "Inactive");
        $statuses = array("Inactive", "Active");

        return View::make('warehouse-location.edit')->with(array(
            'warehouse' => $warehouse,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'statuses' => $statuses
        ));
    }

    /**
     * Update the specified warehouse location in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $validator = Validator::make(Input::all(), WarehouseLocation::$rules);

        if ($validator->passes()) {
            $stateId = Input::get('state');
            $stateName = Transaction::getStateName($stateId);
            $cityId = Input::get('city');
            $cityName = Transaction::getCityName($cityId, $stateId);

            $countryId = Input::get('country');
            $countryName = Input::get('country_name');

            $location = WarehouseLocation::find($id);

            if ($location == null) {
                return Redirect::to('/warehouse-location');
            }

            $location->name = Input::get('name');
            $location->address_1 = Input::get('address_1');
            $location->address_2 = Input::get('address_2');
            $location->postcode = Input::get('postcode');
            $location->city = $cityName;
            $location->city_id = $cityId;
            $location->state = $stateName;
            $location->state_id = $stateId;
            $location->country = $countryName;
            $location->country_id = $countryId;
            $location->pic_name = Input::get('pic_name');
            $location->pic_contact = Input::get('pic_contact');
            $location->tel = Input::get('tel');
            $location->fax = Input::get('fax');
            $location->status = Input::get('status');
            $location->updated_by = Session::get('username');

            if($location->save()) {
                General::audit_trail('WarehouseLocationController.php', 'update()', 'Update Warehouse Location', Session::get('username'), 'CMS');
                return Redirect::to('/warehouse-location')->with('success', 'Warehouse Location(ID: '.$location->id.') updated successfully.');
            }
        } else {
            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
    }
 
    /**
     * Remove the specified warehouse location from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id) {
        $payment = WarehouseLocation::find($id);
        $payment->status = 2;
        $payment->updated_by = Session::get('username');
        $payment->save();
        
        $insert_audit = General::audit_trail('WarehouseLocationController.php', 'delete()', 'Delete Warehouse Location', Session::get('username'), 'CMS');

        return Redirect::to('/warehouse-location');
    }

}
?>