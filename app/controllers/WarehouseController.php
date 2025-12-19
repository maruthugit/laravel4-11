<?php

class WarehouseController extends BaseController 
{

	public function __construct()
    {
        $this->beforeFilter('auth', array('except' => 'anyLogisticscronjob'));
    }

	/**
     * Display the warehouse page.
     *
     * @return Page
     */

	public function anyIndex(){
        // SYS ADMIN 
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->first();
                if($SysAdminRegion->region_id == 0){
                    $addDriver = LogisticDriver::select('id','username')
                                                ->where('status','=',1)
                                                ->get();

                }else{
                    $addDriver = LogisticDriver::select('id','username')
                        ->where("region_id",$SysAdminRegion->region_id)
                        ->where('status','=',1)
                        ->get();
                }

        return View::make('warehouse.index')
        					->with('driverdetails',$addDriver);
        
    }

    public function getProductlist(){

    	// echo 'ok';
    	 try{


    	 	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }


    	$products = Product::select('jocom_products.id','jocom_products.sku','jocom_products.name','jocom_products.qrcode','jocom_products.delivery_time')
			    ->join('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
			    ->whereNotIn('jocom_products.id', function($query) {
			        $query->select('jocom_product_base_item.product_id')
			              ->from('jocom_product_base_item')
			              ->where('jocom_product_base_item.status','=',1);
			              // ->orwhere('jocom_product_base_item.status','=',0);
			    })
			    ->where('jocom_product_price.status','=',1)
                 ->where('jocom_products.is_base_product','=',1)
			   ->groupBy('jocom_product_price.product_id')
			   ->havingRaw('count(jocom_product_price.product_id)=1');
			   
			   if (isset($regionid) && $regionid == 2)
			   {
	           	 $products = $products->where('jocom_products.region_id','=',2);
		        }
		       else if (isset($regionid) && $regionid == 3)
		       {
		       	$products = $products->where('jocom_products.region_id','=',3);
		       }
		       else if (isset($regionid) && $regionid == 1)
		       {
		       	//$products = $products->where('jocom_products.region_id','=',0);
		       	$products = $products->where(function ($query){
                                $query->where('jocom_products.region_id','=',0)
                                      ->orwhere('jocom_products.region_id','=',1);
                               });
		       }

		        // else{
		        //  $products = $products->where('jocom_products.region_id','=',2);
		        // }



			   // ->having('prd','=',1);
			    
			    // $products = $products->get();

			    // echo count($products);

			    // echo '<pre>';
			    // print_r($products);
			    // echo '</pre>';

			    // die();
			   

			  return Datatables::of($products)
                        ->add_column('RootID', function($row){
			  					$value = '';
				  				if(Warehouse::getRootID($row->id) != 0){
				  					$value = Warehouse::getRootID($row->id);
				  				}
				  				
				  				return $value;

			  				}

			  				)
			  			->add_column('Action', function($row){
			  				if(Warehouse::isExists($row->id) == 0){
			  					if (in_array(Session::get('username'), array('joshua', 'quenny','maruthu','asif','jye','winnie'), true ) ) {	
			  						return '<a id="selectItem" class="btn btn-primary active" title="" href="/warehouse/add/'.$row->id.'">Add to Inventory</a>';
			  					}
			  					else 
			  					{
			  						return '<a id="selectItem" class="btn btn-primary disabled" title="" href="/warehouse/add/'.$row->id.'">Add to Inventory</a>';
			  					}
			  				}
			  				else{
			  					return '<span id="selectItem" class="btn btn-primary disabled" >Added</span>';
			  				}


			  			}

			  				)
			  			->make();

			} catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }

