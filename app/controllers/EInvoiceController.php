<?php

class EInvoiceController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function lists() { 

        $einv = EInvoice::lists();

        return Datatables::of($einv)
                        ->add_column('Action', function ($p) {

                            $file = (Config::get('constants.EINV_PDF_FILE_PATH') . '/' . urlencode($p->einv_no) . '.pdf')."#".($p->id).'#'.$p->einv_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '
                                <a class="btn btn-success" title="" data-toggle="tooltip" href="/einvoice/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/einvoice/edit/'.$p->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deletePO" class="btn btn-danger" title="" data-toggle="tooltip" data-value="'.$p->id.'" href="/einvoice/delete/'.$p->id.'"><i class="fa fa-remove"></i></a>
                                <a id="generatePbx" class="btn btn-primary" title="" data-toggle="tooltip" data-value="'.$p->id.'" href="/einvoice/generate-pbx/'.$p->id.'">Generate PracBix</a>
                                
                            ';
                        })
                        ->make();
    }

    /**
     * Display a listing of the purchase order.
     *
     * @return Response
     */
    public function index() {
        return View::make('einvoice.index');
    }

    public function create($grn_id) {

        $grn = DB::table('jocom_warehouse_grn as grn')
                ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                ->join('jocom_warehouse_location as loc', 'grn.warehouse_loc_id', '=', 'loc.id')
                ->join('jocom_seller as seller', 'grn.seller_id', '=', 'seller.id')
                ->where('grn.id', '=', $grn_id)
                ->first();

        $grn_details = DB::table('jocom_warehouse_grn_details')
                        ->where('grn_id', '=', $grn_id)
                        ->get();

        $subtotal = 0.00;
        $sst_total = 0.00;
        $total = 0.00;
        foreach ($grn_details as $detail) {
            $detail->subtotal_sst = $detail->total + $detail->sst;
            $subtotal = $subtotal + $detail->total;
            $sst_total = $sst_total + $detail->sst;
            $total = $total + $detail->subtotal_sst;
        }

        return View::make('einvoice.create')->with(array(
            'grn' => $grn,
            'grn_details' => $grn_details,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'sst_total' => number_format($sst_total, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
        ));
    }

     /**
     * Store a newly created PO in storage.
     *
     * @return Response
     */
    public function store() {
        $validator = Validator::make(Input::all(), EInvoice::$rules);

        if ($validator->passes()) {

            $grn_id = Input::get('grn_id');
            $grn = WarehouseGrn::find($grn_id);
            $grn_details = DB::table('jocom_warehouse_grn_details')
                            ->where('grn_id', '=', $grn_id)
                            ->get();

            $einv_date = Input::get('einv_date');


            try {
                DB::beginTransaction();

                $einv = new EInvoice;
                $einv->einv_date = date_format(date_create($einv_date), 'Y-m-d H:i:s');
                $einv->seller_id = $grn->seller_id;
                $einv->warehouse_loc_id = $grn->warehouse_loc_id;
                $einv->po_id = $grn->po_id;
                $einv->remarks = Input::get('remarks');
                $einv->status = 1;
                $einv->discount_percent = Input::get('discpercent');
                $einv->discount_total = Input::get('disctotal');
                $einv->created_by = Session::get('username');
                $einv->updated_by = Session::get('username');

                $products = array();

                if($einv->save())
                {
                    $einv->einv_no = 'INV' . date('m') . date('y') . str_pad($einv->id, 5, "0", STR_PAD_LEFT);
                    $einv->save();

                    foreach ($grn_details as $detail) {
                        $product = array(
                            'einv_id' => $einv->id,
                            'sku' => $detail->sku,
                            'product_name' => $detail->product_name,
                            'price_label' => $detail->price_label,
                            'uom' => $detail->uom,
                            'base_unit' => $detail->base_unit,
                            'packing_factor' => $detail->packing_factor,
                            'price' => $detail->price,
                            'quantity' => $detail->quantity,
                            'total' => $detail->total,
                            'foc_qty' => $detail->foc_qty,
                            'foc_uom' => $detail->foc_uom,
                            'sst' => $detail->sst
                        );

                        array_push($products, $product);
                    }

                    DB::table('jocom_einvoice_details')->insert($products);

                } 

            } catch(Exception $ex) {
                $isError = true;dd($ex->getMessage());
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/einvoice')->with('success', 'eInvoice(eInvoice Number: '. $einv->einv_no .') added successfully.');
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

        $einv = DB::table('jocom_einvoice as einv')
                ->join('jocom_purchase_order as po', 'einv.po_id', '=', 'po.id')
                ->join('jocom_warehouse_location as loc', 'einv.warehouse_loc_id', '=', 'loc.id')
                ->join('jocom_seller as seller', 'einv.seller_id', '=', 'seller.id')
                ->where('einv.id', '=', $id)
                ->select('einv.*', 'po.po_no', 'loc.id as loc_id', 'loc.name as loc_name', 'loc.address_1', 'loc.address_2', 'loc.pic_name', 'loc.pic_contact', 'seller.id as seller_id', 'seller.company_name')
                ->first();

        if ($einv == null) {
            return Redirect::to('/einvoice');
        }

        $einv->einv_date = date_format(date_create($einv->einv_date), 'Y-m-d');

        $einv_details = DB::table('jocom_einvoice_details')
                        ->where('einv_id', '=', $id)
                        ->get();

        $subtotal = 0.00;
        $sst_total = 0.00;
        $total = 0.00;
        foreach ($einv_details as $detail) {
            $detail->subtotal_sst = $detail->total + $detail->sst;
            $subtotal = $subtotal + $detail->total;
            $sst_total = $sst_total + $detail->sst;
            $total = $total + $detail->subtotal_sst;
        }

        return View::make('einvoice.edit')->with(array(
            'einv' => $einv,
            'einv_details' => $einv_details,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'sst_total' => number_format($sst_total, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
        ));
    }

    /**
     * Update the specified PO in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $einv = EInvoice::find($id);
        if ($einv == null) {
            return Redirect::to('/einvoice');
        }

        $einv->remarks = Input::get('remarks');
        $einv->updated_by = Session::get('username');

        if($einv->save()) {
            General::audit_trail('EInvoiceController.php', 'update()', 'Update eInvoice', Session::get('username'), 'CMS');
            return Redirect::to('/einvoice')->with('success', 'eInvoice(ID: '.$einv->id.') updated successfully.');
        }

    }
 
    /**
     * Remove the specified PO from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id) {

        $einv = EInvoice::find($id);

        if ($einv == null) {
            return Redirect::to('/einvoice');
        }
        $einv->po_id = null;
        $einv->status = 2;
        $einv->updated_by = Session::get('username');
        $einv->save();
        
        $insert_audit = General::audit_trail('EInvoiceController.php', 'delete()', 'Delete eInvoice', Session::get('username'), 'CMS');

        return Redirect::to('/einvoice');
    }

    public function files($loc) {

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);
        $einv_id = $id[0];

        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {

            $einv = DB::table('jocom_einvoice as einv')
                    ->join('jocom_purchase_order as po', 'einv.po_id', '=', 'po.id')
                    ->where('einv.id', '=', $einv_id)
                    ->select('einv.*', 'po.po_no')
                    ->first();

            $EINVView = self::createEINVView($einv);

            return View::make('einvoice.einv_view')
                    ->with('issuer', $EINVView['issuer'])
                    ->with('einv', $EINVView['einv'])
                    ->with('seller', $EINVView['seller'])
                    ->with('warehouse', $EINVView['warehouse'])
                    ->with('products', $EINVView['products'])
                    ->with('htmlview',true);
        }
    }

    public function download($loc){

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);

        $id = explode("#", $loc);
        $einv_no = $id[2];
        $einv_id = $id[1];
        
        $file_path = array_shift(explode("#", $loc));
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];


        $einv = DB::table('jocom_einvoice as einv')
                    ->join('jocom_purchase_order as po', 'einv.po_id', '=', 'po.id')
                    ->where('einv.id', '=', $einv_id)
                    ->select('einv.*', 'po.po_no')
                    ->first();

        include app_path('library/html2pdf/html2pdf.class.php');

        $EINVView = self::createEINVView($einv);
        

        $response =  View::make('einvoice.einv_view')
                        ->with('issuer', $EINVView['issuer'])
                        ->with('einv', $EINVView['einv'])
                        ->with('seller', $EINVView['seller'])
                        ->with('warehouse', $EINVView['warehouse'])
                        ->with('products', $EINVView['products'])
                        ->with('htmlview',false);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );
        
        return Response::download($file_path, $file_name, $headers);
        
    }

    public static function createEINVView($einv) {

        $issuer = [
            "issuer_name" => "Jocom eThirtySeven Sdn. Bhd.",
            "issuer_address_1" => "Unit 9-1, Level 9,",
            "issuer_address_2" => "Tower 3, Avenue 3, Bangsar South,",
            "issuer_address_3" => "No. 8, Jalan Kerinchi,",
            "issuer_address_4" => "59200 Kuala Lumpur.",
            "issuer_tel" => "Tel: 03-2241 6637 Fax: 03-2242 3837",
        ];

        $einv_details = [
            'einv_id' => $einv->id,
            'einv_no' => $einv->einv_no,
            'einv_date' => $einv->einv_date,
            'po_no' => $einv->po_no,
            'remarks' => $einv->remarks,
            'discount_percent' => $einv->discount_percent,
            'discount_total' => $einv->discount_total,
        ];

        $seller = Seller::find($einv->seller_id);
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

        $warehouse = WarehouseLocation::find($einv->warehouse_loc_id);

        $warehouse_details = [
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'postcode' => $warehouse->postcode,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'pic_name' => $warehouse->pic_name,
            'pic_contact' => $warehouse->pic_contact,
        ];

        $product_details = DB::table('jocom_einvoice_details')
                            ->where('einv_id', '=', $einv->id)
                            ->get();

        return array(
            'issuer' => $issuer,
            'einv' => $einv_details,
            'seller' => $seller_details,
            'warehouse' => $warehouse_details,
            'products' => $product_details,
        );

    }

    public function pbxIndex() {
        $today = date('Y-m-d');
        return View::make('einvoice.pbx')->with('today', $today);;
    }

    public function pbxList() {
        $list = DB::table('jocom_einvoice_pbx')
                    ->select('id', 'einv_no', 'file_name', 'status');

        $actionBar = '@if($status==1)<a class="btn btn-primary" title="" data-toggle="tooltip" href="/einvoice/pbx/download/{{$file_name}}" target="_blank"><i class="fa fa-download"></i></a> <a class="btn btn-success" title="" data-toggle="tooltip" href="#" onclick="complete_account({{$id;}});"><i class="fa fa-check"></i></a>@endif @if($status==0)Contact IT Dept @endif';

        return Datatables::of($list)
            ->edit_column('status', '@if($status==0){{Initiated}} @elseif($status==1){{Generated}} @elseif($status==2){{Imported}} @endif')
            ->add_column('Action', $actionBar)
            ->make(true);
    }

    public function generatePbx($id) {

        $einv = DB::table('jocom_einvoice as einv')
                    ->join('jocom_einvoice_details as details', 'einv.id', '=', 'details.einv_id')
                    ->join('jocom_warehouse_location as warehouse', 'einv.warehouse_loc_id', '=', 'warehouse.id')
                    ->join('jocom_seller as seller', 'einv.seller_id', '=', 'seller.id')
                    ->join('jocom_purchase_order as po', 'einv.po_id', '=', 'po.id')
                    ->where('einv.id', '=', $id)
                    ->where('einv.status', '=', '1')
                    ->select('einv.id', 'einv.einv_no', 'einv.einv_date', 'einv.remarks','einv.created_at', 'einv.discount_percent', 'einv.discount_total', 'po.po_no', 'po.po_date', 'po.delivery_date', 'details.product_name', 'details.sku', 'details.uom', 'details.base_unit', 'details.packing_factor', 'details.price','details.quantity', 'details.total', 'details.foc_qty', 'details.foc_uom', 'details.sst', 'warehouse.id as warehouse_id', 'warehouse.name as warehouse_name', 'warehouse.address_1 as warehouse_address_1', 'warehouse.address_2 as warehouse_address_2', 'warehouse.postcode as warehouse_postcode', 'warehouse.city as warehouse_city', 'warehouse.state as warehouse_state', 'seller.id as seller_id')
                    ->get();



        $seller_code = 'SB' . str_pad($einv[0]->seller_id, 5, "0", STR_PAD_LEFT);;

        $text_filename = 'INV_JOCOM_' . $seller_code . '_' . $einv[0]->einv_no . '_' . date('YmdHis') . '.txt';
        $file_path = Config::get('constants.PBX_EINV') . '/' . $text_filename;
        $file = fopen($file_path, 'w');

        $detail = '';
        $seqNo = 1;
        $total = 0.0;

        foreach ($einv as $einv_detail) {

            $detailArr = array(
                'recordType' => 'DET',
                'invNo' => $einv_detail->einv_no,
                'seqNo' => $seqNo,
                'buyerItemCode' => $einv_detail->sku,
                'supplierItemCode' => $einv_detail->sku,
                'barCode' => '',
                'itemDesc' => $einv_detail->product_name,
                'brand' => '',
                'colourCode' => '',
                'colourDesc' => '',
                'sizeCode' => '',
                'sizeDesc' => '',
                'packingFactor' => number_format($einv_detail->packing_factor, 2, '.', ''),
                'invBaseUnit' => $einv_detail->base_unit,
                'invUom' => $einv_detail->uom,
                'invQty' => $einv_detail->quantity,
                'focBaseUnit' => '',
                'focUom' => $einv_detail->foc_uom,
                'focQty' => $einv_detail->foc_qty,
                'unitPrice' => number_format($einv_detail->price, 4, '.', ''),
                'discountAmount' => '0.0000',
                'discountPercent' => '',
                'netPrice' => number_format($einv_detail->price, 4, '.', ''),
                'itemAmount' => number_format($einv_detail->total, 4, '.', ''),
                'netAmount' => number_format($einv_detail->total, 4, '.', ''),
                'itemBuyerSharedCost' => '',
                'itemGrossAmount' => '',
                'itemRemarks' => '',
                'vatRate' => '',
                'vatAmount' => number_format($einv_detail->sst, 4, '.', ''),
                'vatGroup' => '',
            );

            $detail = $detail . implode('|', $detailArr) . "\n";

            $seqNo++;

            $total = $total + floatval($einv_detail->total);
        }

        $headerArr = array(
            'recordType' => 'HDR',
            'invNo' => $einv[0]->einv_no,
            'docAction' => 'A',
            'actionDate' => date('d/m/Y H:i:s'),
            'invType' => 'SOR',
            'invDate' => date_format(date_create($einv[0]->einv_date), 'd/m/Y H:i:s'),
            'poNo' => $einv[0]->po_no,
            'poDate' => date_format(date_create($einv[0]->po_date), 'd/m/Y H:i:s'),
            'deliveryNo' => '',
            'deliveryDate' => $einv[0]->delivery_date,
            'buyerCode' => 'JOCOM',
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
            'supplierBizRegNo' => '',
            'supplierVatRegNo' => '',
            'storeCode' => $einv[0]->warehouse_id,
            'storeName' => $einv[0]->warehouse_name,
            'storeAddr1' => $einv[0]->warehouse_address_1,
            'storeAddr2' => $einv[0]->warehouse_address_2,
            'storeAddr3' => '',
            'storeAddr4' => '',
            'storeCity' => $einv[0]->warehouse_city,
            'storeState' => $einv[0]->warehouse_state,
            'storeCountryCode' => 'MY',
            'storePostalCode' => number_format($einv[0]->warehouse_postcode, 4, '.', ''),
            'payTermCode' => '',
            'payTermDesc' => '',
            'payInstruct' => '',
            'additionalDiscountAmount' => '',
            'additionalDiscountPercent' => number_format($einv[0]->discount_percent, 2, '.', ''),
            'invAmountNoVat' => ($einv[0]->discount_total == '0.00') ? number_format($total, 4) : number_format($einv[0]->discount_total, 4),
            'totalVatAmount' => '0.0000',
            'invAmountWithVat' => ($einv[0]->discount_total == '0.00') ? number_format($total, 4) : number_format($einv[0]->discount_total, 4),
            'vatRate' => '',
            'invRemarks' => '',
            'cashDiscountAmount' => '0.0000',
            'cashDiscountPercent' => '',
            'taxIndicator' => '1'
        );

        $header = implode('|', $headerArr) . "\n";

        fwrite($file, $header . $detail);
        fclose($file);

        $pbx = new EInvoicePbx;
        $pbx->einv_no = $einv[0]->einv_no;
        $pbx->file_name = $text_filename;
        $pbx->status = 1;
        $pbx->content = $header . $detail;
        $pbx->save();

        return Redirect::to('einvoice/pbx')->with('message', 'eInvoice PracBix(ID: ' . $pbx->id . ') generated successfully.');
    }

    public function downloadPbx($filename) {
        $file = Config::get('constants.PBX_EINV') . '/' . $filename;

        if (is_file($file)) {
            return Response::download($file);
        } else {
            echo "<br>File not exists!";
        }
    }

    public function completePbx() {

        $id = Input::get('complete_id');
            
        $pbx = EInvoicePbx::find($id);
        $pbx->status = 2;
        $pbx->save();

        $file_name = $pbx->file_name;


        $file_path = Config::get('constants.PBX_EINV') . '/' . $file_name;

        if(is_file($file_path))
            unlink($file_path);
                  

        return Redirect::to('einvoice/pbx')->with('message', 'eInvoice PracBix(ID: ' . $id . ') updated successfully.');

    }

}
?>