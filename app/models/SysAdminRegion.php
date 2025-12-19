<?php

class SysAdminRegion extends Eloquent
{
    protected $table = 'jocom_sys_admin_region';
    
    public static function getSysAdminRegion($sys_admin_id){
        
        $result = DB::table('jocom_sys_admin_region AS JSR')
               ->select('JSR.*')        
               ->where('JSR.sys_admin_id', $sys_admin_id)
               ->where('JSR.status', 1)
               ->get();

        return $result;
            
    }

    
}
