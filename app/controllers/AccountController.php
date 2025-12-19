<?php

class AccountController extends BaseController
{

    public function anyIndex()
    {
        return View::make('account.index');
    }

    public function anyListing()
    {
        $account = DB::table('accounting_log')
                    ->select(array(
                        'id', 
                        'report_name',
                        'file_name',
                        'status'
                        ))
                    ->whereIn("status",[1,2]);
                    // ->orderBy('status')
                    // ->orderBy('id', 'desc');       

        $actionBar = '@if($status==1)<a class="btn btn-primary" title="" data-toggle="tooltip" href="/account/files/{{$file_name}}" target="_blank"><i class="fa fa-download"></i></a> <a class="btn btn-success" title="" data-toggle="tooltip" href="#" onclick="complete_account({{$id;}});"><i class="fa fa-check"></i></a>@endif @if($status==0)Contact IT Dept @endif';

        return Datatables::of($account)
            // ->add_column('total', '{{number_format(abs($total_amount - $coupon_amount + $gst_total - $point_amount), 2)}}')
            ->edit_column('status', '@if($status==0){{Initiated}} @elseif($status==1){{Generated}} @elseif($status==2){{Imported}} @endif')
            ->add_column('Action', $actionBar)
            ->make(true);
    }

    public function anyFiles($file=null)
    {
        // $tempfile = base64_decode(urldecode($file));
        $file = "accounting/".$file;

        if(is_file($file)) {
            return Response::download($file);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyComplete()
    {
        if (Input::has('complete_account_id'))
        {
            $id = Input::get('complete_account_id');
            
            DB::table('accounting_log')->where('id', $id)->update(array('status' => 2));

            $file = DB::table('accounting_log')->where('id', $id)->first();

            if (count($file) > 0)
            {
                $file_name = "accounting/".$file->file_name;

                if(is_file($file_name))
                    unlink($file_name);
            }               

            return Redirect::to('account/')->with('success', 'Account Log(ID: '.$id.') updated successfully.');
        }
        else
        {
            return Redirect::to('account/')->with('message', 'Account Log update failed.');
        }        

    }

    public function anyMonitor()
    {

        $e          = strtotime("yesterday");
        $date       = date('Ymd', $e);
        $time       = date('his');

        $companies  = array('e37', 'mshopping');

        $types      = array('category', 'creditor', 'debtor', 'product', 'invoice');

        $status     = array('new', 'update');

        $record     = array();

        foreach ($companies as $company)
        {
            foreach ($types as $type)
            {
                foreach ($status as $statustype)
                {
                    if ($statustype == 'update' AND $type == 'invoice')
                    {
                        // invoice no updated
                    }
                    elseif ($company == 'mshopping' AND $type == 'creditor')
                    {
                        // combine database, take e37 record for creditor
                    }
                    elseif ($company == 'e37' AND $type == 'debtor')
                    {
                        // combine database, take mshopping record for debtor
                    }
                    elseif ($company == 'mshopping' AND $type == 'product')
                    {
                        // combine database, take e37 record for creditor
                    }
                    elseif ($company == 'mshopping' AND $type == 'category')
                    {
                        // combine database, take e37 record for creditor
                    }
                    else
                    {

                        $filename   = $type."_daily_".$statustype."_" . $company . "_".$date;                     

                        $result = AccountingLog::CheckExist($filename);

                        if ($result == 0)
                        {
                            $record[] = $filename;
                        }
                    }           
                }       
            }   
        }

        if (empty($record))
        {
            echo "All in order.";
        }            
        else
        {
            $result = AccountingLog::CheckLog($date);

            $text = "";
            $today = date('Y-m-d H:i:s');

            if ($result == 0)
            {
                foreach ($record as $row)
                {
                    $text .= $row.", \n";
                }

                $insert = new AccountingLog;

                $insert->report_name = "missing_".$date;
                $insert->file_name = "missing_".$date;
                $insert->sql_string = $text;
                $insert->status = "0";
                $insert->created_at = $today;

                $insert->save();

                echo "Unexecuted cron job recorded!";
            }
            else
                echo "Unexecuted cron job exist before!";
            
        }
    }
    
    
    /*
     * @Desc : Generate supplier invoice 
     */
    
    public function anySupplierinvoice(){
        
        $Seller = Seller::where("active_status",1)->orderBy("company_name","asc")->get();
        
        return View::make('account.supplier_invoice')->with("seller",$Seller);
}
    
    public function anySaveinvoice(){
        
        
         try{

            $transaction_id = Input::get("transaction_id");
            $process_type = Input::get("process_type");

            $Transaction =  Transaction::find($transaction_id);

            $timestamp = strtotime($Transaction->transaction_date);
            $date = date('Y-m-d', (string)$timestamp);
            $month = date('m', (string)$timestamp);
            $year = date('Y', (string)$timestamp);
            $year_two_character = date('y', (string)$timestamp);

            $supplier_id = 1; // FIMS
            $invoice_date = $date; // FIMS
            $invoice_po = ''; // FIMS
            $invoice_term = 'C.O.D';; // FIMS
            $percentMargin = 3.00; // FIMS
            $transaction_id = $Transaction->id; // FIMS
            $process_type = 1; // FIMS

            $Number = $this->getNumber($month,$year);
            $invoice_number = $this->createInvoiceNumber($Number,$month,$year_two_character);
            
            $generated  = self::generateSupplierInvoice($supplier_id,$invoice_date,$invoice_number,$invoice_po,$invoice_term,$percentMargin,$transaction_id,$process_type);
        
            if($generated){
                // Update Invoice Number
                $this->updateNextNumber($month,$year);
                // Update Invoice Number

            }
            
            return Redirect::to('/account/supplierinvoice');   
            
            } catch (Exception $ex) {
                echo $ex->getMessage();
         }
//                   
    }


    public static function generateSupplierInvoice($suplier_id,$invoice_date,$invoice_number,$invoice_po,$invoice_term,$percentMargin,$transaction_id,$process_type){

        try{
            
            $masterCollection = array();
            $subItemCollection = array();

            $Seller = Seller::find($suplier_id);
            $TDetails = DB::table('jocom_transaction_details AS JTD')
                            ->select('JTD.*','JP.name AS ProductName')
                            ->leftJoin('jocom_products AS JP', 'JTD.sku', '=', 'JP.sku')
                            ->where('transaction_id',$transaction_id)
                            ->orderBy("JTD.id","desc")
                            ->get();


            

            $SupplierInvoice = new SupplierInvoice();
            $SupplierInvoice->invoice_date = $invoice_date;
            $SupplierInvoice->supplier_id = $suplier_id;
            $SupplierInvoice->invoice_number = $invoice_number;
            $SupplierInvoice->margin = $percentMargin;
            $SupplierInvoice->process_type = $process_type;
            $SupplierInvoice->transaction_id = $transaction_id;
            $SupplierInvoice->created_by = Session::get("username") ? Session::get("username") : '' ;
            

            $SupplierInvoice->save();
            $SupplierInvoiceID = $SupplierInvoice->id;
            
            $file_name = $SupplierInvoiceID."_".uniqid().".pdf";
            $SupplierInvoice = SupplierInvoice::find($SupplierInvoiceID);
            $SupplierInvoice->invoice_filename = $file_name;
            $SupplierInvoice->save();
            
            
            foreach ($TDetails as $key => $value) {

                $unit_price = number_format((float)$value->price * ((100 - $percentMargin)/100), 2, '.', '');
                $raw_unit_price = number_format((float)$value->price * ((100 - $percentMargin)/100), 2, '.', '');
                $total_sub_price = $unit_price * $value->unit;
                $subItem = array(
                    "item" => $value->ProductName,
                    "quantity" => $value->unit,
                    "unitPrice" =>  $unit_price,
                    "total" => $total_sub_price
                );

                $subTotal = $subTotal + $total_sub_price;
                array_push($subItemCollection, $subItem);

                $SupplierInvoiceDetails = new SupplierInvoiceDetails();
                $SupplierInvoiceDetails->supplier_invoice_id = $SupplierInvoiceID;
                $SupplierInvoiceDetails->product_id = $value->product_id;
                $SupplierInvoiceDetails->product_name = $value->ProductName;
                $SupplierInvoiceDetails->product_label = "";
                $SupplierInvoiceDetails->quantity = $value->unit;
                $SupplierInvoiceDetails->price = $unit_price;
                $SupplierInvoiceDetails->tax_amount = 0.00;
                $SupplierInvoiceDetails->total_tax_amount = 0.00;
                $SupplierInvoiceDetails->total_amount = $total_sub_price ;
                $SupplierInvoiceDetails->created_by = Session::get("username");
                $SupplierInvoiceDetails->status = 1;
                $SupplierInvoiceDetails->save();

            }

            $State = State::find($Seller->state);

            $masterCollection["supplierInfo"] = array(
                "company_name" => $Seller->company_name,
                "address1" => $Seller->address1,
                "address2" => $Seller->address2,
                "postcode" => $Seller->postcode,
                "state" => $State->name,
                "country" => 'Malaysia'
            );
            $masterCollection["invoice_date"] = $invoice_date;
            $masterCollection["invoice_number"] = $invoice_number;
            $masterCollection["invoice_po"] = $invoice_po;
            $masterCollection["invoice_term"] = $invoice_term;
            $masterCollection["subItems"] = $subItemCollection;
            $masterCollection["subTotal"] = $subTotal;
            $masterCollection["gst"] = "";
            $masterCollection["rounding"] = "";
            $masterCollection["total"] = $subTotal;
            $masterCollection["totalPaid"] = "";
            $masterCollection["totalDue"] = "";
            
            
            
            $file_path = Config::get('constants.SUPPLIER_INVOICE_PDF_FILE_PATH');
            include_once app_path('library/html2pdf/html2pdf.class.php');
            
            $response = View::make('emails.supplierinvoice')->with("data",$masterCollection);
            $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            $html2pdf->setDefaultFont('arialunicid0');
            $html2pdf->WriteHTML($response);
            $html2pdf->Output($file_path."/".$file_name, 'F');

            return true;

        }catch(exception $ex){
            return false;
        }

    }

    /*
     * @Desc    : To list out list of confirmed orders .Generate supplier invoice in PDF format
     * @Param   : None
     * @Return  : (DATATABLE) format
     */
    public function anyListsupplierinvoice() {
        
        // Get Orders
        
        $ordersInvoice = DB::table('jocom_supplier_invoice')->select(array(
                        'jocom_supplier_invoice.id',
                        'jocom_supplier_invoice.invoice_number',
            'jocom_supplier_invoice.transaction_id',
            'jocom_supplier_invoice.created_at',
                        'jocom_supplier_invoice.margin',
                        
                        ))
                    ->where('jocom_supplier_invoice.status',1)
                    ->orderBy('jocom_supplier_invoice.id','desc');

        return Datatables::of($ordersInvoice)->make(true);
        
    }
    
    
    public function anyDownloadsupplierinvoice($invoice_id){

        $SupplierInvoice = SupplierInvoice::find($invoice_id);
        
        $file = Config::get('constants.SUPPLIER_INVOICE_PDF_FILE_PATH') . '/' . $SupplierInvoice->invoice_filename;

        //echo $loc;
        if (file_exists($file)) {
            $headers = array(
              'Content-Type: application/pdf',
            );
            return Response::download($file, $SupplierInvoice->invoice_filename, $headers);
            exit;
        }
        
    }


    public function automateInvoice(){

        $ordersInvoice = DB::table('jocom_supplier_invoice_queue AS JSIQ')
        ->select(array(
            'JSIQ.id',
            'JSIQ.transaction_id',
            'JSIQ.is_generated',
            'JSIQ.supplier_invoice_id',
            'JT.transaction_date',
            'JT.status'
            ))
        ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JSIQ.transaction_id')
        ->where("JT.status",'completed')
        ->where('JSIQ.is_generated',0)
        ->take(10)->get();
        

        if($ordersInvoice){

            foreach ($ordersInvoice as $key => $value) {
  
                $timestamp = strtotime((string)$value->transaction_date);
                $date = date('Y-m-d', (string)$timestamp);
                $month = date('m', (string)$timestamp);
                $year = date('Y', (string)$timestamp);
                $year_two_character = date('y', (string)$timestamp);

                $supplier_id = 66; // FIMS
                $invoice_date = $date; // FIMS
                $invoice_po = ''; // FIMS
                $invoice_term = 'C.O.D';; // FIMS
                $percentMargin = 3.00; // FIMS
                $transaction_id = $value->transaction_id; // FIMS
                $process_type = 1; // FIMS

                $Number = $this->getNumber($month,$year);
                $invoice_number = $this->createInvoiceNumber($Number,$month,$year_two_character);
             
                $generated = self::generateSupplierInvoice($supplier_id,$invoice_date,$invoice_number,$invoice_po,$invoice_term,$percentMargin,$transaction_id,$process_type);
                
                if($generated){
                    // Update Invoice Number
                    $this->updateNextNumber($month,$year);
                    // Update Invoice Number
                    $SupplierInvoiceQueue = SupplierInvoiceQueue::where("transaction_id",$transaction_id)->first();
                    $SupplierInvoiceQueue->is_generated = 1;
                    $SupplierInvoiceQueue->save();
                }
            }

        }
        
    }

    private function registerNumbering($month, $year){

        DB::table('jocom_supplier_numbering')->insert(
            ['numbering' => 1, 'month' => $month, 'year' => $year]
        );
        return 1;
    }

    private function getNumber($month, $year){

        $numbering = DB::table('jocom_supplier_numbering AS JSN')
            ->select(array(
                'JSN.*'
                ))
            ->where("JSN.month",$month)
            ->where("JSN.year",$year)
            ->first();
        
        if(!$numbering){
            $newNumber = $this->registerNumbering($month, $year);
            return str_pad( $newNumber, 5, '0', STR_PAD_LEFT);;
        }

        return str_pad( $numbering->numbering, 5, '0', STR_PAD_LEFT);;
        
    }

    private function createInvoiceNumber($numbering,$month,$year){

        return "INV ".$numbering." / ".$month."-".$year;

    }

    private function updateNextNumber($month,$year){

        $SupplierNumbering = SupplierNumbering::where('month',$month)
            ->where('year',$year)
            ->first();

        if($SupplierNumbering){
            $SupplierNumbering->numbering = $SupplierNumbering->numbering + 1;
            $SupplierNumbering->save();
        }

    }
    
    
    
    
    
}
