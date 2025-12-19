<?php

// use Datatables;
// use App\Models\InventoryIssuance;
// use Illuminate\Support\Facades\Validator;

class InventoryManagerController extends BaseController
{
    public function __construct()
    {
        // return $this->middleware('auth');
    }

    public function index()
    {
        return View::make('inventory-management.index');
    }

    public function getDatatabledata()
    {
        $products = \DB::table('jocom_warehouse_products as w_products')
                    ->leftJoin('jocom_products', 'jocom_products.id', '=', 'w_products.product_id')
                    ->leftJoin('jocom_product_price as product_detail', 'product_detail.product_id', '=', 'w_products.product_id')
                    ->leftJoin('jocom_warehouse_productslinks', function($join){
                            $join->on('jocom_warehouse_productslinks.base_product_id', '=', 'w_products.product_id')
                            ->where('jocom_warehouse_productslinks.status', '=', 1);
                        })
                    ->leftJoin('jocom_warehouse_products_baselinks', function($join){
                            $join->on('jocom_warehouse_products_baselinks.variant_product_id', '=', 'w_products.product_id')
                            ->where('jocom_warehouse_products_baselinks.status', '=', 1);
                        })
                    ->select(
                        'jocom_products.id as pid', 'jocom_products.name', 'jocom_products.sku',
                        'jocom_products.qrcode', 'jocom_products.is_base_product as baseProduct', 'jocom_products.related_product',
                        'product_detail.stock_unit as unit', 'w_products.stockin_hand',
                        'w_products.reserved_in_hand', 'w_products.stocklevel', 'w_products.status as WHStatus',
                        'jocom_warehouse_productslinks.product_id as linkid','jocom_warehouse_productslinks.base_product_id as baseid',
                        'jocom_warehouse_products_baselinks.product_id as base_id'
                        )
                    ->where('w_products.status', '=', 1)
                    ->where('w_products.active', '=', 1);

        return \Datatables::of($products)
            ->addColumn('action', function ($product) {
                if(Warehouse::isExistsBase($product->pid) == 0){
                    return '<div class="text-center">
                        <a href="#" id="'.$product->pid.'" class="btn btn-warning sub" data-toggle="modal"><i class="fa fa-minus"></i></a>
                        <a href="#" id="'.$product->pid.'" class="btn btn-success add" data-toggle="modal"><i class="fa fa-plus"></i></a>
                        <a href="#" id="'.$product->pid.'" class="btn btn-info view" data-toggle="modal"><i class="fa fa-eye"></i></a>
                    </div>';
                } else {
                    return '<div class="text-center alert alert-info alert-message">Not Stock Product</div>';
                }
            })
            ->editColumn('stockin_hand', function($p){
                if($p->stockin_hand == 0){
                    return '<div class="alert alert-danger text-center" role="alert">'.$p->stockin_hand.' No Stock</div>';
                } elseif($p->stockin_hand <= 10){
                return '<div class="alert alert-warning text-center" role="alert">'.$p->stockin_hand.' Critical Level</div>';
                } else {
                    return '<div class="text-center">'.$p->stockin_hand.'</div>';
                }
            })
            ->editColumn('unit', function($p){
                    if($p->unit == "" || $p->unit == null){
                    return '<div class="text-center">-</div>';
                    } else {
                    return '<div class="text-center">'.$p->unit.'</div>';
                    }
                })
            ->editColumn('related_product', function($product){
                $related = explode(",",$product->related_product);
                return $related[0];
            })
            ->editColumn('WHStatus',
                    '@if($WHStatus == 1)
                    <h5><span class="label label-lg label-info">Active</h5>
                    @else
                    <h5><span class="label label-lg label-warning">Inactive</h5>
                    @endif'
                    )
            // ->rawColumns(['action', 'stockin_hand', 'unit', 'WHStatus'])
            ->make(true);
    }

