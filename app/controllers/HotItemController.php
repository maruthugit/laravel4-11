<?php

class HotItemController extends BaseController {

	protected $hotitem;
	protected $country;

	public function __construct(HotItem $hotitem) {
		$this->hotitem = $hotitem;
	}
	
	/**
     * Display the hotitem page.
     *
     * @return Page
     */
	public function anyIndex() {
		$id         = "";

        if (Input::has('up')) {
           
            $id     = Input::get('up');
            HotItem::set_sort($id, 'up');
           
        } else {
            $id     = Input::get('down');
            HotItem::set_sort($id, 'down');
          
        }

        $hotitems    = DB::table('jocom_hot_items')
                        ->orderBy('pos', 'desc')
                        ->get();


		return View::make('hot_item.index', ['hotitems' => $hotitems]);
		
//		return View::make('hot_item.index');
	}

	/**
     * Display a listing of the hotitem resource.
     *
     * @return Response
     */
	public function anyHotitems() {		

		$hotitems = $this->hotitem->select(array(
										'jocom_hot_items.id', 
										'jocom_hot_items_images.thumb_name',
										'jocom_hot_items.qrcode',
										'jocom_region.region',
										'jocom_hot_items.pos'
									))
									->leftjoin('jocom_hot_items_images', 'jocom_hot_items_images.hot_id', '=', 'jocom_hot_items.id')
									->leftjoin('jocom_region', 'jocom_region.id', '=', 'jocom_hot_items.region_id')
									->groupBy('jocom_hot_items.id')
									->orderBy('jocom_hot_items.pos', 'desc')
									->where('jocom_hot_items.status', '=', '1')
									->where('jocom_hot_items_images.language', '=', 'en');

		return Datatables::of($hotitems)
									->edit_column('thumb_name', '
										@if(file_exists(Config::get(\'constants.HOTITEM_THUMB_FILE_PATH\').\'en/\' . $thumb_name))
			                                {{ HTML::image(Config::get(\'constants.HOTITEM_THUMB_FILE_PATH\').\'en/\' . $thumb_name, null ,array( \'width\' => 100, \'height\' => 70 )) }}
			                            @else
			                                {{ HTML::image(\'media/no_images.jpg/\', null ,array( \'width\' => 100, \'height\' => 70 )) }}
			                            @endif
										')
                                    ->edit_column('qrcode', '<div style="word-break: break-all;"> {{ $qrcode }} </div>')
                                    ->edit_column('region', '<?php if ($region=="" || $region=="0") echo "All Region"; else if ($region!="") echo $region; ?>')
									->edit_column('pos', '
										@if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 7, 3, \'AND\'))
										<input type="hidden" name="currentpos{{$id}}" value="{{$pos}}">
										<a class="btn btn-default btn-sm" title="Edit" data-toggle="tooltip" href="/hot_item/index?up={{$id}}"><i class="fa fa-arrow-up"></i></a> 
										<a class="btn btn-default btn-sm" title="Edit" data-toggle="tooltip" href="/hot_item/index?down={{$id}}"><i class="fa fa-arrow-down"></i></a>
										@else
											{{$pos}}
										@endif
									')
									->add_column('Action', '<a class="btn btn-primary" title="Edit" data-toggle="tooltip" href="/hot_item/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
										@if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 7, 9, \'AND\'))
                                            <a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$thumb_name}}" href="/hot_item/delete/{{$id}}"><i class="fa fa-remove"></i></a>
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
			return View::make('hot_item.create', ['language' => $language,'regions' => $regions,'countries' => $countries]);
		}
		else 
			return View::make('home.denied', array('module' => 'Hot Items > Add Hot Items'));
	}

	/**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
	public function anyStore() {
		
        $hotitem     	= new HotItem;
        $hotitem_input  = Input::all();
        $languages      = Setting::getAllLang();
        $saved          = false;
        $devices        = array('phone' => 'hotitem', 'tablet' => 'hotitem_tab');
        $validator 		= Validator::make(Input::all(), HotItem::$rules, HotItem::$messages);
        $pos 			= DB::table('jocom_hot_items')->select('jocom_hot_items.pos')->orderBy('pos', 'DESC')->first();

        if ($validator->passes()) {
        	$new_pos 				 = $pos->pos + 1;
        	$hotitem->pos 			 = $new_pos;
            $hotitem->qrcode         = Input::get('qrcode');
            // $hotitem->url_link       = Input::get('url_link');
            $hotitem->timestamps     = false;
            $hotitem->insert_by      = Session::get('username');
            $hotitem->insert_date    = date("Y-m-d H:i:s");
//            if(Input::get('platform') == 'JUE'){
//                $hotitem->is_jocom_app    = 0;
//                $hotitem->is_juepin_app   = 1;
//            }else{
//                $hotitem->is_jocom_app    = 1;
//                $hotitem->is_juepin_app   = 0;
//            }
            
            $hotitem->region_country_id    = Input::get('region_country_id');
            $hotitem->region_id   = Input::get('region_id');
            $hotitem_id              = "";


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
                            $dest_path          = Config::get('constants.HOTITEM_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.HOTITEM_THUMB_FILE_PATH'); 
                            $max_file_width     = 645;
                            $max_file_height    = 645;
                            $thumb_width        = 320;
                            $thumb_height       = 220;
                            $max_thumb_width    = 325;
                            $max_thumb_height   = 225;
                        break;
                    
                    case 'tablet':  
                            $dest_path          = Config::get('constants.HOTITEM_TAB_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.HOTITEM_TAB_THUMB_FILE_PATH'); 
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
                        $arr_hotitem     = array();
                        
                        if(Input::hasFile($fname .'_' . $code)) {
                            $file                   = Input::file($fname . "_" . $code);
                            $file_ext               = $file->getClientOriginalExtension();
                            
                            list($width, $height)   = getimagesize($file);

                            if($width > $max_file_width || $height > $max_file_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid file size for '.$type.' ('.$name.') should be 645 x 645 pixels.']);
                            } else {
                                if($saved == false) {
                                    if ($hotitem->save()) {
                                        $saved      = true;
                                        $hotitem_id = $hotitem->id;
                                    }
                                }

                                if($saved == true) {
                                    $file_name                  = $hotitem_id . "." . $file_ext;
                                    $thumb_name                 = $file_name;

                                    $upload_file_succ           = Input::file($fname .'_' . $code)->move($dest_path. $code . '/', $file_name);

                                    $arr_hotitem['hot_id']	    = $hotitem_id;
                                    $arr_hotitem['language']    = $code;
                                    $arr_hotitem['device']      = $type;
                                    $arr_hotitem['insert_by']   = Session::get('username');
                                    $arr_hotitem['insert_date'] = date('Y-m-d H:i:s');

                                    if ($upload_file_succ)  $arr_hotitem['file_name']    = $file_name;
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
                                $thumb_name         = $hotitem_id . "." . $thumb_ext;
                                // $dest_thumb_path    = './' . Config::get('constants.HOTITEM_THUMB_FILE_PATH') . $code . '/';  //'./images/banner/thumbs/';
                                $upload_thumb_succ  = Input::file($fname.'_thumb_' . $code)->move($dest_thumb_path. $code . '/', $thumb_name);

                                if ($upload_thumb_succ)  $arr_hotitem['thumb_name']   = $thumb_name;
                            }
                        }

                        if(Input::hasFile($fname . "_" . $code)) {
                            // echo "<br>[fname: $fname] [code: $code] <br>[".$fname."_thumb_".$code."] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                            if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                // echo "<br> &nbsp;&nbsp; - - -> [THUMB NOT FOUND!][$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                                $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path. $code . '/', $dest_thumb_path. $code . '/');
                                $arr_hotitem['thumb_name'] = $thumb_name;
                            }
                        }

                        if (count($arr_hotitem) > 0)     HotItem::insertHotItemImage($arr_hotitem);
                    }
                }
            }
            return Redirect::to('/hot_item/index');
               
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
    public function anyEdit($item_id) {
    	$hotitem_images = HotItem::getHotItemImages($item_id);
        $languages      = Setting::getAllLang();

        /** NEW **/
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
                    
                 
        $hotitem = HotItem::find($item_id);
        $country_id = $hotitem->region_country_id;
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

    	$hotitem 		= $this->hotitem->select(array(
                                        'jocom_hot_items.id', 
                                        'jocom_hot_items.file_name',
                                        'jocom_hot_items.qrcode',
//                                        'jocom_hot_items.is_jocom_app',
//                                        'jocom_hot_items.is_juepin_app',
                                        'jocom_hot_items.region_country_id',
                                        'jocom_hot_items.region_id',
// 'jocom_hot_items.url_link',
                                        'jocom_hot_items.insert_date'
                                ))
                                ->where('jocom_hot_items.status', '=', '1')
                                ->where('jocom_hot_items.id', '=', $item_id)
                                ->first();
		
        return View::make('hot_item.edit')->with(array('hotitem' => $hotitem, 'languages' => $languages, 'hotitem_images' => $hotitem_images, 'countries' => $countries, 'regions' => $regions));
    }    
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $item_id
     * @return Response
     */
    public function anyUpdate($id) {
    
        $hotitem            = new HotItem();
        $arr_input_all      = Input::all();
        $allowance          = 5;
        $languages          = Setting::getAllLang();
        $devices            = array('phone' => 'hotitem', 'tablet' => 'hotitem_tab');

        if (count($arr_input_all) > 0) {
            $arr_validate       = HotItem::getUpdateRules($arr_input_all);
            $arr_input          = HotItem::getUpdateInputs($arr_input_all);
                if ($arr_validate != 0) {
                    $validator          = Validator::make($arr_input, $arr_validate);

                    if ($validator->passes()) {
                            $arr_udata		= HotItem::getUpdateDbDetails($arr_input);
                            
                            $hotitemData = HotItem::find($id);
//                            if(Input::get('platform') == 'JUE'){
//                                $hotitemData->is_jocom_app    = 0;
//                                $hotitemData->is_juepin_app   = 1;
//                            }else{
//                                $hotitemData->is_jocom_app    = 1;
//                                $hotitemData->is_juepin_app   = 0;
//                            }
                            
                            $hotitemData->region_country_id    = Input::get("region_country_id");
                            $hotitemData->region_id    = Input::get("region_id");
                            
                            $hotitemData->save();

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
                        $dest_path          = Config::get('constants.HOTITEM_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.HOTITEM_THUMB_FILE_PATH'); 
                        $max_file_width     = 645;
                        $max_file_height    = 965;
                        $thumb_width        = 640;
                        $thumb_height       = 400;
                        $max_thumb_width    = 320;
                        $max_thumb_height   = 220;
                    break;
                
                case 'tablet':  
                        $dest_path          = Config::get('constants.HOTITEM_TAB_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.HOTITEM_TAB_THUMB_FILE_PATH'); 
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
                    $arr_hotitem        = array();
                    $old_file_name      = "";
                    $query_type         = "";
                    $thumb_name         = "";

                    if(Input::hasFile($fname .'_' . $code)) {
                        $file                   = Input::file($fname .'_' . $code);
                        $cur_file_name          = $file->getRealPath() . "/" . $file->getClientOriginalName(); 
                        $old_file_name          = HotItem::getOldFilename($id, "actual", $type, $code);               
                        $file_ext               = $file->getClientOriginalExtension();
                        $file_name              = HotItem::getOldFilename($id, "actual", $type, $code);
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
                                $file_name          = $id . "." . $file_ext; 
                                $upload_file_succ   = $file->move($dest_path. $code . '/', $file_name);
                                $thumb_name         = $file_name;
                                                                               
                            } else {
                                $query_type             = "insert";
                                $arr_hotitem['hot_id']	= $id;
                                $arr_hotitem['language']= $code;
                                $arr_hotitem['device']  = $type;
                                $file_name              = $id . "." . $file_ext; 
                                $upload_file_succ       = $file->move($dest_path . $code . '/', $file_name);
                            }
                        }

                        $arr_hotitem['file_name'] = $file_name;
                    } 
                    
                    if(Input::hasFile($fname .'_thumb_' . $code)) {
                        $thumb                  = Input::file($fname .'_thumb_' . $code);
                        $thumb_name             = HotItem::getOldFilename($id, "thumb", $type, $code); 
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
                            
                            if($thumb_name != 0 && $thumb_name !== $cur_thumb_name) {
                                $query_type         = "update";
                                if(file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                    File::delete($dest_thumb_path . $code . '/' . $thumb_name);
                                } 
                                
                                $thumb_name         = $id . "." . $thumb_ext;
                               	$upload_thumb_succ  = $thumb->move($dest_thumb_path . $code . '/', $thumb_name);

                            } else {
                                $query_type             	= "insert";
                                $arr_hotitem['hot_id']		= $id;
                                $arr_hotitem['language'] 	= $code;
                                $thumb_name             	= $id . "." . $thumb_ext;
                                $upload_thumb_succ      	= $thumb->move($dest_thumb_path . $code . '/', $thumb_name);
                            }
                        }

                        $thumb_name                 = $id . "." . $thumb_ext;
                        $arr_hotitem['thumb_name']  = $thumb_name;
                    }
                    
                    $thumb_name = HotItem::getOldFilename($id, "thumb", $type, $code); 

                    if ($thumb_name == "") $thumb_name = $file_name;

                    if(Input::hasFile($fname . "_" . $code)) {
                        if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                            // echo "<br> &nbsp;&nbsp; - - -> [THUMB NOT FOUND!][$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                            $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path . $code . '/', $dest_thumb_path . $code . '/');
                            $arr_hotitem['thumb_name'] = $thumb_name;
                        }
                    }

                    $arr_udata['modify_by']     = Session::get('username');
                    $arr_udata['modify_date']   = date('Y-m-d H:i:s');

                    if($hotitem->updateHotItem($id, $arr_udata)){
                        // echo "<br>[query_type: ".$query_type."]";
                        if(count($arr_hotitem) > 0) {
                            switch ($query_type) {
                                case 'insert'   : 
                                    $arr_hotitem['insert_by']    = Session::get('username');
                                    $arr_hotitem['insert_date']  = date('Y-m-d H:i:s');
                                    HotItem::insertHotItemImage($arr_hotitem);
                                                    
                                    break;

                                case 'update'   : 
                                    $arr_hotitem['modify_by']    = Session::get('username');
                                    $arr_hotitem['modify_date']  = date('Y-m-d H:i:s');
                                    HotItem::updateHotItemImage($id, $code, $type, $arr_hotitem);
                                                    
                                    break;
                            }
                            
                            Session::flash('success', 'Setting has been successfully save!');
                        }
                    } 
                    else {
                        echo "<br>Failed to update Hot Item!";
                    }
                }
            }
        }
        
        $hotitem            = $this->hotitem->find($id);
        $hotitem_images 	= HotItem::getHotItemImages($id);
         $countries = Country::getActiveCountry();
        $regions      =  Region::where("country_id",$hotitem->region_country_id)->get();
        return View::make('hot_item.edit')->with(array(
            'hotitem'        => $hotitem,
            'languages'      => $languages,
            'hotitem_images' => $hotitem_images,
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
	public function anyMovedown($item_id) {
		$getPos 	= $this->hotitem->select('jocom_hot_items.pos')->where('jocom_hot_items.id', '=', $item_id)->first();
		$getNextId 	= $this->hotitem->select('jocom_hot_items.id', 'jocom_hot_items.pos')->where('jocom_hot_items.pos', '=', $getPos->pos + 1)->first();

		if (count($getNextId) > 0) {
			$this->hotitem->where('id', '=', $item_id)
						->update(array(
							'pos' => $getPos->pos + 1,
							'modify_by' => Session::get('username')
							));

			$this->hotitem->where('id', '=', $getNextId->id)
							->update(array(
								'pos' => $getNextId->pos - 1,
								'modify_by' => Session::get('username')
								));
			
			Session::flash('message', 'Successfully updated.');
		}
		
		return Redirect::to('hot_item');
	}

	/**
     * Update the specified resource order in storage.
     *
     * @param  int  $item_id
     * @return Response
     */
	public function anyMoveup($item_id) {
		$getPos 	= $this->hotitem->select('jocom_hot_items.pos')->where('jocom_hot_items.id', '=', $item_id)->first();
		$getNextId 	= $this->hotitem->select('jocom_hot_items.id', 'jocom_hot_items.pos')->where('jocom_hot_items.pos', '=', $getPos->pos - 1)->first();

		if ($getPos->pos > 1) {
			$this->hotitem->where('id', '=', $item_id)
						->update(array(
							'pos' => $getPos->pos - 1,
							'modify_by' => Session::get('username')
							));
		

			$this->hotitem->where('id', '=', $getNextId->id)
						->update(array(
							'pos' => $getNextId->pos + 1,
							'modify_by' => Session::get('username')
							));

			Session::flash('message', 'Successfully updated.');
		}
		
		return Redirect::to('hot_item');
	}

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $item_id
     * @return Response
     */
    public function anyDelete($id) {
        $hotitems 	= HotItem::getHotItemImages($id);
      	$languages	= array('en', 'cn', 'my');

        foreach ($hotitems as $hotitem) {
            if ($hotitem->file_name != "") {
            	foreach($languages as $language) {
                	if(file_exists("./" . Config::get('constants.HOTITEM_FILE_PATH') . "/" . $language . "/" . $hotitem->file_name))
                		File::delete("./" . Config::get('constants.HOTITEM_FILE_PATH') . "/" . $language . "/" . $hotitem->file_name); 
            	}       
            }
            
            if ($hotitem->thumb_name != "") {
            	foreach($languages as $language) {
                	if(file_exists("./" . Config::get('constants.HOTITEM_THUMB_FILE_PATH') . "/" . $language . "/" . $hotitem->thumb_name))
                		File::delete("./" . Config::get('constants.HOTITEM_THUMB_FILE_PATH') . "/" . $language . "/" . $hotitem->thumb_name);
            	}        
            } 
        }

		DB::table('jocom_hot_items_images')->where('hot_id', '=', $id)->delete();
        HotItem::destroy($id);

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('/hot_item/index');
    }
}