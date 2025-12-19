<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Product extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $fillable = ['product_name', 'product_desc', 'product_category', 'seller_name', 'seller_sku', 'price_label', 'price', 'price_promo', 'qty', 'p_referral_fees', 'image1', 'image2', 'image3', 'product_video', 'delivery_time', 'zone_id', 'default', 'status', 'freshness', 'bulk', 'p_weight'];

    public static $rules = [
        'seller_name' => 'required',
        'product_name' => 'required|min:5',
        //'product_desc' => 'required|min:10',
        //'product_category' => 'required',
        'price_label' => 'requiredOrArray',
        'price' => 'required|numericOrArray',
        //'price_promo' => 'required|numericOrArray',
        'p_referral_fees' => 'required|numericOrArray',
        'qty' => 'required|integerOrArray',
        //'image1' => 'required|mimes:jpeg,jpg,png',
        //'image2' => 'mimes:jpeg,jpg,png',
        //'image3' => 'mimes:jpeg,jpg,png',
        //'product_video' => 'url',
        'delivery_time' => 'required',
        // 'delivery_fee' => 'requiredOrArray',
        'zone_id'   => 'required',
        'default' => 'requiredOrArray'
    ];

    public static $message = [
        //'product_desc.required' => 'The product description is required.',
        //'image1.required' => 'The product needs at least one image.',
        'p_referral_fees.required' => 'The referral fees is required.',
        'qty.required' => 'The quantity is required.',
        'qty.numeric' => 'The quantity must be a number.'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_products';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'insert_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modify_date';


    public function isValid() {
        $validation = Validator::make($this->attributes, static::$rules, static::$message);

        if($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

    // File Name must in png
    public function generateQR($text='', $dir='', $file_name='') {
		include app_path('library/phpqrcode/qrlib.php');

        if (!is_dir($dir))
            mkdir($dir);

        $filename = $dir . '/' . $file_name;
        $errorCorrectionLevel = 'H'; // 'L','M','Q','H'
        $matrixPointSize = 8; // 1 - 10

        QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    // without declare classes again
    public function generateQR2($text='', $dir='', $file_name='') {
        
        if (!is_dir($dir))
            mkdir($dir);
        
        $filename = $dir . '/' . $file_name;
        $errorCorrectionLevel = 'H'; // 'L','M','Q','H'
        $matrixPointSize = 8; // 1 - 10
        
        QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
    }

    public static function dashboard_totalProducts()
    {

        $total =  DB::table('jocom_products')
               // ->where('status', 1)
                ->count();

        return $total;
    }

    public static function arrangeCategories(array $categories, $firstLevelPrefix = '', $secondLevelPrefix = '', $thirdLevelPrefix = '')
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

    public static function getGstValue($price_id, $specialPrice = false)
    {
        $price_gst = 1;
        $gst_status = Fees::get_gst_status();
        $table = $specialPrice ? 'jocom_sp_product_price' : 'jocom_product_price';
        if ($gst_status == '1')
        {
               $gstcheck = DB::table("{$table} AS a")
                    ->select('b.gst', 'b.gst_value')
                    ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                    ->where('a.id', '=', $price_id)
                    ->first();

                if (count($gstcheck)>0 && $gstcheck->gst == 2)
                {
                    $price_gst += $gstcheck->gst_value/100;
                }
        }

        return $price_gst;
    }

    public static function insert_product($product, $category, $zone, $newfile, $insertfile, $field, $jobID)
    {
        // create a copy of inserted product
        $inserted = fopen($insertfile, "w");
        fputcsv($inserted, $field);

        $fp = fopen($newfile, "r");

        $qrcount = 0;

        while(! feof($fp))
        {
            $data = fgetcsv($fp);
            $num = count($data);

            if (! is_bool($data))
            {
                $image['img_1'] = '';
                $image['img_2'] = '';
                $image['img_3'] = '';

                for ($i = 0; $i < $num; $i++)
                {
                    $insertInd = true;

                    if ($data[$i] == "Name")
                    {
                        $insertInd = false;
                        break;
                    }
                    

                    switch ($i)
                    {
                        case 0:
                            $product['name'] = $data[$i];
                            break;

                        case 1:
                            $product['description'] = $data[$i];
                            break;

                        case 2:
                            $price['label'] = $data[$i];
                            break;

                        case 3:
                            $price['price'] = $data[$i];
                            break;

                        case 4:
                            $price['price_promo'] = $data[$i];
                            break;

                        case 5:
                            $price['seller_sku'] = $data[$i];
                            break;

                        case 6:
                            $price['qty'] = $data[$i];
                            break;

                        case 7:
                            $price['stock'] = $data[$i];
                            break;

                        case 8:
                            $price['p_referral_fees'] = $data[$i];
                            break;

                        case 9:
                            $price['p_referral_fees_type'] = $data[$i];
                            break;

                        case 10:
                            $product['gst_value'] = $data[$i];
                            if ($product['gst_value'] <= 0)
                                $product['gst'] = 1;
                            elseif ($product['gst_value'] > 0)
                                $product['gst'] = 2;
                            break;

                        case 11:
                            $image['img_1'] = $data[$i];
                            break;

                        case 12:
                            $image['img_2'] = $data[$i];
                            break;

                        case 13:
                            $image['img_3'] = $data[$i];
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }

                if ($insertInd == true)
                {
                    $price['status']    = 1;
                    $price['default']   = 1;
                    $qrcount++;

                    DB::transaction(function() use ($product, $price, $category, $zone, $qrcount, $image, $jobID, $productID)
                    {
                        // $productID = Product::InsertProduct($product, 'jocom_products');
                        $productID = DB::table('jocom_products')->insertGetId($product);

                        $imageDone = Product::uploadImage($image, $jobID, $productID);

                        $qrCode     = 'TM'.$productID;
                        $qrCodeFile = $productID.'.png';

                        if($qrcount == 1)
                            $qrcode = Product::generateQR($qrCode, 'images/qrcode/', $qrCodeFile);
                        else
                            $qrcode = Product::generateQR2($qrCode, 'images/qrcode/', $qrCodeFile);

                        $insert_sellerID = DB::table('jocom_product_seller')->insert(array(
                                        'seller_id' => $product['sell_id'], 
                                        'product_id' => $productID, 
                                        'created_at' => $category['created_at'],
                                        'updated_at' => $category['created_at'],
                                        ));                             

                        $prow = Product::find($productID);

                        $prow->sku          = 'TM-'.str_pad($productID, 13, '0', STR_PAD_LEFT);
                        $prow->qrcode       = $qrCode;
                        $prow->qrcode_file  = $qrCodeFile;
                        $prow->img_1        = $imageDone[1];
                        $prow->img_2        = $imageDone[2];
                        $prow->img_3        = $imageDone[3];
                        $prow->save();

                        $category['product_id'] = $productID;
                        // $catID = Product::InsertProduct($category, 'jocom_categories');
                        $catID = DB::table('jocom_categories')->insertGetId($category);

                        $price['product_id'] = $productID;
                        // $labelID = Product::InsertProduct($price, 'jocom_product_price');
                        $labelID = DB::table('jocom_product_price')->insertGetId($price);

                        foreach ($zone['zone_id'] as $key => $value)
                        {
                            $tempzone['zone_id'] = $zone['zone_id'][$key];
                            $tempzone['price'] = $zone['price'][$key];
                            $tempzone['product_id'] = $productID;
                            // $zoneID = Product::InsertProduct($tempzone, 'jocom_product_delivery');
                            $zoneID = DB::table('jocom_product_delivery')->insertGetId($tempzone);
                        }
                    });

                    fputcsv($inserted, $data, ",", "\"");
                }
            }
        }

        fclose($inserted);
        fclose($fp);
        
    }


    public static function diff_pending($job)
    {
        $path  = Config::get('constants.CSV_IMPORT_PATH');

        $no_pending = false;

        if (count($job) > 0)
        {
           
            foreach ($job as $key => $value)
            {
                $file_original = 'original_' . $job['in_file'];
                $file_inserted = 'inserted_' . $job['in_file'];
                $file_diff = 'diff_' . $job['in_file'];

                if(file_exists($path.$file_original))
                {
                    // generate diff file for pending product list
                    $output = shell_exec('diff -N --unchanged-line-format= --old-line-format= --new-line-format=\'%L\' '. $path.$file_inserted .' '. $path.$file_original .' > '. $path.$file_diff);

                    // -N, --new-file
                    //     treat absent files as empty

                    // -E, --ignore-tab-expansion
                    //     ignore changes due to tab expansion

                    
                    unlink($path.$file_original);
                    rename($path.$file_diff, $path.$file_original); // rename diff file to original

                    if(file_exists($path.$file_inserted))
                        unlink($path.$file_inserted);


                    if(filesize($path.$file_original) == 0)
                    {
                        unlink($path.$file_original);                       

                        $temprow = DB::table('jocom_job_queue')->where('id', '=', $job['id'])->update(array('status' => 2));

                        if (file_exists($path.$job['id']))
                            Product::delTree($path.$job['id']); // delete image folder
                            // rmdir($path.$job['id']); // delete image folder

                        $no_pending = true;
                    }
                    else
                        $no_pending = false;
                }
                else
                {
                    $temprow = DB::table('jocom_job_queue')->where('id', '=', $job['id'])->update(array('status' => 2)); // update to complete if no pending file

                    if (file_exists($path.$job['id']))
                        Product::delTree($path.$job['id']); // delete image folder
                        // rmdir($path.$job['id']); // delete image folder

                    $no_pending = true;
                }
            }
        }

        return $no_pending;
    }

    public function uploadImage($image, $jobID, $productID)
    {
        $imgFilename = array();

        $folder  = Config::get('constants.CSV_IMPORT_PATH').$jobID;

        // var_dump($img);
        // echo "<br> jobID: " . $jobID;
        // echo "<br> productID: " . $productID . "<br>";

        if (!empty($image) AND file_exists($folder))
        {
            $unique = time();
            for($i = 1; $i < 4; $i++)
            {
                // echo $img["img_$i"] . "<br";

                if ($image["img_$i"] != '')
                {
                    $ext = explode(".", $image["img_$i"]);

                    $fileRight = false;
                    $fileType = array('gif', 'jpeg', 'jpg', 'png');

                    if (in_array($ext[1], $fileType))
                        $fileRight = true;

                    if (file_exists($folder."/".$image["img_$i"]) AND $fileRight)
                    {
                        $imgFilename[$i] = $productID . "-img$i-" . $unique . '.' . $ext[1];

                        rename($folder."/".$image["img_$i"], './images/data/'.$imgFilename[$i]);

                        Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(640, null, function($constraint) { $constraint->aspectRatio(); })->save()->destroy();
                        Image::make(sprintf('images/data/%s', $imgFilename[$i]))->resize(320, null, function($constraint) { $constraint->aspectRatio(); })->save('images/data/thumbs/' . $imgFilename[$i])->destroy();
                    }
                }
            }
        }

        return $imgFilename;        
    }

    public static function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));

        foreach ($files as $file)
        {
          (is_dir("$dir/$file")) ? Product::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
            
    public static function insert_tag(array $inputs) 
    {
        return DB::table('jocom_product_tags')
                    ->insert($inputs);
    }

    public static function delete_tag($id)
    {
        return DB::table('jocom_product_tags')->where('product_id', '=', $id)->delete();
    }

    public static function get_tags($id)
    {
        return DB::table('jocom_product_tags')
                ->select('tag_name')
                ->where('product_id', '=', $id)->get();
    }

    public static function get_all_price_id($id) {
        return DB::table('jocom_product_price')
                ->select('id')
                ->where('product_id', '=', $id)
                ->where('status', '=', 1)
                ->get();
    }
    
    public static function getBySKU($sku){
        
        $result = Product::where('sku',"=",$sku)->first();
    
        return $result;
        
    }
    
    public static function getByProductName($name){
    
        $result = Product::where('name',"=",$name)->first();
   
        return $result;
    
    }
    
    public static function getCheckStockproduct($productid){

        $result = Product::where('id',"=",$productid)->first();
        $stock_product = $result->is_base_product;
        return $stock_product;
    }
    
    public static function findProductInfoByQRCODE($qrCode) {
        $query = DB::table('jocom_products AS JP')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->select(
                    'JP.*', 
                    'JPP.label','JPP.price','JPP.price_promo','JPP.p_referral_fees','JPP.p_referral_fees_type','JPP.id AS ProductPriceID')
                    ->where("JP.qrcode","=",$qrCode)
                    ->where("JPP.status","=",1)
                    ->where("JPP.default","=",1)->first();
    
        return $query;
    }
   
    public static function getBackupproducts($productid){

        $dataprd = array();
        $datapri = array(); 

        $result = Product::where('id',$productid)
                          //->where('status','=',1)
                          ->get();

        if(count($result)>0){
                foreach ($result as $key => $value) {
                        
                        $dataprd = array('product_id' => $value->id,
                                         'sku' => $value->sku,
                                         'sell_id' => $value->sell_id,
                                         'name' => $value->name,
                                         'shortname' => $value->shortname,
                                         'name_cn' => $value->name_cn,
                                         'name_my' => $value->name_my,
                                         'category' => $value->category,
                                         'description' => $value->description,
                                         'description_cn' => $value->description_cn,
                                         'description_my' => $value->description_my,
                                         'img_1' => $value->img_1,
                                         'img_2' => $value->img_2,
                                         'img_3' => $value->img_3,
                                         'vid_1' => $value->vid_1,
                                         'qrcode' => $value->qrcode,
                                         'qrcode_file' => $value->qrcode_file,
                                         'delivery_time' => $value->delivery_time,
                                         'insert_by' => $value->insert_by,
                                         'insert_date' => $value->insert_date,
                                         'modify_by' => $value->modify_by,
                                         'modify_date' => $value->modify_date,
                                         'gst' => $value->gst,
                                         'gst_value' => $value->gst_value,
                                         'related_product' => $value->related_product,
                                         'status' => $value->status,
                                         'weight' => $value->weight,
                                         'do_cat' => $value->do_cat,
                                         'freshness' => $value->freshness,
                                         'freshness_days' => $value->freshness_days,
                                         'bulk' => $value->bulk,
                                         'halal' => $value->halal,
                                         'min_qty' => $value->min_qty,
                                         'max_qty' => $value->max_qty,
                                         'region_country_id' => $value->region_country_id,
                                         'region_id' => $value->region_id,
                                         'is_base_product' => $value->is_base_product,
                                         'is_popbox_available' => $value->is_popbox_available,
                                         'app_product_margin' => $value->app_product_margin,
                                         'created_at'   => date('Y-m-d H:i:s'),
                                         'created_by'   => Session::get('username'),
                                          );
                }

                $resultprice = Price::where('product_id',$productid)
                                  ->where('status','=',1)
                                  ->get();

                 foreach ($resultprice as $key => $value) {
                            
                            $temparray = array('price_id' => $value->id,
                                               'label' => $value->label,
                                               'label_cn' => $value->label_cn,
                                               'label_my' => $value->label_my,
                                               'alternative_label_name' => $value->alternative_label_name,
                                               'seller_sku' => $value->seller_sku,
                                               'barcode' => $value->barcode,
                                               'price' => $value->price,
                                               'price_promo' => $value->price_promo,
                                               'qty' => $value->qty,
                                               'stock' => $value->stock,
                                               'stock_unit' => $value->stock_unit,
                                               'p_referral_fees' => $value->p_referral_fees,
                                               'p_referral_fees_type' => $value->p_referral_fees_type,
                                               'default' => $value->default,
                                               'product_id' => $value->product_id,
                                               'status' => $value->status,
                                               'p_weight' => $value->p_weight,
                                               'created_at' => date('Y-m-d H:i:s'),
                                               'created_by'   => Session::get('username'),
                                              );

                            array_push($datapri, $temparray);

                        }                  

        }

        DB::table('jocom_products_backuphistory')->insert($dataprd);
        DB::table('jocom_product_price_backuphistory')->insert($datapri);

    }
    
    public function generateQR3($text='', $dir='', $file_name='') {
		require_once app_path('library/phpqrcode/qrlib.php');

        if (!is_dir($dir))
            mkdir($dir);

        $filename = $dir . '/' . $file_name;
        $errorCorrectionLevel = 'H'; // 'L','M','Q','H'
        $matrixPointSize = 8; // 1 - 10

        QRcode::png($text, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    
    
    
}
