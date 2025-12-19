<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Seller extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_seller';


    /**
     * Validation rules for creating a new user.
     * @var array
     */
    public static $rules = array(
        'username'              =>'required|min:3|unique:jocom_seller,username',
        'pic_full_name'         =>'required|min:5',
        'email'                 =>'required|email|unique:jocom_seller',
        //'company_name'          =>'alpha_spaces',
        'tel_num'               =>'numeric',
        'mobile_no'             =>'numeric',
        'postcode'              =>'numeric',
        'bank_acc_no'           =>'numeric',
        'password'              =>'required|alpha_num|between:6,12|confirmed',
        'password_confirmation' =>'required|alpha_num|between:6,12',
        // 'logo'                  =>'required|max:10000|mimes:gif,jpeg,jpg,png',
    );

    public function scopeGetUpdateRules($query, array $inputs) {
        $validate_rule = array();

        foreach($inputs as $key => $value) {

            if(!empty($value)) {

                // $arr_input[$key] = $value;

                switch($key) {
                    case 'username'     :
                                $validate_rule[$key] = "required|min:3";
                                break;

                    case 'company_name' :
                                $validate_rule[$key] = "required|min:5";
                                break;

                    case 'tel_num'      :
                                $validate_rule[$key] = "numeric";
                                break;

                    case 'postcode'     :
                                $validate_rule[$key] = "numeric";
                                break;

                    case 'pic_full_name':
                                $validate_rule[$key] = "required|min:5";
                                break;

                    case 'email'        :
                                $validate_rule[$key] = "required|email";
                                break;

                    case 'mobile_no'    :
                                $validate_rule[$key] = "numeric";
                                break;

                    case 'bank_acc_no'  :
                                $validate_rule[$key] = "numeric";
                                break;

                    case 'password'     :
                                $validate_rule[$key] = "required|alpha_num|between:6,12|confirmed";
                                break;

                    case 'password_confirmation' :
                                $validate_rule[$key] = "required|alpha_num|between:6,12";
                                break;

                    // case 'logo'      :
                    //             $validate_rule[$key] = "required|max:10000|mimes:gif,jpeg,jpg,png";

                }

            }

        }
        return $validate_rule;
    }

    public function scopeGetUpdateInputs($query, array $inputs) {
        foreach($inputs as $key => $value) {
            // if($value != NULL) {
                $arr_input[$key] = $value;
            // }
        }
        return $arr_input;
    }

    public function scopeGetUpdateDbDetails($query, array $inputs) {
        $arr_udata = array();

        foreach($inputs as $key => $value) {
            switch ($key) {
                case 'username':
                    $arr_udata['username'] = $value;
                    break;

                case 'company_name':
                    $arr_udata['company_name'] = $value;
                    break;

                case 'company_reg_num':
                    $arr_udata['company_reg_num'] = $value;
                    break;

                case 'gst_reg_num':
                    $arr_udata['gst_reg_num'] = $value;
                    break;

                case 'non_gst':

                    if ($value == null OR $value == 0)
                        { $non_gst = 0; }
                    else
                        { $non_gst = $value; }


                    $arr_udata['non_gst'] = $non_gst;
                    break;
                    
                case 'notification':

                    if ($value == null OR $value == 0)
                        { $notification = 0; }
                    else
                        { $notification = $value; }


                    $arr_udata['notification'] = $notification;
                    break;
                
                case 'tel_num':
                    $arr_udata['tel_num'] = $value;
                    break;

                 case 'email':
                    $arr_udata['email'] = $value;
                    break;
                
                case 'email2':
                    $arr_udata['email1'] = $value;
                    break;

                case 'email3':
                    $arr_udata['email2'] = $value;
                    break;

                case 'pic_full_name':
                    $arr_udata['pic_full_name'] = $value;
                    break;

                case 'mobile_no':
                    $arr_udata['mobile_no'] = $value;
                    break;

                case 'ic_no':
                    $arr_udata['ic_no'] = $value;
                    break;

                case 'address1':
                    $arr_udata['address1'] = $value;
                    break;

                case 'address2':
                    $arr_udata['address2'] = $value;
                    break;

                case 'postcode':
                    $arr_udata['postcode'] = $value;
                    break;

                case 'country':
                    $arr_udata['country'] = $value;
                    break;

                case 'state':
                    $arr_udata['state'] = $value;
                    break;

                case 'city':
                    $arr_udata['city'] = $value;
                    break;

                case 'country':
                    $arr_udata['country'] = $value;
                    break;

                case 'bank_acc_no':
                    $arr_udata['bank_acc_no'] = $value;
                    break;

                case 'bank_type':
                    $arr_udata['bank_type'] = $value;
                    break;

                case 'status':
                    $arr_udata['active_status'] = $value;
                    break;

                case 'file_name':
                    $arr_udata['file_name'] = $value;
                    break;

                case 'password':
                    $arr_udata['password'] = Hash::make($value);
                    break;
                    
                case 'credit_term':
                    $arr_udata['credit_term'] = $value;
                    break;
                
                case 'business_method':
                    $arr_udata['business_method'] = $value;
                    break;
                
                case 'description':
                    $arr_udata['description'] = $value;
                    break;
            }
        }

        $arr_udata['modify_by']     = Session::get('username');
        $arr_udata['modify_date']   = date("Y-m-d H:i:s");

        return $arr_udata;
    }

    public function scopeUpdateSeller($query, $id, array $data) {
        $seller = DB::table('jocom_seller')
                    ->where('id', $id)
                    ->update($data);
        return $seller;
    }

    public function scopeGetOldFilename($query, $id) {
        $seller = DB::table('jocom_seller')
                    ->where('id', $id)
                    ->first();

        return $seller->file_name;
    }

    public function sortByCompany()
    {
        return DB::table($this->table)
            ->orderBy('company_name', 'asc')
            ->get();
    }
    
    public function sortByCompanyRegion()
    {
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                       ->where("status",1)->first();
        $states = DB::table('jocom_country_states')->where('region_id','=',$SysAdminRegion->region_id)->lists('id');
        
        if ($SysAdminRegion->region_id==0) {

            return DB::table($this->table)
            ->orderBy('company_name', 'asc')
            ->get();

        }else{

            return DB::table($this->table)
                ->whereIn('state',$states)
                ->orderBy('company_name', 'asc')
                ->get();
        }
    }

    public function scopeAlphabeticalOrder($query)
    {
        return $query->orderBy('company_name');
    }

}
