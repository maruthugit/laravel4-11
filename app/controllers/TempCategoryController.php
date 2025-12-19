<?php

class TempCategoryController extends BaseController
{

	public function anyIndex()
	{
		if (Session::get('role_id') == '1')
		{
			$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'temp_category')->get();

			return View::make('admin.temp_category', [
				'job'						=> $job,
			]);
		}
		else			
			return View::make('home.denied', array('module' => 'Temp Move Category'));
		
	}

	public function anyMovecategory()
	{
		$path  = Config::get('constants.CSV_JIT_PATH');

		$job = DB::table('jocom_job_queue')->select('*')->where('status', '=', 0)->where('job_name', '=', 'temp_category')->get();

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
            		$field = explode(',' , "ProductID,CategoryID,Second1,Second2,Second3");

	        		Transaction::temp_category($newfile, $insertfile, $field);
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

			$file_name  = 'temp_category_' . $date . '.csv';
			$file_inserted  = 'inserted_temp_category_' . $date . '.csv';
			$file_original  = 'original_temp_category_' . $date . '.csv';
			$file_ext   = $file->getClientOriginalExtension();


			if(strtolower($file_ext) == "csv") {
                $upload_file   = $file->move($dest_path, $file_name);
            }

            $newfile = $dest_path.$file_name;
            $insertfile = $log_path.$file_inserted;
            $originalfile = $log_path.$file_original;
            
            $count = 0;

            $job                = array(); 
	        $job['ref_id']      = 0;
	        $job['job_name']    = "temp_category";
	        $job['in_file']     = $file_name;
	        $job['remark']     	= "";
	        $job['request_by']  = Session::get('user_id');
	        $job['request_at']  = date('Y-m-d H:i:s');	        

	        $job['id'] = DB::table('jocom_job_queue')->insertGetId($job);

            if (file_exists($newfile))
            {
            	$field = explode(',' , "ProductID,CategoryID,Second1,Second2,Second3");

            	// create a copy of import list into same format
            	$original = fopen($originalfile, "w");
            	$temp_original = fopen($newfile, "r");

            	while(! feof($temp_original))
				{
					$data_original = fgetcsv($temp_original);

					if($count == 0 AND $data_original[$count] != "ProductID")
						fputcsv($original, $field);
					
					if (! is_bool($data_original))
						fputcsv($original, $data_original, ",", "\"");

					$count++;
				}

				fclose($original);
				fclose($temp_original);


				// call model
				Transaction::temp_category($newfile, $insertfile, $field);
				$done = Transaction::diff_pending($job);

				if ($done)
				{
					return Redirect::to('temp_category')->with('success', 'Move successfully!');
				}
				else
				{
					return Redirect::to('temp_category')->with('message', 'Move failed. Queue created!');
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


