<?php

class PalletController extends BaseController {

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
    public function anypallets() { 
   

    
            $pallet = Pallet::select([
                                       'jocom_pallet.id',
                                        'jocom_pallet.pallet_code',
                                        'jocom_pallet.pallet_Description',
                                        'jocom_pallet.pallet_price',
                                        'jocom_pallet_history.stockin',
                                        'jocom_supplier.supplier_name',
                                        'jocom_pallet_history.quantity',
                                        'jocom_pallet_history.id as sid',
                                        'jocom_pallet_history.status',
                                      
                                       
                                         ])

                                    ->leftJoin('jocom_pallet_history', 'jocom_pallet_history.pallet_id', '=', 'jocom_pallet.id')
                                     ->leftJoin('jocom_supplier', 'jocom_supplier.id', '=', 'jocom_pallet_history.supplier_id');
                                
                                      return Datatables::of($pallet)


                         ->add_column('Action', function($row) {

                                       if($row->status==1)
                                {


                                    $edit .= '
                                           <a class="btn btn-danger" title="" data-toggle="tooltip" href="'.url('pallet/history').$row->sid.'"><i class="fa fa-history"></i></a>
                                         <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('supplier/tea').$row->sid.'">Export</a>
                                         <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('pallet/edit').$row->id.'"><i class="fa fa-pencil"></i></a>
                                            ';
                                }

                                else{

                                   $edit .= ' <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('pallet/edit').$row->id.'"><i class="fa fa-pencil"></i></a>
                                            ';
                                }
                               
                                

                          
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
        $pallet   = Pallet::all();

        return View::make('pallet.index', ['pallet' => $pallet]);
    }


    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function Create()
    {   
         return View::make('pallet/create');
    }


     /**
     * Store a newly created customer in storage.
     *
     * @return Response
     */
    public function Store()
    {
            $validator = Validator::make(Input::all(), [
            'pallet_code'   => 'required|unique:jocom_pallet',
             'pallet_Description'   => 'required',
              'pallet_price'   => 'required',

             
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

           $pallet         = new Pallet;
           $pallet->pallet_code       = Input::get('pallet_code');
            $pallet->pallet_Description  = Input::get('pallet_Description');
            $pallet->pallet_price  = Input::get('pallet_price');

        $pallet->created_by = Session::get('username');
        $pallet->created_at = date('Y-m-d H:i:s');
      
  if ($pallet->save()) {
            Session::flash('success', 'pallet added successfully.');
        }

              return View::make('pallet.index');

        
    }


 


        
        
      

   public function stocki()


    {   

         $suppliersSelect = [];
           $palletsSelect = [];

                $pallets       = Pallet::alphabeticalOrder()->get();
               $suppliers       = Supplier::alphabeticalOrder()->get(); 



      foreach ($pallets as $pallet) {

            $palletsSelect[$pallet->id] = $pallet->pallet_code;
        }

        foreach ($suppliers as $supplier) {

            $suppliersSelect[$supplier->id] = $supplier->supplier_name;
        }




         return View::make('pallet/stockin', ['pallets' => $palletsSelect,'suppliers'=> $suppliersSelect]);
         
    }

     public function stocko()
    {   

        

         $suppliersSelect = [];
           $palletsSelect = [];

           

              $pallets=  DB::table('jocom_pallet_history AS jpt')
                    ->leftJoin('jocom_pallet AS jp', 'jp.id', '=', 'jpt.pallet_id')
                    ->get();


             $suppliers=  DB::table('jocom_pallet_history AS jpt')
                    ->leftJoin('jocom_supplier AS jp', 'jp.id', '=', 'jpt.supplier_id')
                    ->get();
// dd($suppliers);
       $pallet_id = Input::get('pallet_id');

                 $pallethistory= DB::table('jocom_pallet_history')
                 ->select('supplier_id')
                 ->where("pallet_id",$pallet_id)
                ->get();


                 // dd($pallethistory);

 $data['supplier'] = $pallethistory;

      foreach ($pallets as $pallet) {

            $palletsSelect[$pallet->id] = $pallet->pallet_code;
        }

        foreach ($suppliers as $supplier) {

            $suppliersSelect[$supplier->id] = $supplier->supplier_name;
        }

      
         return View::make('pallet/stockout', ['pallets' => $palletsSelect,'suppliers'=> $suppliersSelect, 'data' => $data]);
    }


        public function stockin()
    {

        $input = Input::all();    


   
    $validator = Validator::make(Input::all(), [
            
               'file_name'   => 'required|mimes:jpeg,png,jpg,zip,pdf',

             
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
      
      
        
           $stockin         = new Stockin;
        
            $stockin->quantity  = Input::get('quantity');
            $stockin->remarks  = Input::get('remarks');
            $stockin->date  = Input::get('date');

              $file = Input::file('file_name');

      $fileName = $file->getClientOriginalName() ;
      $path='media/pdf/pallet';  
      
      $x= Input::file('file_name')->move($path,  $fileName);
 

    $stockin->file_name = $fileName;
         
       $pallet_id=Input::has('pallet_id');

 
                          if (Input::has('pallet_id')) {
                $stockin->pallet_id = Input::get('pallet_id');
            }

                    if (Input::has('supplier_name')) {
                $stockin->supplier_id = Input::get('supplier_name');
            }

              $id = Input::get('pallet_id');
            $sd = Input::get('supplier_name');
           
           $qty= Input::get('quantity');
       
        $regions =DB::table('jocom_pallet_history')->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->first();

                            $hid=$regions->id;

                        $palletdetails =DB::table('jocom_pallet_details')
                        ->select('jocom_pallet_details.debtstock')
                  ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->orderBy('modify_date', 'DESC')->first();

                            $debt=$palletdetails->debtstock;  

                           
// dd($palletdetails);


                            $q=$regions->stockin;
                             $qs=$regions->quantity;




  if (count($regions)<=0)
            {
                   DB::table('jocom_pallet_history')->insertGetId(array( 
                                               
                          
                               'pallet_id' => $id,
                               'supplier_id'=> $sd,
                               'stockin'=>Input::get('quantity'),

                               'status' => 1,

                                'quantity' =>  Input::get('quantity'))

                    );



            }

  
  else{

     DB::table('jocom_pallet_history')
                            ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)
                            ->update(array('quantity' => 
                              $qs+$qty,'stockin' => 
                              $q+$qty,'modify_date'=>date('Y-m-d H:i:s'),'modify_by'=>Session::get('username')));  

  }


     DB::table('jocom_pallet_details')->insertGetId(array( 
                                               
                          
                               'pallet_id' => $id,
                               'supplier_id'=> $sd,
                               'stockin'=>Input::get('quantity'),

                               'type' => STOCKIN,
                                'debtstock' => $debt+$qty,
                                  'stockout' =>  '',
                             'date'=>Input::get('date'),
                              'remarks' => Input::get('remarks'),
                               'history_id' =>$hid,
                               'modify_date'=>date('Y-m-d H:i:s'),
                               'modify_by'=>Session::get('username'))

                            
                    );


        
        if ($stockin->save()) {
            Session::flash('success', 'Stock In successfully.');
        }

       return View::make('pallet.index');

        
    }
    
 public function stockout()
    {                 

       if(Input::has('pp_id') AND Input::has('supplier_id')  AND Input::has('rm') AND Input::has('dt') AND Input::has('qty') AND Input::has('type')){

         



      $stockout         = new Stockout;
          
       
            $stockout->remarks  =Input::get('rm');
            $stockout->date  = Input::get('dt');
            $stockout->type  = Input::get('type');
            $stockout->pallet_id = Input::get('pp_id');
            $stockout->supplier_id = Input::get('supplier_id');

             $stockout->quantity = Input::get('qty');
             $stockout->created_by = Session::get('username');
            
            $qtys=Input::get('qty');


            $id = Input::get('pp_id');
            $sd = Input::get('supplier_id');
                
        
         

         $pallet =DB::table('jocom_pallet_history')
                  ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->first();



                                  $palletdetails =DB::table('jocom_pallet_details')
                  ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->orderBy('modify_date', 'DESC')->first();



      $debt=$palletdetails->debtstock;
                            

$qa=$pallet->quantity;
$hid=$pallet->id;
               
  if ($qtys>$debt) {

  return Redirect::back()->withInput()->with('alert', 'Quantity Should be lessdhan !');
                       
         } 



                            DB::table('jocom_pallet_details')->insertGetId(array( 
                                               
                          
                               'pallet_id' => $id,
                               'supplier_id'=> $sd,
                               'stockin'=>'',

                               'type' => Input::get('type'),
                                'debtstock' =>    $debt-$qtys,
                                  'stockout' => Input::get('qty'),
                                    'remarks' => Input::get('rm'),
                              'date'=>Input::get('dt'),

                               'modify_date'=>date('Y-m-d H:i:s'),
                              'history_id'=>$hid,
                               'modify_by'=>Session::get('username'))
                            
                    );

                         
                     DB::table('jocom_pallet_history')
                            ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)
                            ->update(array('quantity' => 
                              $qa - $qtys,
                             'modify_date'=>date('Y-m-d H:i:s'),'modify_by'=>Session::get('username')));     

                                if ($stockout->save()) {
            Session::flash('success', 'pallet added successfully.');
        }    

       return View::make('pallet.index');

    
      }


// dd($input);
     

        if(Input::has('pp_id1') AND Input::has('sp')  AND Input::has('rem')  AND Input::has('qty1') AND Input::has('dat') AND Input::has('type')){        
             


                 
            $stockout         = new Stockout;
          
       
            $stockout->remarks  =Input::get('rem');
            $stockout->type  = Input::get('type');
            $stockout->pallet_id = Input::get('pp_id1');
            $stockout->supplier_id = Input::get('sp');
            $stockout->date = Input::get('dat');

               $stockout->quantity = Input::get('qty1');
            $qt=Input::get('qty1');


            $id = Input::get('pp_id1');
            $sd = Input::get('sp');
                
            $pallet = DB::table('jocom_pallet_history')
                               ->where('pallet_id','=',$id)
                                  ->where('supplier_id','=',$sd)
                               ->first();
              $qa =$pallet->quantity;


                  $palletdetails =DB::table('jocom_pallet_details')
                  ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->orderBy('modify_date', 'DESC')->first();



      $debt=$palletdetails->debtstock;



 if ($qt>$debt) {

  return Redirect::back()->withInput()->with('alert', 'Quantity Should be lessdhan !');
                       
         }   


     
 DB::table('jocom_pallet_details')->insertGetId(array( 
                                               
                          
                               'pallet_id' => $id,
                               'supplier_id'=> $sd,
                               'stockin'=>'',

                               'type' => Input::get('type'),
                                'debtstock' =>    $debt- $qt,
                                  'stockout' => Input::get('qty1'),
                                   'remarks' => Input::get('rem'),
                             'date'=>Input::get('dat'),
                                'modify_date'=>date('Y-m-d H:i:s'),
                               'modify_by'=>Session::get('username'))
                            
                    );



    
          
                         
                     DB::table('jocom_pallet_history')
                            ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)
                            ->update(array('quantity' => 
                              $qa - $qt,'modify_date'=>date('Y-m-d H:i:s'),'modify_by'=>Session::get('username'),'type'=> Input::get('type'),'stockout'=> Input::get('quantity')));     

                                if ($stockout->save()) {
            Session::flash('success', 'pallet added successfully.');
        }    

        return View::make('pallet.index');



                  

}


        if(Input::has('pp_id2') AND Input::has('supplier_id2')  AND Input::has('quantity')  AND Input::has('from') AND Input::has('to')  AND Input::has('date2') AND Input::has('remarks')){        
             


                 
                $stockout         = new Stockout;
          
       
            $stockout->remarks  =Input::get('remarks');
            $stockout->date  = Input::get('date2');
            $stockout->type  = Input::get('type');
            $stockout->pallet_id = Input::get('pp_id2');
            $stockout->supplier_id = Input::get('supplier_id2');
              $stockout->quantity = Input::get('quantity');
        
            $qut=Input::get('quantity');


            $id = Input::get('pp_id2');
            $sd = Input::get('supplier_id2');
                
            $pallet = DB::table('jocom_pallet_history')
                               ->where('pallet_id','=',$id)
                                  ->where('supplier_id','=',$sd)
                               ->first();
              $qa =$pallet->quantity;


                   $palletdetails =DB::table('jocom_pallet_details')
                  ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)->orderBy('modify_date', 'DESC')->first();



      $debt=$palletdetails->debtstock;


  if ($qut>$debt) {

  return Redirect::back()->withInput()->with('alert', 'Quantity Should be lessdhan !');
                       
         }               

          DB::table('jocom_pallet_details')->insertGetId(array( 
                                               
                          
                               'pallet_id' => $id,
                               'supplier_id'=> $sd,
                               'stockin'=>'',

                               'type' => Input::get('type'),
                                'debtstock' => $debt-$qut,
                                  'stockout' => Input::get('quantity'),
                                   'remarks' => Input::get('remarks'),
                              'date'=>Input::get('date2'),
                                'modify_date'=>date('Y-m-d H:i:s'),
                               'modify_by'=>Session::get('username'))
                            
                    );
 
                         
                     DB::table('jocom_pallet_history')
                            ->where('pallet_id',$id)
                            ->where('supplier_id', $sd)
                            ->update(array('quantity' => 
                              $qa - $qut,'modify_date'=>date('Y-m-d H:i:s'),'modify_by'=>Session::get('username'),'stockout'=> Input::get('quantity')
                               ));     

                                if ($stockout->save()) {
            Session::flash('success', 'pallet added successfully.');
        }    

  return View::make('pallet.index');

        }
        
        
    }



  

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return Response
     */
    public function Edit($id)
    {
        $pallet = Pallet::find($id);
        return View::make('pallet.edit')->with(array(
                        'pallet'    => $pallet
                       
        ));
     }
      



        
    

    /**
     * Update the specified customer in storage.
     *
     * @param  int  $id
     * @return Response
     */
      public function update($id)
    {
         
    

    $validator = Validator::make(Input::all(), [
          
             'pallet_Description'   => 'required',
              'pallet_price'   => 'required',

             
        ]);

         if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else 

   
       $pallet = Pallet::find($id);

       
            $pallet->pallet_Description  = Input::get('pallet_Description');
             $pallet->pallet_price  = Input::get('pallet_price');
  
       

        
       

      

   
            if ($pallet->save()) {
                Session::flash('success', 'pallet updated successfully.');
            }
        

      return View::make('pallet.index');

    }
 
    /**
     * Remove the specified seller from storage.
     *
     * @param  int  $id
     * @return Response
     */
      public function history($id){

  

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

                 
    return View::make('pallet.history', ['pallet' => $pallet,'pallethistory' => $pallethistory,'history' => $history]);
    }
      
   
       public function export($id){
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $stockarray = array();
        $repname    = "";
        $reptitle   = "";

        // echo '<pre>';
        // print_r(Input::all());
        // echo '</pre>';
        
        try{

 

      
 
 
 
    
  $pallethist = DB::table('jocom_pallet_history AS JTD')
                           ->select('JTD.pallet_id','JTD.supplier_id')
                           ->leftJoin('jocom_pallet','jocom_pallet.id','=','JTD.pallet_id')
                            ->leftJoin('jocom_supplier','jocom_supplier.id','=','JTD.supplier_id')

                            ->where('JTD.id',$id)
                          
                            ->first();
                            
                           
             
$pallet_id=$pallethist->pallet_id;
$supplier_id=$pallethist->supplier_id;


      
      $history = DB::table('jocom_pallet_details AS JTD')
                            ->select('JTD.*','jocom_pallet.pallet_Description','jocom_pallet.pallet_price','jocom_pallet.pallet_code','jocom_supplier.supplier_name')
                         
                              ->leftJoin('jocom_pallet','jocom_pallet.id','=','JTD.pallet_id')
                               ->leftJoin('jocom_supplier','jocom_supplier.id','=','JTD.supplier_id')
                               

                    ->where('JTD.pallet_id',$pallet_id)
                     ->where('JTD.supplier_id',$supplier_id)
                       ->get();
                            
                    //   dd($pallet);       
                            
                          

                
              
 $data = array(
                       
                         
                        'history'      =>$history
                           
                    );

        $date = date('Y-m-d H:i:s');

        $path = "pallet";    

        } catch (Exception $ex) {
            echo $ex->getMessage();
        } finally {
            // return $data;
                  return Excel::create(date("dmyHis"), function($excel) use ($data) {
                    $excel->sheet('pallet', function($sheet) use ($data)
                    {   
                        $sheet->loadView('pallet.templatepallet', array('data' =>$data, 'history' =>$history));
                        
                    });
                })->download('xls');

           

        }



    }
        public function getpallet()
    {
                
                   $suppliers=  DB::table('jocom_pallet_history AS jpt')
                    ->leftJoin('jocom_supplier AS jp', 'jp.id', '=', 'jpt.supplier_id')
                 ->where("jpt.pallet_id",$pallet_id)
                    ->get();


       
                   $pallets = DB::table('jocom_pallet')->get();


      return View::make('pallet/stockout', ['pallets' => $pallets,'suppliers' => $suppliers]);

    }

                      
    public function getsuppliers() {

         $data = array();
    
        $errorCode = "";
     

        $pallet_id=Input::get('pallet_id');

        $suppliers=  DB::table('jocom_pallet_history AS jpt')
                    ->leftJoin('jocom_supplier AS jp', 'jp.id', '=', 'jpt.supplier_id')
                 ->where("jpt.pallet_id",$pallet_id)
                    ->get();
// dd($suppliers);
            $data['suppliers'] = $suppliers;

         $response = array( "error_code" => $errorCode, "data" => $data);
        return $response;

    }
    
    
}
?>
    

     



    
           
         
       




           
    






      
     