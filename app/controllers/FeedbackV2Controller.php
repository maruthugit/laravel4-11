<?php

class FeedbackV2Controller extends BaseController
{
    const DATE_FORMAT_ymdhhmm = "1";

    public function anyIndex(){

        return View::make('feedback.index');
    }

    public function anyListing(){

    	$result = DB::table('jocom_feedback AS JF')
    			->leftjoin('jocom_user AS JU', 'JU.id', '=', 'JF.user_id')
    			->leftjoin('jocom_feedback_img AS JFI', 'JFI.feedback_id', '=', 'JF.id')
    			->select('JF.id','JF.insert_date','JU.full_name','JF.comment','JFI.attachment','JF.type','JF.email')
    			->groupby('JF.id')
    			->orderBy('JF.insert_date', 'Desc');
 		
 		$actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="feedback/edit/{{$id}}"><i class="fa fa-pencil"></i></a>';

        return Datatables::of($result)
        		->edit_column('type', '@if($type == 0) <span class="label label-danger">Issue</span> @else <span class="label label-success">Feedback</span> @endif')
        		->add_column('Action', $actionBar)
            	->make(true);

    }

    public function anyEdit($id = null){

    	if (isset($id)) { 
    		$feedback = DB::table('jocom_feedback AS JF')
                ->leftjoin('jocom_user AS JU', 'JU.id', '=', 'JF.user_id')
                ->leftjoin('jocom_feedback_img AS JFI', 'JFI.feedback_id', '=', 'JF.id')
                ->where('JF.id','=', $id)
                ->select('JF.*','JU.email','JU.full_name','JU.mobile_no')
                ->orderBy('JF.insert_date', 'Desc')->first();

            $images = DB::table('jocom_feedback_img AS JFI')->where('feedback_id','=',$feedback->id)->get();

            return View::make('feedback.feedback_edit')->with(array('feedback' => $feedback, 'images'=>$images));

    	}

    }

    
}
