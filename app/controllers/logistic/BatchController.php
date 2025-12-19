<?php

class BatchController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }

 
    /**
     * Display a listing of the batch.
     *
     * @return Response
     */
    public function anyIndex()
    {
        // echo "sddasf";
        //$batch   = LogisticBatch::all();
        return View::make('logistic/batch.index');
    }

     /**
     * Display a listing of the batch on datatable.
     *
     * @return Response
     */
    
    public function anyBatch() {     
        // $batch = LogisticBatch::select('id', 'logistic_id', 'batch_date', 'driver_id', 'do_no', 'status');
        $userstatus ="";

        try{
            
        
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }
        
        $batch = DB::table('logistic_batch AS a')
                    ->select(array('a.id','a.batch_date', 'b.transaction_id', 'c.username', 'b.special_msg', 'b.do_no', 'a.status',
                        'b.delivery_city',
                        'b.delivery_state',
                        'b.delivery_addr_1',
                        'b.delivery_addr_2',
                        'b.delivery_postcode',  ))
                    ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                    ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id');
                 
        if(count($stateName) > 0){
            $batch = $batch->whereIn('b.delivery_state', $stateName);
        }
                        
        return Datatables::of($batch)
                ->edit_column('username',function($row){
                    // $name = DB::table('jocom_courier_orders')->where('batch_id', '=', $row->id)->lists('username');
                    $result = LogisticBatch::getBatchValid($row->id);
                    if($result == 0){
                        $userstatus = $row->username;
                    }
                    else if($result == 1)
                    {
                        $userstatus = "Ta Q Bin"; 
                    }
                    else if($result == 2){
                        $userstatus = "Line Clear"; 
                    }
                    return $userstatus;
                   
                })
                ->edit_column('status', '
                     @if($status == 0)
                        <p class="text-danger">Pending</p>
                    @elseif ($status == 1)
                        <p class="text-danger">Sending</p>
                    @elseif ($status == 2)
                        <p class="text-danger">Returned</p>
                    @elseif ($status == 3)
                        <p class="text-danger">Undelivered</p>
                    @elseif ($status == 4)
                        <p class="text-success">Sent</p>
                    @else 
                        <p class="text-danger">Cancelled</p>
                    @endif
                    ')
                ->add_column('address', function($row){
  
                    $address= $row->delivery_addr_1." ".$row->delivery_addr_2." ".$row->delivery_postcode." ".$row->delivery_city." ".$row->delivery_state;
                    
                    return $address;
                   
                })  
                ->add_column('Action', '
                        <a class="btn btn-primary" title="" data-toggle="tooltip" href="/batch/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 8, 9, \'AND\'))
                        <a id="deleteBatch" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$username}}" href="#"><i class="fa fa-remove"></i></a>
                        @endif
                    ')
                ->make();
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
    }
    }
    // href="/batch/delete/{{$id}}"


    public function anyEdit($id = null)
    {
        
        try{
            
        

        $batch = LogisticBatch::find($id);

        // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
        $Transaction = LogisticTransaction::find($batch->logistic_id);
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }
        if(!in_array($Transaction->delivery_state, $stateName)){
            $access = false;
            $SysAdmin = User::where("username",Session::get('username'))->first();
            $SysAdminRegion = SysAdminRegion::getSysAdminRegion($SysAdmin->id);
            foreach ($SysAdminRegion as $key => $value) {
                if($value->region_id == 0 ){
                    $access = true;
                }
            }
            if(!$access){
                return Redirect::to('batch')->with('message', "You are don't have access right for that Transaction ID")->withErrors($validator)->withInput();
            }
        }
        // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
        
        
        $transaction = LogisticTransaction::find($batch->logistic_id);

        $list = DB::table('logistic_batch_item AS a')
                    ->select('a.id', 'a.qty_assign', 'a.qty_pickup', 'a.qty_sent', 'a.remark as remark2', 'b.sku', 'b.name', 'b.label')
                    ->leftJoin('logistic_transaction_item AS b', 'a.transaction_item_id', '=', 'b.id')
                    ->where('a.batch_id', '=', $id)
                    ->get();

        $status = LogisticBatch::get_status($batch->status);
        $driver = LogisticDriver::find($batch->driver_id);
        //Delivery batch info
        if($driver->id != 0){
            $shipping_method = 'Jocom Delivery';
            $shipping_method_id = 0;
            $driver_id = $driver->id;
            $driver_name = $driver->name;
            $driver_username = $driver->username;
        }else{
            $Courier = Courier::find($batch->shipping_method);
            $shipping_method = $Courier->courier_name;
            $shipping_method_id = $batch->shipping_method;
            $driver_id = '';
            $driver_name = '';
            $driver_username = '';
        }
        
        

        $data['batch_id'] = $batch->id;
        $data['batch_date'] = $batch->batch_date;
        $data['sign_name'] = $batch->sign_name;
        $data['sign_ic'] = $batch->sign_ic;
        $data['shipping_method'] = $batch->shipping_method;
        $data['tracking_number'] = $batch->tracking_number;
        $data['accept_date'] = $batch->accept_date;
        $data['transaction_id'] = $transaction->transaction_id;
        $data['transaction_date'] = $transaction->transaction_date;
        $data['delivery_name'] = $transaction->delivery_name;
        $data['delivery_contact_no'] = $transaction->delivery_contact_no;
        $data['buyer_email'] = $transaction->buyer_email;
        $data['delivery_addr_1'] = $transaction->delivery_addr_1;
        $data['delivery_addr_2'] = $transaction->delivery_addr_2;
        $data['delivery_city'] = $transaction->delivery_city;
        $data['delivery_postcode'] = $transaction->delivery_postcode;
        $data['delivery_state'] = $transaction->delivery_state;
        $data['delivery_country'] = $transaction->delivery_country;
        $data['special_msg'] = $transaction->special_msg;
        $data['do_no'] = $batch->do_no;
        $data['remark'] = nl2br($batch->remark);
        $data['status'] = $status;
        $data['status2'] = $batch->status;
        $data['username'] = $driver_username;
        $data['driver_name'] = $driver_name;
        $data['driver_id'] = $driver_id;
        $data['shipping_method_name'] =$shipping_method;
        $data['shipping_method_id'] = $shipping_method_id;

        foreach ($list as $row)
        {
            $details[] = array(
                'item_id' => $row->id,
                'sku' => $row->sku,
                'name' => $row->name,
                'label' => $row->label,
                'qty_assign' => $row->qty_assign,
                'qty_pickup' => $row->qty_pickup,
                'qty_sent' => $row->qty_sent,
                'remark' => nl2br($row->remark2)
            );


        }

        $data['item'] = $details;

        return View::make('logistic.batch.edit')
                    ->with('display_batch', $data);   
                    
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }
    }

    /**
     * Update the specified batch in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        try{
            
        
        $data  = Input::all();

        if(LogisticBatch::UpdateBatch($id, $data))
        {
            $insert_audit = General::audit_trail('BatchController.php', 'Edit()', 'Edit Batch', Session::get('username'), 'CMS');
            return Redirect::to('batch/edit/'.$id)->with('success', 'Batch(ID: '.$id.') updated successfully.');
        }
        else
            return Redirect::to('batch/edit/'.$id)->with('message', 'Batch(ID: '.$id.') update failed.');
            
            
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
    }
    
    public function anyUnassign(){

        $driver = LogisticDriver::where('status',1)
                ->select('id','name')
                ->get();

        return View::make('logistic.batch.batch_unassign')->with('driver',$driver);   

    }
    public function anyUnassignbatch(){

        $driver  = Input::get('driver');
        $username = Session::get('username');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        $status = Input::get('status');

        $data = DB::table('logistic_batch AS LB')
                    ->leftjoin('logistic_driver AS LD', 'LD.id', '=', 'LB.driver_id')
                    ->leftjoin('logistic_transaction AS LT', 'LB.logistic_id', '=', 'LT.id')
                    ->select('LD.name', "LB.id","LB.assign_date","LB.logistic_id","LT.delivery_city","LT.delivery_state","LT.transaction_id", DB::raw("CASE WHEN LB.status = 0 THEN 'Pending' WHEN LB.status = 1 THEN 'Sending' WHEN LB.status = 2 THEN 'Returned' WHEN LB.status = 3 THEN 'Undelivered' WHEN LB.status = 4 THEN 'Sent' WHEN LB.status = 5 THEN 'Cancelled' ELSE 'NOT LOG IN' END AS 'status'"))
                    ->whereIn("LB.status",[0,1])
                    ->orderby('LB.id',Desc);

        if (Input::get('driver') != NULL) {
            $data= $data->where('LD.id', $driver);
        }

        if (Input::get('from_date') != NULL) {
            $data= $data->where('LB.assign_date', '>=', $from_date);
        }

        if (Input::get('to_date') != NULL) {
            $data= $data->where('LB.assign_date', '<=', $to_date);
        }

        if (Input::get('status') != NULL) {
            $data= $data->where('LB.status', $status);
        }

        return Datatables::of($data)->make(true);      

    }

    public function anyUnassignupdate()
    {
        $driver  = Input::get('driver');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        $status = Input::get('status');
        
        $result = DB::table('logistic_batch AS LB')
                    ->leftjoin('logistic_driver AS LD', 'LD.id', '=', 'LB.driver_id')
                    ->leftjoin('logistic_transaction AS LT', 'LB.logistic_id', '=', 'LT.id')
                    ->where('LD.id', $driver)
                    ->wherebetween('LB.assign_date', [$from_date, $to_date])
                    ->where('LB.status',$status)
                    ->select('LB.id')
                    ->lists('LB.id');

        foreach ($result as $key => $value) {
            $id = $value;

            $data['status'] = 2;
            $data['username'] = Session::get('username');

            if(LogisticBatch::UpdateBatch($id, $data))
            {
                $insert_audit = General::audit_trail('BatchController.php', 'Edit()', 'Edit Batch', Session::get('username'), 'CMS');
            }

        }



    }
    
    public function anyReturn() {

        if (Request::ajax()) {

            if (Input::get('driver_id')) {
                $input['driver_id'] = Input::get('driver_id');
            }

            $batches = DB::table('logistic_batch')
                        ->join('logistic_transaction', 'logistic_batch.logistic_id', '=', 'logistic_transaction.id')
                        ->join('logistic_driver', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                        ->where('logistic_batch.status', '=', 0)
                        ->select('logistic_batch.id', 'logistic_transaction.transaction_id', 'logistic_driver.name', 'logistic_batch.assign_date');

            if (!empty($input['driver_id']) && $input['driver_id'] != 'any') {
                $batches = $batches->where('driver_id', '=', $input['driver_id']);
            }

            return Datatables::of($batches)->make();   
        }

        $driverss = LogisticDriver::where('status', 1)
                    ->where('is_logistic_dashboard', 1)
                    ->select('id', 'name')
                    ->get();

        $drivers = array();
        $drivers['any'] = 'Any';
        foreach ($driverss as $driver) {
            $drivers[$driver->id] = $driver->name;
        }

        return View::make('logistic.batch.return')->with('drivers', $drivers);  
    }
    
    public function postReturnselected() {
        $batch_id = Input::get('batch_id');
        $batch_ids = explode(',', $batch_id);

        $isError = false;
        $message = 'Success';
        
        try{
            
            DB::beginTransaction();

            for ($i = 0; $i < count($batch_ids); $i++) {
                LogisticBatch::UpdateBatch($batch_ids[$i], array("status" => 2));
            }

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage();
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }
    }
   
}
?>