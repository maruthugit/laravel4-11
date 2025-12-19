<?php
use Helper\ImageHelper as Image;
class JocomComboDealsController extends BaseController
{
    
    /*
     * @Desc    : Get campaign's product
     * @Param   : campaign_id
     * @Method  : POST
     * @Return  : JSON
     */
        
    public function anyIndex(){

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

            $list = DB::table('jocom_combodeals AS JF')
                    ->leftJoin('jocom_combodeals_products AS JFP', 'JFP.fid','=','JF.id')
                    ->where('JF.status', '!=', 0)
                    ->select(array(
                        'JF.id', 
                        'JF.rule_name',
                        'JF.main_title', 
                        'JF.valid_from',
                        'JF.valid_to', 
                        'JF.status', 
                    ))
                    ->groupBy('JF.id')->get();

        return View::make('jcmcombodeals.jcmcombodeals_sales')->with('products',$products)->with('list',$list);
    }


    public function anyProductsajax()
    {
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/jcmcombodeals/ajaxprice/{{$id}}">Select</a>')
            ->make();
    }

    public function anyAjaxproduct()
    {
        return View::make('jcmcombodeals.ajaxproduct');
    }

    public function anyPricesajax($id)
    {
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
            // ->edit_column('default', '')
            ->add_column('Action', '<input type="hidden" name="default" value="{{ $default }}">
                                    <a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>'
                        )
            ->make();
    }

    public function anyAjaxprice($id)
    {
        $product = DB::table('jocom_products')
            ->select('jocom_products.name', 'jocom_products.sku', 'jocom_products.qrcode', 'jocom_product_price.default')
            ->leftJoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_products.id')
            ->where('jocom_products.id', '=', $id)->first();

        return View::make('jcmcombodeals.ajaxprice')->with([
            'id'        => $id,
            'name'      => addslashes($product->name),
            'sku'       => $product->sku,
            'qrcode'    => $product->qrcode,
        ]);
    }

    public function anyStore()
    {
        $flash         = new JocomComboDeals;
        $validator    = Validator::make(Input::all(), JocomComboDeals::$rules);

        if ($validator->passes()) {
          $flash->main_title   = Input::get('main_title');
          $flash->rule_name   = Input::get('rule_name');
          $flash->type   = "ComboDeals";
          $flash->valid_from  = Input::get('valid_from');
          $flash->valid_to    = Input::get('valid_to');
          $flash->status      = 2;
          $flash->created_at  = date('Y-m-d h:i:s');
          $flash->created_by  = Session::get('username');
          if (Input::hasFile('banner_filename')) {
                $banner = Input::file('banner_filename');
                $destination_path = 'combodeals/banner/';
                $attach1 =  date("Ymdhis")."-".uniqid();
                $filename1 = $attach1. '_' . Input::file('banner_filename')->getClientOriginalName();
                $mim1=Input::file('banner_filename')->getMimeType();
                Input::file('banner_filename')->move($destination_path,$filename1);
              $flash->banner_filename   = $filename1;
              $flash->banner_mime       = $mim1;
            }
              
          if($flash->save()) 
          {
            $label_id = Input::get('label_id');
            $label = Input::get('label');
            $price = Input::get('price');
            $promo_price = Input::get('promo_price');
            $product_id = Input::get('product_id');
            $qty = Input::get('qty');
            if($qty==""){
              $qty="0";  
            }
            $seq = Input::get('seq');
            $limitsqty=Input::get('limit'); //added by boobalan

            if (isset($label_id)) {
                foreach($label_id as $indx => $value) {
                    $lid = $label_id[$indx];
                    $lbl = $label[$indx];
                    $actualprice = $price[$indx];
                    $promoprice = $promo_price[$indx];
                    $productqty = $qty[$indx];
                    if($productqty==""){
                    $productqty="0";
                    }
                    $productid = $product_id[$indx];
                    $productseq = $seq[$indx];
                    $id = $flash->id;
                    $limitqty=$limitsqty[$indx];

                    JocomComboDealsProducts::scopeStoreProducts($lid,$lbl,$actualprice,$promoprice,$productqty,$limitqty,$productid,$productseq,$id);     
                } 
            }

            return Redirect::to('/jcmcombodeals')->with('message', 'Added successfully.');
          }

        }else{    
            return Redirect::to('/jcmcombodeals')->withErrors($validator)->withInput();
        }
    }

