<?php
class ImportProductsController extends BaseController
{

    protected $product;
    protected $zone;
    protected $category;
    protected $seller;
    protected $price;
    protected $delivery;

    public function __construct(Product $product, Zone $zone, Category $category, Seller $seller, Price $price, Delivery $delivery)
    {

        $this->beforeFilter('auth', array(
            'except' => array(
                'Productdashboard'
            )
        ));

        $this->product = $product;
        $this->zone = $zone;
        $this->category = $category;
        $this->seller = $seller;
        $this->price = $price;
        $this->delivery = $delivery;

    }

    public function anyCreateimport()
    {
        return View::make('product.import');
    }
    public function anyImportproduct()
    {

        if (Input::hasFile('import'))
        {

            $csv_file = Input::file('import');
            $destinationPath = 'media/csv/importproduct';
            $file_name = 'productimport_' . time() . '.csv';
            $filepath = $destinationPath . '/' . $file_name;

            $csv_file->move($destinationPath, $file_name);
            $file = fopen($filepath, 'r');
            $isError = false;
            try
            {
                DB::beginTransaction();

                $data = fgetcsv($file, 1900, ",");
                $start_row = 1; //define start row
                $i = 1; //define row count flag
                while (($data = fgetcsv($file, 1900, ",")) !== false)
                {
                    
                    $region_country_id = $data[0];
                    $region_id = $data[1];
                    $seller_mult = explode(',', $data[2]);
                    foreach ($seller_mult as $sm1)
                    {
                        $seller_multiple[] = $sm1;
                    }
                    $product_name = $data[3];
                    $product_desc = $data[4];
                    $main_category = $data[5];
                    $primary_category[] = $data[5];
                    $subcat = explode(',', $data[6]);
                    foreach ($subcat as $subcc1)
                    {
                        $categoriess[] = $subcc1;
                    }
                    $sub_category = array_merge($primary_category, $categoriess);
                    $is_foreign_market=$data[11];
                    $price = array();
                    $price[]['label'] = $data[7];
                    if($is_foreign_market==1){
                    $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                    ->select('JER.*')
                    ->where('JER.currency_code_from','=','USD')
                    ->where('JER.currency_code_to','=','MYR')
                    ->first();
                    
                    $price[]['price']=ROUND($data[12] * $ExchangeRateMain->amount_to,2);
                    $price[]['price_promo'] =ROUND($data[13] * $ExchangeRateMain->amount_to,2);
                    }else{
                    $price[]['price'] = $data[8];
                    $price[]['price_promo'] = $data[9];
                    }
                    
                    $price[]['cost_price'][$data[2]] = $data[10];
                    if($is_foreign_market==1){
                    $price[]['foreign_price'] = $data[12];
                    $price[]['foreign_price_promo'] = $data[13];
                    }
                    $price[]['qty'] = $data[14];
                    $price[]['stock'] = $data[15];
                    $price[]['stock_unit'] = $data[16];
                    $price[]['p_referral_fees'] = 0;
                    $price[]['default'] = 1;
                    $gst = "2";
                    $gst_value = '0';
                    $delivery_time = "3-7 business days";
                    $zone_id[] = $data[19];
                    $zone_price[] = 0;
                    $status_csv = $data[20];
                    
                    $product_base_list=explode(',', $data[17]);
                    $product_base_quantity=explode(',', $data[18]);
                    
                    $productbase=array();
                    $result = [];
                    foreach ($product_base_list as $keys=> $bse)
                    {
                     if($bse!=0){
                       $bote=[$bse =>$product_base_quantity[$keys]];
                       $result = array_merge($result , $bote);
                       $productbase['option-1']['sku']=$result;
                     }
                    }
                    
                    $product_base_list=$productbase;
                    $weight = 0;

                    ///product post
                    $categories = array_unique($sub_category);
                    $seller_multiple = $seller_mult;
                    $vendor_multiple = "";
                    $is_base_product = '0';
                    $product = new Product;
                    $product->sku = '';
                    $product->sell_id = $seller_multiple[0]; // Warning: Misleading name
                    $product->name = $product_name;
                    $product->shortname = "";
                    $product->name_cn = "";
                    $product->name_my = "";
                    $product->category = implode(',', $categories); // For backward compatibility purpose
                    $product->description = $product_desc;
                    $product->description_cn = "";
                    $product->description_my = "";
                    $product->delivery_time = $delivery_time;
                    $product->insert_by = Session::get('username');
                    $product->modify_by = Session::get('username');
                    $product->gst = $gst;
                    $product->gst_value = $gst_value;
                    $product->related_product = "";
                    $product->do_cat = "";;
                    $product->status = $status_csv;
                    $product->weight = $weight;
                    $product->region_country_id = $region_country_id;
                    $product->region_id = $region_id;
                    $product->bulk = NULL;
                    $product->halal = 0;
                    $product->freshness = "";
                    $product->freshness_days = "";
                    $product->is_base_product = 0;
                    $product->min_qty = "";
                    $product->max_qty = "";
                    $product->is_foreign_market =$is_foreign_market;

                    $product->new_arrival_expire = NULL;

                    $product->save();

                    $productId = $product->id;

                    foreach ($seller_multiple as $value)
                    {

                        $ProductSeller = new ProductSeller;
                        $ProductSeller->product_id = $product->id;
                        $ProductSeller->seller_id = $value;
                        $ProductSeller->save();

                    }

                    $insert_audit = General::audit_trail('ImportProductsController.php', 'store()', 'Add Product', Session::get('username') , 'CMS');

                    foreach ($categories as $categoryId)
                    {

                        ProductsCategory::insert(['product_id' => $product->id, 'category_id' => $categoryId, 'main' => 0, 'created_at' => date('Y-m-d H:i:s') , ]);

                    }

                    $productsCategory = new ProductsCategory;
                    $productsCategory->setMainCategory($product->id, $main_category);

                    $tags = trim(Input::get('tag'));
                    $arr_tag = explode(',', $tags);

                    if ($tags != "")
                    {
                        foreach ($arr_tag as $tag)
                        {
                            if ($tag != " " && $tag != "")
                            {
                                Product::insert_tag(['product_id' => $product->id, 'tag_name' => trim($tag) , 'created_at' => date('Y-m-d H:i:s') , ]);
                            }
                        }
                    }

                    $price_option_cost_price = array();

                    $pricess = $price;
                    if ($pricess)
                    {
                        $result = $this
                            ->price
                            ->getPrices($product->id);
                        $price = [];
                        $count = 0;

                        foreach ($pricess as $p)
                        {

                            foreach ($p as $key => $value)
                            {
                                if ($key == 'cost_price')
                                {
                                    $price_option_cost_price[] = $value;
                                }
                                if ($key == 'label')
                                {
                                    $count++;

                                    if ($count > 1)
                                    {
                                        $arr_p['cost_pricelist'] = $price_option_cost_price;
                                        $price[] = $arr_p;
                                        $arr_p = '';
                                        $price_option_cost_price = array();
                                    }
                                }

                                $arr_p[$key] = $value;
                            }
                        }

                        $arr_p['cost_pricelist'] = $price_option_cost_price;
                        $price[] = $arr_p;
                        $option_row = 1;
                        foreach ($price as $p)
                        {
                            $data = ['default' => 0];

                            foreach ($p as $key => $value)
                            {
                                if ($key == 'default')
                                {
                                    $data[$key] = 1;
                                }

                                if (!empty($value))
                                {
                                    $data[$key] = $value;
                                }
                            }

                            $data['product_id'] = $product->id;
                            $costOptionPrice = $data['cost_pricelist'];
                            unset($data['cost_pricelist']);
                            unset($data['cost_price']);
                           
                            // FOREIGN MARKET PRICE //
                            if ($product->is_foreign_market == 1)
                            {
                                
                                $data['foreign_currency'] = 'USD';
                                $ratePrice = self::finalrate2($data['id'], $data['foreign_price'], $data['foreign_price_promo']);
                                foreach ($ratePrice['rate'] as $keyRP => $valueRP)
                                {
                                    if ($valueRP['currency_code_to'] == 'MYR')
                                    {
                                        $data['price'] = $valueRP['price'];
                                        $data['price_promo'] = $valueRP['price_promo'];
                                    }
                                }
                            }
                            // FOREIGN MARKET PRICE //
                            $newPriceID = $this
                                ->price
                                ->insertGetId($data);
                            foreach ($costOptionPrice as $keyCP => $valueCP)
                            {

                                $seller_id = key($valueCP);
                                $amountCostPrice = $valueCP[$seller_id];
                                // INSERT NEW
                                $PPriceSeller = new ProductPriceSeller;
                                $PPriceSeller->product_price_id = $newPriceID;
                                $PPriceSeller->seller_id = $seller_id;
                                $PPriceSeller->cost_price = $amountCostPrice;
                                $PPriceSeller->created_by = Session::get('username');
                                $PPriceSeller->updated_by = Session::get('username');
                                $PPriceSeller->activation = 1;
                                $PPriceSeller->save();
                            }
                            
                            if (($is_base_product != 1) && (count($product_base_list) > 0) && (isset($product_base_list['option-' . $option_row]['sku'])))
                            {
                                //if($is_base_product !=1 && count($product_base_list) > 0){
                                foreach ($product_base_list['option-' . $option_row]['sku'] as $key => $value)
                                {
                                    //                                            echo "<pre>";
                                    //                                            echo "SKU:". $key."<p>";
                                    //                                            echo "QTY:". $value."<p>";
                                    //                                            echo "</pre>";
                                    $Product = Product::where("sku", $key)->first();

                                    $ProductBaseItem = new ProductBaseItem();
                                    $ProductBaseItem->product_id = $productId;
                                    $ProductBaseItem->product_base_id = $Product->id;
                                    $ProductBaseItem->price_option_id = $newPriceID;
                                    $ProductBaseItem->quantity = $value;
                                    $ProductBaseItem->status = 1;
                                    $ProductBaseItem->created_by = Session::get('username');
                                    $ProductBaseItem->save();
                                    //print_r($value);
                                    
                                }
                                
                            }

                            $option_row++;

                            $insert_audit = General::audit_trail('ImportProductsController.php', 'store()', 'Add Product Label', Session::get('username') , 'CMS');
                        }
                    }

                    foreach ($zone_id as $key => $value)
                    {
                        $hello[] = $this
                            ->delivery
                            ->insert(['product_id' => $product->id, 'zone_id' => $value, 'price' => $zone_price[$key], ]);

                        $insert_audit = General::audit_trail('ImportProductsController.php', 'store()', 'Add Product Zone', Session::get('username') , 'CMS');
                    }
                    //   print_r($zone_id);
                    $qrCode = 'TM' . $product->id;
                    $qrCodeFile = $product->id . '.png';
                    $qrs = Product::generateQR3($qrCode, 'images/qrcode/', $qrCodeFile);
                    
                    // $this->product->generateQR($qrCode, 'images/qrcode/', $qrCodeFile);
                    $product->sku = 'TM-' . str_pad($product->id, 7, '0', STR_PAD_LEFT);
                    $product->qrcode = $qrCode;
                    $product->qrcode_file = $qrCodeFile;
                    $product->save();
                    $qrCodeFile = "";
                    $qrCode = "";
                    //===== Product History ================
                    if ($pricess)
                    {

                        //   $result = $this->price->getPrices($product->id);
                        $price = [];
                        $count = 0;

                        foreach ($pricess as $p)
                        {
                            foreach ($p as $key => $value)
                            {
                                if ($key == 'cost_price')
                                {
                                    $price_option_cost_price[] = $value;
                                }
                                if ($key == 'label')
                                {
                                    $count++;

                                    if ($count > 1)
                                    {
                                        $arr_p['cost_pricelist'] = $price_option_cost_price;
                                        $price[] = $arr_p;
                                        $arr_p = '';
                                        $price_option_cost_price = array();
                                    }
                                }

                                $arr_p[$key] = $value;
                            }
                        }

                        $arr_p['cost_pricelist'] = $price_option_cost_price;
                        $price[] = $arr_p;

                        $prices = Price::where('jocom_product_price.product_id', '=', $productId)->join('jocom_product_price_seller as seller', 'jocom_product_price.id', '=', 'seller.product_price_id')
                            ->select('jocom_product_price.*', 'seller.seller_id', 'seller.cost_price')
                            ->get();

                        foreach ($prices as $price)
                        {
                            $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                                ->select('product_base_id')
                                ->get();

                            $base_ids = array();
                            foreach ($bases as $base)
                            {
                                array_push($base_ids, $base->product_base_id);
                            }
                            $base_id = implode(',', $base_ids);

                            $producthistory = new ProductsHistory;
                            $producthistory->type = "New Product";
                            $producthistory->product_id = $productId;
                            $producthistory->sku = 'TM-' . str_pad($productId, 7, '0', STR_PAD_LEFT);
                            $producthistory->name = $product_name;
                            $producthistory->prd_status = $status_csv;
                            $producthistory->price_id = $price->id;
                            $producthistory->label = $price->label;
                            $producthistory->price = $price->price;
                            $producthistory->price_promo = $price->price_promo;
                            $producthistory->qty = $price->qty;
                            $producthistory->stock = $price->stock;
                            $producthistory->stock_unit = $price->stock_unit;
                            $producthistory->p_referral_fees = $price->p_referral_fees;
                            $producthistory->p_referral_fees_type = $price->p_referral_fees_type;
                            $producthistory->default = $price->default == '' ? $default : $price->default;
                            $producthistory->pri_product_id = $productId;
                            $producthistory->pri_status = 1;
                            $producthistory->p_weight = $price->p_weight;

                            $producthistory->created_by = Session::get('username');
                            $producthistory->seller_id = $price->seller_id;
                            $producthistory->cost = $price->cost_price;
                            $producthistory->base_id = $base_id;
                            $producthistory->gst_status = $gst;

                            $producthistory->delivery_time = $delivery_time;
                            $delivery_fees = array();
                            foreach ($zone_id as $key => $value)
                            {
                                array_push($delivery_fees, Zone::find($zone_id[$key])->name . ':' . $zone_price[$key]);
                            }
                            $delivery_fee = implode(',', $delivery_fees);

                            $producthistory->delivery_fee = $delivery_fee;
                            $producthistory->save();
                            $zone_id = "";

                        }

                    }

                    $products[] = ["product_id" => $productId, "product_name" => $product_name, "status" => 'success', "email" => Input::get('email') ];

                }

            }
            catch(Exception $ex)
            {
                $isError = true;
            }
            finally
            {
                
                if ($isError)
                {
                    DB::rollback();
                    Session::flash('message', 'Something went wrong!');
                    $data=['status'=>'failed'];
                    return Response::json($data);
                }
                else
                {
                    if($products!=""){
                    DB::commit();
                    self::createMail($products);
                    Session::flash('message', 'Products Data Imported successfully. Check Given Mail ID For Product Details');
                    $data=['status'=>'success'];
                    return Response::json($data);
                    }
                    else
                    {
                    DB::rollback();
                    Session::flash('message', 'Product Not Avilable');
                    $data=['status'=>'fails'];
                    return Response::json($data);
                    }
                }
            }

        }
        else
        {
          Session::flash('message', 'No CSV file detected');
                    return Redirect::back()->with('message', 'Something went wrong! Try Again');  
        }
    }
    
    
        public static function finalrate2($option_id,$price_actual,$price_promo){
            
            $option_id = $option_id;
            $price_actual = $price_actual;
            $price_promo = $price_promo;

            $finalCollection = array();

            $PriceOptionData = DB::table('jocom_product_price AS JPP' )
                         ->leftJoin('jocom_products AS JP','JP.id','=','JPP.product_id')
                         ->leftJoin('jocom_countries AS JC','JC.id','=','JP.region_country_id')
                         ->select('JPP.product_id','JP.region_country_id','JC.currency','JC.name')
                         ->where('JPP.id','=',$option_id)
                         ->first();


            $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                         ->select('JER.*')
                         ->where('JER.currency_code_from','=','USD')
                         ->where('JER.currency_code_to','=','MYR')
                         ->first();


            $localRate = array(
                "Country" => 'Malaysia',
                "currency_code_from" => $ExchangeRateMain->currency_code_from,
                "exchange_rate_from" => $ExchangeRateMain->amount_from,
                "currency_code_to" => 'MYR',
                "exchange_rate_to" => $ExchangeRateMain->amount_to,
                "price" => ROUND($price_actual * $ExchangeRateMain->amount_to,2),
                "price_promo" => ROUND($price_promo * $ExchangeRateMain->amount_to,2)
            );


            $ExchangeRateSub = DB::table('jocom_exchange_rate AS JER' )
                         ->select('JER.*')
                         ->where('JER.currency_code_from','=','USD')
                         ->where('JER.currency_code_to','=',$PriceOptionData->currency)
                         ->first();

     //       print_r($ExchangeRateSub);

            $SubRate = array(
                "Country" => $PriceOptionData->name,
                "currency_code_from" => $ExchangeRateSub->currency_code_from,
                "exchange_rate_from" => $ExchangeRateSub->amount_from,
                "currency_code_to" => $PriceOptionData->currency,
                "exchange_rate_to" => $ExchangeRateSub->amount_to,
                "price" => ROUND($price_actual * $ExchangeRateSub->amount_to,2),
                "price_promo" => ROUND($price_promo * $ExchangeRateSub->amount_to,2)
            );


            $finalCollection['rate'][] = $localRate;
            $finalCollection['rate'][] = $SubRate;

            return $finalCollection;
          
       /* REGION CODE */
        

}