    public function outStocks()
    {
        $validator = Validator::make(Input::all(), [
            'stockQty' => 'required|numeric',
            'stockReceivedBy' => 'required',
            'stockRemarks' => 'required|min:4|max:255'
        ]);

        if ($validator->fails())
        {
            return json_encode($validator->messages());
        }
        else
        {
            $inventory = \InventoryIssuance::create([
                'product_id' => Input::get('stockId'),
                'product_sku' => Input::get('stockSku'),
                'quantity' => Input::get('stockQty'),
                'sender_receiver' => Input::get('stockReceivedBy'),
                'stock_status' => "out",
                'remarks' => Input::get('stockRemarks'),
                'updated_by' => session::get('user_id')
            ]);

            $warehouse_product = \DB::table('jocom_warehouse_products')->where('product_id', '=', Input::get('stockId'))->first(['stockin_hand']);

            $quantity = round($warehouse_product->stockin_hand - Input::get('stockQty'));

            $update = \DB::table('jocom_warehouse_products')->where('product_id', '=', Input::get('stockId'))->update(['stockin_hand' => $quantity]);

            return Response::json(['status'=>true, 'data'=>$inventory, 'message' => 'Stocks have been updated']);
        }
    }

    public function inStocks()
    {

        $inventory = \InventoryIssuance::create([
            'product_id' => Input::get('stockId'),
            'product_sku' => Input::get('stockSku'),
            'quantity' => Input::get('stockQty'),
            'stock_status' => Input::get('stockStatus'),
            'remarks' => Input::get('stockRemarks'),
            'expiry_date' => Input::get('stockExpiryDate'),
            'driver' => Input::get('driverInfo'),
            'updated_by' => session::get('user_id')
        ]);

        $warehouse_product = \DB::table('jocom_warehouse_products')->where('product_id', '=', Input::get('stockId'))->first(['stockin_hand']);

        $quantity = round($warehouse_product->stockin_hand + Input::get('stockQty'));

        $update = \DB::table('jocom_warehouse_products')->where('product_id', '=', Input::get('stockId'))->update(['stockin_hand' => $quantity]);

        return Response::json(['status'=>true, 'data'=>$inventory, 'message' => 'On #'.Input::get('stockId').' Stocks have been added']);

    }

    public function fetchStockData()
    {
        $id = Input::get('id');

        $inventory =  \DB::table('jocom_warehouse_products as warehouse')
                        ->leftJoin('jocom_products as products', 'products.id', '=', 'warehouse.product_id')
                        ->select('warehouse.product_id as id', 'warehouse.stockin_hand as stocksinHand', 'products.name as name', 'products.sku as sku')
                        ->where('product_id', '=', $id)
                        ->first();

        $output = array(
            'stockId' =>  $inventory->id,
            'stockName' =>  $inventory->name,
            'stockSku' =>  $inventory->sku,
            'stockQty' =>  $inventory->stocksinHand,
        );

        echo json_encode($output);
    }

    public function fetchStockLatestHistory()
    {
        $id = Input::get('id');

        $inventory = \InventoryIssuance::where('product_id', '=', $id)->orderBy('id', 'desc')->take(5)->get();

        foreach ($inventory as $key => $invent) {
            $invent->updated_by = \User::where('id', '=', $invent->updated_by)->first(['id', 'email', 'full_name']);
        }

        echo json_encode($inventory);

    }

    public function history()
    {
        $id = Input::get('productid');

        $inventory = \InventoryIssuance::where('product_id', '=', $id)->orderBy('id', 'desc')->get();

        foreach ($inventory as $key => $invent) {
            $invent->updated_by = \User::where('id', '=', $invent->updated_by)->first(['id', 'email', 'full_name']);
        }

        return View::make('inventory-management.stocks-history')->with(['data' => $inventory]);
    }

    public function historyTable()
    {
        $id = Input::get('productid');

        $inventory = \InventoryIssuance::where('product_id', '=', $id)->select(['id', 'product_id', 'product_sku', 'quantity', 'stock_status', 'remarks', 'updated_by', 'updated_at']);

        foreach ($inventory as $key => $invent) {
            $invent->updated_by = \User::where('id', '=', $invent->updated_by)->first(['id', 'email', 'full_name']);
        }

        return \Datatables::of($inventory)
                        ->editColumn('stock_status', function($in){
                            if($in->stock_status == "out"){
                                return '<span class="label label-info">'.$in->stock_status.'</span>';
                            } elseif($in->stock_status == "return") {
                                return '<span class="label label-warning">'.$in->stock_status.'</span>';
                            } else {
                                return '<span class="label label-lg label-success">'.$in->stock_status.'</span>';
                            }
                        })
        ->make(true);
    }

    public function reports()
    {
        return View::make('inventory-management.reports');
    }

