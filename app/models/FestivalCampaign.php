<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FestivalCampaign extends Eloquent
{
    use SoftDeletingTrait;
    protected $table = 'jocom_festival_campaigns';
    protected $dates = ['deleted_at'];
}
