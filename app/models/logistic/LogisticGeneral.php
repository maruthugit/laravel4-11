<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticGeneral extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;

    public static function generate_batch_DO($batch)
    {
        $transaction = LogisticTransaction::find($batch->logistic_id);
        $do_no = $transaction->do_no;


        $haveFile = true;
        $file_name = "";
        $add = "-1";
        $count = 2;

        while ($haveFile === true)
        {
            $file_name = $do_no . $add . ".pdf";
            $path = Config::get('constants.LOGISTIC_DO_PATH');

            if(LogisticBatch::check_file($file_name, $path))
            {
                //file exist
                $haveFile = true;
                $add = "-" . $count;
                $count++;
            }
            else
                //no file
                $haveFile = false;
        }

        $final_do_no = $do_no . $add;

        $paypal = TPayPal::where('transaction_id', '=', $transaction->transaction_id)->first();
        $payment_id = "";

        if ($paypal != null)
        {
            $payment_id = $paypal->txn_id;
        }

        $driver = LogisticDriver::find($batch->driver_id);
        $deliveryservice = false;
        /* CHECK DO FOR DELIVERY SERVICE */
        $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction->transaction_id)->first();
        if($DeliveryOrder->id > 0){
            $deliveryservice = true;
        }else{
            $deliveryservice = false;
        }


        $general = array(
            "signature_file" => $batch->signature_file,
            "sign_name" => $batch->sign_name,
            "sign_ic" => $batch->sign_ic,
            "sign_date" => date("d-m-Y", strtotime($batch->accept_date)),
            "sign_time" => date("h:ia", strtotime($batch->accept_date)),
            "do_no" => $final_do_no,
            "do_date" => date("d-m-Y", strtotime($batch->accept_date)),
            "driver_name" => isset($driver->name) ? $driver->name : "",
            "payment_terms" => "cash/cc",
            "transaction_id" => $transaction->transaction_id,
            "delivery_contact_no" => $transaction->delivery_contact_no,
            "payment_id" => $payment_id,
            "transaction_date" => date("d-m-Y", strtotime($transaction->transaction_date)),
            "delivery_name" => $transaction->delivery_name,
            "delivery_address_1" => $transaction->delivery_addr_1,
            "delivery_address_2" => $transaction->delivery_addr_2,
            "delivery_address_3" => ($transaction->delivery_postcode != '' ? $transaction->delivery_postcode . " " : '') . ($transaction->delivery_state != '' ? $transaction->delivery_state . ", " : '') . $transaction->delivery_country,
            "special_instruction" => ($transaction->special_msg != "" ? $transaction->special_msg : "None"),
            "delivery_contact_no" => $transaction->delivery_contact_no,
            "remark" => nl2br($batch->remark),
        );

        $batch_item = LogisticBatchItem::where('batch_id', '=', $batch->id)->get();

        foreach ($batch_item as $item)
        {
            $itemlist = LogisticTItem::find($item->transaction_item_id);
            $items[] = array(
                "sku" => $itemlist->sku,
                "price_label" => $itemlist->label,
                "description" => (isset($itemlist->name) ? $itemlist->name : ""),
                "qty" => $item->qty_sent,
                "remark" => nl2br($item->remark)
            );
        }

        $general['items'] = $items;

        $DeliveryOrderItems = array();
        if($deliveryservice){
                    
            $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction->transaction_id)->first();
            $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
        }

        include app_path('library/html2pdf/html2pdf.class.php');

        $trans = Transaction::find($transaction->transaction_id);
        $DOView = TransactionController::createDOView($trans);

        $response =  View::make('checkout.do_view')
                    ->with('display_details', $general)
                    ->with('display_trans', $DOView['trans'])
                    ->with('display_seller', $DOView['paypal'])
                    ->with('display_product', $DOView['product'])
                    ->with('display_group', $DOView['group'])
                    ->with('deliveryservice', $DOView['deliveryservice'])
                    ->with("display_delivery_service_items",$DOView['DeliveryOrderItems']);
        
