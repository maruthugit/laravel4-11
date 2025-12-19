<?php
use Helper\ImageHelper as Image;
class FlashSaleController extends BaseController{
    /*
     * @Desc    : Get campaign's product
     * @Param   : campaign_id
     * @Method  : POST
     * @Return  : JSON
     */
    public function anyIndex(){
        $products = DB::table('jocom_products')->select('jocom_products.qrcode AS qrcode', 'jocom_products.name AS name')
            ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
            ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
            ->where('jocom_product_price.default', '=', 1)
            ->where('jocom_product_price.status', '=', 1)
            ->where('jocom_products.status', '=', 1)
            ->groupBy('jocom_products.id')
            ->get();

        $list = DB::table('jocom_flashsale AS JF')
            ->leftJoin('jocom_flashsale_products AS JFP', 'JFP.fid','=','JF.id')
            ->where('JF.status', '!=', 0)
            ->select(['JF.id', 'JF.rule_name', 'JF.valid_from', 'JF.valid_to', 'JF.status'])
            ->groupBy('JF.id')->get();

        return View::make('flashsale.flash_sales')->with('products', $products)->with('list', $list);
    }

    public function anyProductsajax()
    {
        $is_edit = Input::get('editpage') ? true : false;
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
        ->where('jocom_products.status', '!=', '2');
        // ->where('jocom_product_price.status', '=', 1)
        // ->where('jocom_product_price.default', '=', '1');


        return Datatables::of($products)
            ->edit_column('status', function($row){
                return ($row->status === 1 ? '<span class="label label-success">Active</span>' : ( $row->status === 2 ? '<span class="label label-danger">Deleted/Archive</span>' : '<span class="label label-warning">Inactive</span>'));
            })
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/flashsale/ajaxprice/{{$id}}' . ($is_edit ? '?editpage=1' : '') . '">Select</a>')->make();
    }

    public function anyAjaxproduct(){
        return View::make('flashsale.ajaxproduct');
    }

