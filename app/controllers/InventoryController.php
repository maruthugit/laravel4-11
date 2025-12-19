<?php 

class InventoryController extends BaseController {

    /**
     * Default listing for all coupon.
     * @return [type] [description]
     */
    public function anyIndex()
    {
        return View::make('inventory.history_listing')->with(Input::all());
    }

    public function anyListing()
    {
        // if (Input::has('qrcode'))
        // var_dump(Input::all());exit;

        $history = DB::table('jocom_inventory_history AS a')
                ->select(array(
                    'a.id', 
                    'a.qrcode',
                    'a.priceopt',
                    'a.type',
                    'a.qty',
                    'a.stock',
                    'a.stock_unit',
                    'a.pre_stock',
                    'a.username',
                    'a.update_date',
                    'b.name',
                    'c.label',
                ))
                ->leftJoin('jocom_products AS b', 'b.qrcode', '=', 'a.qrcode')
                ->leftJoin('jocom_product_price AS c', 'c.id', '=', 'a.priceopt')
                ;

        
        if (Input::has('qrcode'))
            $history = $history->where('a.qrcode', '=', Input::get('qrcode'));

        // var_dump($history);exit;
        
       

        return Datatables::of($history)
                ->edit_column('type', function($row)
                {
                    switch ($row->type)
                    {
                        case "plus":
                            return '<span class="label label-success">+</span>';
                            break;
                        case "minus":
                            return '<span class="label label-danger">-</span>';
                            break;
                        default:
                            return '<span class="label label-warning">=</span>';
                            break;
                    }
                })
                ->make(true);


                
        /*
        return Datatables::of($history)
                ->edit_column('amount', '<?php if ($amount_type=="Nett") echo "RM".number_format($amount,2); else if ($amount_type=="%") echo number_format($amount,0)."%"; ?>')
                ->edit_column('status', '<?php if ($status==1) echo "Active"; elseif ($status==0) echo "Inactive"; ?>')
                // ->edit_column('status', '{{ucwords($status);}}')
                ->add_column('Action', $actionBar)
                ->make(true);
        */
       
    }

    
    public function anyUpdatestock() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            DB::beginTransaction();
            
            /* UPDATE STOCK */
            
            $actualStock = Input::get('actualStock');
            $qtyStock = Input::get('qtyStock');
            $priceOptionID = Input::get('priceOptionID');
            $productID = Input::get('productID');
            
            // Product Info
            $Product = Product::find($productID);
  
            // Price Option Info
            $PriceOption = Price::where("id",$priceOptionID)
                    ->where("product_id",$productID)
                    ->first();
            
            $currentQty = $PriceOption->qty;
            $currentActualStock = $PriceOption->stock;
            $newStock = $currentActualStock + $actualStock;
            $newQty = $currentQty + $qtyStock;
            
            $PriceOption->qty = $newQty;
            $PriceOption->stock = $newStock;
            $PriceOption->save();
            
            /* UPDATE INVENTORY HISTORY */
            
            $qrcode = $Product->qrcode;
            $priceopt = $PriceOption->id;
            $type = 'plus';
            $qty = $actualStock;
            $pre_stock = $currentActualStock;
            $username = $Product->qrcode;
            
            $row = array(
                'qrcode'        => $qrcode,
                'priceopt'      => $priceopt,
                'type'          => $type,
                'qty'           => $qty,
                'stock'         => $newStock,
                'stock_unit'    => $PriceOption->stock_unit,
                'pre_stock'     => $pre_stock,
                'username'      => Session::get('username'),
                'update_date'   => date('Y-m-d H:i:s'),
            );
            
            if($actualStock > 0){
                $insert = DB::table('jocom_inventory_history')->insert($row);
}

