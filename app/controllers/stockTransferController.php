<?php

class stockTransferController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function index()
    {


      
        return View::make('stock.index');
    }


      public function anystocks() {
        $stocks = Stocks::select([
                                        'jocom_stocktransfer.id',
                                        'jocom_stocktransfer.st_no',
                                        'jocom_stocktransfer.deliver_from',
                                        'jocom_stocktransfer.deliver_to',
                                        'jocom_stocktransfer.expired_date',
                                        'jocom_stocktransfer.file_name',
                                        'jocom_stocktransfer.st_status',
                                         'jocom_stocktransfer.sendby',
                                          'jocom_stocktransfer.receivedby',
                                           'jocom_stocktransfer.approvedby'
                                        
                                    ])
                                  
                                    ->where('jocom_stocktransfer.status', '=', '1');
                                  
        return Datatables::of($stocks)
                        ->edit_column('st_status', function($row){
                                        if ($row->st_status == 1) {
                    $label = '<span class="label label-success">Open</span>';
                } else {
                    $label = '<span class="label label-danger">Closed</span>';
                }
                  return $label;
            })
                    

                
                                    ->add_column('Action', function($row) {

                                              $path = Config::get('constants.INVOICE_PDF_FILE_PATH') . '/' . urlencode($row->st_no) . '.pdf';
                                            $file = ($row->id)."#".($row->st_no).$path;
                                            $encrypted = Crypt::encrypt($file);
                                            $encrypted = urlencode(base64_encode($encrypted));
                                    
                                
                                            if($row->st_status==0)
                                {
                                  $edit .= '
                                              <a class="btn btn-danger" title="" data-toggle="tooltip"  href="'.url('stock/files').$row->file_name.'"  target="_blank"><i class="fa fa-download"></i></a>
                                                       <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('stock/stocklist').$row->id.'"><i class="fa fa-eye"></i></a>
                                            ';
                                }
                                else{
                                  $edit .= '<a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('stock/upload').$row->id.'"><i class="fa fa-file-image-o"></i></a> 
                                            <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('stock/edit').$row->id.'"><i class="fa fa-pencil"></i></a>
                                                     <a class="btn btn-primary" title="" data-toggle="tooltip" href="'.url('stock/stocklist').$row->id.'"><i class="fa fa-eye"></i></a>
                                              
                                            ';

                                }
                                                                     

                                
                                        return $edit;
                                    })
                                    ->make();
    }


    public function create()
                      
                         {
                             $stk = Warehouse::select([
                                  'jocom_products.id',
                                  'jocom_products.name',
                                  'jocom_products.sku',
                                  'jocom_products.lable'
                                   ])->leftJoin('jocom_products', 'jocom_warehouse_products.product_id', '=', 'jocom_products.id');

        
      
         
               return View::make('stock.create')->with("stk",$stk);
    }

    public function  store()
                
                      {

                      $rules = array(
        'st_no'   => 'required|unique:jocom_stocktransfer,st_no',
        'st_date'  => 'required',
        'transfer_date' => 'required',
        'purposeoftransfer'  => 'required',
        'sendby' => 'required',
        'approvedby'  => 'required',
        
        'deliver_from'  => 'required',
         'expired_date'  => 'required',
        'deliver_to' => 'required',
        'lid' => 'required',
        'qty' => 'required'
    );
    $validator = Validator::make(Input::all(), $rules);

                      $input = Input::all();             
                    $Filename = '';
     
  if ($validator->fails()) {
        return Redirect::back()->withInput()->withErrors($validator);
          
    } else {
            // $Filename = array_fill(1, 4, '');
          $id = DB::table('jocom_stocktransfer')->insertGetId(array(
                            
                            'st_no' => trim(Input::get('st_no')),
                            'st_date' => trim(Input::get('st_date')),
                            'transfer_date' => trim(Input::get('transfer_date')),
                            'purposeoftransfer' => trim(Input::get('purposeoftransfer')),
                            'sendby' => trim(Input::get('sendby')),
                            'receivedby' => trim(Input::get('receivedby')),
                            'approvedby' => trim(Input::get('approvedby')),
                             'file_name' => $Filename,
                            // 'img_2' => $imgFilename[2],
                            // 'img_3' => $imgFilename[3],
                            // 'vid_1' => trim(Input::get('product_video')),
                            // 'qrcode' => $qrCode,
                            // 'qrcode_file' => $qrCodeFile,
                            'deliver_from' => trim(Input::get('deliver_from')),
                            'expired_date' => trim(Input::get('expired_date')),
                            'created_by' => Session::get('username'),
                             'st_status'  => '1',
                            'deliver_to' => trim(Input::get('deliver_to')),

                            
                              'created_at'=>date("Y-m-d H:i:s"),
                           
            ));

            if (Input::has('lid')) {
                foreach(Input::get('lid') as $key => $value) {
                    DB::table('jocom_stocktransfer_details')->insertGetId(array( 
                                               
                          
                               'stock_id' => $id,
                               'product_id'=>trim(Input::get("lid.$key")),
                                'qty' => trim(Input::get("qty.$key")))
                    );

                 $insert_audit = General::audit_trail('stockTransferController.php', 'store()', 'Add Stock Transfer', Session::get('username'), 'CMS');
                }
            }

             return Redirect::to('stock');

    }



    


    }



  


     public function edit($id)
    {


        $stock = Stocks::find($id);

        $product_stock = DB::table('jocom_stocktransfer_details')
                                    ->select('jocom_products.name', 'jocom_stocktransfer_details.qty','jocom_products.sku','jocom_stocktransfer_details.product_id')
                             
                                     ->leftJoin('jocom_products', 'jocom_products.id', '=', 'jocom_stocktransfer_details.product_id')
                                    ->where('jocom_stocktransfer_details.stock_id', '=', $id)
                                  
                                    ->get();
 // dd($product_stock);
     

                                            return View::make('stock/edit', ['stock' => $stock,'product_stock' => $product_stock]);


    }  

    public function update($id)
    {

  $input = Input::all();             

     
        $stock = Stocks::find($id);
        // dd($stock);
      

           $stock->st_no     = Input::get('st_no');
        $stock->deliver_from    = Input::get('deliver_from');
        $stock->deliver_to   = Input::get('deliver_to');
       
        $stock->expired_date = Input::get('expired_date');

        $stock->purposeoftransfer   = Input::get('purposeoftransfer');
        $stock->sendby  = Input::get('sendby');
        $stock->created_by   = Session::get('username');
        $stock->receivedby = Input::get('receivedby');
        $stock->approvedby = Input::get('approvedby');
         $stock->transfer_date = Input::get('transfer_date');
          $stock->st_date =Input::get('st_date');
          $stock->save();

                  $insert_audit = General::audit_trail('stockTransferController.php', 'update()', 'Edit Stock', Session::get('username'), 'CMS');



                    // $sts = DB::table('jocom_stocktransfer_details')->where('stock_id',$id)->get();

                    // dd($sts);
             DB::table('jocom_stocktransfer_details')->where('stock_id', '=', $id)->delete();
        if (Input::has('lid')) {

            // echo "string";
            // die();
            foreach(Input::get('lid') as $key => $value) {
                DB::table('jocom_stocktransfer_details')->insert(array(
                          
                           
                            
                              
                               'stock_id' => $id,
                               'product_id' => trim(Input::get("lid.$key")),
                               
                            'qty'=>trim(Input::get("qty.$key"))
                            )
                );
                $insert_audit = General::audit_trail('stockTransferController.php', 'update()', 'Edit Stock Product', Session::get('username'), 'CMS');

            }
           
        }
      
      

        if ($stock->save()) {
                Session::flash('success', ' Successfully updated.');
            }

       

       

        Session::flash('message', 'Successfully updated.');
 

        return Redirect::to('stock');
    }

     public function anyDelete($id)
      {

      $stocks = Stocks::find($id);
              $stocks->status = 0;

    
       DB::table('jocom_stocktransfer_details')->where('stock_id', '=', $id)->delete();

        $stocks->save();

        if ($stocks->save()) 
                             {
                                 Session::flash('success', ' Successfully deleted.');
                             }

        
          return Redirect::to('stock');
     }

     public function anyWareproducts()
    {
        return View::make('stock.wareproducts');
    }

     public function anywproducts()
    {
        $ware_products = Warehouse::select([
            'jocom_products.id',
            'jocom_products.name',
            'jocom_products.sku',
         
            // 'jocom_product_price.price',
            // 'jocom_product_price.price_promo'
        ])->leftJoin('jocom_products', 'jocom_warehouse_products.product_id', '=', 'jocom_products.id');

           
               
                
     
        

        return Datatables::of($ware_products)
           
            ->add_column('Action', '<a id="selectItem" class="btn btn-primary" title="" href="">Select</a>')
            ->make();
    }


  
    

   
     public function stockform($locs = null)
              {


        $locs = base64_decode(urldecode($locs));
        $locs = Crypt::decrypt($locs);

// echo "string";
// die();
        $id = explode("#", $locs);
// dd($id);
        // $eINV_no = $id[1];
         
        // $po_no = $id[1];

        // $epo_no = $id[1];
        
        $id = $id[0];

        
        if (file_exists($locs)) {
            $paths = explode("/", $locs);
            header('Cache-Control: public');
            header('Content-Type: application/pdf');
            header('Content-Length: '.filesize($locs));
            header('Content-Disposition: filename="'.$paths[sizeof($paths) - 1].'"');
            $locs = readfile($locs);

        } else {
        // echo "<script>window.close();</script>";
          

                $stock = Stocks::find($id);    
      // dd($stock);  

              $DOView = self::createSTview($stock);

                return View::make('stock.stock_view')
                     ->with('display_details', $DOView['generals'])
                     ->with('display_stocks', $DOView['stks'])
                     ->with('htmlview',true);

           
        }

    }

 public static function createSTview($stock){



    
    
    /* CHECK DO FOR DELIVERY SERVICE */
 
    
   
    
    $payment_id = 0;
    $generals = [
            "id"               => $stock->id,
            "st_no"               => $stock->st_no,
         
         
           
           "expired_date" =>$stock->expired_date,
            "transfer_date" => $stock->transfer_date,
            "deliver_from" => $stock->deliver_from,
            "deliver_to" => $stock->deliver_to,
              "st_date" => $stock->st_date,
               "created_by" => $stock->created_by,
               "purposeoftransfer" => $stock->purposeoftransfer,


           
        ];

 // dd($generals);

         


             $stks = DB::table('jocom_stocktransfer AS a')
            ->select('a.*','b.*','c.*')
            ->leftJoin('jocom_stocktransfer_details AS b', 'b.stock_id', '=', 'a.id')
            ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
            ->where('a.id', '=', $stock->id)
         
     
            ->get();

          
   
// var_dump($stks);
// die();


  

  

    return array(
            'generals'=>$generals,
            'stks'=>$stks,
        );
               
}



    public function anyUpload($id) {

      $stock = DB::table('jocom_stocktransfer')
                                    ->select('*')
                                   
                                    ->where('jocom_stocktransfer.id', '=', $id)
                                    ->first();

if ($stock->st_status =='0') {

  Session::flash('message', 'Successfully deleted.');
  return View::make('stock.index')->with("edit",'cannot edit');
    Session::flash('message', 'Successfully deleted.');
  
}
    else{


      return View::make('stock.upload')->with(array('stock' => $stock));

    }

    }


