<?php

class GrnAdminController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function anyIndex() 
    {
        return View::make('admingrn.index');
    }

    public function anyCreate()
    {
        return View::make('admingrn.create');
    }

    public function anyGrnlist()
    {

        $grn = WarehouseAdminGrn::lists();

        return Datatables::of($grn)
                        ->edit_column('status', function ($grn) {
                                 
                            if($grn->status=="3"){
                                return'<button type="button" class="btn btn-primary btn-sm">Partial</button>';
                            }
                            if($grn->status=="0")
                            {
                             return'<button type="button" class="btn btn-warning btn-sm">Pending</button>';   
                            }
                            else{
                              return'<button type="button" class="btn btn-success btn-sm">Completed</button>';  
                            }
                        })
                        ->add_column('Action', function ($g) {

                            $file = (Config::get('constants.GRN_PDF_FILE_PATH') . '/' . urlencode($g->grn_no) . '.pdf')."#".($g->id).'#'.$g->grn_no;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '
                                <a class="btn btn-success" title="" data-toggle="tooltip" href="/admingrn/download/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/admingrn/edit/'.$g->id.'"><i class="fa fa-pencil"></i></a>
                                <a id="deleteGRN" class="btn btn-danger" title="" data-toggle="tooltip" data-value="'.$g->id.'" href="/admingrn/delete/'.$g->id.'"><i class="fa fa-remove"></i></a>
                            ';
                        })
                        ->make();
    }

    public function anyStore()
    {
        $validator = Validator::make(Input::all(), WarehouseAdminGrn::$rules);

        if ($validator->passes()) {

            try {
                DB::beginTransaction();
                $checked_count=Input::get('product_count');
                $all_count=count(Input::get('sku'));
                 if($checked_count=='0'){
                     $cmp_status="0";
                 }
                 if($checked_count==$all_count){
                     $cmp_status="1"; 
                 }
                 if($checked_count!=$all_count && $checked_count!="0" ){
                     $cmp_status="3"; 
                 }
                $grn = new WarehouseAdminGrn;
                $grn->grn_date = date_format(date_create(Input::get('grn_date')), 'Y-m-d H:i:s');
                $grn->warehouse_loc_id = Input::get('whloc_id');
                $grn->po_id = Input::get('po_id');
                $grn->seller_id = Input::get('seller_id');
                $grn->seller_do_no = Input::get('seller_do_no');
                $grn->seller_driver_name = Input::get('seller_driver_name');
                $grn->remarks = Input::get('remarks');
                $grn->deliver_by = Input::get('deliverby');
                $grn->received_by = Input::get('receivedby');
                $grn->verified_by = Input::get('verifiedby');
                $grn->status = $cmp_status;
                $grn->created_by = Session::get('username');
                $grn->updated_by = Session::get('username');

                $products = array();

                if($grn->save())
                {
                    $grn->grn_no = 'GRN' . date('m') . date('y') . str_pad($grn->id, 5, "0", STR_PAD_LEFT);
                    $grn->save();

                    $skus = Input::get('sku');
                    $productNames = Input::get('product_name');
                    $priceLabels = Input::get('price_label');
                    $uoms = Input::get('uom');
                    $baseUnits = Input::get('base_unit');
                    $packingFactors = Input::get('packing_factor');
                    $prices = Input::get('price');
                    $quantitys = Input::get('qty');
                    $totals = Input::get('total');
                    $focQtys = Input::get('foc_qty');
                    $focUoms = Input::get('foc_uom');
                    $ssts = Input::get('sst');
                    $remark = Input::get('remark');
                    $cus_status = Input::get('sin_status');

                    for ($i = 0; $i < count($skus); $i++) { 
                        $product = array(
                            'grn_id' => $grn->id,
                            'sku' => $skus[$i],
                            'product_name' => $productNames[$i],
                            'price_label' => $priceLabels[$i],
                            'uom' => $uoms[$i],
                            'base_unit' => $baseUnits[$i],
                            'packing_factor' => $packingFactors[$i],
                            'price' => $prices[$i],
                            'quantity' => $quantitys[$i],
                            'total' => $totals[$i],
                            'foc_qty' => $focQtys[$i],
                            'foc_uom' => $focUoms[$i],
                            'remarks' => $remark[$i],
                            'status' => $cus_status[$i],
                            'sst' => $ssts[$i]
                        );

                        array_push($products, $product);
                        //Automated
                        
                        $productidgrn =0;
                        $unitgrn =0;
                        $expirydategrn ='';
                        $unitpricegrn = 0;
                        $totalamountgrn = 0;
                        $remarksgrn = '';

                        if($cus_status[$i]!='2'){
                        $productidgrn = DB::table('jocom_products')->where('sku','=',$skus[$i])->first();

                        $unitgrn = $quantitys[$i] + $focQtys[$i];
                        $unitpricegrn = $prices[$i];
                        $totalamountgrn = $unitgrn * $unitpricegrn; 
                        $remarksgrn = "Stock in - ".$grn->grn_no;



                        //WarehouseController::savestockingrn($productidgrn->id,$unitgrn,$expirydategrn,$unitpricegrn,$totalamountgrn,$remarksgrn);
                    }
                        
                    }
                    DB::table('jocom_warehouse_admin_grn_details')->insert($products);
                    $po = PurchaseOrder::find(Input::get('po_id'));
                    $po->grn_id = $grn->id;
                    $po->save();
                } 

            } catch(Exception $ex) {
                $isError = true;dd($ex->getMessage());
            } finally {
                if ($isError) {
                    DB::rollback();
                    return Redirect::back()->with('message', 'Error');
                } else {
                    DB::commit();
                    return Redirect::to('/admingrn')->with('success', 'Warehouse GRN(GRN Number: '. $grn->grn_no .') added successfully.');
                }
            }
        } else {
            return Redirect::back()
                    ->withInput()
                    ->withErrors($validator);
        }
    }

    public function anyEdit($id) {

        $grn = WarehouseAdminGrn::get($id);
        if ($grn == null) {
            return Redirect::to('/admingrn');
        }

        $grn->grn_date = date_format(date_create($grn->grn_date),'Y-m-d');

        $grn_details = DB::table('jocom_warehouse_admin_grn_details')
                        ->where('grn_id', '=', $id)
                        ->get();

        $total = 0.0;
        foreach ($grn_details as $detail) {

            if ($detail->base_unit == 'U') {
                $detail->base_unit = 'Unit';
            } else {
                $detail->base_unit = 'Pack/Carton';
            }

            $total += floatval($detail->total);
        }

        $managers = Manager::activeList();

        return View::make('admingrn.edit')->with(array(
            'grn' => $grn,
            'grn_details' => $grn_details,
            'total' => $total,
            'managers' => $managers,
        ));
    }

    public function anyUpdate()
    {
        $validator = Validator::make(Input::all(), WarehouseAdminGrn::$updateRules);

        if ($validator->passes()) {
            $grn_id = Input::get('grn_id');
            $grn = WarehouseAdminGrn::find($grn_id);

            if ($grn == null) {
                return Redirect::to('/admingrn');
            }
            $checked_count=Input::get('product_count');
            $all_count=count(Input::get('sin_id'));
            if($checked_count=='0'){
                $cmp_status=$grn->status;
            }
            if($checked_count==$all_count){
                $cmp_status="1"; 
            }
            if($checked_count!=$all_count && $checked_count!="0" ){
                $cmp_status="3"; 
            }
            $grn->seller_do_no = Input::get('seller_do_no');
            $grn->seller_driver_name = Input::get('seller_driver_name');
            $grn->remarks = Input::get('remarks');
            $grn->deliver_by = Input::get('deliverby');
            $grn->received_by = Input::get('receivedby');
            $grn->verified_by = Input::get('verifiedby');
            $grn->status = $cmp_status;

            if($grn->save()) {

                 $ids = Input::get('sin_id');
                    $quantitys = Input::get('qty');
                    $focQtys = Input::get('foc_qty');
                    $focUoms = Input::get('foc_uom');
                    $remark = Input::get('remark');
                    $cus_status = Input::get('sin_status');

                    for ($i = 0; $i < count($ids); $i++) {

                        if($cus_status[$i]!='2'){

                        $detail=DB::table('jocom_warehouse_admin_grn_details')
                               ->select('sku','price','grn_id') 
                               ->where('id','=',$ids[$i])
                               ->first();
                            
                        $grn_no_detail=DB::table('jocom_warehouse_admin_grn')
                               ->select('grn_no') 
                               ->where('id','=',Input::get('grn_id'))
                               ->first();
                        $totals=$quantitys[$i]*$detail->price;
                DB::table('jocom_warehouse_admin_grn_details')
                ->where('id','=',$ids[$i])
                ->update(['quantity' => $quantitys[$i],
                            'total' => $totals,
                            'foc_qty' => $focQtys[$i],
                            'foc_uom' => $focUoms[$i],
                            'remarks' => $remark[$i],
                            'status' => $cus_status[$i]]);
                      
                        $productidgrn =0;
                        $unitgrn =0;
                        $expirydategrn ='';
                        $unitpricegrn = 0;
                        $totalamountgrn = 0;
                        $remarksgrn = '';
                        $productidgrn = DB::table('jocom_products')->where('sku','=',$detail->sku)->first();
                     
                        $unitgrn = $quantitys[$i] + $focQtys[$i];
                        $unitpricegrn = $detail->price;
                        $totalamountgrn = $unitgrn * $unitpricegrn;
                        $remarksgrn = "Stock in - ".$grn_no_detail->grn_no;
                      
                       $wahere=WarehouseController::savestockingrn($productidgrn->id,$unitgrn,$expirydategrn,$unitpricegrn,$totalamountgrn,$remarksgrn);
}
}
                General::audit_trail('GrnAdminController.php', 'update()', 'Update GRN', Session::get('username'), 'CMS');
                return Redirect::to('/admingrn')->with('success', 'GRN(ID: '.$grn->id.') updated successfully.');
            }

        } else {
            return Redirect::back()
                    ->withInput()
                    ->withErrors($validator);
        }
        
    }

    public function anyDelete($id) 
    {

        $grn = WarehouseAdminGrn::find($id);

        if ($grn == null) {
            return Redirect::to('/admingrn');
        }
        $grn->status = 2;
        $grn->updated_by = Session::get('username');
        $grn->save();

        $po = PurchaseOrder::find($grn->po_id);
        $po->grn_id = null;
        $po->save();
        
        $insert_audit = General::audit_trail('GrnAdminController.php', 'delete()', 'Delete GRN', Session::get('username'), 'CMS');

        return Redirect::to('/admingrn')->with('success', 'GRN(ID: '.$po->id.') deleted successfully.');
    }

    public function anyGrnproducts()
    {
        return View::make('admingrn.grnproducts');
    }

    public function productAjax() 
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary">Select</a>')
            ->make();
    }

    public function anyFiles($loc) {

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);
       
        $id = explode("#", $loc);
        $grn_id = $id[0];

        if (file_exists($loc)) {
            $paths = explode("/", $loc);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($loc));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $file = readfile($loc);

        } else {
 
            $grn = DB::table('jocom_warehouse_admin_grn as grn')
                    ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                    ->where('grn.id', '=', $grn_id)
                    ->select('grn.*', 'po.po_no')
                    ->first();

            $GRNView = self::createGRNView($grn);

            return View::make('admingrn.grn_view')
                    ->with('issuer', $GRNView['issuer'])
                    ->with('grn', $GRNView['grn'])
                    ->with('seller', $GRNView['seller'])
                    ->with('warehouse', $GRNView['warehouse'])
                    ->with('products', $GRNView['products'])
                    ->with('htmlview',true);
        }
    }

    public function anyDownload($loc){

        $loc = base64_decode(urldecode($loc));
        $loc = Crypt::decrypt($loc);

        $id = explode("#", $loc);
        $grn_id = $id[1];
        
        $file_path = array_shift(explode("#", $loc));
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];


        $grn = DB::table('jocom_warehouse_admin_grn as grn')
                    ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                    ->where('grn.id', '=', $grn_id)
                    ->select('grn.*', 'po.po_no')
                    ->first();

        $GRNView = self::createGRNView($grn);
        
        $response =  View::make('admingrn.grn_view')
                        ->with('issuer', $GRNView['issuer'])
                        ->with('grn', $GRNView['grn'])
                        ->with('seller', $GRNView['seller'])
                        ->with('warehouse', $GRNView['warehouse'])
                        ->with('products', $GRNView['products'])
                        ->with('htmlview',false);
        include app_path('library/html2pdf/html2pdf.class.php');
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );
       
        return Response::download($file_path, $file_name, $headers);
        
    }

    public static function createGRNView($grn) {

        $issuer = [
            "issuer_name" => "Jocom eThirtySeven Sdn. Bhd.",
            "issuer_address_1" => "Unit 9-1, Level 9,",
            "issuer_address_2" => "Tower 3, Avenue 3, Bangsar South,",
            "issuer_address_3" => "No. 8, Jalan Kerinchi,",
            "issuer_address_4" => "59200 Kuala Lumpur.",
            "issuer_tel" => "Tel: 03-2241 6637 Fax: 03-2242 3837",
        ];

        $grn_details = [
            'grn_id' => $grn->id,
            'grn_no' => $grn->grn_no,
            'grn_date' => $grn->grn_date,
            'po_no' => $grn->po_no,
            'seller_do_no' => $grn->seller_do_no,
            'seller_driver_name' => $grn->seller_driver_name,
            'remarks' => $grn->remarks,
            'received_by' => $grn->received_by,
            'delivered_by' => $grn->deliver_by,
            'verified_by' => $grn->verified_by,
        ];
        $seller = Seller::find($grn->seller_id);
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

        $warehouse = WarehouseLocation::find($grn->warehouse_loc_id);

        $warehouse_details = [
            'name' => $warehouse->name,
            'address_1' => $warehouse->address_1,
            'address_2' => $warehouse->address_2,
            'postcode' => $warehouse->postcode,
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'pic_name' => $warehouse->pic_name,
            'pic_contact' => $warehouse->pic_contact,
        ];

        $product_details = DB::table('jocom_warehouse_admin_grn_details')
                            ->where('grn_id', '=', $grn->id)
                            ->get();

        return array(
            'issuer' => $issuer,
            'grn' => $grn_details,
            'seller' => $seller_details,
            'warehouse' => $warehouse_details,
            'products' => $product_details,
        );

    }


    public function anyProducts() {
        return View::make('admingrn.ajaxproduct');
    }

    public function anyProductajax() {
            
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
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="/admingrn/prices/{{$id}}">Select</a>')
            ->make();
    }

    public function anyPrices($id) {
        $tempID = $id;

        $product = DB::table('jocom_products')
            ->select('name', 'sku', 'qrcode')
            ->where('id', '=', $tempID)->first();

        return View::make('admingrn.ajaxprice')->with([
            'id'     => $id,
            'name'   => addslashes($product->name),
            'sku'    => $product->sku,
            'qrcode' => $product->qrcode,
        ]);
    }

    public function anyPriceajax($id) {

        $product_prices = DB::table('jocom_product_price')
            ->select('jocom_product_price.id', 'jocom_product_price.label', 'jocom_product_price.price', 'jocom_product_price.price_promo')
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
            ->add_column('base_unit', '
                <select class="form-control" style="text-align-last: center;" name="base_unit[]" id="base_unit{{$id}}" onchange="enablePacking({{$id}})">
                    <option value="U" selected>Unit</option>
                    <option value="P">Pack/Carton</option>
                </select>
                ')
            ->add_column('packing_factor', '<input type="number" class="form-control" id="packing_factor{{$id}}" disabled>')
            ->add_column('foc_qty', '<input type="number" class="form-control" id="foc_qty{{$id}}">')
            ->add_column('foc_uom', '<input type="text" class="form-control" id="foc_uom{{$id}}">')
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
            ->make();
    }

    public function anyPbx() {
        $today = date('Y-m-d');
        return View::make('admingrn.pbx')->with('today', $today);;
    }

    public function anyPbxlist() {
        $list = DB::table('jocom_grn_pbx')
                    ->select('id', 'file_name', 'status');

        $actionBar = '@if($status==1)<a class="btn btn-primary" title="" data-toggle="tooltip" href="/admingrn/pbxdownload/{{$file_name}}" target="_blank"><i class="fa fa-download"></i></a> <a class="btn btn-success" title="" data-toggle="tooltip" href="#" onclick="complete_account({{$id;}});"><i class="fa fa-check"></i></a>@endif @if($status==0)Contact IT Dept @endif';

        return Datatables::of($list)
            ->edit_column('status', '@if($status==0){{Initiated}} @elseif($status==1){{Generated}} @elseif($status==2){{Imported}} @endif')
            ->add_column('Action', $actionBar)
            ->make(true);
    }

    public function anyPbxgeneratezip() {
        $grn_date = Input::get('grn_date');
        $start = $grn_date . ' 00:00:00';
        $end = $grn_date . ' 23:59:59';

        $grnsDB = DB::table('jocom_warehouse_admin_grn as grn')
                ->join('jocom_warehouse_admin_grn_details as details', 'grn.id', '=', 'details.grn_id')
                ->join('jocom_warehouse_location as warehouse', 'grn.warehouse_loc_id', '=', 'warehouse.id')
                ->join('jocom_seller as seller', 'grn.seller_id', '=', 'seller.id')
                ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                ->whereBetween('grn_date', [$start, $end])
                ->where('grn.status', '=', '1')
                ->select('grn.id', 'grn.grn_no', 'grn.grn_date', 'grn.remarks','grn.created_at', 'po.po_no', 'po.po_date', 'details.product_name', 'details.sku', 'details.uom', 'details.base_unit', 'details.packing_factor', 'details.price','details.quantity', 'details.total', 'details.foc', 'details.foc_qty', 'details.foc_uom', 'warehouse.id as warehouse_id', 'warehouse.name as warehouse_name', 'warehouse.address_1 as warehouse_address_1', 'warehouse.address_2 as warehouse_address_2', 'warehouse.postcode as warehouse_postcode', 'warehouse.city as warehouse_city', 'warehouse.state as warehouse_state', 'seller.id as seller_id')
                ->orderBy('grn.id')
                ->get();

        $grn_grouped = array();
        foreach ($grnsDB as $grnDB) {
            $grn_grouped[$grnDB->id][] = $grnDB;
        }

        if (count($grn_grouped) == 0) {
            return Response::json(array('status' => 404, 'message' => 'No GRN.'));
        }

        $text_file_arr = array();

        $zip = new ZipArchive();
        $fname = 'GRN_JOCOM_' . date('YmdHis') . '.zip';
        $filename = Config::get('constants.PBX_GRN') . '/' . $fname;
        $zip->open($filename, ZipArchive::CREATE);

        $contentArr = array();

        foreach ($grn_grouped as $id => $grns) {

            $seller_code = 'SB' . str_pad($grns[0]->seller_id, 5, "0", STR_PAD_LEFT);;

            $text_filename = 'GRN_JOCOM_' . $seller_code . '_' . $grns[0]->grn_no . '_' . date('YmdHis') . '.txt';
            $file_path = Config::get('constants.PBX_GRN') . '/' . $text_filename;
            $file = fopen($file_path, 'w');

            $detail = '';
            $loc = '';
            $seqNo = 1;
            $total = 0.0;

            foreach ($grns as $grn) {

                $detailArr = array(
                    'recordType' => 'DET',
                    'grnNo' => $grn->grn_no,
                    'seqNo' => $seqNo,
                    'buyerItemCode' => $grn->sku,
                    'supplierItemCode' => '',
                    'barCode' => '',
                    'itemDesc' => $grn->product_name,
                    'brand' => '',
                    'colourCode' => '',
                    'colourDesc' => '',
                    'sizeCode' => '',
                    'sizeDesc' => '',
                    'packingFactor' => number_format($grn->packing_factor, 2, '.', ''),
                    'orderBaseUnit' => $grn->base_unit,
                    'orderUom' => $grn->uom,
                    'orderQty' => $grn->quantity,
                    'receiveQty' => $grn->quantity,
                    'focBaseUnit' => '',
                    'focUom' => $grn->foc_uom,
                    'focQty' => $grn->foc_qty,
                    'receiveFocQty' => $grn->foc_qty,
                    'unitCost' => number_format($grn->price, 4, '.', ''),
                    'itemCost' => number_format($grn->total, 4, '.', ''),
                    'retailPrice' => '',
                    'itemRetailAmount' => '',
                    'itemRemarks' => '',
                    'lineRefNo' => '',
                    'costDiscountAmount' => '',
                    'costDiscountAmountDesc' => '',
                    'netUnitCost' => '',
                    'unitVatAmount' => '',
                    'netUnitCostVat' => '',
                    'vatRate' => '',
                    'vatAmount' => '',
                    'vatGroup' => '',
                    'itemCostVat' => ''
                );

                $detail = $detail . implode('|', $detailArr) . "\n";

                $seqNo++;
            }

            $headerArr = array(
                'recordType' => 'HDR',
                'grnNo' => $grns[0]->grn_no,
                'docAction' => 'A',
                'actionDate' => date('d/m/Y H:i:s'),
                'grnDate' => date_format(date_create($grns[0]->grn_date), 'd/m/Y H:i:s'),
                'poNo' => $grns[0]->po_no,
                'poDate' => date_format(date_create($grns[0]->po_date), 'd/m/Y H:i:s'),
                'createDate' => date_format(date_create($grns[0]->created_at), 'd/m/Y H:i:s'),
                'buyerCode' => 'JOCOM',
                'buyerName' => '',
                'supplierCode' => $seller_code,
                'supplierName' => '',
                'receiveStoreCode' => $grns[0]->warehouse_id,
                'receiveStoreName' => $grns[0]->warehouse_name,
                'totalReceivedQty' => '',
                'itemCount' => '',
                'netCost' => '',
                'totalCost' => '',
                'grnRemarks' => $grns[0]->remarks,
                'invNo' => '',
                'invDate' => '',
                'deliveryNo' => '',
                'totalCostWithGst' => '',
                'vatGroup' => '',
                'discountAmount' => '',
                'grnDueDate' => '',
                'creditTermCode' => ''
            );

            $header = implode('|', $headerArr) . "\n";

            fwrite($file, $header . $detail);
            fclose($file);
            $zip->addFile($file_path, $text_filename);

            array_push($text_file_arr, $file_path);
            array_push($contentArr, $header . $detail . $loc);
        }

        $zip->close();

        foreach ($text_file_arr as $f) {
            unlink($f);
        }

        $pbx = new WarehouseGrnPbx;
        $pbx->file_name = $fname;
        $pbx->status = 1;
        $pbx->content = implode('&', $contentArr);
        $pbx->save();

        return Response::json(array('status' => 200, 'message' => 'Zip generated successfully.'));
    }

    public function anyPbxdownload($filename) {
        $file = Config::get('constants.PBX_GRN') . '/' . $filename;

        if (is_file($file)) {
            return Response::download($file);
        } else {
            echo "<br>File not exists!";
        }
    }

    public function anyPbxcomplete() {

        $id = Input::get('complete_id');
            
        $pbx = WarehouseGrnPbx::find($id);
        $pbx->status = 2;
        $pbx->save();

        $file_name = $pbx->file_name;


        $file_path = Config::get('constants.PBX_GRN') . '/' . $file_name;

        if(is_file($file_path))
            unlink($file_path);


        return Redirect::to('grn/pbx')->with('success', 'GRN PracBix(ID: ' . $id . ') updated successfully.');

    }
    public function anyDashboard()
    {
        // $grn = WarehouseAdminGrn::lists()->get();

        // dd($grn);
        return View::make('admingrn.dashboard');
        // return View::make('admingrn.dashboard')->with('grns',$grn);
    }

    public function anyGrnReport()
    {
        Log::info("MASUK");
        Log::info($start_date);
        Log::info($end_date);
        Log::info($time_period);
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
          case 'Daily':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Weekly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Monthly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
            break;
          case 'Custom':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;

          default:
              $query = WarehouseAdminGrn::lists()
                ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
            break;
        }
        $grn = $query->select('grn.grn_no',DB::raw('DATE(grn.grn_date)'),'grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.price','grn_details.quantity', 'seller.company_name as company_name','grn_details.status');
// dd($time_period,$start_date,$end_date,$grn->first());
        return Datatables::of($grn)
        ->edit_column('status', function ($grn) {
                                 
                            if($grn->status=="2"){
                                return'Partial';
                            }
                            else{
                              return'Completed';  
                            }
                        })
        ->make();
    }

    public function anyGrnData(){
      $start_date = Input::get('start_date');
      $end_date = Input::get('end_date');

      switch (Input::get('time_period')) {
              case 'Daily':
                  $query = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),$start_date);
                  $query_completed = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),$start_date)->where('grn.status','=',1);
                  $query_partial = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),$start_date)->where('grn.status','=',3);
                  $query_percent = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');

                  $grn_total_old = $query_percent
                              ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');
                  $grn_total_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                              ->where(DB::raw('DATE(grn.grn_date)'),'<=',$start_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');

                  $grn_old = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                                  ->first();
                  $grn_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$start_date)
                                  ->first();

                break;
              case 'Weekly':
                  $prv_date = Carbon\Carbon::parse($start_date);
                  $prv_date->addDays(-7);
                  $query = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
                  $query_completed = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                  ->where('grn.status','=','1');
                  $query_partial = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                  ->where('grn.status','=','3');
                  $query_percent = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');

                  $grn_total_old = $query_percent
                              ->where(DB::raw('DATE(grn.grn_date)'),'>=',$prv_date)
                              ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');
                  $grn_total_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                              ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                              ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');

                  $grn_old = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$prv_date)
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                                  ->first();
                  $grn_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                                  ->first();
                break;
              case 'Monthly':
                  $start_date = Carbon\Carbon::parse($start_date);
                  $prv_month = Carbon\Carbon::parse($start_date);
                  $prv_month->addMonth(-1);

                  $query = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                  ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
                  $query_completed = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                  ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year)
                  ->where('grn.status','=','1');
                  $query_partial = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                  ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year)
                  ->where('grn.status','=','3');

                  $query_percent = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');

                  $grn_total_old = $query_percent
                              ->where(DB::raw('MONTH(grn.grn_date)'),'=',$prv_month->month)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');
                  $grn_total_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                              ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');

                  $grn_old = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$prv_month->month)
                                  ->first();
                  $grn_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                                  ->first();
                break;
              case 'Custom':
                  $query = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
                  $query_percent = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
                  $query_completed = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)->where('grn.status','=','1');
                  $query_partial = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)->where('grn.status','=','3');

                  $grn_total_old = $query_percent
                              ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');
                  $grn_total_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                              ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                              ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                              ->distinct('grn.grn_no')
                              ->count('grn.grn_no');

                  $grn_old = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<',$start_date)
                                  ->first();
                  $grn_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                  ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date)
                                  ->first();
                break;

              default:
                $query = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),\Carbon\Carbon::today());
                $query_percent = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
                $query_completed = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),\Carbon\Carbon::today())->where('grn.status','=','1');
                $query_partial = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where(DB::raw('DATE(grn.grn_date)'),\Carbon\Carbon::today())->where('grn.status','=','3');

                $grn_total_old = $query_percent
                            ->where(DB::raw('DATE(grn.grn_date)'),'<',\Carbon\Carbon::today())
                            ->distinct('grn.grn_no')
                            ->count('grn.grn_no');
                $grn_total_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                            ->where(DB::raw('DATE(grn.grn_date)'),'<=',\Carbon\Carbon::today())
                            ->distinct('grn.grn_no')
                            ->count('grn.grn_no');

                $grn_old = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                ->where(DB::raw('DATE(grn.grn_date)'),'<',\Carbon\Carbon::today())
                                ->first();
                $grn_new = WarehouseAdminGrn::lists()->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                                ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')
                                ->where(DB::raw('DATE(grn.grn_date)'),'<=',\Carbon\Carbon::today())
                                ->first();

                break;
            }

      $grn_total = $query->distinct('grn.grn_no')->count('grn.grn_no');
      $completed_total = $query_completed->distinct('grn.grn_no')->count('grn.grn_no');
      $partial_total = $query_partial->distinct('grn.grn_no')->count('grn.grn_no');

      $grn_quantity_amount = $query
                    ->selectRaw('ifnull(sum(grn_details.quantity),0) as grn_quantity, ifnull(sum(grn_details.total),0) as grn_amount')->first();

      // PERCENTAGE CALCULATION

      $grn_total_diff = $grn_total_new - $grn_total_old;
      if($grn_total_old && $grn_total_old >= 1){
        $grn_total_percentage = round( ($grn_total_diff/$grn_total_old*100), 2);
      }
      else {
        $grn_total_percentage = $grn_total_new;
      }
      $grn_quantity_diff = $grn_new->grn_quantity - $grn_old->grn_quantity;
      if($grn_old->grn_quantity && $grn_old->grn_quantity >= 1){
        $grn_quantity_percentage = round( ($grn_quantity_diff/$grn_old->grn_quantity*100), 2);
      }
      else {  
        $grn_quantity_percentage = $grn_new->grn_quantity;
      }

      $grn_amount_diff = $grn_new->grn_amount - $grn_old->grn_amount;
      if($grn_old->grn_amount && $grn_old->grn_amount >= 1){
        $grn_amount_percentage = round( ($grn_amount_diff/$grn_old->grn_amount*100), 2);
      }
      else {
        $grn_amount_percentage = $grn_new->grn_amount;
      }
      // PERCENTAGE CALCULATION

      return Response::json(array('success'=>true,'grn_total'=>$grn_total,'completed'=>$completed_total,'partial'=>$partial_total,'grn_quantity'=>$grn_quantity_amount->grn_quantity,'grn_amount'=>$grn_quantity_amount->grn_amount,'total_percentage'=>$grn_total_percentage,'quantity_percentage'=>$grn_quantity_percentage,'amount_percentage'=>$grn_amount_percentage),200);
    }

    public function anyDownloadexcel($type)
    {
      $start_date = Carbon\Carbon::parse(Input::get('start_date'));
      $end_date = Carbon\Carbon::parse(Input::get('end_date'));
      $time_period = Input::get('time_period');
      switch ($time_period) {
        case 'Daily':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;
        case 'Weekly':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;
        case 'Monthly':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                  ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
          break;
        case 'Custom':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;

        default:
            $query = WarehouseAdminGrn::lists()
              ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
          break;
      }


        $data = $query->select('grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.base_unit','grn_details.packing_factor','grn_details.price','grn_details.quantity','grn_details.total','grn_details.foc','grn_details.foc_qty','grn_details.foc_uom', 'seller.company_name as company_name','grn_details.status')->get();
        // $data = Post::get()->toArray();
        $data_array[] = array('GRN NO','SKU','PRODUCT NAME','LABEL','UOM','BASE UNIT','PACKING FACTOR','PRICE','TOTAL QUANTITY','TOTAL AMOUNT','FOC','FOC QUANTITY','FOC UOM','COMPANY NAME','STATUS');

        foreach ($data as $key => $value) {
            if($value->status=="2"){
                $Status="Partial";
            }else{
                $Status="Completed";
            }
          $data_array[] = array(
            'GRN NO' => $value->grn_no,
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
            'FOC QUANTITY' => $value->foc_qty,
            'FOC UOM' => $value->foc_uom,
            'COMPANY NAME' => $value->company_name,
            'STATUS'=>$Status,
          );
        }

        return Excel::create('Product Details', function($excel) use ($data_array,$start_date,$end_date) {

            $excel->sheet('Product Details', function($sheet) use ($data_array,$start_date,$end_date)
            {
                $sheet->cell('A2', function($cell) {$cell->setValue('GRN Dashboard - Product Details');   });
                $sheet->cell('A2', function($cell) {$cell->setFont(array(
                  'size' => '25',
                  'bold' => true
                ));   });
                $sheet->cell('A3', function($cell) {$cell->setFont(array(
                  'bold' => true
                ));   });
                $sheet->cell('A4', function($cell) {$cell->setFont(array(
                  'bold' => true
                ));   });

                $sheet->mergeCells('A2:O2');

                $sheet->setCellValue('A3', 'From Date - ');
                $sheet->setCellValue('B3', $start_date);
                $sheet->setCellValue('A4', 'To Date - ');
                $sheet->setCellValue('B4', $end_date);
                $sheet->setCellValue('A5', '');
                $sheet->rows($data_array);

                // $sheet->setAllBorders('A6:J10', 'thin');
                $sheet->row(6, function($row) {
                    $row->setBackground('#D9D9D9');
                });
                // $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->setHeight(6,29);
                $sheet->cell('A6:O6',function($cell){
                  $cell->setValignment('center');
                  $cell->setAlignment('center');
                  $cell->setFontWeight('bold');
                });
                // $sheet->cell('A6:N6', function($cell) {$cell->setFont(array(
                //   'bold' => true
                // ));   });

                $count = 5 + count($data_array);
                $range = "A7:O".$count;

                $sheet->cells('A6:O6', function($cells) {
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

    public function anyExportpdf()
    {
        
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
          case 'Daily':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Weekly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Monthly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
            break;
          case 'Custom':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;

          default:
              $query = WarehouseAdminGrn::lists()
                ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
            break;
        }
        $grn_details = $query->select('grn.id','grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.base_unit','grn_details.packing_factor','grn_details.price','grn_details.quantity','grn_details.total','grn_details.foc','grn_details.foc_qty','grn_details.foc_uom', 'seller.company_name as company_name','grn_details.status')->get();
        
        $pdf = PDF::loadView('grn.pdf_view', ['grn_details' => $grn_details,'start_date' => $start_date,'end_date' => $end_date,'time_period' => $time_period]);
        
        return $pdf->setPaper('a4')->setOrientation('landscape')->setWarnings(false)->download('grn'.Carbon\Carbon::now()->timestamp.'.pdf');
    }

    public function anyDownloadgrndashexcel($type)
    {
      $start_date = Carbon\Carbon::parse(Input::get('start_date'));
      $end_date = Carbon\Carbon::parse(Input::get('end_date'));
      $time_period = Input::get('time_period');
      switch ($time_period) {
        case 'Daily':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;
        case 'Weekly':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;
        case 'Monthly':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                  ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
          break;
        case 'Custom':
            $query = WarehouseAdminGrn::lists()
                  ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                  ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                  ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
          break;

        default:
            $query = WarehouseAdminGrn::lists()
              ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
          break;
      }


        $data = $query->select('grn.id','grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.base_unit','grn_details.packing_factor','grn_details.price','grn_details.quantity','grn_details.total','grn_details.foc','grn_details.foc_qty','grn_details.foc_uom', 'seller.company_name as company_name','grn.status')
        ->groupBy(DB::raw('grn.grn_no'))->get();

        return Excel::create('Product Details', function($excel) use ($data, $data_array,$start_date,$end_date,$time_period) {

            $excel->sheet('Product Details', function($sheet) use ($data,$data_array,$start_date,$end_date,$time_period)
            {
                $sheet->mergeCells('A2:D2');

                $sheet->cell('A2', function($cell) {$cell->setValue('GRN Dashboard');   });
                $sheet->cell('A2', function($cell) {$cell->setFont(array(
                  'size' => '25',
                  'bold' => true
                ));   });
                $sheet->cell('A3', function($cell) {$cell->setFont(array(
                  'bold' => true
                ));   });
                $sheet->cell('A4', function($cell) {$cell->setFont(array(
                  'bold' => true
                ));   });

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

                  foreach ($data as $key => $value) {
                    $data_array[] = array('GRN NO','SKU','PRODUCT NAME','LABEL','UOM','BASE UNIT','PACKING FACTOR','PRICE','TOTAL QUANTITY','TOTAL AMOUNT','FOC','FOC QUANTITY','FOC UOM','STATUS');

                    $grn_details = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where('grn.id', '=', $value->id)
                  ->select('grn.id','grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.base_unit','grn_details.packing_factor','grn_details.price','grn_details.quantity','grn_details.total','grn_details.foc','grn_details.foc_qty','grn_details.foc_uom', 'seller.company_name as company_name','grn_details.status')->get();
                    foreach ($grn_details as $key => $grn_detail) {
                         if($grn_detail->status=="2"){
                $Status="Partial";
            }else{
                $Status="Completed";
            }
                      $data_array[] = array(
                        'GRN NO' => $grn_detail->grn_no,
                        'SKU' => $grn_detail->sku,
                        'PRODUCT NAME' => $grn_detail->product_name,
                        'LABEL' => $grn_detail->price_label,
                        'UOM' => $grn_detail->uom,
                        'BASE UNIT' => $grn_detail->base_unit,
                        'PACKING FACTOR' => $grn_detail->packing_factor,
                        'PRICE' => $grn_detail->price,
                        'TOTAL QUANTITY' => $grn_detail->quantity,
                        'TOTAL AMOUNT' => $grn_detail->total,
                        'FOC' => $grn_detail->foc,
                        'FOC QUANTITY' => $grn_detail->foc_qty,
                        'FOC UOM' => $grn_detail->foc_uom,
                        'STATUS' => $Status,
                      );
                    }
            $table_heading_no = $heading_row_no-1;
            if($value->status=="3"){
                $status="Partial";
            }elseif($value->status=="1"){
                $status="Completed";
            }
            else{
             $status="Pending";
            }
                    $sheet->setCellValue('A'.$table_heading_no,'Grn No : '.$value->grn_no.' | Seller Name : '.strtoupper($value->company_name).' | Status : '.strtoupper($status));
                    $sheet->cell('A'.$table_heading_no, function($cell) {$cell->setFont(array(
                      'size' => '15',
                      'bold' => true
                    ));   });
                    $sheet->mergeCells('A'.$table_heading_no.':L'.$table_heading_no);

                    $sheet->rows($data_array);

                    $sheet->row($heading_row_no, function($row) {
                        $row->setBackground('#D9D9D9');
                    });
                    // $sheet->getRowDimension($heading_row_no)->setRowHeight(20);
                    $sheet->setHeight($heading_row_no,29);
                    $sheet->cell('A'.$heading_row_no.':N'.$heading_row_no,function($cell){
                      $cell->setValignment('center');
                      $cell->setAlignment('center');
                      $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A'.$heading_row_no.':N'.$heading_row_no, function($cell) {$cell->setFont(array(
                      'bold' => true
                    ));   });

                    $record_count = $heading_row_no + count($data_array) - 1;
                    $range_heading = "A".$heading_row_no.":N".$heading_row_no;
                    $data_row_no = $heading_row_no + 1;
                    $range = "A".$data_row_no.":N".$record_count;
                    // $range = 'A7:N10';

                    $sheet->cells($range_heading, function($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells($range, function($cells) {
                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $heading_row_no = $heading_row_no+count($data_array)+2;
                    // $record_count = $record_count + $record_count+2;
                    $empty_row_no = $heading_row_no - 1;
                    $sheet->setCellValue('A'.$empty_row_no, '');

                    unset($data_array);
                  }


            });
        })->setFilename('Product-details-dashboard'.Carbon\Carbon::now()->timestamp)
        ->download($type);
    }

    public function anyExportgrndashpdf()
    {
        // return View::make('admingrn.pdf_view_grn_dash');
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
          case 'Daily':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Weekly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Monthly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
            break;
          case 'Custom':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;

          default:
              $query = WarehouseAdminGrn::lists()
                ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
            break;
        }

        $grns = $query->selectRaw('grn.id, grn.grn_no, sum(grn_details.quantity) as grn_quantity, sum(grn_details.total) as grn_amount, seller.company_name,grn.status')
        ->groupBy(DB::raw('grn.grn_no'))
        ->get();

        // $grns = WarehouseAdminGrn::lists()
        //             ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
        //             ->selectRaw('grn.id, grn.grn_no, sum(grn_details.quantity) as grn_quantity, sum(grn_details.total) as grn_amount, seller.company_name')
        //             ->groupBy(DB::raw('grn.grn_no'))
        //             ->get();
        $pdf = PDF::loadView('grn.pdf_view_grn_dash',['grns' => $grns,'start_date' => $start_date,'end_date' => $end_date,'time_period' => $time_period]);
        return $pdf->setPaper('a4')->setOrientation('landscape')->setWarnings(false)->download('grn-dashboard'.Carbon\Carbon::now()->timestamp.'.pdf');

    }

    public function anyGrnDashboardData()
    {
        $start_date = Carbon\Carbon::parse(Input::get('start_date'));
        $end_date = Carbon\Carbon::parse(Input::get('end_date'));
        $time_period = Input::get('time_period');
        switch ($time_period) {
          case 'Daily':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Weekly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;
          case 'Monthly':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('MONTH(grn.grn_date)'),'=',$start_date->month)
                    ->where(DB::raw('YEAR(grn.grn_date)'),'=',$start_date->year);
            break;
          case 'Custom':
              $query = WarehouseAdminGrn::lists()
                    ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                    ->where(DB::raw('DATE(grn.grn_date)'),'>=',$start_date)
                    ->where(DB::raw('DATE(grn.grn_date)'),'<=',$end_date);
            break;

          default:
              $query = WarehouseAdminGrn::lists()
                ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id');
            break;
        }

        $grns = $query->selectRaw('grn.id, grn.grn_no, sum(grn_details.quantity) as grn_quantity, sum(grn_details.total) as grn_amount, seller.company_name,grn.status')
        ->groupBy(DB::raw('grn.grn_no'))
        ->get();

        return $grns;

    }

    public function anyGrnDashboardChildData()
    {
        $grn_no = Input::get('grn_no');
        $id = Input::get('id');
        $grn_details = WarehouseAdminGrn::lists()
                        ->join('jocom_warehouse_admin_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')
                        ->where('grn.grn_no', '=', $grn_no)
                        ->select('grn.id','grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.price','grn_details.quantity', 'grn_details.total', 'seller.company_name as company_name','grn_details.status')->get();
        return $grn_details;

    }
    public function anyAjaxfetchseller($id) {

        $po=DB::table('jocom_purchase_order')
            ->select('seller_id')
            ->where('id','=',$id)
            ->first();
     
        $sellers = DB::table('jocom_seller')
            ->select('id', 'company_name')
            ->where('id','=',$po->seller_id)
            ->first();

        return Response::json($sellers);
    }
     public function anyAjaxfetchwarehouse($id) {

        $po=DB::table('jocom_purchase_order')
            ->select('warehouse_location_id')
            ->where('id','=',$id)
            ->first();
        $warehouse = DB::table('jocom_warehouse_location')
            ->select('id', 'name','address_1','address_2','pic_contact','pic_name')
            ->where('id','=',$po->warehouse_location_id)
            ->first();

        return Response::json($warehouse);
    }

}