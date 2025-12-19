<?php

class ReportController extends BaseController
{
    private static $platform = [
        "prestomall" => 'Prestomall',
        "11Street" => '11Street',
        "lazada" => 'Lazada',
        "Qoo10" => 'Qoo10',
        "shopee" => 'Shopee',
        "astrogo" => 'Astro Go Shop',
        "vettons" => 'Vettons',
        "jocom" => 'Jocom',
    ];
    private static $po_type = [
        1 => 'PURCHASE ORDER',
        2 => 'PURCHASE REQUISITION FORM',
        3 => 'PURCHASE ORDER GS',
    ];
    
    public function anyIndex()
    {
        echo "Oops, wrong URL...";
    }

    public function anyProduct()
    {
        // die('Oops...');
        Input::flash();

        if (Input::get('generate') == true) {
            if (Input::has('email')) {
                $email = Input::get('email');
                Input::flush();
            } else {
                $errors['email'] = 'Email is required!';
                $exit            = true;
            }

            if ($exit != true) {
                $active   = Input::has('active') ? '1' : '';
                $inactive = Input::has('inactive') ? '0' : '';
                $delete   = Input::has('delete') ? '2' : '';

                $status = '';
                $status = array_diff([$active, $inactive, $delete], ['']);

                if (empty($status)) {
                    $status = ["1"];
                }

                $group_label   = Input::has('group_label') ? Input::get('group_label') : '0';
                $seller        = Input::has('seller') ? Input::get('seller') : 'all';
                $categories    = Input::has('categories') ? Input::get('categories') : null;
                $quantity      = Input::has('quantity') ? Input::get('quantity') : '0';
                $quantity_from = Input::has('quantity_from') ? Input::get('quantity_from') : '0';
                $quantity_to   = Input::has('quantity_to') ? Input::get('quantity_to') : '0';
                $stock         = Input::has('stock') ? Input::get('stock') : '0';
                $stock_from    = Input::has('stock_from') ? Input::get('stock_from') : '0';
                $stock_to      = Input::has('stock_to') ? Input::get('stock_to') : '0';
                $price         = Input::has('price') ? Input::get('price') : '0';
                $price_from    = Input::has('price_from') ? Input::get('price_from') : '0';
                $price_to      = Input::has('price_to') ? Input::get('price_to') : '0';
                $referral      = Input::has('referral') ? Input::get('referral') : '0';
                $referral_from = Input::has('referral_from') ? Input::get('referral_from') : '0';
                $referral_to   = Input::has('referral_to') ? Input::get('referral_to') : '0';
                $created       = Input::has('created') ? Input::get('created') : '0';
                $created_from  = Input::has('created_from') ? Input::get('created_from') : date('Y-m-d', strtotime("yesterday"));
                $created_to    = Input::has('created_to') ? Input::get('created_to') : date('Y-m-d');

                $data['status']        = $status;
                $data['group_label']   = $group_label;
                $data['email']         = $email;
                $data['seller']        = $seller;
                $data['categories']    = $categories;
                $data['quantity']      = $quantity;
                $data['quantity_from'] = $quantity_from;
                $data['quantity_to']   = $quantity_to;
                $data['stock']         = $stock;
                $data['stock_from']    = $stock_from;
                $data['stock_to']      = $stock_to;
                $data['price']         = $price;
                $data['price_from']    = $price_from;
                $data['price_to']      = $price_to;
                $data['referral']      = $referral;
                $data['referral_from'] = $referral_from;
                $data['referral_to']   = $referral_to;
                $data['created']       = $created;
                $data['created_from']  = $created_from;
                $data['created_to']    = $created_to;

                $path = Config::get('constants.REPORT_PATH');
                $date = date('Ymd_his');

                $fileName = 'report_general_product_'.$date.'.csv';

                $job               = [];
                $job['ref_id']     = '0';
                $job['job_name']   = "general_report_product";
                $job['in_file']    = $fileName;
                $job['remark']     = json_encode(array_merge($data));
                $job['request_by'] = Session::get('user_id');
                $job['request_at'] = date('Y-m-d H:i:s');

                $products = GeneralReport::get_product($data);
                // $products = GeneralReport::get_product($status, $seller, $categories, $quantity, $quantity_from, $quantity_to, $price, $price_from, $price_to, $referral, $referral_from, $referral_to, $created, $created_from, $created_to);

                $queries = DB::getQueryLog();

                $last_query         = end($queries);
                $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
                $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
                $query['count']     = count($products);
                $query['filename']  = $job['in_file'];
                $query['email']     = $email;

                if (count($products) > 0) {
                    $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);
                    $toview    = "success";
                    $toviewmsg = "A report is in queue!";
                } else {
                    $toview    = "message";
                    $toviewmsg = "No report will be generated!";
                }
            }

        }

        $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', '=', 'general_report_product')->where('request_by', '=', Session::get('user_id'))->orderBy('id', 'desc')->get();

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

        $parentCategory[] = [
            'id'               => 0,
            'category_name'    => 'Parent',
            'category_name_cn' => 'Parent',
            'category_name_my' => 'Parent',
            'status'           => 1,
            'permission'       => 0,
            'weight'           => 10,
        ];

        $temp = new Category;

        $categoriesOptions = array_merge($parentCategory, Product::arrangeCategories($temp->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- '));

        return View::make('report.product')
            ->with('row', $job)
            ->with('query', $query)
            ->with('sellersOptions', $sellersOptions)
            ->with('categoriesOptions', $categoriesOptions)
            ->withErrors($errors)
            ->with($toview, $toviewmsg);

    }

    public function anyTransaction()
    {
        //  die('Oops...');
        Input::flash();
        set_time_limit(0);
       
        if (Input::get('generate') == true) {
            if (Input::has('email')) {
                $email = Input::get('email');
                Input::flush();
            } else {
                $errors['email'] = 'Email is required!';
                $exit            = true;
            }

            if ($exit != true) {
                $completed = Input::has('completed') ? 'completed' : '';
                $cancelled = Input::has('cancelled') ? 'cancelled' : '';
                $refund    = Input::has('refund') ? 'refund' : '';
                $pending   = Input::has('pending') ? 'pending' : '';

                $status = '';
                $status = array_diff([$completed, $cancelled, $refund, $pending], ['']);

                if (empty($status)) {
                    $status = ["completed"];
                }
                
                $street = Input::has('prestomall') ? 'prestomall' : '';
                $street11 = Input::has('11Street') ? '11Street' : '';
                $lazada = Input::has('lazada') ? 'lazada' : '';
                $qoo10    = Input::has('Qoo10') ? 'Qoo10' : '';
                $shopee   = Input::has('shopee') ? 'shopee' : '';
                $jocom   = Input::has('jocom') ? 'jocom' : '';
                $astro   = Input::has('astrogo') ? 'Astro Go Shop' : '';
                $vettons   = Input::has('vettons') ? 'vettons' : '';
             
                
                $buyer_username = '';
                $buyer_username = array_diff([$street, $street11, $lazada, $qoo10, $shopee, $jocom, $astro, $vettons], ['']);

                if (Input::has('agent') && (Input::get('agent') != 'all')) {
                    $agent = Agent::where('agent_code', '=', Input::get('agent'))->pluck('id');
                } else {
                    $agent = '';
                }
                
                $r_countryid  = Input::has('region_country_id') ? Input::get('region_country_id') : '0';
                $region_id    = Input::has('region_id') ? Input::get('region_id') : '0';
                
				$company      = Input::has('company') ? Input::get('company') : '0';
                $gateway      = Input::has('gateway') ? Input::get('gateway') : '0';
                $customer     = Input::has('customer') ? Input::get('customer') : 'all';
                $seller       = Input::has('seller') ? Input::get('seller') : 'all';
                $breakdown    = Input::has('breakdown') ? Input::get('breakdown') : '0';
                $product      = Input::has('product') ? Input::get('product') : 'all';
                $amount       = Input::has('amount') ? Input::get('amount') : '0';
                $amount_from  = Input::has('amount_from') ? Input::get('amount_from') : '0';
                $amount_to    = Input::has('amount_to') ? Input::get('amount_to') : '0';
                $created      = Input::has('created') ? Input::get('created') : '0';
                $created_from = Input::has('created_from') ? Input::get('created_from') : date('Y-m-d', strtotime("yesterday"));
                $created_to   = Input::has('created_to') ? Input::get('created_to') : date('Y-m-d');
                $special_msg  = Input::has('special_msg') ? Input::get('special_msg') : '0';
                
                $data['r_countryid']  = $r_countryid;
                $data['region_id']    = $region_id;
                
                $data['status']       = $status;
                $data['buyer_username']= $buyer_username;
                $data['email']        = $email;
                $data['company']      = $company;
                $data['customer']     = $customer;
                $data['seller']       = $seller;
                $data['breakdown']    = $breakdown;
                $data['agent']        = $agent;
                $data['product']      = $product;
                $data['amount']       = $amount;
                $data['amount_from']  = $amount_from;
                $data['amount_to']    = $amount_to;
                $data['created']      = $created;
                $data['created_from'] = $created_from;
                $data['created_to']   = $created_to;
                $data['special_msg']  = $special_msg;
                $data['gateway']      = $gateway;

                $path = Config::get('constants.REPORT_PATH');
                $date = date('Ymd_his');

                $fileName = 'report_general_transaction_'.$date.'.csv';

                $job               = [];
                $job['ref_id']     = '0';
                $job['job_name']   = "general_report_transaction";
                $job['in_file']    = $fileName;
                $job['remark']     = json_encode(array_merge($data));
                $job['request_by'] = Session::get('user_id');
                $job['request_at'] = date('Y-m-d H:i:s');
                
                //  echo '<pre>';
                // print_r($data); 
                // echo '</pre>';
                
                $products = GeneralReport::get_transaction($data);
                
                //  echo '<pre>';
                // print_r($products); 
                // echo '</pre>';
                // die();
                $queries  = DB::getQueryLog();

                $last_query         = end($queries);
                $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
                $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
                $query['count']     = count($products);
                $query['filename']  = $job['in_file'];
                $query['email']     = $email;

                if (count($products) > 0) {
                    $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);
                    $toview    = "success";
                    $toviewmsg = "A report is in queue!";
                } else {
                    $toview    = "message";
                    $toviewmsg = "No report will be generated!";
                }
            }

        }

        $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', '=', 'general_report_transaction')->where('request_by', '=', Session::get('user_id'))->orderBy('id', 'desc')->get();

        // $customers = Customer::orderBy('username', 'asc')->get();

        // foreach ($customers as $customer)
        // {
        //     switch ($customer->active_status)
        //     {
        //         case 0:
        //             $status = ' **[Inactive]';
        //             break;
        //         case 2:
        //             $status = ' **[Deleted]';
        //             break;
        //         default:
        //             $status = '';
        //             break;
        //     }

        //     $customersOptions[$customer->username] = $customer->username." (".$customer->full_name.") ".$status;
        // }
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id",458)->get();
        
        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
        }
        
