<?php

class StockTransferNewController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing on datatable.
     *
     * @return Response
     */
    public function anyLists() { 

        $st = DB::table('jocom_stock_transfer')
                ->leftjoin('jocom_seller as seller', 'jocom_stock_transfer.seller_id', '=', 'seller.id')
                ->where('status', '=', '1')
                ->select('jocom_stock_transfer.id', 'st_no', 'seller.company_name as seller', 'delivery_date');

        return Datatables::of($st)
                        ->edit_column('seller',function($row){
                            $stockt=StockTransfer::find($row->id);
                            if($row->seller==null){
                                $seller=$stockt->newseller_name;
                            }else{
                                $seller=$row->seller;  
                            }
                            return $seller;
                        })
                        ->add_column('Action', function ($p) {

                            $file = (Config::get('constants.STOCK_TRANSFER_PDF_FILE_PATH') . '/' . urlencode($p->st_no) . '.pdf')."#".($p->id).'#'.$p->st_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '
                                <a class="btn btn-success" title="" data-toggle="tooltip" href="/stock-transfer/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/stock-transfer/edit/'.$p->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deleteST" class="btn btn-danger" title="" data-toggle="tooltip" data-value="'.$p->id.'" href="/stock-transfer/delete/'.$p->id.'"><i class="fa fa-remove"></i></a>
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
        return View::make('stock-transfer.index');
    }

    /**
     * Show the form for creating a new stock transfer.
     *
     * @return Response
     */
    public function anyCreate() {   
        return View::make('stock-transfer.create');
    }

     /**
     * Store a newly created stock transfer in storage.
     *
     * @return Response
     */
    public function anyStore() {
        $validator = Validator::make(Input::all(), StockTransfer::$rules, StockTransfer::$message);

        if ($validator->passes()) {

            $delivery_date = Input::get('delivery_date');
            $seller_id = Input::get('seller_id');
            $warehouse_id = Input::get('warehouse_id');
            $remark = Input::get('remark');
            // add new address
            $address_check=Input::get('address_check');
           ////
            try {
                DB::beginTransaction();

                $stock_transfer = new StockTransfer;

                $running = DB::table('jocom_running_po')->where('value_key', '=', 'st_no')->first();
                if ($running->year == date('Y')) {
                    $count = $running->count + 1;
                } else {
                    $count = 1;
                }
                DB::table('jocom_running_po')->where('value_key', '=', 'st_no')
                    ->update(array('count' => $count, 'year' => date('Y')));
                    
                $stock_transfer->st_no = 'STRE1'.str_pad($count, 4, "0", STR_PAD_LEFT).'-'.date('m').'-'.date('Y');
                $stock_transfer->delivery_date = $delivery_date;
                // add new address
                if($address_check=="1"){
                $stock_transfer->newseller_name=Input::get('new_seller_name');
                $stock_transfer->address_1=Input::get('address_1');
                $stock_transfer->address_2=Input::get('address_2');
                $stock_transfer->postcode=Input::get('postcode');
                $stock_transfer->city=Input::get('city');
                $stock_transfer->state=Input::get('state');
                $stock_transfer->tel_num=Input::get('tel_num');
                $stock_transfer->attn=Input::get('attn');
                $seller_id=0;
                }else{
                $stock_transfer->newseller_name=null;
                $stock_transfer->address_1=null;
                $stock_transfer->address_2=null;
                $stock_transfer->postcode=null;
                $stock_transfer->city=null;
                $stock_transfer->state=null;
                $stock_transfer->tel_num=null;
                $stock_transfer->attn=null;
                $seller_id=$seller_id;  
                }
                //
                $stock_transfer->seller_id = $seller_id;
                $stock_transfer->warehouse_id = $warehouse_id;
                $stock_transfer->remark = Input::get('remarks');
                $stock_transfer->status = 1;
                $stock_transfer->created_by = Session::get('username');
                $stock_transfer->updated_by = Session::get('username');

                $products = array();

                if($stock_transfer->save())
                {

                    $product_ids = Input::get('product_id');
                    $quantitys = Input::get('quantity');

                    $details = array();

                    for ($i = 0; $i < count($product_ids); $i++) { 
                        array_push($details, array('st_id' => $stock_transfer->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i]));
                    }

                    DB::table('jocom_stock_transfer_details')->insert($details);

                } 

            } catch(Exception $ex) {
                $isError = true;
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/stock-transfer')->with('success', 'Stock Transfer(Stock Transfer Number: '. $stock_transfer->st_no .') added successfully.');
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

        $stock_transfer = StockTransfer::get($id);

        if ($stock_transfer == null) {
            return Redirect::to('/stock-transfer');
        }

        $details = DB::table('jocom_stock_transfer_details as details')
                        ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                        ->where('st_id', '=', $id)
                        ->select('details.product_id', 'products.sku', 'products.name', 'details.quantity')
                        ->get();
        //addded for new address
        if($stock_transfer->seller_id=="0"){
          $newseller="0";   
        }else{
          $newseller="1";     
        }
       //end new address
        return View::make('stock-transfer.edit')->with(array(
            'stock_transfer' => $stock_transfer,
            'details' => $details,
            'newseller'=>$newseller,//added for new address
        ));
    }

    /**
     * Update the specified stock transfer in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id) {

        $stock_transfer = StockTransfer::find($id);
        $address_check=Input::get('address_check'); // add new address
        if ($stock_transfer == null) {
            return Redirect::to('/stock-transfer');
        }
       //add new address
        if($address_check=="1"){
            $stock_transfer->newseller_name=Input::get('new_seller_name');
            $stock_transfer->address_1=Input::get('address_1');
            $stock_transfer->address_2=Input::get('address_2');
            $stock_transfer->postcode=Input::get('postcode');
            $stock_transfer->city=Input::get('city');
            $stock_transfer->state=Input::get('state');
            $stock_transfer->tel_num=Input::get('tel_num');
            $stock_transfer->attn=Input::get('attn');
            $seller_id=0;
        }else{
            $stock_transfer->newseller_name=null;
            $stock_transfer->address_1=null;
            $stock_transfer->address_2=null;
            $stock_transfer->postcode=null;
            $stock_transfer->city=null;
            $stock_transfer->state=null;
            $stock_transfer->tel_num=null;
            $stock_transfer->attn=null;
           $seller_id=Input::get('seller_id');  
        }
        
        //
        $stock_transfer->delivery_date = Input::get('delivery_date');
        $stock_transfer->seller_id = $seller_id;//check add new address
        $stock_transfer->warehouse_id = Input::get('warehouse_id');
        $stock_transfer->remark = Input::get('remark');
        $stock_transfer->updated_by = Session::get('username');

        if($stock_transfer->save()) {

            $product_ids = Input::get('product_id');
            $quantitys = Input::get('quantity');

            $product_count = count($product_ids);

            $existing_products = DB::table('jocom_stock_transfer_details')
                                    ->where('st_id', '=', $id)
                                    ->get();

            foreach ($existing_products as $ep) {
                if (in_array($ep->product_id, $product_ids)) {
                    $index = array_search($ep->product_id, $product_ids);

                    // exclude if product and quantity remain the same
                    if ($ep->quantity == $quantitys[$index]) {
                        unset($product_ids[$index]);
                        unset($quantitys[$index]);
                    } else {

                        DB::table('jocom_stock_transfer_details')
                            ->where('id', '=', $ep->id)->delete();
                    }
                } else {

                    DB::table('jocom_stock_transfer_details')
                            ->where('id', '=', $ep->id)->delete();
                }
            }

            $details = array();

            for ($i = 0; $i < $product_count; $i++) { 

                if (isset($product_ids[$i])) {
                    array_push($details, array('st_id' => $stock_transfer->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i]));
                }
            }

            if (count($details) > 0) {
                DB::table('jocom_stock_transfer_details')->insert($details);
            }

            General::audit_trail('StockTransferController.php', 'update()', 'Update Stock Transfer', Session::get('username'), 'CMS');
            return Redirect::to('/stock-transfer')->with('success', 'Stock Transfer(ID: '.$stock_transfer->id.') updated successfully.');
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

        $stock_transfer = StockTransfer::find($id);

        if ($stock_transfer == null) {
            return Redirect::to('/stock-transfer');
        }
        $stock_transfer->status = 2;
        $stock_transfer->updated_by = Session::get('username');
        $stock_transfer->save();
        
        General::audit_trail('StockTransferController.php', 'delete()', 'Delete Stock Transfer', Session::get('username'), 'CMS');

        return Redirect::to('/stock-transfer')->with('success', 'Stock Transfer(ID: '.$stock_transfer->id.') deleted successfully.');
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

            $st = StockTransfer::find($st_id);

            $STView = self::createSTView($st);

            return View::make('stock-transfer.st_view')
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


        $st = StockTransfer::find($st_id); 

        include app_path('library/html2pdf/html2pdf.class.php');

        $STView = self::createSTView($st);

        $response =  View::make('stock-transfer.st_view')
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
         if($st->seller_id==0){
        $seller_details = [
            'company_name' => $st->newseller_name,
            'address_1' => $st->address_1,
            'address_2' => $st->address_2,
            'postcode' => $st->postcode,
            'city' => $st->city,
            'state' => $st->state,
            'attn' => $st->attn,
            'tel' => $st->tel_num,
        ];
       }else{
         $seller = Seller::find($st->seller_id);
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
       }
        

        $st_details = [
            'st_id' => $st->id,
            'st_no' => $st->st_no,
            'st_date' => date_format(date_create($st->created_at), 'Y-m-d'),
            'delivery_date' => $st->delivery_date,
            'remark' => $st->remark,
            'created_by' => $st->created_by,
        ];

        $product_details = DB::table('jocom_stock_transfer_details as details')
                            ->join('jocom_products as products', 'details.product_id', '=', 'products.id')
                            ->where('details.st_id', '=', $st->id)
                            ->select('details.*', 'products.sku', 'products.name')
                            ->get();

        return array(
            'st' => $st_details,
            'warehouse' => $warehouse_details,
            'seller' => $seller_details,
            'products' => $product_details,
        );

    }
}
?>