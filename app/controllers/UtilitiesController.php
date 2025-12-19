<?php

class UtilitiesController extends BaseController
{
    
    /*
     * @Desc : DO sorter page
     */
    public function sort(){
        
        return View::make('utilities.dosort');
        
    }
    
    
    public static function getLogisticStatusInfo($logistisStatus){
        
        $result = array();
        $collection = array();
        
        switch ($logistisStatus) {
            case 0:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Pending",
                );
                break;
            case 1:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Undelivered",
                );
                break;
            case 2:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Partial Send",
                );
                break;
            case 3:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Returned",
                );
                break;
            case 4:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Sending",
                );
                break;
            case 5:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Sent",
                );
                break;
            case 5:
                $collection = array(
                    "status"=>$logistisStatus,
                    "status_description"=>"Cancelled",
                );
                break;

            default:
                $collection = false;
        }
        
        $result = $collection;
        return $result;
        
    }
    
}
