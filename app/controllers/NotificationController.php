<?php
    
class NotificationController extends BaseController
{   
    const NOTI_ASSIGN_TASK = 'TSGN';
    const NOTI_COMMENT_TASK = 'TCMN';
    const NOTI_COMPLETED_TASK = 'TCPT';
    const NOTI_CANCEL_TASK = 'TCLD';


    /*
     * Save Notification
     */
    public static function saveNotification($typeCode,$dower,$dower_id,$target,$targetID,$recipient,$description = ''){
        
        $is_error = false;
        
        try{
            
            $Noti = new Notification;
            $Noti->action_type = $typeCode;
            $Noti->dower = $dower;
            $Noti->dower_id = $dower_id;
            $Noti->target = $target;
            $Noti->target_id = $targetID;
            $Noti->receiver_id = $recipient;
            $Noti->description = $description;
            $Noti->is_view = 0;
            $Noti->save();
            
            
        } catch (Exception $ex) {
            
            $is_error = true;
            echo $ex->getMessage();
            
        } finally {
            
          
            return $is_error;
        }
        
        
    }
    
    
    public function getNotification(){
        
        try{
            
            $Notifications = array();
            
            $ownerID = Session::get("user_id");
            
            $Notifications = DB::table('jocom_notification AS JN')->select(array(
                        'JN.id','JN.dower','JN.action_type','JN.target','JN.target_id','JN.description','JN.is_view','JN.created_at','JNT.wording','JSA.user_photo'
                        ))
                    ->leftJoin('jocom_notification_type AS JNT', 'JNT.type', '=', 'JN.action_type')
                    ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JN.dower_id')
                    ->where('JN.receiver_id', '=', $ownerID)
                    ->where('JN.activation', '=', 1)
                    ->take(6)
                    ->orderBy('JN.id', 'DESC');
            
            $result = $Notifications->get();
            
            $listID = array_pluck($Notifications->get(), 'id');
            DB::table('jocom_notification')
            ->where('is_view', 0)
            ->whereIn('id', $listID)
            ->update(['is_view' =>1]);
            
        } catch (Exception $ex) {
            
            return false;
            
        } finally {
            return $result;
            
        }
        
    }
    
    public function getNextNotification(){
        
        try{
            
            $lastId = Input::get("lastId");
            
            $Notifications = array();
            $ownerID = Session::get("user_id");
            
            $Notifications = DB::table('jocom_notification AS JN')->select(array(
                        'JN.id','JN.dower','JN.action_type','JN.target','JN.target_id','JN.description','JN.is_view','JN.created_at','JNT.wording','JSA.user_photo'
                        ))
                    ->leftJoin('jocom_notification_type AS JNT', 'JNT.type', '=', 'JN.action_type')
                    ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JN.dower_id')
                    ->where('JN.receiver_id', '=', $ownerID)
                    ->where('JN.activation', '=', 1)
                    ->where('JN.id', '<', $lastId)
                    ->orderBy('JN.id', 'DESC');
            
            $result = $Notifications->take(6)->get();
            $listID = array_pluck($Notifications->take(6)->get(), 'id');
            
            DB::table('jocom_notification')
            ->whereIn('id', $listID)
            ->where('is_view', 0)
            ->update(['is_view' =>1]);
            
        } catch (Exception $ex) {
            
            return false;
            
        } finally {
            return $result;
            
        }
        
    }
  
    
}
