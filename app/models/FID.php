<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class FID extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    // const MAXADDR = 5;

    // public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_fid';

}
?>