<?php

class ProductUpdateV2Controller extends BaseController {

    public function getImport()
    {
        $lists = DB::table('jocom_product_price_import')->orderBy('imported_at', 'desc')->paginate(10);

        return View::make('product-update.import', ['lists' => $lists]);
    }

    public function getImportlist() {
        $list = DB::table('jocom_product_price_import')->select('id', 'filename', 'imported_by', 'imported_at');

        return Datatables::of($list)
                ->add_column('Action', function ($p) {
                    return '<a id="" class="btn btn-success" href="/product-update/files/'.$p->filename.'"><i class="fa fa-download"></i></a>';
                })
                ->make();
    }

    public function postImport() {
        if (Input::hasFile('csv')) {
            $csv_file = Input::file('csv');

            $destinationPath = Config::get('constants.PRODUCT_IMPORT_CSV_IMPORT_PATH');
            $file_name = 'product_import_' . date_format(date_create(), 'Ymd_His') . '.csv';
            $file_full_name = $destinationPath . $file_name;
            $csv_file->move($destinationPath, $file_name);
            $file = fopen($file_full_name, 'r');

            $products = array();

            DB::beginTransaction();

            DB::table('jocom_product_price_import')->insert([
                'filename' => $file_name,
                'imported_by' => Session::get('username'),
                'imported_at' => date_format(date_create(), 'Y-m-d H:i:s'),
            ]);

            $data = fgetcsv($file, 1400, ",");
            while (($data = fgetcsv($file, 1400, ",")) !== FALSE) {
                $sku = $data[0];
                $price_id = $data[1];
                $seller_id = $data[2];
                $cost = $data[3];
                $actual_price = $data[4];
                $promo_price = $data[5];

                try {

                    if ($actual_price != -1 || $promo_price != -1) {
                        $newPrice = DB::table('jocom_product_price')
                            ->where('id', '=', $price_id)
                            ->update([
                                'price' => $actual_price,
                                'price_promo' => $promo_price,
                            ]);
                    }
                    

                    $newCost = DB::table('jocom_product_price_seller')
                                ->where('product_price_id', '=', $price_id)
                                ->where('seller_id', '=', $seller_id)
                                ->update(['cost_price' => $cost]);

                    $product = DB::table('jocom_products as product')
                                ->join('jocom_product_price as price', 'product.id', '=', 'price.product_id')
                                ->join('jocom_product_price_seller as seller', 'price.id', '=', 'seller.product_price_id')
                                ->where('sku', '=', $sku)
                                ->select('product.id as id', 'product.name', 'product.status as prod_status', 'price.id as price_id', 'label', 'price', 'price_promo', 'qty', 'stock', 'stock_unit', 'p_referral_fees', 'p_referral_fees_type', 'default', 'price.status as price_status', 'p_weight', 'seller.seller_id as seller_id', 'seller.cost_price as cost_price')
                                ->first();

                    $producthistory             = new ProductsHistory;
                    $producthistory->type       = "Update Product";
                    $producthistory->product_id = $product->id;
                    $producthistory->sku = $sku;
                    $producthistory->name = $product->name;
                    $producthistory->prd_status = $product->prod_status;
                    $producthistory->price_id = $product->price_id;
                    $producthistory->label = $product->label;
                    $producthistory->price = $product->price;
                    $producthistory->price_promo = $product->price_promo;
                    $producthistory->qty = $product->qty;
                    $producthistory->stock = $product->stock;
                    $producthistory->stock_unit = $product->stock_unit;
                    $producthistory->p_referral_fees = $product->p_referral_fees;
                    $producthistory->p_referral_fees_type = $product->p_referral_fees_type;
                    $producthistory->default = $product->default==''? $default : $product->default;
                    $producthistory->pri_product_id = $product->id;
                    $producthistory->pri_status = $product->price_status;
                    $producthistory->p_weight = $product->p_weight;

                    $producthistory->updated_by = Session::get('username');
                    $producthistory->seller_id = $product->seller_id;
                    $producthistory->cost = $product->cost_price;
                    $producthistory->save();

                } catch (\Exception $e) {

                    DB::rollback();

                    return Redirect::back()->with('message', $e->getMessage());
                }
                
            }

            DB::commit();

            return Redirect::back()->with('success', 'Import Success');
        } else {
            return Redirect::back()->with('message', 'Please select file');
        }
    }

    public function anyFiles($file=null) {

        $path = Config::get('constants.PRODUCT_IMPORT_CSV_IMPORT_PATH'); //"media/csv/import/cost_price";
            
        if(is_file($path.$file)) {
            //header('Content-Type: application/csv');
            return Response::download($path.$file);
        }
        else {
            echo "<br>File not exists!";
        }

    }


}

?>