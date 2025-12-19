<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class CustomerCategory extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

    public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_user_category';

	
	public static $rules = array(
	   'username'=>'required',
	);
   
	public function scopeGetUpdateInputs($query, array $inputs) {
        $arr_input  = array();
        foreach($inputs as $key => $value) {
            if(!empty($value)) {
                $arr_input[$key] = $value;
            }
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

                case 'category_id':
                    $arr_udata['category_id'] = $value;
                    break;
                
            }
        }

        return $arr_udata;
    }

    public function scopeUpdateCustomerCategory($query, $id, array $data) {
        $category = DB::table('jocom_user_category')
                    ->where('id', $id)
                    ->update($data);
        return $category;
    }


}
?>