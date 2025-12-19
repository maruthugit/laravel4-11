<?php

class InsertJITController extends BaseController
{

	public function anyIndex()
	{
		if (Session::get('role_id') == '1')
		{
			$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'insert_new_jit')->get();

			return View::make('admin.jit_insert', [
				'job'						=> $job,
			]);
		}
		else			
			return View::make('home.denied', array('module' => 'Insert JIT Transaction'));
		
	}

	public function anyInsertnewjit()
	{
		$path  = Config::get('constants.CSV_JIT_PATH');

		$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'insert_new_jit')->get();

		if (count($job) > 0)
		{
			echo "Completed for: </br>";

			foreach ($job as $row)
			{

				$job['id']    		= $row->id;
	        	$job['in_file']     = $row->in_file;

	        	$done = Transaction::diff_pending($job);

	        	if(!$done)
	        	{
	        		$newfile = $path.'original_' . $row->in_file;
            		$insertfile = $path.'inserted_' . $row->in_file;
            		$field = explode(',' , "Date,BuyerUsername,ProductID,Paid,Quantity,Price,ReferralFees,ReferralType");

	        		Transaction::insert_jit($newfile, $insertfile, $field);
					$done = Transaction::diff_pending($job);

	        		if ($done)
	        		{
	        			echo "Job ID: " . $job['id'] . "</br>";
	        			echo "File Name: " . $job['in_file'] . "</br>";
	        		}
	        	}
			}
		}
	}

	public function postStore()
	{
		if (Input::hasFile('csv'))
		{
			$file = Input::file('csv');

			
			$dest_path  = Config::get('constants.CSV_JIT_PATH');
			$log_path  = Config::get('constants.CSV_JIT_PATH');
        	$date       = date('Ymd_his');

			$file_name  = 'insert_new_jit_' . $date . '.csv';
			$file_inserted  = 'inserted_insert_new_jit_' . $date . '.csv';
			$file_original  = 'original_insert_new_jit_' . $date . '.csv';
			$file_ext   = $file->getClientOriginalExtension();


			if(strtolower($file_ext) == "csv") {
                $upload_file   = $file->move($dest_path, $file_name);
            }

            $newfile = $dest_path.$file_name;
            $insertfile = $log_path.$file_inserted;
            $originalfile = $log_path.$file_original;
            
            $count = 0;

            $job                = array(); 
	        $job['ref_id']      = 40;
	        $job['job_name']    = "insert_new_jit";
	        $job['in_file']     = $file_name;
	        $job['remark']     	= "";
	        $job['request_by']  = Session::get('user_id');
	        $job['request_at']  = date('Y-m-d H:i:s');	        

	        $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);

            if (file_exists($newfile))
            {
            	$field = explode(',' , "Date,BuyerUsername,ProductID,Paid,Quantity,Price,ReferralFees,ReferralType");

            	// create a copy of import list into same format
            	$original = fopen($originalfile, "w");
            	$temp_original = fopen($newfile, "r");

            	while(! feof($temp_original))
				{
					$data_original = fgetcsv($temp_original);

					if($count == 0 AND $data_original[$count] != "Date")
						fputcsv($original, $field);
					
					if (! is_bool($data_original))
						fputcsv($original, $data_original, ",", "\"");

					$count++;
				}

				fclose($original);
				fclose($temp_original);


				// call model
				Transaction::insert_jit($newfile, $insertfile, $field);
				$done = Transaction::diff_pending($job);

				if ($done)
				{
					return Redirect::to('jit_insert')->with('success', 'Import successfully!');
				}
				else
				{
					return Redirect::to('jit_insert')->with('message', 'Import failed. Queue created!');
				}
            }
		}

		
	}

	public function anyFiles($file = null) 
    {
    	$log_path  = Config::get('constants.CSV_JIT_PATH');

        if (file_exists($log_path.$file))
        {
            return Response::download($log_path.$file);            

        }
        else
        {
            echo "<br>File not exists!";
        }
    }
    /*    
    to change MShopping transaction to JIT transaction

    1. create using manual order
	2. https://api.jocom.com.my/jit_insert/alter/5096
	3. db change transaction date and adjust figure(if any)
	4. MS status to complete
	5. https://api.jocom.com.my/jit_insert/print/1730
	*/
    public function anyAlter($id) 
    {
    	echo "locked in coding";
    	exit;

    	$tempdata = DB::table('jocom_running_jit')->select('*')->where('value_key', '=', 'transaction_id')->first();

    	$counter = $tempdata->counter + 1;

    	if ($counter <= 4600)
    	{
    		$trans = Transaction::find($id);

    		if (count($trans) > 0)
    		{
    			DB::transaction(function() use ($id, $counter)
	            {
	            	DB::table('jocom_transaction')
			            ->where('id', $id)
			            ->update(array('id' => $counter));

			        // e37 to JOCOM IT
			        DB::table('jocom_transaction_details')
			            ->where('transaction_id', $id)
			            ->where('seller_id', '69')
			            ->update(array('seller_id' => '40', 'seller_username' => 'JOCOM'));

			        DB::table('jocom_transaction_details')
			            ->where('transaction_id', $id)
			            ->update(array('transaction_id' => $counter, 'parent_seller' => '0'));			        

			        DB::table('jocom_transaction_details_group')
			            ->where('transaction_id', $id)
			            ->update(array('transaction_id' => $counter));

			        DB::table('jocom_transaction_coupon')
			            ->where('transaction_id', $id)
			            ->update(array('transaction_id' => $counter));

			        DB::table('jocom_running_jit')
			            ->where('value_key', 'transaction_id')
			            ->update(array('counter' => $counter));
	            });

	            echo "Change transaction_date in DB, change CMS status, and you may proceed to ".asset('/')."jit_insert/print/".$counter." to generate pdf.";
    		}
    		else
    			echo "Transaction ID not found";

	        
    	}
    	else
    		echo "Transaction ID reach Max 4600";
    	
    }

    public function anyPrint($id) 
    {
    	echo "locked in coding";
    	exit;
    	
    	$tempInv = Transaction::generateInv($id);
        $tempPO  = Transaction::generatePO($id);
        $tempDO  = Transaction::generateDO($id);

        echo "done";
    }

	private function getValidator()
	{
		$rules = [
			'seller_id'		=> 'required',
			'zone_id'		=> 'required',
			'csv'		=> 'required',
		];

		$message = [
			'seller_id.required'	=> 'The Seller field is required.',
			'zone_id.required'		=> 'The Delivery Fee field is required.',
			'csv.required'		=> 'The Delivery Fee field is required.',
		];

		return Validator::make(Input::all(), $rules, $messages);
	}
}


