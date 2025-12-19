<?php

class ImportProductController extends BaseController
{
	protected $product;
	protected $zone;
	protected $category;
	protected $seller;
	protected $price;
	protected $delivery;

	public function __construct(Product $product, Zone $zone, Category $category, Seller $seller, Price $price, Delivery $delivery)
	{

		$this->product = $product;
		$this->zone = $zone;
        $this->category = $category;
		$this->seller = $seller;
		$this->price = $price;
		$this->delivery = $delivery;

	}

	/**
     * Display create product form
     * @return Response
     */
	public function anyIndex()
	{
		$sellers = $this->seller->sortByCompany();

		foreach ($sellers as $seller)
		{
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

		// $category_options = $this->category->orderBy('category_parent', 'asc')->orderBy('category_name', 'asc')->lists('category_name','id');
		// 
		// $categoriesOptions = array_merge($parentCategory, Product::arrangeCategories($temp->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- '));

        $categoriesOptions = Product::arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- ');

        foreach ($categoriesOptions as $category)
        {
            if ($category['id'] > 0)
            {
                $categoriesMainOptions[$category['id']] = $category['category_name']." [".$category['id']."]";
            }
        }

		$zoneOptions = $this->zone->all();

		$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'import_new_product')->get();
		
		$regions = DB::table('jocom_region')->select('*')->where('status',1)->get();

		if (Session::get('role_id') == '1')
		{
			return View::make('product.product_insert', [
				'sellersOptions'			=> $sellersOptions,
				'categoriesMainOptions' => $categoriesMainOptions,
				'zoneOptions'				=> $zoneOptions,
				'job'						=> $job,
				'regions'					=> $regions,
			]);
		}
		else
			// return Redirect::to('home')->with('message', 'Access Denied!');
			return View::make('home.denied', array('module' => 'Import New Product'));
		
	}

	public function anyImportnewproduct()
	{
		$path  = Config::get('constants.CSV_IMPORT_PATH');

		$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'import_new_product')->get();

