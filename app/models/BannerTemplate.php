<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class BannerTemplate extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_managebanners_new';

    
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

    public function scopeUpdateTemplateQrcode($qrcode, $id) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_managebanners_images_new')
                ->where('id',$id)
                // ->where('banner_seq',$seq)
                ->where('qrcode','!=', $qrcode)
                ->update(array(
                    'qrcode'=>$qrcode, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateTemplateImage($image, $id) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_managebanners_images_new')
                ->where('id',$id)
                // ->where('banner_seq',$seq)
                ->where('file_name','!=',$image)
                ->update(array(
                    'file_name'=>$image, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateTemplateStatus($status,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        if ($status == 2) {

        $db =  DB::table('jocom_managebanners_new')->where('id',$id)->delete();

        }else{

        $db =  DB::table('jocom_managebanners_new')
                ->where('id',$id)
                // ->where('type',$type)
                ->where('active_status','!=',$status)
                ->update(array(
                    'active_status'=>$status,
                    'modify_by'=>$modify_by,
                    'modify_date'=>$modify_date
                    )
                );  
        }
         
        return $db;
       
    }

    public function scopeUpdateTemplateSeq($seq,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');


        $db =  DB::table('jocom_managebanners_new')
                ->where('id',$id)
                // ->where('type',$type)
                ->where('seq','!=',$seq)
                ->update(array(
                    'seq'=>$seq,
                    'modify_by'=>$modify_by,
                    'modify_date'=>$modify_date
                    )
                );  
         
        return $db;
       
    }

    public function scopeTemplateList($region) {
       if($region == 5){
          $list = DB::table('jocom_managebanners_new AS JM')
                ->leftjoin('jocom_managebanners_images_new AS JMI', 'JMI.banner_id', '=', 'JM.id')
                // ->where('JM.type', '=', $id)
                ->where('JM.region_id', '=', $region)
                // ->where('JM.active_status', '=', 1)
                // ->where('JMI.active_status', '=', 1)
                // ->select('*')
                // ->where('JMI.banner_seq','=',1)
                ->select('JM.type','JM.seq','JMI.file_name','JMI.qrcode','JMI.id','JM.active_status','JMI.banner_id', 'JMI.max_width','JMI.max_height')
                 // ->orderBy('JM.seq', 'asc')
                ->get(); 
       }
       else{
       $list = DB::table('jocom_managebanners_new AS JM')
                ->leftjoin('jocom_managebanners_images_new AS JMI', 'JMI.banner_id', '=', 'JM.id')
                // ->where('JM.type', '=', $id)
                ->where('JM.region_id', '=', $region)
                // ->where('JM.active_status', '=', 1)
                // ->where('JMI.active_status', '=', 1)
                // ->select('*')
                // ->whereIn('JMI.banner_seq',[1,2,3])
                ->select('JM.type','JM.seq','JMI.file_name','JMI.qrcode','JMI.id','JM.active_status','JMI.banner_id', 'JMI.max_width','JMI.max_height')
                ->orderBy('JM.seq', 'asc')
                ->get();
       }
       return $list;
       
    }

    public function scopeLayoutUpdate($id,$type) {

        switch ($type) {
            case 'B001':   
                $max_width_1  = "150";
                $max_height_1 = "325";
                $max_width_2  = "310";
                $max_height_2 = "262";
                $max_width_3  = "310";
                $max_height_3 = "262";
            break;                    
            case 'B002':  
                $max_width_1  = "640";
                $max_height_1 = "262";
                $max_width_2  = "310";
                $max_height_2 = "262";
                $max_width_3  = "310";
                $max_height_3 = "262";
            break;
            case 'B003':  
                $max_width_1  = "310";
                $max_height_1 = "262";
                $max_width_2  = "150";
                $max_height_2 = "325";
                $max_width_3  = "310";
                $max_height_3 = "262";
            break;
            case 'B004':  
                $max_width_1  = "310";
                $max_height_1 = "262";
                $max_width_2  = "310";
                $max_height_2 = "262";
                $max_width_3  = "640";
                $max_height_3 = "262";
            break;
            case 'B005':  
                $max_width_1  = "640";
                $max_height_1 = "262";
            break;

        }

        if ($type === "B005") {
            DB::table('jocom_managebanners_images_new')
                    ->insert(array(
                        'banner_id'=>$id,
                        'banner_seq'=>'1',
                        'file_name'=>'',
                        'qrcode'=>'',
                        'max_width'=>$max_width_1,
                        'max_height'=>$max_height_1,
                        'language'=>'',
                        'active_status'=>1,
                        'insert_by'=>Session::get('username'),
                        'insert_date'=>date('Y-m-d H:i:s'),
                        'modify_by'=>'',
                        'modify_date'=>'',
                        ));
                
            DB::table('jocom_managebanners_images_new')
                        ->insert(array(
                            'banner_id'=>$id,
                            'banner_seq'=>'',
                            'file_name'=>'',
                            'qrcode'=>'',
                            'max_width'=>'',
                            'max_height'=>'',
                            'language'=>'',
                            'active_status'=>0,
                            'insert_by'=>Session::get('username'),
                            'insert_date'=>date('Y-m-d H:i:s'),
                            'modify_by'=>'',
                            'modify_date'=>'',
                            ));
            DB::table('jocom_managebanners_images_new')
                        ->insert(array(
                            'banner_id'=>$id,
                            'banner_seq'=>'',
                            'file_name'=>'',
                            'qrcode'=>'',
                            'max_width'=>'',
                            'max_height'=>'',
                            'language'=>'',
                            'active_status'=>0,
                            'insert_by'=>Session::get('username'),
                            'insert_date'=>date('Y-m-d H:i:s'),
                            'modify_by'=>'',
                            'modify_date'=>'',
                            ));
        }else{

            DB::table('jocom_managebanners_images_new')
                    ->insert(array(
                        'banner_id'=>$id,
                        'banner_seq'=>'1',
                        'file_name'=>'',
                        'qrcode'=>'',
                        'max_width'=>$max_width_1,
                        'max_height'=>$max_height_1,
                        'language'=>'',
                        'active_status'=>1,
                        'insert_by'=>Session::get('username'),
                        'insert_date'=>date('Y-m-d H:i:s'),
                        'modify_by'=>'',
                        'modify_date'=>'',
                        ));
                
            DB::table('jocom_managebanners_images_new')
                        ->insert(array(
                            'banner_id'=>$id,
                            'banner_seq'=>'2',
                            'file_name'=>'',
                            'qrcode'=>'',
                            'max_width'=>$max_width_2,
                            'max_height'=>$max_height_2,
                            'language'=>'',
                            'active_status'=>1,
                            'insert_by'=>Session::get('username'),
                            'insert_date'=>date('Y-m-d H:i:s'),
                            'modify_by'=>'',
                            'modify_date'=>'',
                            ));
            DB::table('jocom_managebanners_images_new')
                        ->insert(array(
                            'banner_id'=>$id,
                            'banner_seq'=>'3',
                            'file_name'=>'',
                            'qrcode'=>'',
                            'max_width'=>$max_width_3,
                            'max_height'=>$max_height_3,
                            'language'=>'',
                            'active_status'=>1,
                            'insert_by'=>Session::get('username'),
                            'insert_date'=>date('Y-m-d H:i:s'),
                            'modify_by'=>'',
                            'modify_date'=>'',
                            ));
           
        }

       
    }

}
