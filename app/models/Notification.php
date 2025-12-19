<?php


class Notification extends Eloquent
{
    
    protected $table = 'jocom_notification';
    
    public static function getTotalNotification($user_id){
        
        $total = DB::table('jocom_notification')
                ->where('receiver_id', $user_id)
                ->where('is_view', 0)->count();
        
        return $total;
        
    }
    
    
}


