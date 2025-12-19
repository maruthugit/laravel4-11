<?php
use Helper\ImageHelper as Image;
class VisitorController extends BaseController
{
    
    
    /*
     * @Desc    : Get Visitor details
     * @Param   : visitor_id
     * @Method  : POST
     * @Return  : JSON
     */
        
    public function anyIndex(){


            $visitor = DB::table('jocom_visitor_details')
                    ->select(array(
                        'id', 
                        'name', 
                        'ic',
                        'visitor_datetime', 
                        'visitor_purpose', 
                        'status', 
                    ))
                    ->orderBy('id','desc')->get();

        return View::make('visitor.visitor_add')->with('visitor',$visitor);
    }

    public function anyListing(){

         $visitor = DB::table('jocom_visitor_details AS AD')
                    ->select(array(
                        'AD.id', 
                        'AD.name', 
                        'AD.ic',
                        'AD.visitor_datetime', 
                        'AD.visitor_purpose', 
                        'AD.status' 
                    ))
                    ->where('AD.status','=',1)
                    ->orderBy('AD.id','desc');


        return Datatables::of($visitor)
            ->edit_column('status', '
                @if($status == 1)
                    <p class="text-success">Active</p>
                @else
                    <p class="text-danger">Inactive</p>
                @endif
            ')
            ->add_column('Action', '
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/visitor/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                <a class="btn btn-danger" id="deletevisitor" title="" data-value="{{$name}}" data-toggle="tooltip" href="/visitor/delete/{{$id}}"><i class="fa fa-times"></i></a>
                
            ')
            ->make();

    }


    public function anyStore()
    {
        $visitor         = new Visitor;
        $validator    = Validator::make(Input::all(), Visitor::$rules);

        if ($validator->passes()) {

          $visitor->name                = Input::get('visitor_name');
          $visitor->ic                  = Input::get('visitor_ic');
          $visitor->visitor_datetime    = Input::get('visitor_datetime');
          $visitor->visitor_purpose     = Input::get('visit_purpose');
          $visitor->status              = 1;
          $visitor->created_at  = date('Y-m-d h:i:s');
          $visitor->created_by  = Session::get('username');

          
              
          if($visitor->save()) 
          {

            return Redirect::to('/visitor')->with('message', 'Added successfully.');
          }

        }else{    
            return Redirect::to('/visitor')->withErrors($validator)->withInput();
        }
    }

    public function anyEdit($id)
    {
        $visitor  =  DB::table('jocom_visitor_details AS VD')
                ->where('VD.id', $id)->first();

       

        $status = array("1"=>"Active","2"=>"Inactive");

        return View::make('visitor.visitor_edit')->with(array(
            'visitor'          => $visitor,
            'status'       => $status,
        ));
    }

    public function anyUpdate($id){

        $visitor = Visitor::find($id);

        $validator    = Validator::make(Input::all(), Visitor::$rules);

        if ($validator->passes()) {

            $visitor->name                = Input::get('visitor_name');
            $visitor->ic                  = Input::get('visitor_ic');
            $visitor->visitor_datetime    = Input::get('visitor_datetime');
            $visitor->visitor_purpose     = Input::get('visit_purpose');
            $visitor->status              = Input::get('status');
            $visitor->updated_by          = Session::get('username');
            $visitor->updated_at          = date('Y-m-d h:i:s');

            //update havent finish
            if($visitor->save()) 
            {
                 
                return Redirect::to('/visitor')->with('message', '(ID: '.$id.') updated successfully.');
            }
        }else{    
            return Redirect::to('/visitor/edit/'.$id)->withErrors($validator)->withInput();
        }


    }

    public function anyDelete($id)
    {
        $visitor = Visitor::find($id);

        $visitor->status = '2';
        $visitor->updated_by = Session::get('username');
        $visitor->updated_at = date('Y-m-d h:i:s');
        $visitor->save();

        return Redirect::to('/visitor')->with('message','(ID: '.$id.') deleted successfully.');
    }

    public function anyTemperaturelog()
    {
        if (Request::ajax()) {

            $log = DB::table('temperature_log')
                     ->select('id', 'name', 'temperature', 'phone', 'type', 'logged_at');

            if (Input::has('type')) {
                $type = Input::get('type');

                if ($type != 'any') {
                    $log = $log->where('type', $type);
                }
            }

            if (Input::has('date')) {
                $date = Input::get('date');

                $start = $date . ' 00:00:00';
                $end = $date . ' 23:59:59';

                $log = $log->where('logged_at', '>=', $start)
                           ->where('logged_at', '<=', $end);
            }
                     
            return Datatables::of($log)
                ->make();
        }

        return View::make('visitor.temperature_log');
    }

    public function anyExporttemperaturelog()
    {
        $log = DB::table('temperature_log')
                 ->select('name', 'phone', 'temperature', 'type', 'logged_at');

        if (Input::has('type')) {
            $type = Input::get('type');

            if ($type != 'any') {
                $log = $log->where('type', $type);
            }
        }

        if (Input::has('date')) {
            $date = Input::get('date');

            $start = $date . ' 00:00:00';
            $end = $date . ' 23:59:59';

            $log = $log->where('logged_at', '>=', $start)
                       ->where('logged_at', '<=', $end);
        }

        $log = $log->get();

        if (count($log) > 0) {
            $path = Config::get('constants.REPORT_PATH');
            $filename = $path.'/temperature_log_'.time().'.csv';
            $file = fopen($filename, 'w');

            fputcsv($file, ['Name', 'Phone', 'Temperature', 'Type', 'Logged At']);

            foreach ($log as $record) {
                fputcsv($file, [
                    $record->name,
                    $record->phone,
                    $record->temperature,
                    $record->type,
                    $record->logged_at
                ]);
            }

            fclose($file);

            return Response::download($filename);
        }

        return Response::json(['message' => 'No record.']);
    }
    
    
}
