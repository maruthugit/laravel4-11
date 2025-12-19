<?php

class Manager extends Eloquent
{
    protected $table = 'jocom_manager';

    public static $rules = array(
        'name' => 'required',
    );

    public function nameList() {
    	return Manager::where('status', '<', 2)->lists('name');
    }

    public function activeList() {
    	return Manager::where('status', '=', 1)->lists('name');
    }
}
