<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Latestnews extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_latest_news';


	/**
	 * Validation rules for creating a new user.
	 * @var array
	 */
	public static $rules = array(
	    // 'url_link'		=>'url',
	    'news_en'	       =>'required|mimes:gif,jpeg,jpg,png',
        'news_thumb_en'    =>'required|mimes:gif,jpeg,jpg,png',
	    );

    public static $messages = array(
        'news_en.required'       => 'The default Latest News is required.',
        'news_thumb_en.required' => 'The default Latest News Thumbnail is required.',
    );

	/**
	 * [getUpdateRules description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public static function getUpdateRules(array $inputs) {
		$validate_rule = array();

		foreach($inputs as $key => $value) {

              // if(isset($value) && $value != "") {
            	if($key == "qrcode") $validate_rule[$key] = "regex:/^[A-Za-z0-9 ,]+$/";
//              	if($key == "url_link") $validate_rule[$key] = "required";
              	
                $param   = explode("_", $key);
                if($param[0] == "news") {
                    //echo "<br>param[0]: ".$param[0];
                    $validate_rule[$key] = "mimes:gif,jpeg,jpg,png";  
                }
            // }
  
        }
        return (count($validate_rule) > 0) ? $validate_rule : 0;
	}

	public function scopeGetUpdateInputs($query, array $inputs) {
		// $arr_input = array();
		foreach($inputs as $key => $value) {
//            if(isset($value) && $value != "") {
                $arr_input[$key] = $value;
//            }
        }
        return $arr_input;
    }

	public function scopeGetUpdateDbDetails($query, array $inputs) {
		$arr_udata = array();

		foreach($inputs as $key => $value) {
            switch ($key) {
                case 'url_link':
                    $arr_udata['url_link'] = $value;
                    break;

                case 'qrcode':
                    $arr_udata['qrcode']   = $value;
                    break;
                
                case 'title':
                    $arr_udata['title']   = $value;
                    break;

                case 'description':
                    $arr_udata['description']   = $value;
                    break;
            }
	    }

	    $arr_udata['modify_by']		= Session::get('username');
	    $arr_udata['modify_date']	= date("Y-m-d H:i:s");
	    
	    return $arr_udata;
	}

	public function scopeUpdateLatestNews($query, $id, array $data) {
        $news = DB::table('jocom_latest_news')
                    ->where('id', $id)
                    ->update($data);
        return $news;
    }

	public static function getOldFilename($id, $file_type, $device_type, $language) {

        if($file_type == "actual") {
            $file_type  = "file_name";
        }
                    

        if($file_type == "thumb") {
            $file_type  = "thumb_name";
        }


		$news = DB::table('jocom_latest_news_images')
                    ->select($file_type)
                    ->where('news_id', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->first();

        return (count($news) > 0) ? $news->$file_type : 0;

    }



    public function scopeSet_sort($query, $id, $option) {
        $data   = array();
        $pos    = DB::table('jocom_latest_news')
                    ->orderBy('pos', 'desc')
                    ->get();

        $count  = 0;

        // echo "<br>option ==> ".$option . " - - - ID: ".$id;

        foreach($pos as $p) {
            $count += 2;
            $tmp_count  = $count;
            $opt        = "";

            if($p->id == $id) {
                if($option == 'up') {
                    $opt = " <<< UP";
                    $tmp_count -= 3;
                } else {
                    // echo "<br>[UP] ".$p->id;
                    $opt = " <<< DOWN";
                    $tmp_count += 3;
                }

            }
            // echo "<br>[ID: ".$p->id."] [ori_pos: ".$p->pos."] [count: ".$count."] [tmp_count: ".$tmp_count."] ".$opt;

            $data[$tmp_count] = $p->id;
        }

        krsort($data);
        $count = 0;

        foreach($data as $d) {
            $count++;
            $udata['modify_by']     = Session::get('username');
            $udata['modify_date']   = date('Y-m-d H:i:s');
            $udata['pos']           = $count;

            $pos = DB::table('jocom_latest_news')
                        ->where('id', '=', $d)
                        ->where('pos', '!=', $count)
                        // ->get()
                        ->update($udata);
        }
        return $pos;

    }


    public function scopeInsertNewsImage($query, array $inputs) {
        
        return DB::table('jocom_latest_news_images')
                    ->insert($inputs);
    }

    public function scopeUpdateNewsImage($query, $id, $language, $device_type, array $inputs) {
         return DB::table('jocom_latest_news_images')
                    ->where('news_id', '=', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->update($inputs);
    }

    public function scopeGetNewsImages($query, $id) {
         return DB::table('jocom_latest_news_images')
                    ->where('news_id', '=', $id)
                    ->get();
    }

	public static function getImagesByLanguage($id, $device_type, $language) {
        $query = DB::table('jocom_latest_news_images')
                        ->select('file_name', 'thumb_name')
                        ->where('news_id', '=', $id)
                        ->where('language', '=', $language)
                        ->where('device', '=', $device_type)
                        ->first();
        return $query;
    }
}

?>