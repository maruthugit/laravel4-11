<?php

class BannerController extends BaseController {

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

        $banners    = DB::table('jocom_banners')
                        ->orderBy('pos', 'desc')
                        ->get();

        return View::make('banner.index', ['banners' => $banners]);
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
                           
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
                $banners = $banners->where('region_id', '=', $SysAdminRegion->region_id);
        }

        return Datatables::of($banners)
                                    // ->edit_column('price', '{{money_format(\'%i\', $price)}}')
                        ->edit_column('thumb_name', '
                            @if(file_exists(Config::get(\'constants.BANNER_THUMB_FILE_PATH\').\'en/\' . $thumb_name))
                                {{ HTML::image(Config::get(\'constants.BANNER_THUMB_FILE_PATH\').\'en/\' . $thumb_name, null ,array( \'width\' => 100, \'height\' => 70 )) }}
                            @else
                                {{ HTML::image(\'media/no_images.jpg/\', null ,array( \'width\' => 100, \'height\' => 70 )) }}
                            @endif
                            ')
                        ->edit_column('qrcode', '<div style="word-break: break-all;"> {{ $qrcode }} </div>')     
                        ->edit_column('region', '<?php if ($region=="" || $region=="0") echo "All Region"; else if ($region!="") echo $region; ?>') 
                        ->edit_column('pos', '
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 3, \'AND\'))
                                <a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/banner/index?up={{$id}}"><i class="fa fa-arrow-up"></i></a>
                                <a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/banner/index?down={{$id}}"><i class="fa fa-arrow-down"></i></a>
                            @else
                                {{$pos}}
                            @endif
                            ')
                        ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/banner/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 9, \'AND\'))
                                <a id="deleteBan" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$thumb_name}}" href="/banner/delete/{{$id}}"><i class="fa fa-times"></i></a>
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
            $language  = Setting::getAllLang();

            $charityOptions = CharityCategory::getCharityOption();

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


            return View::make('banner.create', ['language' => $language, 'category' => $charityOptions,'regions' => $regions,'countries' => $countries]);
        }
        else
            return View::make('home.denied', array('module' => 'Banner > Add Banner'));

    }

     /**
     * Store a newly created banner in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        $banner         = new Banner;
        $banner_input   = Input::all();
        $languages      = Setting::getAllLang();
        $saved          = false;
        $devices        = array('phone' => 'banner', 'tablet' => 'banner_tab');
        $validator      = Validator::make(Input::all(), Banner::$rules, Banner::$messages);

        if ($validator->passes()) {
            $banner->qrcode         = Input::get('qrcode');
            $banner->category_id    = Input::get('category_id');
            // $banner->url_link       = Input::get('url_link');
            $banner->timestamps     = false;
            $banner->insert_by      = Session::get('username');
            $banner->insert_date    = date("Y-m-d H:i:s");
            $banner_id              = "";
            $banner->region_country_id = Input::get('region_country_id');
            $banner->region_id         = Input::get('region_id');

            foreach ($devices as $type => $fname) {
                // echo "<br><br> [Type: ".$type."]";
                $lang               = $code;
                $file_name          = "";
                $dest_path          = "";
                $dest_thumb_path    = "";
                $max_file_width     = "";
                $max_file_height    = "";
                $thumb_width        = "";
                $thumb_height       = "";
                $max_thumb_width    = "";
                $max_thumb_height   = "";

                switch ($type) {
                    case 'phone':   
                            $dest_path          = Config::get('constants.BANNER_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.BANNER_THUMB_FILE_PATH'); 
                            $max_file_width     = 960; // 640
                            $max_file_height    = 645;
                            $thumb_width        = 645;
                            $thumb_height       = 405;
                            $max_thumb_width    = 645;
                            $max_thumb_height   = 405;
                        break;
                    
                    case 'tablet':  
                            $dest_path          = Config::get('constants.BANNER_TAB_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH'); 
                             $max_file_width     = 960; // 640
                            $max_file_height    = 645;
                            $thumb_width        = 1024;
                            $thumb_height       = 358;
                            $max_thumb_width    = 1026;
                            $max_thumb_height   = 360;
                        break;
                }

                if (count($languages) > 0) {
                    foreach ($languages as $code => $name) {
                        $arr_banner         = array();
                        
                        if(Input::hasFile($fname . "_" . $code)) {
                            $file                   = Input::file($fname . "_" . $code);
                            $file_ext               = $file->getClientOriginalExtension();
                            list($width, $height)   = getimagesize($file);

                            if($width > $max_file_width || $height > $max_file_height) {
                                return Redirect::back()
                                            ->withErrors(['The valid file size for '.$type.' ('.$name.') should be '.$max_file_height.' x '.$max_file_width.' pixels.']);
                            } else {
                                if($saved == false) {
                                    if($banner->save()) {
                                        $saved      = true; 
                                        $banner_id  = $banner->id;
                                    }
                                }
                                
                                if($saved == true)
                                {
                                    $file_name                  = $banner_id . "." . $file_ext;
                                    $thumb_name                 = $file_name;

                                    $upload_file_succ           = Input::file($fname . "_" . $code)->move($dest_path . $code . '/', $file_name);

                                    $arr_banner['banner_id']    = $banner_id;
                                    $arr_banner['language']     = $code;
                                    $arr_banner['device']       = $type;
                                    $arr_banner['insert_by']    = Session::get('username');
                                    $arr_banner['insert_date']  = date('Y-m-d H:i:s');

                                    if ($upload_file_succ)  $arr_banner['file_name'] = $file_name;
                                }
                            }
                        }

                        if(Input::hasFile($fname . "_thumb_" . $code)) {
                            $thumb                  = Input::file($fname . "_thumb_" . $code);
                            $thumb_ext              = $thumb->getClientOriginalExtension();
                            list($width, $height)   = getimagesize($thumb);

                            if($width > $max_thumb_width || $height > $max_thumb_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid thumbnail for '.$type .' ('.$name.') size should be '.$thumb_height.' x '.$thumb_width.' pixels.']);
                            } else {
                                $thumb_name         = $banner_id . "." . $thumb_ext;
                                // $dest_thumb_path    = Config::get('constants.BANNER_THUMB_FILE_PATH') . $code . '/';  //'./images/banner/thumbs/';
                                $upload_thumb_succ  = Input::file($fname . "_thumb_" . $code)->move($dest_thumb_path . $code . '/', $thumb_name);

                                if ($upload_thumb_succ)  $arr_banner['thumb_name']   = $thumb_name;
                            }
                        }

                        if(Input::hasFile($fname . "_" . $code)) {
                            if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path . $code . '/', $dest_thumb_path . $code . '/');
                                $arr_banner['thumb_name'] = $thumb_name;
                            }
                        }

                        if (count($arr_banner) > 0)     Banner::insertBannerImage($arr_banner);
                    }
                }
            }

            return Redirect::to('/banner/index');
               
        } else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
        
    }

    /**
     * Show the form for editing the specified banner.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $banner         = Banner::find($id);
        $banner_images  = Banner::getBannerImages($id);
        $languages      = Setting::getAllLang();        

        $charityOptions = CharityCategory::getCharityOption();

        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();

        $country_id = $banner->region_country_id;
        if(count($regions) <= 0){
            $regions      = Region::where("country_id",$country_id)->get();
        }

        if($SysAdminRegion->region_id != 0){

            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id",$country_id)->get();

        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
        $countries = Country::getActiveCountry();
        }

//        $countries = Country::getActiveCountry();
//        $regions      =  Region::where("country_id",$banner->region_country_id)->get();

        return View::make('banner.edit')->with(array(
            'banner'        => $banner,
            'languages'     => $languages,
            'banner_images' => $banner_images,
            'category'      => $charityOptions,
            'regions' => $regions,
            'countries' => $countries
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
        $banner             = new Banner();
        $arr_input_all      = Input::all();
        $allowance          = 5;
        $languages          = Setting::getAllLang();
        $arr_udata          = array();
        $devices            = array('phone' => 'banner', 'tablet' => 'banner_tab');

        // var_dump($arr_input_all);exit;

        if (count($arr_input_all) > 0) {
           
            $arr_validate       = Banner::getUpdateRules($arr_input_all);
            $arr_input          = Banner::getUpdateInputs($arr_input_all);

            if($arr_validate != 0) {
                $validator          = Validator::make($arr_input, $arr_validate);

                if ($validator->passes()) {
                    $arr_udata      = Banner::getUpdateDbDetails($arr_input);

                } else {
                    return Redirect::back()
                            ->withInput()
                            ->withErrors($validator)->withInput();
                }
            }
        }

        foreach ($devices as $type => $fname) {
            $file_name          = "";
            $dest_path          = "";
            $dest_thumb_path    = "";
            $max_file_width     = "";
            $max_file_height    = "";
            $thumb_width        = "";
            $thumb_height       = "";
            $max_thumb_width    = "";
            $max_thumb_height   = "";

            switch ($type) {
                case 'phone':   
                        $dest_path          = Config::get('constants.BANNER_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.BANNER_THUMB_FILE_PATH'); 
                            $max_file_width     = 645; // 640
                        $max_file_height    = 960;
                            $thumb_width        = 645;
                            $thumb_height       = 405;
                        $max_thumb_width    = 645;
                        $max_thumb_height   = 405;
                    break;
                
                case 'tablet':  
                        $dest_path          = Config::get('constants.BANNER_TAB_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH'); 
                        $max_file_width     = 960; // 640
                            $max_file_height    = 645;
                        $thumb_width        = 1024;
                        $thumb_height       = 358;
                        $max_thumb_width    = 1026;
                        $max_thumb_height   = 360;






                    break;
            }

            if(count($languages) > 0) {
                foreach ($languages as $code => $name) {
                    $arr_banner         = array();
                    $old_file_name      = "";
                    $query_type         = "";
                    $thumb_name         = "";

                    if(Input::hasFile($fname . "_" . $code)) {
                        $file                   = Input::file($fname . "_" . $code);
                        $cur_file_name          = $file->getRealPath() . "/" . $file->getClientOriginalName(); 
                        $old_file_name          = Banner::getOldFilename($id, "actual", $type, $code);               
                        $file_ext               = $file->getClientOriginalExtension();

                        $file_name              = Banner::getOldFilename($id, "actual", $type, $code);
                        list($width, $height)   = getimagesize($file);

                        // echo "<br>- [width: ".$width."] [height: ".$height."] [file_ext: ".$file_ext."] [old_file_name: ".$old_file_name."] [cur_file_name: ".$cur_file_name."] [dest_path: ".$dest_path."]";

                        if($width > $max_file_width || $height > $max_file_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid BANNER size for '.$type.' ('.$name.') should be '.$max_file_height.' x '.$max_file_width.' pixels.']);
                        } else {
                            // var_dump($old_file_name);

                            if($old_file_name != 0 && $old_file_name !== $cur_file_name) {
                                $query_type = "update";
                                /* Delete old file. */
                                if(file_exists($dest_path . $code . '/' . $old_file_name)) { 
                                    // echo "<br>[".$code."] File EXISTS! - -> ". $dest_path . $code . '/' . $old_file_name;

                                    File::delete($dest_path . $code . '/' . $old_file_name);
                                }
                                                                    /* Replace with new file. */
                                $file_name          = $id . "." . $file_ext; 
                                $upload_file_succ   = $file->move($dest_path . $code . '/', $file_name);
                                $thumb_name         = $file_name;
                                                                               
                            } else {
                                $query_type             = "insert";
                                $arr_banner['banner_id']= $id;
                                $arr_banner['language'] = $code;
                                $arr_banner['device']   = $type;
                                $file_name              = $id . "." . $file_ext; 
                                $upload_file_succ       = $file->move($dest_path . $code . '/', $file_name);
                            }
                        }

                        $arr_banner['file_name'] = $file_name;
                    } 
                    
                    if(Input::hasFile($fname . "_thumb_" . $code)) {
                        $thumb                  = Input::file($fname . "_thumb_" . $code);
                        $thumb_name             = Banner::getOldFilename($id, "thumb", $type, $code); 
                        $cur_thumb_name         = $thumb->getRealPath() . "/" . $thumb->getClientOriginalName(); 
                        $thumb_ext              = $thumb->getClientOriginalExtension();
                        $max_thumb_width        = $thumb_width + $allowance;
                        $max_thumb_height       = $thumb_height + $allowance;

                        list($width, $height)   = getimagesize($thumb);

                        if($width > $max_thumb_width || $height > $max_thumb_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid THUMBNAIL file size for '.$type.' ('.$name.') should be '.$thumb_height.' x '.$thumb_width.' pixels.']);
                        } 
                        else {
                            if($thumb_name != 0 && $thumb_name !== $cur_thumb_name) {
                                $query_type         = "update";

                                if(file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                    File::delete($dest_thumb_path . $code . '/' . $thumb_name);
                                } 
                                
                                $thumb_name                 = $id . "." . $thumb_ext;
                                $upload_thumb_succ          = $thumb->move($dest_thumb_path . $code . '/', $thumb_name);

                            } else {
                                $query_type                 = "insert";
                                $arr_banner['banner_id']    = $id;
                                $arr_banner['language']     = $code;
                                $arr_banner['device']       = $type;
                                $thumb_name                 = $id . "." . $thumb_ext;
                                $upload_thumb_succ          = $thumb->move($dest_thumb_path . $code . '/', $thumb_name);
                            }
                        }

                        $thumb_name                 = $id . "." . $thumb_ext;
                        $arr_banner['thumb_name']   = $thumb_name;
                    }

                    $thumb_name = Banner::getOldFilename($id, "thumb", $type, $code); 

                    if ($thumb_name == "") $thumb_name = $file_name;

                    if(Input::hasFile($fname . "_" . $code)) {
                        if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                            // echo "<br> &nbsp;&nbsp; - - -> [THUMB NOT FOUND!][$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                            $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path . $code . '/', $dest_thumb_path . $code . '/');
                            $arr_banner['thumb_name'] = $thumb_name;
                        }
                    }

                    $countries = Country::getActiveCountry();
                    $regions      =  Region::where("country_id",$countries[0]->id)->get();

                    $arr_udata['modify_by']     = Session::get('username');
                    $arr_udata['modify_date']   = date('Y-m-d H:i:s');

                    $arr_udata['region_country_id'] = Input::get('region_country_id');
                    $arr_udata['region_id']         = Input::get('region_id');

                    if($banner->updateBanner($id, $arr_udata)){
                        if(count($arr_banner) > 0) {
                            switch ($query_type) {
                                case 'insert'   : 
                                    $arr_banner['insert_by']    = Session::get('username');
                                    $arr_banner['insert_date']  = date('Y-m-d H:i:s');

                                    Banner::insertBannerImage($arr_banner);
                                                    
                                    break;

                                case 'update'   : 
                                    $arr_banner['modify_by']    = Session::get('username');
                                    $arr_banner['modify_date']  = date('Y-m-d H:i:s');

                                    Banner::updateBannerImage($id, $code, $type, $arr_banner);
                                                    
                                    break;
                            }
                            
                            Session::flash('success', 'Setting has been successfully save!');
                        }
                    } 
                    else {
                        echo "<br>Failed to update banner!";
                    }
                }

            }
        }
        


        $banner             = Banner::find($id);
        $banner_images      = Banner::getBannerImages($id);

        $charityOptions = CharityCategory::getCharityOption();
        $countries = Country::getActiveCountry();
        $regions      =  Region::where("country_id",$banner->region_country_id)->get();

        return View::make('banner.edit')->with(array(
            'banner'        => $banner,
            'languages'     => $languages,
            'banner_images' => $banner_images,
            'category'      => $charityOptions,
            'regions' => $regions,
            'countries' => $countries

        ));
    }
 
    /**
     * Remove the specified banner from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $date           = date("Ymd_His");
        $banners        = Banner::getBannerImages($id);
        $languages      = array('en', 'cn', 'my');
        $devices        = array('phone', 'tablet');    
              
        foreach ($banners as $banner) 
        {
            foreach ($devices as $device) {
                switch ($device) {
                    case 'phone' : 
                            $file   = Config::get('constants.BANNER_FILE_PATH');
                            $thumb  = Config::get('constants.BANNER_THUMB_FILE_PATH');
                        break;

                    case 'tablet': 
                            $file   = Config::get('constants.BANNER_TAB_FILE_PATH');
                            $thumb  = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH');
                        break;

                }

                if ($banner->file_name != "") {
                    foreach($languages as $language) {
                        if(file_exists($file . "/". $language . "/" . $banner->file_name))
                            File::delete($file . "/". $language . "/" . $banner->file_name);        
                    }
                }
                
                if ($banner->thumb_name != "") {
                    foreach($languages as $language) {
                        if(file_exists($thumb . "/". $language . "/" . $banner->thumb_name))
                            File::delete($thumb . "/". $language . "/" . $banner->thumb_name);        
                    }
                }
            }
        }

        DB::table('jocom_banners_images')->where('banner_id', '=', $id)->delete();

        Banner::destroy($id);
        
        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('/banner/index');
        
        
        
    }
    
    public function anyBannertemplate(){

        $b001_hq_list   = Banner::scopeBannerTemplateList(1);  
        $b002_hq_list   = Banner::scopeBannerTemplateList(2);
        $b003_hq_list  = Banner::scopeBannerTemplateList(3);
        // print_r($b003_hq_list);
        // die();
        $b004_hq_list  = Banner::scopeBannerTemplateList(4);

        $b001_jb_list   = Banner::scopeBannerTemplateList(5);
        $b002_jb_list   = Banner::scopeBannerTemplateList(6);
        $b003_jb_list  = Banner::scopeBannerTemplateList(7);
        $b004_jb_list  = Banner::scopeBannerTemplateList(8);

        $b001_png_list  = Banner::scopeBannerTemplateList(9);
        $b002_png_list  = Banner::scopeBannerTemplateList(10);
        $b003_png_list  = Banner::scopeBannerTemplateList(11);
        $b004_png_list  = Banner::scopeBannerTemplateList(12);

        //HQ
        foreach ($b001_hq_list as $b1hq) {
            $b001_hq[] =  (array) $b1hq;
        }
        foreach ($b002_hq_list as $b2hq) {
            $b002_hq[] =  (array) $b2hq;
        }
        foreach ($b003_hq_list as $b3hq) {
            $b003_hq[] =  (array) $b3hq;
        }
        foreach ($b004_hq_list as $b4hq) {
            $b004_hq[] =  (array) $b4hq;
        }
        //END HQ
        //JB
        foreach ($b001_jb_list as $b1jb) {
            $b001_jb[] =  (array) $b1jb;
        }
        foreach ($b002_jb_list as $b2jb) {
            $b002_jb[] =  (array) $b2jb;
        }
        foreach ($b003_jb_list as $b3jb) {
            $b003_jb[] =  (array) $b3jb;
        }
        foreach ($b004_jb_list as $b4jb) {
            $b004_jb[] =  (array) $b4jb;
        }
        //END JB
        //PNG
        foreach ($b001_png_list as $b1png) {
            $b001_png[] =  (array) $b1png;
        }
        foreach ($b002_png_list as $b2png) {
            $b002_png[] =  (array) $b2png;
        } 
        foreach ($b003_png_list as $b3png) {
            $b003_png[] =  (array) $b3png;
        }
        foreach ($b004_png_list as $b4png) {
            $b004_png[] =  (array) $b4png;
        }
        // END PNG
        // echo "<pre>";
        // print_r($b003_jb);
        // die();
        // echo "</pre>";
        $active = DB::table('jocom_managebanners AS JM')->select('*')->orderby('region_id')->get();

        foreach ($active as $object2) {
            $actives[] =  (array) $object2;
        }

        $status  = array('0' => 'Inactive', '1'=> 'Active');

        return View::make('banner.banner_template')
                    ->with('actives', $actives)
                    ->with('status', $status)
                    ->with('b001_hq', $b001_hq)
                    ->with('b002_hq', $b002_hq)
                    ->with('b003_hq', $b003_hq)
                    ->with('b004_hq', $b004_hq)
                    ->with('b001_jb', $b001_jb)
                    ->with('b002_jb', $b002_jb)
                    ->with('b003_jb', $b003_jb)
                    ->with('b004_jb', $b004_jb)
                    ->with('b001_png', $b001_png)
                    ->with('b002_png', $b002_png)
                    ->with('b003_png', $b003_png)
                    ->with('b004_png', $b004_png);
    }

    public function anyBannertemplateupdate(){

        $B001_hq = Input::get('B001_hq');
        $B001_hq_ori = Input::get('B001_hq_ori');
        $B002_hq = Input::get('B002_hq');
        $B002_hq_ori = Input::get('B002_hq_ori');
        $B003_hq = Input::get('B003_hq');
        $B003_hq_ori = Input::get('B003_hq_ori');
        $B004_hq = Input::get('B004_hq');
        $B004_hq_ori = Input::get('B004_hq_ori');

        $B001_jb = Input::get('B001_jb');
        $B001_jb_ori = Input::get('B001_jb_ori');
        $B002_jb = Input::get('B002_jb');
        $B002_jb_ori = Input::get('B002_jb_ori');
        $B003_jb = Input::get('B003_jb');
        $B003_jb_ori = Input::get('B003_jb_ori');
        $B004_jb = Input::get('B004_jb');
        $B004_jb_ori = Input::get('B004_jb_ori');

        $B001_png = Input::get('B001_png');
        $B001_png_ori = Input::get('B001_png_ori');
        $B002_png = Input::get('B002_png');
        $B002_png_ori = Input::get('B002_png_ori');
        $B003_png = Input::get('B003_png');
        $B003_png_ori = Input::get('B003_png_ori');
        $B004_png = Input::get('B004_png');
        $B004_png_ori = Input::get('B004_png_ori');

        $image1 = Input::file('image1');
        $image2 = Input::file('image2');
        $image3 = Input::file('image3');
        $image4 = Input::file('image4');
        $image5 = Input::file('image5');
        $image6 = Input::file('image6');
        $image7 = Input::file('image7');
        $image8 = Input::file('image8');
        $image9 = Input::file('image9');
        $image10 = Input::file('image10');
        $image11 = Input::file('image11');
        $image12 = Input::file('image12');

        $image13 = Input::file('image13');
        $image14 = Input::file('image14');
        $image15 = Input::file('image15');
        $image16 = Input::file('image16');
        $image17 = Input::file('image17');
        $image18 = Input::file('image18');
        $image19 = Input::file('image19');
        $image20 = Input::file('image20');
        $image21 = Input::file('image21');
        $image22 = Input::file('image22');
        $image23 = Input::file('image23');
        $image24 = Input::file('image24');

        $image25 = Input::file('image25');
        $image26 = Input::file('image26');
        $image27 = Input::file('image27');
        $image28 = Input::file('image28');
        $image29 = Input::file('image29');
        $image30 = Input::file('image30');
        $image31 = Input::file('image31');
        $image32 = Input::file('image32');
        $image33 = Input::file('image33');
        $image34 = Input::file('image34');
        $image35 = Input::file('image35');
        $image36 = Input::file('image36');

        $qrcode1 = Input::get('qrcode1');
        $qrcode1_ori = Input::get('qrcode1_ori');
        $qrcode2 = Input::get('qrcode2');
        $qrcode2_ori = Input::get('qrcode2_ori');
        $qrcode3 = Input::get('qrcode3');
        $qrcode3_ori = Input::get('qrcode3_ori');
        $qrcode4 = Input::get('qrcode4');
        $qrcode4_ori = Input::get('qrcode4_ori');
        $qrcode5 = Input::get('qrcode5');
        $qrcode5_ori = Input::get('qrcode5_ori');
        $qrcode6 = Input::get('qrcode6');
        $qrcode6_ori = Input::get('qrcode6_ori');
        $qrcode7 = Input::get('qrcode7');
        $qrcode7_ori = Input::get('qrcode7_ori');
        $qrcode8 = Input::get('qrcode8');
        $qrcode8_ori = Input::get('qrcode8_ori');
        $qrcode9 = Input::get('qrcode9');
        $qrcode9_ori = Input::get('qrcode9_ori');
        $qrcode10 = Input::get('qrcode10');
        $qrcode10_ori = Input::get('qrcode10_ori');
        $qrcode11 = Input::get('qrcode11');
        $qrcode11_ori = Input::get('qrcode11_ori');
        $qrcode12 = Input::get('qrcode12');
        $qrcode12_ori = Input::get('qrcode12_ori');

        $qrcode13 = Input::get('qrcode13');
        $qrcode13_ori = Input::get('qrcode13_ori');
        $qrcode14 = Input::get('qrcode14');
        $qrcode14_ori = Input::get('qrcode14_ori');
        $qrcode15 = Input::get('qrcode15');
        $qrcode15_ori = Input::get('qrcode15_ori');
        $qrcode16 = Input::get('qrcode16');
        $qrcode16_ori = Input::get('qrcode16_ori');
        $qrcode17 = Input::get('qrcode17');
        $qrcode17_ori = Input::get('qrcode17_ori');
        $qrcode18 = Input::get('qrcode18');
        $qrcode18_ori = Input::get('qrcode18_ori');
        $qrcode19 = Input::get('qrcode19');
        $qrcode19_ori = Input::get('qrcode19_ori');
        $qrcode20 = Input::get('qrcode20');
        $qrcode20_ori = Input::get('qrcode20_ori');
        $qrcode21 = Input::get('qrcode21');
        $qrcode21_ori = Input::get('qrcode21_ori');
        $qrcode22 = Input::get('qrcode22');
        $qrcode22_ori = Input::get('qrcode22_ori');
        $qrcode23 = Input::get('qrcode23');
        $qrcode23_ori = Input::get('qrcode23_ori');
        $qrcode24 = Input::get('qrcode24');
        $qrcode24_ori = Input::get('qrcode24_ori');

        $qrcode25 = Input::get('qrcode25');
        $qrcode25_ori = Input::get('qrcode25_ori');
        $qrcode26 = Input::get('qrcode26');
        $qrcode26_ori = Input::get('qrcode26_ori');
        $qrcode27 = Input::get('qrcode27');
        $qrcode27_ori = Input::get('qrcode27_ori');
        $qrcode28 = Input::get('qrcode28');
        $qrcode28_ori = Input::get('qrcode28_ori');
        $qrcode29 = Input::get('qrcode29');
        $qrcode29_ori = Input::get('qrcode29_ori');
        $qrcode30 = Input::get('qrcode30');
        $qrcode30_ori = Input::get('qrcode30_ori');
        $qrcode31 = Input::get('qrcode31');
        $qrcode31_ori = Input::get('qrcode31_ori');
        $qrcode32 = Input::get('qrcode32');
        $qrcode32_ori = Input::get('qrcode32_ori');
        $qrcode33 = Input::get('qrcode33');
        $qrcode33_ori = Input::get('qrcode33_ori');
        $qrcode34 = Input::get('qrcode34');
        $qrcode34_ori = Input::get('qrcode34_ori');
        $qrcode35 = Input::get('qrcode35');
        $qrcode35_ori = Input::get('qrcode35_ori');
        $qrcode36 = Input::get('qrcode36');
        $qrcode36_ori = Input::get('qrcode36_ori');

        $unique = time();
        $path = Config::get('constants.NEW_BANNER_FILE_PATH');

        //STATUS
        if ($B001_hq != $B001_hq_ori) {
            $type = 'B001';
            $region = '1';
            Banner::scopeUpdateBannerTemplateStatus($B001_hq,$type,$region);   
        }

        if ($B002_hq != $B002_hq_ori) {
            $type = 'B002';
            $region = '1';
            Banner::scopeUpdateBannerTemplateStatus($B002_hq,$type,$region);   
        }

        if ($B003_hq != $B003_hq_ori) {
            $type = 'B003';
            $region = '1';
            Banner::scopeUpdateBannerTemplateStatus($B003_hq,$type,$region);   
        }

        if ($B004_hq != $B004_hq_ori) {
            $type = 'B004';
            $region = '1';
            Banner::scopeUpdateBannerTemplateStatus($B004_hq,$type,$region);   
        }

        if ($B001_jb != $B001_jb_ori) {
            $type = 'B001';
            $region = '2';
            Banner::scopeUpdateBannerTemplateStatus($B001_jb,$type,$region);   
        }

        if ($B002_jb != $B002_jb_ori) {
            $type = 'B002';
            $region = '2';
            Banner::scopeUpdateBannerTemplateStatus($B002_jb,$type,$region);   
        }
        if ($B003_jb != $B003_jb_ori) {
            $type = 'B003';
            $region = '2';
            Banner::scopeUpdateBannerTemplateStatus($B003_jb,$type,$region);   
        }

        if ($B004_jb != $B004_jb_ori) {
            $type = 'B004';
            $region = '2';
            Banner::scopeUpdateBannerTemplateStatus($B004_jb,$type,$region);   
        }

        if ($B001_png != $B001_png_ori) {
            $type = 'B001';
            $region = '3';
            Banner::scopeUpdateBannerTemplateStatus($B001_png,$type,$region);   
        }

        if ($B002_png != $B002_png_ori) {
            $type = 'B002';
            $region = '3';
            Banner::scopeUpdateBannerTemplateStatus($B002_png,$type,$region);   
        }
        if ($B003_png != $B003_png_ori) {
            $type = 'B003';
            $region = '3';
            Banner::scopeUpdateBannerTemplateStatus($B003_png,$type,$region);   
        }

        if ($B004_png != $B004_png_ori) {
            $type = 'B004';
            $region = '3';
            Banner::scopeUpdateBannerTemplateStatus($B004_png,$type,$region);   
        }

        //STATUS

        //IMAGE & QRCDOE HQ
        if ($image1!='') {
            $id = 1;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image1->getClientOriginalExtension();           
            $image1->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);   
        }
        if ($qrcode1!=$qrcode1_ori) {
            $qrcode = $qrcode1;
            $id = 1;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);            
        }
        if ($image2!='') {           
            $id = 1;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image2->getClientOriginalExtension();            
            $image2->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);   
        }
        if ($qrcode2!=$qrcode2_ori) {
            $qrcode = $qrcode2;
            $id = 1;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image3!='') {
            $id = 1; 
            $seq = 3;       
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image3->getClientOriginalExtension();           
            $image3->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode3!=$qrcode3_ori) {
            $qrcode = $qrcode3;
            $id = 1;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image4!='') {
            $id = 2;
            $seq = 1;   
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image4->getClientOriginalExtension();
            $image4->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode4!=$qrcode4_ori) {
            $qrcode = $qrcode4;
            $id = 2;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image5!='') {
            $id = 2; 
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image5->getClientOriginalExtension();           
            $image5->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode5!=$qrcode5_ori) {
            $qrcode = $qrcode5;
            $id = 2;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image6!='') {
            $id = 2;
            $seq = 3; 
            $image = $id . '-' . $seq . '-' . '.' . $image6->getClientOriginalExtension();           
            $image6->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode6!=$qrcode6_ori) {
            $qrcode = $qrcode6;
            $id = 2;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image7!='') {
            $id = 3;
            $seq = 1; 
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image7->getClientOriginalExtension();          
            $image7->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode7!=$qrcode7_ori) {
            $qrcode = $qrcode7;
            $id = 3;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image8!='') {
            $id = 3;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image8->getClientOriginalExtension();
            $image8->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode8!=$qrcode8_ori) {
            $qrcode = $qrcode8;
            $id = 3;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image9!='') {
            $id = 3;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image9->getClientOriginalExtension();           
            $image9->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode9!=$qrcode9_ori) {
            $qrcode = $qrcode9;
            $id = 3;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image10!='') {
            $id = 4;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image10->getClientOriginalExtension();
            $image10->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode10!=$qrcode10_ori) {
            $qrcode = $qrcode10;
            $id = 4;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image11!='') {
            $id = 4;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image11->getClientOriginalExtension();    
            $image11->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode11!=$qrcode11_ori) {
            $qrcode = $qrcode11;
            $id = 4;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image12!='') {
            $id = 4;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image12->getClientOriginalExtension();    
            $image12->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode12!=$qrcode12_ori) {
            $qrcode = $qrcode12;
            $id = 4;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        // END HQ
        if ($image13!='') {
            $id = 5;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image13->getClientOriginalExtension();       
            $image13->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode13!=$qrcode13_ori) {
            $qrcode = $qrcode13;
            $id = 5;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image14!='') {
            $id = 5;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image14->getClientOriginalExtension();       
            $image14->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode14!=$qrcode14_ori) {
            $qrcode = $qrcode14;
            $id = 5;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image15!='') {
            $id = 5;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image15->getClientOriginalExtension();      
            $image15->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode15!=$qrcode15_ori) {
            $qrcode = $qrcode15;
            $id = 5;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        if ($image16!='') {
            $id = 6;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image16->getClientOriginalExtension();           
            $image16->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);   
        }
        if ($qrcode16!=$qrcode16_ori) {
            $qrcode = $qrcode16;
            $id = 6;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);            
        }
        if ($image17!='') {           
            $id = 6;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image17->getClientOriginalExtension();            
            $image17->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);   
        }
        if ($qrcode17!=$qrcode17_ori) {
            $qrcode = $qrcode17;
            $id = 6;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image18!='') {
            $id = 6;
            $seq = 3;       
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image18->getClientOriginalExtension();           
            $image18->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode18!=$qrcode18_ori) {
            $qrcode = $qrcode18;
            $id = 6;
            $seq = 3;   
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image19!='') {
            $id = 7;
            $seq = 1;     
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image19->getClientOriginalExtension();
            $image19->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode19!=$qrcode19_ori) {
            $qrcode = $qrcode19;
            $id = 7;
            $seq = 1;   
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image20!='') {
            $id = 7;
            $seq = 3;   
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image20->getClientOriginalExtension();           
            $image20->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode20!=$qrcode20_ori) {
            $qrcode = $qrcode20;
            $id = 7;
            $seq = 3;   
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image21!='') {
            $id = 7;
            $seq = 2;   
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image21->getClientOriginalExtension();           
            $image21->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode21!=$qrcode21_ori) {
            $qrcode = $qrcode21;
            $id = 7;
            $seq = 2;   
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image22!='') {
            $id = 8;
            $seq = 1;   
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image22->getClientOriginalExtension();          
            $image22->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode22!=$qrcode22_ori) {
            $qrcode = $qrcode22;
            $id = 8;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image23!='') {
            $id = 8;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image23->getClientOriginalExtension();
            $image23->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode23!=$qrcode23_ori) {
            $qrcode = $qrcode23;
            $id = 8;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image24!='') {
            $id = 8;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image24->getClientOriginalExtension();           
            $image24->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode24!=$qrcode24_ori) {
            $qrcode = $qrcode24;
            $id = 8;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image25!='') {
            $id = 9;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image25->getClientOriginalExtension();
            $image25->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode25!=$qrcode25_ori) {
            $qrcode = $qrcode25;
            $id = 9;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image26!='') {
            $id = 9;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image26->getClientOriginalExtension();    
            $image26->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode26!=$qrcode26_ori) {
            $qrcode = $qrcode26;
            $id = 9;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image27!='') {
            $id = 9;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image27->getClientOriginalExtension();    
            $image27->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode27!=$qrcode27_ori) {
            $qrcode = $qrcode27;
            $id = 9;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        // END HQ
        if ($image28!='') {
            $id = 10;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image28->getClientOriginalExtension();       
            $image28->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode28!=$qrcode28_ori) {
            $qrcode = $qrcode28;
            $id = 10;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image29!='') {
            $id = 10;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image29->getClientOriginalExtension();       
            $image29->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode29!=$qrcode29_ori) {
            $qrcode = $qrcode29;
            $id = 10;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image30!='') {
            $id = 10;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image30->getClientOriginalExtension();      
            $image30->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode30!=$qrcode30_ori) {
            $qrcode = $qrcode30;
            $id = 10;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        if ($image31!='') {
            $id = 11;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image31->getClientOriginalExtension();      
            $image31->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode31!=$qrcode31_ori) {
            $qrcode = $qrcode31;
            $id = 11;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        if ($image32!='') {
            $id = 11;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image32->getClientOriginalExtension();       
            $image32->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode32!=$qrcode32_ori) {
            $qrcode = $qrcode32;
            $id = 11;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image33!='') {
            $id = 11;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image33->getClientOriginalExtension();      
            $image33->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode33!=$qrcode33_ori) {
            $qrcode = $qrcode33;
            $id = 11;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        if ($image34!='') {
            $id = 12;
            $seq = 1;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image34->getClientOriginalExtension();       
            $image34->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }

        if ($qrcode34!=$qrcode34_ori) {
            $qrcode = $qrcode34;
            $id = 12;
            $seq = 1;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }
        if ($image35!='') {
            $id = 12;
            $seq = 2;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image35->getClientOriginalExtension();      
            $image35->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode35!=$qrcode35_ori) {
            $qrcode = $qrcode35;
            $id = 12;
            $seq = 2;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        if ($image36!='') {
            $id = 12;
            $seq = 3;
            $image = $id . '-' . $seq . '-' . $unique . '.' . $image36->getClientOriginalExtension();      
            $image36->move($path, $image);
            Banner::scopeUpdateBannerTemplateImage($image,$id,$seq);
        }
        if ($qrcode36!=$qrcode36_ori) {
            $qrcode = $qrcode36;
            $id = 12;
            $seq = 3;
            Banner::scopeUpdateBannerTemplateQrcode($qrcode,$id,$seq);
        }

        return Redirect::back()->with('success','Banner has been updated.');

    }
    
    
    // POPUP //
    
    public function anyPopupcreate()
    {   
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
        {
            $language  = Setting::getAllLang();

            $charityOptions = CharityCategory::getCharityOption();
            
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
            

            return View::make('banner.create_popup', ['language' => $language, 'category' => $charityOptions,'regions' => $regions,'countries' => $countries]);
        }
        else
            return View::make('home.denied', array('module' => 'Banner > Add Banner'));

    }
    
    public function anyPopup()
    {   
       
        return View::make('banner.popup');

    }
    
    public function anyGetpopup()
    {   
       
        $user_id = Session::get("user_id");
                
        $tasks = DB::table('jocom_popup_banner AS JPB')->select(array(
            'JPB.id',
            'JPB.region_id',
            'JPB.country_id',
            'JPB.qr_code',
            'JPB.category_id',
            'JPB.from_date',
            'JPB.to_date',
            'JPB.description',
            'JPB.created_at',
            'JPB.created_by'
           ))
           ->where('JPB.status', '=',1)
           ->orderBy('JPB.id','DESC');
        
        return Datatables::of($tasks)
                
        ->add_column('country_name', function($tasks){
            $Country = Country::find($tasks->country_id);
            return $Country->name;
        })
        ->add_column('region_name', function($tasks){
            if($tasks->region_id == 0){
                return 'All Region';
            }else{
                $Region = Region::find($tasks->region_id);
                return $Region->region;
            }
           
        })

        ->make(true);

    }
    
    public function anySavepopup(){
        
        
        try{
            
            DB::beginTransaction();
            
            //             echo "<pre>";
            // print_r(Input::all());
            // echo "</pre>";
            // die();
           
            
            $id = Input::get("id") > 0 ? Input::get("id") : 0 ;
            
            $region_id= Input::get("region_id");
            $country_id = Input::get("region_country_id");
            $qr_code = Input::get("qrcode");
            $category_id = Input::get("category_id");
            $from_date = Input::get("from_date");
            $to_date = Input::get("to_date");
            $description = Input::get("description");
            $activation = Input::get("activation");
            $removeList = array();
          
            if($id > 0 ){
                $Popup = PopupBanner::find($id);
                $removeList = Input::get("image_rmv");
            }else{
                $Popup = new PopupBanner();
            }
            
            $Popup->region_id = $region_id;
            $Popup->country_id = $country_id;
            $Popup->qr_code = $qr_code;
            $Popup->category_id = $category_id;
            $Popup->from_date = $from_date;
            $Popup->to_date = $to_date;
            $Popup->description = $description;
            $Popup->activation = 1;
            
            $Popup->created_by = Session::get("username");
           
            if($Popup->save()){
                
                $PopupID = $Popup->id;
                
                if(Input::hasFile('image_1')){
                    
                    $file_1 = Input::file("image_1");
                    $file_ext = ".".$file_1->getClientOriginalExtension();
                    $file_name_1  = $PopupID . "_image1" . $file_ext;
                    $Popup->image_1 = $file_name_1;
                    
                }
                
                if(Input::hasFile('image_2')){
                    
                    $file_2 = Input::file("image_2");
                    $file_ext = ".".$file_2->getClientOriginalExtension();
                    $file_name_2  = $PopupID . "_image2" . $file_ext;
                    $Popup->image_2 = $file_name_2;
                    
                }
                
                if(Input::hasFile('image_3')){
                    
                    $file_3 = Input::file("image_3");
                    $file_ext = ".".$file_3->getClientOriginalExtension();
                    $file_name_3  = $PopupID . "_image3" . $file_ext;
                    $Popup->image_3 = $file_name_3;
                    
                }
                
                if(Input::hasFile('image_4')){
                    
                    $file_4 = Input::file("image_4");
                    $file_ext = ".".$file_4->getClientOriginalExtension();
                    $file_name_4  = $PopupID . "_image4" . $file_ext;
                    $Popup->image_4 = $file_name_4;
                    
                }
                
                if(Input::hasFile('image_5')){
                    
                    $file_5 = Input::file("image_5");
                    $file_ext = ".".$file_5->getClientOriginalExtension();
                    $file_name_5  = $PopupID . "_image5" . $file_ext;
                    $Popup->image_5 = $file_name_5;
                    
                }
                
                
                if($Popup->save()){
                    
                    //Path
                    $dest_path = Config::get('constants.BANNER_POPUP');
                    
                    for($x=1;$x<=5;$x++){
                        
                        if(Input::hasFile('image_'.$x)){
                            $file_ext = ".".Input::file('image_'.$x)->getClientOriginalExtension();
                            $file_name = $PopupID . "_image".$x.$file_ext;
                            Input::file('image_'.$x)->move($dest_path . '/', $file_name);
                        }
                    }
                    
                }
                
                foreach ($removeList as $value) {
                   
                    if($value != ''){
                        $column = "image_".$value;
                        $Popup->$column = '';
                    }
                    $Popup->save();
                }
                
                DB::commit();
                
                
                if($id>0){
                    return Redirect::to('banner/popupedit/'.$PopupID)
                        ->with('message', 'Saved Succesfully!')
                        ->with('success', '1'); 
                }else{
                    return Redirect::to('banner/popupcreate')
                        ->with('message', 'Saved Succesfully!')
                        ->with('success', '1'); 
                }
                
                
            }else{
                throw new Exception('Failed');
            }
            
            
        } catch (Exception $ex) {
            
            DB::rollback();
            return Redirect::back()->withInput()->with('success', '0')->with('message', 'Save Failed');
        }
        
          
    }
    
    public function anyPopupedit($id){
        
        $popupID = $id;
        
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
        {
            $language  = Setting::getAllLang();

            $charityOptions = CharityCategory::getCharityOption();
            
            $sysAdminInfo = User::where("username",Session::get('username'))->first();
            $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
                        
            
            if(count($SysAdminRegion) <= 0){
                $regions      = Region::where("country_id",$country_id)->get();
            }

            if($SysAdminRegion->region_id != 0){
                $Region = Region::find($SysAdminRegion->region_id);
                $country_id = $Region->country_id;
                
                $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
                $countries = Country::where("id",$country_id)->get();

            }else{
                $regions = Region::where("country_id",458)
                    ->where("activation",1)->get();
                $countries = Country::getActiveCountry();
            }
            
            
            $PopupBanner = PopupBanner::find($popupID);

            $directoryPath = Config::get('constants.BANNER_POPUP');

            return View::make('banner.edit_popup', ['popup' => $PopupBanner,'regions' => $regions,'countries' => $countries,"path"=>$directoryPath]);
        }
        else
            return View::make('home.denied', array('module' => 'Banner > Edit Popup'));


    }
    
    // POPUP 
}
?>