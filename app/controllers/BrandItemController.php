<?php

class BrandItemController extends BaseController {

	protected $branditem;
	protected $country;

	public function __construct(BrandItem $branditem) {
		$this->branditem = $branditem;
	}
	
	/**
     * Display the branditem page.
     *
     * @return Page
     */
	public function anyIndex() {
		$id         = "";

        // if (Input::has('up')) {
           
        //     $id     = Input::get('up');
        //     BrandItem::set_sort($id, 'up');
           
        // } else {
        //     $id     = Input::get('down');
        //     BrandItem::set_sort($id, 'down');
          
        // }

        $branditems    = DB::table('jocom_brand_items')
                        ->orderBy('pos', 'desc')
                        ->get();


		return View::make('brand_item.index', ['branditems' => $branditems]);
		
//		return View::make('brand_item.index');
	}

	/**
     * Display a listing of the branditem resource.
     *
     * @return Response
     */
	public function anyBranditems() {		

		$branditems = $this->branditem->select(array(
										'jocom_brand_items.id', 
										'jocom_brand_items_images.thumb_name',
										'jocom_brand_items.qrcode',
										'jocom_region.region',
										'jocom_brand_items.pos'
									))
									->leftjoin('jocom_brand_items_images', 'jocom_brand_items_images.brand_id', '=', 'jocom_brand_items.id')
									->leftjoin('jocom_region', 'jocom_region.id', '=', 'jocom_brand_items.region_id')
									->groupBy('jocom_brand_items.id')
									->orderBy('jocom_brand_items.pos', 'asc')
									->where('jocom_brand_items.status', '=', '1')
									->where('jocom_brand_items_images.language', '=', 'en');

		return Datatables::of($branditems)
									->edit_column('thumb_name', '
										@if(file_exists(Config::get(\'constants.BRANDITEM_THUMB_FILE_PATH\').\'en/\' . $thumb_name))
			                                {{ HTML::image(Config::get(\'constants.BRANDITEM_THUMB_FILE_PATH\').\'en/\' . $thumb_name, null ,array( \'width\' => 100, \'height\' => 70 )) }}
			                            @else
			                                {{ HTML::image(\'media/no_images.jpg/\', null ,array( \'width\' => 100, \'height\' => 70 )) }}
			                            @endif
										')
                                    ->edit_column('qrcode', '<div style="word-break: break-all;"> {{ $qrcode }} </div>')
                                    ->edit_column('region', '<?php if ($region=="" || $region=="0") echo "All Region"; else if ($region!="") echo $region; ?>')
									->edit_column('pos', '
									
											{{$pos}}
									')
									->add_column('Action', '<a class="btn btn-primary" title="Edit" data-toggle="tooltip" href="/brands/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
										@if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 7, 9, \'AND\'))
                                            <a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/brands/delete/{{$id}}"><i class="fa fa-remove"></i></a>
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
		if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 5, 'AND')) {
		    
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
 
		    
		    
			$language  = Setting::getAllLang();
			return View::make('brand_item.create', ['language' => $language,'regions' => $regions,'countries' => $countries]);
		}
		else 
			return View::make('home.denied', array('module' => 'Brand Items > Add Brand'));
	}

	/**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	public function anyStore() {

        $branditem     	= new BrandItem;
        $branditem_input  = Input::all();
        $languages      = Setting::getAllLang();
        $saved          = false;
        $devices        = array('phone' => 'branditem', 'tablet' => 'branditem_tab');
        $validator 		= Validator::make(Input::all(), BrandItem::$rules, BrandItem::$messages);
        $pos 			= DB::table('jocom_brand_items')->select('jocom_brand_items.pos')->orderBy('pos', 'DESC')->first();
        $input_pos=Input::get('pos');

        if ($validator->passes()) {
            $position= DB::table('jocom_brand_items')->select('jocom_brand_items.pos')->where('jocom_brand_items.pos','=',$input_pos)->first();
            if(!$position->pos){
             $new_pos= $input_pos;   
            }else
            {
              $get_ids=DB::table('jocom_brand_items')->select('jocom_brand_items.id','jocom_brand_items.pos')->where('jocom_brand_items.pos','>=',$input_pos)->orderBy('jocom_brand_items.pos','ASC')->get();
              if($get_ids){
                  foreach($get_ids as $replace){
                      $newposto=$replace->pos+1;
                      $newid=$replace->id;
                      if($newposto){
                         $brandsitem=BrandItem::find($newid);
                         $brandsitem->pos=$newposto;
                         $brandsitem->save();
                      }
                        
                  }
              }
              $new_pos= $input_pos;
            }
        	$branditem->pos 			 = $new_pos;
            $branditem->qrcode         = Input::get('qrcode');
            // $branditem->url_link       = Input::get('url_link');
            $branditem->timestamps     = false;
            $branditem->insert_by      = Session::get('username');
            $branditem->insert_date    = date("Y-m-d H:i:s");
//            if(Input::get('platform') == 'JUE'){
//                $branditem->is_jocom_app    = 0;
//                $branditem->is_juepin_app   = 1;
//            }else{
//                $branditem->is_jocom_app    = 1;
//                $branditem->is_juepin_app   = 0;
//            }
            
            $branditem->region_country_id    = Input::get('region_country_id');
            $branditem->region_id   = Input::get('region_id');
            $branditem_id              = "";


            foreach ($devices as $type => $fname) {
                // echo "<br> - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -";
                // echo "<br> [Type: ".$type."] [Fname: $fname]";
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
                            $dest_path          = Config::get('constants.BRANDITEM_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.BRANDITEM_THUMB_FILE_PATH'); 
                            $max_file_width     = 645;
                            $max_file_height    = 645;
                            $thumb_width        = 320;
                            $thumb_height       = 220;
                            $max_thumb_width    = 325;
                            $max_thumb_height   = 225;
                        break;
                    
                    case 'tablet':  
                            $dest_path          = Config::get('constants.BRANDITEM_TAB_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.BRANDITEM_TAB_THUMB_FILE_PATH'); 
                            $max_file_width     = 645;
                            $max_file_height    = 645;
                            $thumb_width        = 320;
                            $thumb_height       = 220;
                            $max_thumb_width    = 325;
                            $max_thumb_height   = 225;
                        break;
                }

                if (count($languages) > 0) {
                    foreach ($languages as $code => $name) {
                        $arr_branditem     = array();
                        
                        if(Input::hasFile($fname .'_' . $code)) {
                            $file                   = Input::file($fname . "_" . $code);
                            $file_ext               = $file->getClientOriginalExtension();
                            
                            list($width, $height)   = getimagesize($file);

                            if($width > $max_file_width || $height > $max_file_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid file size for '.$type.' ('.$name.') should be 645 x 645 pixels.']);
                            } else {
                                if($saved == false) {
                                    if ($branditem->save()) {
                                        $saved      = true;
                                        $branditem_id = $branditem->id;
                                    }
                                }
                                if($saved == true) {
                                    $file_name                  = '' . time(). "." . $file_ext;
                                    $thumb_name                 = $file_name;

                                    $upload_file_succ           = Input::file($fname .'_' . $code)->move($dest_path. $code . '/', $file_name);

                                    $arr_branditem['brand_id']	    = $branditem_id;
                                    $arr_branditem['language']    = $code;
                                    $arr_branditem['device']      = $type;
                                    $arr_branditem['insert_by']   = Session::get('username');
                                    $arr_branditem['insert_date'] = date('Y-m-d H:i:s');

                                    if ($upload_file_succ)  $arr_branditem['file_name']    = $file_name;
                                }
                            }
                        }

                        if(Input::hasFile($fname .'_thumb_' . $code)) {
                            $thumb                  = Input::file($fname . "_thumb_" . $code);
                            $thumb_ext              = $thumb->getClientOriginalExtension();
                            list($width, $height)   = getimagesize($thumb);

                            if($width > $max_thumb_width || $height > $max_thumb_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid thumbnail for '.$type .' ('.$name.') size should be 325 x 225 pixels.']);
                            } else {
                                $thumb_name         = '' . time() ."." . $thumb_ext;
                                $upload_thumb_succ  = Input::file($fname.'_thumb_' . $code)->move($dest_thumb_path. $code . '/', $thumb_name);

                                if ($upload_thumb_succ)  $arr_branditem['thumb_name']   = $thumb_name;
                            }
                        }

                        if(Input::hasFile($fname . "_" . $code)) {
                        
                            if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                
                                $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path. $code . '/', $dest_thumb_path. $code . '/');
                                $arr_branditem['thumb_name'] = $thumb_name;
                            }
                        }

                        if (count($arr_branditem) > 0)     BrandItem::insertBrandItemImage($arr_branditem);
                    }
                }
            }
            return Redirect::to('/brands/index');
               
        } else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
	}
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  int  $item_id
     * @return Response
     */
    public function anyEdit($brand_id) {
    	$branditem_images = BrandItem::getBrandItemImages($brand_id);
        $languages      = Setting::getAllLang();

        /** NEW **/
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
                    
                 
        $branditem = BrandItem::find($brand_id);
        $country_id = $branditem->region_country_id;
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
        
      
        
        /** NEW **/

    	$branditem 		= $this->branditem->select(array(
                                        'jocom_brand_items.id', 
                                        'jocom_brand_items.file_name',
                                        'jocom_brand_items.qrcode',
                                        'jocom_brand_items.pos',
//                                        'jocom_brand_items.is_juepin_app',
                                        'jocom_brand_items.region_country_id',
                                        'jocom_brand_items.region_id',
// 'jocom_brand_items.url_link',
                                        'jocom_brand_items.insert_date'
                                ))
                                ->where('jocom_brand_items.status', '=', '1')
                                ->where('jocom_brand_items.id', '=', $brand_id)
                                ->first();
		
        return View::make('brand_item.edit')->with(array('branditem' => $branditem, 'languages' => $languages, 'branditem_images' => $branditem_images, 'countries' => $countries, 'regions' => $regions));
    }    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $item_id
     * @return Response
     */
    public function anyUpdate($id) {
    
                            
        $branditem            = new BrandItem();
        $arr_input_all      = Input::all();
        
        $allowance          = 5;
        $languages          = Setting::getAllLang();
        $devices            = array('phone' => 'branditem', 'tablet' => 'branditem_tab');

        if (count($arr_input_all) > 0) {
            
            $arr_validate       = BrandItem::getUpdateRules($arr_input_all);
            $arr_input          = BrandItem::getUpdateInputs($arr_input_all);
                if ($arr_validate != 0) {
                    $validator          = Validator::make($arr_input, $arr_validate);

                    if ($validator->passes()) {
                       
                            $arr_udata		= BrandItem::getUpdateDbDetails($arr_input);
                            
                            $branditemData = BrandItem::find($id);
                            $newinput=Input::get("pos");
                            $currentpos=$branditemData->pos;
                            $getAllList=$get_ids=DB::table('jocom_brand_items')->select('jocom_brand_items.id','jocom_brand_items.pos')	->where('jocom_brand_items.status', '=', '1')->orderBy('jocom_brand_items.pos','ASC')->get();
                            if($currentpos>$newinput){
                            for($i=$newinput-1;$i<$currentpos-1;$i++){
                                $currenti=$getAllList[$i]->pos;
                                $pos=$currenti+1;
                                $SavePos=BrandItem::find($getAllList[$i]->id);
                                $SavePos->pos=$pos;
                                $SavePos->save();
                                
                            }
                            }
                           
                            if($currentpos<$newinput){
                            for($i=$currentpos;$i<$newinput;$i++){
                                $currenti=$getAllList[$i]->pos;
                                $pos=$currenti-1;
                                $SavePos=BrandItem::find($getAllList[$i]->id);
                                $SavePos->pos=$pos;
                                $SavePos->save();
                            }
                            }
                            
//                            if(Input::get('platform') == 'JUE'){
//                                $branditemData->is_jocom_app    = 0;
//                                $branditemData->is_juepin_app   = 1;
//                            }else{
//                                $branditemData->is_jocom_app    = 1;
//                                $branditemData->is_juepin_app   = 0;
//                            }

                            $branditemData->pos  = $newinput;
                            $branditemData->region_country_id    = Input::get("region_country_id");
                            $branditemData->region_id    = Input::get("region_id");
                            
                            $branditemData->save();
                           
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
                        $dest_path          = Config::get('constants.BRANDITEM_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.BRANDITEM_THUMB_FILE_PATH'); 
                        $max_file_width     = 645;
                        $max_file_height    = 965;
                        $thumb_width        = 640;
                        $thumb_height       = 400;
                        $max_thumb_width    = 320;
                        $max_thumb_height   = 220;
                    break;
                
                case 'tablet':  
                        $dest_path          = Config::get('constants.BRANDITEM_TAB_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.BRANDITEM_TAB_THUMB_FILE_PATH'); 
                        $max_file_width     = 645;
                        $max_file_height    = 965;
                        $thumb_width        = 640;
                        $thumb_height       = 400;
                        $max_thumb_width    = 645;
                        $max_thumb_height   = 405;
                    break;
            }

            if(count($languages) > 0) {
                foreach ($languages as $code => $name) {
                    $arr_branditem        = array();
                    $old_file_name      = "";
                    $query_type         = "";
                    $thumb_name         = "";

                    if(Input::hasFile($fname .'_' . $code)) {
                        
                        $file                   = Input::file($fname .'_' . $code);
                        $cur_file_name          = $file->getRealPath() . "/" . $file->getClientOriginalName(); 
                        $old_file_name          = BrandItem::getOldFilename($id, "actual", $type, $code);               
                        $file_ext               = $file->getClientOriginalExtension();
                        $file_name              = BrandItem::getOldFilename($id, "actual", $type, $code);
                        list($width, $height)   = getimagesize($file);

                        if($width > $max_file_width || $height > $max_file_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid BANNER size for '.$name.' should be 640 x 400 pixels.']);
                        } else {
                            if($old_file_name != 0 && $old_file_name !== $cur_file_name) {
                                $query_type = "update";

                                /* Delete old file. */
                                if(file_exists($dest_path. $code . '/' . $old_file_name)) { 
                                    File::delete($dest_path. $code . '/' . $old_file_name);
                                }
                                
                                /* Replace with new file. */
                                $file_name          = '' . time(). "." . $file_ext; 
                                $upload_file_succ   = $file->move($dest_path. $code . '/', $file_name);
                                $thumb_name         = $file_name;
                               
                                                                               
                            } else {
                                $query_type             = "insert";
                                $arr_branditem['brand_id']	= $id;
                                $arr_branditem['language']= $code;
                                $arr_branditem['device']  = $type;
                                $file_name              = ''. time()."." . $file_ext; 
                                $upload_file_succ       = $file->move($dest_path . $code . '/', $file_name);
                            }
                        }

                        $arr_branditem['file_name'] = $file_name;
                    } 
                    
                    if(Input::hasFile($fname .'_thumb_' . $code)) {
                        $thumb                  = Input::file($fname .'_thumb_' . $code);
                        $thumb_name             = BrandItem::getOldFilename($id, "thumb", $type, $code); 
                        $cur_thumb_name         = $thumb->getRealPath() . "/" . $thumb->getClientOriginalName(); 
                        $thumb_ext              = $thumb->getClientOriginalExtension();
                        $max_thumb_width        = $thumb_width + $allowance;
                        $max_thumb_height       = $thumb_height + $allowance;
                      
                        list($width, $height)   = getimagesize($thumb);
                       
                        if($width > $max_thumb_width || $height > $max_thumb_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid THUMBNAIL file size for '.$name.' should be 640 x 400 pixels.']);
                        } 
                        else {
                            
                            if($thumb_name !="" && $thumb_name !== $cur_thumb_name) {
                               
                                $query_type         = "update";
                                if(file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                    
                                    File::delete($dest_thumb_path . $code . '/' . $thumb_name);
                                } 
                                
                                $thumb_name         = '' . time() ."." . $thumb_ext;
                               	$upload_thumb_succ  = $thumb->move($dest_thumb_path . $code . '/', $thumb_name);
                               
                            } else {
                                $query_type             	= "insert";
                                $arr_branditem['brand_id']		= $id;
                                $arr_branditem['language'] 	= $code;
                                $thumb_name             	= '' . time() ."." . $thumb_ext;
                                $upload_thumb_succ      	= $thumb->move($dest_thumb_path . $code . '/', $thumb_name);
                            }
                        }

                        $thumb_name                 = '' . time() ."." . $thumb_ext;
                        $arr_branditem['thumb_name']  = $thumb_name;
                    }
                    
                    //$thumb_name = BrandItem::getOldFilename($id, "thumb", $type, $code); 

                    //if ($thumb_name == "") $thumb_name = $file_name;

                    if(Input::hasFile($fname . "_" . $code)) {
                        if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                            // echo "<br> &nbsp;&nbsp; - - -> [THUMB NOT FOUND!][$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                            $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path . $code . '/', $dest_thumb_path . $code . '/');
                            $arr_branditem['thumb_name'] = $thumb_name;
                        }
                    }

                    $arr_udata['modify_by']     = Session::get('username');
                    $arr_udata['modify_date']   = date('Y-m-d H:i:s');

                    if($branditem->updateBrandItem($id, $arr_udata)){
                        // echo "<br>[query_type: ".$query_type."]";
                        if(count($arr_branditem) > 0) {
                            switch ($query_type) {
                                case 'insert'   : 
                                    $arr_branditem['insert_by']    = Session::get('username');
                                    $arr_branditem['insert_date']  = date('Y-m-d H:i:s');
                                    BrandItem::insertBrandItemImage($arr_branditem);
                                                    
                                    break;

                                case 'update'   : 
                                    $arr_branditem['modify_by']    = Session::get('username');
                                    $arr_branditem['modify_date']  = date('Y-m-d H:i:s');
                                    
                                   BrandItem::updateBrandItemImage($id, $code, $type, $arr_branditem);
                                                    
                                    break;
                            }
                            
                            Session::flash('success', 'Setting has been successfully save!');
                        }
                    } 
                    else {
                        echo "<br>Failed to update Brand!";
                    }
                }
            }
        }
        
        $branditem            = $this->branditem->find($id);
        $branditem_images 	= BrandItem::getBrandItemImages($id);
         $countries = Country::getActiveCountry();
        $regions      =  Region::where("country_id",$branditem->region_country_id)->get();
        return View::make('brand_item.edit')->with(array(
            'branditem'        => $branditem,
            'languages'      => $languages,
            'branditem_images' => $branditem_images,
             'regions' => $regions,
            'countries' => $countries
        ));
    }

    /**
     * Update the specified resource order in storage.
     *
     * @param  int  $item_id
     * @return Response
     */
	public function anyMovedown($brand_id) {
		$getPos 	= $this->branditem->select('jocom_brand_items.pos')->where('jocom_brand_items.id', '=', $item_id)->first();
		$getNextId 	= $this->branditem->select('jocom_brand_items.id', 'jocom_brand_items.pos')->where('jocom_brand_items.pos', '=', $getPos->pos + 1)->first();

		if (count($getNextId) > 0) {
			$this->branditem->where('id', '=', $item_id)
						->update(array(
							'pos' => $getPos->pos + 1,
							'modify_by' => Session::get('username')
							));

			$this->branditem->where('id', '=', $getNextId->id)
							->update(array(
								'pos' => $getNextId->pos - 1,
								'modify_by' => Session::get('username')
								));
			
			Session::flash('message', 'Successfully updated.');
		}
		
		return Redirect::to('brands');
	}

	/**
     * Update the specified resource order in storage.
     *
     * @param  int  $item_id
     * @return Response
     */
	public function anyMoveup($item_id) {
		$getPos 	= $this->branditem->select('jocom_brand_items.pos')->where('jocom_brand_items.id', '=', $item_id)->first();
		$getNextId 	= $this->branditem->select('jocom_brand_items.id', 'jocom_brand_items.pos')->where('jocom_brand_items.pos', '=', $getPos->pos - 1)->first();

		if ($getPos->pos > 1) {
			$this->branditem->where('id', '=', $item_id)
						->update(array(
							'pos' => $getPos->pos - 1,
							'modify_by' => Session::get('username')
							));
		

			$this->branditem->where('id', '=', $getNextId->id)
						->update(array(
							'pos' => $getNextId->pos + 1,
							'modify_by' => Session::get('username')
							));

			Session::flash('message', 'Successfully updated.');
		}
		
		return Redirect::to('brands');
	}

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $item_id
     * @return Response
     */
    public function anyDelete($id) {
        $branditems 	= branditem::getbranditemImages($id);
      	$languages	= array('en', 'cn', 'my');

        foreach ($branditems as $branditem) {
            if ($branditem->file_name != "") {
            	foreach($languages as $language) {
                	if(file_exists("./" . Config::get('constants.branditem_FILE_PATH') . "/" . $language . "/" . $branditem->file_name))
                		File::delete("./" . Config::get('constants.branditem_FILE_PATH') . "/" . $language . "/" . $branditem->file_name); 
            	}       
            }
            
            if ($branditem->thumb_name != "") {
            	foreach($languages as $language) {
                	if(file_exists("./" . Config::get('constants.branditem_THUMB_FILE_PATH') . "/" . $language . "/" . $branditem->thumb_name))
                		File::delete("./" . Config::get('constants.branditem_THUMB_FILE_PATH') . "/" . $language . "/" . $branditem->thumb_name);
            	}        
            } 
        }
        $PosShift=BrandItem::find($id);
          $get_ids=DB::table('jocom_brand_items')->select('jocom_brand_items.id','jocom_brand_items.pos')->where('jocom_brand_items.pos','>',$PosShift->pos)->orderBy('jocom_brand_items.pos','ASC')->get();
              if($get_ids){
                  foreach($get_ids as $replace){
                      $newposto=$replace->pos-1;
                      $newid=$replace->id;
                      if($newposto){
                         $brandsitem=BrandItem::find($newid);
                         $brandsitem->pos=$newposto;
                         $brandsitem->save();
                      }
                        
                  }
              }

		DB::table('jocom_brand_items_images')->where('brand_id', '=', $id)->delete();
        branditem::destroy($id);
      

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('/brands/index');
    }
}