public function anyDownload($locs=null){
    // dd('here');
     $locs = base64_decode(urldecode($locs));
    // dd($locs);
    $locs = Crypt::decrypt($locs);
    $id = explode("#", $locs);
    // dd($id);


     $epo_no = $id[2];
    // print_r($epo_no);
    // die();
    $eINV_no = $id[2];

    $po_no = $id[2];

    $id = $id[1];
    
    $file_path = array_shift(explode("#", $locs));

    $file_name = explode("/", $file_path);

    $file_name = $file_name[3];

          $stock = Stocks::find($id);
          // dd($stock);

          // dd('here');
 $inview="";
 $inview = 'stock.stock_view';

            $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH');

                include app_path('library/html2pdf/html2pdf.class.php');
                
                          $stock = Stocks::find($id);

// dd($stock);
                $INVVview = self::createSTview($stock);

              // dd($INVVview);
                
                $response = View::make($inview)
                     ->with('display_details', $INVVview['generals'])
                     ->with('display_stocks',  $INVVview['stks']);

                     // dd($response);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);

                // $html2pdf->Output($file_name, $display_details, $headers);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);


          
    }

     public function uploadfile($id) 
     
       {

     $stfk =Stocks::find($id);
       
      $file = Input::file("file");
     $fileName = $file->getClientOriginalName() ;
          
      $path='media/pdf/stock';  
      
      $x= Input::file('file')->move($path,  $fileName);
               // dd($x); 
       
    
        
    
        $stfk->file_name = $fileName;
        $stfk->st_status ='0';
        $stfk->receivedby =Input::get("receivedby");;
        $stfk->path_to_file =$path;
        $stfk->name_on_disk = basename($path);

        $stfk->save();

        if($stfk->save()){

          Session::flash('message', 'File Successfully Uploaded.');
        }

      

 Session::flash('message', 'Successfully updated.');
        return Redirect::to('stock')->with('message', 'Save Failed');;
       
    }

   
    public function getfiles($file=null)
    {
        
        $file_path = Config::get('constants.STOCK_INVOICE_PDF_FILE_PATH') . '/' . $file;

       

        if(is_file($file_path)) {
            return Response::download($file_path);
        }
        else {
            echo "<br>File not exists!";
        }

    }
    
     public function view($id)
    {


        $stock = Stocks::find($id);

        $product_stock = DB::table('jocom_stocktransfer_details')
                                    ->select('jocom_products.name', 'jocom_stocktransfer_details.qty','jocom_products.sku','jocom_stocktransfer_details.product_id')
                             
                                     ->leftJoin('jocom_products', 'jocom_products.id', '=', 'jocom_stocktransfer_details.product_id')
                                    ->where('jocom_stocktransfer_details.stock_id', '=', $id)
                                  
                                    ->get();

     

                                            return View::make('stock/stocklist', ['stock' => $stock,'product_stock' => $product_stock]);


    }  


   

    

    

}