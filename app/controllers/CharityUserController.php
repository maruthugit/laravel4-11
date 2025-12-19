<?php
 
class CharityUserController extends BaseController {
 
     /**
     * Display a listing of the charity users on datatable.
     *
     * @return Response
     */
    public function anyUsers()
    {
        $users = CharityUser::select(array(
                                'jocom_charity_users.id', 
                                'jocom_charity_users.username', 
                                'jocom_charity_users.full_name',
                                'jocom_charity_category.name', 
                                'jocom_charity_users.email', 
								'jocom_charity_users.status',
                            ))
                            ->leftjoin('jocom_charity_category', 'jocom_charity_users.charity_id', '=', 'jocom_charity_category.id');

        // $users->get();
        // var_dump($users);exit;
                          
        return Datatables::of($users)
        				->edit_column('status', '
                                    @if($status == 1)
                                        <p class="text-success">Active</p>
                                    @else
                                        <p class="text-danger">Inactive</p>
                                    @endif
                            ')
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/charity/user/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 10, 9, \'AND\'))
                                <a id="deleteUser" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$username}}" href="/charity/user/delete/{{$id}}"><i class="fa fa-times"></i></a>
                                @endif
                            ')
                        ->make();
    }

     /**
     * Display a listing of the user.
     *
     * @return Response
     */
    public function anyIndex()
    {
        return View::make('charity.user.index');
    }
 

 
    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        $charityOptions = CharityCategory::getCharityOption2();
        
        return View::make('charity.user.create', ['category' => $charityOptions]);
        // return View::make('user.create', ['roles' => $role_list]);
    }
 
    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        $user = new CharityUser;
 
        $validator = Validator::make(Input::all(), CharityUser::$rules);

        if ($validator->passes()) {
            $user->username     = Input::get('username');
            $user->full_name    = Input::get('full_name');
            $user->email        = Input::get('email');
            $user->contact_no   = Input::get('contact_no');
            $user->password     = Hash::make(Input::get('password'));
            $user->charity_id   = Input::get('charity_id');
            $user->status       = Input::get('status');
            $user->timestamps   = false;
            $user->created_by   = Session::get('username');
            $user->created_date = date("Y-m-d H:i:s");
            // $user->modify_date = date('Y-m-d H:i:s', strtotime($date));
            
            if($user->save()) {
                return Redirect::to('charity/user/index')->with('message', 'User added successfully!');       
            } else {
                echo "<br> Failed to add user!";
            }
                
        } else {

            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
             // return Redirect::to('users/create')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
        }
        
    }
 
    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $user           = CharityUser::find($id);
        $charityOptions = CharityCategory::getCharityOption2();

        return View::make('charity.user.edit')->with(array(
                'user'          => $user,
                'category'      => $charityOptions
            )
        );
    }
 
    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        $arr_validate   = array();
        $arr_input      = array();
        
        $arr_input_user['username']             = Input::get('username');
        $arr_input_user['password']             = Input::get('password');
        $arr_input_user['password_confirmation']= Input::get('password_confirmation');
        $arr_input_user['full_name']            = Input::get('full_name');
        $arr_input_user['email']                = Input::get('email');
        $arr_input_user['contact_no']           = Input::get('contact_no');
        $arr_input_user['status']               = Input::get('status');
        $arr_input_user['charity_id']           = Input::get('charity_id');

        $arr_validate   = CharityUser::getUpdateRules($arr_input_user);
        $arr_input      = CharityUser::getUpdateInputs($arr_input_user);
        $arr_udata      = CharityUser::getUpdateDbDetails($arr_input);
        $validator      = Validator::make($arr_input, $arr_validate);

        if ($validator->passes()) {

            if(CharityUser::updateUser($id, $arr_udata)){
                return Redirect::to('charity/user/index');
            } 
        
        }else {
            
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
    }
 
    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $user = CharityUser::find($id);
        // User::destroy($id);
        $user->status = '0';
        $user->timestamps = false;
        $user->modify_by = Session::get('username');
        $user->modify_date = date('Y-m-d H:i:s');
        $user->status = '0';
        $user->save();

        return Redirect::to('charity/user');
    }

    /**
     * Display a listing of the users resource.
     *
     * @return Response
     */
    public function anyUserajax() {     
        $users = DB::table('jocom_user')->select(array(
                                        'jocom_user.id', 
                                        'jocom_user.username', 
                                        'jocom_user.full_name'
                                    ));
        return Datatables::of($users)
                                    ->add_column('Action', '<a id="selectItem" class="btn btn-primary" href="{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $asd
     * @return Response
     */
    public function anyAjaxuser() {
        return View::make('user.ajaxuser');
    }

}

?>