<?php

class LatestnewsController extends BaseController {

    public function __construct()
    {
        
        // $this->beforeFilter('auth');
        // echo "<br>check authentication ";
    }


    /**
     * Display a listing of the latest news.
     *
     * @return Response
     */
    public function anyIndex()
    {

         $id         = "";
        if (Input::has('up')) {
            $id     = Input::get('up');
            Latestnews::set_sort($id, 'up');

        } else {
            $id     = Input::get('down');
            Latestnews::set_sort($id, 'down');

        }

        $news    = DB::table('jocom_latest_news')
                        ->orderBy('pos', 'desc')
                        ->get();

        // dd(DB::getQueryLog());
            
        return View::make('latestnews.index', ['news' => $news]);
        
    
    }



    /**
     * Display a listing of the latest news on datatable.
     *
     * @return Response
     */
    public function anyLatestnews() {     
        $latestnews = Latestnews::select('jocom_latest_news.id', 'jocom_latest_news_images.thumb_name', 'jocom_latest_news.qrcode', 'jocom_latest_news.pos')
                            ->leftjoin('jocom_latest_news_images', 'jocom_latest_news_images.news_id', '=', 'jocom_latest_news.id')
                            ->groupBy('jocom_latest_news.id')
                            ->where('jocom_latest_news.active_status', '=', '1')
                            ->where('jocom_latest_news_images.language', '=', 'en');

        return Datatables::of($latestnews)
                                    // ->edit_column('price', '{{money_format(\'%i\', $price)}}')
                        ->edit_column('thumb_name', 
                            '@if(file_exists(Config::get(\'constants.LATESTNEWS_THUMB_FILE_PATH\').\'en/\' . $thumb_name))
                                {{ HTML::image(Config::get(\'constants.LATESTNEWS_THUMB_FILE_PATH\') .\'en/\' . $thumb_name, null ,array( \'width\' => 100, \'height\' => 70 )) }}
                            @else
                                {{ HTML::image(\'media/no_images.jpg/\', null ,array( \'width\' => 100, \'height\' => 70 )) }}
                            @endif
                            ')
                        ->edit_column('qrcode', '<div style="word-break: break-all;"> {{ $qrcode }} </div>')
                        ->edit_column('pos', 

                            '<a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/latestnews/index?up={{$id}}"><i class="fa fa-arrow-up"></i></a>
                             <a class="btn btn-default btn-sm" title="" data-toggle="tooltip" href="/latestnews/index?down={{$id}}"><i class="fa fa-arrow-down"></i></a>
                          
                            ')
                        ->add_column('Action', 

                            '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/latestnews/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        	 <a id="deleteNews" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$thumb_name}}" href="/latestnews/delete/{{$id}}"><i class="fa fa-times"></i></a>
                           
                            ')
                        ->make();
    }

    

    /**
     * Create lastest news
     *
     * @return  Response 
     */
    public function anyCreate()
    {
    	if ( Permission::CheckAccessLevel(Session::get('role_id'), 6, 5, 'AND')) {
            $language  = Setting::getAllLang();
            return View::make('latestnews.create', ['language' => $language]);
        }
   		else
			return View::make('home.denied', array('module' => 'Latest News > Add Latest News'));

    }


    /**
     * Store latest news
     *
     * @return  Response 
     * 
     */
    public function anyStore()
    {
        $news         	= new Latestnews;
        $news_input   	= Input::all();
        $languages      = Setting::getAllLang();
        $saved          = false;
        $devices        = array('phone' => 'news', 'tablet' => 'news_tab');
        $validator 		= Validator::make(Input::all(), Latestnews::$rules, Latestnews::$messages);

        if ($validator->passes()) {
            $news->qrcode           = Input::get('qrcode');
            $news->title            = Input::get('title');
            $news->description      = Input::get('description');
            // $news->url_link       	= Input::get('url_link');
            $news->timestamps     	= false;
            $news->insert_by        = Session::get('username');
            $news->insert_date    	= date("Y-m-d H:i:s");
            $news_id                = "";

            foreach ($devices as $type => $fname) {
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
                            $dest_path          = Config::get('constants.LATESTNEWS_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.LATESTNEWS_THUMB_FILE_PATH'); 
                            $max_file_width     = 645;
                            $max_file_height    = 965;
                            $thumb_width        = 640;
                            $thumb_height       = 400;
                            $max_thumb_width    = 645;
                            $max_thumb_height   = 405;
                        break;
                    
                    case 'tablet':  
                            $dest_path          = Config::get('constants.LATESTNEWS_TAB_FILE_PATH');
                            $dest_thumb_path    = Config::get('constants.LATESTNEWS_TAB_THUMB_FILE_PATH'); 
                            $max_file_width     = 645;
                            $max_file_height    = 965;
                            $thumb_width        = 640;
                            $thumb_height       = 400;
                            $max_thumb_width    = 645;
                            $max_thumb_height   = 405;
                        break;
                }

                if (count($languages) > 0) {
                    foreach ($languages as $code => $name) 
                    {
                        $arr_news     = array(); 

                        if(Input::hasFile($fname . "_" . $code)) {
                            $file                   = Input::file($fname . "_" . $code);
                        	$file_ext               = $file->getClientOriginalExtension();
                            list($width, $height)   = getimagesize($file);

                            if($width > $max_file_width || $height > $max_file_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid file size for '.$type.' ('.$name.') should be 640 x 960 pixels.']);
                            } else {
                                if ($saved == false) {
                                    if ($news->save()) {
                                        $saved      = true;
                                        $news_id    = $news->id;
                                    }
                                }

                                if($saved == true) {
                                    $file_name              = $news_id . "." . $file_ext;
                                    $thumb_name             = $file_name;
                                    $upload_file_succ       = Input::file($fname . "_" . $code)->move($dest_path . $code . '/', $file_name);

                                    $arr_news['news_id']    = $news_id;
                                    $arr_news['language']   = $code;
                                    $arr_news['device']     = $type;
                                    $arr_news['insert_by']  = Session::get('username');
                                    $arr_news['insert_date']= date('Y-m-d H:i:s');

                                    if ($upload_file_succ)  $arr_news['file_name']    = $file_name;
                                }
                            }
                        }

                        if(Input::hasFile($fname . "_thumb_" . $code)) {
                            $thumb                  = Input::file($fname . "_thumb_" . $code);

                        	$thumb_ext              = $thumb->getClientOriginalExtension();

                            list($width, $height)   = getimagesize($thumb);

                            if($width > $max_thumb_width || $height > $max_thumb_height) {
                                
                                return Redirect::back()
                                            ->withErrors(['The valid thumbnail for '.$type.' ('.$name.') size should be 640 x 400 pixels.']);
                            } else {
                                $thumb_name         = $news_id . "." . $thumb_ext;
                                $upload_thumb_succ  = Input::file($fname . "_thumb_" . $code)->move($dest_thumb_path . $code . '/', $thumb_name);

                                if ($upload_thumb_succ) {
                                    $arr_news['thumb_name']   = $thumb_name;
                                }
                            }
                            
                        }

                        if(Input::hasFile($fname . "_" . $code)) {
                            if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                                $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path. $code . '/', $dest_thumb_path. $code . '/');
                                $arr_news['thumb_name'] = $thumb_name;
                            }
                        }

                        if (count($arr_news) > 0)   Latestnews::insertNewsImage($arr_news);

                    }

                }
            }
            
            return Redirect::to('/latestnews/index');

        } else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput(); 
        }
       
     
    }




    /**
     * Show the form for editing the specified latest news.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $news         = Latestnews::find($id);
        $jocom_latest_news_images  = Latestnews::getNewsImages($id);
        $languages      = Setting::getAllLang();
        
        return View::make('latestnews.edit')
                ->with(array(
                'news'        => $news,
                'languages'     => $languages,
                'jocom_latest_news_images' => $jocom_latest_news_images,
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
        $news        = Latestnews::find($id);
        $news->set_sort($id, 'up');

        $news   = DB::table('jocom_latest_news')
                    ->orderBy('pos')
                    ->get();
    }



/**
     * Update the specified news in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        $news               = new Latestnews();
        $arr_input_all      = Input::all();
        $allowance          = 5;
        $languages          = Setting::getAllLang();
        $devices            = array('phone' => 'news', 'tablet' => 'news_tab');

        if (count($arr_input_all) > 0) {
           
            $arr_validate       = Latestnews::getUpdateRules($arr_input_all);
            $arr_input          = Latestnews::getUpdateInputs($arr_input_all);

            if ($arr_validate != 0) {
                $validator = Validator::make($arr_input, $arr_validate);

                if ($validator->passes()) {
                    $arr_udata  = Latestnews::getUpdateDbDetails($arr_input);

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
                        $dest_path          = Config::get('constants.LATESTNEWS_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.LATESTNEWS_THUMB_FILE_PATH'); 
                        $max_file_width     = 645;
                        $max_file_height    = 965;
                        $thumb_width        = 640;
                        $thumb_height       = 400;
                        $max_thumb_width    = 645;
                        $max_thumb_height   = 405;
                    break;
                
                case 'tablet':  
                        $dest_path          = Config::get('constants.LATESTNEWS_TAB_FILE_PATH');
                        $dest_thumb_path    = Config::get('constants.LATESTNEWS_TAB_THUMB_FILE_PATH'); 
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
                    // echo "<br>[fname: $fname] [Code: $code]";
                    $arr_news           = array();
                    $old_file_name      = "";
                    $query_type         = "";
                    $thumb_name         = "";

                    if(Input::hasFile($fname . "_" . $code)) {
                        $file                   = Input::file($fname . "_" . $code);
                        $cur_file_name          = $file->getRealPath() . "/" . $file->getClientOriginalName(); 
                        $old_file_name          = Latestnews::getOldFilename($id, "actual", $type, $code);               
                        $file_ext               = $file->getClientOriginalExtension();
                        $file_name              = Latestnews::getOldFilename($id, "actual", $type, $code);
                        list($width, $height)   = getimagesize($file);

                        if($width > $max_file_width || $height > $max_file_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid LATEST NEWS size for '.$type.' ('.$name.') should be 640 x 960 pixels.']);
                        } else {

                           if($old_file_name != 0 && $old_file_name !== $cur_file_name) {
                                $query_type = "update";
                            
                                /* Delete old file. */
                                if(file_exists($dest_path . $code . '/' . $old_file_name)) {
                                    File::delete($dest_path . $code . '/' . $old_file_name);        
                                }

                                $file_name          = $id . "." . $file_ext; 
                                $upload_file_succ   = $file->move($dest_path . $code . '/', $file_name);
                                $thumb_name         = $file_name;

                            } else {
                                $query_type             = "insert";
                                $arr_news['news_id']    = $id;
                                $arr_news['language']   = $code;
                                $arr_news['device']     = $type;
                                $file_name              = $id . "." . $file_ext; 
                                $upload_file_succ       = $file->move($dest_path. $code . '/', $file_name);
                            }
                        }

                        $arr_news['file_name'] = $file_name;
                    } 

                    if(Input::hasFile($fname . "_thumb_" . $code)) {
                        $thumb                  = Input::file($fname . "_thumb_" . $code);
                        $thumb_name             = Latestnews::getOldFilename($id, "thumb", $type, $code);  
                        $cur_thumb_name         = $thumb->getRealPath() . "/" . $thumb->getClientOriginalName(); 
                        $thumb_ext              = $thumb->getClientOriginalExtension();
                        $max_thumb_width        = $thumb_width + $allowance;
                        $max_thumb_height       = $thumb_height + $allowance;

                        list($width, $height)   = getimagesize($thumb);

                        if($width > $max_thumb_width || $height > $max_thumb_height) {
                            
                            return Redirect::back()
                                        ->withErrors(['The valid THUMBNAIL file size for '.$type.' ('.$name.') should be 640 x 400 pixels.']);
                        } else {
                            if($thumb_name != 0 && $thumb_name !== $cur_thumb_name) {
                                $query_type         = "update";

                                if(file_exists($dest_thumb_path . $code . '/'. $thumb_name)) {
                                    File::delete($dest_thumb_path . $code . '/'. $thumb_name);
                                }

                                $thumb_name                 = $id . "." . $thumb_ext;
                                $upload_thumb_succ          = $thumb->move($dest_thumb_path. $code . '/', $thumb_name);
                                    
                            } else {
                                $query_type                 = "insert";
                                $arr_news['news_id']        = $id;
                                $arr_news['language']       = $code;
                                $arr_news['device']         = $type;
                                $thumb_name                 = $id . "." . $thumb_ext;
                                $upload_thumb_succ          = $thumb->move($dest_thumb_path. $code . '/', $thumb_name);
                            }
                        }

                        $thumb_name             = $id . "." . $thumb_ext;
                        $arr_news['thumb_name'] = $thumb_name;

                    }

                    $thumb_name = Latestnews::getOldFilename($id, "thumb", $type, $code); 

                    if ($thumb_name == "") $thumb_name = $file_name;

                    if(Input::hasFile($fname . "_" . $code)) {
                        // echo "<br>[fname: $fname] [code: $code] <br>[$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                        if (!file_exists($dest_thumb_path . $code . '/' . $thumb_name)) {
                            // echo "<br> &nbsp;&nbsp; - - -> [THUMB NOT FOUND!][$fname_thumb_$code] ".$dest_thumb_path  . $code . '/' . $thumb_name;
                            $thumbnail = create_thumbnail($file_name, $thumb_width, $thumb_height, $dest_path . $code . '/', $dest_thumb_path . $code . '/');
                            $arr_news['thumb_name'] = $thumb_name;
                        }
                    }
                        
                    $arr_udata['modify_by']     = Session::get('username');
                    $arr_udata['modify_date']   = date('Y-m-d H:i:s');
                    
                    if($news->updateLatestNews($id, $arr_udata)){
                        
                        if(count($arr_news) > 0) {
                            switch ($query_type) {
                                case 'insert'   : 
                                        $arr_news['insert_by']      = Session::get('username');
                                        $arr_news['insert_date']    = date('Y-m-d H:i:s'); 
                                        Latestnews::insertNewsImage($arr_news);
                                    break;

                                case 'update'   : 
                                        $arr_news['modify_by']      = Session::get('username');
                                        $arr_news['modify_date']    = date('Y-m-d H:i:s');
                                        Latestnews::updateNewsImage($id, $code, $type, $arr_news);
                                    break;
                            }
                            
                            // echo "<br>[query_type: ".$query_type."] <br>";
                            print_r($arr_news);

                            Session::flash('success', 'Setting has been successfully save!');
                        }
                    } 
                    else {
                        echo "<br>Failed to update news!";
                    }
                }
            }
        }

        $news                       = Latestnews::find($id);
        $jocom_latest_news_images   = Latestnews::getNewsImages($id);

        return View::make('latestnews.edit')->with(array(
            'news'                      => $news,
            'languages'                 => $languages,
            'jocom_latest_news_images'  => $jocom_latest_news_images
        ));
    }



     /**
     * Remove the specified latest news from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $date           = date("Ymd_His");
        $latestnews     = Latestnews::getNewsImages($id);
        $languages      = array('en', 'cn', 'my');
        $devices        = array('phone', 'tablet'); 

        foreach ($latestnews as $news) 
        {
            foreach ($devices as $device) {
                switch ($device) {
                    case 'phone' : 
                            $file   = Config::get('constants.LATESTNEWS_FILE_PATH');
                            $thumb  = Config::get('constants.LATESTNEWS_THUMB_FILE_PATH');
                        break;

                    case 'tablet': 
                            $file   = Config::get('constants.LATESTNEWS_TAB_FILE_PATH');
                            $thumb  = Config::get('constants.LATESTNEWS_TAB_THUMB_FILE_PATH');
                        break;

                }

                if ($news->file_name != "") 
                {
               		foreach($languages as $language) {
                    	if(file_exists($file ."/". $language . "/" . $news->file_name))
                    		File::delete($file ."/". $language . "/" . $news->file_name);
               		}
                }

                if ($news->thumb_name != "") 
                {
                	foreach($languages as $lang) {
                    	if(file_exists($file ."/". $language . "/" . $news->thumb_name))
                   			File::delete($file ."/". $language . "/" . $news->thumb_name);
                	}
                }
            }
        }
        
        DB::table('jocom_latest_news_images')->where('news_id', '=', $id)->delete();

        Latestnews::destroy($id);
        
        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('/latestnews/index');
    }

}

?>