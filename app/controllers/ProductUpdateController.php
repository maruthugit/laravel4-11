<?php

class ProductUpdateController extends BaseController {
    /**
     * Display a listing of the special price customers.
     *
     * @return Response
     */
    public function anyIndex()
    {
       return View::make('product_update.index');
    }

    public function getExport() 
    {
        $sellers    = ProductUpdate::get_sellers();   
        $jobs       = ProductUpdate::get_export_job(Session::get('user_id'), 'export_product_price_qty');

        return View::make('product_update.export_price_qty', ['sellers' => $sellers, 'type' => 'add', 'jobs' => $jobs]);
        
    }

    public function postExport($type)
    {
        switch ($type) 
        {
            case 'add'  :
                $seller_id  = Input::get('seller');
                $job_name   = "export_product_price_qty";
                $this->addQueue($seller_id, Session::get('user_id'), $job_name);
                break;

            // default:
            //     echo "<br>Default!";
        }

        $sellers    = ProductUpdate::get_sellers();   
        $jobs       = ProductUpdate::get_export_job(Session::get('user_id'), 'export_product_price_qty');

        return View::make('product_update.export_price_qty', ['sellers' => $sellers, 'type' => 'add', 'jobs' => $jobs]);
    }

    public function getImport()
    {
        $sellers    = ProductUpdate::get_sellers();   
        $jobs       = ProductUpdate::get_import_job(Session::get('user_id'), 'import_product_price_qty');

        return View::make('product_update.import_price_qty', ['sellers' => $sellers, 'type' => 'add', 'jobs' => $jobs]);
    }

    public function postImport($type)
    {
        switch ($type) 
        {
            case 'add'  :
                $seller_id  = Input::get('seller');
                $job_name   = "import_product_price_qty";
                $file       = Input::file('csv');
                $date       = date('Ymd_his');
                $file_name  = 'import_price_qty_'. Session::get('user_id') .'_'.$seller_id.'_'. $date. '.csv'; 

                if (Input::hasFile('csv')) {
                    $this->addQueue($seller_id, Session::get('user_id'), $job_name, $file_name);

                    $this->upload($seller_id, Session::get('user_id'), $file, $file_name);
                }

                break;

            // default:
            //     echo "<br>Default!";
        }

        $sellers    = ProductUpdate::get_sellers();   
        $jobs       = ProductUpdate::get_import_job(Session::get('user_id'), 'import_product_price_qty');

        return View::make('product_update.import_price_qty', ['sellers' => $sellers, 'type' => 'add', 'jobs' => $jobs]);
    }


    public function anyFiles($file=null) {
        if (Input::has('out')) {
            $path = Config::get('constants.CSV_FILE_PATH'); //"media/csv/";
            $file = Input::get('out');
        }

        if (Input::has('in')) {
            $path = Config::get('constants.CSV_UPLOAD_PATH'); //"media/csv/upload";
            $file = Input::get('in');
        }

        if (Input::has('log')) {
            $path = Config::get('constants.CSV_LOG_PATH'); //"media/csv/log/";
            $file = Input::get('log');
        }
            
        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyLog($file=null) {
        $path = Config::get('constants.CSV_LOG_PATH');  //"media/csv/log/";

        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyExportjob() {
        
        try{
            
        
        $job_name   = "export_product_price_qty";
        $path_log   = Config::get('constants.CSV_LOG_PATH');        //"../public/media/csv/log/";
        $path_from  = Config::get('constants.CSV_UPLOAD_PATH');     //"../public/media/csv/upload/";
        $path_to    = Config::get('constants.CSV_FILE_PATH');       //"../public/media/csv/";
        $date       = date("Ymd_His");
        $filename   = "";

        $jobs       = ProductUpdate::get_job(Session::get('user_id'), $job_name);
       
        foreach ($jobs as $job) {
            ProductUpdate::update_job_queue($job->id, array('status' => '1'));

            $filename   = "product_price_qty_".$job->ref_id."_".$date.".csv";
            $seller_id  = $job->ref_id;
            $result     = ProductUpdate::export_get_header();
            $contents   = ProductUpdate::export_get_csv_content($seller_id);
            
        //   echo "<pre>";
        // print_r($contents);
        // echo "</pre>";
          
            
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
                    if($count == 3) {
                        if($value == '0') $value = 'Inactive';
                        else $value = 'Active';
                    }

                    $arr_line[] = $value;
                    $count++;
                }

                fputcsv($fp, $arr_line, ",", "\""); 
            }

            if (fclose($fp)) 
                ProductUpdate::update_job_queue($job->id, array('status' => '2', 'out_file' => $filename));
        }
        
        }catch(exception $ex){
            echo $ex->getMessage();
        }

    }

