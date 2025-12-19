<?php
include app_path('library/PDFMerger/Classes/PDFMerger.php');

class PurchaseOrderController extends BaseController {

    private static $po_type = [
        1 => 'PURCHASE ORDER',
        2 => 'PURCHASE REQUISITION FORM',
        3 => 'PURCHASE ORDER GS',
    ];
    
    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function orders() { 
        $po = PurchaseOrder::orderlists();

        return Datatables::of($po)
                        ->remove_column('grn_id')
                        ->remove_column('einv_id')
                        ->remove_column('einv_status')
                        ->remove_column('delivery_date')
                        ->add_column('total', function ($po) {
                        $po_detail = DB::table('jocom_purchase_order_details')
                        ->where('po_id', '=', $po->id)
                        ->sum('total');
                                
                        return $po_detail;
                            
                        })
                        ->edit_column('status', function ($po) {
                                 
                            if($po->status=="2"){
                                return'<button type="button" class="btn btn-danger btn-sm">Cancelled</button>';
                            }
                            if($po->status=="3")
                            {
                             return'<button type="button" class="btn btn-warning btn-sm">Mistake</button>';   
                            }
                            if($po->status=="4"){
                              return'<button type="button" class="btn btn-primary btn-sm">Revised</button>';   
                            }
                            else{
                              return'<button type="button" class="btn btn-success btn-sm">Active</button>';  
                            }
                        })
                        ->add_column('Action', function ($p) {

                            $file = (Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($p->po_no) . '.pdf')."#".($p->id).'#'.$p->po_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            $generate_button = '';
                            if ($p->grn_id != null) {
                                $generate_button = '<a id="generateEInv" class="btn btn-primary" title="" data-toggle="tooltip"  data-value="'.$p->id.'" href="/einvoice/create/'.$p->grn_id.'">Generate eInvoice</a>';
                            }else{
                                                            $generate_button = '<a class="btn btn-warning" id="signedpo" title="Upload Signed PO" data-toggle="modal" data-target="#myModal"  data-value="'.$p->id.'"><i class="fa fa-upload" aria-hidden="true"> Signed PO</i></a>';
    
                            }
                            
                            if ($p->einv_id != null && $p->einv_status == 1) {
                                $generate_button = '<a class="btn btn-warning" id="signedpo" title="Upload Signed PO" data-toggle="modal" data-target="#myModal"  data-value="'.$p->id.'"><i class="fa fa-upload" aria-hidden="true"> Signed PO</i></a>';
                            }
                            
                             if($p->status=="2"){
                                return '
                                <a class="btn btn-success" title="download" data-toggle="tooltip" href="/purchase-order/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>                              
                                ';
                            }else{
                            return '
                                <a class="btn btn-success" title="download" data-toggle="tooltip" href="/purchase-order/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="Edit" data-toggle="tooltip" href="/purchase-order/edit/'.$p->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deletePO" class="btn btn-danger" title="status" data-toggle="tooltip" data-value="'.$p->id.'" href="/purchase-order/delete/'.$p->id.'"><i class="fa fa-times-circle" style="font-size:17px"></i></a>
                                ' . $generate_button;
                            }
                        })
                        ->make();
    }

    /**
     * Display a listing of the purchase order.
     *
     * @return Response
     */
    public function index() {
       
        return View::make('purchase-order.index');
    }


    /**
     * Show the form for creating a new purchase order.
     *
     * @return Response
     */
    public function create() {   
        $payment_terms = PaymentTerms::activeList();
        $managers = Manager::activeList();
        return View::make('purchase-order.create_purchaseorder')
            ->with([
                'payment_terms' => $payment_terms,
                'managers' => $managers,
                'po_type' => self::$po_type,
            ]);
    }

     /**
     * Store a newly created PO in storage.
     *
     * @return Response
     */
    public function store() {
        $validator = Validator::make(Input::all(), PurchaseOrder::$rules);

        if ($validator->passes()) {

            $type = Input::get('type');
            $po_date = Input::get('po_date');
            $payment_terms = Input::get('payment_terms');
            $delivery_date = Input::get('delivery_date');
            $from = Input::get('from');
            $seller_id = Input::get('seller_id');
            $warehouse_location_id = Input::get('warehouse_id');
            $manager = Input::get('manager');
            $remark = Input::get('specialmsg');

            try {
                DB::beginTransaction();

                $po = new PurchaseOrder;
                $po->type = $type;
                $po->po_date = date_format(date_create($po_date), 'Y-m-d H:i:s');
                $po->payment_terms = $payment_terms;
                $po->delivery_date = date_format(date_create($delivery_date), 'Y-m-d H:i:s');
                $po->from = $from;
                $po->seller_id = $seller_id;
                $po->warehouse_location_id = $warehouse_location_id;
                $po->manager = $manager;
                $po->remark = $remark;
                $po->status = 1;
                $po->discount_percent = Input::get('discpercent');
                $po->discount_total = str_replace(',', '', Input::get('disctotal'));
                $po->created_by = Session::get('username');
                $po->updated_by = Session::get('username');

                if ($type == 1){
                    $running = DB::table('jocom_running_po')->where('value_key', '=', 'poe')->first();
                    if ($running->year == date('Y')) {
                        $count = $running->count + 1;
                    } else {
                        $count = 1;
                    }
                    DB::table('jocom_running_po')->where('value_key', '=', 'poe')
                            ->update(array('count' => $count, 'year' => date('Y')));
                    $po->po_no = 'POE1' . str_pad($count, 4, "0", STR_PAD_LEFT).'-'.date('n').'-'.date('y');
                } else {
                    $running = DB::table('jocom_running_po')->where('value_key', '=', 'prc')->first();
                    if ($running->year == date('Y')) {
                        $count = $running->count + 1;
                    } else {
                        $count = 1;
                    }
                    DB::table('jocom_running_po')->where('value_key', '=', 'prc')
                            ->update(array('count' => $count, 'year' => date('Y')));
                    $po->po_no = 'PRC1' . str_pad($count, 4, "0", STR_PAD_LEFT).'-'.date('n').'-'.date('y');
                }

                $products = array();

                if($po->save())
                {

                    $priceopts = Input::get('priceopt');
                    $qrcodes = Input::get('qrcode');
                    $promo_prices = Input::get('price_promo_local');
                    $quantitys = Input::get('qty');
                    $baseUnits = Input::get('base_unit');
                    $packingFactor = Input::get('packing_factor');
                    $ssts = Input::get('sst');

                    for ($i = 0; $i < count($priceopts); $i++) { 
                        $product = DB::table('jocom_products as product')
                                    ->join('jocom_product_price as price', 'product.id', '=', 'price.product_id')
                                    ->where('product.qrcode', '=', $qrcodes[$i])
                                    ->where('price.id', '=', $priceopts[$i])
                                    ->where('price.status', '=', '1')
                                    ->select('product.id', 'product.name', 'product.sku', 'price.label', 'price.price', 'price.stock_unit')
                                    ->first();

                        $foc = 0;
                        $product_price = floatval($product->price);
                        $promo_price = floatval($promo_prices[$i]);
                        $price = $product_price;
                        if ($product_price == 0 && $promo_price == 0) {
                            $foc = 1;
                        }

                        if ($promo_price > 0) {
                            $price = $promo_price;
                        }

                        $quantity = floatval($quantitys[$i]);
                        $base_unit = $baseUnits[$i];
                        $packing_factor = $packingFactor[$i];
                        $product = array(
                            'po_id' => $po->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'sku' => $product->sku,
                            'price_label' => $product->label,
                            'uom' => $product->stock_unit,
                            'base_unit' => $base_unit,
                            'packing_factor' => $packing_factor,
                            'price' => $price,
                            'quantity' => $quantity,
                            'total' => ($price * $quantity) - $sst[$i],
                            'foc' => $foc,
                            'sst' => $ssts[$i]
                        );

                        array_push($products, $product);

                        
                    }
                    DB::table('jocom_purchase_order_details')->insert($products);

                } 

            } catch(Exception $ex) {
                $isError = true;dd($ex->getMessage());
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/purchase-order')->with('success', 'Purchase Order(PO Number: '. $po->po_no .') added successfully.');
                }
            }

        } else {
            return Redirect::back()
                    ->withInput()
                    ->withErrors($validator);
        }
        
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

