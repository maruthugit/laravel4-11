<?php

class EcomBannerController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
        // echo "<br>check authentication ";
    }

    /**
     * Display a listing of the banner.
     *
     * @return Response
     */
    public function anyIndex()
    {
        // $banner     = new Banner;
        $id         = "";

        if (Input::has('up')) {
           
            $id     = Input::get('up');
            Banner::set_sort($id, 'up');
           
        } else {
            $id     = Input::get('down');
            Banner::set_sort($id, 'down');
          
        }

        return View::make('ecombanner.index');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function anyBanners() {
     
        $banners = Banner::select('jocom_banners.id', 'jocom_banners_images.thumb_name', 'jocom_banners.qrcode', 'jocom_banners.pos', 'jocom_products_category.category_name', 'jocom_region.region')
                            ->leftjoin('jocom_banners_images', 'jocom_banners_images.banner_id', '=', 'jocom_banners.id')
                            ->leftjoin('jocom_products_category', 'jocom_products_category.id', '=', 'jocom_banners.category_id')
                            ->leftjoin('jocom_region', 'jocom_region.id', '=', 'jocom_banners.region_id')
                            ->groupBy('jocom_banners.id')
                            ->where('jocom_banners.active_status', '=', '1')
                            ->where('jocom_banners_images.language', '=', 'en');

        $banners = EcomBanners::select('id', 'file_name', 'qrcode');
                           
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();


        return Datatables::of($banners)
                                    // ->edit_column('price', '{{money_format(\'%i\', $price)}}')
                        ->edit_column('file_name', '
                            @if(file_exists(Config::get(\'constants.ECOM_BANNER_FILE_PATH\') ."/". $file_name))
                                {{ HTML::image(Config::get(\'constants.ECOM_BANNER_FILE_PATH\') ."/". $file_name, null ,array( \'width\' => 140, \'height\' => 70 )) }}
                            @else
                                {{ HTML::image(\'media/no_images.jpg/\', null ,array( \'width\' => 140, \'height\' => 70 )) }}
                            @endif
                            ')
                        // ->edit_column('pos', '
                        //     @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 3, \'AND\'))
                        //         <a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/banner/index?up={{$id}}"><i class="fa fa-arrow-up"></i></a>
                        //         <a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/banner/index?down={{$id}}"><i class="fa fa-arrow-down"></i></a>
                        //     @else
                        //         {{$pos}}
                        //     @endif
                        //     ')
                        ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/ecombanner/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 9, \'AND\'))
                                <a id="deleteBan" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/ecombanner/delete/{{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                            ')
                        ->make();
    }

    /**
     * Show the form for creating a new banner.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
        {

            $sysAdminInfo = User::where("username",Session::get('username'))->first();
            $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
            
            
            
            $country_id = $SysAdminRegion;
            if(count($regions) <= 0){
                $regions      = Region::where("country_id",$country_id)->get();
            }

            if($SysAdminRegion->region_id != 0){
                $Region = Region::find($SysAdminRegion->region_id);
                $country_id = $Region->country_id;
                
                $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
                $countries = Country::where("id",$country_id)->get();

            }else{
                $regions = Region::where("country_id",$country_id)
                    ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
            }


            return View::make('ecombanner.create', ['regions' => $regions,'countries' => $countries]);
        }
        else
            return View::make('home.denied', array('module' => 'Banner > Add Banner'));

    }

     /**
     * Store a newly created banner in storage.
     *
     * @return Response
     */

     public function anyStore() {
        $banner = new EcomBanners;
        if (Input::get('qrcode') != null && Input::get('qrcode') != '') {
            $product_id = DB::table('jocom_products')
                        ->where('qrcode', '=', Input::get('qrcode'))
                        ->pluck('id');
            if ($product_id == null) {
                return Redirect::back()
                        ->withErrors(['QR Code Not found.']);
            }
        }
        
        $banner->product_id = $product_id;
        $banner->qrcode = Input::get('qrcode');
        $banner->created_by = Session::get('username');
        $banner->updated_by = Session::get('username');

        $dest_path = Config::get('constants.ECOM_BANNER_FILE_PATH');
        $max_file_width = 800; // 640
        $max_file_height = 400;

        $file = Input::file('banner_image');
        if ($file == null) {
            return Redirect::back()
                    ->withErrors(['Please upload image']);
        }
        $file_ext = $file->getClientOriginalExtension();
        list($width, $height) = getimagesize($file);

        if ($width > $max_file_width || $height > $max_file_height) {
            return Redirect::back()
                    ->withErrors(['The valid file size should be '.$max_file_height.' x '.$max_file_width.' pixels.']);
        }
        $banner->save();


        $file_name = $banner->id . "." . $file_ext;
        Input::file('banner_image')->move($dest_path . '/', $file_name);
        $banner->file_name = $file_name;
        $banner->save();
        return Redirect::to('/ecombanner');
        
     }

    /**
     * Show the form for editing the specified banner.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $banner = EcomBanners::find($id);
//        $countries = Country::getActiveCountry();
//        $regions      =  Region::where("country_id",$banner->region_country_id)->get();

        return View::make('ecombanner.edit')->with(array(
            'id' => $banner->id,
            'qrcode' => $banner->qrcode,
            'image' => Config::get('constants.ECOM_BANNER_FILE_PATH') . '/' . $banner->file_name,
        ));
    }

     /**
     * Edit Up a sequence and show the form for editing the specified banner.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUp($id)
    {
        $banner         = Banner::find($id);
        $banner->set_sort($id, 'up');

        $banners   = DB::table('jocom_banners')
                    ->orderBy('pos')
                    ->get();
    }

    /**
     * Update the specified banner in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        $banner = EcomBanners::find($id);
        if (Input::get('qrcode') != null && Input::get('qrcode') != '') {
            $product_id = DB::table('jocom_products')
                        ->where('qrcode', '=', Input::get('qrcode'))
                        ->pluck('id');
            if ($product_id == null) {
                return Redirect::back()
                        ->withErrors(['QR Code Not found.']);
            }
        }
        
        $banner->product_id = $product_id;
        $banner->qrcode = Input::get('qrcode');

        $dest_path = Config::get('constants.ECOM_BANNER_FILE_PATH');
        $max_file_width = 800; // 640
        $max_file_height = 400;

        $file = Input::file('banner_image');
        if ($file != null) {
            $file_ext = $file->getClientOriginalExtension();
            list($width, $height) = getimagesize($file);

            if ($width > $max_file_width || $height > $max_file_height) {
                return Redirect::back()
                        ->withErrors(['The valid file size should be '.$max_file_height.' x '.$max_file_width.' pixels.']);
            } else {
                $file_name = $banner->file_name;
                Input::file('banner_image')->move($dest_path . '/', $file_name);
                
            }
        }

        $banner->save();
        return Redirect::to('/ecombanner');
        
    }
 
    /**
     * Remove the specified banner from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $banner = EcomBanners::find($id);

        if (file_exists(Config::get('constants.ECOM_BANNER_FILE_PATH') . '/' . $banner->file_name))
            File::delete(Config::get('constants.ECOM_BANNER_FILE_PATH') . '/' . $banner->file_name);   


        EcomBanners::destroy($id);
        
        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('/ecombanner');

    }
    
    

    ///

    
    // POPUP 
}
?>