		if (count($job) > 0)
		{
			echo "Completed for: </br>";

			foreach ($job as $row)
			{
				if ($row->remark != Null or $row->remark != "")
				{
					$data = json_decode($row->remark, true);

					$product['sell_id'] 		= $data['sell_id'];
					$product['category']		= $data['category'];
					$product['status']			= $data['status'];
					$product['delivery_time']	= $data['delivery_time'];
					$product['insert_by']		= $data['insert_by'];
					$product['insert_date']		= $data['insert_date'];
					$product['modify_by']		= $data['modify_by'];
					$product['modify_date']		= $data['modify_date'];

					$zone['zone_id']			= $data['zone_id'];
					$zone['price']				= $data['price'];

					// $price['status']			= $data['status'];
					// $price['default']			= $data['default'];
					$price = "";

					$category['main']			= $data['main'];
					$category['category_id']	= $data['category_id'];
					$category['created_at']		= $data['created_at'];

					$job['id']    		= $row->id;
		        	$job['in_file']     = $row->in_file;

		        	$done = Product::diff_pending($job);

		        	if(!$done)
		        	{
		        		$newfile = $path.'original_' . $row->in_file;
	            		$insertfile = $path.'inserted_' . $row->in_file;
	            		$field = explode(',' , "Name,Desc,Label,Price,Promo,Seller SKU,Qty,Stock,Referral Fees,Referral Type,GST,Image1,Image2,Image3");

		        		Product::insert_product($product, $category, $zone, $newfile, $insertfile, $field, $job['id']);

		        		$done = Product::diff_pending($job);

		        		if ($done)
		        		{
		        			echo "Job ID: " . $job['id'] . "</br>";
		        			echo "File Name: " . $job['in_file'] . "</br>";
		        		}
		        	}
				}
				else
					$temprow = DB::table('jocom_job_queue')->where('id', '=', $row->id)->update(array('status' => 2)); // update to complete if no remark
				
			}
		}
	}

	public function postStore()
	{

		$validator	= $this->getValidator();

		if ($validator->fails())
		{
			$errors = $validator->messages();

			return Redirect::back()->withInput()->withErrors($errors);
		}

		if (Input::has('seller_id'))
		{
            $product['region_id'] 		= Input::get('region_id');
            
			$product['sell_id'] 		= Input::get('seller_id');
			$product['category']		= Input::get('product_category');
			$product['status'] 			= Input::get('status');
			$product['delivery_time'] 	= Input::get('delivery_time');
			$product['insert_by'] 		= Session::get('username');
			$product['insert_date'] 	= date("Y-m-d h:i:sa");
			$product['modify_by'] 		= Session::get('username');
			$product['modify_date'] 	= date("Y-m-d h:i:sa");

			$zone['zone_id'] 			= Input::get('zone_id');
			$zone['price'] 				= Input::get('zone_price');

			$file 						= Input::file('csv');
			$img 						= Input::hasFile('img') ? Input::file('img') : Null;
			$img_ext   					= Input::hasFile('img') ? $img->getClientOriginalExtension() : Null;

	        // $price['status'] 	= 1;
	        // $price['default'] 	= 1;

	        $category['main'] 			= 1;
	        $category['category_id'] 	= Input::get('product_category');
	        $category['created_at'] 	= date("Y-m-d h:i:sa");

	        

			
			$dest_path  = Config::get('constants.CSV_IMPORT_PATH');
			$log_path  = Config::get('constants.CSV_IMPORT_PATH');
			$img_path  = Config::get('constants.CSV_IMPORT_PATH');

        	$date       = date('Ymd_his');

			$file_name  = 'import_new_product_' . $date . '.csv';
			$file_inserted  = 'inserted_import_new_product_' . $date . '.csv';
			$file_original  = 'original_import_new_product_' . $date . '.csv';
			$file_ext   = $file->getClientOriginalExtension();


			if(strtolower($file_ext) == "csv") {
                $upload_file   = $file->move($dest_path, $file_name);
            }

            $newfile = $dest_path.$file_name;
            $insertfile = $log_path.$file_inserted;
            $originalfile = $log_path.$file_original;
            
            $count = 0;

            $job                = array(); 
	        $job['ref_id']      = Input::get('seller_id');
	        $job['job_name']    = "import_new_product";
	        $job['in_file']     = $file_name;
	        $job['remark']     	= json_encode(array_merge($product, $category, $zone));
	        $job['request_by']  = Session::get('user_id');
	        $job['request_at']  = date('Y-m-d H:i:s');	        

	        $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);

	        if ($img != Null AND strtolower($img_ext) == "zip")
	        {
	        	$zip = new ZipArchive;
				$res = $zip->open($img);
				if ($res === TRUE)
				{
					// echo "haha";exit;
					if(!file_exists(".".$img_path.$job['id']))
						mkdir(".".$img_path.$job['id'], 0755, true);

				  	$zip->extractTo($img_path.$job['id']);
					$zip->close();
				}
	        }

            if (file_exists($newfile))
            {
            	$field = explode(',' , "Name,Desc,Label,Price,Promo,Seller SKU,Qty,Stock,Referral Fees,Referral Type,GST,Image1,Image2,Image3");

            	// create a copy of import list into same format
            	$original = fopen($originalfile, "w");
            	$temp_original = fopen($newfile, "r");

            	while(! feof($temp_original))
				{
					$data_original = fgetcsv($temp_original);

					if($count == 0 AND $data_original[$count] != "Name")
						fputcsv($original, $field);
					
					if (! is_bool($data_original))
						fputcsv($original, $data_original, ",", "\"");

					$count++;
				}

				fclose($original);
				fclose($temp_original);


				// call model
				Product::insert_product($product, $category, $zone, $newfile, $insertfile, $field, $job['id']);
				$done = Product::diff_pending($job);

				if ($done)
				{
					return Redirect::to('product_insert')->with('success', 'Import successfully!');
				}
				else
				{
					return Redirect::to('product_insert')->with('message', 'Import failed. Queue created!');
				}
            }
		}

		
	}

	public function anyFiles($file = null) 
    {
    	$log_path  = Config::get('constants.CSV_IMPORT_PATH');

        if (file_exists($log_path.$file))
        {
            return Response::download($log_path.$file);            

        }
        else
        {
            echo "<br>File not exists!";
        }
    }

	private function getValidator()
	{
		$rules = [
			'seller_id'		=> 'required',
			'zone_id'		=> 'required',
			'csv'		=> 'required',
		];

		$message = [
			'seller_id.required'	=> 'The Seller field is required.',
			'zone_id.required'		=> 'The Delivery Fee field is required.',
			'csv.required'		=> 'The Delivery Fee field is required.',
		];

		return Validator::make(Input::all(), $rules, $messages);
	}
}


