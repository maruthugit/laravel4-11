<?php

class ApiBCardController extends BaseController
{
    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyRegister()
    {
        $result = array();

        $result = array_merge($result, BcardM::new_card(Input::all()));

        // return json_encode($result, JSON_UNESCAPED_SLASHES);

        // if ( ! is_array($result)) {
        //     $result = ['status' => 0, 'message' => $result];
        // } else {
        //     $result = array_merge($result, ['status' => 1]);
        // }

        return json_encode($result);
    }

    // public function anyRegisterstatus()
    // {
    //     $result = PointModule::getStatus('bcard_register');

    //     return json_encode([
    //         'status' => $result ? 1 : 0,
    //     ]);
    // }

    

}
