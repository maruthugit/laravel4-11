<?php

class TPoint extends Eloquent
{
    protected $table = 'jocom_transaction_point';

    public function scopeTransaction($query, $transactionId)
    {
        return $query->join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
            ->where('transaction_id', '=', $transactionId)
            ->where('jocom_transaction_point.status', '=', 1);
    }

    public function scopeVoid($query, $transactionId, $pointTypeId)
    {
        $point = $query->where('transaction_id', '=', $transactionId)
            ->where('point_type_id', '=', $pointTypeId)
            ->delete();
    }

    public function scopeTransactionAmount($query, $transactionId)
    {
        return $query->select('amount')
            ->where('transaction_id', '=', $transactionId)
            ->where('jocom_transaction_point.status', '=', 1);
    }
}
