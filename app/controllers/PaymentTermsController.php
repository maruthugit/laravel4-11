<?php

class PaymentTermsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function payments() {     
        $payments = PaymentTerms::select('id', 'period', 'status')
                        ->where('status', '<', '2');;

        return Datatables::of($payments)
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/payment-terms/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 9, 9, \'AND\'))
                                <a id="deletePayment" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/payment-terms/delete/{{$id}}"><i class="fa fa-remove"></i></a>
                                @endif
                            ')
                        ->edit_column('status', '
                                @if($status == 1)
                                    <p class="text-success">Active</p>
                                @else
                                    <p class="text-danger">Inactive</p>
                                @endif
                                ')
                        ->make();
    }

    /**
     * Display a listing of the payment terms.
     *
     * @return Response
     */
    public function index() {
        $paymentTerms = PaymentTerms::all();
        return View::make('payment-terms.index', ['payemnt_period' => $paymentTerms]);
    }


    /**
     * Show the form for creating a new payment terms.
     *
     * @return Response
     */
    public function create() {   
        return View::make('payment-terms.create_paymentterms');
    }

     /**
     * Store a newly created payment terms in storage.
     *
     * @return Response
     */
    public function store() {
        $payment_input   = Input::all();

        $validator = Validator::make(Input::all(), PaymentTerms::$rules);

        $payment = new PaymentTerms;
            
        if ($validator->passes()) {
            $payment->period = Input::get('period');
            $payment->status = 1;
            $payment->created_by = Session::get('username');
            $payment->updated_by = Session::get('username');
            
            if($payment->save())
            {
                General::audit_trail('PaymentTermsController.php', 'store()', 'Add Payment Terms', Session::get('username'), 'CMS');
                return Redirect::to('/payment-terms')->with('success', 'Payment Terms(ID: '.$payment->id.') added successfully.');
            }
               
        } else {

            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
        
    }

    /**
     * Show the form for editing the specified payment terms.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $payment = PaymentTerms::find($id);

        if ($payment == null) {
            return Redirect::to('/payment-terms');
        }

        $statuses = array("Inactive", "Active");

        return View::make('payment-terms.edit')->with(array(
            'payment' => $payment,
            'statuses' => $statuses
        ));
    }

    /**
     * Update the specified payment terms in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $validator = Validator::make(Input::all(), PaymentTerms::$rules);

        if ($validator->passes()) {

            $payment = PaymentTerms::find($id);

            if ($payment == null) {
                return Redirect::to('/payment');
            }

            $payment->period = Input::get('period');
            $payment->status = Input::get('status');
            $payment->updated_by = Session::get('username');

            if($payment->save()) {
                General::audit_trail('PaymentTermsController.php', 'update()', 'Update Payment Terms', Session::get('username'), 'CMS');
                return Redirect::to('/payment-terms')->with('success', 'Payment Terms(ID: '.$payment->id.') updated successfully.');
            }
        } else {
            return Redirect::back()
                    ->withErrors($validator)->withInput();
        }
    }
 
    /**
     * Remove the specified payment terms from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id) {
        $payment = PaymentTerms::find($id);
        $payment->status = 2;
        $payment->updated_by = Session::get('username');
        $payment->save();
        
        $insert_audit = General::audit_trail('PaymentTermsController.php', 'delete()', 'Delete Payment Terms', Session::get('username'), 'CMS');

        return Redirect::to('/payment-terms');
    }
}
?>