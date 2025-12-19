<?php

class RefundController extends BaseController {

	public function __construct()
    {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the refund.
     *
     * @return Response
     */
    public function anyIndex()
    {
       return View::make('refund.index');
    }


    // 07/04/2022 - Add checkbox/bulk confirmation fo marcho/finance 
    public function anyRefunds() {  

        $refunds = Refund::select('jocom_refund.id', 'jocom_refund.created_date', 'jocom_refund.trans_id','jocom_refund.created_by', 
                                    'jocom_refund_form.approve_by','jocom_refund.platform_store','jocom_refund.amount', 'jocom_refund.cn_no', 'jocom_refund.status')
                            // ->leftjoin('jocom_refund_details', 'jocom_refund_details.refund_id', '=', 'jocom_refund.id')
                            ->leftjoin('jocom_refund_form', 'jocom_refund_form.refund_id', '=', 'jocom_refund.id')
                            ->leftjoin('jocom_transaction', 'jocom_transaction.id', '=', 'jocom_refund.trans_id')
                            ->leftjoin('jocom_user', 'jocom_user.id', '=', 'jocom_transaction.buyer_id')
                            // ->where('jocom_refund.status', '!=', 'deleted')
                            ->orderBy('jocom_refund.id', 'DESC')
                            ->groupBy('jocom_refund.id');

        return Datatables::of($refunds)
                        ->edit_column('amount', '
                                        {{ number_format($amount, 2) }}
                                    ')
        				->edit_column('status', '
                                    @if($status == "approved" || $status == "confirmed")
                                        <p class="text-success">{{ ucfirst($status) }}</p>
                                    @else
                                        <p class="text-danger">{{ ucfirst($status) }}</p>
                                    @endif
                            ')
                        ->add_column('Finance Confirmation', 
                            '@if ($status == "confirmed" || $status == "pending" || $status == "deleted" || Refund::permission(Session::get(\'username\'), \'1,2,3,4\'))
                                <input type="checkbox" name="bulk_confirm[]" id="bulk_confirm" value="{{$id}}" disabled>
                            @else
                                <input type="checkbox" name="bulk_confirm[]" id="bulk_confirm" value="{{$id}}">
                            @endif
                            ')                                
                        ->add_column('Action', 
                            '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/refund/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        	@if (Refund::permission(Session::get(\'username\'), \'0,5\') && $status != "deleted" && $status != "confirmed")
                                <a id="deleteRefund" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/refund/delete/{{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                            ')
                        ->make();
    }

    // 07/04/2022 - Dashboard/total refund and transaction
    public function anyDashboard(){
        $month_start = date('Y-m-01'); // hard-coded '01' for first day
        $month_end  = date('Y-m-t');

        $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.)
        $week_start = date('Y-m-d', strtotime('-'.$day.' days')); // contains the date for Sunday of the current week as mm-dd-yyyy
        $week_end = date('Y-m-d', strtotime('+'.(7-$day).' days')); // contains the date for the Saturday of the current week as mm-dd-yyyy

        $totalRefundMonthly = DB::table('jocom_refund')
                ->selectRaw('round(sum(amount),2) as totalRefundMonthly')
                ->where(DB::raw('date(confirmed_date)'), '>=', $month_start)
                ->where(DB::raw('date(confirmed_date)'), '<=', $month_end)
                ->where('status', '=', 'confirmed')
                ->first();

        $totalRefundWeekly = DB::table('jocom_refund')
                ->selectRaw('round(sum(amount),2) as totalRefundWeekly')
                ->where(DB::raw('date(confirmed_date)'), '>=', $week_start)
                ->where(DB::raw('date(confirmed_date)'), '<=', $week_end)
                ->where('status', '=', 'confirmed')
                ->first();

        $totalTransMonthly = DB::table('jocom_refund')
                ->selectRaw('count(id) as totalTransMonthly')
                ->where(DB::raw('date(confirmed_date)'), '>=', $month_start)
                ->where(DB::raw('date(confirmed_date)'), '<=', $month_end)
                ->where('status', '=', 'confirmed')
                ->first();

        $totalTransWeekly = DB::table('jocom_refund')
                ->selectRaw('count(id) as totalTransWeekly')
                ->where(DB::raw('date(confirmed_date)'), '>=', $week_start)
                ->where(DB::raw('date(confirmed_date)'), '<=', $week_end)
                ->where('status', '=', 'confirmed')
                ->first();
                
        $returnTotal = array(
            "totalRefundMonthly" => $totalRefundMonthly->totalRefundMonthly ? "RM ".$totalRefundMonthly->totalRefundMonthly : "RM 0.00",
            "totalRefundWeekly" => $totalRefundWeekly->totalRefundWeekly ? "RM ".$totalRefundWeekly->totalRefundWeekly : "RM 0.00",
            "totalTransMonthly" => $totalTransMonthly->totalTransMonthly,
            "totalTransWeekly" => $totalTransWeekly->totalTransWeekly,
        );

        return $returnTotal;
    }

    public function anyCreate()
    {   
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND')) {
            return View::make('refund.create');
        }
        else
            return View::make('home.denied', array('module' => 'Refund > Add Refund'));

    }

    // 14/04/2022 - Add permission for refund
    public function anyPermission()
    {   
        return View::make('refund.permission');

    }

    // 14/04/2022 - View page of user for refund's permission
    public function userList() {
        return View::make('refund.user-list');
    }

    // 14/04/2022 - Get list of user to assign permission for refund
    public function ajaxFetchUser() {
        $users = DB::table('jocom_sys_admin')
            ->select('id', 'username', 'full_name', 'email');

        return Datatables::of($users)
            ->add_column('Action', '<a id="selectUser" class="btn btn-primary">Select</a>')
            ->make();
    }

    // 14/04/2022 - Add permission for refund
    public function anyCreatepermission() { 
        $username   = Input::get('username');
        $actions    = Input::get('actions');
        $email      = Input::get('user_email');

        $refundId = DB::table('jocom_refund_permission')
            ->insert(
                ['username'     => $username,
                'email'         => $email,
                'role'          => $actions,
                'status'        => 1,
                'created_by'    => Session::get('username'),
                'created_at'    => date("Y-m-d H:i:s")]
            );  

        return Redirect::to('/refund/permission');
    } 

    // 14/04/2022 - User permission for refund list
    public function userPermission() {  
        $permissions = DB::table('jocom_refund_permission')
                ->select('id', 'username', 'email', 'role', 'status', 'created_by')
                ->where('status', '<>', 3);

        return Datatables::of($permissions)
        				->edit_column('role', '@if($role==0){{Developer}} @elseif($role==1){{"Customer Service"}} @elseif($role==2){{Operations}} 
                            @elseif($role==3){{Interns}} @elseif($role==4){{"Operations Level-2"}} @elseif($role==5){{Finance}} @endif')    
                        ->edit_column('status', '@if($status==1){{Active}} @elseif($role==0){{"Inactive"}} @endif')                       
                        ->add_column('Action', 
                            '@if (Refund::permission(Session::get("username"), "0,4") )
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/refund/permission/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                <a id="deletePermission" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/refund/permission/delete/{{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                            ')
                        ->make();
    }

    // 14/04/2022 - Update permission for refund
    public function anyUpdatepermission() { 
        $id         = Input::get('id');
        $actions    = Input::get('actions');
        $status     = Input::get('status');

        $refundId = DB::table('jocom_refund_permission')
            ->where('id', '=', $id)
            ->update([
                'role'        => $actions,
                'status'      => $status,
                'modify_by'   => Session::get('username'),
                'modify_at'   => date("Y-m-d H:i:s"),
            ]);  

        return Redirect::to('/refund/permission');
    } 

    // 14/04/2022 - Open page to update user permission 
    public function editPermission($id){
        
        $permission = Refund::getPermission($id);
        
        return View::make('refund.permission_edit')
                ->with("permission",$permission);
        
    }

    // 14/04/2022 - Delete user permission 
    public function deletePermission($id) {  

        $refundId = DB::table('jocom_refund_permission')
            ->where('id', '=', $id)
            ->update([
                'role'        => $actions,
                'status'      => 3,
            ]);  

        Session::flash('success', 'Successfully deleted.');
        return Redirect::to('/refund/permission');
    }

    public function anyAjaxtrans() {
        return View::make('refund.ajaxtrans');
    }

    public function anyTransactionsajax() {
        $transactions   = Transaction::select(array(
                                        'jocom_transaction.id',
                                        'jocom_transaction.transaction_date',
                                        'jocom_transaction.buyer_id',
                                        'jocom_transaction.buyer_username',
                                        'jocom_transaction.total_amount',
                                        'jocom_transaction.status',
                                    ))
//                                    ->where('jocom_transaction.status', '=', 'completed')
//                                    ->whereNotExists(function($query) {
//                                        $query->select(DB::raw('trans_id'))
//                                              ->from('jocom_refund')
//                                              ->whereRaw('jocom_refund.trans_id = jocom_transaction.id');
//                                    })
                                    ->orderBy('jocom_transaction.id', 'DESC');

        return Datatables::of($transactions)
                                    ->edit_column('buyer_username', '{{ $buyer_username }}  ')
                                    ->edit_column('total_amount', '
                                        {{ number_format($total_amount, 2) }}
                                    ')
                                    ->edit_column('status', '{{ ucfirst($status) }}')
                                    ->add_column('Action', '<a id="selectTrans" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
                                    ->make();
    }

    public function anyAjaxproduct($id) {
        $transaction    = DB::table('jocom_transaction')
                            ->select('jocom_transaction.id', 'jocom_transaction.transaction_date', 'jocom_transaction.total_amount')
                            ->where('jocom_transaction.id', '=', $id)->first();

        return View::make('refund.ajaxproduct')->with([
                    'id'    => $id, 
                    'date'  => $transaction->transaction_date, 
                    'amount'  => $transaction->total_amount,
                    'itemID'  => $itemID,
                    ]);
    }

    public function anyProductsajax($id) {
        // $transactions = DB::table('jocom_transaction_details')
        //                 ->select('jocom_transaction_details.id', 'jocom_transaction_details.sku', 'jocom_products.name', 
        //                     'jocom_product_price.label','jocom_transaction_details.unit', 'jocom_transaction_details.price', 
        //                     'jocom_transaction_details.disc','jocom_transaction_details.gst_rate_item','jocom_transaction_details.total')
        //                 ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_transaction_details.product_id')
        //                 ->leftjoin('jocom_product_price', 'jocom_product_price.id', '=', 'jocom_transaction_details.p_option_id')
        //                 ->where('jocom_transaction_details.transaction_id', '=', $id)
        //                 ->orderBy('jocom_transaction_details.transaction_id', 'DESC');

        // $transactions = DB::table('jocom_transaction_details')
        //                 ->select(DB::Raw('ifnull(jocom_products.sku, jocom_transaction_details.sku) as sku'), 
        //                         DB::Raw('ifnull(jocom_products.id, jocom_transaction_details.product_id) as itemID'), 
        //                         'jocom_transaction_details.id', 'jocom_transaction_details.product_name', 
        //                         DB::Raw('ifnull(jocom_products.name, jocom_transaction_details.product_name) as name'),
        //                         DB::Raw('ifnull(jocom_product_price.label, jocom_transaction_details.price_label) as label'),
        //                         'jocom_transaction_details.unit', 'jocom_transaction_details.price', 'jocom_transaction_details.disc',
        //                         'jocom_transaction_details.gst_rate_item', 'jocom_transaction_details.total')
        //                 ->leftjoin('jocom_product_base_item', 'jocom_product_base_item.product_id', '=', 'jocom_transaction_details.product_id')
        //                 ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_product_base_item.product_base_id')
        //                 ->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_product_base_item.product_base_id')
        //                 ->where('jocom_transaction_details.transaction_id', '=', $id)
        //                 // ->where('jocom_product_base_item.status', '=', '1')
        //                 ->groupBy(DB::raw("ifnull(jocom_product_base_item.product_base_id, jocom_transaction_details.product_id)"))
        //                 ->orderBy('jocom_transaction_details.id', 'DESC');

        $transactions = DB::table('jocom_transaction_details')
                        ->select(DB::Raw('ifnull(jocom_products.sku, jocom_transaction_details.sku) as sku'), 
                                DB::Raw('ifnull(jocom_products.id, jocom_transaction_details.product_id) as itemID'), 
                                'jocom_transaction_details.id', 'jocom_transaction_details.product_name', 
                                DB::Raw('ifnull(jocom_products.name, jocom_transaction_details.product_name) as name'),
                                DB::Raw('ifnull(jocom_product_price.label, jocom_transaction_details.price_label) as label'),
                                'jocom_transaction_details.price')
                        ->leftjoin('jocom_product_base_item', 'jocom_product_base_item.product_id', '=', 'jocom_transaction_details.product_id')
                        ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_product_base_item.product_base_id')
                        ->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_product_base_item.product_base_id')
                        ->where('jocom_transaction_details.transaction_id', '=', $id)
                        // ->where('jocom_product_base_item.status', '=', '1')
                        ->groupBy(DB::raw("ifnull(jocom_product_base_item.product_base_id, jocom_transaction_details.product_id)"))
                        ->orderBy('jocom_transaction_details.id', 'DESC');

        return Datatables::of($transactions)
                            ->edit_column('name', '
                                {{ addslashes($name) }}
                            ')
                            // ->edit_column('price', '
                            //     {{ number_format($price, 2) }}
                            // ')
                            ->edit_column('price', '<input type="text" name="price" class="price form-control text-center" value="{{ number_format($price, 2) }}" size="4">')
                            ->add_column('Refund Quantity', '<input type="text" name="refund_quantity" class="refund_quantity form-control text-center" value="0" size="4">')
                            ->add_column('Refund Price', '<input type="text" name="refund_price" class="refund_price form-control text-center" value="{{ number_format($price, 2) }}" size="4">')
                            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
                            ->make();
    }

    // Add Refund
    // 21/03/2022 - change codes (add product's details direct to refund_details table)
    // 24/03/2022 - optimize the codees/remove un-used codes
    public function postStore() {
        // echo "<pre>";
        //     Print_r(Input::all());
        // echo "</pre>";
        // die();
        
        $refund             = new Refund;
        $validator          = Validator::make(Input::all(), Refund::$rules, Refund::$message);
        $refund_id          = "";
        $arr_refund_trans   = array();
        $arr_refund_other   = array();
        $arr_refund         = array();
        $arr_refund_type    = array();
        $arr_type           = array();
        $dest_path_pdf      = Config::get('constants.ATTACHMENT_PDF');
        $dest_path_image    = Config::get('constants.ATTACHMENT_IMAGE');
        $supp_docs          = Input::file('remark_doc');
        $collectAttachment  = [];

        if ($validator->passes()) {
            $refund->trans_id           = Input::get('trans_id');
            $refund->buyer_id           = Input::get('buyer_id');
            $refund->amount             = Input::get('grand_total_refund');
            $refund->customer_name      = Input::get('buyer');
            $refund->ic_no              = Input::get('ic_no');
            $refund->bank_name          = Input::get('bank_name');
            $refund->bank_account_no    = Input::get('bank_acc_no');
            $refund->email              = Input::get('email');
            $refund->order_no           = Input::get('order_no');
            $refund->platform_store     = Input::get('platform_store');
            $refund->hp_no              = Input::get('phone');
            $refund->address            = Input::get('address');
            $refund->postcode           = Input::get('postcode');
            $refund->remarks            = Input::get('remark');
            $refund->status             = "pending";
            $refund->timestamps         = false;
            $refund->created_from       = "manual";
            $refund->created_by         = Session::get('username');
            $refund->created_date       = date("Y-m-d H:i:s");
            
            if ($refund->save()) { // Save refund request
                $refund_id                      = $refund->id;
            }

            if (Input::has('trans')) { // insert refund details
                // var_dump(Input::get('trans'));

                if ($refund_id != "")  $arr_refund_trans['refund_id'] = $refund_id;

                $arr_transaction        = Input::get('trans');
                $arr_refund_quantity    = Input::get('refund_quantity');
                $arr_refund_price       = Input::get('refund_price');
                $arr_product_name       = Input::get('productName');
                $arr_sku                = Input::get('sku');
                $arr_item_name          = Input::get('itemName');
                $arr_label              = Input::get('label');
                $arr_price              = Input::get('price');
                $arr_prod_id            = Input::get('productID');
                $i = 0;

                foreach ($arr_transaction as $transaction) {

                    $arr_refund_trans['trans_detail_id']    = $transaction;
                    $arr_refund_trans['product_name']       = $arr_product_name[$i];
                    $arr_refund_trans['sku']                = $arr_sku[$i];
                    $arr_refund_trans['item_name']          = $arr_item_name[$i];
                    $arr_refund_trans['label']              = $arr_label[$i];
                    $arr_refund_trans['ori_price']          = $arr_price[$i];

                    $arr_trans_items = Refund::get_trans_item($transaction);

                    if(count($arr_trans_items) > 0) {
                        foreach($arr_trans_items as $key => $value) {

                            // if($key == "disc") echo "<br>".$arr_disc[$i];

                            switch ($key) {
                                case 'product_id'   : $arr_refund_trans['product_id']   = $arr_prod_id[$i];
                                    break;

                                case 'unit'         : $arr_refund_trans['unit']         = $arr_refund_quantity[$i];
                                    break;

                                case 'p_option_id'  : $arr_refund_trans['price_id']     = $value;
                                    break;

                                case 'gst_rate_item': $arr_refund_trans['gst_rate']     = $value;
                                    break;

                                case 'price'        : $arr_refund_trans['price']        = $arr_refund_price[$i];
                                    break;

                                case 'total'        : $arr_refund_trans['total']        = number_format(($arr_refund_quantity[$i]*$arr_refund_price[$i]), 2);
                                    break;

                                default:
                                    $arr_refund_trans[$key] = $value;
                            }
                            
                        }
                    }
                    $i++;
                    $arr_refund[] = $arr_refund_trans;

                }
                // echo "<br><br>* * * * * *[TRANS] ARR_REFUND = = = >";
                // var_dump($arr_refund);
                if (count($arr_refund) > 0) {
                    Refund::insert_refund_details($arr_refund); 
                }
            }

            if (Input::has('other')) { // insert others into refund details table
                $arr_other  = Input::get('other');
                $arr_refund = "";
                $count      = 0;

                foreach ($arr_other as $other) {
                    foreach ($other as $key => $value) {
                        if($key == "name") {
                            // echo "<br> - - - - - [COUNT: $count] - - - - - - - - - - - - - - -";
                            if(count($arr_refund_other) > 0) {
                                $arr_refund[]       = $arr_refund_other;
                                $arr_refund_other   = "";
                            }
                            
                            if ($refund_id != "")  $arr_refund_other['refund_id'] = $refund_id;
                        }
                        // echo "<br>[OTHER] [$key] $value";

                        switch ($key) {
                            case 'name'     :   
                                                $arr_refund_other['product_name'] = $value;
                                                $count++;
                                break;

                            default:
                                $arr_refund_other[$key] = $value;
                        }
                    }

                }
                $arr_refund[] = $arr_refund_other;

                // echo "<br><br>* * * * *[OTHER] ARR_REFUND = = = >";
                // var_dump($arr_refund);

                if (count($arr_refund) > 0) {
                    Refund::insert_refund_details($arr_refund);
                }
            }
            
            // Add supporting docs
            $attachment = 0;
            if (Input::hasFile('remark_doc')) {
                foreach($supp_docs as $doc){
                    $file_ext               = $doc->getClientOriginalExtension();
                    $attachment++;
                    $file_name_attachment   = 'refund_'.$refund_id.'_'.$refund->trans_id.'_attachment_'.$attachment. '.'. $file_ext;
                    
                    array_push($collectAttachment, $file_name_attachment);
    
                    if(strtolower($file_ext) == "pdf") { 
                        $upload_file_attachment  = $doc->move($dest_path_pdf, $file_name_attachment);
                    }else{
                        $upload_file_attachment  = $doc->move($dest_path_image, $file_name_attachment);
                    }
                }
            }

            if ($attachment > 0) {
                foreach($collectAttachment as $attachmentName){
                    $refundAttachment = DB::table('jocom_refund_supp_docs')
                                    ->insert(
                                        ['refund_id' => $refund_id,
                                        'supp_docs'  => $attachmentName,
                                        'created_at' => date("Y-m-d H:i:s")]
                                    );
                }
            }

            // Create pdf for "Refund Request Form"
            // Collect refund data/info
            $refund_form['refund_id']         = $refund_id;
            $refund_form['customer_name']     = $refund->customer_name;
            $refund_form['ic_no']             = $refund->ic_no;
            $refund_form['hp_no']             = $refund->hp_no;
            $refund_form['email']             = $refund->email;
            $refund_form['address']           = $refund->address;
            $refund_form['postcode']          = $refund->postcode;
            $refund_form['bank_name']         = $refund->bank_name;
            $refund_form['bank_account']      = $refund->bank_account_no;
            $refund_form['trans_id']          = $refund->trans_id;
            $refund_form['order_no']          = $refund->order_no;
            $refund_form['platform_store']    = $refund->platform_store;
            $refund_form['total']             = $refund->amount;
            $refund_form['remarks']           = $refund->remarks;
            $refund_form['supp_docs']         = Input::hasFile('remark_doc') ? "Yes" : "No";
            $refund_form['request_by']        = Session::get('username');
            $refund_form['date_request']      = date("Y-m-d H:i:s");

            Refund::insert_form_details($refund_form); // insert details into refund_form table
            return $this->generatePDF($refund_id, $refund_form); // Go to generatePDF() function

        }
        else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
    }

    // 21/03/2022 -  change codes (add product's details direct to refund_details table)
    // 24/03/2022 - optimize the codees/remove un-used codes
    public function getEdit($id) {
        $refund             = Refund::get_refund($id);
        $refund_products    = Refund::get_refund_products($id, $refund->status);
        $refund_others      = Refund::get_refund_others($id, $refund->status);
        $refund_types       = Refund::get_refund_types($id);
        $refund_remarks     = Refund::get_refund_remarks($id);
        $refund_supp_docs   = Refund::get_refund_supp_docs($id);
        $cn_no              = Refund::get_cn_no($id);

        $types              = array();
        $buyer_details      = array();
        $amount_n_type;

        // var_dump($refund_types);
        if (count($refund_types) > 0) {
            foreach($refund_types as $type) {
                $arr_type = array();

                switch($type->refund_type) {
                    case 'Cash'     : $amount_n_type = Config::get('constants.CURRENCY').' '.$type->amount;
                        break;

                    case 'JoPoint'  : $amount_n_type = $type->amount." points";
                        break;

                    default:
                        $amount_n_type = $type->amount;
                }

                $arr_type['id']             = $type->id;
                $arr_type['refund_type']    = $type->refund_type;
                $arr_type['amount']         = $type->amount;
                $arr_type['amount_type']    = ($type->amount_type == "deduct") ? "-" : "+";
                $arr_type['amount_n_type']  = $amount_n_type;
                $arr_type['cash_value']     = $type->cash_value;
                $arr_type['coupon_code']    = $type->coupon_code;
                $types[] = $arr_type;
            }
        } 

        $buyer_details  = ($cn_no == "") ? Refund::get_buyer_details($id) : Refund::get_cn_details($id);
        // var_dump($types);
        
        return View::make('refund.edit')->with(array(
            'refund'            => $refund,
            'products'          => $refund_products,
            'others'            => $refund_others,
            'types'             => $types,
            'remarks'           => $refund_remarks,
            'cn_no'             => $cn_no,
            'buyer_details'     => $buyer_details,
            'refund_supp_docs'  => $refund_supp_docs,
        ));
    }

    // 25/03/2022 - optimize the codees/remove un-used codes
    public function anyUpdate($id) {
        // echo "<pre>";
        //     Print_r(Input::all());
        // echo "</pre>";
        // die();
        // var_dump(Input::all());
        // exit;

        $arr_buyer          = array();
        $buyer_details      = Refund::get_buyer_details($id);
        $refund             = Refund::get_refund($id);

        if(Input::has('reject')) {
            // echo "<br>[UPDATE] Reject Refund!";
            $this->postReject($id);
            return Redirect::to('/refund/index');
        }
        else if (Input::has('save_edit')) { // Edit Refund
            $action = Input::get('save_edit');

            // Temporary for operations to replace uploaded file
            $dest_path_pdf              = Config::get('constants.ATTACHMENT_PDF');
            $dest_path_image            = Config::get('constants.ATTACHMENT_IMAGE');
            $supp_docs                  = Input::file('remark_doc');
            $collectAttachment          = [];
            
            // Add supporting docs
            $attachment = 0;
            if (Input::hasFile('remark_doc')) {

                foreach($supp_docs as $doc){
                    $file_ext               = $doc->getClientOriginalExtension();
                    $attachment++;
                    $file_name_attachment   = 'refund_'.$id.'_'.$refund->trans_id.'_attachment_'.$attachment. '.'. $file_ext;
                    
                    array_push($collectAttachment, $file_name_attachment);
    
                    if(strtolower($file_ext) == "pdf") { 
                        $upload_file_attachment  = $doc->move($dest_path_pdf, $file_name_attachment);
                    }else{
                        $upload_file_attachment  = $doc->move($dest_path_image, $file_name_attachment);
                    }
                }
            }

            if ($attachment > 0) {
                foreach($collectAttachment as $attachmentName){
                    $refundAttachment = DB::table('jocom_refund_supp_docs')
                                    ->insert(
                                        ['refund_id' => $id,
                                        'supp_docs'  => $attachmentName,
                                        'created_at' => date("Y-m-d H:i:s")]
                                    );
                }
            }

            if (Input::has('trans')) { // insert refund details
                // var_dump(Input::get('trans'));

                if ($refund_id != "")  $arr_refund_trans['refund_id'] = $refund_id;

                $arr_transaction        = Input::get('trans');
                $arr_refund_quantity    = Input::get('refund_quantity');
                $arr_refund_price       = Input::get('refund_price');
                $arr_product_name       = Input::get('productName');
                $arr_sku                = Input::get('sku');
                $arr_item_name          = Input::get('itemName');
                $arr_label              = Input::get('label');
                $arr_price              = Input::get('price');
                $arr_prod_id            = Input::get('productID');
                $i = 0;

                foreach ($arr_transaction as $transaction) {

                    $arr_refund_trans['trans_detail_id']    = $transaction;
                    $arr_refund_trans['product_name']       = $arr_product_name[$i];
                    $arr_refund_trans['sku']                = $arr_sku[$i];
                    $arr_refund_trans['item_name']          = $arr_item_name[$i];
                    $arr_refund_trans['label']              = $arr_label[$i];
                    $arr_refund_trans['ori_price']          = $arr_price[$i];

                    $arr_trans_items = Refund::get_trans_item($transaction);

                    if(count($arr_trans_items) > 0) {
                        foreach($arr_trans_items as $key => $value) {

                            // if($key == "disc") echo "<br>".$arr_disc[$i];

                            switch ($key) {
                                case 'product_id'   : $arr_refund_trans['product_id']   = $arr_prod_id[$i];
                                    break;

                                case 'unit'         : $arr_refund_trans['unit']         = $arr_refund_quantity[$i];
                                    break;

                                case 'p_option_id'  : $arr_refund_trans['price_id']     = $value;
                                    break;

                                case 'gst_rate_item': $arr_refund_trans['gst_rate']     = $value;
                                    break;

                                case 'price'        : $arr_refund_trans['price']        = $arr_refund_price[$i];
                                    break;

                                case 'total'        : $arr_refund_trans['total']        = number_format(($arr_refund_quantity[$i]*$arr_refund_price[$i]), 2);
                                    break;

                                default:
                                    $arr_refund_trans[$key] = $value;
                            }
                            
                        }
                    }
                    $i++;
                    $arr_refund[] = $arr_refund_trans;

                }
                // echo "<br><br>* * * * * *[TRANS] ARR_REFUND = = = >";
                // var_dump($arr_refund);
                if (count($arr_refund) > 0) {
                    Refund::insert_refund_details($arr_refund); 
                }
            }

            // Temporary for operations to replace uploaded file
            $refund_form['refund_id']         = $id;
            $refund_form['trans_id']          = Input::get('trans_id');
            $refund_form['customer_name']     = Input::get('buyer');
            $refund_form['ic_no']             = Input::get('ic_no');
            $refund_form['hp_no']             = Input::get('hp_no');
            $refund_form['email']             = Input::get('email');
            $refund_form['address']           = Input::get('address');
            $refund_form['postcode']          = Input::get('postcode');
            $refund_form['bank_name']         = Input::get('bank_name');
            $refund_form['bank_account']      = Input::get('bank_acc_no');
            $refund_form['order_no']          = Input::get('order_no');
            $refund_form['platform_store']    = Input::get('platform_store');
            $refund_form['total']             = Input::get('grand_total_refund');
            $refund_form['remarks']           = Input::get('remark');

            Refund::update_refund($id, $refund_form, $action); // Update refund
            Refund::update_form_details($refund_form); 

            return $this->generatePDF($id, $refund_form); // Go to generatePDF() function

        }
        else if (Input::has('approve')) { // Approve Refund. For Kean, Melissa and Coco\
            $action = Input::get('approve');

            $get_refund_prod_id         = Input::get('product');
            $get_refund_oriPrice        = Input::get('price');
            $get_refund_oriPrice_req    = Input::get('refund_oriPrice_request');
            $get_refund_quantity        = Input::get('refund_quantity');
            $get_refund_quantity_req    = Input::get('refund_quantity_request');
            $get_refund_price           = Input::get('refund_price');
            $get_refund_price_req       = Input::get('refund_price_request');
            $get_refund_total           = Input::get('grand_total_refund');
            $get_refund_remark          = Input::get('remark');

            $dest_path_pdf              = Config::get('constants.ATTACHMENT_PDF');
            $dest_path_image            = Config::get('constants.ATTACHMENT_IMAGE');
            $supp_docs                  = Input::file('remark_doc');
            $collectAttachment          = [];
            
            // Refund::update_refund_total($id, $get_refund_total); // Update total refund

            // Combine array. Get created/requested refund (exist product when approving refund)
            // $res = array();
            // foreach($get_refund_prod_id as $key=>$val){ // Loop though existed product
            //     $val2 = $get_refund_quantity_req[$key]; // Get the object and values from the existed product
            //     $val3 = $get_refund_price_req[$key]; // Get the  object and values from the existed product
            //     $val4 = $get_refund_oriPrice_req[$key]; // Get the  object and values from the existed product
            //     $res[$key] = $val + array('refund_id' => $id) + $val2 + $val3 + $val4; // combine 'em
            // }

            if (Input::has('product')) { // If refund has product (from created/requested refund)
                // foreach($res as $key => $value){
                foreach($get_refund_prod_id as $key => $value){
                    Refund::update_refund_details($value); // Update refund product details
                }
                // }
            }

            // If there are new product added when approving the refund's request
            if (Input::has('trans')) { 
                // var_dump(Input::get('trans'));

                $arr_refund_trans['refund_id'] = $id;

                $arr_transaction        = Input::get('trans');
                $arr_refund_quantity    = Input::get('refund_quantity');
                $arr_refund_price       = Input::get('refund_price');
                $arr_prod_id            = Input::get('productID');
                $arr_product_name       = Input::get('productName');
                $arr_sku                = Input::get('sku');
                $arr_item_name          = Input::get('itemName');
                $arr_label              = Input::get('label');
                $arr_price              = Input::get('price');
                $i = 0;

                foreach ($arr_transaction as $transaction) {

                    $arr_refund_trans['trans_detail_id']    = $transaction;
                    $arr_refund_trans['product_name']       = $arr_product_name[$i];
                    $arr_refund_trans['sku']                = $arr_sku[$i];
                    $arr_refund_trans['item_name']          = $arr_item_name[$i];
                    $arr_refund_trans['label']              = $arr_label[$i];
                    $arr_refund_trans['ori_price']          = $arr_price[$i];


                    $arr_trans_items = Refund::get_trans_item($transaction);

                    if(count($arr_trans_items) > 0) {
                        foreach($arr_trans_items as $key => $value) {

                            // if($key == "disc") echo "<br>".$arr_disc[$i];

                            switch ($key) {
                                case 'product_id'   : $arr_refund_trans['product_id']   = $arr_prod_id[$i];
                                    break;

                                case 'unit'         : $arr_refund_trans['unit']         = $arr_refund_quantity[$i];
                                    break;

                                case 'p_option_id'  : $arr_refund_trans['price_id']     = $value;
                                    break;

                                case 'gst_rate_item': $arr_refund_trans['gst_rate']     = $value;
                                    break;

                                case 'price'        : $arr_refund_trans['price']        = $arr_refund_price[$i];
                                    break;

                                // case 'disc'         : $arr_refund_trans['disc']         = $arr_disc[$i];
                                //     break;

                                case 'total'        : $arr_refund_trans['total']        = number_format(($arr_refund_quantity[$i]*$arr_refund_price[$i]-$arr_disc[$i]), 2);
                                    break;

                                default:
                                    $arr_refund_trans[$key] = $value;
                            }
                            
                        }
                    }
                    $arr_refund_trans['approved']  = 1;
                    $i++;
                    $arr_refund[] = $arr_refund_trans;

                }
                // echo "<br><br>* * * * * *[TRANS] ARR_REFUND = = = >";
                // Log::info($arr_refund);
                if (count($arr_refund) > 0) {
                    Refund::insert_refund_details($arr_refund);
                }
            }

            if (Input::has('other_request')) { // Update existed "Others" 
                $other_request = Input::get('other_request');
                // Log::info($other_request);
                foreach($other_request as $key => $value){
                    Refund::update_refund_others($value); 
                }
            }

            if (Input::has('other')) { // Add "Others" if there are new details added when approving refund's cosmic sin request
                $arr_other  = Input::get('other');
                $arr_refund = "";

                foreach ($arr_other as $other) {
                    foreach ($other as $key => $value) {
                        if($key == "name") {
                            if(count($arr_refund_other) > 0) {
                                $arr_refund[]       = $arr_refund_other;
                                $arr_refund_other   = "";
                            }
                            
                            $arr_refund_other['refund_id'] = $id;
                        }

                        switch ($key) {
                            case 'name'     :   
                                                $arr_refund_other['product_name'] = $value;
                                                $count++;
                                break;

                            default:
                                $arr_refund_other[$key] = $value;
                        }
                    }

                }
                $arr_refund_other['approved']  = 1;
                $arr_refund[] = $arr_refund_other;

                // echo "<br><br>* * * * *[OTHER] ARR_REFUND = = = >";
                // var_dump($arr_refund);

                if (count($arr_refund) > 0) {
                    Refund::insert_refund_details($arr_refund);
                }
            }

            // Temporary for operations to replace uploaded file
            // Add supporting docs
            $attachment = 0;
            if (Input::hasFile('remark_doc')) {

                foreach($supp_docs as $doc){
                    $file_ext               = $doc->getClientOriginalExtension();
                    $attachment++;
                    $file_name_attachment   = 'refund_'.$id.'_'.$refund->trans_id.'_attachment_'.$attachment. '.'. $file_ext;
                    
                    array_push($collectAttachment, $file_name_attachment);
    
                    if(strtolower($file_ext) == "pdf") { 
                        $upload_file_attachment  = $doc->move($dest_path_pdf, $file_name_attachment);
                    }else{
                        $upload_file_attachment  = $doc->move($dest_path_image, $file_name_attachment);
                    }
                }
            }

            if ($attachment > 0) {
                foreach($collectAttachment as $attachmentName){
                    $refundAttachment = DB::table('jocom_refund_supp_docs')
                                    ->insert(
                                        ['refund_id' => $id,
                                        'supp_docs'  => $attachmentName,
                                        'created_at' => date("Y-m-d H:i:s")]
                                    );
                }
            }
            // Temporary for operations to replace uploaded file


            // if(Input::has('remark')){ // Update remarks
            //     Refund::update_refund_remark($id, $get_refund_remark);
            // }
            
            // WHY NEED TO UPDATE JOCOM_PRODUCT_PRICE
            // if (count($refund_products) > 0) {
            //     foreach ($refund_products as $products) {
            //         if ($products->price_id != "0") {
            //             // echo "<br> [".$products->name."] [Price Label : ".$products->price_id ."] [Qty: ".$products->unit."]";
            //             $arr_stock['qty'] = 'qty + '.$products->unit;
                        
            //             Refund::add_stock($products->price_id, $products->unit);
            //         }
            //     }
            // }
            
            // Update refund details for Refund Request Form
            $refund_form['refund_id']         = $id;
            $refund_form['trans_id']          = Input::get('trans_id');
            $refund_form['customer_name']     = Input::get('buyer');
            $refund_form['ic_no']             = Input::get('ic_no');
            $refund_form['hp_no']             = Input::get('hp_no');
            $refund_form['email']             = Input::get('email');
            $refund_form['address']           = Input::get('address');
            $refund_form['postcode']          = Input::get('postcode');
            $refund_form['bank_name']         = Input::get('bank_name');
            $refund_form['bank_account']      = Input::get('bank_acc_no');
            $refund_form['order_no']          = Input::get('order_no');
            $refund_form['platform_store']    = Input::get('platform_store');
            $refund_form['total']             = Input::get('grand_total_refund');
            $refund_form['remarks']           = Input::get('remark');
            $refund_form['approve_by']        = Session::get('username');
            $refund_form['date_approve']      = date("Y-m-d H:i:s");

            Refund::update_refund($id, $refund_form, $action); // Update refund
            Refund::update_form_details($refund_form); 
            // Refund::update_refund_status($id, "approved");

            return $this->generatePDF($id, $refund_form); // Go to generatePDF() function
     
        }
        else if (Input::has('confirm')) { // Confirm refund. By Finance
       	    // echo "<pre>"; 
            //     var_dump(Input::all());
            // echo "</pre>";
            // exit;
            $buyer_id = Input::get('buyer_id');
            $trans_id = Input::get('trans_id');

            if ($this->postConfirm($id, $buyer_id, $trans_id)) {
                $cn_no              = Refund::get_cn_no($id);
                $buyer_details      = Refund::get_buyer_details($id);
                $refund_types       = Refund::get_refund_types($id);

                if (count($refund_types) > 0) {
                    foreach($refund_types as $type) {
                        $arr_type       = array();
                        $amount_type    = "";
                        $coupon_code    = "-";
                        $amount_type    = ($type->amount_type == "deduct") ? "-" : "+";

                        switch($type->refund_type) {
                            case 'Cash'     :   $amount_n_type = Config::get('constants.CURRENCY').' '.number_format($type->amount, 2);
                                break;

                            case 'JoPoint'  :   $amount_n_type  = $amount_type . $type->amount." points";
                                break;

                            default:
                                $amount_n_type = $type->amount;
                        }

                        $arr_type['id']             = $type->id;
                        $arr_type['refund_type']    = $type->refund_type;
                        $arr_type['amount']         = $type->amount;
                        $arr_type['amount_type']    = $amount_type;
                        $arr_type['amount_n_type']  = $amount_n_type;
                        $arr_type['cash_value']     = $type->cash_value;
                        $arr_type['coupon_code']    = $type->coupon_code;
                        $types[] = $arr_type;
                    }
                } 
                
                // $refund_form['refund_id']         = $id;
                // $refund_form['trans_id']          = Input::get('trans_id');
                // $refund_form['approve_by']        = Session::get('username');
                // $refund_form['date_approve']      = date("Y-m-d H:i:s");
                // Refund::update_form_details($refund_form); // update refund's details 
                // return $this->generatePDF($id, $refund_form); // Go to generatePDF() function

                Refund::update_refund_status($id, "confirmed");

                return Redirect::to('/refund/edit/'.$id);
            }
        }
        else if (Input::has('save')) { //Save button on finance site
            $this->postSave($id);

            $refund_types       = Refund::get_refund_types($id);
            $buyer_details      = ($cn_no == "") ? Refund::get_buyer_details($id) : Refund::get_cn_details($id);

            return Redirect::to('/refund/edit/'.$id)->with(array(
                                'cn_no'             => $cn_no,
                                'buyer_details'     => $buyer_details,
                        ));
        }
        else if (Input::has('generateCN')) { // Generate Credit Note. By finance
            // var_dump(Input::all());
            // exit;
            $cur_cn_no  = Refund::generate_cn_no(); // get counter number
            $new_cn_no  = $cur_cn_no + 1;
            
            $arr_buyer['refund_id']     = $id;
            $arr_buyer['full_name']     = Input::get('full_name');
            $arr_buyer['attn']          = Input::get('attn');
            $arr_buyer['address1']      = Input::get('address1');
            $arr_buyer['address2']      = Input::get('address2');
            $arr_buyer['postcode']      = Input::get('postcode');
            $arr_buyer['city']          = Input::get('city');
            $arr_buyer['state']         = Input::get('state');
            $arr_buyer['country']       = Input::get('country');
            $arr_buyer['gst_no']        = Input::get('gst_no');
            $arr_buyer['reason']        = Input::get('reason');
            $arr_buyer['created_by']    = Session::get('username');
            $arr_buyer['created_date']  = date("Y-m-d H:i:s");

            Refund::insert_cn_details($arr_buyer);

            if ($new_cn_no != "") {
                // echo "<br>[CN NO] [Cur: ".$cur_cn_no."] [New: ".$new_cn_no."]";
                Refund::update_cn_running("cn_no", $new_cn_no);
                return $this->generateCN($id, $new_cn_no, $arr_buyer);
            }

            return Redirect::to('/refund/edit/'.$id);

        }
    }

    public function postReject($id) {
        echo "<br>[REJECT] Form [ID: $id]";
        Refund::update_refund_status($id, "rejected");
    }

    public function postConfirm($id, $buyer_id, $trans_id) {
        $refund_id = $id;

        if (Input::has('type')) {
            $arr_types      = Input::get('type');
            $count          = 0;

            foreach ($arr_types as $type) {
                foreach ($type as $key => $value) {
                    $amount_type = "";

                    if($key == "name") {
                        if (count($arr_refund_type) > 0) {
                            $arr_type[]         = $arr_refund_type;
                            $arr_refund_type    = "";
                        }
                    }

                    if ($key == "amount_type" && $value != "") {
                        $amount_type = ($value == "+") ? "add" : "deduct";
                    }

                    if ($refund_id != "") $arr_refund_type['refund_id'] = $refund_id;

                    switch ($key) {
                        case 'name'         :   $arr_refund_type['refund_type'] = $value;
                                                $count++;
                            break;

                        case 'amount_type'  :   $arr_refund_type['amount_type'] = $amount_type;
                            break;

                        default:
                            $arr_refund_type[$key] = $value;

                    }
                }
            }

            $arr_type[] = $arr_refund_type;

            if(count($arr_type) > 0) {
                Refund::insert_refund_types($arr_type);
            }


            $remark = trim(Input::get('new_remark'));

            if ($remark != "") {
                $arr_remark['refund_id']    = $refund_id;
                $arr_remark['remark']       = $remark;
                $arr_remark['created_by']   = Session::get('username');
                $arr_remark['created_date'] = date('Y-m-d H:i:s');

                Refund::insert_refund_remark($arr_remark);
            }

            // echo "<br>[Buyer ID: ".$buyer_id."] [Trans ID: ".$trans_id."]";
			
            // // Why need this codes? (point)
			// $arr_point      = PointUser::getOrCreate($buyer_id, PointType::JOPOINT, TRUE);
			
			// if (count($arr_point) > 0) {
            //     $pointUser      = PointUser::getPoint($buyer_id, PointType::JOPOINT, true);

            //     if ($pointUser) {
            //         $pointTrans  = new PointTransaction($pointUser);
            //         $pointRemark = array('refund_id' => $refund_id, 'status' => 'confirmed', 'remark' => $remark);
            //         $pointRemark = json_encode($pointRemark);

            //         foreach ($arr_type as $type) {
            //             $point = $type['amount'];

            //             if ($type['refund_type'] == "JoPoint") {
            //                 $cash_value = $type['cash_value'];

            //                 if ($type['amount_type'] == "deduct") {
            //                     $cash_value = "-" . $type['cash_value'];
            //                     // $point      = "-" . $point;
            //                 }

            //                 $point = ($type['amount_type'] == "deduct") ? "-" . $point : $point;

            //                 // echo "<br> - - -> [POINT TRANS][".$type['refund_type']."] [Point: ".$point."] [Cash_value: ".$cash_value."]";

            //                 $pointTrans->refund($trans_id, $point, $cash_value, $pointRemark);
            //             }
            //         }
            //     }
            // }
            //	echo "<pre>";
            //  var_dump($arr_type);
            //  echo "</pre>";
            //  exit;

            return true;
        }
        return true;
    }

    public function postSave($id) {        
        //Save button on finance site
        if(Input::has('new_remark')) {
            $remark = trim(Input::get('new_remark'));

            if ($remark != "") {
                $arr_remark['refund_id']    = $id;
                $arr_remark['remark']       = $remark;
                $arr_remark['created_by']   = Session::get('username');
                $arr_remark['created_date'] = date('Y-m-d H:i:s');

                Refund::insert_refund_remark($arr_remark);
            }
        }
    }

    public function generateCN($id, $cn_no, array $buyer) {
        $cn_prefix          = "CN-";
        $full_cn_no         = $cn_prefix . str_pad($cn_no, 5, "0", STR_PAD_LEFT);
        Refund::update_cn_no($id, $full_cn_no);

        $invoice            = Refund::get_invoice_no($id);
        $refund             = Refund::get_refund($id);
        $refund_status      = $refund->status;
        $refund_products    = Refund::get_refund_products($id, $refund_status);
        $refund_others      = Refund::get_refund_others($id, $refund_status);
        $file_name          = $full_cn_no . ".pdf";
       
        $issuer = array(
                    "issuer_name"       => "Jocom MShopping Sdn. Bhd.",
                    "issuer_address_1"  => "Unit 9-1, Level 9,",
                    "issuer_address_2"  => "Tower 3, Avenue 3, Bangsar South,",
                    "issuer_address_3"  => "No. 8, Jalan Kerinchi,",
                    "issuer_address_4"  => "59200 Kuala Lumpur.",
                    "issuer_tel"        => "Tel: 03-2241 6637 Fax: 03-2242 3837",
                    "issuer_gst"        => "",
                );

        $general = array(
                    "cn_no"                 => $full_cn_no,
                    "cn_date"               => date('d/m/Y'),
                    "invoice_no"            => $invoice->invoice_no,
                    "transaction_id"        => $refund->trans_id,
                    "transaction_gst_rate"  => $refund->gst_rate,
                    "refund_total"          => $refund->amount,
                    "buyer_name"            => isset($buyer['full_name']) ? $buyer['full_name'] : "",
                    "buyer_gst_no"          => isset($buyer['gst_no']) ? $buyer['gst_no'] : "",
                    "attn"                  => isset($buyer['attn']) ? $buyer['attn'] : "",
                    //"buyer_email"           => isset($buyer->email) ? $buyer->email : "",
                    "delivery_address_1"    => ($buyer['address1'] != '' ? $buyer['address1'] . "," : ''),
                    "delivery_address_2"    => ($buyer['address2'] != '' ? $buyer['address2'] . "," : ''),
                    "delivery_address_3"    => ($buyer['postcode'] != '' ? $buyer['postcode'] . " " : '') . ($buyer['city'] != '' ? $buyer['city'] . "," : '') ,
                    "delivery_address_4"    => ($buyer['state'] != '' ? $buyer['state'] . ", " : '') . $buyer['country'] . ".",
                    "reason"                => ($buyer['reason'] != "" ? $buyer['reason'] : "-"),
                );

        if(!file_exists(Config::get('constants.CN_PDF_FILE_PATH') . $file_name))
        {
            include app_path('library/html2pdf/html2pdf.class.php');

            $response = View::make('refund.credit_note')
                    ->with('display_details', $general)
                    ->with('display_issuer', $issuer)
                    // ->with('display_refund', $refund)
                    ->with('display_product', $refund_products)
                    ->with('display_other', $refund_others);

            $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
            $html2pdf->setDefaultFont('arialunicid0');
            $html2pdf->WriteHTML($response);
            $html2pdf->Output("./" . Config::get('constants.CN_PDF_FILE_PATH') . $file_name, 'F');
            
            return Redirect::to('/refund/edit/'.$id);
        }

        if (file_exists(Config::get('constants.CN_PDF_FILE_PATH') . $file_name)) {
            $refund             = Refund::get_refund($id);
            $refund_status      = $refund->status;
            $refund_products    = Refund::get_refund_products($id, $refund_status);
            $refund_others      = Refund::get_refund_others($id, $refund_status);
            $refund_types       = Refund::get_refund_types($id);
            $refund_remarks     = Refund::get_refund_remarks($id);
            $buyer_details      = Refund::get_cn_details($id);
            $cn_no              = Refund::get_cn_no($id);

            if (count($refund_types) > 0) {
                foreach($refund_types as $type) {
                    $arr_type = array();
                    if($type->amount_type == "N") {
                        switch($type->refund_type) {
                            case 'Cash'     : $amount_n_type = Config::get('constants.CURRENCY').' '.$type->amount;
                                break;

                            case 'JoPoint'  : $amount_n_type = $type->amount." points";
                                break;

                            default:
                                $amount_n_type = $type->amount;
                        }
                    }

                    if($type->amount_type == "%") {
                        switch ($type->refund_type) {
                            case 'Coupon': $amount_n_type = $type->amount." ".$type->amount_type;
                                break;
                        }
                    }

                    $arr_type['id']             = $type->id;
                    $arr_type['refund_type']    = $type->refund_type;
                    $arr_type['amount']         = $type->amount;
                    $arr_type['amount_type']    = $type->amount_type;
                    $arr_type['amount_n_type']  = $amount_n_type;
                    $arr_type['redeem_rate']    = $type->redeem_rate;
                    $types[] = $arr_type;
                }
            } 

            return Redirect::to('/refund/edit/'.$id);
        }
    }

    // 24/03/2022 - optimize the codees/remove un-used codes
    public function generatePDF($id, array $buyer) {
        // Create Refund Request Form in pdf format

        $trans_id           = $buyer['trans_id'];
        $file_name          = "RF". "_". $id . "_". $trans_id .".pdf"; //create file name
       
        // If file not exist, create new pdf file and add to public folder
        if(!file_exists(Config::get('constants.ATTACHMENT_PDF') . $file_name))
        {
            include app_path('library/html2pdf/html2pdf.class.php');

            $response = View::make('refund.refund_pdf')
                    ->with('display_details', $buyer);

            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, '');
            $html2pdf->setDefaultFont('arialunicid0');
            $html2pdf->WriteHTML($response);
            $html2pdf->Output("./" . Config::get('constants.ATTACHMENT_PDF') . $file_name, 'F');

            $refundAttachment = DB::table('jocom_refund_supp_docs')
                                    ->insert(
                                        ['refund_id' => $id,
                                        'supp_docs'  => $file_name,
                                        'created_at' => date("Y-m-d H:i:s")]
                                    );

            return Redirect::to('/refund/edit/'.$id);;

        }
        
        // If file exist, update details inside refund form table and update pdf file
        if (file_exists(Config::get('constants.ATTACHMENT_PDF') . $file_name)) 
        {
            include app_path('library/html2pdf/html2pdf.class.php');

            $path_from     = Config::get('constants.ATTACHMENT_PDF');     
            $get_file      = $path_from.$file_name; //get file from path

            unlink($get_file); // remove existing file

            $getFormDetails = DB::table('jocom_refund_form') // get form details 
                                    ->where('refund_id', $id)
                                    ->where('trans_id', $buyer['trans_id'])
                                    ->get();
            $array =  array_shift(json_decode(json_encode($getFormDetails),true));

            $response = View::make('refund.refund_pdf')
                    ->with('display_details', $array);

            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, '');
            $html2pdf->setDefaultFont('arialunicid0');
            $html2pdf->WriteHTML($response);
            $html2pdf->Output("./" . Config::get('constants.ATTACHMENT_PDF') . $file_name, 'F');

            return Redirect::to('/refund/edit/'.$id);
        }
    }

    /**
     * Open file for CN & Remark support Document download
     * @param  [type] $loc [description]
     * @return [type]      [description]
     */
    public function anyFiles($loc = null)
    {
        // Log::info("TEST_FILES_1");
        // Log::info($loc);
        $loc = base64_decode(urldecode($loc));
        // Log::info($loc);
        $loc = Crypt::decrypt($loc);
        // Log::info($loc);

        if (file_exists($loc))
        {
            // Log::info("TEST_FILES_2");
            $paths = explode("/", $loc);
            $ext = strtolower(pathinfo($loc, PATHINFO_EXTENSION));
            header('Cache-Control: public');
            // Log::info($paths);
            // Log::info($ext);
            // Log::info(header('Cache-Control: public'));

            if($ext === 'pdf'){
                header('Content-Type: application/pdf');
            }else{
                $ctype = '';
                switch( $ext ) {
                    case "gif": $ctype = "image/gif"; break;
                    case "png": $ctype = "image/png"; break;
                    case "jpeg":
                    case "jpg": $ctype = "image/jpeg"; break;
                    case "svg": $ctype = "image/svg+xml"; break;
                    default:
                }

                if($ctype){
                    header('Content-type: ' . $ctype);
                }else{
                    header("Content-Description: File Transfer"); 
                    header("Content-Type: application/octet-stream"); 
                }
                
            }
            header('Content-Length: ' . filesize($loc));
            header('Content-Disposition: filename="' . $paths[sizeof($paths) - 1] . '"');
            $file = readfile($loc);
            // Log::info($file);
        }else {
            echo "<script>window.close();</script>";
        }
    }
    
    // 07/04/2022 - Add checkbox/bulk confirmation fo marcho/finance 
    public function anyBulkconfirm() { 
        $bulkConfirm = Input::get('bulk_confirm');

        foreach($bulkConfirm as $key => $val){
            Refund::update_refund_status($val, "confirmed");
        }

        return Redirect::to('/refund');
    } 

    public function anyDelete($id) {  
        $refund         = Refund::get_refund($id);
        $refund_types   = Refund::get_refund_types($id);
        $remark         = "";

        $pointUser      = PointUser::getPoint($refund->buyer_id, PointType::JOPOINT, true);

        if ($pointUser) {
            $pointTrans     = new PointTransaction($pointUser);
            $pointRemark    = array('refund_id' => $id, 'status' => 'deleted', 'remark' => $remark);
            $pointRemark    = json_encode($pointRemark);
          
            if ($refund->status == "confirmed" && count($refund_types) > 0) {       
                foreach ($refund_types as $type) {
                    
                    if ($type->refund_type == "JoPoint" && $type->cash_value > 0) {
                        $reverse_point  = ($type->amount_type == "add") ? "-" . $type->amount : $type->amount;
                        $cash_value     = ($type->amount_type == "add") ? "-" . $type->cash_value : $type->cash_value;
    //                    echo "<br>[TransID: ".$refund->trans_id."] [TYPE: ".Z$type->refund_type."] [Amount_Type: ".$type->amount_type."] [Point: ".$reverse_point."] [Cash_Value: ".$cash_value."]";

                        if(!$pointTrans->refund($refund->trans_id, $reverse_point, $cash_value, $pointRemark)) {
                            Session::flash('message', 'Failed to reverse points!');
                            return Redirect::to('/refund/index');
                        }
                    }
                }
            }
        }

		Refund::update_refund_status($id, "deleted");

        Session::flash('success', 'Successfully deleted.');
        return Redirect::to('/refund/index');
    }

    public function anyImport(Request $request) {
        // $inputs             = Input::all();
        // $tran_id            = Input::get('trans_id');
        // $supp_docs          = Input::file('remark_doc');
        // $dest_path_pdf      = Config::get('constants.ATTACHMENT_PDF');
        // $dest_path_image    = Config::get('constants.ATTACHMENT_IMAGE');
        // $collectAttachment = [];

        // $attachment = 0;
        // if (Input::hasFile('remark_doc')) {
        //     foreach($supp_docs as $doc){
        //         $file_ext               = $doc->getClientOriginalExtension();
        //         $name_attachment        = $doc->getClientOriginalName();
        //         $attachment++;
        //         $file_name_attachment   = 'refund_'. Session::get('user_id').'_'.'attachment'.$attachment. '_'. $date. '.'. $file_ext;
                
        //         array_push($collectAttachment, $file_name_attachment);

        //         if(strtolower($file_ext) == "pdf") { 
        //             $upload_file_attachment   = $doc->move($dest_path_pdf, $file_name_attachment);
        //         }else{
        //             $upload_file_attachment  = $doc->move($dest_path_image, $file_name_attachment);
        //         }
        //     }
        // }

        // // Refund::insert_refund_with_tranID($tran_id);        

        $validator          = Validator::make(Input::all(), Refund::$rules, Refund::$message);
        $file               = Input::file('csv');
        $supp_docs              = Input::file('remark_doc');
        $dest_path          = Config::get('constants.CSV_UPLOAD_PATH');
        $dest_path_pdf      = Config::get('constants.ATTACHMENT_PDF');
        $dest_path_image    = Config::get('constants.ATTACHMENT_IMAGE');
        $date               = date('Ymd_his');
        $file_name          = 'import_'. Session::get('user_id').'_'.'refund'. '_'. $date. '.csv';
        $arr_disc_item  = "";
        $collectAttachment = [];
        array_push($collectAttachment, $file_name);
        if ($validator->passes()) {
            if (Input::hasFile('csv')) {
                $file_ext           = $file->getClientOriginalExtension();
                $name               = $file->getClientOriginalName();
                
                if(strtolower($file_ext) == "csv") { 
                    $upload_file   = $file->move($dest_path, $file_name);
                }
            }
        
            $attachment = 0;
            if (Input::hasFile('remark_doc')) {
                foreach($supp_docs as $doc){
                    $file_ext               = $doc->getClientOriginalExtension();
                    $name_attachment        = $doc->getClientOriginalName();
                    $attachment++;
                    $file_name_attachment   = 'refund_'. Session::get('user_id').'_'.'attachment'.$attachment. '_'. $date. '.'. $file_ext;
                    
                    array_push($collectAttachment, $file_name_attachment);

                    if(strtolower($file_ext) == "pdf") { 
                        $upload_file_attachment   = $doc->move($dest_path_pdf, $file_name_attachment);
                    }else{
                        $upload_file_attachment  = $doc->move($dest_path_image, $file_name_attachment);
                    }
                }
            }

            $arr_columns    = array('customer_name', 'ic_no', 'address', 'postcode', 'hp_no', 'email', 
                    'bank_name', 'bank_account_no', 'transaction_id', 'order_no', 'platform_store',
                    'amount', 'remarks'
            );

            // media/csv/upload/
            $path_from      = Config::get('constants.CSV_UPLOAD_PATH');     //"../public/media/csv/upload/";
            $file_insert    = $path_from.$file_name; //get file from path
            

            if (file_exists($file_insert)) { 
                $fp             = fopen($file_insert, "r");
                $values         = "";

                while($data = fgetcsv($fp)) { //loop get row from fgetcsv($fp)
                    $values = "'1'";
                    $num    = count($data); //total column count from row
                    $col    = 0;

                    $arr_disc_item['module'] = "refund";

                    for ($i = 0; $i < $num; $i++) { //loop from column count
                        $d = $data[$i];

                        if ($d == "customer_name") { //if first loop (first_row) is header, ignore and break loop
                            $total_ignore++;
                            break;
                        }

                        // LATER TRY UNCOMMENT TO SEE THE OUTPUT
                        // if($i == 0) { //if array reach 2 object
                        //     $arr_disc_item['customer_name'] = $data[$i];
                        //     $col++;
                        //     Log::info("TEST 3B");
                        //     Log::info($i);
                        //     Log::info($arr_disc_item['customer_name'] );
                        //     Log::info($col);
                        // }

                        if($i >= 0 && $i < 13) {
                            $values     = $values.","."'".$data[$i]."'";
                            $column     = $arr_columns[$col];
                            $arr_disc_item[$column] = trim($data[$i]); //removes space 
                            $col++;
                        }

                    }

                    $arr_discount_insert[] = $arr_disc_item;
                    if ($col > 0) {
                        if(Refund::insert_refund($arr_disc_item, $file_name, $collectAttachment )) {
                            // $str_log = "\n[INSERT-OK][Line:".$count."] ".$values."\n";
                            $total_insert++;
                        } else {
                            $str_log = "\n[INSERT-NO][Line:".$count."] ".$values."\n";
                        }
                    }

                    $count++;
                }
                // Log::info("test2");
                // Log::info($arr_disc_item);
            }
            $refundId = Session::get('refundId'); // get refund id from Refund::insert_refund()

            // Create pdf for "Refund Request Form"
            // Try $this->genereatePDF. call function from this anyImport() function
            // $cur_cn_no  = Refund::generate_cn_no(); // get counter number
            // $new_cn_no  = $cur_cn_no + 1;
            
            // Collect refund data/info
            $refund_form['refund_id']         = $refundId;
            $refund_form['customer_name']     = $arr_disc_item['customer_name'];
            $refund_form['ic_no']             = $arr_disc_item['ic_no'];
            $refund_form['hp_no']             = $arr_disc_item['hp_no'];
            $refund_form['email']             = $arr_disc_item['email'];
            $refund_form['address']           = $arr_disc_item['address'];
            $refund_form['postcode']          = $arr_disc_item['postcode'];
            $refund_form['bank_name']         = $arr_disc_item['bank_name'];
            $refund_form['bank_account']      = $arr_disc_item['bank_account_no'];
            $refund_form['trans_id']          = $arr_disc_item['transaction_id'];
            $refund_form['order_no']          = $arr_disc_item['order_no'];
            $refund_form['platform_store']    = $arr_disc_item['platform_store'];
            $refund_form['total']             = $arr_disc_item['amount'];
            $refund_form['remarks']           = $arr_disc_item['remarks'];
            $refund_form['supp_docs']         = Input::hasFile('remark_doc') ? "Yes" : "No";
            $arr_refund_formbuyer['request_by']        = Session::get('username');
            $refund_form['date_request']      = date("Y-m-d H:i:s");
            // Log::info($arr_buyer);

            Refund::insert_form_details($refund_form); // insert refund's details 

            // if ($new_cn_no != "") {
                // echo "<br>[CN NO] [Cur: ".$cur_cn_no."] [New: ".$new_cn_no."]";
                // Refund::update_cn_running("cn_no", $new_cn_no);
                $this->generatePDF($refundId, $arr_buyer); // Go to generatePDF() function
            // }

            return Redirect::to('/refund');
        }
    }

    private function replace_carriage_return($file) {
        $str = file_get_contents($file);
        $str = str_replace("\r", "\n", $str);

        file_put_contents($file, $str); 
    }



}
?>