    public static function createMail($products)
    {

        $fileName = 'productimport' . date("Ymdhis") . ".csv";

        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path . '/' . $fileName, 'w');

        fputcsv($file, ['Product ID', 'Product Name', 'Status']);
        
        foreach ($products as $row)
        {

            // echo $row['driver_username'];
            fputcsv($file, [$row['product_id'], $row['product_name'], $row['status']]);
            //}
            
        }

        fclose($file);
        $test = Config::get('constants.ENVIRONMENT');
        $mail = $products[0]['email'];
        $subject = "Product Import details : " . $fileName;
        $attach = $path . "/" . $fileName;
        $body = array(
            'title' => 'Product Import Details'
        );

        Mail::send('emails.attendance', $body, function ($message) use ($subject, $mail, $attach)
        {
            $message->from('notification@jocom.my', 'Jocom CMS Product Import');
            $message->to($mail, '')->subject($subject);
            $message->attach($attach);
        });

        unlink($attach);

    }
    
    //Import transaction module 9/9/9
    public function anyImporttransaction()
    {

        return View::make('admin.importtransaction');

    }

    public function anyTransactionimport()
    {

        if (Input::hasFile('import'))
        {

            $csv_file = Input::file('import');

            $destinationPath = 'public/media/csv/importtransaction';
            $file_name = 'transactionimport_' . time() . '.csv';
            $filepath = $destinationPath . '/' . $file_name;

            $csv_file->move($destinationPath, $file_name);
            $file = fopen($filepath, 'r');
            $isError = false;

            try
            {
                DB::beginTransaction();
                $data = fgetcsv($file, 1900, ",");

                $start_row = 1; //define start row
                $i = 1; //define row count flag
                while (($data = fgetcsv($file, 1900, ",")) !== false)
                {

                    $useraccess = Customer::where('username', Session::get('username'))->first();
                    $date = DateTime::createFromFormat('m/d/Y', $data[0]);
                    $trnsdate = $date->format('Y-m-d');
                    $transactiondate = $trnsdate;
                    $devicetype = "manual";
                    $is_self_collect = $data[2];
                    $user_id = $useraccess->id;
                    $users = $data[1];
                    $invoice_to_address = $data[3];
                    $external_ref_number = $data[4];
                    $product_ids = $data[5];
                    $proopds = explode('|', $product_ids);
                    $priceopt = array();
                    $qrcode = array();
                    $qty = array();
                    foreach ($proopds as $value)
                    {

                        $labels = Product::select('jocom_product_price.id as priceopt', 'jocom_products.qrcode as qrcode', 'jocom_product_price.price as price_local', 'jocom_product_price.price_promo as price_promo_local', 'jocom_product_price.foreign_price as price_foreign', 'jocom_product_price.foreign_price_promo as price_promo_foreign')->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                            ->where('jocom_products.id', '=', $value)->where('jocom_product_price.status', '=', 1)
                            ->first();

                        $priceopt[] = $labels->priceopt;
                        $qrcode[] = $labels->qrcode;
                        $price_local[] = $labels->price_local;
                        $price_promo_local[] = $labels->price_promo_local;
                        $price_foreign[] = $labels->price_foreign;
                        $price_promo_foreign[] = $labels->price_promo_foreign;

                    }
                    $proquantity = explode(',', $data[6]);
                    foreach ($proquantity as $valuess)
                    {
                        $qty[] = $valuess;
                    }
                    $deliveradd1 = $data[7];
                    $deliveradd2 = $data[8];
                    $deliverpostcode = $data[9];
                    $delivercountry = $data[10];
                    $state = $data[11];
                    $city = $data[12];
                    $delivery_charges = $data[13];
                    $delivername = $data[14];
                    $delivercontactno = $data[15];
                    $specialmsg = $data[16];

                    if ($data[0] == "")
                    {
                        $data = ['status' => 'failed'];
                        return Response::json($data);
                    }

                    $mycash = "";
                    $mycash = $devicetype;

                    $transaction_date = "";

                    if (date("Y-m-d H:i:s") >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                    {
                        /* SST TEMPLATE */
                        $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                    }
                    else
                    {
                        /* GST TEMPLATE */
                        $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                    }

                    if ($transactiondate)
                    {
                        $transaction_date = ($transactiondate != "") ? $transactiondate . " 00:00:00" : '';

                        if ($transaction_date >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                        {
                            /* SST TEMPLATE */
                            $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                        }
                        else
                        {
                            /* GST TEMPLATE */
                            $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                        }
                    }

                    $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';

                    /* WEB CHECKOUT */
                    // Check on user
                    //        try{
                    $ApiLog = new ApiLog;
                    $ApiLog->api = 'CHECKOUT';
                    $ApiLog->data = json_encode(Input::all());
                    $ApiLog->save();

                    $tax_rate = Fees::get_tax_percent();

                    $CustomerInfo = Customer::where('username', $users)->first();

                    $delivery_country_id = trim($delivercountry);

                    $countryInfo = DB::table('jocom_countries AS JC')->select('JC.id', 'JC.currency', 'JC.business_currency')
                        ->where('JC.id', '=', $CustomerInfo->country_id)
                        ->first();

                    // Jocom APP only Accept MYR
                    if (Input::get('devicetype') == 'android' || Input::get('devicetype') == 'ios')
                    {
                        $_POST["main_bussines_currency"] = 'MYR';
                    }
                    else
                    {
                        $_POST["main_bussines_currency"] = $countryInfo->business_currency;
                    }

                    // from cart to checkout, only with $_POST["user"], no return from PayPal
                    if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]) && isset($users))
                    {

                        /* CURRENCY SET */
                        $main_business_currency = isset($_POST["main_bussines_currency"]) ? $_POST["main_bussines_currency"] : 'MYR';

                        switch ($main_business_currency)
                        {

                            case 'MYR':

                                $main_business_currency = 'MYR';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';
                                $foreign_country_currency = 'MYR';

                                $main_business_currency_data = ExchangeRate::getExchangeRate($base_currency, $main_business_currency);
                                $base_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $standard_currency);
                                $foreign_country_rate_data = ExchangeRate::getExchangeRate($base_currency, $foreign_country_currency);

                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;
                                $foreign_country_rate = $foreign_country_rate_data->amount_to;
                                $main_business_currency_rate = $main_business_currency_data->amount_to;

                            break;

                            case 'RMB':

                                $main_business_currency = 'RMB';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';

                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;

                            case 'USD':

                                $main_business_currency = 'USD';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';
                                $foreign_country_currency = 'RMB';

                                $main_business_currency_data = ExchangeRate::getExchangeRate($main_business_currency, $main_business_currency);
                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $foreign_country_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $foreign_country_currency);

                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;
                                $foreign_country_rate = $foreign_country_rate_data->amount_to;
                                $main_business_currency_rate = $main_business_currency_data->amount_to;

                            break;

                            default:

                                $main_business_currency = 'MYR';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';

                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;

                        }

                        /* CURRENCY SET */

                        $get = array(
                            'user' => trim($users) , // Buyer Username
                            'pass' => "", // Buyer Password
                            'delivery_name' => trim($delivername) , // delivery name
                            'delivery_contact_no' => trim($delivercontactno) , // delivery contact no
                            'special_msg' => trim($specialmsg) , // special message
                            'delivery_addr_1' => trim($deliveradd1) ,
                            'delivery_addr_2' => trim($deliveradd2) ,
                            'delivery_postcode' => trim($deliverpostcode) ,
                            'delivery_city' => $city ? trim($city) : '', // City ID
                            'delivery_state' => trim($state) , // State ID
                            'delivery_country' => trim($delivercountry) , // Country ID
                            'delivery_charges' => trim($delivery_charges) ,
                            'qrcode' => $qrcode,
                            'price_option' => $priceopt, // Price Option
                            'qty' => $qty,
                            'devicetype' => $devicetype,
                            'uuid' => NULL, // City ID
                            'lang' => 'EN',
                            'ip_address' => Input::has('ip') ? Input::get('ip') : Request::getClientIp() ,
                            'location' => Input::has('location') ? Input::get('location') : '',
                            'isPopbox' => Input::has('isPopbox') ? Input::get('isPopbox') : '',
                            'deliverPopbox' => Input::has('deliverPopbox') ? Input::get('deliverPopbox') : '',
                            'popaddresstext' => Input::has('popaddresstext') ? Input::get('popaddresstext') : '',
                            'transaction_date' => $transaction_date,
                            'is_self_collect' => $is_self_collect,
                            'create_by_user' => Session::get('username') != '' ? Session::get('username') : '',
                            'charity_id' => Input::has('charity_id') ? Input::get('charity_id') : '',
                            'external_ref_number' => $external_ref_number ? $external_ref_number : '',
                            'selected_invoice_date' => Input::has('selected_invoice_date') ? Input::get('selected_invoice_date') : null,
                            'invoice_to_address' => $invoice_to_address ? $invoice_to_address : 1,

                            // CURRENCY //
                            'invoice_bussines_currency' => $main_business_currency,
                            'invoice_bussines_currency_rate' => $main_business_currency_rate,
                            'standard_currency' => $standard_currency,
                            'standard_currency_rate' => $standard_currency_rate,
                            'base_currency' => $base_currency,
                            'base_currency_rate' => $base_currency_rate,
                            'foreign_country_currency' => $foreign_country_currency,
                            'foreign_country_currency_rate' => $foreign_country_rate,

                            // CURRENCY //
                            
                        );

                        $ApiLog = new ApiLog;
                        $ApiLog->api = 'CHECKOUT_DATA_REQUEST';
                        $ApiLog->data = json_encode($get);
                        $ApiLog->save();

                        /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */
                        if ((Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0))
                        {

                            switch (Input::get('transfer_order_id_type'))
                            {
                                case 1: // ELEVENSTREET
                                    $elevenStreetDeliveryCharges = 0;
                                    $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        $elevenStreetDeliveryCharges = $elevenStreetDeliveryCharges + ($APIData['dlvCst'] * (100 / (100 + $tax_rate)));
                                    }

                                    $get['elevenstreetDeliveryCharges'] = $elevenStreetDeliveryCharges;

                                break;
                                case 2: // LAZADA
                                    $lazadaDeliveryCharges = 0;
                                    $OrderDataDetails = LazadaOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->order_items_details, true);
                                        $lazadaDeliveryCharges = $lazadaDeliveryCharges + ($APIData['ShippingAmount'] * (100 / (100 + $tax_rate)));
                                    }

                                    $get['lazadaDeliveryCharges'] = $lazadaDeliveryCharges;

                                break;
                                case 3: // Qoo10
                                    $qoo10DeliveryCharges = 0;
                                    $OrderDataDetails = QootenOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    // Check Account
                                    $QootenOrder = QootenOrder::where("id", Input::get('transfer_order_id'))->first();
                                    // Check Account
                                    if ($QootenOrder->from_account == 2)
                                    {
                                        $ExchangeRate = ExchangeRate::getExchangeRate('SGD', 'MYR');
                                        $ExchangeRateAmount = $ExchangeRate->amount_to;
                                    }
                                    else
                                    {
                                        $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR');
                                        $ExchangeRateAmount = $ExchangeRate->amount_to;
                                    }

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        // $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                                        
                                    }
                                    $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100 / (100 + $tax_rate)));

                                    //$get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges  * $ExchangeRateAmount;
                                    $get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges;

                                break;
                                case 4: // Shopee
                                    $shopeeDeliveryCharges = 0;
                                    $OrderDataDetails = ShopeeOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        // $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                                        
                                    }
                                    $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['estimated_shipping_fee'] * (100 / (100 + $tax_rate)));

                                    $get['shopeeDeliveryCharges'] = $shopeeDeliveryCharges;

                                break;
                                case 5: // PGMall
                                    $pgmallDeliveryCharges = 0;
                                    $OrderDataDetails = PGMallOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                    }
                                    $pgmallDeliveryCharges = $pgmallDeliveryCharges + ($APIData['shipping_amount'] * (100 / (100 + $tax_rate)));

                                    $get['pgmallDeliveryCharges'] = $pgmallDeliveryCharges;

                                break;
                                default:
                                break;
                            }

                        }
                        /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */

                        Session::put('lang', $get["lang"]);
                        Session::put('devicetype', $devicetype);

                        $signal_check = base64_encode(serialize($get));

                        // **********************************
                        // to remove later, for testing only
                        //Session::put('checkout_signal_check', $signal_check);
                        $data = array();
                        /*
                        // if with posting and transaction ID stored in session
                        if (Session::get('checkout_signal_check') && Session::get('checkout_transaction_id')) {
                        if (Session::get('checkout_signal_check') == $signal_check) {
                        $data['transaction_id'] = Session::get('checkout_transaction_id');
                        $data['status']         = 'success';
                        $data['message']        = 'valid';
                        } else {
                        Session::forget('checkout_signal_check');
                        }
                        }
                        */
                        // if no transaction ID
                        if ((!isset($data['transaction_id'])))
                        {
                            //$data = $this->checkout_m->checkout_transaction($get);
                            

                            $data = MCheckout::checkout_transaction($get);
                            $code = array();
                            $code['001'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.';
                            $code['002'] = 'Apologies for cancelling of your order.<br />Thank you for visiting JOCOM.';
                            $code['003'] = 'Check your order status in Transaction History.';
                            $code['004'] = 'Your payment status was "Pending".' . ' Transaction ID :' . $id . '. <br /> Thank you for shopping at JOCOM.';
                            $code['005'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting JOCOM.';
                            $code['006'] = 'Payment successful.<br>Your point will be credited directly to your account.';
                            $code['101'] = 'Invalid request. Please try again.';
                            $code['102'] = 'Invalid buyer or wrong password.';
                            $code['103'] = 'Invalid country.';
                            $code['104'] = 'Invalid state.';
                            $code['105'] = 'Invalid location selected.';
                            $code['106'] = 'Invalid request. (Product not found.)';
                            $code['107'] = 'Invalid request. (The product your order was out of stock.)';
                            $code['108'] = 'Invalid request. (Selected product is not available to your location.)';
                            $code['109'] = 'Invalid request. (Invalid price option selected.)';
                            $code['110'] = 'Oops, you are not allow to purchase this product Kindly remove it from your cart.';
                            $code['111'] = 'Oops, you do not meet the minimum purchase requirement for special pricing.';
                            $code['112'] = 'Invalid city.';
                            $code['113'] = 'Oops, you do not meet the minimum purchase requirement.';
                            $code['114'] = 'Invalid request. (Please activate your account before make any purchase.)';
                            $code['115'] = 'Oops, you are not allow to purchase this product more than 2 quantity.';
                            $stockmessage = "";
                            $text = "";
                            if ($data['status'] == 'success')
                            {
                                if ($data['outStockList'])
                                {
                                    foreach ($data['outStockList'] as $valuesku)
                                    {
                                        $stockskus[] = $valuesku['productID'];
                                    }
                                    $stockmessage = implode(',', $stockskus);
                                    $text = " Product ID - ";
                                }
                                $messcode = "Valid";
                                $maildata[] = array(
                                    'transaction_id' => $data['transaction_id'],
                                    'status' => $data['status'],
                                    'message' => $messcode,
                                    'username' => $data['userinfo']['username'],
                                    'email' => Input::get('email')
                                );
                            }
                            else
                            {
                                if ($data['outStockList'])
                                {
                                    foreach ($data['outStockList'] as $valuesku)
                                    {
                                        $stockskus[] = $valuesku['productID'];
                                    }
                                     $stockmessage = implode(',', $stockskus);
                                    $text = " Product ID - ";
                                    $messcode = $code[$data['message']] . $text . $stockmessage;
                                }
                                else{
                                    $messcode = $code[$data['message']];
                                }
                                if($messcode!=""){
                                $errordata=$messcode;
                                }
                                $maildata[] = array(
                                    'transaction_id' => '',
                                    'status' => $data['status'],
                                    'message' => $messcode,
                                    'username' => '',
                                    'email' => Input::get('email')
                                );
                            }

                            if ((Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0))
                            {

                                switch (Input::get('transfer_order_id_type'))
                                {
                                    case 1: // ELEVENSTREET
                                        // Update Status 11Street Order
                                        $ElevenStreetOrder = ElevenStreetOrder::find(Input::get('transfer_order_id'));
                                        $ElevenStreetOrder->status = 2;
                                        $ElevenStreetOrder->transaction_id = $data["transaction_id"];
                                        $ElevenStreetOrder->save();
                                    break;
                                    case 2: // LAZADA
                                        // Update Status 11Street Order
                                        $LazadaOrder = LazadaOrder::find(Input::get('transfer_order_id'));
                                        $LazadaOrder->status = 2;
                                        $LazadaOrder->transaction_id = $data["transaction_id"];
                                        $LazadaOrder->save();

                                    break;
                                    case 3: // Qoo10
                                        // Update Status Qoo10 Order
                                        $Qoo10Order = QootenOrder::find(Input::get('transfer_order_id'));
                                        $Qoo10Order->status = 2;
                                        $Qoo10Order->transaction_id = $data["transaction_id"];
                                        $Qoo10Order->save();

                                    break;
                                    case 4: // Shopee
                                        // Update Status ShopeeOrder Order
                                        $ShopeeOrder = ShopeeOrder::find(Input::get('transfer_order_id'));
                                        $ShopeeOrder->status = 2;
                                        $ShopeeOrder->transaction_id = $data["transaction_id"];
                                        $ShopeeOrder->save();

                                    break;
                                    case 5: // PGMall
                                        // Update Status PGMallOrder Order
                                        $PGMallOrder = PGMallOrder::find(Input::get('transfer_order_id'));
                                        $PGMallOrder->status = 2;
                                        $PGMallOrder->transaction_id = $data["transaction_id"];
                                        $PGMallOrder->save();

                                    break;
                                    default:
                                    break;
                                }

                            }

                            if ($get['isPopbox'] == 1)
                            {
                                $popBoxReturn = PopboxController::savePopBox($data["transaction_id"], $get['deliverPopbox'], $get['popaddresstext']);

                            }
                            $data['popbox'] = $popBoxReturn;

                        }
                        $ApiLog = new ApiLog;
                        $ApiLog->api = 'CHECKOUT_RESPONSE';
                        $ApiLog->data = json_encode($data);
                        $ApiLog->save();

                        // succesfully checkout
                        if (isset($data['status']) && $data['status'] == 'success')
                        {
                            Session::put('checkout_signal_check', $signal_check);
                            Session::put('checkout_transaction_id', $data["transaction_id"]);
                            Session::put('lang', $data["lang"]);
                            Session::put('devicetype', $data["devicetype"]);
                            Session::put('android_orderid', $data["transaction_id"]);
                            $cashbackflag = $data["cashbackflag"];
                            $cb_productid = $data["cashbacktext"];

                            $tempdata = MCheckout::get_checkout_info($data["transaction_id"]);
                            $data = array_merge($data, $tempdata);

                            if ($web_checkout && Input::get('devicetype') == "web")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 2; // Web Checkout
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "web_others")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 3; // Web Other Platforms Checkout //ie., Ecommunity
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "web_mycash")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 4; // Web Other Platforms Checkout //ie., MyCashOnline etc
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "webboost")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 5; // Web Checkout Boost Payment
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "webasean")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 6; // Web Checkout Boost Payment
                                $Transaction->save();
                            }

                            if (isset($data['trans_query']))
                            {
                                $buyerId = $data['trans_query']->buyer_id;
                                $points = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                                $data['points'] = $points;

                                //Start JCashback
                                if (isset($cashbackflag) && $cashbackflag == 1)
                                {

                                    $trans_cashback = DB::table('jocom_transaction_jcashback')->where('user_id', '=', $buyerId)->where('qrcode', '=', $cb_productid)->where('status', '=', 1)
                                        ->where('jcash_point_used', '=', 0)
                                        ->orderBy('id', 'ASC')
                                        ->first();

                                    if (count($trans_cashback) > 0)
                                    {

                                        $cback_array = array(
                                            'id' => $trans_cashback->id,
                                            'sku' => $trans_cashback->sku,
                                            'user_id' => $trans_cashback->user_id,
                                            'product_name' => $trans_cashback->product_name,
                                            'jcash_point' => $trans_cashback->jcash_point
                                        );

                                        $tempdatacashback['trans_cashback'] = $cback_array;

                                        $data = array_merge($data, $tempdatacashback);

                                    }

                                }
                                //End JCashback
                                if ($web_checkout)
                                {

                                    return $data;
                                }
                                else
                                {

                                    $back[] = self::edits($data['transaction_id']);

                                    // return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                                    
                                }

                            }
                            else
                            {
                                if ($data['message'] == '')
                                {
                                    $data['message'] = '101';
                                }
                                // $data['message'] = 'Invalid request. Please try again.';
                                /* WEB CHECKOUT CHANGES */
                                if ($web_checkout)
                                {
                                    return $data;
                                }
                                else
                                {

                                    return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('dataCollection', $data);
                                }
                                /* WEB CHECKOUT CHANGES */

                            }
                        }
                        else
                        {
                            Session::forget('checkout_signal_check');
                            Session::forget('checkout_transaction_id');

                            if ($web_checkout)
                            {
                                return $data;
                            }
                            else
                            {
                                if ($data['message'] == '')
                                {
                                    $data['message'] = '101';
                                }

                                // $data['message'] = 'Invalid request. Please try again.';
                                return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('kkwprod', $data['kkwprod'])->with('userinfo', $data['userinfo'])->with('dataCollection', $data);;
                            }

                        }
                    }
                }
            }
            catch(Exception $ex)
            {
                $ApiLog = new ApiLog;
                $ApiLog->api = 'TRANSACTION_IMPORT_ERROR';
                $ApiLog->data = $ex->getMessage() . '-' . $ex->getLine();
                $ApiLog->save();
                $isError = true;
            }
            finally
            {
                if ($isError)
                {
                    DB::rollback();
                    Session::flash('message', 'Someting Went Wrong ! Please Check CSV File');
                    $data = ['status' => 'failed'];
                    return Response::json($data);
                }
                else
                {
                    if($errordata!=""){
                   $data = ['status' => 'alert','message'=>$errordata];
                    return Response::json($data);  
                  }else{
                    DB::commit();
                    self::transactionMail($maildata);
                    Session::flash('success', 'Transaction Data Imported successfully.Please Check given Mail ID');
                    $data = ['status' => 'success'];
                    return Response::json($data);
                  }
                }
            }

        }

    }

    public function Edits($id = null)

    {

        if (isset($id))
        {

            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION
            $Transaction = Transaction::find($id);

            $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
            $stateName = array();
            foreach ($SysAdminRegion as $value)
            {
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS)
                {
                    $stateName[] = $valueS->name;
                }
            }
            if (!in_array($Transaction->delivery_state, $stateName))
            {
                $access = false;
                $SysAdmin = User::where("username", Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::getSysAdminRegion($SysAdmin->id);
                foreach ($SysAdminRegion as $key => $value)
                {
                    if ($value->region_id == 0)
                    {
                        $access = true;
                    }
                }
                if (!$access)
                {
                    return Redirect::to('transaction')->with('message', "You don't have access right for that Transaction ID")
                        ->withErrors($validator)->withInput();
                }
            }

            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION
            if (Input::has('id'))
            {

                $validator = Validator::make(Input::all() , Transaction::$rules, Transaction::$message);

                if ($validator->passes())
                {

                    $rs = Transaction::save_transaction();

                    if ($rs == true)
                    {

                        $insert_audit = General::audit_trail('TransactionController.php', 'Edit()', 'Edit Transaction', Session::get('username') , 'CMS');
                        return Redirect::to('transaction/edit/' . $id)->with('success', 'Transaction(ID: ' . $id . ') updated successfully.');
                    }
                    else
                    {
                        return Redirect::to('transaction')->with('message', 'Transaction(ID: ' . $id . ') update failed.');
                    }

                }
                else
                {

                    return Redirect::to('transaction/edit/' . $id)->with('message', 'The highlighted field is required')
                        ->withErrors($validator)->withInput();
                }
            }
            else
            {

                $editTrans = Transaction::where('id', '=', $id)->get();
                //$addCust = Customer::all()->lists('username', 'full_name');
                $editCoupon = TCoupon::where('transaction_id', '=', $id)->first();
                // $editDetails = TDetails::where('transaction_id','=', $id)->get();
                $editDetails = DB::table('jocom_transaction_details AS a')->select('a.*', 'b.name')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                //->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                
                    ->where('a.transaction_id', '=', $id)
                //->where('c.main', '=', '1')
                // ->groupBy('a.sku')
                //->orderBy('c.category_id')
                ->orderBy('b.name')
                //->where('a.product_group', '!=', '')
                
                    ->get();

                $parentInv = DB::table('jocom_transaction_parent_invoice')->where('transaction_id', '=', $id)->get();
                $editPaypal = TPayPal::where('transaction_id', '=', $id)->get();
                $editMolPay = TMolPay::where('transaction_id', '=', $id)->get();
                $editTMPay = TMPay::where('transaction_id', '=', $id)->get();
                $editBoost = BoostTransaction::where('transaction_id', '=', $id)->get();
                $editGrabPay = DB::table('jocom_grabpay_transaction')->where('transaction_id', '=', $id)->get();
                $editFavePay = DB::table('jocom_favepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPacePay = DB::table('jocom_pacepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')->where('jocom_transaction_point.transaction_id', '=', $id)->get();

                // Earned points
                $earnedPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)->where('point_users.status', '=', 1)
                    ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                    ->get();

                $earnedId = [];

                foreach ($earnedPoints as $earnedPoint)
                {
                    $earnedId[] = $earnedPoint->id;
                }

                $reversalPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                    ->get();

                $reversedId = [];

                foreach ($reversalPoints as $reversalPoint)
                {
                    $reversedId[] = $reversalPoint->reversal;
                }

                $earnedPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))->get();

                $earnedBPoint = DB::table('bcard_transactions')->where('bill_no', '=', 'T' . $id)->pluck('point');

                $agentId = array_get(reset($editTrans->toArray()) , 'agent_id');

                if ($agentId != 1)
                {
                    $agent = Agent::find($agentId);
                    $agentCode = $agent->agent_code;
                }

                // Get delivery status
                $logisticStatus = LogisticTransaction::where('transaction_id', '=', $id)->pluck('status');

                if ($logisticStatus)
                {
                    $deliveryStatus = LogisticTransaction::get_status($logisticStatus);
                }
                else
                {
                    $deliveryStatus = '-';
                }

                $customerInvoiceLog = DB::table('jocom_customer_invoice_log AS JCIL')->where("JCIL.transaction_id", $id)->where("JCIL.status", 1)
                    ->first();

                // Nadzri - Add PGMall (22/03/2022)
                $externalPlatformList = array(
                    "11Street",
                    "lazada",
                    "shopee",
                    "pgmall",
                    "Qoo10",
                    "wiraizkandar"
                );

                if (count($customerInvoiceLog) > 0 && in_array($Transaction->buyer_username, $externalPlatformList))
                {
                    $isVisible = true;
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => true
                    );
                }
                else
                {

                    $isGenerated = false;
                    $isVisible = false;

                    if (in_array($Transaction->buyer_username, $externalPlatformList))
                    {
                        $isVisible = true;
                    }
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => $isGenerated
                    );

                }

                return ['id' => $id];
                
            }
        }
        else
        {
            return Redirect::to('transaction')->with('message', 'No transaction is selected for edit.');
        }
    }

    public static function transactionMail($maildata)
    {

        $fileName = 'TransactionImport' . date("Ymdhis") . ".csv";

        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path . '/' . $fileName, 'w');

        fputcsv($file, ['Transaction ID', 'Status', 'Username', 'Message']);

        foreach ($maildata as $row)
        {

            // echo $row['driver_username'];
            fputcsv($file, [$row['transaction_id'], $row['status'], $row['username'], $row['message']]);
            //}
            
        }

        fclose($file);
        $test = Config::get('constants.ENVIRONMENT');
        $mail = $maildata[0]['email'];
        $subject = "Transaction Import details : " . $fileName;
        $attach = $path . "/" . $fileName;
        $body = array(
            'title' => 'Transaction Import Details'
        );

        Mail::send('emails.attendance', $body, function ($message) use ($subject, $mail, $attach)
        {
            $message->from('orders@jocom.my', 'Jocom CMS Transaction Import');
            $message->to($mail, '')->subject($subject);
            $message->attach($attach);
        });

        unlink($attach);

    }

    public function anyImportshopeetransaction()
    {

        return View::make('admin.importshoppetransaction');

    }

    public function anyTransactionshopeeimport()
    {

        if (Input::hasFile('import'))
        {

            $csv_file = Input::file('import');

            $destinationPath = 'public/media/csv/importshopeetransaction';
            $file_name = 'transactionshopeeimport_' . time() . '.csv';
            $filepath = $destinationPath . '/' . $file_name;

            $csv_file->move($destinationPath, $file_name);
            $file = fopen($filepath, 'r');
            $isError = false;

            try
            {
                DB::beginTransaction();
                $data = fgetcsv($file, 1900, ",");

                $start_row = 1; //define start row
                $i = 1; //define row count flag
                $result = array();

                while (($data = fgetcsv($file, 1900, ",")) !== false)
                {
                    $result[$data[0]][] = $data;
                }
                foreach ($result as $key => $final)
                {
                    $productlast = "";
                    $pricelast = "";
                    $quantitylast = "";
                    $product_name = "";
                    foreach ($final as $keys => $last)
                    {
                        $code = count($final);
                        if ($key == $last[0] && $keys != $code - 1)
                        {
                            $product_contain=strpos($last[2],'TM');
                            
                            if($product_contain===0){
                              $pro1 = explode("TM", $last[2]);
                              $productlast .= $pro1[1] . ",";  
                            }else{
                               $proids = DB::table('jocom_product_price')->select('product_id')
                               ->where('id', '=', $last[2])
                               ->first(); 
                               $productlast .=$proids->product_id . ",";
                            }
                        }
                        if ($key == $last[0] && $keys == $code - 1)
                        {
                            $product_contain=strpos($last[2],'TM');
                            if($product_contain===0){
                            $pro1 = explode("TM", $last[2]);
                            $productlast .= $pro1[1];
                            }else{
                               $proids = DB::table('jocom_product_price')->select('product_id')
                               ->where('id', '=', $last[2])
                               ->first(); 
                               $productlast .=$proids->product_id;
                            }
                        }
                        if ($key == $last[0] && $keys != $code - 1)
                        {
                            $pricelast .= $last[3] . ",";
                        }
                        if ($key == $last[0] && $keys == $code - 1)
                        {
                            $pricelast .= $last[3];
                        }
                        if ($key == $last[0] && $keys != $code - 1)
                        {
                            $quantitylast .= $last[4] . ",";
                        }
                        if ($key == $last[0] && $keys == $code - 1)
                        {
                            $quantitylast .= $last[4];
                        }
                        if ($key == $last[0] && $keys != $code - 1)
                        {
                            $product_name .= $last[1] . "|";
                        }
                        if ($key == $last[0] && $keys == $code - 1)
                        {
                            $product_name .= $last[1];
                        }

                        $delivery_fees = $last[5];
                        $reciever_name = $last[6];
                        $phone = $last[7];
                        $delivery_address = $last[8];
                        $city = $last[9];
                        $state = $last[10];
                        $country = $last[11];
                        $postcode = $last[12];
                        $from_store=$last[13];
                    }
                   
                    $exact_data[] = array(
                        "orderid" => $key,
                        "product_name" => $product_name,
                        "product_id" => $productlast,
                        "price" => $pricelast,
                        "quantity" => $quantitylast,
                        "delivery_free" => $delivery_fees,
                        "reciever_name" => $reciever_name,
                        "phone" => $phone,
                        "delivery_address" => $delivery_address,
                        "city" => $city,
                        "state" => $state,
                        "country" => $country,
                        "postcode" => $postcode,
                        "store"=>$from_store
                    );

                }
                foreach ($exact_data as $data)
                {

                    $cities = DB::table('jocom_cities AS JSPS ')->where('JSPS.name', 'LIKE',$data["city"])->first();
                    $states = DB::table('jocom_country_states AS JCS ')->where('JCS.name', 'LIKE',$data["state"])->first();
                    $cities_id = $cities->id;
                    $statess_id = $states->id;
                    $country_id_s = $states->country_id;

                    $useraccess = Customer::where('username', Session::get('username'))->first();

                    $transactiondate = date("Y-m-d");
                    $devicetype = "manual";
                    $is_self_collect = "0";
                    $user_id = $useraccess->id;
                    $users = "shopee";
                    $invoice_to_address = "1";
                    $external_ref_number = $data["orderid"];
                    $product_ids = $data["product_id"];
                    $proopds = explode(',', $product_ids);
                    $priceopt = array();
                    $qrcode = array();
                    $qty = array();
                    $shopee_original_price=array();
                    foreach ($proopds as $value)
                    {

                        $labels = Product::select('jocom_product_price.id as priceopt', 'jocom_products.qrcode as qrcode', 'jocom_product_price.price as price_local', 'jocom_product_price.price_promo as price_promo_local', 'jocom_product_price.foreign_price as price_foreign', 'jocom_product_price.foreign_price_promo as price_promo_foreign')->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                            ->where('jocom_products.id', '=', $value)->where('jocom_product_price.status', '=', 1)
                            ->first();
                        $priceopt[] = $labels->priceopt;
                        $qrcode[] = $labels->qrcode;
                        $price_local[] = $labels->price_local;
                        $price_promo_local[] = $labels->price_promo_local;
                        $price_foreign[] = $labels->price_foreign;
                        $price_promo_foreign[] = $labels->price_promo_foreign;

                    }
                    $proquantity = explode(',', $data["quantity"]);
                    foreach ($proquantity as $valuess)
                    {
                        $qty[] = $valuess;
                    }
                    $proprices = explode(',', $data["price"]);
                    foreach ($proprices as $pricesvalue)
                    {
                        $shopee_original_price[] = $pricesvalue;
                    }

                    $deliveradd1 = $data["delivery_address"];
                    $deliveradd2 = "";
                    $deliverpostcode = $data["postcode"];
                    $delivercountry = $country_id_s;
                    $state = $statess_id;
                    $city = $cities_id;
                    $delivery_charges = $data["delivery_free"];
                    $delivername = $data["reciever_name"];
                    $delivercontactno = $data["phone"];
                    $specialmsg = "Transaction transfer from Shopee(Order Number:" . $data["orderid"] . ")";

                    $mycash = "";
                    $mycash = $devicetype;

                    $transaction_date = "";

                    if (date("Y-m-d H:i:s") >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                    {
                        /* SST TEMPLATE */
                        $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                    }
                    else
                    {
                        /* GST TEMPLATE */
                        $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                    }

                    if ($transactiondate)
                    {
                        $transaction_date = ($transactiondate != "") ? $transactiondate . " 00:00:00" : '';

                        if ($transaction_date >= Config::get('constants.NEW_INVOICE_SST_START_DATE'))
                        {
                            /* SST TEMPLATE */
                            $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                        }
                        else
                        {
                            /* GST TEMPLATE */
                            $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
                        }
                    }

                    $checkout_view = ($devicetype == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';

                    /* WEB CHECKOUT */
                    // Check on user
                    //        try{
                    $ApiLog = new ApiLog;
                    $ApiLog->api = 'CHECKOUT';
                    $ApiLog->data = json_encode(Input::all());
                    $ApiLog->save();

                    $tax_rate = Fees::get_tax_percent();

                    $CustomerInfo = Customer::where('username', $users)->first();

                    $delivery_country_id = trim($delivercountry);

                    $countryInfo = DB::table('jocom_countries AS JC')->select('JC.id', 'JC.currency', 'JC.business_currency')
                        ->where('JC.id', '=', $CustomerInfo->country_id)
                        ->first();

                    // Jocom APP only Accept MYR
                    if (Input::get('devicetype') == 'android' || Input::get('devicetype') == 'ios')
                    {
                        $_POST["main_bussines_currency"] = 'MYR';
                    }
                    else
                    {
                        $_POST["main_bussines_currency"] = $countryInfo->business_currency;
                    }

                    // from cart to checkout, only with $_POST["user"], no return from PayPal
                    if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]) && isset($users))
                    {

                        /* CURRENCY SET */
                        $main_business_currency = isset($_POST["main_bussines_currency"]) ? $_POST["main_bussines_currency"] : 'MYR';

                        switch ($main_business_currency)
                        {

                            case 'MYR':

                                $main_business_currency = 'MYR';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';
                                $foreign_country_currency = 'MYR';

                                $main_business_currency_data = ExchangeRate::getExchangeRate($base_currency, $main_business_currency);
                                $base_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $standard_currency);
                                $foreign_country_rate_data = ExchangeRate::getExchangeRate($base_currency, $foreign_country_currency);

                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;
                                $foreign_country_rate = $foreign_country_rate_data->amount_to;
                                $main_business_currency_rate = $main_business_currency_data->amount_to;

                            break;

                            case 'RMB':

                                $main_business_currency = 'RMB';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';

                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;

                            case 'USD':

                                $main_business_currency = 'USD';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';
                                $foreign_country_currency = 'RMB';

                                $main_business_currency_data = ExchangeRate::getExchangeRate($main_business_currency, $main_business_currency);
                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $foreign_country_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $foreign_country_currency);

                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;
                                $foreign_country_rate = $foreign_country_rate_data->amount_to;
                                $main_business_currency_rate = $main_business_currency_data->amount_to;

                            break;

                            default:

                                $main_business_currency = 'MYR';
                                $base_currency = 'MYR';
                                $standard_currency = 'USD';

                                $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
                                $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
                                $base_currency_rate = $base_currency_rate_data->amount_to;
                                $standard_currency_rate = $standard_currency_rate_data->amount_to;

                        }

                        /* CURRENCY SET */

                        $get = array(
                            'user' => trim($users) , // Buyer Username
                            'pass' => "", // Buyer Password
                            'delivery_name' => trim($delivername) , // delivery name
                            'delivery_contact_no' => trim($delivercontactno) , // delivery contact no
                            'special_msg' => trim($specialmsg) , // special message
                            'delivery_addr_1' => trim($deliveradd1) ,
                            'delivery_addr_2' => trim($deliveradd2) ,
                            'delivery_postcode' => trim($deliverpostcode) ,
                            'delivery_city' => $city ? trim($city) : '', // City ID
                            'delivery_state' => trim($state) , // State ID
                            'delivery_country' => trim($delivercountry) , // Country ID
                            'delivery_charges' => trim($delivery_charges) ,
                            'qrcode' => $qrcode,
                            'price_option' => $priceopt, // Price Option
                            'shopee_original_price'=>$shopee_original_price,
                            'qty' => $qty,
                            'devicetype' => $devicetype,
                            'uuid' => NULL, // City ID
                            'lang' => 'EN',
                            'ip_address' => Input::has('ip') ? Input::get('ip') : Request::getClientIp() ,
                            'location' => Input::has('location') ? Input::get('location') : '',
                            'isPopbox' => Input::has('isPopbox') ? Input::get('isPopbox') : '',
                            'deliverPopbox' => Input::has('deliverPopbox') ? Input::get('deliverPopbox') : '',
                            'popaddresstext' => Input::has('popaddresstext') ? Input::get('popaddresstext') : '',
                            'transaction_date' => $transaction_date,
                            'is_self_collect' => $is_self_collect,
                            'create_by_user' => Session::get('username') != '' ? Session::get('username') : '',
                            'charity_id' => Input::has('charity_id') ? Input::get('charity_id') : '',
                            'external_ref_number' => $external_ref_number ? $external_ref_number : '',
                            'selected_invoice_date' => Input::has('selected_invoice_date') ? Input::get('selected_invoice_date') : null,
                            'invoice_to_address' => $invoice_to_address ? $invoice_to_address : 1,

                            // CURRENCY //
                            'invoice_bussines_currency' => $main_business_currency,
                            'invoice_bussines_currency_rate' => $main_business_currency_rate,
                            'standard_currency' => $standard_currency,
                            'standard_currency_rate' => $standard_currency_rate,
                            'base_currency' => $base_currency,
                            'base_currency_rate' => $base_currency_rate,
                            'foreign_country_currency' => $foreign_country_currency,
                            'foreign_country_currency_rate' => $foreign_country_rate,

                            // CURRENCY //
                            
                        );

                        $ApiLog = new ApiLog;
                        $ApiLog->api = 'CHECKOUT_DATA_REQUEST';
                        $ApiLog->data = json_encode($get);
                        $ApiLog->save();

                        /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */
                        if ((Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0))
                        {

                            switch (Input::get('transfer_order_id_type'))
                            {
                                case 1: // ELEVENSTREET
                                    $elevenStreetDeliveryCharges = 0;
                                    $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        $elevenStreetDeliveryCharges = $elevenStreetDeliveryCharges + ($APIData['dlvCst'] * (100 / (100 + $tax_rate)));
                                    }

                                    $get['elevenstreetDeliveryCharges'] = $elevenStreetDeliveryCharges;

                                break;
                                case 2: // LAZADA
                                    $lazadaDeliveryCharges = 0;
                                    $OrderDataDetails = LazadaOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->order_items_details, true);
                                        $lazadaDeliveryCharges = $lazadaDeliveryCharges + ($APIData['ShippingAmount'] * (100 / (100 + $tax_rate)));
                                    }

                                    $get['lazadaDeliveryCharges'] = $lazadaDeliveryCharges;

                                break;
                                case 3: // Qoo10
                                    $qoo10DeliveryCharges = 0;
                                    $OrderDataDetails = QootenOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    // Check Account
                                    $QootenOrder = QootenOrder::where("id", Input::get('transfer_order_id'))->first();
                                    // Check Account
                                    if ($QootenOrder->from_account == 2)
                                    {
                                        $ExchangeRate = ExchangeRate::getExchangeRate('SGD', 'MYR');
                                        $ExchangeRateAmount = $ExchangeRate->amount_to;
                                    }
                                    else
                                    {
                                        $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR');
                                        $ExchangeRateAmount = $ExchangeRate->amount_to;
                                    }

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        // $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                                        
                                    }
                                    $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100 / (100 + $tax_rate)));

                                    //$get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges  * $ExchangeRateAmount;
                                    $get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges;

                                break;
                                case 4: // Shopee
                                    $shopeeDeliveryCharges = 0;
                                    $OrderDataDetails = ShopeeOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                        // $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                                        
                                    }
                                    $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['estimated_shipping_fee'] * (100 / (100 + $tax_rate)));

                                    $get['shopeeDeliveryCharges'] = $shopeeDeliveryCharges;

                                break;
                                case 5: // PGMall
                                    $pgmallDeliveryCharges = 0;
                                    $OrderDataDetails = PGMallOrderDetails::getByOrderID(Input::get('transfer_order_id'));

                                    foreach ($OrderDataDetails as $keyDetails => $valueDetails)
                                    {
                                        $APIData = json_decode($valueDetails->api_result_return, true);
                                    }
                                    $pgmallDeliveryCharges = $pgmallDeliveryCharges + ($APIData['shipping_amount'] * (100 / (100 + $tax_rate)));

                                    $get['pgmallDeliveryCharges'] = $pgmallDeliveryCharges;

                                break;
                                default:
                                break;
                            }

                        }
                        /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */

                        Session::put('lang', $get["lang"]);
                        Session::put('devicetype', $devicetype);

                        $signal_check = base64_encode(serialize($get));

                        // **********************************
                        // to remove later, for testing only
                        //Session::put('checkout_signal_check', $signal_check);
                        $predata = $data;
                        $data = array();

                        /*
                        // if with posting and transaction ID stored in session
                        if (Session::get('checkout_signal_check') && Session::get('checkout_transaction_id')) {
                        if (Session::get('checkout_signal_check') == $signal_check) {
                        $data['transaction_id'] = Session::get('checkout_transaction_id');
                        $data['status']         = 'success';
                        $data['message']        = 'valid';
                        } else {
                        Session::forget('checkout_signal_check');
                        }
                        }
                        */
                        // if no transaction ID
                        if ((!isset($data['transaction_id'])))
                        {
                            //$data = $this->checkout_m->checkout_transaction($get);
                            $exsixttorders= ShopeeOrder::where('ordersn','=', $predata["orderid"])->first();
                            if(empty(($exsixttorders))){
                             //print"<pre>";print_r($get);
                            $data = MCheckout::checkout_transaction($get);
                            
                            // print"<pre>";print_r($predata);
                            //print"<pre>";print_r($cath);
                            if ($data['status'] == "success")
                            {
                                $shopeeorder = new ShopeeOrder;
                                $shopeeorder->ordersn = $predata["orderid"];
                                $shopeeorder->name = $predata["reciever_name"];
                                $shopeeorder->phone = $predata["phone"];
                                $shopeeorder->transaction_id = $data['transaction_id'];
                                $shopeeorder->migrate_from = "Shopee";
                                $shopeeorder->created_by = Session::get('username');
                                $shopeeorder->created_at = DATE("Y-m-d h:i:s");
                                $shopeeorder->updated_by = "manual";
                                $shopeeorder->updated_at = DATE("Y-m-d h:i:s");
                                $shopeeorder->status = "2";
                                $shopeeorder->is_completed = "2";
                                $shopeeorder->activation = "1";
                                $shopeeorder->from_account = $predata['store'];
                                $shopeeorder->save();

                                $product = explode(",", $predata["product_id"]);
                                $product_name1 = explode("|", $predata["product_name"]);
                                $product1quantity = explode(",", $predata["quantity"]);
                                $product1price = explode(",", $predata["price"]);

                                foreach ($product as $prokeys => $valuews)
                                {

                                    $api_encode = array(
                                        'item_name' => $product_name1[$prokeys],
                                        'item_sku' => "TM" . $valuews,
                                        'variation_quantity_purchased' => $product1quantity[$prokeys],
                                        'deal_price' => $product1price[$prokeys],
                                        'name' => $predata["reciever_name"],
                                        'migrate_from' => "shopee",
                                        'phone' => $predata["phone"],
                                        'delivery_address' => $predata["delivery_address"],
                                        'state' => $predata["state"],
                                        'city' => $predata["city"],
                                        'country' => $predata["country"],
                                        'postcode' => $predata["postcode"]
                                    );

                                    $shopeeorder_detailss = new ShopeeOrderDetails;
                                    $shopeeorder_detailss->order_id = $shopeeorder->id;
                                    $shopeeorder_detailss->ordersn = $predata["orderid"];
                                    $shopeeorder_detailss->item_name = $product_name1[$prokeys];
                                    $shopeeorder_detailss->item_sku = "TM" . $valuews;
                                    $shopeeorder_detailss->api_result_return = json_encode($api_encode);
                                    $shopeeorder_detailss->api_result_full = json_encode($api_encode);
                                    $shopeeorder_detailss->created_at = DATE("Y-m-d h:i:s");
                                    $shopeeorder_detailss->created_by = Session::get('username');
                                    $shopeeorder_detailss->updated_at = DATE("Y-m-d h:i:s");
                                    $shopeeorder_detailss->updated_by = "manual";
                                    $shopeeorder_detailss->status = "1";
                                    $shopeeorder_detailss->activation = "1";
                                    $shopeeorder_detailss->save();

                                }

                            }
                            $code = array();
                            $code['001'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.';
                            $code['002'] = 'Apologies for cancelling of your order.<br />Thank you for visiting JOCOM.';
                            $code['003'] = 'Check your order status in Transaction History.';
                            $code['004'] = 'Your payment status was "Pending".' . ' Transaction ID :' . $id . '. <br /> Thank you for shopping at JOCOM.';
                            $code['005'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting JOCOM.';
                            $code['006'] = 'Payment successful.<br>Your point will be credited directly to your account.';
                            $code['101'] = 'Invalid request. Please try again.';
                            $code['102'] = 'Invalid buyer or wrong password.';
                            if($states==""){
                              $code['103'] = 'Please check state name ';   
                            }else{
                               $code['103'] = 'Invalid country '; 
                            }
                            $code['104'] = 'Invalid state ';
                            $code['105'] = 'Invalid location selected.';
                            $code['106'] = 'Invalid request. (Product not found.)';
                            $code['107'] = 'Invalid request. (The product your order was out of stock.)';
                            $code['108'] = 'Invalid request. (Selected product is not available to your location.)';
                            $code['109'] = 'Invalid request. (Invalid price option selected.)';
                            $code['110'] = 'Oops, you are not allow to purchase this product Kindly remove it from your cart.';
                            $code['111'] = 'Oops, you do not meet the minimum purchase requirement for special pricing.';
                            $code['112'] = 'Invalid city.';
                            $code['113'] = 'Oops, you do not meet the minimum purchase requirement.';
                            $code['114'] = 'Invalid request. (Please activate your account before make any purchase.)';
                            $code['115'] = 'Oops, you are not allow to purchase this product more than 2 quantity.';
                            $stockmessage = "";
                            $text = "";

                            if ($data['status'] == 'success')
                            {

                                if ($data['outStockList'])
                                {
                                    foreach ($data['outStockList'] as $valuesku)
                                    {
                                        $stockskus[] = $valuesku['productID'];
                                    }
                                    $stockmessage = implode(',', $stockskus);
                                    $text = " Product ID - ";
                                }
                                $messcode = "Valid";
                                $maildata[] = array(
                                    'transaction_id' => $data['transaction_id'],
                                    'status' => $data['status'],
                                    'message' => $messcode,
                                    'username' => $data['userinfo']['username'],
                                    'email' => Input::get('email'),
                                    'shopee_id'=>$predata["orderid"]
                                );
                            }
                            else
                            {
                                if ($data['outStockList'])
                                {
                                    foreach ($data['outStockList'] as $valuesku)
                                    {
                                        $stockskus[] = $valuesku['productID'];
                                    }
                                    $stockmessage = implode(',', $stockskus);
                                    $text = " Product ID - ";
                                    $messcode = $code[$data['message']] . $text . $stockmessage;
                                }
                                else{
                                    $messcode = $code[$data['message']]."in the Order ID".$predata["orderid"];
                                }
                                
                                if($messcode!=""){
                                    $errordata =$messcode;
                                }
                                
                                
                                $maildata[] = array(
                                    'transaction_id' => '',
                                    'status' => $data['status'],
                                    'message' => $messcode,
                                    'username' => '',
                                    'email' => Input::get('email'),
                                    'shopee_id'=>$predata["orderid"]
                                );
                            }
                            if ((Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0))
                            {

                                switch (Input::get('transfer_order_id_type'))
                                {
                                    case 1: // ELEVENSTREET
                                        // Update Status 11Street Order
                                        $ElevenStreetOrder = ElevenStreetOrder::find(Input::get('transfer_order_id'));
                                        $ElevenStreetOrder->status = 2;
                                        $ElevenStreetOrder->transaction_id = $data["transaction_id"];
                                        $ElevenStreetOrder->save();
                                    break;
                                    case 2: // LAZADA
                                        // Update Status 11Street Order
                                        $LazadaOrder = LazadaOrder::find(Input::get('transfer_order_id'));
                                        $LazadaOrder->status = 2;
                                        $LazadaOrder->transaction_id = $data["transaction_id"];
                                        $LazadaOrder->save();

                                    break;
                                    case 3: // Qoo10
                                        // Update Status Qoo10 Order
                                        $Qoo10Order = QootenOrder::find(Input::get('transfer_order_id'));
                                        $Qoo10Order->status = 2;
                                        $Qoo10Order->transaction_id = $data["transaction_id"];
                                        $Qoo10Order->save();

                                    break;
                                    case 4: // Shopee
                                        // Update Status ShopeeOrder Order
                                        $ShopeeOrder = ShopeeOrder::find(Input::get('transfer_order_id'));
                                        $ShopeeOrder->status = 2;
                                        $ShopeeOrder->transaction_id = $data["transaction_id"];
                                        $ShopeeOrder->save();

                                    break;
                                    case 5: // PGMall
                                        // Update Status PGMallOrder Order
                                        $PGMallOrder = PGMallOrder::find(Input::get('transfer_order_id'));
                                        $PGMallOrder->status = 2;
                                        $PGMallOrder->transaction_id = $data["transaction_id"];
                                        $PGMallOrder->save();

                                    break;
                                    default:
                                    break;
                                }

                            }

                            if ($get['isPopbox'] == 1)
                            {
                                $popBoxReturn = PopboxController::savePopBox($data["transaction_id"], $get['deliverPopbox'], $get['popaddresstext']);

                            }
                            $data['popbox'] = $popBoxReturn;
                        }
                        else{
                            
                          $errordata="Please Remove the Duplicate Orders! Check CSV File.Order ID: ".$predata["orderid"];
                        }

                        }
                        $ApiLog = new ApiLog;
                        $ApiLog->api = 'CHECKOUT_RESPONSE';
                        $ApiLog->data = json_encode($data);
                        $ApiLog->save();

                        // succesfully checkout
                        if (isset($data['status']) && $data['status'] == 'success')
                        {
                            Session::put('checkout_signal_check', $signal_check);
                            Session::put('checkout_transaction_id', $data["transaction_id"]);
                            Session::put('lang', $data["lang"]);
                            Session::put('devicetype', $data["devicetype"]);
                            Session::put('android_orderid', $data["transaction_id"]);
                            $cashbackflag = $data["cashbackflag"];
                            $cb_productid = $data["cashbacktext"];

                            $tempdata = MCheckout::get_checkout_info($data["transaction_id"]);
                            $data = array_merge($data, $tempdata);

                            if ($web_checkout && Input::get('devicetype') == "web")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 2; // Web Checkout
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "web_others")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 3; // Web Other Platforms Checkout //ie., Ecommunity
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "web_mycash")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 4; // Web Other Platforms Checkout //ie., MyCashOnline etc
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "webboost")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 5; // Web Checkout Boost Payment
                                $Transaction->save();
                            }

                            if ($web_checkout && Input::get('devicetype') == "webasean")
                            {
                                $Transaction = Transaction::find($data["transaction_id"]);
                                $Transaction->checkout_source = 6; // Web Checkout Boost Payment
                                $Transaction->save();
                            }

                            if (isset($data['trans_query']))
                            {
                                $buyerId = $data['trans_query']->buyer_id;
                                $points = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                                $data['points'] = $points;

                                //Start JCashback
                                if (isset($cashbackflag) && $cashbackflag == 1)
                                {

                                    $trans_cashback = DB::table('jocom_transaction_jcashback')->where('user_id', '=', $buyerId)->where('qrcode', '=', $cb_productid)->where('status', '=', 1)
                                        ->where('jcash_point_used', '=', 0)
                                        ->orderBy('id', 'ASC')
                                        ->first();

                                    if (count($trans_cashback) > 0)
                                    {

                                        $cback_array = array(
                                            'id' => $trans_cashback->id,
                                            'sku' => $trans_cashback->sku,
                                            'user_id' => $trans_cashback->user_id,
                                            'product_name' => $trans_cashback->product_name,
                                            'jcash_point' => $trans_cashback->jcash_point
                                        );

                                        $tempdatacashback['trans_cashback'] = $cback_array;

                                        $data = array_merge($data, $tempdatacashback);

                                    }

                                }
                                //End JCashback
                                if ($web_checkout)
                                {

                                    return $data;
                                }
                                else
                                {

                                    $back[] = self::editshopee($data['transaction_id']);

                                    // return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                                    
                                }

                            }
                            else
                            {
                                if ($data['message'] == '')
                                {
                                    $data['message'] = '101';
                                }
                                // $data['message'] = 'Invalid request. Please try again.';
                                /* WEB CHECKOUT CHANGES */
                                if ($web_checkout)
                                {
                                    return $data;
                                }
                                else
                                {

                                    return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('dataCollection', $data);
                                }
                                /* WEB CHECKOUT CHANGES */

                            }
                        }
                        else
                        {
                            Session::forget('checkout_signal_check');
                            Session::forget('checkout_transaction_id');

                            if ($web_checkout)
                            {
                                return $data;
                            }
                            else
                            {
                                if ($data['message'] == '')
                                {
                                    $data['message'] = '101';
                                }

                                // $data['message'] = 'Invalid request. Please try again.';
                                return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('kkwprod', $data['kkwprod'])->with('userinfo', $data['userinfo'])->with('dataCollection', $data);;
                            }

                        }
                    }

                }
            }
            catch(Exception $ex)
            {

                $ApiLog = new ApiLog;
                $ApiLog->api = 'TRANSACTION_IMPORT_ERROR';
                $ApiLog->data = $ex->getMessage() . '-' . $ex->getLine();
                $ApiLog->save();
                $isError = true;

            }
            finally
            {
                if ($isError)
                {
                    DB::rollback();
                    Session::flash('message', 'Someting Went Wrong ! Please Check CSV File');
                    $data = ['status' => 'failed'];
                    return Response::json($data);
                }
                else
                {
        
                  if($errordata!=""){
                   $data = ['status' => 'alert','message'=>$errordata];
                    return Response::json($data);  
                  }else{
                    DB::commit();
                    self::shopeetransactionMail($maildata);
                    Session::flash('success', 'Shopee Transaction Data Imported successfully.Please Check given Mail ID');
                    $data = ['status' => 'success'];
                    return Response::json($data);
                  }
                }
            }

        }

    }


    public function Editshopee($id = null)

    {

        if (isset($id))
        {

            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION
            $Transaction = Transaction::find($id);

            $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
            $stateName = array();
            foreach ($SysAdminRegion as $value)
            {
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS)
                {
                    $stateName[] = $valueS->name;
                }
            }
            if (!in_array($Transaction->delivery_state, $stateName))
            {
                $access = false;
                $SysAdmin = User::where("username", Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::getSysAdminRegion($SysAdmin->id);
                foreach ($SysAdminRegion as $key => $value)
                {
                    if ($value->region_id == 0)
                    {
                        $access = true;
                    }
                }
                if (!$access)
                {
                    return Redirect::to('transaction')->with('message', "You don't have access right for that Transaction ID")
                        ->withErrors($validator)->withInput();
                }
            }

            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION
            if (Input::has('id'))
            {

                $validator = Validator::make(Input::all() , Transaction::$rules, Transaction::$message);

                if ($validator->passes())
                {

                    $rs = Transaction::save_transaction();

                    if ($rs == true)
                    {

                        $insert_audit = General::audit_trail('TransactionController.php', 'Edit()', 'Edit Transaction', Session::get('username') , 'CMS');
                        return Redirect::to('transaction/edit/' . $id)->with('success', 'Transaction(ID: ' . $id . ') updated successfully.');
                    }
                    else
                    {
                        return Redirect::to('transaction')->with('message', 'Transaction(ID: ' . $id . ') update failed.');
                    }

                }
                else
                {

                    return Redirect::to('transaction/edit/' . $id)->with('message', 'The highlighted field is required')
                        ->withErrors($validator)->withInput();
                }
            }
            else
            {

                $editTrans = Transaction::where('id', '=', $id)->get();
                //$addCust = Customer::all()->lists('username', 'full_name');
                $editCoupon = TCoupon::where('transaction_id', '=', $id)->first();
                // $editDetails = TDetails::where('transaction_id','=', $id)->get();
                $editDetails = DB::table('jocom_transaction_details AS a')->select('a.*', 'b.name')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                //->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                
                    ->where('a.transaction_id', '=', $id)
                //->where('c.main', '=', '1')
                // ->groupBy('a.sku')
                //->orderBy('c.category_id')
                ->orderBy('b.name')
                //->where('a.product_group', '!=', '')
                
                    ->get();

                $parentInv = DB::table('jocom_transaction_parent_invoice')->where('transaction_id', '=', $id)->get();
                $editPaypal = TPayPal::where('transaction_id', '=', $id)->get();
                $editMolPay = TMolPay::where('transaction_id', '=', $id)->get();
                $editTMPay = TMPay::where('transaction_id', '=', $id)->get();
                $editBoost = BoostTransaction::where('transaction_id', '=', $id)->get();
                $editGrabPay = DB::table('jocom_grabpay_transaction')->where('transaction_id', '=', $id)->get();
                $editFavePay = DB::table('jocom_favepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPacePay = DB::table('jocom_pacepay_transaction')->where('transaction_id', '=', $id)->get();
                $editPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')->where('jocom_transaction_point.transaction_id', '=', $id)->get();

                // Earned points
                $earnedPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)->where('point_users.status', '=', 1)
                    ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                    ->get();

                $earnedId = [];

                foreach ($earnedPoints as $earnedPoint)
                {
                    $earnedId[] = $earnedPoint->id;
                }

                $reversalPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->where('point_transactions.transaction_id', '=', $id)->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                    ->get();

                $reversedId = [];

                foreach ($reversalPoints as $reversalPoint)
                {
                    $reversedId[] = $reversalPoint->reversal;
                }

                $earnedPoints = DB::table('point_transactions')->select('point_types.*', 'point_transactions.*')
                    ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                    ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                    ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))->get();

                $earnedBPoint = DB::table('bcard_transactions')->where('bill_no', '=', 'T' . $id)->pluck('point');

                $agentId = array_get(reset($editTrans->toArray()) , 'agent_id');

                if ($agentId != 1)
                {
                    $agent = Agent::find($agentId);
                    $agentCode = $agent->agent_code;
                }

                // Get delivery status
                $logisticStatus = LogisticTransaction::where('transaction_id', '=', $id)->pluck('status');

                if ($logisticStatus)
                {
                    $deliveryStatus = LogisticTransaction::get_status($logisticStatus);
                }
                else
                {
                    $deliveryStatus = '-';
                }

                $customerInvoiceLog = DB::table('jocom_customer_invoice_log AS JCIL')->where("JCIL.transaction_id", $id)->where("JCIL.status", 1)
                    ->first();

                // Nadzri - Add PGMall (22/03/2022)
                $externalPlatformList = array(
                    "11Street",
                    "lazada",
                    "shopee",
                    "pgmall",
                    "Qoo10",
                    "wiraizkandar"
                );

                if (count($customerInvoiceLog) > 0 && in_array($Transaction->buyer_username, $externalPlatformList))
                {
                    $isVisible = true;
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => true
                    );
                }
                else
                {

                    $isGenerated = false;
                    $isVisible = false;

                    if (in_array($Transaction->buyer_username, $externalPlatformList))
                    {
                        $isVisible = true;
                    }
                    $customerInvoice = array(
                        "isVisible" => $isVisible,
                        "isGenerated" => $isGenerated
                    );

                }

                return ['id' => $id];
                // return View::make(Config::get('constants.ADMIN_FOLDER').'.transaction_edit')
                //     ->with('display_trans', $editTrans)
                //     ->with('display_cust', $addCust)
                //     ->with('display_coupon', $editCoupon)
                //     ->with('display_points', $editPoints)
                //     ->with('display_details', $editDetails)
                //     ->with('display_parent_inv', $parentInv)
                //     ->with('display_paypal', $editPaypal)
                //     ->with('display_molpay', $editMolPay)
                //     ->with('display_mpay', $editTMPay)
                //     ->with('display_boost', $editBoost)
                //     ->with('display_grab', $editGrabPay)
                //     ->with('display_Fave', $editFavePay)
                //     ->with('display_Pace', $editPacePay)
                //     ->with('display_earns', $earnedPoints)
                //     ->with('display_bpoint', $earnedBPoint)
                //     ->with('display_agent', $agentCode)
                //     ->with('display_delivery_status', $deliveryStatus)
                //     ->with('display_customer_invoice', $customerInvoice);
                
            }
        }
        else
        {
            return Redirect::to('transaction')->with('message', 'No transaction is selected for edit.');
        }
    }

    public static function shopeetransactionMail($maildata)
    {

        $fileName = 'TransactionShopeeImport' . date("Ymdhis") . ".csv";

        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path . '/' . $fileName, 'w');

        fputcsv($file, ['Transaction ID','Shopee Order ID', 'Status', 'Username', 'Message']);

        foreach ($maildata as $row)
        {

            // echo $row['driver_username'];
            fputcsv($file, [$row['transaction_id'],$row['shopee_id'], $row['status'], $row['username'], $row['message']]);
            //}
            
        }

        fclose($file);
        $test = Config::get('constants.ENVIRONMENT');
        $mail = $maildata[0]['email'];
        $subject = "Transaction Import details : " . $fileName;
        $attach = $path . "/" . $fileName;
        $body = array(
            'title' => 'Transaction Shopee Import Details'
        );

        Mail::send('emails.attendance', $body, function ($message) use ($subject, $mail, $attach)
        {
            $message->from('orders@jocom.my', 'Jocom CMS Transaction Shopee Order Import');
            $message->to($mail, '')->subject($subject);
            $message->attach($attach);
        });

        unlink($attach);

    }
    
    

}