    public function anyImportjob() {
        $job_name   = 'import_product_price_qty';
        $jobs       = ProductUpdate::get_job(Session::get('user_id'), $job_name);
        $path_log   = Config::get('constants.CSV_LOG_PATH');        //"../public/media/csv/log/";
        $path_from  = Config::get('constants.CSV_UPLOAD_PATH');     //"../public/media/csv/upload/";
        $path_to    = Config::get('constants.CSV_FILE_PATH');       //"../public/media/csv/";
        $date       = date("Ymd");

        foreach ($jobs as $job) {
            $id             = $job->id;
            $ref_id         = $job->ref_id;
            $uid            = $job->request_by;
            $file_fr_user   = $job->in_file;
            $total_update   = 0;
            $count          = 0;
            $exist          = false;
            $log_name       = "log_".$uid."_".$id."_".$date.".txt";
            $log_file       = $path_log.$log_name;

            $log            = fopen($log_file, "w"); 
            $str_log        = "";
            $file_update    = "";

            if (ProductUpdate::check_exists($ref_id)) {
                $str_log = "[DateTime] ".date("Y-m-d H:i:s");
                fwrite($log, $str_log);

                $exist = true;
                
                // Export a copy of the existing group prices from jocom_sp_product_price.
                $filename   = "export_product_".$ref_id.'_'.$date.'.csv';
                $file_fr_sys= $path_to.$filename;
                $csv        = $this->get_data_to_csv($ref_id, $file_fr_sys);

                // Get the uploaded file from user, diff both files, then output the result into another file.
                if ($csv) {
                    $str_log = "\n[FILE from USER] ".$path_from.$file_fr_user;
                    fwrite($log, $str_log);
                    
                    $new_file = "new_".$file_fr_user;
                    
                    if($this->filter_uploaded_file($path_from.$file_fr_user, $path_to.$new_file, 4)) {
                        // exec('sed -e \'s/^M/\n/g\' '.$path_to.$new_file .' > '. $path_to.'test.csv', $output);
                         
                        $this->replace_carriage_return($path_to.$new_file);
                        
                        $str_log = "\n[NEW User FILE] " . $path_to.$new_file;
                        fwrite($log, $str_log);
                    }

                    $str_log = "\n[FILE from SYSTEM] ".$file_fr_sys."\n";
                    fwrite($log, $str_log);

                    // $diff_file      = 'diff_'.$id.'_'.$date.'.csv'; 
                    // Diff both files and output the result into another file.
                    // if (file_exists($path_from.$file_fr_user) && file_exists($file_fr_sys)) {
                    //     $output = shell_exec('diff --unchanged-line-format= --old-line-format= --new-line-format=\'%L\' '. $file_fr_sys .' '. $path_to.$new_file .' > '. $path_to.$diff_file);
                    //     // $output = shell_exec('diff '. $file_fr_sys .' '. $path_from.$file_fr_user .' > '. $path_to.$diff_file);
                    //     // echo "<br> OUTPUT 2 ==> ".$output;

                    //     $file_update = $path_to.$diff_file;
                    //     echo "<br>FILE INSERT = = >".$file_update ."\n";
                    // }
                }
            } 
            
            if ($exist && file_exists($path_to.$new_file)) {
                $arr_product_columns    = array("description", "description_my", "gst");
                $arr_price_columns      = array("price", "price_promo", "qty", "stock", "p_referral_fees", "p_referral_fees_type","p_weight", "seller_sku");

                $fp             = fopen($path_to.$new_file, "r");
                $total_update   = 0;

                while($data = fgetcsv($fp)) {
                    $num            = count($data); 
                    $price_id       = "";
                    $product_id     = "";
                    $price_data     = array();
                    $product_data   = array();
                    $j              = 0;
                    $k              = 0;
                    $updated        = true;

                    for ($i = 0; $i < $num; $i++) {
                        $value  = "";

                        if ($data[$i] == "product_id") break;
                        if ($i == 0) $product_id= $data[$i];
                        if ($i == 4) $price_id  = $data[$i];

                        if ($i > 0 && $i <= count($arr_product_columns)) {
                            $value = $arr_product_columns[$j];
                            $product_data[$value]   = $data[$i];
                            $j++;
                        }
                        if ($i > 5) {
                            $value = $arr_price_columns[$k];
                            $price_data[$value] = $data[$i];
                            $k++;
                        }
                    }

                    if ($product_id != "") {
                        DB::beginTransaction();

                        try {
                            $newProduct = DB::table('jocom_products')
                                    ->where('id','=', $product_id)
                                    ->update($product_data);

                            $newPrice = DB::table('jocom_product_price')
                                    ->where('id', '=', $price_id)
                                    ->update($price_data);

                        } catch (\Exception $e) {
                            $updated = false;
                            $str_log = "\n[UPDATE: FAILED] [ProductID: $product_id] [PriceID: $price_id]";
                            fwrite($log, $str_log);
                            $total_update++;
                            DB::rollback();
                        }

                        DB::commit();

                        if ($updated == true) {
                            $str_log = "\n[UPDATE: OK] [ProductID: $product_id] [PriceID: $price_id]";
                            fwrite($log, $str_log);
                            $total_update++;
                        }
                    }

                    $count++;
                }
            }

            $str_log  = "\n=============================================================================================================================";
            $str_log .= "\nTOTAL RECORDS: ".$count;
            $str_log .= "\nTOTAL UPDATED: ".$total_update;
            $str_log .= "\n=============================================================================================================================\n";

            fwrite($log, $str_log);
            fclose($log);
            ProductUpdate::update_job_queue($job->id, array('status' => '2', 'out_file' => $log_name));

        }
    }