    public function anyListing(){

    	 try{


    	 	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $regionhq = 0;
	        $region = "";

	        $stockinlink = "";
	        $stockoutlink = "";
	        $stockretlink = "";

	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	           
	        }


	        if (isset($regionid) && $regionid ==0){
	            $region = "All Region";
	        }
	        else{
	            $resultregion = LogisticTransaction::getDriverRegionName($regionid);
	            $region=$resultregion->region;
	        }


	        if (isset($regionid) && $regionid ==0){

    	 	 $products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_products.reserved_in_hand','jocom_product_price.stock_unit','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							 ->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
							->where('jocom_warehouse_products.active','=',1)
    	 	 				->where('jocom_product_price.status','!=',2);
    	 	 				// ->whereNotIn('jocom_warehouse_productslinks.status',2);
    	 	 } else if (isset($regionid) && $regionid ==1){
    	 	 	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_products.reserved_in_hand','jocom_product_price.stock_unit','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
    	 	 				// ->where('jocom_warehouse_products.region_id','=',$regionid);
    	 	 				->where(function ($query){
                                $query->where('jocom_warehouse_products.region_id','=',0)
                                      ->orwhere('jocom_warehouse_products.region_id','=',1);
                               })
                            ->where('jocom_warehouse_products.active','=',1)   
    	 	 				->where('jocom_product_price.status','!=',2);
    	 	 } else {

    	 	 	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_products.reserved_in_hand','jocom_product_price.stock_unit','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
    	 	 				->where('jocom_warehouse_products.region_id','=',$regionid)
    	 	 				->where('jocom_warehouse_products.active','=',1)
    	 	 				->where('jocom_product_price.status','!=',2);

    	 	 }

    	 	if (in_array(Session::get('username'), array('joshua','maruthu'), true ) ) {

    	 		$stockinlink  = '<a id="selectItem" class="btn btn-primary active addStock" title="" data-id="{{$row->product_id}}" ref="/warehouse/stockin/{{$row->product_id}}">Stock In</a>';
    	 		$stockoutlink = '<a id="selectItem" class="btn btn-primary active outStock" title="" data-id="{{$row->product_id}}" ref="/warehouse/stockout/{{$row->product_id}}">Stock Out</a>';
    	 		$stockretlink = '<a id="selectItem" class="btn btn-primary active stockReturn" data-id="{{$row->product_id}}" title="" ref="/warehouse/stockreturn/{{$row->product_id}}">Stock Return</a>';
    	 	} 
    	 	else
    	 	{
    	 		$stockinlink  = '<a id="selectItem" class="btn btn-primary disabled addStock" title="">Stock In</a>';
    	 		$stockoutlink = '<a id="selectItem" class="btn btn-primary disabled outStock" title="">Stock Out</a>';
    	 		$stockretlink = '<a id="selectItem" class="btn btn-primary disabled stockReturn">Stock Return</a>';
    	 	}
    	 	 

    		return Datatables::of($products)	 	 				
    				->add_column('Stock_in', function($row){
	    					if(Warehouse::isExistsBase($row->product_id) == 0){
	    						if (in_array(Session::get('username'), array('joshua','maruthu'), true ) ) {	
	    						return '<a id="selectItem" class="btn btn-primary active addStock" title="" data-id="'.$row->product_id.'" ref="/warehouse/stockin/'.$row->product_id.'">Stock In</a>';
	    						}
	    						else 
	    						{
	    							return '<a id="selectItem" class="btn btn-primary disabled addStock" title="">Stock In</a>';	
	    						}
	    					}
	    					else 
	    					{
	    						return '<a id="selectItem" class="btn btn-primary disabled addStock" title="">Stock In</a>';
	    					}
    					 }
    				  )
    				->add_column('Stock_Out',  function($row){
    					   if(Warehouse::isExistsBase($row->product_id) == 0){
    					   		if (in_array(Session::get('username'), array('joshua','maruthu'), true ) ) {
	    							return '<a id="selectItem" class="btn btn-primary active outStock" title="" data-id="'.$row->product_id.'" ref="/warehouse/stockout/'.$row->product_id.'">Stock Out</a>';
	    						}
	    						else{
	    							return '<a id="selectItem" class="btn btn-primary disabled outStock" title="">Stock Out</a>';
	    						}
	    					}
	    					else 
	    					{
	    						return '<a id="selectItem" class="btn btn-primary disabled outStock" title="">Stock Out</a>';
	    					}
    				    }
 					  )
    				->add_column('Stock_Return', function($row){
    					    if(Warehouse::isExistsBase($row->product_id) == 0){
    					    	if (in_array(Session::get('username'), array('joshua', 'maruthu'), true ) ) {
	    							return '<a id="selectItem" class="btn btn-primary active stockReturn" data-id="'.$row->product_id.'" title="" ref="/warehouse/stockreturn/'.$row->product_id.'">Stock Return</a>';
	    						}
	    						else{
	    							return '<a id="selectItem" class="btn btn-primary disabled stockReturn" title="">Stock Return</a>';
	    						}
	    					}
	    					else 
	    					{
	    						return '<a id="selectItem" class="btn btn-primary disabled stockReturn" title="">Stock Return</a>';
	    					}
    				     }
    				  )
    				->make();

    	    } catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }


    public function anyAdjustmentslisting(){

    	 try{

    	 	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $regionhq = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	           
	        }


	        if (isset($regionid) && $regionid ==0){
	            $region = "All Region";
	        }
	        else{
	            $resultregion = LogisticTransaction::getDriverRegionName($regionid);
	            $region=$resultregion->region;
	        }

	        if (isset($regionid) && $regionid ==0){

		    	 	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label')
		    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				->where('jocom_product_price.status','=',1)
		    	 	 				;
		    } else if (isset($regionid) && $regionid ==1){
		    	 $products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label')
		    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				//->where('jocom_warehouse_products.region_id','=',$regionhq);
		    	 	 				->where('jocom_product_price.status','=',1)
		    	 	 				->where(function ($query){
		                                $query->where('jocom_warehouse_products.region_id','=',0)
		                                      ->orwhere('jocom_warehouse_products.region_id','=',1);
		                               });
		    }else{

		    	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label')
		    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
		    	 	 				->where('jocom_product_price.status','=',1)
		    	 	 				->where('jocom_warehouse_products.region_id','=',$regionid);
		    }
    	 	return Datatables::of($products)
    	 				->add_column("Old_Stock", function($row){
    	 						$result = Warehouse::getInventoryoldstock($row->product_id); 			
    	 						return $result;
    	 				})
    	 				->add_column("New_Stock", function($row){
    	 						$result = Warehouse::getInventoryNewstock($row->product_id); 			
    	 						return $result;
    	 				})
    	 				->add_column("Actual_Stock", function($row){
    	 						$result = Warehouse::getInventoryActualstock($row->product_id); 			
    	 						return $result;
    	 				})
			  			->add_column('Action', function($row){
			  						if (in_array(Session::get('username'), array('joshua', 'asif', 'william','maruthu','ramesh','quenny','eugeneyong'), true ) ) {	
			  							return '<a id="selectItem" class="btn btn-primary active Adjust" title="" data-id="'.$row->product_id.'">Adjust</a>';
			  						}
			  						else{
			  							return '<a id="selectItem" class="btn btn-primary disabled Adjust" title="" data-id="'.$row->product_id.'">Adjust</a>';
			  						}
			  					}

			  				)
			  			->make(); 				

    	 } catch (Exception $ex) {
               echo $ex->getMessage();
         }	

    }

    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */

    public function anyProductsajax() {

    	// print_r(Input::All());
    	 // echo 'OK'.Input::get('id').'U';
    	// die();

    	 $productID = Input::get('id');	

		 $products = Product::select(array(
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
                    ->where('jocom_products.is_base_product', '=', '1')
                    ->where('jocom_products.status', '!=', '2')
                    ->whereNotIn('jocom_products.id', function($query){
                    	$query->select('variant_product_id')
                    		->from('jocom_warehouse_products_baselinks');
                    		// ->where('product_id','=',$productID);
                    })
                    ->whereNotIn('jocom_products.id', function($query1){
                    	$query1->select('product_id')
                    		->from('jocom_warehouse_productslinks');
                    		// ->where('product_id','=',$productID);
                    });
                    if($productID != 0){
                    	$products = $products->where('jocom_products.id','<>',$productID);
                    }

                    
                    $sysAdminInfo = User::where("username",Session::get('username'))->first();
                    $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
                    if($SysAdminRegion->region_id != 0){
                            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
                    }
                    // $products = $products->where('region_id', '=', 2);

                    // ->where('jocom_product_price.status', '=', 1)
                    // ->where('jocom_product_price.default', '=', '1');

                    $variantBaseID1 = "<select id='variantID{{$id}}' name='variantID{{$id}}'  class='selectpicker' data-live-search='true'>";

                	$result =  Warehouse::getBaseLinks($productID);

                	 foreach ($result as  $value) {
                	 	// echo $value->product_id;
                	 	 $variantBaseID .= "<option value=".$value['product_id'].">".$value['product_id']."</option>";
                        }
                        
                    $variantBaseID .=  "</select><input type='hidden' name='hidTempID' id='hidTempID'"; 


                    return Datatables::of($products)
                    // ->edit_column('price', '{{number_format($price, 2)}}')
                    // ->edit_column('price_promo', '{{number_format($price_promo, 2)}}')
                    
	                ->add_column('VariantProduct','<select id=variantID{{$id}} name=variantID{{$id}} data-id={{$id}} class=selectpicker data-live-search=true>'.$variantBaseID)
                    ->add_column('Action', '<a id="selectItem" class="btn btn-primary add" title="" data-id={{$id}} href="/product/edit/{{$id}}">Select</a>')
                    ->make();
	}

	public function anyProductsbaseajax() {


    	 $productID = Input::get('id');	

		 $products = Product::select(array(
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
                    ->where('jocom_products.is_base_product', '=', '1')
                    ->where('jocom_products.status', '!=', '2')
                    ->whereNotIn('jocom_products.id', function($query){
                    	$query->select('product_id')
                    		->from('jocom_warehouse_productslinks');
                    		// ->where('product_id','=',$productID);
                    })
                    ->whereNotIn('jocom_products.id', function($query1){
                    	$query1->select('variant_product_id')
                    		->from('jocom_warehouse_products_baselinks');
                    		// ->where('product_id','=',$productID);
                    });

                    if($productID != 0){
                    	$products = $products->where('jocom_products.id','<>',$productID);
                    }

                    
                    $sysAdminInfo = User::where("username",Session::get('username'))->first();
                    $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
                    if($SysAdminRegion->region_id != 0){
                            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
                    }
                    // $products = $products->where('region_id', '=', 2);

                    // ->where('jocom_product_price.status', '=', 1)
                    // ->where('jocom_product_price.default', '=', '1');

                    return Datatables::of($products)
                    // ->edit_column('price', '{{number_format($price, 2)}}')
                    // ->edit_column('price_promo', '{{number_format($price_promo, 2)}}')
                    
                    ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" data-id={{$id}} href="/product/edit/{{$id}}">Select</a>')
                    ->make();
	}


	public function anyAjaxproduct($productID) {
        return View::make('warehouse.ajaxproduct',
        				['productID'=>$productID]
        	);
    }

    public function anyAjaxbaseproduct($productID) {
        return View::make('warehouse.ajaxbaseproduct',
        				['productID'=>$productID]
        	);
    }

	
	public function anyProductvariant(){

		$data 		= array();
		$refno 		= 0;
		$default 	= 1;
	
		$productid = Input::get('product_id');

		$result = DB::table('jocom_warehouse_products_baselinks')
					->where('product_id','=',$productid)
					->where('default','=',1)
					->get();


		




		// if(count($result)>0){

		// 	$arrayexist =  array('productid' => $result->product_id,
		// 						 'ref_no'	 => $result->ref_no,
		// 						 'default'	 => $result->default,
		// 						 );
		// 	array_push($data, $arrayexist);
		// }
		// else{

		// 	$refno = Warehouse::genReferenceno();
		// 	DB::table('jocom_warehouse_products_baselinks')->insert(array(
  //                           'variant_product_id'   	=> $productid,
  //                           'product_id'    		=> $productid,
  //                           'ref_no'				=> $refno,
  //                           'default'				=> $default, 
  //                           'insert_by'				=> Session::get('username'),
  //                           'created_at'			=> date('Y-m-d H:i:s'),
  //                           'modify_by'				=> Session::get('username'),
  //                           'updated_at'			=> date('Y-m-d H:i:s')
  //                           )
  //                   );

		// 	$arrayexist =  array('productid' => $productid,
		// 						 'ref_no'	 => $refno,
		// 						 'default'	 => $default,
		// 						 );
		// 	array_push($data, $arrayexist);
		// }

		
		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';

		return $data;


	}    


	/**
     * Display the specified resource.
     *
     * @return Response
     */
    public function anyLinkproduct(){

        return View::make('warehouse.product_link');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function getStockin($productID){

    	// die($productID);

        return View::make('warehouse.stockin',['productID'=>$productID]);
    }


    public function anyInvadjustments(){

        return View::make('warehouse.inventory_adjustments');
    }

    public function anyGeneralreport(){

        return View::make('warehouse.generalreport');
    }


    public function anyAdd($id = ""){
        $refno = 0;
    	$MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }
        
        // print_r($id);	
        // echo Session::get('username').$regionid;
        // die();
        
        $refno = Warehouse::getRefernumber($id);

    	$warehouse						= new Warehouse;
    	$warehouse->product_id 			= $id;
    	$warehouse->ref_no 				= $refno;
    	$warehouse->region_country_id 	= $MalaysiaCountryID;
    	$warehouse->region_id 			= $regionid;
    	$warehouse->insert_by			= Session::get('username');
		$warehouse->modify_by			= Session::get('username');

		$warehouse->save();

		Session::flash('message', 'Successfully added.');
	    return Redirect::to('warehouse/linkproduct');

    }


    public function anyStockinhand(){
    	$productid = Input::get('productid');

    	$product  =DB::table('jocom_warehouse_products')  
	                    ->where('product_id','=',$productid)
	                    ->first();

	     return array('productdetails' => $product,
     				 );         

    }


    public function anySavestockin(){

    	$isError = 0;
        $response = 1;
        $currentstock = 0;
        
        try{
        	DB::beginTransaction();


        	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }

	        $productid = Input::get('productID');
	        $unit = Input::get('unit');
	        $expirydate = Input::get('expirydate');
	    	$unitprice = Input::get('unitprice');
	    	$totalamount = Input::get('totalamount');
	    	$remarks = Input::get('remarkstockin');
	    	
	    	// Manage Actual Stock
	    	ProductController::manageActualstock($productid, $unit, 'increase');
	    	$price_id = DB::table('jocom_product_price')
	    					->where('product_id', '=', $productid)
	    					->where('status', '=', 1)
	    					->first()->id;
	    	ProductController::log_actualstockin($productid, $price_id, $unit);

	        $productlinks = DB::table('jocom_warehouse_productslinks')
	        				   ->where('product_id','=',$productid)
	        				   ->where('status','=',1)
	        				   ->first();

	      	if(count($productlinks)>0)
	      	{

	      		$baseID 	= $productlinks->parent_product_id;
	      		$position 	= $productlinks->nodeposition;
	      		$quantity 	= $productlinks->quantity;


	      		$basiclinks = DB::table('jocom_warehouse_productslinks')	
	        				   ->where('parent_product_id','=',$baseID)
	        				   ->where('nodeposition','<=',$position)
	        				   ->orderby('nodeposition','DESC')
	        				   ->get();


	        	$nodecount = count($basiclinks);

	        	if($nodecount == 1)
	        	{
	        		foreach ($basiclinks as  $value) {

	        			$subproductid  = $value->product_id;
	        			$subposition   = $value->nodeposition;
		        		$subquantity   = $value->quantity;


		        		$prodsub = Warehouse::where('product_id','=',$subproductid)
	    								->first();

	    				$getstockinh = $prodsub->stockin_hand;				
		        		$currentst = $getstockinh + $unit;
		        		$actualst  = $currentst / $subquantity;

		        		// echo 'Y'.$currentst.'Y';

		        		list($whole, $decimal) = explode('.', $actualst);

		        		
		        		if($whole != 0){

		        			$subcurrstock = $currentst - ($subquantity * $whole);

		        			$basestock = $whole;
		        			 // echo 'T'.$subcurrstock.'T';

		        			$stockinhandbase = Warehouse::getStockinhand($baseID);
					    	$currentstockbase = $stockinhandbase + $basestock;


					    	$productsBase = Warehouse::where('product_id','=',$baseID)
					    					->first();
					    	$productsBase->stockin_hand = $currentstockbase;
					    	$productsBase->save();
                            
                            $stockret = Warehouse::getStocklevelupdate($baseID,$currentstockbase,1);  // Stock Level update 

					    	$stockinhandNode = Warehouse::getStockinhand($subproductid);
					    	// $currentstockNode = $subcurrstock - $getstockinh;
					    	
					    	$productsNode = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode->stockin_hand = $subcurrstock;
					    	$productsNode->save();

		        		}
		        		else if($whole == 0){

		        			$stockinhandNode0 = Warehouse::getStockinhand($subproductid);
					    	$currentstockNode0 = $unit + $stockinhandNode0;
					    	
					    	$productsNode0 = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode0->stockin_hand = $currentstockNode0;
					    	$productsNode0->save();

		        		} 
	        			
	        		}
	        			

	        	}
	       

	      	}	
	      	else
	      	{

	      		$stockinhand = Warehouse::getStockinhand($productid);
		    	$currentstock = $stockinhand + $unit;

		    	$products = Warehouse::where('product_id','=',$productid)
		    				->first();
		    	$products->stockin_hand = $currentstock;
		    	$products->save();
		    	$stockret = Warehouse::getStocklevelupdate($productid,$currentstock,1);  // Stock Level update 


	      	}

	     
	    	$row = array('product_id' 			=> $productid,
	    				 'unit' 	 			=> $unit,
	    				 'expiry_date'  		=> $expirydate,
	    				 'unitprice' 	 		=> $unitprice,
	    				 'total' 	 			=> $totalamount,
	    				 'remark' 	 			=> $remarks,
	    				 'region_country_id' 	=> $MalaysiaCountryID,	
	    				 'region_id' 	 		=> $regionid,
	    				 'insert_by'      		=> Session::get('username'),
                		 'created_at'   		=> date('Y-m-d H:i:s'),	
	    			);



	    	$stockin = DB::table('jocom_warehouse_stockin')
	    					->insert($row);


    	 }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
         }finally {
        	if ($isError == 1) {
                    DB::rollback();
                } else {
                	Session::flash('success', 'Successfully updated Stock In.');		
                    DB::commit();
                }
            return array(
                "response"=>$response,
            );
            
        }




    }
    
    public function Savestockingrn($productidgrn,$unitgrn,$expirydategrn,$unitpricegrn,$totalamountgrn,$remarksgrn){

    	$isError = 0;
        $response = 1;
        $currentstock = 0;
        
        try{
        	DB::beginTransaction();


        	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }

	        $productid = $productidgrn;
	        $unit = $unitgrn;
	        $expirydate = $expirydategrn;
	    	$unitprice = $unitpricegrn;
	    	$totalamount = $totalamountgrn;
	    	$remarks = $remarksgrn;
	    	
	    	// Manage Actual Stock
	    	ProductController::manageActualstock($productid, $unit, 'increase');
	    	$price_id = DB::table('jocom_product_price')
	    					->where('product_id', '=', $productid)
	    					->where('status', '=', 1)
	    					->first()->id;
	    	ProductController::log_actualstockin($productid, $price_id, $unit);

	        $productlinks = DB::table('jocom_warehouse_productslinks')
	        				   ->where('product_id','=',$productid)
	        				   ->where('status','=',1)
	        				   ->first();

	      	if(count($productlinks)>0)
	      	{

	      		$baseID 	= $productlinks->parent_product_id;
	      		$position 	= $productlinks->nodeposition;
	      		$quantity 	= $productlinks->quantity;


	      		$basiclinks = DB::table('jocom_warehouse_productslinks')	
	        				   ->where('parent_product_id','=',$baseID)
	        				   ->where('nodeposition','<=',$position)
	        				   ->orderby('nodeposition','DESC')
	        				   ->get();


	        	$nodecount = count($basiclinks);

	        	if($nodecount == 1)
	        	{
	        		foreach ($basiclinks as  $value) {

	        			$subproductid  = $value->product_id;
	        			$subposition   = $value->nodeposition;
		        		$subquantity   = $value->quantity;


		        		$prodsub = Warehouse::where('product_id','=',$subproductid)
	    								->first();

	    				$getstockinh = $prodsub->stockin_hand;				
		        		$currentst = $getstockinh + $unit;
		        		$actualst  = $currentst / $subquantity;

		        		// echo 'Y'.$currentst.'Y';

		        		list($whole, $decimal) = explode('.', $actualst);

		        		
		        		if($whole != 0){

		        			$subcurrstock = $currentst - ($subquantity * $whole);

		        			$basestock = $whole;
		        			 // echo 'T'.$subcurrstock.'T';

		        			$stockinhandbase = Warehouse::getStockinhand($baseID);
					    	$currentstockbase = $stockinhandbase + $basestock;


					    	$productsBase = Warehouse::where('product_id','=',$baseID)
					    					->first();
					    	$productsBase->stockin_hand = $currentstockbase;
					    	$productsBase->save();
                            
                            $stockret = Warehouse::getStocklevelupdate($baseID,$currentstockbase,1);  // Stock Level update 

					    	$stockinhandNode = Warehouse::getStockinhand($subproductid);
					    	// $currentstockNode = $subcurrstock - $getstockinh;
					    	
					    	$productsNode = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode->stockin_hand = $subcurrstock;
					    	$productsNode->save();

		        		}
		        		else if($whole == 0){

		        			$stockinhandNode0 = Warehouse::getStockinhand($subproductid);
					    	$currentstockNode0 = $unit + $stockinhandNode0;
					    	
					    	$productsNode0 = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode0->stockin_hand = $currentstockNode0;
					    	$productsNode0->save();

		        		} 
	        			
	        		}
	        			

	        	}
	       

	      	}	
	      	else
	      	{

	      		$stockinhand = Warehouse::getStockinhand($productid);
		    	$currentstock = $stockinhand + $unit;

		    	$products = Warehouse::where('product_id','=',$productid)
		    				->first();
		    	$products->stockin_hand = $currentstock;
		    	$products->save();
		    	$stockret = Warehouse::getStocklevelupdate($productid,$currentstock,1);  // Stock Level update 


	      	}

	     
	    	$row = array('product_id' 			=> $productid,
	    				 'unit' 	 			=> $unit,
	    				 'expiry_date'  		=> $expirydate,
	    				 'unitprice' 	 		=> $unitprice,
	    				 'total' 	 			=> $totalamount,
	    				 'remark' 	 			=> $remarks,
	    				 'region_country_id' 	=> $MalaysiaCountryID,	
	    				 'region_id' 	 		=> $regionid,
	    				 'insert_by'      		=> Session::get('username'),
                		 'created_at'   		=> date('Y-m-d H:i:s'),	
	    			);



	    	$stockin = DB::table('jocom_warehouse_stockin')
	    					->insert($row);


    	 }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
         }finally {
        	if ($isError == 1) {
                    DB::rollback();
                } else {
                	Session::flash('success', 'Successfully updated Stock In.');		
                    DB::commit();
                }
            return array(
                "response"=>$response,
            );
            
        }

    }
    
    public function anyRevertback(){

    	$productreserv = DB::table('jocom_warehouse_product_reserved')
	        				   ->where('batch_no','=','GDL0000000412')
	        				   ->where('updated_at','=','2018-03-17 10:08:20')
	        				   ->get();


	      if(count($productreserv)>0){
	      	echo '<pre>';

	      	foreach ($productreserv as  $value) {
							 
	      			$result = DB::table("jocom_warehouse_products")
		                    ->where('id','=',$value->product_warehouse_id)
		                    ->first();

		            $productid = $result->product_id;
		            $reserdunit = $value->total_reserved;
		                     
	      		echo '<br>'.$value->id.'-'.$value->product_warehouse_id.'-'.$result->product_id;


	      		$productlinks = DB::table('jocom_warehouse_productslinks')
	        				   ->where('product_id','=',$productid)
	        				   ->where('status','=',1)
	        				   ->first();

	      	if(count($productlinks)>0)
	      	{

	      		$baseID 	= $productlinks->base_product_id;
	      		$quantity 	= $productlinks->quantity;


	      		$basiclinks = DB::table('jocom_warehouse_productslinks')	
	        				   ->where('base_product_id','=',$baseID)
	        				   ->get();
                echo '-'.$baseID.'==='.$quantity.'===';

	        	$nodecount = count($basiclinks);

	        	if($nodecount == 1)
	        	{
	        		foreach ($basiclinks as  $value) {

	        			$subproductid  = $value->base_product_id;
		        		$subquantity   = $value->quantity;
                         echo '==='.$subquantity.'===';

		        		$prodsub = Warehouse::where('product_id','=',$subproductid)
	    								->first();

	    				$getstockinh = $prodsub->stockin_hand;				
		        		$currentst = $getstockinh + ($reserdunit/$quantity);
		        		

		        		 echo 'Stock-'.$getstockinh.'-';

		        		list($whole, $decimal) = explode('.', $currentst);
                        
                        echo 'Total'.$whole;
		        		
		        		if($whole != 0){
		        		    
		        		    $productsBase = Warehouse::where('product_id','=',$baseID)
					    					->first();
					    	$productsBase->stockin_hand = $currentst;
					    	$productsBase->save();

		        			$subcurrstock = $currentst - ($subquantity * $whole);

		        			$basestock = $whole;
		        			 // echo 'T'.$subcurrstock.'T';

		        			$stockinhandbase = Warehouse::getStockinhand($baseID);
					    	$currentstockbase = $stockinhandbase + $basestock;

					    	 
					    	


					    	$stockinhandNode = Warehouse::getStockinhand($subproductid);
					    	
					    	// $productsNode = Warehouse::where('product_id','=',$subproductid)
					    	// 				->first();

					    	// $productsNode->stockin_hand = $subcurrstock;
					    	// $productsNode->save();

		        		}
		        		else if($whole == 0){

		        			$stockinhandNode0 = Warehouse::getStockinhand($subproductid);
					    	$currentstockNode0 = $reserdunit + $stockinhandNode0;
					    	echo 'Y'.$currentstockNode0.'Y';
					    	$productsNode0 = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode0->stockin_hand = $currentstockNode0;
					    	$productsNode0->save();

		        		} 
		        		echo '<br>';
	        			
	        		}
	        			

	        	}
	       

	      	}	
	      	else
	      	{

	      		$stockinhand = Warehouse::getStockinhand($productid);
		    	$currentstock = $stockinhand + $reserdunit;

		    	echo 'Stock-'.$stockinhand.'-Total'.$currentstock;

		    	$products = Warehouse::where('product_id','=',$productid)
		    				->first();
		    	$products->stockin_hand = $currentstock;
		    	$products->save();


	      	}	


	      	}

	      	echo '</pre>';
            echo '===============Sucessfully Reverted============';

	      }  				   


    }

    public function anySavestockout(){
    	$isError = 0;
        $response = 1;
        $currentstock = 0;
        $finalcurrentst = 0;
        $calbaseitem = 0;
        $subnodestock = 0;


        try{
        	DB::beginTransaction();

        	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";

	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }

	    	$productid 	= Input::get('productID');
	    	$outunit 	= Input::get('outstockunit');
	    	$assignto 	= Input::get('assignto');
	    	$remarks 	= Input::get('outstockremark');


	    	$productlinks = DB::table('jocom_warehouse_productslinks')
	        				   ->where('product_id','=',$productid)
	        				   ->where('status','=',1)
	        				   ->first();

	        if(count($productlinks)>0)
	      	{
	      		$baseID 	= $productlinks->parent_product_id;
	      		$position 	= $productlinks->nodeposition;
	      		$quantity 	= $productlinks->quantity;

	      		$basiclinks = DB::table('jocom_warehouse_productslinks')	
	        				   ->where('parent_product_id','=',$baseID)
	        				   ->where('nodeposition','<=',$position)
	        				   ->orderby('nodeposition','DESC')
	        				   ->get();

	        	$nodecount = count($basiclinks);

	        	if($nodecount == 1)
	        	{
	        		foreach ($basiclinks as  $value) {

	        			$subproductid  = $value->product_id;
	        			$subposition   = $value->nodeposition;
		        		$subquantity   = $value->quantity;


		        		$prodsub = Warehouse::where('product_id','=',$subproductid)
	    								->first();

	    				$getstockinh = $prodsub->stockin_hand;		

		        		$currentst = $getstockinh - $outunit;

		        		if($currentst>0){
		        			$stockoutstatus = 1;
		        			$finalcurrentst =  $currentst;

		        			$productsbaseStock = Warehouse::where('product_id','=',$subproductid)
					    					->first();
					    	$productsbaseStock->stockin_hand = $finalcurrentst;
					    	$productsbaseStock->save();

		        		}
		        		else 
		        		{
		        			//$calbaseitem  = $outunit / $subquantity;
		        			$substocktotal = $outunit - $productsbaseStock;
		        			$calbaseitem  = $substocktotal / $subquantity;

		        			list($wholestock, $decimal) = explode('.', $calbaseitem);


		        			if($decimal != 0 && $wholestock == 0){
		        				$wholestock = $wholestock + 1;
		        			}

		        			$prodbase = Warehouse::where('product_id','=',$baseID)
	    								->first();
	    					
	    					$basestockinhand = $prodbase->stockin_hand;

	    					if($basestockinhand>=$wholestock){
	    						$productsbaseStock = Warehouse::where('product_id','=',$baseID)
					    					->first();
						    	$productsbaseStock->stockin_hand = $basestockinhand - $wholestock;
						    	$productsbaseStock->save();

						    	$subnodestock = ($wholestock * $subquantity) + $getstockinh; 
						    	$finalcurrentst = $subnodestock - $outunit;

						    	$productsBasicStock = Warehouse::where('product_id','=',$subproductid)
					    					->first();
						    	$productsBasicStock->stockin_hand = $finalcurrentst;
						    	$productsBasicStock->save();


	    					}
	    					else if($basestockinhand == 0){

	    						$productsBasicStock = Warehouse::where('product_id','=',$subproductid)
					    					->first();
						    	$productsBasicStock->stockin_hand = $getstockinh - $outunit;
						    	$productsBasicStock->save();

	    					} 
	    					else if($basestockinhand<$wholestock && $basestockinhand != 0){

	    						$subnodestock = ($wholestock * $subquantity) + $getstockinh; 
						    	$finalcurrentst = $subnodestock - $outunit;

						    	$productsbaseStock = Warehouse::where('product_id','=',$baseID)
					    					->first();
						    	$productsbaseStock->stockin_hand = 0;
						    	$productsbaseStock->save();

						    	$productsBasicStock = Warehouse::where('product_id','=',$subproductid)
					    					->first();
						    	$productsBasicStock->stockin_hand = $finalcurrentst;
						    	$productsBasicStock->save();



	    					}


		        		}
		        		 

	        		}


	        	}			   


	      	}				   
	      	else
	      	{
	      		$products = Warehouse::where('product_id','=',$productid)
	    				->first();
		    	$currentstock = $products->stockin_hand - $outunit;			
		    	$products->stockin_hand = $currentstock;
		    	$products->save();

	      	}




	    	




	    	$row = array('product_id' 			=> $productid,
	    				 'stockout_unit' 	 	=> $outunit,
	    				 'driver_id' 	 		=> $assignto,
	    				 'remark' 	 			=> $remarks,
	    				 'region_country_id' 	=> $MalaysiaCountryID,	
	    				 'region_id' 	 		=> $regionid,
	    				 'insert_by'      		=> Session::get('username'),
                		 'created_at'   		=> date('Y-m-d H:i:s'),	
	    			);



	    	$stockin = DB::table('jocom_warehouse_stockout')
	    					->insert($row);

	    	//UPDATE RESERVED STOCK
	    	$reserved = 0;
    		$reserved_inhand = 0; 
	    	  $stockinhand = Warehouse::where('product_id','=',$productid)
    								   ->first(); 
    			$reserved_inhand = $stockinhand->reserved_in_hand;
    			$reserved = $reserved_inhand - $outunit; 

	    			if($reserved <0){
	    				$reserved = 0;
	    			}

	    		if($reserved_inhand !=0){
	    			$stockinhand->reserved_in_hand = $reserved;
	    			$stockinhand->save();
	    		}				


	    }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
     	}finally {
    		if ($isError == 1) {
                DB::rollback();
            } else {
            	Session::flash('success', 'Successfully updated Stock out.');		
                DB::commit();
            }
        return array(
            "response"=>$response,
        );
            
        }


    }


    public function anySavestockreturn(){
    	$isError = 0;
        $response = 1;
        $currentstock = 0;

        try{
        	DB::beginTransaction();

        	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }
	    	$productid 		= Input::get('productID');
	    	$stockreturn 	= Input::get('stockreturn');
	    	$returnfrom 	= Input::get('returnfrom');
	    	$remarkreturn 	= Input::get('remarkreturn');


	    	$productlinks = DB::table('jocom_warehouse_productslinks')
	        				   ->where('product_id','=',$productid)
	        				   ->where('status','=',1)
	        				   ->first();

	      	if(count($productlinks)>0)
	      	{

	      		$baseID 	= $productlinks->parent_product_id;
	      		$position 	= $productlinks->nodeposition;
	      		$quantity 	= $productlinks->quantity;


	      		$basiclinks = DB::table('jocom_warehouse_productslinks')	
	        				   ->where('parent_product_id','=',$baseID)
	        				   ->where('nodeposition','<=',$position)
	        				   ->orderby('nodeposition','DESC')
	        				   ->get();


	        	$nodecount = count($basiclinks);

	        	if($nodecount == 1)
	        	{
	        		foreach ($basiclinks as  $value) {

	        			$subproductid  = $value->product_id;
	        			$subposition   = $value->nodeposition;
		        		$subquantity   = $value->quantity;


		        		$prodsub = Warehouse::where('product_id','=',$subproductid)
	    								->first();

	    				$getstockinh = $prodsub->stockin_hand;				
		        		$currentst = $getstockinh + $stockreturn;
		        		$actualst  = $currentst / $subquantity;

		        		// echo 'Y'.$currentst.'Y';

		        		list($whole, $decimal) = explode('.', $actualst);

		        		
		        		if($whole != 0){

		        			$subcurrstock = $currentst - ($subquantity * $whole);

		        			$basestock = $whole;
		        			 // echo 'T'.$subcurrstock.'T';

		        			$stockinhandbase = Warehouse::getStockinhand($baseID);
					    	$currentstockbase = $stockinhandbase + $basestock;


					    	$productsBase = Warehouse::where('product_id','=',$baseID)
					    					->first();
					    	$productsBase->stockin_hand = $currentstockbase;
					    	$productsBase->save();


					    	$stockinhandNode = Warehouse::getStockinhand($subproductid);
					    	// $currentstockNode = $subcurrstock - $getstockinh;
					    	
					    	$productsNode = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode->stockin_hand = $subcurrstock;
					    	$productsNode->save();

		        		}
		        		else if($whole == 0){

		        			$stockinhandNode0 = Warehouse::getStockinhand($subproductid);
					    	$currentstockNode0 = $stockreturn + $stockinhandNode0;
					    	
					    	$productsNode0 = Warehouse::where('product_id','=',$subproductid)
					    					->first();

					    	$productsNode0->stockin_hand = $currentstockNode0;
					    	$productsNode0->save();

		        		} 
	        			
	        		}
	        			

	        	}


	      	}
	      	else
	      	{
	      		$products = Warehouse::where('product_id','=',$productid)
	    				->first();
		    	$currentstock = $products->stockin_hand + $stockreturn;			
		    	$products->stockin_hand = $currentstock;
		    	$products->save();

	      	}


	    	


	    	$row = array('product_id' 			=> $productid,
	    				 'stockreturn_unit' 	=> $stockreturn,
	    				 'driver_id' 	 		=> $returnfrom,
	    				 'remark' 	 			=> $remarkreturn,
	    				 'region_country_id' 	=> $MalaysiaCountryID,	
	    				 'region_id' 	 		=> $regionid,
	    				 'insert_by'      		=> Session::get('username'),
                		 'created_at'   		=> date('Y-m-d H:i:s'),	
	    			);

	    	$stockin = DB::table('jocom_warehouse_stockreturn')
	    					->insert($row);


	    	//UPDATE RESERVED STOCK
	    	    $reserved = 0;
    			$reserved_inhand = 0; 
	      		$stockreturnresult = Warehouse::where('product_id','=',$productid)
    								   ->first(); 
    			$reserved_inhand = $stockreturnresult->reserved_in_hand;
    			$reserved = $stockreturn + $reserved_inhand; 

	    		if($reserved_inhand !=0){
	    			$stockreturnresult->reserved_in_hand = $reserved;
	    			$stockreturnresult->save();	
	    		}					


    	}catch (Exception $ex) {
            $isError = 1;
            $response = 0;
     	}finally {
    		if ($isError == 1) {
                DB::rollback();
            } else {
            	Session::flash('success', 'Successfully updated Return Stock.');		
                DB::commit();
            }
        return array(
            "response"=>$response,
        );
            
        }

    }


    public function anySavestockadjust(){
    	$isError = 0;
        $response = 1;

        // print_r(Input::all());

        try{
        	DB::beginTransaction();

        	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $region = "";
	        
	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	        }
	    	$productid 		= Input::get('productID');
	    	$newstock    	= Input::get('newstock');
	    	$stockinhand 	= Input::get('stockinhand');
	    	$remarkadjust 	= Input::get('remarkadjust');



	    	$products = Warehouse::where('product_id','=',$productid)
	    				->first();
	    	$products->stockin_hand = $newstock;
	    	$products->save();
	    	
	    	$stockret = Warehouse::getStocklevelupdate($productid,$newstock,2);  // Stock Level update 

	    	$row = array('product_id' 			=> $productid,
	    				 'oldstock' 			=> $stockinhand,
	    				 'newstock' 	 		=> $newstock,
	    				 'remark' 	 			=> $remarkadjust,
	    				 'region_country_id' 	=> $MalaysiaCountryID,	
	    				 'region_id' 	 		=> $regionid,
	    				 'insert_by'      		=> Session::get('username'),
                		 'created_at'   		=> date('Y-m-d H:i:s'),	
	    			);

	    	$stockin = DB::table('jocom_warehouse_inventoryadjustments')
	    					->insert($row);


	    }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
     	}finally {
    		if ($isError == 1) {
                DB::rollback();
            } else {
            	Session::flash('success', 'Successfully updated Stock Adjustments.');		
                DB::commit();
            }
        return array(
            "response"=>$response,
        );
            
        }	

    }

    public function anyExportinventory(){

    	$isError = 0;
        $response = 1;

        // print_r(Input::all());

        try{

        	$result = Warehouse::getInventorydetails(); 
        	$data = json_decode(json_encode($result), true);

        	$date = "warehouseproduct".date('Y-m-dH:i:s');

            $path = Config::get('constants.CSV_FILE_INVENTORY_PATH');

            Excel::create($date, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('xls');

            


        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
     	}finally {
     		if($response ==0){
     			// Session::flash('message', 'Downloaded file format is Invalid');	
     			return Redirect::to('warehouse')->with('message', 'Error downloading stock report')->withErrors($validator)->withInput();		
     		}
	        return array(
	            "response"=>$response,
	        );
            
        }	
    }

    public function anyExportreport(){
	    $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $stockarray = array();
        $repname    = "";
        $reptitle 	= "";

        // echo '<pre>';
        // print_r(Input::all());
        // echo '</pre>';
        
        try{

        	// $from_date = Input::get('transaction_from').'00::00:00';
         //    $to_date = Input::get('transaction_to').'23::59:59';
            $report_type = Input::get('report_type');

           	if($report_type ==1){

           		$result = Warehouse::getInventorydetails(); 
           		
           		foreach ($result as $value) {

           			$stockarray[]  = array('ProductID'		 => $value->ProductID,
           								   'ProductName'	 => $value->ProductName,
           								   'Label'			 => $value->Label,
           								   'StockInHand'	 => $value->StockInHand,
           								   'Measurement'	 => $value->Measurement,
           								   'CreatedBy'		 => $value->CreatedBy,
           								   'CreatedAt'		 => $value->CreatedAt,
           								   'ModifiedBy'		 => $value->ModifiedBy,
           								   'UpdatedAt'		 => $value->UpdatedAt,

           							 );

           		}

           		$repname	= "Inventorystock";
           		$reptitle   = "Inventory Stock";

           		



           	}
           	else if ($report_type ==2){

           		$from_date = Input::get('transaction_from').' 00::00:00';
            	$to_date = Input::get('transaction_to').' 23::59:59';

           		$stockinresult = Warehouse::getStockinDetails($from_date,$to_date,$report_type);

           		foreach ($stockinresult as  $value) {

           			$expdate = "";

           			if($value->Expirydate != "0000-00-00 00:00:00"){
           				$expdate = $value->Expirydate;
           			}

           			$stockarray[]  = array('ProductID'		 => $value->ProductID,
           								   'ProductName'	 => $value->ProductName,
           								   'Label'			 => $value->Label,
           								   'StockInQty'		 => $value->Qty,
           								   'UnitPrice'		 => $value->UnitPrice,
           								   'Total'			 => $value->Total,
           								   'Remark'			 => $value->Remark,
           								   'Expirydate'		 => $expdate,
           								   'CreatedBy'		 => $value->CreatedBy,
           								   'CreatedAt'		 => $value->CreatedAt,


           							 );
           			
           		}

           		$repname	= "Inventorystockin";
           		$reptitle   = "Inventory StockIn";


           	}
           	else if ($report_type ==3){

           		$from_date = Input::get('transaction_from').' 00::00:00';
            	$to_date = Input::get('transaction_to').' 23::59:59';

           		$stockinresult = Warehouse::getStockinDetails($from_date,$to_date,$report_type);

           		foreach ($stockinresult as  $value) {


           			$stockarray[]  = array('ProductID'		 => $value->ProductID,
           								   'ProductName'	 => $value->ProductName,
           								   'Label'			 => $value->Label,
           								   'StockOutUnit'	 => $value->StockOutUnit,
           								   'DriverName'		 => $value->DriverName,
           								   'Remark'			 => $value->Remark,
           								   'CreatedBy'		 => $value->CreatedBy,
           								   'CreatedAt'		 => $value->CreatedAt,


           							 );
           			
           		}

           		$repname	= "Inventorystockout";
           		$reptitle   = "Inventory StockOut";


           	}
           	else if ($report_type ==4){

           		$from_date = Input::get('transaction_from').' 00::00:00';
            	$to_date = Input::get('transaction_to').' 23::59:59';

           		$stockinresult = Warehouse::getStockinDetails($from_date,$to_date,$report_type);

           		foreach ($stockinresult as  $value) {


           			$stockarray[]  = array('ProductID'		 => $value->ProductID,
           								   'ProductName'	 => $value->ProductName,
           								   'Label'			 => $value->Label,
           								   'StockReturnUnit' => $value->StockReturnUnit,
           								   'DriverName'		 => $value->DriverName,
           								   'Remark'			 => $value->Remark,
           								   'CreatedBy'		 => $value->CreatedBy,
           								   'CreatedAt'		 => $value->CreatedAt,


           							 );
           			
           		}

           		$repname	= "Inventorystockreturn";
           		$reptitle   = "Inventory StockReturn";


           	}




           	$data = array(
           				'Stockrep' 		=> $stockarray,
           				'fromdate' 		=> $from_date,
           				'todate'   		=> $to_date,
           				'rtype'	   		=> $report_type,
           				'reporttitle'	=> $reptitle,		
           		    );

       	$date = date('Y-m-d H:i:s');

        $path = "warehouse";	

        } catch (Exception $ex) {
            echo $ex->getMessage();
        } finally {
            // return $data;


        	return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($data) {
                    $excel->sheet('Warehouse', function($sheet) use ($data)
                    {   
                        $sheet->loadView('warehouse.templatewarehouse', array('data' =>$data));
                        
                    });
                })->download('xls');

           

        }



    }

    public function anyCheckstock(){

    	$isError = 0;
        $response = 1;
        $baseID = 0;


        try{

        	$productid 		= Input::get('productID');
	    	$newstock    	= Input::get('newstock');


	    	$result = DB::table('jocom_warehouse_productslinks')
	    				  ->where('product_id','=',$productid)
	    				  ->where('status','=',2)
	    				  ->first();

	    	if(count($result)>0){

	    		$parent_prd_id = $result->parent_product_id;
	    		$base_product_id = $result->base_product_id;
	    		$levelquantity = $result->quantity;
	    		$refno = $result->ref_no;


	    		$stresult = DB::table('jocom_warehouse_products')
	    					  ->where('product_id','=',$productid)
	    					  ->first();





	    	}






        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
     	}finally {

     	}


    }
    
    public function anyRunningstockcronjob(){

   		$startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";

        $startdate = Config::get('constants.STOCKOUT_START_DATE');

        try{

	       // Begin Transaction
	        DB::beginTransaction();

	   		$LogisticBatch = DB::table('logistic_batch')
	   							->where('batch_date','>=',$startdate)
	   							->where('deduction','=',0)
	   							->get(); 
	   		
	   		 if(count($LogisticBatch)>0){

	   		 	foreach ($LogisticBatch as $key => $value) {
	   		 		$batch_id = $value->id; 
	   		 		$log_id	  = $value->logistic_id; 

	   		 		$log_items = DB::table('logistic_transaction_item')
	   		 						->where('logistic_id','=',$log_id)
	   		 						->get();
	   		 		
	   		 		

			   		if(count($log_items)>0){
			   			
			   			foreach ($log_items as $log_key => $log_value){
			   				$qtyorder = 0;
			   				$qtyorder = $log_value->qty_order;
			   				$log_item_id = $log_value->id;

			   				$BaseItems = DB::table('jocom_product_base_item AS JPBI')
		                                ->select(array(
		                                    'JPBI.*','JP.sku'
		                               	 ))
		                                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')  
		                                ->where("JPBI.product_id",$log_value->product_id)
		                                ->where("JPBI.price_option_id",$log_value->product_price_id)
		                                ->where("JPBI.status",1)
		                                ->get();
		      

		                      	if(count($BaseItems)>0){
		                      		//BASE ITEM
		                      				
		                      		foreach ($BaseItems as $base_key => $base_value) {

		                      			$quantity_require = $base_value->quantity * $qtyorder;
		                      			// echo $base_value->product_base_id.'='.$base_value->quantity.'QT'.$quantity_require.'QT<br>';

		                                $Cronjob = Warehouse::getCronDeduction($base_value->product_base_id,$quantity_require,'CRON',$log_item_id);
		                                
		                               // print_r($Cronjob);
                                        
		                                if(count($Cronjob)>0){
						   				if($Cronjob['deduction'] == 1){
                                        // echo 'ok';
						   					foreach ($Cronjob['cronresult'] as $value) {

							   					$row = array('batch_id' 			=> $batch_id,
									   						 'log_item_id'			=> $log_item_id,
										    				 'basic_warehouse_id' 	=> $value['warehouseid'],
										    				 'base_warehouse_id' 	=> $value['warehouseidbase'],
										    				 'basic_deduction' 	 	=> $value['stockdeductbase'],
										    				 'base_deduction' 	 	=> $value['stockdeductbasic'],
										    				 'insert_by'      		=> 'API_SYSTEM',
									                		 'created_at'   		=> date('Y-m-d H:i:s'),	
										    			);
										    			
										    	$stockin = DB::table('jocom_warehouse_cronjob_history')
										    					->insert($row);
	     									}

							   				$batchresult = LogisticBatch::where('id','=',$batch_id)
	                                                             ->first();
			                                $batchresult->deduction = 1;
			                                $batchresult->save();
			                                
	    								}

							   		  }
	    
		                      		}

								}
		                      	else 
						   		{
						   			//NOT BASE ITEM

						   			$Cronjob = Warehouse::getCronDeduction($log_value->product_id,$qtyorder,'CRON',$log_item_id);

						   			if(count($Cronjob)>0){
						   				if($Cronjob['deduction'] == 1){

						   					foreach ($Cronjob['cronresult'] as $value) {

							   					$row = array('batch_id' 			=> $batch_id,
									   						 'log_item_id'			=> $log_item_id,
										    				 'basic_warehouse_id' 	=> $value['warehouseid'],
										    				 'base_warehouse_id' 	=> $value['warehouseidbase'],
										    				 'basic_deduction' 	 	=> $value['stockdeductbase'],
										    				 'base_deduction' 	 	=> $value['stockdeductbasic'],
										    				 'insert_by'      		=> 'API_SYSTEM',
									                		 'created_at'   		=> date('Y-m-d H:i:s'),	
										    			);
										    	$stockin = DB::table('jocom_warehouse_cronjob_history')
										    					->insert($row);

							   				}

							   				$batchresult = LogisticBatch::where('id','=',$batch_id)
	                                                             ->first();

			                                $batchresult->deduction = 1;
			                                $batchresult->save();

						   				}

							   		}
						   			
						   		}


						  // 		echo '<pre>';
						  // 		print_r($Cronjob);
						  // 		echo '</pre>';	

			   			}

			   		}

	   		 		
	   		 	}


	   		 }

	    } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
        }
        finally{
            if($isError == 0){
                DB::commit();
                 //echo 'ok';
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }


   } 

    
    public function anyCheck(){

   		// $result = Warehouse::getStockavailable('7952','21','CRON');
   	//	$result =Warehouse::getBasicreservedvalue('7879');
   		// echo '<pre>';
   		// print_r($result);
   		// echo '</pre>';		

   		$startdate = Date('Y-m-d')." 00:00:00";
        $enddate   = Date('Y-m-d')." 23:59:59";


   		$LogisticBatch = DB::table('logistic_batch')
	   							->where('batch_date','>=',$startdate)
	   							->where('batch_date','<=',$enddate)
	   							->where('deduction','=',0)
	   							->get(); 
	   		
	   		 if(count($LogisticBatch)>0){

	   		 	foreach ($LogisticBatch as $key => $value) {
	   		 		$batch_id = $value->id; 
	   		 		$log_id	  = $value->logistic_id; 

	   		 		$log_items = DB::table('logistic_transaction_item')
	   		 						->where('logistic_id','=',$log_id)
	   		 						->get();
	   		 		
	   		 		

			   		if(count($log_items)>0){
			   			
			   			foreach ($log_items as $log_key => $log_value){
			   				$qtyorder = 0;
			   				$qtyorder = $log_value->qty_order;
			   				$log_item_id = $log_value->id;

			   				$BaseItems = DB::table('jocom_product_base_item AS JPBI')
		                                ->select(array(
		                                    'JPBI.*','JP.sku'
		                               	 ))
		                                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')  
		                                ->where("JPBI.product_id",$log_value->product_id)
		                                ->where("JPBI.price_option_id",$log_value->product_price_id)
		                                ->where("JPBI.status",1)
		                                ->get();
		      

		                      	if(count($BaseItems)>0){
		                      		//BASE ITEM
		                      				
		                      		foreach ($BaseItems as $base_key => $base_value) {
		                      			$quantity_require = 0;
		                      			$quantity_require = $base_value->quantity * $qtyorder;
		                      			// echo $base_value->product_base_id.'='.$base_value->quantity.'QT'.$quantity_require.'QT<br>';

		                                $Cronjob = Warehouse::getStockavailable($base_value->product_base_id,$quantity_require,'CMS',$log_item_id);
		                                
		                                
	    
		                      		}

								}
		                      	else 
						   		{
						   			//NOT BASE ITEM

						   			$Cronjob = Warehouse::getStockavailable($log_value->product_id,$qtyorder,'CMS',$log_item_id);

						   			
						   			
						   		}


						   		echo '<pre>';
						   		print_r($Cronjob);
						   		echo '</pre>';	

			   			}

			   		}

	   		 		
	   		 	}


	   		 }

   }
   
    public function anyCheck1(){
        try{
             DB::beginTransaction();
   		$result =Warehouse::getSorterdeducation();
        } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
        }
        finally{
            if($isError == 0){
                DB::commit();
               
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }

    }
    
    public function anyStock(){
   	
   		$result3 = Warehouse::Stocklevelnotification();

   }
   
   public function anyMigratestock(){

    	try{

    			$dataarray = array();
    			$dataarrayin = array();
    			
    			$WarehouseReserved = DB::table('jocom_warehouse_product_reserved')
    										->where('id','>=',60584)
    	                                    ->where('id','<=',60952)
    								// 		->orderby('id','ASC')
    										->get();

    // 			$WarehouseReserved = DB::table('jocom_warehouse_product_reserved')->select('id','product_warehouse_id','total_reserved','transaction_id','logistic_id','logistic_item_id','created_at','created_by')
    // 	                                    ->where('id','>=',1)
    // 	                                    ->where('id','<=',10000)
    // 								// 		->orderby('id','ASC')
    // 										->get();
    			$type = "OUT";	
    			$type1 = "IN";	
    			if(count($WarehouseReserved)>0){
    				foreach ($WarehouseReserved as $WRD) {

    					$productwarehouseid = 0; 
    					$totalreserved = 0;
    					$transactionid = 0; 
    					$logisticid = 0;
    					$logisticitemid = 0; 
    					$createdat = "";
    					$createdby = "";
    					



    					$productwarehouseid = $WRD->product_warehouse_id;
    					$totalreserved = $WRD->total_reserved;
    					$transactionid = $WRD->transaction_id;
    					$logisticid = $WRD->logistic_id;
    					$logisticitemid = $WRD->logistic_item_id;
    					$createdat = $WRD->created_at;
    					$createdby = $WRD->created_by;
    					$updatedat = $WRD->updated_at;
    					$updatedby = $WRD->updated_by;

    					
    					$data = Warehouse::getProductID($productwarehouseid);
    					$productid = $data['productid'];

    					$temparray = array('product_warehouse_id' => $productwarehouseid,
    									  'product_id' => $productid,
    									  'type' => $type, 
    									  'quantity' => $totalreserved, 
    									  'reference_no' => $transactionid.'-'.$logisticid.'-'.$logisticitemid,  
    									  'created_by' => $createdby, 
    									  'created_at' => $createdat, 
    									  'updated_by' => $updatedby, 
    									  'updated_at' => $updatedat, 


    						);

    					array_push($dataarray, $temparray);

    					// $WarehouseHistory = new WarehouseProductHistory();
    					// $WarehouseHistory->product_warehouse_id = $productwarehouseid;
    					// $WarehouseHistory->product_id = $productid;
    					// $WarehouseHistory->type = $type;
    					// $WarehouseHistory->quantity = $totalreserved;
    					// $WarehouseHistory->reference_no = $transactionid.'-'.$logisticid.'-'.$logisticitemid;
    					// $WarehouseHistory->created_by = $createdby;
    					// $WarehouseHistory->created_at = $createdat;
    					// $WarehouseHistory->updated_by = $createdby;
    					// $WarehouseHistory->updated_at = $createdat;
    					// $WarehouseHistory->save();

    					$date = date('Y-m-d', strtotime($createdat));
    					
    					// $data = self::getStockinHistory($date);	
    					

    					
    					// print_r(array_column($dataarrayin,'created_at'));
    					// print_r(in_array($date,array_column($dataarrayin,'created_at')));
    					// echo '000'.$date.'000';
    					


    					if(!empty($dataarrayin)){

    						$validate = self::checkarray($date,array_column($dataarrayin,'created_at'));
    					}

    				

    					// echo $validate; 


    					if(empty($dataarrayin) || $validate == 0){
    						$startdate = $date." 00:00:00";
       						$enddate   = $date." 23:59:59";

    						$StockinResult = DB::table('jocom_warehouse_stockin')
				    						 ->where('created_at','>=',$startdate)
				    						 ->where('created_at','<=',$enddate)
				    						 ->get();
				    		// echo '<pre>';print_r($StockinResult);echo '</pre>';				 
					    	if(count($StockinResult)>0){

					    		foreach ($StockinResult as $WSIN) {

					    			$productdata = Warehouse::getStockinhandReserved($WSIN->product_id);

					    			$productwarehouseid = $productdata['warehouseID'];
					    			$totalreserved = $WSIN->unit;



									$temparray_1 = array('product_warehouse_id' => $productwarehouseid,
				    									  'product_id' =>  $WSIN->product_id,
				    									  'type' => $type1, 
				    									  'quantity' => $totalreserved, 
				    									  'reference_no' => '',  
				    									  'created_by' => $WSIN->insert_by, 
				    									  'created_at' => $WSIN->created_at, 
				    									  'updated_by' => $WSIN->modify_by, 
				    									  'updated_at' => $WSIN->updated_at, 


				    						);

				    					array_push($dataarrayin, $temparray_1);

				    					// echo 'Ok';

					    		}
					    	}

    					}

    				}
    					$MergeArrayresult = array(); 
    					$MergeArrayresult =  array_merge($dataarray,$dataarrayin);
    					
    					$orderby = "created_at";

    					foreach ($MergeArrayresult as $key => $row) {
							    $created_at[$key]  = $row['created_at'];
							}

    					array_multisort($created_at,SORT_ASC,$MergeArrayresult); 


				    	foreach ($MergeArrayresult as $WSHIS) {
				    		echo '.';
				    		$WarehouseHistory = new WarehouseProductHistory();
	    					$WarehouseHistory->product_warehouse_id = $WSHIS['product_warehouse_id'];
	    					$WarehouseHistory->product_id = $WSHIS['product_id'];
	    					$WarehouseHistory->type = $WSHIS['type'];
	    					$WarehouseHistory->quantity = $WSHIS['quantity'];
	    					$WarehouseHistory->reference_no = $WSHIS['reference_no'];
	    					$WarehouseHistory->created_by = $WSHIS['created_by'];
	    					$WarehouseHistory->created_at = $WSHIS['created_at'];
	    					$WarehouseHistory->updated_by = $WSHIS['updated_by'];
	    					$WarehouseHistory->updated_at = $WSHIS['updated_at'];
	    					$WarehouseHistory->save();
    					}

    			}						 	

    	}catch (Exception $ex) {
	            $isError = 1;
	            $response = 0;
	            echo $ex->getMessage();
	    }finally {

	    	echo 'Done';
	    }


    }

    public static function checkarray($date, $data = array()) {
      // sort($data);
      $response = 0;
      for($i=0; $i<count($data);$i++){
          if($date == date('Y-m-d', strtotime($data[$i]))) {
            $response = 1;
            break;
          }
      }
      return $response;
    }
   
    public function anyMigratestock1(){
        // die('ok');
    	try{
    	       $type = "OUT";
    	       $WarehouseReserved = DB::table('jocom_warehouse_product_reserved')->select('id','product_warehouse_id','total_reserved','transaction_id','logistic_id','logistic_item_id','created_at','created_by')
    	                                    ->where('id','>=',1)
    	                                    ->where('id','<=',10000)
    								// 		->orderby('id','ASC')
    										->get();
    // 			$WarehouseReserved = WarehouseProductReserved::select('id','product_warehouse_id','total_reserved','created_at','created_by')->get();
    								// 		->orderby('id','ASC')
    										
    				// 		print_r($WarehouseReserved);				
    				
    				
    		     //	die('ok');
    		
    // 			echo $WarehouseReserved;
    			
    			if(count($WarehouseReserved) > 0) {
    				foreach ($WarehouseReserved as $WRD) {
                        echo ".";		
    					$productwarehouseid = 0; 
    					$totalreserved = 0;
    					$transactionid = 0; 
    					$logisticid = 0;
    					$logisticitemid = 0; 
    					$createdat = "";
    					$createdby = "";

           

    					$productwarehouseid = $WRD->product_warehouse_id;
    					$totalreserved = $WRD->total_reserved;
    					$transactionid = $WRD->transaction_id;
    					$logisticid = $WRD->logistic_id;
    					$logisticitemid = $WRD->logistic_item_id;
    					$createdat = $WRD->created_at;
    					$createdby = $WRD->created_by;
    					$updatedat = $WRD->updated_at;
    					$updatedby = $WRD->updated_by;

    					$data = Warehouse::getProductID($productwarehouseid);
    					$productid = $data['productid'];

    					$WarehouseHistory = new WarehouseProductHistory();
    					$WarehouseHistory->product_warehouse_id = $productwarehouseid;
    					$WarehouseHistory->product_id = $productid;
    					$WarehouseHistory->type = $type;
    					$WarehouseHistory->quantity = $totalreserved;
    					$WarehouseHistory->reference_no = $transactionid.'|'.$logisticid.'|'.$logisticitemid;
    					$WarehouseHistory->created_by = $createdby;
    					$WarehouseHistory->created_at = $createdat;
    					$WarehouseHistory->updated_by = $createdby;
    					$WarehouseHistory->updated_at = $createdat;

    					$WarehouseHistory->save();
                        
                        $date = date('Y-m-d', strtotime($createdat));
    					
    					self::getStockinHistory($date);	
    					
    				}

    			}						 	

    	}catch (Exception $ex) {
	    
	            $errorMessage = $ex->getMessage();
	            echo $errorMessage;
	            die();
	    }finally {
	    	echo 'Done';
	    }
    

    }
    
    public static function getStockinHistory($date){
    	$startdate = $date." 00:00:00";
        $enddate   = $date." 23:59:59";

        $type = 'IN';

        $validateResult = WarehouseProductHistory::where('created_at','LIKE','%'.$date.'%')
        										 ->where('type','=',$type)
        										 ->get();

        if(count($validateResult) == 0){
        	$StockinResult = DB::table('jocom_warehouse_stockin')
    						 ->where('created_at','>=',$startdate)
    						 ->where('created_at','<=',$enddate)
    						 ->get();

	    	if(count($StockinResult)>0){

	    		foreach ($StockinResult as $WSIN) {

	    			$productdata = Warehouse::getStockinhandReserved($WSIN->product_id);

	    			$productwarehouseid = $productdata['warehouseID'];
	    			$totalreserved = $WSIN->unit;


	    			$WarehouseHistory = new WarehouseProductHistory();
					$WarehouseHistory->product_warehouse_id = $productwarehouseid;
					$WarehouseHistory->product_id = $WSIN->product_id;
					$WarehouseHistory->type = $type;
					$WarehouseHistory->quantity = $totalreserved;
					$WarehouseHistory->reference_no = '';
					$WarehouseHistory->created_by = $WSIN->insert_by;
					$WarehouseHistory->created_at = $WSIN->created_at;
					$WarehouseHistory->updated_by = $WSIN->modify_by;
					$WarehouseHistory->updated_at = $WSIN->updated_at;

					$WarehouseHistory->save();


	    		}
	    	}

        }	

    	

    }
    
    public function anyLogisticscronjob(){

        $result = LogisticTransaction::getTransactionpending2weeks();
        $result = LogisticTransaction::getBatchlistwithoutimage();
    }
    
    
    // Write Off 
   
   public function anyWriteoff(){
      
        return View::make('writeoff.index');
       
   }

   public function anyGetwriteoff($id){

	$writeOffs = DB::table('jocom_write_off AS JWO')
		->select('JWO.*')
		->where('JWO.id','=',$id )->first();

	$WriteOffDetails = DB::table('jocom_write_off_details AS JWP')
		->select(array(
			'JWP.*','JP.name','JP.sku','JPP.label'
			))
		->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JWP.product_id')  
		->leftJoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
		->where("JPP.default",1)
		->where("JWP.sw_id",$id)
		->where("JWP.activation",1)
		->get();

	$response = array(
		"write_off_info" => $writeOffs,
		"write_off_details" => $WriteOffDetails
	);

	   	return $response;
   }

   public function anyListwriteoff(){

	/*
	- Application No
	- Requestor
	- Approved By
	- Received By
	- Item Summary
	- Status
	- File 
	- Action
	*/
		$writeOffs = DB::table('jocom_write_off AS JWO')
			->select('JWO.id','JWO.doc_no','JWO.prepared_by','JWO.approved_by','JWO.received_by', 'JWO.status','JWO.write_off_attachment')
			->where('JWO.activation','=','1')->orderBy('JWO.id', 'desc');

		return Datatables::of($writeOffs)
			->edit_column('status', function($row){ 
					if($row->status == '2'){
						return "Closed";
					}else{
						return "Open";
					}
			})
			->edit_column('item_summary', function($row){ 
					
					$WriteOffDetails = DB::table('jocom_write_off_details AS JWP')
							->select(array(
								'JWP.*','JP.name','JP.sku'
								))
							->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JWP.product_id')  
							->where("JWP.sw_id",$row->id)
							->where("JWP.activation",1)
							->get();
					$return = '';
					foreach ($WriteOffDetails as $key => $value) {
						if($return == ''){
							$return = $return."<li>".$value->name;
						}else{
							$return = $return."<br><li>".$value->name;
						}
						$return = $return.$value->name;
					}

					return $return;
			})
			->edit_column('file', function($row){ 

				if($row->write_off_attachment != null){
					return '<a target="_blank" href="/warehouse/writedownload/'.$row->id.'" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i></a>';
				}else{
					return  '';
				}
				
			})
			->edit_column('action', function($row){ 

				if($row->status == 1){
					return '<a class="btn btn-default btn-sm edit-write-off"  data-id="'.$row->id.'"><i class="fa fa-pencil"></i> edit</a>
					<a target="_blank" href="/warehouse/writeoffdoc/'.$row->id.'" class="btn btn-default btn-sm print-write-off" data-id="'.$row->id.'"><i class="fa fa-print"></i> Print</a>
					<a class="btn btn-default btn-sm upload-write-off" data-id="'.$row->id.'"><i class="fa fa-file"></i> Upload</a>';
				}else{
					return '';
				}
			
			})
			->make(true);

   }

   
   
   /*
   	@desc : Store write off information
   */
   public function anyStorewriteoff(){

		
		$isError = false;
		$message = 'Failed to save!';

		try{

			DB::beginTransaction();

			$remarks = Input::get("remarks");
			$username = Session::get("username");
			$products = Input::get("products");
			$doc_date = Input::get("doc_date");

			$running_number = DB::table('jocom_running')
			->select('*')
			->where('value_key', '=', 'write_off')->first();
	
			$batchNo = str_pad($running_number->counter + 1,5,"0",STR_PAD_LEFT);
			$NewRunner = Running::find($running_number->id);
			$NewRunner->counter = $running_number->counter + 1;
			$NewRunner->save();

			$NewbatchNo =  'SW-'.$batchNo;
			
			$WriteOff = new WriteOff;
			$WriteOff->doc_no = $NewbatchNo;
			$WriteOff->doc_date = $doc_date;
			$WriteOff->remarks = $remarks;
			$WriteOff->prepared_by = $username;
			$WriteOff->status = 1;
			$WriteOff->save();

			if($WriteOff->save()){
				$writeOffDocID = $WriteOff->id;
				foreach ($products as $key => $value) {
					$WriteOffDetails = new WriteOffDetails;
					$WriteOffDetails->sw_id = $writeOffDocID;
					$WriteOffDetails->product_id = $value['product_id'];
					$WriteOffDetails->expired_date = $value['expired_date'];
					$WriteOffDetails->quantity = $value['quantity'];
					$WriteOffDetails->save();
				}
			}

			$message = 'Record saved successfully!';
			
		}catch(exception $ex){
		
			$isError = true;
			$message = 'Failed to save!';
		}finally{

			if($isError == false){
				DB::commit();
			}else{
				DB::rollback();
			}
			return array(
				"error" => $isError,
				"message" => $message
			);
		}
       
   }

   public function anyUpdatewriteoff(){

		
	$isError = false;
	$message = 'Failed to save!';

	try{

		DB::beginTransaction();

		// echo "<pre>";
		// print_r(Input::all());
		// echo "</pre>";
		// die();

		$id = Input::get("id");
		$remarks = Input::get("remarks");
		$username = Session::get("username");
		$products = Input::get("products");
		$doc_date = Input::get("doc_date");
		$approved_by = Input::get("approved_by");
		$received_by = Input::get("received_by");

		$WriteOff = WriteOff::find($id);
		$WriteOff->doc_date = $doc_date;
		$WriteOff->remarks = $remarks;
		$WriteOff->approved_by = $approved_by;
		$WriteOff->received_by = $received_by;
		

		if($WriteOff->save()){
			$writeOffDocID = $WriteOff->id;
			//echo $writeOffDocID;
			$WriteOffDetails = WriteOffDetails::where("sw_id",$writeOffDocID)->update(['activation' => 0]);
			// foreach ($$WriteOffDetails as $key => $value) {
			// 	# code...
			// }
		
			// $WriteOffDetails->activation = 0;
			// $WriteOffDetails->save();


			foreach ($products as $key => $value) {
				$WriteOffDetails = new WriteOffDetails;
				$WriteOffDetails->sw_id = $writeOffDocID ;
				$WriteOffDetails->product_id = $value['product_id'];
				$WriteOffDetails->expired_date = $value['expired_date'];
				$WriteOffDetails->quantity = $value['quantity'];
				$WriteOffDetails->save();
			}
		}

		$message = 'Record saved successfully!';
		
	}catch(exception $ex){
	
		$isError = true;
		$message = $ex->getMessage().$ex->getLine();
	}finally{

		if($isError == false){
			DB::commit();
		}else{
			DB::rollback();
		}
		return array(
			"error" => $isError,
			"message" => $message
		);
	}
   
}
   
   	public function anyApprovewriteoff(){
       
		$isError = false;
		$message = 'Failed to save!';

		try{

			DB::beginTransaction();

			$id = Input::get("id");
			$username = Session::get("username");
			
			$WriteOff = WriteOff::find($id);
			$WriteOff->status = 2;
			$WriteOff->approved_by = $username;
			$WriteOff->save();
			
			$message = 'Record saved successfully!';
			
		}catch(exception $ex){
		
			$isError = true;
			$message = 'Failed to save!';
		}finally{

			if($isError == false){
				DB::commit();
			}else{
				DB::rollback();
			}
			return array(
				"error" => $isError,
				"message" => $message
			);
		}   
	
   }

   	public function anyUploaddoc(){
       
		$isError = false;
		$message = 'Failed to save!';

		try{

			// echo "<pre>";
			// print_r(Input::all());
			// echo "</pre>";
			// die();

			DB::beginTransaction();

			$id = Input::get("id");
			$file_path = Config::get('constants.STOCK_FILE_WRITE_OFF');
                
			// $htmlFile = fopen($file_path."/".$id.".pdf", "w");
			// fwrite($htmlFile, $htmlContent);
			// fclose($htmlFile);
			
			if(Input::hasFile('file')) {
				$file                   = Input::file('file');
				$file_ext               = $file->getClientOriginalExtension();
				$filename               = date("Ymdhis")."_".$id.".".$file_ext;
				$file_path = Config::get('constants.STOCK_FILE_WRITE_OFF');
				Input::file('file')->move($file_path . '/', $filename);

				$WriteOff = WriteOff::find($id);
				$WriteOff->status = 2;
				$WriteOff->write_off_attachment = $filename;
				$WriteOff->save();
			}
			$message = 'File uploaded successfully!';
			
		}catch(exception $ex){
		
			$isError = true;
			$message = 'Failed to upload!';
			$errorMsg= $ex->getMessage();
		}finally{

			if($isError == false){
				DB::commit();
			}else{
				DB::rollback();
			}
			return array(
				"error" => $isError,
				"message" => $message,
				"error_msg" =>$errorMsg
			);
		}   

	}

   	public function anyRejectwriteoff(){
       
		$isError = false;
		$message = 'Failed to save!';

		try{

			DB::beginTransaction();

			$id = Input::get("id");
			$username = Session::get("username");
			
			$WriteOff = WriteOff::find($id);
			$WriteOff->status = 3;
			$WriteOff->approved_by = $username;
			$WriteOff->save();
			
			$message = 'Record saved successfully!';
			
		}catch(exception $ex){
		
			$isError = true;
			$message = $ex->getMessage().$id;
		}finally{

			if($isError == false){
				DB::commit();
			}else{
				DB::rollback();
			}
			return array(
				"error" => $isError,
				"message" => $message
			);
		}   

	}



   public function anyCheckproduct($id){

		//$id = Input::get("product_id");

		$WareHouseProduct = DB::table('jocom_warehouse_products AS JWP')
			->select(array(
				'JWP.*','JP.name','JP.sku','JPP.label'
				))
			->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JWP.product_id')  
			->leftJoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
			->where("JWP.product_id",$id)
			->first();


		if($WareHouseProduct){
			return Response::json(array(
				'name' => $WareHouseProduct->name, 
				'product_id' => $WareHouseProduct->product_id, 
				'sku' => $WareHouseProduct->sku,
				'label' => $WareHouseProduct->label));
		}else{
			return 0;
		}

   }

	public function anyWriteoffdoc($id){

		$writeOffs = DB::table('jocom_write_off AS JWO')
			->select('JWO.*','JSA.full_name AS NamePrepare','JSA2.full_name AS NameApproved')
			->leftJoin('jocom_sys_admin AS JSA', 'JSA.username', '=', 'JWO.prepared_by')
			->leftJoin('jocom_sys_admin AS JSA2', 'JSA2.username', '=', 'JWO.approved_by')
			->where('JWO.id','=',$id )->first();

		$WriteOffDetails = DB::table('jocom_write_off_details AS JWP')
			->select(array(
				'JWP.*','JP.name','JP.sku','JPP.label'
				))
			->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JWP.product_id')  
			->leftJoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
			->where("JPP.default",1)
			->where("JWP.activation",1)
			->where("JWP.sw_id",$id)
			->get();

		$response = array(
			"write_off_info" => $writeOffs,
			"write_off_details" => $WriteOffDetails
		);

		return View::make('template.write_off_form')->with("data",$response);

	}

	public function anyWritedownload($id){

		$WriteOff = WriteOff::find($id);
		// print_r($WriteOff);
		$file = "media/pdf/writeoff/".$WriteOff->write_off_attachment;
		// echo $file;
        if(is_file($file)) {
            return Response::download($file);
        }
        else {
            echo "<br>File not exists!";
        }
		
	}

   
   
   // Write Off
   
    public function anyManage() {

        $sizes = DB::table('jocom_warehouse_stock_size')->get();

        $negative_stocks = DB::table('jocom_warehouse_products')
        					->where('actual_stock', '<', 0)
        					->select('product_id', 'actual_stock')
        					->get();

		return View::make('warehouse.manage')->with(['sizes' => $sizes, 'negative_stocks' => $negative_stocks]);
	}

	public function getNegative() {
		$negative_stocks = DB::table('jocom_warehouse_products')
							->join('jocom_products', 'jocom_warehouse_products.product_id', '=', 'jocom_products.id')
        					->where('actual_stock', '<', 0)
        					->select('jocom_warehouse_products.product_id', 'jocom_products.sku', 'jocom_warehouse_products.actual_stock', 'jocom_products.name');

        return Datatables::of($negative_stocks)->make();
	}

	public function getSimplelist(){

    	 try{

    	 	$MalaysiaCountryID = 458;
	        $regionid = 0;
	        $regionhq = 0;
	        $region = "";

	        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	        $stateName = array();

	        foreach ($SysAdminRegion as  $value) {
	            $regionid = $value;
	           
	        }

	        if (isset($regionid) && $regionid ==0){
	            $region = "All Region";
	        }
	        else{
	            $resultregion = LogisticTransaction::getDriverRegionName($regionid);
	            $region=$resultregion->region;
	        }


	        if (isset($regionid) && $regionid ==0){
             $products = DB::table('jocom_warehouse_products')->select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    //	 	 $products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							 ->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
							->where('jocom_warehouse_products.active','=',1)
    	 	 				->where('jocom_product_price.status','!=',2);
    	 	 				// ->whereNotIn('jocom_warehouse_productslinks.status',2);
    	 	 } else if (isset($regionid) && $regionid ==1){
    	 	 	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
    	 	 				// ->where('jocom_warehouse_products.region_id','=',$regionid);
    	 	 				->where(function ($query){
                                $query->where('jocom_warehouse_products.region_id','=',0)
                                      ->orwhere('jocom_warehouse_products.region_id','=',1);
                               })
                            ->where('jocom_warehouse_products.active','=',1)   
    	 	 				->where('jocom_product_price.status','!=',2);
    	 	 } else {
                $products = DB::table('jocom_warehouse_products')->select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 //	$products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 				->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 				->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 				// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 				->leftJoin('jocom_warehouse_productslinks', function($join)
							        {
							            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_productslinks.status', '=', 1);
							        })
							->leftJoin('jocom_warehouse_products_baselinks', function($join)
							        {
							            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
							            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
							        })
    	 	 				->where('jocom_warehouse_products.region_id','=',$regionid)
    	 	 				->where('jocom_warehouse_products.active','=',1)
    	 	 				->where('jocom_product_price.status','!=',2);

    	 	 }
    	 	 
    		return Datatables::of($products)
    				->make();

    	    } catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }

    public function getSizelist() {
    	$sizes = DB::table('jocom_warehouse_stock_size')
    				->select('id', 'label', 'quantity');

    	return Datatables::of($sizes)
    			->add_column('Action', function ($p) {
    				return '
    					<button class="btn btn-primary" onclick="updateSize('.$p->id.')"><i class="fa fa-pencil"></i></button>
	                    <button class="btn btn-danger" onclick="deleteSize('.$p->id.')"><i class="fa fa-remove"></i></button>';
    			})
    			->make();
    }

    public function anySavestocksize() {
    	$label = Input::get('label');
    	$quantity = Input::get('quantity');

    	Session::flash('success', 'Successfully added.');
    	DB::table('jocom_warehouse_stock_size')
    		->insert(array('label' => $label, 'quantity' => $quantity));

    	return Response::json(array('message' => 'Success'));
    }

    public function anyUpdatestocksize() {
    	$id = Input::get('id');
    	$label = Input::get('label');
    	$quantity = Input::get('quantity');

    	DB::table('jocom_warehouse_stock_size')
    		->where('id', '=', $id)
    		->update(['label' => $label, 'quantity' => $quantity]);
    	Session::flash('success', 'Successfully edited.');
    	return Response::json(array('success' => 'Success.')); 
    }

    public function anyDeletestocksize() {
    	$id = Input::get('id');
    	DB::table('jocom_warehouse_stock_size')->where('id', '=', $id)->delete();
    	return Response::json(array('success' => 'Success.'));
    }

    public function getProductid($sku) {
    	$product = Product::where('sku', '=', $sku)->select('id','name')->first();

    	if ($product == null) {
    		return Response::json(array('error' => 'Invalid SKU.'));
    	} else {
    		if (Warehouse::isExists($product->id) == 1) {

    			if (Warehouse::isExistsBase($product->id) == 0) {
    				$quantity = DB::table('jocom_warehouse_productslinks')
    								->where('product_id', '=', $product->id)
    								->select('quantity')
    								->first()->quantity;
	    			return Response::json(array('product_id' => $product->id, 'product_name' => $product->name, 'quantity' => $quantity));
	    		} else {
	    			return Response::json(array('error' => 'Not base product.'));
	    		}
    		} else {
    			return Response::json(array('error' => 'Not stock product.'));
    		}
    		
    		
    	}
    }

    public function getSizedetail($id) {
    	$size = DB::table('jocom_warehouse_stock_size')->where('id', '=', $id)->first();
    	return Response::json(array('size' => $size));
    }

    public function getSizelist1() {
    	return DB::table('jocom_warehouse_stock_size')->get();
    }


    public function anyExportstockin() {
          ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
    	$stocks = DB::select('SELECT p.id,p.sku, p.name, wp.stockin_hand, s.company_name, si.expiry_date, si.created_at
								  FROM jocom_warehouse_products as wp
								  JOIN jocom_product_seller as ps ON wp.product_id=ps.product_id
								  JOIN jocom_seller as s ON ps.seller_id=s.id
								  JOIN jocom_products as p ON wp.product_id=p.id
								  LEFT JOIN (select a.product_id,expiry_date,created_at from jocom_warehouse_stockin a 
									left join
									(select product_id,max(created_at) as max_dt from jocom_warehouse_stockin group by product_id) b
									on (a.product_id=b.product_id and a.created_at=b.max_dt)
									where b.product_id is not null) si ON wp.product_id=si.product_id
								  WHERE wp.active=1 AND ps.activation=1 AND p.status!=2
								  ORDER BY s.company_name ASC, p.name ASC');

    	$company_list = array();

    	foreach ($stocks as $stock) {
    		$company_list[$stock->company_name][$stock->sku]['name'] = $stock->name;
    		$company_list[$stock->company_name][$stock->sku]['stockin_hand'] = $stock->stockin_hand;
    		$company_list[$stock->company_name][$stock->sku]['expiry_date'] = $stock->expiry_date;
    		$company_list[$stock->company_name][$stock->sku]['last_stockin'] = $stock->created_at;
    	}

    	return  Excel::create('JOCOM_STOCKIN_'.date("dmyHis"), function($excel) use ($company_list) {
                    $excel->sheet('Warehouse', function($sheet) use ($company_list)
                    {   
                        $sheet->loadView('warehouse.templatestockin', array('company_list' => $company_list));
                    });
                })->download('xls');
    }
    
    public function anyStockinproductlog($product_id) {
    	$log = DB::table('jocom_warehouse_stockin')
    			->where('product_id', '=', $product_id)
    			->select('unit','expiry_date', 'insert_by', 'created_at', 'remark' )
    			->orderBy('created_at', 'desc');

    	return Datatables::of($log)
    			->make();
    }


public function getHistory(){

		try{

			$MalaysiaCountryID = 458;
			$regionid = 0;
			$regionhq = 0;
			$region = "";

			$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
			$stateName = array();

			foreach ($SysAdminRegion as  $value) {
				$regionid = $value;
				
			}

			if (isset($regionid) && $regionid ==0){
				$region = "All Region";
			}
			else{
				$resultregion = LogisticTransaction::getDriverRegionName($regionid);
				$region=$resultregion->region;
			}


			if (isset($regionid) && $regionid ==0){
				// $products = Warehouse::select('jocom_warehouse_products.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand','jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid','jocom_warehouse_products_baselinks.product_id as base_id')
    	 	 	// 			->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
    	 	 	// 			->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_products.product_id')
    	 	 	// 			// ->leftjoin('jocom_warehouse_productslinks','jocom_warehouse_productslinks.parent_product_id','=','jocom_warehouse_products.product_id')
    	 	 	// 			->leftJoin('jocom_warehouse_productslinks', function($join)
				// 			        {
				// 			            $join->on('jocom_warehouse_productslinks.parent_product_id', '=', 'jocom_warehouse_products.product_id')
				// 			            ->where('jocom_warehouse_productslinks.status', '=', 1);
				// 			        })
				// 			 ->leftJoin('jocom_warehouse_products_baselinks', function($join)
				// 			        {
				// 			            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'jocom_warehouse_products.product_id')
				// 			            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
				// 			        })
				// 			->where('jocom_warehouse_products.active','=',1)
    	 	 	// 			->where('jocom_product_price.status','!=',2);
				//   $products = DB::table('jocom_warehouse_products as jwp')
				// 	->select('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label', DB::raw('max(jws.created_at) as latest_created_at'))
				// 	->leftJoin('jocom_products as jp', 'jwp.product_id', '=', 'jp.id')
				// 	->leftJoin('jocom_product_price as jpp', 'jwp.product_id', '=', 'jpp.product_id')
				// 	->join(DB::raw('(SELECT product_id, MAX(created_at) AS created_at FROM jocom_warehouse_stockin GROUP BY product_id) as jws'), 'jwp.product_id', '=', 'jws.product_id')
				// 	->where('jwp.active', '=', 1)
				// 	->where('jpp.status', '!=', 2)
				// 	->groupBy('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label')
				// 	->orderBy('jws.created_at', 'desc');
				

				$products = DB::table('jocom_warehouse_stockin')
					->select('jocom_warehouse_stockin.product_id', 'jocom_products.sku', 'jocom_products.name', 'jocom_product_price.label', 'jocom_warehouse_stockin.created_at')
					->leftJoin('jocom_products', 'jocom_warehouse_stockin.product_id', '=', 'jocom_products.id')
					->leftJoin('jocom_product_price', 'jocom_warehouse_stockin.product_id', '=', 'jocom_product_price.product_id')
					->where('jocom_product_price.status', '!=', 2)
					->groupBy('jocom_warehouse_stockin.product_id')
					->orderBy('jocom_warehouse_stockin.created_at', 'desc');

				} else if (isset($regionid) && $regionid ==1){
					
					$products = DB::table('jocom_warehouse_products as jwp')
						->select('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label', DB::raw('max(jws.created_at) as latest_created_at'))
						->leftJoin('jocom_products as jp', 'jwp.product_id', '=', 'jp.id')
						->leftJoin('jocom_product_price as jpp', 'jwp.product_id', '=', 'jpp.product_id')
						->join(DB::raw('(SELECT product_id, MAX(created_at) AS created_at FROM jocom_warehouse_stockin GROUP BY product_id) as jws'), 'jwp.product_id', '=', 'jws.product_id')
						->where(function ($query){
							$query->where('jwp.region_id','=',0)
									->orwhere('jwp.region_id','=',1);
							})
						->where('jwp.active', '=', 1)
						->where('jpp.status', '!=', 2)
						->groupBy('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label')
						->orderBy('jws.created_at', 'desc');
				} else {

					$products = DB::table('jocom_warehouse_products as jwp')
						->select('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label', DB::raw('max(jws.created_at) as latest_created_at'))
						->leftJoin('jocom_products as jp', 'jwp.product_id', '=', 'jp.id')
						->leftJoin('jocom_product_price as jpp', 'jwp.product_id', '=', 'jpp.product_id')
						->join(DB::raw('(SELECT product_id, MAX(created_at) AS created_at FROM jocom_warehouse_stockin GROUP BY product_id) as jws'), 'jwp.product_id', '=', 'jws.product_id')
						->where('jwp.region_id','=',$regionid)
						->where('jwp.active', '=', 1)
						->where('jpp.status', '!=', 2)
						->groupBy('jwp.product_id', 'jp.sku', 'jp.name', 'jpp.label')
						->orderBy('jws.created_at', 'desc');

				}
				
			return Datatables::of($products)
					->make();

		} catch (Exception $ex) {
			echo $ex->getMessage();
		}

	}




public function anyStockinhistory() {

        $sizes = DB::table('jocom_warehouse_stock_size')->get();

        $negative_stocks = DB::table('jocom_warehouse_products')
        					->where('actual_stock', '<', 0)
        					->select('product_id', 'actual_stock')
        					->get();

		return View::make('warehouse.stockinhistory')->with(['sizes' => $sizes, 'negative_stocks' => $negative_stocks]);
	}
    
    public function anyStockupdate() {
    	
        $stocks = DB::table('jocom_stockin_id')
    			->select('product_id', 'qty')
    			->get();
    	
    	foreach ($stocks as $stock) {
    	    
    	
    	DB::table('jocom_warehouse_products')
    		->where('product_id', '=', $stock->product_id)
    		->update(array('stockin_hand' => $stock->qty));
    		
    		echo 'product_id-> '. $stock->product_id .' stockin_hand-> '.$stock->qty .'<br>';
    		
    	}
    	echo 'Updated';
    
    }
    
    public function anyStockmassimport() {
    	
        $stocks = DB::table('jocom_stockinmassimport')
    			->select('sku', 'stockin')
    			->get();
    	
    	foreach ($stocks as $stock) {
    	$sku = $stock->sku;     
    	$product = Product::where('sku', '=', $sku)->select('id','name')->first();    
    	
    	DB::table('jocom_warehouse_products')
    		->where('product_id', '=', $product->id)
    		->update(array('stockin_hand' => $stock->stockin));
    		
    		echo 'product_id-> '. $product->id .' stockin_hand-> '.$stock->stockin .'<br>';
    		
    	}
    	echo 'Updated';
    
    }
    
    public function anyFreecoupon(){
        $transid= 177770; 
        $coupon = 'FREEITEM';
        
      $freeitem  = MCheckout::FreecouponItemFoc($transid,$coupon);
        
    }
    
    public function anyInsvalue(){

		$result = Transaction::verorder();
	}
    
    public function anyExportrpt(){
        try {
        $result = DB::table('jocom_transaction AS JT')
	    				->select(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y') as Year"),
	    						 DB::raw("sum(JT.total_amount) as 'TransactionTotal'"),
	    						 "JT.delivery_country",
	    						 "JT.delivery_state",
	    						 "JT.delivery_city",
	    						 DB::raw("count(JT.id) as Nooftransaction")
	    						 //DB::raw("(select sum(jocom_transaction_details.unit) from jocom_transaction_details where jocom_transaction_details.transaction_id=JT.id) as BasketSize")
	    						 
	    					)
	    			//	->leftjoin('jocom_transaction_details as JTD', 'JTD.transaction_id', '=', 'JT.id')
	    			//	->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
	    				->where('JT.status','=','completed')
	    				->where(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y')"),'<>','0000')
	    				->groupBy(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y')"),'JT.delivery_country','JT.delivery_state','JT.delivery_city')
	    				->orderby(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y')"),'JT.delivery_country','JT.delivery_state','JT.delivery_city','DESC')
	    				->get();
	   $i=0; 				
	   foreach($result as $value){
	       $i=$i+1;
	       $year= $value->Year.'<br>';
	       //echo $value->TransactionTotal.'<br>';
	       $resultitem = DB::table('jocom_transaction_details AS JTD')
    	                    ->select(DB::raw("sum(JTD.unit) as 'unitTotal'"))
    	                   ->leftjoin('jocom_transaction as JT', 'JTD.transaction_id', '=', 'JT.id')
    	                   ->where('JT.status','=','completed')
    	    			   ->where(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y')"),'=',$value->Year)
    	    			   ->where(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y')"),'<>','0000')
    	    			   ->where('JT.delivery_country', $value->delivery_country)
    	    			   ->where('JT.delivery_state', $value->delivery_state)
    	    			   ->where('JT.delivery_city', $value->delivery_city)
    	                   ->first();
	       
	        echo '<pre>';
	        echo $i.',';
            echo $value->Year.',';
            echo $value->TransactionTotal.',';
            echo $value->Nooftransaction.',';
            echo $value->delivery_country.',';
            echo $value->delivery_state.',';
            echo $value->delivery_city.',';
            echo $resultitem->unitTotal;
            echo '</pre>';
            // die();
	      
	   }
 die();
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        } catch (Exception $ex) {
                echo $ex->getMessage();
            } 
    }
    
    public function anyExportmonthrpt(){
        
        try {
        $platform = ['portoromanorestaurant','portoromanomk','seppianpolpo','seppiapolpo','razermerchantservices','razerliftevent','molpay sdn bhd','molpay','virtuos','modalkuventures','jiawenregalglobal'];
        $result = DB::table('jocom_transaction AS JT')
	    				->select(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y-%m') as Month"),
	    				         DB::raw("count(JT.id) as Nooftransaction"),
	    						 DB::raw("sum(JT.total_amount) as 'TransactionTotal'"),
	    						 "JU.full_name",
	    						 "JT.buyer_username"
	    						 //DB::raw("(select sum(jocom_transaction_details.unit) from jocom_transaction_details where jocom_transaction_details.transaction_id=JT.id) as BasketSize")
	    						 
	    					)
	    			//	->leftjoin('jocom_transaction_details as JTD', 'JTD.transaction_id', '=', 'JT.id')
	    				->leftjoin('jocom_user AS JU', 'JU.id', '=', 'JT.buyer_id')
	    				->where('JT.status','=','completed')
	    				->whereIn('JT.buyer_username', $platform)
	    				->groupBy(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y-%m')"),'JT.buyer_username')
	    				->orderby('JT.id','DESC')
	    				->get();
	    
	   // echo '<pre>';
    //     print_r($result);
    //     echo '</pre>';
    //     die();
        
         $i=0; 				
	   foreach($result as $value){
	       $i=$i+1;
	       $year= $value->Month.'<br>';
	       //echo $value->TransactionTotal.'<br>';
	       $resultitem = DB::table('jocom_transaction_details AS JTD')
    	                    ->select(DB::raw("sum(JTD.unit) as 'unitTotal'"))
    	                   ->leftjoin('jocom_transaction as JT', 'JTD.transaction_id', '=', 'JT.id')
    	                   	->where('JT.status','=','completed')
    	    				->where(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y-%m')"),'<>','0000')
    	    				->where('JT.buyer_username', $value->buyer_username)
    	    				->where(DB::raw("DATE_FORMAT(JT.transaction_date, '%Y-%m')"),'=',$value->Month)
    	                   ->first();
	       
	        echo '<pre>';
	        echo $i.',';
            echo $value->Month.',';
            echo $value->TransactionTotal.',';
            echo $value->Nooftransaction.',';
            echo $resultitem->unitTotal.',';
            echo $value->full_name;
            echo '</pre>';
            // die();
	      
	   }
 die();
 
 
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        
    } catch (Exception $ex) {
                echo $ex->getMessage();
            } 
        
    }
    
    public function anyBaselinksupdate()
    {
     //PRODUCT BASE LINKS

            $ref_no 	 = 0;
            $bflag 	 	 = 0;
            $baseprod_id = 0;
            $default	 = 1;	

			$baseprod_id = $product->id;
			$basestatus = Input::get('base_status_st');
			$baseres = DB::table('jocom_warehouse_products_baselinks')->get();
// 			echo '<pre>';
// 			print_r($baseres);
// 			echo '</pre>';
            die('Done');
			if(count($baseres)>0){
			    foreach($baseres as $keyres){
			        
			        
                    $ref_no 	 = 0;
                    $bflag 	 	 = 0;
                    $baseprod_id = 0;
			        
			         $refno = Warehouse::genReferenceno();
			         $baseprod_id = $keyres->variant_product_id;
			         $Resultdeleted = ProductNode::where("parent_product_id",$baseprod_id)->first();
			         $linkid =  $Resultdeleted->product_id;
			         
			        echo $keyres->variant_product_id .'-'. $linkid.'<br>';
			        
			        $Resultbase = ProductBase::where("variant_product_id",$baseprod_id)->first();
    				$Resultbase->ref_no = $refno;
                    $Resultbase->modify_by = Session::get('username');
    	            $Resultbase->save();      
			        
			        $Resultlinks = ProductNode::where("product_id",$linkid)->first();
    				$Resultlinks->ref_no = $refno;
                    $Resultlinks->modify_by = Session::get('username');
    	            $Resultlinks->save(); 
    	            
    	            $base_update_ref = Warehouse::where('product_id','=',$baseprod_id)->first();
                    $base_update_ref->ref_no = $refno;
                    $base_update_ref->save();
                    
                    $link_update_ref = Warehouse::where('product_id','=',$linkid)->first();
                    $link_update_ref->ref_no = $refno;
                    $link_update_ref->save();
    	            
    	            
    	            
    	            
    	            
			          
			         
			          
			         //echo  $refno;
			         
			         
			         //die('d');
			         
			    }
			   
			}
			
			die('In');
			
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
	           // DB::table('jocom_warehouse_products_baselinks')->insert(array(
	           //                 'variant_product_id'    => $baseprod_id,
	           //                 'product_id'            => $baseprod_id,
	           //                 'ref_no'                => $refno,
	           //                 'default'               => $default, 
	           //                 'insert_by'             => Session::get('username'),
	           //                 'created_at'            => date('Y-m-d H:i:s'),
	           //                 'modify_by'             => Session::get('username'),
	           //                 'updated_at'            => date('Y-m-d H:i:s')
	           //                 )
	           //         );
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
        
    }
    
    

}

?>