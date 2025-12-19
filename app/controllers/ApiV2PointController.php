<?php

class ApiV2PointController extends BaseController
{
    /**
     * Get point-related status
     * @return       (json)
     */
    public function getStatus()
    {
        $pointModules = PointModule::all();

        foreach ($pointModules as $module) {
            $modules[$module->name] = $module->status;
        }

        $conversionRate = PointConversionRate::from(PointType::CASH)->to(PointType::JOPOINT)->active()->first();

        if ($conversionRate) {
            $modules['jpoint_cash_buy_minimum'] = $conversionRate->minimum;
        }

        ksort($modules);

        return json_encode($modules);
    }
    
    
    public function anyPointbalance(){
        
        $setData     = array();
        $points = array();
        
        $TotalBCard = 0;
        $TotalJpoint = 0;
        
        $username = Input::get("username");
        $CustomerProfile = Customer::where("username",$username)
                ->where("active_status",1)->first();
        
        if(count($CustomerProfile) > 0 ){
            
            $user_id = $CustomerProfile->id;
            $JPointData = PointUser::where("user_id",$user_id)
                    ->where("point_type_id",PointType::JOPOINT)
                    ->where("status",1)->first();
            
            $TotalJpoint = $JPointData->point;
            array_push($points, array(
                "type" => 'JPoint',
                "total" => $TotalJpoint
            ));
            
            
            $BcardM = BcardM::where("username",$username)->first();
            if(count($BcardM) > 0 ){
                
                $BCardData = PointUser::where("user_id",$user_id)
                    ->where("point_type_id",PointType::BCARD)
                    ->where("status",1)->first();
                $TotalBCard = $BCardData->point;
                
                
                if(count($BCardData) > 0 ){
                    array_push($points, array(
                        "type" => 'BCard',
                        "total" => $TotalBCard
                    ));
                }
                
               
            }
            
        }
        
        $setData['xml_data']['points']['point'] = $points;

        $data =$setData;

        return Response::json($data);
             
    }
}
