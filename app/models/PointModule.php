<?php

class PointModule extends Eloquent
{
    public static function getStatus($module)
    {
        return PointModule::where('name', '=', $module)
            ->pluck('status');
    }
}
