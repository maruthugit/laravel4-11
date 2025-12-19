<?php

class BcardVoid extends Eloquent
{
    public function setUpdatedAtAttribute($value)
    {
        // Disable Eloquent default `updated_at` column in DB
    }
}
