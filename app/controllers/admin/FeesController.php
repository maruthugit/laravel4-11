<?php 

class FeesController extends BaseController {


    /**
     * Default listing for all coupon.
     * @return [type] [description]
     */
    public function anyIndex()
    {
        // $feesrow = DB::table('jocom_fees')
        // ->select('*')
        // ->find(1);
        // 
        $feesrow = Fees::find(1);

        return View::make(Config::get('constants.ADMIN_FOLDER').'.fees_edit', ['display_fees' => $feesrow]);
    }


    public function anyEdit($id = null)
    {
        if (isset($id))
        {
            if (Input::has('id'))
            {

                $validator = Validator::make(Input::all(), Fees::$rules, Fees::$message);            

                if ($validator->passes()) {
                                        
                    $rs = Fees::save_fees();

                    if ($rs == true)
                    {
                        $insert_audit = General::audit_trail('FeesController.php', 'Edit()', 'Edit Fees', Session::get('username'), 'CMS');
                        return Redirect::to('fees')->with('success', 'Fees updated successfully.');
                    }
                    else{
                        return Redirect::to('fees')->with('message', 'Fees update failed.');
                    } 

                } else {
                    return Redirect::to('fees')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }
            }
            else
            {
                
                // $feesrow = DB::table('jocom_fees')
                //             ->select('*')
                //             ->find(1);

                // return View::make(Config::get('constants.ADMIN_FOLDER').'.fees_edit', ['display_fees' => $feesrow]);
                return Redirect::to('fees');
            }       
        }
        else
        {
            return Redirect::to('fees');
        }
        
    }



}
?>