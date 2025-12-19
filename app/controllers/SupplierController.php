<?php

class SupplierController extends BaseController {

    public function __construct()
    {
        
        $this->beforeFilter('auth');
        // echo "<br>check authentication ";
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function anysuppliers() { 
   

            $supplier = Supplier::select('id', 'supplier_name', 'supplier_code');
                           
                     
      
                    
                            // ->get();

        return Datatables::of($supplier)
                         ->add_column('Action', function($row) {

                                      
                               
                                  $edit .= '
                                            <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('supplier/edit').$row->id.'"><i class="fa fa-pencil"></i></a>
                                         
                                            ';

                          
                                          
                                  
                                                                     

                                
                                        return $edit;
                                    })
                                    ->make();
    }

    /**
     * Display a listing of the customer.
     *
     * @return Response
     */
    public function index()
    {
        $supplier   = Supplier::all();

        return View::make('supplier.index', ['supplier' => $supplier]);
    }


    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function Create()
    {   
         return View::make('supplier/create');
    }


     /**
     * Store a newly created customer in storage.
     *
     * @return Response
     */
    public function Store()
    {
            $validator = Validator::make(Input::all(), [
            'supplier_code'   => 'required|unique:jocom_supplier',
             'supplier_name'   => 'required|unique:jocom_supplier',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

           $supplier         = new Supplier;
           $supplier->supplier_code       = Input::get('supplier_code');
            $supplier->supplier_name  = Input::get('supplier_name');
 

        
        
        if ($supplier->save()) {
            Session::flash('success', 'supplier added successfully.');
        }

        return Redirect::to('supplier');
        
    }

  

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return Response
     */
    public function Edit($id)
    {
        $supplier         = Supplier::find($id);
     
      



        return View::make('supplier.edit')->with(array(
                        'supplier'    => $supplier
                       
        ));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  int  $id
     * @return Response
     */
     public function Update($id)
    {
         
       
         $validator = Validator::make(Input::all(), [
            'supplier_code'   => 'required',
             'supplier_name'   => 'required|unique:jocom_supplier',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $supplier = Supplier::find($id);

          $supplier->supplier_code       = Input::get('supplier_code');
            $supplier->supplier_name  = Input::get('supplier_name');
  
       

        
       

            if ($supplier->save()) {
                Session::flash('success', 'supplier updated successfully.');
            }
      

        return Redirect::to('supplier');
    }
 
    /**
     * Remove the specified seller from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $file_name      = Seller::getOldFilename($id);
        $date           = date("Ymd_His");

        if(file_exists(Config::get('constants.SELLER_FILE_PATH')."/".$file_name))
            rename(Config::get('constants.SELLER_FILE_PATH')."/".$file_name, Config::get('constants.ARC_SELLER_FILE_PATH')."/". $date . "_" . $file_name);

        $seller = Seller::find($id);
        $seller->active_status  = '2';
        $seller->timestamps     = false;
        $seller->modify_date    = date("Y-m-d H:i:s");
        $seller->modify_by      = Session::get('username');
        $seller->save();
        
        // Seller::destroy($id);
        // $user->active_status = '0';
        // $user->timestamps = false;
        // $user->save();

        $insert_audit = General::audit_trail('SellerController.php', 'delete()', 'Delete Seller', Session::get('username'), 'CMS');

        return Redirect::to('/seller/index');
    }
    
    public function xyz($id){


 $history = DB::table('jocom_pallet_history AS JTD')
                           ->select('JTD.pallet_id','JTD.supplier_id')
                           ->leftJoin('jocom_pallet','jocom_pallet.id','=','JTD.pallet_id')
                            ->leftJoin('jocom_supplier','jocom_supplier.id','=','JTD.supplier_id')

                            ->where('JTD.id',$id)
                          
                            ->first();
             
$pallet_id=$history->pallet_id;
$supplier_id=$history->supplier_id;

                 
      $pallet = DB::table('jocom_pallet_details AS JTD')
                            ->select('JTD.*','jocom_pallet.pallet_Description','jocom_pallet.pallet_price','jocom_pallet.pallet_code','jocom_supplier.supplier_name')
                         
                              ->leftJoin('jocom_pallet','jocom_pallet.id','=','JTD.pallet_id')
                               ->leftJoin('jocom_supplier','jocom_supplier.id','=','JTD.supplier_id')
                               

                    ->where('JTD.pallet_id',$pallet_id)
                     ->where('JTD.supplier_id',$supplier_id)
                 
                          
                       
                          
                            ->get();

                             $data = array(
                       
                         
                        'pallet'      =>$pallet
                           
                    );


 return Excel::create(date("dmyHis"), function($excel) use ($data) {
                    $excel->sheet('pallet', function($sheet) use ($data)
                    {   
                        $sheet->loadView('pallet.templatepallet', array('data' =>$data, 'pallet' =>$pallet));
                        
                    });
                })->download('xls');



  
        




    }
}
?>