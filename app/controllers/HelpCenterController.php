<?php

class HelpCenterController extends BaseController {
    
    protected $helpcenter;

    public function __construct(ApiHelpCenter $helpcenter) {
        $this->helpcenter = $helpcenter;
        $this->beforeFilter('auth');
    }
    
    /**
     * Display the Helpcenter page.
     *
     * @return Page
     */
    public function anyIndex(){
        return View::make('helpcenter.index');
    }
    /**
     * Display the Listing Data Response.
     *
     * @return Response
     */
    public function anyListing(){
        $helpcenterdata=DB::table('jocom_helpcenter')
                        ->select('id','username','order_id','query_topic','description','status')
                        ->where('status','!=','3');
                        
       return Datatables::of($helpcenterdata)
                              ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="helpcenter/view/{{$id}}"><i class="fa fa-eye"></i></a>  <a class="btn btn-danger" title="" data-toggle="tooltip" onclick="delete_record({{$id}})"><i class="fa fa-trash"></i></a>')
                              ->edit_column('status',function ($helpcenterdata) {
                if ($helpcenterdata->status==1){
                return '<button type="button" class="btn btn-success btn-sm">Active</button>';
                }
                })
                              ->make(true);
    }
    /**
     * Display a listing of the Customer resource.
     *
     * @return Response
     */
    public function anyCustomersajax() {    
       $customers = DB::table('jocom_user')
                            ->select('id', 'username', 'full_name', 'email');

        return Datatables::of($customers)
                                    ->add_column('Action', '<a id="selectCust" class="btn btn-primary" title="" href="../customer/{{$id}}">Select</a>')
                                    ->make();
    }
    
    /**
     * Display the Customer page resource.
     *
     * @return Response
     */
    public function anyAjaxcustomer() {
        return View::make('helpcenter.ajaxcustomer');
    }
    
    public function anyRemove() 
    {
        if (Input::has('remove_helpcenter_id'))
        {
            $help_id = Input::get('remove_helpcenter_id');
            $helpdata = ApiHelpCenter::find($help_id);    
            $helpdata->status = 3;

            if ($helpdata->save())
            {
                return Redirect::to('helpcenter')->with('success', 'Help Center(ID: '.$help_id.') Data has been deleted.');
            }
            else
            {
                return Redirect::to('helpcenter')->with('message', 'Delete failed. Data has not changed');
            }
        }
             
        
    }
    /**
     * Edit Help Center page.
     * @params id
     * @return Page
     */
    public function anyView($id){
        
        if($id!=""){
            $helpdata=ApiHelpCenter::find($id);
            return View::make('helpcenter.edit')->with('data',$helpdata);
        }
        else{
          return Redirect::to('helpcenter')->with('message', 'Data Not Available');  
        }
    }
   
    
    
}