    public function anyPricesajax($id) {
        $product_prices = DB::table('jocom_product_price')
            ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price')
            ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
            ->where('jocom_product_price.product_id', '=', $id)
            ->where('jocom_product_price.status', '=', 1);

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
            ->add_column('Action', '<input type="hidden" name="default" value="{{ $default }}"><a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
            ->make();
    }

    public function anyAjaxprice($id)
    {
        $product = DB::table('jocom_products')
            ->select('jocom_products.name', 'jocom_products.sku', 'jocom_products.qrcode', 'jocom_product_price.default')
            ->leftJoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_products.id')
            ->where('jocom_products.id', '=', $id)->first();

        return View::make('flashsale.ajaxprice')->with([
            'id'        => $id,
            'name'      => addslashes($product->name),
            'sku'       => $product->sku,
            'qrcode'    => $product->qrcode,
        ]);
    }

    public function anyStore(){
        $validator    = Validator::make(Input::all(), FlashSale::$rules);
        if (!$validator->passes()) return Redirect::to('/flashsale')->withErrors($validator)->withInput();
        
        $flash              = new FlashSale;
        $flash->rule_name   = Input::get('rule_name');
        $flash->type        = "FlashSale";
        $flash->valid_from  = Input::get('valid_from');
        $flash->valid_to    = Input::get('valid_to');
        $flash->status      = 2;
        $flash->created_at  = date('Y-m-d h:i:s');
        $flash->created_by  = Session::get('username');

        if($flash->save()){
            $label_id    = Input::get('label_id');
            $label       = Input::get('label');
            $price       = Input::get('price');
            $promo_price = Input::get('promo_price');
            $product_id  = Input::get('product_id');
            $limit       = Input::get('limit');
            $min         = Input::get('min');
            $qty         = Input::get('qty');
            $seq         = Input::get('seq');

            if (isset($label_id)) {
                foreach($label_id as $indx => $value) {
                    $flash_product_id = DB::table('jocom_flashsale_products')->insertGetId([
                        'fid'            => $flash->id,
                        'label_id'       => $label_id[$indx],
                        'label'          => $label[$indx],
                        'actual_price'   => $price[$indx],
                        'promo_price'    => $promo_price[$indx],
                        'qty'            => 0,
                        'limit_quantity' => (isset($qty[$indx]) && $qty[$indx] ? $qty[$indx] : 0),
                        'max_qty'        => (isset($limit[$indx]) && $limit[$indx] ? $limit[$indx] : 0),
                        'min_qty'        => (isset($min[$indx]) && $min[$indx] ? $min[$indx] : 0),
                        'product_id'     => $product_id[$indx],
                        'seq'            => $seq[$indx],
                        'activation'     => 1,
                        'created_at'     => date('Y-m-d h:i:s'),
                        'created_by'     => Session::get('username'),
                    ]);

                    DB::table('jocom_flashsale_stock')->insert([
                        'stock'       => (isset($qty[$indx]) && $qty[$indx] ? $qty[$indx] : 0), 
                        'fpid'        => $flash_product_id, 
                        'modify_by'   => Session::get('username'),
                        'modify_date' => date('Y-m-d H:i:s')
                    ]);
                } 
            }

            $this->ClearCache();

            return Redirect::to('/flashsale')->with('message', 'Added successfully.');
        }
    }

    public function anyEdit($id){
        $flash = DB::table('jocom_flashsale AS JF')->where('JF.id', $id)->first();

        $flash_products = DB::table('jocom_flashsale_products AS JFP')
            ->leftJoin('jocom_products as JP','JP.id','=','JFP.product_id')
            ->select('JFP.*', 'JP.name', 'JP.sku')
            ->where('JFP.fid', $id)->where("JFP.activation",1)->orderBy('JFP.seq')->get();

        $products = DB::table('jocom_products')
            ->select(
                'jocom_products.qrcode AS qrcode',
                'jocom_products.name AS name'
            )
            ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
            ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')                   
            ->where('jocom_product_price.default', '=', 1)
            ->where('jocom_product_price.status', '=', 1)
            ->where('jocom_products.status', '=', 1)
            ->groupBy('jocom_products.id')
            ->get();

        $status = ["1" => "Active", "2" => "Inactive"];

        return View::make('flashsale.flash_edit')->with([
            'flash'             => $flash,
            'flash_products'    => $flash_products,
            'products'          => $products,
            'status'            => $status,
        ]);
    }

    public function anyUpdate($id){
        $validator = Validator::make(Input::all(), FlashSale::$rules);
        if (!$validator->passes()) return Redirect::to('/flashsale/edit/' . $id)->withErrors($validator)->withInput();
        
        $flash             = FlashSale::find($id);
        $flash->rule_name  = Input::get('rule_name');
        $flash->type       = "FlashSale";
        $flash->valid_from = Input::get('valid_from');
        $flash->valid_to   = Input::get('valid_to');
        $flash->status     = Input::get('status');
        $flash->updated_by = Session::get('username');
        $flash->updated_at = date('Y-m-d h:i:s');

        //update havent finish
        if($flash->save()){
            $label_id    = Input::get('label_id');
            $label       = Input::get('label');
            $price       = Input::get('price');
            $promo_price = Input::get('promo_price');
            $product_id  = Input::get('product_id');
            $min         = Input::get('min');
            $limit       = Input::get('limit');
            $qty         = Input::get('qty');
            $seq         = Input::get('seq');

            $ProductSeller = FlashSaleProducts::where("fid", $id)->where("activation", 1)->get();
                        
            foreach ($ProductSeller as $key => $value) {
                if(!in_array($value->label_id, $label_id)){
                    $ProductSellerInfo             = FlashSaleProducts::find($value->id);
                    $ProductSellerInfo->activation = 2;
                    $ProductSellerInfo->updated_at = date('Y-m-d h:i:s');
                    $ProductSellerInfo->updated_by = Session::get('username');
                    $ProductSellerInfo->save();
                }
                            
                if(in_array($value->label_id, $label_id)){
                    $arrayKey = array_search($value->label_id, $label_id);
                    unset($label_id[$arrayKey]);

                    DB::table('jocom_flashsale_products')->where('id', '=', $value->id)->update([
                        "promo_price"    => $promo_price[$arrayKey],
                        "seq"            => $seq[$arrayKey],
                        "min_qty"        => (isset($min[$arrayKey]) && $min[$arrayKey] ? $min[$arrayKey] : 0),
                        "max_qty"        => (isset($limit[$arrayKey]) && $limit[$arrayKey] ? $limit[$arrayKey] : 0),
                        "limit_quantity" => (isset($qty[$arrayKey]) && $qty[$arrayKey] ? $qty[$arrayKey] : 0),
                        "activation"     => 1,
                        "updated_at"     => date('Y-m-d h:i:s'),
                        "updated_by"     => Session::get('username'),
                    ]);
                }

                DB::table('jocom_flashsale_stock')->insert([
                    'stock'       => $qty[$arrayKey],
                    'fpid'        => $value->id,
                    'modify_by'   => Session::get('username'),
                    'modify_date' => date('Y-m-d H:i:s')
                ]);
            }
                        
            if(count($label_id) > 0){
                foreach ($label_id as $indx => $value) {
                    $ProductSeller                 = new FlashSaleProducts;
                    $ProductSeller->fid            = $id;
                    $ProductSeller->qty            = 0;
                    $ProductSeller->label_id       = $label_id[$indx];
                    $ProductSeller->label          = $label[$indx];
                    $ProductSeller->actual_price   = $price[$indx];
                    $ProductSeller->promo_price    = $promo_price[$indx];
                    $ProductSeller->min_qty        = (isset($min[$indx]) && $min[$indx] ? $min[$indx] : 0);
                    $ProductSeller->max_qty        = (isset($limit[$indx]) && $limit[$indx] ? $limit[$indx] : 0);
                    $ProductSeller->limit_quantity = (isset($qty[$indx]) && $qty[$indx] ? $qty[$indx] : 0);
                    $ProductSeller->product_id     = $product_id[$indx];
                    $ProductSeller->activation     = 1;
                    $ProductSeller->created_by     = Session::get('username');
                    $ProductSeller->created_at     = date('Y-m-d h:i:s');
                    $ProductSeller->seq            = $seq[$indx];
                    $ProductSeller->save();
                    
                    DB::table('jocom_flashsale_stock')->insert([
                        'stock'       => (isset($qty[$indx]) && $qty[$indx] ? $qty[$indx] : 0),
                        'fpid'        => $ProductSeller->id, 
                        'modify_by'   => Session::get('username'),
                        'modify_date' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $this->ClearCache();

            return Redirect::to('/flashsale')->with('message', '(ID: ' . $id . ') updated successfully.');
        }
    }

    public function anyDelete($id) {
        $flash = FlashSale::find($id);

        $flash->status = '0';
        $flash->updated_by = Session::get('username');
        $flash->updated_at = date('Y-m-d h:i:s');
        $flash->save();
        $this->ClearCache();

        return Redirect::to('/flashsale')->with('message','(ID: '.$id.') deleted successfully.');
    }
    
    
    public function check_flashsales_stock($flash_sale_id,$option_id,$qty){
        $checkout_datetime = date("Y-m-d H:i:s");
        $flash_sale_record = DB::table('jocom_flashsale AS JF')
            ->select('JF.*', 'JFP.label_id','JFP.qty', 'JFP.limit_quantity')
            ->leftJoin('jocom_flashsale_products AS JFP', 'JFP.fid', '=', 'JF.id')
            ->where('JF.id','=',$flash_sale_id)
            ->where('JFP.label_id','=',$option_id)
            ->where('JF.valid_from', '<=', $checkout_datetime)
            ->where('JF.valid_to', '>=',  $checkout_datetime)
            ->where('JF.status','=',1);
            
        if($flash_sale_record->count() >= 1) if(($flash_sale_record->first()->limit_quantity - $flash_sale_record->first()->qty) >= $qty) return true;
        return false;
    }

    public function book_flashsales_stock($transaction_id, $flash_sale_id,$option_id){
    }

    public function deduct_flashsales_stock($transaction_id){
        $flash_sale_record = DB::table('jocom_flashsale_transaction_product AS JF')->where("transaction_id",$transaction_id);
        if($flash_sale_record->count() > 0){
            $flash_sale_record = $flash_sale_record->get();
            
            foreach($flash_sale_record as $key => $value){
                $id = $value->flash_sales_id;
                $FlashSaleProducts = FlashSaleProducts::find($id);
                $FlashSaleProducts->qty = $FlashSaleProducts->qty + $value->quantity;
                $FlashSaleProducts->save();
            }
        }
    }

    public function anyDashboardwh(){
        $admin_username = Session::get('username');            
        $user = DB::table('jocom_sys_admin')->where('username','=',$admin_username)->first();       
        $region = DB::table('jocom_sys_admin_region AS JSR')
            ->select('JSR.*')
            ->where('JSR.sys_admin_id', $user->id)
            ->where('JSR.status', 1)
            ->first();
        $regionName = DB::table('jocom_region')->where('id','=',$region->region_id)->first();       
            
        return View::make('logistic.logistic_dashboardwh', ['region_id'=> $region->region_id, 'region_name'=>$regionName->region]);
    }

    public function ClearCache(){
        // Call jocom.my api update record
        $env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
        $ch = curl_init(Config::get('constants.JOCOM_WEBAPI_BASE_' . $env) . '_api_call.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'action_type' => 'flash_reload',
            'API_KEY' => '55EC9F585111E1FAD6CEDFC4F663F',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        return curl_exec($ch);
    }
}
