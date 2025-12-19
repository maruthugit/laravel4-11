<?php

class ContestantController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    public function anyContestantslist() { 

        $contestant = DB::table('jocom_contestant')->select('id', 'contest', 'name', 'invoice_img', 'email', 'contact');

        return Datatables::of($contestant)
                        ->edit_column('invoice_img', function($row)
                        {
                            return '<img class="img img-thumbnail"  src="/images/contestant/'.$row->invoice_img.'" width="100" >';
                        })
                        ->add_column('Action', function ($p) {

                            return '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/contestant/details/'.$p->id.'"><i class="fa fa-pencil"></i></a>';
                        })
                        ->make();
    }

    public function anyIndex() {
        return View::make('contestant.index');
    }

    public function anyDetails($id) {
        $details = DB::table('jocom_contestant')->where('id', '=', $id)->first();

        return View::make('contestant.details')->with(array(
            'contest' => $details->contest,
            'name' => $details->name,
            'email' => $details->email,
            'contact' => $details->contact,
            'invoice_img' => $details->invoice_img,
            'survey1_answer' => $details->survey1_answer,
            'survey1_why' => $details->survey1_why,
            'survey2_answer' => $details->survey2_answer,
            'survey2_why' => $details->survey2_why
        ));
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
                        ->select('details.product_id', 'products.sku', 'products.name', 'details.quantity')
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
        $gdf->updated_by = Session::get('username');

        if($gdf->save()) {

            $product_ids = Input::get('product_id');
            $quantitys = Input::get('quantity');

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
                    array_push($goods_defect_details, array('gdf_id' => $gdf->id,'product_id' => $product_ids[$i], 'quantity' => $quantitys[$i]));
                }
            }

            if (count($goods_defect_details) > 0) {
                DB::table('jocom_goods_defect_form_details')->insert($goods_defect_details);
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
}
?>