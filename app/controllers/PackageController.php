<?php

class PackageController extends BaseController {

    protected $package;
    protected $product;
    protected $zone;
    protected $category;
    protected $seller;
    protected $price;
    protected $delivery;

    public function __construct(Package $package, Product $product, Zone $zone, Category $category, Seller $seller, Price $price, Delivery $delivery) {

        $this->package = $package;
        $this->product = $product;
        $this->zone = $zone;
        $this->category = $category;
        $this->seller = $seller;
        $this->price = $price;
        $this->delivery = $delivery;

    }

    /**
     * Display the package page.
     *
     * @return Page
     */
    public function anyIndex() {
        return View::make('product.package.index');
    }

    /**
     * Display a listing of the packages resource.
     *
     * @return Response
     */
    public function anyPackages() {
        $packages = $this->package->select(array(
                                        'jocom_product_package.id',
                                        'jocom_product_package.sku',
                                        'jocom_product_package.name',
                                        'jocom_product_package.description',
                                        'jocom_products_category.category_name',
                                        'jocom_product_package.status'
                                    ))
                                    ->leftJoin('jocom_products_category', 'jocom_product_package.category', '=', 'jocom_products_category.id')
                                    ->where('jocom_product_package.status', '!=', 2);
        return Datatables::of($packages)
                                    ->edit_column('status', function($row){
                                        if($row->status == 1) $status = '<span class="label label-success">Active</span>';
                                        elseif($row->status == 2) $status = '<span class="label label-danger">Deleted/Archive</span>';
                                        else $status = '<span class="label label-warning">Inactive</span>';
                                        return $status;
                                    })
                                    ->add_column('Action', function($row) {
                                        $product_access = array('1', '2');
                                        if (Session::get('role_id') == 4 || Session::get('role_id') == 1) {
                                            $edit .= '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/package/upload/'.$row->id.'"><i class="fa fa-file-image-o"></i></a> ';
                                        }
                                        if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND')) {
                                            $edit .= '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/package/edit/'.$row->id.'"><i class="fa fa-pencil"></i></a> ';
                                        }
                                        if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 9, 'AND')) {
                                            $edit .= '<a id="deleteItem" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/product/package/delete/'.$row->id.'"><i class="fa fa-remove"></i></a>';
                                        }
                                        return $edit;
                                    })
                                    ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function anyCreate() {

		$categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- ');

		foreach ($categoriesOptions as $category)
		{
			if ($category['id'] > 0)
			{
				$categoriesMainOptions[$category['id']] = $category['category_name'];
			}
		}

        $zone_options = $this->zone->all()->lists('name','id');
        // WORKING
        $category_options = $this->category->orderBy('category_parent', 'asc')->orderBy('category_name', 'asc')->lists('category_name','id');

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 5, 'AND'))
            return View::make('product.package.create', ['zone_options' => $zone_options, 'categoriesMainOptions' => $categoriesMainOptions]);
        else
            return View::make('home.denied', array('module' => 'Products > Add Package'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $input = Input::all();

        if( !$this->package->fill($input)->isValid() ) {
            return Redirect::back()->withInput()->withErrors($this->package->errors);
        } else {
            $lastId = $this->package->select('jocom_product_package.id')->orderBy('id', 'DESC')->first();
            $newSKU = 'JCP-' . str_pad($lastId->id + 1, 12, '0', STR_PAD_LEFT);
            $qrCode = 'JCP' . ($lastId->id + 1);
            $qrCodeFile = 'P' . ($lastId->id + 1) . '.png';
            $imgFilename = '';
            $timestamp = date('Y-m-d H:i:s');

            $imgFilename = array_fill(1, 4, '');

            // Insert Product Package Table
            $id = DB::table('jocom_product_package')->insertGetId(array(
                            'sku' => $newSKU,
                            'name' => trim(Input::get('prod_name')),
                            'name_cn' => trim(Input::get('product_name_cn')),
                            'name_my' => trim(Input::get('product_name_my')),
                            'category' => trim(Input::get('product_category')),
                            'description' => trim(Input::get('product_desc')),
                            'description_cn' => trim(Input::get('product_name_cn')),
                            'description_my' => trim(Input::get('product_name_my')),
                            'img_1' => $imgFilename[1],
                            'img_2' => $imgFilename[2],
                            'img_3' => $imgFilename[3],
                            // 'vid_1' => trim(Input::get('product_video')),
                            'qrcode' => $qrCode,
                            'qrcode_file' => $qrCodeFile,
                            'delivery_time' => trim(Input::get('delivery_time')),
                            'related_product' => trim(Input::get('related_product')),
                            'insert_by' => Session::get('username'),
                            'modify_by' => Session::get('username'),
                            'status' => trim(Input::get('status')),
                            'insert_date' => $timestamp,
                            'modify_date' => $timestamp)
            );

            // Insert Package Product Table
            if (Input::has('lid')) {
                foreach(Input::get('lid') as $key => $value) {
                    DB::table('jocom_product_package_product')->insertGetId(array(
                                'insert_by' => Session::get('username'),
                                'modify_by' => Session::get('username'),
                                'insert_date' => $timestamp,
                                'modify_date' => $timestamp,
                                'package_id' => $id,
                                'product_opt' => trim(Input::get("lid.$key")),
                                'qty' => trim(Input::get("qty.$key")))
                    );

                    $insert_audit = General::audit_trail('PackageController.php', 'store()', 'Add Package Product', Session::get('username'), 'CMS');
                }
            }

            // Generate QR Code
            $this->package->generateQR($qrCode, 'images/qrcode/', $qrCodeFile);

            if ($id)
            {
                $insert_audit = General::audit_trail('PackageController.php', 'store()', 'Add Package', Session::get('username'), 'CMS');
                Session::flash('message', 'Successfully updated.');
            }
            else
                Session::flash('message', 'Error. Unknown error occured.');

            return Redirect::to('product/package');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $package_id
     * @return Response
     */
    public function anyEdit($package_id) {
        $package = $this->package->select('*')
                                    ->where('jocom_product_package.status', '!=', '2')
                                    ->where('jocom_product_package.id', '=', $package_id)
                                    ->first();

        $product_package = DB::table('jocom_product_package_product')->distinct()
                                    ->select('jocom_products.sku', 'jocom_products.name', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo', 'jocom_product_package_product.product_opt', 'jocom_product_package_product.qty')
                                    ->leftJoin('jocom_product_price', 'jocom_product_price.id', '=', 'jocom_product_package_product.product_opt')
                                    ->leftJoin('jocom_products', 'jocom_products.id', '=', 'jocom_product_price.product_id')
                                    ->where('jocom_product_package_product.package_id', '=', $package_id)
                                    ->where('jocom_product_price.status', '=', 1)
                                    ->where('jocom_products.status', '!=', 2)
                                    ->get();

        $category_options = $this->category->orderBy('category_parent', 'asc')->orderBy('category_name', 'asc')->lists('category_name','id');
        $sellers_options = $this->seller->orderBy('id', 'asc')->lists('company_name','id');

		$categoriesOptions = $this->arrangeCategories($this->category->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- ');

		foreach ($categoriesOptions as $category)
		{
			if ($category['id'] > 0)
			{
				$categoriesMainOptions[$category['id']] = $category['category_name'];
			}
		}

		$mainCategory = $package->category;

        $zone_options = DB::table('jocom_zones')
                                    ->select('jocom_zones.id', 'jocom_zones.name')
                                    ->get();

        return View::make('product.package.edit')->with(array('package' => $package, 'mainCategory' => $mainCategory, 'categoriesMainOptions' => $categoriesMainOptions, 'sellers_options' => $sellers_options, 'product_package' => $product_package));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $package_id
     * @return Response
     */
    public function anyUpdate($package_id) {

        $input = Input::all();

        if( !$this->package->fill($input)->isValid() ) {
            return Redirect::back()->withInput()->withErrors($this->package->errors);
        }

        $timestamp = date('Y-m-d H:i:s');

        // UPDATE PACKAGE PRODUCT TABLE
        $package = $this->package->find($package_id);
        $package->name = trim(Input::get('prod_name'));
        $package->name_cn = trim(Input::get('prod_name_cn'));
        $package->name_my = trim(Input::get('prod_name_my'));
        $package->category = trim(Input::get('product_category'));
        $package->description = trim(Input::get('product_desc'));
        $package->description_cn = trim(Input::get('product_desc_cn'));
        $package->description_my = trim(Input::get('product_desc_my'));
        $package->delivery_time = trim(Input::get('delivery_time'));
        $package->related_product = trim(Input::get('related_product'));
        $package->status = trim(Input::get('status'));
        $package->modify_by = Session::get('username');
        $package->save();

        $insert_audit = General::audit_trail('PackageController.php', 'update()', 'Edit Package', Session::get('username'), 'CMS');

        // Update Package Product Table
        DB::table('jocom_product_package_product')->where('package_id', '=', $package_id)->delete();

        if (Input::has('lid')) {
            foreach(Input::get('lid') as $key => $value) {
                DB::table('jocom_product_package_product')->insert(array(
                            'insert_by' => Session::get('username'),
                            'modify_by' => Session::get('username'),
                            'insert_date' => $timestamp,
                            'modify_date' => $timestamp,
                            'package_id' => $package_id,
                            'product_opt' => trim(Input::get("lid.$key")),
                            'qty' => trim(Input::get("qty.$key")))
                );

                $insert_audit = General::audit_trail('PackageController.php', 'update()', 'Edit Package Product', Session::get('username'), 'CMS');
            }
        }

        Session::flash('message', 'Successfully updated.');
        return Redirect::to('product/package');
    }

    /**
     * Delete the specified resource in storage. Not exactly delete but make them inactive ;-)
     *
     * @param  int  $package_id
     * @return Response
     */
    public function anyDelete($package_id) {

        $package = $this->package->find($package_id);
        $package->modify_by = Session::get('username');
        $package->status = 2;
        $package->save();

        Session::flash('message', 'Successfully deleted.');
        return Redirect::to('product/package');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $package_id
     * @return Response
     */
    public function anyUpload($package_id) {
        $package = $this->package->select('*')
                                    ->where('jocom_product_package.status', '!=', 2)
                                    ->where('jocom_product_package.id', '=', $package_id)
                                    ->first();

        return View::make('product.package.upload')->with(array('package' => $package));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $package_id
     * @return Response
     */
    public function anyUpdatephoto($package_id) {

        // UPDATE TABLE
        $package = $this->package->find($package_id);
        $package->vid_1 = trim(Input::get('product_video'));
        $package->modify_by = Session::get('username');

        $imgFilename = array_fill(1, 4, '');

        // Image
        $unique = time();
        for($i = 1; $i < 4; $i++) {
            if (Input::hasFile("newimage$i")) {
                $unique = time();
                $image = Input::file("newimage$i");

                $imgFilename[$i] = $package_id . "-img-p$i-" . $unique . '.' . $image->getClientOriginalExtension();
                $image->move('images/data/', $imgFilename[$i]);
                Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(640, null, function($constraint) { $constraint->aspectRatio(); })->save();
                Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(320, null, function($constraint) { $constraint->aspectRatio(); })->save('images/data/thumbs/' . $imgFilename[$i]);
            } else {
                $imgFilename[$i] = trim(Input::get("image$i"));
            }
        }
        // END Image

        $package->img_1 = $imgFilename[1];
        $package->img_2 = $imgFilename[2];
        $package->img_3 = $imgFilename[3];
        $package->save();

        Session::flash('message', 'Successfully updated.');
        return Redirect::to('product/package');
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
}
