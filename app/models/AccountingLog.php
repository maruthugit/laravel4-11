<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class AccountingLog extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounting_log';

    public function CheckExist($filename)
    {
    	$row = AccountingLog::select('*')
    				->where('file_name', 'LIKE', '%'.$filename.'%')
    				->get();

    	if(count($row)>0)
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}
    }

    public function CheckLog($date)
    {
    	$row = AccountingLog::select('*')
    				->where('file_name', 'LIKE', '%missing_'.$date.'%')
    				->get();

    	if(count($row)>0)
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}
    }

}
?>