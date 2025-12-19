<?php

class PointConversionRate extends Eloquent
{
    public function scopeActive($query) {
        return $query->where('status', '=', 1);
    }

    public function scopeFrom($query, $typeId)
    {
        return $query->where('type_from', '=', $typeId);
    }

    public function scopeTo($query, $typeId)
    {
        return $query->where('type_to', '=', $typeId);
    }
}
