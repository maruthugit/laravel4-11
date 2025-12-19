<?php

class ProductController extends BaseController {

	protected $product;
	protected $zone;
	protected $category;
	protected $seller;
	protected $price;
	protected $delivery;

	public function __construct(Product $product, Zone $zone, Category $category, Seller $seller, Price $price, Delivery $delivery) {
    
        $this->beforeFilter('auth', array('except' => array('Productdashboard')));
        
		$this->product = $product;
		$this->zone = $zone;
		$this->category = $category;
		$this->seller = $seller; 
		$this->price = $price;
		$this->delivery = $delivery;

	}

	/**
     * Display the product page.
     *
     * @return Page
     */
    public function anyIndex()
	{
		$sellers = [];

		foreach (Seller::alphabeticalOrder()->get() as $seller) {
			$sellers["s{$seller->id}"] = $seller->company_name;
		}

        return View::make('product.index', ['sellers' => $sellers]);
    }
    
    public function anyProductdashboard(){

    	$product_qr = array(
                'JC10481' , 
                'JC10482',
                'JC10483',
                'JC10486',
                'JC10488',
                'JC10489',
                'JC10490',
                'JC10491',
                'JC8242',
                'JC7301',
                'JC8243',
                'JC4066',
                'JC8244',
                'JC8245',
                'JC4067',
                'JC8246',
                'JC8248',
                'JC8249',
                'JC8263',
                'JC8264',
                'JC8265',
                'JC8266',
                'JC7299',
                'JC8268',
                'JC6568',
                'JC3243',
                'JC3244',
                'JC8269',
                'JC8270',
                'JC8272',
                'JC8273',
                'JC8271',
                'JC10182',
                'JC10496',
                'JC10498',
                'JC10524',
                'JC8744',
                'JC8550',
                'JC8454',
                'JC8302',
                'JC8299',
                'JC8303',
                'JC8301',
                'JC8300',
                'JC8455',
                'JC8106',
                'JC10522',
                'JC10568',
                'JC10569',
                'JC10570',
                'JC10571',
                'JC10572',
                'JC10573',
                'JC10574',
                'JC10575',
                'JC10576',
                'JC10577',
                'JC10592',
                'JC10593',
                'JC10594',
                'JC10597',
                'JC10598',
                'JC10599',
                'JC10600',
                'JC10601',
                'JC10602',
                'JC10579',
                'JC10584',
                'JC10586',
                'JC10588',
                'JC10589',
                'JC10590',
                'JC10591',
                'JC10596',
                'JC10578',
                'JC10580',
                'JC10582',
                'JC10583',
                'JC10585',
                'JC10587',
                'JC10581',
                'JC10594',
                'JC5489',
                'JC10595',
                'JC5488',
                'JC10341',
                'JC10342'
            );

    	$collection = array();
    	$collection3 = array();

    	foreach ($product_qr as $key => $value) {

    		$qr_sku = DB::table('jocom_products')->where('qrcode',$value)->first();

    // 		if (!empty($qr_sku)) {
    			$sku[] = $qr_sku->sku;
    // 		}else{
    // 			$sku[] = '';
    // 		}
    		
    	}
    		$qr_sku2 = DB::table('jocom_products')->whereIn('qrcode',$product_qr)->get();
    		$start = '2017-12-11 00:00:00';
    		$end = '2018-1-14 23:59:59';
    	$total_soldAll = DB::table('jocom_transaction_details AS JTD')
                        ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                        ->leftJoin('jocom_products AS JP','JP.sku','=','JTD.sku')
                        // ->leftJoin('jocom_product_price AS JPP','JP.id','=','JPP.product_id')
                        ->whereIn('JP.qrcode',$product_qr)
                        ->where('JT.status','=','completed')
                        ->where('JT.transaction_date','>=',$start)
                		->where('JT.transaction_date','<=',$end)
                        ->select(DB::raw("SUM(JTD.unit) as total, JTD.sku, JP.name, JTD.price_label, JP.qrcode"))
                        ->first();
                        
                        
 	//echo "<pre>";
    // 	if (!empty($sku)) {

    		foreach ($qr_sku2 as $key => $value2) {
        
    		$start = '2017-12-11 00:00:00';
    		$end = '2018-1-14 23:59:59';

    		$total_sold = DB::table('jocom_transaction_details AS JTD')
                        ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                        ->leftJoin('jocom_products AS JP','JP.sku','=','JTD.sku')
                        // ->leftJoin('jocom_product_price AS JPP','JP.id','=','JPP.product_id')
                        ->where('JTD.sku','=',$value2->sku)
                        ->where('JT.status','=','completed')
                        ->where('JT.transaction_date','>=',$start)
                		->where('JT.transaction_date','<=',$end)
                        ->select(DB::raw("SUM(JTD.unit) as total, JTD.sku, JP.name, JTD.price_label, JP.qrcode"))
                        ->first();
                        
           // print_r($total_sold);            
                        
            $sub = array(
                    "sku"=>$value2->sku,
                    "name"=>$value2->name,
                    "total"=>$total_sold->total > 0 ? $total_sold->total : 0,
                );
                
                // print_r($sub);
            array_push($collection, $sub);  
            

	    	}
	 //  print_r($collection);
      // echo "</pre>";
	    	foreach ($sku as $key => $value3) {
	  
	    		$start = Date('Y-m-d',strtotime("-7 days"))." 00:00:00";
	            $end   = Date('Y-m-d')." 23:59:59";

	    		$total_sold3 = DB::table('jocom_transaction_details AS JTD')
	                        ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
	                        ->leftJoin('jocom_products AS JP','JP.sku','=','JTD.sku')
	                        // ->leftJoin('jocom_product_price AS JPP','JP.id','=','JPP.product_id')
	                        ->where('JTD.sku','=',$value3)
	                        ->where('JT.status','=','completed')
	                        ->where('JT.transaction_date','>=',$start)
	                		->where('JT.transaction_date','<=',$end)
	                        ->select(DB::raw("SUM(JTD.unit) as total, JTD.sku, JP.name, JTD.price_label, JT.transaction_date, JP.qrcode"))
	                        ->get();

	            array_push($collection3, $total_sold3);  
	    	}
	    

			foreach ($collection as $key => $row) {
			    
			    $list[$key]  = $row['total'];
			}

			array_multisort($list, SORT_ASC, $collection);
			rsort($collection);

			foreach ($collection3 as $key => $row3) {
			    $list3[$key]  = $row3['total'];
			}

			array_multisort($list3, SORT_DESC, $collection3);
			rsort($collection3);

			$output3 = array_slice($collection3, 0, 5);
			asort($output3);

    // 	}
    // 	print_r($output3);die();
    //$total_soldAll
		return View::make('product.product_dashboard')->with('collection', $collection)->with('collection3', $output3)->with('totalAll', $total_soldAll->total);
   
    }
    
    public function anyMasterinventory()
	{
		$sellers = [];

		foreach (Seller::alphabeticalOrder()->get() as $seller) {
			$sellers["s{$seller->id}"] = $seller->company_name;
		}

        return View::make('product.master_inventory', ['sellers' => $sellers]);
    }

