<?php

class GoodsDefectFormController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing on datatable.
     *
     * @return Response
     */
    public function anyForms() { 

        $gdf = GoodsDefectForm::select('id', 'gdf_no', 'type', 'created_at','current_status')
                        ->where('status', '=', '1');
       
        return Datatables::of($gdf)
                        ->add_column('Seller', function ($gdf) { 
                        $seller=DB::table('jocom_goods_defect_form as gdf')
                        ->join('jocom_warehouse_location as warehouse', 'gdf.warehouse_id', '=', 'warehouse.id')
                        ->join('jocom_seller as seller', 'gdf.seller_id', '=', 'seller.id')
                        ->where('gdf.id', '=', $gdf->id)
                        ->select('seller.company_name as seller_name')
                        ->first();
                        return $seller->seller_name;
                        
                        }) 
                        ->edit_column('created_at', function ($gdf) {
                            return date_format(date_create($gdf->created_at), 'Y-m-d');
                        })
                        ->add_column('Action', function ($p) {

                            $file = (Config::get('constants.GDF_PDF_FILE_PATH') . '/' . urlencode($p->gdf_no) . '.pdf')."#".($p->id).'#'.$p->gdf_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '
                                <a class="btn btn-success" title="" data-toggle="tooltip" href="/gdf/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/gdf/edit/'.$p->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deleteGDF" class="btn btn-danger" title="" data-toggle="tooltip" data-value="'.$p->id.'" href="/gdf/delete/'.$p->id.'"><i class="fa fa-remove"></i></a>
                                ';
                        })
                        ->make();
    }

    /**
     * Display a listing of the GDF.
     *
     * @return Response
     */
    public function anyIndex() {
        return View::make('gdf.index');
    }

    /**
     * Show the form for creating a new GDF.
     *
     * @return Response
     */
    public function anyCreate() {   
        return View::make('gdf.create');
    }

     /**
     * Store a newly created GDF in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $validator = Validator::make(Input::all(), GoodsDefectForm::$rules, GoodsDefectForm::$message);

        if ($validator->passes()) {

            $type = Input::get('type');
            $warehouse_id = Input::get('warehouse_id');
            $seller_id = Input::get('seller_id');
            $reason = Input::get('reason');
            $current_status = Input::get('current_status');

            try {
                DB::beginTransaction();

                $gdf = new GoodsDefectForm;

                $running = DB::table('jocom_running_po')->where('value_key', '=', 'gdf_no')->first();
                if ($running->year == date('Y')) {
                    $count = $running->count + 1;
                } else {
                    $count = 1;
                }
                DB::table('jocom_running_po')->where('value_key', '=', 'gdf_no')
                    ->update(array('count' => $count, 'year' => date('Y')));
                    
                $gdf->gdf_no = 'GDF1'.str_pad($count, 4, "0", STR_PAD_LEFT).'-'.date('m').'-'.date('Y');
                $gdf->type = $type;
                $gdf->warehouse_id = $warehouse_id;
                // $gdf->seller_id = ($type == 'Returned To Vendor') ? $seller_id : 0;
                $gdf->seller_id = $seller_id;
                $gdf->reason = $reason;
                $gdf->current_status = $current_status;
            if(Input::hasFile('image')){
            $files =Input::file('image');
            $destination_path = 'public/gdf-img/upload/';

            foreach ($files as $file) {
            $attach =  date("Ymdhis")."-".uniqid();
            $filename = $attach. '_' .$file->getClientOriginalName();
            $file->move($destination_path,$filename);
            $input[]= $filename;
            }
            $final_path=json_encode($input);
                $gdf->gdf_image_path=$final_path;   
            }

                $gdf->status = 1;
                $gdf->created_by = Session::get('username');
                $gdf->updated_by = Session::get('username');

                $products = array();

                if($gdf->save())
                {

                    $product_ids = Input::get('product_id');
                    $quantitys = Input::get('quantity');
                    $remarks = Input::get('remark');


                    $goods_defect_details = array();

                    for ($i = 0; $i < count($product_ids); $i++) { 
                        $this->manageWarehouse($product_ids[$i], $quantitys[$i], 'decrease');
                        array_push($goods_defect_details, array('gdf_id' => $gdf->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i],'remark' => $remarks[$i]));
                    }

                    DB::table('jocom_goods_defect_form_details')->insert($goods_defect_details);

                } 

            } catch(Exception $ex) {
                $isError = true;dd($ex->getMessage());
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/gdf')->with('success', 'Goods Defect Form(GDF Number: '. $gdf->gdf_no .') added successfully.');
                }
            }

        } else {
            return Redirect::back()
                    ->withInput()
                    ->withErrors($validator);
        }
        
    }

    /**
     * Show the form for editing the specified GDF.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id) {

        $gdf = GoodsDefectForm::get($id);

        if ($gdf == null) {
            return Redirect::to('/gdf');
        }

        $gdf_details = DB::table('jocom_goods_defect_form_details as details')
                        ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                        ->where('gdf_id', '=', $id)
                        ->select('details.product_id', 'products.sku', 'products.name', 'details.quantity','details.remark')
                        ->get();

        if ($gdf->seller_id != 0) {
            $seller = Seller::where('id', '=', $gdf->seller_id)->select('company_name')->first();
        }

        return View::make('gdf.edit')->with(array(
            'gdf' => $gdf,
            'details' => $gdf_details,
            'seller_name' => $seller->company_name
        ));
    }

    /**
     * Update the specified GDF in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id) {

        $gdf = GoodsDefectForm::find($id);

        if ($gdf == null) {
            return Redirect::to('/gdf');
        }

        $gdf->type = Input::get('type');
        $gdf->warehouse_id = Input::get('warehouse_id');
        // $gdf->seller_id = ($gdf->type == 'Returned To Vendor') ? Input::get('seller_id') : 0;
        $gdf->seller_id = Input::get('seller_id');
        $gdf->reason = Input::get('reason');
        $gdf->current_status = Input::get('current_status');
        $gdf->updated_by = Session::get('username');

          if(Input::hasFile('image')){
            $files =Input::file('image');
            $destination_path = 'public/gdf-img/upload/';

            foreach ($files as $file) {
            $attach =  date("Ymdhis")."-".uniqid();
            $filename = $attach. '_' .$file->getClientOriginalName();
            $file->move($destination_path,$filename);
            $input[]= $filename;
            }
            $final_path=json_encode($input);
            if($gdf->gdf_image_path!=null){
                $old_path=json_decode($gdf->gdf_image_path);
                $finalpath=array_merge($old_path ,$input);
                $result=json_encode($finalpath);
                $gdf->gdf_image_path=$result;  
            }
            else{
                $gdf->gdf_image_path=$final_path;  
            }
            }

        if($gdf->save()) {

            $product_ids = Input::get('product_id');
            $quantitys = Input::get('quantity');
            $remarks = Input::get('remark');
            $product_remark_id = Input::get('product_id');

            
            $product_count = count($product_ids);

            $existing_products = DB::table('jocom_goods_defect_form_details')
                                    ->where('gdf_id', '=', $id)
                                    ->get();
            

            foreach ($existing_products as $ep) {
                if (in_array($ep->product_id, $product_ids)) {
                    $index = array_search($ep->product_id, $product_ids);

                    // exclude if product and quantity remain the same
                    if ($ep->quantity == $quantitys[$index]) {
                        unset($product_ids[$index]);
                        unset($quantitys[$index]);
                    } else {
                        $this->manageWarehouse($ep->product_id, $ep->quantity, 'increase');
                        DB::table('jocom_goods_defect_form_details')
                            ->where('id', '=', $ep->id)->delete();
                    }
                } else {
                    $this->manageWarehouse($ep->product_id, $ep->quantity, 'increase');
                    DB::table('jocom_goods_defect_form_details')
                            ->where('id', '=', $ep->id)->delete();
                }
                
            }

            $goods_defect_details = array();

            for ($i = 0; $i < $product_count; $i++) { 

                if (isset($product_ids[$i])) {
                    $this->manageWarehouse($product_ids[$i], $quantitys[$i], 'decrease');
                    array_push($goods_defect_details, array('gdf_id' => $gdf->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i],'remark' => $remark[$i]));
                }
            }

            if (count($goods_defect_details) > 0) {
                DB::table('jocom_goods_defect_form_details')->insert($goods_defect_details);
            }

//remark update
             foreach ($remarks as $key => $value) {
               
               $remarks_update = DB::table('jocom_goods_defect_form_details')
                                    ->where('gdf_id', '=', $id)
                                    ->where('product_id', '=',$product_remark_id[$key])
                                    ->update(['remark' => $value]);
                                   
             }
            General::audit_trail('GoodsDefectFormController.php', 'update()', 'Update Goods Defect Form', Session::get('username'), 'CMS');
            return Redirect::to('/gdf')->with('success', 'Goods Defect Form(ID: '.$gdf->id.') updated successfully.');
        }

    }
 
    /**
     * Remove the specified GDF from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id) {

        $gdf = GoodsDefectForm::find($id);

        if ($gdf == null) {
            return Redirect::to('/gdf');
        }
        $gdf->status = 2;
        $gdf->updated_by = Session::get('username');
        $gdf->save();

        $existing_products = DB::table('jocom_goods_defect_form_details')
                                    ->where('gdf_id', '=', $id)
                                    ->get();

        foreach ($existing_products as $ep) {
            $this->manageWarehouse($ep->product_id, $ep->quantity, 'increase');
        }
        
        General::audit_trail('GoodsDefectFormController.php', 'delete()', 'Delete Goods Defect Form', Session::get('username'), 'CMS');

        return Redirect::to('/gdf')->with('success', 'Goods Defect Form(ID: '.$gdf->id.') deleted successfully.');
    }

    public function anyFiles($loc) {

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);
        $gdf_id = $id[0];

        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {

            $gdf = GoodsDefectForm::find($gdf_id);

            $GDFView = self::createGDFView($gdf);

            return View::make('gdf.gdf_view')
                    ->with('gdf', $GDFView['gdf'])
                    ->with('warehouse', $GDFView['warehouse'])
                    ->with('seller', $GDFView['seller'])
                    ->with('products', $GDFView['products'])
                    ->with('htmlview',true);
        }
    }

    public function anyDownload($loc){

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);

        $id = explode("#", $loc);
        $gdf_no = $id[2];
        $gdf_id = $id[1];
        
        $file_path = array_shift(explode("#", $loc));
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];


        $gdf = GoodsDefectForm::find($gdf_id); 

        include app_path('library/html2pdf/html2pdf.class.php');

        $GDFView = self::createGDFView($gdf);

        $response =  View::make('gdf.gdf_view')
                        ->with('gdf', $GDFView['gdf'])
                        ->with('warehouse', $GDFView['warehouse'])
                        ->with('seller', $GDFView['seller'])
                        ->with('products', $GDFView['products'])
                        ->with('htmlview',false);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );
        
        return Response::download($file_path, $file_name, $headers);
        
    }

    public static function createGDFView($gdf) {

        $warehouse = WarehouseLocation::find($gdf->warehouse_id);
        $warehouse_details = [
            'name' => $warehouse->name,
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'postcode' => $warehouse->postcode,
            'city' => $warehouse->city,
            'country' => $warehouse->country,
        ];

        $seller = Seller::find($gdf->seller_id);
        if ($seller != null) {
            $sellerState = State::find($seller->state)->name;
            $sellerCity = City::find($seller->city)->name;
            $seller_details = [
                'company_name' => $seller->company_name,
                'address_1' => $seller->address1,
                'address_2' => $seller->address2,
                'postcode' => $seller->postcode,
                'city' => $sellerCity,
                'state' => $sellerState,
                'attn' => $seller->pic_full_name,
                'tel' => $seller->tel_num,
            ];
        } else {
            $seller_details = null;
        }
        

        $gdf_details = [
            'gdf_id' => $gdf->id,
            'gdf_no' => $gdf->gdf_no,
            'type' => $gdf->type,
            'gdf_date' => $gdf->created_at,
            'attn' => $gdf->attn,
            'reason' => $gdf->reason,
            'created_by' => $gdf->created_by,
        ];

        $product_details = DB::table('jocom_goods_defect_form_details as details')
                            ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                            ->where('details.gdf_id', '=', $gdf->id)
                            ->select('details.*', 'products.sku', 'products.name')
                            ->get();

        return array(
            'gdf' => $gdf_details,
            'warehouse' => $warehouse,
            'seller' => $seller_details,
            'products' => $product_details,
        );

    }

    public function anyProducts() {
        return View::make('gdf.ajaxproduct');
    }

    public function anyProductajax() {

        $products = DB::table('jocom_products')
        ->select([
            'jocom_products.id',
            'jocom_products.qrcode',
            'jocom_products.sku',
            'jocom_products.name',
            'jocom_products.status',
        ])
        ->join('jocom_warehouse_products', 'jocom_products.id', '=', 'jocom_warehouse_products.product_id')
        ->where('jocom_products.status', '!=', '2')->where('jocom_products.is_foreign_market', '<>', 1);
            
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" >Select</a>')
            ->make();
    }

    private function manageWarehouse($product_id, $quantity, $action) {
        $product_link = DB::table('jocom_warehouse_productslinks')
                            ->where('product_id', '=', $product_id)
                            ->select('parent_product_id', 'quantity')
                            ->first();

        if ($product_link != null) {

            $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $product_id)
                                ->select('stockin_hand')
                                ->first();

            $product_stock = $current_stock->stockin_hand;
            $parent_product_id = $product_link->parent_product_id;
            
            $carton = intval($quantity / $product_link->quantity);
            $loosed = $quantity % $product_link->quantity;

            if ($action == 'increase') {
                $this->increaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);
            } else {
                $this->decreaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);
            }
            
            
        } else {
            $query = DB::table('jocom_warehouse_products')
                        ->where('product_id', '=', $product_id);

            if ($action == 'increase') {
                $query->increment('stockin_hand', $quantity);
            } else {
                $query->decrement('stockin_hand', $quantity);
            }
        }
    }

    private function increaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {

            $loose = $loosed + $product_stock;
            if ($loose >= $carton_size) {
                $loose = $loose % $carton_size;
                $carton = $carton + 1;
            }

            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $product_id)
                ->update(['stockin_hand' => $loose]);
        } 

        DB::table('jocom_warehouse_products')
            ->where('product_id', '=', $parent_product_id)
            ->increment('stockin_hand', $carton);
    }

    private function decreaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {
            if ($loosed > $product_stock) {
                $loose = $carton_size - $loosed + $product_stock;
                $carton = $carton + 1;
            } else {
                $loose = $product_stock - $loosed;
            }

            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $product_id)
                ->update(['stockin_hand' => $loose]);
        } 

        DB::table('jocom_warehouse_products')
            ->where('product_id', '=', $parent_product_id)
            ->decrement('stockin_hand', $carton);
    }

    public function anyDeleteimage($filename,$id){

     $gdf = GoodsDefectForm::find($id);
     $destination_path = 'public/gdf-img/upload/';
     $decode=json_decode($gdf->gdf_image_path);
     $array=array_diff($decode,[$filename]);
     $encode=array_values($array);
     $final_path=json_encode($encode);

     if(file_exists($destination_path. '/' . $filename)) { 
        $gdf->gdf_image_path=$final_path;
        if($gdf->save()){
            File::delete($destination_path. '/' . $filename); 
            return Redirect::back()->with('message', 'Image Deleted successfully ');  
        }
        else{
        return Redirect::back()->with('message', 'Something went wrong');
        }
        }else{
        return Redirect::back()->with('message', 'File Not Found');
        } 
     
    }
   public function anyReport(){
            return View::make('gdf.export_gdf_report');

   }
    
    Public function anyReportdownload(){

       $type=Input::get('type');
       $transaction_from=Input::get('transaction_from');
       $transaction_to=Input::get('transaction_to');
        $start = $transaction_from . ' 00:00:00';
        $end = $transaction_to . ' 23:59:59';

        
        $result= DB::table('jocom_goods_defect_form as goodsfm')
                        ->join('jocom_goods_defect_form_details as details', 'goodsfm.id', '=', 'details.gdf_id')
                         ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                         ->join('jocom_product_price as product_price', 'details.product_id', '=', 'product_price.product_id')
                         ->join('jocom_product_price_seller as seller_price', 'product_price.id', '=', 'seller_price.product_price_id')
                        ->join('jocom_seller as seller', 'goodsfm.seller_id', '=', 'seller.id')
                        ->whereBetween('goodsfm.created_at', [$start, $end])
                        ->where('seller_price.activation','=','1');
                        
      if($type=='All'){
           $gdf_details=$result->select('goodsfm.created_at','goodsfm.gdf_no','goodsfm.created_by','goodsfm.current_status','seller.company_name as seller_name','details.product_id', 'products.sku', 'products.name','seller_price.cost_price as price', 'details.quantity','details.remark','goodsfm.reason')
                     ->orderBy('goodsfm.gdf_no', 'ASC')
                     ->get();
        }else{
             $gdf_details=$result->where('goodsfm.type','=',$type)
             ->select('goodsfm.created_at','goodsfm.gdf_no','goodsfm.created_by','goodsfm.current_status','seller.company_name as seller_name','details.product_id', 'products.sku', 'products.name','seller_price.cost_price as price', 'details.quantity','details.remark','goodsfm.reason')
             ->orderBy('goodsfm.gdf_no', 'ASC')
             ->get();
        
            
        }
      
        if(empty($gdf_details)){
            return Redirect::back()->with('message', 'No Data Found');
            }
            $redit=json_decode(json_encode($gdf_details ,true),true);
            $data_array[] = array('NO','DATE CREATED','DATE COMPLETED','GDF NO','VENDOR','SKU','ITEM','QUANTITY','COST/QTY','COST PRICE','REMARK','REASON','STATUS','PREPARED BY');
               $i=0;
               foreach ($redit as $key => $value) {
                     $prev=$redit[$key-1]['gdf_no'];
                    if($prev!=$value['gdf_no']){ 
                        $i++;
                    }
                    $data_array[] = array(
                    'NO'=>$i,
                    'DATE CREATED'=>$value['created_at'],
                    'DATE COMPLETED'=>'',
                    'GDF NO'=>$value['gdf_no'],
                    'VENDOR'=>$value['seller_name'],
                    'SKU'=>$value['sku'],
                    'ITEM'=>$value['name'],
                    'QUANTITY'=>$value['quantity'],
                    'COST/QTY'=>'RM '.$value['price'],
                    'COST PRICE'=>'RM '.$value['price']*$value['quantity'],
                    'REMARK'=>$value['remark'],
                    'REASON'=>$value['reason'],
                    'STATUS'=>$value['current_status'],
                    'PREPARED BY'=>$value['created_by'],
                    );
                     
                }
        return Excel::create('Goods Defect Form', function($excel) use ($redit,$data_array,$start,$end,$type) {

            $excel->sheet('Goods Defect Form', function($sheet) use ($redit,$data_array,$start,$end,$type){
                $sheet->cell('A2', function($cell) {$cell->setValue('Goods Defect Form');   });
                $sheet->cell('A2', function($cell) {$cell->setFont(array(
                        'size' => '25',
                        'bold' => true
                    ));   
                });
                $sheet->cell('A3', function($cell) {$cell->setFont(array(
                        'bold' => true
                    ));   
                });
                $sheet->cell('A4', function($cell) {$cell->setFont(array(
                        'bold' => true
                    ));   
                });
                
                $sheet->mergeCells('A2:N2');

                $sheet->setCellValue('A3', 'From Date - ');
                $sheet->setCellValue('B3', $start);
                $sheet->setCellValue('A4', 'To Date - ');
                $sheet->setCellValue('B4', $end);
                $sheet->setCellValue('A5', 'Export Type');
                $sheet->setCellValue('B5', $type);  
                $sheet->setCellValue('A6', '');
                $sheet->rows($data_array);
                $a7='8';
                $a8='8';
                $i='8';
                $j='8';
                foreach ($redit as $key => $value) {

                    $prev=$redit[$key-1]['gdf_no'];
                    if($prev==$value['gdf_no']){
                        $merge=$a7-1;
                    $sheet->mergeCells('A'.$merge.':'.'A'.$a8);
                    $sheet->mergeCells('B'.$merge.':'.'B'.$a8);
                    $sheet->mergeCells('D'.$merge.':'.'D'.$a8);
                    $sheet->mergeCells('E'.$merge.':'.'E'.$a8);
                    $sheet->mergeCells('L'.$merge.':'.'L'.$a8);
                    $sheet->mergeCells('C'.$merge.':'.'C'.$a8);
                    $sheet->mergeCells('M'.$merge.':'.'M'.$a8);
                    $sheet->mergeCells('N'.$merge.':'.'N'.$a8);
                    
                    }
                     $total+=$value['price']*$value['quantity'];
                     $a7++; 
                     $a8++;
                     $i++;


                }
                $sheet->setBorder('A'.$j.':'.'A'.$i);
                $sheet->setBorder('B'.$j.':'.'B'.$i);
                $sheet->setBorder('C'.$j.':'.'C'.$i);
                $sheet->setBorder('D'.$j.':'.'D'.$i);
                $sheet->setBorder('E'.$j.':'.'E'.$i);
                $sheet->setBorder('L'.$j.':'.'L'.$i);
                $sheet->setBorder('M'.$j.':'.'M'.$i);
                $sheet->setBorder('N'.$j.':'.'N'.$i);

                $sheet->cell('A'.$j.':'.'A'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B'.$j.':'.'B'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                    $sheet->cell('D'.$j.':'.'D'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                    $sheet->cell('E'.$j.':'.'E'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                    $sheet->cell('L'.$j.':'.'L'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                 $sheet->cell('M'.$j.':'.'M'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                $sheet->cell('N'.$j.':'.'N'.$i,function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                });
                $sheet->setCellValue('J'.$a8,'RM '.$total);
                $sheet->setCellValue('I'.$a8,'Total');
    
               $sheet->cell('J'.$a8,function($cell){
                    $cell->setBackground('#cce6ff');
                    $cell->setFontWeight('bold');
                });
               $sheet->cell('I'.$a8,function($cell){
                    $cell->setBackground('#cce6ff');
                    $cell->setFontWeight('bold');
                });
                $sheet->row(7, function($row) {
                    $row->setBackground('#D9D9D9');

                });
                $sheet->setHeight(7,29);
                $sheet->cell('A7:N7',function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                });

               
                

                $sheet->cells('A7:N7', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
            
                $sheet->setAllBorders('thin');
            });
        })->setFilename('Goods Defect Form'.Carbon\Carbon::now()->timestamp)
        ->download('xls');              
       
    }
}
?>