    public function anyEdit($id)
    {
        $flash  =  DB::table('jocom_combodeals AS JF')
                ->where('JF.id', $id)->first();

        $flash_products = DB::table('jocom_combodeals_products AS JFP')
                    ->leftJoin('jocom_products as JP','JP.id','=','JFP.product_id')
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

        $status = array("1"=>"Active","2"=>"Inactive");

        return View::make('jcmcombodeals.jcmcombodeals_edit')->with(array(
            'flash'          => $flash,
            'flash_products' => $flash_products,
            'products'       => $products,
            'status'       => $status,
        ));
    }

    public function anyUpdate($id){

        $flash = JocomComboDeals::find($id);
    
        $validator    = Validator::make(Input::all(), JocomComboDeals::$rules);

        if ($validator->passes()) {
            $flash->main_title = Input::get('main_title');
            $flash->rule_name = Input::get('rule_name');
            $flash->type   = "ComboDeals";
            $flash->valid_from = Input::get('valid_from');
            $flash->valid_to = Input::get('valid_to');
            $flash->status = Input::get('status');
            $flash->updated_by = Session::get('username');
            $flash->updated_at = date('Y-m-d h:i:s');
            if (Input::hasFile('banner_filename')) {
                $banner = Input::file('banner_filename');
                $destination_path = 'combodeals/banner/';
                $attach1 =  date("Ymdhis")."-".uniqid();
                $filename1 = $attach1. '_' . Input::file('banner_filename')->getClientOriginalName();
                $mim1=Input::file('banner_filename')->getMimeType();
                Input::file('banner_filename')->move($destination_path,$filename1);
              $flash->banner_filename   = $filename1;
              $flash->banner_mime       = $mim1;
            }

            //update havent finish
            if($flash->save()) 
            {
                $label_id = Input::get('label_id');
                $label = Input::get('label');
                $price = Input::get('price');
                $promo_price = Input::get('promo_price');
                $product_id = Input::get('product_id');
                $qty = Input::get('qty');
                if($qty==""){
                    $qty="0";
                    }
                $seq = Input::get('seq');
                $limitsqty = Input::get('limit'); // added  by boobalan

                $ProductSeller = JocomComboDealsProducts::where("fid",$id)
                                    ->where("activation",1)->get();
                            
                foreach ($ProductSeller as $key => $value) {

                    if(!in_array($value->label_id, $label_id)){

                        $ProductSellerInfo = JocomComboDealsProducts::find($value->id);
                        $ProductSellerInfo->activation = 2;
                        $ProductSellerInfo->updated_at = date('Y-m-d h:i:s');
                        $ProductSellerInfo->updated_by = Session::get('username');
                        $ProductSellerInfo->save();
                    }
                                
                    if(in_array($value->label_id, $label_id)){

                        $arrayKey = array_search($value->label_id, $label_id);
                        unset($label_id[$arrayKey]);

                        $lid = $label_id[$arrayKey];
                        $promoprice = $promo_price[$arrayKey];
                        $productqty = $qty[$arrayKey];
                        if($productqty==""){
                    $productqty="0";
                    }
                        $productseq = $seq[$arrayKey];
                        $id2 = $product_id[$arrayKey];
                        $limits=$limitsqty[$arrayKey]; //added by boobalan

                        DB::table('jocom_combodeals_products')
                            ->where('id','=',$value->id)
                            ->update(array(
                                "promo_price"=> $promoprice,
                                "seq"=> $productseq,
                                "qty"=> $productqty, //added by boobalan
                                "limit_quantity"=>$limits, //added by boobalan
                                "activation"=> 1,
                                "updated_at"=> date('Y-m-d h:i:s'),
                                "updated_by"=> Session::get('username'),
                            ));
                        }
                        
                        DB::table('jocom_combodeals_stock')
                        ->insert(array(
                            'stock'=>$productqty, 
                            'fpid'=>$value->id,
                            'modify_by'=>Session::get('username'),
                            'modify_date'=>date('Y-m-d H:i:s')
                        ));
                }
                            
                if(count($label_id)> 0){

                    foreach ($label_id as $indx => $value) {

                        $lid = $label_id[$indx];
                        $lbl = $label[$indx];
                        $promoprice = $promo_price[$indx];
                        $actualprice = $price[$indx];
                        $productqty = $qty[$indx];
                        if($productqty==""){
                    $productqty="0";
                    }
                        $limitqty = $limitsqty[$indx];// added by boobalan
                        $id2 = $product_id[$indx];
                        $sequence=$seq[$indx];//added by boobalan

                        $ProductSeller = new JocomComboDealsProducts;
                        $ProductSeller->fid = $id;
                        $ProductSeller->label_id = $lid;
                        $ProductSeller->label = $lbl;
                        $ProductSeller->actual_price = $actualprice;
                        $ProductSeller->promo_price = $promoprice;
                        $ProductSeller->limit_quantity = $limitqty; //added by boobalan
                        $ProductSeller->qty=$productqty; //added by boobalan
                        $ProductSeller->seq=$sequence;  //added by boobalan
                        $ProductSeller->product_id = $id2;
                        $ProductSeller->activation = 1;
                        $ProductSeller->created_by = Session::get('username');
                        $ProductSeller->created_at = date('Y-m-d h:i:s');
                        $ProductSeller->save();
                        
                        DB::table('jocom_combodeals_stock')
                            ->insert(array(
                                'stock'=>$productqty, 
                                'fpid'=>$ProductSeller->id, 
                                'modify_by'=>Session::get('username'),
                                'modify_date'=>date('Y-m-d H:i:s')
                            ));
                                
                    }   
                }  
                return Redirect::to('/jcmcombodeals')->with('message', '(ID: '.$id.') updated successfully.');
            }
        }else{    
            return Redirect::to('/jcmcombodeals/edit/'.$id)->withErrors($validator)->withInput();
        }


    }