        $po = PurchaseOrder::get($id);
        

        if ($po == null) {
            return Redirect::to('/purchase-order');
        }

        $po_details = DB::table('jocom_purchase_order_details')
                        ->where('po_id', '=', $id)
                        ->get();
        $po_logs = DB::table('jocom_po_update_log')
                        ->where('po_id', '=', $id)
                        ->orderBy('id', 'DESC')
                        ->get();

        $total = 0.0;
        foreach ($po_details as $detail) {
            if ($detail->base_unit == 'U') {
                $detail->base_unit = 'Unit';
            } else {
                $detail->base_unit = 'Pack/Carton';
            }

            $total += (floatval($detail->total) + floatval($detail->sst));
        }

        $managers = Manager::activeList();
        $selected_manager = array_search($po->manager, $managers);
        

        return View::make('purchase-order.edit')->with([
            'po' => $po,
            'po_details' => $po_details,
            'total' => $total,
            'managers' => $managers,
            'selected_manager' => $selected_manager,
            'po_type' => self::$po_type,
        ]);
    }

    /**
     * Update the specified PO in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

       
 $po_details = DB::table('jocom_purchase_order_details')
                        ->where('po_id', '=', $id)
                        ->get();
$total = 0.0;
        foreach ($po_details as $detail) {
            if ($detail->base_unit == 'U') {
                $detail->base_unit = 'Unit';
            } else {
                $detail->base_unit = 'Pack/Carton';
            }

            $total += (floatval($detail->total) + floatval($detail->sst));
        }
        $product=array();
     foreach ($po_details as $trans_details) {
           $product[]=([
            'sku'=> $trans_details->sku,
            'quantity'=> $trans_details->quantity,
            'price'=> $trans_details->price,
            'sst'=> $trans_details->sst,

       ]);
     }

        $po = PurchaseOrder::find($id);
        $po_status = PurchaseOrder::editlists($id);
        $po_update = PurchaseOrder::find($id);
         
        if ($po == null) {
            return Redirect::to('/purchase-order');
        }

        if($po->from!=Input::get('from')){
         $from_data_old=$po->from;
         $from_data=Input::get('from');
 
        }else{
        $from_data=$po->from;
        $from_data_old=$po->from;

        }
        if( $po->discount_percent!=Input::get('discpercent')){
        $discount_percent_old=$po->discount_percent;
        $discount_percent = Input::get('discpercent');
        }
        else
        {
        $discount_percent=$po->discount_percent;
        $discount_percent_old=$po->discount_percent;
        }
        if($po->manager !=Input::get('manager')) {
        $manager_old=$po->manager;
        $manager = Input::get('manager');
        }
        else{
        $manager=$po->manager;
        $manager_old=$po->manager;
        }
        if ($po->remark !=Input::get('specialmsg')){
        $remark_old=$po->remark;
        $remark = Input::get('specialmsg');
        }
        else{
        $remark=$po->remark;
        $remark_old=$po->remark;
        }
        if ($po->delivery_date !=Input::get('delivery_date')." 00:00:00"){
        $delivery_date_old=$po->delivery_date;
        $delivery_date = Input::get('delivery_date')." 00:00:00";
        }
        else{
        $delivery_date=$po->delivery_date;
        $delivery_date_old=$po->delivery_date;
        }
        
        if($po->updated_by != Session::get('username')){
        $updated_by_old=$po->updated_by;
        $updated_by = Session::get('username');
        }
        else{
        $updated_by=Session::get('username');;
        $updated_by_old=$po->updated_by;
        }

        $po->from = Input::get('from');
        $po->discount_percent = Input::get('discpercent');
        $po->discount_total = str_replace(',', '', Input::get('disctotal'));
        $po->manager = Input::get('manager');
        $po->remark = Input::get('specialmsg');
        $po->delivery_date = Input::get('delivery_date');
        $po->updated_by = Session::get('username');

        
        if($po->save()) {

            // edit existing po items
            $edit_ids = Input::get('edit_id');
            $edit_quantitys = Input::get('edit_quantity');
            $edit_prices = Input::get('edit_price');
            $edit_totals = Input::get('edit_total');
            $edit_ssts = Input::get('edit_sst');

            $products=array();
            for ($i = 0; $i < count($edit_ids); $i++) {

            if($product[$i]['quantity']!=$edit_quantitys[$i]){
            $products[$i]['sku']=$product[$i]['sku'];
            $products[$i]['quantity']=$edit_quantitys[$i];
            
            }
            else{
            $products[$i]['sku']=$product[$i]['sku'];
            $products[$i]['quantity']=$product[$i]['quantity'];
            }
            if($product[$i]['price']!=$edit_prices[$i]){
            $products[$i]['price']=$edit_prices[$i];
            }
            else{
            $products[$i]['price']=$product[$i]['price'];
            }
            if($product[$i]['sst']!=$edit_ssts[$i]){
            $products[$i]['sst']=$edit_ssts[$i];
            }
            else{
            $products[$i]['sst']=$product[$i]['sst'];
            }
            }
    
            for ($i = 0; $i < count($edit_ids); $i++) { 
                
                DB::table('jocom_purchase_order_details')
                    ->where('id', '=', $edit_ids[$i])
                    ->update([
                        'price' => str_replace(',', '', $edit_prices[$i]),
                        'quantity' => str_replace(',', '', $edit_quantitys[$i]),
                        'total' => str_replace(',', '', $edit_totals[$i]),
                        'sst' => $edit_ssts[$i]
                    ]);
            }

            $delete_ids = Input::get('delete_id');  
            $product_delete=array();
            $product_add=array();
            $delete_pro=array();
            for ($i = 0; $i < count($delete_ids); $i++) { 
            $delete_pro[]=DB::table('jocom_purchase_order_details')
            ->where('id', '=', $delete_ids[$i])
            ->get();         
            }

            // delete existing po items
           
            for ($i = 0; $i < count($delete_ids); $i++) { 
            DB::table('jocom_purchase_order_details')
            ->where('id', '=', $delete_ids[$i])
            ->delete();
            }    
            for ($i = 0; $i < count($delete_ids); $i++) { 
            $product_delete[]=$delete_pro[$i][0]->product_id;
            }
          
            // after genarerated invoice if update any.will set revised status 
            if ($po_status->einv_id != null && $po_status->einv_status == 1) {
            DB::table('jocom_purchase_order')
            ->where('id', '=',$id)
            ->update([
            'status' => 4,
            'updated_by' => Session::get('username'),
            ]);
            $product_old=json_encode($product);
            $product_new=json_encode($products);
            $delete_pr=json_encode(array_filter($product_delete));

            //update logs after every revised
            // PurchaseOrder::po_update_log($from_data,$discount_percent,$manager,$remark,$updated_by,$id,$from_data_old,$discount_percent_old,$manager_old,$remark_old,$updated_by_old,$product_new,$product_old,$delete_pr);

            }

            // add po items
            $priceopts = Input::get('priceopt');
            $qrcodes = Input::get('qrcode');
            $promo_prices = Input::get('add_price');
            $quantitys = Input::get('add_quantity');
            $totals = Input::get('add_total');
            $baseUnits = Input::get('base_unit');
            $packingFactor = Input::get('packing_factor');
            $ssts = Input::get('add_sst');

            if (count($priceopts) > 0) {
                $products = array();

                for ($i = 0; $i < count($priceopts); $i++) { 
                    $product = DB::table('jocom_products as product')
                                ->join('jocom_product_price as price', 'product.id', '=', 'price.product_id')
                                ->where('product.qrcode', '=', $qrcodes[$i])
                                ->where('price.id', '=', $priceopts[$i])
                                ->where('price.status', '=', '1')
                                ->select('product.id', 'product.name', 'product.sku', 'price.label', 'price.price', 'price.stock_unit')
                                ->first();

                    $foc = 0;
                    $product_price = floatval($product->price);
                    $promo_price = floatval($promo_prices[$i]);
                    $price = $product_price;
                    if ($product_price == 0 && $promo_price == 0) {
                        $foc = 1;
                    }

                    if ($promo_price > 0) {
                        $price = $promo_price;
                    }

                    $quantity = floatval($quantitys[$i]);
                    $base_unit = $baseUnits[$i];
                    $packing_factor = $packingFactor[$i];
                    $total = floatval($totals[$i]);
                    $product_add[]=array('product_id' => $product->id,
                                        'quantity' => $quantity);
                    $product = array(
                        'po_id' => $po->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                        'price_label' => $product->label,
                        'uom' => $product->stock_unit,
                        'base_unit' => $base_unit,
                        'packing_factor' => $packing_factor,
                        'price' => $promo_price,
                        'quantity' => $quantity,
                        'total' => $total,
                        'foc' => $foc,
                        'sst' => $ssts[$i]
                    );

                    array_push($products, $product);

                }
                 

                DB::table('jocom_purchase_order_details')->insert($products);
            }
            if ($po_status->einv_id != null && $po_status->einv_status == 1) {
                 $f_product_add=json_encode($product_add);
                 if($from_data==$from_data_old && $discount_percent==$discount_percent_old && $manager==$manager_old && $remark==$remark_old && $delivery_date==$delivery_date_old && $product_new==$product_old && $delete_pr=="[]" && $f_product_add=="[]"){
                    $no="no opration";
                 }
                 else{
                PurchaseOrder::po_update_log($from_data,$discount_percent,$manager,$remark,$delivery_date,$updated_by,$id,$from_data_old,$discount_percent_old,$manager_old,$remark_old,$delivery_date_old,$updated_by_old,$product_new,$product_old,$delete_pr,$f_product_add);
                  
                 }
                                           
            }
           

            General::audit_trail('PurchaseOrcerController.php', 'update()', 'Update Purchase Order', Session::get('username'), 'CMS');
            return Redirect::to('/purchase-order')->with('success', 'Puchase Order(ID: '.$po->id.') updated successfully.');
        }

    }
 
    /**
     * Remove the specified PO from storage.
     *
     * @param  int  $id
     * @return Response
     */