            $data['inventoryResult'] = array(
                "stock_history" => $row,
                "product_id" => $productID,
                "price_option_id" => $priceOptionID,
                "new_total_qty" => $newQty,
                "new_total_stock" => $newStock,
                "updated_date" => date('Y-m-d H:i:s')
            );
            /* UPDATE INVENTORY HISTORY */
            
             
        
        } catch (Exception $ex) {
            } finally {
                if ($is_error) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function anyReport(){

        
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            /*
             * Extract inventory report from date range basis
             */
            
            $from_date = Input::get('transaction_from');
            $to_date = Input::get('transaction_to');
            $report_type = Input::get('report_type');
//            echo "<pre>";
//            print_r(Input::all());
//            echo "</pre>";
            
            if($report_type == 1){ // Base on Invoice
                $Transaction = DB::table('jocom_transaction AS JT')
                    
                    ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')
                    ->select('JT.transaction_date', 'JT.id', 'JT.invoice_date', 'JT.invoice_no', 'JT.do_no', 'LTI.product_id','LTI.product_price_id','LTI.sku', 'LTI.name', 'LTI.label', 'LTI.qty_order'
                        ,DB::raw("CASE 
                            WHEN LT.status = 0 THEN 'Pending'
                            WHEN LT.status = 1 THEN 'Undelivered'
                            WHEN LT.status = 2 THEN 'Partial Send'
                            WHEN LT.status = 3 THEN 'Returned'
                            WHEN LT.status = 4 THEN 'Sending'
                            WHEN LT.status = 5 THEN 'Sent'
                            WHEN LT.status = 6 THEN 'Cancelled'
                            ELSE 'NOT LOG IN'
                            END AS 'LogisticStatus'
                            ")
                        )
                    ->where("JT.invoice_date",">=",$from_date)
                    ->where("JT.invoice_date","<=",$to_date)
                    ->where("JT.status",'completed')
                    ->where("JT.invoice_no","<>","")
                    ->orderBy('JT.invoice_date','asc')
                    ->get();
                
                $TransactionCollection = json_decode(json_encode($Transaction), True);
                
                
                $base_stock_list_column = array();
                $base_stock_list_qty = array();
                
                $column_counter = 1;
                foreach ($Transaction as $key => $value) {
                    // Find Details
                    
                    $ProductBaseItem = DB::table('jocom_product_base_item AS JPBI')
                    ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')
                    ->select('JP.qrcode','JP.name','JPBI.price_option_id','JPBI.quantity')
                    ->where("JPBI.price_option_id",$value->product_price_id)
                    ->where("JPBI.status",1)
                    ->get();
                    
                    foreach ($ProductBaseItem as $keyPB => $valuePb) {
                        $base_stock_list_column[$valuePb->qrcode] = array(
                            "position"=>$column_counter,
                            "columnCode"=>$valuePb->qrcode,
                            "columnName"=>$valuePb->qrcode."/".$valuePb->name
                        );
                        
                        $base_stock_list_qty[$valuePb->price_option_id][] = array(
                            "jcode"=>$valuePb->qrcode,
                            "quantity"=>$valuePb->quantity
                        );
                        
                        //array_push($base_stock_list_column, $sub_column_array);

}
                    $column_counter++;
                    
                }
                
                $data = array(
                    "totalColumBase"=>$column_counter,
                    "baseStockListColumn"=>$base_stock_list_column,
                    "baseStockListColumnQty"=>$base_stock_list_qty,
                    "from_date" => $from_date,
                    "to_date" => $to_date,
                    "transaction" => $TransactionCollection
                );
                
//                echo "<pre>";
//                print_r($data);
//                echo "</pre>";
//                die();
                
                return Excel::create('inventory('.$from_date.'- '.$to_date.')', function($excel) use ($data) {
                    $excel->sheet('Inventory Invoiced Transaction', function($sheet) use ($data)
                    {
                        $sheet->loadView('admin.inventoryinvoiced', array('data' =>$data));
                    });
                })->download('xls');
                
            }
            
            if($report_type == 2){
                $listProduct = array();
                $qrcodes = Input::has('jcode') ? explode(',', str_replace(' ', '', Input::get('jcode'))) : ['all'];
//              echo "<pre>";
//              print_r($qrcodes);
//              echo "</pre>";
                
                foreach ($qrcodes as $key => $value) {
                        
                        $product = Product::where('qrcode',$value)->first();
                        $Transaction = DB::table('jocom_transaction AS JT')
                    
                        ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                        ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')
                        ->join('jocom_product_base_item AS JPBI', 'JPBI.price_option_id', '=', 'LTI.product_price_id')
                        ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPBI.product_base_id')
                        ->select('JT.transaction_date', 'JT.id', 'JT.invoice_date', 'JT.invoice_no', 'JT.do_no', 'LTI.product_id','LTI.product_price_id','LTI.sku', 'LTI.name', 'LTI.label', 'LTI.qty_order', 'JPBI.quantity AS productBaseQty'
                            ,DB::raw("CASE 
                                WHEN LT.status = 0 THEN 'Pending'
                                WHEN LT.status = 1 THEN 'Undelivered'
                                WHEN LT.status = 2 THEN 'Partial Send'
                                WHEN LT.status = 3 THEN 'Returned'
                                WHEN LT.status = 4 THEN 'Sending'
                                WHEN LT.status = 5 THEN 'Sent'
                                WHEN LT.status = 6 THEN 'Cancelled'
                                ELSE 'NOT LOG IN'
                                END AS 'LogisticStatus'
                                ")
                            )
                        ->where("JT.invoice_date",">=",$from_date)
                        ->where("JT.invoice_date","<=",$to_date)
                        ->where("JT.status",'completed')
                        ->where("JT.invoice_no","<>","")
                        ->where("JP.qrcode",$value)
                        ->where("JPBI.status",1)
                        ->get();
                       
                                      
                        $listProduct[] = array(
                            "productName"=> $product->name,
                            "productJcode"=> $product->qrcode,
                            "productSKU"=> $product->sku,
                            "transaction"=> $Transaction,
                        );
                        
                }
                $data['product'] = $listProduct;
                
//                 echo $value;
//                                      echo "<pre>";
//                                      print_r($listProduct);
//                                      echo "</pre>";
//                
//                die();
     
                return Excel::create('inventory('.$from_date.'- '.$to_date.')', function($excel) use ($data) {
                    
                    foreach ($data['product'] as $key => $value) {
                        $productInfo = $value;
                        $excel->sheet($value['productJcode'], function($sheet) use ($productInfo)
                        {
                            $sheet->loadView('admin.templateInventory', array('data' =>$productInfo));
                        });
                    }
                })->download('xls');
                
            }
 
            
         
        } catch (Exception $ex) {
            echo $ex->getMessage();
        } finally {
            
        }
        
        
    }
    
    public function anyInventoryexport2(){
        
         return View::make('admin.inventoryinvoiced');
        
    }
    
    public function anyInventoryexport(){
        
         return View::make('inventory.inventory_export');
        
    }
    

}
?>