    public function stockDifferences()
    {
        $id = Input::get('id');
        $stockInHand = \DB::table('jocom_warehouse_products')->where('product_id', '=', $id)->select(['stockin_hand'])->get();
        return $stockInHand;
    }

    // PDF ---------------------------
    public function exportsAllToPdf()
    {
        $id = Input::get('id');
        $data = [];
        $from = \Carbon\Carbon::createFromFormat('d/m/Y', Input::get('stock_report_from'));
        $to = \Carbon\Carbon::createFromFormat('d/m/Y', Input::get('stock_report_to'));

        $inventory = \InventoryIssuance::select(['id', 'product_id', 'product_sku', 'quantity', 'stock_status', 'remarks', 'updated_by', 'updated_at'])
                        ->where('product_id', '=', $id)
                        ->orWhere('updated_at', '>=', $from)
                        ->orWhere('updated_at', '<=', $to)
                        ->get();

        foreach ($inventory as $key => $invent) {
            $invent->updated_by = \User::where('id', '=', $invent->updated_by)->first(['id', 'email', 'full_name']);
        }

        $data['id'] = $id;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['items'] = $inventory;


        $pdf = \PDF::loadView('inventory-management.exports.pdf-report', $data);
        return $pdf->download('report-'. $id .'.pdf');
    }

    // Excell ---------------------------
    public function exportsAllToExcel($id)
    {
        // $inventories = exportAllReportsToExcell($id);
    }

    public function getDatatable()
    {
        $id = Input::get('productid');

        return \Datatable::collection(\InventoryIssuance::all(array('id','product_id', 'product_sku', 'quantity', 'stock_status', 'remarks', 'updated_by')))
            ->showColumns('id','product_id', 'product_sku','quantity', 'stock_status', 'remarks')
            ->searchColumns('id','product_id', 'stock_status')
            ->orderColumns('id','product_id', 'stock_status')
            ->make();
    }

    // get View of Purchase Lists---------------
    public function sortedIndex()
    {
        return View::make('inventory-management.sorted-items.index');
    }

    // Get List of Sorted Transactions-------------
    public function getSortedTransactions()
    {
        $lists = DB::table('jocom_sort_generator AS JSG')
                        ->select(array('JSG.id','JSG.batch_no','JSG.filename','JSG.collection','JSG.total_success','JSG.total_failed','JSG.total_duplicate','JSG.created_at','JSG.created_by'))
                        ->where('JSG.activation',1);
                        // ->orderBy('JSG.id','DESC');

        return Datatables::of($lists)
            ->addColumn('status', function($list){
                $count = DB::table('jocom_sort_transaction AS JST')
                            ->leftJoin('jocom_sort_generator as JSG', 'JST.sort_id', '=', 'JSG.id')
                            ->where('JST.sort_id', '=', $list->id)
                            ->count();
                return ($count > 1) ? '<span class="label label-primary task-label">'.$count.'</span>' : '<span class="label label-primary task-label">'.$count.'</span>';
            })
            ->addColumn('action', function($list){
                return '<div class="buttons">
                    <a href="/warehouse/purchase-requests-list/'. $list->id .'" title="show purchase lists" data-title="'.$list->batch_no.'" class="btn btn-success btn-md show-task-modal"><span class="glyphicon glyphicon-check"></span> Complete Fresh Inventory &nbsp;&nbsp;&nbsp;<span style="display:none;" id="loading-on-'.$list->batch_no.'" class="fa fa-spinner fa-spin"></span></a>
                </div>';
            })
            ->make(true);

        // return View::make('inventory-management.sorted-items.task-index', compact('lists'));
    }