public function delete($id) {

        
        $po = PurchaseOrder::find($id);
        

        if ($po == null) {
            return Redirect::to('/purchase-order');
        }
        $grn=DB::table('jocom_warehouse_grn')
                            ->where('po_id', '=', $id)
                            ->where('status','=',1)
                            ->first();

        $gdetail=DB::table('jocom_warehouse_grn_details')
                            ->where('grn_id', '=',$grn->id)
                            ->where('status','=',1)
                            ->get();

          if($gdetail){

          foreach ($gdetail as $value) {
           
            $productidgrn = DB::table('jocom_products')->where('sku','=',$value->sku)->first();
             
             $current_stock = DB::table('jocom_warehouse_products')
                                ->where('product_id', '=', $productidgrn->id)
                                ->select('stockin_hand', 'actual_stock', 'actual_stock_migrated')
                                ->first();
             
            $stockin_hand = $current_stock->stockin_hand-$value->quantity;
            
            DB::table('jocom_warehouse_products')
                ->where('product_id', '=', $productidgrn->id)
                ->update(['stockin_hand' => $stockin_hand]);             
          }
      }
        $po->status =2;
        $po->grn_id = null;
        $po->updated_by = Session::get('username');
        $po->save();
        
        if($po){
        $po_grn = WarehouseGrn::find($grn->id);
        $po_grn->status='2';
        $po_grn->updated_by = Session::get('username');
        $po_grn->save();
        }
        
        $insert_audit = General::audit_trail('PurchaseOrderController.php', 'delete()', 'Delete Purchase Order', Session::get('username'), 'CMS');

        return Redirect::to('/purchase-order')->with('success', 'Puchase Order(ID: '.$po->id.') Cancelled successfully.');
    }

    public function files($loc) {

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);
        $po_id = $id[0];

        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {

            $po = PurchaseOrder::find($po_id); 

            $POView = self::createPOView($po);

            return View::make('purchase-order.po_view')
                    ->with('issuer', $POView['issuer'])
                    ->with('po', $POView['po'])
                    ->with('seller', $POView['seller'])
                    ->with('warehouse', $POView['warehouse'])
                    ->with('products', $POView['products'])
                    ->with('htmlview',true);
        }
    }

    public function download($loc){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);

        $id = explode("#", $loc);
        $po_no = $id[2];
        $po_id = $id[1];
        
        $file_path = array_shift(explode("#", $loc));
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];


        $po = PurchaseOrder::find($po_id); 

        include app_path('library/html2pdf/html2pdf.class.php');

        $POView = self::createPOView($po);

        $response =  View::make('purchase-order.po_view')
                        ->with('issuer', $POView['issuer'])
                        ->with('po', $POView['po'])
                        ->with('seller', $POView['seller'])
                        ->with('warehouse', $POView['warehouse'])
                        ->with('products', $POView['products'])
                        ->with('htmlview',false);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );
        
        return Response::download($file_path, $file_name, $headers);
        
    }
    
    public function ajaxFetchSeller() {
        $sellers = DB::table('jocom_seller')
            ->select('id', 'username', 'company_name', 'email');

        return Datatables::of($sellers)
            ->add_column('Action', '<a id="selectSeller" class="btn btn-primary">Select</a>')
            ->make();
    }

    public function sellerList() {
        return View::make('purchase-order.seller-list');
    }

    public function ajaxFetchWarehouse() {
        $warehouse = WarehouseLocation::activeDataList();

        return Datatables::of($warehouse)
            ->add_column('Action', '<a id="selectWarehouse" class="btn btn-primary">Select</a>')
            ->make();
    }

    public function warehouseList() {
        return View::make('purchase-order.warehouse-list');
    }

    public static function createPOView($po) {

        $issuer = [
            "issuer_name" => $po->from,
            "issuer_address_1" => "9, Jalan TSB 5,",
            "issuer_address_2" => "Taman Industri Sungai Buloh,",
            "issuer_address_3" => "Petaling Jaya,",
            "issuer_address_4" => "47810 Petaling Jaya, Selangor.",
            "issuer_tel" => "Tel: 03-6734 8744",
        ];

        $po_details = [
            'po_id' => $po->id,
            'po_no' => $po->po_no,
            'po_type' => $po->type == 1 ? 'PURCHASE ORDER': 'PURCHASE REQUISITION FORM',
            'po_date' => $po->po_date,
            'payment_terms' => $po->payment_terms . ' Days',
            'delivery_date' => $po->delivery_date,
            'created_by' => $po->created_by,
            'manager' => $po->manager,
            'remark' => $po->remark,
            'discount_percent' => $po->discount_percent,
            'discount_total' => $po->discount_total,
        ];

        $seller = Seller::find($po->seller_id);
        $sellerState = State::find($seller->state)->name;
        $sellerCity = City::find($seller->city)->name;

        $seller_details = [
            'company_name' => $seller->company_name,
            'address1' => $seller->address1,
            'address2' => $seller->address2,
            'postcode' => $seller->postcode,
            'city' => $sellerCity,
            'state' => $sellerState,
            'attn' => $seller->pic_full_name,
            'tel' => $seller->tel_num,
        ];

        $warehouse = WarehouseLocation::find($po->warehouse_location_id);

        $warehouse_details = [
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'postcode' => $warehouse->postcode,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'pic_name' => $warehouse->pic_name,
            'pic_contact' => $warehouse->pic_contact,
        ];

        $product_details = DB::table('jocom_purchase_order_details')
                            ->where('po_id', '=', $po->id)
                            ->orderBy(id,'ASC')
                            ->get();

        return array(
            'issuer' => $issuer,
            'po' => $po_details,
            'seller' => $seller_details,
            'warehouse' => $warehouse_details,
            'products' => $product_details,
        );

    }

    public function pbxIndex() {
        $today = date('Y-m-d');
        return View::make('purchase-order.pbx')->with('today', $today);;
    }

    public function pbxList() {
        $list = DB::table('jocom_purchase_order_pbx')
                    ->select('id', 'file_name', 'status');

        $actionBar = '@if($status==1)<a class="btn btn-primary" title="" data-toggle="tooltip" href="/purchase-order/pbx/download/{{$file_name}}" target="_blank"><i class="fa fa-download"></i></a> <a class="btn btn-success" title="" data-toggle="tooltip" href="#" onclick="complete_account({{$id;}});"><i class="fa fa-check"></i></a>@endif @if($status==0)Contact IT Dept @endif';

        return Datatables::of($list)
            // ->add_column('total', '{{number_format(abs($total_amount - $coupon_amount + $gst_total - $point_amount), 2)}}')
            ->edit_column('status', '@if($status==0){{Initiated}} @elseif($status==1){{Generated}} @elseif($status==2){{Imported}} @endif')
            ->add_column('Action', $actionBar)
            ->make(true);
    }

    public function generateZip() {
        $po_date = Input::get('po_date');
        $start = $po_date . ' 00:00:00';
        $end = $po_date . ' 23:59:59';

        $posDB = DB::table('jocom_purchase_order as order')
                ->join('jocom_purchase_order_details as details', 'order.id', '=', 'details.po_id')
                ->join('jocom_warehouse_location as warehouse', 'order.warehouse_location_id', '=', 'warehouse.id')
                ->join('jocom_seller as seller', 'order.seller_id', '=', 'seller.id')
                ->whereBetween('po_date', [$start, $end])
                ->where('order.status', '=', '1')
                ->select('order.id', 'order.po_no', 'order.po_date', 'order.po_date', 'order.discount_percent', 'order.discount_total', 'details.product_name', 'details.sku', 'details.uom', 'details.base_unit', 'details.packing_factor', 'details.price','details.quantity', 'details.total', 'details.foc', 'details.sst', 'warehouse.id as warehouse_id', 'warehouse.name as warehouse_name', 'warehouse.address_1 as warehouse_address_1', 'warehouse.address_2 as warehouse_address_2', 'warehouse.postcode as warehouse_postcode', 'warehouse.city as warehouse_city', 'warehouse.state as warehouse_state', 'seller.id as seller_id')
                ->orderBy('order.id')
                ->get();

        $po_grouped = array();
        foreach ($posDB as $poDB) {
            $po_grouped[$poDB->id][] = $poDB;
        }

        if (count($po_grouped) == 0) {
            return Response::json(array('status' => 404, 'message' => 'No purchase order.'));
        }

        $text_file_arr = array();

        $zip = new ZipArchive();
        $fname = 'PO_JOCOM_' . date('YmdHis') . '.zip';
        $filename = Config::get('constants.PBX_PO') . '/' . $fname;
        $zip->open($filename, ZipArchive::CREATE);

        $contentArr = array();

        foreach ($po_grouped as $id => $pos) {

            $seller_code = 'SB' . str_pad($pos[0]->seller_id, 5, "0", STR_PAD_LEFT);;

            $text_filename = 'PO_JOCOM_' . $seller_code . '_' . $pos[0]->po_no . '_' . date('YmdHis') . '.txt';
            $file_path = Config::get('constants.PBX_PO') . '/' . $text_filename;
            $file = fopen($file_path, 'w');

            $detail = '';
            $loc = '';
            $seqNo = 1;
            $total = 0.0;

            foreach ($pos as $po) {

                $base_unit = '';
                $uom = '';
                $qty = '';
                $foc_base_unit = '';
                $foc_uom = '';
                $foc_qty = '';
                $unit_cost = '';
                $pack_cost = '';

                if ($po->foc == 1) {
                    $foc_base_unit = $po->base_unit;
                    $foc_uom = $po->uom;
                    $foc_qty = $po->quantity;
                } else {
                    $base_unit = $po->base_unit;
                    $uom = $po->uom;
                    $qty = $po->quantity;
                }

                if ($po->base_unit == 'U') {
                    $unit_cost = number_format($po->price, 4);
                } else {
                    $pack_cost = number_format($po->price, 4);
                }

                $detailArr = array(
                    'recordType' => 'DET',
                    'poNo' => $po->po_no,
                    'seqNo' => $seqNo,
                    'buyerItemCode' => $po->sku,
                    'supplierItemCode' => '',
                    'barCode' => '',
                    'itemDesc' => $po->product_name,
                    'brand' => '',
                    'colourCode' => '',
                    'colourDesc' => '',
                    'sizeCode' => '',
                    'sizeDesc' => '',
                    'packingFactor' => number_format($po->packing_factor, 2, '.', ''),
                    'orderBaseUnit' => $base_unit,
                    'orderUom' => $uom,
                    'orderQty' => $qty,
                    'focBaseUnit' => $foc_base_unit,
                    'focUom' => $foc_uom,
                    'focQty' => $foc_qty,
                    'unitCost' => $unit_cost,
                    'packCost' => $pack_cost,
                    'costDiscountAmount' => '0.0000',
                    'costDiscountPercent' => '',
                    'retailDiscountAmount' => '',
                    'netUnitCost' => $unit_cost,
                    'netPackCost' => $pack_cost,
                    'itemCost' => number_format($po->total, 4, '.', ''),
                    'itemSharedCost' => '',
                    'itemGrossCost' => '',
                    'retailPrice' => '',
                    'itemRetailAmount' => '',
                    'itemRemarks' => '',
                    'vatRate' => '',
                    'vatAmount' => number_format($po->sst, 4, '.', ''),
                    'vatGroup' => '',
                    'vatCode' => ''
                );

                $locArr = array(
                    'recordType' => 'LOC',
                    'poNo' => $po->po_no,
                    'lineSeqNo' => $seqNo,
                    'locationSeqNo' => $seqNo,
                    'locationCode' => $po->warehouse_id,
                    'locationName' => $po->warehouse_name,
                    'locationShipQty' => $qty,
                    'locationFocQty' => $foc_qty,
                    'srcLineRefNo' => '',
                    'locationType' => ''
                );

                $detail = $detail . implode('|', $detailArr) . "\n";
                $loc = $loc . implode('|', $locArr) . "\n";

                $seqNo++;
                $total = $total + floatval($po->total);
            }

            $headerArr = array(
                'recordType' => 'HDR',
                'poNo' => $pos[0]->po_no,
                'docAction' => 'A',
                'actionDate' => date('d/m/Y H:i:s'),
                'poType' => 'SOR',
                'invoicingMode' => '1',
                'poDate' => date_format(date_create($pos[0]->po_date), 'd/m/Y H:i:s'),
                'deliveryDateFrom' => '',
                'deliveryDateTo' => '',
                'expiryDate' => '',
                'buyerCode' => 'tmGrocer',
                'buyerName' => '',
                'buyerAddr1' => '',
                'buyerAddr2' => '',
                'buyerAddr3' => '',
                'buyerAddr4' => '',
                'buyerCity' => '',
                'buyerState' => '',
                'buyerCountryCode' => '',
                'buyerPostalCode' => '',
                'supplierCode' => $seller_code,
                'supplierName' => '',
                'supplierAddr1' => '',
                'supplierAddr2' => '',
                'supplierAddr3' => '',
                'supplierAddr4' => '',
                'supplierCity' => '',
                'supplierState' => '',
                'supplierCountryCode' => '',
                'supplierPostalCode' => '',
                'shipToCode' => $pos[0]->warehouse_id,
                'shipToName' => $pos[0]->warehouse_name,
                'shipToAddr1' => $pos[0]->warehouse_address_1,
                'shipToAddr2' => $pos[0]->warehouse_address_2,
                'shipToAddr3' => '',
                'shipToAddr4' => '',
                'shipToCity' => $pos[0]->warehouse_city,
                'shipToState' => $pos[0]->warehouse_state,
                'shipToCountryCode' => 'MY',
                'shipToPostalCode' => $pos[0]->warehouse_postcode,
                'deptCode' => '',
                'deptName' => '',
                'subDeptCode' => '',
                'subDeptName' => '',
                'creditTermCode' => '',
                'creditTermDesc' => '',
                'totalCost' => number_format($total, 4),
                'additionalDiscountAmount' => '',
                'additionalDiscountPercent' => number_format($pos[0]->discount_percent, 2),
                'netCost' => ($pos[0]->discount_total != '0.00') ? number_format($pos[0]->discount_total, 4) : '',
                'grossProfitMargin' => '',
                'totalSharedCost' => '',
                'totalGrossCost' => '',
                'totalRetailAmount' => '',
                'orderRemarks' => '',
                'periodStartDate' => '',
                'periodEndDate' => '',
                'poSubType2' => '',
                'expenseGl' => '',
                'totalVatAmount' => '',
                'poAmountWithVat' => ($pos[0]->discount_total == '0.00') ? number_format($total, 4) : number_format($pos[0]->discount_total, 4)
            );

            $header = implode('|', $headerArr) . "\n";

            fwrite($file, $header . $detail . $loc);
            fclose($file);
            $zip->addFile($file_path, $text_filename);

            array_push($text_file_arr, $file_path);
            array_push($contentArr, $header . $detail . $loc);
        }

        $zip->close();

        foreach ($text_file_arr as $f) {
            unlink($f);
        }

        $pbx = new PurchaseOrderPbx;
        $pbx->file_name = $fname;
        $pbx->status = 1;
        $pbx->content = implode('&', $contentArr);
        $pbx->save();

        return Response::json(array('status' => 200, 'message' => 'Zip generated successfully.'));
    }

    public function downloadPbx($filename) {
        $file = Config::get('constants.PBX_PO') . '/' . $filename;

        if (is_file($file)) {
            return Response::download($file);
        } else {
            echo "<br>File not exists!";
        }
    }

    public function completePbx() {

        $id = Input::get('complete_id');
            
        $pbx = PurchaseOrderPbx::find($id);
        $pbx->status = 2;
        $pbx->save();

        $file_name = $pbx->file_name;


        $file_path = Config::get('constants.PBX_PO') . '/' . $file_name;

        if(is_file($file_path))
            unlink($file_path);
                  
        return Redirect::to('purchase-order/pbx')->with('success', 'PO PracBix(ID: ' . $id . ') updated successfully.');

    }

    public function products() {
        return View::make('purchase-order.ajaxproduct');
    }

    public function productAjax() {
            
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
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
            ->where('jocom_products.status', '!=', '2')->where('jocom_products.is_foreign_market', '<>', 1);
            
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
            $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
        }
        
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/purchase-order/pbx/prices/{{$id}}">Select</a>')
            ->make();
    }

    public function prices($id) {
        $tempID = $id;

        $product = DB::table('jocom_products')
            ->select('name', 'sku', 'qrcode')
            ->where('id', '=', $tempID)->first();

        return View::make('purchase-order.ajaxprice')->with([
            'id'     => $id,
            'name'   => addslashes($product->name),
            'sku'    => $product->sku,
            'qrcode' => $product->qrcode,
        ]);
    }

    public function priceAjax($id) {

        $product_prices = DB::table('jocom_product_price')
            ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo')
            ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
            ->where('jocom_product_price.product_id', '=', $id)
            ->where('jocom_product_price.status', '=', 1);

        return Datatables::of($product_prices)
            ->edit_column('price', '{{ number_format($price, 2) }}')
            ->edit_column('price_promo', '<input type="number" class="form-control text-right" value="{{ number_format($price_promo, 2) }}">')
            ->add_column('price', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price;
                    }
                
                    return ;
            })
            ->add_column('price_promo', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price_promo * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price_promo;
                    }
            })
            ->add_column('base_unit', '
                <select class="form-control" style="text-align-last: center;" name="base_unit[]" id="base_unit{{$id}}" onchange="enablePacking({{$id}})">
                    <option value="U" selected>Unit</option>
                    <option value="P">Pack/Carton</option>
                </select>
                ')
            ->add_column('packing_factor', '<input type="number" class="form-control" id="packing_factor{{$id}}" disabled>')
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
            ->make();
    }


    //////

    public function editproducts() {
        return View::make('purchase-order.editajaxproduct');
    }

    public function editproductAjax() {
            
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
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
            ->where('jocom_products.status', '!=', '2')->where('jocom_products.is_foreign_market', '<>', 1);
            
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
            ->where("status",1)->first();
        if($SysAdminRegion->region_id != 0){
                $products = $products->where('region_id', '=', $SysAdminRegion->region_id);
        }
        
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/purchase-order/pbx/editprices/{{$id}}">Select</a>')
            ->make();
    }

    public function editprices($id) {
        $tempID = $id;

        $product = DB::table('jocom_products')
            ->select('name', 'sku', 'qrcode')
            ->where('id', '=', $tempID)->first();

        return View::make('purchase-order.editajaxprice')->with([
            'id'     => $id,
            'name'   => addslashes($product->name),
            'sku'    => $product->sku,
            'qrcode' => $product->qrcode,
        ]);
    }

    public function editpriceAjax($id) {

        $product_prices = DB::table('jocom_product_price')
            ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo')
            ->leftJoin('jocom_products', 'jocom_product_price.product_id', '=', 'jocom_products.id')
            ->where('jocom_product_price.product_id', '=', $id)
            ->where('jocom_product_price.status', '=', 1);

        return Datatables::of($product_prices)
            ->edit_column('price', '{{ number_format($price, 2) }}')
            ->edit_column('price_promo', '<input type="number" class="form-control text-right" value="{{ number_format($price_promo, 2) }}">')
            ->add_column('price', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price;
                    }
                
                    return ;
            })
            ->add_column('price_promo', function($product_prices){
                
                    if($product_prices->is_foreign_market == 1){
                        
                        $ExchangeRateMain = DB::table('jocom_exchange_rate AS JER' )
                            ->select('JER.*')
                            ->where('JER.currency_code_from','=','USD')
                            ->where('JER.currency_code_to','=','MYR')
                            ->first();
                        
                        $actual_price = $product_prices->foreign_price_promo * $ExchangeRateMain->amount_to;
                        return $actual_price;
                        
                    }else{
                        return $product_prices->price_promo;
                    }
            })
            ->add_column('base_unit', '
                <select class="form-control" style="text-align-last: center;" name="base_unit[]" id="base_unit{{$id}}" onchange="enablePacking({{$id}})">
                    <option value="U" selected>Unit</option>
                    <option value="P">Pack/Carton</option>
                </select>
                ')
            ->add_column('packing_factor', '<input type="number" class="form-control" id="packing_factor{{$id}}" disabled>')
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
            ->make();
    }

    //////

    public function poList() {
        return View::make('purchase-order.po-list');
    }

    public function ajaxFetchPO()
    {
        $po = DB::table('jocom_purchase_order as po')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->whereIn('po.status', ['1','4'])
                ->whereNull('po.grn_id')
                ->select('po.id', 'po.po_no', 'po.po_date', 'seller.company_name')
                ->orderBy('po.id', 'DESC');

        return Datatables::of($po)
                ->add_column('Action', '<a id="selectPO" class="btn btn-primary">Select</a>')
                ->make();
    }

    public function ajaxFetchPoProducts($po_id)
    {
        return DB::table('jocom_purchase_order_details')
                    ->where('po_id', '=', $po_id)
                    ->get();
    }
    
     // PO Dashbaoard
    public function anyDashboard() // Open view file
    {
 
        return View::make('purchase-order.dashboard');
    }

    // PO Dashbaoard
    public function anyPoReport() // Process data base on the selected date (Product Details)
    {
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
          case 'Daily':
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
            break;
        }
        $po = $query->select('po.po_no',DB::raw('DATE(po.po_date)'),'seller.company_name as company_name','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.price','po_details.quantity','po.status as status');

        return Datatables::of($po)->make();
    }

    // PO Dashbaoard 
    public function anyPoData(){ // Data other than anyPoReport() (The 3 boxes above "Product Details")
        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
  
        switch (Input::get('time_period')) {
                case 'Daily':
                    $query = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),$start_date);
                    $query_percent = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
  
                    $po_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$start_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');

                    $seller_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
                    $seller_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');                          

                    $po_old = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->first();
                    $po_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$start_date)
                                ->first();
                    $po_cancelled=PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_revised=PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_mistake=PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_cancelled_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->first();
                    $po_revised_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->first();
                    $po_mistake_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',$start_date)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->first();
  
                break;
                case 'Weekly':
                    $prv_date = Carbon\Carbon::parse($start_date);
                    $prv_date->addDays(-7);
                    $query = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
                    $query_percent = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
  
                    $po_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$prv_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');

                    $seller_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$prv_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
                    $seller_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
  
                    $po_old = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$prv_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->first();
                    $po_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->first();

                    $po_cancelled = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_revised = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_mistake= PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                     $po_cancelled_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','2')
                                ->first();
                    $po_revised_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','4')
                                ->first();
                    $po_mistake_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','3')
                                ->first();


                break;
                case 'Monthly':
                    $start_date = Carbon\Carbon::parse($start_date);
                    $prv_month = Carbon\Carbon::parse($start_date);
                    $prv_month->addMonth(-1);
  
                    $query = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                        ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
  
                    $query_percent = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
  
                    $po_total_old = $query_percent
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$prv_month->month)
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->distinct('seller.id')
                                ->count('seller.id');

                    $seller_total_old = $query_percent
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$prv_month->month)
                                ->distinct('seller.id')
                                ->count('seller.id');
                    $seller_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->distinct('seller.id')
                                ->count('seller.id');
  
                    $po_old = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$prv_month->month)
                                ->first();
                    $po_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->first();

                     $po_cancelled = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_revised = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_mistake = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_cancelled_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                               ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->first();
                    $po_revised_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                               ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->first();
                    $po_mistake_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                               ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->first();

                break;
                case 'Custom':
                    $query = PurchaseOrder::lists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
                    $query_percent = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
  
                    $po_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->distinct('po.po_no')
                                ->count('po.po_no');

                    $seller_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
                    $seller_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
  
                    $po_old = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->first();
                    $po_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->first();
                    $po_cancelled = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_revised = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_mistake = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                     $po_cancelled_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                 ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->first();
                    $po_revised_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                 ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->first();
                    $po_mistake_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                 ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date)
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->first();

                break;
  
                default:
                    $query = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),\Carbon\Carbon::today());
                    $query_percent = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
    
                    $po_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',\Carbon\Carbon::today())
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',\Carbon\Carbon::today())
                                ->distinct('po.po_no')
                                ->count('po.po_no');

                    $seller_total_old = $query_percent
                                ->where(DB::raw('DATE(po.po_date)'),'<',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');
                    $seller_total_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',$start_date)
                                ->distinct('seller.id')
                                ->count('seller.id');    
    
                    $po_old = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'<',\Carbon\Carbon::today())
                                ->first();
                    $po_new = PurchaseOrder::dashboardlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'<=',\Carbon\Carbon::today())
                                ->first();
                   $po_cancelled = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_revised = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_mistake = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->count('po.po_no');
                    $po_cancelled_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','2')
                                ->distinct('po.po_no')
                                ->first();
                     $po_revised_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','4')
                                ->distinct('po.po_no')
                                ->first();
                    $po_mistake_status = PurchaseOrder::orderlists()->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')
                                ->where(DB::raw('DATE(po.po_date)'),'=',\Carbon\Carbon::today())
                                ->where('po.status','=','3')
                                ->distinct('po.po_no')
                                ->first();
  
  
                break;
            }
        
        $po_cancelled_raw_amount=$po_cancelled_status->po_amount;
        $po_cancelled_raw_quantity=$po_cancelled_status->po_quantity;
        $po_revised_raw_amount=$po_revised_status->po_amount;
        $po_revised_raw_quantity=$po_revised_status->po_quantity;
        $po_mistake_raw_amount=$po_mistake_status->po_amount;
        $po_mistake_raw_quantity=$po_mistake_status->po_quantity;

        $po_total = $query->distinct('po.po_no')->count('po.po_no');
        $seller_total = $query->distinct('seller.id')->count('seller.id');
        $po_quantity_amount = $query
                      ->selectRaw('ifnull(sum(po_details.quantity),0) as po_quantity, ifnull(sum(po_details.total),0) as po_amount')->first();
  
        // PERCENTAGE CALCULATION
        $po_total_diff = $po_total_new - $po_total_old;
        if($po_total_old && $po_total_old >= 1){
            $po_total_percentage = round( ($po_total_diff/$po_total_old*100), 2);
        }
        else {
            $po_total_percentage = $po_total_new;
        }
        $po_quantity_diff = $po_new->po_quantity - $po_old->po_quantity;
        if($po_old->po_quantity && $po_old->po_quantity >= 1){
            $po_quantity_percentage = round( ($po_quantity_diff/$po_old->po_quantity*100), 2);
        }
        else {
          $po_quantity_percentage = $po_new->po_quantity;
        }
  
        $po_amount_diff = $po_new->po_amount - $po_old->po_amount;
        if($po_old->po_amount && $po_old->po_amount >= 1){
            $po_amount_percentage = round( ($po_amount_diff/$po_old->po_amount*100), 2);
        }
        else {
            $po_amount_percentage = $po_new->po_amount;
        }
        $seller_total_diff = $seller_total_new - $seller_total_old;
        if($seller_total_old && $seller_total_old >= 1){
            $seller_total_percentage = round( ($seller_total_diff/$seller_total_old*100), 2);
        }
        else {
            $seller_total_percentage = $seller_total_new;
        }
        // PERCENTAGE CALCULATION
  
        return Response::json(array('success'=>true,'po_total'=>$po_total,'seller_total'=>$seller_total,'po_quantity'=>$po_quantity_amount->po_quantity,'po_amount'=>$po_quantity_amount->po_amount,'total_percentage'=>$po_total_percentage,'seller_percentage'=>$seller_total_percentage,'quantity_percentage'=>$po_quantity_percentage,'amount_percentage'=>$po_amount_percentage,'po_cancelled'=>$po_cancelled,'po_revised'=>$po_revised,'po_mistake'=>$po_mistake,'po_s'=>$po_cancelled_status,'po_cancelled_raw_quantity'=>$po_cancelled_raw_quantity,'po_cancelled_raw_amount'=>$po_cancelled_raw_amount,'po_revised_raw_quantity'=>$po_revised_raw_quantity,'po_revised_raw_amount'=>$po_revised_raw_amount,'po_mistake_raw_quantity'=>$po_mistake_raw_quantity,'po_mistake_raw_amount'=>$po_mistake_raw_amount),200);
    }

    // PO Dashbaoard 
    public function anyPoDashboardData() // Other data than anyPoReport() (The table below "Product Details")
    {
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
            case 'Daily':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
            break;
        }

        $pos = $query->selectRaw('po.id,po.status as po_status, po.po_no, sum(po_details.quantity) as po_quantity, sum(po_details.total) as po_amount, seller.company_name')
            ->groupBy(DB::raw('po.po_no'))
            ->get();
         
        return $pos;
    }

    // PO Dashbaoard 
    public function anyPoDashboardChildData() // Data for anyPoDashboardData(). 
    {
        $po_no = Input::get('po_no');
        $id = Input::get('id');
        $po_details = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where('po.po_no', '=', $po_no)
                        ->select('po.id','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.price','po_details.quantity', 'po_details.total', 'seller.company_name as company_name')->get();
        return $po_details;

    }

    // PO Dashbaoard 
    public function downloadExcel($type) { // Excel for Product Details
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
            case 'Daily':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                    ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
            break;
        }


        $data = $query->select('seller.company_name as company_name','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.base_unit','po_details.packing_factor','po_details.price','po_details.quantity','po_details.total','po.status as status')->get();
        // $data = Post::get()->toArray();
        $data_array[] = array('SELLER NAME','PO NO','SKU','PRODUCT NAME','LABEL','UOM','BASE UNIT','PACKING FACTOR','PRICE','TOTAL QUANTITY','TOTAL AMOUNT','FOC','STATUS');

        foreach ($data as $key => $value) {
            if($value->status==1){
                $status="Active";
            }
            elseif ($value->status==4) {
              $status="Revised";
            }
            else{
               $status="Cancelled"; 
            }
            $data_array[] = array(
                'SELLER NAME' => $value->company_name,
                'PO NO' => $value->po_no,
                'SKU' => $value->sku,
                'PRODUCT NAME' => $value->product_name,
                'LABEL' => $value->price_label,
                'UOM' => $value->uom,
                'BASE UNIT' => $value->base_unit,
                'PACKING FACTOR' => $value->packing_factor,
                'PRICE' => $value->price,
                'TOTAL QUANTITY' => $value->quantity,
                'TOTAL AMOUNT' => $value->total,
                'FOC' => $value->foc,
                'STATUS'=>$status,
            );
        }

        return Excel::create('Product Details', function($excel) use ($data_array,$start_date,$end_date) {

            $excel->sheet('Product Details', function($sheet) use ($data_array,$start_date,$end_date){
                $sheet->cell('A2', function($cell) {$cell->setValue('PO Dashboard - Product Details');   });
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
                
                $sheet->mergeCells('A2:L2');

                $sheet->setCellValue('A3', 'From Date - ');
                $sheet->setCellValue('B3', $start_date);
                $sheet->setCellValue('A4', 'To Date - ');
                $sheet->setCellValue('B4', $end_date);
                $sheet->setCellValue('A5', '');
                $sheet->rows($data_array);
                // $hi=$sheet->getCell('M6')->getValue();
                // print"<pre>";
                // print_r($hi);
                // exit;
                // $sheet->setAllBorders('A6:J10', 'thin');
                $sheet->row(6, function($row) {
                    $row->setBackground('#D9D9D9');

                });
                // $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->setHeight(6,29);
                $sheet->cell('A6:M6',function($cell){
                    $cell->setValignment('center');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                });

                // $sheet->cell('A6:N6', function($cell) {$cell->setFont(array(
                //   'bold' => true
                // ));   });

                $count = 5 + count($data_array);
                $range = "A7:M".$count;

                 $range2 =count($data_array);
                 $m=7;
                 for($i=1;$i<$range2;$i++){
                     $statusif=$sheet->getCell("L".$m)->getValue();

                     if($statusif=="Cancelled"){
                        $colomn[]="L".$m;
                       $sheet->cells("L".$m, function($cells) {
                      $cells->setFontColor('#fc2626');
                      });
                     }
                     $m++;          
                 }

                $sheet->cells('A6:M6', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells($range, function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->setAllBorders('thin');;
            });
        })->setFilename('Product-details'.Carbon\Carbon::now()->timestamp)
        ->download($type);
    }

    // PO Dashbaoard 
    public function exportPdf() // PDF for Product Details
    {
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
            case 'Daily':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                        ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
            break;
        }
        
        $po_details = $query->select('seller.company_name as company_name','po.id','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.base_unit','po_details.packing_factor','po_details.price','po_details.quantity','po_details.total','po_details.foc','po.status as status')->get();
        
        $pdf = PDF::loadView('purchase-order.pdf_view', ['po_details' => $po_details,'start_date' => $start_date,'end_date' => $end_date,'time_period' => $time_period]);
        
        return $pdf->setPaper('a4')->setOrientation('landscape')->setWarnings(false)->download('PO'.Carbon\Carbon::now()->timestamp.'.pdf');
    }

    // PO Dashbaoard 
    public function downloadPoDashExcel($type) // Excel for PO dashboard
    {
      $start_date = Carbon\Carbon::parse(Input::get('start_date'));
      $end_date = Carbon\Carbon::parse(Input::get('end_date'));
      $time_period = Input::get('time_period');
      switch ($time_period) {
        case 'Daily':
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
        case 'Weekly':
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
        break;
        case 'Monthly':
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
        break;
        case 'Custom':
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
        break;

        default:
            $query = PurchaseOrder::orderlists()
                ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
        break;
    }


        $data = $query->select('po.id','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.base_unit','po_details.packing_factor','po_details.price','po_details.quantity','po_details.total','po_details.foc', 'seller.company_name as company_name','po.status as status')
            ->groupBy(DB::raw('po.po_no'))->get();

        return Excel::create('Product Details', function($excel) use ($data, $data_array,$start_date,$end_date,$time_period) {

            $excel->sheet('Product Details', function($sheet) use ($data,$data_array,$start_date,$end_date,$time_period)
            {
                $sheet->mergeCells('A2:D2');

                $sheet->cell('A2', function($cell) {$cell->setValue('PO Dashboard');   });
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

                if($time_period == 'Monthly'){
                    $sheet->setCellValue('A3', 'From Date - ');
                    $sheet->setCellValue('B3', $start_date->format('F').' '.$start_date->year);
                    $sheet->setCellValue('A4', 'To Date - ');
                    $sheet->setCellValue('B4', $end_date->format('F').' '.$end_date->year);
                }
                else {
                    $sheet->setCellValue('A3', 'From Date - ');
                    $sheet->setCellValue('B3', $start_date);
                    $sheet->setCellValue('A4', 'To Date - ');
                    $sheet->setCellValue('B4', $end_date);
                }

                $sheet->setCellValue('A5', '');
                $sheet->setCellValue('A6', '');

                $heading_row_no = 8;
                $empty_row_no = 7;
                     $data_array[] = array('SELLER NAME','PO NO','SKU','PRODUCT NAME','LABEL','UOM','BASE UNIT','PACKING FACTOR','PRICE','TOTAL QUANTITY','TOTAL AMOUNT','FOC','STATUS');

                  foreach ($data as $key => $value) {
                     //status 
                    if($value->status=="1"){
                        $status="Active";
                    }
                    elseif($value->status=="4"){
                    $status="Revised";
                    }
                    if($value->status=="2"){
                      $status="Cancelled";  
                    }
                    $po_details = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where('po.id', '=', $value->id)
                        ->select('po.id','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.base_unit','po_details.packing_factor','po_details.price','po_details.quantity','po_details.total','po_details.foc','seller.company_name as company_name')->get();
                    foreach ($po_details as $key => $po_detail) {
                        $data_array[] = array(
                            'SELLER NAME' => $po_detail->company_name,
                            'PO NO' => $po_detail->po_no,
                            'SKU' => $po_detail->sku,
                            'PRODUCT NAME' => $po_detail->product_name,
                            'LABEL' => strip_tags($po_detail->price_label),
                            'UOM' => $po_detail->uom,
                            'BASE UNIT' => $po_detail->base_unit,
                            'PACKING FACTOR' => $po_detail->packing_factor,
                            'PRICE' => $po_detail->price,
                            'TOTAL QUANTITY' => $po_detail->quantity,
                            'TOTAL AMOUNT' => $po_detail->total,
                            'FOC' => $po_detail->foc,
                            'STATUS'=>$status,
                        );
                    }
                    
                   
                    $table_heading_no = $heading_row_no-1;
                  
                
                    // $sheet->setCellValue('A'.$table_heading_no,'Po No : '.$value->po_no.'| Seller Name : '.strtoupper($value->company_name).'|Status:'.$status);
                    if($status=="Cancelled"){
                    $sheet->cell('A'.$table_heading_no, function($cell) {$cell->setFont(array(
                            'size' => '15',
                            'bold' => true,
                            'color' => array(
        'rgb' => 'ff0000'
    )
                        ));   
                    });
                   }else{
                     $sheet->cell('A'.$table_heading_no, function($cell) {$cell->setFont(array(
                            'size' => '15',
                            'bold' => true
                        ));   
                    });
                   }
                    $sheet->mergeCells('A'.$table_heading_no.':M'.$table_heading_no);

                    $sheet->rows($data_array);

                    $sheet->row($heading_row_no, function($row) {
                        $row->setBackground('#D9D9D9');
                    });
                    // $sheet->getRowDimension($heading_row_no)->setRowHeight(20);
                    $sheet->setHeight($heading_row_no,29);
                    $sheet->cell('A'.$heading_row_no.':M'.$heading_row_no,function($cell){
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A'.$heading_row_no.':M'.$heading_row_no, function($cell) {$cell->setFont(array(
                            'bold' => true
                        ));   
                    });

                    $record_count = $heading_row_no + count($data_array);
                    $range_heading = "A".$heading_row_no.":M".$heading_row_no;
                    $data_row_no = $heading_row_no + 1;
                    $range = "A".$data_row_no.":M".$record_count;
                    // $range = 'A7:N10';

                    $sheet->cells($range_heading, function($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // $sheet->cells($range, function($cells) {
                    //     $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    // });

                    // $heading_row_no = $heading_row_no+count($data_array)+2;
                    // // $record_count = $record_count + $record_count+2;
                    // $empty_row_no = $heading_row_no - 1;
                    // $sheet->setCellValue('A'.$empty_row_no, '');

                    unset($data_array);
                }


            });
        })->setFilename('Product-details-dashboard'.Carbon\Carbon::now()->timestamp)
        ->download($type);
    }

    // PO Dashbaoard 
    public function exportPoDashPdf() // PDF for PO Dashboard
    {
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
            case 'Daily':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                        ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                        ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists()
                    ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id');
            break;
        }

        $pos = $query->selectRaw('po.id, po.po_no, sum(po_details.quantity) as po_quantity, sum(po_details.total) as po_amount, seller.company_name,po.status as status')
        ->groupBy(DB::raw('po.po_no'))
        ->get();

        // $pos = PurchaseOrder::lists()
        //             ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')
        //             ->selectRaw('po.id, po.po_no, sum(po_details.quantity) as po_quantity, sum(po_details.total) as po_amount, seller.company_name')
        //             ->groupBy(DB::raw('po.po_no'))
        //             ->get();
        $pdf = PDF::loadView('purchase-order.pdf_view_po_dash',['pos' => $pos,'start_date' => $start_date,'end_date' => $end_date,'time_period' => $time_period]);
        return $pdf->setPaper('a4')->setOrientation('landscape')->setWarnings(false)->download('po-dashboard'.Carbon\Carbon::now()->timestamp.'.pdf');

    }

   public function UploadSPO(){
        
        $id = Input::get('signpoid');
        $po=PurchaseOrder::find($id);
        $store=array();
         if($po!=null){
        if (Input::hasFile('signedpo')) {
             
            $files =Input::file('signedpo');
            $destination_path = 'public/signedpo/';

            if(Input::file('signedpo')){
            foreach ($files as $file) {
            $attach =  date("Ymdhis")."-".uniqid();
            $filename = $attach. '_' .$file->getClientOriginalName();
            $file->move($destination_path,$filename);
            $input[]= $filename;
            }
            $final_path=json_encode($input);


            if($po->sign_po_path!=null){
                $old_path=json_decode($po->sign_po_path);
                $finalpath=array_merge($old_path ,$input);
                $result=json_encode($finalpath);
                $po->sign_po_path=$result;  
            }
            else{
                $po->sign_po_path=$final_path;   
            }
            $po->sign_po_status="1";
            $po->save();
             
            return Redirect::to('/purchase-order')->with('success', 'Purchase Order(PO Number: '. $po->po_no .') Signed PO Upload successfully.');
            }
            else{
            return Redirect::back()->with('message', 'Error');

                 }
              }

            }
    }
   public function signedpdf($filename){

     if (file_exists('public/signedpo/'.$filename)) {

        $headers = array('Content-Type: application/pdf',);
        return Response::download('public/signedpo/'.$filename,$filename, $headers);
        exit;
        }
    }

    public function deletesignedpdf($filename,$id){

     $po=PurchaseOrder::find($id);
     $destination_path = 'public/signedpo/';
     $decode=json_decode($po->sign_po_path);
     $array=array_diff($decode,[$filename]);
     $encode=array_values($array);
     $final_path=json_encode($encode);

     if(file_exists($destination_path. '/' . $filename)) { 
        $po->sign_po_path=$final_path;
        if($po->save()){
            File::delete($destination_path. '/' . $filename); 
            return Redirect::back()->with('success', 'PO Deleted successfully ');  
        }
        else{
        return Redirect::back()->with('message', 'Something went wrong');
        }
        }else{
        return Redirect::back()->with('message', 'File Not Found');
        } 
     
    }
    
    public function pdfmerge(){

        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
            case 'Daily':
                $query = PurchaseOrder::orderlists()                  
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Weekly':
                $query = PurchaseOrder::orderlists()
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;
            case 'Monthly':
                $query = PurchaseOrder::orderlists()
                        ->where(DB::raw('MONTH(po.po_date)'),'=',$start_date->month)
                        ->where(DB::raw('YEAR(po.po_date)'),'=',$start_date->year);
            break;
            case 'Custom':
                $query = PurchaseOrder::orderlists()
                        ->where(DB::raw('DATE(po.po_date)'),'>=',$start_date)
                        ->where(DB::raw('DATE(po.po_date)'),'<=',$end_date);
            break;

            default:
                $query = PurchaseOrder::orderlists();
            break;
        }
        
        $po_details = $query->select('po.id','po.sign_po_path as path')->get();
        
        $destination_path = 'public/signedpo';
        $pdf = new PDFMerger();
         if(empty($po_details)){
          return Redirect::back()->with('message', 'No PDFs to merge.');
          }else{
        foreach ($po_details as $key => $value) {
        $path=json_decode($value->path);
        $val[]=$path;
        $resul=array_filter($val); 
        }
        }
        if(empty($resul)){
          return Redirect::back()->with('message', 'No PDFs to merge.');
        }
        else{
         foreach ($resul as $key =>$file) {
             foreach ($file as $keys => $values) {
               $pdf->addPDF($destination_path. '/' . $values, 'all');
             }
          }   
        }           
         $pdf->merge('download', "signedpdf.pdf");  
     }
   

    
}
?>