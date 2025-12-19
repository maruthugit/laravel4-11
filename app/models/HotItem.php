<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class HotItem extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;

    // protected $fillable = ['url_link', 'image'];

    public static $rules = array(
        // 'url_link'          =>'required|url',
        'hotitem_en'        =>'required|mimes:gif,jpeg,jpg,png',
        'hotitem_thumb_en'  =>'required|mimes:gif,jpeg,jpg,png',
        // 'image' => 'required|mimes:jpeg,jpg,png',
    );

    public static $messages = array(
        // 'url_link.required' => 'The URL is required.',
        'hotitem_en.required'       => 'The default Hot Item Image is required.',
        'hotitem_thumb_en.required' => 'The default Hot Item Thumbnail Image is required.',
    );

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_hot_items';

	/**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'insert_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modify_date';

    public function isValid() {
        $validation = Validator::make($this->attributes, static::$rules, static::$message);

        if($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

    public static function getUpdateRules(array $inputs) {
        $validate_rule    = array();

        foreach($inputs as $key => $value) {
           
            // if(isset($value) && $value != "") {
            	if($key == "qrcode") $validate_rule[$key] = "regex:/^[A-Za-z0-9 ,]+$/";
            	
//            	if($key == "url_link") $validate_rule[$key] = "required";
                $param   = explode("_", $key);
                if($param[0] == "hotitem") {
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
            }
        }

        $arr_udata['modify_by']     = Session::get('username');
        $arr_udata['modify_date']   = date("Y-m-d H:i:s");
        
        return $arr_udata;
    }

    public function scopeUpdateHotItem($query, $id, array $data) {
        $banner = DB::table('jocom_hot_items')
                    ->where('id', $id)
                    ->update($data);
        return $banner;
    }

    public static function getOldFilename($id, $type, $device_type, $language) {
        if($type == "actual") {
            $file_type  = "file_name";
        }
                    

        if($type == "thumb") {
            $file_type  = "thumb_name";
        }

        $banner = DB::table('jocom_hot_items_images')
                    ->select($file_type)
                    ->where('hot_id', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->first();

        return (count($banner) > 0) ? $banner->$file_type : 0;

    }

    public function scopeInsertHotItemImage($query, array $inputs) {
        return DB::table('jocom_hot_items_images')
                    ->insert($inputs);
    }

    public function scopeUpdateHotItemImage($query, $id, $language, $device_type, array $inputs) {
        return DB::table('jocom_hot_items_images')
                    ->where('hot_id', '=', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->update($inputs);
    }

    public function scopeGetHotItemImages($query, $id) {
        return DB::table('jocom_hot_items_images')
                    ->where('hot_id', '=', $id)
                    ->get();
    }

    public static function getImagesByLanguage($id, $device_type, $language) {
        $query = DB::table('jocom_hot_items_images')
                        ->select('file_name', 'thumb_name')
                        ->where('hot_id', '=', $id)
                        ->where('language', '=', $language)
                        ->where('device', '=', $device_type)
                        ->first();

        return $query;
    }
    
    public function scopeSet_sort($query, $id, $option) {
        $data   = array();
        $pos    = DB::table('jocom_hot_items')
                    ->orderBy('pos', 'desc')
                    ->get();

        $count  = 0;

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

            $data[$tmp_count] = $p->id;
        }

        krsort($data);
        $count = 0;

        foreach($data as $d) {
            $count++;
            $udata['modify_by']     = Session::get('username');
            $udata['modify_date']   = date('Y-m-d H:i:s');
            $udata['pos']           = $count;

            $pos = DB::table('jocom_hot_items')
                        ->where('id', '=', $d)
                        ->where('pos', '!=', $count)
                        // ->get()
                        ->update($udata);
        }
        return $pos;

    }
}