    public static function purchaseList($sort_id){

        $sort_id = $sort_id;
        $finalList = array();
        $removedProduct = array();

        $query = "SELECT JST.id,JST.transaction_id,LTI.name,LTI.sku,LTI.label,
            COUNT(JST.transaction_id) AS 'TotalTransactions',SUM(LTI.qty_order) AS 'TotalOrderSet' ,
            LTI.product_price_id, JPP.price, JPP.price_promo,
            JSTD.is_completed, JSTD.purchased_quantity_set, JSTD.where_purchased, JSTD.inventory_remarks
            FROM jocom_sort_transaction as JST
            LEFT JOIN jocom_sort_transaction_details AS JSTD ON JSTD.sort_transaction_id = JST.id
            LEFT JOIN logistic_transaction_item AS LTI ON LTI.id = JSTD.logistic_item_id
            LEFT JOIN jocom_product_price AS JPP ON JPP.id = LTI.product_price_id
            WHERE JST.sort_id = ".$sort_id." AND JST.generated = 0 AND JSTD.is_failed = 2
            GROUP BY LTI.product_price_id; ";

       $list = DB::select($query);

       $removeItems =  DB::table('jocom_sort_remove_purchase AS JSRP')
                    ->where("JSRP.sort_id",$sort_id)
                    ->where("JSRP.status",1)
                    ->get();

        foreach ($removeItems as $keyR => $valueR) {
            array_push($removedProduct, $valueR->product_id);
        }

       foreach ($list as $key => $value) {

            $baseProduct = array();
            $TotalTransactions = 0;
            $sort_transaction_id = $value->id;
            $transactionID = $value->transaction_id;

            $baseItem = DB::table('jocom_sort_transaction_details AS JSTD')
                        ->select(array(
                            'JP.name','JP.sku','JP.id','JPP.label',DB::raw('SUM(JSTD.order_quantity) as order_quantity'),'JPP.price','JPP.price_promo',
                            'JSTD.is_completed', 'JSTD.purchased_quantity_set', 'JSTD.where_purchased', 'JSTD.inventory_remarks'
                            ))
                        ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
                        ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                        ->where("JST.sort_id",$sort_id)
                        ->where("JPP.default",1)
                        ->where("JSTD.is_failed",2)
                        ->where("LTI.product_price_id",$value->product_price_id)
                        ->groupBy('JSTD.product_id')
                        ->get();

           $baseItem2 =  DB::table('jocom_product_base_item AS JPBI')
                    ->where("JPBI.price_option_id",$value->product_price_id)
                   ->where("JPBI.status",1)
                    ->get();

            if(count($baseItem2) > 0){

                foreach ($baseItem as $kB => $vB) {

                    // GET Price //
                    $baseProductPrice =  DB::table('jocom_transaction_details_base_product AS JTDBP')
                                            ->select(array(
                                                    'JTDBP.*'
                                            ))
                                            ->leftjoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'JTDBP.transaction_details_id')
                                            ->where("JTD.transaction_id",$transactionID)
                                            ->where("JTD.p_option_id",$value->product_price_id)
                                            ->first();
                    // GET Price //

                    if(!in_array($vB->id, $removedProduct)){
                        array_push($baseProduct, array(
                            "sort_trans_id" => $sort_transaction_id,
                            "product_name" => $vB->name,
                            "product_id" => $vB->id,
                            "product_label" => $vB->label,
                            "product_sku" => $vB->sku,
                            "unit_price" => $baseProductPrice->price > 0 ? $baseProductPrice->price : '-',
                            "quantityPerSet" =>  $value->qty_order_set ,
                            "totalQuantity" => $vB->order_quantity,
                            "is_completed" => $vB->is_completed,
                            "purchased_quantity_set" => $vB->purchased_quantity_set,
                            "where_purchased" => $vB->where_purchased,
                            "inventory_remarks" => $vB->inventory_remarks
                        ));
                    }

                }

            }


            $totalTransaction = DB::table('jocom_sort_transaction_details AS JSTD')
                                    ->leftjoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JSTD.logistic_item_id')
                                    ->leftjoin('jocom_sort_transaction AS JST', 'JST.id', '=', 'JSTD.sort_transaction_id')
                                    ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JSTD.product_id')
                                    ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                                    ->where("JST.sort_id",$sort_id)
                                    ->where("JPP.default",1)
                                    ->where("JSTD.is_failed",2)
                                    ->where("LTI.product_price_id",$value->product_price_id)
                                    ->groupBy('JSTD.product_id')
                                    ->count();


            $ProductInfo = DB::table('jocom_products')->where("sku",$value->sku)->first();

            if(!in_array($ProductInfo->id, $removedProduct)){
                array_push($finalList, array(
                    "sort_trans_id" => $sort_transaction_id,
                    "product_name" => $value->name,
                    "product_sku" => $value->sku,
                    "product_id" => $ProductInfo->id,
                    "product_label" => $value->label,
                    "option_id" => $value->product_price_id,
                    "unit_price" => $value->price,
                    "total_order" => $value->TotalTransactions,
                    "stock_type" => $value->type_product,
                    "req_qty" => $value->TotalOrderSet,
                    //"in_stock" => $value->in_stock,
                    //"balance_need" => $value->req_qty - $value->in_stock,
                    "is_completed" => $value->is_completed,
                    "purchased_quantity_set" => $value->purchased_quantity_set,
                    "where_purchased" => $value->where_purchased,
                    "inventory_remarks" => $value->inventory_remarks,
                    "base_product" => $baseProduct
                ));
            }

       }
        //    echo "<pre>";
        //    print_r($finalList);
        // return View::make('inventory-management.sorted-items.task-index', compact('finalList'));
        return View::make('inventory-management.fresh-inventory.index', compact('finalList'));
    }

    // public function updateListTask($sort_id, $product_id)
    public function updateListTask()
    {
        
        $data = Input::get('data');
        $bdata = Input::get('baseItemData');
        
        if(!empty($data) && $data !== null && $bdata === null){
            foreach ($data as $k => $value) {
                if (isset($data) && $data !== null && $data['base'] == null) {
                    $query = DB::table('jocom_sort_transaction_details')
                            ->where('sort_transaction_id', '=', $value['sorted_tansaction_id'])
                            ->where('product_id', '=', $value['product_id'])
                            ->update([
                                'is_completed' => ($value['check_completed'] || !empty($value['where_purchased'])) ? $value['check_completed'] : null,
                                'purchased_quantity_set' => $value['purchased_quantity_set'],
                                'where_purchased' => $value['where_purchased'],
                                'inventory_remarks' => $value['inventory_remarks'],
                                'completed_at' => \Carbon\Carbon::now(),
                                'completed_by' => Auth::id(),
                                'updated_at' => \Carbon\Carbon::now()
                            ]);
                }
            }
        }

        if (isset($bdata) && $bdata !== null) {
            foreach ($bdata as $k => $bv) {
                $query = DB::table('jocom_sort_transaction_details')
                            ->where('sort_transaction_id', '=', $bv['sorted_tansaction_id'])
                            ->where('product_id', '=', $bv['product_id'])
                            ->update([
                                'is_completed' => ($bv['check_completed'] || !empty($bv['where_purchased'])) ? $bv['check_completed'] : null,
                                'purchased_quantity_set' => $bv['purchased_quantity_set'],
                                'where_purchased' => $bv['where_purchased'],
                                'inventory_remarks' => $bv['inventory_remarks'],
                                'completed_at' => \Carbon\Carbon::now(),
                                'completed_by' => Auth::id(),
                                'updated_at' => \Carbon\Carbon::now()
                            ]);
            }
        }
        
        return Redirect::to('/warehouse/sorted/purchase-todos')->with(["updated_status" => "Fresh Inventory Updated"]);
    }

    public function getFreshInventoryHistory()
    {
        return View::make('inventory-management.sorted-items.purchase-requests-history');
    }

    public function historyOfPurchaseRequests()
    {
        $query = DB::table('jocom_sort_transaction_details as JSTD')
                    ->leftJoin('jocom_sort_transaction as JST', 'JSTD.sort_transaction_id', '=', 'JST.id')
                    ->leftJoin('jocom_sort_generator as JSG', 'JST.sort_id', '=', 'JSG.id')
                    ->leftJoin('jocom_sys_admin as JSA', 'JSTD.completed_by', '=', 'JSA.id')
                    ->select(['JSTD.id', 'JSG.batch_no', 'JSTD.product_id', 'JSG.created_at', 'JSG.created_by', 'JSTD.completed_at', 'JSA.full_name as completed_by', 'JSTD.is_completed as status']);

        return Datatables::of($query)
                    ->addColumn('status', function($list){
                        return ($list->status == 1) ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                    })
                    // ->addColumn('action', function($list){
                    //     return '<div class="buttons">
                    //         <a href="/warehouse/purchase-requests-list/'. $list->id .'" title="show purchase lists" data-title="'.$list->batch_no.'" class="btn btn-success btn-md show-task-modal"><span class="glyphicon glyphicon-check"></span> Check &amp; Complete &nbsp;&nbsp;&nbsp;<span style="display:none;" id="loading-on-'.$list->batch_no.'" class="fa fa-spinner fa-spin"></span></a>
                    //     </div>';
                    // })
                    ->make(true);
    }
}
