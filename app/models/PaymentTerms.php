<?php

class PaymentTerms extends Eloquent
{
    protected $table = 'payment_terms';

    /**
     * Validation rules for creating a new payment terms.
     * @var array
     */
    public static $rules = array(
        'period' => 'required|numeric|min:1',
    );

    public function getPaymentTermsById($id) {
    	return DB::table('payment_terms')
    			->where('id', '=', $id)
    			->select('period')
    			->first()->period;
    }

    public function activeList() {
        return PaymentTerms::where('status', '=', 1)->lists('period');
    }
}