        return View::make('report.transaction')
            ->with('row', $job)
            ->with('query', $query)
            ->with('countries', $countries)
            ->with('regions', $regions)
        // ->with('customersOptions', $customersOptions)
            ->withErrors($errors)
            ->with($toview, $toviewmsg);

    }

    public function anyTop()
    {
        Input::flash();

        if (Input::get('generate') == true) {
            if (Input::has('email')) {
                $email = Input::get('email');
                Input::flush();
            } else {
                $errors['email'] = 'Email is required!';
                $exit            = true;
            }

            if ($exit != true) {
                $customer     = Input::has('customer') ? Input::get('customer') : 'all';
                $created      = Input::has('created') ? Input::get('created') : '0';
                $created_from = Input::has('created_from') ? Input::get('created_from') : date('Y-m-d', strtotime("yesterday"));
                $created_to   = Input::has('created_to') ? Input::get('created_to') : date('Y-m-d');
                $topcount     = Input::has('topcount') ? Input::get('topcount') : '';
                $sort_type    = Input::has('sort_type') ? Input::get('sort_type') : '0';

                $data['email']        = $email;
                $data['customer']     = $customer;
                $data['created']      = $created;
                $data['created_from'] = $created_from;
                $data['created_to']   = $created_to;
                $data['topcount']     = $topcount;
                $data['sort_type']    = $sort_type;

                $path = Config::get('constants.REPORT_PATH');
                $date = date('Ymd_his');

                $fileName = 'report_general_top_'.$date.'.csv';

                $job               = [];
                $job['ref_id']     = '0';
                $job['job_name']   = "general_report_top";
                $job['in_file']    = $fileName;
                $job['remark']     = json_encode(array_merge($data));
                $job['request_by'] = Session::get('user_id');
                $job['request_at'] = date('Y-m-d H:i:s');

                $products = GeneralReport::get_top($data);
                // $products = GeneralReport::get_top($customer, $created, $created_from, $created_to, $topcount, $sort_type);

                $queries = DB::getQueryLog();

                $last_query         = end($queries);
                $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
                $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
                $query['count']     = count($products);
                $query['filename']  = $job['in_file'];
                $query['email']     = $email;

                if (count($products) > 0) {
                    $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);
                    $toview    = "success";
                    $toviewmsg = "A report is in queue!";
                } else {
                    $toview    = "message";
                    $toviewmsg = "No report will be generated!";
                }

            }
        }

        $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', '=', 'general_report_top')->where('request_by', '=', Session::get('user_id'))->orderBy('id', 'desc')->get();

        // $customers = Customer::orderBy('username', 'asc')->get();

        // foreach ($customers as $customer)
        // {
        //     switch ($customer->active_status)
        //     {
        //         case 0:
        //             $status = ' **[Inactive]';
        //             break;
        //         case 2:
        //             $status = ' **[Deleted]';
        //             break;
        //         default:
        //             $status = '';
        //             break;
        //     }

        //     $customersOptions[$customer->username] = $customer->username." (".$customer->full_name.") ".$status;
        // }

        return View::make('report.top')
            ->with('row', $job)
            ->with('query', $query);
    }

    public function anyQrcode()
    {
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

        $parentCategory[] = [
            'id'               => 0,
            'category_name'    => 'Parent',
            'category_name_cn' => 'Parent',
            'category_name_my' => 'Parent',
            'status'           => 1,
            'permission'       => 0,
            'weight'           => 10,
        ];

        $temp = new Category;

        $categoriesOptions = array_merge($parentCategory, Product::arrangeCategories($temp->sortByWeight()->toArray(), '-- ', '-- -- ', '-- -- -- '));

        return View::make('report.qrcode')
            ->with('sellersOptions', $sellersOptions)
            ->with('categoriesOptions', $categoriesOptions)
            ->withErrors($errors)
            ->with($toview, $toviewmsg);
    }
    


    public function process_report($id = null)
    {
        //  die('Oops...');
        try{
        
    
        $tempcount = $this->processPointReport();

        //if task more than 3days
        $job_check = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', 'LIKE', '%general_report_%')->orderBy('id', 'desc')->get();

        foreach ($job_check as $row) {

            $request_date = date('Y-m-d', strtotime($row->request_at));

            // echo "2day: " . date('Y-m-d', strtotime('2 days ago')) . "<br>";
            // echo "2day: " . strtotime('2 days ago') . "<br>";
            // echo "mydate: " . $request_date . "<br>";

            if (strtotime($request_date) < strtotime('3 days ago')) {
                $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 3]);
            }
        }
        // end for if task more than 3days

        if (isset($id)) {
            $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0])->where('id', '=', $id)->get();
        } else {
            $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0])->where('job_name', 'LIKE', 'general_report_%')->orderBy('id', 'asc')->get();
        }

        foreach ($job as $row) {
            $done = false;

            if ($row->remark == '') {
                $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 3]);
                break;
            } else
            // update to Process
            {
                $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 1]);
            }

            $data = json_decode($row->remark, true);

            $report_type = explode("_", $row->in_file);

            if (trim($report_type[2]) == 'product') {
                $status        = $data['status'];
                $group_label   = $data['group_label'];
                $email         = explode(",", $data['email']);
                $seller        = $data['seller'];
                $categories    = $data['categories'];
                $quantity      = $data['quantity'];
                $quantity_from = $data['quantity_from'];
                $quantity_to   = $data['quantity_to'];
                $stock         = $data['stock'];
                $stock_from    = $data['stock_from'];
                $stock_to      = $data['stock_to'];
                $price         = $data['price'];
                $price_from    = $data['price_from'];
                $price_to      = $data['price_to'];
                $referral      = $data['referral'];
                $referral_from = $data['referral_from'];
                $referral_to   = $data['referral_to'];
                $created       = $data['created'];
                $created_from  = $data['created_from'];
                $created_to    = $data['created_to'];

                $products = GeneralReport::get_product($data);

                if (count($products) > 0) {
                    $path = Config::get('constants.REPORT_PATH');

                    $fileName = $row->in_file;

                    $file = fopen($path.'/'.$fileName, 'w');

                    fputcsv($file, ['SKU', 'Product Name', 'Label', 'Seller', 'Category', 'CategoryID', 'Quantity', 'Stock', 'Price', 'Promo', 'Referral Fees', 'Referral Type', 'Status', 'Date Created', 'Date Modified', 'Cost Price']);

                    foreach ($products as $record) {

                        fputcsv($file, [
                            $record->sku,
                            $record->name,
                            $record->label,
                            $record->seller,
                            $record->category,
                            $record->categoryID,
                            $record->qty,
                            $record->stock,
                            $record->price,
                            $record->promo,
                            $record->referral_fees,
                            $record->referral_fees_type,
                            $record->status,
                            $record->insert_date,
                            $record->modify_date,
                            $record->cost_price,
                        ]);
                    }

                    fclose($file);

                    $mail = $email;

                    $subject = "General Report: ".$fileName;
                    $attach  = $path."/".$fileName;

                    $temp = 'Product Listing: ';

                    if ($seller != null and $seller != 'all') {
                        $temp = $temp." [Seller ID ".$seller."]";
                    }

                    if ( ! empty($categories) and is_array($categories)) {
                        $temp = $temp." [Category ID ".implode(", ", $categories)."]";
                    }

                    if ($group_label == '1') {
                        $temp = $temp." [Display Multi Price Label]";
                    }

                    if ($quantity != '0') {
                        $temp = $temp." [Quantity from ".$quantity_from." to ".$quantity_to."]";
                    }

                    if ($stock != '0') {
                        $temp = $temp." [Stock from ".$stock_from." to ".$stock_to."]";
                    }

                    if ($price != '0') {
                        $temp = $temp." [Price from ".$price_from." to ".$price_to."]";
                    }

                    if ($referral == '1') {
                        $temp = $temp." [Referral Fees from ".Config::get('constants.CURRENCY').$referral_from." to ".Config::get('constants.CURRENCY').$referral_to."]";
                    }

                    if ($referral == '2') {
                        $temp = $temp." [Referral Fees from ".$referral_from."% to ".$referral_to."%]";
                    }

                    if ($created == '1') {
                        $temp = $temp." [Date Created from ".$created_from." to ".$created_to."]";
                    }

                    if ($created == '2') {
                        $temp = $temp." [Date Modified from ".$created_from." to ".$created_to."]";
                    }

                    $body = ['title' => $temp];

                    $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 2]);

                    $done = true;

                    $tempcount++;
                }
            }

            if (trim($report_type[2]) == 'transaction') {
                $status       = $data['status'];
                $email        = explode(",", $data['email']);
                $company      = $data['company'];
                $customer     = $data['customer'];
                $delivery_name     = $data['delivery_name'];
                $delivery_no     = $data['delivery_contact_no'];
                $breakdown    = $data['breakdown'];
                $agent        = $data['agent'];
                $product      = $data['product'];
                $amount       = $data['amount'];
                $amount_from  = $data['amount_from'];
                $amount_to    = $data['amount_to'];
                $created      = $data['created'];
                $created_from = $data['created_from'];
                $created_to   = $data['created_to'];
                $special_msg  = $data['special_msg'];
                $gateway      = $data['gateway'];

                switch ($gateway) {
                    case '1': $gw = 'ManagePay';
                        break;
                    case '2': $gw = 'MOLPay';
                        break;
                    case '3': $gw = 'PayPal';
                        break;
                    case '4': $gw = 'Boost';
                        break;
                }

                $transaction = GeneralReport::get_transaction($data);
                
                // echo '<pre>';
                // print_r($transaction); 
                // echo '</pre>';
                
                // die();

                if (count($transaction) > 0) {
                    $path = Config::get('constants.REPORT_PATH');

                    $fileName = $row->in_file;

                    $file = fopen($path.'/'.$fileName, 'w');

                    if ($breakdown == '0') {
                        fputcsv($file, ['Date', 'TransID', 'Invoice','Invoice Date','DO NO', 'Buyer Username','Delivery Name','Delivery Contact No', 'State','Status', 'Order Number', 'Delivery Charges', 'Process Fees', 'GST Delivery', 'GST Process', 'GST Total', 'Total', 
                        				'Total Product Disc ', 'Total GMV Without GST','Total GMV GST','Total Product Disc by Coupon', 'Free Delivery by Coupon', 'Free Processing by Coupon', 'Point Redeemed', 'Point Redeemed Amount', 'Point Earned', 
                        				'Point Earned Amount', 'Total Paid', 'Special Message', 'Agent Code','Coupon Code', 'Payment Gateway'
                        		]);

                        foreach ($transaction as $record)
                        {
                            
                            if ($data['company'] == 0)
                            {
                                if ($record->mpayid != '' or $record->mpayid != NULL)
                                    $gateway = 'ManagePay';

                                elseif ($record->molpayid != '' or $record->molpayid != NULL)
                                    $gateway = 'MOLPay';

                                elseif ($record->paypalid != '' or $record->paypalid != NULL)
                                    $gateway = 'PayPal';
                                
                                elseif ($record->boostid != '' or $record->boostid != NULL)
                                    $gateway = 'Boost';
                                
                                elseif ($record->revpayid != '' or $record->revpayid != NULL)
                                    $gateway = 'RevPay';
                                
                                elseif ($record->grabpayid != '' or $record->grabpayid != NULL)
                                    $gateway = 'GrabPay';
                                    
                                else
                                    $gateway = 'Cash';
                                    
                                switch ($record->buyer_username) {
                                    case 'prestomall':
                                        $order_number = $record->order_number_eleven != '' ? $record->order_number_eleven : $record->external_ref_number;
                                        break;
                                    case 'lazada':
                                        $order_number = $record->order_number_lazada != '' ? $record->order_number_lazada : $record->external_ref_number;;
                                        break;
                                    case 'shopee':
                                        $order_number = $record->order_number_shopee != '' ? $record->order_number_shopee : $record->external_ref_number;;
                                        break;
                                    case 'Qoo10':
                                        $order_number = $record->order_number_qoo10 != '' ? $record->order_number_qoo10 : $record->external_ref_number;;
                                        break;

                                    default:
                                        $order_number = $record->external_ref_number === NULL ? '':$record->external_ref_number;
                                        break;
                                }
                                
                                if($record->buyer_username == 'Qoo10' && $record->quo_total_amount > 0){
                                    
                                    if($record->quo_total_amount > 0){
                                        $actual_amount = $record->quo_total_amount + $record->quo_gst_total;
                                    }else{
                                        $actual_amount = $record->total_amount + $record->gst_total;
                                    }
                                    
                                    $delivery_charges = $record->quo_delivery_charges > 0 ? $record->quo_delivery_charges : $record->delivery_charges;
                                    $gst_delivery = $record->quo_gst_delivery > 0 ? $record->quo_gst_delivery : $record->gst_delivery;
                                    $gst_total = $record->quo_gst_total > 0 ? $record->quo_gst_total : $record->gst_total;
                                    $total_amount = $record->quo_total_amount > 0 ? $record->quo_total_amount : $record->total_amount;
                                    $actual_total = $actual_amount;
                                    
                                    
                                }else{
                                    
                                    $delivery_charges = $record->delivery_charges;
                                    $gst_delivery = $record->gst_delivery;
                                    $gst_total = $record->gst_total;
                                    $total_amount = $record->total_amount;
                                    $actual_total = $record->actual_total < 0 ? 0 : $record->actual_total; // $record->actual_total;
                                    
                                }
                                
                                // Calculate GMV
                                
                                if( in_array(Session::get('username'), Config::get('constants.REPORT_GMV_GROUP'))){
                                    
                                    $GMVInfo = DB::table('jocom_transaction_details')->select(DB::raw("SUM(disc_per_unit * unit) as ItemTotalDiscount , 
                                    SUM(actual_total_amount) as actual_total_amount , 
                                    SUM(actual_price_gst_amount) as actual_price_gst_amount"))
                                    ->where('transaction_id', '=', $record->id)->first();
                                    
                                    $GMVItemTotalDiscount = $GMVInfo->ItemTotalDiscount;
                                    $GMVactual_total_amount = $GMVInfo->actual_total_amount;
                                    $GMVactual_price_gst_amount = $GMVInfo->actual_price_gst_amount;
                                    
                                }else{
                                    $GMVItemTotalDiscount = '-';
                                    $GMVactual_total_amount = '-';
                                    $GMVactual_price_gst_amount = '-';
                                }

                                
                                
                                // MShopping
                                fputcsv($file, [
                                    $record->date,
                                    $record->id,
                                    $record->invoice_no,
                                    $record->invoice_date,
                                    $record->do_no,
                                    $record->buyer_username,
                                    $record->delivery_name,
                                    $record->delivery_contact_no,
                                    $record->delivery_state,
                                    $record->status,
                                    $order_number,
                                    $delivery_charges, //$record->delivery_charges,
                                    $record->process_fees,
                                    $gst_delivery, //$record->gst_delivery,
                                    $record->gst_process,
                                    $gst_total, //$record->gst_total,
                                    $total_amount, //$record->total_amount,
                                    $GMVItemTotalDiscount,
                                    $GMVactual_total_amount,
                                    $GMVactual_price_gst_amount,
                                    $record->coupon_amount,
                                    $record->coupon_delivery_fee,
                                    $record->coupon_processing_fee,
                                    $record->point_redeemed,
                                    $record->point_amount,
                                    $record->point_earned,
                                    $record->point_earned_value,
                                    $actual_total,
                                    $record->special_msg,
                                    $record->agent_code,
                                    $record->coupon_code,
                                    $gateway,
                                ]);
                            }
                            else
                            {
                                
                                switch ($record->buyer_username) {
                                    case 'prestomall':
                                        $order_number = $record->order_number_eleven != '' ? $record->order_number_eleven : $record->external_ref_number;
                                        break;
                                    case 'lazada':
                                        $order_number = $record->order_number_lazada != '' ? $record->order_number_lazada : $record->external_ref_number;;
                                        break;
                                    case 'shopee':
                                        $order_number = $record->order_number_shopee != '' ? $record->order_number_shopee : $record->external_ref_number;;
                                        break;
                                    case 'Qoo10':
                                        $order_number = $record->order_number_qoo10 != '' ? $record->order_number_qoo10 : $record->external_ref_number;;
                                        break;

                                    default:
                                        $order_number = $record->external_ref_number === NULL ? '':$record->external_ref_number;
                                        break;
                                }
                                
                                // Amount will use quo10 invoice price
                                if($record->buyer_username == 'Qoo10' && $record->quo_total_amount > 0){
                                    
                                    $delivery_charges = $record->quo_delivery_charges;
                                    $gst_delivery = $record->quo_gst_delivery;
                                    $gst_total = round($record->quo_gst_total - $record->quo_gst_delivery, 2);
                                    $total_amount = round($record->quo_total_amount - $record->quo_delivery_charges, 2); 
                                    $total_paid = round($record->quo_gst_total + $record->quo_total_amount, 2);
                                    
                                    
                                }else{
                                    
                                    $delivery_charges = $record->delivery_charges;
                                    $gst_delivery = $record->gst_delivery;
                                    $gst_total = round($record->gst_total - $record->gst_delivery - $record->gst_process, 2);
                                    $total_amount = round($record->total_amount - $record->delivery_charges - $record->process_fees, 2);
                                    $total_paid_raw = round($record->actual_total - $record->delivery_charges - $record->process_fees - $record->gst_delivery - $record->gst_process + $record->point_amount, 2);
                                    
                                    $total_paid = $total_paid_raw < 0  ? 0 : $total_paid_raw;
                                    
                                }
                                
                                // E37
                                fputcsv($file, [
                                    $record->date,
                                    $record->id,
                                    $record->parent_invoice,
                                    $record->invoice_date,
                                    $record->do_no,
                                    $record->buyer_username,
                                    $record->delivery_name,
                                    $record->delivery_contact_no,
                                    $record->delivery_state,
                                    $record->status,
                                    $order_number,
                                    '0', //$record->delivery_charges,
                                    '0', //$record->process_fees,
                                    '0', //$record->gst_delivery,
                                    '0', //$record->gst_process,
                                    $gst_total, // round($record->gst_total - $record->gst_delivery - $record->gst_process, 2),
                                    $total_amount, // round($record->total_amount - $record->delivery_charges - $record->process_fees, 2),
                                    $record->coupon_amount,
                                    '0', //$record->coupon_delivery_fee,
                                    '0', //$record->coupon_processing_fee,
                                    '0', //$record->point_redeemed,
                                    '0', //$record->point_amount,
                                    '0', //$record->point_earned,
                                    '0', //$record->point_earned_value,
                                    $total_paid, // round($record->actual_total - $record->delivery_charges - $record->process_fees - $record->gst_delivery - $record->gst_process + $record->point_amount, 2),
                                    '', //$record->special_msg,
                                    '', //$record->agent_code,
                                    '', //$gateway,
                                ]);
                            }

                            
                        }
                    } else {
                        fputcsv($file, ['Date', 'TransID', 'Invoice','DO NO', 'Buyer Username','Delivery Name','Delivery Contact No','Delivery State','Seller', 'Status', 'Order Number', 'Delivery Charges', 'Process Fees', 'GST Delivery', 'GST Process', 'GST Total', 'Order Total Amount', 
                        				'Total Product Disc by Coupon', 'Free Delivery by Coupon', 'Free Processing by Coupon', 
                        				'Point Redeemed', 'Point Redeemed Amount', 'Point Earned', 'Point Earned Amount', 'Total Paid', 'Special Message', 'Agent Code', 'Coupon Code', 'Payment Gateway', 
                        				'SKU', 'Product Name', 'Label','Base Product ID', 'Base Product Name', 'Actual Price', 'Price', 'Unit','Cost Unit', 'Referral Fees', 'Referral Type', 'Seller', 'Disc', 'GST Rate', 'GST Amount','Total Excluded GST', 'Item Total Amount','Total GST GMV','Total GMV Before GST','GST Seller', 'PO No', 'Platform original price'
                        		]);

                        foreach ($transaction as $record)
                        {
                            if ($data['company'] == 0)
                            {
                                
                                
                                
                                if ($record->mpayid != '' or $record->mpayid != NULL)
                                    $gateway = 'ManagePay';

                                elseif ($record->molpayid != '' or $record->molpayid != NULL)
                                    $gateway = 'MOLPay';

                                elseif ($record->paypalid != '' or $record->paypalid != NULL)
                                    $gateway = 'PayPal';
                                
                                elseif ($record->boostid != '' or $record->boostid != NULL)
                                    $gateway = 'Boost';
                                    
                                elseif ($record->revpayid != '' or $record->revpayid != NULL)
                                    $gateway = 'RevPay';
                                
                                elseif ($record->grabpayid != '' or $record->grabpayid != NULL)
                                    $gateway = 'GrabPay';
                                
                                else
                                    $gateway = 'Cash';
                                    
                                switch ($record->buyer_username) {
                                    case 'prestomall':
                                        $order_number = $record->order_number_eleven != '' ? $record->order_number_eleven : $record->external_ref_number;
                                        break;
                                    case 'lazada':
                                        $order_number = $record->order_number_lazada != '' ? $record->order_number_lazada : $record->external_ref_number;;
                                        break;
                                    case 'shopee':
                                        $order_number = $record->order_number_shopee != '' ? $record->order_number_shopee : $record->external_ref_number;;
                                        break;
                                    case 'Qoo10':
                                        $order_number = $record->order_number_qoo10 != '' ? $record->order_number_qoo10 : $record->external_ref_number;;
                                        break;

                                    default:
                                        $order_number = $record->external_ref_number === NULL ? '':$record->external_ref_number;
                                        break;
                                }
                                
                                if($record->buyer_username == 'Qoo10' && $record->quo_total_amount > 0){
                                    
                                    $unit_price = $record->quo_price > 0 ? $record->quo_price : $record->price;
                                    $gst_amount = $record->quo_gst_amount > 0 ? $record->quo_gst_amount : $record->gst_amount;
                                    
                                }else{
                                    $unit_price = $record->price;
                                    $gst_amount = $record->gst_amount;
                                }
                                
                                // SECTION MAIN TRANSACTION 
                                if($record->buyer_username == 'Qoo10' && $record->quo_total_amount > 0){
                                    
                                    $delivery_charges = $record->quo_delivery_charges;
                                    $gst_delivery = $record->quo_gst_delivery;
                                    $gst_total = $record->quo_gst_total;
                                    $total_amount = $record->quo_total_amount;
                                    $actual_total = $record->quo_total_amount + $record->quo_gst_total;
                                    $actual_price = $unit_price;
                                    
                                }else{
                                    
                                    $delivery_charges = $record->delivery_charges;
                                    $gst_delivery = $record->gst_delivery;
                                    $gst_total = $record->gst_total;
                                    $total_amount = $record->total_amount;
                                    $actual_total = $record->actual_total < 0 ? 0 : $record->actual_total; // $record->actual_total;
                                    $actual_price = $record->actual_price;
                                    
                                }
                                
                                if($record->date < '2017-09-31 23:59:59'){
                                    $item_total_amount = $record->unit * ($unit_price + $gst_amount);
                                }else{
                                    $item_total_amount = $record->unit * $unit_price ;
                                }
                                
                                if( in_array(Session::get('username'), Config::get('constants.REPORT_GMV_GROUP'))){
                                    $actual_price_gst_amount = $record->actual_price_gst_amount;
                                    $actual_total_amount = $record->actual_total_amount;
                                }else{
                                    $actual_price_gst_amount = '-' ;
                                    $actual_total_amount = '-' ;
                                }
                                
                               
                                
                                // MShopping
                                fputcsv($file, [
                                    $record->date,
                                    $record->id,
                                    $record->invoice_no,
                                    $record->do_no,
                                    $record->buyer_username,
                                    $record->delivery_name,
                                    $record->delivery_contact_no,
                                    $record->delivery_state,
                                    $record->seller_name,
                                    $record->status,
                                    $order_number,
                                    $delivery_charges, //$record->delivery_charges,
                                    $record->process_fees,
                                    $gst_delivery, //$record->gst_delivery,
                                    $record->gst_process,
                                    $gst_total,
                                    $total_amount, //$record->total_amount,
                                    $record->coupon_amount,
                                    $record->coupon_delivery_fee,
                                    $record->coupon_processing_fee,
                                    $record->point_redeemed,
                                    $record->point_amount,
                                    $record->point_earned,
                                    $record->point_earned_value,
                                    $actual_total, //$record->actual_total,
                                    $record->special_msg,
                                    $record->agent_code,
                                    $record->coupon_code,
                                    $gateway,
                                    $record->sku,
                                    $record->product_name,
                                    trim(strip_tags($record->price_label)),
                                    $record->base_product_id,
                                    $record->base_product_name,
                                    $actual_price,
                                    $unit_price, // $record->price,
                                    $record->unit,
                                    $record->cost_unit_amount,
                                    $record->p_referral_fees,
                                    $record->p_referral_fees_type,
                                    $record->seller_username,
                                    $record->disc,
                                    $record->gst_rate_item,
                                    $gst_amount,
                                    $item_total_amount - $gst_amount,
                                    $item_total_amount, 
                                    $actual_price_gst_amount,
                                    $actual_total_amount,
                                    $record->gst_seller,
                                    $record->po_no,
                                    $record->platform_original_price,
                                ]);
                            }
                            else
                            {
                                // E37
                                
                                switch ($record->buyer_username) {
                                    case 'prestomall':
                                        $order_number = $record->order_number_eleven != '' ? $record->order_number_eleven : $record->external_ref_number;
                                        break;
                                    case 'lazada':
                                        $order_number = $record->order_number_lazada != '' ? $record->order_number_lazada : $record->external_ref_number;;
                                        break;
                                    case 'shopee':
                                        $order_number = $record->order_number_shopee != '' ? $record->order_number_shopee : $record->external_ref_number;;
                                        break;
                                    case 'Qoo10':
                                        $order_number = $record->order_number_qoo10 != '' ? $record->order_number_qoo10 : $record->external_ref_number;;
                                        break;

                                    default:
                                        $order_number = $record->external_ref_number === NULL ? '':$record->external_ref_number;
                                        break;
                                }
                                
                                fputcsv($file, [
                                    $record->date,
                                    $record->id,
                                    $record->parent_invoice,
                                    $record->do_no,
                                    $record->buyer_username,
                                    $record->delivery_name,
                                    $record->delivery_contact_no,
                                    $record->status,
                                    $order_number,
                                    '0', //$record->delivery_charges,
                                    '0', //$record->process_fees,
                                    '0', //$record->gst_delivery,
                                    '0', //$record->gst_process,
                                    round($record->gst_total - $record->gst_delivery - $record->gst_process, 2),
                                    round($record->total_amount - $record->delivery_charges - $record->process_fees, 2),
                                    $record->coupon_amount,
                                    '0', //$record->coupon_delivery_fee,
                                    '0', //$record->coupon_processing_fee,
                                    '0', //$record->point_redeemed,
                                    '0', //$record->point_amount,
                                    '0', //$record->point_earned,
                                    '0', //$record->point_earned_value,
                                    round($record->actual_total - $record->delivery_charges - $record->process_fees - $record->gst_delivery - $record->gst_process + $record->point_amount, 2),
                                    '', //$record->special_msg,
                                    '', //$record->agent_code,
                                    '', //$gateway,
                                    $record->sku,
                                    trim(strip_tags($record->price_label)),
                                    $record->price,
                                    $record->unit,
                                    $record->p_referral_fees,
                                    $record->p_referral_fees_type,
                                    $record->seller_username,
                                    $record->disc,
                                    $record->gst_rate_item,
                                    $record->gst_amount,
                                    $item_total_amount - $record->gst_amount,
                                    $item_total_amount,
                                    $record->gst_seller,
                                    $record->parent_po,
                                    $record->platform_original_price,
                                ]);
                            }

                            
                        }
                    }

                    fclose($file);

                    $mail = $email;

                    $subject = "General Report: ".$fileName;
                    $attach  = $path."/".$fileName;

                    $temp = 'Transaction Listing: ';

                    if ($customer != null and $customer != 'all') {
                        $temp = $temp." [Customer ID ".$customer."]";
                    }

                    if ($amount != '0') {
                        $temp = $temp." [Total Amount from ".$amount_from." to ".$amount_to."]";
                    }

                    if ($created != '0') {
                        $temp = $temp." [Transaction Date from ".$created_from." to ".$created_to."]";
                    }

                    if ($special_msg == '1') {
                        $temp = $temp." [Transaction with Special Message only]";
                    }

                    if ($breakdown != '0') {
                        $temp = $temp." [Breakdown by Product]"." [".$product."]";
                    }
                    
                    if ($gateway != '0') {
                        $temp = $temp." [Payment Gateway for ".ucfirst($gw)."]";
                    }

                    $body = ['title' => $temp];

                    $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 2]);

                    $done = true;

                    $tempcount++;
                }
            }

            if (trim($report_type[2]) == 'top') {
                $email        = explode(",", $data['email']);
                $customer     = $data['customer'];
                $created      = $data['created'];
                $created_from = $data['created_from'];
                $created_to   = $data['created_to'];
                $topcount     = $data['topcount'];
                $sort_type    = $data['sort_type'];

                $transaction = GeneralReport::get_top($data);

                if (count($transaction) > 0) {
                    $path = Config::get('constants.REPORT_PATH');

                    $fileName = $row->in_file;

                    $file = fopen($path.'/'.$fileName, 'w');

                    fputcsv($file, ['ProductID', 'Product Name', 'Label ID', 'Label', 'Category', 'Unit', 'Price', 'Total']);

                    foreach ($transaction as $record) {

                        fputcsv($file, [
                            $record->product_id,
                            $record->product_name,
                            $record->label_id,
                            $record->product_label,
                            $record->category,
                            $record->unit,
                            $record->price,
                            $record->total,
                        ]);
                    }

                    fclose($file);

                    $mail = $email;

                    $subject = "General Report: ".$fileName;
                    $attach  = $path."/".$fileName;

                    $temp = 'Top Item Sold Listing: ';

                    if ($customer != null and $customer != 'all') {
                        $temp = $temp." [Customer ID ".$customer."]";
                    }

                    if ($topcount != '') {
                        $temp = $temp." [TOP ".$topcount."]";
                    }

                    if ($created != '0') {
                        $temp = $temp." [Transaction Date from ".$created_from." to ".$created_to."]";
                    }

                    if ($sort_type == '0') {
                        $temp = $temp." [Sort By Total]";
                    }

                    if ($sort_type == '1') {
                        $temp = $temp." [Sort By Unit]";
                    }

                    $body = ['title' => $temp];

                    $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 2]);

                    $done = true;

                    $tempcount++;
                }
            }
            
            if (trim($report_type[2]) == 'dailytransaction') {
                $email = $data['email'];
                $r_countryid  = $data['r_countryid'];
                $region_id    = $data['region_id'];
                $date_from = $data['date_from'];
                $date_to = $data['date_to'];

                $products = GeneralReport::get_dailytransaction($data);

                if (count($products) > 0) {
                    $path = Config::get('constants.REPORT_PATH');

                    $fileName = $row->in_file;

                    $company_list = array();
                    $platform_username = ['Astro Go Shop', 'lazada', 'prestomall', 'Qoo10', 'shopee'];

                    foreach ($products as $product) {

                        if (in_array($product->buyer_username, $platform_username)) {
                            $product->platform = strtoupper($product->buyer_username);
                        } else {
                            $product->platform = 'JOCOM';
                        }
                        $company_list[$product->company_name][$product->sku]['platform'] = $product->platform;
                        $company_list[$product->company_name][$product->sku]['name'] = $product->name;
                        $company_list[$product->company_name][$product->sku]['label'] = $product->label;
                        $company_list[$product->company_name][$product->sku]['transaction_count'] = $product->transaction_count;
                        $company_list[$product->company_name][$product->sku]['total_required'] = number_format($product->total_required);
                    }
                    
                    $sorted_company_list = [];
                    foreach ($company_list as $seller => $details) {
                        $sorted_product = [];
                        foreach ($details as $sku => $detail) {
                            array_push($sorted_product, [
                                'platform' => $detail['platform'],
                                'sku' => $sku,
                                'name' => $detail['name'],
                                'label' => $detail['label'],
                                'transaction_count' => $detail['transaction_count'],
                                'total_required' => $detail['total_required']
                            ]);
                        }
                        usort($sorted_product, function($a, $b) {
                            return strcmp($a['platform'], $b['platform']);
                        });
                        
                        $sorted_company_list[$seller] = $sorted_product;
                    }
                    
                    ksort($sorted_company_list);

                    Excel::create($fileName, function($excel) use ($sorted_company_list, $date_from, $date_to) {
                        $excel->sheet('Warehouse', function($sheet) use ($sorted_company_list, $date_from, $date_to)
                        {   
                            $sheet->loadView('report.template_dailytransaction', array('company_list' => $sorted_company_list, 'date_from' => $date_from, 'date_to' => $date_to));
                        }); 
                    })->store('xls', $path);  

                    $mail = $email;

                    $subject = "General Report: ".$fileName;
                    $attach  = $path."/".$fileName.".xls";

                    $temp = 'Daily Transaction Listing From '.$date_from.' to '.$date_to;

                    $body = ['title' => $temp];

                    $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 2]);

                    $done = true;

                    $tempcount++;
                }
            }
            
            if (trim($report_type[1]) == 'consignment') {
                $mail = explode(",", $data['email']);
                $data['platform'] = self::$platform;
                $data['po_type'] = self::$po_type;
                $products = GeneralReport::Consignment($data);

                $seller = ($data['seller'] ? $data['seller'] : '-');
                $is_int = ((int)$data['seller'] ? 1 : 0);
                if($is_int){
                    $seller_data = DB::table('jocom_seller')->where('id', (int)$seller)->first();
                    $seller_name = $seller_data->company_name;
                }else{
                    $seller_name = $seller;
                }


                if (count($products) > 0) {
                    $path = Config::get('constants.REPORT_PATH');

                    $fileName = $row->in_file;

                    $subject = "Consignment Report: " . $fileName;
                    $file = Excel::create($row->in_file, function($excel) use ($products, $seller_name, $data) {
                            // Not more than 30 char issue
                            $excel->sheet(substr($seller_name, 0, 30), function($sheet) use ($products, $seller_name, $data){
                                $sheet->loadView('admin.templateConsignmentReport', ['data' => $products, 'input' => $data, 'seller_name' => $seller_name]);
                            });
                    });
                    $store = $file->store("xls", false, true);
                    $attach = $store['full'];


                    $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $row->id)->update(['status' => 2]);
                    $done = true;

                    $body = ['title' => ''];

                    $tempcount++;
                }
            }

            if ($done == true) {
                Mail::queue('emails.blank', $body, function ($message) use ($subject, $mail, $attach) {
                    $message->from('payment@jocom.my', 'tmGrocer');
                    $message->to($mail, '')->subject($subject);
                    $message->attach($attach);
                }
                );

                unlink($attach);
            }

        }

        $type = Input::get('type');

        switch ($type) {
            case 'trans':
                return Redirect::to('report/transaction')->with('success', $tempcount.' queing job completed with email sent out.');
                break;
            
            case 'consignment':
                return Redirect::to('report/consignmentreport')->with('success', $tempcount . ' queing job completed with email sent out.');
                break;

            case 'top':
                return Redirect::to('report/top')->with('success', $tempcount.' queing job completed with email sent out.');
                break;

            case 'point':
                return Redirect::to('report/points')->with('success', $tempcount.' queing job completed with email sent out.');
                break;
                
            case 'dailytrans':
                return Redirect::to('report/dailytransaction')->with('success', $tempcount.' queing job completed with email sent out.');
                break;

            default:
                return Redirect::to('report/product')->with('success', $tempcount.' queing job completed with email sent out.');
                break;
        }
        
    }catch (exception $ex){
        echo $ex->getMessage();
    }

    }

    public function cancel_report($id = null)
    {
        $type = Input::get('type');

        if (isset($id)) {
            $jobupdate = DB::table('jocom_job_queue')->select('*')->where('id', '=', $id)->update(['status' => 3]);
        }

        switch ($type) {
            case 'trans':
                return Redirect::to('report/transaction')->with('success', 'Report ID: '.$id.' has been cancelled.');
                break;

            case 'top':
                return Redirect::to('report/top')->with('success', 'Report ID: '.$id.' has been cancelled.');
                break;

            case 'point':
                return Redirect::to('report/points')->with('success', 'Report ID: '.$id.' has been cancelled.');
                break;
                
            case 'dailytrans':
                return Redirect::to('report/dailytransaction')->with('success', 'Report ID: '.$id.' has been cancelled.');
                break;

            default:
                return Redirect::to('report/product')->with('success', 'Report ID: '.$id.' has been cancelled.');
                break;
        }
    }

    public function anyCustomersearch()
    {
        $keyword = Input::get('keyword');
        $limit   = Input::get('limit', 10);

        if (is_numeric($keyword)) {
            return Customer::where('id', '=', $keyword)->limit($limit)->get()->toJson();
        } else {
            $keys      = ['id', 'full_name', 'username'];
            $relevants = [];
            $results   = Customer::where('full_name', 'LIKE', "%{$keyword}%")
                ->orWhere('username', 'LIKE', "%{$keyword}%")
                ->get();

            foreach ($results as $result) {
                $rate                        = floor((strlen($keyword) / strlen($result->full_name)) * 10000);
                $endrate                     = 10000 + $result->id;
                $relevants[$rate.$endrate] = "{$result->id}:{$result->full_name}:{$result->username}";
            }

            krsort($relevants);

            foreach (array_slice($relevants, 0, $limit) as $relevant) {
                $values    = explode(':', $relevant);
                $objects[] = array_combine($keys, $values);
            }

            return json_encode($objects);
        }
    }

    public function anyCategorysearch()
    {
        $keyword = Input::get('keyword');
        $limit   = Input::get('limit', 10);

        if (is_numeric($keyword)) {
            return Category::where('id', '=', $keyword)->limit($limit)->groupBy('category_name')->get()->toJson();
        } else {
            $keys      = ['id', 'category_name'];
            $relevants = [];
            $results   = Category::where('category_name', 'LIKE', "%{$keyword}%")
                ->orWhere('category_name_cn', 'LIKE', "%{$keyword}%")
                ->orWhere('category_name_my', 'LIKE', "%{$keyword}%")
                ->groupBy('category_name')
                ->get();

            foreach ($results as $result) {
                $rate                        = floor((strlen($keyword) / strlen($result->category_name)) * 10000);
                $endrate                     = 10000 + $result->id;
                $relevants[$rate.$endrate] = "{$result->id}:{$result->category_name}";
            }

            krsort($relevants);

            foreach (array_slice($relevants, 0, 5) as $relevant) {
                $values    = explode(':', $relevant);
                $objects[] = array_combine($keys, $values);
            }

            return json_encode($objects);
        }
    }



    public function anySellersearch()
    {
        $keyword = Input::get('keyword');
        $limit   = Input::get('limit', 10);

        if (is_numeric($keyword)) {
            return Seller::where('id', '=', $keyword)->limit($limit)->get()->toJson();
        } else {
            $keys      = ['id', 'company_name', 'username'];
            $relevants = [];
            $results   = Seller::where('company_name', 'LIKE', "%{$keyword}%")
                ->orWhere('username', 'LIKE', "%{$keyword}%")
                ->get();

            foreach ($results as $result) {
                $rate                        = floor((strlen($keyword) / strlen($result->company_name)) * 10000);
                $endrate                     = 10000 + $result->id;
                $relevants[$rate.$endrate] = "{$result->id}:{$result->company_name}:{$result->username}";
            }

            krsort($relevants);

            foreach (array_slice($relevants, 0, $limit) as $relevant) {
                $values    = explode(':', $relevant);
                $objects[] = array_combine($keys, $values);
            }

            return json_encode($objects);
        }
    }

    public function anyQrtransaction()
    {
        $buyer = [];

        $buyer = Input::has('customer') ? explode(',', Input::get('customer')) : ['all'];

        $listing = DB::table('jocom_transaction AS a')
            ->select('a.id', 'c.id AS product_id', 'c.name', 'b.id AS label_id', 'b.price_label', 'c.img_1', 'c.qrcode_file')
            ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
            ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
            ->where('a.status', '=', 'completed')
            ->where(function ($query) use ($buyer) {
                if ($buyer[0] == 'all') {
                    $nothing;
                } else {
                    $query->whereIn('a.buyer_username', $buyer);
                }

            })
            ->groupBy('c.id')
            ->orderBy('c.id', 'desc')
            // ->paginate(12);
            ->get();

        return View::make('report.qrlisting', ['display_listing' => $listing])->withParam('customer')->withValue(Input::get('customer'));
    }

    public function anyQrseller()
    {
        $seller = [];

        $seller = Input::has('seller') ? explode(',', Input::get('seller')) : ['all'];

        $listing = DB::table('jocom_products AS a')
            ->select('a.id AS product_id', 'a.name', 'b.id AS label_id', 'b.label AS price_label', 'a.img_1', 'a.qrcode_file')
            ->leftJoin('jocom_product_price AS b', 'a.id', '=', 'b.product_id')
            ->where(function ($query) use ($seller) {
                if ($seller[0] == 'all') {
                    $nothing;
                } else {
                    $query->whereIn('a.sell_id', $seller);
                }

            })
            ->groupBy('a.id')
            ->orderBy('a.id', 'desc')
            // ->paginate(12);
            ->get();

        return View::make('report.qrlisting', ['display_listing' => $listing])->withParam('seller')->withValue(Input::get('seller'));
    }

    public function anyQrcategory()
    {
        $categories = [];

        $categories = Input::has('categories') ? explode(';', Input::get('categories')) : ['all'];

        $listing = DB::table('jocom_products AS a')
            ->select('a.id AS product_id', 'a.name', 'b.id AS label_id', 'b.label AS price_label', 'a.img_1', 'a.qrcode_file')
            ->leftJoin('jocom_product_price AS b', 'a.id', '=', 'b.product_id')
            ->leftJoin('jocom_categories AS c', 'a.id', '=', 'c.product_id')
            ->leftJoin('jocom_products_category AS d', 'd.id', '=', 'c.category_id')
            ->where(function ($query) use ($categories) {
                if ($categories[0] == null or $categories[0] == 'all') {
                    $nothing;
                } else {
                    $query->whereIn('d.category_name', $categories);
                }

            })
            ->groupBy('a.id')
            ->orderBy('a.id', 'desc')
            // ->paginate(12);
            ->get();

        return View::make('report.qrlisting', ['display_listing' => $listing])->withParam('categories')->withValue(Input::get('categories'));
    }

    public function anyQrbycode()
    {
        $qrcodes = [];

        $qrcodes = Input::has('qrcode') ? explode(',', str_replace(' ', '', Input::get('qrcode'))) : ['all'];

        $listing = DB::table('jocom_products AS a')
            ->select('a.id AS product_id', 'a.name', 'b.id AS label_id', 'b.label AS price_label', 'a.img_1', 'a.qrcode_file')
            ->leftJoin('jocom_product_price AS b', 'a.id', '=', 'b.product_id')
            ->leftJoin('jocom_categories AS c', 'a.id', '=', 'c.product_id')
            ->leftJoin('jocom_products_category AS d', 'd.id', '=', 'c.category_id')
            ->where(function ($query) use ($qrcodes) {
                if ($qrcodes[0] == null or $qrcodes[0] == 'all') {
                    $nothing;
                } else {
                    $query->whereIn('a.qrcode', $qrcodes);
                }

            })
            ->groupBy('a.id')
            ->orderBy('a.id', 'desc')
            ->get();

        return View::make('report.qrlisting', ['display_listing' => $listing])->withParam('qrcode')->withValue(Input::get('qrcode'));
    }

    public function getPoints()
    {
        $queue = DB::table('jocom_job_queue')
            ->whereIn('status', [0, 1])
            ->where('job_name', '=', 'general_report_point')
            ->where('request_by', '=', Session::get('user_id'))
            ->orderBy('id', 'desc')
            ->get();

        return View::make('report.points', ['queue' => $queue]);
    }

    public function postPoints()
    {
        if ( ! Input::has('email')) {
            return Redirect::to('report/points');
        }

        $requests = [
            'emails'     => Input::get('email'),
            'jpoint'     => Input::get('jpoint', 0),
            'bcard'      => Input::get('bcard', 0),
            'actionType' => Input::get('action_type'),
            'amount'     => Input::get('amount', 0),
            'amountFrom' => Input::get('amount_from'),
            'amountTo'   => Input::get('amount_to'),
            'date'       => Input::get('created', 0),
            'dateFrom'   => strtotime(Input::get('created_from')),
            'dateTo'     => strtotime(Input::get('created_to')),
        ];

        $job['job_name']   = 'general_report_point';
        $job['ref_id']     = 0;
        $job['in_file']    = 'report_general_point_'.date('Ymd_his').'.csv';
        $job['remark']     = json_encode($requests);
        $job['request_by'] = Session::get('user_id');
        $job['request_at'] = date('Y-m-d H:i:s');

        DB::table('jocom_job_queue')->insert($job);

        return Redirect::to('report/points');
    }

    public function processPointReport()
    {
        $tasks = 0;
        $queue = DB::table('jocom_job_queue')
            ->where('job_name', '=', 'general_report_point')
            ->where('status', '=', 0)
            ->get();

        foreach ($queue as $record) {
            $data         = [];
            $request      = json_decode(object_get($record, 'remark'));
            $emails       = object_get($request, 'emails');
            $jpoint       = object_get($request, 'jpoint');
            $bcard        = object_get($request, 'bcard');
            $actionType   = object_get($request, 'actionType');
            $amount       = object_get($request, 'amount');
            $amountFrom   = object_get($request, 'amountFrom');
            $amountTo     = object_get($request, 'amountTo');
            $date         = object_get($request, 'date');
            $dateFrom     = object_get($request, 'dateFrom');
            $dateTo       = object_get($request, 'dateTo');
            $title[]      = 'Point Transactions';
            $pointActions = PointAction::whereIn('id', $actionType)->lists('action');

            if ($amount && $amountTo > $amountFrom) {
                $amount  = ['from' => $amountFrom, 'to' => $amountTo];
                $title[] = "[Point: {$amountFrom} - {$amountTo}]";
            } else {
                $amount = null;
            }

            if ($date && $dateTo > $dateFrom) {
                $date    = ['from' => date('Y-m-d 00:00:00', $dateFrom), 'to' => date('Y-m-d 23:59:59', $dateTo)];
                $title[] = '[Date: '.date('Y-m-d 00:00:00', $dateFrom).' - '.date('Y-m-d 23:59:59', $dateTo).']';
            } else {
                $date = null;
            }

            if ($jpoint) {
                $data = array_merge($data, ReportController::getJPointData($amount, $date));
            }

            if ($bcard) {
                $data = array_merge($data, ReportController::getBCardData($amount, $date));
                $data = array_merge($data, ReportController::getBCardVoidedData($amount, $date));
            }

            if (count($data)) {
                uasort($data, [$this, 'dateCompare']);

                $subject    = 'General Report: '.object_get($record, 'in_file');
                $fileName   = Config::get('constants.REPORT_PATH').'/'.object_get($record, 'in_file');
                $fileStream = fopen($fileName, 'w');

                fputcsv($fileStream, ['Transaction / Conversion ID', 'Date', 'Point Type', 'Point In', 'Point Out', 'Amount In', 'Amount Out', 'Point Action', 'Reversal ID', 'Reward ID', 'BCard No.']);

                foreach ($data as $transaction) {
                    if (in_array(array_get($transaction, 'action'), $pointActions)) {
                        $row = [
                            array_get($transaction, 'transaction_id'),
                            date('Y-m-d', strtotime(array_get($transaction, 'created_at'))),
                            array_get($transaction, 'type'),
                            (array_get($transaction, 'point') < 0) ? abs(array_get($transaction, 'point')) : '',
                            (array_get($transaction, 'point') >= 0) ? abs(array_get($transaction, 'point')) : '',
                            (array_get($transaction, 'point') < 0) ? abs(array_get($transaction, 'point')) * array_get($transaction, 'rate') : '',
                            (array_get($transaction, 'point') >= 0) ? abs(array_get($transaction, 'point')) * array_get($transaction, 'rate') : '',
                            array_get($transaction, 'action'),
                            array_get($transaction, 'reversal'),
                            array_get($transaction, 'reward_id'),
                            array_get($transaction, 'bcard'),
                        ];

                        fputcsv($fileStream, $row);
                    }
                }

                fclose($fileStream);

                $attachment = $fileName;

                Mail::queue('emails.blank', [
                    'title' => implode(' ', $title),
                ], function ($message) use ($subject, $emails, $attachment) {
                    $message->from('payment@jocom.my', 'tmGrocer');
                    $message->to($emails)->subject($subject);
                    $message->attach($attachment);
                });

                $queueId = object_get($record, 'id');

                DB::table('jocom_job_queue')
                    ->where('id', '=', $queueId)
                    ->update(['status' => 2]);

                unlink($attachment);
            }

            $tasks++;
        }

        return $tasks;
    }

    protected function getJPointData($amount, $date)
    {
        $pointTransaction = PointTransaction::select(
            DB::raw('IF (point_transactions.transaction_id = "", CONCAT("C", SUBSTR(point_transactions.remark, 15)), CONCAT("T", point_transactions.transaction_id)) AS transaction_id'),
            'point_transactions.created_at',
            'point_types.type',
            'point_transactions.point',
            'point_transactions.rate',
            'point_actions.action',
            'point_transactions.reversal'
        )
            ->join('point_actions', 'point_transactions.point_action_id', '=', 'point_actions.id')
            ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
            ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id');

        if ($amount) {
            $pointTransaction = $pointTransaction->where('point', '>=', array_get($amount, 'from'))
                ->where('point', '<=', array_get($amount, 'to'));
        }

        if ($date) {
            $pointTransaction = $pointTransaction->where('created_at', '>=', array_get($date, 'from'))
                ->where('created_at', '<=', array_get($date, 'to'));
        }

        $pointTransactions = $pointTransaction->get()->toArray();

        foreach ($pointTransactions as $key => $pointTransaction) {
            $data = array_merge(['type' => 'JPoint'], $pointTransaction);

            ksort($data);

            $pointTransactions[$key] = $data;
        }

        return $pointTransactions;
    }

    protected function getBCardData($amount, $date)
    {
        $bcardTransaction = BcardTransaction::select('action', 'point', 'request', 'created_at', 'reward_id');

        if ($amount) {
            $bcardTransaction = $bcardTransaction->where('point', '>=', array_get($amount, 'from'))
                ->where('point', '<=', array_get($amount, 'to'));
        }

        if ($date) {
            $bcardTransaction = $bcardTransaction->where('created_at', '>=', array_get($date, 'from'))
                ->where('created_at', '<=', array_get($date, 'to'));
        }

        $bcardTransactions = $bcardTransaction->get()->toArray();
        $type              = 'BCard';
        $pointType         = PointType::where('type', '=', $type)->first();

        foreach ($bcardTransactions as $key => $bcardTransaction) {
            $request       = json_decode(array_get($bcardTransaction, 'request'));
            $transactionId = object_get($request, 'BillNo');
            $bcard         = object_get($request, 'Card');

            if (is_numeric($transactionId)) {
                if (array_get($bcardTransaction, 'action') == 'Convert') {
                    $transactionId = 'C'.$transactionId;
                } else {
                    $transactionId = 'T'.$transactionId;
                }
            }

            unset($bcardTransaction['request']);

            $data = array_merge([
                'type'           => $type,
                'transaction_id' => $transactionId,
                'rate'           => $pointType->redeem_rate,
                'reversal'       => null,
                'bcard'          => $bcard,
            ], $bcardTransaction);

            ksort($data);

            $bcardTransactions[$key] = $data;
        }

        return $bcardTransactions;
    }

    protected function getBCardVoidedData($amount, $date)
    {
        $bcardTransaction = BcardVoid::select('action', 'point', 'request', 'created_at', 'reward_id');

        if ($amount) {
            $bcardTransaction = $bcardTransaction->where('point', '>=', array_get($amount, 'from'))
                ->where('point', '<=', array_get($amount, 'to'));
        }

        if ($date) {
            $bcardTransaction = $bcardTransaction->where('created_at', '>=', array_get($date, 'from'))
                ->where('created_at', '<=', array_get($date, 'to'));
        }

        $bcardTransactions = $bcardTransaction->get()->toArray();
        $type              = 'BCard';
        $pointType         = PointType::where('type', '=', $type)->first();

        foreach ($bcardTransactions as $key => $bcardTransaction) {
            $request       = json_decode(array_get($bcardTransaction, 'request'));
            $transactionId = object_get($request, 'BillNo');
            $bcard         = object_get($request, 'Card');
            $reversal      = array_get($bcardTransaction, 'reward_id');

            if (is_numeric($transactionId)) {
                if (array_get($bcardTransaction, 'action') == 'Convert') {
                    $transactionId = 'C'.$transactionId;
                } else {
                    $transactionId = 'T'.$transactionId;
                }
            }

            unset($bcardTransaction['request']);
            unset($bcardTransaction['reward_id']);

            $data = array_merge([
                'type'           => $type,
                'transaction_id' => $transactionId,
                'rate'           => $pointType->redeem_rate,
                'reversal'       => $reversal,
                'reward_id'      => null,
                'bcard'          => $bcard,
            ], $bcardTransaction, ['action' => 'Void']);

            ksort($data);

            $bcardTransactions[$key] = $data;
        }

        return $bcardTransactions;
    }

    protected function dateCompare($x, $y)
    {
        return array_get($x, 'created_at') > array_get($y, 'created_at') ? 1 : -1;
    }
    
    public function anyElevenstreetcompare(){

        return View::make('report.elevenstreetcompare');

    }
    
  public function anyElevenexport(){
        $isError = 0;
        $alldata = array();
        $firstcols = array();
        $orderno = "";
        $transferamount = 0; 
        $settlementamount = 0; 
        $deductedamount = 0; 
        $totalsalesamount = 0; 
        $prepaidshippingamount = 0; 
        $returnshippingamount = 0; 
        $transactionfee = 0; 
        $paymentgatewayfee = 0; 
        $discountcouponfee = 0; 
        $multipurchasedisc = 0; 
        $pointusagefee = 0; 
        $claimfee = 0; 
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
        
        try{

       //  DB::beginTransaction();    
        $filename = Input::file('csv');

        $filesize = filesize(Input::file('csv'));
       
       $handle = fopen($filename, "r");

       $file_name = Input::file('csv')->getClientOriginalName();

      
        $first_row = true;
        $final_ata = array();
        $headers = array();
        // $startdate = Config::get('constants.ELEVENSTREET_PST_START_DATE');
        // $enddate = Config::get('constants.ELEVENSTREET_PST_END_DATE');

        $startdate = Input::get('start_from')." 00:00:00";
        $enddate = Input::get('end_to')." 23:59:59";
        

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {  
        //while (($data = fgetcsv($handle, 0, $delimiter, ',')) !== FALSE) {         
            if($first_row) {
                $headers = $data;
                $first_row = false;
            } else {
                $final_ata[] = array_combine($headers, array_values($data));
            }
        }

        sort($final_ata);

        foreach (array_unique(array_column($final_ata,'Order No.')) as $key => $value) {
            
            $orderno = $value;

            
            $keyvalue = 0;
            $i = 0;
            $tcount = 0;
            $frow = false;
            unset($arraylist);
            $arraylist = array();
            $keyvalue = $key;

            $cmsresult = Transaction::get11streettransaction($orderno);
            // $tcount = count($cmsresult);
          

            $resulttrans = Transaction::Elevenstreettransaction($orderno);

            $t_marketngcost = 0;
            $t_eleventotalamount = 0; 

         
            
                # code...

                    for($iint=$key;$iint<=count($final_ata);$iint++){


                        if($orderno == $final_ata[$iint]['Order No.']){
                            $tcount = $tcount +1;
                            // echo '<pre>';
                          //  echo $final_ata[$iint]['Order No.'].'-'.$tcount.'<br>';
                            // echo '</pre>';

                                $t_transferamount         = $final_ata[$iint]['Transfer Amount'];
                                $t_settlementamount       = $final_ata[$iint]['Settlement Amount'];
                                $t_deductedamount         = $final_ata[$iint]['Deducted Amount'];
                                $t_totalsalesamount       = $final_ata[$iint]['[Settlement] Total Sales'];
                                $t_prepaidshippingamount  = $final_ata[$iint]['[Settlement]Pre-paid Shipping Fee'];
                                $t_returnshippingamount   = $final_ata[$iint]['[Settlement] Return Shipping Fee'];
                                $t_transactionfee         = $final_ata[$iint]['[Deduction] Transaction Fee'];
                                $t_paymentgatewayfee      = $final_ata[$iint]['[Deduction] Payment Gateway Fee'];
                                $t_discountcouponfee      = $final_ata[$iint]['[Deduction] Discount Coupon Usage Fee'];
                                $t_multipurchasedisc      = $final_ata[$iint]['[Deduction]Multiple Purchase Discount'];
                                $t_pointusagefee          = $final_ata[$iint]['[Deduct] Point Usage Fee'];
                                $t_claimfee               = $final_ata[$iint]['[Deduct] Claim Fee'];
                               

                                $temparray = array('t_orderno'                  => $final_ata[$iint]['Order No.'], 
                                                   't_transferamount'           => $final_ata[$iint]['Transfer Amount'],
                                                   't_settlementamount'         => $final_ata[$iint]['Settlement Amount'],
                                                   't_deductedamount'           => $final_ata[$iint]['Deducted Amount'],
                                                   't_totalsalesamount'         => $final_ata[$iint]['[Settlement] Total Sales'],
                                                   't_prepaidshippingamount'    => $final_ata[$iint]['[Settlement]Pre-paid Shipping Fee'],
                                                   't_returnshippingamount'     => $final_ata[$iint]['[Settlement] Return Shipping Fee'],
                                                   't_transactionfee'           => $final_ata[$iint]['[Deduction] Transaction Fee'],
                                                   't_paymentgatewayfee'        => $final_ata[$iint]['[Deduction] Payment Gateway Fee'],
                                                   't_discountcouponfee'        => $final_ata[$iint]['[Deduction] Discount Coupon Usage Fee'],
                                                   't_multipurchasedisc'        => $final_ata[$iint]['[Deduction]Multiple Purchase Discount'],
                                                   't_pointusagefee'            => $final_ata[$iint]['[Deduct] Point Usage Fee'],
                                                   't_claimfee'                 => $final_ata[$iint]['[Deduct] Claim Fee'],
                                                   't_productnumber'            => $final_ata[$iint]['Product Number']

                                                );
                                $t_marketngcost +=  ($t_returnshippingamount + $t_transactionfee + $t_paymentgatewayfee + $t_discountcouponfee + $t_multipurchasedisc + $t_pointusagefee + $t_claimfee)-$t_prepaidshippingamount;

                                $t_eleventotalamount += ($t_transferamount + $t_returnshippingamount + $t_transactionfee + $t_paymentgatewayfee + $t_discountcouponfee + $t_multipurchasedisc + $t_pointusagefee + $t_claimfee) - $t_prepaidshippingamount;

                                array_push($arraylist, $temparray);



                        }
                        else{
                            break;
                        }



                    }

                    if($tcount >1){
                       $frow = true; 
            }

             foreach ($resulttrans as $rowvalue) {       

                    $t_cmsgst = $rowvalue['gst'];
                    $t_cmstotalexc = $rowvalue['totalamount'];
                    $t_cmstotalinc = $t_cmsgst + $t_cmstotalexc;
                    $t_cmstotalsales = $t_cmstotalexc + $t_marketngcost;

                    $t_variance = round((round($t_cmstotalsales,2) - round($t_eleventotalamount,2)),2);

                    foreach ($arraylist as  $innervalue) {
                    if($tcount >1){
                        if($frow == true){
                            $frow = false;

                            $tempdata = array('cmstransdate' => $rowvalue['transdate'], 
                                  'cmstransid'   => $rowvalue['transID'], 
                                  'cmsinvno'     => $rowvalue['invNo'], 
                                  'cmsinvdate'   => $rowvalue['invDate'], 
                                  'cmsbuyser'    => $rowvalue['buyerUser'], 
                                  'cmsbstatus'   => $rowvalue['status'], 
                                  'estreetordno' => $orderno, 
                                  'cmsgst'       => $rowvalue['gst'], 
                                  'cmstotalexc'   => $rowvalue['totalamount'], 
                                  'cmstotalinc'   => $t_cmstotalinc, 
                                  'marketngcost'   => $t_marketngcost, 
                                  'cmstotalsales'   => $t_cmstotalsales, 
                                  'orderno'         => $innervalue['t_orderno'], 
                                  'productnumber'          => $innervalue['t_productnumber'], 
                                  'transferamount'         => $innervalue['t_transferamount'], 
                                  'prepaidshippingamount'  => $innervalue['t_prepaidshippingamount'], 
                                  'returnshippingamount'   => $innervalue['t_returnshippingamount'], 
                                  'transactionfee'         => $innervalue['t_transactionfee'], 
                                  'paymentgatewayfee'      => $innervalue['t_paymentgatewayfee'], 
                                  'discountcouponfee'      => $innervalue['t_discountcouponfee'], 
                                  'multipurchasedisc'      => $innervalue['t_multipurchasedisc'], 
                                  'pointusagefee'          => $innervalue['t_pointusagefee'], 
                                  'claimfee'               => $innervalue['t_claimfee'], 
                                  'eleventotalamount'      => $t_eleventotalamount, 
                                  'variance'               => $t_variance,
                                  'paymentstatus'          => 'Paid'
                                  
                                 );

                                 array_push($alldata, $tempdata);

                                 $res = DB::select('
                                                select count(id) as cnt 
                                                from jocom_elevenstreet_compare_logs jecl  
                                                where jecl.order_id='.$innervalue['t_orderno'].' and jecl.product_number ='.$innervalue['t_productnumber']
                                                );
                                  if(isset($res[0]->cnt) && $res[0]->cnt == 0){

                                    // echo 'First'.$innervalue['t_orderno'] .'--'.$innervalue['t_productnumber'];

                                    $Elevencomparedata = new ElevenStreetCompareLogs();
                                    $Elevencomparedata->cmstransdate = $rowvalue['transdate']; 
                                    $Elevencomparedata->cmstransid = $rowvalue['cmstransid']; 
                                    $Elevencomparedata->cmsinvno = $rowvalue['invNo']; 
                                    $Elevencomparedata->cmsinvdate = $rowvalue['invDate']; 
                                    $Elevencomparedata->cmsbuyser = $rowvalue['buyerUser']; 
                                    $Elevencomparedata->cmsbstatus = $rowvalue['status']; 
                                    $Elevencomparedata->estreetordno = $orderno; 
                                    $Elevencomparedata->cmsgst = $rowvalue['gst']; 
                                    $Elevencomparedata->cmstotalexc = $rowvalue['totalamount']; 
                                    $Elevencomparedata->cmstotalinc = $t_cmstotalinc; 
                                    $Elevencomparedata->marketngcost = $t_marketngcost; 
                                    $Elevencomparedata->cmstotalsales = $t_cmstotalsales; 

                                    $Elevencomparedata->order_id = $innervalue['t_orderno']; 
                                    $Elevencomparedata->product_number = $innervalue['t_productnumber'];
                                    $Elevencomparedata->transfer_amount = $innervalue['t_transferamount']; 
                                    $Elevencomparedata->prepaid_shippingfee = $innervalue['t_prepaidshippingamount']; 
                                    $Elevencomparedata->return_shippingfee = $innervalue['t_returnshippingamount'];
                                    $Elevencomparedata->transaction_fee = $innervalue['t_transactionfee'];
                                    $Elevencomparedata->payment_gateway_fee = $innervalue['t_paymentgatewayfee']; 
                                    $Elevencomparedata->discount_couponusage_fee = $innervalue['t_discountcouponfee'];
                                    $Elevencomparedata->multiple_purchase_discount = $innervalue['t_multipurchasedisc']; 
                                    $Elevencomparedata->point_usage_fee = $innervalue['t_pointusagefee'];
                                    $Elevencomparedata->claim_fee = $innervalue['t_claimfee'];
                                    $Elevencomparedata->total_sale = $t_eleventotalamount;
                                    $Elevencomparedata->variance = $t_variance;
                                    $Elevencomparedata->paymentstatus = 'Paid';
                                    $Elevencomparedata->eleventstreet_created_at = $rowvalue['created_at'];
                                    $Elevencomparedata->insert_by = Session::get('username');
                                    $Elevencomparedata->modify_by = Session::get('username');
                                    $Elevencomparedata->save();
                                 }


                        }
                        else{
                            $tempdata = array('cmstransdate' => $rowvalue['transdate'], 
                                  'cmstransid'   => $rowvalue['transID'], 
                                  'cmsinvno'     => $rowvalue['invNo'], 
                                  'cmsinvdate'   => $rowvalue['invDate'], 
                                  'cmsbuyser'    => $rowvalue['buyerUser'], 
                                  'cmsbstatus'   => $rowvalue['status'], 
                                  'estreetordno' => $orderno, 
                                  'cmsgst'       => $rowvalue['gst'], 
                                  'cmstotalexc'   => $rowvalue['totalamount'], 
                                  'cmstotalinc'   => '', 
                                  'marketngcost'   => '', 
                                  'cmstotalsales'   => '', 
                                  'orderno'         => $innervalue['t_orderno'], 
                                  'productnumber'          => $innervalue['t_productnumber'], 
                                  'transferamount'  => $innervalue['t_transferamount'], 
                                  'prepaidshippingamount'  => $innervalue['t_prepaidshippingamount'], 
                                  'returnshippingamount'   => $innervalue['t_returnshippingamount'], 
                                  'transactionfee'         => $innervalue['t_transactionfee'], 
                                  'paymentgatewayfee'      => $innervalue['t_paymentgatewayfee'], 
                                  'discountcouponfee'      => $innervalue['t_discountcouponfee'], 
                                  'multipurchasedisc'      => $innervalue['t_multipurchasedisc'], 
                                  'pointusagefee'          => $innervalue['t_pointusagefee'], 
                                  'claimfee'               => $innervalue['t_claimfee'], 
                                  'eleventotalamount'      => '', 
                                  'variance'               => '',
                                  'paymentstatus'          => ''

                                 );

                                 array_push($alldata, $tempdata);
                                 //Start Inserting Compare data 
                                 $res = DB::select('
                                                select count(id) as cnt 
                                                from jocom_elevenstreet_compare_logs jecl  
                                                where jecl.order_id='.$innervalue['t_orderno'].' and jecl.product_number ='.$innervalue['t_productnumber']
                                                );
                                // print_r($res);
                                // echo $res[0]->cnt;
                                 if(isset($res[0]->cnt) && $res[0]->cnt == 0){
                                     // echo 'Third'.$innervalue['t_orderno'] .'--'.$innervalue['t_productnumber'];
                                    $Elevencomparedata = new ElevenStreetCompareLogs();
                                    $Elevencomparedata->cmstransdate = $rowvalue['transdate']; 
                                    $Elevencomparedata->cmstransid = $rowvalue['transID']; 
                                    $Elevencomparedata->cmsinvno = $rowvalue['invNo']; 
                                    $Elevencomparedata->cmsinvdate = $rowvalue['invDate']; 
                                    $Elevencomparedata->cmsbuyser = $rowvalue['buyerUser']; 
                                    $Elevencomparedata->cmsbstatus = $rowvalue['status']; 
                                    $Elevencomparedata->estreetordno = $orderno; 
                                    $Elevencomparedata->cmsgst = $rowvalue['gst']; 
                                    $Elevencomparedata->cmstotalexc = $rowvalue['totalamount']; 
                                    $Elevencomparedata->cmstotalinc = ''; 
                                    $Elevencomparedata->marketngcost = ''; 
                                    $Elevencomparedata->cmstotalsales = ''; 

                                    $Elevencomparedata->order_id = $innervalue['t_orderno']; 
                                    $Elevencomparedata->product_number = $innervalue['t_productnumber'];
                                    $Elevencomparedata->transfer_amount = $innervalue['t_transferamount']; 
                                    $Elevencomparedata->prepaid_shippingfee = $innervalue['t_prepaidshippingamount']; 
                                    $Elevencomparedata->return_shippingfee = $innervalue['t_returnshippingamount'];
                                    $Elevencomparedata->transaction_fee = $innervalue['t_transactionfee'];
                                    $Elevencomparedata->payment_gateway_fee = $innervalue['t_paymentgatewayfee']; 
                                    $Elevencomparedata->discount_couponusage_fee = $innervalue['t_discountcouponfee'];
                                    $Elevencomparedata->multiple_purchase_discount = $innervalue['t_multipurchasedisc']; 
                                    $Elevencomparedata->point_usage_fee = $innervalue['t_pointusagefee'];
                                    $Elevencomparedata->claim_fee = '';
                                    $Elevencomparedata->total_sale = '';
                                    $Elevencomparedata->variance = '';
                                    $Elevencomparedata->paymentstatus = '';
                                    $Elevencomparedata->eleventstreet_created_at = $rowvalue['created_at'];
                                    $Elevencomparedata->insert_by = Session::get('username');
                                    $Elevencomparedata->modify_by = Session::get('username');
                                    $Elevencomparedata->save();
                                 }   

                                 //End Inserting Compare data 
                        }
                    }
                    else{

                        $tempdata = array('cmstransdate' => $rowvalue['transdate'], 
                                  'cmstransid'   => $rowvalue['transID'], 
                                  'cmsinvno'     => $rowvalue['invNo'], 
                                  'cmsinvdate'   => $rowvalue['invDate'], 
                                  'cmsbuyser'    => $rowvalue['buyerUser'], 
                                  'cmsbstatus'   => $rowvalue['status'], 
                                  'estreetordno' => $orderno, 
                                  'cmsgst'       => $rowvalue['gst'], 
                                  'cmstotalexc'   => $rowvalue['totalamount'], 
                                  'cmstotalinc'   => $t_cmstotalinc, 
                                  'marketngcost'   => $t_marketngcost, 
                                  'cmstotalsales'   => $t_cmstotalsales, 
                                  'orderno'         => $innervalue['t_orderno'], 
                                  'productnumber'          => $innervalue['t_productnumber'], 
                                  'transferamount'  => $innervalue['t_transferamount'], 
                                  'prepaidshippingamount'  => $innervalue['t_prepaidshippingamount'], 
                                  'returnshippingamount'   => $innervalue['t_returnshippingamount'], 
                                  'transactionfee'         => $innervalue['t_transactionfee'], 
                                  'paymentgatewayfee'      => $innervalue['t_paymentgatewayfee'], 
                                  'discountcouponfee'      => $innervalue['t_discountcouponfee'], 
                                  'multipurchasedisc'      => $innervalue['t_multipurchasedisc'], 
                                  'pointusagefee'          => $innervalue['t_pointusagefee'], 
                                  'claimfee'               => $innervalue['t_claimfee'], 
                                  'eleventotalamount'      => $t_eleventotalamount, 
                                  'variance'               => $t_variance,
                                  'paymentstatus'          => 'Paid'

                                 );

                        array_push($alldata, $tempdata);

                                $res = DB::select('
                                                select count(id) as cnt 
                                                from jocom_elevenstreet_compare_logs jecl  
                                                where jecl.order_id='.$innervalue['t_orderno'].' and jecl.product_number ='.$innervalue['t_productnumber']
                                                );
                                // print_r($rowvalue);
                                // echo $res[0]->cnt;
                                 if(isset($res[0]->cnt) && $res[0]->cnt == 0){
                                    //   echo 'Second'.$rowvalue['transdate'] .'--'.$rowvalue['invDate'];
                                    $Elevencomparedata = new ElevenStreetCompareLogs();
                                    $Elevencomparedata->cmstransdate = $rowvalue['transdate']; 
                                    $Elevencomparedata->cmstransid = $rowvalue['transID']; 
                                    $Elevencomparedata->cmsinvno = $rowvalue['invNo']; 
                                    $Elevencomparedata->cmsinvdate = $rowvalue['invDate']; 
                                    $Elevencomparedata->cmsbuyser = $rowvalue['buyerUser']; 
                                    $Elevencomparedata->cmsbstatus = $rowvalue['status']; 
                                    $Elevencomparedata->estreetordno = $orderno; 
                                    $Elevencomparedata->cmsgst = $rowvalue['gst']; 
                                    $Elevencomparedata->cmstotalexc = $rowvalue['totalamount']; 
                                    $Elevencomparedata->cmstotalinc = $t_cmstotalinc; 
                                    $Elevencomparedata->marketngcost = $t_marketngcost; 
                                    $Elevencomparedata->cmstotalsales = $t_cmstotalsales; 

                                    $Elevencomparedata->order_id = $innervalue['t_orderno']; 
                                    $Elevencomparedata->product_number = $innervalue['t_productnumber'];
                                    $Elevencomparedata->transfer_amount = $innervalue['t_transferamount']; 
                                    $Elevencomparedata->prepaid_shippingfee = $innervalue['t_prepaidshippingamount']; 
                                    $Elevencomparedata->return_shippingfee = $innervalue['t_returnshippingamount'];
                                    $Elevencomparedata->transaction_fee = $innervalue['t_transactionfee'];
                                    $Elevencomparedata->payment_gateway_fee = $innervalue['t_paymentgatewayfee']; 
                                    $Elevencomparedata->discount_couponusage_fee = $innervalue['t_discountcouponfee'];
                                    $Elevencomparedata->multiple_purchase_discount = $innervalue['t_multipurchasedisc']; 
                                    $Elevencomparedata->point_usage_fee = $innervalue['t_pointusagefee'];
                                    $Elevencomparedata->claim_fee = $innervalue['t_claimfee'];
                                    $Elevencomparedata->total_sale = $t_eleventotalamount;
                                    $Elevencomparedata->variance = $t_variance;
                                    $Elevencomparedata->paymentstatus = 'Paid';
                                    $Elevencomparedata->eleventstreet_created_at = $rowvalue['created_at'];
                                    $Elevencomparedata->insert_by = Session::get('username');
                                    $Elevencomparedata->modify_by = Session::get('username');
                                    $Elevencomparedata->save();
                                 }

                    }
                }

            }        


            
                 

           

            $tcount = 0;  
            


            // die();
        }


        
        $resultunpaid = DB::table('jocom_elevenstreet_order')
                    // ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    ->select('order_number')
                    ->where('api_payment_status','=',0)
                    // ->where('JP.status','=','completed')
                    ->where('created_at','>=',$startdate)
                    ->where('created_at','<=',$enddate)
                    ->where('activation','=',1)
                    ->groupBy('order_number')
                    ->get();
                    
    // To be enable this block
   
        foreach ($resultunpaid as  $unpaidvalue) {
           
            $un_orderno = $unpaidvalue->order_number;

            $resultstrans = Transaction::Elevenstreettransaction($un_orderno);

            if(count($resultstrans)>0){
                 foreach ($resultstrans as $rwvalue){


                    $tt_cmsgst = $rwvalue['gst'];
                    $tt_cmstotalexc = $rwvalue['totalamount'];
                    $tt_cmstotalinc = $tt_cmsgst + $tt_cmstotalexc;

                    $tempdata_1 = array('cmstransdate' => $rwvalue['transdate'], 
                                  'cmstransid'   => $rwvalue['transID'], 
                                  'cmsinvno'     => $rwvalue['invNo'], 
                                  'cmsinvdate'   => $rwvalue['invDate'], 
                                  'cmsbuyser'    => $rwvalue['buyerUser'], 
                                  'cmsbstatus'   => $rwvalue['status'], 
                                  'estreetordno' => $un_orderno, 
                                  'cmsgst'       => $rwvalue['gst'], 
                                  'cmstotalexc'   => $rwvalue['totalamount'], 
                                  'cmstotalinc'   => $tt_cmstotalinc, 
                                  'marketngcost'   => $t_marketngcost, 
                                  'cmstotalsales'   => $tt_cmstotalinc, 
                                  'orderno'         => '', 
                                  'productnumber'         => '', 
                                  'transferamount'  => '', 
                                  'prepaidshippingamount'  => '', 
                                  'returnshippingamount'   => '', 
                                  'transactionfee'         => '', 
                                  'paymentgatewayfee'      => '', 
                                  'discountcouponfee'      => '', 
                                  'multipurchasedisc'      => '', 
                                  'pointusagefee'          => '', 
                                  'claimfee'               => '', 
                                  'eleventotalamount'      => '', 
                                  'variance'               => '',
                                  'paymentstatus'          => 'Un Paid' 

                                 );

                        array_push($alldata, $tempdata_1);

                 }   

            }


        }

    

        $tdata = array(
                        'totdata'      => $alldata,
                             
                    );
       
        // echo '<pre>';
        // print_r($tdata);
        // echo '</pre>';
        // die();

        $date = date('Y-m-d H:i:s');
        $repname = "Prestomallsalereconciliaton";

        } catch (Exception $ex) {
            $isError = 1;
            // DB::rollBack();
            $errorMessage = $ex->getMessage();
            echo $errorMessage;
        } finally {
            // return $data;
            if($isError == 0){
                // DB::commit();
               
            }
            // return $filename;
            return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($tdata) {
                    $excel->sheet('Prestomall', function($sheet) use ($tdata)
                    {   
                        $sheet->loadView('report.template11street', array('data' =>$tdata));
                        
                    });
                })->download('xls');

           

        }
        

    }
    
    public function anyDownload(){

            $isError = 0;
            $alldata = array();
            $un_orderno = "";
            
            $startdate = Input::get('start_from')." 00:00:00";
            $enddate = Input::get('end_to')." 23:59:59";

            // $startdate = Config::get('constants.ELEVENSTREET_PST_START_DATE'); 
            // $enddate = Config::get('constants.ELEVENSTREET_PST_END_DATE');
            // echo 'Start-'.$startdate.'-End-'.$enddate;

            try{

                // Start Paid list 

                $resultpaid = DB::table('jocom_elevenstreet_compare_logs')
                    // ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    ->where('cmsinvdate','>=',$startdate)
                    ->where('cmsinvdate','<=',$enddate)
                    
                    // ->where('eleventstreet_created_at','>=',$startdate)
                    // ->where('eleventstreet_created_at','<=',$enddate)
                    ->groupBy('order_id')
                     ->orderBy('cmsinvdate','ASC')
                    ->get();

                // print_r($resultpaid);

                foreach ($resultpaid as  $paidvalue) {

                    $tempdata_01 = array('cmstransdate' => $paidvalue->cmstransdate, 
                                      'cmstransid'   => $paidvalue->cmstransid, 
                                      'cmsinvno'     => $paidvalue->cmsinvno, 
                                      'cmsinvdate'   => $paidvalue->cmsinvdate, 
                                      'cmsbuyser'    => $paidvalue->cmsbuyser, 
                                      'cmsbstatus'   => $paidvalue->cmsbstatus, 
                                      'estreetordno' => $paidvalue->estreetordno, 
                                      'cmsgst'       => $paidvalue->cmsgst, 
                                      'cmstotalexc'   => $paidvalue->cmstotalexc, 
                                      'cmstotalinc'   => $paidvalue->cmstotalinc,  
                                      'marketngcost'   => $paidvalue->marketngcost, 
                                      'cmstotalsales'   => $paidvalue->cmstotalsales, 
                                      'orderno'         => $paidvalue->order_id, 
                                      'productnumber'   => $paidvalue->product_number, 
                                      'transferamount'  =>$paidvalue->transfer_amount, 
                                      'prepaidshippingamount'  => $paidvalue->prepaid_shippingfee, 
                                      'returnshippingamount'   => $paidvalue->return_shippingfee, 
                                      'transactionfee'         => $paidvalue->transaction_fee, 
                                      'paymentgatewayfee'      => $paidvalue->payment_gateway_fee, 
                                      'discountcouponfee'      => $paidvalue->discount_couponusage_fee, 
                                      'multipurchasedisc'      => $paidvalue->multiple_purchase_discount, 
                                      'pointusagefee'          => $paidvalue->point_usage_fee, 
                                      'claimfee'               => $paidvalue->claim_fee, 
                                      'eleventotalamount'      => $paidvalue->total_sale, 
                                      'variance'               => $paidvalue->variance, 
                                      'paymentstatus'          => $paidvalue->paymentstatus,  

                                     );

                            array_push($alldata, $tempdata_01);


                }


                // print_r($alldata);



                // End Paid List 






                    // $resultunpaid = DB::table('jocom_elevenstreet_order')
                    // // ->leftjoin('jocom_transaction as JP','JP.id','=','EO.transaction_id')
                    // ->select('order_number')
                    // ->where('api_payment_status','=',0)
                    // // ->where('JP.status','=','completed')
                    // ->where('created_at','>=',$startdate)
                    // ->where('created_at','<=',$enddate)
                    // ->where('activation','=',1)
                    // ->groupBy('order_number')
                    // ->orderBy('api_payment_status','DESC')
                    // ->get();
                    $resultunpaid = DB::table('jocom_elevenstreet_order as JP')
                     ->leftjoin('jocom_transaction as JT','JT.id','=','JP.transaction_id')
                    ->select('JP.order_number','JT.id')
                    ->where('JP.api_payment_status','=',0)
                    // ->where('JP.status','=','completed')
                    ->where('JT.transaction_date','>=',$startdate)
                    ->where('JT.transaction_date','<=',$enddate)
                    ->where('JP.activation','=',1)
                    // ->groupBy('JP.order_number')
                    ->orderBy('JP.api_payment_status','DESC')
                    ->get();

            foreach ($resultunpaid as  $unpaidvalue) {
               
                $un_orderno = $unpaidvalue->order_number;
                
                $un_TransID = $unpaidvalue->id;

               // $resultstrans = Transaction::Elevenstreettransaction($un_orderno);
               
                $resultstrans = Transaction::ElevenstreettransactionTransID($un_TransID);

                if(count($resultstrans)>0){
                     foreach ($resultstrans as $rwvalue){


                        $tt_cmsgst = $rwvalue['gst'];
                        $tt_cmstotalexc = $rwvalue['totalamount'];
                        $tt_cmstotalinc = $tt_cmsgst + $tt_cmstotalexc;

                        $tempdata_1 = array('cmstransdate' => $rwvalue['transdate'], 
                                      'cmstransid'   => $rwvalue['transID'], 
                                      'cmsinvno'     => $rwvalue['invNo'], 
                                      'cmsinvdate'   => $rwvalue['invDate'], 
                                      'cmsbuyser'    => $rwvalue['buyerUser'], 
                                      'cmsbstatus'   => $rwvalue['status'], 
                                      'estreetordno' => $un_orderno, 
                                      'cmsgst'       => $rwvalue['gst'], 
                                      'cmstotalexc'   => $rwvalue['totalamount'], 
                                      'cmstotalinc'   => $tt_cmstotalinc, 
                                      'marketngcost'   => $t_marketngcost, 
                                      'cmstotalsales'   => $tt_cmstotalinc, 
                                      'orderno'         => '', 
                                      'productnumber'   => '', 
                                      'transferamount'  => '', 
                                      'prepaidshippingamount'  => '', 
                                      'returnshippingamount'   => '', 
                                      'transactionfee'         => '', 
                                      'paymentgatewayfee'      => '', 
                                      'discountcouponfee'      => '', 
                                      'multipurchasedisc'      => '', 
                                      'pointusagefee'          => '', 
                                      'claimfee'               => '', 
                                      'eleventotalamount'      => '', 
                                      'variance'               => '',
                                      'paymentstatus'          => 'Un Paid' 

                                     );

                            array_push($alldata, $tempdata_1);

                     }   

                }


            }



            $tdata = array(
                            'totdata'      => $alldata,
                                 
                        );

            $date = date('Y-m-d H:i:s');
            $repname = "Prestomallsalereconciliation";

            } catch (Exception $ex) {
                $isError = 1;
                DB::rollBack();
                $errorMessage = $ex->getMessage();
            } finally {
                // return $data;
                if($isError == 0){
                    DB::commit();
                   
                }

                return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($tdata) {
                        $excel->sheet('Prestomall', function($sheet) use ($tdata)
                        {   
                            $sheet->loadView('report.template11street', array('data' =>$tdata));
                            
                        });
                    })->download('xls');

               

            }

     }
    
    public function anyToppending(){
        
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

         return View::make('report.toppending')
                     ->with('sellersOptions', $sellersOptions);
        
    }


    public function anyToptransactionsproducts()
    {

        $limit=0;
        $seller = '';
        $product ='';

        $sql = '';


        $limit   = Input::has('limit_display') ? Input::get('limit_display') : '0';
        $seller        = Input::has('seller') ? Input::get('seller') : '';
        $product    = Input::has('product_name') ? Input::get('product_name') : '';

        if($seller != 'all' && $seller != ''){

            $sql = ' JP.sell_id='. $seller;

        }

        if($product != ''){

            $sql = $sql + ' JP.name like %'.$product.'%';
        }

        if($sql != '') {

            $sql = ' AND '. $sql;
        }


        
            $data = DB::select(DB::raw("SELECT LTI.name, LTI.sku, SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                LEFT JOIN jocom_products AS JP ON JP.sku = LTI.sku
                                WHERE LT.status IN (0) ".$sql." GROUP BY LTI.name ORDER BY total_qty DESC LIMIT ".$limit));

       
        

        
        
        // return Response::json(['data' => array_merge($data)]);
        
        // Export as Excell
        Excel::create('Top-Pending-Products-J-'.Config::get('constants.ENVIRONMENT').'-'.date('d-m-Y'), function($excel) use($data) {
            $excel->sheet('Top Products', function($sheet) use($data) {
                $sheetArray = array();
                $sheetArray[] = array('Product Name', 'SKU', 'Total Quantity');
                //$sheetArray[] = array(); // Add an empty row
                //$sheetArray[] = array(); // Add an empty row
                foreach($data as $row){
                    if($row->name != ''){
                        $sheetArray[] = array($row->name, $row->sku, $row->total_qty);
                    }
                }
                $sheet->fromArray($sheetArray, null, 'A1', false, false);
                // $sheet->setStyle(array(
                //   'font' => array(
                //   'name'      =>  'Calibri',
                //   'size'      =>  12,
                //   'bold'      =>  true
                //   )
                // ));
            });
        })->export('xls');
    }
    
    public function anyDailytransaction()
    {
        Input::flash();
        set_time_limit(0);

        if (Input::get('generate') == true) {
            if (Input::has('email')) {
                $email = Input::get('email');
                Input::flush();
            } else {
                $errors['email'] = 'Email is required!';
                $exit            = true;
            }

            if ($exit != true) {
                
                $r_countryid  = Input::has('region_country_id') ? Input::get('region_country_id') : '0';
                $region_id    = Input::has('region_id') ? Input::get('region_id') : '0';

                $date_from = Input::has('date_from') ? date_format(date_create(Input::get('date_from')), 'Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime('yesterday 12pm'));
                $date_to = Input::has('date_to') ? date_format(date_create(Input::get('date_to')), 'Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime('today 12pm'));

                $data['r_countryid'] = $r_countryid;
                $data['region_id'] = $region_id;
                $data['email'] = $email;
                $data['date_from'] = $date_from;
                $data['date_to']  = $date_to;

                $path = Config::get('constants.REPORT_PATH');
                $date = date('Ymd_his');

                $fileName = 'report_general_dailytransaction_'.$date;

                $job               = [];
                $job['ref_id']     = '0';
                $job['job_name']   = "general_report_dailytransaction";
                $job['in_file']    = $fileName;
                $job['remark']     = json_encode(array_merge($data));
                $job['request_by'] = Session::get('user_id');
                $job['request_at'] = date('Y-m-d H:i:s');

                $products = GeneralReport::get_dailytransaction($data);
                $queries  = DB::getQueryLog();

                $last_query         = end($queries);
                $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
                $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
                $query['count']     = count($products);
                $query['filename']  = $job['in_file'];
                $query['email']     = $email;

                if (count($products) > 0) {
                    $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);
                    $toview    = "success";
                    $toviewmsg = "A report is in queue!";
                } else {
                    $toview    = "message";
                    $toviewmsg = "No report will be generated!";
                }
            }

        }

        $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', '=', 'general_report_dailytransaction')->where('request_by', '=', Session::get('user_id'))->orderBy('id', 'desc')->get();
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id",458)->get();
        
        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
        }
        
        return View::make('report.daily_transaction')
            ->with('row', $job)
            ->with('query', $query)
            ->withErrors($errors)
            ->with($toview, $toviewmsg);

    }
    
    public function anyTransactionsgmv()
    {
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id",458)->get();
        
        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
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

         return View::make('report.transactiongmv')
                    ->with('countries', $countries)
                    ->with('regions', $regions)
                    ->with('sellersOptions', $sellersOptions);

    }

    public function anyGenerategmv()
    {

        $limit=0;
        $seller = '';
        $product ='';

        $completed = Input::has('completed') ? 'completed' : '';
        $cancelled = Input::has('cancelled') ? 'cancelled' : '';
        $refund    = Input::has('refund') ? 'refund' : '';
        $pending   = Input::has('pending') ? 'pending' : '';

        $status = '';
        $status = array_diff([$completed, $cancelled, $refund, $pending], ['']);

        if (empty($status)) {
            $status = ["completed"];
        }

        $r_countryid  = Input::has('region_country_id') ? Input::get('region_country_id') : '0';
        $region_id    = Input::has('region_id') ? Input::get('region_id') : '0';

        $start_from = Input::has('start_from') ? Input::get('start_from') : date('Y-m-d', strtotime("yesterday"));
        $end_to   = Input::has('end_to') ? Input::get('end_to') : date('Y-m-d');


        $customer   = Input::has('customer') ? Input::get('customer') : '';
        $seller        = Input::has('seller') ? Input::get('seller') : '';
        $product    = Input::has('product_sku') ? Input::get('product_sku') : '';
        $created      = Input::has('created') ? Input::get('created') : '0';

        $stateName = array();

        if($region_id == 0){
            $State = State::getStateByCountry($r_countryid);
        }
        else{
            $State = State::getStateByRegion($region_id);
        }
        
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }

        // die();


        $transactions =  DB::table('jocom_transaction AS JT')
                                ->select('JT.id',
                                         DB::raw('DATE_FORMAT(JT.transaction_date, "%Y-%m-%d") AS Date'),
                                         'JT.total_amount as TotalAmount', 
                                         DB::raw('SUM(JTD.actual_total_amount) AS TotalGMV')     
                                   )
                                ->leftjoin('jocom_transaction_details as JTD','JTD.transaction_id','=','JT.id');


                    if(count($stateName) > 0){
                        $transactions = $transactions->whereIn('JT.delivery_state', $stateName);
                    }
                    $transactions = $transactions->whereIn('JT.status', $status);
                    if(isset($customer) && $customer != ''){
                        $transactions = $transactions->whereIn('JT.buyer_id', $customer);
                    }

                    if(isset($seller) && $seller != 'all'){
                        $transactions = $transactions->whereIn('JTD.seller_username', $seller);
                    }

                    if(isset($created) && $created != ''){
                        switch ($created) {
                            case '1':
                                $transactions = $transactions->whereBetween('JT.transaction_date', [$start_from.' 00:00:00', $end_to.' 23:59:59']);
                                break;
                            case '2':
                                $transactions = $transactions->whereBetween('JT.insert_date', [$start_from.' 00:00:00', $end_to.' 23:59:59']);
                                break;
                            case '3':
                                $transactions = $transactions->whereBetween('JT.invoice_date', [$start_from, $end_to]);
                                break;
                        }
                    }

                $transactions = $transactions->groupBy('JT.id'); 
                $transactions = $transactions->orderBy('JT.transaction_date','DESC'); 
                $transactions = $transactions->get();


        
        $repname = 'Transactiongmv';
        
        // return Response::json(['data' => array_merge($data)]);
        
        // Export as Excell
        // Excel::create('Transaction-GMV-'.Config::get('constants.ENVIRONMENT').'-'.date('d-m-Y'), function($excel) use($transactions) {
        //     $excel->sheet('Top Products', function($sheet) use($transactions) {
        //         $sheetArray = array();
        //         $sheetArray[] = array('Transaction ID', 'Transaction Date', 'Total Sale','Total GMV');
                
        //         foreach($transactions as $row){
        //             $sheetArray[] = array($row->id, $row->Date, $row->TotalAmount, $row->TotalGMV);
        //         }
        //         $sheet->fromArray($sheetArray, null, 'A1', false, false);
        //     });
        // })->export('xls');

        return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($transactions) {
                        $excel->sheet('TransGMV', function($sheet) use ($transactions)
                        {   
                            $sheet->loadView('report.templatetransgmv', array('data' =>$transactions));
                            
                        });
                    })->download('xls');


    }
    
    public function anyTopselling()
    {
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id",458)->get();
        
        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
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

         return View::make('report.topsellingproducts')
                    ->with('countries', $countries)
                    ->with('regions', $regions)
                    ->with('sellersOptions', $sellersOptions);

    }

    public function anyGeneratetopselling()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $limit=0;
        $seller = '';
        $product ='';

        $completed = Input::has('completed') ? 'completed' : '';
        $cancelled = Input::has('cancelled') ? 'cancelled' : '';
        $refund    = Input::has('refund') ? 'refund' : '';
        $pending   = Input::has('pending') ? 'pending' : '';

        $status = '';
        $status = array_diff([$completed, $cancelled, $refund, $pending], ['']);

        if (empty($status)) {
            $status = ["completed"];
        }

        $r_countryid  = Input::has('region_country_id') ? Input::get('region_country_id') : '0';
        $region_id    = Input::has('region_id') ? Input::get('region_id') : '0';

        $start_from = Input::has('start_from') ? Input::get('start_from') : date('Y-m-d', strtotime("yesterday"));
        $end_to   = Input::has('end_to') ? Input::get('end_to') : date('Y-m-d');


        $seller        = Input::has('seller') ? Input::get('seller') : '';
        $product    = Input::has('product_sku') ? Input::get('product_sku') : '';
        $created      = Input::has('created') ? Input::get('created') : '0';

        $stateName = array();

        if($region_id == 0){
            $State = State::getStateByCountry($r_countryid);
        }
        else{
            $State = State::getStateByRegion($region_id);
        }
        
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }

        // die();


        $transactions =  DB::table('jocom_transaction_details AS JTD')
                                ->select('JTD.sku',
                                         'JTD.product_id',
                                         'JTD.product_name',
                                         'JTD.price_label',
                                         'JTD.price',

                                         DB::raw('SUM(JTD.unit) AS Quantity')     
                                   )
                                ->leftjoin('jocom_transaction as JT','JTD.transaction_id','=','JT.id');


                    if(count($stateName) > 0){
                        $transactions = $transactions->whereIn('JT.delivery_state', $stateName);
                    }
                    $transactions = $transactions->whereIn('JT.status', $status);
                    

                    if(isset($seller) && $seller != 'all'){
                        $transactions = $transactions->whereIn('JTD.seller_username', $seller);
                    }

                    if(isset($created) && $created != ''){
                        switch ($created) {
                            case '1':
                                $transactions = $transactions->whereBetween('JT.transaction_date', [$start_from.' 00:00:00', $end_to.' 23:59:59']);
                                break;
                            case '2':
                                $transactions = $transactions->whereBetween('JT.insert_date', [$start_from.' 00:00:00', $end_to.' 23:59:59']);
                                break;
                            case '3':
                                $transactions = $transactions->whereBetween('JT.invoice_date', [$start_from, $end_to]);
                                break;
                        }
                    }

                $transactions = $transactions->groupBy('JTD.product_id'); 
                $transactions = $transactions->orderBy('Quantity','DESC'); 
                $transactions = $transactions->get();

        
        $repname = 'Topsellingproducts';
        
    
        return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($transactions) {
                        $excel->sheet('TopSelling', function($sheet) use ($transactions)
                        {   
                            $sheet->loadView('report.templatetopselling', array('data' =>$transactions));
                            
                        });
                    })->download('xls');


    }
    
    public function anyConsignmentreport(){
        $method = Request::method();
        if (Request::isMethod('post')) {

            // DO report Generate Job
            $path = Config::get('constants.REPORT_PATH');
            $date = date('Ymd_his');

            $fileName = 'report_consignment_' . $date;

            $job               = [];
            $job['ref_id']     = '0';
            $job['job_name']   = "report_consignment";
            $job['in_file']    = $fileName;
            $job['remark']     = json_encode(array_merge(Input::all()));
            $job['request_by'] = Session::get('user_id');
            $job['request_at'] = date('Y-m-d H:i:s');

            $products = GeneralReport::Consignment(array_merge(Input::all(), ['platform' => self::$platform], ['po_type' => self::$po_type]));
            $queries  = DB::getQueryLog();


            $last_query         = end($queries);
            $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
            $query = [];
            $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
            $query['count']     = count($products);
            $query['filename']  = $job['in_file'];
            $query['email']     = $email;

            if (count($products) > 0) {
                $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);
                $toview    = "success";
                $toviewmsg = "A report is in queue!";
            } else {
                $toview    = "message";
                $toviewmsg = "No report will be generated!";
            }
        }else if (Request::isMethod('put')){
            // <input type="hidden" name="_method" value="PUT">
        }

        $job = DB::table('jocom_job_queue')->select('*')->whereIn('status', [0, 1])->where('job_name', '=', 'report_consignment')->where('request_by', '=', Session::get('user_id'))->orderBy('id', 'desc')->get();

        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
            $countries = Country::where("id", 458)->get();
        }else{
            $regions = Region::where("country_id",$country_id)
                ->where("activation",1)->get();
            $countries = Country::getActiveCountry();
        }


        return View::make('report.consignment')
            ->with('row', ($job ? $job : null))
            ->with('query', ($query ? $query : null))
            ->with('countries', $countries)
            ->with('regions', $regions)
            ->withErrors($errors)
            ->with($toview, $toviewmsg);
    }
    
    public function anyEdagang(){
        $plateform = [
            "prestomall" => 'Prestomall',
            "11Street" => '11Street',
            "lazada" => 'Lazada',
            "Qoo10" => 'Qoo10',
            "shopee" => 'Shopee',
            "astrogo" => 'Astro Go Shop',
            "vettons" => 'Vettons',
            "jocom" => 'jocom',
        ];
        $filepath = base_path() . "/media/EdagangSellerReportFormat.txt";

        if(Request::isMethod('post')){
            if(Input::get('apply') === 'true'){
                if(Input::file('format') && Input::file('format')->getClientOriginalExtension() === 'txt'){
                    // do update and store the format for next usage at specified location
                    $file = Input::file('format');
                    $file->move(base_path() . '/media/', 'EdagangSellerReportFormat.txt');
                    return Redirect::to('report/edagang')->with('success', 'Format Has been Apply');
                }
                return Redirect::to('report/edagang')->with('message', 'Seller Name Format only support txt file');
            }else if(Input::get('clear') === 'true'){
                if(file_exists($filepath)) unlink($filepath);
                return Redirect::to('report/edagang')->with('success', 'Format Has been Remove');
            }else if(Input::get('download') === 'true'){
                return (file_exists($filepath) ? Response::download($filepath, 'format.txt', ['Content-Type' => mime_content_type($filepath)]) : Redirect::to('report/edagang')->with('message', 'Format not found!'));
            }

            // Check seller name format
            $format = [];
            if(file_exists($filepath)){
                $input = fopen($filepath, "r");

                while(!feof($input)) {
                    $g = fgets($input);
                    $g = ($g ? rtrim(preg_replace('/([\t\n\r])/i', ' ', $g)) : false);
                    if($g !== 'product_id seller_id seller_name'){
                        $n = explode(' ', $g, 3);
                        if(count($n) < 3) continue;
                        $a = (!$n[0] ? 'seller' : $n[0]);
                        if(!isset($format[$n[$a]])) $format[$a] = [];
                        if(!isset($format[$n[$a]][$n[1]])) $format[$a][$n[1]] = $n[2];
                    }
                }
                unset($g, $n, $a);
            }

            $c_code = [
                'ABW' => 'Aruba', 'AFG' => 'Afghanistan', 'AGO' => 'Angola', 'AIA' => 'Anguilla', 'ALA' => 'Åland Islands', 'ALB' => 'Albania', 'AND' => 'Andorra', 'ARE' => 'United Arab Emirates', 'ARG' => 'Argentina', 'ARM' => 'Armenia', 'ASM' => 'American Samoa', 'ATA' => 'Antarctica', 'ATF' => 'French Southern Territories', 'ATG' => 'Antigua and Barbuda', 'AUS' => 'Australia', 'AUT' => 'Austria', 'AZE' => 'Azerbaijan', 'BDI' => 'Burundi', 'BEL' => 'Belgium', 'BEN' => 'Benin', 'BES' => 'Bonaire, Sint Eustatius and Saba', 'BFA' => 'Burkina Faso', 'BGD' => 'Bangladesh', 'BGR' => 'Bulgaria', 'BHR' => 'Bahrain', 'BHS' => 'Bahamas', 'BIH' => 'Bosnia and Herzegovina', 'BLM' => 'Saint Barthélemy', 'BLR' => 'Belarus', 'BLZ' => 'Belize', 'BMU' => 'Bermuda', 'BOL' => 'Bolivia (Plurinational State of)', 'BRA' => 'Brazil', 'BRB' => 'Barbados', 'BRN' => 'Brunei Darussalam', 'BTN' => 'Bhutan', 'BVT' => 'Bouvet Island', 'BWA' => 'Botswana', 'CAF' => 'Central African Republic', 'CAN' => 'Canada', 'CCK' => 'Cocos (Keeling) Islands', 'CHE' => 'Switzerland', 'CHL' => 'Chile', 'CHN' => 'China', 'CIV' => 'Côte d\'Ivoire', 'CMR' => 'Cameroon', 'COD' => 'Congo, Democratic Republic of the', 'COG' => 'Congo', 'COK' => 'Cook Islands', 'COL' => 'Colombia', 'COM' => 'Comoros', 'CPV' => 'Cabo Verde', 'CRI' => 'Costa Rica', 'CUB' => 'Cuba', 'CUW' => 'Curaçao', 'CXR' => 'Christmas Island', 'CYM' => 'Cayman Islands', 'CYP' => 'Cyprus', 'CZE' => 'Czechia', 'DEU' => 'Germany', 'DJI' => 'Djibouti', 'DMA' => 'Dominica', 'DNK' => 'Denmark', 'DOM' => 'Dominican Republic', 'DZA' => 'Algeria', 'ECU' => 'Ecuador', 'EGY' => 'Egypt', 'ERI' => 'Eritrea', 'ESH' => 'Western Sahara', 'ESP' => 'Spain', 'EST' => 'Estonia', 'ETH' => 'Ethiopia', 'FIN' => 'Finland', 'FJI' => 'Fiji', 'FLK' => 'Falkland Islands (Malvinas)', 'FRA' => 'France', 'FRO' => 'Faroe Islands', 'FSM' => 'Micronesia (Federated States of)', 'GAB' => 'Gabon', 'GBR' => 'United Kingdom of Great Britain and Northern Ireland', 'GEO' => 'Georgia', 'GGY' => 'Guernsey', 'GHA' => 'Ghana', 'GIB' => 'Gibraltar', 'GIN' => 'Guinea', 'GLP' => 'Guadeloupe', 'GMB' => 'Gambia', 'GNB' => 'Guinea-Bissau', 'GNQ' => 'Equatorial Guinea', 'GRC' => 'Greece', 'GRD' => 'Grenada', 'GRL' => 'Greenland', 'GTM' => 'Guatemala', 'GUF' => 'French Guiana', 'GUM' => 'Guam', 'GUY' => 'Guyana', 'HKG' => 'Hong Kong', 'HMD' => 'Heard Island and McDonald Islands', 'HND' => 'Honduras', 
                'HRV' => 'Croatia', 'HTI' => 'Haiti', 'HUN' => 'Hungary', 'IDN' => 'Indonesia', 'IMN' => 'Isle of Man', 'IND' => 'India', 'IOT' => 'British Indian Ocean Territory', 'IRL' => 'Ireland', 'IRN' => 'Iran (Islamic Republic of)', 'IRQ' => 'Iraq', 'ISL' => 'Iceland', 'ISR' => 'Israel', 'ITA' => 'Italy', 'JAM' => 'Jamaica', 'JEY' => 'Jersey', 'JOR' => 'Jordan', 'JPN' => 'Japan', 'KAZ' => 'Kazakhstan', 'KEN' => 'Kenya', 'KGZ' => 'Kyrgyzstan', 'KHM' => 'Cambodia', 'KIR' => 'Kiribati', 'KNA' => 'Saint Kitts and Nevis', 'KOR' => 'Korea, Republic of', 'KWT' => 'Kuwait', 'LAO' => 'Lao People\'s Democratic Republic', 'LBN' => 'Lebanon', 'LBR' => 'Liberia', 'LBY' => 'Libya', 'LCA' => 'Saint Lucia', 'LIE' => 'Liechtenstein', 'LKA' => 'Sri Lanka', 'LSO' => 'Lesotho', 'LTU' => 'Lithuania', 'LUX' => 'Luxembourg', 'LVA' => 'Latvia', 'MAC' => 'Macao', 'MAF' => 'Saint Martin (French part)', 'MAR' => 'Morocco', 'MCO' => 'Monaco', 'MDA' => 'Moldova, Republic of', 'MDG' => 'Madagascar', 'MDV' => 'Maldives', 'MEX' => 'Mexico', 'MHL' => 'Marshall Islands', 'MKD' => 'North Macedonia', 'MLI' => 'Mali', 'MLT' => 'Malta', 'MMR' => 'Myanmar', 'MNE' => 'Montenegro', 'MNG' => 'Mongolia', 'MNP' => 'Northern Mariana Islands', 'MOZ' => 'Mozambique', 'MRT' => 'Mauritania', 'MSR' => 'Montserrat', 'MTQ' => 'Martinique', 'MUS' => 'Mauritius', 'MWI' => 'Malawi', 'MYS' => 'Malaysia', 'MYT' => 'Mayotte', 'NAM' => 'Namibia', 'NCL' => 'New Caledonia', 'NER' => 'Niger', 'NFK' => 'Norfolk Island', 'NGA' => 'Nigeria', 'NIC' => 'Nicaragua', 'NIU' => 'Niue', 'NLD' => 'Netherlands', 'NOR' => 'Norway', 'NPL' => 'Nepal', 'NRU' => 'Nauru', 'NZL' => 'New Zealand', 'OMN' => 'Oman', 'PAK' => 'Pakistan', 'PAN' => 'Panama', 'PCN' => 'Pitcairn', 'PER' => 'Peru', 'PHL' => 'Philippines', 'PLW' => 'Palau', 'PNG' => 'Papua New Guinea', 'POL' => 'Poland', 'PRI' => 'Puerto Rico', 'PRK' => 'Korea (Democratic People\'s Republic of)', 'PRT' => 'Portugal', 'PRY' => 'Paraguay', 'PSE' => 'Palestine, State of', 'PYF' => 'French Polynesia', 'QAT' => 'Qatar', 'REU' => 'Réunion', 'ROU' => 'Romania', 'RUS' => 'Russian Federation', 'RWA' => 'Rwanda', 'SAU' => 'Saudi Arabia', 'SDN' => 'Sudan', 'SEN' => 'Senegal', 'SGP' => 'Singapore', 'SGS' => 'South Georgia and the South Sandwich Islands', 'SHN' => 'Saint Helena, Ascension and Tristan da Cunha', 'SJM' => 'Svalbard and Jan Mayen', 'SLB' => 'Solomon Islands', 'SLE' => 'Sierra Leone', 
                'SLV' => 'El Salvador', 'SMR' => 'San Marino', 'SOM' => 'Somalia', 'SPM' => 'Saint Pierre and Miquelon', 'SRB' => 'Serbia', 'SSD' => 'South Sudan', 'STP' => 'Sao Tome and Principe', 'SUR' => 'Suriname', 'SVK' => 'Slovakia', 'SVN' => 'Slovenia', 'SWE' => 'Sweden', 'SWZ' => 'Eswatini', 'SXM' => 'Sint Maarten (Dutch part)', 'SYC' => 'Seychelles', 'SYR' => 'Syrian Arab Republic', 'TCA' => 'Turks and Caicos Islands', 'TCD' => 'Chad', 'TGO' => 'Togo', 'THA' => 'Thailand', 'TJK' => 'Tajikistan', 'TKL' => 'Tokelau', 'TKM' => 'Turkmenistan', 'TLS' => 'Timor-Leste', 'TON' => 'Tonga', 'TTO' => 'Trinidad and Tobago', 'TUN' => 'Tunisia', 'TUR' => 'Türkiye', 'TUV' => 'Tuvalu', 'TWN' => 'Taiwan, Province of China', 'TZA' => 'Tanzania, United Republic of', 'UGA' => 'Uganda', 'UKR' => 'Ukraine', 'UMI' => 'United States Minor Outlying Islands', 'URY' => 'Uruguay', 'USA' => 'United States of America', 'UZB' => 'Uzbekistan', 'VAT' => 'Holy See', 'VCT' => 'Saint Vincent and the Grenadines', 'VEN' => 'Venezuela (Bolivarian Republic of)', 'VGB' => 'Virgin Islands (British)', 'VIR' => 'Virgin Islands (U.S.)', 'VNM' => 'Viet Nam', 'VUT' => 'Vanuatu', 'WLF' => 'Wallis and Futuna', 'WSM' => 'Samoa', 'YEM' => 'Yemen', 'ZAF' => 'South Africa', 'ZMB' => 'Zambia', 'ZWE' => 'Zimbabwe'
            ];
            $c_code = array_flip($c_code);
            $time_raw = strtotime(Input::get('time_e') . ' 23:59:59');
            $time_s = date('Y-m-d H:i:s', strtotime(Input::get('time_s') . ' 00:00:00'));
            $time_e = date('Y-m-d H:i:s', $time_raw);

            $query = DB::table('jocom_transaction AS trans')->leftJoin('jocom_transaction_details AS trans_detail', 'trans_detail.transaction_id', '=', 'trans.id')->leftJoin('jocom_transaction_coupon AS trans_coupon', 'trans_detail.transaction_id', '=', 'trans_coupon.transaction_id')->leftJoin('jocom_seller AS seller', 'seller.id', '=', 'trans_detail.seller_id');

            if(in_array(Input::get('plateform'), ['all', '11Street'])) $query = $query->leftjoin('jocom_elevenstreet_order AS eleven', 'trans.id', '=', 'eleven.transaction_id');
            if(in_array(Input::get('plateform'), ['all', 'shopee'])) $query = $query->leftjoin('jocom_shopee_order AS shopee', 'trans.id', '=', 'shopee.transaction_id');
            if(in_array(Input::get('plateform'), ['all', 'Qoo10'])){
                $query = $query->leftjoin('jocom_qoo10_order AS qoo10', 'trans.id', '=', 'qoo10.transaction_id');
                $query = $query->leftJoin('jocom_transaction_qoo10 AS quo', 'quo.id', '=', 'trans.id');
                $query = $query->leftJoin('jocom_transaction_details_qoo10 as quodtl', function($join) {
                    $join->on('quodtl.sku', '=', 'trans_detail.sku');
                    $join->on('quodtl.transaction_id', '=', 'trans.id');
                });
            }
            if(in_array(Input::get('plateform'), ['all', 'lazada'])) $query = $query->leftjoin('jocom_lazada_order AS lazada', 'trans.id', '=', 'lazada.transaction_id');

            $query = $query->leftJoin('jocom_mpay_transaction AS mpay', function ($join) {
                $join->on(DB::raw('(mpay.transaction_id = trans.id AND mpay.payment_status = "0")'), DB::raw(''), DB::raw(''));
            })->leftJoin('jocom_molpay_transaction AS molpay', function ($join) {
                $join->on(DB::raw('(molpay.transaction_id = trans.id AND molpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
            })->leftJoin('jocom_paypal_transaction AS paypal', function ($join) {
                $join->on(DB::raw('(paypal.transaction_id = trans.id AND paypal.payment_status = "Completed")'), DB::raw(''), DB::raw(''));
            })->leftJoin('jocom_boost_transaction AS boost', function ($join) {
                $join->on(DB::raw('(boost.transaction_id = trans.id AND boost.transaction_status = "completed")'), DB::raw(''), DB::raw(''));
            })->leftJoin('jocom_revpay_transaction AS revpay', function ($join) {
                $join->on(DB::raw('(revpay.transaction_id = trans.id AND revpay.payment_status = "00")'), DB::raw(''), DB::raw(''));
            })->leftJoin('jocom_grabpay_transaction AS grabpay', function ($join) {
                $join->on(DB::raw('(grabpay.transaction_id = trans.id AND grabpay.status = "success")'), DB::raw(''), DB::raw(''));
            });
            
            $query = $query->whereIn('trans.status', ['completed', 'cancelled'])->where('trans.invoice_date', '>=', $time_s)->where('trans.invoice_date', '<=', $time_e);
            
            if(Input::get('plateform') !== 'all') $query = (Input::get('plateform') === 'jocom' ? $query->whereNotIn('trans.buyer_username', array_diff($plateform, ['jocom' => 'jocom'])) : $query->where('trans.buyer_username', '=', Input::get('plateform')) );
            $query = $query->select('trans.id AS trans_id', DB::raw('DATE_FORMAT(trans.transaction_date, "%Y-%m-%d") AS trans_date'), 'trans.invoice_date AS inv_date', 'trans.buyer_username AS customer_username', 'trans_coupon.coupon_code', 'trans_coupon.coupon_amount', 'trans_detail.seller_id AS merchant_ID', 'trans_detail.seller_username AS merchant_username', 'seller.company_name AS company_name', 'trans.delivery_country AS shipping_country', 'trans.delivery_state AS shipping_state', 'trans_detail.product_id AS product_id', 'trans_detail.product_name AS product_name', DB::raw('ROUND(trans_detail.total, 2) AS pro_total'), DB::raw('ROUND(trans_detail.actual_total_amount, 2) AS pro_gross'), 'trans.delivery_charges AS delivery_charges', 'trans.foreign_delivery_charges AS foreign_delivery_charges', 'trans.total_amount AS trans_total', 'mpay.id AS mpayid', 'molpay.id AS molpayid', 'paypal.id AS paypalid', 'boost.id AS boostid', 'revpay.id AS revpayid', 'grabpay.id AS grabpayid');
            if(in_array(Input::get('plateform'), ['all', '11Street'])) $query = $query->addSelect('eleven.order_number AS order_number_eleven');
            if(in_array(Input::get('plateform'), ['all', 'shopee'])) $query = $query->addSelect('shopee.ordersn AS order_number_shopee');
            if(in_array(Input::get('plateform'), ['all', 'lazada'])) $query = $query->addSelect('lazada.order_number AS order_number_lazada');
            if(in_array(Input::get('plateform'), ['all', 'Qoo10'])) $query = $query->addSelect('qoo10.packNo AS order_number_qoo10');
            $trans_d = $query->orderBy('trans_detail.seller_id', 'ASC')->orderBy('trans.id', 'ASC')->get();


            $trans = []; $seller = []; $state_list = [];
            if($trans_d){
                // create the Trans Report Info
                foreach ($trans_d as $v) {
                    if(!isset($trans[$v->merchant_ID])) $trans[$v->merchant_ID] = [];
                    $c = ($c_code[$v->shipping_country] ? $c_code[$v->shipping_country] : $v->shipping_country);
                    if(!isset($trans[$v->merchant_ID][$c])) $trans[$v->merchant_ID][$c] = [];
                    if(!isset($trans[$v->merchant_ID][$c][$v->shipping_state])) $trans[$v->merchant_ID][$c][$v->shipping_state] = [
                        'total_sales' => 0,
                        'transactions_ID' => [], // hidden data wont show on excel
                        'number_of_transactions' => 0,
                        'consumers' => [], // hidden data wont show on excel
                        'number_of_consumers' => 0,
                        'disbursed_amount' => 0,
                        'matching_amount' => 0,
                        // 'discount_amount' => 0,
                        'budget_bucket' => 'halal & agro',
                    ];
                    
                    // Doing stack count logic
                    $trans[$v->merchant_ID][$c][$v->shipping_state]['total_sales'] += (float)$v->pro_total;
                    if(!in_array($v->trans_id, $trans[$v->merchant_ID][$c][$v->shipping_state]['transactions_ID'])){
                        $trans[$v->merchant_ID][$c][$v->shipping_state]['transactions_ID'][] = $v->trans_id;
                        $trans[$v->merchant_ID][$c][$v->shipping_state]['number_of_transactions']++;
                    }
                    if(!in_array($v->customer_username, $trans[$v->merchant_ID][$c][$v->shipping_state]['consumers'])){
                        $trans[$v->merchant_ID][$c][$v->shipping_state]['consumers'][] = $v->customer_username;
                        $trans[$v->merchant_ID][$c][$v->shipping_state]['number_of_consumers']++;
                    }
                    // if($v->coupon_code && $v->coupon_amount){
                    //     $p_total = (float)$v->trans_total - (float)$v->delivery_charges - (float)$v->coupon_amount;
                    //     $percent_disc = (float)$v->coupon_amount / $p_total;
                    //     $trans[$v->merchant_ID][$c][$v->shipping_state]['discount_amount'] += (float)$v->pro_total * $percent_disc;
                    // }
                }

                // create the Seller Report
                $trans_d = json_decode(json_encode($trans_d), true);
                $idlist = array_unique(array_column($trans_d, 'merchant_ID'));
                $seller = DB::table('jocom_seller')->whereIn('id', $idlist)->select('id AS merchant_ID', 'created_date AS onboard_date', 'company_name AS company_name', 'ic_no', 'state', 'postcode', 'email', 'mobile_no AS mobile', 'company_reg_num AS reg_num')->get();
                if($seller){
                    $seller = json_decode(json_encode($seller), true);
                    $state_idlist = array_unique(array_column($seller, 'state'));
                    $state_list = DB::table('jocom_country_states')->whereIn('id', $state_idlist)->lists('name', 'id');
                }
            }else{
                $trans_d = [];
            }

            
            // Maatwebsite\Excel\Facades\Excel
            return Excel::create('Report_Weekly_[W' . str_pad(date('W', $time_raw), 2, '0') . ']_[' . strtoupper(Input::get('plateform')) . ']_[' . date('dmY', $time_raw)  . ']', function($excel) use ($seller, $trans_d, $trans, $state_list, $c_code, $format) {
                $excel->sheet('Seller Report', function($sheet) use ($seller, $state_list) {
                    $sheet->loadView('report.edagang_seller', ['data' => [ 'seller' => $seller, 'state_list' => $state_list]]);
                });

                $excel->sheet('Transaction Details Report', function($sheet) use ($trans_d, $c_code, $format) {
                    $sheet->loadView('report.edagang_trans_d', ['data' => ['trans' => $trans_d, 'c_code' => $c_code, 'format' => $format]]);
                });

                $excel->sheet('Transaction Report', function($sheet) use ($trans) {
                    $sheet->loadView('report.edagang_trans', ['data' => $trans]);
                });

                $excel->sheet('Claimable Summary', function($sheet) {
                    $sheet->loadView('report.edagang_claim', ['data' => []]);
                });
            })->download('xls');
        }
        return View::make('report.edagang_index')->with('plateform', $plateform)->with('filepath', $filepath);
    }
    
}