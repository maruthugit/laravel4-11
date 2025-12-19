<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Helper\ImageHelper as Image;

class Feedback extends Eloquent {

    protected $table = 'jocom_feedback';

    public static function fetch_feedback($limit = 50, $offset = 0, $params = [])
    { 
        try{

            $username   = array_get($params, 'username', NULL);
            $comment      = array_get($params, 'comment', NULL) . '-Type-'.array_get($params, 'type', NULL);
            $email      = array_get($params, 'email', NULL);
            $type      = array_get($params, 'type', NULL);
            $attachment      = array_get($params, 'attachment', NULL);
            $title      = array_get($params, 'title', NULL);
            // print_r($attachment);
            // die();
            $user = DB::table('jocom_user')->select('*')
                ->where('username', '=', $username)
                ->first();

            if (empty($user) && $email == '') {
                $xmldata['status']     = '0';
                $xmldata['status_msg'] = '#301';
            
            }else{   
                
                if($user->id != ''){
                    $user_id = $user->id;
                }else{
                    $user_id = '';
                }

               $id = DB::table('jocom_feedback')->insertGetId(array(
                    'user_id'      => $user_id,
                    'comment'      => $comment,
                    'email'      => $email,
                    'type'         => $type,
                    'insert_by'    => "phone_app",
                    'insert_date'  => date('Y-m-d H:i:s'),
                    'title'     =>$title,
                ));
                
                 $body = array('title' => $title,
                         'name' => $user->username,
                         'email'=>$user->email,
                         'phonenumber'=>$user->mobile_no,
                         'type'=>$type,
                         'remarks'=>$comment);
                
                
                 Mail::send('emails.feedback', $body, function($message) use ($subject)
            {
                $message->from('notification@jocom.my', 'CUSTOMER FEEDBACK');
                $message->to('feedback@jocom.my', '')->subject($subject);
                // $message->bcc('yasser@jocom.my', '')->subject($subject);
             
            }
        );
                
                
             

                if (!empty($attachment)) {

                    foreach ($attachment as $key => $value) {

                        $file_name = "feedback"."_".uniqid().".".$value->getClientOriginalExtension(); 
                        $path = Config::get('constants.FEEDBACK_IMG');
                        $upload_file_succ = $value->move($path, $file_name);

                        DB::table('jocom_feedback_img')->insert(array('feedback_id'=>$id, 'attachment'=>$file_name));
                    }

                }

                $xmldata['status']     = '1';
                $xmldata['status_msg'] = 'Thank you for the feedback.';
            }

            return ['xml_data' => $xmldata];

        }catch (Exception $ex){
            echo $ex->getMessage();

        }

    }
}




?>
