<?php
use Helper\ImageHelper as Image;
class PlatformController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }
     public function anyIndex(){
        
        $platforms=DB::table('jocom_plaforms_details')->select('*')->where('status','=',1)->get();
        
        return View::make('platforms.platform')->with('list',$platforms);
    }
    public function anyCreate(){
        
        $platforms=DB::table('jocom_plaforms_details')->select('*')->where('status','=',1)->get();
        
        return View::make('platforms.index')->with('platforms',$platforms);
    }
    
    public function anyAjaxcustomer()
    {
        return View::make('platforms.ajaxcustomer');
    }
    
    public function anyPlatforms(){
        
     $platformname=Input::get('platform_name');
     $platform_username=Input::get('platform_username');
     $platform_userid=Input::get('platform_user_id');
     $platform_status=Input::get('status');
     
     $platform=DB::table('jocom_plaforms_details')->insert(['platform_name'=>$platformname, 'platform_username'=>$platform_username, 'platform_user_id'=>$platform_userid,'status'=>$platform_status,'inserted_by'=>Session::get('username')]);
     if($platform){
         return Redirect::to('/platforms')->with('success', 'Platform Added successfully.');
     }else{
        return Redirect::to('/platforms/create')->with('message', 'Something went wrong!try again'); 
     }
    
    }
    
    public function anyStore(){
        
     $store_name=Input::get('store_name');
     $store_id=Input::get('store_id');
     $platform_id=Input::get('platform_id');
     $store_status=Input::get('status');
     
     $platform_store=DB::table('jocom_platform_stores')->insert(['store_name'=>$store_name,'external_store_id'=>$store_id,'platform_id'=>$platform_id,'status'=>$store_status,'created_by'=>Session::get('username')]);
     if($platform_store){
         return Redirect::to('/platforms/create')->with('success', 'Platform Store Added successfully.');
     }else{
        return Redirect::to('/platforms/create')->with('message', 'Something went wrong!try again'); 
     }
    
    }
    
    public function anyDeleteplatform($id)
    {
        $platforms = DB::table('jocom_plaforms_details')->where('id','=',$id)->first();
        if($platforms){
        $platform = DB::table('jocom_plaforms_details')->where('id','=',$id)->update(['status'=>2,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);
        
            if($platform){
             return Redirect::to('/platforms')->with('message','(ID: '.$id.') deleted successfully.');   
            }else{
                return Redirect::to('/platforms')->with('message', 'Something went wrong!try again'); 
            }
        }else{
             return Redirect::to('/platforms')->with('message', 'ID not found!');
        }
        
    }
    
    public function anyPlatformedit($id){
        $platforms = DB::table('jocom_plaforms_details')->select('*')->where('id','=',$id)->first();
        
        return View::make('platforms.platform_edit')->with('list',$platforms);
    }
    public function anyPlatformupdate($id){
        
     $platformname=Input::get('platform_name');
     $platform_username=Input::get('platform_username');
     $platform_userid=Input::get('platform_user_id');
     $platform_status=Input::get('status');
     $platform = DB::table('jocom_plaforms_details')->where('id','=',$id)->update(['platform_name'=>$platformname,'platform_username'=>$platform_username, 'platform_user_id'=>$platform_userid,'status'=>$platform_status,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);

        if($platform){
             return Redirect::to('/platforms')->with('success','(ID: '.$id.') Updated successfully.');   
            }else{
                return Redirect::to('/platforms')->with('message', 'Something went wrong!try again'); 
            }
    }
     public function anyPlatformstoreedit($id){
        $stores = DB::table('jocom_platform_stores')->select('*')->where('id','=',$id)->first();
        $platforms=DB::table('jocom_plaforms_details')->select('*')->where('status','=',1)->get();
        return View::make('platforms.platform_edit')->with('store',$stores)->with('platforms',$platforms);;
    }
    public function anyStores(){
        $platform_store=DB::table('jocom_platform_stores')
                               ->select('jocom_platform_stores.id','jocom_plaforms_details.platform_name','jocom_platform_stores.store_name','jocom_platform_stores.external_store_id','jocom_platform_stores.status')
                               ->leftjoin('jocom_plaforms_details','jocom_platform_stores.platform_id','=','jocom_plaforms_details.id')
                               ->where('jocom_platform_stores.status','!=',2);
         return Datatables::of($platform_store)
            ->edit_column('status',function($row){
                
                if($row->status==1){
                    return '<button title="Active" alt="Active" class="btn btn-success">Active</button>';
                }else{
                  return '<button title="Inactive" alt="Inactive" class="btn btn-danger">Inactive</button>';  
                }
            })
            ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/platforms/platformstoreedit/{{$id}}"><i class="fa fa-pencil"></i></a>  <a id="deleteBan" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/platforms/deleteplatformstore/{{$id}}"><i class="fa fa-times"></i></a>')
            ->make();
                               
    }
     public function anyDeleteplatformstore($id)
    {
        $platforms = DB::table('jocom_platform_stores')->where('id','=',$id)->first();
        if($platforms){
        $platform = DB::table('jocom_platform_stores')->where('id','=',$id)->update(['status'=>2,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);
        
            if($platform){
             return Redirect::to('/platforms')->with('message','(ID: '.$id.') deleted successfully.');   
            }else{
                return Redirect::to('/platforms')->with('message', 'Something went wrong!try again'); 
            }
        }else{
             return Redirect::to('/platforms')->with('message', 'ID not found!');
        }
        
    }
    public function anyUpdatestores($id){
        
     $store_name=Input::get('store_name');
     $store_id=Input::get('store_id');
     $platform_id=Input::get('platform_id');
     $store_status=Input::get('status');
     $store = DB::table('jocom_platform_stores')->where('id','=',$id)->update(['store_name'=>$store_name,'external_store_id'=>$store_id,'platform_id'=>$platform_id,'status'=>$store_status,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);

        if($store){
             return Redirect::to('/platforms')->with('success','(ID: '.$id.') Updated successfully.');   
            }else{
                return Redirect::to('/platforms')->with('message', 'Something went wrong!try again'); 
            }
    }
    public function anyStorelist(){
        $platform_id=Input::get('platform_id');
        $platform_store=DB::table('jocom_platform_stores')
        ->select('jocom_platform_stores.store_name','jocom_platform_stores.external_store_id')
        ->where('jocom_platform_stores.status','=',1)
        ->where('jocom_platform_stores.platform_id','=',$platform_id)
        ->get();
        
        return $platform_store; 
        
    }
    

    
}
?>