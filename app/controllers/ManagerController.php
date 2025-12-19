<?php

class ManagerController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function managers() {     
        $payments = Manager::select('id', 'name', 'status')
                        ->where('status', '<', '2');;

        return Datatables::of($payments)
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/manager/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 9, 9, \'AND\'))
                                <a id="deleteManager" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/manager/delete/{{$id}}"><i class="fa fa-remove"></i></a>
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
     * Display a listing of the manager.
     *
     * @return Response
     */
    public function index() {
        $paymentTerms = Manager::all();
        return View::make('manager.index');
    }


    /**
     * Show the form for creating a new manager.
     *
     * @return Response
     */
    public function create() {   
        return View::make('manager.create_manager');
    }

     /**
     * Store a newly created manager in storage.
     *
     * @return Response
     */
    public function store() {
        $manager_input = Input::all();

        $validator = Validator::make(Input::all(), Manager::$rules);

        $manager = new Manager;
            
        if ($validator->passes()) {
            $manager->name = Input::get('name');
            $manager->status = 1;
            $manager->created_by = Session::get('username');
            $manager->updated_by = Session::get('username');
            
            if($manager->save()) {
                General::audit_trail('ManagerController.php', 'store()', 'Add Manager', Session::get('username'), 'CMS');
                return Redirect::to('/manager')->with('success', 'Manger(ID: '.$manager->id.') added successfully.');
            }
               
        } else {

            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
        
    }

    /**
     * Show the form for editing the specified manager.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        $manager = Manager::find($id);

        
        if ($manager == null) {
            return Redirect::to('/manager');
        }

        $statuses = array("Inactive", "Active");

        return View::make('manager.edit')->with(array(
            'manager' => $manager,
            'statuses' => $statuses
        ));
    }

    /**
     * Update the specified manager in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $validator = Validator::make(Input::all(), Manager::$rules);

        if ($validator->passes()) {

            $manager = Manager::find($id);

            if ($manager == null) {
                return Redirect::to('/manager');
            }

            $manager->name = Input::get('name');
            $manager->status = Input::get('status');
            $manager->updated_by = Session::get('username');

            if($manager->save()) {
                General::audit_trail('ManagerController.php', 'update()', 'Update Manager', Session::get('username'), 'CMS');
                return Redirect::to('/manager')->with('success', 'Manager(ID: '.$manager->id.') updated successfully.');
            }
        } else {
            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
    }
 
    /**
     * Remove the specified manager from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id) {
        $manager = Manager::find($id);
        $manager->status = 2;
        $manager->updated_by = Session::get('username');
        $manager->save();
        
        $insert_audit = General::audit_trail('ManagerController.php', 'delete()', 'Delete Manager', Session::get('username'), 'CMS');

        return Redirect::to('/manager');
    }
}
?>