<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Helper\ImageHelper as Image;

class JocomMy extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    protected $table = 'jocommy_banners';

    /**
     * Listing for transaction
     * @return [type] [description]
     */
    
    public function scopeTemplateList($type) {

       $list = DB::table('jocommy_banners AS JB')
                ->leftjoin('jocommy_banners_images AS JBI', 'JBI.banner_id', '=', 'JB.id')
                // ->where('JM.type', '=', $id)
                ->where('JB.type', '=', $type)
                // ->select('*')
                ->select('JB.type','JB.seq','JBI.file_name','JBI.heading','JBI.sub_heading','JBI.id','JB.active_status','JBI.banner_id', 'JBI.max_width','JBI.max_height')
                ->orderBy('JB.seq', 'asc')
                ->get();

       return $list;
       
    }

    public function scopeLayoutUpdate($id,$type,$seq) {

        DB::table('jocommy_banners_images')
                    ->insert(array(
                        'banner_id'=>$id,
                        'banner_seq'=>$seq,
                        'file_name'=>'',
                        'heading'=>'',
                        'sub_heading'=>'',
                        'max_width'=>'',
                        'max_height'=>'',
                        'active_status'=>0,
                        'insert_by'=>Session::get('username'),
                        'insert_date'=>date('Y-m-d H:i:s'),
                        'modify_by'=>'',
                        'modify_date'=>'',
                        ));

    }

    public function scopeUpdateTemplateHeading($heading, $id) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocommy_banners_images')
                ->where('id',$id)
                // ->where('banner_seq',$seq)
                ->where('heading','!=', $heading)
                ->update(array(
                    'heading'=>$heading, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateTemplateSubHeading($sub_heading, $id) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocommy_banners_images')
                ->where('id',$id)
                // ->where('banner_seq',$seq)
                ->where('sub_heading','!=', $sub_heading)
                ->update(array(
                    'sub_heading'=>$sub_heading, 
                    'modify_by'=>$modify_by, 
                    'modify_date'=>$modify_date
                ));
    }

    public function scopeUpdateTemplateImage($image, $id) {
        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocommy_banners_images')
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


        $db =  DB::table('jocommy_banners')
                ->where('id',$id)
                // ->where('type',$type)
                ->where('active_status','!=',$status)
                ->update(array(
                    'active_status'=>$status,
                    'modify_by'=>$modify_by,
                    'modify_date'=>$modify_date
                    )
                );  
         
        return $db;
       
    }

    public function scopeUpdateTemplateSeq($seq,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');


        $db =  DB::table('jocommy_banners')
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
    
    public static function fetch_jocommy($limit = 50, $offset = 0, $params = [])
    { 
        $bannermasters = DB::table('jocommy_banners')
                        ->select('id','type','seq')
                        ->where('type',1)
                        ->where('active_status','=',1)
                        ->orderBy('seq','asc')
                        ->get();

        if (!empty($bannermasters)) {
            
            foreach ($bannermasters as $bannermaster) {

                // print_r($bannermaster);die();
                    $banners = DB::table('jocommy_banners_images')
                            ->where('banner_id','=',$bannermaster->id)
                            ->select('file_name','heading','sub_heading','banner_seq')
                            ->orderBy('banner_seq','desc')
                            ->get();
                     $array_banners['banner'] ="";       
                    foreach ($banners as $banner) {

                        $file_name = Config::get('constants.NEW_JOCOMMY_BANNER_PATH').$banner->file_name;
                        
                        if ($file_name!='') {

                            $array_banners['banner'][] = array(
                                'file_name' => Image::link($file_name),
                                // 'banner_seq'=> $banner->banner_seq,
                                'heading'    => $banner->heading,
                                'sub_heading' => $banner->sub_heading,
                                'type' => $bannermaster->type,
                                
                            );

                        }
                    }
                        $data['item'][] = array(
                            'id'        => $bannermaster->id,
                            'type'      => $bannermaster->type,
                            'seq'      => $bannermaster->seq,
                            'layout'    => array($array_banners),
                         );   
                        
                }
            }else{

                $data['item'][] = array();  
            }

        return array('xml_data' => $data);
    }
}
