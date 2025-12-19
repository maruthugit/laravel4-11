<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Country extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;

    protected $fillable = ['name'];

    public static $rules = [
        'name' => 'required|min:3',
    ];

    public static $message = [
        'name.required' => 'The field name is required.',
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_countries';

	/**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'insert_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modify_date';

    public function isValid() {
        $validation = Validator::make($this->attributes, static::$rules, static::$message);

        if($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

    public static function getActiveCountry(){
        
       $query =  Country::where("status",1)->get();
       return $query;
        
    }
		 
}