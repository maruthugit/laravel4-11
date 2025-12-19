<?php

class SpecialpriceController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */
    public function anyCustomersajax() {    
        $customers = DB::table('jocom_user as u')
                            ->select('u.id', 'u.username', 'u.firstname', 'u.lastname', 'u.email');
                            // ->whereNotIn('u.id', function($q){
                            //     $q->select('c.user_id')->from('jocom_sp_customer as c');
                            // });

        return Datatables::of($customers)
                                    ->add_column('Action', '<a id="selectCust" class="btn btn-primary" title="" href="../customer/{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyAjaxcustomer() {
        return View::make('special_price.ajaxcustomer');
    }

    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */
    public function anySellersajax() {     
        $sellers = DB::table('jocom_seller')
                            ->select(array('id', 'company_name', 'pic_full_name', 'email'))
                            ->where('active_status', '=', '1');
                                    
        return Datatables::of($sellers)
                                    ->add_column('Action', '<a id="selectSeller" class="btn btn-primary" title="" href="../seller/{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyAjaxseller() {
        return View::make('special_price.ajaxseller');
    }

    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */
    public function anyGroupsajax() {     

        $groups = SpecialPrice::select('jocom_sp_group.id', 'jocom_sp_group.name', 'jocom_seller.company_name', 'jocom_seller.email')
                                ->leftJoin('jocom_seller', 'jocom_seller.id', '=', 'jocom_sp_group.seller_id')
                                ->where('jocom_sp_group.status', '=', '1');
                                    
        return Datatables::of($groups)
                                    ->add_column('Action', '<a id="selectGroup" class="btn btn-primary" title="" href="../group/{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
    public function anyAjaxgroup() {
        return View::make('special_price.ajaxgroup');
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/special_price/ajaxprice/{{$id}}">Select</a>')
            ->make();
    }

    public function anyAjaxproduct()
    {
        return View::make('special_price.ajaxproduct');
    }

    public function anyPricesajax($id)
    {
        $product_prices = DB::table('jocom_product_price')
            ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo', 'jocom_product_price.default')
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
            // ->edit_column('default', '')
            ->add_column('Discount Amount', '<input size="10" id="amount" type="text" name="amount" class="form-control" placeholder="Disc. Amount"> ')
            ->add_column('Discount Type', '{{ Form::select(\'type\', [\'%\' => \'%\', \'N\' => \'Nett\'], null, [\'class\' => \'form-control\', \'id\' => \'type\']) }}')
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

        return View::make('special_price.ajaxprice')->with([
            'id'        => $id,
            'name'      => addslashes($product->name),
            'sku'       => $product->sku,
            'qrcode'    => $product->qrcode,
        ]);
    }

    public function anyDiscounts($id) 
    {
        $discounts = DB::table('jocom_sp_product_price as price')
                        ->select('price.id', 'product.name', 'price.label', 'price.price', 'price.price_promo', 'price.disc_amount', 'price.disc_type')
                        ->leftjoin('jocom_products as product', 'product.id', '=', 'price.product_id')
                        ->where('price.sp_group_id', '=', $id);

        return Datatables::of($discounts)
                        ->edit_column('price', '{{ number_format($price, 2) }} ')
                        ->edit_column('price_promo', '{{ number_format($price_promo, 2) }} ')
                        ->edit_column('disc_amount', '{{ number_format($disc_amount, 2) }} @if($disc_type == "N") Nett @else {{$disc_type}} @endif')
                        ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href=""><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 9, \'AND\'))
                                <a id="deleteDiscount" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/special_price/discount?delete={{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                            ')
                        ->make();
    }


    public function anyAjaxdiscount($id) {
        $product = DB::table('jocom_products as product')
                        ->select('product.name', 'product.sku', 'product.qrcode', 'price.label', 'price.disc_amount', 'price.disc_type')
                        ->leftjoin('jocom_sp_product_price as price', 'price.product_id', '=', 'product.id')
                        ->where('price.id', '=', $id)
                        ->first();

        return View::make('special_price.edit_discount')->with([
            'id'        => $id,
            'name'      => addslashes($product->name),
            'sku'       => $product->sku,
            'qrcode'    => $product->qrcode,
            'label'     => $product->label,
            'disc_amount' => $product->disc_amount,
            'disc_type' => $product->disc_type,
        ]);
    }

    /**
     * Display a listing of the special price customers.
     *
     * @return Response
     */
    public function anyExport()
    {
        $sellers    = SpecialPrice::get_sellers();   
        $jobs       = SpecialPrice::get_export_job(Session::get('user_id'), 'export_seller_products');

        return View::make('special_price.export', ['sellers' => $sellers, 'jobs' => $jobs]);
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function anyExportcsv()
    {   
        // echo "exporting . . .";
        $date       = date('Ymd_his');
        $filename   = "product_list_".$date.".csv";
        $file       = "media/csv/".$filename;   //storage_path($filename);

        /*
        "SELECT GROUP_CONCAT(COLUMN_NAME SEPARATOR '\t') FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE table_schema='phineas_and_ferb' and table_name='characters' INTO OUTFILE '~/tmp/output.txt' FIELDS TERMINATED BY '\t' OPTIONALLY ENCLOSED BY '' ESCAPED BY '' LINES TERMINATED BY '\n';"

        "SELECT * FROM characters INTO OUTFILE '~/tmp/data.txt' FIELDS TERMINATED BY '\t' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n';"

        SELECT * INTO OUTFILE "Database/business.sql"
        FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' 
        FROM `business`
         */
        
        // $headers    = "'Product ID', 'SKU', 'Product Name', 'price_id', 'label', 'label_cn', 'label_my', 'Seller SKU', 'Price', 'Promo Price', 'Qty', 'Referral fees', 'Referral fees type', 'Default', 'Product ID', 'Status'";
        // $fields     = "p.id, p.sku, p.name, price.*";

        // if (DB::statement("SELECT ".$headers." UNION SELECT ".$fields." INTO OUTFILE '".$file."' FIELDS TERMINATED BY ';' ENCLOSED BY '' ESCAPED BY '' LINES TERMINATED BY '\\r\\n' FROM jocom_products as p LEFT JOIN jocom_product_price as price on price.product_id=p.id")) {
        //     Session::flash('message', 'File exported successfully. File: '.$filename);
        //     return View::make('special_price.export');
        // }
        // else
        //     echo "Failed to export to csv file.";
        // DB::statement("SELECT p.id, p.sku, p.name, price.* INTO OUTFILE './public/media/productlist.csv' FIELDS TERMINATED BY ';' ENCLOSED BY '"' LINES TERMINATED BY  '\r\n'FROM jocom_products as p LEFT JOIN jocom_product_price as price on price.product_id=p.id");


        $result     = DB::table('jocom_products as p')
                        ->select('p.id', 'p.sku', 'p.name', 'price.*')
                        ->leftjoin('jocom_product_price as price', 'price.product_id', '=', 'p.id')
                        ->first();

        
        foreach($result as $key => $value) {
            // echo "<br> - - [".$key."] ".$value;
            $header[] = $key;
        }
        
        // exit();
        // $filename = "toy_csv.csv";
        // $fp = fopen('php://output', 'w');
        $fp = fopen($file, "w");

        // header('Content-type: application/csv');
        // header('Content-Disposition: attachment; filename='.$filename);
        fputcsv($fp, $header);

        $result     = DB::table('jocom_products as p')
                        ->select('p.id', 'p.sku', 'p.name', 'price.*')
                        ->leftjoin('jocom_product_price as price', 'price.product_id', '=', 'p.id')
                        ->get();
       
        foreach ($result as $row) {
            $line = "";
            // echo "<br>";
            foreach ($row as $key => $value) {
                // echo "<br> - - [".$key."] ".$value;
                if ($line == "") {
                    $line = $value.';';
                }
                else {
                    $line = $line.$value.";";
                }
               
            }
            // echo "<br>".$line;
            fputcsv($fp, explode(';',$line));
        }
        
        if (fclose($fp)) {
            Session::flash('message', 'File exported successfully. File: '.$filename);
            return View::make('special_price.export', ['filename' => $filename]);
        }
        // exit;
    }

    public function anyAddexport() {
        // var_dump(Input::all());
        $job        = array(); 

        $job['ref_id']      = Input::get('seller');
        $job['job_name']    = "export_seller_products";
        $job['request_by']  = Session::get('user_id');
        $job['request_at']  = date('Y-m-d H:i:s');

        if (SpecialPrice::add_job($job)) {
            $jobs = SpecialPrice::get_export_job(Session::get('user_id'), 'export_seller_products');
        }

        if (count($jobs) > 0) {
            $sellers    = SpecialPrice::get_sellers();
            return View::make('special_price.export', ['sellers' => $sellers, 'jobs' => $jobs]);
        }
        
    }

    public function anyFiles($file=null) {
        $path = "media/csv/";

        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyImport() {
        $groups     = SpecialPrice::get_group_list();  
        $jobs       = SpecialPrice::get_import_job(Session::get('user_id'), 'import_special_price');
        
        return View::make('special_price.import', ['groups' => $groups, 'jobs' => $jobs]);
    }

    public function anyAddimport() {
        $inputs     = Input::all();
        $file       = Input::file('csv');
        $dest_path  = Config::get('constants.CSV_UPLOAD_PATH');
        $date       = date('Ymd_his');
        $file_name  = 'import_'. Session::get('user_id').'_'.Input::get('group').'_'. $date. '.csv';

        $job                = array(); 
        $job['ref_id']      = Input::get('group');
        $job['job_name']    = "import_special_price";
        $job['in_file']     = $file_name;
        $job['request_by']  = Session::get('user_id');
        $job['request_at']  = date('Y-m-d H:i:s');

        if (Input::hasFile('csv')) {
            $file_ext           = $file->getClientOriginalExtension();

            if (SpecialPrice::add_job($job)) {
                $jobs   = SpecialPrice::get_import_job(Session::get('user_id'), 'import_special_price');
            }

            if(strtolower($file_ext) == "csv") {
                $upload_file   = $file->move($dest_path, $file_name);
            }
        }

        if (count($jobs) > 0) {
            $groups     = SpecialPrice::get_group_list(); 
            return View::make('special_price.import', ['groups' => $groups, 'jobs' => $jobs]);
        }
    }

    public function anyUpload($file=null) {
        $path = "media/csv/upload/";

        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyLog($file=null) {
        $path = "media/csv/log/";

        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }
    
    public function anySetting() {
        $settings   = SpecialPrice::get_settings();
//        var_dump($settings);

        return View::make('special_price.setting')->with(array(
                'settings' => $settings,

            ));
    }

    public function anyUpdatesetting() {
        $inputs     = Input::all();
        $udata      = array();

        // var_dump($inputs);

        $udata['default_qty']   = Input::get('qty');
        $udata['updated_by']    = Session::get('username');
        $udata['updated_at']    = date('Y-m-d H:i:s');

        if (SpecialPrice::update_setting($udata)) {
            // $username   = SpecialPrice::get_username();

            $settings   = SpecialPrice::get_settings();

            Session::flash('success', 'Setting has been successfully save!');
            return View::make('special_price.setting', ['settings' => $settings]);
        }
        else { 
            return Redirect::back()
                        ->withInput()
                        ->withErrors('Sorry, failed to save settings.');
        }
    }

    public function anyList() {
        return View::make('special_price.list');   
    }

    public function anyListing() {
        $sp = SpecialPrice::select('id', 'name', 'created_at')
                ->orderBy('id', 'DESC');

        return Datatables::of($sp)
                        ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/special_price/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 5, 9, \'AND\'))
                                <a id="deleteRefund" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$id}}" href="/special_price/delete/{{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                            ')
                        ->make();
    }

    public function anyAdd() {
        $groups = SpecialPrice::get_group_list();

        return View::make('special_price.add')->with(array(
                'groups' => $groups,

            ));
    }

    public function postStore() {
        $discount   = new SpecialPrice;
        $validator  = Validator::make(Input::all(), SpecialPrice::$disc_rules, SpecialPrice::$disc_message);
        $group_id   = "";
        $i;

        if($validator->passes()) {
            $group_id     = Input::get('group_id');
            
            if (Input::has('label')) {
                $arr_name           = Input::get('name');
                $arr_disc_amt       = Input::get('disc_amount');
                $arr_disc_type      = Input::get('disc_type');
                $arr_actual_price   = Input::get('actual_price');
                $arr_promo_price    = Input::get('promo_price');
                $arr_product        = Input::get('product');
                $arr_default        = Input::get('default');
                $i                  = 0;
                $table              = "jocom_sp_product_price";
                $sql_update         = "";

                foreach (Input::get('label') as $label) {
                    $arr_disc_item['sp_group_id']   = $group_id;
                    $arr_disc_item['label_id']      = $label;
                    $arr_disc_item['product_id']    = trim($arr_product[$i]);
                    $arr_disc_item['default']       = trim($arr_default[$i]);
                    $arr_disc_item['label']         = trim($arr_name[$i]);
                    $arr_disc_item['disc_amount']   = trim($arr_disc_amt[$i]);
                    $arr_disc_item['disc_type']     = trim($arr_disc_type[$i]);
                    $arr_disc_item['price']         = trim($arr_actual_price[$i]);
                    $arr_disc_item['price_promo']   = trim($arr_promo_price[$i]);

                    $str_value  = "";
                    $str_key    = "";
                    $str_update = "";

                    foreach ($arr_disc_item as $key => $value) {
                        $str_key    = ($str_key == "") ? '`'.$key.'`' : $str_key . "," . '`'.$key.'`';
                        // $str_value  = ($str_value == "") ? "'".trim($value)."'" : $str_value . ", '" . trim($value) ."'";
                        $str_update = ($str_update == "") ? '`'.$key ."`='".trim($value)."'" : $str_update .", `". $key ."`='".trim($value)."'";
                    }
                    
                    $condition  = "sp_group_id='".$group_id."' AND label_id='".$label."'";

                    $sql_update = "UPDATE ".$table." SET ".$str_update." WHERE ".$condition."; ";
                    // $sql_insert = "INSERT INTO ".$table." (".$str_key.") VALUES (".$str_value.")";

                    $result = DB::select('SELECT * FROM '.$table.' WHERE '.$condition);
                    // $query  = (count($result) > 0) ? $sql_update : $sql_insert;

                    if (count($result) > 0 && $sql_update != "") {
                        echo "<br>[$i] ".$sql_update;
                        DB::statement(DB::raw($sql_update));
                        // $update_query = ($update_query == "") ? $sql_update : $update_query . $sql_update;
                    } else {
                        $arr_discount_insert[] = $arr_disc_item;
                    }

                    $i++;
                }
            }

            if(count($arr_discount_insert) > 0) {
                SpecialPrice::insert_special_price($arr_discount_insert);
            }

            // Log::info(DB::getQueryLog());

            // Log::info(DB::getQueryLog());
            return Redirect::to('/special_price/list/')->with('success', 'Settings have been saved successfully!');
        }
        else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
    }

    public function getEdit($id) {
        Session::forget('message');
        $group_name     = SpecialPrice::get_group_name($id);
        $products       = SpecialPrice::get_sp_price_list($id);

        return View::make('special_price.edit')->with(array(
                'id'            => $id,
                'group'         => $group_name,
                'products'      => $products,
                'sellers'       => $sellers,
            ));
    }

    public function anyUpdate($id) {
        // $groups     = SpecialPrice::get_group_list();
        // $products   = SpecialPrice::get_sp_price_list($id);

        $amount     = Input::get('amount');
        $type       = Input::get('disc_type');

        if (SpecialPrice::update_discount($id, $amount, $type)) {
            return View::make('special_price.close');
            
        }
        else {
            Session::put('message', ' Successfully updated!');
            return Redirect::to('/special_price/ajaxdiscount/'.$id);
        }
        dd(Input::all());


        return View::make('special_price.edit')->with(array(
                'id'        => $id,
                'groups'    => $groups,
                'products'  => $products,
            ));
    }

    public function getProcess($id=null) {
        $path_log   = Config::get('constants.CSV_LOG_PATH');        //"../public/media/csv/log/";
        $path_from  = Config::get('constants.CSV_UPLOAD_PATH');     //"../public/media/csv/upload/";
        $path_to    = Config::get('constants.CSV_FILE_PATH');       //"../public/media/csv/";
        $date       = date("Ymd_His");
        $filename   = "";

        if(Input::has('export')) {
            $id         = Input::get('export');
            $job        = SpecialPrice::get_job_by_id($id);

            SpecialPrice::update_job_queue($id, array('status' => '1'));

            $seller_id  = $job->ref_id;
            $filename   = "product_list_seller_".$seller_id."_".$date.".csv";

            $result     = SpecialPrice::export_get_header();
            $contents   = SpecialPrice::export_get_csv_content($seller_id);
            $fp         = fopen($path_to.$filename, "w");

            if($fp) {
                foreach ($result as $r) {
                    foreach ($r as $key => $value) {
                        $header[] = $key;
                    }
                }
                fputcsv($fp, $header);
            }

            foreach ($contents as $content) {
                $count      = 0;
                $arr_line   = "";

                foreach ($content as $key => $value) {
                    $arr_line[] = trim($value);
                }

                fputcsv($fp, $arr_line, ",", "\"");
            }

            if (fclose($fp)) 
                SpecialPrice::update_job_queue($id, array('status' => '2', 'out_file' => $filename));

            return Redirect::to('special_price/export');
        }

        if(Input::has('import')) {
            $id             = Input::get('import');
            $job            = SpecialPrice::get_job_by_id($id);
            $date           = date("Ymd_his");
            $group_id       = $job->ref_id;
            $uid            = $job->request_by;
            $file_fr_user   = $job->in_file;
            $log_name       = "log_".$uid."_".$id."_".$date.".txt";
            $log_file       = $path_log.$log_name;
            $log            = fopen($log_file, "w"); 
            $str_log        = "";
            $file_insert    = "";
            $count          = 0;
            $arr_columns    = array('label','label_id','price','price_promo','qty','p_referral_fees','p_referral_fees_type',
                                    'disc_amount','disc_type','default','product_id','status'
                                );

            $str_log = "[DateTime] ".date("Y-m-d H:i:s");
            fwrite($log, $str_log);

            if (SpecialPrice::check_exists($group_id)) {
                $exist = true;
                
                // Export a copy of the existing group prices from jocom_sp_product_price.
                $filename   = "export_group_".$group_id.'_'.$date.'.csv';
                $file_fr_sys= $path_to.$filename;
                $csv        = $this->get_data_to_csv($group_id, $file_fr_sys);

                // Get the uploaded file from user, diff both files, then output the result into another file.
                if ($csv) {
                    $diff_file      = 'diff_'.$id.'_'.$date.'.csv'; 

                    $str_log = "\n[FILE from USER] ".$path_from.$file_fr_user;
                    fwrite($log, $str_log);

                    $str_log = "\n[FILE from SYSTEM] ".$file_fr_sys."\n";
                    fwrite($log, $str_log);
                    
                    // Diff both files and output the result into another file.
                    if (file_exists($path_from.$file_fr_user) && file_exists($file_fr_sys)) {
                        $output = shell_exec('diff --unchanged-line-format= --old-line-format= --new-line-format=\'%L\' '. $file_fr_sys .' '. $path_from.$file_fr_user .' > '. $path_to.$diff_file);
                        // $output = shell_exec('diff '. $file_fr_sys .' '. $path_from.$file_fr_user .' > '. $path_to.$diff_file);
                        // echo $output;
                        $file_insert = $path_to.$diff_file;
                        $this->replace_carriage_return($file_insert);
                    }

                    
                }
            } 
            else {
                $file_insert    = $path_from.$file_fr_user;
                $this->replace_carriage_return($file_insert);

                $str_log = "\n[FILE from USER] ".$path_from.$file_fr_user;
                fwrite($log, $str_log);

                if (file_exists($file_insert)) {
                    $fp             = fopen($file_insert, "r");
                    $values         = "";
                    $arr_disc_item  = "";

                    var_dump($arr_columns);

                    while($data = fgetcsv($fp)) {
                        $values = "'".$group_id."'";
                        $num    = count($data);
                        $col    = 0;
                        
                        $arr_disc_item['sp_group_id'] = $group_id;

                        for ($i = 0; $i < $num; $i++) {
                            // echo "<br>[Count: $count][$i] ".$data[$i];
                            $d = $data[$i];

                            if ($d == "sku") {
                                $total_ignore++;
                                break;
                            }

                            if($i == 2) {
                                $arr_disc_item['label'] = $data[$i];
                                $col++;
                            }

                            if($i > 5 && $i < 17) {
                                $values     = $values.","."'".$data[$i]."'";
                                $column     = $arr_columns[$col];
                                $arr_disc_item[$column] = trim($data[$i]); 
                                $col++;
                            }
                        }

                        $arr_discount_insert[] = $arr_disc_item;
                        
                        if ($col > 0) {
                            if(SpecialPrice::insert_special_price($arr_disc_item)) {
                                $str_log = "\n[INSERT-OK][Line:".$count."] ".$values."\n";
                                $total_insert++;
                            } else {
                                $str_log = "\n[INSERT-NO][Line:".$count."] ".$values."\n";
                            }
                            fwrite($log, $str_log);
                        }

                        $count++;
                    }
                }
            }

            if ($exist && file_exists($file_insert)) {
                $fp             = fopen($file_insert, "r");
                $total_update   = 0;
                $total_no_update= 0;

                while($data = fgetcsv($fp)) {
                    $values     = "'".$group_id."'";
                    $num        = count($data);
                    $label_id   = "";
                    $udata      = "";
                    $arr_discount_insert = "";

                    for ($i = 0; $i < $num; $i++) {
                        $d = $data[$i];

                        if ($d == "sku") {
                            $count--;
                            break;
                        }

                        if($i == 2) {
                            $udata[] = addslashes($data[$i]);
                        }

                        if ($i > 5) {
                            if ($i == 6)  $label_id = $d;

                            $udata[]    = addslashes($data[$i]);
                            $values     = $values.","."'".addslashes($data[$i])."'";
                        }
                    }

                    if ($label_id != "") {
                        $query      = "";
                        $got_record = "";
                        $arr_disc_item['sp_group_id'] = $group_id;

                        for ($j = 0; $j < count($arr_columns); $j++) {
                            $column = $arr_columns[$j];
                            $u      = addslashes($udata[$j]);

                            $arr_disc_item[$column] = $u;

                            if ($query == "") {
                                $query = $column." = '".$u."'";
                            } else {
                                $query .= ", ".$column." = '".$u."'";
                            }
                        }

                        $arr_discount_insert[] = $arr_disc_item;

                        $str_log = "\n[QUERY][label_id: $label_id][group_id: $group_id] ".$query."\n";
                        $got_record = SpecialPrice::check_record($label_id, $group_id);

                        if (count($got_record) > 0) {
                            unset($arr_disc_item['label']);
                            // echo "<br>=============================== [ U P D A T E - Group: ".$group_id."] = [Label ID: ".$label_id."] ==================================";
                            // var_dump($arr_disc_item);
                            if (SpecialPrice::update_special_price($group_id, $label_id, $arr_disc_item)) {
                                $str_log .= "[UPDATE-OK][Line:".$count."][VALUES] ".$values."\n";
                                $total_update++;
                            } else {
                                $str_log .= "[UPDATE-NO][Line:".$count."][VALUES] ".$values."\n";
                                $total_no_update++;
                            }
                            fwrite($log, $str_log);
                        }
                        else {
                            // echo "<br>=============================== [ I N S E R T - Group: ".$group_id."] = [Label ID: ".$label_id."] ==================================";
                            // var_dump($arr_disc_item);

                            if(SpecialPrice::insert_special_price($arr_disc_item)) {
                                $str_log .= "[INSERT-OK][Line:".$count."][VALUES] ".$values."\n";
                                fwrite($log, $str_log);
                                $total_insert++;
                            }
                        }
                        $str_log = "";
                    }
                    
                    $count++;
                }
            }

            $str_log  = "\n=============================================================================================================================";
            $str_log .= "\nTOTAL RECORDS    : ".$count;
            $str_log .= "\nTOTAL UPDATED    : ".$total_update;
            $str_log .= "\nTOTAL NOT UPDATED: ".$total_no_update;
            $str_log .= "\nTOTAL INSERT     : ".$total_insert;
            $str_log .= "\n=============================================================================================================================\n";

            fwrite($log, $str_log);
            fclose($log);
            ProductUpdate::update_job_queue($job->id, array('status' => '2', 'out_file' => $log_name));

            return Redirect::to('special_price/import');
        }
    }


    public function getCancel($id=null) {
        if(Input::has('export')) {
            $id     = Input::get('export');
            SpecialPrice::update_job_queue($id, array('status' => '3'));
            return Redirect::to('special_price/export');
        } 
        else if(Input::has('import')) {
            $id     = Input::get('import');
            SpecialPrice::update_job_queue($id, array('status' => '3'));
            return Redirect::to('special_price/import');
        }
    }

    public function anyDelete($id) {
        $sp = DB::delete('jocom_sp_product_price')->where('sp_group_id', $id)->delete();
    }

    public function get_data_to_csv($id, $file) {
        echo "<br> - - - [get_data_to_csv] - - - <br>";
        echo "File: ".$file;
        // $columns = "`price.label_id`,`price.label`,`price.label_cn`,`price.label_my`,`price.seller_sku`,`price.price`,`price.price_promo`,`price.qty`,`price.p_referral_fees`,`price.p_referral_fees_type`,`price.default`,`price.product_id`,`price.status`";
        $columns = "price.label_id, price.label, price.label_cn, price.label_my, price.seller_sku, price.price, price.price_promo, price.qty, price.p_referral_fees, price.p_referral_fees_type, price.price as disc_amount, price.price as disc_type, price.default, price.product_id, price.status";

        $fp         = fopen($file, "w");
        $header     = SpecialPrice::import_get_header();
        $contents   = SpecialPrice::import_get_csv_content($id);

        if (count($header) > 0) {
            fputcsv($fp, $header);
        }
        
        foreach ($contents as $content) {
            $arr_line   = "";

            foreach ($content as $key => $value) {
                $arr_line[] = trim($value);
            }
            fputcsv($fp, $arr_line); 
        }
        
        return fclose($fp) ? true : false;    
    }

    private function filter_uploaded_file($old_file, $new_file, $start) {
        $fin        = fopen($old_file, "r");
        $fout       = fopen($new_file, "w");
        $values     = "";

        while($data = fgetcsv($fin)) {
            $arr_line   = array();
            $num        = count($data);

            for ($i = 0; $i < $num; $i++) {
                $d = $data[$i];

                if($i >= $start) {
                    $arr_line[] = $data[$i];
                }
            }
            fputcsv($fout, $arr_line);
        }

        if (fclose($fin) && fclose($fout)) {
            return true;
        } 

        return false;
    }

    private function replace_carriage_return($file) {
        $str = file_get_contents($file);
        $str = str_replace("\r", "\n", $str);

        file_put_contents($file, $str);
    }

}

?>