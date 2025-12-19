<?php

class BcardTransaction extends Eloquent
{
    public function setUpdatedAtAttribute($value)
    {
        // Disable Eloquent default `updated_at` column in DB
    }

    public function getDatatables()
    {
        return BcardTransaction::select('bcard_transactions.id', 'bcard_transactions.request', 'bcard_transactions.response', 'bcard_transactions.action as transaction_type', 'bcard_transactions.api', 'bcard_transactions.point', 'bcard_transactions.created_at', DB::raw('COUNT(`bcard_voids`.`id`) AS status'))
            ->leftJoin('bcard_voids', 'bcard_transactions.reward_id', '=', 'bcard_voids.reward_id')
            ->groupBy('bcard_transactions.id');
    }
}
