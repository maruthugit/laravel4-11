---- TransactionController.php ----
<?php  ?>

require_once app_path('library/barcodemaster/src/Milon/Barcode/DNS1D.php');
use \Milon\Barcode\DNS1D;


class TransactionController extends BaseController
{

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Default listing for all transaction.
     * @return [type] [description]
     */
    public function anyIndex()
    {
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)
            ->first();
        
        $region_id = $SysAdminRegion->region_id;
        
        if($region_id == 0){
            $regionList = Region::where("status",1)->get();
        }else{
            $regionList = Region::where("status",1)
                    ->where("id",$region_id)
                    ->get();
    }

        return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_listing')->with("region",$regionList);
    }

    public function anyLognew(){

        return View::make('admin.lognew');
   
    }
    public function anyLognewimport(){ 

        $successList = array();
        $unsuccessList = array();

        $file   = Input::file('csv');   

            if($_FILES["csv"]["type"] == "text/csv")
            {
                //convert csv to array
                $csvAsArray = array_map('str_getcsv', file($_FILES['csv']['tmp_name']));

                    for ($index = 1;  $index < count($csvAsArray); $index++){
                        //echo $index;
                        $value2 = $csvAsArray[$index];
                       
                        $value = (int)$value2['0'];
                       
                        $transaction = Transaction::where('id',$value)->select('status')->first();
                      
                        if($transaction->status=='completed')
                        {
                            //get transaction_id                            
                            $data = LogisticTransaction::log_transaction($value);
                            // check if log is success
                            if($data['type']=='success')
                            {
                               array_push($successList,array('transaction_id'=>$value));
                           
                            }
                            else
                            {
                               array_push($unsuccessList,array('transaction_id'=>$value));
                                                         
                            }                           
                        }
                        else
                        {
                            // push to unsuccess list 
                            array_push($unsuccessList,array('transaction_id'=>$value));
                         
                        }
                    }

                $data = array(
                    'suc' => $successList,
                    'unsuc' => $unsuccessList ); 


                return View::make('admin.lognew')->with('result', $data); 
                 
            }
            else
            {
                 return View::make('admin.lognew')->with('message','Uploaded file is invalid file type');
            }
 
    }

    public function anyListing()
    {
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }
       
        $trans = Transaction::select([
            'jocom_transaction.id',
            'jocom_transaction.transaction_date',
            'jocom_transaction.buyer_username',
            'jocom_transaction.total_amount',
            'jocom_transaction.status',
            'jocom_transaction.gst_total',
            'jocom_transaction.external_ref_number',
            'jocom_transaction_coupon.coupon_code',
            'jocom_transaction_coupon.coupon_amount',
            'jocom_transaction_point.amount AS point_amount',
            'jocom_delivery_order.reference_number',
            'jocom_transaction.delivery_state AS delivery_state',
            'jocom_transaction.delivery_area_type',
            'jocom_transaction.invoice_no',
        ])
            ->leftJoin('jocom_transaction_coupon', 'jocom_transaction.id', '=', 'jocom_transaction_coupon.transaction_id')
            ->leftJoin('jocom_transaction_point', 'jocom_transaction.id', '=', 'jocom_transaction_point.transaction_id')
            // ->leftjoin('jocom_elevenstreet_order', 'jocom_transaction.id', '=', 'jocom_elevenstreet_order.transaction_id')
            ->leftJoin('jocom_delivery_order', 'jocom_transaction.id', '=', 'jocom_delivery_order.id');

        $transactionFrom = Input::get('transaction_from');
        $transactionTo   = Input::get('transaction_to');
        $username        = Input::get('username');
        $status          = Input::get('status');
        $amountFrom      = Input::get('amount_from');
        $amountTo        = Input::get('amount_to');
        $productName     = Input::get('product_name');
        $productSku      = Input::get('product_sku');
        $coupon          = Input::get('coupon');
        $agent           = Input::get('agent');
        $payment_option  = Input::get('payment_option');
        $reference_number  = Input::get('reference_number');
        $invoice_number  = Input::get('invoice_number');
        $region_id  = Input::get('region_id');
        
        $delivery_address_type = Input::get('delivery_address_type');
        
        if($delivery_address_type){
            $trans = $trans->where('jocom_transaction.delivery_area_type', '=', $delivery_address_type);
        }

        if (isset($transactionFrom) &&  ! empty($transactionFrom)) {
            $trans = $trans->where('jocom_transaction.transaction_date', '>=', $transactionFrom." 00:00:00");
        }

        if (isset($transactionTo) &&  ! empty($transactionTo)) {
            $trans = $trans->where('jocom_transaction.transaction_date', '<=', $transactionTo." 23:59:59");
        }

        if (isset($username) &&  ! empty($username)) {
            $trans = $trans->where('jocom_transaction.buyer_username', 'like', "%{$username}%");
        }

        if (isset($status) &&  ! empty($status) && $status != 'any') {
            $trans = $trans->where('jocom_transaction.status', '=', $status);
        }

        if(count($stateName) > 0){
            $trans = $trans->whereIn('jocom_transaction.delivery_state', $stateName);
        }

        if (isset($amountFrom) &&  ! empty($amountFrom)) {
            $trans = $trans->where('total_amount', '>=', $amountFrom);
        }

        if (isset($amountTo) &&  ! empty($amountTo)) {
            $trans = $trans->where('total_amount', '<=', $amountTo);
        }
        
        if (isset($invoice_number) &&  ! empty($invoice_number)) {
            $trans = $trans->where('invoice_no', '=', $invoice_number);
        }

        if (isset($productName) &&  ! empty($productName)) {
            $productNames = implode('|', array_map('trim', explode(',', $productName)));
            $trans        = $trans->leftJoin('jocom_transaction_details', 'jocom_transaction.id', '=', 'jocom_transaction_details.transaction_id');
            $trans        = $trans->where('jocom_transaction_details.price_label', 'REGEXP', $productNames);
        }

        if (isset($productSku) &&  ! empty($productSku)) {
            $productSkus = implode('|', array_map('trim', explode(',', $productSku)));
            $trans       = $trans->leftJoin('jocom_transaction_details', 'jocom_transaction.id', '=', 'jocom_transaction_details.transaction_id');
            $trans       = $trans->where('jocom_transaction_details.sku', 'REGEXP', $productSkus);
        }

        if (isset($coupon) &&  ! empty($coupon)) {
            $trans = $trans->where('coupon_code', '=', $coupon);
        }
        
        if (isset($region_id) &&  ! empty($region_id) &&  $region_id != '') {
            $stateList = State::getStateByRegion($region_id);
            $regionState = array();
            
            foreach ($stateList as $key => $value) {
                array_push($regionState, $value->name);
            }
            $trans = $trans->whereIn('delivery_state', $regionState);
        }

        if (isset($agent) && ! empty($agent)) {
            $trans = $trans->leftJoin('jocom_agents', 'jocom_transaction.agent_id', '=', 'jocom_agents.id');
            $trans = $trans->whereRaw('(jocom_agents.agent_code = ? OR jocom_agents.id = ?)', [$agent, $agent]);
        }
        
        if (Input::get('payment_option')== 'Cash') {
            $trans = $trans->leftjoin('jocom_molpay_transaction AS mol', 'mol.transaction_id', '=','jocom_transaction.id')
                ->leftjoin('jocom_paypal_transaction AS paypal', 'paypal.transaction_id', '=','jocom_transaction.id')
                ->leftjoin('jocom_mpay_transaction AS mpay', 'mpay.transaction_id', '=','jocom_transaction.id')
                ->leftjoin('jocom_revpay_transaction AS revpay', 'revpay.transaction_id', '=','jocom_transaction.id')
                ->whereNull('mol.transaction_id')
                ->whereNull('paypal.transaction_id')
                ->whereNull('mpay.transaction_id')
                ->whereNull('revpay.transaction_id')
                ->where('jocom_transaction.status','=','completed');

        }
        if (Input::get('payment_option')== 'Revpay') {
            $trans = $trans->join('jocom_revpay_transaction', 'jocom_revpay_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_revpay_transaction.payment_status','=','00');
        }
        if (Input::get('payment_option')== 'MOLPay') {
            $trans = $trans->join('jocom_molpay_transaction', 'jocom_molpay_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_molpay_transaction.payment_status','=','00');
        }
        if (Input::get('payment_option')== 'mPAY') {
            $trans = $trans->join('jocom_mpay_transaction', 'jocom_mpay_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_mpay_transaction.payment_status','=','0');
        }
        if (Input::get('payment_option')== 'Boost') {
            $trans = $trans->join('jocom_boost_transaction', 'jocom_boost_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_boost_transaction.transaction_status','=','completed');
        }
        if (Input::get('payment_option')== 'GrabPay') {
            $trans = $trans->join('jocom_grabpay_transaction', 'jocom_grabpay_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_grabpay_transaction.status','=','success');
        }
        if (Input::get('payment_option')== 'FavePay') {
            $trans = $trans->join('jocom_favepay_transaction', 'jocom_favepay_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_favepay_transaction.status','=','successful');
        }
        if (Input::get('payment_option')== 'PayPal') {
            $trans = $trans->join('jocom_paypal_transaction', 'jocom_paypal_transaction.transaction_id', '=', 'jocom_transaction.id')
                            ->where('jocom_paypal_transaction.payment_status','=','completed');
        }
        if (isset($reference_number) &&  ! empty($reference_number)) {
            $trans = $trans->where('jocom_delivery_order.reference_number', '=', $reference_number);
        }

        $trans = $trans->groupBy('jocom_transaction.id');

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="transaction/edit/{{$id}}"><i class="fa fa-pencil"></i></a>';



        // if(Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
        // {
        //     $actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_transaction({{$id;}});"><i class="fa fa-remove"></i></a>';
        // }

        return Datatables::of($trans)
            ->add_column('total', '{{number_format(abs($total_amount - $coupon_amount + $gst_total - $point_amount), 2)}}')
            ->add_column('paymentgateway', function($row){      // Added by Maruthu
                        return Transaction::getPaymentGateway($row->id,$row->status);
                })
            ->add_column('order_number', function($row){      // Added by Maruthu
                switch ($row->buyer_username) {
                    case '11Street':

                        $OrderInfo = DB::table('jocom_elevenstreet_order AS JEO' )
                            ->select('JEO.order_number')
                            ->where("JEO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        // return $OrderInfo->order_number;
                        break;
                        
                    case 'prestomall':

                        $OrderInfo = DB::table('jocom_elevenstreet_order AS JEO' )
                            ->select('JEO.order_number')
                            ->where("JEO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        // return $OrderInfo->order_number;
                        break;
                        
                    case 'lazada':
                        $OrderInfo = DB::table('jocom_lazada_order AS JLZD' )
                            ->select('JLZD.order_number')
                            ->where("JLZD.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        break;
                    
                    case 'shopee':
                        $OrderInfo = DB::table('jocom_shopee_order AS JSPE' )
                            ->select('JSPE.ordersn')
                            ->where("JSPE.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $row->external_ref_number;
                        break;
                    
                    case 'Qoo10':
                        $OrderInfo = DB::table('jocom_qoo10_order AS JQUO' )
                            ->select('JQUO.packNo')
                            ->where("JQUO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->packNo != '' ? $OrderInfo->packNo : $row->external_ref_number;
                        break;
                    
                    case 'pgmall':
                        $OrderInfo = DB::table('jocom_pgmall_order AS JPO' )
                            ->select('JPO.ordersn')
                            ->where("JPO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $row->external_ref_number;
                        break;

                    default:
                        
                        return  $row->external_ref_number;
                        break;
                }
                })
            ->edit_column('status', '{{ucwords($status)}}')
            ->add_column('Action', $actionBar)
            ->make(true);
    }

    public function anyCustomersajax()
    {
        $customers = DB::table('jocom_user')
            ->select('id', 'username', 'full_name', 'email');

        return Datatables::of($customers)
            ->add_column('Action', '<a id="selectCust" class="btn btn-primary" title="" href="../customer/{{$id}}">Select</a>')
            ->make();
    }

    public function anyAjaxcustomer()
    {
        return View::make('admin.ajaxcustomer');
    }

    public function anyProductsajax($type)
    {
        if($type == 2){
          
            $products = Product::select([
            'jocom_products.id',
            'jocom_products.qrcode',
            'jocom_products.sku',
            'jocom_seller.company_name',
            'jocom_products.name',
            'jocom_products_category.category_name',
            'jocom_products.status',
            // 'jocom_product_price.price',
            // 'jocom_product_price.price_promo'
            ])
        // ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
            ->where('jocom_products.status', '!=', '2')
            ->where('jocom_products.is_foreign_market', '=', 1);
            
        }else{
            
            $products = Product::select([
            'jocom_products.id',
            'jocom_products.qrcode',
            'jocom_products.sku',
            'jocom_seller.company_name',
            'jocom_products.name',
            'jocom_products_category.category_name',
            'jocom_products.status',
            // 'jocom_product_price.price',
            // 'jocom_product_price.price_promo'
            ])
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
            ->where('jocom_products.status', '!=', '2')->where('jocom_products.is_foreign_market', '<>', 1);
            
        }
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
                $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
        }
        
        // ->where('jocom_product_price.status', '=', 1)
        // ->where('jocom_product_price.default', '=', '1');
        return Datatables::of($products)
        // ->edit_column('price', '{{number_format($price, 2)}}')
        // ->edit_column('price_promo', '{{number_format($price_promo, 2)}}')
            ->edit_column('status', function($row)
            {
                switch ($row->status)
                {
                    case 1:
                        return '<span class="label label-success">Active</span>';
                        break;
                    case 2:
                        return '<span class="label label-danger">Deleted/Archive</span>';
                        break;
                    default:
                        return '<span class="label label-warning">Inactive</span>';
                        break;
                }
            })
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/transaction/ajaxprice/{{$id}}">Normal</a> <a id="selectItem" class="btn btn-success" title="" href="/transaction/ajaxprice/S{{$id}}">Special</a>')
            ->make();
    }

    public function anyAjaxproduct($type)
    {
        return View::make('admin.ajaxproduct')->with("type",$type);;
    }

    public function anyPricesajax($id)
    {
        if (substr($id, 0, 1) == 'S')
        {
            $tempID = substr($id, 1);

            $product_prices = DB::table('jocom_sp_product_price')
                ->select(
                    DB::raw("
                        concat('S', jocom_sp_product_price.id) AS id
                    "),
                    'jocom_product_price.label',
                    DB::raw("
                        (CASE
                            WHEN jocom_sp_product_price.disc_amount > 0 AND jocom_sp_product_price.disc_type = '%' THEN (jocom_sp_product_price.price * (1-jocom_sp_product_price.disc_amount/100))
                            WHEN jocom_sp_product_price.disc_amount > 0 AND jocom_sp_product_price.disc_type = 'N' THEN (jocom_sp_product_price.price - jocom_sp_product_price.disc_amount)
                            ELSE jocom_sp_product_price.price
                        END) AS price
                    "),
                    'jocom_sp_product_price.price_promo',
                    'jocom_products.gst',
                    'jocom_products.gst_value'
                    )
                ->leftJoin('jocom_products', 'jocom_sp_product_price.product_id', '=', 'jocom_products.id')
                ->leftjoin('jocom_product_price', 'jocom_product_price.id', '=', 'jocom_sp_product_price.label_id')
                ->where('jocom_sp_product_price.product_id', '=', $tempID)
                ->where('jocom_product_price.status', '=', 1);
        }
        else
        {
            $product_prices = DB::table('jocom_product_price')
                ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo', 'jocom_product_price.foreign_price', 'jocom_product_price.foreign_price_promo', 'jocom_products.is_foreign_market')
                ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
                ->where('jocom_product_price.product_id', '=', $id)
                ->where('jocom_product_price.status', '=', 1);
        }
        

        return Datatables::of($product_prices)
            ->edit_column('price', '
                                    @if ($gst == 2 && $price > 0)
                                        <?php
                                            $gst_price  = 1 + $gst_value / 100;
                                            $actual_price = $price * $gst_price;
                                        ?>
                                        {{ number_format($actual_price, 2) }}
                                    @else
                                        {{ number_format($price, 2) }}
                                    @endif
                                    ')
            ->edit_column('price_promo', '
                                    @if ($gst == 2 && $price_promo > 0)
                                        <?php
                                            $gst_price  = 1 + $gst_value / 100;
                                            $promotion_price = $price_promo * $gst_price;
                                        ?>
                                        {{ number_format($promotion_price, 2) }}
                                    @else
                                        {{ number_format($price_promo, 2) }}
                                    @endif
                                    ')
            ->add_column('price', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price;
                    }
                
                    return ;
            })
            ->add_column('price_promo', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price_promo * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price_promo;
                    }
            })
            ->add_column('foreign_price', function($product_prices){
                
                    return $product_prices->foreign_price;
            })
            ->add_column('foreign_price_promo', function($product_prices){
                    return $product_prices->foreign_price_promo;

         })
            // ->edit_column('gst', '{{$gst}}')
            // ->edit_column('gst_value', '{{$gst_value}}')
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
            ->make();
    }

    public function anyAjaxprice($id)
    {
        $tempID = $id;

        if (substr($id, 0, 1) == 'S')
        {
            $tempID = substr($id, 1);
        }

        $product = DB::table('jocom_products')
            ->select('name', 'sku', 'qrcode')
            ->where('id', '=', $tempID)->first();

        return View::make('admin.ajaxprice')->with([
            'id'     => $id,
            'name'   => addslashes($product->name),
            'sku'    => $product->sku,
            'qrcode' => $product->qrcode,
        ]);
    }

    /**
     * Add new transaction
     * @return [type] [description]
     */
    public function anyAdd($type = "", $order_id = "")
    {
        
        try{
            
        
        if (Input::has('add_check')) {
            $validator = Validator::make(Input::all(), Transaction::$rules, Transaction::$message);

            Input::flash();

            if ($validator->passes()) {
                $rs = Transaction::add_transaction();

                Input::flush();

                if ($rs == true) {
                    // $insert_audit = General::audit_trail('TransactionController.php', 'Add()', 'Add Transaction', Session::get('username'), 'CMS');
                    return Redirect::to('transaction/edit/'.$rs)->with('success', 'Transaction(ID: '.$rs.') added successfully');
                } else {
                    return Redirect::to('transaction')->with('message', 'Error adding new transaction.');
                }
            } else {
                return Redirect::to('transaction/add')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
            }
        }else {
            
            $countries = Delivery::getDeliveryCountries();
            // $addCust   = Customer::all()->lists('username', 'full_name');
            $addCust = DB::table('jocom_user')
                                ->select('username', 'full_name')
                                ->get();
            $orderMappedInfo = array();
            $ProductCollection = array();

            // For 11Street's order transfer transaction
            if($type !== "" && $order_id !== ""){
                
                
                switch ($type) {
                    case 1: // ELEVEN STREET ORDER
                    //
                // Get 11Street order information
                $OrderData = ElevenStreetOrder::find($order_id);
                $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID($order_id);
                $transactionDate = substr($OrderData->order_number,0,4)."-".substr($OrderData->order_number,4,2)."-".substr($OrderData->order_number,6,2);
                
                
                if(count($OrderDataDetails) > 0 ){
                    //$OrderDataDetails
                    foreach ($OrderDataDetails as $key => $value) {
                        $APIData = json_decode($value->api_result_return, true);
                        
                       
                        $receiverName = $APIData['rcvrNm'];
                        $receiverMobileNumber = !empty($APIData['rcvrTlphn']) ? $APIData['rcvrTlphn'] : $APIData['ordTlphnNo'];
                        $AddressInfo = array(
                            "street_address_1" => $APIData['rcvrDtlsAddr'],
                            "street_address_2" => $APIData['rcvrBaseAddr'],
                            "postcode" => $APIData['rcvrMailNo'],
                            "country" => "458",  // Malaysia Region only. 
                            // "state" => "458004",
                            // "city" => ""
                        );
                        
                        // Puzzle for 11Street API 
                        
                        $countryid = "458"; // Malaysia Region only.
                        $statelist = Delivery::getStateList($countryid);

                        $postcode=$AddressInfo['postcode'];
                        $postcode_1 ="";
                        $street_address_1_1 = "";
                        $street_address_2_1 = "";

                        if($postcode=='' || $postcode==0){
                            $pcode=preg_match("/(\d\d\d\d\d)/", $AddressInfo['street_address_2'], $matches);
                            $postcode   = $matches[0];
                            $postcode_1 = $matches[0];

                            $pieces = explode($postcode, $AddressInfo['street_address_2']);
                            $street_address_1_1 = $pieces[0];
                            $street_address_2_1 = $postcode.' '.$pieces[1];
                        }

                        $getpostcode = array();
                        $st_code = "";
                        $post_office = "";
                        $statename   = "";
                        $status = 0;
                        $getpostcode = Transaction::getXMLpostcode($postcode);

                        if($getpostcode['status']==1){
                            $st_code = $getpostcode['st_code'];
                            $post_office = $getpostcode['post_office'];
                            $statename   = $getpostcode['state'];        
			    $status      = $getpostcode['status']; 

                        }
                        
                        $stateinfo = Transaction::getStateID($statename);
                        $stateid = $stateinfo->id;

                        
                        $cityid="";
                        $cityinfo = Delivery::getCityList($stateid);

                        $city_info = Transaction::getCityID($post_office,$stateid);
                        $cityid   = $city_info->id;

                        $AddressInfo_1 = array("state"              => $stateid,
                                               "city"               => $cityid,
                                               "postcode_1"         => $postcode_1,
                                               "street_address_1_1" => $street_address_1_1,
                                               "street_address_2_1" => $street_address_2_1,
                                               "status"             => $status,  


                            );
                    if(count($APIData['sellerPrdCd']) > 0) {
                        $ProductInformation = Product::findProductInfoByQRCODE($APIData['sellerPrdCd']);
                        
                        // CHECK DEFINE LABEL PRICE CODE //
                                    $optionName = $APIData['slctPrdOptNm'];
                                 

                                    if(count($optionName) > 0 ){
                                         // TEST CASE 10752
                                        //$optionName = '[10753]'.' '.$optionName;
                                        
                                        
                                        if(isset($APIData['partCode']) && $APIData['partCode'] != '' && count($APIData['partCode']) > 0 ){
                                            // TAKE DEFINED LABEL ID //
                                            $selectedPriceOptionID = $APIData['partCode'];
                                            $PriceOption = Price::find($selectedPriceOptionID);
                    
                                            if(count($PriceOption)>0){  
                                                $ProductPriceInformationID = $selectedPriceOptionID;  
                                                $ProductPriceInformationLabel = $PriceOption->label;
                                                $ProductPriceInformationActualPrice = $PriceOption->price;
                                                $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                            }else{
                                                $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                $ProductPriceInformationLabel = $PriceOption->label;
                                                $ProductPriceInformationActualPrice = $PriceOption->price;
                                                $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                            }
                                        
                                        }else{

                                            if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                                // TAKE DEFINED LABEL ID //
                                                 
                                                $selectedPriceOptionID = substr($optionName,strpos($string,"[") + 1,strpos($optionName,"]") -1);
                                                 
                                                $PriceOption = Price::find($selectedPriceOptionID);
                                             
                                                if(count($PriceOption)>0){
                                                    
                                                    $ProductPriceInformationID = $selectedPriceOptionID;  
                                                    $ProductPriceInformationLabel = $PriceOption->label;
                                                    $ProductPriceInformationActualPrice = $PriceOption->price;
                                                    $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                                     //echo "<pre>";
                                                       // print_r($ProductPriceInformationID);die();
                                                 //   echo "</pre>";
                                                }else{
                                                    $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                    $ProductPriceInformationLabel = $ProductInformation->label;
                                                    $ProductPriceInformationActualPrice = $ProductInformation->price;
                                                    $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                                }
                                            }else{
                                                $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                $ProductPriceInformationLabel = $ProductInformation->label;
                                                $ProductPriceInformationActualPrice = $ProductInformation->price;
                                                $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                            }
                                        
                                        }

                                    }else{
                                        $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                        $ProductPriceInformationLabel = $ProductInformation->label;
                                        $ProductPriceInformationActualPrice = $ProductInformation->price;
                                        $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    }
                                    // CHECK DEFINE LABEL PRICE CODE //
                        
                        if ($ProductInformation->gst == 2 && $ProductPriceInformationActualPrice > 0){
                            $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $actual_price = $ProductPriceInformationActualPrice * $gst_price;
                        }else{
                            $actual_price = $ProductPriceInformationActualPrice;
                        }
                        
                        if ($ProductInformation->gst == 2 && $ProductPriceInformationPromoPrice > 0){ 
                            $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $promotion_price = $ProductPriceInformationPromoPrice * $gst_price;
                        }else{ 
                            $promotion_price = number_format($ProductPriceInformationPromoPrice, 2) ;
                        }
                                 
                         
                        if(count($ProductInformation) > 0 ){
                            $ProductSubCollection = array(
                                "product_id" =>  $ProductInformation->id,
                                "product_name" =>  $ProductInformation->name,
                                "qrcode" =>  $ProductInformation->qrcode,
                                "quantity" =>  $APIData['ordQty'],
                                "option_price_id" =>  $ProductPriceInformationID,
                                "sku" =>  $ProductInformation->sku,
                                "label" =>  $ProductPriceInformationLabel,
                                "actual_price" =>   number_format($actual_price, 2),
                                "promotion_price" =>  $promotion_price,
                                "referral_fees" =>  $ProductInformation->p_referral_fees,
                                "p_referral_fees_type" => $ProductInformation->p_referral_fees_type,
                                "Sub-total" => $ProductInformation->product_name
                            );
                            array_push($ProductCollection, $ProductSubCollection);
                        }
                      }else{
                            
                        }
                    }
                   
                }
                
                // Create collection 
                $orderMappedInfo = array(
                    "id" => $OrderData->id,
                            "transfer_type" => 1,
                    "order_number" => $OrderData->order_number,
                    "transaction_date" => $transactionDate,
                    "customer_order" => $OrderData->customer_name,
                    "product"=> $ProductCollection,
                    "delivery_address" => $AddressInfo,
                    "delivery_address_1" => $AddressInfo_1,
                    "receiver_name" => $receiverName,
                    "mobile_no" => $receiverMobileNumber,
                    "statelist" => $statelist,
                    "citylist" => $cityinfo,
                    "special_message" => "Transaction transfer from Prestomall ( Order Number : ".$OrderData->order_number." )" 
                   
                ); 
                
                        break;
                    case 2: // LAZADA STREET ORDER
                        
                        // Get 11Street order information
                        $OrderData = LazadaOrder::find($order_id);
                        $OrderDataDetails = LazadaOrderDetails::getByOrderID($order_id);
                        $transactionDate = date_format($date=date_create($OrderData->order_datetime),"Y-m-d"); ;


                        if(count($OrderDataDetails) > 0 ){
                            //$OrderDataDetails
                            foreach ($OrderDataDetails as $key => $value) {
                                $APIOrderData = json_decode($OrderData->api_data_return, true);
                                $APIOrderDetailData = json_decode($value->order_items_details, true);
                                
                                // OLD FORMAT //
                                // $receiverName = $APIOrderData['AddressShipping']['FirstName']." ".$APIOrderData['AddressShipping']['LastName'];
                                // $receiverMobileNumber = $APIOrderData['AddressShipping']['Phone'];
                                // $AddressInfo = array(
                                //     "street_address_1" => $APIOrderData['AddressShipping']['Address1'],
                                //     "street_address_2" => $APIOrderData['AddressShipping']['Address2'],
                                //     "postcode" => $APIOrderData['AddressShipping']['PostCode'],
                                //     "country" => "458",  // Malaysia Region only. 
                                //     // "state" => "458004",
                                //     // "city" => ""
                                // );
                                
                                // echo '<pre>';
                                // echo $APIOrderData['shipping_fee'];
                                // print_r($APIOrderDetailData);
                                // echo '</pre>';
                                
                                // NEW FORMAT //
                                $receiverName = $APIOrderData['address_shipping']['first_name']." ".$APIOrderData['address_shipping']['last_name'];
                                $receivermobilenumber = $APIOrderData['address_shipping']['phone'];
                                // echo $receivermobilenumber;
                                $AddressInfo = array(
                                    "street_address_1" => $APIOrderData['address_shipping']['address1'],
                                    "street_address_2" => $APIOrderData['address_shipping']['address2'],
                                    "postcode" => $APIOrderData['address_shipping']['post_code'],
                                    "country" => "458",  // Malaysia Region only. 
                                    // "state" => "458004",
                                    // "city" => ""
                                );

                                // Puzzle for 11Street API 

                                $countryid = "458"; // Malaysia Region only.
                                $statelist = Delivery::getStateList($countryid);

                                $postcode=$AddressInfo['postcode'];
                                $postcode_1 ="";
                                $street_address_1_1 = "";
                                $street_address_2_1 = "";

                                if($postcode=='' || $postcode==0){
                                    $pcode=preg_match("/(\d\d\d\d\d)/", $AddressInfo['street_address_2'], $matches);
                                    $postcode   = $matches[0];
                                    $postcode_1 = $matches[0];

                                    $pieces = explode($postcode, $AddressInfo['street_address_2']);
                                    $street_address_1_1 = $pieces[0];
                                    $street_address_2_1 = $postcode.' '.$pieces[1];
            }

                                $getpostcode = array();
                                $st_code = "";
                                $post_office = "";
                                $statename   = "";
                                $status = 0;
                                $getpostcode = Transaction::getXMLpostcode($postcode);
                                

                                if($getpostcode['status']==1){
                                    $st_code = $getpostcode['st_code'];
                                    $post_office = $getpostcode['post_office'];
                                    $statename   = $getpostcode['state'];   
                                    $status      = $getpostcode['status'];        

                                } 

                                $stateinfo = Transaction::getStateID($statename);
                                $stateid = $stateinfo->id;


                                $cityid="";
                                $cityinfo = Delivery::getCityList($stateid);

                                $city_info = Transaction::getCityID($post_office,$stateid);
                                $cityid   = $city_info->id;

                                $AddressInfo_1 = array("state"              => $stateid,
                                                       "city"               => $cityid,
                                                       "postcode_1"         => $postcode_1,
                                                       "street_address_1_1" => $street_address_1_1,
                                                       "street_address_2_1" => $street_address_2_1,
                                                       "status"             => $status,  


                                    );
                                    // echo $APIOrderDetailData['paid_price'];
                                if(count($APIOrderDetailData['sku']) > 0) {
                                    $ProductInformation = Product::findProductInfoByQRCODE($APIOrderDetailData['sku']);

                                    if ($ProductInformation->gst == 2 && $ProductInformation->price > 0){
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                                        $actual_price = $ProductInformation->price * $gst_price;
                                    }else{
                                        $actual_price = $ProductInformation->price;
                                    }

                                    if ($ProductInformation->gst == 2 && $ProductInformation->price_promo > 0){ 
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                                        // $promotion_price = $ProductInformation->price_promo * $gst_price;
                                        $promotion_price = number_format($APIOrderDetailData['paid_price'], 2) ;
                                    }else{ 
                                        // $promotion_price = number_format($ProductInformation->price_promo, 2) ;
                                        $promotion_price = number_format($APIOrderDetailData['paid_price'], 2) ;
                                    }



                                    if(count($ProductInformation) > 0 ){
                                        $ProductSubCollection = array(
                                            "product_id" =>  $ProductInformation->id,
                                            "product_name" =>  $ProductInformation->name,
                                            "qrcode" =>  $ProductInformation->qrcode,
                                            "quantity" =>  1, //$APIData['ordQty'],
                                            "option_price_id" =>  $ProductInformation->ProductPriceID,
                                            "sku" =>  $ProductInformation->sku,
                                            "label" =>  $ProductInformation->label,
                                            "actual_price" =>   number_format($actual_price, 2),
                                            "promotion_price" =>  $promotion_price,
                                            "referral_fees" =>  $ProductInformation->p_referral_fees,
                                            "p_referral_fees_type" => $ProductInformation->p_referral_fees_type,
                                            "Sub-total" => $ProductInformation->product_name
                                        );
                                        array_push($ProductCollection, $ProductSubCollection);
                                    }
                                }else{

                                }
                            }

                        }
                       
                                
                        // Create collection 
                        $orderMappedInfo = array(
                            "id" => $OrderData->id,
                            "transfer_type" => 2,
                            "order_number" => $OrderData->order_number,
                            "transaction_date" => $transactionDate,
                            "customer_order" => $OrderData->customer_name,
                            "product"=> $ProductCollection,
                            "delivery_address" => $AddressInfo,
                            "delivery_address_1" => $AddressInfo_1,
                            "receiver_name" => $receiverName,
                            "mobile_no" => $receivermobilenumber,
                            "shipping_fee" => $APIOrderData['shipping_fee'],
                            "statelist" => $statelist,
                            "citylist" => $cityinfo,
                            "special_message" => "Transaction transfer from Lazada ( Order Number : ".$OrderData->order_number." )" 

                        );
                                // echo '<pre>';
                                // // echo $postcode;
                                // print_r($orderMappedInfo);
                                // echo '</pre>';
                                
                                // die('In');
                        break;
                        
                    case 3:  // Qoo10 ORDER
                    //
                        // Get Qoo10 order information
                        $OrderData = QootenOrder::find($order_id);
                    
                        $OrderDataDetails = QootenOrderDetails::getByOrderID($order_id);
                        $transactionDate = date_format($date=date_create($OrderData->order_datetime),"Y-m-d");

                        if(count($OrderDataDetails) > 0 ){
                            //$OrderDataDetails
                            foreach ($OrderDataDetails as $key => $value) {
                                $APIData = json_decode($value->api_result_return, true);
                              
                                $receiverName = $APIData['receiver'];
                                $receiverMobileNumber = $APIData['receiverMobile'];
                                $AddressInfo = array(
                                    "street_address_1" => $APIData['Addr2'],
                                    "street_address_2" => $APIData['Addr1'],
                                    "postcode" => $APIData['zipCode'],
                                    "country" => "458",  // Malaysia Region only. 
                                    // "state" => "458004",
                                    // "city" => ""
                                );

                                // Puzzle for Qoo10 API 

                                $countryid = "458"; // Malaysia Region only.
                                $statelist = Delivery::getStateList($countryid);

                                $postcode=$AddressInfo['postcode'];
                                $postcode_1 ="";
                                $street_address_1_1 = "";
                                $street_address_2_1 = "";

                                if($postcode=='' || $postcode==0){
                                    $pcode=preg_match("/(\d\d\d\d\d)/", $AddressInfo['street_address_2'], $matches);
                                    $postcode   = $matches[0];
                                    $postcode_1 = $matches[0];

                                    $pieces = explode($postcode, $AddressInfo['street_address_2']);
                                    $street_address_1_1 = $pieces[0];
                                    $street_address_2_1 = $postcode.' '.$pieces[1];
                                }

                                $getpostcode = array();
                                $st_code = "";
                                $post_office = "";
                                $statename   = "";
                                $status = 0;
                                $getpostcode = Transaction::getXMLpostcode($postcode);

                                if($getpostcode['status']==1){
                                    $st_code = $getpostcode['st_code'];
                                    $post_office = $getpostcode['post_office'];
                                    $statename   = $getpostcode['state'];   
                                    $status      = $getpostcode['status'];        

                                } 

                                $stateinfo = Transaction::getStateID($statename);
                                $stateid = $stateinfo->id;


                                $cityid="";
                                $cityinfo = Delivery::getCityList($stateid);

                                $city_info = Transaction::getCityID($post_office,$stateid);
                                $cityid   = $city_info->id;

                                $AddressInfo_1 = array("state"              => $stateid,
                                                       "city"               => $cityid,
                                                       "postcode_1"         => $postcode_1,
                                                       "street_address_1_1" => $street_address_1_1,
                                                       "street_address_2_1" => $street_address_2_1,
                                                       "status"             => $status,  


                                    );
                                if(count($APIData['sellerItemCode']) > 0) {
                                    $ProductInformation = Product::findProductInfoByQRCODE($APIData['sellerItemCode']);

                                    // CHECK DEFINE LABEL PRICE CODE //
                                    $optionName = $APIData['option'];
                                 

                                    if(count($optionName) > 0 ){
                                         // TEST CASE 10752
                                        //$optionName = '[10753]'.' '.$optionName;

                                        if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                            // TAKE DEFINED LABEL ID //
                                             
                                            $selectedPriceOptionID = substr($optionName,strpos($string,"[") + 1,strpos($optionName,"]") -1);
                                             
                                            $PriceOption = Price::find($selectedPriceOptionID);
                                         
                                            if(count($PriceOption)>0){
                                                
                                                $ProductPriceInformationID = $selectedPriceOptionID;  
                                                $ProductPriceInformationLabel = $PriceOption->label;
                                                $ProductPriceInformationActualPrice = $PriceOption->price;
                                                $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                                 //echo "<pre>";
                                                   // print_r($ProductPriceInformationID);die();
                                             //   echo "</pre>";
                                            }else{
                                                $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                $ProductPriceInformationLabel = $ProductInformation->label;
                                                $ProductPriceInformationActualPrice = $ProductInformation->price;
                                                $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                            }
                                        }else{
                                            $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                            $ProductPriceInformationLabel = $ProductInformation->label;
                                            $ProductPriceInformationActualPrice = $ProductInformation->price;
                                            $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                        }

                                    }else{
                                        $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                        $ProductPriceInformationLabel = $ProductInformation->label;
                                        $ProductPriceInformationActualPrice = $ProductInformation->price;
                                        $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    }
                                    // CHECK DEFINE LABEL PRICE CODE //
                        
                        if ($ProductInformation->gst == 2 && $ProductPriceInformationActualPrice > 0){
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $actual_price = $ProductPriceInformationActualPrice * $gst_price;
                                    }else{
                            $actual_price = $ProductPriceInformationActualPrice;
                                    }

                        if ($ProductInformation->gst == 2 && $ProductPriceInformationPromoPrice > 0){ 
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $promotion_price = $ProductPriceInformationPromoPrice * $gst_price;
                                    }else{ 
                            $promotion_price = number_format($ProductPriceInformationPromoPrice, 2) ;
                                    }


                                    if(count($ProductInformation) > 0 ){
                                        $ProductSubCollection = array(
                                            "product_id" =>  $ProductInformation->id,
                                            "product_name" =>  $ProductInformation->name,
                                            "qrcode" =>  $ProductInformation->qrcode,
                                            "quantity" =>  $APIData['orderQty'],
                                "option_price_id" =>  $ProductPriceInformationID,
                                            "sku" =>  $ProductInformation->sku,
                                "label" =>  $ProductPriceInformationLabel,
                                            "actual_price" =>   number_format($actual_price, 2),
                                            "promotion_price" =>  $promotion_price,
                                            "referral_fees" =>  $ProductInformation->p_referral_fees,
                                            "p_referral_fees_type" => $ProductInformation->p_referral_fees_type,
                                            "Sub-total" => $ProductInformation->product_name
                                        );
                                        array_push($ProductCollection, $ProductSubCollection);
                                    }
                                }else{

                                }
                            }

                        }

                        // Create collection 
                        $orderMappedInfo = array(
                            "id" => $OrderData->id,
                            "transfer_type" => 3,
                            "order_number" => $OrderData->orderNo,
                            "transaction_date" => $transactionDate,
                            "customer_order" => $OrderData->buyer,
                            "product"=> $ProductCollection,
                            "delivery_address" => $AddressInfo,
                            "delivery_address_1" => $AddressInfo_1,
                            "receiver_name" => $receiverName,
                            "mobile_no" => $receiverMobileNumber,
                            "statelist" => $statelist,
                            "citylist" => $cityinfo,
                            "special_message" => "Transaction transfer from Qoo10 ( Pack Number : ".$OrderData->packNo." )" 

                        );

                        break;
                        
                    case 4: // Shopee ORDER
                    //
                        // Get Shopee order information
                        $OrderData = ShopeeOrder::find($order_id);
                    
                        $OrderDataDetails = ShopeeOrderDetails::getByOrderID($order_id);
                        $transactionDate = date_format($date=date_create($OrderData->order_datetime),"Y-m-d");

                        if(count($OrderDataDetails) > 0 ){
                            //$OrderDataDetails
                            foreach ($OrderDataDetails as $key => $value) {
                                $APIData = json_decode($value->api_result_return, true);
                              
                                $receiverName = $APIData['name'];
                                $receiverMobileNumber = $APIData['phone'];
                                $AddressInfo = array(
                                    "street_address_1" => $APIData['full_address'],
                                    "street_address_2" => "",
                                    "postcode" => $APIData['zipcode'],
                                    "country" => "458",  // Malaysia Region only. 
                                    // "state" => "458004",
                                    // "city" => ""
                                );

                                // Puzzle for Shopee API 

                                $countryid = "458"; // Malaysia Region only.
                                $statelist = Delivery::getStateList($countryid);

                                $postcode=$AddressInfo['postcode'];
                                $postcode_1 ="";
                                $street_address_1_1 = "";
                                $street_address_2_1 = "";

                                if($postcode=='' || $postcode==0){
                                    $pcode=preg_match("/(\d\d\d\d\d)/", $AddressInfo['street_address_2'], $matches);
                                    $postcode   = $matches[0];
                                    $postcode_1 = $matches[0];

                                    $pieces = explode($postcode, $AddressInfo['street_address_2']);
                                    $street_address_1_1 = $pieces[0];
                                    $street_address_2_1 = $postcode.' '.$pieces[1];
                                }

                                $getpostcode = array();
                                $st_code = "";
                                $post_office = "";
                                $statename   = "";
                                $status = 0;
                                $getpostcode = Transaction::getXMLpostcode($postcode);

                                if($getpostcode['status']==1){
                                    $st_code = $getpostcode['st_code'];
                                    $post_office = $getpostcode['post_office'];
                                    $statename   = $getpostcode['state'];   
                                    $status      = $getpostcode['status'];        

                                } 

                                $stateinfo = Transaction::getStateID($statename);
                                $stateid = $stateinfo->id;


                                $cityid="";
                                $cityinfo = Delivery::getCityList($stateid);

                                $city_info = Transaction::getCityID($post_office,$stateid);
                                $cityid   = $city_info->id;

                                $AddressInfo_1 = array("state"              => $stateid,
                                                       "city"               => $cityid,
                                                       "postcode_1"         => $postcode_1,
                                                       "street_address_1_1" => $street_address_1_1,
                                                       "street_address_2_1" => $street_address_2_1,
                                                       "status"             => $status,  


                                    );
                                if(count($APIData['item_sku']) > 0) {
                                    $ProductInformation = Product::findProductInfoByQRCODE($APIData['item_sku']);

                                    // // CHECK DEFINE LABEL PRICE CODE OLD //
                                    // $optionName = $APIData['variation_sku'];
                                 

                                    // if(count($optionName) > 0 ){
                                    //      // TEST CASE 10752
                                    //     //$optionName = '[10753]'.' '.$optionName;

                                    //     if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                    //         // TAKE DEFINED LABEL ID //
                                             
                                    //         $selectedPriceOptionID = substr($optionName,strpos($string,"[") + 1,strpos($optionName,"]") -1);
                                             
                                    //         $PriceOption = Price::find($selectedPriceOptionID);
                                         
                                    //         if(count($PriceOption)>0){
                                                
                                    //             $ProductPriceInformationID = $selectedPriceOptionID;  
                                    //             $ProductPriceInformationLabel = $PriceOption->label;
                                    //             $ProductPriceInformationActualPrice = $PriceOption->price;
                                    //             $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                    //              //echo "<pre>";
                                    //               // print_r($ProductPriceInformationID);die();
                                    //          //   echo "</pre>";
                                    //         }else{
                                    //             $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                    //             $ProductPriceInformationLabel = $ProductInformation->label;
                                    //             $ProductPriceInformationActualPrice = $ProductInformation->price;
                                    //             $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    //         }
                                    //     }else{
                                    //         $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                    //         $ProductPriceInformationLabel = $ProductInformation->label;
                                    //         $ProductPriceInformationActualPrice = $ProductInformation->price;
                                    //         $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    //     }

                                    // }else{
                                    //     $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                    //     $ProductPriceInformationLabel = $ProductInformation->label;
                                    //     $ProductPriceInformationActualPrice = $ProductInformation->price;
                                    //     $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    // }
                                    // // CHECK DEFINE LABEL PRICE CODE OLD //
                                    
                                    // CHECK DEFINE LABEL PRICE CODE //
                                    $optionId = $APIData['variation_sku'];
                                    $shopee_original_price = $APIData['variation_discounted_price'];
                                 
                                    if(strlen($optionId) > 0 ){
                                         // TEST CASE 10752
                                        //$optionName = '[10753]'.' '.$optionName;

                                        if ($optionId !='') {
                                            // TAKE DEFINED LABEL ID //
                                             
                                            // $selectedPriceOptionID = substr($optionId,strpos($string,"[") + 1,strpos($optionId,"]") -1);
                                             
                                            $PriceOption = Price::find($optionId);
                                         
                                            if(count($PriceOption)>0){
                                                
                                                $ProductPriceInformationID = $optionId;  
                                                $ProductPriceInformationLabel = $PriceOption->label;
                                                $ProductPriceInformationActualPrice = $PriceOption->price;
                                                // $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                                $ProductPriceInformationPromoPrice = $shopee_original_price;
                                                 //echo "<pre>";
                                                   // print_r($ProductPriceInformationID);die();
                                             //   echo "</pre>";
                                            }else{
                                                $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                $ProductPriceInformationLabel = $ProductInformation->label;
                                                $ProductPriceInformationActualPrice = $ProductInformation->price;
                                                $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                            }
                                        }else{
                                            $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                            $ProductPriceInformationLabel = $ProductInformation->label;
                                            $ProductPriceInformationActualPrice = $ProductInformation->price;
                                            $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                        }

                                    }else{
                                        $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                        $ProductPriceInformationLabel = $ProductInformation->label;
                                        $ProductPriceInformationActualPrice = $ProductInformation->price;
                                        $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    }
                                    // CHECK DEFINE LABEL PRICE CODE //
                        
                        if ($ProductInformation->gst == 2 && $ProductPriceInformationActualPrice > 0){
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $actual_price = $ProductPriceInformationActualPrice * $gst_price;
                                    }else{
                            $actual_price = $ProductPriceInformationActualPrice;
                                    }

                        if ($ProductInformation->gst == 2 && $ProductPriceInformationPromoPrice > 0){ 
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                            $promotion_price = $ProductPriceInformationPromoPrice * $gst_price;
                                    }else{ 
                            $promotion_price = number_format($ProductPriceInformationPromoPrice, 2) ;
                                    }


                                    if(count($ProductInformation) > 0 ){
                                        $ProductSubCollection = array(
                                            "product_id" =>  $ProductInformation->id,
                                            "product_name" =>  $ProductInformation->name,
                                            "qrcode" =>  $ProductInformation->qrcode,
                                            "quantity" =>  $APIData['variation_quantity_purchased'],
                                "option_price_id" =>  $ProductPriceInformationID,
                                            "sku" =>  $ProductInformation->sku,
                                "label" =>  $ProductPriceInformationLabel,
                                            "actual_price" =>   number_format($actual_price, 2),
                                            "promotion_price" =>  $promotion_price,
                                            "referral_fees" =>  $ProductInformation->p_referral_fees,
                                            "p_referral_fees_type" => $ProductInformation->p_referral_fees_type,
                                            "Sub-total" => $ProductInformation->product_name
                                        );
                                        array_push($ProductCollection, $ProductSubCollection);
                                    }
                                }else{

                                }
                            }

                        }

                        // Create collection 
                        $orderMappedInfo = array(
                            "id" => $OrderData->id,
                            "transfer_type" => 4,
                            "order_number" => $OrderData->ordersn,
                            "transaction_date" => $transactionDate,
                            "customer_order" => $OrderData->name,
                            "product"=> $ProductCollection,
                            "delivery_address" => $AddressInfo,
                            "delivery_address_1" => $AddressInfo_1,
                            "receiver_name" => $receiverName,
                            "mobile_no" => $receiverMobileNumber,
                            "statelist" => $statelist,
                            "citylist" => $cityinfo,
                            "special_message" => "Transaction transfer from Shopee ( Order Number : ".$OrderData->ordersn." )" 

                        );

                        break;   
                    
                    case 5: // PGMall ORDER
                        
                        // Get PGMall order information
                        $OrderData = PGMallOrder::find($order_id);
                    
                        $OrderDataDetails = PGMallOrderDetails::getByOrderID($order_id);
                        $transactionDate = date_format($date=date_create($OrderData->order_datetime),"Y-m-d");
                        
                        if(count($OrderDataDetails) > 0 ){
                            //$OrderDataDetails
                            foreach ($OrderDataDetails as $key => $value) {
                                $APIData = json_decode($value->api_result_return, true);
                              
                                $receiverName = $APIData['name'];
                                $receiverMobileNumber = $APIData['phone'];
                                $AddressInfo = array(
                                    "street_address_1" => $APIData['full_address'],
                                    "street_address_2" => "",
                                    "postcode" => $APIData['shipping_postcode'],
                                    "country" => "458",  // Malaysia Region only. 
                                    // "state" => "458004",
                                    // "city" => ""
                                );

                                // Puzzle for PGMall API 

                                $countryid = "458"; // Malaysia Region only.
                                $statelist = Delivery::getStateList($countryid);

                                $postcode=$AddressInfo['postcode'];
                                $postcode_1 ="";
                                $street_address_1_1 = "";
                                $street_address_2_1 = "";

                                if($postcode=='' || $postcode==0){
                                    $pcode=preg_match("/(\d\d\d\d\d)/", $AddressInfo['street_address_2'], $matches);
                                    $postcode   = $matches[0];
                                    $postcode_1 = $matches[0];

                                    $pieces = explode($postcode, $AddressInfo['street_address_2']);
                                    $street_address_1_1 = $pieces[0];
                                    $street_address_2_1 = $postcode.' '.$pieces[1];
                                }

                                $getpostcode = array();
                                $st_code = "";
                                $post_office = "";
                                $statename   = "";
                                $status = 0;
                                
                                $getpostcode = Transaction::getXMLpostcode($postcode);

                                if($getpostcode['status']==1){
                                    $st_code     = $getpostcode['st_code'];
                                    $post_office = $getpostcode['post_office'];
                                    $statename   = $getpostcode['state'];   
                                    $status      = $getpostcode['status'];        

                                } 

                                $stateinfo = Transaction::getStateID($statename);
                                $stateid = $stateinfo->id;

                                $cityid="";
                                $cityinfo = Delivery::getCityList($stateid);

                                $city_info = Transaction::getCityID($post_office,$stateid);
                                $cityid   = $city_info->id;

                                $AddressInfo_1 = array("state"              => $stateid,
                                                       "city"               => $cityid,
                                                       "postcode_1"         => $postcode_1,
                                                       "street_address_1_1" => $street_address_1_1,
                                                       "street_address_2_1" => $street_address_2_1,
                                                       "status"             => $status,  


                                    );                            
        
                                if(count($APIData['item_sku']) > 0) {
                                    $ProductInformation = Product::findProductInfoByQRCODE($APIData['item_sku']);
 
                                    // CHECK DEFINE LABEL PRICE CODE //
                                    $optionId = $APIData['item_sku'];
                                    $pgmall_original_price = $APIData['variation_original_price'];
                                 
                                    if(strlen($optionId) > 0 ){
                                         // TEST CASE 10752
                                        //$optionName = '[10753]'.' '.$optionName;

                                        if ($optionId !='') {
                                            // TAKE DEFINED LABEL ID //
                                             
                                            // $selectedPriceOptionID = substr($optionId,strpos($string,"[") + 1,strpos($optionId,"]") -1);
                                             
                                            $PriceOption = Price::find($optionId);
                                         
                                            if(count($PriceOption)>0){
                                                
                                                $ProductPriceInformationID = $optionId;  
                                                $ProductPriceInformationLabel = $PriceOption->label;
                                                $ProductPriceInformationActualPrice = $PriceOption->price;
                                                // $ProductPriceInformationPromoPrice = $PriceOption->price_promo;
                                                $ProductPriceInformationPromoPrice = $pgmall_original_price;
                                                 //echo "<pre>";
                                                   // print_r($ProductPriceInformationID);die();
                                             //   echo "</pre>";
                                            }else{
                                                $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                                $ProductPriceInformationLabel = $ProductInformation->label;
                                                $ProductPriceInformationActualPrice = $ProductInformation->price;
                                                $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                            }
                                        }else{
                                            $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                            $ProductPriceInformationLabel = $ProductInformation->label;
                                            $ProductPriceInformationActualPrice = $ProductInformation->price;
                                            $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                        }

                                    }else{
                                        $ProductPriceInformationID = $ProductInformation->ProductPriceID;
                                        $ProductPriceInformationLabel = $ProductInformation->label;
                                        $ProductPriceInformationActualPrice = $ProductInformation->price;
                                        $ProductPriceInformationPromoPrice = $ProductInformation->price_promo;
                                    }
                                    // CHECK DEFINE LABEL PRICE CODE //
                        
                                    if ($ProductInformation->gst == 2 && $ProductPriceInformationActualPrice > 0){
                                        $gst_price  = 1 + $ProductInformation->gst_value / 100;
                                        $actual_price = $ProductPriceInformationActualPrice * $gst_price;
                                    }else{
                                        $actual_price = $ProductPriceInformationActualPrice;
                                    }

                                    if ($ProductInformation->gst == 2 && $ProductPriceInformationPromoPrice > 0){ 
                                                    $gst_price  = 1 + $ProductInformation->gst_value / 100;
                                        $promotion_price = $ProductPriceInformationPromoPrice * $gst_price;
                                    }else{ 
                                        $promotion_price = number_format($ProductPriceInformationPromoPrice, 2) ;
                                    }


                                    if(count($ProductInformation) > 0 ){
                                        $ProductSubCollection = array(
                                            "product_id" =>  $ProductInformation->id,
                                            "product_name" =>  $ProductInformation->name,
                                            "qrcode" =>  $ProductInformation->qrcode,
                                            "quantity" =>  $APIData['order_quantity'],
                                            "option_price_id" =>  $ProductPriceInformationID,
                                            "sku" =>  $ProductInformation->sku,
                                            "label" =>  $ProductPriceInformationLabel,
                                            "actual_price" =>   number_format($actual_price, 2),
                                            "promotion_price" =>  $promotion_price,
                                            "referral_fees" =>  $ProductInformation->p_referral_fees,
                                            "p_referral_fees_type" => $ProductInformation->p_referral_fees_type,
                                            "Sub-total" => $ProductInformation->product_name
                                        );
                                        array_push($ProductCollection, $ProductSubCollection);
                                    }
                                }else{

                                }
                            }

                        }

                        // Create collection 
                        $orderMappedInfo = array(
                            "id" => $OrderData->id,
                            "transfer_type" => 5,
                            "order_number" => $OrderData->ordersn,
                            "transaction_date" => $transactionDate,
                            "customer_order" => $OrderData->name,
                            "product"=> $ProductCollection,
                            "delivery_address" => $AddressInfo,
                            "delivery_address_1" => $AddressInfo_1,
                            "receiver_name" => $receiverName,
                            "mobile_no" => $receiverMobileNumber,
                            "statelist" => $statelist,
                            "citylist" => $cityinfo,
                            "special_message" => "Transaction transfer from PGMall ( Order Number : ".$OrderData->ordersn." )" 

                        );
                        break;
                        
                    default:
                        break;
                }
                
                 
                
            }

            Session::put('devicetype', "manual");

            return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_add')
                ->with([
                    'display_cust' => $addCust,
                    'countries'    => $countries,
                    "orderMappedInfo" => $orderMappedInfo
                ]);
        }
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
    }
    }


    /**
     * Edit transaction details
     * @param  [type] $id [Transation ID]
     * @return [type]     [description]
     */
    public function anyEdit($id = null)
    {
       
        if (isset($id)) {
            
            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
            $Transaction = Transaction::find($id);
            
            $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
            $stateName = array();
            foreach ($SysAdminRegion as  $value) {
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }
            if(!in_array($Transaction->delivery_state, $stateName)){
                $access = false;
                $SysAdmin = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::getSysAdminRegion($SysAdmin->id);
                foreach ($SysAdminRegion as $key => $value) {
                    if($value->region_id == 0 ){
                        $access = true;
                    }
                }
                if(!$access){
                    return Redirect::to('transaction')->with('message', "You don't have access right for that Transaction ID")->withErrors($validator)->withInput();
                }
            }
            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
            // die('in');
            if (Input::has('id')) {

                $validator = Validator::make(Input::all(), Transaction::$rules, Transaction::$message);
                
                if ($validator->passes()) {
                     
                    $rs = Transaction::save_transaction();
                    
                    if ($rs == true) {
                        $insert_audit = General::audit_trail('TransactionController.php', 'Edit()', 'Edit Transaction', Session::get('username'), 'CMS');
                        return Redirect::to('transaction/edit/'.$id)->with('success', 'Transaction(ID: '.$id.') updated successfully.');
                    } else {
                        return Redirect::to('transaction')->with('message', 'Transaction(ID: '.$id.') update failed.');
                    }

                } else {
                    return Redirect::to('transaction/edit/'.$id)->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }
            } else {
                
                
                
                $editTrans  = Transaction::where('id', '=', $id)->get();
                
                // $addCust    = Customer::all()->lists('username', 'full_name');
                $addCust = DB::table('jocom_user')
                                ->select('username', 'full_name')
                                ->get();
                // die('in');
                $editCoupon = TCoupon::where('transaction_id', '=', $id)->first();
                // $editDetails = TDetails::where('transaction_id','=', $id)->get();
            
                $editDetails = DB::table('jocom_transaction_details AS a')
                    ->select('a.*', 'b.name')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                    //->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                    ->where('a.transaction_id', '=', $id)
                    //->where('c.main', '=', '1')
                    // ->groupBy('a.sku')
                    //->orderBy('c.category_id')
                    ->orderBy('b.name')
                //->where('a.product_group', '!=', '')
                    ->get();

                $parentInv  = DB::table('jocom_transaction_parent_invoice')->where('transaction_id', '=', $id)->get();
                $editPaypal = TPayPal::where('transaction_id', '=', $id)->get();
                $editMolPay = TMolPay::where('transaction_id', '=', $id)->get();
                $editTRevPay = TRevPay::where('transaction_id', '=', $id)->get();
                $editTMPay  = TMPay::where('transaction_id', '=', $id)->get();
                $editBoost  = BoostTransaction::where('transaction_id', '=', $id)->get();
                $editGrabPay  = DB::table('jocom_grabpay_transaction')->where('transaction_id', '=', $id)->get();
                $editFavePay  = DB::table('jocom_favepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPacePay  = DB::table('jocom_pacepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
                    ->where('jocom_transaction_point.transaction_id', '=', $id)
                    ->get();

                // Earned points
                $earnedPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)
                    ->where('point_users.status', '=', 1)
                    ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                    ->get();

                $earnedId = [];

                foreach ($earnedPoints as $earnedPoint) {
                    $earnedId[] = $earnedPoint->id;
                }

                $reversalPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)
                    ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                    ->get();

                $reversedId = [];

                foreach ($reversalPoints as $reversalPoint) {
                    $reversedId[] = $reversalPoint->reversal;
                }

                $earnedPoints = DB::table('point_transactions')
                    ->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))
                    ->get();

                $earnedBPoint = DB::table('bcard_transactions')
                    ->where('bill_no', '=', 'T'.$id)
                    ->pluck('point');

                $agentId = array_get(reset($editTrans->toArray()), 'agent_id');

                if ($agentId != 1) {
                    $agent     = Agent::find($agentId);
                    $agentCode = $agent->agent_code;
                }

                // Get delivery status
                $logisticStatus = LogisticTransaction::where('transaction_id', '=', $id)->pluck('status');

                if ($logisticStatus) {
                    $deliveryStatus = LogisticTransaction::get_status($logisticStatus);
                } else {
                    $deliveryStatus = '-';
                }
                
                $purchasehistory = PurchaseHistory::where("transaction_id",$id)
                        ->get();
                
                $lazadaprofitloss = DB::table('jocom_lazada_transaction_details')
                                        ->where("transaction_id",$id)
                                        ->get();
                                        
                $shopeeprofitloss = DB::table('jocom_shopee_transaction_details')
                                        ->where("transaction_id",$id)
                                        ->get();
                
                $tiktokprofitloss = DB::table('jocom_tiktok_transaction_details')
                                        ->where("transaction_id",$id)
                                        ->get();
                                        
                $customerInvoiceLog = DB::table('jocom_customer_invoice_log AS JCIL')
                        ->where("JCIL.transaction_id",$id)
                        ->where("JCIL.status",1)
                        ->first();
                
                $externalPlatformList = array("11Street","prestomall","lazada","shopee","Qoo10","pgmall");
                
                if(count($customerInvoiceLog) > 0 &&  in_array($Transaction->buyer_username,$externalPlatformList )){
                    $isVisible = true;
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => true
                    );
                }else{
                    
                    $isGenerated= false;
                    $isVisible = false;
                     
                    if(in_array($Transaction->buyer_username,$externalPlatformList )){
                       $isVisible = true;
                    }
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => $isGenerated
                    );
                    
                }
                
                
                $sellers = Seller::orderBy('company_name', 'asc')->get();

                    foreach ($sellers as $seller) {
                        switch ($seller->active_status) {
                            case 0:
                                $status = ' **[Inactive]';
                                break;
                            case 2:
                                $status = ' **[Deleted]';
                                break;
                            default:
                                $status = '';
                                break;
                        }

                        $sellersOptions[$seller->id] = $seller->company_name.$status;
                    }
            
                return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_edit')
                    ->with('display_trans', $editTrans)
                    ->with('display_cust', $addCust)
                    ->with('display_coupon', $editCoupon)
                    ->with('display_points', $editPoints)
                    ->with('display_details', $editDetails)
                    ->with('display_parent_inv', $parentInv)
                    ->with('display_paypal', $editPaypal)
                    ->with('display_molpay', $editMolPay)
                    ->with('display_revpay', $editTRevPay)
                    ->with('display_mpay', $editTMPay)
                    ->with('display_boost', $editBoost)
                    ->with('display_grab', $editGrabPay)
                    ->with('display_Fave', $editFavePay)
                    ->with('display_Pace', $editPacePay)
                    ->with('display_earns', $earnedPoints)
                    ->with('display_bpoint', $earnedBPoint)
                    ->with('display_agent', $agentCode)
                    ->with('sellersOptions', $sellersOptions)
                    ->with('display_delivery_status', $deliveryStatus)
                    ->with('purchase_history', $purchasehistory)
                    ->with('lazada_profitloss', $lazadaprofitloss)
                    ->with('shopee_profitloss', $shopeeprofitloss)
                    ->with('tiktok_profitloss', $tiktokprofitloss)
                    ->with('display_customer_invoice', $customerInvoice);
            }
        } else {
            return Redirect::to('transaction')->with('message', 'No transaction is selected for edit.');
        }
    }
    
    public function anyPurchase(){
        $response = 1;
        $Seller = Seller::find(Input::get('seller_name'));
        $details = TDetails::find(Input::get('trans_item_id'));


        DB::table('jocom_transaction_purchase_history')->insert(array(
                            'transaction_id'     =>  Input::get('transaction_id'),
                            'date_of_purchase'   =>  date_format($date=date_create(Input::get('purchase_date')),"Y-m-d"),
                            'ref_no'             =>  Input::get('receipt_no'),
                            'seller_id'          =>  Input::get('seller_name'),
                            'seller_company'     =>  $Seller->company_name,
                            'product_id'         =>  $details->product_id,
                            'product_name'       =>  $details->product_name,
                            'price'              =>  Input::get('item_price'),
                            'product_qty'        =>  Input::get('item_qty'),
                            'amount'              =>  Input::get('totalamount'),
                            'remarks'              =>  Input::get('purchase_remark'),
                            'insert_by'             => Session::get('username'),
                            'insert_date'         => date('Y-m-d H:i:s'),
                            'modify_by'              => Session::get('username'),
                            'modify_date'         => date('Y-m-d H:i:s')
                            )
                    );
       return $response;
    }
    
    public function anyPurchaseupdate(){

            $response = 1;
            $Seller = Seller::find(Input::get('seller_name'));

            $id = Input::get('trans_purchase_item_id');

            $purchase = PurchaseHistory::find($id);
            $purchase->transaction_id =  Input::get('trans_id');
            $purchase->date_of_purchase = Input::get('purchase_date') ? date_format($date=date_create(Input::get('purchase_date')),"Y-m-d") : date('Y-m-d');
            $purchase->ref_no =  Input::get('receipt_no');
            $purchase->seller_id =  Input::get('seller_name');
            $purchase->seller_company =  $Seller->company_name;
            $purchase->product_id =  Input::get('product_id');
            $purchase->product_name =  Input::get('item_name');
            $purchase->price =  Input::get('item_price');
            $purchase->product_qty =  Input::get('item_qty');
            $purchase->amount =  Input::get('totalamount');
            $purchase->remarks =  Input::get('purchase_remark');
            $purchase->modify_by =  Session::get('username');
            $purchase->modify_date =  date('Y-m-d H:i:s');
            $purchase->save();

        return $response;
    }

    public function anyDeletepurchase() {
        $id = Input::get('id');
        DB::table('jocom_transaction_purchase_history')->where('id', '=', $id)->delete();
        return Response::json(array('success' => 'Success.'));
    }
    
    public function anyLazadasaveprofitloss(){
        $response = 1;

        DB::table('jocom_lazada_transaction_details')->insert(array(
                        'transaction_id'                =>  Input::get('transaction_id'),
                        'commission'                    =>  Input::get('lazada_commission'),
                        'payment_fee'                  =>  Input::get('lazada_payment_fee'),
                        'campaign_fee'                  =>  Input::get('lazada_campaign_fee'),
                        'lazcoins_discount'             =>  Input::get('lazada_lazcoins_discount'),
                        'shipping_fee_voucher_by_lazada' =>  Input::get('lazada_shipping_fee_voucher'),
                        'platform_shippingfee_subsidy_tax' => Input::get('lazada_platform_shipping_tax'),
                        'shipping_fee_paid_by_cus'      =>  Input::get('lazada_shipping_paid_by_customer'),
                        'lazcoins_discount_promotion_fee' =>  Input::get('lazcoins_discount_promotion_fee'),
                        'item_price_credit'             =>  Input::get('lazada_item_price_credit'),                            
                        'insert_by'                     => Session::get('username'),
                        'insert_date'                   => date('Y-m-d H:i:s'),
                        'modify_by'                     => Session::get('username'),
                        'modify_date'                   => date('Y-m-d H:i:s')
                        )
                    );
       return $response;
    }

    public function anyProfitlossupdate(){
            $response = 1;
          try {
            $id = Input::get('ed_lazada_item_hidden_id');

            $profitloss = LazadaTransaction::find($id);
            $profitloss->transaction_id =  Input::get('transaction_id');
            $profitloss->commission = Input::get('lazada_commission');
            $profitloss->payment_fee =  Input::get('lazada_payment_fee');
            $profitloss->campaign_fee =  Input::get('lazada_campaign_fee');
            $profitloss->lazcoins_discount =  Input::get('lazada_lazcoins_discount');
            $profitloss->shipping_fee_voucher_by_lazada =  Input::get('lazada_shipping_fee_voucher');
            $profitloss->platform_shippingfee_subsidy_tax =  Input::get('lazada_platform_shipping_tax');
            $profitloss->shipping_fee_paid_by_cus =  Input::get('lazada_shipping_paid_by_customer');
            $profitloss->lazcoins_discount_promotion_fee =  Input::get('lazcoins_discount_promotion_fee');
            $profitloss->item_price_credit =  Input::get('lazada_item_price_credit');
            $profitloss->modify_by =  Session::get('username');
            $profitloss->modify_date =  date('Y-m-d H:i:s');
            $profitloss->save();
         }
        catch(Exception $ex) {
            $isError = true;
            $message = $ex->getMessage();
            return $message;
        }
        finally{
            return $response;
        }
        
    }

    public function anyDeleteprofitloss() {
        $response = 1;
        $id = Input::get('id');
        DB::table('jocom_lazada_transaction_details')->where('id', '=', $id)->delete();
        return $response;
        // return Response::json(array('success' => 'Success.'));
    }
    
    
    public function anyShopeesaveprofitloss(){
        $response = 1;

        DB::table('jocom_shopee_transaction_details')->insert(array(
                        'transaction_id'                            =>  Input::get('transaction_id'),
                        'shippingfee_paid_by_buyer'                 =>  Input::get('shopee_shippingfee_paid_by_buyer'),
                        'shippingfee_charged_by_logistic_provider'  =>  Input::get('shopee_shippingfee_charged_by_logistic_provider'),
                        'seller_paid_shippingfee'                   =>  Input::get('shopee_seller_paid_shippingfee'),
                        'product_discount_rebate'                   =>  Input::get('shopee_product_discount_rebate'),
                        'commission'                                =>  Input::get('shopee_commission'),
                        'service_fee'                               =>  Input::get('shopee_service_fee'),
                        'transaction_fee'                           =>  Input::get('shopee_transaction_fee'),
                        'saver_programme_fee'                       =>  Input::get('shopee_saver_programme_fee'),
                        'ams_commission_fee'                        =>  Input::get('shopee_ams_commission_fee'),
                        'other_fee'                                 =>  Input::get('shopee_other_fee'),
                        'item_price_credit'                         =>  Input::get('shopee_item_price_credit'),                            
                        'insert_by'                                 => Session::get('username'),
                        'insert_date'                               => date('Y-m-d H:i:s'),
                        'modify_by'                                 => Session::get('username'),
                        'modify_date'                               => date('Y-m-d H:i:s')
                        )
                    );
       return $response;
    }

    public function anyShopeeprofitlossupdate(){
            $response = 1;
          try {
            $id = Input::get('ed_shopee_item_hidden_id');

            $profitloss = ShopeeTransaction::find($id);
            $profitloss->transaction_id =  Input::get('transaction_id');
            $profitloss->shippingfee_paid_by_buyer = Input::get('shopee_shippingfee_paid_by_buyer');
            $profitloss->shippingfee_charged_by_logistic_provider =  Input::get('shopee_shippingfee_charged_by_logistic_provider');
            $profitloss->seller_paid_shippingfee =  Input::get('shopee_seller_paid_shippingfee');
            $profitloss->product_discount_rebate =  Input::get('shopee_product_discount_rebate');
            $profitloss->commission =  Input::get('shopee_commission');
            $profitloss->service_fee =  Input::get('shopee_service_fee');
            $profitloss->transaction_fee =  Input::get('shopee_transaction_fee');
            $profitloss->saver_programme_fee =  Input::get('shopee_saver_programme_fee');
            $profitloss->ams_commission_fee =  Input::get('shopee_ams_commission_fee');
            $profitloss->other_fee =  Input::get('shopee_other_fee');
            $profitloss->item_price_credit =  Input::get('shopee_item_price_credit');
            $profitloss->modify_by =  Session::get('username');
            $profitloss->modify_date =  date('Y-m-d H:i:s');
            $profitloss->save();
         }
        catch(Exception $ex) {
            $isError = true;
            $message = $ex->getMessage();
            return $message;
        }
        finally{
            return $response;
        }
        
    }

    
    public function anyDeleteshopeeprofitloss() {
        $response = 1;
        $id = Input::get('id');
        DB::table('jocom_shopee_transaction_details')->where('id', '=', $id)->delete();
        return $response;
        // return Response::json(array('success' => 'Success.'));
    }  
    
    
    
    /** 
     * TikTok Profit & Loss 
     * <Add>/ <Update> / <Delete>
     */

     public function anyTiktoksaveprofitloss(){
        $response = 1;

        DB::table('jocom_tiktok_transaction_details')->insert(array(
                        'transaction_id'                            =>  Input::get('transaction_id'),
                        'subtotal_after_discounts'                  =>  Input::get('tiktok_subtotal_after_discounts'),
                        'subtotal_before_discounts'                 =>  Input::get('tiktok_subtotal_before_discounts'),
                        'seller_discounts'                          =>  Input::get('tiktok_seller_discounts'),
                        'transaction_fee'                           =>  Input::get('tiktok_transaction_fee'),
                        'commission_fee'                            =>  Input::get('tiktok_commission_fee'),
                        'actual_shipping_fee'                       =>  Input::get('tiktok_actual_shipping_fee'),
                        'platform_shipping_fee_discount'            =>  Input::get('tiktok_platform_shipping_fee_discount'),
                        'customer_shipping_fee_before_discounts'    =>  Input::get('tiktok_customer_shipping_fee_before_discounts'),
                        'seller_shipping_fee_discount'              =>  Input::get('tiktok_seller_shipping_fee_discount'),
                        'tiktokshop_shipping_fee_discount'          =>  Input::get('tiktok_tiktokshop_shipping_fee_discount'),
                        'actual_return_shipping_fee'                =>  Input::get('tiktok_actual_return_shipping_fee'), 
                        'refunded_customer_shipping_fee'            =>  Input::get('tiktok_refunded_customer_shipping_fee'), 
                        'shipping_subsidy'                          =>  Input::get('tiktok_shipping_subsidy'), 
                        'affiliate_commission'                      =>  Input::get('tiktok_affiliate_commission'), 
                        'bonus_cashback_service_fee'                =>  Input::get('tiktok_bonus_cashback_service_fee'), 
                        'voucher_xtra_service_fee'                  =>  Input::get('tiktok_voucher_xtra_service_fee'), 
                        'other_fees'                                =>  Input::get('tiktok_other_fees'), 
                        'total_settlement_amount'                   =>  Input::get('tiktok_total_settlement_amount'),                            
                        'insert_by'                                 => Session::get('username'),
                        'insert_date'                               => date('Y-m-d H:i:s'),
                        'modify_by'                                 => Session::get('username'),
                        'modify_date'                               => date('Y-m-d H:i:s')
                        )
                    );
       return $response;
    }

    public function anyTiktokprofitlossupdate(){
            $response = 1;
          try {
            $id = Input::get('ed_tiktok_item_hidden_id');

            $profitloss = TiktokTransaction::find($id);
            $profitloss->transaction_id =  Input::get('transaction_id');
            $profitloss->subtotal_after_discounts = Input::get('tiktok_subtotal_after_discounts');
            $profitloss->subtotal_before_discounts =  Input::get('tiktok_subtotal_before_discounts');
            $profitloss->seller_discounts =  Input::get('tiktok_seller_discounts');
            $profitloss->transaction_fee =  Input::get('tiktok_transaction_fee');
            $profitloss->commission_fee =  Input::get('tiktok_commission_fee');
            $profitloss->actual_shipping_fee =  Input::get('tiktok_actual_shipping_fee');
            $profitloss->platform_shipping_fee_discount =  Input::get('tiktok_platform_shipping_fee_discount');
            $profitloss->customer_shipping_fee_before_discounts =  Input::get('tiktok_customer_shipping_fee_before_discounts');
            $profitloss->seller_shipping_fee_discount =  Input::get('tiktok_seller_shipping_fee_discount');
            $profitloss->tiktokshop_shipping_fee_discount =  Input::get('tiktok_tiktokshop_shipping_fee_discount');
            $profitloss->actual_return_shipping_fee =  Input::get('tiktok_actual_return_shipping_fee');
            $profitloss->refunded_customer_shipping_fee =  Input::get('tiktok_refunded_customer_shipping_fee');
            $profitloss->shipping_subsidy =  Input::get('tiktok_shipping_subsidy');
            $profitloss->affiliate_commission =  Input::get('tiktok_affiliate_commission');
            $profitloss->bonus_cashback_service_fee =  Input::get('tiktok_bonus_cashback_service_fee');
            $profitloss->voucher_xtra_service_fee =  Input::get('tiktok_voucher_xtra_service_fee'); 
            $profitloss->other_fees =  Input::get('tiktok_other_fees');
            $profitloss->total_settlement_amount =  Input::get('tiktok_total_settlement_amount');
            $profitloss->modify_by =  Session::get('username');
            $profitloss->modify_date =  date('Y-m-d H:i:s');
            $profitloss->save();
         }
        catch(Exception $ex) {
            $isError = true;
            $message = $ex->getMessage();
            return $message;
        }
        finally{
            return $response;
        }
        
    }

    public function anyDeletetiktokprofitloss() {
        $response = 1;
        $id = Input::get('id');
        DB::table('jocom_tiktok_transaction_details')->where('id', '=', $id)->delete();
        return $response;
        // return Response::json(array('success' => 'Success.'));
    }

        
    /**
     * Delete transaction
     * @return [type] [description]
     */
    public function anyRemove()
    {
        if (Input::has('remove_transaction_id')) {
            $transaction_id = Input::get('remove_transaction_id');
            $trans          = Transaction::find($transaction_id);

            if ($trans->status == 'completed' || $trans->status == 'refund') {
                return Redirect::to('transaction')->with('message', 'Delete failed. Completed or Refunded transaction cannot be delete.');
            } else {
                if ($trans->delete()) {
                    $insert_audit = General::audit_trail('TransactionController.php', 'Remove()', 'Delete Transaction', Session::get('username'), 'CMS');

                    $transD = TDetails::where('transaction_id', '=', $transaction_id)->delete();
                    $transC = TCoupon::where('transaction_id', '=', $transaction_id)->delete();

                    return Redirect::to('transaction')->with('success', 'Transaction(ID: '.$transaction_id.') has been deleted.');
                } else {
                    return Redirect::to('transaction')->with('message', 'Delete failed. Data has not changed');
                }
            }
        }

        // removed, as not allow to delete transaction details
        // elseif (Input::has('remove_details_id'))
        // {
        //     $details_id = Input::get('remove_details_id');
        //     $trans_id = Input::get('transID');
        //     $details = TDetails::find($details_id);

        //     if ($details->delete())
        //     {
        //         $transD = TDetails::where('id', '=', $details_id)->delete();
        //         return Redirect::to('transaction/edit/'.$trans_id)->with('success', 'Transaction details(ID: '.$details_id.') has been deleted.');
        //     }
        //     else
        //     {
        //         return Redirect::to('transaction/edit/'.$trans_id)->with('message', 'Delete failed. Data has not changed');
        //     }
        // }
    }

    /**
     * Open file for invoice, PO, DO
     * @param  [type] $loc [description]
     * @return [type]      [description]
     */
    public function anyFiles($loc = null)
    {
        
        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);

        $eINV_no = $id[1];
         
        $po_no = $id[1];

        $epo_no = $id[1];
        
        $id = $id[0];
        // print_r($loc);
        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {
            // echo "<script>window.close();</script>";
            if (strpos($loc, 'DO') !== false) {

                $trans = Transaction::find($id);

                if($trans->qr_code == ''){
                
                    include app_path('library/phpqrcode/qrlib.php');

                    $qrCode     = $trans->do_no;
                    $qrCodeFile = $trans->do_no.'.png';
                    // $path = 'images/qrcode/';

                    QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);

                    $trans->qr_code = $qrCodeFile;
                    $trans->save();
                    
                    $trans = Transaction::find($id);
                }

                $DOView = self::createDOView($trans);
                
                // echo "<pre>";
                // print_r( $DOView['product']);
                // echo "</pre>";

                return View::make('checkout.do_view')
                     ->with('display_details', $DOView['general'])
                     ->with('display_trans', $DOView['trans'])
                     ->with('display_seller', $DOView['paypal'])
                     ->with('display_product', $DOView['product'])
                     ->with('display_group', $DOView['group'])
                     ->with('delivery_type', $DOView['delivery_type'])
                     ->with('deliveryservice', $DOView['deliveryservice'])
                     ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                     ->with('htmlview',true);

            }
            
            if (strpos($loc, 'PL') !== false) {

                $trans = Transaction::find($id);

                // print_r($trans);


                $PLView = self::createPLView($trans);
                
                // echo "<pre>";
                // print_r($PLView);
                // echo "</pre>";

                // die('ok');

                return View::make('checkout.purchase_view_history')
                     ->with('display_details', $PLView['general'])
                     ->with('display_trans', $PLView['trans'])
                     ->with('display_seller', $PLView['paypal'])
                     ->with('display_product', $PLView['product'])
                     ->with('display_group', $PLView['group'])
                     ->with('delivery_type', $PLView['delivery_type'])
                     ->with('deliveryservice', $PLView['deliveryservice'])
                     ->with("display_delivery_service_items",$PLView['DeliveryOrderItems'])
                     ->with('htmlview',true);

            }

            if (strpos($loc, '/TMG') !== false) {
                
                $trans = Transaction::find($id);
                $invToCountry = strtolower($trans->delivery_country);
                
                if($trans->buyer_username == 'macrolink'){
                   $invToCountry = 'malaysia'; 
                }
                
                // echo $invToCountry;
                
                switch ($invToCountry) {
                    case 'malaysia':
                    case 'singapore':
                    case 'united states':
                    case 'canada':
                    case 'south korea':   
                    case 'indonesia':   
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            21004,
                            23143,
                            19959,
                            21003,
                            20057,
                            24276,
                            24275,
                            24279,
                            24286,
                            24287,
                            28176,
                            24288);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        return View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('display_jcash', $INVView['jcashback'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('htmlview',true)
                                ->with('downloadLink',$downloadLink);
                                
                         break;
                    case 'china':
                        $transactionDate = $trans->transaction_date;
                        if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                            $invoiceview = 'checkout.invoice_international_china_v2';
                        }  else{
                            $invoiceview = 'checkout.invoice_international_china';
                        }
                        $INVView = self::createChinaInternationalInvView($trans);
                        return View::make($invoiceview)
                                ->with('trans', $trans)
                                ->with('invoiceInfo', $INVView['invoiceInfo'])
                                ->with('issuer', $INVView['issuer'])
                                ->with('invoiceTo', $INVView['invoiceTo'])
                                ->with('product',$INVView['product'])
                                ->with('totalDeclared', $INVView['totalDeclared'])
                                ->with('countryOrigin', $INVView['countryOrigin'])
                                ->with('ReasonSending', $INVView['ReasonSending'])
                                ->with('productItems', $INVView['productItems'])
                                ->with('alternativeAmount', $INVView['alternativeAmount'])
                                ->with('TotalBusinessCurrency', $INVView['TotalBusinessCurrency'])
                                ->with('TotalAlternativeCurrency', $INVView['TotalAlternativeCurrency'])
                                ->with('BusinessCurrency', $INVView['BusinessCurrency'])
                                ->with('AlternativeCurrency', $INVView['AlternativeCurrency'])
                                ->with('Remarks', $INVView['Remarks'])
                                ->with('TotalBusinessCurrencyDeliveryCharges', $INVView['TotalBusinessCurrencyDeliveryCharges'])
                                ->with('TotalAlternativeCurrencyDeliveryCharges', $INVView['TotalAlternativeCurrencyDeliveryCharges']);
                        break;
                    default:
                        break;
                        
                }

            }
            
            if (strpos($loc, '/BINV') !== false) {

                $trans = Transaction::find($id);
                $INVView = self::createChinaInternationalBuydayInvView($trans);
                return View::make('checkout.invoice_buyday_international')
                        ->with('trans', $trans)
                        ->with('invoiceInfo', $INVView['invoiceInfo'])
                        ->with('issuer', $INVView['issuer'])
                        ->with('invoiceTo', $INVView['invoiceTo'])
                        ->with('product',$INVView['product'])
                        ->with('totalDeclared', $INVView['totalDeclared'])
                        ->with('countryOrigin', $INVView['countryOrigin'])
                        ->with('ReasonSending', $INVView['ReasonSending'])
                        ->with('productItems', $INVView['productItems'])
                        ->with('alternativeAmount', $INVView['alternativeAmount'])
                        ->with('TotalBusinessCurrency', $INVView['TotalBusinessCurrency'])
                        ->with('TotalAlternativeCurrency', $INVView['TotalAlternativeCurrency'])
                        ->with('BusinessCurrency', $INVView['BusinessCurrency'])
                        ->with('AlternativeCurrency', $INVView['AlternativeCurrency'])
                        ->with('TotalBusinessCurrencyDeliveryCharges', $INVView['TotalBusinessCurrencyDeliveryCharges'])
                        ->with('TotalAlternativeCurrencyDeliveryCharges', $INVView['TotalAlternativeCurrencyDeliveryCharges'])
                        ->with('Remarks', $INVView['Remarks']);
                      
            }
            
            if (strpos($loc, 'CUS') !== false) {

                $trans = Transaction::find($id);

                $INVView = self::createINVView($trans,true);
                //New Invoice Start 
                $invoiceview = "";
                $transactionDate = $trans->transaction_date;
                // Select type of Invoice format
                
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                            $invoiceview = 'checkout.invoice_view_new_v3';
                }  elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                    $invoiceview = 'checkout.invoice_view_new_v2';
                    
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                    
                    $invoiceview = 'checkout.invoice_view_new';
                    
                }  else{
                    
                    $invoiceview = 'checkout.invoice_view';
                }
                // Select type of Invoice format
                $downloadLink = Crypt::encrypt($trans->id."#".$trans->id."#CUS");
                $downloadLink = urlencode(base64_encode($downloadLink));
                //New Invoice End
                return View::make($invoiceview)
                        ->with('display_details', $INVView['general'])
                        ->with('display_trans', $INVView['trans'])
                        ->with('display_issuer',$INVView['issuer'])
                        ->with('display_seller', $INVView['paypal'])
                        ->with('display_coupon', $INVView['coupon'])
                        ->with('display_product', $INVView['product'])
                        ->with('display_group', $INVView['group'])
                        ->with('display_points', $INVView['points'])
                        ->with('display_earns', $INVView['earnedPoints'])
                        ->with('toCustomer', $INVView['toCustomer'])
                        ->with('buyer_type', $INVView['buyer_type'])
                        ->with('htmlview',true)
                        ->with('CustomerInvoice',true)
                        ->with('downloadLink',$downloadLink);

            }
            
            if (strpos($loc, '/QINV') !== false) {

                $trans = TransactionQoo10::find($id);

                $INVView = self::createQoo10INVView($trans);
                //New Invoice Start 
                $invoiceview = "";
                $inv_newdate = Config::get('constants.NEW_INVOICE_SST_START_DATE');

                $currentdate = $trans->transaction_date;
                if($currentdate >= $inv_newdate){
                    $invoiceview = 'checkout.inv_qooten_new_v3';
                }
                else 
                {
                     $invoiceview = 'checkout.inv_qooten_new_v2';
                }

                //New Invoice End

                return View::make($invoiceview)
                         ->with('display_details', $INVView['general'])
                         ->with('display_trans', $INVView['trans'])
                         ->with('display_issuer',$INVView['issuer'])
                         ->with('display_seller', $INVView['paypal'])
                         ->with('display_coupon', $INVView['coupon'])
                         ->with('display_product', $INVView['product'])
                         ->with('display_group', $INVView['group'])
                         ->with('display_points', $INVView['points'])
                         ->with('display_earns', $INVView['earnedPoints'])
                         ->with('toCustomer', $INVView['toCustomer'])
                         ->with('Cart_Discount_Seller', $INVView['Cart_Discount_Seller'])
                         ->with('Cart_Discount_Qoo10', $INVView['Cart_Discount_Qoo10'])
                         ->with('htmlview',true);

            }

            if (strpos($loc, '/eINVV') !== false) {
                $einvval = 0;    
                $arraytrans = array(
                    24276,
                    24275,
                    24279,
                    24286,
                    24287,
                    24288,
                    20057,
                    21003,
                    19959,
                    23143,
                    21004);     
                $einvval = in_array($id, $arraytrans); 
                $trans = Transaction::find($id);

                $EINVView = self::createEINVView($trans,$eINV_no);

                //New Invoice Start 
                $invoiceview = "";
                $transactionDate = $trans->transaction_date;
                
                // Select type of Invoice
                
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') ){ //
                        $invoiceview = 'checkout.Einvoice_view_new_v4';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                        $invoiceview = 'checkout.invoice_view_new_v3';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                        $invoiceview = 'checkout.invoice_view_new_v2';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                    
                    $invoiceview = 'checkout.invoice_view_new';
                    
                }  else{
                    
                    $invoiceview = 'checkout.invoice_view';
                } 
                
                if(isset($einvval) && $einvval == 1){
                 $invoiceview = 'checkout.invoice_view_new_e37';
                }
                
                //Added on 09-08-2018
                $file = (Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH') . '/' . urlencode($eINV_no) . '.pdf')."#".($trans->id).'#'.$eINV_no;
                $downloadLink = Crypt::encrypt($file);
                $downloadLink = urlencode(base64_encode($downloadLink));
                //=====================
                 //New Invoice End
                return View::make($invoiceview)
                         ->with('display_details', $EINVView['general'])
                         ->with('display_trans', $EINVView['trans'])
                         ->with('display_issuer',$EINVView['issuer'])
                         ->with('display_seller', $EINVView['paypal'])
                         ->with('display_product', $EINVView['product'])
                         ->with('display_group', $EINVView['group'])
                         ->with('display_coupon', $EINVView['coupon'])
                         ->with('display_points', $EINVView['points'])
                         ->with('display_earns', $EINVView['earnedPoints'])
                         ->with('toCustomer', $EINVView['toCustomer'])
                         ->with('downloadLink',$downloadLink)
                         ->with('htmlview',true);

            }

            if (strpos($loc, '/PO') !== false) {

                $trans = transaction::find($id);
                
                $poview = "";		
                $transactionDate = $trans->transaction_date;		
                

                $POView = self::createPOView($trans, $po_no);
                
                // Select type of Invoice
                
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                {
                    $poview = 'checkout.po_view_parent_v3';
                }
                elseif($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate <= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                {
                    $poview = 'checkout.po_view_parent_v2';
                }
                else
                {   
                    $poview = 'checkout.po_view_parent';
                }

                //endseller=0
                    return View::make($poview)
                        ->with('display_details', $POView['general'])
                        ->with('display_trans', $POView['trans'])
                        ->with('display_seller', $POView['seller'])
                        ->with('display_issuer', $POView['issuer'])
                        ->with('display_coupon', $POView['coupon'])
                        ->with('display_product', $POView['product'])
                        ->with('endSeller', $POView['endSeller'])
                        ->with('htmlview',true);

            }
            
            if (strpos($loc, '/CPO') !== false) {
                
                $trans = transaction::find($id);
                $invToCountry = strtolower($trans->delivery_country);
                
                switch ($invToCountry) {
                    case 'malaysia':
                        $poview = "";
                        $transactionDate = $trans->transaction_date;

                        $POView = self::createPOView($trans, $po_no);

                        if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){

                            $poview = 'checkout.po_view_parent_v2';

                        }   else{

                            $poview = 'checkout.po_view_parent';
                        }

                        //endseller=0
                            return View::make($poview)
                                ->with('display_details', $POView['general'])
                                ->with('display_trans', $POView['trans'])
                                ->with('display_seller', $POView['seller'])
                                ->with('display_issuer', $POView['issuer'])
                                ->with('display_coupon', $POView['coupon'])
                                ->with('display_product', $POView['product'])
                                ->with('endSeller', $POView['endSeller'])
                                ->with('htmlview',true);
                            
                        break;
                        
                    case 'china':

                        $INVView = self::createChinaInternationalPOView($trans);
                        return View::make('checkout.po_international_china')
                                ->with('trans', $trans)
                                ->with('invoiceInfo', $INVView['invoiceInfo'])
                                ->with('issuer', $INVView['issuer'])
                                ->with('invoiceTo', $INVView['invoiceTo'])
                                ->with('product',$INVView['product'])
                                ->with('totalDeclared', $INVView['totalDeclared'])
                                ->with('countryOrigin', $INVView['countryOrigin'])
                                ->with('ReasonSending', $INVView['ReasonSending'])
                                ->with('productItems', $INVView['productItems'])
                                ->with('alternativeAmount', $INVView['alternativeAmount'])
                                ->with('TotalBusinessCurrency', $INVView['TotalBusinessCurrency'])
                                ->with('TotalAlternativeCurrency', $INVView['TotalAlternativeCurrency'])
                                ->with('BusinessCurrency', $INVView['BusinessCurrency'])
                                ->with('AlternativeCurrency', $INVView['AlternativeCurrency'])
                                ->with('Remarks', $INVView['Remarks']);
                        break;

                }
            
            }

            if (strpos($loc, '/ePO') !== false) {

                $trans = transaction::find($id);
                	                		
                $transactionDate = $trans->transaction_date;		
                		
                // Select type of Invoice
                		
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                {
                    $epoview = 'checkout.po_view_v3';
                }
                elseif($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate <= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                {
                    $epoview = 'checkout.po_view_v2';
                }   
                elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) 
                {   
                    $epoview = 'checkout.po_view';
                }  
                else
                {   
                    $epoview = 'checkout.po_view';
                }

                $EPOView = self::createEPOView($trans, $epo_no);

                //endseller=1
                    return View::make($epoview)
                        ->with('display_details', $EPOView['general'])
                        ->with('display_trans', $EPOView['trans'])
                        ->with('display_seller', $EPOView['seller'])
                        ->with('display_issuer', $EPOView['issuer'])
                        ->with('display_product', $EPOView['product'])
                        ->with('endSeller', $EPOView['endSeller'])
                        ->with('htmlview',true);
            }
        }
}

    public function anyAgrofiles($loc = null)
    {
        
        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);

        $eINV_no = $id[1];
         
        $po_no = $id[1];

        $epo_no = $id[1];
        
        $id = $id[0];
        // print_r($loc);
        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {
            // echo "<script>window.close();</script>";
            if (strpos($loc, 'DO') !== false) {

                $trans = Transaction::find($id);

                if($trans->qr_code == ''){
                
                    include app_path('library/phpqrcode/qrlib.php');

                    $qrCode     = $trans->do_no;
                    $qrCodeFile = $trans->do_no.'.png';
                    // $path = 'images/qrcode/';

                    QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);

                    $trans->qr_code = $qrCodeFile;
                    $trans->save();
                    
                    $trans = Transaction::find($id);
                }

                $DOView = self::createDOView($trans);
                
                // echo "<pre>";
                // print_r( $DOView['product']);
                // echo "</pre>";

                return View::make('checkout.do_view')
                     ->with('display_details', $DOView['general'])
                     ->with('display_trans', $DOView['trans'])
                     ->with('display_seller', $DOView['paypal'])
                     ->with('display_product', $DOView['product'])
                     ->with('display_group', $DOView['group'])
                     ->with('delivery_type', $DOView['delivery_type'])
                     ->with('deliveryservice', $DOView['deliveryservice'])
                     ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                     ->with('htmlview',true);

            }

            if (strpos($loc, '/TMG') !== false) {

                $trans = Transaction::find($id);
                $invToCountry = strtolower($trans->delivery_country);
                
                if($trans->buyer_username == 'macrolink'){
                   $invToCountry = 'malaysia'; 
                }
                
                // echo $invToCountry;
                
                switch ($invToCountry) {
                    case 'malaysia':
                    case 'singapore':
                    case 'united states':
                    case 'canada':
                    case 'south korea':   
                    case 'indonesia':   
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            21004,
                            23143,
                            19959,
                            21003,
                            20057,
                            24276,
                            24275,
                            24279,
                            24286,
                            24287,
                            28176,
                            24288);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        $invoiceview = 'checkout.invoice_view_new_agro';
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        return View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('display_jcash', $INVView['jcashback'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('htmlview',true)
                                ->with('downloadLink',$downloadLink);
                                
                         break;
                    case 'china':
                        $transactionDate = $trans->transaction_date;
                        if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                            $invoiceview = 'checkout.invoice_international_china_v2';
                        }  else{
                            $invoiceview = 'checkout.invoice_international_china';
                        }
                        $INVView = self::createChinaInternationalInvView($trans);
                        return View::make($invoiceview)
                                ->with('trans', $trans)
                                ->with('invoiceInfo', $INVView['invoiceInfo'])
                                ->with('issuer', $INVView['issuer'])
                                ->with('invoiceTo', $INVView['invoiceTo'])
                                ->with('product',$INVView['product'])
                                ->with('totalDeclared', $INVView['totalDeclared'])
                                ->with('countryOrigin', $INVView['countryOrigin'])
                                ->with('ReasonSending', $INVView['ReasonSending'])
                                ->with('productItems', $INVView['productItems'])
                                ->with('alternativeAmount', $INVView['alternativeAmount'])
                                ->with('TotalBusinessCurrency', $INVView['TotalBusinessCurrency'])
                                ->with('TotalAlternativeCurrency', $INVView['TotalAlternativeCurrency'])
                                ->with('BusinessCurrency', $INVView['BusinessCurrency'])
                                ->with('AlternativeCurrency', $INVView['AlternativeCurrency'])
                                ->with('Remarks', $INVView['Remarks'])
                                ->with('TotalBusinessCurrencyDeliveryCharges', $INVView['TotalBusinessCurrencyDeliveryCharges'])
                                ->with('TotalAlternativeCurrencyDeliveryCharges', $INVView['TotalAlternativeCurrencyDeliveryCharges']);
                        break;
                    default:
                        break;
                        
                }

            }
            

            

            
            
            
            



        }
}


    public static function createDOView($trans){

    $d = new DNS1D();
    $d->setStorPath(__DIR__."/cache/");
    

    $paypal = TPayPal::where('transaction_id', '=', $trans->id)->first();

    $delivery_time = TDetails::where('transaction_id', '=', $trans->id)->orderBy('delivery_time', asc)->first();
    
    /* CHECK DO FOR DELIVERY SERVICE */
    $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
    if($DeliveryOrder->id > 0){
        $deliveryservice = true;
    }else{
        $deliveryservice = false;
    }
    
    $BarcodeData = str_replace('DO-','JCM',$trans->do_no); // will be DO Number without '-'
    
    if($trans->external_ref_number != ''){
        $extraMessage = " ".$trans->buyer_username.": ".$trans->external_ref_number;
    }else{
        $extraMessage = '';
    }
    
    $delivery_type = 'standard';
    $buyername = '';
    $buyername = $trans->buyer_username;
    
    $payment_id = 0;
    $general = [
            "do_no"               => $trans->do_no,
            //"do_date"             => date('Y-m-d H:i:s'),
            // "do_date" => date('d/m/Y'),
            "do_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "delivery_contact_no" => $trans->delivery_contact_no,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),
            "delivery_name"       => isset($trans->delivery_name) ? $trans->delivery_name : "",
            // "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
            // "buyer_email" => isset($buyer->email) ? $buyer->email : "",
            "delivery_area_type"  => ($trans->delivery_area_type != '' ? $trans->delivery_area_type : ''),
            "delivery_state_id"  => ($trans->delivery_state_id != '' ? $trans->delivery_state_id."," : ''),
            "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
            "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
            "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
            "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg.$extraMessage : "".$extraMessage ),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "qr_code" => $trans->qr_code,
            "delivery_time" => $delivery_time->delivery_time,
            "doprint_count" => $trans->doprint_count, 
             "barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG($BarcodeData, "C128A",2,50) . '" alt="barcode"   />', 

        ];

    $product = DB::table('jocom_transaction_details AS a')
            ->select('a.*', 'b.name','a.product_name as description')
            ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
            ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'a.transaction_id')
            //->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')
            ->leftJoin('logistic_transaction_item AS LTI', function($join)
                         {
                             $join->on('LTI.logistic_id', '=', 'LT.id');
                             $join->on('LTI.sku', '=', 'a.sku');
                         })
            ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
            ->where('a.transaction_id', '=', $trans->id)
            ->where('c.main', '=', '1')
            //->groupBy('a.sku')
            ->groupBy('a.id')
            ->orderBy('c.category_id')
            ->orderBy('b.name')
            //->where('a.product_group', '!=', '')
            ->get();
            

            
    // echo "<pre>";
    // print_r($product);
    // echo "</pre>";
    // die();
    
    if(isset($buyername) && $buyername == 'lazada'){
            $type = 'express';    
            $lazada = DB::table('jocom_lazada_order as JLO')
                            ->select('JLO.*')
                            ->leftjoin('jocom_lazada_order_items as JLOI','JLOI.order_id','=','JLO.id')
                            ->where('JLOI.shipping_provider_type','=',$type)
                            ->where('JLO.transaction_id','=',$trans->id)
                            ->get();

            if(count($lazada)>0){
                $delivery_type = 'express';

            }                                   

        } 

    $group = DB::table('jocom_transaction_details_group AS a')
            ->select('a.*', 'b.name')
            ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
            ->where('a.transaction_id', '=', $trans->id)
            ->orderBy('b.category')
            ->orderBy('b.name')
            ->get();

    $DeliveryOrderItems = array();
        if($deliveryservice){
                    
            $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
            $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
        }

    return array(
            'general'=>$general,
            'trans'=>$trans,
            'paypal'=>$paypal,
            'product'=>$product,
            'group'=>$group,
            'deliveryservice'=>$deliveryservice,
            "DeliveryOrderItems"=> $DeliveryOrderItems,
            "barcode"=>$barcode,
            "delivery_type"=>$delivery_type,
        );
               
}

public static function createPLView($trans){

   

    $delivery_time = TDetails::where('transaction_id', '=', $trans->id)->orderBy('delivery_time', asc)->first();
    
    /* CHECK DO FOR DELIVERY SERVICE */
    $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
    if($DeliveryOrder->id > 0){
        $deliveryservice = true;
    }else{
        $deliveryservice = false;
    }
        
    if($trans->external_ref_number != ''){
        $extraMessage = " ".$trans->buyer_username.": ".$trans->external_ref_number;
    }else{
        $extraMessage = '';
    }
    
    $delivery_type = 'standard';
    $buyername = '';
    $buyername = $trans->buyer_username;
    
    $payment_id = 0;
    $general = [
            "invoice_no"          => $trans->invoice_no,
            "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
            "do_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "delivery_contact_no" => $trans->delivery_contact_no,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),
            "delivery_name"       => isset($trans->delivery_name) ? $trans->delivery_name : "",
            // "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
            // "buyer_email" => isset($buyer->email) ? $buyer->email : "",
            "delivery_area_type"  => ($trans->delivery_area_type != '' ? $trans->delivery_area_type : ''),
            "delivery_state_id"  => ($trans->delivery_state_id != '' ? $trans->delivery_state_id."," : ''),
            "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
            "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
            "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
            "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg.$extraMessage : "".$extraMessage ),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "qr_code" => $trans->qr_code,
            "delivery_time" => $delivery_time->delivery_time,
            "doprint_count" => $trans->doprint_count, 
             // "barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG($BarcodeData, "C128A",2,50) . '" alt="barcode"   />', 

        ];

    $product = DB::table('jocom_transaction_purchase_history AS PH')->select(DB::raw('GROUP_CONCAT(DISTINCT(PH.ref_no)  SEPARATOR " , ") as refno'),DB::raw('DATE_FORMAT(PH.date_of_purchase, "%d-%b-%Y") as date_of_purchase'), 'PH.seller_company', 'PH.price as costprice', 'a.price as Sellingprice', DB::raw('SUM(a.total) as total'), DB::raw('SUM(PH.amount) as amount'))
            ->leftJoin('jocom_transaction_details AS a', 'PH.transaction_id', '=', 'a.transaction_id')
            ->where('PH.transaction_id', '=', $trans->id)
            ->groupBy('PH.seller_id')
             ->get();
            



    $DeliveryOrderItems = array();
        if($deliveryservice){
                    
            $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
            $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
        }

    return array(
            'general'=>$general,
            'trans'=>$trans,
            'product'=>$product,
        );
               
}

public static function createINVView($trans,$is_external_customer = false){

    $issuer = [
            "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
            "issuer_address_1" => "10, Jalan Str 1,",
            "issuer_address_2" => "Saujana Teknologi Park,",
            "issuer_address_3" => "Rawang,",
            "issuer_address_4" => "48000 Rawang, Selangor, Malaysia",
            "issuer_tel"       => "Tel: +603 6734 8744",
            "issuer_gst"       => "",
        ];
    $id = $trans->id;
    $buyer = Customer::where('username', '=', $trans->buyer_username)->first();
    $buyer_username = $buyer->username;
    $buyer_type = $buyer->type;
    $payment_id   = 0;
    $toCustomer = 1 ;
    
    $buyerUsername = strtolower($trans->buyer_username);
    switch ($buyerUsername) {
        case '11street':
    $ElevenStreet =  ElevenStreetOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $ElevenStreet->order_number;
            break;
        case 'prestomall':
            $ElevenStreet =  ElevenStreetOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $ElevenStreet->order_number;
            break;
        case 'lazada':
            $LazadaOrder = LazadaOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $LazadaOrder->order_number;
            break;
        case 'shopee':
            $ShopeeOrder =  ShopeeOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $ShopeeOrder->order_number;
            break;
        case 'pgmall':
            $PGMallOrder =  PGMallOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $PGMallOrder->order_number;
            break;
        case 'qoo10':
            $QootenOrder =  QootenOrder::where('transaction_id', '=', $id)->first();  
            $referenceNumber = $QootenOrder->order_number;
            break;

        default:
            $referenceNumber = '';
            break;
    }
    
    $TransactionInvoiceAddress = TransactionInvoiceAddress::where("transaction_id",$trans->id)->first();
    if($TransactionInvoiceAddress){

        $delivery_address_1 = ($TransactionInvoiceAddress->invoice_address_1 != '' ? $TransactionInvoiceAddress->invoice_address_1."," : '');
        $delivery_address_2 = ($TransactionInvoiceAddress->invoice_address_2 != '' ? $TransactionInvoiceAddress->invoice_address_2."," : '');
        $delivery_address_3 = ($TransactionInvoiceAddress->invoice_postcode != '' ? $TransactionInvoiceAddress->invoice_postcode." " : '').($TransactionInvoiceAddress->invoice_city != '' ? $TransactionInvoiceAddress->invoice_city."," : '');
        $delivery_address_4 = ($TransactionInvoiceAddress->invoice_state != '' ? $TransactionInvoiceAddress->invoice_state.", " : '').$TransactionInvoiceAddress->invoice_country.".";

    }else{

        $delivery_address_1 = ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : '');
        $delivery_address_2 = ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : '');
        $delivery_address_3 = ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : '');
        $delivery_address_4 = ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".";

    }
     

    if($buyer->type == 'corporate'){
        
        if($is_external_customer){
            
        $general = [
            "invoice_no"          => $trans->invoice_no,
            "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
            // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),
            "buyer_name"          => isset($trans->delivery_name) ? $trans->delivery_name : "",
            "buyer_email"         =>  "",
            "delivery_address_1"  => $delivery_address_1,
            "delivery_address_2"  => $delivery_address_2,
            "delivery_address_3"  => $delivery_address_3,
            "delivery_address_4"  => $delivery_address_4,
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "elevenstr_order_no"  => $referenceNumber,  
            ];

        }else{
            
            $general = [
                "invoice_no"          => $trans->invoice_no,
                "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
                        // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                "payment_terms"       => "cash/cc",
                "transaction_id"      => $trans->id,
                "payment_id"          => $payment_id,
                "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),
    
                "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
                "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
                "delivery_address_1"  => $delivery_address_1,
                "delivery_address_2"  => $delivery_address_2,
                "delivery_address_3"  => $delivery_address_3,
                "delivery_address_4"  => $delivery_address_4,
                "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                "delivery_contact_no" => $trans->delivery_contact_no,
                "elevenstr_order_no"  => ($ElevenStreet->order_number !="" ? $ElevenStreet->order_number : "None"), 
            ];
        
        }
        
    }else{
        $general = [
            "invoice_no"          => $trans->invoice_no,
            "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
                    // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

            "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
            // "delivery_name"       => isset($trans->delivery_name) ? $trans->delivery_name : "",
            "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
            "delivery_address_1"  => $delivery_address_1,
            "delivery_address_2"  => $delivery_address_2,
            "delivery_address_3"  => $delivery_address_3,
            "delivery_address_4"  => $delivery_address_4,
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "elevenstr_order_no"  => ($ElevenStreet->order_number !="" ? $ElevenStreet->order_number : "None"), 
        ];
    }


    $sellerTable = DB::table('jocom_seller')
                ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                ->where('id', '=', $parentSeller)
                ->first();

    $paypal = TPayPal::where('transaction_id', '=', $id)->first();
    $coupon = TCoupon::where('transaction_id', '=', $id)->first();

    // $product = DB::table('jocom_transaction_details AS a')
    //         ->select('a.*', 'b.name','LTI.name as description')
    //         ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
    //          ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'a.transaction_id')
    //         ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')
    //         ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
    //         ->where('a.transaction_id', '=', $trans->id)
    //         ->where('c.main', '=', '1')
    //         //->groupBy('a.sku')
    //         ->orderBy('c.category_id')
    //         ->orderBy('b.name')
    //         ->get();
            
            
      $product = DB::table('jocom_transaction_details AS a')
            ->select('a.*', 'b.name','b.gst','LTI.name as description')
            ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
            ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'a.transaction_id')
            //->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')
            ->leftJoin('logistic_transaction_item AS LTI', function($join)
                         {
                             $join->on('LTI.logistic_id', '=', 'LT.id');
                             $join->on('LTI.sku', '=', 'a.sku');
                         })
            ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
            ->where('a.transaction_id', '=', $trans->id)
            ->where('c.main', '=', '1')
            //->groupBy('a.sku')
             ->groupBy('a.id')
            ->orderBy('c.category_id')
            ->orderBy('b.name')
            //->where('a.product_group', '!=', '')
            ->get();
    // die();


    $group = DB::table('jocom_transaction_details_group AS a')
            ->select('a.*', 'b.name')
            ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
            ->where('a.transaction_id', '=', $trans->id)
            ->orderBy('b.category')
            ->orderBy('b.name')
            ->get();

    $points = TPoint::where("transaction_id",$trans->id)->get();

    $BcardM = BcardM::where("username","=",$buyer_username)->first();      
    if(count($BcardM)> 0){
        $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*','point_types.id AS point_type_id')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_users.status', '=', 1)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();
    }else{
        $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*','point_types.id AS point_type_id')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_users.status', '=', 1)
                ->where('point_types.id', '!=', PointType::BCARD)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();
    }
    
    $jcashback = DB::table('jocom_jcashback_transactiondetails')
                        ->where("transaction_id",$trans->id)
                        ->where("status",'=',1)
                        ->first();

    return array(
                'general'=>$general,
                'trans'=>$trans,
                'issuer'=>$issuer,
                'paypal'=>$paypal,
                'coupon'=>$coupon,
                'product'=>$product,
                'buyer_type'=>$buyer_type,
                'group'=>$group,
                'points'=>$points,
                'earnedPoints'=>$earnedPoints,
                'toCustomer'=>$toCustomer,
                'jcashback'=>$jcashback,
                'invoice_bussines_currency'=>$trans->invoice_bussines_currency,
                'invoice_bussines_currency_rate'=>$trans->invoice_bussines_currency_rate,
                'standard_currency'=>$trans->standard_currency,
                'standard_currency_rate'=>$trans->standard_currency_rate
            );
               
}

/*
     * This Invoice Template is for china international invoice
     * 
     */
    public static function createChinaInternationalInvView($trans){
        
        try{
            
            $id = $trans->id;
            
            $productItems = array();
            $alternativeAmount = 0;

            $TransInfo =   DB::table('jocom_transaction AS JT')
                    ->select('JT.id','JT.invoice_no','JT.invoice_date','JT.buyer_username','JT.delivery_name','JT.delivery_contact_no','JT.special_msg','JT.buyer_email','JT.buyer_email'
                            ,'JT.delivery_addr_1','JT.delivery_addr_2','JT.delivery_postcode','JT.delivery_city','JT.delivery_state','JT.delivery_country',
                            'JT.invoice_bussines_currency','JT.invoice_bussines_currency_rate'
                            ,'JT.standard_currency','JT.standard_currency_rate'
                            ,'JT.base_currency','JT.base_currency_rate'
                            ,'JT.foreign_country_currency','JT.foreign_country_currency_rate','JT.delivery_charges','foreign_delivery_charges')
                    ->where('JT.id', '=', $id)
                    ->first();
            
            $invoiceInfo = array(
                "invoice_no" => $TransInfo->invoice_no,
                "invoice_date" => $TransInfo->invoice_date,
                "invoice_transaction_id" => $TransInfo->id,
            );

            $issuer = [
                    "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
                    "issuer_address_1" => "10, Jalan Str 1,",
                    "issuer_address_2" => "Saujana Teknologi Park,",
                    "issuer_address_3" => "Rawang,",
                    "issuer_address_4" => "48000 Rawang, Selangor, Malaysia",
                    "issuer_contact_name"  => "Mr. Joshua Sew",
                    "issuer_tel"       => "Tel : +603 6734 8744",
                    "issuer_id"       => "",
                    "issuer_gst"       => "",
                ];

            $Customer = Customer::where("username",$TransInfo->buyer_username)->first();

            $invoiceTo = [
                    "invoice_to_name"      => $Customer->full_name,
                    "invoice_to_address_1" => $Customer->address1,
                    "invoice_to_address_2" => '',
                    "invoice_to_address_3" => $Customer->postcode." ".$Customer->city." ".$Customer->state.", ".$Customer->country,
                    "invoice_to_tel"       => $Customer->mobile_no,
                    "invoice_to_recipient" => $TransInfo->delivery_name,
                    "invoice_to_serial_id" => $TransInfo->id,
                ];

            $productInfo =   DB::table('jocom_transaction_details AS JTD')
                    ->select('JTD.foreign_price','JTD.foreign_actual_price','JTD.product_name', 'JTD.price_label', 'JTD.sku', 'JTD.price', 'JTD.unit', 'JTD.total_weight', 'JTD.total_weight','JPP.p_weight',DB::raw(' ROUND((JTD.total_weight / 1000), 2) as TotalWeight'))
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'JTD.p_option_id')
                    ->where('JTD.transaction_id', '=', $id)
                    ->get();

            $countryInfo =  DB::table('jocom_countries AS JC')->where("JC.name",$TransInfo->delivery_country)->first();

            $businessCurrency = $TransInfo->invoice_bussines_currency;
            $baseCurrency = $TransInfo->base_currency;
            
            
            $TotalBusinessCurrency = 0.00;
            $TotalAlternativeCurrency = 0.00;

            foreach ($productInfo as $key => $value) {

                $subLine = array(
                    "product_name" => $value->product_name,
                    "product_label" => $value->price_label,
                    "product_sku" => $value->sku,
                    "product_weight" => round($value->total_weight / 1000, 2),
                    "product_piece" => $value->unit,
                    "product_quantity" => $value->unit,
                    
                    //"product_unit_price" => self::GetConvertedRate('MYR' ,$businessCurrency, $value->price, $TransInfo->invoice_bussines_currency_rate ),
                    "product_actual_unit_price" => self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_actual_price, $TransInfo->invoice_bussines_currency_rate ),
                    "product_actual_alternative_unit_price" => self::GetConvertedRate($businessCurrency, 'MYR', $value->foreign_actual_price, $TransInfo->base_currency_rate ),
                    
                    "product_unit_price" => self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_price, $TransInfo->invoice_bussines_currency_rate ),
                    "product_alternative_unit_price" => self::GetConvertedRate($businessCurrency, 'MYR', number_format($value->foreign_price, 2, '.', ''), $TransInfo->base_currency_rate ),
                    
                    "product_total_price" =>  self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_price, $TransInfo->invoice_bussines_currency_rate ) * $value->unit,
                    "product_alternative_total_price" => number_format(self::GetConvertedRate($businessCurrency, 'MYR', number_format($value->foreign_price, 2, '.', ''), $TransInfo->base_currency_rate ), 2, '.', '') * $value->unit,
                    //"product_alternative_total_price" => self::GetConvertedRate($businessCurrency, 'MYR', $value->foreign_price, $TransInfo->base_currency_rate ) * $value->unit,
                );
               
                array_push($productItems, $subLine);
                
                $TotalBusinessCurrency = $TotalBusinessCurrency +  $subLine['product_total_price'];
                $TotalAlternativeCurrency = $TotalAlternativeCurrency +  $subLine['product_alternative_total_price'];
                
            }
            
            // Delivery Charges 
            $TotalBusinessCurrencyDeliveryCharges = $TransInfo->foreign_delivery_charges;
           
            $TotalAlternativeCurrencyDeliveryCharges = self::GetConvertedRate($businessCurrency ,$baseCurrency, $TransInfo->foreign_delivery_charges, $TransInfo->base_currency_rate );
            // Delivery Charges 


            $result = array(
                    'invoiceInfo' => $invoiceInfo,
                    'issuer' => $issuer,
                    'invoiceTo' => $invoiceTo,
                    'product' => $productInfo,
                    'totalDeclared' => 0.00,
                    'countryOrigin' => 'Malaysia',
                    'ReasonSending' => 1,
                    'productItems' => $productItems,
                    'alternativeAmount' => $alternativeAmount,
                    'TotalBusinessCurrency' => $TotalBusinessCurrency,
                    'TotalAlternativeCurrency' => $TotalAlternativeCurrency,
                    'BusinessCurrency' => $businessCurrency,
                    'AlternativeCurrency' => $baseCurrency,
                    'TotalBusinessCurrencyDeliveryCharges' => $TotalBusinessCurrencyDeliveryCharges,
                    'TotalAlternativeCurrencyDeliveryCharges' => $TotalAlternativeCurrencyDeliveryCharges,
                    'Remarks' => '',
                );
         
            return $result;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die();
        }
       

    }
    
    public static function createChinaInternationalBuydayInvView($trans){
        
        try{
            
            $id = $trans->id;
            
            $productItems = array();
            $alternativeAmount = 0;

            $TransInfo =   DB::table('jocom_transaction AS JT')
                    ->select('JT.id','JT.invoice_no','JT.invoice_date','JT.buyer_username','JT.delivery_name','JT.delivery_contact_no','JT.delivery_identity_number','JT.special_msg','JT.buyer_email','JT.buyer_email'
                            ,'JT.delivery_charges','JT.delivery_addr_1','JT.delivery_addr_2','JT.delivery_postcode','JT.delivery_city','JT.delivery_state','JT.delivery_country',
                            'JT.invoice_bussines_currency','JT.invoice_bussines_currency_rate','JT.foreign_invoice_no'
                            ,'JT.standard_currency','JT.standard_currency_rate'
                            ,'JT.base_currency','JT.base_currency_rate'
                            ,'JT.foreign_country_currency','JT.foreign_country_currency_rate','foreign_delivery_charges')
                    ->where('JT.id', '=', $id)
                    ->first();
            
//            echo "<pre>";
//            print_r($TransInfo);
//            echo "</pre>";
//            
            $invoiceInfo = array(
                "invoice_no" => $TransInfo->foreign_invoice_no,
                "invoice_date" => $TransInfo->invoice_date,
                "invoice_transaction_id" => $TransInfo->id,
            );

            $issuer = [
                    "issuer_name"      => "杭州天购科技有限公司",
                    "issuer_address_1" => "浙江省杭州市萧山区经济技术开发区金一路37号A101-5室",
                    "issuer_address_2" => "",
                    "issuer_address_3" => "Hangzhou,",
                    "issuer_address_4" => "Zhejiang Sheng, China",
                    "issuer_contact_name"  => "",
                    "issuer_tel"       => "+603-2241 6637",
                    "issuer_id"       => "",
                    "issuer_gst"       => "",
                ];

            $Customer = Customer::where("username",$TransInfo->buyer_username)->first();

            $invoiceTo = [
                    "invoice_to_name"      => $TransInfo->delivery_name,
                    "invoice_to_address_1" => $TransInfo->delivery_addr_1,
                    "invoice_to_address_2" => $TransInfo->delivery_addr_2,
                    "invoice_to_address_3" => $TransInfo->postcode." ".$TransInfo->city." ".$TransInfo->state.", ".$TransInfo->country,
                    "invoice_to_tel"       => $TransInfo->delivery_contact_no,
                    "invoice_to_recipient" => $TransInfo->delivery_name,
                    "invoice_to_serial_id" => $TransInfo->delivery_identity_number,
                ];

            $productInfo =   DB::table('jocom_transaction_details AS JTD')
                    ->select('JTD.foreign_price','JTD.foreign_actual_price','JTD.product_name', 'JTD.price_label', 'JTD.sku', 'JTD.price', 'JTD.unit', 'JTD.total_weight', 'JTD.total_weight','JPP.p_weight',DB::raw(' ROUND((JTD.total_weight / 1000), 2) as TotalWeight'))
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'JTD.p_option_id')
                    ->where('JTD.transaction_id', '=', $id)
                    ->get();

            $countryInfo =  DB::table('jocom_countries AS JC')->where("JC.name",$TransInfo->delivery_country)->first();

            $businessCurrency = $TransInfo->invoice_bussines_currency;
            $baseCurrency = $TransInfo->base_currency;
            $ForeignCurrency = $TransInfo->foreign_country_currency;
            
            
            $TotalBusinessCurrency = 0.00;
            $TotalAlternativeCurrency = 0.00;

            foreach ($productInfo as $key => $value) {

                $subLine = array(
                    "product_name" => $value->product_name,
                    "product_label" => $value->price_label,
                    "product_sku" => $value->sku,
                    "product_weight" => round($value->total_weight / 1000, 2),
                    "product_piece" => $value->unit,
                    "product_quantity" => $value->unit,
                    
                    "product_unit_price" => self::GetConvertedRate($businessCurrency ,$ForeignCurrency, $value->foreign_price, $TransInfo->foreign_country_currency_rate ),
                    "product_alternative_unit_price" => self::GetConvertedRate($businessCurrency, $businessCurrency, $value->foreign_price, $TransInfo->standard_currency_rate ),
                    
                    "product_total_price" =>  self::GetConvertedRate($businessCurrency ,$ForeignCurrency, $value->foreign_price, $TransInfo->foreign_country_currency_rate ) * $value->unit,
                    "product_alternative_total_price" => self::GetConvertedRate($businessCurrency, $businessCurrency, $value->foreign_price, $TransInfo->standard_currency_rate ) * $value->unit,
                );
               
                array_push($productItems, $subLine);
                
                $TotalBusinessCurrency = $TotalBusinessCurrency +  $subLine['product_total_price'];
                $TotalAlternativeCurrency = $TotalAlternativeCurrency +  $subLine['product_alternative_total_price'];
                
            }
            
            // Delivery Charges 
            $TotalBusinessCurrencyDeliveryCharges = self::GetConvertedRate($businessCurrency ,$ForeignCurrency, $TransInfo->foreign_delivery_charges, $TransInfo->foreign_country_currency_rate );
            $TotalAlternativeCurrencyDeliveryCharges = self::GetConvertedRate($businessCurrency ,$ForeignCurrency, $TransInfo->foreign_delivery_charges, $TransInfo->standard_currency_rate );
            // Delivery Charges 

            $result = array(
                    'invoiceInfo' => $invoiceInfo,
                    'issuer' => $issuer,
                    'invoiceTo' => $invoiceTo,
                    'product' => $productInfo,
                    'totalDeclared' => 0.00,
                    'countryOrigin' => 'Malaysia',
                    'ReasonSending' => 1,
                    'productItems' => $productItems,
                    'alternativeAmount' => $alternativeAmount,
                    'TotalBusinessCurrency' => $TotalBusinessCurrency,
                    'TotalAlternativeCurrency' => $TotalAlternativeCurrency,
                    'BusinessCurrency' => $ForeignCurrency,
                    'AlternativeCurrency' => $businessCurrency,
                    'TotalBusinessCurrencyDeliveryCharges' => $TotalBusinessCurrencyDeliveryCharges,
                    'TotalAlternativeCurrencyDeliveryCharges' => $TotalAlternativeCurrencyDeliveryCharges,
                    'Remarks' => '',
                );
            
            return $result;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die();
        }
       

    }
    
    public static function createChinaInternationalPOView($trans){
        
        try{
            
            $id = $trans->id;
            
            $productItems = array();
            $alternativeAmount = 0;

            $TransInfo =   DB::table('jocom_transaction AS JT')
                    ->select('JT.id','JT.invoice_no','JT.invoice_date','JT.buyer_username','JT.delivery_name','JT.delivery_contact_no','JT.special_msg','JT.buyer_email','JT.buyer_email'
                            ,'JT.delivery_addr_1','JT.delivery_addr_2','JT.delivery_postcode','JT.delivery_city','JT.delivery_state','JT.delivery_country',
                            'JT.invoice_bussines_currency','JT.invoice_bussines_currency_rate'
                            ,'JT.standard_currency','JT.standard_currency_rate'
                            ,'JT.base_currency','JT.base_currency_rate'
                            ,'JT.foreign_country_currency','JT.foreign_country_currency_rate','JT.delivery_charges')
                    ->where('JT.id', '=', $id)
                    ->first();
            
            $invoiceInfo = array(
                "invoice_no" => $TransInfo->invoice_no,
                "invoice_date" => $TransInfo->invoice_date,
                "invoice_transaction_id" => $TransInfo->id,
            );

            $issuer = [
                    "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
                    "issuer_address_1" => "10, Jalan Str 1,",
                    "issuer_address_2" => "Saujana Teknologi Park,",
                    "issuer_address_3" => "Rawang,",
                    "issuer_address_4" => "48000 Rawang, Selangor, Malaysia",
                    "issuer_contact_name"  => "Mr. Brian",
                    "issuer_tel"       => "Tel : +603-6734 8744",
                    "issuer_id"       => "",
                    "issuer_gst"       => "",
                ];
            
            $Customer =Customer::where("username",$TransInfo->buyer_username)->first();

            $invoiceTo = [
                    "invoice_to_name"      => $Customer->full_name,
                    "invoice_to_address_1" => $Customer->address1,
                    "invoice_to_address_2" => '',
                    "invoice_to_address_3" => $Customer->postcode." ".$Customer->city." ".$Customer->state.", ".$Customer->country,
                    "invoice_to_tel"       => $Customer->mobile_no,
                    "invoice_to_recipient" => $TransInfo->delivery_name,
                    "invoice_to_serial_id" => "",
                ];

            $productInfo =   DB::table('jocom_transaction_details AS JTD')
                    ->select('JTD.foreign_price','JTD.foreign_actual_price','JTD.product_name', 'JTD.price_label', 'JTD.sku', 'JTD.price', 'JTD.unit', 'JTD.total_weight', 'JTD.total_weight','JPP.p_weight',DB::raw(' ROUND((JTD.total_weight / 1000), 2) as TotalWeight'))
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'JTD.p_option_id')
                    ->where('JTD.transaction_id', '=', $id)
                    ->get();

            $countryInfo =  DB::table('jocom_countries AS JC')->where("JC.name",$TransInfo->delivery_country)->first();

            $businessCurrency = $TransInfo->invoice_bussines_currency;
            $baseCurrency = $TransInfo->base_currency;
            
            
            $TotalBusinessCurrency = 0.00;
            $TotalAlternativeCurrency = 0.00;

            foreach ($productInfo as $key => $value) {

                $subLine = array(
                    "product_name" => $value->product_name,
                    "product_label" => $value->price_label,
                    "product_sku" => $value->sku,
                    "product_weight" => round($value->total_weight / 1000, 2),
                    "product_piece" => $value->unit,
                    "product_quantity" => $value->unit,
                    // GetConvertedRate($from_currency ,$to_currency, $amount, $rate)
                    "product_actual_unit_price" => self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_actual_price, $TransInfo->invoice_bussines_currency_rate ),
                    "product_actual_alternative_unit_price" => self::GetConvertedRate($businessCurrency, 'MYR', $value->foreign_actual_price, $TransInfo->base_currency_rate ),
                    
                    "product_unit_price" => self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_price, $TransInfo->invoice_bussines_currency_rate ),
                    "product_alternative_unit_price" => self::GetConvertedRate($businessCurrency, 'MYR', $value->foreign_price, $TransInfo->base_currency_rate ),
                    
                    "product_total_price" =>  self::GetConvertedRate($businessCurrency ,$businessCurrency, $value->foreign_price, $TransInfo->invoice_bussines_currency_rate ) * $value->unit,
                    "product_alternative_total_price" => self::GetConvertedRate($businessCurrency, 'MYR', $value->foreign_price, $TransInfo->base_currency_rate ) * $value->unit,
           
                );
               
                array_push($productItems, $subLine);
                
                $TotalBusinessCurrency = $TotalBusinessCurrency +  $subLine['product_total_price'];
                $TotalAlternativeCurrency = $TotalAlternativeCurrency +  $subLine['product_alternative_total_price'];
                
            }
            
            // Delivery Charges 
            $TotalBusinessCurrencyDeliveryCharges = self::GetConvertedRate($businessCurrency ,$baseCurrency, $TransInfo->delivery_charges, $TransInfo->invoice_bussines_currency_rate );
           
            $TotalAlternativeCurrencyDeliveryCharges = self::GetConvertedRate($businessCurrency ,$baseCurrency, $TransInfo->delivery_charges, $TransInfo->base_currency_rate );
            // Delivery Charges 


            $result = array(
                    'invoiceInfo' => $invoiceInfo,
                    'issuer' => $issuer,
                    'invoiceTo' => $invoiceTo,
                    'product' => $productInfo,
                    'totalDeclared' => 0.00,
                    'countryOrigin' => 'Malaysia',
                    'ReasonSending' => 1,
                    'productItems' => $productItems,
                    'alternativeAmount' => $alternativeAmount,
                    'TotalBusinessCurrency' => $TotalBusinessCurrency,
                    'TotalAlternativeCurrency' => $TotalAlternativeCurrency,
                    'BusinessCurrency' => $businessCurrency,
                    'AlternativeCurrency' => $baseCurrency,
                    'TotalBusinessCurrencyDeliveryCharges' => $TotalBusinessCurrencyDeliveryCharges,
                    'TotalAlternativeCurrencyDeliveryCharges' => $TotalAlternativeCurrencyDeliveryCharges,
                    'Remarks' => '',
                );


            return $result;
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die();
        }
       

    }
    
    /*
     * This Invoice Template is for china international invoice
     * 
     */

public static function createQoo10INVView($trans){

    $issuer = [
            // "issuer_name"      => "Jocom MShopping Sdn. Bhd.",
            // "issuer_address_1" => "Unit 9-1, Level 9,",
            // "issuer_address_2" => "Tower 3, Avenue 3, Bangsar South,",
            // "issuer_address_3" => "No. 8, Jalan Kerinchi,",
            // "issuer_address_4" => "59200 Kuala Lumpur.",
            // "issuer_tel"       => "Tel: 03-2241 6637 Fax: 03-2242 3837",
            // "issuer_gst"       => "001077620736",
            
            "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
            "issuer_address_1" => "10, Jalan Str 1,",
            "issuer_address_2" => "Saujana Teknologi Park,",
            "issuer_address_3" => "Rawang,",
            "issuer_address_4" => "48000 Rawang, Selangor, Malaysia",
            "issuer_tel"       => "Tel: +603 6734 8744",
            "issuer_gst"       => "",
        ];
    $id = $trans->id;
    $buyer = Customer::where('username', '=', $trans->buyer_username)->first();
    $buyer_username = $buyer->username;
    $buyer_type = $buyer->type;
    $payment_id   = 0;
    $toCustomer = 1 ;
    
    $ElevenStreet =  ElevenStreetOrder::where('transaction_id', '=', $id)->first();  
    
    if($buyer->type == 'corporate'){
    $general = [
            "invoice_no"          => $trans->invoice_no,
            "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
                    // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

            "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
            "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
            "delivery_address_1"  => ($buyer->address1 != '' ? $buyer->address1."," : ''),
            "delivery_address_2"  => ($buyer->address2 != '' ? $buyer->address2."," : ''),
            "delivery_address_3"  => ($buyer->postcode != '' ? $buyer->postcode." " : '').($buyer->city != '' ? $buyer->city."," : ''),
            "delivery_address_4"  => ($buyer->state != '' ? $buyer->state.", " : '').$buyer->country.".",
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "elevenstr_order_no"  => ($ElevenStreet->order_number !="" ? $ElevenStreet->order_number : "None"),  
        ];
        
    }else{
        $general = [
            "invoice_no"          => $trans->invoice_no,
            "invoice_date"        => $trans->invoice_date == "" ? date("d-m-Y") : date("d/m/Y", strtotime($trans->invoice_date)),
                    // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
            "payment_terms"       => "cash/cc",
            "transaction_id"      => $trans->id,
            "payment_id"          => $payment_id,
            "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

            "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
            "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
            "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
            "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
            "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
            "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
            "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            "delivery_contact_no" => $trans->delivery_contact_no,
            "elevenstr_order_no"  => ($ElevenStreet->order_number !="" ? $ElevenStreet->order_number : "None"),  
        ];
    }


    $sellerTable = DB::table('jocom_seller')
                ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                ->where('id', '=', $parentSeller)
                ->first();

    $paypal = TPayPal::where('transaction_id', '=', $id)->first();
    $coupon = TCoupon::where('transaction_id', '=', $id)->first();

    $product = DB::table('jocom_transaction_details_qoo10 AS a')
            ->select('a.*', 'd.name')
            ->leftJoin('logistic_transaction_item AS d', 'd.product_id', '=', 'a.product_id')
            ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
            ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
            ->where('a.transaction_id', '=', $trans->id)
            ->where('c.main', '=', '1')
            //->groupBy('a.sku')
            ->orderBy('c.category_id')
            ->orderBy('b.name')
            ->groupBy('a.p_option_id')
            ->get(); 

    $group = DB::table('jocom_transaction_details_group AS a')
            ->select('a.*', 'b.name')
            ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
            ->where('a.transaction_id', '=', $trans->id)
            ->orderBy('b.category')
            ->orderBy('b.name')
            ->get();

    $points = TPoint::transaction($trans->id)->get();
    
    $BcardM = BcardM::where("username","=",$buyer_username)->first();      
    if(count($BcardM)> 0){
        $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*','point_types.id AS point_type_id')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_users.status', '=', 1)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();
    }else{
        $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*','point_types.id AS point_type_id')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_users.status', '=', 1)
                ->where('point_types.id', '!=', PointType::BCARD)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();
    }
    
    $Cart_Discount_Seller = $qoo10->Cart_Discount_Seller;
    $Cart_Discount_Qoo10  = $qoo10->Cart_Discount_Qoo10;

    return array(
                'general'=>$general,
                'trans'=>$trans,
                'issuer'=>$issuer,
                'paypal'=>$paypal,
                'coupon'=>$coupon,
                'product'=>$product,
                'group'=>$group,
                'points'=>$points,
                'earnedPoints'=>$earnedPoints,
                'toCustomer'=>$toCustomer,
                'Cart_Discount_Seller'=>$Cart_Discount_Seller,
                'Cart_Discount_Qoo10'=>$Cart_Discount_Qoo10,
            );
               
}

public static function createEINVView($trans,$eINV_no){


    $involved_parent = DB::table('jocom_transaction_details')
                    ->where('transaction_id', '=', $trans->id)
                    ->where('parent_seller', '!=', 0)
                    ->groupBy('parent_seller')
                    ->lists('parent_seller');

     foreach ($involved_parent as $parentSeller) {
                    // $sellerPO = MCheckout::processINV($trans, $general, $paypal, $coupon, $parentSeller, 0);
                }

    $sellerTable = DB::table('jocom_seller')
                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                    ->where('id', '=', $parentSeller)
                    ->first();

    $tempcountry = "";
    $tempstate   = "";
    $tempcity    = "";

    $sellerCountry = Country::find($sellerTable->country);
    
    if ($sellerCountry != null) {
        $tempcountry = $sellerCountry->name;
    }

    $sellerState = State::find($sellerTable->state);
    if ($sellerState != null) {
        $tempstate = $sellerState->name.", ";
    }

    if (is_numeric($sellerTable->city)) {
        $city_row = City::find($sellerTable->city);

        if (count($city_row) > 0) {
            $tempcity = $city_row->name;
        }

    }else{
        $tempcity = $sellerTable->city;
    }
    $id = $trans->id;
    $issuer = [
                "issuer_name"      => $sellerTable->company_name,
                "issuer_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "issuer_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "issuer_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "issuer_address_4" => $tempstate.$tempcountry.".",
                "issuer_tel"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                "issuer_gst"       => $sellerTable->gst_reg_num,
            ];

    $ElevenStreet =  ElevenStreetOrder::where('transaction_id', '=', $id)->first();  

    $buyer = Customer::where('username', '=', $trans->buyer_username)->first();
    $payment_id   = 0;
    $general = [
                "invoice_no"          => $eINV_no,
                //"invoice_date"        => date('d/m/Y'),
                "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                "payment_terms"       => "cash/cc",
                "transaction_id"      => $trans->id,
                "payment_id"          => $payment_id,
                "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

                "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
                "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
                "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                "delivery_contact_no" => $trans->delivery_contact_no,
                "elevenstr_order_no"  => ($ElevenStreet->order_number !="" ? $ElevenStreet->order_number : "None"),  
            ];     

    $paypal = TPayPal::where('transaction_id', '=', $id)->first();

    $coupon = TCoupon::where('transaction_id', '=', $id)->first();


    $product = DB::table('jocom_transaction_details AS a')
            ->select('a.*', 'b.name','LTI.name as description')
            ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
            ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'a.transaction_id')
            ->leftJoin('logistic_transaction_item AS LTI', function($join)
                         {
                             $join->on('LTI.logistic_id', '=', 'LT.id');
                             $join->on('LTI.sku', '=', 'a.sku');
                         })
            ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
            ->where('a.transaction_id', '=', $trans->id)
            ->where('c.main', '=', '1')
             ->groupBy('a.id')
            ->orderBy('c.category_id')
            ->orderBy('b.name')
            ->get();

    $group = DB::table('jocom_transaction_details_group AS a')
            ->select('a.*', 'b.name')
            ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
            ->where('a.transaction_id', '=', $trans->id)
            ->orderBy('b.category')
            ->orderBy('b.name')
            ->get();

    $points = TPoint::where("transaction_id",$trans->id)->get();

    $earnedPoints = DB::table('point_transactions')
            ->select('point_types.*', 'point_transactions.*')
            ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
            ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
            ->where('point_transactions.transaction_id', '=', $trans->id)
            ->where('point_transactions.point_action_id', '=', PointAction::EARN)
            ->get();

    $general['buyer_name']          = "Tien Ming Distribution Sdn Bhd.";
    $general['buyer_email']         = "";
    $general['delivery_address_1']  = "10, Jalan Str 1,";
    $general['delivery_address_2']  = "Saujana Teknologi Park,";
    $general['delivery_address_3']  = "Rawang,";
    $general['delivery_address_4']  = "48000 Rawang, Selangor, Malaysia";
    $general['special_instruction'] = "";
    $general['delivery_contact_no'] = "Tel : +603 6734 8744";

    return array(
                'general'=>$general,
                'trans'=>$trans,
                'issuer'=>$issuer,
                'paypal'=>$paypal,
                'coupon'=>$coupon,
                'product'=>$product,
                'group'=>$group,
                'points'=>$points,
                'earnedPoints'=>array(),
                'toCustomer'=>$toCustomer
            );
               
}

public static function createPOView($trans, $po_no){
    $endSeller = 0;
    $coupon = TCoupon::where('transaction_id', '=', $trans->id)->first();

    $issuer = [
                "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
                "issuer_address_1" => "10, Jalan Str 1,",
                "issuer_address_2" => "Saujana Teknologi Park,",
                "issuer_address_3" => "Rawang,",
                "issuer_address_4" => "48000 Rawang, Selangor, Malaysia.",
                "issuer_tel"       => "Tel : +603 6734 8744",
                "issuer_gst"       => "",
            ];

    $general = [
                "po_no"               => $po_no,
                //"po_date"             => date('d/m/Y'),
                "po_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                "payment_terms"       => "Cash/Credit Card",
                "transaction_id"      => $trans->id,
                "delivery_name"       => ($trans->delivery_name != '' ? $trans->delivery_name."" : ''),
                "delivery_contact_no" => ($trans->delivery_contact_no != '' ? $trans->delivery_contact_no."" : ''),
                "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            ];


    $involved_seller = DB::table('jocom_transaction_details')
                    ->select('seller_username', 'parent_seller')
                    ->where('transaction_id', '=', $trans->id)
                    ->where('po_no', '=', $po_no)
                    ->groupBy('seller_username')
                    ->get();
                    
    // echo "<pre>";
    // print_r($involved_seller);
    // echo "</pre>";

    foreach ($involved_seller as $sellerrow)
    {
    

        $select_seller = 'parent_seller';
        $select_value  = $sellerrow->parent_seller;

                    // to parent seller
        $sellerTable = DB::table('jocom_seller')
                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                    ->where('id', '=', $sellerrow->parent_seller)
                    ->first();
               
        $tempcountry = "";
        $tempstate   = "";
        $tempcity    = "";

        $sellerCountry = Country::find($sellerTable->country);
        if ($sellerCountry != null) {
            $tempcountry = $sellerCountry->name;
        }

        $sellerState = State::find($sellerTable->state);
        if ($sellerState != null) {
            $tempstate = $sellerState->name.", ";
        }

        if (is_numeric($sellerTable->city)) {
            $city_row = City::find($sellerTable->city);

            if (count($city_row) > 0) {
                $tempcity = $city_row->name;
            }

        } else {
                $tempcity = $sellerTable->city;
            }

        $seller = [
                "seller_name"      => $sellerTable->company_name,
                "seller_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "seller_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "seller_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "seller_address_4" => $tempstate.$tempcountry.".",
                "seller_email"     => $sellerTable->email,
                "attn_name"        => $sellerTable->company_name,
                "contact_no"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                "seller_gst"       => $sellerTable->gst_reg_num,
            ];

        $product = DB::table('jocom_transaction_details AS a')
                ->select('a.*', 'b.name', 'c.name AS pname')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_product_package AS c', 'a.product_group', '=', 'c.sku')
                ->leftJoin('jocom_categories AS d', 'b.id', '=', 'd.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where("a.{$select_seller}", '=', $select_value)
                ->where('d.main', '=', '1')
                ->where('a.po_no', '=', $po_no)
                //->groupBy('a.sku')
                ->orderBy('d.category_id')
                ->orderBy('b.name')
                ->get();
                // echo "<pre>";
                // print_r($product);
                // echo "</pre>";
                // die();
    }


        return array(
                'general'=>$general,
                'trans'=>$trans,
                'seller'=>$seller,
                'issuer'=>$issuer,
                'coupon'=>$coupon,
                'product'=>$product,
                'endSeller'=>$endSeller
            );

}

public static function createEPOView($trans, $epo_no){
    $endSeller = 1;
    $coupon = TCoupon::where('transaction_id', '=', $trans->id)->first();

    $issuer = [
                "issuer_name"      => "Tien Ming Distribution Sdn Bhd.",
                "issuer_address_1" => "10, Jalan Str 1,",
                "issuer_address_2" => "Saujana Teknologi Park,",
                "issuer_address_3" => "Rawang,",
                "issuer_address_4" => "48000 Rawang, Selangor, Malaysia.",
                "issuer_tel"       => "Tel: +603 6734 8744",
                "issuer_gst"       => "",
            ];

    $general = [
                "po_no"               => $epo_no,
                //"po_date"             => date('d/m/Y'),
                "po_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                "payment_terms"       => "Cash/Credit Card",
                "transaction_id"      => $trans->id,
                "delivery_name"       => ($trans->delivery_name != '' ? $trans->delivery_name."" : ''),
                "delivery_contact_no" => ($trans->delivery_contact_no != '' ? $trans->delivery_contact_no."" : ''),
                "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
            ];


    $involved_seller = DB::table('jocom_transaction_details')
                    ->select('seller_username', 'parent_seller')
                    ->where('transaction_id', '=', $trans->id)
                    ->where('parent_po', '=', $epo_no)
                    ->groupBy('seller_username')
                    ->get();

    foreach ($involved_seller as $sellerrow)
    {
    

        $select_seller = 'seller_username';
        $select_value  = $sellerrow->seller_username;

                    // to end seller
        $sellerTable = DB::table('jocom_seller')
                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                    ->where('username', '=', $sellerrow->seller_username)
                    ->first();

        if ($sellerrow->parent_seller != '0')
        {

            $parentTable = DB::table('jocom_seller')
                        ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                        ->where('id', '=', $sellerrow->parent_seller)
                        ->first();

            $parentcountry = "";
            $parentstate   = "";
            $parentcity    = "";

            $parentCountry = Country::find($parentTable->country);
            if ($parentCountry != null) {
                $parentcountry = $parentCountry->name;
            }

            $parentState = State::find($parentTable->state);
            if ($parentState != null) {
                $parentstate = $parentState->name.", ";
            }

            if (is_numeric($parentTable->city)) {
                $city_row = City::find($parentTable->city);

                    if (count($city_row) > 0) {
                        $parentcity = $city_row->name;
                    }

            } else {

                    $parentcity = $parentTable->city;
                }

            $issuer = [
                    "issuer_name"      => $parentTable->company_name,
                    "issuer_address_1" => ($parentTable->address1 != '' ? $parentTable->address1."," : ''),
                    "issuer_address_2" => ($parentTable->address2 != '' ? $parentTable->address2."," : ''),
                    "issuer_address_3" => ($parentTable->postcode != '' ? $parentTable->postcode." " : '').($parentcity != '' ? $parentcity.", " : ''),
                    "issuer_address_4" => $parentstate.$parentcountry.".",
                    "issuer_tel"       => $parentTable->tel_num.($parentTable->tel_num != "" && $parentTable->mobile_no != "" ? "/" : '').$parentTable->mobile_no,
                    "issuer_gst"       => $parentTable->gst_reg_num,
                ];
        }
               
        $tempcountry = "";
        $tempstate   = "";
        $tempcity    = "";

        $sellerCountry = Country::find($sellerTable->country);
        if ($sellerCountry != null) {
            $tempcountry = $sellerCountry->name;
        }

        $sellerState = State::find($sellerTable->state);
        if ($sellerState != null) {
            $tempstate = $sellerState->name.", ";
        }

        if (is_numeric($sellerTable->city)) {
            $city_row = City::find($sellerTable->city);

            if (count($city_row) > 0) {
                $tempcity = $city_row->name;
            }

        } else {
                $tempcity = $sellerTable->city;
            }

        $seller = [
                "seller_name"      => $sellerTable->company_name,
                "seller_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "seller_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "seller_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "seller_address_4" => $tempstate.$tempcountry.".",
                "seller_email"     => $sellerTable->email,
                "attn_name"        => $sellerTable->company_name,
                "contact_no"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                 "seller_gst"       => $sellerTable->gst_reg_num,
            ];

        $product = DB::table('jocom_transaction_details AS a')
                ->select('a.*', 'b.name', 'c.name AS pname')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_product_package AS c', 'a.product_group', '=', 'c.sku')
                ->leftJoin('jocom_categories AS d', 'b.id', '=', 'd.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where("a.{$select_seller}", '=', $select_value)
                ->where('d.main', '=', '1')
                //->groupBy('a.sku')
                ->orderBy('d.category_id')
                ->orderBy('b.name')
                ->get();
                // print_r($product);
                // die();
    }


    return array(
                'general'=>$general,
                'trans'=>$trans,
                'seller'=>$seller,
                'coupon'=>$coupon,
                'issuer'=>$issuer,
                'product'=>$product,
                'endSeller'=>$endSeller
            );

}

public static function GetConvertedRate($from_currency ,$to_currency, $amount, $rate){
    
   
    $convertedAmount =  $amount *  $rate;
    // echo $amount."*".$rate."=".$convertedAmount;
   // echo $amount."*".$rate."=".$convertedAmount;
    return ROUND($convertedAmount,5);
    
}

public function anyDownload($loc=null){
    
    // set_time_limit(0);
    // ini_set('memory_limit', '-1');
    // define("DOMPDF_ENABLE_REMOTE", true);    
    $loc = base64_decode(urldecode($loc));
    $loc = Crypt::decrypt($loc);

    $id = explode("#", $loc);
    
    $epo_no = $id[2];
  
    $eINV_no = $id[2];

    $po_no = $id[2];

    $id = $id[1];
    
    $file_path = array_shift(explode("#", $loc));

    $file_name = explode("/", $file_path);

    $file_name = $file_name[3];


//    if (file_exists($file_path)) {
//
//        $headers = array(
//                  'Content-Type: application/pdf',
//                );
//
//        // return Response::download($file_path, $display_details, $headers);
//        header('Cache-Control: public');
//        header('Content-Type: application/pdf');
//        header('Content-Length: '.filesize($file_path));
//        header('Content-Disposition: filename="'.$file_name.'"');
//        $file = readfile($file_path);
//        
//    } 
//    else{
            $trans = Transaction::find($id); 

            //New Invoice Start 
            $invoiceview = "";
            $inv_newdate = Config::get('constants.NEW_INVOICE_START_DATE');

            $transactionDate = $trans->transaction_date;
            
            $special_inv = array(
                            21004,
                            23143,
                            19959,
                            21003,
                            20057,
                            24276,
                            24275,
                            24279,
                            24286,
                            24287,
                            28176,
                            24288);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
            // Select type of Invoice format

            if (strpos($loc, '/TMG') !== false) {
                $file_name = str_replace("%2F","-",$file_name);
                $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');

                include app_path('library/html2pdf/html2pdf.class.php');
                
                $INVView = self::createINVView($trans);

                $response = View::make($invoiceview)
                        ->with('display_details', $INVView['general'])
                        ->with('display_trans', $INVView['trans'])
                        ->with('display_issuer',$INVView['issuer'])
                        ->with('display_seller', $INVView['paypal'])
                        ->with('display_product', $INVView['product'])
                        ->with('display_group', $INVView['group'])
                        ->with('display_coupon', $INVView['coupon'])
                        ->with('display_points', $INVView['points'])
                        ->with('display_earns', $INVView['earnedPoints'])
                        ->with('buyer_type', $INVView['buyer_type'])
                        ->with('toCustomer', $INVView['toCustomer'])
                        ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                        ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                        ->with('standard_currency',$INVView['standard_currency'])
                        ->with('standard_currency_rate',$INVView['standard_currency_rate']);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);

            }
            
            if (strpos($loc, 'CUS') !== false) {
                
                
                $trans = Transaction::find($id);
                $INVView = self::createINVView($trans,true);
                //New Invoice Start 
                $invoiceview = "";
                $transactionDate = $trans->transaction_date;
                $file_name = $trans->invoice_no.".pdf";
                
                $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                
                // Select type of Invoice format
                
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                            $invoiceview = 'checkout.invoice_view_new_v3';
                }  elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                    $invoiceview = 'checkout.invoice_view_new_v2';
                    
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                    
                    $invoiceview = 'checkout.invoice_view_new';
                    
                }  else{
                    
                    $invoiceview = 'checkout.invoice_view';
                }
                // Select type of Invoice format

                //New Invoice End
                

                 $response = View::make($invoiceview)
                        ->with('display_details', $INVView['general'])
                        ->with('display_trans', $INVView['trans'])
                        ->with('display_issuer',$INVView['issuer'])
                        ->with('display_seller', $INVView['paypal'])
                        ->with('display_product', $INVView['product'])
                        ->with('display_group', $INVView['group'])
                        ->with('display_coupon', $INVView['coupon'])
                        ->with('display_points', $INVView['points'])
                        ->with('display_earns', $INVView['earnedPoints'])
                        ->with('buyer_type', $INVView['buyer_type'])
                        ->with('toCustomer', $INVView['toCustomer']);
                
//                echo $response;
//                die();

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);

            }

            if (strpos($loc, '/eINV') !== false) {
                 
                if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                        $invoiceview = 'checkout.Einvoice_view_new_v4';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                        $invoiceview = 'checkout.invoice_view_new_v3';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                        $invoiceview = 'checkout.invoice_view_new_v2';
                }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                    
                    $invoiceview = 'checkout.invoice_view_new';
                    
                }  else{
                    
                    $invoiceview = 'checkout.invoice_view';
                } 

                $file_path = Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH');
                
                include app_path('library/html2pdf/html2pdf.class.php');

                $ENVView = self::createEINVView($trans,$eINV_no);

                $response = View::make($invoiceview)
                        ->with('display_details', $ENVView['general'])
                        ->with('display_trans', $ENVView['trans'])
                        ->with('display_issuer',$ENVView['issuer'])
                        ->with('display_seller', $ENVView['paypal'])
                        ->with('display_product', $ENVView['product'])
                        ->with('display_coupon', $INVView['coupon'])
                        ->with('display_group', $ENVView['group'])
                        ->with('display_points', $ENVView['points'])
                        ->with('display_earns', $ENVView['earnedPoints'])
                        ->with('toCustomer', $ENVView['toCustomer']);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);

            }

            if (strpos($loc, 'DO') !== false) {

                $doprint = $trans->doprint_count + 1;

                $data = Transaction::find($trans->id);
                $data->doprint_count = $doprint;
                $data->save();

                $trans = Transaction::find($id); 

                DB::table('jocom_doprinting_history')->insert(array(
                            'username'          =>  Session::get('username'),
                            'transaction_id'    =>  $trans->id,
                            'do_no'             =>  $trans->do_no,
                            'type'              =>  'Download',
                            'total_doprint'     =>  $doprint
                            )
                    );



                $file_path = Config::get('constants.DO_PDF_FILE_PATH');

                include app_path('library/html2pdf/html2pdf.class.php');
                
                // echo "<pre>";
                // print_r($DOView['product']);
                // echo "</pre>";

                $DOView = self::createDOView($trans);

                $response =  View::make('checkout.do_view')
                            ->with('display_details', $DOView['general'])
                            ->with('display_trans', $DOView['trans'])
                            ->with('display_seller', $DOView['paypal'])
                            ->with('display_product', $DOView['product'])
                            ->with('display_group', $DOView['group'])
                            ->with('delivery_type', $DOView['delivery_type'])
                            ->with('deliveryservice', $DOView['deliveryservice'])
                            ->with('barcode_test', $DOView['barcode'])
                            ->with("display_delivery_service_items",$DOView['DeliveryOrderItems']);
                 // print_r($DOView['general']);           
                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);

            }

            if (strpos($loc, '/PO') !== false) {
                  		
                if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){		
                    		
                    $poview = 'checkout.po_view_parent_v2';		
                    		
                }   else{		
                    		
                    $poview = 'checkout.po_view_parent';		
                }

                $file_path = Config::get('constants.PO_PDF_FILE_PATH');

                include app_path('library/html2pdf/html2pdf.class.php');

                $POView = self::createPOView($trans,$po_no);
                
                // echo "<pre>";
                // print_r($POView);
                // echo "</pre>";

                $response = View::make($poview)
                            ->with('display_details', $POView['general'])
                            ->with('display_trans', $POView['trans'])
                            ->with('display_seller', $POView['seller'])
                            ->with('display_issuer', $POView['issuer'])
                            ->with('display_coupon', $POView['coupon'])
                            ->with('display_product', $POView['product'])
                            ->with('endSeller', $POView['endSeller']);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);

            }

               if (strpos($loc, '/ePO') !== false) {

                $file_path = Config::get('constants.PO_PARENT_PDF_FILE_PATH');

                include app_path('library/html2pdf/html2pdf.class.php');
                
                 		
                if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){		
                    		
                    $epoview = 'checkout.po_view_v2';		
                }  elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {		
                    $epoview = 'checkout.po_view';		
                    		
                } else{		
                    $epoview = 'checkout.po_view';		
                } 

                $EPOView = self::createEPOView($trans, $epo_no);

                //endseller=1
                $response = View::make($epoview)
                            ->with('display_details', $EPOView['general'])
                            ->with('display_trans', $EPOView['trans'])
                            ->with('display_seller', $EPOView['seller'])
                            ->with('display_issuer', $EPOView['issuer'])
                            ->with('display_product', $EPOView['product'])
                            ->with('endSeller', $EPOView['endSeller']);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);
            } 
             
        //}
        
}

    /**
     * Manually generate PO, DO and Invoice
     * @param  [type] $loc [description]
     * @return [type]      [description]
     */
    public function anyNewfile($id = null)
    {

        if (isset($id)) {
            $id = base64_decode(urldecode($id));
            $id = Crypt::decrypt($id);

            $allMessage = 'No file';

            $tempInv = MCheckout::generateInv($id, true);
            if ($tempInv != 'no') {
                $allMessage = "Invoice";
            }
            $tempPO = MCheckout::generatePO($id, true);
            if ($tempPO != 'no') {
                $allMessage = "PO";
            }
            
            $Transaction = Transaction::find($id);
            if($Transaction->buyer_username == 'Qoo10'){
                $tempQ0010Inv = MCheckout::generateQoo10Inv($id, true);
                if ($tempQ0010Inv != 'no') {
                    $allMessage = "Q10Inv";
                }
            }
            
            $tempDO = MCheckout::generateDO($id, true);
            if ($tempDO != 'no') {
                $allMessage = "DO";
            }else{
                log_transaction($id);
            }

            if ($allMessage != 'No file') {
                
                $Transaction = Transaction::find($id);
                $Transaction->status = 'completed';
                $Transaction->save();
                
                return Redirect::to('transaction/edit/'.$id)->with('success', 'New PO, DO and Invoice were generated!');

            } else {
                return Redirect::to('transaction/edit/'.$id)->with('message', 'No PO, DO and Invoice were generated!');
            }
        } else {
            return Redirect::to('transaction/edit/'.$id)->with('message', 'No PO, DO and Invoice were generated!');
        }
    }

    public function anyAddremark()
    {
        $remarkId    = Input::get('remarkId');
        $trans          = Transaction::find($remarkId);
        $remark         = "[".date('Y-m-d H:i:s') . "] " . Session::get('username') . ": " . trim(Input::get('remark'));

        if($trans->remark == '')
            $trans->remark = $remark;
        else
            $trans->remark = $trans->remark . "\n" . $remark;

        $trans->save();

        return Redirect::back();
    }

    public function anyTest()
    {
        $total = Transaction::dashboard_total();
        print_r($total);
        echo "<br> Next: <br>";

        $latest = Transaction::dashboard_latest_transaction();
        print_r($latest);
    }

    /*
     * @Desc    : View page
     */
    public function anyLocation(){
        
        $MalaysiaCountryID = 458;
        
        $States = State::where('status', '=', 1)
                ->where('country_id', '=', $MalaysiaCountryID)->get(); // Malaysia Country ID
        
        return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_location_listing')
                ->with("States",$States);
        
}
    /*
     * @desc    : Datatables location listing
     * @Return  : Datatables type collection
     */
    public function anyLocationlisting(){
        
        
        /* Get Posted value */
        $state_id = Input::get('state');
        $city_id = Input::get('city');
        $postcode = Input::get('postcode');
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');

        
        $orders = DB::table('jocom_transaction')->select(array(
                        'id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode'
                        ))
                ->where('status','completed');
        
        if(!empty($state_id) && $state_id != "-"){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_state",$State->name);
        }
        
        if(!empty($city_id) && $city_id != "-"){
            $orders = $orders->where("delivery_city_id",$city_id);
        }
        
        if(!empty($postcode)){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_postcode",$postcode);
        }
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction_date', '>=', $transaction_from." 00:00:00");
        }else{
            $orders = $orders->where('transaction_date', '>=', DATE("Y-m-d")." 00:00:00");
        }
        
        if(!empty($transaction_to)){
            $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        }
        
        return Datatables::of($orders)->make(true);
       
        
    }
    
    public function anyMaplocations(){
        
        $allLocation = array();
        
        /* Get Posted value */
        $state_id = Input::get('state');
        $city_id = Input::get('city');
        $postcode = Input::get('postcode');
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        
        $orders = DB::table('jocom_transaction')->select(array(
                        'id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode'
                        ))
                ->where('status','completed');
        
        if(!empty($state_id) && $state_id != "-"){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_state",$State->name);
        }
        
        if(!empty($city_id) && $city_id != "-"){
            $orders = $orders->where("delivery_city_id",$city_id);
        }
        
        if(!empty($postcode)){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_postcode",$postcode);
        }
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction_date', '>=', $transaction_from." 00:00:00");
        }else{
            $orders = $orders->where('transaction_date', '>=', DATE("Y-m-d")." 00:00:00");  
        }
        
        if(!empty($transaction_to)){
            $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        }
        
        $orders = $orders->get();
        
        foreach ($orders as $key => $value) {
           
            $transactionID = $value->id;
            $LocationInfo = $this->getLocationDetails($transactionID);
            array_push($allLocation, $LocationInfo);
            
        }
        
        return $allLocation;
        
    }
    
    /*
     * @Desc    : Collection transaction location information 
     * @Return  : Array
     */
    public function getLocationDetails($TransactionID){
        
        $TransactionInfo = Transaction::find($TransactionID);
        
        $street = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_addr_1));
        $route = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_addr_2));
        $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_city));
        $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_state));
        $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_country));
        $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_postcode));
        
        if($TransactionInfo->gps_latitude == ""){
            $apiGoogleMapKey = Config::get('constants.GOOGLE_MAP_API_KEY');
            $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$street.",+".$route.",+".$city."+".$postcode.",".$country."&key=".$apiGoogleMapKey;
            
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$URL );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $output = curl_exec($ch);
            curl_close($ch);

            $ArrayMap = json_decode($output) ;

            $latitude = $ArrayMap->results[0]->geometry->location->lat;
            $longitute = $ArrayMap->results[0]->geometry->location->lng;
            
        }else{
            
            $latitude = $TransactionInfo->gps_latitude;
            $longitute = $TransactionInfo->gps_longitude;
            
        }
        
        return array(
            "transaction_id" => $TransactionID,
            "latitude" => $latitude,
            "longitude" => $longitute,
            "address" =>array(
                "street_1" => $TransactionInfo->delivery_addr_1,
                "street_2" => $TransactionInfo->delivery_addr_2,
                "city" => $TransactionInfo->delivery_city,
                "state" => $TransactionInfo->delivery_state,
                "postcode" => $TransactionInfo->delivery_postcode,
                "country" => $TransactionInfo->delivery_country,
            )
        );
        
        
    }
    
    /*
     * @Desc    : Get location details base on transaction id
     */
    public function anyGetlocation(){
        
        // Get address information based on transaction id
        
        $TransactionID = Input::get('transactionID');
        $TransactionInfo = Transaction::find($TransactionID);
        $locationDetails = $this->getLocationDetails($TransactionID);
        return $locationDetails;

    }
    
    /*
     * @Desc    : Get delivery location altitude and longititude
     */
    public function anyUpdatelatlong(){
        
        $isError = 0;
        $response = 1;
        
        try{
            $transactionID = Input::get("transactionID");
            $latlong = explode(",",Input::get("latlong"));
            $latitude = $latlong[0];
            $longitude = $latlong[1];

            $TransactionData = Transaction::find($transactionID);
            $TransactionData->gps_latitude = $latitude;
            $TransactionData->gps_longitude = $longitude;
            $TransactionData->modify_by = Session::get('username');
            $TransactionData->modify_date = DATE("Y-m-d H:i:s");
            $TransactionData->save();
        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }finally {
            return array(
                "response"=>$response,
                "latitude"=>$latitude,
                "longitude"=>$longitude,
            );
            
        }
        
        
    }

    /*
     * Desc : Send daily product 
     */
    

   public function anyUpdatedoprinting(){
        $response = 0;

       
        $transactionid  = Input::get('transactionID');
        $printcount     = Input::get('printcount');
       
            if (isset($transactionid))
            {

                $data = Transaction::find($transactionid);
                $data->doprint_count = $printcount;
                $data->save();

                $trans = Transaction::find($transactionid); 

                DB::table('jocom_doprinting_history')->insert(array(
                            'username'          =>  Session::get('username'),
                            'transaction_id'    =>  $trans->id,
                            'do_no'             =>  $trans->do_no,
                            'type'              =>  'Print',
                            'total_doprint'     =>  $printcount
                            )
                    );

                $response = 1;
            }

         return $response;   

    }
    
    public function anyHistory(){

        return View::make('admin.status_history');
    }

    public function anyHistorylisting(){

        $result = DB::table('status_history')->select('trans_id','logistic_id','batch_id','old_status', 'status', 'type', 'modify_by', 'modify_date')->orderBy('id', desc);

        return Datatables::of($result)->make(true);
    }  
    
    // THE MESS START HERE
    
    /*
     * @Desc : View Sort DO
     */
    
    
    
    /*
     * @Desc : To sort DO base on filter submitted
     * SENSETIVE FUNCTION . TOUCH IT SOFTLY LIKE REALLY2 SOFT PLEASE
     * 
     */
    
    public function anySort(){


        $regionList = Region::where("status",1)->get();
    
        $totalFailed = DB::table('jocom_sort_transaction AS JST')
                    ->where("JST.generated",0)
                    ->where("JST.regenarate",0)
                    ->where("JST.status",1)
                    ->where("JST.activation",1)->count();
 
        return View::make('utilities.dosort2')->with("regionList",$regionList)
                ->with("totalFailed",$totalFailed);
    
    
    }
    
    public function anySort2(){


        $regionList = Region::where("status",1)->get();
    
        $totalFailed = DB::table('jocom_sort_transaction AS JST')
                    ->where("JST.generated",0)
                    ->where("JST.regenarate",0)
                    ->where("JST.status",1)
                    ->where("JST.activation",1)->count();
 
        return View::make('utilities.dosort2')->with("regionList",$regionList)
                ->with("totalFailed",$totalFailed);
    
    
    }
    
    public function anySorter() {

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $automaticGenerate = false;


        try {
            
            DB::beginTransaction();
            
            $ApiLog = new ApiLog ;
            $ApiLog->api = 'SORTER';
            $ApiLog->data = json_encode(Input::all());
            $ApiLog->save();
            
            // echo "<pre>";
            // print_r($ApiLog);
            // echo "</pre>";

            $region_id = Input::get("region");
            $from_id = Input::get("from_id");
            $to_id = Input::get("to_id");
            $list_transaction = Input::get("list_transaction");
            
            $create_separator = Input::get("create_separator") == 1 ? true :false;
            $is_include_failed = Input::get("is_include_failed") == 1 ? true :false;
            $failed_do_only = Input::get("failed_do_only") == 1 ? true :false;
            
            $sortResponse = self::dosorting($region_id,$from_id,$to_id,$list_transaction, $create_separator,$is_include_failed,$failed_do_only);
                
        } catch (Exception $ex) {
            $isError = true;
            $data =  array(
                "response" => '0',
                "batch_no" => '',
                "total_success" => '0',
                "total_failed" => '0',
                "total_duplicate" => '0',
            );
            // echo $ex->getMessage();
            // echo $ex->getLine();
            // echo $ex->getTrace();
            // echo $ex->getFile();

        } finally {
            if($isError){
                DB::rollback();
            }else{
//                DB::rollback();
                DB::commit();
                
            }
            
            return array(
                "response" =>  $isError ? 0 : 1,
                "data" => $data
            );
        }


    
    }
    
    public static function dosorting($region_id,$from_id,$to_id,$list_transaction, $create_separator,$is_include_failed,$failed_do_only){
        
        $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'sorter_batch')->first();
                
            $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
            $NewRunner = Running::find($running_number->id);
            $NewRunner->counter = $running_number->counter + 1;
            $NewRunner->save();
            
            $NewbatchNo =  'GDL'.$batchNo;

            // Predefined Variable
            $isError = false;
            $stateList = [];
            $TIDCollection = [];
            $SuccesSortedList = array();
            $FailedsortedList = array();
            $DuplicatesortedList = array();
            $RegenerateSortedList = array();
            $contents = '';
            
            // Filter parameter
//            $from_id = 23951;
//            $to_id = 24169;
//            $region_id = 1;
            $loopCounter = 0;
            $separator = '';
         
            // Get state
            $State = State::where("region_id",$region_id)->get();
            foreach ($State as $key => $value) {
                $stateList[] = $value->name;
            }
            
            if($failed_do_only){
                
                $RawFailedLTListRawLTList = DB::table('jocom_sort_transaction')
                    ->select(array(
                        'jocom_sort_transaction.*'
                        ))
                    ->where("jocom_sort_transaction.generated",0)
                    ->where("jocom_sort_transaction.status",1)
                    ->where("jocom_sort_transaction.activation",1)->get();
                   
            
                foreach ($RawFailedLTListRawLTList as $k => $v) {
                    array_push($RegenerateSortedList, array(
                            "sort_transaction_id"=>$v->id,
                            "transaction_id"=>$v->transaction_id,
                            "remarks"=>'',
                            "Tdetails"=>'',
                    ));
                    $TIDCollection[] = $v->transaction_id;
                }
                
                
                // echo "<pre>";
                // print_r($TIDCollection);
                // echo "</pre>";
              
                
            }else{
                
                $RawFailedLTListRawLTList = DB::table('logistic_transaction')
                    ->select(array(
                        'logistic_transaction.*'
                        ))
                    ->where("logistic_transaction.transaction_id",">=",$from_id)
                    ->where("logistic_transaction.transaction_id","<=",$to_id)->get();
                    //->where("logistic_transaction.transaction_id","<=",$to_id)->limit(1)->get();
           
                foreach ($RawFailedLTListRawLTList as $k => $v) {
                    $TIDCollection[] = $v->transaction_id;
                }

                if($is_include_failed){
                    $RawFailedLTList = DB::table('jocom_sort_transaction')
                        ->select(array(
                            'jocom_sort_transaction.*'
                            ))
                        ->where("jocom_sort_transaction.generated","=",0)
                        ->where("jocom_sort_transaction.activation","=",1)
                        ->get();

                    foreach ($RawLTList as $kF => $vF) {
                        $TIDCollection[] = $vF->transaction_id;
                    }
                }
            
                
            }
            
            if(strlen($list_transaction) > 0 ){
                $selectedID = str_replace(" ", "", $list_transaction);
                $ListSelectedID = explode(",",$selectedID);
                if(count($ListSelectedID) > 0 ){
                    foreach ($ListSelectedID as $vSelID) {
                        $TIDCollection[] = $vSelID;
                    }
                }
            }
            
           
            
            $LogisticTransaction = DB::table('logistic_transaction')
                    ->select(array(
                        'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
                        ))
                    ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
                    ->whereIn('logistic_transaction.transaction_id', $TIDCollection)
                    //->where("logistic_transaction.transaction_id",">=",$from_id)
                    //->where("logistic_transaction.transaction_id","<=",$to_id)
                    //->where("logistic_transaction.transaction_id","<=",$to_id)
                    ->where("jocom_transaction.status",'completed')
                    ->where("logistic_transaction.status",0)
                    ->whereIn("logistic_transaction.delivery_state",$stateList)
                    ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
                    ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
                    ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
                    ->orderBy('logistic_transaction.transaction_id', 'ASC')
                    //->limit(5)
                    ->get();
                    
            // echo "<pre>";
            //  print_r($TIDCollection);
            // print_r($stateList);
            // print_r($selectedID);
            // print_r($LogisticTransaction);
            
            // echo "</pre>";
            // die();

        
        //   print_r($LogisticTransaction);
            include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');
            
            $totalRecord = count($LogisticTransaction);

            foreach ($LogisticTransaction as $key => $value) {
                
                // Main player in this loop: Transaction ID and Logistic ID
                // Who change this beware!! 
                $logisticID = $value->LogisticID;
                $transactionID = $value->transaction_id;
                
                // Check on if order duplicate / already generate
                $Duplicate = DB::table('jocom_sort_transaction')
                    ->select(array(
                        'jocom_sort_transaction.*','jocom_sort_generator.batch_no'
                        ))
                    ->leftJoin('jocom_sort_generator', 'jocom_sort_generator.id', '=', 'jocom_sort_transaction.sort_id')    
                    ->where("jocom_sort_transaction.transaction_id",$transactionID)
                    //->where("jocom_sort_transaction.generated",1)
                    //->whereIn('jocom_sort_transaction.generated', array(1))
                    ->where("jocom_sort_transaction.status",1)
                    ->where("jocom_sort_transaction.activation",1)->first();
  
                if(count($Duplicate) > 0){
//                    
//                    echo "<pre>";
//                    print_r($Duplicate);
//                    echo "</pre>";
//                    exit();    
//                    
                    array_push($DuplicatesortedList, array(
                        "transaction_id" => $transactionID,
                        "remarks"=>"Duplicate : Batch No : ".$Duplicate->batch_no,
                    ));
                    
                }else{
                    
                    
                    
                    $Items = DB::table('logistic_transaction_item')
                    ->select(array(
                        'logistic_transaction_item.*'
                        ))
                    ->leftJoin('logistic_transaction', 'logistic_transaction.id', '=', 'logistic_transaction_item.logistic_id')  
                    ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')   
                    ->where("logistic_transaction.transaction_id",$transactionID)->get();
                    
//                    echo "======= PRODUCT ITEMS ==========";
//                    echo "<pre>";
//                    print_r($Items);
//                    echo "</pre>";
//                    echo "======= PRODUCT ITEMS ==========";
                    
                    
                    $totalNotEnoughStock = 0;
                    
                    // $allProductAvailable : Indicator to define all base items are have enough available stock in hand.
                    $allProductAvailable = true;
                    $allProductAvailableCollection = array();
                    
                    $TSortDetails = array();
                    $failedRemarks = '';
                    // OPEN LOOPING ITEM
                    
                    foreach ($Items as $kI => $vI) { 
                        
                        $logistic_item_id = $vI->id;

                        $BaseItems = DB::table('jocom_product_base_item AS JPBI')
                                ->select(array(
                                    'JPBI.*','JP.sku'
                                ))
                                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')  
                                ->where("JPBI.product_id",$vI->product_id)
                                ->where("JPBI.price_option_id",$vI->product_price_id)
                                ->where("JPBI.status",1)
                                ->get();
                        
                       
                       
                        
                        // Have base item so not main stock product
                        if(count($BaseItems) > 0 ) {
                            
                            // check base item stock
                            // echo "IS BASE";
                            foreach ($BaseItems as $kBI => $vBI) {
                                
                                $quantity_required = $vBI->quantity * $vI->qty_order;
//                                echo "qty Product". $vBI->product_base_id;
//                                echo "qty required:". $quantity_required;
                                $StockInHand = Warehouse::getStockavailable($vBI->product_base_id,$quantity_required,'CMS');
                                // echo "<pre>";
                                // print_r($StockInHand);
                                // echo "</pre>";
                                $ProductWareHouseID = $StockInHand['productWarehouseID'];
                                $Product = Product::find($vBI->product_base_id);
                                
                                // Proceed to reserve the stock : Becareful !!
                                if($StockInHand['stockExist'] == 1){
                                    
                                    // HAVE ENOUGH AVAILABLE STOCK
                                    if( $StockInHand['stockEnough'] == 1){
                                        
                                        // 1. Reserved Stock 
                                        // 2. Store in collection first because need to see other base item also enough stock or not
                                        
                                        if(isset($StockInHand['reservedData']) && ($StockInHand['reservedData']['response'] == 1)){
                                        
                                            $ReserveData = $StockInHand['reservedData'];
                                            $totalReserved = $ReserveData['quantity'] * $quantity_required;
                                            $reservedSet = array(
                                                "productWareHouseID" => $ReserveData['ProductWareHouseID'],
                                                "productID" => $ReserveData['productid'],
                                                "reservedQuantity" => $ReserveData['quantity'] * $quantity_required,
                                            );
                                            
                                            // reserved here! why reserve ? long story bro!
                                            self::reservedStock($ReserveData['ProductWareHouseID'], $totalReserved, $transactionID, $logisticID, $NewbatchNo, Session::get('username'),$logistic_item_id);
                                        
                                        
                                        }
                                        
                                        array_push($TSortDetails, array(
                                            "product_id" => $vBI->product_base_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 1,
                                            "order_quantity" => $quantity_required,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "is_failed" => 1,
                                            "remarks" => '',
                                            "stockResult" => $StockInHand
                                        ));
                                    
                                        array_push($allProductAvailableCollection, array(
    //                                            "productWarehouseID" => $ProductWareHouseID,
                                                "product_id" => $vI->product_id,
                                                "quantity_require" => $quantity_required,
                                                "logistic_id" => $logisticID,
                                                "transaction_id" => $transactionID,
                                                "reserved" => $reservedSet,
                                        ));
                                        
                                        
                                        
                                    
                                    }else{
                                        
                                    //  NOT ENOUGH AVAILABLE STOCK
                                        array_push($TSortDetails, array(
                                            "product_id" => $vBI->product_base_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 1,
                                            "order_quantity" => $quantity_required,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "is_failed" => 2,
                                            "remarks" => '',
                                            "stockResult" => $StockInHand
                                        ));

                                        $failedRemarks = $failedRemarks.'Not enough Base Stock 1: '.$Product->qrcode.", Order: ".$vBI->quantity.", Stock : " .$StockInHand['stockAvailable']." <br>";
                                        $totalNotEnoughStock++; 
                                        
                                        // Set flag to false //
                                        $allProductAvailable = false;  
                                    }
                                    
                                }else{
                                    
                                    // Not Allow because no stock Exist / registered in warehouse
                                    // Set flag to false //
                                    array_push($TSortDetails, array(
                                        "product_id" => $vBI->product_base_id,
                                        "logistic_item_id" => $vI->id,
                                        "type_product" => 1,
                                        "order_quantity" => $vBI->quantity * $vI->qty_order,
                                        "available_quantity" => (int)$StockInHand['stockAvailable'],
                                        "is_failed" => 2,
                                        "remarks" => 'Stock Not Registered',
                                        "stockResult" => $StockInHand
                                    ));

                                    $failedRemarks = $failedRemarks.'Not enough Base Stock 2: '.$Product->qrcode.", Order: ".$vBI->quantity.", Stock : " .$StockInHand['stockAvailable']." <br>";
                                    $totalNotEnoughStock++; 
                                    $allProductAvailable = false;  
                                        
                                }
                                
                            }
//                            
//                            if($totalNotEnoughStock > 0){
//                                array_push($FailedsortedList, array(
//                                    "transaction_id"=>$transactionID,
//                                    "transactionDetails"=>$TSortDetails,
//                                    "remarks"=>$failedRemarks,
//                                ));
//                            }
                            
                        }else{
                          //  echo "IS NOT";
//                            // FOR NOT BASE PRODUCT ITEM
//                            echo $vI->product_id;
                            $quantity_required = $vI->qty_order;
                            $StockInHand = Warehouse::getStockavailable($vI->product_id,$quantity_required,'CMS');
                            // echo "<pre>";
                            //     print_r($StockInHand);
                            //     echo "</pre>";
                            $ProductWareHouseID = $StockInHand['productWarehouseID'];
                            
                            $Product = Product::find($vI->product_id);
                            
                            // STOCK EXIST
                            if($StockInHand['stockExist'] == 1){
                                
                                $Product = Product::find($vI->product_id);
                                
                                // CHECK QTY ORDER LESS THAN AVAILABLE QUANTITY
                                if( $StockInHand['stockEnough'] == 1){
                                    
                               
                                    if(isset($StockInHand['reservedData']) && ($StockInHand['reservedData']['response'] == 1)){
                                        
                                        $ReserveData = $StockInHand['reservedData'];
                                        $totalReserved = $ReserveData['quantity'] * $quantity_required;
                                        $reservedSet = array(
                                            "productWareHouseID" => $ReserveData['ProductWareHouseID'],
                                            "productID" => $ReserveData['productid'],
                                            "reservedQuantity" => $totalReserved,
                                        );
                                        
                                        // reserved here! why reserve ? long story bro!
                                        self::reservedStock($ReserveData['ProductWareHouseID'], $totalReserved, $transactionID, $logisticID, $NewbatchNo, Session::get('username'),$logistic_item_id);
                                        
                                    }
                                    
                                    array_push($allProductAvailableCollection, array(
//                                            "productWarehouseID" => $ProductWareHouseID,
                                            "product_id" => $vI->product_id,
                                            "quantity_require" => $quantity_required,
                                            "logistic_id" => $logisticID,
                                            "transaction_id" => $transactionID,
                                            "reserved" => $reservedSet
                                    ));
                                    
                                    array_push($TSortDetails, array(
                                            "product_id" => $vI->product_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 0,
                                            "order_quantity" => $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "is_failed" => 1,
                                            "remarks" => '',
                                            "stockResult" => $StockInHand
                                    ));
                                    
                                    
                                    
                                }else{
                                    // NOT ENOUGH
                                    // add remark no stock for failed list
                                    $failedRemarks = 'Not enough Stock 3: '.$Product->qrcode.", Order: ".$vI->qty_order.", Stock : " .$StockInHand['stockAvailable']."";

                                    array_push($TSortDetails, array(
                                            "product_id" => $vI->product_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 0,
                                            "order_quantity" => $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "is_failed" => 2, //failed
                                            "remarks" => '',
                                            "stockResult" => $StockInHand
                                        ));

//                                    array_push($FailedsortedList, array(
//                                        "transaction_id"=>$value->transaction_id,
//                                        "remarks"=>$failedRemarks,
//                                        "transactionDetails"=>$TSortDetails,
//                                    ));
                                    $totalNotEnoughStock++;
                                    
                                    // Not Allow because not enough stock
                                    $allProductAvailable = false;  
                                }
                                
                            }else{
                                
                                // STOCK NOT EXIST
                                // Not Allow because no stock Exist / registered in warehouse
                                array_push($TSortDetails, array(
                                            "product_id" => $vI->product_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 0,
                                            "order_quantity" => $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "is_failed" => 2, //failed
                                            "remarks" => '',
                                            "stockResult" => $StockInHand
                                        ));
                                
                                $failedRemarks = 'Not enough Stock 4: '.$Product->qrcode.", Order: ".$vI->qty_order.", Stock : " .$StockInHand['stockAvailable']."";

//                                array_push($FailedsortedList, array(
//                                    "transaction_id"=>$value->transaction_id,
//                                    "remarks"=>$failedRemarks,
//                                    "transactionDetails"=>$TSortDetails,
//                                ));
                                $allProductAvailable = false;  
                                
                            }
                        }
                        
                        
                    }
                    // CLOSE LOOPING ITEM
                    
                    // Check of all valid for reserver or not: Check now if indicator TRUE
                    // echo "<pre>";
                    // print_r($allProductAvailableCollection);
                    // echo "</pre>";
//                    exit();
                    
                    if(count($TSortDetails) > 0){
                        array_push($FailedsortedList, array(
                            "transaction_id"=>$transactionID,
                            "remarks"=>$failedRemarks,
                            "transactionDetails"=>$TSortDetails,
                        ));
                    }
                    
                    $reserveNow  = false;    
                    if($allProductAvailable && $reserveNow){
                        
                        // echo "<pre>";
                        // echo "<START>";
                        // print_r($allProductAvailableCollection);
                        // echo "</pre>";
                        // die();

                        // Proceed for reserve , Start reserve stock
                        foreach ($allProductAvailableCollection as $kResPro => $vResPro) {
//                        echo "<START>";
//                        print_r($vResPro);
//                        echo "</pre>";
                            // OPEN: Store reserve list
                            $WarehouseProductReserved = new WarehouseProductReserved;
                            $WarehouseProductReserved->product_warehouse_id = $vResPro['reserved']['productWareHouseID'];
                            $WarehouseProductReserved->total_reserved = $vResPro['reserved']['reservedQuantity'];
                            $WarehouseProductReserved->transaction_id = $vResPro['transaction_id'];
                            $WarehouseProductReserved->logistic_id = $vResPro['logistic_id'];
                            $WarehouseProductReserved->batch_no = $NewbatchNo; // Batch No declared at early of function
                            $WarehouseProductReserved->activation = 1;
                            $WarehouseProductReserved->status = 1; // Active Reserved // 2 ; should be completed reserved and deducted from reserved quantity
                            $WarehouseProductReserved->is_completed = 0;
                            $WarehouseProductReserved->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";;
                            $WarehouseProductReserved->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";;
                            $WarehouseProductReserved->save();
                            
//                            echo "END";
                            // CLOSE: Store reserve list

                            // UPDATE MAIN STOCK TABLE
                            $WarehouseStockProduct = Warehouse::where("id",$vResPro['reserved']['productWareHouseID'])
                                    ->where("status",1)->first();
                            
//                            print_r($WarehouseStockProduct);
//                            
//                            echo "END";
//                            echo "REserved: ".$WarehouseStockProduct->reserved_in_hand  + $vResPro['reserved']['reservedQuantity']; 

                            
                            $newTotalReserve = $WarehouseStockProduct->reserved_in_hand  + $vResPro['reserved']['reservedQuantity'];
//                            echo 
                            $WarehouseStockProduct->reserved_in_hand = $newTotalReserve; 
                            $WarehouseStockProduct->modify_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                            $WarehouseStockProduct->save();
                            
//                                 echo "END";
                            // UPDATE MAIN STOCK TABLE
                        }

                    }
    
                    // Open: Create list for Regenerate
                    
                    if($is_include_failed == true || $failed_do_only == true){
                        
                        $Regenerate = DB::table('jocom_sort_transaction')
                            ->select(array(
                                'jocom_sort_transaction.*','jocom_sort_generator.batch_no'
                                ))
                            ->leftJoin('jocom_sort_generator', 'jocom_sort_generator.id', '=', 'jocom_sort_transaction.sort_id')    
                            ->where("jocom_sort_transaction.transaction_id",$transactionID)
                            ->where("jocom_sort_transaction.generated",0)
                            ->where("jocom_sort_transaction.status",1)
                            ->where("jocom_sort_transaction.activation",1)->first();
                        
                        array_push($RegenerateSortedList, array(
                            "sort_transaction_id"=>$Regenerate->id,
                            "transaction_id"=>$Regenerate->transaction_id,
                            "remarks"=>'',
                        ));
                    }
                    // Close: Create list for Regenerate 
                    
                    $loopCounter++;
                    
                    // CREATE SEPARATOR
                    if($create_separator){
                        if($separator != $value->delivery_city){
                            $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                            $separator = $value->delivery_city;
                        }
                    }
                    // CREATE SEPARATOR
                     
                    if($allProductAvailable ){
                        $platform="";
                        if($value->buyer_email == 'lazada@tmgrocer.com'){
                            $platform = 'Lazada';
                        } else if ($value->buyer_email == 'shopee@tmgrocer.com'){
                            $platform = 'Shopee';
                        } else if ($value->buyer_email == 'fnlife@tmgrocer.com'){
                            $platform = 'FN-Life';
                        } else if ($value->buyer_email == 'fnlifesuite@tmgrocer.com'){
                            $platform = 'FN-TikTok';
                        }
                        else
                        {
                            $platform = 'tmGrocer';
                        }
                        $transaction = $value;

                        array_push($SuccesSortedList, array(
                            "transaction_id"=>$value->transaction_id,
                            "delivery_city"=>$value->delivery_city,
                            "transactionDetails"=>$TSortDetails,
                            "platform" => $platform,
                            "remarks"=>'',
                        ));

                        if($value->qr_code == ''){
                            $transaction = Transaction::find($value->transaction_id);
                            $qrCode     = $transaction->do_no;
                            $qrCodeFile = $transaction->do_no.'.png';
                            // $path = 'images/qrcode/';
                            QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);
                            $transaction->qr_code = $qrCodeFile;
                            $transaction->save();
                        }
                        if($automaticGenerate){
                            $DOView = self::createDOView($transaction);
                            $view = View::make('checkout.do_view')
                                        ->with('display_details', $DOView['general'])
                                        ->with('display_trans', $DOView['trans'])
                                        ->with('display_seller', $DOView['paypal'])
                                        ->with('display_product', $DOView['product'])
                                        ->with('display_group', $DOView['group'])
                                        ->with('delivery_type', $DOView['delivery_type'])
                                        ->with('deliveryservice', $DOView['deliveryservice'])
                                        ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                                        ->with('multiPDF',true);

                            $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                            $html1 =  substr($sub,0,strpos($sub,"</body>"));
                            //$html1 = str_replace("<page_header>","",$html1);
                            //$html1 = str_replace("</page_header>","",$html1);
                            //$html1 = str_replace("ody>","",$html1);

                            if($loopCounter == $totalRecord){
                                $contents = $contents.(string)$html1.'<page></page>';
                            }else{
                                $contents = $contents.(string)$html1.'<page></page>';
                            }
                        }
                        
                    }
                    
                }
                
            }
//            echo "<pre>";
//            print_r($SuccesSortedList);
//            echo "</pre>";
            if($automaticGenerate){
                
                $sortedList = View::make('checkout.sorter_list')->with("transaction",$SuccesSortedList);
                $contents = $contents.(string)$sortedList;

                //$contents = str_replace("<page_header>","",$contents);
                //$contents = str_replace("</page_header>","",$contents);
                //$contents = str_replace("<page_header>","",$contents);
                // $contents = str_replace("</page_header>","",$contents);

                $contents = str_replace("ody>","",$contents);
                $contents = str_replace("page_header>","",$contents);
                $finale = '<html><body style="border: solid 1px #888080; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

                $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
                $file_name = date("Ymd")."_".date("His").'.pdf';

                $headers = array(
                    'Content-Type: application/pdf',
                );


                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                $html2pdf->setDefaultFont('arialunicid0');
                $html2pdf->WriteHTML($finale);
                $html2pdf->Output($file_path."/".$file_name, 'F');
                
            }
            
            
            // Update Database
            
            $totalFailed = 0;
            foreach ($FailedsortedList as $key => $vFL) {
                
                $is_failed = false;
                
                foreach ($vFL['transactionDetails'] as $kF => $vF) {
                    
                    if($vF['is_failed'] == 2 && $is_failed == false){
                        $is_failed = true;
                        $totalFailed++;    
                    }
                    
                }
                
            }
            
            $SortGenerator =  new SortGenerator();
            $SortGenerator->batch_no = $NewbatchNo;
//            $SortGenerator->filename = $file_name;
            $SortGenerator->total_success = count($SuccesSortedList);
            $SortGenerator->total_failed = $totalFailed;
            $SortGenerator->total_duplicate = count($DuplicatesortedList);
            $SortGenerator->created_by = Session::get("username");
            $SortGenerator->save();
            
            $SortID = $SortGenerator->id;
            
            // Save File
            $SortFile = new SortGeneratorFile();
            $SortFile->filename = $file_name;
            $SortFile->sort_id = $SortID;
            $SortFile->created_by = Session::get("username");
            $SortFile->updated_by = Session::get("username");
            $SortFile->activation = 1;
            $SortFile->save();
            
//            foreach ($SuccesSortedList as $kSS => $vSS) {
//                
//                $SortTransaction = new SortTransaction;
//                $SortTransaction->sort_id = $SortID;
//                $SortTransaction->transaction_id = $vSS['transaction_id'];
//                $SortTransaction->remarks = '';
//                $SortTransaction->created_by = Session::get("username");
//                $SortTransaction->generated = 1;
//                $SortTransaction->status = 1;
//                $SortTransaction->save();
//                
//            }
            
//            echo "<pre>";
//            print_r($FailedsortedList);
//            echo "</pre>";
            
            foreach ($FailedsortedList as $kSS => $vSS) {
                
                
//                $stockDetailsInfo = self::sortStockDetails($vSS['transaction_id']);
//                $generated = $stockDetailsInfo['failedStock'] > 0 ? 0:1;
                $generated = 0;
                
                $SortTransaction = new SortTransaction;
                $SortTransaction->sort_id = $SortID;
                $SortTransaction->transaction_id = $vSS['transaction_id'];
                $SortTransaction->remarks = $vSS['remarks'];;
                $SortTransaction->created_by = Session::get("username");
                $SortTransaction->generated = $generated;
                $SortTransaction->status = 1;
                $SortTransaction->save();
                
//                echo "<pre>";
//                print_r($vSS);
//                echo "</pre>";
                
                $sortTransactionID = $SortTransaction->id;
                
                $is_failed = false;
                
                foreach ($vSS['transactionDetails'] as $kTD => $vTD) {
                    
                    $SortTransactionDetails =  new SortTransactionDetails;
                    $SortTransactionDetails->sort_transaction_id = $sortTransactionID;
                    $SortTransactionDetails->logistic_item_id = $vTD['logistic_item_id'];
                    $SortTransactionDetails->product_id = $vTD['product_id'];
                    $SortTransactionDetails->type_product = $vTD['type_product'];
                    $SortTransactionDetails->order_quantity = $vTD['order_quantity'];
                    $SortTransactionDetails->available_quantity = $vTD['available_quantity'];
                    $SortTransactionDetails->is_failed = $vTD['is_failed'];
                    $SortTransactionDetails->remarks = '';
                    $SortTransactionDetails->save();
                    
                    if($vTD['is_failed'] == 2 && $is_failed == false){
                        $is_failed = true;
                    }
                    
                    $SorterStockLog = new SortStockLog();
                    $SorterStockLog->sort_transaction_details_id = $SortTransactionDetails->id; 
                    $SorterStockLog->sort_id = $SortID; 
                    $SorterStockLog->data = json_encode($vTD); 
                    $SorterStockLog->save();
                    
                }
                
                
                if($is_failed == false){
                    $SortTransaction->generated = 1;
                    $SortTransaction->save();
                }
                
                
   
            }
            
            foreach ($DuplicatesortedList as $kSD => $vSD) {
                
                $SortTransaction = new SortTransaction;
                $SortTransaction->sort_id = $SortID;
                $SortTransaction->transaction_id = $vSD['transaction_id'];
                $SortTransaction->remarks = $vSD['remarks'];
                $SortTransaction->created_by = Session::get("username");
                $SortTransaction->generated = 0;
                $SortTransaction->status = 2;
                $SortTransaction->save();
                
                $stockDetailsInfo = self::sortStockDetails($transaction_id);
                
                foreach ($stockDetailsInfo['sortStockDetails'] as $kTD => $vTD) {
                    
                    $SortTransactionDetails =  new SortTransactionDetails;
                    $SortTransactionDetails->sort_transaction_id = $sortTransactionID;
                    $SortTransactionDetails->logistic_item_id = $vTD['logistic_item_id'];
                    $SortTransactionDetails->product_id = $vTD['product_id'];
                    $SortTransactionDetails->type_product = $vTD['type_product'];
                    $SortTransactionDetails->order_quantity = $vTD['order_quantity'];
                    $SortTransactionDetails->available_quantity = $vTD['available_quantity'];
                    $SortTransactionDetails->remarks = '';
                    $SortTransactionDetails->save();
                    
                }
                
            }
            
            foreach ($RegenerateSortedList as $kSR => $vSR) {
                
                $SortTransaction = SortTransaction::find($vSR['sort_transaction_id']);
                $SortTransaction->regenarate = 1;
                $SortTransaction->generated = 0;
                $SortTransaction->status = 3;
                $SortTransaction->save();
                
            }
            
            
            
            $data =  array(
                "response" => '1',
                "batch_no" => $NewbatchNo,
                "total_success" => count($SuccesSortedList),
                "total_failed" => $totalFailed,
                "total_duplicate" => count($DuplicatesortedList),
            );
            
            $result = Warehouse::getSorterdeducation();
            $result1 =Warehouse::Stocklevelnotification();
        
    }
    
    public static function reservedStock($product_warehouse_id,$total_reserved,$transaction_id,$logistic_id,$batch_no,$username,$logistic_item_id){
        
        
        $WarehouseProductReserved = new WarehouseProductReserved;
        $WarehouseProductReserved->product_warehouse_id = $product_warehouse_id;
        $WarehouseProductReserved->total_reserved = $total_reserved;
        $WarehouseProductReserved->transaction_id = $transaction_id;
        $WarehouseProductReserved->logistic_id = $logistic_id;
        $WarehouseProductReserved->logistic_item_id = $logistic_item_id;
        $WarehouseProductReserved->batch_no = $batch_no; // Batch No declared at early of function
        $WarehouseProductReserved->activation = 1;
        $WarehouseProductReserved->status = 1; // Active Reserved // 2 ; should be completed reserved and deducted from reserved quantity
        $WarehouseProductReserved->is_completed = 0;
        $WarehouseProductReserved->created_by = $username != "" ? $username : "api_update";;
        $WarehouseProductReserved->updated_by = $username != "" ? $username : "api_update";;
        $WarehouseProductReserved->save();

        // $WarehouseStockProduct = Warehouse::where("id",$product_warehouse_id)
        //         ->where("status",1)->first();

        // $newTotalReserve = $WarehouseStockProduct->reserved_in_hand  + $total_reserved;

        // $WarehouseStockProduct->reserved_in_hand = $newTotalReserve; 
        // $WarehouseStockProduct->modify_by = $username != "" ? $username : "api_update";
        // $WarehouseStockProduct->save();
        
    }
    
    public static function sortStockDetails($transaction_id){
        
        
        // declare
        
        $collectionData = array();
        $stockCompareList = array();
        
        $Items = DB::table('logistic_transaction_item')
                    ->select(array(
                        'logistic_transaction_item.*'
                        ))
                    ->leftJoin('logistic_transaction', 'logistic_transaction.id', '=', 'logistic_transaction_item.logistic_id')  
                    ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')   
                    ->where("logistic_transaction.transaction_id",$transaction_id)->get();
        
        
        
                    
        $totalNotEnoughStock = 0;
                    
        foreach ($Items as $kI => $vI) {

            $BaseItems = DB::table('jocom_product_base_item')
                    ->where("product_id",$vI->product_id)
                    ->where("status",1)
                    ->get();

            $failedRemarks = '';
            $collectionData = array();

            if(count($BaseItems) > 0 ){
                // check base item stock
                foreach ($BaseItems as $kBI => $vBI) {

                    $StockInHand = Warehouse::getStockinhand($vBI->product_base_id);
                    $Product = Product::find($vBI->product_base_id);
                        array_push($collectionData, array(
                            "product_id" => $vBI->product_base_id,
                            "logistic_item_id" => $vI->id,
                            "type_product" => 1,
                            "order_quantity" => $vBI->quantity,
                            "available_quantity" => $StockInHand,
                            "remarks" => ''
                        ));

                    if($StockInHand < $vBI->quantity){
                        $totalNotEnoughStock++;
                    }

                }

            }else{

                $StockInHand = Warehouse::getStockinhand($vI->product_id);

                $Product = Product::find($vI->product_id);

                array_push($collectionData, array(
                        "product_id" => $vI->product_id,
                        "logistic_item_id" => $vI->id,
                        "type_product" => 0,
                        "order_quantity" => $vI->qty_order,
                        "available_quantity" => $StockInHand,
                        "remarks" => ''
                ));

                if($StockInHand < $vI->qty_order){
                    $totalNotEnoughStock++;
                }
            }
        }


    // Final Collection
    $results = array(
        "failedStock" => $totalNotEnoughStock,
        "sortStockDetails" =>$collectionData
    );

    // Return results
    return $results ;
        
    }
    
    public function getSortlist(){
        
         $orders = DB::table('jocom_sort_generator AS JSG')->select(array(
                         'JSG.id','JSG.batch_no','JSG.filename','JSG.collection','JSG.total_success','JSG.total_failed','JSG.total_duplicate','JSG.created_at','JSG.created_by'
                        ))
                    ->where('JSG.activation',1)
                    ->orderBy('JSG.id','DESC');

        return Datatables::of($orders)
        ->add_column('total_regenerated', function($orders){
            
                $sort_id = $orders->id;
               
                $total_remaining_failed = SortTransaction::where('regenarate',1)
                    ->where('sort_id',$sort_id)
                    ->where('activation',1)
                    ->count();
                    
                return $total_remaining_failed;
        })
        ->add_column('total_transactions', function($orders){
                $total_transactions = $orders->total_success + $orders->total_failed;
                return $total_transactions;
        })
        ->add_column('files', function($orders){
                $sort_id = $orders->id;
                $sort_files = SortGeneratorFile::where('sort_id',$sort_id)
                    ->where('sort_id',$sort_id)
                    ->where('activation',1)
                    ->get();
                
                return $sort_files;
        })
        ->add_column('total_remaining_failed', function($orders){
                // this one will be so confuse..much confuse than our life
                $sort_id = $orders->id;
               
                $total_remaining_failed = SortTransaction::where('generated',0)
                    ->where('regenarate',0)
                    ->where('sort_id',$sort_id)
                    ->where('activation',1)
                    ->count();
                    
                return $total_remaining_failed;
                
        })->make(true);
       
         
    }
    
    public function anySorttransactions(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        
        $sortId = Input::get("sortid");
        $CollectionfailedDO = array();
        $approvedList = array();
        
        
        $successDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->where('JST.sort_id',$sortId)
                    ->where(function ($query) {
                        $query->where('JST.generated',1)
                        ->orWhere('JST.regenarate',1)
                        ->orWhere('JST.special_pass',1);
                    })
                    ->orderBy('JST.transaction_id','DESC')->get();
        
        $failedDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                        'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ,'JT.do_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->where('JST.generated',0)
                    ->where('JST.regenarate',0)
                    ->where('JST.special_pass',0)
                    ->where('JST.status','<>',2)
                    ->where('JST.sort_id',$sortId)
                    ->orderBy('JST.transaction_id','DESC')->get();

//   
        
        $specialDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                        'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ,'JT.do_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->where('JST.generated',0)
                    ->where('JST.special_pass',1)
                    ->where('JST.status','<>',2)
                    ->where('JST.sort_id',$sortId)
                    ->orderBy('JST.transaction_id','DESC')->get();
        
        
        if(count($failedDO) > 0 ){
            
            foreach ($failedDO as $keyFailed => $valueFailed) {
                
                $subLineCollection = array();
                
                $subProducts = DB::table('jocom_sort_transaction_details AS JSTD')
                    ->select(array(
                        'JSTD.*','JP.name','JP.sku'
                        ))
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
                    ->where('JSTD.sort_transaction_id', '=', $valueFailed->id)
                    ->where('JSTD.status',1)
                    ->where('JSTD.status',1)->get();
                
                $subLineCollection = array(
                    "id" => $valueFailed->id,
                    "sort_id" => $valueFailed->sort_id,
                    "transaction_id" => $valueFailed->transaction_id,
                    "remarks" => $valueFailed->remarks,
                    "created_at" =>  $valueFailed->created_at,
                    "created_by" =>  $valueFailed->created_by,
                    "updated_at" => $valueFailed->updated_at,
                    "generated" => $valueFailed->generated,
                    "batch_no" => $valueFailed->batch_no,
                    "do_no" => $valueFailed->do_no,
                    "productCollection" => $subProducts,
                );
                
                array_push($CollectionfailedDO, $subLineCollection);
                
            }
        }
        
        
        if(count($specialDO) > 0 ){
            
            foreach ($specialDO as $keyFailed => $valueFailed) {
                
                $subLineCollection = array();
                
                $subProducts = DB::table('jocom_sort_transaction_details AS JSTD')
                    ->select(array(
                        'JSTD.*','JP.name','JP.sku'
                        ))
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
                    ->where('JSTD.sort_transaction_id', '=', $valueFailed->id)
                    ->where('JSTD.status',1)
                    ->where('JSTD.is_failed',2)
                    ->where('JSTD.status',1)->get();
                
                $subLineCollection = array(
                    "id" => $valueFailed->id,
                    "sort_id" => $valueFailed->sort_id,
                    "transaction_id" => $valueFailed->transaction_id,
                    "remarks" => $valueFailed->remarks,
                    "created_at" =>  $valueFailed->created_at,
                    "created_by" =>  $valueFailed->created_by,
                    "updated_at" => $valueFailed->updated_at,
                    "generated" => $valueFailed->generated,
                    "batch_no" => $valueFailed->batch_no,
                    "do_no" => $valueFailed->do_no,
                    "productCollection" => $subProducts,
                );
                
                array_push($approvedList, $subLineCollection);
                
            }
        }
        
        $DuplicateDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->where('JST.generated',0)
                    ->where('JST.status',2)
                    ->where('JST.sort_id',$sortId)
                    ->orderBy('JST.transaction_id','DESC')->get();
        
        
        $purchaseList = self::createpurchaselist2($sortId);
        
        return array(
            "batchNo" =>$successDO,
            "successList" =>$successDO,
            "failedList" =>$CollectionfailedDO,
            "approvedList" =>$approvedList,
            "DuplicateList" =>$DuplicateDO,
            "purchaseList" =>$purchaseList
        );
         
    }
    
    public static function createpurchaselist($sort_id){
        
        try{
            
       ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        
        $sort_id = $sort_id;
        // $sort_id = 'GDL0000000073';
        $finalList = array();
        $removedProduct = array();
       
        // OLD // 
        /*
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo,JS.company_name
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            LEFT JOIN jocom_product_seller AS JPS ON JPS.product_id = LTI.product_id
            LEFT JOIN jocom_seller AS JS ON JS.id = JPS.seller_id
            WHERE JST.sort_id = ".$sort_id."
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
        */   
        // OLD //   
        
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            WHERE JST.sort_id = ".$sort_id."
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
       
       $list = DB::select($query);
       
    //   echo "<pre>";
    //   print_r($list);
    //   echo "</pre>";
    //   die();
       
       $removeItems =  DB::table('jocom_sort_remove_purchase AS JSRP')
                    ->where("JSRP.sort_id",$sort_id)
                    ->where("JSRP.status",1)
                    ->get();
       
        foreach ($removeItems as $keyR => $valueR) {
            array_push($removedProduct, $valueR->product_id);
        }
            
       
       foreach ($list as $key => $value) {
           
            $baseProduct = array();
            $TotalTransactions = 0;

            
            $transactionID = $value->transaction_id;

            $baseItem = DB::table('jocom_sort_transaction_details AS JSTD')
                ->select(array(
                    'JP.name','JP.sku','JP.id','JPP.label',DB::raw('SUM(JSTD.order_quantity) as order_quantity'),'JPP.price','JPP.price_promo'
                    //,'JS.company_name'
                    ))
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_product_seller AS JPS', 'JPS.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->get();
                    // echo "<pre>";
                    // print_r($baseItem);
                    // echo "</pre>";
            
           $baseItem2 =  DB::table('jocom_product_base_item AS JPBI')
                    ->where("JPBI.price_option_id",$value->product_price_id)
                   ->where("JPBI.status",1)
                    ->get();
           
        //   echo "<pre>";
        //   print_r($baseItem2);
        //   echo "</pre>";
            
            if(count($baseItem2) > 0){
                
                foreach ($baseItem as $kB => $vB) { 
                    
                    // GET Price //
                    $baseProductPrice =  DB::table('jocom_transaction_details_base_product AS JTDBP')
                        ->select(array(
                                'JTDBP.*'
                        ))
                        ->leftjoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'JTDBP.transaction_details_id')
                        ->where("JTD.transaction_id",$transactionID)
                        ->where("JTD.p_option_id",$value->product_price_id)
                        ->first();
                    // GET Price //
                    
                     $sellerInfo = DB::table('jocom_product_seller AS JPS')
                        ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
                        ->where("JPS.product_id",$vB->id)
                        ->where("JPS.activation",1)->first();
                    
                    if(!in_array($vB->id, $removedProduct)){
                        array_push($baseProduct, array(
                            "product_name" => $vB->name,
                            "product_id" => $vB->id,
                            "product_label" => $vB->label,
                            "company_name" =>  $sellerInfo->company_name,//$vB->company_name,
                            "product_sku" => $vB->sku,
                            "unit_price" => 'TEST2',// $baseProductPrice->price > 0 ? $baseProductPrice->price : '-',
                            "quantityPerSet" =>  $value->qty_order_set ,
                            "totalQuantity" => $vB->order_quantity
                        ));
                    }
                       
                }
                
            }
            
            
            $totalTransaction = DB::table('jocom_sort_transaction_details AS JSTD')
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->count();
            
            $ProductInfo = DB::table('jocom_products')->
                    where("sku",$value->sku)->first();
            
            if(!in_array($ProductInfo->id, $removedProduct)){
                
            // Get Seller Info //
            
            $sellerInfo = DB::table('jocom_product_seller AS JPS')
            ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JPS.product_id",$ProductInfo->id)
            ->where("JPS.activation",1)->first();
            
           
            array_push($finalList, array(
                "product_name" => $value->name,
                "product_sku" => $value->sku,
                "product_id" => $ProductInfo->id,
                "product_label" => $value->label,
                "company_name" => $sellerInfo->company_name, //$value->company_name,
                "option_id" => $value->product_price_id,
                "unit_price" => 'TEST',// $value->price,
                "total_order" => $value->TotalTransactions,
                "stock_type" => $value->type_product,
                "req_qty" => $value->TotalOrderSet,
                //"in_stock" => $value->in_stock,
                //"balance_need" => $value->req_qty - $value->in_stock,
                "base_product" => $baseProduct,
            ));
           
       }
       
            
           
       }
       
        }catch(exception $ex){
            echo $ex->getMessage();
        }finally{
            
           
            return $finalList;
         
           
        }


      
       
       
       
                
    }
    public static function doPrinter($logisticID,$sort_id = "",$create_separator = false,$listing = false,$fileName = ""){
        
        try{
            
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $separator = '';
        $loopCounter = 0;
        $totalRecord = count($logisticID);
        $SuccesSortedList = array();
        $sortedList = "";
        

        foreach ($logisticID as $key => $value) {

            $loopCounter++;
            // echo $value->transaction_id . '<br>';
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            // echo $value->transaction_id .'<br>';
            $transaction = Transaction::find($value->transaction_id);
            
            $platform="";
            if($value->buyer_email == 'lazada@tmgrocer.com'){
                $platform = 'Lazada';
            } else if ($value->buyer_email == 'shopee@tmgrocer.com'){
                $platform = 'Shopee';
            } else if ($value->buyer_email == 'fnlife@tmgrocer.com'){
                $platform = 'FN-Life';
            } else if ($value->buyer_email == 'fnlifesuite@tmgrocer.com'){
                $platform = 'FN-TikTok';
            }
            else
            {
                $platform = 'tmGrocer';
            }
            
            
            array_push($SuccesSortedList, array(
                "transaction_id" => $value->transaction_id,
                "delivery_city" => $value->delivery_city,
                "platform" => $platform,
            ));
            
            if($value->qr_code == ''){

                    
                    $qrCode     = $transaction->do_no;
                    $qrCodeFile = $transaction->do_no.'.png';
                    // $path = 'images/qrcode/';
                    QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);
                    $transaction->qr_code = $qrCodeFile;
                    $transaction->save();
            }

            $DOView = self::createDOView($transaction);

            $view = View::make('checkout.do_view')
                        ->with('display_details', $DOView['general'])
                        ->with('display_trans', $DOView['trans'])
                        ->with('display_seller', $DOView['paypal'])
                        ->with('display_product', $DOView['product'])
                        ->with('display_group', $DOView['group'])
                        ->with('delivery_type', $DOView['delivery_type'])
                        ->with('deliveryservice', $DOView['deliveryservice'])
                        ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                        ->with('multiPDF',true);

            $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
            $html1 =  substr($sub,0,strpos($sub,"</body>"));
            //$html1 = str_replace("<page_header>","",$html1);
            //$html1 = str_replace("</page_header>","",$html1);
            //$html1 = str_replace("ody>","",$html1);

            if($loopCounter == $totalRecord){
                $contents = $contents.(string)$html1.'<page></page>';
            }else{
                $contents = $contents.(string)$html1.'<page></page>';
            }

            


        }
        if($listing){
            $sortedList = View::make('checkout.sorter_list')->with("transaction",$SuccesSortedList);
        }

       
        $contents = $contents.(string)$sortedList;

        $contents = str_replace("ody>","",$contents);
        $contents = str_replace("page_header>","",$contents);
        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';
    
        


        $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
        
        if($fileName != ""){
            $file_name = $fileName;
        }else{
            $file_name = date("Ymd")."_".date("His").'.pdf';
        }
        

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        $html2pdf->Output($file_path."/".$file_name, 'F');

        // Update Database
        // Save File

        if($sort_id != ''){

            $SortFile = new SortGeneratorFile();
            $SortFile->filename = $file_name;
            $SortFile->sort_id = $sort_id;
//                $SortFile->created_by = Session::get("username");
//                $SortFile->updated_by = Session::get("username");
            $SortFile->activation = 1;
            $SortFile->save();

            return $file_name;

        }
        
        return $file_name;
        
        } catch(Exception $ex){
            
            echo $ex->getMessage();
            echo $ex->getLine();
            echo $ex->getTrace();
            echo $ex->getFile();
        }
                
    }
    
    public static function generator($LogisticTransaction, $sort_id = 0){
        
        
        
        
        if($sort_id != 0){
            
            $SortGenerator = SortGenerator::find($sort_id);
            $NewbatchNo = $SortGenerator->batch_no;
            $failed_do_only = true;
            $allProductAvailable = true;
            
        }
        
        
        // Predefined Variable
        $isError = false;
        $stateList = [];
        $TIDCollection = [];
        $SuccesSortedList = array();
        $FailedsortedList = array();
        $DuplicatesortedList = array();
        $RegenerateSortedList = array();
        $contents = '';

        // Filter parameter
  //            $from_id = 23951;
  //            $to_id = 24169;
  //            $region_id = 1;
        $loopCounter = 0;
        $separator = '';
        
        foreach ($LogisticTransaction as $key => $value) {
                
                // Main player in this loop: Transaction ID and Logistic ID
                // Who change this beware!! 
                $logisticID = $value->LogisticID;
                $transactionID = $value->transaction_id;
                
                // Check on if order duplicate / already generate
                $Duplicate = DB::table('jocom_sort_transaction')
                    ->select(array(
                        'jocom_sort_transaction.*','jocom_sort_generator.batch_no'
                        ))
                    ->leftJoin('jocom_sort_generator', 'jocom_sort_generator.id', '=', 'jocom_sort_transaction.sort_id')    
                    ->where("jocom_sort_transaction.transaction_id",$transactionID)
                    //->where("jocom_sort_transaction.generated",1)
                    ->whereIn('jocom_sort_transaction.generated', array(1))
                    ->where("jocom_sort_transaction.status",1)
                    ->where("jocom_sort_transaction.activation",1)->first();
  
                if(count($Duplicate) > 0){
                    
                     
                    array_push($DuplicatesortedList, array(
                        "transaction_id" => $transactionID,
                        "remarks"=>"Duplicate : Batch No : ".$Duplicate->batch_no,
                    ));
                    
                }else{
                    
                    
                    
                    $Items = DB::table('logistic_transaction_item')
                    ->select(array(
                        'logistic_transaction_item.*'
                        ))
                    ->leftJoin('logistic_transaction', 'logistic_transaction.id', '=', 'logistic_transaction_item.logistic_id')  
                    ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')   
                    ->where("logistic_transaction.transaction_id",$transactionID)->get();
                    
//                    echo "======= PRODUCT ITEMS ==========";
//                    echo "<pre>";
//                    print_r($Items);
//                    echo "</pre>";
//                    echo "======= PRODUCT ITEMS ==========";
                    
                    
                    $totalNotEnoughStock = 0;
                    
                    // $allProductAvailable : Indicator to define all base items are have enough available stock in hand.
                    $allProductAvailable = true;
                    $allProductAvailableCollection = array();
                    
                    
                    // OPEN LOOPING ITEM
                    foreach ($Items as $kI => $vI) { 

                        $BaseItems = DB::table('jocom_product_base_item AS JPBI')
                                ->select(array(
                                    'JPBI.*','JP.sku'
                                ))
                                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')  
                                ->where("JPBI.product_id",$vI->product_id)
                                ->where("JPBI.price_option_id",$vI->product_price_id)
                                ->where("JPBI.status",1)
                                ->get();
                        
                        $failedRemarks = '';
                        $TSortDetails = array();
                        
                        // Have base item so not main stock product
                        if(count($BaseItems) > 0 ) {
                            
                            // check base item stock
                            echo "IS BASE";
                            
                            foreach ($BaseItems as $kBI => $vBI) {
                                
//                                echo "<pre>";
//                                print_r($vBI);
//                                echo "</pre>";
                                $quantity_require = $vBI->quantity * $vI->qty_order;
                                $StockInHand = Warehouse::getStockavailable($vBI->product_base_id,$quantity_require,'CMS');
                               
                                $ProductWareHouseID = $StockInHand['productWarehouseID'];
                                $Product = Product::find($vBI->product_base_id);
                                
                                // Proceed to reserve the stock : Becareful !!
                                if($StockInHand['stockExist'] == 1){
                                    
                                    // HAVE ENOUGH AVAILABLE STOCK
                                    if( $StockInHand['stockEnough'] == 1){
                                        
                                        // 1. Reserved Stock 
                                        // 2. Store in collection first because need to see other base item also enough stock or not
                                        array_push($allProductAvailableCollection, array(
                                            "productWarehouseID" => $ProductWareHouseID,
                                            "product_id" => $StockInHand['productID'],
                                            "quantity_require" => $StockInHand['stockRequired'],
                                            "logistic_id" => $logisticID,
                                            "transaction_id" => $transactionID
                                        ));
                                    
                                    }else{
                                        
                                    //  NOT ENOUGH AVAILABLE STOCK
                                        array_push($TSortDetails, array(
                                            "product_id" => $vBI->product_base_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 1,
                                            "order_quantity" => $vBI->quantity * $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "remarks" => ''
                                        ));

                                        $failedRemarks = $failedRemarks.'Not enough Base Stock 1: '.$Product->qrcode.", Order: ".$vBI->quantity.", Stock : " .$StockInHand['stockAvailable']." <br>";
                                        $totalNotEnoughStock++; 
                                        
                                        // Set flag to false //
                                        $allProductAvailable = false;  
                                    }
                                    
                                }else{
                                    
                                    // Not Allow because no stock Exist / registered in warehouse
                                    // Set flag to false //
                                    array_push($TSortDetails, array(
                                        "product_id" => $vBI->product_base_id,
                                        "logistic_item_id" => $vI->id,
                                        "type_product" => 1,
                                        "order_quantity" => $vBI->quantity * $vI->qty_order,
                                        "available_quantity" => (int)$StockInHand['stockAvailable'],
                                        "remarks" => 'Stock Not Registered'
                                    ));

                                    $failedRemarks = $failedRemarks.'Not enough Base Stock 2: '.$Product->qrcode.", Order: ".$vBI->quantity.", Stock : " .$StockInHand['stockAvailable']." <br>";
                                    $totalNotEnoughStock++; 
                                    $allProductAvailable = false;  
                                        
                                }
                                
                            }
                            
                            if($totalNotEnoughStock > 0){
                                array_push($FailedsortedList, array(
                                    "transaction_id"=>$transactionID,
                                    "transactionDetails"=>$TSortDetails,
                                    "remarks"=>$failedRemarks,
                                ));
                            }
                            
                        }else{
                            echo "IS NOT";
                            // FOR NOT BASE PRODUCT ITEM
                            $quantity_required = $vI->qty_order;
                            $StockInHand = Warehouse::getAvailableStock($vI->product_id,$quantity_required);
                            $ProductWareHouseID = $StockInHand['productWarehouseID'];
                            
                            $Product = Product::find($vI->product_id);
                            
                            // STOCK EXIST
                            if($StockInHand['stockExist'] == 1){
                                
                                
                                $Product = Product::find($vI->product_id);
                                
                                // CHECK QTY ORDER LESS THAN AVAILABLE QUANTITY
                                // HAVE ENOUGH AVAILABLE STOCK
                                if( $StockInHand['stockEnough'] == 1){
                                    
                                    array_push($allProductAvailableCollection, array(
                                            "productWarehouseID" => $ProductWareHouseID,
                                            "product_id" => $StockInHand['productID'],
                                            "quantity_require" => $StockInHand['stockRequired'],
                                            "logistic_id" => $logisticID,
                                            "transaction_id" => $transactionID
                                    ));
                                    
                                }else{
                                    // NOT ENOUGH
                                    // add remark no stock for failed list
                                    $failedRemarks = 'Not enough Stock 3: '.$Product->qrcode.", Order: ".$vI->qty_order.", Stock : " .$StockInHand['stockAvailable']."";

                                    array_push($TSortDetails, array(
                                            "product_id" => $vI->product_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 0,
                                            "order_quantity" => $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "remarks" => ''
                                        ));

                                    array_push($FailedsortedList, array(
                                        "transaction_id"=>$value->transaction_id,
                                        "remarks"=>$failedRemarks,
                                        "transactionDetails"=>$TSortDetails,
                                    ));
                                    $totalNotEnoughStock++;
                                    
                                    // Not Allow because not enough stock
                                    $allProductAvailable = false;  
                                }
                                
                            }else{
                                
                                // STOCK NOT EXIST
                                // Not Allow because no stock Exist / registered in warehouse
                                array_push($TSortDetails, array(
                                            "product_id" => $vI->product_id,
                                            "logistic_item_id" => $vI->id,
                                            "type_product" => 0,
                                            "order_quantity" => $vI->qty_order,
                                            "available_quantity" => (int)$StockInHand['stockAvailable'],
                                            "remarks" => ''
                                        ));
                                
                                $failedRemarks = 'Not enough Stock 4: '.$Product->qrcode.", Order: ".$vI->qty_order.", Stock : " .$StockInHand['stockAvailable']."";

                                array_push($FailedsortedList, array(
                                    "transaction_id"=>$value->transaction_id,
                                    "remarks"=>$failedRemarks,
                                    "transactionDetails"=>$TSortDetails,
                                ));
                                $allProductAvailable = false;  
                                
                            }
                        }
                        
                        
                    }
                    // CLOSE LOOPING ITEM
                    
                    // Check of all valid for reserver or not: Check now if indicator TRUE
//                    echo "<pre>";
//                    print_r($allProductAvailableCollection);
//                    echo "</pre>";
//                    exit();    
                        
                    if($allProductAvailable){
                        
                        echo "<pre>";
                        print_r($allProductAvailableCollection);
                        echo "</pre>";

                        // Proceed for reserve , Start reserve stock
                        foreach ($allProductAvailableCollection as $kResPro => $vResPro) {

                            // OPEN: Store reserve list
                            $WarehouseProductReserved = new WarehouseProductReserved;
                            $WarehouseProductReserved->product_warehouse_id = $vResPro['productWarehouseID'];
                            $WarehouseProductReserved->total_reserved = $vResPro['quantity_require'];
                            $WarehouseProductReserved->transaction_id = $vResPro['transaction_id'];
                            $WarehouseProductReserved->logistic_id = $vResPro['logistic_id'];
                            $WarehouseProductReserved->batch_no = $NewbatchNo; // Batch No declared at early of function
                            $WarehouseProductReserved->activation = 1;
                            $WarehouseProductReserved->status = 1; // Active Reserved // 2 ; should be completed reserved and deducted from reserved quantity
                            $WarehouseProductReserved->is_completed = 0;
                            $WarehouseProductReserved->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";;
                            $WarehouseProductReserved->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";;
                            $WarehouseProductReserved->save();
                            // CLOSE: Store reserve list

                            // UPDATE MAIN STOCK TABLE
                            $WarehouseStockProduct = Warehouse::where("id",$vResPro['productWarehouseID'])
                                    ->where("status",1)->first();
                            
//                            echo "---------------------";
//                            echo "<pre>";
//                            print_r($ProductWareHouseID); 
//                            print_r($WarehouseStockProduct); 
//                            echo $WarehouseStockProduct->reserved_in_hand  + $vResPro['quantity_require']; 
//                            echo "</pre>";
//                            echo "---------------------";
                            
                            $newTotalReserve = $WarehouseStockProduct->reserved_in_hand  + $vResPro['quantity_require'];
                            $WarehouseStockProduct->reserved_in_hand = $newTotalReserve; 
                            $WarehouseStockProduct->modify_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                            $WarehouseStockProduct->save();
                            // UPDATE MAIN STOCK TABLE
                        }

                    }
    
                    // Open: Create list for Regenerate
                    
                    if($is_include_failed == true || $failed_do_only == true){
                        
                        $Regenerate = DB::table('jocom_sort_transaction')
                            ->select(array(
                                'jocom_sort_transaction.*','jocom_sort_generator.batch_no'
                                ))
                            ->leftJoin('jocom_sort_generator', 'jocom_sort_generator.id', '=', 'jocom_sort_transaction.sort_id')    
                            ->where("jocom_sort_transaction.transaction_id",$transactionID)
                            ->where("jocom_sort_transaction.generated",0)
                            ->where("jocom_sort_transaction.status",1)
                            ->where("jocom_sort_transaction.activation",1)->first();
                        
                        array_push($RegenerateSortedList, array(
                            "sort_transaction_id"=>$Regenerate->id,
                            "transaction_id"=>$Regenerate->transaction_id,
                            "remarks"=>'',
                        ));
                    }
                    // Close: Create list for Regenerate 
                    
                    $loopCounter++;
                    
                    // CREATE SEPARATOR
                    if($create_separator){
                        if($separator != $value->delivery_city){
                            $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                            $separator = $value->delivery_city;
                        }
                    }
                    // CREATE SEPARATOR
                     
                    if($allProductAvailable){
                        
                        $platform="";
                        if($value->buyer_email == 'lazada@tmgrocer.com'){
                            $platform = 'Lazada';
                        } else if ($value->buyer_email == 'shopee@tmgrocer.com'){
                            $platform = 'Shopee';
                        } else if ($value->buyer_email == 'fnlife@tmgrocer.com'){
                            $platform = 'FN-Life';
                        } else if ($value->buyer_email == 'fnlifesuite@tmgrocer.com'){
                            $platform = 'FN-TikTok';
                        }
                        else
                        {
                            $platform = 'tmGrocer';
                        }
                        
                        $transaction = $value;

                        array_push($SuccesSortedList, array(
                            "transaction_id"=>$value->transaction_id,
                            "delivery_city"=>$value->delivery_city,
                            "transactionDetails"=>$TSortDetails,
                            "platform" => $platform,
                            "remarks"=>'',
                        ));

                        if($value->qr_code == ''){
                            $transaction = Transaction::find($value->transaction_id);
                            $qrCode     = $transaction->do_no;
                            $qrCodeFile = $transaction->do_no.'.png';
                            // $path = 'images/qrcode/';
                            QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);
                            $transaction->qr_code = $qrCodeFile;
                            $transaction->save();
                        }

                        $DOView = self::createDOView($transaction);

                        $view = View::make('checkout.do_view')
                                    ->with('display_details', $DOView['general'])
                                    ->with('display_trans', $DOView['trans'])
                                    ->with('display_seller', $DOView['paypal'])
                                    ->with('display_product', $DOView['product'])
                                    ->with('display_group', $DOView['group'])
                                    ->with('delivery_type', $DOView['delivery_type'])
                                    ->with('deliveryservice', $DOView['deliveryservice'])
                                    ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                                    ->with('multiPDF',true);

                        $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                        $html1 =  substr($sub,0,strpos($sub,"</body>"));
                        //$html1 = str_replace("<page_header>","",$html1);
                        //$html1 = str_replace("</page_header>","",$html1);
                        //$html1 = str_replace("ody>","",$html1);

                        if($loopCounter == $totalRecord){
                            $contents = $contents.(string)$html1.'<page></page>';
                        }else{
                            $contents = $contents.(string)$html1.'<page></page>';
                        }
                    }
                    
                }
                
            }
            echo "SUCCESS";
            
            echo "SUCCESS";
            
           
            
            
            
            $sortedList = View::make('checkout.sorter_list')->with("transaction",$SuccesSortedList);
            $contents = $contents.(string)$sortedList;
            
//            echo $contents;
            
            //$contents = str_replace("<page_header>","",$contents);
            //$contents = str_replace("</page_header>","",$contents);
            //$contents = str_replace("<page_header>","",$contents);
            // $contents = str_replace("</page_header>","",$contents);
            
            $contents = str_replace("ody>","",$contents);
            $contents = str_replace("page_header>","",$contents);
            $finale = '<html><body style="border: solid 1px #888080; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

            $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
            $file_name = date("Ymd")."_".date("His").'.pdf';
            
            $headers = array(
                'Content-Type: application/pdf',
            );
            
            if(count($SuccesSortedList) > 0 ){
                
                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                $html2pdf->setDefaultFont('arialunicid0');
                $html2pdf->WriteHTML($finale);
                $html2pdf->Output($file_path."/".$file_name, 'F');

                // Update Database

                // Save File
                $SortFile = new SortGeneratorFile();
                $SortFile->filename = $file_name;
                $SortFile->sort_id = $sort_id;
                $SortFile->created_by = Session::get("username");
                $SortFile->updated_by = Session::get("username");
                $SortFile->activation = 1;
                $SortFile->save();

                 echo "<pre>";
                print_r($SuccesSortedList);
                echo "</pre>";


                foreach ($SuccesSortedList as $kSS => $vSS) {

                    $SortThis = SortTransaction::where("sort_id",$sort_id)
                            ->where("transaction_id",$vSS["transaction_id"])->first();
                    echo "SORT ID: ".$SortThis->id;

                    $SortTransaction = SortTransaction::find($SortThis->id);
                    $SortTransaction->updated_by = Session::get("username");
                    $SortTransaction->regenarate = 1;
                    $SortTransaction->save();

                }
                
            }
            
        
        
    }
    
    public function anyPassdo(){
        
        $is_error = false;
        $result = 1;
        $response = 1;
        
        try{
            
            DB::beginTransaction();
            
            $TIDCollection = array();
            
            $proceedlist =  array();
            
            $totalFailed = 0;
            $totalSuccess = 0;
            
            $sort_id = Input::get("sort_id");
            $transactionIDs = Input::get("transaction_id");
            
            $SortGenerator = SortGenerator::find($sort_id);
            $batchNO = $SortGenerator->batch_no;
            
            $SortTransaction = SortTransaction::where("sort_id",$sort_id)
                    ->where("generated",0)
                    ->where("regenarate",0)
                    ->whereIn("transaction_id",$transactionIDs)
                    ->get();
        
            if(count($SortTransaction) > 0 ){
                    
                    /*
                     * 1. Set special pass = 1
                     * 2. Update reserved stock = 1
                     * 3. Update remarks
                     * 4. Regenerate DO
                     */
                    
                    foreach ($SortTransaction as $key => $vST) {
                        
                        $transaction_id = $vST->transaction_id;
                        $TIDCollection[] = $transaction_id;
                        $sort_transaction_id = $vST->id;
                        $LogisticTransaction = LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        
                        
                        
                        // UPDATE RESERVATION STOCK

                        
                        $TDetails = DB::table('logistic_transaction AS LT')
                                    ->select(array(
                                        'LTI.*'
                                    ))
                                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')  
                                    //->leftJoin('jocom_sort_transaction_details AS JSTD', 'JSTD.logistic_item_id', '=', 'LTI.id')  
                                    //->where("JSTD.is_failed",2)
                                    ->where("LT.transaction_id",$transaction_id)
                                    ->get();
                                    
                                   
                        
                        $allSuccess = true;
                        $reserveItems = array();
                        
                        foreach ($TDetails as $key => $value) {
                            
                            $logistic_item_id = $value->id;
                            
                            // echo "Product ID".$value->product_id;
                                    
                            $BaseItems = DB::table('jocom_product_base_item AS JPBI')
                                    ->select(array(
                                        'JPBI.*','JP.sku'
                                    ))
                                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')  
                                    ->where("JPBI.product_id",$value->product_id)
                                    ->where("JPBI.price_option_id",$value->product_price_id)
                                    ->where("JPBI.status",1)
                                    ->get();
                                    
                            // echo "<pre>";
                           
                            // print_r($BaseItems);
                            // echo "</pre>";
                           
                
                            if(count($BaseItems) > 0){


                                foreach ($BaseItems as $keyBI => $valueBI) {

                                    $product_base_id = $valueBI->product_base_id;
                                    // echo "PBI".$product_base_id;
                                    $quantity_required = $valueBI->quantity * $value->qty_order;

                                    $StockInHand = Warehouse::getStockavailable($product_base_id,$quantity_required,'CMS');
                            //       echo "<pre>";
                            //       echo "wira";
                            // print_r($StockInHand);
                            // echo "</pre>";

                                    if(isset($StockInHand['reservedData']) && isset($StockInHand['reservedData']['ProductWareHouseID']) && $StockInHand['reservedData']['ProductWareHouseID'] != '' ){

                                        $isReserved = DB::table('jocom_warehouse_product_reserved AS JWPR')
                                                ->where("JWPR.product_warehouse_id",$StockInHand['reservedData']['ProductWareHouseID'])
                                                ->where("JWPR.logistic_item_id",$logistic_item_id)
                                                ->first();
                                        
                                        if(count($isReserved) <= 0){
                                            
                                            array_push($reserveItems, array(
                                                "product_warehouse_id" => $StockInHand['reservedData']['ProductWareHouseID'],
                                                "total_reserved" => $StockInHand['reservedData']['quantity'] * $quantity_required,
                                                "transaction_id" => $transaction_id,
                                                "logistic_id" => $LogisticTransaction->id,
                                                "batch_no" => $batchNO,
                                                "username" => Session::get('username') != "" ? Session::get('username') : "api_update",
                                                "logistic_item_id" =>$logistic_item_id
                                            ));
                                            
                                        }

                                    }else{
                                        
                                        if($allSuccess){
                                            // echo $product_base_id;
                                            $allSuccess = false;
                                        }
                                    }

                                }

                            }else{
                        
                                $product_base_id = $value->product_id;
                                $quantity_require = $value->qty_order;
                     
                                $StockInHand = Warehouse::getStockavailable($product_base_id,$quantity_require,'CMS');
                           
                                if(isset($StockInHand['reservedData']['response']) && $StockInHand['reservedData']['response'] == 1){
                                    
                                    // push into proceedlist
                                    array_push($reserveItems, array(
                                          "product_warehouse_id" => $StockInHand['reservedData']['ProductWareHouseID'],
                                          "total_reserved" => $StockInHand['reservedData']['quantity'] * $quantity_require,
                                          "transaction_id" => $transaction_id,
                                          "logistic_id" => $LogisticTransaction->id,
                                          "batch_no" => $batchNO,
                                          "username" => Session::get('username') != "" ? Session::get('username') : "api_update",
                                          "logistic_item_id" =>$logistic_item_id
                                      ));
                                    
                                }else{
                                    // throw exception
                                    if($allSuccess){
                                        // echo $product_base_id;
                                        $allSuccess = false;
                                    }
                                }

                            }
                            
                        }
                        // echo "<pre>";
                        // print_r($StockInHand);
                        // echo "</pre>";
                       
                        if($allSuccess){
                             // Start Reserved Stock Here
                            $STransactions = SortTransaction::find($sort_transaction_id);
                            $STransactions->special_pass = 1;
                            $STransactions->regenarate = 1;
                            $STransactions->save();
                            
                            foreach ($reserveItems as $kR => $vR) {
                                self::reservedStock($vR['product_warehouse_id'],$vR['total_reserved'], $vR['transaction_id'], $vR['logistic_id'], $vR['batch_no'], $vR['username'],$vR['logistic_item_id']);
                            }   
                            
                            $totalSuccess++;
                            // Start Reserved Stock Here
                        }else{
                            $totalFailed++;
                        }
                           
                    }
                    // STEP 4:
//                    $LogisticTransaction = DB::table('logistic_transaction')
//                    ->select(array(
//                        'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
//                        ))
//                    ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
//                    ->whereIn('logistic_transaction.transaction_id', $TIDCollection)
//                    ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
//                    ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
//                    ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
//                    ->orderBy('logistic_transaction.transaction_id', 'ASC')
//                    //->limit(5)
//                    ->get();


                   // self::doPrinter($LogisticTransaction,$sort_id);
             
                
            }else{
                
            }
            $result = Warehouse::getSorterdeducation();

        } catch (Exception $ex) {
            $response = 0;
            $is_error = true;
            echo $ex->getMessage();
            echo $ex->getLine();
         print_r($ex->getTraceAsString());
        }  finally {
            if($is_error){
                DB::rollback();
            }else{
                DB::commit();
                //  DB::rollback();
            }
            
            return array(
              "response" => $response,
              "result" => array(
                  "totalApproved" => $totalSuccess,
                  "totalFailed" => $totalFailed,
                  "message" => $totalFailed
              )
          );
        }
        
         
        
    }
    
    public function anyPrintsuccessdo(){
        
        $is_error = false;
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        try{
            
            $TIDCollection = array();
            $createSeparator = false;
            
            $sort_id = Input::get("sort_id");
            $separator = Input::get("separator");
            
            if($separator == 1){
               $createSeparator = true;
            }
           
            
            $successDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->where('JST.sort_id',$sort_id)
                    ->where(function ($query) {
                        $query->where('JST.generated',1)
                        ->orWhere('JST.regenarate',1)
                        ->orWhere('JST.special_pass',1);
                    })
                    ->orderBy('JST.transaction_id','DESC')->get();
            
//            $transID = $successDO->pluck("transaction_id");
            
            foreach ($successDO as $key => $value) {
                $TIDCollection[] = $value->transaction_id;
            }
            
            
            $LogisticTransaction = DB::table('logistic_transaction')
            ->select(array(
                'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
                ))
            ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
            ->whereIn('logistic_transaction.transaction_id', $TIDCollection)
            ->where("jocom_transaction.status",'completed')
            ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
            ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
            ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
            ->orderBy('logistic_transaction.transaction_id', 'ASC')
            //->limit(5)
            ->get();
            
            
            
           $filename =  self::doPrinter($LogisticTransaction, $sort_id, $createSeparator,true);
            
        } catch (Exception $ex) {
            
            $is_error = true;
            
        }  finally {
            if($is_error){
                DB::rollback();
                return array(
                    "response" => 0,
                    "filename" =>  $filename
                );
            }else{
                DB::commit();
                return array(
                    "response" => 1,
                    "filename" =>  $filename
                );
            }
          
            
            
        }
        
        
    }
    
    public function anyGeneratefailed(){
        
        try{
        
        DB::beginTransaction();
        
     
        
        $sort_id = Input::get("sort_id");
       
        $TIDCollection = array();
        
       
        
        $failedTransaction = DB::table('jocom_sort_transaction')
                ->where("sort_id",$sort_id)
                ->where("generated",0)
                ->where("regenarate",0)
                ->get();
       
        
        foreach ($failedTransaction as $key => $value) {
            $TIDCollection[] = $value->transaction_id;
        }
        
        $LogisticTransaction = DB::table('logistic_transaction')
            ->select(array(
                'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
                ))
            ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
            ->whereIn('logistic_transaction.transaction_id', $TIDCollection)
            //->where("logistic_transaction.transaction_id",">=",$from_id)
            //->where("logistic_transaction.transaction_id","<=",$to_id)
            //->where("status",0)
            ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
            ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
            ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
            ->orderBy('logistic_transaction.transaction_id', 'ASC')
            //->limit(5)
            ->get();
        
        
            self::generator($LogisticTransaction,$sort_id);
       
        
            
        } catch (Exception $ex) {

        } 
        finally {
            
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
    }
    
    public function getFailedsortlist(){
        
         $orders = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->where('JST.activation',1)
                    ->where('JST.generated',0)
                    ->where('JST.regenarate',0)
                    ->where('JST.status',1)
                    ->orderBy('JST.id','DESC');

        return Datatables::of($orders)->make(true);
       
    }
    
    public function anyDownloadsorter(){
        
        
        $file_id = Input::get("id");
        $SortedList = SortGeneratorFile::find($file_id);
        $file_name = $SortedList->filename;
        
        $file_path = $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
        return Response::download($file_path."/".$file_name);
        
       
        
    }
    
    public function anyDownloadcombinesorter(){
        

        $file_id = Input::get("id");
        $SortedList = SortCombine::find($file_id);
        
        $file_name = $SortedList->filename;
        $file_path = $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
        return Response::download($file_path."/".$file_name);
        
       
        
    }
    
    public function anyExportpurchaselist(){
        
        try{
            
            $sort_id = Input::get("sort_id");
//            $sort_id = 1;
            $sorterData = DB::table('jocom_sort_generator AS JSG')
                    ->where('JSG.id',$sort_id)
                    ->first();
            
            $batchNo = $sorterData->batch_no;
            
            $data  = self::createpurchaselist2($sort_id);
            
            $SortGenerator = SortGenerator::where("id",$sort_id)->first();
            $date = $SortGenerator->created_at;
            $batchNo = $SortGenerator->batch_no;

            return Excel::create('PURCHASE_LIST_'.$batchNo.'_'.date("dmyHis"), function($excel) use ($data,$date,$batchNo) {
                    $excel->sheet('Purchase List', function($sheet) use ($data,$date,$batchNo)
                    {   
                        $sheet->loadView('emails.purchaselist', array('data' =>$data,'date'=>$date,'batchNo'=>$batchNo));
                        
                    });
                })->download('xls');
//            
        
        
        } catch (Exception $ex) {

        }
        
        
        
    }
    
    public function anyPurchaselist(){
        
        return View::make('emails.purchaselist');
        
    }
    
    /*
     * Module : Sorter
     */
    public function anyRemoveitempurchase(){
        
        $isError = false;
        $response = 1;
        $totalRemoved = 0;
        
        try{
            DB::beginTransaction();
            
            $sort_id = Input::get("sort_id");
            $productIDs = Input::get("product_id");
           
            foreach ($productIDs as  $id) {
                
                $SortRemovePurchase = new SortRemovePurchase();
                $SortRemovePurchase->product_id = $id;
                $SortRemovePurchase->sort_id = $sort_id;
                $SortRemovePurchase->save();
                
                $totalRemoved++;
            }
            
            
        } catch (Exception $ex) {
            
            $isError = true;
            $response = 0;
            $totalRemoved = 0;
            
        }  finally {
            
            if($isError == false){
                DB::commit();
            }else{
                DB::rollback();
            }
            
            return array(
                "response" => $response,
                "totalProductRemove" => $totalRemoved
            );
            
        }
        
    }
    
     /*
     * To combine sorter batch in one PDF
     */
    public function anyCombinebatchprint(){
        
        $isError = false;
        $response = 1 ;
        $fileName = ''; 
        
        // DB::beginTransaction();
        set_time_limit(0);
        ini_set('memory_limit', '-1');
       
        try{
            
            $sort_id = Input::get("sort_id");
            // echo "<pre>";
            // print_r($sort_id);
            // echo "<pre>";
            // die();
            
            
            //  $successDO = DB::table('jocom_sort_transaction AS JST')->select(array(
            //              'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
            //             ))
            //         ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
            //         ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
            //         ->where('JST.activation',1)
            //         ->whereIn('JST.sort_id',$sort_id)
            //         ->where('JST.generated',1)
            //         ->where(function ($query) {
            //             $query->where('JST.generated',1)
            //             ->orWhere('JST.regenarate',1)
            //             ->orWhere('JST.special_pass',1);
            //         })
            //         ->orderBy('JST.transaction_id','DESC')->get();
            
            
            $successDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->whereIn('JST.sort_id',$sort_id)
                    ->where('JST.generated',1)
                    ->orderBy('JST.transaction_id','DESC')->get();
                    
            $specialDO = DB::table('jocom_sort_transaction AS JST')->select(array(
                         'JST.id','JST.sort_id','JST.transaction_id','JST.remarks','JST.created_by','JST.created_at','JST.updated_at','JST.generated','JSG.batch_no','JT.do_no'
                        ))
                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
                    ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JST.transaction_id')
                    ->where('JST.activation',1)
                    ->whereIn('JST.sort_id',$sort_id)
                    ->where('JST.special_pass',1)
                    ->orderBy('JST.transaction_id','DESC')->get();
                    
            
            foreach ($successDO as $key => $value) {
                $TIDCollection[] = $value->transaction_id;
            }
            
            foreach ($specialDO as $key => $value) {
                $TIDCollection[] = $value->transaction_id;
            }
            
            // $TIDCollection = array_unique($TIDCollectionRAW);
          
            
            $logisticID = DB::table('logistic_transaction')
            ->select(array(
                'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
                ))
            ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
            ->whereIn('logistic_transaction.transaction_id', $TIDCollection)
            ->whereIn('logistic_transaction.status', [0,4])
            //->where("logistic_transaction.status",0)
            ->where("jocom_transaction.status",'completed')
            ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
            ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
            ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
            ->orderBy('logistic_transaction.transaction_id', 'ASC')->get();
            //->limit(5)
            
            
           
            
            $FileName = date("Ymd")."_".date("His").'.pdf';
            
            // save
            $SortCombine = new SortCombine();
            $SortCombine->filename = $FileName;
            $SortCombine->created_by = Session::get("username");
            $SortCombine->updated_by = Session::get("username");
            $SortCombine->save();
            
            $SortcombineId = $SortCombine->id;
            
            foreach ($sort_id as $vSI) {
                
                $SortCombineBatch =  new SortCombineBatch();
                $SortCombineBatch->sort_id = $vSI;
                $SortCombineBatch->combine_id = $SortcombineId;
                $SortCombineBatch->save();
                
            }
            // echo '<pre>';
            // print_r($TIDCollection);
            // print_r($FileName);
            // echo '</pre>';
            // die();
            
            
            $fileName = self::doPrinter($logisticID,"",false,true,$FileName);
            
            // if($fileName != ''){
                
            //     // save
            //     $SortCombine = new SortCombine();
            //     $SortCombine->filename = $fileName;
            //     $SortCombine->created_by = Session::get("username");
            //     $SortCombine->updated_by = Session::get("username");
            //     $SortCombine->save();
                
            //     $SortcombineId = $SortCombine->id;
                
            //     foreach ($sort_id as $vSI) {
                    
            //         $SortCombineBatch =  new SortCombineBatch();
            //         $SortCombineBatch->sort_id = $vSI;
            //         $SortCombineBatch->combine_id = $SortcombineId;
            //         $SortCombineBatch->save();
                    
            //     }
                  
            // }
            
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
            $isError = true;
            $response = 0;
        }  finally {
            
            // if($isError){
            //     DB::rollback();
            // }else{
            //     DB::commit();
            // }
            
            return array(
                "response" => $response,
                "filename" => $FileName
            );
        }
        
    }
    
    public function anyCombinesort(){
        
        $orders = DB::table('jocom_sort_combine AS JSC')->select(array(
                         'JSC.id','JSC.filename','JSC.created_at','JSC.created_by','JSC.status'
                        ))
                    ->where('JSC.status',1)
                    ->orderBy('JSC.id','DESC');

        return Datatables::of($orders)
        
        ->add_column('batchNo', function($orders){
            
                $combine_id = $orders->id;
                 $SortCombineBatch = DB::table('jocom_sort_combine_batch AS JSCB')
                         ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JSCB.sort_id')
                         ->where('JSCB.status',1)
                         ->where('JSCB.combine_id',$combine_id)->get();
//                $SortCombineBatch = DB::table('jocom_sort_combine_batch AS JSCB')->select(array(
//                         'JSCB.id','JSCB.sort_id','JSCB.combine_id,JSG.batch_no'
//                        ))
//                    ->leftJoin('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JSCB.sort_id')
//                    ->where('JSCB.status',1)
//                    ->where('JSCB.combine_id',$combine_id)->get();
                    
                    
//               
                $batchNo = array();
                
                foreach ($SortCombineBatch as $key => $value) {
                    $batchNo[] = $value->batch_no;
                }
                return $batchNo;
                
        })->make(true);
        
    }
    
    public function anyCustomerinvoice($id = null){
        
        
        try{
            
            $id = base64_decode(urldecode($id));
            $transaction_id = Crypt::decrypt($id);
            
            $Transaction  = Transaction::find($transaction_id);
            
            if(count($Transaction) > 0 ){
                
                $buyerUsername = strtolower($Transaction->buyer_username);
                switch ($buyerUsername) {
                    case '11street':
                        $platform = '11S';
                        break;
                    case 'prestomall':
                        $platform = 'POM';
                        break;
                    case 'lazada':
                        $platform = 'LZD';
                        break;
                    case 'shopee':
                        $platform = 'SPE';
                        break;
                    case 'pgmall':
                        $platform = 'PGM';
                        break;
                    case 'qoo10':
                        $platform = 'QUO';
                        break;

                    default:
                        $platform = 'APP';
                        break;
                }

                $CustomerInvoiceLog = new CustomerInvoiceLog();
                $CustomerInvoiceLog->transaction_id = $transaction_id;
                $CustomerInvoiceLog->generated_by = Session::get('username');
                $CustomerInvoiceLog->platform_code = $platform;
                $CustomerInvoiceLog->save();
                
                return Redirect::to('transaction/edit/'.$transaction_id)->with('success', 'Customer invoice has been generated');
                
            }else{
                return Redirect::to('transaction/edit/'.$transaction_id)->with('message', 'No valid id found!');
            }
            
            
            
            
        } catch (Exception $ex) {
            
            return Redirect::to('transaction/edit/'.$transaction_id)->with('message', 'No valid id found!');

        }
        
          
        
    }
    
    
    // THE MESS END HERE
    
    public function anySearchsuggestion(){
        
        $isError = false;
        
        try{
            
            $keyword = Input::get('keyword');
            
            $Result = DB::table('jocom_transaction AS JT')
                ->select(array(
                    'JT.id','JT.delivery_name','JT.buyer_username','LT.status'
                ))
                ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                ->orWhere('JT.id', 'LIKE', "%{$keyword}%")
                ->orWhere('JT.delivery_name', 'LIKE', "%{$keyword}%")
                ->orWhere('JT.buyer_username', 'LIKE', "%{$keyword}%")
                ->orWhere('JT.invoice_no', 'LIKE', "%{$keyword}%")
//                ->orderBy('JT.transaction_date', 'desc')
                ->limit(3)
                ->get();
            
            
        } catch (Exception $ex) {
            
            $isError = true;
            
        }  finally {
            
            return $Result;
            
        }
        
        
    }
    
    
    public function anySendvendorpo()
    {
        try{
        
        $id =   89427;
        $buyer_usermame = 'HankerFoods';
        if ($id != null) {

            $trans = Transaction::find($id);
            //valid transaction
            if ($trans != null) {
                
                // Send email notification
                $product2 = DB::table('jocom_transaction AS JT')
                            ->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id', '=', 'JT.id')
                            ->leftJoin('jocom_seller AS JS', 'JS.id','=', 'JTD.seller_id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->where('JTD.transaction_id', '=', $id)
                            ->where('JTD.seller_username', '=', $buyer_usermame)
                            ->where('JS.notification', '=', '1')
                            ->select('JTD.transaction_id', 'JTD.price_label', 'JTD.unit','JS.email','JS.company_name', 'JP.name','JP.sku', 'JT.transaction_date','JTD.parent_seller')
                            ->get();
       
                $newArray = array();
                
                foreach ($product2 as $val) 
                {
                    if ( !isset($newArray[$val->email]) ) 
                    {
                        $newArray[$val->email] = array(
                                'transaction_date' => $val->transaction_date,
                                'transaction_id' => $val->transaction_id,
                                'parent_seller' => $val->parent_seller,
                                'company_name' =>$val->company_name,
                                'email' =>$val->email,
                                'product' => array()
                            );
                    }

                    $newArray[$val->email]['product'][] = array(
                            'sku'  => $val->sku,
                            'name'  => $val->name,
                            'price_label'  => $val->price_label,
                            'unit'  => $val->unit,    
                        );
                }

                foreach ($newArray as $key => $value) 
                {
                    $test        = Config::get('constants.ENVIRONMENT');

                    if ($test == 'test') {
                        $email  = Config::get('constants.TEST_MAIL');
                    } else {
                        $email  = $key;
                    }
                    
                    $transaction_date = $value['transaction_date'];
                    $transaction_id = $value['transaction_id'];
                    $company_name = $value['company_name'];

                    $data = array(
                        'transaction_date' => $transaction_date,
                        'transaction_id' => $transaction_id,
                        'company_name' => $company_name,
                        'product'  => $value['product'],
                    );

                    $subject = "Product Notification [Transaction ID: {$trans->id}]";
                    // Deactivated :: 23/06/2020
                    // Mail::send('emails.notificationnew', $data, function($message) use ($email,$subject)
                    // {
                    //     $message->from('payment@jocom.my', 'JOCOM');
                    //     $message->to($email)->cc('humairah@jocom.my')
                    //     ->subject($subject);
                    // });
                }
                // END Send email notification

                return 'yes';
            } // end valid transaction
        } else {
            return 'no';
        }
        
           
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    public function anyTestsort2(){
        $result = self::createpurchaselist2(1138);
        echo "<pre>";
        print_r( $result);
        echo "</pre>";
    }
    
    public function anyTestsort3(){
        try{
            ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
            $sort_id = 7122;
//            $sort_id = 1;
            $sorterData = DB::table('jocom_sort_generator AS JSG')
                    ->where('JSG.id',$sort_id)
                    ->first();
            
            $batchNo = 'BatchJuly01-10';
            
            $data  = self::createpurchaselist3($sort_id);
            
            $SortGenerator = SortGenerator::where("id",$sort_id)->first();
            $date = $SortGenerator->created_at;
            $batchNo = $SortGenerator->batch_no;

            return Excel::create('PURCHASE_LIST_'.$batchNo.'_'.date("dmyHis"), function($excel) use ($data,$date,$batchNo) {
                    $excel->sheet('Purchase List', function($sheet) use ($data,$date,$batchNo)
                    {   
                        $sheet->loadView('emails.purchaselist2', array('data' =>$data,'date'=>$date,'batchNo'=>$batchNo));
                        
                    });
                })->download('xls');
//            
        
        
        } catch (Exception $ex) {

        }
    }
    
    
    public static function createpurchaselist2($sort_id){
        
        try{
            
       
        
        $sort_id = $sort_id;
        // $sort_id = 'GDL0000000073';
        $finalList = array();
        $removedProduct = array();
       
        // OLD // 
        /*
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo,JS.company_name
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            LEFT JOIN jocom_product_seller AS JPS ON JPS.product_id = LTI.product_id
            LEFT JOIN jocom_seller AS JS ON JS.id = JPS.seller_id
            WHERE JST.sort_id = ".$sort_id."
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
        */   
        // OLD //   
        
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            WHERE JST.sort_id = ".$sort_id."
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
       
       $list = DB::select($query);
       
    //   echo "<pre>";
    //   print_r($list);
    //   echo "</pre>";
    //   die();
       
       $removeItems =  DB::table('jocom_sort_remove_purchase AS JSRP')
                    ->where("JSRP.sort_id",$sort_id)
                    ->where("JSRP.status",1)
                    ->get();
       
        foreach ($removeItems as $keyR => $valueR) {
            array_push($removedProduct, $valueR->product_id);
        }
            
       
       foreach ($list as $key => $value) {
           
            $baseProduct = array();
            $TotalTransactions = 0;

            
            $transactionID = $value->transaction_id;
            
            // Count total Transaction
            
            $query_total_transaction = "select count(JST.id) AS TotalTransaction 
                FROM jocom_sort_transaction AS JST 
                LEFT JOIN jocom_sort_generator AS JSG ON JSG.id = JST.sort_id
                LEFT JOIN jocom_transaction_details AS JTD ON JTD.transaction_id = JST.transaction_id
                WHERE JSG.id = ".$sort_id." AND JST.generated <> 1
                AND JTD.p_option_id = ".$value->product_price_id;
                
           
       
            $total_transaction = DB::select($query_total_transaction);
            
            // echo "<pre>";
            // print_r($total_transaction);
            // echo "</pre>";
            
            $query_total_quantity = "select SUM(JTD.unit) AS req_qty 
                FROM jocom_sort_transaction AS JST 
                LEFT JOIN jocom_sort_generator AS JSG ON JSG.id = JST.sort_id
                LEFT JOIN jocom_transaction_details AS JTD ON JTD.transaction_id = JST.transaction_id
                WHERE JSG.id = ".$sort_id." AND JST.generated <> 1
                AND JTD.p_option_id = ".$value->product_price_id;
       
            $total_quantity = DB::select($query_total_quantity);
            
            //  echo "<pre>";
            // print_r($total_quantity);
            // echo "</pre>";
            
            // Count total Quantity

            $baseItem = DB::table('jocom_sort_transaction_details AS JSTD')
                ->select(array(
                    'JP.name','JP.sku','JP.id','JPP.label',DB::raw('SUM(JSTD.order_quantity) as order_quantity'),'JPP.price','JPP.price_promo'
                    //,'JS.company_name'
                    ))
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_product_seller AS JPS', 'JPS.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->get();
                    // echo "<pre>";
                    // print_r($baseItem);
                    // echo "</pre>";
            
           $baseItem2 =  DB::table('jocom_product_base_item AS JPBI')
                    ->where("JPBI.price_option_id",$value->product_price_id)
                   ->where("JPBI.status",1)
                    ->get();
           
        //   echo "<pre>";
        //   print_r($baseItem2);
        //   echo "</pre>";
            
            if(count($baseItem2) > 0){
                
                foreach ($baseItem as $kB => $vB) { 
                    
                    // GET Price //
                    $baseProductPrice =  DB::table('jocom_transaction_details_base_product AS JTDBP')
                        ->select(array(
                                'JTDBP.*'
                        ))
                        ->leftjoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'JTDBP.transaction_details_id')
                        ->where("JTD.transaction_id",$transactionID)
                        ->where("JTD.p_option_id",$value->product_price_id)
                        ->first();
                    // GET Price //
                    
                     $sellerInfo = DB::table('jocom_product_seller AS JPS')
                        ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
                        ->where("JPS.product_id",$vB->id)
                        ->where("JPS.activation",1)->first();
                        
                    $GSsellerInfo = DB::table('jocom_products_gs_vendor AS JPGS')
                        ->select(array(
                            'JSG.seller_name'
                        ))
                        ->leftjoin('jocom_seller_gs AS JSG', 'JSG.id', '=', 'JPGS.gs_vendor_id')
                        ->where("JPGS.product_id",$vB->id)
                        ->where("JPGS.activation",1)->first();

                    if($GSsellerInfo ){
                        $gs_vendor = " ,".$GSsellerInfo->seller_name;
                    }else{
                        $gs_vendor = "";
                    }
                        
                     $pricebaseinfo = DB::table('jocom_product_price AS JPP')
                        ->where("JPP.product_id",$vB->id)->first();
                    
                    if(!in_array($vB->id, $removedProduct)){
                        array_push($baseProduct, array(
                            "product_name" => $vB->name,
                            "product_id" => $vB->id,
                            "product_label" => $vB->label,
                            "company_name" =>  $sellerInfo->company_name.$gs_vendor ,//$vB->company_name,
                            "product_sku" => $vB->sku,
                            "unit_price" => $pricebaseinfo->price_promo > 0 ? $pricebaseinfo->price_promo : $pricebaseinfo->price,
                            "quantityPerSet" =>  $value->qty_order_set ,
                            "totalQuantity" => $vB->order_quantity
                        ));
                    }
                       
                }
                
            }
            
            
            $totalTransaction = DB::table('jocom_sort_transaction_details AS JSTD')
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->count();
            
            $ProductInfo = DB::table('jocom_products')->
                    where("sku",$value->sku)->first();
            
            if(!in_array($ProductInfo->id, $removedProduct)){
                
            // Get Seller Info //
            
            $sellerInfo = DB::table('jocom_product_seller AS JPS')
            ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JPS.product_id",$ProductInfo->id)
            ->where("JPS.activation",1)->first();
            
            $GSsellerInfo = DB::table('jocom_products_gs_vendor AS JPGS')
                ->select(array(
                    'JSG.seller_name'
                ))
                ->leftjoin('jocom_seller_gs AS JSG', 'JSG.id', '=', 'JPGS.gs_vendor_id')
                ->where("JPGS.product_id",$ProductInfo->id)
                ->where("JPGS.activation",1)->first();

            if($GSsellerInfo ){
                $gs_vendor = " ,".$GSsellerInfo->seller_name;
            }else{
                $gs_vendor = "";
            }
            
           
            array_push($finalList, array(
                "product_name" => $value->name,
                "product_sku" => $value->sku,
                "product_id" => $ProductInfo->id,
                "product_label" => $value->label,
                "company_name" => $sellerInfo->company_name.$gs_vendor, //$value->company_name,
                "option_id" => $value->product_price_id,
                "unit_price" => $value->price_promo, //'TEST', //$value->price,
                "total_order" => $total_transaction[0]->TotalTransaction, //,$value->TotalTransactions,
                "stock_type" => $value->type_product,
                "req_qty" => $total_quantity[0]->req_qty, //$value->TotalOrderSet,
                //"in_stock" => $value->in_stock,
                //"balance_need" => $value->req_qty - $value->in_stock,
                "base_product" => $baseProduct,
            ));
           
       }
       
            
           
       }
       
        }catch(exception $ex){
            echo $ex->getMessage();
        }finally{
            
           
            return $finalList;
         
           
        }


      
       
       
       
                
    }
    
    
    public static function createpurchaselist3($sort_id){
        
        try{
            
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        
        $sort_id = $sort_id;
        // $sort_id = 'GDL0000000073';
        $finalList = array();
        $removedProduct = array();
       
        // OLD // 
        /*
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo,JS.company_name
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            LEFT JOIN jocom_product_seller AS JPS ON JPS.product_id = LTI.product_id
            LEFT JOIN jocom_seller AS JS ON JS.id = JPS.seller_id
            WHERE JST.sort_id = ".$sort_id."
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
        */   
        // OLD //   
        
        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,LTI.product_price_id,JPP.price,JPP.price_promo
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            WHERE JST.sort_id in(7028,
7027,
7026,
7024,
7021,
7015,
7012,
7010,
7009,
7008,
7005,
7000,
6999,
6996,
6992,
6989,
6986,
6984,
6983,
6980,
6972
)
            AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";
       
       $list = DB::select($query);
       
    //   echo "<pre>";
    //   print_r($list);
    //   echo "</pre>";
    //   die();
       
       $removeItems =  DB::table('jocom_sort_remove_purchase AS JSRP')
                    ->where("JSRP.sort_id",$sort_id)
                    ->where("JSRP.status",1)
                    ->get();
       
        foreach ($removeItems as $keyR => $valueR) {
            array_push($removedProduct, $valueR->product_id);
        }
            
       
       foreach ($list as $key => $value) {
           
            $baseProduct = array();
            $TotalTransactions = 0;

            
            $transactionID = $value->transaction_id;
            
            // Count total Transaction
            
            $query_total_transaction = "select count(JST.id) AS TotalTransaction 
                FROM jocom_sort_transaction AS JST 
                LEFT JOIN jocom_sort_generator AS JSG ON JSG.id = JST.sort_id
                LEFT JOIN jocom_transaction_details AS JTD ON JTD.transaction_id = JST.transaction_id
                WHERE JSG.id = ".$sort_id." AND JST.generated <> 1
                AND JTD.p_option_id = ".$value->product_price_id;
                
           
       
            $total_transaction = DB::select($query_total_transaction);
            
            // echo "<pre>";
            // print_r($total_transaction);
            // echo "</pre>";
            
            $query_total_quantity = "select SUM(JTD.unit) AS req_qty 
                FROM jocom_sort_transaction AS JST 
                LEFT JOIN jocom_sort_generator AS JSG ON JSG.id = JST.sort_id
                LEFT JOIN jocom_transaction_details AS JTD ON JTD.transaction_id = JST.transaction_id
                WHERE JSG.id = ".$sort_id." AND JST.generated <> 1
                AND JTD.p_option_id = ".$value->product_price_id;
       
            $total_quantity = DB::select($query_total_quantity);
            
            //  echo "<pre>";
            // print_r($total_quantity);
            // echo "</pre>";
            
            // Count total Quantity

            $baseItem = DB::table('jocom_sort_transaction_details AS JSTD')
                ->select(array(
                    'JP.name','JP.sku','JP.id','JPP.label',DB::raw('SUM(JSTD.order_quantity) as order_quantity'),'JPP.price','JPP.price_promo'
                    //,'JS.company_name'
                    ))
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_product_seller AS JPS', 'JPS.product_id', '=', 'JP.id')
            // ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->get();
                    // echo "<pre>";
                    // print_r($baseItem);
                    // echo "</pre>";
            
           $baseItem2 =  DB::table('jocom_product_base_item AS JPBI')
                    ->where("JPBI.price_option_id",$value->product_price_id)
                   ->where("JPBI.status",1)
                    ->get();
           
        //   echo "<pre>";
        //   print_r($baseItem2);
        //   echo "</pre>";
            
            if(count($baseItem2) > 0){
                
                foreach ($baseItem as $kB => $vB) { 
                    
                    // GET Price //
                    $baseProductPrice =  DB::table('jocom_transaction_details_base_product AS JTDBP')
                        ->select(array(
                                'JTDBP.*'
                        ))
                        ->leftjoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'JTDBP.transaction_details_id')
                        ->where("JTD.transaction_id",$transactionID)
                        ->where("JTD.p_option_id",$value->product_price_id)
                        ->first();
                    // GET Price //
                    
                     $sellerInfo = DB::table('jocom_product_seller AS JPS')
                        ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
                        ->where("JPS.product_id",$vB->id)
                        ->where("JPS.activation",1)->first();
                        
                    $GSsellerInfo = DB::table('jocom_products_gs_vendor AS JPGS')
                        ->select(array(
                            'JSG.seller_name'
                        ))
                        ->leftjoin('jocom_seller_gs AS JSG', 'JSG.id', '=', 'JPGS.gs_vendor_id')
                        ->where("JPGS.product_id",$vB->id)
                        ->where("JPGS.activation",1)->first();

                    if($GSsellerInfo ){
                        $gs_vendor = " ,".$GSsellerInfo->seller_name;
                    }else{
                        $gs_vendor = "";
                    }
                        
                     $pricebaseinfo = DB::table('jocom_product_price AS JPP')
                        ->where("JPP.product_id",$vB->id)->first();
                    
                    if(!in_array($vB->id, $removedProduct)){
                        array_push($baseProduct, array(
                            "product_name" => $vB->name,
                            "product_id" => $vB->id,
                            "product_label" => $vB->label,
                            "company_name" =>  $sellerInfo->company_name.$gs_vendor ,//$vB->company_name,
                            "product_sku" => $vB->sku,
                            "unit_price" => $pricebaseinfo->price_promo > 0 ? $pricebaseinfo->price_promo : $pricebaseinfo->price,
                            "quantityPerSet" =>  $value->qty_order_set ,
                            "totalQuantity" => $vB->order_quantity
                        ));
                    }
                       
                }
                
            }
            
            
            $totalTransaction = DB::table('jocom_sort_transaction_details AS JSTD')
            ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
            ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
            ->where("JST.sort_id",$sort_id)
            ->where("JPP.default",1)
            ->where("JSTD.is_failed",2)
            ->where("LTI.product_price_id",$value->product_price_id)
            ->groupBy('JSTD.product_id')
            ->count();
            
            $ProductInfo = DB::table('jocom_products')->
                    where("sku",$value->sku)->first();
            
            if(!in_array($ProductInfo->id, $removedProduct)){
                
            // Get Seller Info //
            
            $sellerInfo = DB::table('jocom_product_seller AS JPS')
            ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->where("JPS.product_id",$ProductInfo->id)
            ->where("JPS.activation",1)->first();
            
            $GSsellerInfo = DB::table('jocom_products_gs_vendor AS JPGS')
                ->select(array(
                    'JSG.seller_name'
                ))
                ->leftjoin('jocom_seller_gs AS JSG', 'JSG.id', '=', 'JPGS.gs_vendor_id')
                ->where("JPGS.product_id",$ProductInfo->id)
                ->where("JPGS.activation",1)->first();

            if($GSsellerInfo ){
                $gs_vendor = " ,".$GSsellerInfo->seller_name;
            }else{
                $gs_vendor = "";
            }
            
           
            array_push($finalList, array(
                "product_name" => $value->name,
                "product_sku" => $value->sku,
                "product_id" => $ProductInfo->id,
                "product_label" => $value->label,
                "company_name" => $sellerInfo->company_name.$gs_vendor, //$value->company_name,
                "option_id" => $value->product_price_id,
                "unit_price" => $value->price_promo, //'TEST', //$value->price,
                "total_order" => $total_transaction[0]->TotalTransaction, //,$value->TotalTransactions,
                "stock_type" => $value->type_product,
                "req_qty" => $total_quantity[0]->req_qty, //$value->TotalOrderSet,
                //"in_stock" => $value->in_stock,
                //"balance_need" => $value->req_qty - $value->in_stock,
                "base_product" => $baseProduct,
                "transaction_id" =>$transactionID,
            ));
           
       }
       
            
           
       }
       
        }catch(exception $ex){
            echo $ex->getMessage();
        }finally{
            
           
            return $finalList;
         
           
        }


      
       
       
       
                
    }
    
    public function anyGetuser($username){

        $userinfo = DB::table('jocom_user AS JU')->where("username",$username)->first();

        return Response::json($userinfo);

    }
    
    public function anyGetpurchase($purchase_id){
        $purchaseinfo = DB::table('jocom_transaction_purchase_history')->where("id",$purchase_id)
                                                                        ->first();
        return Response::json($purchaseinfo);

    }
    
    
    public function anyGetprofitloss($profit_id){
        $profitinfo = DB::table('jocom_lazada_transaction_details')->where("id",$profit_id)
                                                                        ->first();
        return Response::json($profitinfo);

    }

    public function anyGetshopeeprofitloss($profit_id){
        $profitinfo = DB::table('jocom_shopee_transaction_details')->where("id",$profit_id)
                                                                        ->first();
        return Response::json($profitinfo);

    }

    public function anyGettiktokprofitloss($profit_id){
        $profitinfo = DB::table('jocom_tiktok_transaction_details')->where("id",$profit_id)
                                                                        ->first();
        return Response::json($profitinfo);

    }
    
    /*
     * @Desc : List of attention DO 
     */
    public function getAttentionlist(){
        

        
        $AttentionOrders = DB::table('jocom_transaction AS JT')->select(array(
            'JT.transaction_date',
            'JT.buyer_username',
            'JT.id AS TransactionID',
             DB::raw('DATEDIFF(NOW(),JT.transaction_date) AS TotalDaysDelay'),
            'JT.status AS TransactionStatus',
            'LT.id AS LogisticID',
            'JT.delivery_state AS DeliveryState',
            DB::raw("CASE
            WHEN LT.status = 0 THEN 'Pending'
            WHEN LT.status = 1 THEN 'Undelivered'
            WHEN LT.status = 2 THEN 'Partial Send'
            WHEN LT.status = 3 THEN 'Returned'
            WHEN LT.status = 4 THEN 'Sending'
            WHEN LT.status = 5 THEN 'Sent'
            WHEN LT.status = 6 THEN 'Cancelled'
            ELSE 'NOT LOG IN'
            END AS 'Logistic_Status'"),
            'LT.insert_date AS LogLogisticDate',
            DB::raw("CASE WHEN LT.status = 3 THEN LT.modify_date ELSE ' - ' END AS 'LogisticStatusUpdatedAT'"),
            DB::raw("CASE WHEN LT.status = 3 THEN LT.modify_by ELSE ' - ' END AS 'LogisticStatusUpdatedBY'"),
            DB::raw("CASE WHEN JSG.batch_no IS NOT NULL THEN JSG.batch_no ELSE ' No Sort ' END AS 'SortBatchNo'"),
          //  'JSG.batch_no AS SortBatchNo',
            'JST.created_by AS SortBY',
            'JST.created_at AS SortAT',
            DB::raw("CASE WHEN JST.generated = 1 THEN 'SUCCESS' WHEN JSG.batch_no IS NULL THEN ' - ' ELSE 'FAILED' END AS SORT_STATUS"),
            DB::raw("CASE WHEN JST.special_pass = 1 THEN 'APPROVED' WHEN  JST.generated = 1  THEN ' - ' WHEN JSG.batch_no IS NULL THEN ' - ' ELSE 'NOT APPROVED' END AS APPROVAL"),
	      // DB::raw("CASE WHEN  JST.remarks LIKE '%duplicate%' THEN 'DUPLICATE'  ELSE ' - ' END AS DUPLICATED")
            
        ))
        ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id') 
        ->leftJoin('jocom_sort_transaction AS JST', 'JT.id', '=', 'JST.transaction_id') 
        // ->leftJoin('jocom_sort_transaction', function ($join) {
        //         $join->on('jocom_sort_transaction.id', '=', DB::raw('(SELECT transaction_id FROM jocom_sort_transaction WHERE jocom_sort_transaction.transaction_id = jocom_transaction.id LIMIT 1)'));
        //     })
        ->join('jocom_sort_generator AS JSG', 'JSG.id', '=', 'JST.sort_id')
        ->where('JT.status','=','completed')
        ->where('LT.status','<>',5)
        ->where('LT.status','<>',4)
        ->where('LT.status','<>',3)
        ->where('LT.status','<>',2)
        ->where('LT.status','<>',6)
        //->where('DUPLICATED','<>',' - ')
        ->where('JT.delivery_state','<>','johor')
        ->where('JT.delivery_state','<>','pulau pinang')
        ->where(function ($query) {
            $query->whereNull('JSG.batch_no')
                ->orWhere(function ($query2){
                    $query2->where('JST.generated','<>',1)
                    ->where('JST.special_pass','<>',1);
                });
        })
        ->where(function ($query3) {
            $query3->whereNull('JST.status')
                ->orWhere('JST.status','=',1);
        })
        ->where('JT.transaction_date','>=',date("Y-m-d 00:00:00", strtotime( '-30 day' ) ))
        //->where('JT.transaction_date','<=',date("Y-m-d 00:00:00", strtotime( '+1 day' ) ))
        ->where('JT.transaction_date','<=','2019-01-24 23:59:59')
        ->orderBy('JST.created_at','ASC')
        ->orderBy('JT.id','ASC')
        ->groupBy('JT.id');
        //->orderBy('JST.created_at','ASC');
        //->groupBy('JT.id')->limit(10)->get();

         return Datatables::of($AttentionOrders)->make(true);

        // // $finalList = array();
        // // foreach ($AttentionOrders as $key => $value) {
            
        // //     if($value->DUPLICATE !== 'DUPLICATE'){
        // //         array_push($finalList,$value);
        // //     }

        // // }

        echo "<pre>";
        print_r($AttentionOrders);
        echo "</pre>";
        
    }
    
        public function getAttentionnosortlist(){
        

        
        $AttentionOrders = DB::table('jocom_transaction AS JT')->select(array(
            'JT.transaction_date',
            'JT.buyer_username',
            'JT.id AS TransactionID',
           DB::raw('DATEDIFF(NOW(),JT.transaction_date) AS TotalDaysDelay'),
            'JT.status AS TransactionStatus',
            'LT.id AS LogisticID',
            'JT.delivery_state AS DeliveryState',
            DB::raw("CASE
            WHEN LT.status = 0 THEN 'Pending'
            WHEN LT.status = 1 THEN 'Undelivered'
            WHEN LT.status = 2 THEN 'Partial Send'
            WHEN LT.status = 3 THEN 'Returned'
            WHEN LT.status = 4 THEN 'Sending'
            WHEN LT.status = 5 THEN 'Sent'
            WHEN LT.status = 6 THEN 'Cancelled'
            ELSE 'NOT LOG IN'
            END AS 'Logistic_Status'"),
            'LT.insert_date AS LogLogisticDate',
             'JST.id AS JSTID',
            
        ))
        ->leftJoin('jocom_sort_transaction AS JST', 'JT.id', '=', 'JST.transaction_id') 
        ->join('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id') 
        ->whereNull('JST.id')
         ->where('JT.status','=','completed')
        ->where('LT.status','<>',5)
        ->where('LT.status','<>',4)
        ->where('LT.status','<>',3)
        ->where('LT.status','<>',2)
        ->where('LT.status','<>',6)
        
         ->where('JT.transaction_date','>=',date("Y-m-d 00:00:00", strtotime( '-15 day' ) ))
        //->where('JT.transaction_date','<=',date("Y-m-d 00:00:00", strtotime( '+1 day' ) ))
        ->where('JT.transaction_date','<=','2019-01-24 23:59:59');

         return Datatables::of($AttentionOrders)->make(true);

        // // $finalList = array();
        // // foreach ($AttentionOrders as $key => $value) {
            
        // //     if($value->DUPLICATE !== 'DUPLICATE'){
        // //         array_push($finalList,$value);
        // //     }

        // // }

        echo "<pre>";
        print_r($AttentionOrders);
        echo "</pre>";
        
    }
    
    
    public function anyTestsort(){
        
        $product_base_id = 17624 ;
        $quantity = 1;
        
        echo "<pre>";
        echo "Product Base id :".$product_base_id ;
        echo "Quantity :".$quantity ;
        echo "</pre>";
        
        
        
        $test = Warehouse::getStockavailable($product_base_id,$quantity,'CMS');
        
        echo "<pre>";
        print_r($test);
        echo $test['reservedData']['ProductWareHouseID'];
        echo "</pre>";
        
        // $product_warehouse_id = $test['reservedData']['ProductWareHouseID'];
        // $action_type = 'NEWSTOCK';
        // $quantity = $test['reservedData']['quantity'] * $quantity ;
        // $reference_id = '789639';
        
        // $WarehouseProductHistory = WarehouseProductLog::saveRecord($product_warehouse_id,$action_type,$quantity,$reference_id);
        
       
        
        
        
    }
    
    public function anyPendingtransaction(){
        
        return View::make('admin.pendingtrans');
        
    }
    

    public function anyListingbulk()
    {
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }

        $start_date = '2019-09-17 00:00:00';
       
        $trans = Transaction::select([
            'jocom_transaction.id',
            'jocom_transaction.transaction_date',
            'jocom_transaction.buyer_username',
            DB::raw("CASE
                WHEN jocom_transaction.buyer_username = 'prestomall' THEN 'Prestomall'
                WHEN jocom_transaction.buyer_username = 'lazada' THEN 'Lazada'
                WHEN jocom_transaction.buyer_username = 'Qoo10' THEN 'Qoo10'
                WHEN jocom_transaction.buyer_username = 'shopee' THEN 'shopee'
                WHEN jocom_transaction.buyer_username = 'Astro Go Shop' THEN 'Astro Go Shop'
                ELSE 'Jocom'
                END AS 'Platform'"),
            'jocom_transaction.total_amount',
            'jocom_transaction.delivery_state',
            'jocom_transaction.status',
           
        ])
            ->leftJoin('jocom_transaction_coupon', 'jocom_transaction.id', '=', 'jocom_transaction_coupon.transaction_id')
            ->leftJoin('jocom_transaction_point', 'jocom_transaction.id', '=', 'jocom_transaction_point.transaction_id')
            // ->leftjoin('jocom_elevenstreet_order', 'jocom_transaction.id', '=', 'jocom_elevenstreet_order.transaction_id')
            // ->leftJoin('jocom_delivery_order', 'jocom_transaction.id', '=', 'jocom_delivery_order.id');
            ->where('jocom_transaction.status','=','pending')
            ->where('jocom_transaction.transaction_date','>=',$start_date);

        

        $trans = $trans->groupBy('jocom_transaction.id');

         $actionBar = '<a class="btn" title="" data-toggle="tooltip" href="/transaction/edit/{{$id}}">{{$status}}</a>';

        return Datatables::of($trans)
           
            ->edit_column('status', $actionBar)
            ->make(true);
    }

    public function anyBulkapprove(){

        $isError = 0;
        $respStatus = 0;
        $errorMessage = "";
        $data = "";
        
        DB::beginTransaction();

        try{
        
        $sData = Input::get('sData'); 

        $data = explode(",", $sData); 

        foreach ($data as $key => $value) {
          
            $trans = Transaction::find($value);
            $trans->status = 'completed';
            $trans->modify_by = Session::get('username');
            $trans->modify_date = date("Y-m-d h:i:sa");
            $trans->save();

            $respStatus = 1;
        }

        foreach ($data as $key => $value) {

                 $Transaction = Transaction::find($value);

                    if($Transaction->status == 'completed'){
                        
                        $TransactionInfo = Transaction::find($value);

                        if($TransactionInfo->do_no == ""){
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($value, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($value, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($value, true);
                            
                            
                        }
                    
                         // AUTOMATED LOG TO LOGISTIC APP
                              LogisticTransaction::log_transaction($value);
                        
                       
                    }

        }


        } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
            echo $errorMessage;
        }
        finally{
            if($isError == 0){
                DB::commit();
            }else{
                DB::rollBack();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }

    }
    
    public function anyManualdo(){

        $SuccesTransList = array(

651227,
651266,
651254






                );
                
        die('Done');
        
        $totalRecord = count($SuccesTransList);

         foreach ($SuccesTransList as $key => $value) {

            $tempDO = MCheckout::generatenewDO($value, true);
            
                    // $Transaction = Transaction::find($value);

                    // if($Transaction->status == 'completed'){
                        
                    //     $TransactionInfo = Transaction::find($value);

                    //     if($TransactionInfo->do_no == ""){
                    //         // CREATE INV
                    //         $tempInv = MCheckout::generateInv($value, true);
                    //         // CREATE PO
                    //         $tempPO = MCheckout::generatePO($value, true);
                    //         // CREATE DO
                    //         $tempDO = MCheckout::generateDO($value, true);
                            
                    //         // Lazada 
                    //         //  $result = DB::table('jocom_lazada_order AS JLO')->select('JLO.id')
                    //         //             ->where('transaction_id','=',$value)->first();
                    //         // $LazadaOrder = LazadaOrder::find($result->id);
                    //         // $LazadaOrder->status = "2";
                    //         // $LazadaOrder->is_completed = "1";
                    //         // $LazadaOrder->updated_by = "SYSTEM";
                    //         // $LazadaOrder->save();
                            
                    //     }
                    
                    //      // AUTOMATED LOG TO LOGISTIC APP
                    //         //   LogisticTransaction::log_transaction($value);
                        
                       
                    // }


         }

         echo 'Process Done...';


    }
    
    

    public function anyBulkcancel(){
        $isError = 0;
        $respStatus = 0;
        $errorMessage = "";
        $data = "";
        
        DB::beginTransaction();

        try{

            $sData = Input::get('sData'); 

            $data = explode(",", $sData); 

            foreach ($data as $key => $value) {
              
                $trans = Transaction::find($value);
                $trans->status = 'cancelled';
                $trans->modify_by = Session::get('username');
                $trans->modify_date = date("Y-m-d h:i:sa");
                $trans->save();

                $respStatus = 1;
            }

        } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
        }
        finally{
            if($isError == 0){
                DB::commit();
            }else{
                DB::rollBack();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }

    }
        
    public function getStatussummary() {
        return View::make('admin.transaction_stats_summary');
    }

    public function anySummarytable() {

        $isError = 0;
        $respStatus = 1;
        $data = array();
        
        $rangeType = Input::get("rangeType");
        $startDate = Input::get("startDate");
        $toDate = Input::get("toDate");
        $navigate = Input::get("navigation");

        // default weekly
        if ($startDate == '' && $toDate == '') {
            $startDate = date("Y-m-d", strtotime('monday this week'));
            $toDate = date("Y-m-d",  strtotime('sunday this week'));
        }

        switch ($rangeType) {
            case 1: // Daily
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' -1 days', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' +1 days', time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }else{
                    $startDate = date('Y-m-d', strtotime(date($startDate), time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }


                break;
            case 2: // Weekly
                
                $day = 7;
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' -'.$day.' days', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' -'.($day).' days', time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' +'.$day.' days', time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($toDate).' +'.$day.' days', time()))." 23:23:59";
                }

                break;
            case 3: // Monthly
        
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' first day of last month', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' last day of last month', time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' first day of next month', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' last day of next month', time()))." 23:23:59";
                }

                break;

            default:
                break;
        }

        $transaction_count = Transaction::where('transaction_date', '>=', $startDate)
                                ->where('transaction_date', '<=', $toDate)
                                ->groupBy('status')
                                ->select('status', DB::raw('COUNT(status) as count'))
                                ->get();

        $total_transaction = 0;
        $total_cancelled = 0;
        $total_cancelled_percent = '(0.00%)';
        $total_refund = 0;
        $total_refund_percent = '(0.00%)';
        $total_pending = 0;
        $total_pending_percent = '(0.00%)';

        foreach ($transaction_count as $transaction) {
            $total_transaction = $total_transaction + $transaction->count;

            if ($transaction->status == 'cancelled') {
                $total_cancelled = $transaction->count;
            } else if ($transaction->status == 'refund') {
                $total_refund = $transaction->count;
            } else if ($transaction->status == 'pending') {
                $total_pending = $transaction->count;
            }
        }

        if ($total_transaction > 0) {
            $total_cancelled_percent = '('.number_format(($total_cancelled / $total_transaction * 100), 2).'%)';
            $total_refund_percent =  '('.number_format(($total_refund / $total_transaction * 100), 2).'%)';
            $total_pending_percent =  '('.number_format(($total_pending / $total_transaction * 100), 2).'%)';
        }

        $status = ['cancelled', 'pending', 'refund'];
        $platform = ['Astro Go Shop', 'lazada', 'prestomall', 'Qoo10', 'shopee', 'pgmall'];

        $status_count = Transaction::whereIn('buyer_username', $platform)
                            ->where('transaction_date', '>=', $startDate)
                            ->where('transaction_date', '<=', $toDate)
                            ->whereIn('status', $status)
                            ->groupBy(['buyer_username', 'status'])
                            ->orderBy('buyer_username')
                            ->select('buyer_username', 'status', DB::raw('COUNT(status) as count'))
                            ->get();
                            
        $jocom_status_count = Transaction::whereNotIn('buyer_username', $platform)
                            ->where('transaction_date', '>=', $startDate)
                            ->where('transaction_date', '<=', $toDate)
                            ->whereIn('status', $status)
                            ->groupBy(['status'])
                            ->select('buyer_username', 'status', DB::raw('COUNT(status) as count'))
                            ->get();

        $total_count = Transaction::where(function($query) use ($platform) {
                                $query->whereIn('buyer_username', $platform)
                                      ->orWhereIn('device_platform', ['android', 'ios']);
                            })
                            ->where('transaction_date', '>=', $startDate)
                            ->where('transaction_date', '<=', $toDate)
                            ->groupBy(['buyer_username'])
                            ->orderBy('buyer_username')
                            ->select('buyer_username', DB::raw('COUNT(buyer_username) as count'))
                            ->get();
                            
        $total_jocom_count = Transaction::whereNotIn('buyer_username', $platform)
                            ->where('transaction_date', '>=', $startDate)
                            ->where('transaction_date', '<=', $toDate)
                            ->groupBy(['status'])
                            ->select('buyer_username', DB::raw('COUNT(buyer_username) as count'))
                            ->get();
                            
        $summary = array();
        $total = array();

        foreach ($status_count as $st) {
            $summary[$st->buyer_username][$st->status] = $st->count;
        }
        
        foreach ($jocom_status_count as $st) {
            $summary['jocom'][$st->status] = $st->count;
        }

        foreach ($total_count as $st) {
            $total[$st->buyer_username] = $st->count;
        }
        
        foreach ($total_jocom_count as $st) {
            $total['jocom'] = $total['jocom'] + $st->count;
        }

        $platform[] = 'jocom';
        
        foreach ($platform as $p) {
            foreach ($status as $s) {
                $summary[$p][$s] = (isset($summary[$p][$s])) ? $summary[$p][$s] : 0;

                $summary[$p]['total'] = (isset($total[$p])) ? $total[$p] : 0;;
                if ($summary[$p]['total'] > 0) {
                    $summary[$p][$s . '_percent'] = number_format(($summary[$p][$s] / $summary[$p]['total']) * 100, 2);
                } else {
                    $summary[$p][$s . '_percent'] = 0.00;
                }
                
            }
        }

        return array(
            "weekly_start_date" =>  date("Y-m-d", strtotime('monday this week')), 
            "weekly_end_date" =>  date("Y-m-d",  strtotime('sunday this week')),
            "monthly_start_date" => date('Y-m-1'),
            "monthly_end_date" => date('Y-m-t'),
            'start_date' => $startDate,
            'end_date' => $toDate,
            'start_date_display' => date_format(date_create($startDate),"d M Y"),
            'end_date_display' => date_format(date_create($toDate),"d M Y"),
            'total_transaction' => $total_transaction,
            'total_cancelled' => $total_cancelled,
            'total_refund' => $total_refund,
            'total_pending' => $total_pending,
            'total_cancelled_percent' => $total_cancelled_percent,
            'total_refund_percent' => $total_refund_percent,
            'total_pending_percent' => $total_pending_percent,
            'summary' => $summary
        );
    }
    
    public function anyBulkadd() {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_bulkadd');
    }

    public function postBulkstore() {

        $price_option = Input::get('priceopt');
        $qrcode = Input::get('qrcode');
        $qty = Input::get('qty');
        $with_total = Input::get('with_total');

        $csv_file = Input::file('csv_file');
        $qrcodes = explode(',', $qrcode);
        $price_options = explode(',', $price_option);
        $qtys = explode(',', $qty);

        $destinationPath = storage_path() . '/astrogo';
        $filename = $destinationPath . '/a.csv';
        $csv_file->move($destinationPath, 'a.csv');
        $file = fopen($filename, 'r');

        $orders = array();

        $transferedProcessOrder = array();
        $manualProcessOrder = array();

        $data = fgetcsv($file, 1400, ",");

        try {
            DB::beginTransaction();
            while (($data = fgetcsv($file, 1400, ",")) !== FALSE) {
                $buyer_id = $data[0];
                $buyer_username = $data[1];
                $delivery_name = $data[2];
                $delivery_contact_no = $data[3];
                $special_msg = $data[4];
                $buyer_email = $data[5];
                $delivery_addr_1 = $data[6];
                $delivery_addr_2 = $data[7];
                $delivery_postcode = $data[8];
                $delivery_city = $data[9];
                $delivery_state = $data[10];
                $delivery_country = $data[11];

                if ($with_total == 1) {
                    $delivery_charges = $data[12];
                } else {
                    $delivery_charges = 0;
                }
                
                $isError = false;

                
                $stateCity = AstroGoController::getStateCityIdByPostcode($delivery_postcode);

                $transaction = [
                    'user' => $buyer_username,
                    'pass' => '',
                    'delivery_name' => $delivery_name,
                    'delivery_contact_no' => $delivery_contact_no,
                    'special_msg' => $special_msg,
                    'delivery_addr_1' => $delivery_addr_1,
                    'delivery_addr_2' => $delivery_addr_2,
                    'delivery_postcode' => $delivery_postcode,
                    'delivery_city' => $stateCity->city_id,
                    'delivery_state' => $stateCity->state_id,
                    'delivery_country' => $stateCity->country_id,
                    'delivery_charges' => $delivery_charges,
                    'qrcode' => $qrcodes,
                    'price_option' => $price_options,
                    'qty' => $qtys,
                    'devicetype' => 'cms',
                    'uuid' => NULL, // City ID
                    'lang' => 'EN',
                    'ip_address'  => Request::getClientIp(),
                    'location' => '',
                    'transaction_date' => date("Y-m-d H:i:s"),
                    'charity_id' => '',
                ];

                

                $data = MCheckout::checkout_transaction($transaction);

                if($data['status'] == "success") {
                    $transaction_id = $data["transaction_id"];

                    // PUSH TO SUCCESS LIST 
                    array_push($transferedProcessOrder, array(
                        "buyername" => $transaction['delivery_name'],
                        "transactionID" => $transaction_id
                    )); 

                    
                    // SAVE AS COMPLETED TRANSACTION //
                    $trans = Transaction::find($transaction_id);
                    if ($with_total == 2) {
                        $trans->total_amount = 0;
                        $trans->delivery_charges = 0;
                    }
                    $trans->status = 'completed';
                    $trans->modify_by = Session::get('username');
                    $trans->modify_date = date("Y-m-d h:i:sa");
                    $trans->save();
                    // SAVE AS COMPLETED TRANSACTION //
                    
                    // Transaction Details set price, disc, total, original_price to 0 for without amount
                    if ($with_total == 2) {
                        DB::table('jocom_transaction_details')
                          ->where('transaction_id', '=', $transaction_id)
                          ->update([
                            'price' => 0.00,
                            'disc' => 0.00,
                            'total' => 0.00,
                            'original_price' => 0.00
                          ]);
                    }
                    // 

                    // CREATE INV
                    MCheckout::generateInv($transaction_id, true);
                    // CREATE PO
                    MCheckout::generatePO($transaction_id, true);
                    // CREATE DO
                    MCheckout::generateDO($transaction_id, true);

                    // AUTOMATED LOG TO LOGISTIC APP
                    LogisticTransaction::log_transaction($transaction_id);

                } else {
                    array_push($manualProcessOrder, array(
                        "buyername" => $transaction['delivery_name']
                    ));
                    
                }

            }

            // MANUAL PROCESS HANDLING
            // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
            $subject = "Bulk Import Transaction Notification";
            $recipient = array(
                // "email"=>Config::get('constants.AstroGoShopManagerEmail'),
                "email" => 'quenny.leong@tmgrocer.com'
            );
            $data = array(
                    'execution_datetime'      => date("Y-m-d H:i:s"),
                    'total_records'  => count($manualProcessOrder) + count($transferedProcessOrder),
                    'manual_process'  => count($manualProcessOrder),
                    'manual_order_list'  => $manualProcessOrder,
                    'transfered_orders'  => $transferedProcessOrder,
                    'acc_name'  => 'tmGrocer',
            );

            Mail::queue('emails.bulkuploadtransaction', $data, function ($message) use ($recipient,$subject) {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($recipient['email'], $recipient['name'])
                        // ->cc(Config::get('constants.AstroGoShopManagerEmailCC'))
                        ->cc(['maruthu@tmgrocer.com'])
                        ->subject($subject);
            });
            
            $running_number = DB::table('jocom_running')
                    ->select('*')
                    ->where('value_key', '=', 'batch_no')->first();
            
            $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
            $NewRunner = Running::find($running_number->id);
            $NewRunner->counter = $running_number->counter + 1;
            $NewRunner->save();
            
        }
        catch(Exception $ex) {
            $isError = true;
            $message = $ex->getMessage();
        } finally {
            unlink($filename);

            if ($isError) {
                DB::rollback();

                return Response::json(array('status' => 400, 'message' => $message));
            } else {
                DB::commit();
                return Response::json(array('status' => 200, 'message' => 'Upload success', 
                    'transferedProcessOrder' => $transferedProcessOrder, 'manualProcessOrder' => $manualProcessOrder));
            }
        }
        
    }
    
    public function anyTaobao() {
        return View::make('taobao.index');
    }
    
    public function anyOne688() {
        return View::make('one688.index');
    }
    
    public function anyTmall() {
        return View::make('tmall.index');
    }

    public function anyTaobaolisting() {
        
        $buyusername = 'BuyDay';

        $listing = DB::table('jocom_transaction')
                    ->select('id', 'transaction_date', 'delivery_state', 'status')->where('buyer_username','=',$buyusername)->where('status','=','completed')->orderBy('transaction_date','DESC');

        return Datatables::of($listing)
                    ->edit_column('status', function($row)
                        {
                            switch ($row->status)
                            {
                                case 'completed':
                                    return '<span class="label label-success">Transfered</span>';
                                    break;
                                case 'pending':
                                    return '<span class="label label-danger">Pending</span>';
                                    break;
                                default:
                                    return '<span class="label label-warning">Cancelled</span>';
                                    break;
                            }
                        })
                    ->make(true);
    }
    
    public function anyOne688listing() {
        
        $buyusername = '1688';

        $listing = DB::table('jocom_transaction')
                    ->select('id', 'transaction_date', 'delivery_state', 'status')->where('buyer_username','=',$buyusername)->where('status','=','completed')->orderBy('transaction_date','DESC');

        return Datatables::of($listing)
                    ->edit_column('status', function($row)
                        {
                            switch ($row->status)
                            {
                                case 'completed':
                                    return '<span class="label label-success">Transfered</span>';
                                    break;
                                case 'pending':
                                    return '<span class="label label-danger">Pending</span>';
                                    break;
                                default:
                                    return '<span class="label label-warning">Cancelled</span>';
                                    break;
                            }
                        })
                    ->make(true);
    }
    
    public function anyTmalllisting() {
        
        $buyusername = 'tmall';

        $listing = DB::table('jocom_transaction')
                    ->select('id', 'transaction_date', 'delivery_state', 'status')->where('buyer_username','=',$buyusername)->where('status','=','completed')->orderBy('transaction_date','DESC');

        return Datatables::of($listing)
                    ->edit_column('status', function($row)
                        {
                            switch ($row->status)
                            {
                                case 'completed':
                                    return '<span class="label label-success">Transfered</span>';
                                    break;
                                case 'pending':
                                    return '<span class="label label-danger">Pending</span>';
                                    break;
                                default:
                                    return '<span class="label label-warning">Cancelled</span>';
                                    break;
                            }
                        })
                    ->make(true);
    }
    
    public function anyCheckvalue(){
        
        $retrun = Transaction::Checkjcmeleven11(204456); 
        
        print_r($retrun);
        
    }
    
    public function anyCreatebulkinvoice()
    {
        return View::make('admin.bulkinvoice');
    }
     public function anyBulkinvoice(){
        $transaction_list = Input::get('transaction_id');
        $email_id = Input::get('email_id');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        // $SuccesTransList =explode(',',$transaction_list);
        
        if(strlen($transaction_list) > 0 ){
                $selectedID = str_replace(" ", "", $transaction_list);
                $ListSelectedID = explode(",",$selectedID);
                if(count($ListSelectedID) > 0 ){
                    foreach ($ListSelectedID as $vSelID) {
                        $SuccesTransList[] = trim($vSelID);
                    }
                }
            }
            

        $totalRecord = count($SuccesTransList);
    
        // foreach ($SuccesTransList as $key => $value) {
        //     echo $value .'<br>';
            
        // }
        // die();

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        foreach ($SuccesTransList as $key => $value) {
            // echo $value .'<br>';

            $loopCounter++;
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            

            $trans = Transaction::find($value);
                $invToCountry = strtolower($trans->delivery_country);
              
                
                // echo $invToCountry;
               
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            185690,
186528,
185684,
185947,
185682,
187030);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        // $invoiceview = 'checkout.invoice_view_new_vpenjana';
                        
                         $invoiceview = 'checkout.invoice_view_new_v4';
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
                        $file_path = $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        $view = View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('multiPDF',true);
                                
                          $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                                $html1 =  substr($sub,0,strpos($sub,"</body>"));
                                //$html1 = str_replace("<page_header>","",$html1);
                                //$html1 = str_replace("</page_header>","",$html1);
                                //$html1 = str_replace("ody>","",$html1);

                                if($loopCounter == $totalRecord){
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }else{
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }

                                $contents = str_replace("ody>","",$contents);
                                // $contents = str_replace("<","",$contents);
                                $contents = str_replace("page_header>","",$contents);
                                
                                
          
                    
                        
                // }


        }

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';


        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
 
        $html2pdf->Output($file_path."/".$file_name, 'F');
        $mail =$email_id;
        $subject = "Bulk Invoice Details : " . $file_name;
        $attach = $file_path."/".$file_name;
        $body = array(
            'title' => 'Bulk Import Details'
        );

        Mail::send('emails.attendance', $body, function ($message) use ($subject, $mail, $attach)
        {
            $message->from('maruthu@tmgrocer', 'tmGrocer CMS Bulk Invoice Generated');
            $message->to($mail, 'Admin')->subject($subject);
            $message->attach($attach);
        });
        if (count(Mail::failures()) > 0) {
              Session::flash('message', 'Bulk Invoice Mail Failure.');     
            }
            else{
                Session::flash('success', 'Bulk Invoice Generated Successfully Please Check Mail! Pdf Dowload will start shortly'); 
            }
            
        return Redirect::to('transaction/createbulkinvoice')->with( ['pdf' => $file_path."/".$file_name,'file_name'=>$file_name] );
    

    }
    
    public function anyBulkagroinvoice(){
        $transaction_list = Input::get('transaction_id');
        $email_id=Input::get('email_id');
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        // $SuccesTransList =explode(',',$transaction_list);
        if(strlen($transaction_list) > 0 ){
                $selectedID = str_replace(" ", "", $transaction_list);
                $ListSelectedID = explode(",",$selectedID);
                if(count($ListSelectedID) > 0 ){
                    foreach ($ListSelectedID as $vSelID) {
                        $SuccesTransList[] = trim($vSelID);
                    }
                }
            }
            


// die('End');

        $totalRecord = count($SuccesTransList);

        // echo $totalRecord;

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        foreach ($SuccesTransList as $key => $value) {
            // echo $value .'<br>';

            $loopCounter++;
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            

            $trans = Transaction::find($value);
                $invToCountry = strtolower($trans->delivery_country);
              
                
                // echo $invToCountry;
               
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            185690,
186528,
185684,
185947,
185682,
187030);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        // $invoiceview = 'checkout.invoice_view_new_vpenjana';
                        
                         $invoiceview = 'checkout.invoice_view_new_agro';
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
                        $file_path = $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        $view = View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('multiPDF',true);
                                
                          $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                                $html1 =  substr($sub,0,strpos($sub,"</body>"));
                                //$html1 = str_replace("<page_header>","",$html1);
                                //$html1 = str_replace("</page_header>","",$html1);
                                //$html1 = str_replace("ody>","",$html1);

                                if($loopCounter == $totalRecord){
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }else{
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }

                                $contents = str_replace("ody>","",$contents);
                                // $contents = str_replace("<","",$contents);
                                $contents = str_replace("page_header>","",$contents);
                                
                                
          
                    
                        
                // }


        }

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        

        $html2pdf->Output($file_path."/".$file_name, 'F');
        $mail =$email_id;
        $subject = "Bulk Agro Invoice Details : " . $file_name;
        $attach = $file_path."/".$file_name;
        $body = array(
            'title' => 'Bulk Import Details'
        );

        Mail::send('emails.attendance', $body, function ($message) use ($subject, $mail, $attach)
        {
            $message->from('maruthu@tmgrocer.com', 'tmGrocer CMS Bulk Agro Invoice Generated');
            $message->to($mail, 'Admin')->subject($subject);
            $message->attach($attach);
        });
        if (count(Mail::failures()) > 0) {
              Session::flash('message', 'Bulk Invoice Mail Failure.');     
            }
            else{
                Session::flash('success', 'Bulk Invoice Generated Successfully Please Check Mail! Pdf Dowload will start shortly'); 
            }

        return Redirect::to('transaction/createbulkinvoice')->with( ['pdf' => $file_path."/".$file_name,'file_name'=>$file_name] );


    }
    
    public function anyBulkmanualinvoice(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        $SuccesTransList = array(
1568,
1569,
1570,
1571













);

// die('End');

        $totalRecord = count($SuccesTransList);

        // echo $totalRecord;

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        foreach ($SuccesTransList as $key => $value) {
            // echo $value .'<br>';

            $loopCounter++;
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            

            $trans = Transaction::find($value);
                $invToCountry = strtolower($trans->delivery_country);
              
                
                // echo $invToCountry;
               
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            185690,
186528,
185684,
185947,
185682,
187030);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        // $invoiceview = 'checkout.invoice_view_new_vpenjana';
                        
                         $invoiceview = 'checkout.invoice_view_new_v4';
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
                        $file_path = $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        $view = View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('multiPDF',true);
                                
                          $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                                $html1 =  substr($sub,0,strpos($sub,"</body>"));
                                //$html1 = str_replace("<page_header>","",$html1);
                                //$html1 = str_replace("</page_header>","",$html1);
                                //$html1 = str_replace("ody>","",$html1);

                                if($loopCounter == $totalRecord){
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }else{
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }

                                $contents = str_replace("ody>","",$contents);
                                // $contents = str_replace("<","",$contents);
                                $contents = str_replace("page_header>","",$contents);
                                
                                
          
                    
                        
                // }


        }

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

        // echo '<pre>';
        // print_r($finale); 
        //  echo '</pre>';
        // die();

        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        // $html2pdf->Output($file_path."/".$file_name, 'F');

        $html2pdf->Output($file_path."/".$file_name, 'F');



        return Response::download($file_path."/".$file_name);



    }
    
    public function anyBulkmanualagroinvoice(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        $SuccesTransList = array(

636330,
636465,
637318,
637342,
637343,
637359,
637376,
637395,
637446,
637449,
637451,
637457,
637470,
637949,
637950,
637958,
637960,
637966,
637968,
637977,
637987,
638003,
638006,
638009,
638014,
638015,
638021,
638040,
638041,
638053,
638057,
638061,
638067,
638073,
638079,
638083,
638202,
638203,
638204,
638207,
638208,
638210,
638211,
638212,
638213,
638215,
638216,
638221,
638224,
638228,
638229,
638231,
638232,
638238,
638239,
638246,
638248,
638251,
638253,
638255,
638257,
638264,
638269,
638274,
638277,
638282,
638283,
638284,
638288,
638289,
638291,
638292,
638293,
638299,
638303,
638304,
638305,
638307,
638337,
638339,
638340,
638345,
638346,
638347,
638358,
638359,
638361,
638365,
638366,
638367,
638370,
638371,
638372,
638376,
638377,
638378,
638381,
638383,
638384,
638385,
638389,
638390,
638525,
638527,
638529,
638530,
638531,
638538,
638540,
638541,
638542,
638543,
638544,
638547,
638549,
638550,
638551,
638552,
638554,
638555,
638557,
638642,
638643,
638644,
638645,
638648,
638649,
638650,
638651,
638652,
638653,
638654,
638655,
638656,
638657,
638658,
638660,
638661,
638662,
638663,
638664,
638665,
638667,
638669,
638671,
638672,
638673,
638674,
638675,
638676,
638681,
638685,
638686,
638689,
638691,
638693,
638694,
638696,
638697,
638698,
638702,
638703,
638705,
638706,
638707,
638708,
638709,
638711,
638712,
638713,
638714,
638716,
638862,
638863,
638864,
638865,
638866,
638867,
638869,
638870,
638872,
638874,
638876,
638877,
638878,
638879,
638880,
638881,
638882,
638883,
638884,
638885,
638886,
638888,
638889,
638890,
638892,
638893,
638895,
638896,
638898,
638899,
638900,
638902,
638903,
638905,
639089,
639090,
639091,
639092,
639093,
639094,
639096,
639097,
639098,
639099,
639101,
639102,
639103,
639104,
639106,
639107,
639108,
639112,
639114,
639115,
639131,
639264,
639265,
639266,
639267,
639268,
639270,
639273,
639275,
639278,
639280,
639289,
639292,
639527,
639531,
639539,
639540,
639544,
639545





);

// die('End');

        $totalRecord = count($SuccesTransList);

        // echo $totalRecord;

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        foreach ($SuccesTransList as $key => $value) {
            // echo $value .'<br>';

            $loopCounter++;
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            

            $trans = Transaction::find($value);
                $invToCountry = strtolower($trans->delivery_country);
              
                
                // echo $invToCountry;
               
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            185690,
186528,
185684,
185947,
185682,
187030);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        // $invoiceview = 'checkout.invoice_view_new_vpenjana';
                        
                         $invoiceview = 'checkout.invoice_view_new_agro';
                        
                        
                        // Select type of Invoice format
        
                        //New Invoice End
                        $file_path = $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                        
                        $file = (Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
        
                        $view = View::make($invoiceview)
                                 ->with('display_details', $INVView['general'])
                                 ->with('display_trans', $INVView['trans'])
                                 ->with('display_issuer',$INVView['issuer'])
                                 ->with('display_seller', $INVView['paypal'])
                                 ->with('display_coupon', $INVView['coupon'])
                                 ->with('display_product', $INVView['product'])
                                 ->with('display_group', $INVView['group'])
                                 ->with('display_points', $INVView['points'])
                                 ->with('display_earns', $INVView['earnedPoints'])
                                ->with('toCustomer', $INVView['toCustomer'])
                                ->with('buyer_type', $INVView['buyer_type'])
                                ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                                ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                                ->with('standard_currency',$INVView['standard_currency'])
                                ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                                ->with('multiPDF',true);
                                
                          $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                                $html1 =  substr($sub,0,strpos($sub,"</body>"));
                                //$html1 = str_replace("<page_header>","",$html1);
                                //$html1 = str_replace("</page_header>","",$html1);
                                //$html1 = str_replace("ody>","",$html1);

                                if($loopCounter == $totalRecord){
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }else{
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }

                                $contents = str_replace("ody>","",$contents);
                                // $contents = str_replace("<","",$contents);
                                $contents = str_replace("page_header>","",$contents);
                                
                                
          
                    
                        
                // }


        }

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

        // echo '<pre>';
        // print_r($finale); 
        //  echo '</pre>';
        // die();

        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        // $html2pdf->Output($file_path."/".$file_name, 'F');

        $html2pdf->Output($file_path."/".$file_name, 'F');



        return Response::download($file_path."/".$file_name);



    }
    
    
    public function anyBulkeinvoice(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        $SuccesTransList = array(
306680,
306997,
458425,
460343,
463604,
465775



);

die();

        $totalRecord = count($SuccesTransList);

        // echo $totalRecord;

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        foreach ($SuccesTransList as $key => $value) {
            // echo $value .'<br>';

            $loopCounter++;
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            

            $trans = Transaction::find($value);
                $invToCountry = strtolower($trans->delivery_country);
              
                
                // echo $invToCountry;
               
                        $INVView = self::createINVView($trans);
                        //New Invoice Start 
                        $invoiceview = "";
                        $transactionDate = $trans->transaction_date;
                        
                        $trans_parent = DB::table('jocom_transaction_parent_invoice')->where('transaction_id','=',$value)->first();
                        $eINV_no = $trans_parent->parent_inv;
                        
                        // Select type of Invoice format
                        $special_inv = array(
                            185690,
186528,
185684,
185947,
185682,
187030);
                            
                        if(in_array($trans->id, $special_inv)){
                            $invoiceview = 'checkout.invoice_view_new_v2';
                        }else{
                            
                            
                            
                            if($transactionDate >= Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE') || $trans->id == 120464){ //|| $trans->id == 120464
                                $invoiceview = 'checkout.invoice_view_new_v4';
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_SST_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_DISCOUNT_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v3';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                                $invoiceview = 'checkout.invoice_view_new_v2';
                                
                            }   elseif ($transactionDate >= Config::get('constants.NEW_INVOICE_START_DATE') && $transactionDate < Config::get('constants.NEW_INVOICE_V2_START_DATE')) {
                                
                                $invoiceview = 'checkout.invoice_view_new';
                                
                            }  else{
                                
                                $invoiceview = 'checkout.invoice_view';
                            }
                            
                        }
                        
                        // $invoiceview = 'checkout.invoice_view_new_vpenjana';
                        
                         $invoiceview = 'checkout.Einvoice_view_new_v4';
                        
                        
                        // Select type of Invoice format
                        
                        $file_path = Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH');
                
                        include app_path('library/html2pdf/html2pdf.class.php');
        
                        $ENVView = self::createEINVView($trans,$eINV_no);
        
                        
        
                        //New Invoice End
                        // $file_path = $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');
                        
                        $file = (Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH') . '/' . urlencode($trans->invoice_no) . '.pdf')."#".($trans->id).'#'.$trans->invoice_no;
                        // $file = ($display_details['transaction_id'])."#".$path;
                        $downloadLink = Crypt::encrypt($file);
                        $downloadLink = urlencode(base64_encode($downloadLink));
                        
                        $view = View::make($invoiceview)
                                ->with('display_details', $ENVView['general'])
                                ->with('display_trans', $ENVView['trans'])
                                ->with('display_issuer',$ENVView['issuer'])
                                ->with('display_seller', $ENVView['paypal'])
                                ->with('display_product', $ENVView['product'])
                                ->with('display_coupon', $INVView['coupon'])
                                ->with('display_group', $ENVView['group'])
                                ->with('display_points', $ENVView['points'])
                                ->with('display_earns', $ENVView['earnedPoints'])
                                ->with('toCustomer', $ENVView['toCustomer'])
                                ->with('multiPDF',true);
        
                        // $view = View::make($invoiceview)
                        //          ->with('display_details', $INVView['general'])
                        //          ->with('display_trans', $INVView['trans'])
                        //          ->with('display_issuer',$INVView['issuer'])
                        //          ->with('display_seller', $INVView['paypal'])
                        //          ->with('display_coupon', $INVView['coupon'])
                        //          ->with('display_product', $INVView['product'])
                        //          ->with('display_group', $INVView['group'])
                        //          ->with('display_points', $INVView['points'])
                        //          ->with('display_earns', $INVView['earnedPoints'])
                        //         ->with('toCustomer', $INVView['toCustomer'])
                        //         ->with('buyer_type', $INVView['buyer_type'])
                        //         ->with('invoice_bussines_currency',$INVView['invoice_bussines_currency'])
                        //         ->with('invoice_bussines_currency_rate',$INVView['invoice_bussines_currency_rate'])
                        //         ->with('standard_currency',$INVView['standard_currency'])
                        //         ->with('standard_currency_rate',$INVView['standard_currency_rate'])
                        //         ->with('multiPDF',true);
                                
                          $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
                                $html1 =  substr($sub,0,strpos($sub,"</body>"));
                                //$html1 = str_replace("<page_header>","",$html1);
                                //$html1 = str_replace("</page_header>","",$html1);
                                //$html1 = str_replace("ody>","",$html1);

                                if($loopCounter == $totalRecord){
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }else{
                                    $contents = $contents.(string)$html1.'<page></page>';
                                }

                                $contents = str_replace("ody>","",$contents);
                                // $contents = str_replace("<","",$contents);
                                $contents = str_replace("page_header>","",$contents);
                                
                                
          
                    
                        
                // }


        }

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

        // echo '<pre>';
        // print_r($finale); 
        //  echo '</pre>';
        // die();

        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        // $html2pdf->Output($file_path."/".$file_name, 'F');

        $html2pdf->Output($file_path."/".$file_name, 'F');



        return Response::download($file_path."/".$file_name);



    }
    
    public function anyBulkdo(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
        $loopCounter = 0;
        $SuccesTransList = array(
3053,
3057,
3063,
3066,
3070,
3076,
3078,
3079,
3081,
3084,
3062,
3066,
3072,
3075,
3080,
3082




);

// die();

        $totalRecord = count($SuccesTransList);
        
        $logisticID = DB::table('logistic_transaction')
            ->select(array(
                'logistic_transaction.*','jocom_transaction.qr_code','jocom_transaction.id','logistic_transaction.id AS LogisticID'
                ))
            ->leftJoin('jocom_transaction', 'jocom_transaction.id', '=', 'logistic_transaction.transaction_id')
            ->whereIn('logistic_transaction.transaction_id', $SuccesTransList)
            ->whereIn('logistic_transaction.status', [0,4])
            //->where("logistic_transaction.status",0)
            ->where("jocom_transaction.status",'completed')
            ->orderBy('logistic_transaction.delivery_city_id', 'ASC')
            ->orderBy('logistic_transaction.delivery_postcode', 'ASC')
            ->orderBy('logistic_transaction.delivery_addr_1', 'ASC')
            ->orderBy('logistic_transaction.transaction_id', 'ASC')->get();
            //->limit(5)
            
            
           
            
            // $FileName = date("Ymd")."_".date("His").'.pdf';

        // echo $totalRecord;

        include app_path('library/phpqrcode/qrlib.php');
            include app_path('library/html2pdf/html2pdf.class.php');

        $separator = '';
        $loopCounter = 0;
        $totalRecord = count($logisticID);
        $SuccesSortedList = array();
        $sortedList = "";
        

        foreach ($logisticID as $key => $value) {

            $loopCounter++;
            // echo $value->transaction_id . '<br>';
            if($create_separator){
                if($separator != $value->delivery_city){
                    $contents = $contents.'<div style="text-align:center;width:100%; margin-top:400px;font-size:70px;">'.$value->delivery_city.'</div>'.'<page></page>';
                    $separator = $value->delivery_city;
                }
            }
            $platform="";
            if($value->buyer_email == 'lazada@tmgrocer.com'){
                $platform = 'Lazada';
            } else if ($value->buyer_email == 'shopee@tmgrocer.com'){
                $platform = 'Shopee';
            } else if ($value->buyer_email == 'fnlife@tmgrocer.com'){
                $platform = 'FN-Life';
            } else if ($value->buyer_email == 'fnlifesuite@tmgrocer.com'){
                $platform = 'FN-TikTok';
            }
            else
            {
                $platform = 'tmGrocer';
            }
            // echo $value->transaction_id .'<br>';
            $transaction = Transaction::find($value->transaction_id);
            
            array_push($SuccesSortedList, array(
                "transaction_id" => $value->transaction_id,
                "delivery_city" => $value->delivery_city,
                "platform" => $platform,
            ));
            
            if($value->qr_code == ''){

                    
                    $qrCode     = $transaction->do_no;
                    $qrCodeFile = $transaction->do_no.'.png';
                    // $path = 'images/qrcode/';
                    QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);
                    $transaction->qr_code = $qrCodeFile;
                    $transaction->save();
            }

            $DOView = self::createDOView($transaction);

            $view = View::make('checkout.do_view')
                        ->with('display_details', $DOView['general'])
                        ->with('display_trans', $DOView['trans'])
                        ->with('display_seller', $DOView['paypal'])
                        ->with('display_product', $DOView['product'])
                        ->with('display_group', $DOView['group'])
                        ->with('delivery_type', $DOView['delivery_type'])
                        ->with('deliveryservice', $DOView['deliveryservice'])
                        ->with("display_delivery_service_items",$DOView['DeliveryOrderItems'])
                        ->with('multiPDF',true);

            $sub = substr($view, strpos($view,"<body>")+strlen("<body>")+4,strlen($view));
            $html1 =  substr($sub,0,strpos($sub,"</body>"));
            //$html1 = str_replace("<page_header>","",$html1);
            //$html1 = str_replace("</page_header>","",$html1);
            //$html1 = str_replace("ody>","",$html1);

            if($loopCounter == $totalRecord){
                $contents = $contents.(string)$html1.'<page></page>';
            }else{
                $contents = $contents.(string)$html1.'<page></page>';
            }

            


        }
        if($listing){
            $sortedList = View::make('checkout.sorter_list')->with("transaction",$SuccesSortedList);
        }

       
        $contents = $contents.(string)$sortedList;

        $contents = str_replace("ody>","",$contents);
        $contents = str_replace("page_header>","",$contents);
        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';
    
        


        $file_path = Config::get('constants.SORTER_DO_PDF_FILE_PATH');
        
        if($fileName != ""){
            $file_name = $fileName;
        }else{
            $file_name = date("Ymd")."_".date("His").'.pdf';
        }
        

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        $html2pdf->Output($file_path."/".$file_name, 'F');
        

        $finale = '<html><body style="border:solid 1px #000; max-width: 100%;font-size: 12px;font-family: "Arial", Georgia, Serif;margin:50px;">'.$contents.'</body></html>';

        // echo '<pre>';
        // print_r($finale); 
        //  echo '</pre>';
        // die();

        $file_name = date("Ymd")."_".date("His").'.pdf';

        $headers = array(
            'Content-Type: application/pdf',
        );
//
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        $html2pdf->WriteHTML($finale);
        // $html2pdf->Output($file_path."/".$file_name, 'F');

        $html2pdf->Output($file_path."/".$file_name, 'F');



        return Response::download($file_path."/".$file_name);



    }
    
    public function anyPlatformupdate(){
        
        $lazadatrans = array(
                530409
                
                
                
                
                );
        
        $dateyday= date("2022-09-15"); 
        
        $lazadatrans = DB::table('jocom_transaction')
                              ->select('id')
                              ->where('transaction_date','LIKE','%'.$dateyday.'%')
                              ->where('buyer_username','=','shopee')
                              ->get();
                die('Done');

        if(count($lazadatrans) > 0){

                foreach ($lazadatrans as $value) {
                    // echo $value .'New <br>';

                    $t_id = 0;
                    $t_id = $value->id;

                     $trasdetails = DB::table('jocom_transaction_details')->where('transaction_id','=',$t_id)->get();

                    //  if(count($trasdetails)>0){
                    //         foreach ($trasdetails as $value2) {
                    //             $d_id = 0; 
                    //             $d_price = 0; 
                    //             $d_unit = 0; 
                    //             $d_total = 0;
                    //             $d_id = $value2->id;
                    //             $d_price = round($value2->price,2);
                    //             $d_unit = (int)$value2->unit;

                    //             $d_total = round(($d_price * $d_unit),2);


                    //             DB::table('jocom_transaction_details')
                    //                 ->where("id","=",$d_id)
                    //                 ->update(
                    //                         ['total' => $d_total]
                    //                 );
                    //             # code...
                    //         }

                    //  }

                     $trasdetails = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as total_amount'))
                                    ->where('transaction_id','=',$t_id)->first();

                     if(count($trasdetails)>0){

                        echo $value->id .'-'.$trasdetails->total_amount.'<br>';
                        
                        // die();

                        $transactions = Transaction::find($t_id);
                        $transactions->total_amount = $transactions->delivery_charges + $trasdetails->total_amount;
                        $transactions->save();

                     }               



                }

               echo 'Process completed';

            }
    }
    
    
    
    
    
}