    public function anyDelete($id)
    {
        $flash = JocomComboDeals::find($id);

        $flash->status = '0';
        $flash->updated_by = Session::get('username');
        $flash->updated_at = date('Y-m-d h:i:s');
        $flash->save();
        return Redirect::to('/jcmcombodeals')->with('message','(ID: '.$id.') deleted successfully.');
    }
    
    
    public function check_flashsales_stock($flash_sale_id,$option_id,$qty){
        $checkout_datetime = date("Y-m-d H:i:s");
        $flash_sale_record = DB::table('jocom_combodeals AS JF')
            ->select('JF.*', 'JFP.label_id','JFP.qty', 'JFP.limit_quantity')
            ->leftJoin('jocom_combodeals_products AS JFP', 'JFP.fid', '=', 'JF.id')
            ->where('JF.id','=',$flash_sale_id)
            ->where('JFP.label_id','=',$option_id)
            ->where('JF.valid_from', '<=', $checkout_datetime)
            ->where('JF.valid_to', '>=',  $checkout_datetime);
        if(Input::get('devicetype') !== "web") $flash_sale_record = $flash_sale_record->where('JF.status', '=', 1);

        if($flash_sale_record->count() >= 1){
            if(($flash_sale_record->first()->limit_quantity - $flash_sale_record->first()->qty) >= $qty) return true;
        }
        return false;
    }

    public function book_flashsales_stock($transaction_id, $flash_sale_id,$option_id){
    }

    public function deduct_flashsales_stock($transaction_id){
        
        $flash_sale_record = DB::table('jocom_combodeals_transaction_product AS JF')->where("transaction_id",$transaction_id);
        
        if($flash_sale_record->count() > 0){
            
            $flash_sale_record = $flash_sale_record->get();
            
            foreach($flash_sale_record as $key => $value){
                $id = $value->flash_sales_id;
                $SaleProducts = JocomComboDealsProducts::find($id);
                $SaleProducts->qty = $SaleProducts->qty + $value->quantity;
                $SaleProducts->save();
                
            }
            
        }
        

    }
    
}
