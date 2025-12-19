<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Banner extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_banners';

    
    /**
     * Validation rules for creating a new user.
     * @var array
     */
    public static $rules = array(
        // 'url_link'           =>'url',
        'banner_en'         =>'required|mimes:gif,jpeg,jpg,png',
        'banner_thumb_en'   =>'required|mimes:gif,jpeg,jpg,png',
    );

    public static $messages = array(
        'banner_en.required'        => 'The default Banner Image is required.',
        'banner_thumb_en.required'  => 'The default Banner Thumbnail Image is required.',
    );

    public static function getUpdateRules(array $inputs) {
        $validate_rule    = array();

        foreach($inputs as $key => $value) {
           //echo "<br> [".$key."] [".$value."]";
            // if(isset($value) && $value != "") {
                if($key == "qrcode") $validate_rule[$key] = "regex:/^[A-Za-z0-9 ,]+$/";

                if($key == "category_id") $validate_rule[$key] = "min:1";

//              if($key == "url_link")  $validate_rule[$key] = "required";
                
                $param   = explode("_", $key);
                if($param[0] == "banner") {
                    //echo "<br>param[1]: ".$param[1];
                    //exit;
                    $validate_rule[$key] = "mimes:gif,jpeg,jpg,png";  
                }
            // }
        }
        // var_dump($validate_rule);
        //exit;
        return (count($validate_rule) > 0) ? $validate_rule : 0;
    }

    public function scopeGetUpdateInputs($query, array $inputs) {
        // $arr_input = array();
        foreach($inputs as $key => $value) {
//          if(isset($value) && $value != "") {
                $arr_input[$key] = $value;
//          }
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

                case 'category_id':
                    $arr_udata['category_id']   = $value;
                    break;
            }
        }

        $arr_udata['modify_by']     = Session::get('username');
        $arr_udata['modify_date']   = date("Y-m-d H:i:s");
        
        return $arr_udata;
    }

    public function scopeUpdateBanner($query, $id, array $data) {
        $banner = DB::table('jocom_banners')
                    ->where('id', '=', $id)
                    ->update($data);
        return $banner;
    }

    public static function getOldFilename($id, $file_type, $device_type, $language) {
        if($file_type == "actual") {
            $file_type  = "file_name";
        }
                    
        if($file_type == "thumb") {
            $file_type  = "thumb_name";
        }

        $banner = DB::table('jocom_banners_images')
                    ->select($file_type)
                    ->where('banner_id', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->first();

        return (count($banner) > 0) ? $banner->$file_type : 0;

    }

    // $sql = "SELECT * FROM `jocom_banners` ORDER BY `pos` DESC";
    public function scopeSet_sort($query, $id, $option) {
        $data   = array();
        $pos    = DB::table('jocom_banners')
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

            $pos = DB::table('jocom_banners')
                        ->where('id', '=', $d)
                        ->where('pos', '!=', $count)
                        // ->get()
                        ->update($udata);
        }
        return $pos;

    }

    public function scopeInsertBannerImage($query, array $inputs) {
        return DB::table('jocom_banners_images')
                    ->insert($inputs);
    }

    public function scopeUpdateBannerImage($query, $id, $language,  $device_type, array $inputs) {
        return DB::table('jocom_banners_images')
                    ->where('banner_id', '=', $id)
                    ->where('language', '=', $language)
                    ->where('device', '=', $device_type)
                    ->update($inputs);
    }

    public function scopeGetBannerImages($query, $id) {
        return DB::table('jocom_banners_images')
                    ->where('banner_id', '=', $id)
                    ->get();
    }

    public static function getImagesByLanguage($id, $device_type, $language) {
        $query = DB::table('jocom_banners_images')
                        ->select('file_name', 'thumb_name')
                        ->where('banner_id', '=', $id)
                        ->where('language', '=', $language)
                        ->where('device', '=', $device_type)
                        ->first();

        return $query;
    }
    
    public function scopeUpdateBannerTemplateQrcode($qrcode, $id,$seq) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_managebanners_images')
                ->where('banner_id',$id)
                ->where('banner_seq',$seq)
                ->update(array(
                    'qrcode'=>$qrcode, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateBannerTemplateImage($image, $id,$seq) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_managebanners_images')
                ->where('banner_id',$id)
                ->where('banner_seq',$seq)
                ->update(array(
                    'file_name'=>$image, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateBannerTemplateStatus($status,$type,$region) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

       return DB::table('jocom_managebanners')
                ->where('region_id',$region)
                ->where('type',$type)
                ->update(array(
                    'active_status'=>$status,
                    'modify_by'=>$modify_by,
                    'modify_date'=>$modify_date
                    )
                );    
       
    }

    public function scopeBannerTemplateList($id) {

       $list = DB::table('jocom_managebanners AS JM')
                ->leftjoin('jocom_managebanners_images AS JMI', 'JMI.banner_id', '=', 'JM.id')
                ->where('JMI.banner_id', '=', $id)
                ->select('*')
                ->get();


       return $list;
       
    }

}