//        $response = View::make('checkout.do_view')
//                ->with('display_details', $general)
//                ->with('deliveryservice', $deliveryservice)
//                ->with("display_delivery_service_items",$DeliveryOrderItems);
        
     
        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        // $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arialunicid0');
        // $html2pdf = new HTML2PDF('P','A4');
        $html2pdf->WriteHTML($response);
        //$html2pdf->Output("example.pdf");
        $html2pdf->Output("./" . Config::get('constants.LOGISTIC_DO_PATH') . "/" . $file_name, 'F');

        return $final_do_no;
    }

    public static function do_mailout($row, $donum, $batchid)
    {
        $subject = "tmGrocer Delivery Notification [Transaction ID: " . $row->transaction_id . "] [" . $donum . "]";
        $body = "
            Delivery To:<br />
            Name: " . $row->delivery_name . "<br />
            Contact No: " . $row->delivery_contact_no . "<br />
            Address 1: " . $row->delivery_addr_1 . "<br />
            Address 2: " . $row->delivery_addr_2 . "<br />
            State: " . $row->delivery_state . "<br />
            Postcode: " . $row->delivery_postcode . "<br />
            Country: " . $row->delivery_country . "<br />
            <br />
            Special Message: " . $row->special_msg . "<br />
            <br />
            Buyer Email: " . $row->buyer_email . "<br />
            <br />
            <table border=1 class=item>
                <tr>
                    <td class=item><b>SKU</b></td>
                    <td class=item><b>Description</b></td>
                    <td class=item><b>Label</b></td>
                    <td class=item><b>Quantity</b></td>
                </tr>
        ";



        $pro_body = "";
        $transaction_id  = $row->transaction_id;
        /* CHECK DO FOR DELIVERY SERVICE */
        $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction_id)->first();
        if($DeliveryOrder->id > 0){
            $deliveryservice = true;
            $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction_id)->first();
            $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
        }else{
            $deliveryservice = false;
        }

        $dquery = LogisticBatchItem::where('batch_id', '=', $batchid)->get();
        if($deliveryservice){

            foreach ($DeliveryOrderItems as $keyD => $valueD) {
                $pro_body.= "
                    <tr>
                        <td class=item>" . $valueD->item_sku . "</td>
                        <td class=item>" . (isset($valueD->item_description) ? $valueD->item_description : '') . "</td>
                        <td class=item>" . $valueD->item_label . "</td>
                        <td class=item>" . $valueD->quantity . "</td>
                    </tr>
                ";
            }
            
        }else{
            
        foreach($dquery as $drow)
        {
            $prow = LogisticTItem::find($drow->transaction_item_id);
            $pro_body.= "
                <tr>
                    <td class=item>" . $prow->sku . "</td>
                    <td class=item>" . (isset($prow->name) ? $prow->name : '') . "</td>
                    <td class=item>" . $prow->label . "</td>
                    <td class=item>" . $drow->qty_sent . "</td>
                </tr>
            ";
                
        }

        }
        

        $brow = DB::table('jocom_user')
                        ->select('*')
                        ->where('username', '=', $row->buyer_username)
                        ->first();

        // $body2 = "
        //     Dear " . (($brow->full_name != "") ? $brow->full_name : $row->buyer_username) . ",<br /><br />
        //     The delivery is done for the following items:-<br /><br />
        //     ";
        $body2 = "
            Dear " . $row->delivery_name . ",<br /><br />
            The delivery is done for the following items:-<br /><br />
            ";

        $body2 .= $body . $pro_body;

        $body2 .= "
            </table>
            <br /><br />
            Please call us at 03-67348744 or email us at enquiries@tmgrocer.com with your username and Transaction ID if you require any assistance.<br /><br />
            Thank you and have a nice day!<br />
            <br />

        ";

        $file_name = urlencode($donum) . ".pdf";

        $attach = Config::get('constants.LOGISTIC_DO_PATH') . "/" . $file_name;

        $data = array('notify_body' => $body2);

        $mail = $row->buyer_email;

        $test = Config::get('constants.ENVIRONMENT');
        $testmail = Config::get('constants.TEST_MAIL');

        if ($test == 'test')
        {
            $mail = $testmail;
        }

        // $buyer_name = ($brow->full_name != "") ? $brow->full_name : $row->buyer_username;
        $buyer_name = $row->delivery_name;

        Mail::send('emails.notification', $data, function($message) use ($subject, $attach, $mail, $buyer_name)
        {
            $message->from('enquiries@tmgrocer.com', 'tmGrocer');
            $message->to($mail, $buyer_name)->subject($subject);
            $message->attach($attach);
        }
        );


    }


}