    private function addQueue($seller_id, $user_id, $job_name, $file="") 
    {
        $job                = array(); 
        $job['ref_id']      = $seller_id;
        $job['job_name']    = $job_name;
        $job['request_by']  = $user_id;
        $job['request_at']  = date('Y-m-d H:i:s');
        $job['in_file']     = $file;

        ProductUpdate::add_job($job);
    }

    private function upload($file, $seller, $file, $file_name)
    {
        $inputs     = Input::all();
        $dest_path  = Config::get('constants.CSV_UPLOAD_PATH');
        
        $job                = array(); 
        $job['ref_id']      = $seller;
        $job['job_name']    = $job_name;
        $job['in_file']     = $file_name;
        $job['request_by']  = Session::get('user_id');
        $job['request_at']  = date('Y-m-d H:i:s');
  
        $file_ext           = $file->getClientOriginalExtension();

        if(strtolower($file_ext) == "csv") {
            $upload_file   = $file->move($dest_path, $file_name);
        }
    }

    

    private function get_data_to_csv($id, $file) {
        $seller_id          = $id;
        // echo "\n - - - [get_data_to_csv] - - - \n";
        // echo "File: ".$file;

        $fp         = fopen($file, "w");
        $result     = ProductUpdate::import_get_header();
        $contents   = ProductUpdate::import_get_csv_content($seller_id);

        if (count($result) > 0) {
            foreach ($result as $r) {
                foreach ($r as $key => $value) {
                    $header[] = $key;
                }
            }
            fputcsv($fp, $header);
        }
       
        foreach ($contents as $content) {
            $arr_line   = "";

            foreach ($content as $key => $value) {
                $arr_line[] = $value;
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