    public function anyProductcampaign()
    {
        $sellers = [];
                $products = CampaignProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_campaign', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductlivestream()
    {
        $sellers = [];
                $products = CampaignLiveProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_livestreaming', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductboostdeals()
    {
        $sellers = [];
        $statusres = "";
                $products = CampaignBoostProducts::getCampaignProduct(1);

                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_boost', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductboost11deals()
    {
        $sellers = [];
        $statusres = "";
                $products = CampaignBoost11Products::getCampaignProduct(1);

                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_boost11', ['sellers' => $sellers,'products'=>$products]);
    }

    public function anyProductjocomelevendeals()
    {
        $sellers = [];
        $statusres = "";
                $products = CampaignJocom11Products::getCampaignProduct(1);

                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_jocom11', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductjocomfeatured()
    {
        $sellers = [];
        $statusres = "";
                $products = JocomFeaturedProducts::getCampaignProduct(1);

                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_jocomfeatured', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductofficepantry()
    {
        $sellers = [];
                $products = OfficePantryProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_officepantry', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyProductcrossborder()
    {
        $sellers = [];
                $products = CrossborderProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_crossborder', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyBoostonlinestore()
    {
        $sellers = [];
                $products = BoostOnlinestoreProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_boostonlinestore', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyEcommunity()
    {
        $sellers = [];
                $products = ECommunityProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_ecommunity', ['sellers' => $sellers,'products'=>$products]);
    }
    
    public function anyMycashonline()
    {
        $sellers = [];
                $products = MyCashProducts::getCampaignProduct(1);
                
        foreach (Seller::alphabeticalOrder()->get() as $seller) {
                    $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_mycash', ['sellers' => $sellers,'products'=>$products]);
    }
    
	/**
	 * Retrieve list of product in Datatables format
	 * @return Datatables
	 */
	public function getProducts()
	{
		if (Input::get('name')) {
			$input['name'] = Input::get('name');
		}

		if (Input::get('seller')) {
			$input['seller'] = Input::get('seller');
		}

		if (Input::get('category')) {
			$input['category'] = Input::get('category');
		}

		if (Input::get('status')) {
			$input['status'] = Input::get('status');
		}
                
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
                $farid = $sysAdminInfo->id; 
		if ( ! empty($input)) {
			$products = DB::table('jocom_products')
				->select(
					'jocom_products.id AS id',
					'jocom_products.sku AS sku',
					'jocom_seller.company_name AS company_name',
					'jocom_products.name AS name',
					'jocom_product_price.price AS price',
					'jocom_product_price.price_promo AS price_promo',
					DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"),
					'jocom_products.status AS status',
					'jocom_products.weight AS weight',
					'jocom_products.img_1 AS img_1'
				)
				->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
				->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
				->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
				->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                                
                                
				->where('jocom_product_price.default', '=', 1)
				->where('jocom_product_price.status', '=', 1)
				->where('jocom_products.status', '<>', 2)
				->groupBy('jocom_products.id');

			if ( ! empty($input['name'])) {
                            $products = $products->where('jocom_products.name', 'LIKE', "%{$input['name']}%");
			}
                        
                        
                        if($SysAdminRegion->region_id != 0){
                            if($farid == 104){
                                $products = $products->whereIn('region_id',[0,3]);
                                $products = $products->whereNotIn('jocom_products_category.id',[659,649,704,609,702,650,805,778,891,370,893]);
                            }
                             elseif($farid == 105){
                                 $products = $products->whereIn('region_id',[5]);
                             }
                            else
                            {
                            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
                            }
                        }

			if ( ! empty($input['seller']) && $input['seller'] != 'any') {
				$products = $products->where('jocom_products.sell_id', '=', substr($input['seller'], 1));
			}

			if ( ! empty($input['category'])) {
				$products = $products->where('category_name', 'LIKE', "%{$input['category']}%");
			}
			
			if ( ! empty($input['status']) && $input['status'] != 'any') {
				switch ($input['status']) {
					case 'active':
						$products = $products->where('jocom_products.status', '=', 1);
						break;
					case 'inactive':
						$products = $products->where('jocom_products.status', '=', 0);
						break;
				}
			}
		} else {
//			$products = DB::table('datatable_products')->select('id', 'sku', 'company_name', 'name', 'price', 'category_name', 'status', 'weight', 'img_1');
//                        
                        
                        $products = DB::table('jocom_products')
				->select(
					'jocom_products.id AS id',
					'jocom_products.sku AS sku',
					'jocom_seller.company_name AS company_name',
					'jocom_products.name AS name',
					'jocom_product_price.price AS price',
					'jocom_product_price.price_promo AS price_promo',
					DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"),
					'jocom_products.status AS status',
					'jocom_products.weight AS weight',
					'jocom_products.img_1 AS img_1'
				)
				->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
				->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
				->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
				->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                                
                                
				->where('jocom_product_price.default', '=', 1)
				->where('jocom_product_price.status', '=', 1)
				->where('jocom_products.status', '<>', 2)
				->groupBy('jocom_products.id');
                        
                if($SysAdminRegion->region_id != 0){
                    if($SysAdminRegion->region_id == 4){
                        $products = $products->where('jocom_products.category', 'like', '%' . 949 . '%');
                    }else{
                        if($farid == 104){
                               $products = $products->whereIn('region_id',[0,3]);  
                              $products = $products->whereNotIn('jocom_products_category.id',[659,649,704,609,702,650,805,778,891,370,893]);
                             }
                             elseif($farid == 105){
                                 $products = $products->whereIn('region_id',[5]);
                             }
                             else{
                        $products = $products->where('region_id', $SysAdminRegion->region_id);
                             }
                    }
                }
                        
		}
        
		return Datatables::of($products)
			->edit_column('price', '{{ number_format($price, 2) }}')
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
			->add_column('Action', function($row)
			{
				if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
				{
					$edit .= '<a class="btn btn-primary btn-sm" data-toggle="tooltip" href="'.url('product/upload/'.$row->id).'"><i class="fa fa-file-image-o"></i></a> ';
					$edit .= '<a class="btn btn-primary btn-sm" data-toggle="tooltip" href="'.url('product/edit/'.$row->id).'"><i class="fa fa-pencil"></i></a> ';
				}

				if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 9, 'AND'))
				{
					$edit .= '<a id="deleteItem" class="btn btn-danger btn-sm" data-toggle="tooltip" data-value="{{ $id }}" href="'.url('product/delete/'.$row->id).'"><i class="fa fa-remove"></i></a>';
				}

				return $edit;
			})
			->add_column('img_1', function($row)
			{
				if($row->img_1 != ""){
					return '<img class="img img-thumbnail"  src="/images/data/'.$row->img_1.'" >';
				}
				else{
					return '<img class="img img-thumbnail"  src="/images/data/thumbs/noimage.png" >';
				}
			})
			->make();
	}
        
    public function anyProductinline()
	{
		$sellers = [];

		foreach (Seller::alphabeticalOrder()->get() as $seller) {
			$sellers["s{$seller->id}"] = $seller->company_name;
		}

        return View::make('product.product_editable', ['sellers' => $sellers]);
    }
    
    public function getProducts2()
	{
        try{

            if (Input::get('name')) {
                $input['name'] = Input::get('name');
            }
    
            if (Input::get('seller')) {
                $input['seller'] = Input::get('seller');
            }
    
            if (Input::get('category')) {
                $input['category'] = Input::get('category');
            }
    
            if (Input::get('status')) {
                $input['status'] = Input::get('status');
            }

            $products = DB::table('jocom_product_price AS JPP')
                ->select(
                    'JP.id AS id',
                    'JP.sku AS sku',
                    'JP.status AS status',
                    'JP.qrcode AS qrcode',
                    'JP.name AS name',
                    'JP.img_1 AS img_1',
                    'JPP.id AS PriceID',  
                    'JPP.label AS label',    
                    'JPP.price AS price',
                    // DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"),

                    'JPP.price_promo AS price_promo',
                    'JPP.qty AS quantity',
                    'JPP.stock AS stock',
                    'JPP.stock_unit AS stock_unit',
                    'JPP.p_referral_fees AS p_referral_fees',
                    'JPP.p_referral_fees_type AS p_referral_fees_type',
                    'JPP.p_weight AS weight',
                    'SP.cost_price'
                )
                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPP.product_id')
                ->leftJoin('jocom_seller', 'JP.sell_id', '=', 'jocom_seller.id')
                // ->leftJoin('jocom_categories', 'JP.id', '=', 'jocom_categories.product_id')
                // ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
//                        ->where('JPP.default', '=', 1)
                ->join('jocom_product_price_seller AS SP', 'SP.product_price_id', '=', 'JPP.id')
                ->where('JPP.status', '=', 1)
                ->where('JP.status', '<>', 2);
                    //->groupBy('JP.id');

            if ( ! empty($input['name'])) {
                $products = $products->where('JP.name', 'LIKE', "%{$input['name']}%");
            }

            if ( ! empty($input['seller']) && $input['seller'] != 'any') {
				$products = $products->where('JP.sell_id', '=', substr($input['seller'], 1));
			}

// 			if ( ! empty($input['category'])) {
// 				$products = $products->where('category_name', 'LIKE', "%{$input['category']}%");
// 			}
			
			if ( ! empty($input['status']) && $input['status'] != 'any') {
				switch ($input['status']) {
					case 'active':
						$products = $products->where('JP.status', '=', 1);
						break;
					case 'inactive':
						$products = $products->where('JP.status', '=', 0);
						break;
				}
			}
			
			$products = $products->orderBy('JP.id','DESC');

            return Datatables::of($products)
                         ->add_column('img_1', function($row)
                            {
                                if($row->img_1 != ""){
                                    return '<img class="img img-thumbnail"  src="/images/data/'.$row->img_1.'" >';
                                }
                                else{
                                    return '<img class="img img-thumbnail"  src="/images/data/thumbs/noimage.png" >';
                                }
                            })
                        ->make(true);

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    public function anyUpdatepriceoption(){

        $isError = 0;
        $message = '';
         
        try{

            DB::beginTransaction();

            $price_option_id = Input::get('price_option_id');
            $price_option_type = Input::get('price_option_type');

            if(!empty($price_option_id)){
                
                
                $Price = Price::find($price_option_id);
                
                
                $price_amount = Input::get('price_amount');
                if($Price && $price_option_type == 1){
                    $Price->price = $price_amount;
                    $Price->save();
                }

                if($Price && $price_option_type == 2){
                    $price_amount = Input::get('price_amount');
                    $Price->price_promo = $price_amount;
                    $Price->save();
                }

                if($Price && $price_option_type == 'QTY'){
                    $value = Input::get('value');
                    $Price->qty = $value;
                    $Price->save();
                }

                if($Price && $price_option_type == 'PRM'){
                    $Price->price_promo = $price_amount;
                    $Price->save();
                }

                if($Price && $price_option_type == 'STK'){
                    
                    $value = Input::get('value');
                    $Price->stock = $value;
                    $Price->save();
                }

                if($Price && $price_option_type == 'LBL'){
                    
                    $value = Input::get('value');
                    $Price->label = $value;
                    $Price->save();
                }

                if($Price && $price_option_type == 'WGT'){
                    
                    $value = Input::get('value');
                    $Price->p_weight = $value;
                    $Price->save();
                }
            }
            
            $Product = Product::find($Price->product_id);

            $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                                ->select('product_base_id')->get();

            $base_ids = array();
            foreach ($bases as $base) {
                array_push($base_ids, $base->product_base_id);
            }
            $base_id = implode(',', $base_ids);
            
            $producthistory  = new ProductsHistory;
            $producthistory->type       = "Update Product";
            $producthistory->product_id = $Price->product_id;
            $producthistory->sku = 'TM-'.str_pad($Price->product_id, 7, '0', STR_PAD_LEFT);
            $producthistory->name = $Product->name;
            $producthistory->prd_status = $Product->status;
            $producthistory->price_id = $Price->id;
            $producthistory->label = $Price->label;
            $producthistory->price = $Price->price;
            $producthistory->price_promo = $Price->price_promo;
            $producthistory->qty = $Price->qty;
            $producthistory->stock = $Price->stock;
            $producthistory->stock_unit = $Price->stock_unit;
            $producthistory->p_referral_fees = $Price->p_referral_fees; 
            $producthistory->p_referral_fees_type = $Price->p_referral_fees_type;
            $producthistory->default = $Price->default;
            $producthistory->pri_product_id = $Price->product_id;
            $producthistory->pri_status = $Price->status;
            $producthistory->p_weight = $Price->p_weight;

            $seller = ProductPriceSeller::where('product_price_id', '=', $Price->id)
                        ->where('activation', '=', 1)
                        ->first();

            $producthistory->updated_by = Session::get('username');
            $producthistory->seller_id = $seller->seller_id;
            $producthistory->cost = $seller->cost_price;
            $producthistory->base_id = $base_id;
            $producthistory->gst_status = $Product->gst;

            $producthistory->delivery_time = $Product->delivery_time;

            $deliverys = Delivery::where('product_id', '=', $Price->product_id)->get();
            $delivery_fees = array();
            foreach ($deliverys as $delivery)
            {
                array_push($delivery_fees, Zone::find($delivery->zone_id)->name.':'.$delivery->price);
            }
            $delivery_fee = implode(',', $delivery_fees);
            $producthistory->delivery_fee = $delivery_fee;
            $producthistory->save();
            
            foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
            

        }catch(exception $ex){
            $isError = 1;
            $message = $ex->getMessage();
        }finally{

            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array("status"=>$isError,"message"=>$message);
        }
    }
    
    public function anyUpdatesellercost() {
        $price_option_id = Input::get('price_option_id');

        $sellerPrice = ProductPriceSeller::where('product_price_id', '=', $price_option_id)
                            ->first();

        $sellerPrice->cost_price = Input::get('cost_price');
        $sellerPrice->save();

        $Price = Price::find($price_option_id);
        
        $Product = Product::find($Price->product_id);

        $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                            ->select('product_base_id')->get();

        $base_ids = array();
        foreach ($bases as $base) {
            array_push($base_ids, $base->product_base_id);
        }
        $base_id = implode(',', $base_ids);
        
        $producthistory  = new ProductsHistory;
        $producthistory->type       = "Update Product";
        $producthistory->product_id = $Price->product_id;
        $producthistory->sku = 'TM-'.str_pad($Price->product_id, 7, '0', STR_PAD_LEFT);
        $producthistory->name = $Product->name;
        $producthistory->prd_status = $Product->status;
        $producthistory->price_id = $Price->id;
        $producthistory->label = $Price->label;
        $producthistory->price = $Price->price;
        $producthistory->price_promo = $Price->price_promo;
        $producthistory->qty = $Price->qty;
        $producthistory->stock = $Price->stock;
        $producthistory->stock_unit = $Price->stock_unit;
        $producthistory->p_referral_fees = $Price->p_referral_fees; 
        $producthistory->p_referral_fees_type = $Price->p_referral_fees_type;
        $producthistory->default = $Price->default;
        $producthistory->pri_product_id = $Price->product_id;
        $producthistory->pri_status = $Price->status;
        $producthistory->p_weight = $Price->p_weight;

        $seller = ProductPriceSeller::where('product_price_id', '=', $Price->id)
                    ->where('activation', '=', 1)
                    ->first();

        $producthistory->updated_by = Session::get('username');
        $producthistory->seller_id = $seller->seller_id;
        $producthistory->cost = Input::get('cost_price');
        $producthistory->base_id = $base_id;
        $producthistory->gst_status = $Product->gst;

        $producthistory->delivery_time = $Product->delivery_time;

        $deliverys = Delivery::where('product_id', '=', $Price->product_id)->get();
        $delivery_fees = array();
        foreach ($deliverys as $delivery)
        {
            array_push($delivery_fees, Zone::find($delivery->zone_id)->name.':'.$delivery->price);
        }
        $delivery_fee = implode(',', $delivery_fees);
        $producthistory->delivery_fee = $delivery_fee;
        $producthistory->save();
        foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
        
    }


	/**
     * Display a listing of the products resource.
     *
     * @return Response
     */
	public function anyProductsajax() {
		$products = $this->product->select(array(
                            'jocom_products.id',
                            'jocom_products.sku',
                            'jocom_seller.company_name',
                            'jocom_products.name',
                            'jocom_products_category.category_name',
                            // 'jocom_product_price.price',
                            // 'jocom_product_price.price_promo'
                    ))
                    // ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                    ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
                    ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
                    ->where('jocom_products.status', '!=', '2');
                    
                    $sysAdminInfo = User::where("username",Session::get('username'))->first();
                    $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
                    if($SysAdminRegion->region_id != 0){
                            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
                    }
                    $products = $products->where('region_id', '=', 2);
                    // ->where('jocom_product_price.status', '=', 1)
                    // ->where('jocom_product_price.default', '=', '1');
                    return Datatables::of($products)
                    // ->edit_column('price', '{{number_format($price, 2)}}')
                    // ->edit_column('price_promo', '{{number_format($price_promo, 2)}}')
                    ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/product/ajaxprice/{{$id}}">Select</a>')
                    ->make();
	}

	/**
     * Display the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyAjaxproduct() {
        return View::make('product.ajaxproduct');
    }

    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */
	public function anyPricesajax($id) {

		$product_prices = DB::table('jocom_product_price')
								->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo')
								->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
								->where('jocom_product_price.product_id', '=', $id)
								->where('jocom_product_price.status', '=', 1);

		return Datatables::of($product_prices)
								->edit_column('price', '{{number_format($price, 2)}}')
								->edit_column('price_promo', '{{number_format($price_promo, 2)}}')
								->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
								->make();
	}

	/**
     * Display the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyAjaxprice($id) {
        $product    = DB::table('jocom_products')
                            ->select('name', 'sku')
                            ->where('id', '=', $id)->first();

        //var_dump($product);

        return View::make('product.ajaxprice')->with(['id' => $id, 'name' => $product->name, 'sku' => $product->sku]);
    }

	/**
     * Display create product form
     * @return Response
     */
	public function getCreate()
	{
		
		$isFixedOption = false;

		$parentCategory[] = [
			'id' => 0,
			'category_name' => 'Parent',
			'category_name_cn' => 'Parent',
			'category_name_my' => 'Parent',
			'status' => 1,
			'permission' => 0,
			'weight' => 10
		];
		$categoriesOptions = array_merge($parentCategory, $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- '));
		$zoneOptions = $this->zone->all();
                
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                // $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                //             ->where("status",1)->first();
                            
                $SysAdminRegion = DB::table('jocom_sys_admin_region AS JSAR' )
      			->leftJoin('jocom_region AS JR', 'JR.id', '=', 'JSAR.region_id')
      			->select('JSAR.*','JR.*')
      			->where("JSAR.status",1)
                        ->where("JSAR.sys_admin_id",$sysAdminInfo->id)
                        ->first();
                
                if($SysAdminRegion->region_id != 0){
                    
                    if($SysAdminRegion->region_id == 4){
                        $zoneOptions = $this->zone->where("id",42)->get();
                        /*  FOR CHINA ADMIN */
                        $regions = DB::table('jocom_region')->where("id",1)->get();
                        $countries = Country::where("id",458)->get();
                        $isFixedOption = true;
                        $sellers = $this->seller->sortByCompany();
                        /*  FOR CHINA ADMIN */
                    }
                    elseif($SysAdminRegion->region_id == 5){
                        $zoneOptions = $this->zone->where("id",42)->get();
                        /*  FOR CHINA ADMIN */
                        $regions = DB::table('jocom_region')->where("id",5)->get();
                        $countries = Country::where("id",36)->get();
                        // $isFixedOption = true;
                        $sellers = $this->seller->sortByCompany();
                        /*  FOR CHINA ADMIN */
                    }
                    else{
                        $zoneOptions = $this->zone->where("country_id",$SysAdminRegion->country_id)->get();
                        $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
                        $countries = Country::where("id",$SysAdminRegion->country_id)->get();
		                $sellers = $this->seller->sortByCompanyRegion();
                    }
                    
                }else{
                    $zoneOptions = $this->zone->get();
                    $regions = Region::where("country_id",$country_id)
                        ->where("activation",1)->get();
                    $countries = Country::getActiveCountry();
                    $sellers = $this->seller->sortByCompany();
                }
                
//                $countries = Country::getActiveCountry();
//                $regions      =  Region::where("country_id",$countries[0]->id)->get();

        foreach ($sellers as $seller)
		{   $status="";
			switch ($seller->active_status)
			{   
				case 0:
					$status = ' **[Inactive]';
					break;
				case 2:
					$status = ' **[Deleted]';
					break;
			}

			$sellersOptions[$seller->id] = $seller->company_name.$status;
		}
		
		$GSVendor = GSVendor::where("activation",1)->get();
        $vendorOptions = array();
		foreach ($GSVendor as $vendor)
		{
			$vendorOptions[$vendor->id] = $vendor->seller_name;
		}
                
                
		if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
		{
			return View::make('product.create', [
				'sellersOptions'			=> $sellersOptions,
				'vendorOptions' => $vendorOptions,
				'categoriesOptions'			=> $categoriesOptions,
				'gst'						=> Fees::get_gst(),
				'zoneOptions'				=> $zoneOptions,
                                'regions' => $regions,
                        'countries' => $countries,
                        'isFixedOption' => $isFixedOption
			]);
		}
		else
		{
			return View::make('home.denied', ['module' => 'Products > Add Product']);
		}
	}

	public function postStore()
	{
	    try{
	        
		$count		= 0;
		$validator	= $this->getValidator($count);
              
		if ($validator->fails())
		{
			$errors = $validator->messages();

			Session::flash('errorsPriceOptions', $count);

			return Redirect::back()->withInput()->withErrors($errors);
		}
		else
		{
		    
		  //  echo "<pre>";
		  //  print_r(Input::all());
		  //  echo "</pre>";
			$categories					= array_unique(Input::get('categories'));
                        $seller_multiple = Input::get('seller_multiple');
                         $vendor_multiple= Input::get('vendor_multiple');
                        $product_base_list =     Input::get('productbase') != "" ? Input::get('productbase') : array();    
                        $is_base_product = Input::get('is_base_product') != "" ? Input::get('is_base_product') : 0;
			$product					= new Product;
			$product->sku				= '';
			$product->sell_id			= $seller_multiple[0];		// Warning: Misleading name
			$product->name				= Input::get('product_name');
			$product->shortname			= Input::get('product_shortname');
			$product->name_cn			= Input::get('product_name_cn');
			$product->name_my			= Input::get('product_name_my');
			$product->category			= implode(',', $categories);		// For backward compatibility purpose
			$product->description		= Input::get('product_desc');
			$product->description_cn	= Input::get('product_desc_cn');
			$product->description_my	= Input::get('product_desc_my');
			$product->delivery_time		= Input::get('delivery_time');
			$product->insert_by			= Session::get('username');
			$product->modify_by			= Session::get('username');
			$product->gst				= Input::get('gst');
			$product->gst_value			= Input::get('gst_value');
			$product->related_product 	= Input::get('related_product');
			$product->do_cat 			= strtoupper(Input::get('do_cat'));
			$product->status			= Input::get('status');
			$product->weight			= Input::get('weight');
            $product->region_country_id	= Input::get('region_country_id');
            $product->region_id			= Input::get('region_id');
			$product->bulk				= Input::get('bulk');
			$product->halal				= Input::get('halal') != "" ? Input::get('halal') : 0;
			$product->freshness			= Input::get('freshness');
			$product->freshness_days    = Input::get('freshness_days');
                        $product->is_base_product		= Input::get('is_base_product') != "" ? Input::get('is_base_product') : 0;
            $product->min_qty    		= Input::get('min_qty');
            $product->max_qty    		= Input::get('max_qty');
            
            $product->is_foreign_market    	= Input::get('enable_foreign_market') != "" ? Input::get('enable_foreign_market') : 0; 
            $product->is_jpoint    	= Input::get('is_jpoint') != "" ? Input::get('is_jpoint') : 0;
            $product->is_voucher_code    	= Input::get('is_voucher_code') != "" ? Input::get('is_voucher_code') : 0;
            $product->is_bank_card_promo    	= Input::get('is_bank_card_promo') != "" ? Input::get('is_bank_card_promo') : 0;
            
            $product->new_arrival_expire = Input::get('new_arrival_expire') != "" ? Input::get('new_arrival_expire') . " 00:00:00" : NULL;
            
			$product->save();
                        
            $productId = $product->id;
            
            foreach ($seller_multiple as $value) {
                
                $ProductSeller =  new ProductSeller;
                $ProductSeller->product_id  = $product->id;
                $ProductSeller->seller_id  = $value;
                $ProductSeller->save();
               
            }
                        
            if(count($vendor_multiple)> 0){

                foreach ($vendor_multiple as $value) {
                    
                    $ProductSeller =  new ProductGSVendor;
                    $ProductSeller->product_id  = $product->id;
                    $ProductSeller->gs_vendor_id  = $value;
                    $ProductSeller->save();
                    
                }

            }

			$insert_audit = General::audit_trail('ProductController.php', 'store()', 'Add Product', Session::get('username'), 'CMS');

			foreach ($categories as $categoryId)
			{
				ProductsCategory::insert([
                    'product_id'    => $product->id,
                    'category_id'   => $categoryId,
                    'main'          => 0,
                    'created_at'    => date('Y-m-d H:i:s'),
				]);
			}

			$productsCategory = new ProductsCategory;
			$productsCategory->setMainCategory($product->id, Input::get('main_category'));

            $tags       = trim(Input::get('tag'));
            $arr_tag    = explode(',', $tags);

			if($tags != "") {
				foreach ($arr_tag as $tag) {
					if($tag != " " && $tag != "") {
						Product::insert_tag([
                            'product_id'    => $product->id,
                            'tag_name'      => trim($tag),
                            'created_at'    => date('Y-m-d H:i:s'),     
						]);
					}
				}	
			}
					
                        $price_option_cost_price = array();
			if (Input::has('price'))
			{
                $result = $this->price->getPrices($product->id);
                $price  = [];
                $count  = 0;

				foreach(Input::get('price') as $p)
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
							{       $arr_p['cost_pricelist'] = $price_option_cost_price;
								$price[]	= $arr_p;
								$arr_p		= '';
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
					$data 	= ['default' => 0];

					foreach ($p as $key => $value)
					{
						if ($key == 'default')
						{
							$data[$key] = 1;
						}

						if ( ! empty($value))
						{
							$data[$key] = $value;
						}
					}

					$data['product_id'] = $product->id;
                    $costOptionPrice = $data['cost_pricelist'];
                    unset($data['cost_pricelist']);
                    unset($data['cost_price']);
                    
                    // FOREIGN MARKET PRICE //
                    if($product->is_foreign_market == 1){
                        $data['foreign_currency'] = 'USD';
                        $ratePrice = self::finalrate2($data['id'], $data['foreign_price'], $data['foreign_price_promo']);
                        foreach ($ratePrice['rate'] as $keyRP => $valueRP) {
                            if($valueRP['currency_code_to'] == 'MYR'){
                                $data['price'] = $valueRP['price'];
                                $data['price_promo'] = $valueRP['price_promo'];   
                            }
                        }
                    }
                    // FOREIGN MARKET PRICE //
                                        
					$newPriceID = $this->price->insertGetId($data);
                                        foreach ($costOptionPrice as $keyCP => $valueCP) {
                                                    
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
                                        if(($is_base_product !=1) && (count($product_base_list) > 0) && (isset($product_base_list['option-'.$option_row]['sku']))){
                                        //if($is_base_product !=1 && count($product_base_list) > 0){
                                            foreach ($product_base_list['option-'.$option_row]['sku'] as $key => $value) {
    //                                            echo "<pre>";
    //                                            echo "SKU:". $key."<p>";
    //                                            echo "QTY:". $value."<p>";
    //                                            echo "</pre>";

                                                $Product = Product::where("sku",$key)->first();

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

					$insert_audit = General::audit_trail('ProductController.php', 'store()', 'Add Product Label', Session::get('username'), 'CMS');
				}
			}

			foreach (Input::get('zone_id') as $key => $value)
			{
				$this->delivery->insert([
					'product_id'	=> $product->id,
					'zone_id'		=> Input::get("zone_id.{$key}"),
					'price'			=> Input::get("zone_price.{$key}"),
				]);

				$insert_audit = General::audit_trail('ProductController.php', 'store()', 'Add Product Zone', Session::get('username'), 'CMS');
			}

			$qrCode		= 'TM'.$product->id;
			$qrCodeFile	= $product->id.'.png';

			$this->product->generateQR($qrCode, 'images/qrcode/', $qrCodeFile);

			$product->sku			= 'TM-'.str_pad($product->id, 7, '0', STR_PAD_LEFT);
			$product->qrcode		= $qrCode;
			$product->qrcode_file	= $qrCodeFile;
			$product->save();
            
            //===== Product History ================

            if (Input::has('price'))
            {

             //   $result = $this->price->getPrices($product->id);
                $price  = [];
                $count  = 0;

                foreach(Input::get('price') as $p)
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
                            {       $arr_p['cost_pricelist'] = $price_option_cost_price;
                                $price[]    = $arr_p;
                                $arr_p      = '';
                                                                $price_option_cost_price = array();
                            }
                        }

                        $arr_p[$key] = $value;
                    }
                }
                                
                $arr_p['cost_pricelist'] = $price_option_cost_price;
                $price[] = $arr_p;

                $prices = Price::where('jocom_product_price.product_id', '=', $productId)
                            ->join('jocom_product_price_seller as seller', 'jocom_product_price.id', '=', 'seller.product_price_id')
                            ->select('jocom_product_price.*', 'seller.seller_id', 'seller.cost_price')
                            ->get();

                foreach($prices as $price)
                {
                    $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                                ->select('product_base_id')->get();

                    $base_ids = array();
                    foreach ($bases as $base) {
                        array_push($base_ids, $base->product_base_id);
                    }
                    $base_id = implode(',', $base_ids);

                    $producthistory             = new ProductsHistory;
                    $producthistory->type       = "New Product";
                    $producthistory->product_id = $productId;
                    $producthistory->sku = 'TM-'.str_pad($productId, 7, '0', STR_PAD_LEFT);
                    $producthistory->name = Input::get('product_name');
                    $producthistory->prd_status = Input::get('status');
                    $producthistory->price_id = $price->id;
                    $producthistory->label = $price->label;
                    $producthistory->price = $price->price;
                    $producthistory->price_promo = $price->price_promo;
                    $producthistory->qty = $price->qty;
                    $producthistory->stock = $price->stock;
                    $producthistory->stock_unit = $price->stock_unit;
                    $producthistory->p_referral_fees = $price->p_referral_fees;
                    $producthistory->p_referral_fees_type = $price->p_referral_fees_type;
                    $producthistory->default = $price->default==''? $default : $price->default;
                    $producthistory->pri_product_id = $productId;
                    $producthistory->pri_status = 1;
                    $producthistory->p_weight = $price->p_weight;

                    $producthistory->created_by = Session::get('username');
                    $producthistory->seller_id = $price->seller_id;
                    $producthistory->cost = $price->cost_price;
                    $producthistory->base_id = $base_id;
                    $producthistory->gst_status = Input::get('gst');

                    $producthistory->delivery_time = Input::get('delivery_time');
                    $delivery_fees = array();
                    foreach (Input::get('zone_id') as $key => $value)
                    {
                        array_push($delivery_fees, Zone::find(Input::get("zone_id.{$key}"))->name.':'.Input::get("zone_price.{$key}"));
                    }
                    $delivery_fee = implode(',', $delivery_fees);
                    $producthistory->delivery_fee = $delivery_fee;

                    $producthistory->save();
                    foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
                }

            }


            //===== Product History ================
            
			Session::flash('message', 'Successfully added.');
			return Redirect::to('product');
		}
		
	    } catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
            }

	public function getEdit($productId)
	{
	    
	    try{
	        
	    
        
                $productFinalPrice = array();
                $productSellerCost = array();
            
		$product = $this->product->findOrFail($productId);
		
		  //echo "<pre>";
    //                 print_r($product);
    //                 echo "</pre>";
    //                 die();
		//$sellers = $this->seller->sortByCompany();
// 		$sellers = $this->seller->sortByCompanyRegion();
                
                $sellersMultiple = ProductSeller::getProductSeller($productId);
                
		$GSVendor = GSVendor::where("activation",1)->get();
        $vendorOptions = array();
		foreach ($GSVendor as $vendor)
		{
			$vendorOptions[$vendor->id] = $vendor->seller_name;
		}

        $GSVendorSelected = DB::table('jocom_products_gs_vendor AS JPGV')
            ->leftJoin('jocom_seller_gs AS JSG', 'JSG.id', '=', 'JPGV.gs_vendor_id')
            ->select('JPGV.*','JSG.seller_name')
            ->where("JPGV.activation",1)->where("JPGV.product_id",$productId)->get();
                
		$vendorGS = array();
		foreach ($GSVendorSelected as $vendor)
		{
			$vendorGS[$vendor->gs_vendor_id] = $vendor->seller_name;
		}

		$parentCategory[] = [
			'id' => 0,
			'category_name' => 'Parent',
			'category_name_cn' => 'Parent',
			'category_name_my' => 'Parent',
			'status' => 1,
			'permission' => 0,
			'weight' => 10
		];
		$categoriesOptions = array_merge($parentCategory, $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- '));
		$productPrices = $this->price->getActivePrices($productId);
                $ProductBaseItem = ProductBaseItem::where("product_id",$productId)
                        ->where("status",1)->get();
                $optionBaseProduct = array();
                
                foreach ($ProductBaseItem as $keyBase => $valueBase) {
                    
                    $productInfo = Product::find($valueBase->product_base_id);
                    $optionBaseProduct[$valueBase->price_option_id][] = array(
                        "product_id"=>$valueBase->product_base_id,
                        "sku"=>$productInfo->sku,
                        "name"=>$productInfo->name,
                        "qty"=>$valueBase->quantity
                            );
                }

		if ( ! $product->gst_value)
		{
			$product->gst_value = 0;
		}

		$zoneOptions	= $this->zone->all();
		$deliveryFees	= $this->delivery->getByZone($productId);
		
		$productPriceExhangeRate = array();
                
                foreach ($productPrices as $key => $value)
                {

                    if($product->gst == 2){
                        $actual_final_price = $value->price * ((100 + $product->gst_value) / 100);
                        $promotion_final_price = $value->price_promo * ((100 + $product->gst_value) / 100);
                    }else{
                        $actual_final_price = $value->price ;
                            $promotion_final_price = $value->price_promo ;
                    }
                    $productFinalPrice['actual_price'][$value->id] = number_format($actual_final_price, 2, '.', '');
                    $productFinalPrice['promo_price'][$value->id] = number_format($promotion_final_price, 2, '.', '');
                    $productSellerCost['product_price_seller'][$value->id] = ProductPriceSeller::gtSellerProductPrice($value->id);
                   
                    if($product->is_foreign_market == 1){
                        
                        $rate = self::finalrate2($value->id,$value->foreign_price , $value->foreign_price_promo);
                        $productPriceExhangeRate['exchange_rate'][$value->id] = $rate;
                        /* Malaysia */
                        
                    }
                    

                }
                
                

        $zoneOptions    = $this->zone->all();
        $deliveryFees   = $this->delivery->getByZone($productId);

		foreach ($deliveryFees as $fee)
		{
			$deliveryZone[] = $fee->zone_id;
		}

		$productsCategory	= new ProductsCategory;
		$mainCategory		= $productsCategory->getMainCategory($productId);
		$selectedCategories	= $productsCategory->getByProduct($productId);
		$arr_tag 			= Product::get_tags($productId);
		$tag 				= array();

		foreach ($arr_tag as $t) {
			$tag[] = $t->tag_name;
 		}

		$tags = implode(', ', $tag);

		foreach ($selectedCategories as $category)
		{
                    $selectedCategoriesArray[] = $category->category_id;
		}
                
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                
                $SysAdminRegion = DB::table('jocom_sys_admin_region AS JSAR' )
      			->leftJoin('jocom_region AS JR', 'JR.id', '=', 'JSAR.region_id')
      			->select('JSAR.*','JR.*')
      			->where("JSAR.status",1)
                        ->where("JSAR.sys_admin_id",$sysAdminInfo->id)
                        ->first();
                
                $country_id = $product->region_country_id;
                if(count($regions) <= 0){
                    $regions      = Region::where("country_id",$country_id)->get();
                }
                
                if($SysAdminRegion->region_id != 0){
                    if($SysAdminRegion->region_id == 4){
                        $regions = DB::table('jocom_region')->where("id",1)->get();
                        $countries = Country::where("id",458)->get();
                        $isFixedOption = true;
                        $sellers = $this->seller->sortByCompany();
                    }else{
                        $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
                        $countries = Country::where("id",$country_id)->get();
                        $sellers = $this->seller->sortByCompanyRegion();
                    }
                    $zoneOptions    = $this->zone->where("country_id",$SysAdminRegion->country_id)->get();
                }else{
                    $regions = Region::where("country_id",$country_id)
                        ->where("activation",1)->get();
                    $countries = Country::getActiveCountry();
                    $zoneOptions    = $this->zone->get();
                    $sellers = $this->seller->sortByCompany();
                }
                
                foreach ($sellers as $seller)
        		{ $status='';
        			switch ($seller->active_status)
        			{
        				case 0:
        					$status = ' **[Inactive]';
        					break;
        				case 2:
        					$status = ' **[Deleted]';
        					break;
        			}
        
        			$sellersOptions[$seller->id] = $seller->company_name.$status;
        		}
            
//                $regions      = Region::where("country_id",$product->region_region_id)->get();
//                if(count($regions) <= 0){
//                    $regions      = Region::where("country_id",458)->get();
//                }

           /*Warehouse Link Node Products    */
           $nodeProducts = array();   
           $baseProducts = array();   
           $nodevalid = 0;  
           $basevalid = 0;
           $nodevalid = Warehouse::isExistsBasic($productId);
           $basevalid = Warehouse::isExistsproductbase($productId);

           $nodeProducts = Warehouse::getProductslinksup($productId); 

           $baseProducts = Warehouse::getProductsbaselinksup($productId);  

                
		return View::make('product.edit', [
		    'vendorGS' => $vendorGS,
            'vendorOptions' => $vendorOptions,
			'product'					=> $product,
			'sellersOptions'			=> $sellersOptions,
			'nodeProducts'				=> $nodeProducts,
			'baseProducts'				=> $baseProducts,
			'nodevalid'					=> $nodevalid,
			'basevalid'					=> $basevalid,
			'categoriesOptions'			=> $categoriesOptions,
			'productPrices'				=> $productPrices,
			'zoneOptions'				=> $zoneOptions,
			'deliveryFees'				=> $deliveryFees,
			'deliveryZone'				=> $deliveryZone,
			'mainCategory'				=> $mainCategory,
			'selectedCategoriesArray'	=> $selectedCategoriesArray,
			'tags'						=> $tags,
                        'productFinalPrice'            => $productFinalPrice,
                        'productSellerCost' => $productSellerCost,
                        'sellersMultiple'=>$sellersMultiple,
                        'regions' => $regions,
                        'countries' => $countries,
                        "optionBaseProduct" => $optionBaseProduct,
                        "isFixedOption" => $isFixedOption,
                        "productPriceExhangeRate" => $productPriceExhangeRate
		]);
		
	    }catch(Exception $ex){
	        echo $ex->getMessage();
	         echo $ex->getLine();
	    }
	}

	public function putUpdate($productId)
	{
    
		$count		= 0;
		$validator	= $this->getValidator($count);
        
        $p_flag = 0; 
        
		if ($validator->fails())
		{
              
			$errors = $validator->messages();

			Session::flash('errorsPriceOptions', $count);

			return Redirect::back()->withInput()->withErrors($errors);
		}
		else
		{
            $res = Product::getBackupproducts($productId);        
             
			$categories					= Input::get('categories');
                        $sellerMultiple = Input::get('seller_multiple');
                        $vendor_multiple = Input::get('vendor_multiple') != "" ? Input::get('vendor_multiple') :  array();  
                        
                        $cost_price = Input::get('cost_price');
                        $price_option_cost_price = array();
                        $product_base_list =     Input::get('productbase') != "" ? Input::get('productbase') : array();    
                        $is_base_product = Input::get('is_base_product') != "" ? Input::get('is_base_product') : 0;
                        
                        $stock_product = Product::getCheckStockproduct($productId);

                         if($stock_product == 1){
                            $is_base_product = 1; 
                         }

			$product					= Product::findOrFail($productId);
			$product->sell_id			= $sellerMultiple[0];		// Warning: Misleading name
			$product->name				= Input::get('product_name');
                        $product->shortname			= Input::get('product_shortname');
			$product->name_cn			= Input::get('product_name_cn');
			$product->name_my			= Input::get('product_name_my');
			$product->category			= implode(',', $categories);		// For backward compatibility purpose
                        $product->description		= Input::get('product_desc');
                        $product->description_cn	= Input::get('product_desc_cn');
                        $product->description_my	= Input::get('product_desc_my');
                        $product->delivery_time		= Input::get('delivery_time');
			$product->modify_by			= Session::get('username');
                        $product->modify_date       = date('Y-m-d H:i:s');
			$product->gst				= Input::get('gst');
                        $product->related_product	= Input::get('related_product');
			$product->do_cat			= strtoupper(Input::get('do_cat'));
			$product->status			= Input::get('status');
			$product->weight			= Input::get('weight');
                        $product->region_country_id	        = Input::get('region_country_id');
                        $product->region_id			= Input::get('region_id');
			$product->bulk				= Input::get('bulk');
			$product->halal				= Input::get('halal') != "" ? Input::get('halal') : 0;
                        $product->is_base_product		= $is_base_product; //Input::get('is_base_product') != "" ? Input::get('is_base_product') : 0;
			$product->freshness			= Input::get('freshness');
                        $product->freshness_days    = Input::get('freshness_days');
            $product->min_qty    		= Input::get('min_qty');
            $product->max_qty    		= Input::get('max_qty');
            $product->is_foreign_market    	= Input::get('enable_foreign_market') != "" ? Input::get('enable_foreign_market') : 0;
            $product->is_jpoint    	= Input::get('is_jpoint') != "" ? Input::get('is_jpoint') : 0;
            $product->is_voucher_code    	= Input::get('is_voucher_code') != "" ? Input::get('is_voucher_code') : 0;
            $product->is_bank_card_promo    	= Input::get('is_bank_card_promo') != "" ? Input::get('is_bank_card_promo') : 0;
            $product->new_arrival_expire = Input::get('new_arrival_expire') != "" ? Input::get('new_arrival_expire') . " 00:00:00" : NULL;
                   
			$product->save();

			$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product', Session::get('username'), 'CMS');
                        // UPDATE SELLER 
                           
                            $ProductSeller = ProductSeller::where("product_id",$productId)
                                    ->where("activation",1)->get();
                            
                            foreach ($ProductSeller as $key => $value) {
                              
                                if(!in_array($value->seller_id, $sellerMultiple)){
                                    $ProductSellerInfo = ProductSeller::find($value->id);
                                    $ProductSellerInfo->activation = 0;
                                    $ProductSellerInfo->save();
                                }
                                
                                if(in_array($value->seller_id, $sellerMultiple)){
                                    $arrayKey = array_search($value->seller_id, $sellerMultiple);
                                    unset($sellerMultiple[$arrayKey]);
                                }
                            }
                            
                            if(count($sellerMultiple)> 0){
                                foreach ($sellerMultiple as $value) {
                                    $ProductSeller = new ProductSeller;
                                    $ProductSeller->product_id = $productId;
                                    $ProductSeller->seller_id = $value;
                                    $ProductSeller->save();
                                
                                }   
                            }
                            
                            
                            $ProductGSVendor = ProductGSVendor::where("product_id",$productId)
                                    ->where("activation",1)->get();


                            foreach ($ProductGSVendor as $key => $value) {

                                $GSVendor = ProductGSVendor::find($value->id);
                                $GSVendor->activation = 0;
                                $GSVendor->save();
                            }

                            if(count($vendor_multiple)> 0){
                               
                                foreach ($vendor_multiple as $value) {
                                    $ProductSeller =  new ProductGSVendor;
                                    $ProductSeller->product_id  = $product->id;
                                    $ProductSeller->gs_vendor_id  = $value;
                                    $ProductSeller->save();
                                    
                                }
                            } 
                              
                        
                        // UPDATE SELLER
			$this->updateCategories($product->id, $categories);

			$tags 		= trim(Input::get('tag'));
			$arr_tag	= explode(',', $tags);

			$tag_exists = DB::table('jocom_product_tags')->where('product_id', '=', $product->id)->first();

			if (count($tag_exists) > 0) {
				Product::delete_tag($product->id);
			}

			if ($tags != "") {
				foreach ($arr_tag as $tag) {
					if($tag != " " && $tag != "") {
						Product::insert_tag([
							'product_id' 	=> $product->id,
							'tag_name'		=> trim($tag),
							'created_at'	=> date('Y-m-d H:i:s'),
						]);
					}
				}
			}
			
			if (Input::has('price'))
			{
				$result	= $this->price->getPrices($product->id);
				$price	= [];
				$count	= 0;

				foreach(Input::get('price') as $p)
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
								$price[]	= $arr_p;
								$arr_p		= '';
                                                                $price_option_cost_price = array();
							}
						}
                                               
                                                
						$arr_p[$key] = $value;
                                                
					}
				}
                                
                                
                                $arr_p['cost_pricelist'] = $price_option_cost_price;
				$price[] = $arr_p;

				$arr_tmp = Product::get_all_price_id($product->id);

				foreach ($arr_tmp as $tmp) {                                    
					$arr_old_price[] = $tmp->id;
				}
                                
                                $productPriceSellerID = array();
                                $productPriceSellerOLD = array();
                                
                               
                                $option_row = 1;
                                $ProductBaseItem = ProductBaseItem::where("product_id",$product->id)
                                        ->where("status",1)->update(['status' => 0,'updated_by' => Session::get('username')]);
				foreach ($price as $p) {
					$price_id = $p['id'];
                                        
                                        $ProductPriceSellerOld = ProductPriceSeller::where("product_price_id",$price_id)
                                                ->where("activation",1)->get();
                                               
                                        foreach ($ProductPriceSellerOld as $key => $value) {
                                            $productPriceSellerOLD[] = $value->id;
                                        }
                                        
        
					$p['default'] = array_key_exists('default', $p) ? 1 : 0;
                                        
                                        // ADD SELLER COST PRICE //
                                        
                                        foreach ($p['cost_pricelist'] as $keyCP => $valueCP) {
                                            
                                            $seller_id = key($valueCP);
                                            $amountCostPrice = $valueCP[$seller_id];
//                                         print_r($valueCP[$seller_id]);
//                                            die();
                                            $ProductPriceSeller = ProductPriceSeller::getPriceBySeller($price_id,$seller_id);
                                            
                                            if(count($ProductPriceSeller)> 0){
                                                // UPDATE
                                                $PPriceSeller = ProductPriceSeller::find($ProductPriceSeller->id);
                                                $PPriceSeller->cost_price = $amountCostPrice;
                                                $PPriceSeller->updated_by = Session::get('username');
                                                $PPriceSeller->save();
                                                $productPriceSellerID[] = $ProductPriceSeller->id;
                                            }else{
                                                // INSERT NEW
                                                $PPriceSeller = new ProductPriceSeller;
                                                $PPriceSeller->product_price_id = $price_id;
                                                $PPriceSeller->seller_id = $seller_id;
                                                $PPriceSeller->cost_price = $amountCostPrice;
                                                $PPriceSeller->created_by = Session::get('username');
                                                $PPriceSeller->updated_by = Session::get('username');
                                                $PPriceSeller->activation = 1;
                                                $PPriceSeller->save();
                                                $productPriceSellerID[] = $PPriceSeller->id;
                                            }
                                            
                                           
                                        }
                                        
                                        // ADD SELLER COST PRICE // 

                    $old_actual_stock = 0;
					if ($price_id != "") {
						if(in_array($price_id, $arr_old_price)) {
							// echo "<br>[UPDATE - STILL EXISTS] [ID: ".$price_id."]";
							// echo " < - - - - [GOT!] [ID: ".$price_id."]";
                            unset($p['cost_pricelist']);
                            unset($p['cost_price']);
							$arr_price_matched[] = $p['id'];
							$p['foreign_currency'] = 'USD';
                            if($product->is_foreign_market == 1){
                                $ratePrice = self::finalrate2($p['id'], $p['foreign_price'], $p['foreign_price_promo']);
                                
                                foreach ($ratePrice['rate'] as $keyRP => $valueRP) {
                                    if($valueRP['currency_code_to'] == 'MYR'){
                                        $p['price'] = $valueRP['price'];
                                        $p['price_promo'] = $valueRP['price_promo'];   
                                    }
                                }
                            }
                            $old_actual_stock = Price::find($price_id)->stock;
							$this->price->where('id', '=', $price_id)->update($p);
                                                        $option_price_id = $price_id;
							// var_dump($p);
						}
					}
					else {
						// echo "<br>[NEW Price] ";
						$p['product_id'] = $product->id;
                                                $costOptionPrice = $p['cost_pricelist'];
                                                unset($p['cost_pricelist']);
                                                unset($p['cost_price']);
						$newPriceID = $this->price->insertGetId($p);
                                                $option_price_id = $newPriceID;
                                                $old_actual_stock = $p['stock'];
                                                foreach ($costOptionPrice as $keyCP => $valueCP) {
                                                    
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
                                                    $productPriceSellerID[] = $PPriceSeller->id;
                                                }
//                                                 die();
					}
                                        
                                        // Product Base 
                                        //echo "PRICE OPTION ID:".$option_price_id."<p>";
                                        
                                        
                                        if(($is_base_product !=1) && (count($product_base_list) > 0) && (isset($product_base_list['option-'.$option_row]['sku']))){
                                            foreach ($product_base_list['option-'.$option_row]['sku'] as $key => $value) {

                                                $Product = Product::where("sku",$key)->first();

                                                $ProductBaseItem = new ProductBaseItem();
                                                $ProductBaseItem->product_id = $productId;
                                                $ProductBaseItem->product_base_id = $Product->id;
                                                $ProductBaseItem->price_option_id = $option_price_id;
                                                $ProductBaseItem->quantity = $value;
                                                $ProductBaseItem->status = 1;
                                                $ProductBaseItem->created_by = Session::get('username');
                                                $ProductBaseItem->save();
                                                //print_r($value);
                                             }
                                        }
                                        
                                        
                                        $option_row++;
					$this->deductWarehouseActualstock($product->id, $option_price_id, $p['stock'], $old_actual_stock);
					$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product Label', Session::get('username'), 'CMS');
				}

                                foreach ($productPriceSellerOLD as $v ) {

                                     if(!in_array($v, $productPriceSellerID)){
                                         $PPriceSellerDeactivate = ProductPriceSeller::find($v);
                                         $PPriceSellerDeactivate->activation = 0;
                                         $PPriceSellerDeactivate->updated_by = Session::get('username');
                                         $PPriceSellerDeactivate->save();
                                     }               

                                }
   
				foreach ($arr_old_price as $old_price) {
					if (!in_array($old_price, $arr_price_matched)) {

						// echo "<br>[UPDATE - NO LONGER EXISTS] [ID: ".$old_price."]";
						$udata['default'] 	= 0;
						$udata['status'] 	= 2;

						$this->price->where('id', '=', $old_price)->update($udata);
						// var_dump($udata);
						
						$prices = Price::where('jocom_product_price.id', '=', $old_price)
                            ->join('jocom_product_price_seller as seller', 'jocom_product_price.id', '=', 'seller.product_price_id')
                            ->select('jocom_product_price.*', 'seller.seller_id', 'seller.cost_price')
                            ->get();

                        foreach ($prices as $price) 
                        {
                            $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                                ->where('status', '=', 1)
                                ->select('product_base_id')->get();

                            $base_ids = array();
                            foreach ($bases as $base) {
                                array_push($base_ids, $base->product_base_id);
                            }
                            $base_id = implode(',', $base_ids);

                            $producthistory             = new ProductsHistory;
                            $producthistory->type       = "Update Product";
                            $producthistory->product_id = $productId;
                            $producthistory->sku = 'TM-'.str_pad($productId, 7, '0', STR_PAD_LEFT);
                            $producthistory->name = Input::get('product_name');
                            $producthistory->prd_status = Input::get('status');
                            $producthistory->price_id = $price->id;// continue here
                            $producthistory->label = $price->label;
                            $producthistory->price = $price->price;
                            $producthistory->price_promo = $price->price_promo;
                            $producthistory->qty = $price->qty;
                            $producthistory->stock = $price->stock;
                            $producthistory->stock_unit = $price->stock_unit;
                            $producthistory->p_referral_fees = $price->p_referral_fees;
                            $producthistory->p_referral_fees_type = $price->p_referral_fees_type;
                            $producthistory->default = $price->default==''? $default : $price->default;
                            $producthistory->pri_product_id = $productId;
                            $producthistory->pri_status = 2;
                            $producthistory->p_weight = $price->p_weight;

                            $producthistory->updated_by = Session::get('username');
                            $producthistory->seller_id = $price->seller_id;
                            $producthistory->cost = $price->cost_price;
                            $producthistory->base_id = $base_id;
                            $producthistory->gst_status = Input::get('gst');

                            $producthistory->delivery_time = Input::get('delivery_time');
                            $delivery_fees = array();
                            foreach (Input::get('zone_id') as $key => $value)
                            {
                                array_push($delivery_fees, Zone::find(Input::get("zone_id.{$key}"))->name.':'.Input::get("zone_price.{$key}"));
                            }
                            $delivery_fee = implode(',', $delivery_fees);
                            $producthistory->delivery_fee = $delivery_fee;
                            $producthistory->save();
                            foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
                        }
					}

					$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product Label', Session::get('username'), 'CMS');
				}

				// foreach($result as $row)
				// {
				// 	$exists = false;

				// 	foreach ($price as $p)
				// 	{
				// 		$udata = [
				// 			'default' => 0,
				// 		];

				// 		foreach ($p as $key => $value)
				// 		{
				// 			if ($key == 'id' && ! empty($value))
				// 			{
				// 				if ($value == $row->id)
				// 				{
				// 					$exists = true;
				// 				}
				// 			}

				// 			if ($key == 'default')
				// 			{
				// 				$udata[$key] = 1;
				// 			}

				// 			if ($key != 'default')
				// 			{
				// 				$udata[$key] = $value;
				// 			}
				// 		}

				// 		if ($exists == true)
				// 		{
				// 			$this->price->where('id', '=', $row->id)->update($udata);
				// 			break;
				// 		}
				// 	}

				// 	if ($exists == false)
				// 	{
				// 		$udata = [
				// 			'default'	=> 0,
				// 			'status'	=> 2,
				// 		];

				// 		$this->price->where('id', '=', $row->id)->update($udata);
				// 		break;
				// 	}

				// 	$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product Label', Session::get('username'), 'CMS');
				// }

				// foreach ($price as $p)
				// {
				// 	$insert = false;
				// 	$data 	= [
				// 		'default' => 0,
				// 	];

				// 	foreach ($p as $key => $value)
				// 	{
				// 		if ($key == 'id' && empty($value))
				// 		{
				// 			$insert = true;
				// 		}

				// 		if ($key == 'default')
				// 		{
				// 			$data[$key] = 1;
				// 		}

				// 		if ( ! empty($value))
				// 		{
				// 			$data[$key] = $value;
				// 		}
				// 	}

				// 	if ($insert == true)
				// 	{
				// 		$data['product_id'] = $product->id;
				// 		$this->price->insert($data);
				// 	}

				// 	$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product Label', Session::get('username'), 'CMS');
				// }
			}

			$this->delivery->where('product_id', '=', $product->id)->delete();

			foreach (Input::get('zone_id') as $key => $value)
			{
				$this->delivery->insert([
					'product_id'	=> $product->id,
					'zone_id'		=> Input::get("zone_id.{$key}"),
					'price'			=> Input::get("zone_price.{$key}"),
				]);

				$insert_audit = General::audit_trail('ProductController.php', 'update()', 'Edit Product Zone', Session::get('username'), 'CMS');
			}
            
            //===== Product History ================

            if (Input::has('price'))
            {

               $result = $this->price->getPrices($product->id);
                $price  = [];
                $count  = 0;
                $default = 0;

                foreach(Input::get('price') as $p)
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
                            {       $arr_p['cost_pricelist'] = $price_option_cost_price;
                                $price[]    = $arr_p;
                                $arr_p      = '';
                                                                $price_option_cost_price = array();
                            }
                        }

                        $arr_p[$key] = $value;
                    }
                }
                                
                $arr_p['cost_pricelist'] = $price_option_cost_price;
                $price[] = $arr_p;

                foreach($price as $key => $ph)
                {
                
                    $prices = Price::where('jocom_product_price.id', '=', $ph['id'])
                            ->join('jocom_product_price_seller as seller', 'jocom_product_price.id', '=', 'seller.product_price_id')
                            ->select('jocom_product_price.*', 'seller.seller_id', 'seller.cost_price')
                            ->get();

                    foreach($prices as $price)
                    {
                        $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                                    ->where('status', '=', 1)
                                    ->select('product_base_id')->get();
    
                        $base_ids = array();
                        foreach ($bases as $base) {
                            array_push($base_ids, $base->product_base_id);
                        }
                        $base_id = implode(',', $base_ids);
    
                        $producthistory             = new ProductsHistory;
                        $producthistory->type       = "Update Product";
                        $producthistory->product_id = $productId;
                        $producthistory->sku = 'TM-'.str_pad($productId, 7, '0', STR_PAD_LEFT);
                        $producthistory->name = Input::get('product_name');
                        $producthistory->prd_status = Input::get('status');
                        $producthistory->price_id = $price->id;
                        $producthistory->label = $price->label;
                        $producthistory->price = $price->price;
                        $producthistory->price_promo = $price->price_promo;
                        $producthistory->qty = $price->qty;
                        $producthistory->stock = $price->stock;
                        $producthistory->stock_unit = $price->stock_unit;
                        $producthistory->p_referral_fees = $price->p_referral_fees;
                        $producthistory->p_referral_fees_type = $price->p_referral_fees_type;
                        $producthistory->default = $price->default==''? $default : $price->default;
                        $producthistory->pri_product_id = $productId;
                        $producthistory->pri_status = 1;
                        $producthistory->p_weight = $price->p_weight;
    
                        $producthistory->updated_by = Session::get('username');
                        $producthistory->seller_id = $price->seller_id;
                        $producthistory->cost = $price->cost_price;
                        $producthistory->base_id = $base_id;
                        $producthistory->gst_status = Input::get('gst');

                        $producthistory->delivery_time = Input::get('delivery_time');
                        $delivery_fees = array();
                        foreach (Input::get('zone_id') as $key => $value)
                        {
                            array_push($delivery_fees, Zone::find(Input::get("zone_id.{$key}"))->name.':'.Input::get("zone_price.{$key}"));
                        }
                        $delivery_fee = implode(',', $delivery_fees);
                        $producthistory->delivery_fee = $delivery_fee;
                        $producthistory->save();
                        foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
                    }

                }



            }


            //===== Product History ================
            
			//PRODUCT BASE LINKS

            $ref_no 	 = 0;
            $bflag 	 	 = 0;
            $baseprod_id = 0;
            $default	 = 1;	

			$baseprod_id = $product->id;
			$basestatus = Input::get('base_status_st');
			
			if(Input::has('parent_productID') || Input::has('base_productID')){

            $baseresult = DB::table('jocom_warehouse_products_baselinks AS WPB')
            				  ->where('WPB.variant_product_id','=',$baseprod_id)
            				  ->where('WPB.product_id','=',$baseprod_id)
            				  ->where('WPB.default','=',1)
            				  ->where('WPB.status','<>',2)
            				  ->first();
            if(count($baseresult)>0){
            	$ref_no = $baseresult->ref_no;
            }	
            else
            {
            	$baseresult_01 = DB::table('jocom_warehouse_products_baselinks AS WP')
                    ->where(function ($query){
                                $query->where('WP.variant_product_id','=',$baseprod_id)
                                      ->orwhere('WP.product_id','=',$baseprod_id);
                               }) 
                    ->get();

		        if(count($baseresult_01)==0){
            	$refno = Warehouse::genReferenceno();
	            DB::table('jocom_warehouse_products_baselinks')->insert(array(
	                            'variant_product_id'    => $baseprod_id,
	                            'product_id'            => $baseprod_id,
	                            'ref_no'                => $refno,
	                            'default'               => $default, 
	                            'insert_by'             => Session::get('username'),
	                            'created_at'            => date('Y-m-d H:i:s'),
	                            'modify_by'             => Session::get('username'),
	                            'updated_at'            => date('Y-m-d H:i:s')
	                            )
	                    );
	            	$ref_no = $refno; 	
	            	$bflag = 1;
	        	}
            }
			}

            if($ref_no != 0){
            	$base_productID[] = array();

            	if(Input::has('base_productID')){

            		$base_productID = Input::get('base_productID');



            		$basecount = count($base_productID)-1;

            		for($i=0;$i<=$basecount;$i++){

            			$prdbaseID 	 = $base_productID[$i];


	            		$ProdBase =  ProductBase::where("variant_product_id",$prdbaseID)->first();

	            		// echo '<pre>';
	            		// print_r($ProdBase);
	            		// echo '</pre>';
            			// echo $ref_no;
	            		// print_r($prdbaseID.'UO');
            			// die();


	            		if(count($ProdBase)>0){

	            			$baseId = $ProdBase->id; 
	            			// echo $base_productID[$basecount][$i]; die();
		            		$ProductBaseUpdate =  ProductBase::find($baseId);
				            $ProductBaseUpdate->variant_product_id = $base_productID[$i];
			                $ProductBaseUpdate->modify_by = Session::get('username');
				            $ProductBaseUpdate->save();

	            		}
	            		else{

	            			if(isset($base_productID[$i])){

	            				$ProductBase = new ProductBase;
				            	$ProductBase->variant_product_id = $base_productID[$i];
					            $ProductBase->product_id = $baseprod_id;
					            $ProductBase->ref_no = $ref_no;
					            $ProductBase->insert_by = Session::get('username');
				                $ProductBase->modify_by = Session::get('username');
					            $ProductBase->save();


	            			}

	            		}



            		}


            	}

            }



			//PRODUCT NODE Links 

			$node_productID[] = array();
			$node_NodeLevel[] = array();
			$node_quantity[]  = array();
			$node_variantproductID[]  = array();
			$nodestatus = Input::get('node_status_st');

			if(isset($nodestatus) && $nodestatus ==2){
				$Resultdeleted = ProductNode::where("parent_product_id",$product->id)->first();

				$Resultdeleted->status = $nodestatus;
                $Resultdeleted->modify_by = Session::get('username');
	            $Resultdeleted->save();

			}

            
            $comparray      = array();




            if(Input::has('parent_productID')){


            	if($ref_no != 0){

            		// echo $ref_no.'ok2';
            		$node_productID = Input::get('parent_productID');
	            	$node_variantproductID = Input::get('node_variantproductID');
					// $node_NodeLevel[] = Input::get('node_NodeLevel');
					$node_quantity = Input::get('node_quantity');

					// echo '<pre>';
					// print_r($node_variantproductID);
					// echo '</pre>';

					$node_status = Input::get('node_status');

					$wcount = 0;
	            	$count = count($node_productID);
		          
		            for($i=0;$i<$count;$i++){
		            	$wcount = $wcount + 1;
		            	$prdID = $node_productID[$i];
		            	// echo $prdID.'-';
		            	$ProdNode =  ProductNode::where("product_id",$prdID)->first();
		            	if(count($ProdNode)>0){

		            		$nodeId = $ProdNode->id; 

		            		$ProductNodeUpdate =  ProductNode::find($nodeId);
		            		$ProductNodeUpdate->base_product_id    = $baseprod_id;	
		            		$ProductNodeUpdate->nodeposition = $wcount;//$node_NodeLevel[$count][$i];
				            $ProductNodeUpdate->quantity = $node_quantity[$i];
				            $ProductNodeUpdate->ref_no = $ref_no;
				            // $ProductNodeUpdate->status = $node_status;
			                $ProductNodeUpdate->modify_by = Session::get('username');
				            $ProductNodeUpdate->save();

		            	}
		            	else{
		            		// echo 'Ok3';
		            		if(isset($node_productID[$i])){
		            			$ProductNode = new ProductNode;
				            	$ProductNode->product_id = $node_productID[$i];
					            $ProductNode->parent_product_id = $node_variantproductID[$i];
					            $ProductNode->base_product_id    = $baseprod_id;	
					            $ProductNode->nodeposition = $wcount;//$node_NodeLevel[$count][$i];
					            $ProductNode->quantity = $node_quantity[$i];
					            $ProductNode->ref_no = $ref_no;

					            $ProductNode->insert_by = Session::get('username');
				                $ProductNode->modify_by = Session::get('username');
					            $ProductNode->save();


		            		}
		            		
		            	} 
		            	
		            }

		            // die();
            		
            	
            	}



            	



            }

            


			
    

			Session::flash('message', 'Successfully updated.');
                       
			return Redirect::to('product');
		}
   
	}
	
	public function anyDuplicateadd($productId){

        try{

        $productmain = $this->product->findOrFail($productId);
        

        if(count($productmain)>0){
            // echo 'In';
            $product                    = new Product;
            $product->sku               = '';
            $product->sell_id           = $productmain->sell_id;      // Warning: Misleading name
            $product->name              = $productmain->name; 
            $product->shortname         = $productmain->shortname; 
            $product->name_cn           = $productmain->name_cn; 
            $product->name_my           = $productmain->name_my; 
            $product->category          = $productmain->category;      // For backward compatibility purpose
            $product->description       = $productmain->description; 
            $product->description_cn    = $productmain->description_cn; 
            $product->description_my    = $productmain->description_my; 
            $product->delivery_time     = $productmain->delivery_time;
            $product->insert_by         = Session::get('username');
            $product->modify_by         = Session::get('username');
            $product->gst               = $productmain->gst;
            $product->gst_value         = $productmain->gst_value;
            $product->related_product   = $productmain->related_product;
            $product->do_cat            = $productmain->do_cat;
            $product->status            = $productmain->status;
            $product->weight            = $productmain->weight;
            $product->region_country_id = $productmain->region_country_id;
            $product->region_id         = $productmain->region_id;
            $product->bulk              = $productmain->bulk;
            $product->halal             = $productmain->halal;
            $product->freshness         = $productmain->freshness;
            $product->freshness_days    = $productmain->freshness_days;
            $product->is_base_product   = $productmain->is_base_product;
            $product->min_qty           = $productmain->min_qty;
            $product->max_qty           = $productmain->max_qty;
            
            $product->is_foreign_market = $productmain->is_foreign_market;
            $product->is_jpoint         = $productmain->is_jpoint;
            $product->is_voucher_code    = $productmain->is_voucher_code;
            $product->is_bank_card_promo = $productmain->is_bank_card_promo;
            $product->new_arrival_expire = $productmain->new_arrival_expire;
            
            $product->save();
                        
            $product_Id = $product->id;
            echo $product_Id;


            $qrCode     = 'TM'.$product->id;
            $qrCodeFile = $product->id.'.png';

            $this->product->generateQR($qrCode, 'images/qrcode/', $qrCodeFile);

            $product->sku           = 'TM-'.str_pad($product->id, 7, '0', STR_PAD_LEFT);
            $product->qrcode        = $qrCode;
            $product->qrcode_file   = $qrCodeFile;
            $product->save();

            $ProductSellerresults = ProductSeller::where("product_id",$productId)
                                    ->where("activation",1)->get();
            
            foreach ($ProductSellerresults as $key => $value) {
                
                $ProductSeller =  new ProductSeller;
                $ProductSeller->product_id  = $product_Id;
                $ProductSeller->seller_id  = $value->seller_id;
                $ProductSeller->save();
               
            }

            $ProductGSVendorresults = ProductGSVendor::where("product_id",$productId)
                                    ->where("activation",1)->get();
                        
            if(count($ProductGSVendorresults)> 0){

                foreach ($ProductGSVendorresults  as $key => $value) {
                    
                    $ProductSeller =  new ProductGSVendor;
                    $ProductSeller->product_id  = $product_Id;
                    $ProductSeller->gs_vendor_id  = $value->gs_vendor_id;
                    $ProductSeller->save();
                    
                }

            }

            $insert_audit = General::audit_trail('ProductController.php', 'store()', 'Add Product', Session::get('username'), 'CMS');

            $productsCategory   = new ProductsCategory;
            $selectedCategories = $productsCategory->getByProduct($productId);
            $arr_tag            = Product::get_tags($productId);
            $tag                = array();

            foreach ($selectedCategories as $key => $categoryId)
            {
                ProductsCategory::insert([
                    'product_id'    => $product_Id,
                    'category_id'   => $categoryId->category_id,
                    'main'          => $categoryId->main,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($arr_tag as $key => $tag) {
                Product::insert_tag([
                            'product_id'    => $product_Id,
                            'tag_name'      => $tag->tag_name,
                            'created_at'    => date('Y-m-d H:i:s'),     
                        ]);
            }

            // Price Option Start

                $resultprice = Price::where('product_id',$productId)
                                  ->where('status','=',1)
                                  ->get();

            if(count($resultprice)>0){
                    foreach ($resultprice as $key => $value) {

                            $temparray = array(
                                               'label' => $value->label,
                                               'label_cn' => $value->label_cn,
                                               'label_my' => $value->label_my,
                                               'alternative_label_name' => $value->alternative_label_name,
                                               'seller_sku' => $value->seller_sku,
                                               'barcode' => $value->barcode,
                                               'price' => $value->price,
                                               'price_promo' => $value->price_promo,
                                               'foreign_price' => $value->foreign_price,
                                               'foreign_price_promo' => $value->foreign_price_promo,
                                               'foreign_currency' => $value->foreign_currency,
                                               'qty' => $value->qty,
                                               'stock' => $value->stock,
                                               'stock_unit' => $value->stock_unit,
                                               'p_referral_fees' => $value->p_referral_fees,
                                               'p_referral_fees_type' => $value->p_referral_fees_type,
                                               'default' => $value->default,
                                               'product_id' => $product_Id,
                                               'status' => $value->status,
                                               'p_weight' => $value->p_weight
                                              );

                            $priceID = DB::table('jocom_product_price')->insertGetId(array(
                                               'label' => $value->label,
                                               'label_cn' => $value->label_cn,
                                               'label_my' => $value->label_my,
                                               'alternative_label_name' => $value->alternative_label_name,
                                               'seller_sku' => $value->seller_sku,
                                               'barcode' => $value->barcode,
                                               'price' => $value->price,
                                               'price_promo' => $value->price_promo,
                                               'foreign_price' => $value->foreign_price,
                                               'foreign_price_promo' => $value->foreign_price_promo,
                                               'foreign_currency' => $value->foreign_currency,
                                               'qty' => $value->qty,
                                               'stock' => $value->stock,
                                               'stock_unit' => $value->stock_unit,
                                               'p_referral_fees' => $value->p_referral_fees,
                                               'p_referral_fees_type' => $value->p_referral_fees_type,
                                               'default' => $value->default,
                                               'product_id' => $product_Id,
                                               'status' => $value->status,
                                               'p_weight' => $value->p_weight
                                              )
                                );

                             if(count($priceID)>0){
                                    $resultpriceSeller = ProductPriceSeller::where('product_price_id',$value->id)
                                                          ->where('activation','=',1)
                                                          ->first();
                                    if(count($resultpriceSeller)>0){
                                            $PPriceSeller = new ProductPriceSeller;
                                            $PPriceSeller->product_price_id = $priceID;
                                            $PPriceSeller->seller_id = $resultpriceSeller->seller_id;
                                            $PPriceSeller->cost_price = $resultpriceSeller->cost_price;
                                            $PPriceSeller->created_by = Session::get('username');
                                            $PPriceSeller->updated_by = Session::get('username');
                                            $PPriceSeller->activation = 1;
                                            $PPriceSeller->save();
                                    }   

                                    $resultProductBaseItem = ProductBaseItem::where('price_option_id',$value->id)
                                                          ->where('product_id',$value->product_id)    
                                                          ->where('status','=',1)
                                                          ->get();

                                    if(count($resultProductBaseItem)>0){ 
                                            foreach ($resultProductBaseItem as $key => $value1) {
                                                $ProductBaseItem = new ProductBaseItem();
                                                $ProductBaseItem->product_id = $product_Id;
                                                $ProductBaseItem->product_base_id = $value1->product_base_id;
                                                $ProductBaseItem->price_option_id = $priceID;
                                                $ProductBaseItem->quantity = $value1->quantity;
                                                $ProductBaseItem->status = 1;
                                                $ProductBaseItem->created_by = Session::get('username');
                                                $ProductBaseItem->save();
                                            }
                                    }                     

                             }

                    }                  
            
            

            }

            // Price Option End 
            $tempzone = array();
            $resultProductDel = DB::table("jocom_product_delivery")
                      ->where('product_id','=',$productId)
                      ->get();
            
            foreach ($resultProductDel as $key => $value)
            {
                $tempzone['zone_id'] = $value->zone_id;
                $tempzone['price'] = $value->price;
                $tempzone['product_id'] = $product_Id;
                // $zoneID = Product::InsertProduct($tempzone, 'jocom_product_delivery');
                $zoneID = DB::table('jocom_product_delivery')->insertGetId($tempzone);
            }



        }


            Session::flash('message', 'Successfully Item Duplicated.');
                       
            // return Redirect::to('product');
            // die('end');

        } catch (Exception $ex) {
                echo $ex->getMessage();
            }

        

    }

	private function updateCategories($productId, $categories)
	{
		$productsCategory	= new ProductsCategory;
		$dbCategories		= array_pluck($productsCategory->getByProduct($productId)->toArray(), 'category_id');
		$insertCategories	= array_unique(array_diff($categories, $dbCategories));
		$deleteCategories	= array_unique(array_diff($dbCategories, $categories));

		if ( ! empty($insertCategories))
		{
			foreach ($insertCategories as $categoryId)
			{
				ProductsCategory::insert([
					'product_id'	=> $productId,
					'category_id'	=> $categoryId,
					'main'			=> 0,
					'created_at'	=> date('Y-m-d H:i:s'),
				]);
			}
		}

		if ( ! empty($deleteCategories))
		{
			foreach ($deleteCategories as $categoryId)
			{
				$productsCategory = $productsCategory->findMatch($productId, $categoryId);

				if ($productsCategory)
				{
					$productsCategory->delete();
				}
			}
		}

		$productsCategory->setMainCategory($productId, Input::get('main_category'));
	}

	private function getValidator(&$count)
	{
		$rules = [
			'seller_multiple'		=> 'required',
			'product_name'		=> 'required|min:5',
			'main_category'		=> 'required|numeric',
			'categories'		=> 'required',
			'delivery_time'		=> 'required',
			'zone_id'			=> 'required',
			'related_product'	=> 'regex:/^[A-Za-z0-9 ,]+$/',
		];

		$default	= false;
		$prices		= Input::get('price');

		foreach ($prices as $price)
		{
			foreach ($price as $key => $value)
			{
				$field		= '';
				$rule		= '';
				$message	= '';

				switch ($key)
				{
					case 'default':
						$default	= true;
						break;
					case 'label':
						$field		= $key;
						$rule		= 'required';
						$message	= 'The Price Label field is required.';
						break;
					case 'price':
						$field		= $key;
						$rule		= 'required';
						$message	= 'The Price field is required.';
						break;
					case 'qty':
						$field		= $key;
						$rule		= 'required|numeric';
						$message	= 'The Quantity field is required.';
						break;
					case 'p_referral_fees':
						$field		= $key;
						$rule		= 'required';
						$message	= 'The Referral Fees field is required.';
						break;
					case 'p_referral_fees_type':
						$field		= $key;
						$rule		= 'required';
						$message	= 'The Referral Fees Type field is required.';
						break;
				}

				if ( ! empty($field))
				{
					$rules["price.{$count}.{$field}"]				= $rule;
					$messages["price.{$count}.{$field}.required"]	= $message;
				}

				$count++;
			}
		}

		if ( ! $default)
		{
			$rules["price.{$count}.default"]				= 'required';
			$messages["price.{$count}.default.required"]	= 'The default price label is required';
			$count++;
		}

		return Validator::make(Input::all(), $rules, $messages);
	}

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyDelete($product_id) {

        $product = $this->product->find($product_id);

        $product->modify_by = Session::get('username');
        $product->status = 2;
        $product->save();

        $insert_audit = General::audit_trail('ProductController.php', 'delete()', 'Delete Product', Session::get('username'), 'CMS');

        $prices = Price::where('jocom_product_price.product_id', '=', $product->id)
                            ->join('jocom_product_price_seller as seller', 'jocom_product_price.id', '=', 'seller.product_price_id')
                            ->select('jocom_product_price.*', 'seller.seller_id', 'seller.cost_price')
                            ->get();

        foreach ($prices as $price) {
            
            $bases = ProductBaseItem::where('price_option_id', '=', $price->id)
                        ->where('status', '=', 1)
                        ->select('product_base_id')->get();

            $base_ids = array();
            foreach ($bases as $base) {
                array_push($base_ids, $base->product_base_id);
            }
            $base_id = implode(',', $base_ids);

            $producthistory             = new ProductsHistory;
            $producthistory->type       = "Delete Product";
            $producthistory->product_id = $product->id;
            $producthistory->sku = $product->sku;
            $producthistory->name = $product->name;
            $producthistory->prd_status = $product->status;
            $producthistory->price_id = $price->id;
            $producthistory->label = $price->label;
            $producthistory->price = $price->price;
            $producthistory->price_promo = $price->price_promo;
            $producthistory->qty = $price->qty;
            $producthistory->stock = $price->stock;
            $producthistory->stock_unit = $price->stock_unit;
            $producthistory->p_referral_fees = $price->p_referral_fees;
            $producthistory->p_referral_fees_type = $price->p_referral_fees_type;
            $producthistory->default = $price->default==''? $default : $price->default;
            $producthistory->pri_product_id = $product->id;
            $producthistory->pri_status = $price->status;
            $producthistory->p_weight = $price->p_weight;

            $producthistory->updated_by = Session::get('username');
            $producthistory->seller_id = $price->seller_id;
            $producthistory->cost = $price->cost_price;
            $producthistory->base_id = $price->base_id;
            $producthistory->gst_status = $product->gst;

            $producthistory->delivery_time = Input::get('delivery_time');
            $delivery_fees = array();
            foreach (Input::get('zone_id') as $key => $value)
            {
                array_push($delivery_fees, Zone::find(Input::get("zone_id.{$key}"))->name.':'.Input::get("zone_price.{$key}"));
            }
            $delivery_fee = implode(',', $delivery_fees);
            $producthistory->delivery_fee = $delivery_fee;
            $producthistory->save();
            foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_TM' . $producthistory->product_id . '_' . strtoupper($lang)); // Purge API product details cache
        }
        
        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('product');
    }

    /**
     * Show the form for uploading images to product.
     *
     * @return Response
     */
	public function anyUpload($product_id) {

		$product = $this->product->select('*')
									->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
									->where('jocom_products.status', '!=', '2')
									->where('jocom_product_price.default', '=', '1')
									->where('jocom_product_price.status', '=', 1)
									->where('jocom_products.id', '=', $product_id)
									->first();

		$delivery_fees = DB::table('jocom_product_delivery')
									->select('jocom_product_delivery.zone_id','jocom_product_delivery.price', 'jocom_zones.name')
									->leftJoin('jocom_products', 'jocom_products.id', '=', 'jocom_product_delivery.product_id')
									->leftJoin('jocom_zones', 'jocom_zones.id', '=', 'jocom_product_delivery.zone_id')
									->where('jocom_products.id', '=', $product_id)
									->get();

		$category_options = $this->category->orderBy('category_parent', 'asc')->orderBy('category_name', 'asc')->lists('category_name','id');
		$sellers_options = $this->seller->orderBy('id', 'asc')->lists('company_name','id');
		$zone_options = DB::table('jocom_zones')
									->select('jocom_zones.id', 'jocom_zones.name')
									->get();

		return View::make('product.upload')->with(array('product' => $product, 'category_options' => $category_options, 'sellers_options' => $sellers_options, 'zone_options' => $zone_options, 'delivery_fees' => $delivery_fees));

	}

	/**
     * Update the specified resource in storage.
     *
     * @param  int  $product_id
     * @return Response
     */
    
    public function anyUpdatephoto($product_id) {

        // UPDATE PRODUCT TABLE
        $product = $this->product->find($product_id);
        $product->vid_1 = trim(Input::get('product_video'));
        $product->modify_by = Session::get('username');

        $imgFilename = array_fill(1, 4, '');

        for($i = 1; $i <= 3; $i++) {
            if (Input::hasFile("newimage$i")) {
                $image = Input::file("newimage$i");

                $imgFilename[$i] = $product_id . "-img$i-" . time() . '.' . $image->getClientOriginalExtension();
                $image->move('./images/data/', $imgFilename[$i]);
                // Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(640, null, function($constraint) { $constraint->aspectRatio(); })->save();
                Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(320, null, function($constraint) { $constraint->aspectRatio(); })->save('images/data/thumbs/' . $imgFilename[$i]);
                if(preg_match('/[.]([pP][nN][gG]|[jJ][pP][gG]|[jJ][pP][eE][gG])$/i', $imgFilename[$i], $match)){
                    $img_d = (strtolower($match[0]) === '.png' ? imagecreatefrompng('./images/data/' . $imgFilename[$i]) : imagecreatefromjpeg('./images/data/' . $imgFilename[$i]));
                    $img_t = (strtolower($match[0]) === '.png' ? imagecreatefrompng('./images/data/thumbs/' . $imgFilename[$i]) : imagecreatefromjpeg('./images/data/thumbs/' . $imgFilename[$i]));
                    // create the webp image, same path like the DB store except it extension name
                    imagewebp($img_d, './images/data/' . preg_replace('/[.](png|jpg)$/i', '.webp', $imgFilename[$i]), 83);
                    imagewebp($img_t, './images/data/thumbs/' . preg_replace('/[.](png|jpg)$/i', '.webp', $imgFilename[$i]), 83);
                }
            } else {
                $imgFilename[$i] = trim(Input::get("image$i"));
            }

            if ($imgFilename[$i] !== $product->{'img_' . $i}){ // delete image if not match
                File::delete('./images/data/' . $product->{'img_' . $i});
                File::delete('./images/data/thumbs/' . $product->{'img_' . $i});
            }
            $product->{'img_' . $i} = $imgFilename[$i];
        }
        $product->save();

        $insert_audit = General::audit_trail('ProductController.php', 'updatePhoto()', 'Update Product', Session::get('username'), 'CMS');

        Session::flash('message', 'Successfully updated.');
        return Redirect::to('product');
    }
    
	public function anyUpdatephoto_old($product_id) {

		// UPDATE PRODUCT TABLE
		$product = $this->product->find($product_id);
		$product->vid_1 = trim(Input::get('product_video'));
		$product->modify_by = Session::get('username');

		$imgFilename = array_fill(1, 4, '');

		// Image
		$unique = time();
		for($i = 1; $i < 4; $i++) {
			if (Input::hasFile("newimage$i")) {
				$unique = time();
				$image = Input::file("newimage$i");

				$imgFilename[$i] = $product_id . "-img$i-" . $unique . '.' . $image->getClientOriginalExtension();
				$image->move('./images/data/', $imgFilename[$i]);
				Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(640, null, function($constraint) { $constraint->aspectRatio(); })->save();
				Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(320, null, function($constraint) { $constraint->aspectRatio(); })->save('images/data/thumbs/' . $imgFilename[$i]);
			} else {
				$imgFilename[$i] = trim(Input::get("image$i"));
			}
		}
		// END Image

		if ( $imgFilename[1] != $product->img_1)
		{
    		File::delete('./images/data/'.$product->img_1);
    		File::delete('./images/data/thumbs/'.$product->img_1);
		}

		if ( $imgFilename[2] != $product->img_2)
    	{
    		File::delete('./images/data/'.$product->img_2);
    		File::delete('./images/data/thumbs/'.$product->img_2);
		}
    	
    	if ( $imgFilename[3] != $product->img_3)
    	{
    		File::delete('./images/data/'.$product->img_3);
    		File::delete('./images/data/thumbs/'.$product->img_3);
		}

		$product->img_1 = $imgFilename[1];
		$product->img_2 = $imgFilename[2];
		$product->img_3 = $imgFilename[3];
		$product->save();

		$insert_audit = General::audit_trail('ProductController.php', 'updatePhoto()', 'Update Product', Session::get('username'), 'CMS');

		Session::flash('message', 'Successfully updated.');
		return Redirect::to('product');
	}

	private function arrangeCategories(array $categories, $firstLevelPrefix = '', $secondLevelPrefix = '', $thirdLevelPrefix = '')
	{
		foreach ($categories as $key => $category)
		{
			if ($category['category_parent'] == 0 && $category['id'] != 0)
			{
				$greatgrandparent = $category['id'];
				$arranged[] = $category;
				unset($categories[$key]);

				foreach ($categories as $key => $category)
				{
					if ($category['category_parent'] == $greatgrandparent)
					{
						$grandparent = $category['id'];
						$category['category_name'] = $firstLevelPrefix.$category['category_name'];
						$arranged[] = $category;
						unset($categories[$key]);

						foreach ($categories as $key => $category)
						{
							if ($category['category_parent'] == $grandparent)
							{
								$parent = $category['id'];
								$category['category_name'] = $secondLevelPrefix.$category['category_name'];
								$arranged[] = $category;
								unset($categories[$key]);

								foreach ($categories as $key => $category)
								{
									if ($category['category_parent'] == $parent)
									{
										$category['category_name'] = $thirdLevelPrefix.$category['category_name'];
										$arranged[] = $category;
										unset($categories[$key]);
									}
								}
							}
						}
					}
				}
			}
		}

		return $arranged;
	}
        
        
        public function anySellerinfo(){
            
            $seller_id = Input::get('seller_id');
            
            $Seller = Seller::find($seller_id);
            $State = State::find($Seller->state);
            
            return array(
                "seller_id" =>$Seller->id,
                "seller_name" =>$Seller->company_name,
                "seller_state" =>$State->name
            );
            
        }
        
    public function anyProductbase()
    {
       
        return View::make('product.baseproducts');
    }

    public function anyBaseproducts(){
        
        $region = array();
        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        try{

        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }



        if($regionid == 0 || $regionid == 1){

            $region = array(0,1);
        }
        else{
             $region = array($regionid);
        }

       

        $products = Product::leftJoin('jocom_seller','jocom_seller.id','=','jocom_products.sell_id')
                        ->select('jocom_products.id','jocom_seller.company_name','jocom_products.sku','jocom_products.qrcode','jocom_products.name','jocom_products.region_id')
                        ->where('jocom_products.is_base_product','=',1)
                        ->whereIn('jocom_products.region_id',$region);

        // $products = Product::getBaseproducts($regionid);

        return Datatables::of($products)
                        ->edit_column('region_id', function($row)
                            {
                                switch ($row->region_id)
                                {
                                    case 0:
                                        return '<span class="text-success">All Region</span>';
                                        break;
                                    case 1:
                                        return '<span class="text-primary">Malaysia - HQ</span>';
                                        break;
                                    case 2:
                                        return '<span class="text-info">Malaysia - Johor</span>';
                                        break;
                                    case 3:
                                        return '<span class="text-warning">Malaysia - Penang</span>';
                                        break;
                                }
                            })
                        ->add_column('Status', function($row){
                            if(Warehouse::isExists($row->id) == 1){
                                
                                return '<span id="selectItem" class="btn-sm btn-success active" title="Linked">Linked</span>';
                            }
                            else{
                                return '<span id="selectItem" class="btn-sm btn-danger active" title="No Link Found">No Link Found</span>';
                            }


                            })
                        ->make();

        } catch (Exception $ex) {
                echo $ex->getMessage();
            }
    }
    
    public function anyHistory()
    {
       
        return View::make('product.product_history');
    }

    public function anyProducthistory(){

         try{

                $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
                $stateName = array();

                foreach ($SysAdminRegion as  $value) {
                    $regionid = $value;
                }



                if($regionid == 0 || $regionid == 1){

                    $region = array(0,1);
                }
                else{
                     $region = array($regionid);
                }


                $products = ProductsHistory::select('id','type','product_id','name','prd_status','price_id','label','price','price_promo','pri_status','seller_id','cost','created_by','created_at','updated_by','updated_at')
                        ->orderBy('id','DESC');

                  return Datatables::of($products)
                    ->edit_column('prd_status', function($row)
                        {
                            switch ($row->prd_status)
                            {
                                case 1:
                                    return '<span class="label label-success">Active</span>';
                                    break;
                                case 2:
                                    return '<span class="label label-danger">Deleted</span>';
                                    break;
                                default:
                                    return '<span class="label label-warning">Inactive</span>';
                                    break;

                            }
                        })
                    ->edit_column('pri_status', function($row)
                        {
                            switch ($row->pri_status)
                            {
                                case 1:
                                    return '<span class="label label-success">Active</span>';
                                    break;
                                case 2:
                                    return '<span class="label label-danger">Deleted</span>';
                                    break;
                                default:
                                    return '<span class="label label-warning">Inactive</span>';
                                    break;
                                    
                            }
                        })
                    ->add_column('Action', function ($row) {
                            return '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/historydetail/'.$row->id.'"><i class="fa fa-eye"></i></a>';
                        })
                    ->make();   



            } catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }
    
    public function getHistorydetail($id) {
        $history = ProductsHistory::find($id);

        $historys = ProductsHistory::where('price_id', '=', $history->price_id)
                        ->where('seller_id', '=', $history->seller_id)
                        ->orderBy('id', 'asc')
                        ->get();

        for ($i = 0; $i < count($historys); $i++) { 
            if ($historys[$i]->id == $history->id) {
                $previous_history = $historys[$i - 1];
            }
        }

        if ($history->prd_status == 0) {
            $history->prd_status = 'Inactive';
        } else if ($history->prd_status == 1) {
            $history->prd_status = 'Active';
        } else {
            $history->prd_status = 'Deleted';
        }

        if ($history->pri_status == 0) {
            $history->pri_status = 'Inactive';
        } else if ($history->pri_status == 1) {
            $history->pri_status = 'Active';
        } else {
            $history->pri_status = 'Deleted';
        }

        if ($history->type != 'New Product' && count($historys) > 1) {
            if ($previous_history->prd_status == 0) {
                $previous_history->prd_status = 'Inactive';
            } else if ($previous_history->prd_status == 1) {
                $previous_history->prd_status = 'Active';
            } else {
                $previous_history->prd_status = 'Deleted';
            }

            if ($previous_history->pri_status == 0) {
                $previous_history->pri_status = 'Inactive';
            } else if ($previous_history->pri_status == 1) {
                $previous_history->pri_status = 'Active';
            } else {
                $previous_history->pri_status = 'Deleted';
            }
        }


        return View::make('product.product_history_detail')->with(array('history' => $history, 'previous_history' => $previous_history));
    }

    public function anyHistorydetaillist($id) {
        $history = ProductsHistory::find($id);

        $products = ProductsHistory::select('id','type','product_id','name','prd_status','price_id','label','price','price_promo','pri_status','seller_id','cost','created_by','created_at','updated_by','updated_at')
                        ->where('price_id', '=', $history->price_id)
                        ->where('seller_id', '=', $history->seller_id)
                        ->orderBy('id','DESC');

            return Datatables::of($products)
                ->edit_column('prd_status', function($row)
                    {
                        switch ($row->prd_status)
                        {
                            case 1:
                                return '<span class="label label-success">Active</span>';
                                break;
                            case 2:
                                return '<span class="label label-danger">Deleted</span>';
                                break;
                            default:
                                return '<span class="label label-warning">Inactive</span>';
                                break;

                        }
                    })
                ->edit_column('pri_status', function($row)
                    {
                        switch ($row->pri_status)
                        {
                            case 1:
                                return '<span class="label label-success">Active</span>';
                                break;
                            case 2:
                                return '<span class="label label-danger">Deleted</span>';
                                break;
                            default:
                                return '<span class="label label-warning">Inactive</span>';
                                break;
                                
                        }
                    })
                ->add_column('Action', function ($row) {
                        return '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/historydetail/'.$row->id.'"><i class="fa fa-eye"></i></a>';
                    })
                ->make();  
    }
    
    public function anyPrdalert(){

        $result = DB::table('jocom_warehouse_products AS JWP' )
               // ->where(DB::raw('JWP.stockin_hand * 0.2'), '<=', 20)
                ->where('JWP.stockin_hand','!=', 0)
                ->where('JWP.status','=',1)
                ->get();

                foreach ($result as $key => $value) {
                     list($whole,$decimal)  = explode('.', ($value->stockin_hand * 0.2));
                     if($whole >0){
                        print $value->stockin_hand .'-'. ($value->stockin_hand * 0.2) .'-'.$whole.'<br>';
                     }

                }
    }
    
    
     /*
     * Desc : To get final selling price base on 
     */
    public function anyFinalrate(){
        
        try{
            
            $option_id = Input::get("option_id");
            $price_actual = Input::get("price_actual");
            $price_promo = Input::get("price_promo");
            $to_country = Input::get("to_country");

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

            if($to_country > 0 ){
                
                $PriceOptionData = DB::table('jocom_countries AS JC' )
                    ->where('JC.id','=',$to_country)
                    ->first();
                
                $ExchangeRateSub = DB::table('jocom_exchange_rate AS JER' )
                    ->select('JER.*')
                    ->where('JER.currency_code_from','=','USD')
                    ->where('JER.currency_code_to','=',$PriceOptionData->currency)
                    ->first();
                
            }else{
                $ExchangeRateSub = DB::table('jocom_exchange_rate AS JER' )
                         ->select('JER.*')
                         ->where('JER.currency_code_from','=','USD')
                         ->where('JER.currency_code_to','=',$PriceOptionData->currency)
                         ->first();
            }
            

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


     //        $finalCollection[$localRate['currency_code_to']] = $localRate;
     //        $finalCollection[$SubRate['currency_code_to']] = $SubRate;

             $finalCollection['rate'][] = $localRate;
             $finalCollection['rate'][] = $SubRate;

             return Response::json($finalCollection);
       
        } catch (Exception $ex) {
           echo $ex->getMessage();
        }
       
       
       /* REGION CODE */
        
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

 /* Quenny Johor Module */
    public function anyUpdateprice(){

        return View::make('product.updateprice');

    }

    public function anyAddprice(){

        $id = 0;
        $message = 'Price Added';

        try{

            $option_id = Input::get("optionid");

            $Totalrecord = DB::table('jocom_selected_price_update')
            ->where("option_id",$option_id)->count();
            if($Totalrecord  > 0 ){
                $message = "Price is already exist!";
            }else{
                $TotalrecordProduct = DB::table('jocom_product_price AS JPP')
                ->where("JPP.id",$option_id)->count();
                if($TotalrecordProduct  > 0 ){
                    $id = DB::table('jocom_selected_price_update')->insertGetId(
                        ['option_id' => $option_id , 'created_at' => date("Y-m-d h:i:s"), 'status' => 1]
                    );
                }else{
                    $message = "Price option is not exist";
                }
            }

        }catch(exception $ex){
            $message = 'Failed to add';
        }finally{
            return array(
                "id" => $id,
                "message" =>  $message
            );
        }
    }

    public function anyRemoveprice(){

        $id = 0;
        $message = 'Price Removed!';

        try{

            $option_id = Input::get("optionid");

            $Totalrecord = DB::table('jocom_selected_price_update')
            ->where("option_id",$option_id)->count();
            if($Totalrecord  > 0 ){
                $id = DB::table('jocom_selected_price_update')
                ->where("option_id",$option_id)
                ->update(
                    ['updated_at' => date("Y-m-d h:i:s"), 'status' => 0]
                );
            }else{
                $message = "Price is not exist!";
            }

        }catch(exception $ex){
            $message = 'Failed to remove';
        }finally{
            return array(
                "id" => $id,
                "message" =>  $message
            );
        }
    }

    public function anySaveprice(){

        $message = "Price has been updated!";

        try{

            $option_id = Input::get("optionid");
            $price = Input::get("price");
            $price_promo = Input::get("price_promo");

            $Price = Price::find($option_id);
            $Price->price = $price;
            $Price->price_promo = $price_promo ;
            $Price->save();

        }catch(exception $ex){
            $message = "Price failed to update";
        }finally{
            return array(
                "message" => $message 
            );
        }
    }

    public function anyProductupdateprice(){
       
        $price_list = DB::table('jocom_selected_price_update AS JSPU' )
                         ->select('JP.id AS ProductID','JPP.id','JP.sku','JP.name','JPP.label','JPP.price','JPP.price_promo')
                         ->leftJoin('jocom_product_price AS JPP','JPP.id','=','JSPU.option_id')
                         ->leftJoin('jocom_products AS JP','JP.id','=','JPP.product_id')
                         ->where("JSPU.status",1)
                         ->get();

        return $price_list;
    }



    /* Quenny Module */
    /*
        @Desc : Page listing for registered FOC campaign
    */
    public function anyFoc(){
        return View::make('product.product_foc_list');
    }

    public function anyFocinfo(){
        try{

            $focs = DB::table('jocom_foc_reward AS JFR' )
                            ->select('JFR.id'
                            ,'JFR.type'
                            ,'JFR.quantity'
                            ,'JFR.reward_quantity'
                            ,'JFR.balance_quantity'
                            ,'JFR.product_id'
                            ,'JFR.rule'
                            ,'JFR.base_reference'
                            ,'JFR.start_date'
                            ,'JFR.end_date'
                            ,'JFR.region'
                            ,'JFR.seller_id'
                            ,'JFR.target_product_id'
                            ,'JFR.created_at'
                            ,'JFR.activation'
                            ,'JP1.name AS ProductNameFOC'
                            ,'JP2.name AS ProductNameFOCDepend')
                            ->leftJoin('jocom_products AS JP1','JP1.id','=','JFR.product_id')
                            ->leftJoin('jocom_products AS JP2','JP2.id','=','JFR.target_product_id');
            

            return Datatables::of($focs)
                ->edit_column('region', function($row)
                    {
                        $list_of_state_ids = explode(',',$row->region);
                        $list = DB::table('jocom_country_states')
                        ->select('name')
                        ->whereIn('id', $list_of_state_ids )->get();

                        return $list;
                    })
                ->make(true);

        }catch(exception $ex){
         
            echo $ex->getMessage;
        }
        //return $focs;
    }

    public function anyCreatefoc(){
        return View::make('product.product_foc_create');
    }

    public function anyEditfoc(){
        return View::make('product.product_foc_edit');
    }
    // getEdit($productId)
    public function getFocdetails($id){
     
        $foc = DB::table('jocom_foc_reward AS JFR')
            ->select('JFR.*','JP1.name AS ProductNameFOC','JP2.name AS ProductNameFOCDepend')
            ->leftJoin('jocom_products AS JP1','JP1.id','=','JFR.product_id')
            ->leftJoin('jocom_products AS JP2','JP2.id','=','JFR.target_product_id')
            ->where('JFR.id',$id)
            ->first();

        $statesIds = explode(",",$foc->region);

        $states = DB::table('jocom_country_states AS JCS')
            ->select('JCS.name AS state','JCS.id')
            ->whereIn('JCS.id',$statesIds)
            ->get();

        $collectionData = array(
            "info" => $foc,
            "states"=> $states
        );

        return Response::json($collectionData);
    }

    public function getSeller(){

        $sellers = DB::table('jocom_seller AS JS')
            ->select('JS.username','JS.company_name','JS.id')
            ->where('JS.active_status',1)
            ->get();

        return $sellers;

    }

    public function getStates(){

        $states = DB::table('jocom_country_states AS JCS')
            ->select('JCS.name','JCS.id')
            ->where('JCS.status',1)
            ->where('JCS.country_id',458)
            ->get();

        return $states;

    }

    public function anyInfo(){
        
        $productid = Input::get('id');
        $product = DB::table('jocom_products AS JP')
            ->select('JP.name','JP.id')
            ->where('JP.id',$productid)
            ->first();

        if($product) {
            return array(
                "product_id" => $product->id,
                "product_name" => $product->name
            );
        }

        return array();

        

    }

    /*
        @Desc : Page listing for registered FOC campaign
    */
    public function anySavefoc(){

        $message = '';
        $isError = false;
        $isErrorCode = 1;

        try{

            DB::beginTransaction();

         

            $start_date = Input::get('start_date')." 00:00:00" ;
            $end_date = Input::get('end_date')." 23:59:59" ;;
            $rule_code = Input::get('rule_code');
            $foc_product_id = Input::get('foc_product_id');
            $foc_limit_qty = Input::get('foc_limit_qty');
            $foc_qty = Input::get('foc_qty');
            $depend_product_id = Input::get('depend_product_id');
            $sales_amount = Input::get('sales_amount');
            $seller_id = Input::get('seller');
            //$country_id = Input::get('country'); // TODO : Add in database table
            $state_ids = Input::get('states');
            $isActive = Input::get('isActive') ? 1 :0 ;
        
            $FocReward = new FocReward;
            $FocReward->type = 'FOC';
            $FocReward->quantity = $foc_limit_qty;
            $FocReward->reward_quantity = $foc_qty;
            $FocReward->balance_quantity = $foc_limit_qty;
            $FocReward->product_id = $foc_product_id;
            $FocReward->rule = $rule_code;
            $FocReward->base_reference = $sales_amount;
            $FocReward->start_date = $start_date;
            $FocReward->end_date = $end_date;
            $FocReward->region = $state_ids;
            $FocReward->seller_id = $seller_id;
            $FocReward->target_product_id = $depend_product_id;
            $FocReward->activation = $isActive;
            $FocReward->save();

            if($FocReward->save()){
                $message = 'Save successfully';
            }

        }catch(exception $ex){
            $isError = true;
            $isErrorCode = 0;
            $message = $ex->getMessage();
        }finally{

            if($isError){
                // Rollback save / update
                DB::rollBack();
            }else{
                // Commit save data
                DB::commit();
            }
            return array(
                'message' => $message,
                'isErrorCode' => $isErrorCode,
                'isError' => $isError,
            );
        }
        
    }

    public function anyUpdatefoc(){

        $message = '';
        $isError = false;
        $isErrorCode = 1;

        try{

            DB::beginTransaction();

            $id = Input::get('id');
            $start_date = Input::get('start_date')." 00:00:00" ;
            $end_date = Input::get('end_date')." 23:59:59" ;;
            $rule_code = Input::get('rule_code');
            $foc_product_id = Input::get('foc_product_id');
            $foc_limit_qty = Input::get('foc_limit_qty');
            $foc_qty = Input::get('foc_qty');
            $depend_product_id = Input::get('depend_product_id');
            $sales_amount = Input::get('sales_amount');
            $seller_id = Input::get('seller');
            //$country_id = Input::get('country'); // TODO : Add in database table
            $state_ids = Input::get('states');
            $isActive = Input::get('isActive') ? 1 :0 ;
        
            $FocReward = FocReward::find($id);
            
            // Diff Previous Allocation 
            if($foc_limit_qty != $FocReward->quantity){
                $newBalance = $foc_limit_qty - ($FocReward->quantity - $FocReward->balance_quantity );
            }else{
                $newBalance =  $FocReward->balance_quantity;
            }
            // Diff Previous Allocation 

            $FocReward->type = 'FOC';
            $FocReward->quantity = $foc_limit_qty;
            $FocReward->reward_quantity = $foc_qty;
            $FocReward->balance_quantity = $newBalance;
            $FocReward->product_id = $foc_product_id;
            $FocReward->rule = $rule_code;
            $FocReward->base_reference = $sales_amount;
            $FocReward->start_date = $start_date;
            $FocReward->end_date = $end_date;
            $FocReward->region = $state_ids;
            $FocReward->seller_id = $seller_id;
            $FocReward->target_product_id = $depend_product_id;
            $FocReward->activation = $isActive;
            $FocReward->save();

            if($FocReward->save()){
                $message = 'Save successfully';
            }

        }catch(exception $ex){
            $isError = true;
            $isErrorCode = 0;
            $message = $ex->getMessage();
        }finally{

            if($isError){
                // Rollback save / update
                DB::rollBack();
            }else{
                // Commit save data
                DB::commit();
            }
            return array(
                'message' => $message,
                'isErrorCode' => $isErrorCode,
                'isError' => $isError,
            );
        }
        
    }

    public function anyScheduleajax() {
        $price_id = Input::get('price_id');

        $price_schedule = ProductPriceSchedule::where('product_price_id', '=', $price_id)
                                ->where('date', '>', Carbon\Carbon::now())
                                ->orderBy('date')
                                ->get();

        return View::make('product.schedule_list', ['price_id' => $price_id,'price_schedule' => $price_schedule]);
    }

    public function anyCreateschedule() {

        $price_id = Input::get('price_id');
        $price = Input::get('price');
        $price_promo = Input::get('price_promo');
        $cost = Input::get('cost');
        $date = Input::get('date') . ' 00:00:00';

        $exist_schedule = ProductPriceSchedule::where('product_price_id', '=', $price_id)
                                ->where('date', '=', $date)->first();
        if ($exist_schedule != null) {
            return array( "message" => 'Schedule ID - ' . $exist_schedule->id . ' have the same date. Please update.');
        }

        $price_schedule = new ProductPriceSchedule;

        $price_schedule->product_id = Price::find($price_id)->product_id;
        $price_schedule->product_price_id = $price_id;
        $price_schedule->price = ($price != '') ? $price : null;
        $price_schedule->price_promo = ($price_promo != '') ? $price_promo : null;
        $price_schedule->cost = ($cost != '') ? $cost : null;
        $price_schedule->date = ($date != '') ? $date : null;
        $price_schedule->save();

        return array('id' => $price_schedule->id, "message" => 'Schedule ID - ' . $price_schedule->id . ' created successfully.');
    }

    public function anyUpdateschedule() {
        $id = Input::get('id');
        $price = Input::get('price');
        $price_promo = Input::get('price_promo');
        $cost = Input::get('cost');
        $date = Input::get('date') . ' 00:00:00';

        $price_id = ProductPriceSchedule::find($id)->product_price_id;
        $exist_schedule = ProductPriceSchedule::where('product_price_id', '=', $price_id)
                                ->where('date', '=', $date)->first();
        if ($exist_schedule != null) {
            return array( "message" => 'Schedule ID - ' . $exist_schedule->id . ' have the same date. Please update.');
        }

        $price_schedule = ProductPriceSchedule::find($id);
        $price_schedule->price = ($price != '') ? $price : null;
        $price_schedule->price_promo = ($price_promo != '') ? $price_promo : null;
        $price_schedule->cost = ($cost != '') ? $cost : null;
        $price_schedule->date = ($date != '') ? $date : null;
        $price_schedule->save();

        return array("message" => 'Schedule ID - ' . $id . ' updated successfully.');
    }

    public function anyDeleteschedule() {
        $id = Input::get('id');
        $price_schedule = ProductPriceSchedule::find($id);
        $price_schedule->delete();

        return array("message" => 'Schedule ID - ' . $id . ' deleted successfully.');
    }
    
    public function anyBulkeditstatus()
    {
        $sellers = [];

        foreach (Seller::alphabeticalOrder()->get() as $seller) {
            $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_bulkedit_status', ['sellers' => $sellers, 'name' => Input::get('name'), 'seller' => Input::get('seller'), 'category' => Input::get('category'), 'status' => Input::get('status')]);
    }

    public function getProductstatus()
    {
        if (Input::get('name')) {
            $input['name'] = Input::get('name');
        }

        if (Input::get('seller')) {
            $input['seller'] = Input::get('seller');
        }

        if (Input::get('category')) {
            $input['category'] = Input::get('category');
        }

        if (Input::get('status')) {
            $input['status'] = Input::get('status');
        }
                
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
  
        if ( ! empty($input)) {
            $products = DB::table('jocom_products')
                ->select(
                    'jocom_products.id AS id',
                    'jocom_products.sku AS sku',
                    'jocom_products.name AS name',
                    DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"),
                    'jocom_products.status AS status',
                    'jocom_seller.company_name AS company_name'
                    
                )
                ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
                ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                ->where('jocom_product_price.default', '=', 1)
                ->where('jocom_product_price.status', '=', 1)
                ->where('jocom_products.status', '<>', 2)
                ->groupBy('jocom_products.id');

            if ( ! empty($input['name'])) {
                            $products = $products->where('jocom_products.name', 'LIKE', "%{$input['name']}%");
            }
                        
            if($SysAdminRegion->region_id != 0){
                $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
            }

            if ( ! empty($input['seller']) && $input['seller'] != 'any') {
                $products = $products->where('jocom_products.sell_id', '=', substr($input['seller'], 1));
            }

            if ( ! empty($input['category'])) {
                $products = $products->where('category_name', 'LIKE', "%{$input['category']}%");
            }
            
            if ( ! empty($input['status']) && $input['status'] != 'any') {
                switch ($input['status']) {
                    case 'active':
                        $products = $products->where('jocom_products.status', '=', 1);
                        break;
                    case 'inactive':
                        $products = $products->where('jocom_products.status', '=', 0);
                        break;
                }
            }
        } else {                
                        
            $products = DB::table('jocom_products')
                ->select(
                    'jocom_products.id AS id',
                    'jocom_products.sku AS sku',
                    'jocom_products.name AS name',
                    DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"),
                    'jocom_products.status AS status',
                    'jocom_seller.company_name AS company_name'
                )
                ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
                ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                                
                                
                ->where('jocom_product_price.default', '=', 1)
                ->where('jocom_product_price.status', '=', 1)
                ->where('jocom_products.status', '<>', 2)
                ->groupBy('jocom_products.id');
                        
                if($SysAdminRegion->region_id != 0){
                    if($SysAdminRegion->region_id == 4){
                        $products = $products->where('jocom_products.category', 'like', '%' . 949 . '%');
                    }else{
                        $products = $products->where('region_id', $SysAdminRegion->region_id);
                    }
                }
                        
        }
        
        return Datatables::of($products)
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
            ->add_column('Action', function($row)
            {
                $checked = '';

                if ($row->status == 1) {
                    $checked = ' checked';
                }

                $edit = '<label class="switch">
                          <input product-id="'.$row->id.'" class="toggle" type="checkbox"'. $checked .'>
                          <span class="slider round"></span>
                        </label>';

                return $edit;
            })
            ->make();
    }

    public function postEnable($product_id) {
        $result =  DB::table('jocom_products')->where('id', '=', $product_id)
                    ->update(['status' => 1]);

        if ($result != 1) {
            return ['status' => 'Error'];
        }

        return ['status' => 'Active'];
    }

    public function postDisable($product_id) {
        $result =  DB::table('jocom_products')->where('id', '=', $product_id)
                    ->update(['status' => 0]);

        if ($result != 1) {
            return ['status' => 'Error'];
        }

        return ['status' => 'Inactive'];
    }

    public function postEnableselected() {
        $data = Input::get('data');

        $product_ids = explode(",", $data);

        $result = DB::table('jocom_products')
                    ->whereIn('id', $product_ids)
                    ->update(['status' => 1]);

        return $result;
    }

    public function postDisableselected() {
        $data = Input::get('data');

        $product_ids = explode(",", $data);

        $result = DB::table('jocom_products')
                    ->whereIn('id', $product_ids)
                    ->update(['status' => 0]);

        return $result;
    }

    public function postEnableall() {
        $name = Input::get('loaded_name');
        $seller = Input::get('loaded_seller');
        $category = Input::get('loaded_category');
        $status = Input::get('loaded_status');

        $products = DB::table('jocom_products')
                    ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
                    ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                    ->select(DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"));

        if (!empty($name)) {
            $products = $products->where('jocom_products.name', 'LIKE', "%{$name}%");
        }
                    
        if (!empty($seller) && $seller != 'any') {
            $products = $products->where('jocom_products.sell_id', '=', substr($seller, 1));
        }

        if (!empty($category)) {
            $products = $products->where('category_name', 'LIKE', "%{$category}%");
        }
        
        if (!empty($status) && $status != 'any') {
            switch ($status) {
                case 'active':
                    $products = $products->where('jocom_products.status', '=', 1);
                    break;
                case 'inactive':
                    $products = $products->where('jocom_products.status', '=', 0);
                    break;
            }
        }

        $result = $products->update(['jocom_products.status' => 1]);
        return $result;

    }

    public function postDisableall() {
        $name = Input::get('loaded_name');
        $seller = Input::get('loaded_seller');
        $category = Input::get('loaded_category');
        $status = Input::get('loaded_status');

        $products = DB::table('jocom_products')
                    ->leftJoin('jocom_categories', 'jocom_products.id', '=', 'jocom_categories.product_id')
                    ->leftJoin('jocom_products_category', 'jocom_categories.category_id', '=', 'jocom_products_category.id')
                    ->select(DB::raw("group_concat(distinct jocom_products_category.category_name separator ', ') AS category_name"));

        if (!empty($name)) {
            $products = $products->where('jocom_products.name', 'LIKE', "%{$name}%");
        }
                    
        if (!empty($seller) && $seller != 'any') {
            $products = $products->where('jocom_products.sell_id', '=', substr($seller, 1));
        }

        if (!empty($category)) {
            $products = $products->where('category_name', 'LIKE', "%{$category}%");
        }
        
        if (!empty($status) && $status != 'any') {
            switch ($status) {
                case 'active':
                    $products = $products->where('jocom_products.status', '=', 1);
                    break;
                case 'inactive':
                    $products = $products->where('jocom_products.status', '=', 0);
                    break;
            }
        }

        $result = $products->update(['jocom_products.status' => 0]);
        return $result;
        
    }

    public function anyBulkeditquantity()
    {
        $sellers = [];

        foreach (Seller::alphabeticalOrder()->get() as $seller) {
            $sellers["s{$seller->id}"] = $seller->company_name;
        }

        return View::make('product.product_bulkedit_quantity', ['sellers' => $sellers, 'seller' => Input::get('seller'), 'seller_name' => $sellers[Input::get("seller")], 'category' => Input::get('category')]);
    }

    public function getProductsquantity()
    {
        if (Input::get('seller')) {
            $input['seller'] = Input::get('seller');
        }

        if (Input::get('category')) {
            $input['category'] = Input::get('category');
        }

        $categorys = explode(',', $input['category']);
                
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                ->where("status",1)->first();
  
        if ( ! empty($input)) {
            $products = DB::table('jocom_product_price')
                ->select(
                    'jocom_product_price.id AS id',
                    'jocom_products.sku AS sku',
                    'jocom_products.name AS name',
                    'jocom_products.category',
                    'jocom_product_price.label as label',
                    'jocom_product_price.qty as qty'
                )
                ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
                ->where('jocom_product_price.status', '=', 1)
                ->where('jocom_products.status', '<>', 2)
                ->groupBy('jocom_product_price.id');

            if ( ! empty($input['name'])) {
                            $products = $products->where('jocom_products.name', 'LIKE', "%{$input['name']}%");
            }
                        
            if($SysAdminRegion->region_id != 0){
                $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
            }

            if ( ! empty($input['seller']) && $input['seller'] != 'any') {
                $products = $products->where('jocom_products.sell_id', '=', substr($input['seller'], 1));
            }

            if ( ! empty($input['category'])) {
                $products = $products->where(function($q) use ($categorys) {
                    for ($i = 0; $i < count($categorys); $i++) {
                        if ($i == 0) {
                            $q->where('category', 'LIKE', "%{$categorys[$i]}%");
                        } else {
                            $q->orWhere('category', 'LIKE', "%{$categorys[$i]}%");
                        }
                        
                    }
                });
                
            }
            
            if ( ! empty($input['status']) && $input['status'] != 'any') {
                switch ($input['status']) {
                    case 'active':
                        $products = $products->where('jocom_products.status', '=', 1);
                        break;
                    case 'inactive':
                        $products = $products->where('jocom_products.status', '=', 0);
                        break;
                }
            }
        } else {                
                        
            $products = DB::table('jocom_product_price')
                ->select(
                    'jocom_product_price.id AS id',
                    'jocom_products.sku AS sku',
                    'jocom_products.name AS name',
                    'jocom_products.category',
                    'jocom_product_price.label as label',
                    'jocom_product_price.qty as qty'
                )
                ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
                ->where('jocom_product_price.status', '=', 1)
                ->where('jocom_products.status', '<>', 2)
                ->groupBy('jocom_product_price.id');
                        
                if($SysAdminRegion->region_id != 0){
                    if($SysAdminRegion->region_id == 4){
                        $products = $products->where('jocom_products.category', 'like', '%' . 949 . '%');
                    }else{
                        $products = $products->where('region_id', $SysAdminRegion->region_id);
                    }
                }
        }
        
        return Datatables::of($products)
            ->edit_column('qty', function($row)
            {
                return '<input class="form-control" id="'.$row->id.'" value="'.$row->qty.'" type="number" />';
            })
            ->make();
    }

    public function postAmendselected() {
        $price_id = Input::get('price_id');
        $qty = Input::get('qty');

        $price_ids = explode(',', $price_id);
        $qtys = explode(',', $qty);

        $isError = false;
        $message = 'Success';
        
        try{
            
            DB::beginTransaction();

            $price_quantity = new ProductPriceQuantity;
            $price_quantity->status = 'amended';
            $price_quantity->save();

            $details = [];

            for ($i = 0; $i < count($price_ids); $i++) {
                $price = Price::find($price_ids[$i]);

                array_push($details, [
                    'quantity_id' => $price_quantity->id,
                    'price_id' => $price->id,
                    'original_qty' => $price->qty,
                ]);
                
                $price->qty = $qtys[$i];
                $price->save();
            }

            DB::table('jocom_product_price_quantity_details')->insert($details);

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage();
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }

    }

    public function postAmendall() {
        $seller = Input::get('seller');
        $category = Input::get('category');
        $quantity = Input::get('quantity');
        $option = Input::get('option');

        $categorys = explode(',', $category);
        $seller_name = '';

        $products = DB::table('jocom_product_price')
                    ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
                    ->select('jocom_product_price.id', 'jocom_product_price.qty')
                    ->where('jocom_product_price.status', '=', 1)
                    ->where('jocom_products.status', '<>', 2);
                    
        if (!empty($seller) && $seller != 'any') {
            $products = $products->where('jocom_products.sell_id', '=', substr($seller, 1));
            $seller_name = DB::table('jocom_seller')->where('id', '=', substr($seller, 1))
                            ->select('company_name')->first()->company_name;
        }

        if (!empty($category)) {
            $products = $products->where(function($q) use ($categorys) {
                for ($i = 0; $i < count($categorys); $i++) {
                    if ($i == 0) {
                        $q->where('category', 'LIKE', "%{$categorys[$i]}%");
                    } else {
                        $q->orWhere('category', 'LIKE', "%{$categorys[$i]}%");
                    }
                    
                }
            });
        }

        $prices = $products->get();

        $isError = false;
        $message = 'Success';
        
        try{
            
            DB::beginTransaction();

            $price_quantity = new ProductPriceQuantity;
            $price_quantity->status = 'amended';
            $price_quantity->seller = $seller_name;
            $price_quantity->category = $category;
            $price_quantity->save();

            $details = [];

            foreach ($prices as $price) {
                array_push($details, [
                    'quantity_id' => $price_quantity->id,
                    'price_id' => $price->id,
                    'original_qty' => $price->qty,
                ]);

                if ($option == 'append') {
                    DB::table('jocom_product_price')->where('id', '=', $price->id)
                        ->update(['qty' => $price->qty + $quantity]);
                }
            }

            if ($option == 'update') {
                $products->update(['jocom_product_price.qty' => $quantity]);
            }
            

            DB::table('jocom_product_price_quantity_details')->insert($details);

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage();
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }

    }

    public function getRevokelist() {
        $price_quantity = DB::table('jocom_product_price_quantity')
                            ->select('id', 'created_at', 'seller', 'category')
                            ->where('status', '!=', 'revoked');

        return Datatables::of($price_quantity)
            ->edit_column('created_at', function($row) 
            {
                return date_format(date_create($row->created_at), 'Y-m-d');
            })
            ->add_column('time', function($row)
            {
                return date_format(date_create($row->created_at), 'H:i:s');
            })
            ->add_column('action', function($row)
            {
                return '<button class="btn btn-primary revokeBtn" id="rev'.$row->id.'">Revoke</button>';
            })
            ->remove_column('id')
            ->make();
    }

    public function postRevokeall() {
        $quantity_id = Input::get('quantity_id');

        $isError = false;
        $message = 'Success';
        
        try{
            
            DB::beginTransaction();

            $price_quantity = ProductPriceQuantity::find($quantity_id);
            $price_quantity->status = 'revoked';
            $price_quantity->save();

            $details = DB::table('jocom_product_price_quantity_details')
                        ->where('quantity_id', '=', $quantity_id)
                        ->get();

            foreach ($details as $detail) {
                DB::table('jocom_product_price')->where('id', '=', $detail->price_id)
                    ->update(['qty' => $detail->original_qty]);
            }

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage();
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }
        
    }
    
    public function deductWarehouseActualstock($product_id, $price_id, $actual_stock, $old_actual_stock) {

        $stock_difference = abs($actual_stock - $old_actual_stock);

        $base_items = DB::table('jocom_product_base_item AS bi')
                        ->where("bi.product_id", $product_id)
                        ->where("bi.price_option_id", $price_id)
                        ->where("bi.status", 1)
                        ->select('bi.product_base_id', 'bi.quantity')
                        ->get();

        if (count($base_items) > 0) {
            foreach ($base_items as $base_item) {
                $this->manageActualstock($base_item->product_base_id, $stock_difference * $base_item->quantity, 'decrease');
            }
        } else {
            $this->manageActualstock($product_id, $stock_difference, 'decrease');
        }

        $this->log_actualstockout($product_id, $price_id, $stock_difference);
    }

    public static function manageActualstock($product_id, $quantity, $action) {

        $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $product_id)
                                ->select('stockin_hand', 'actual_stock', 'actual_stock_migrated')
                                ->first();

        // 
        if ($current_stock->actual_stock_migrated == 0) {
            $actual_stock = $current_stock->stockin_hand;
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $product_id)
                ->update(['actual_stock' => $actual_stock, 'actual_stock_migrated' => 1]);
        } else {
            $actual_stock = $current_stock->actual_stock;
        }

        $product_link = DB::table('jocom_warehouse_productslinks')
                            ->where('product_id', '=', $product_id)
                            ->select('parent_product_id', 'quantity')
                            ->first();
            // print_r($product_link);
            // die();
        if ($product_link != null && $product_link->quantity > 0) {
            $parent_product_id = $product_link->parent_product_id;
            
            $carton = intval($quantity / $product_link->quantity);
            $loosed = $quantity % $product_link->quantity;

            if ($action == 'increase') {
                ProductController::increaseLooseItem($product_id, $actual_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);

                ProductController::resetActualstock($product_id);
                ProductController::resetActualstock($parent_product_id);
            } else { // action == 'decrease'
                ProductController::decreaseLooseItem($product_id, $actual_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);
            }
            
        } else {
            $query = DB::table('jocom_warehouse_products')
                        ->where('product_id', '=', $product_id);

            if ($action == 'increase') {
                $query->increment('actual_stock', $quantity);
                ProductController::resetActualstock($product_id);
            } else { // action == 'decrease'
                $query->decrement('actual_stock', $quantity);
            }
        }
         
        
    }

    private function increaseLooseItem($product_id, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {

            $loose = $loosed + $product_stock;
            if ($loose >= $carton_size) {
                $loose = $loose % $carton_size;
                $carton = $carton + 1;
            }

            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $product_id)
                ->update(['actual_stock' => $loose]);
        }

        $parent_product = DB::table('jocom_warehouse_products')
                             ->where('product_id', '=', $parent_product_id)
                             ->select('stockin_hand', 'actual_stock', 'actual_stock_migrated')
                             ->first();

        if ($parent_product->actual_stock_migrated == 0) {
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $parent_product_id)
                ->update(['actual_stock' => $parent_product->stockin_hand + $carton,
                          'actual_stock_migrated' => 1]);
        } else {
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $parent_product_id)
                ->increment('actual_stock', $carton);
        }
    }

    private function resetActualstock($product_id) {
        $price_ids = DB::table('jocom_product_base_item')
                        ->where('product_base_id', '=', $product_id)
                        ->where('status', '=', 1)
                        ->select('price_option_id')
                        ->get();

        foreach ($price_ids as $price_id) {
            DB::table('jocom_product_price')
                ->where('id', '=', $price_id->price_option_id)
                ->update(['stock' => 0]);
        }
    }

    private function decreaseLooseItem($product_id, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {
            if ($loosed > $product_stock) {
                $loose = $carton_size - $loosed + $product_stock;
                $carton = $carton + 1;
            } else {
                $loose = $product_stock - $loosed;
            }

            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $product_id)
                ->update(['actual_stock' => $loose]);
        } 

        $parent_product = DB::table('jocom_warehouse_products')
                             ->where('product_id', '=', $parent_product_id)
                             ->select('stockin_hand', 'actual_stock', 'actual_stock_migrated')
                             ->first();

        if ($parent_product->actual_stock_migrated == 0) {
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $parent_product_id)
                ->update(['actual_stock' => $parent_product->stockin_hand - $carton,
                          'actual_stock_migrated' => 1]);
        } else {
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $parent_product_id)
                ->decrement('actual_stock', $carton);
        }
    }
    
    public static function log_actualstockin($product_id, $price_id, $quantity) {
        if ($quantity > 0) {
            DB::table('jocom_actualstockin_log')->insert([
                'product_id' => $product_id,
                'price_id' => $price_id,
                'quantity' => $quantity,
                'created_by' => Session::get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public static function log_actualstockout($product_id, $price_id, $quantity, $file_name = "") {
        if ($quantity > 0) {
            DB::table('jocom_actualstockout_log')->insert([
                'product_id' => $product_id,
                'price_id' => $price_id,
                'quantity' => $quantity,
                'file_name' => $file_name,
                'created_by' => Session::get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function anyBulkeditactualstock() {
        $sizes = DB::table('jocom_warehouse_stock_size')->get();

        return View::make('product.bulkedit_actualstock')->with(['sizes' => $sizes]);
    }

    public function getActualstockoutlist() {
        $stockout = DB::table('jocom_actualstockout_log')
                        ->join('jocom_products', 'jocom_actualstockout_log.product_id', '=', 'jocom_products.id')
                        ->join('jocom_product_price', 'jocom_actualstockout_log.price_id', '=', 'jocom_product_price.id')
                        ->select('jocom_actualstockout_log.id', 'jocom_actualstockout_log.product_id', 'jocom_products.name', 'price_id', 'jocom_product_price.label', 'quantity', 'jocom_actualstockout_log.created_by', 'jocom_actualstockout_log.created_at');
        return Datatables::of($stockout)->make();
    }

    public function postUploadactualstockexcel() {

        if (Input::hasFile('csv_file')) {

            $csv_file = Input::file('csv_file');

            $destinationPath = 'media/csv/actual_stock';
            $file_name = 'stockout_' . time() . '.csv';
            $filepath = $destinationPath . '/' . $file_name;
            $csv_file->move($destinationPath, $file_name);
            $file = fopen($filepath, 'r');

            $isError = false;

            try {
                DB::beginTransaction();

                $data = fgetcsv($file, 1400, ",");
                while (($data = fgetcsv($file, 1400, ",")) !== FALSE) {

                    $product_id = $data[0];
                    $price_id = $data[1];
                    $quantity = $data[2];

                    DB::table('jocom_product_price')->where('id', '=', $price_id)->decrement('stock', $quantity);

                    $base_items = DB::table('jocom_product_base_item AS bi')
                            ->where("bi.product_id", $product_id)
                            ->where("bi.price_option_id", $price_id)
                            ->where("bi.status", 1)
                            ->select('bi.product_base_id', 'bi.quantity')
                            ->get();

                    if (count($base_items) > 0) {
                        foreach ($base_items as $base_item) {
                            $this->manageActualstock($base_item->product_base_id, $quantity * $base_item->quantity, 'decrease');
                        }
                    } else {
                        $this->manageActualstock($product_id, $quantity, 'decrease');
                    }

                    $this->log_actualstockout($product_id, $price_id, $quantity, $file_name);
                }

            } catch(Exception $ex) {
                $isError = true;
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Response::json(array('status' => 400, 'message' => 'Error'));
                } else {
                    DB::commit();
                    return Response::json(array('status' => 200, 'message' => 'Upload success'));
                }
            }

        }
    }
     public function anyCostprice()
    {
        $sell   = Seller::all();
        $node=[];
        foreach ($sell as $value) {
            $node[]=array('id'=>$value->id,'name'=>$value->company_name);
        }
         
        return View::make('product.cost_price', ['seller' => $node]);
    }
    public function anyCostnode()
    {
            
        $listing = DB::table('jocom_products AS products')
             ->select('products.id','products.sku','products.name','seller.company_name as seller_name','seller_price.cost_price as price')
            ->join('jocom_seller as seller', 'products.sell_id', '=', 'seller.id')
            ->join('jocom_product_price as product_price', 'products.id', '=', 'product_price.product_id')
            ->join('jocom_product_price_seller as seller_price', 'product_price.id', '=', 'seller_price.product_price_id')
            ->where('products.is_base_product', '=', '1')
            ->where('seller_price.activation','=','1')
            ->where('product_price.status','=','1')
            ->where('products.status','<>','2')
            ->groupby('products.id');

        $vendor     = Input::get('vendor');
        $name =Input::get('name');
        $product_id =Input::get('product_id');
        
        if(!empty($product_id) && !empty($name)){
        Redirect::back()->with('message', 'Please Fill only one filter in Product');
              
        }
        if (isset($vendor) && ! empty($vendor) && $vendor!='all') {

            $listing = $listing->where('seller.id', '=',$vendor);
                   
        }
        if (!empty($name)) {
            $listing = $listing->where('products.name', 'LIKE', "%{$name}%");
        }
        if (!empty($product_id)) {
            $listing = $listing->where('products.id', '=',$product_id);
        } 
        
      
        return Datatables::of($listing)
        ->add_column('Inventory', function ($row) {
                      $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $row->id)
                                ->select('stockin_hand')
                                ->first();
                             
                    return $current_stock->stockin_hand;
                    })
        ->add_column('total', function ($row) {
                          $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $row->id)
                                ->select('stockin_hand')
                                ->first();
                      $total=$current_stock->stockin_hand*$row->price;
                      return $total;
                    })
        ->make();
    }
      public function anyCostreport()
    {
       $vendor=Input::get('vendor');

           if(empty($vendor)){
                    $vendor='all';

            }
       $listing = DB::table('jocom_products AS products')
             ->select('products.id','products.sku','products.name','seller.id as seller_id','seller.company_name as seller_name','seller_price.cost_price as price')
            ->join('jocom_seller as seller', 'products.sell_id', '=', 'seller.id')
            ->join('jocom_product_price as product_price', 'products.id', '=', 'product_price.product_id')
            ->join('jocom_product_price_seller as seller_price', 'product_price.id', '=', 'seller_price.product_price_id')
            ->where('products.is_base_product', '=', '1')
            ->where('seller_price.activation','=','1')
            ->where('product_price.status','=','1')
            ->where('products.status','<>','2')
            ->groupby('products.id');
            
        $name =Input::get('name');
        $product_id =Input::get('product_id');
        
        if(!empty($product_id) && !empty($name)){
        Redirect::back()->with('message', 'Please Fill only one filter in Product');
              
        }
        if (isset($vendor) && ! empty($vendor) && $vendor!='all') {

            $listing = $listing->where('seller.id', '=',$vendor);
                   
        }
        if (!empty($name)) {
            $listing = $listing->where('products.name', 'LIKE', "%{$name}%");
        }
        if (!empty($product_id)) {
            $listing = $listing->where('products.id', '=',$product_id);
        }
        $listing = $listing->get();
           if(empty($listing)){
            return Redirect::back()->with('message', 'No Data Found');
            } 
     $data_array[] = array('NO','PRODUCT ID','SKU','PRODUCT NAME','SELLER ID','SELLER NAME','QUANTITY','COST PRICE','TOTAL');
     $i=1;
     foreach ($listing as $value) {
        $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $value->id)
                                ->select('stockin_hand')
                                ->first();
        $totals=$current_stock->stockin_hand*$value->price;
     $data_array[] = array(
                    'NO'=>$i,
                    'PRODUCT ID'=>$value->id,
                    'SKU'=>$value->sku,
                    'PRODUCT NAME'=>$value->name,
                    'SELLER ID'=>$value->seller_id,
                    'SELLER NAME'=>$value->seller_name,
                    'QUANTITY'=>$current_stock->stockin_hand,
                    'COST PRICE'=>$value->price,
                    'TOTAL'=>$totals
                    );
     $i++;
 }                     
          if($vendor!='all'){
           $seller_name = DB::table('jocom_seller as seller')
                     ->where('seller.id', '=',$vendor)
                     ->select('seller.company_name as seller_name')
                     ->get();
           $vendor=$seller_name[0]->seller_name;
           }

   return Excel::create('SELLER COST PRICE', function($excel) use ($data_array,$vendor) {

            $excel->sheet('SELLER COST PRICE', function($sheet) use ($data_array,$vendor){
                $sheet->cell('A1', function($cell) {$cell->setValue('SELLER COST PRICE');   });
                $sheet->cell('A1', function($cell) {$cell->setFont(array(
                        'size' => '25',
                        'bold' => true
                    ));   
                });
                
                $sheet->cell('A3', function($cell) {$cell->setFont(array(
                        'bold' => true
                    ));   
                });
                $sheet->cell('B3', function($cell) {$cell->setFont(array(
                        'bold' => true
                    ));   
                });
                
                $sheet->mergeCells('A1:I1');

                $sheet->setCellValue('A3', 'SELLER');
                $sheet->setCellValue('B3', ucfirst($vendor));  
                $sheet->rows($data_array);
                
                $sheet->row(5, function($row) {
                    $row->setBackground('#D9D9D9');

                });
                $sheet->setHeight(5,29);
                $sheet->cell('A4:H4',function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                });

               
                

                $sheet->cells('A4:H4', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
            
                $sheet->setAllBorders('thin');
            });
        })->setFilename('Cost Price Details'.Carbon\Carbon::now()->timestamp)
        ->download('csv');  

    }
    
     public function anyImportcostprice()
{
          if (Input::hasFile('import')) {

            $csv_file = Input::file('import');

            $destinationPath = 'public/public/media/csv/costprice/';
            $file_name = 'costpriceimport_' . time() . '.csv';
            $filepath = $destinationPath . '/' . $file_name;
            
            $csv_file->move($destinationPath, $file_name);
            $file = fopen($filepath, 'r');
            $isError = false;

            try {
                DB::beginTransaction();
                $data = fgetcsv($file, 1400, ",");
            
                 $start_row =1; //define start row
                 $i = 1; //define row count flag
                while (($data = fgetcsv($file,1400, ",")) !== FALSE) {
               if($i >= $start_row) {
                 
                 $update = DB::table('jocom_products AS products')
            ->join('jocom_seller as seller', 'products.sell_id', '=', 'seller.id')
            ->join('jocom_warehouse_products as warehouse', 'products.id', '=', 'warehouse.product_id')
           ->join('jocom_product_price as product_price', 'products.id', '=', 'product_price.product_id')
            ->join('jocom_product_price_seller as seller_price', 'product_price.id', '=', 'seller_price.product_price_id')
            ->where('products.is_base_product', '=', '1')
            ->where('seller_price.activation','=','1')
            ->where('product_price.status','=','1')
            ->where('products.status','<>','2')
            ->where('products.id','=',$data[0])
            ->select('seller_price.id as costid' )
            ->first();
          
            if($update){

              $costupdate = DB::table('jocom_product_price_seller')->where('id', '=',$update->costid)->update(['cost_price' =>$data[3]]);

            }
                }
                $i++;
                } 
            } catch(Exception $ex) {
                $isError = true;
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    
                    DB::commit();
                    unlink($filepath);
                    return Redirect::to('/product/costprice')->with('success', 'Cost Price Details Imported successfully.');
                }
            }

        } 
}
    
}

Validator::resolver(function($translator, $data, $rules, $messages)
{

    return new Validation($translator, $data, $rules, $messages);

});

class Validation extends Illuminate\Validation\Validator {

    /**
     * Magically adds validation methods. Normally the Laravel Validation methods
     * only support single values to be validated like 'numeric', 'alpha', etc.
     * Here we copy those methods to work also for arrays, so we can validate
     * if a value is OR an array contains only 'numeric', 'alpha', etc. values.
     *
     * $rules = array(
     *     'row_id' => 'required|integerOrArray', // "row_id" must be an integer OR an array containing only integer values
     *     'type'   => 'inOrArray:foo,bar' // "type" must be 'foo' or 'bar' OR an array containing nothing but those values
     * );
     *
     * @param string $method Name of the validation to perform e.g. 'numeric', 'alpha', etc.
     * @param array $parameters Contains the value to be validated, as well as additional validation information e.g. min:?, max:?, etc.
     */
    public function __call($method, $parameters)
    {

        // Convert method name to its non-array counterpart (e.g. validateNumericArray converts to validateNumeric)
        if (substr($method, -7) === 'OrArray')
            $method = substr($method, 0, -7);

        // Call original method when we are dealing with a single value only, instead of an array
        if (! is_array($parameters[1]))
            return call_user_func_array(array($this, $method), $parameters);

        $success = true;
        foreach ($parameters[1] as $value) {
            $parameters[1] = $value;
            $success &= call_user_func_array(array($this, $method), $parameters);
        }

        return $success;

    }

    /**
     * All ...OrArray validation functions can use their non-array error message counterparts
     *
     * @param mixed $attribute The value under validation
     * @param string $rule Validation rule
     */
    protected function getMessage($attribute, $rule)
    {

        if (substr($rule, -7) === 'OrArray')
            $rule = substr($rule, 0, -7);

        return parent::getMessage($attribute, $rule);

    }

}
