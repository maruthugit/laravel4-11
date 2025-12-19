<?php

class StockRequisitionController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing on datatable.
     *
     * @return Response
     */
    public function anyLists() { 

        $st = DB::table('jocom_stock_requisition')
                ->join('jocom_warehouse_location as warehouse', 'jocom_stock_requisition.warehouse_id', '=', 'warehouse.id')
                ->where('jocom_stock_requisition.status', '=', '1')
                ->select('jocom_stock_requisition.id', 'jocom_stock_requisition.st_no','warehouse.name as warehouse_name', 'jocom_stock_requisition.platform as platforms', 'jocom_stock_requisition.delivery_date','jocom_stock_requisition.campaign_end');

        return Datatables::of($st)
                        ->add_column('Action', function ($p) {

                            $file = (Config::get('constants.STOCK_TRANSFER_PDF_FILE_PATH') . '/' . urlencode($p->st_no) . '.pdf')."#".($p->id).'#'.$p->st_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '
                                <a class="btn btn-success" title="" data-toggle="tooltip" href="/stock-requisition/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/stock-requisition/edit/'.$p->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deleteST" class="btn btn-danger" title="" data-toggle="tooltip" data-value="'.$p->id.'" href="/stock-requisition/delete/'.$p->id.'"><i class="fa fa-remove"></i></a>
                                ';
                        })
                        ->make();
    }

    /**
     * Display a listing of the stock transfer.
     *
     * @return Response
     */
    public function anyIndex() {
        return View::make('stock-requisition.index');
    }

    /**
     * Show the form for creating a new stock transfer.
     *
     * @return Response
     */
    public function anyCreate() {
        $platforms=DB::table('jocom_stockrequisition_plaforms')->select('id','platform_name')->where('status','=',1)->get();
        return View::make('stock-requisition.create')->with(array('platforms'=>$platforms));
    }

     /**
     * Store a newly created stock transfer in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $validator = Validator::make(Input::all(), StockRequisition::$rules, StockRequisition::$message);

        if ($validator->passes()) {

            $delivery_date = Input::get('delivery_date');
            $campaign_end = Input::get('campaign_end');
            $warehouse_id = Input::get('warehouse_id');
            $platform = Input::get('platform');
            $remark = Input::get('total_remarks');

            try {
                DB::beginTransaction();

                $stock_transfer = new StockRequisition;

                $running = DB::table('jocom_running_po')->where('value_key', '=', 'sr_no')->first();
                if ($running->year == date('Y')) {
                    $count = $running->count + 1;
                } else {
                    $count = 1;
                }
                DB::table('jocom_running_po')->where('value_key', '=', 'sr_no')
                    ->update(array('count' => $count, 'year' => date('Y')));
                    
                $stock_transfer->st_no = 'STRE1'.str_pad($count, 4, "0", STR_PAD_LEFT).'-'.date('m').'-'.date('Y');
                $stock_transfer->delivery_date = $delivery_date;
                $stock_transfer->campaign_end = $campaign_end;
                $stock_transfer->warehouse_id = $warehouse_id;
                $stock_transfer->platform =$platform;
                $stock_transfer->remark = Input::get('total_remarks');
                $stock_transfer->status = 1;
                $stock_transfer->created_by = Session::get('username');
                $stock_transfer->updated_by = Session::get('username');

                $products = array();

                if($stock_transfer->save())
                {

                    $product_ids = Input::get('product_id');
                    $quantitys = Input::get('quantity');
                     $expiry_date = Input::get('expiry_date');

                    $details = array();

                    for ($i = 0; $i < count($product_ids); $i++) { 
                        array_push($details, array('st_id' => $stock_transfer->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i],'expiry_date' => $expiry_date[$i]));
                    }

                    DB::table('jocom_stock_requisition_details')->insert($details);

                } 

            } catch(Exception $ex) {
                $isError = true;
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/stock-requisition')->with('success', 'Stock Requisition(Stock Requisition Number: '. $stock_transfer->st_no .') added successfully.');
                }
            }

        } else {
            return Redirect::back()
                    ->withInput()
                    ->withErrors($validator);
        }
        
    }

    /**
     * Show the form for editing the specified stock transfer.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id) {

        $stock_transfer = StockRequisition::get($id);

        if ($stock_transfer == null) {
            return Redirect::to('/stock-requisition');
        }

        $details = DB::table('jocom_stock_requisition_details as details')
                        ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                        ->where('st_id', '=', $id)
                        ->select('details.product_id', 'products.sku', 'products.name', 'details.quantity','details.expiry_date')
                        ->get();
        $platforms=DB::table('jocom_stockrequisition_plaforms')->select('id','platform_name')->where('status','=',1)->get();
        $customer=Customer::select('id')->where('username','=',$stock_transfer->customer_name)->first();
        $platform_select=DB::table('jocom_stockrequisition_plaforms')->select('id','platform_name')->where('platform_name','=',$stock_transfer->platform)->first();
        if($customer){
            $is_customer="1";
        }else{
             $is_customer="0";
        }
        if($platform_select){
            $is_platform="1";
        }else{
             $is_platform="0";
        }
        return View::make('stock-requisition.edit')->with(array(
            'stock_transfer' => $stock_transfer,
            'details' => $details,
            'platforms'=>$platforms,
            'customer'=>$is_customer,
            'is_platform'=>$is_platform,
        ));
    }

    /**
     * Update the specified stock transfer in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id) {

        $stock_transfer = StockRequisition::find($id);

        if ($stock_transfer == null) {
            return Redirect::to('/stock-requisition');
        }
           
        $platform = Input::get('platform');
        $stock_transfer->delivery_date = Input::get('delivery_date');
        $stock_transfer->campaign_end = Input::get('campaign_end');
        $stock_transfer->platform =$platform;
        $stock_transfer->warehouse_id = Input::get('warehouse_id');
        $stock_transfer->remark =Input::get('total_remarks');
        $stock_transfer->updated_by = Session::get('username');

        if($stock_transfer->save()) {

            $product_ids = Input::get('product_id');
            $quantitys = Input::get('quantity');
            $expiry_date = Input::get('expiry_date');
            $product_count = count($product_ids);

            $existing_products = DB::table('jocom_stock_requisition_details')
                                    ->where('st_id', '=', $id)
                                    ->get();

            foreach ($existing_products as $ep) {
                if (in_array($ep->product_id, $product_ids)) {
                    $index = array_search($ep->product_id, $product_ids);

                    // exclude if product and quantity remain the same
                    if ($ep->quantity == $quantitys[$index] && $ep->remark == $expiry_date[$index]) {
                        unset($product_ids[$index]);
                        unset($quantitys[$index]);
                        unset($expiry_date[$index]);
                
                    } else {

                        DB::table('jocom_stock_requisition_details')
                            ->where('id', '=', $ep->id)->delete();
                    }
                } else {

                    DB::table('jocom_stock_requisition_details')
                            ->where('id', '=', $ep->id)->delete();
                }
            }

            $details = array();

            for ($i = 0; $i < $product_count; $i++) { 

                if (isset($product_ids[$i])) {
                    array_push($details, array('st_id' => $stock_transfer->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i],'expiry_date' => $expiry_date[$i]));
                }
            }

            if (count($details) > 0) {
                DB::table('jocom_stock_requisition_details')->insert($details);
            }

            General::audit_trail('StockRequisitionController.php', 'update()', 'Update Stock Transfer', Session::get('username'), 'CMS');
            return Redirect::to('/stock-requisition')->with('success', 'Stock Transfer(ID: '.$stock_transfer->id.') updated successfully.');
        }

    }
 
    /**
     * Remove the specified stock transfer

      from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id) {

        $stock_transfer = StockRequisition::find($id);

        if ($stock_transfer == null) {
            return Redirect::to('/stock-requisition');
        }
        $stock_transfer->status = 2;
        $stock_transfer->updated_by = Session::get('username');
        $stock_transfer->save();
        
        General::audit_trail('StockRequisitionController.php', 'delete()', 'Delete Stock Transfer', Session::get('username'), 'CMS');

        return Redirect::to('/stock-requisition')->with('success', 'Stock Transfer(ID: '.$stock_transfer->id.') deleted successfully.');
    }

    public function anyFiles($loc) {

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);
        $st_id = $id[0];

        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {

            $st = StockRequisition::find($st_id);

            $STView = self::createSTView($st);

            return View::make('stock-requisition.st_view')
                    ->with('st', $STView['st'])
                    ->with('seller', $STView['seller'])
                    ->with('warehouse', $STView['warehouse'])
                    ->with('products', $STView['products'])
                    ->with('htmlview',true);
        }
    }

    public function anyDownload($loc){

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);

        $id = explode("#", $loc);
        $st_no = $id[2];
        $st_id = $id[1];
        
        $file_path = array_shift(explode("#", $loc));
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];


        $st = StockRequisition::find($st_id); 

        include app_path('library/html2pdf/html2pdf.class.php');

        $STView = self::createSTView($st);

        $response =  View::make('stock-requisition.st_view')
                        ->with('st', $STView['st'])
                        ->with('seller', $STView['seller'])
                        ->with('warehouse', $STView['warehouse'])
                        ->with('products', $STView['products'])
                        ->with('htmlview',false);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );
        
        return Response::download($file_path, $file_name, $headers);
        
    }

    public static function createSTView($st) {
       
        $warehouse = WarehouseLocation::find($st->warehouse_id);
        $warehouse_details = [
            'name' => $warehouse->name,
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'postcode' => $warehouse->postcode,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'country' => $warehouse->country,
            'pic_name' => $warehouse->pic_name,
            'pic_contact' => $warehouse->pic_contact,
        ];

        $st_details = [
            'st_id' => $st->id,
            'st_no' => $st->st_no,
            'st_date' => date_format(date_create($st->created_at), 'Y-m-d'),
            'delivery_date' => $st->delivery_date,
            'campaign_end' => $st->campaign_end,
            'remark' => $st->remark,
            'platform' => $st->platform,
            'created_by' => $st->created_by,
        ];

        $product_details = DB::table('jocom_stock_requisition_details as details')
                            ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                            ->where('details.st_id', '=', $st->id)
                            ->select('details.*', 'products.sku', 'products.name')
                            ->get();

        return array(
            'st' => $st_details,
            'warehouse' => $warehouse_details,
            'products' => $product_details,
        );

    }
    public function anyAddress(){
        $user=Input::get('id');
        $customer=Customer::select('address1','address2','postcode','city','state','mobile_no')->where('id','=',$user)->first();
        return $customer;
    }
    public function anyProducts() {
        return View::make('stock-requisition.ajaxproduct');
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
    public function anyPlatforms(){
         $platforms=DB::table('jocom_stockrequisition_plaforms')->select('*')->whereIn('status',['0','1'])->get();
        return View::make('stock-requisition.platform')->with('list',$platforms);
    }
    public function anyCreateplatform(){
        
        $platforms=DB::table('jocom_stockrequisition_plaforms')->select('*')->where('status','=',1)->get();
        
        return View::make('stock-requisition.add_platform')->with('platforms',$platforms);
    }
    public function anyStoreplatform(){
        
     $platformname=Input::get('platform_name');
     $platform_status=Input::get('status');
     
     $platform=DB::table('jocom_stockrequisition_plaforms')->insert(['platform_name'=>$platformname,'status'=>$platform_status,'inserted_by'=>Session::get('username')]);
     if($platform){
         return Redirect::to('/stock-requisition/platforms')->with('success', 'Platform Added successfully.');
     }else{
        return Redirect::to('/stock-requisition/createplatform')->with('message', 'Something went wrong!try again'); 
     }
    
    }
    public function anyDeleteplatform($id)
    {
        $platforms = DB::table('jocom_stockrequisition_plaforms')->where('id','=',$id)->first();
        if($platforms){
        $platform = DB::table('jocom_stockrequisition_plaforms')->where('id','=',$id)->update(['status'=>2,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);
        
            if($platform){
             return Redirect::to('/stock-requisition/platforms')->with('message','(ID: '.$id.') deleted successfully.');   
            }else{
                return Redirect::to('/stock-requisition/platforms')->with('message', 'Something went wrong!try again'); 
            }
        }else{
             return Redirect::to('/stock-requisition/platforms')->with('message', 'ID not found!');
        }
        
    }
    public function anyPlatformedit($id){
        $platforms = DB::table('jocom_stockrequisition_plaforms')->select('*')->where('id','=',$id)->first();
        
        return View::make('stock-requisition.platform_edit')->with('list',$platforms);
    }
    public function anyPlatformupdate($id){
        
     $platformname=Input::get('platform_name');
     $platform_status=Input::get('status');
     $platform = DB::table('jocom_stockrequisition_plaforms')->where('id','=',$id)->update(['platform_name'=>$platformname,'status'=>$platform_status,'updated_by'=>Session::get('username'),'updated_at'=> date('Y-m-d h:i:s')]);

        if($platform){
             return Redirect::to('/stock-requisition/platforms')->with('success','(ID: '.$id.') Updated successfully.');   
            }else{
                return Redirect::to('/stock-requisition/platforms')->with('message', 'Something went wrong!try again'); 
            }
    }
}
?>