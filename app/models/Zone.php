<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Zone extends Eloquent implements UserInterface, RemindableInterface {
	
	use UserTrait, RemindableTrait;

    protected $fillable = ['name', 'init_weight', 'init_price', 'add_weight', 'add_price', 'min_weight', 'max_weight'];

    public static $rules = [
        'name' => 'required|min:3',
        'init_weight'=>'numeric',
        'init_price'=>'numeric',
        'add_weight'=>'numeric',
        'add_price'=>'numeric',
    ];

    public static $message = [
        'name.required' => 'The field name is required.',
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_zones';

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
    
        public function GetStates($id)
    {
        return DB::table('jocom_country_states')
                    ->select('id', 'name')
                    ->where('country_id', '=', $id)
                    ->get();
    }

    public function GetCities($id)
    {
        return DB::table('jocom_cities')
                    ->select('id', 'name')
                    ->where('state_id', '=', $id)
                    ->get();
    }

    public function insert_zone(array $inputs)
    {
        return DB::table('jocom_zones')
                    ->insertGetId($inputs);
    }

    public function insert_zone_state(array $inputs)
    {
        return DB::table('jocom_zone_states')
                    ->insert($inputs);
    }

    public function insert_zone_city(array $inputs)
    {
        return DB::table('jocom_zone_cities')
                    ->insert($inputs);
    }    
		 
}