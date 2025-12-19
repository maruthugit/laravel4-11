<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class EcomBanners extends Eloquent {

    use UserTrait, RemindableTrait;

    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_ecom_banners';

    
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


}
