<?php

use Datatables; 
use JocomRepo\FestivalCampaignRepository;

/**
 * Campaigns Controller
 */
class CampaignsController extends BaseController
{

    function __construct(FestivalCampaignRepository $festival)
    {
       $this->festival = $festival;
    }

    public function index()
    {
        // return Response::json($festivals);
        return View::make('campaigns.festivals-browse')->with(['campaigns' => $festivals]);
    }

    public function create()
    {
        return View::make('campaigns.festivals-add');
    }

    public function store()
    {
        // Validate
        $validate = $this->campaignsValidate(Input::all());

        // If validate pass, save to DB
        if ($validate->fails()) {
            return Redirect::back()->withErrors($validate)->withInput(Input::all());
        } else {
            $data = $this->festival->create(Input::all());
            return Redirect::back()->with('message', 'Festival Campaign Created!');
        }
    }

    // public function show($id)
    // {
    //     $festival = $this->festival->find($id);
    //     return Response::json($festival);
    // }

    public function edit($id)
    {
        $festival = $this->festival->find($id);
        return View::make('campaigns.festivals-edit')->with('festival', $festival);
    }

    public function update($id)
    {
        // Validate
        $validate = $this->campaignsValidate(Input::all());

        // If validate pass, update to DB
        if ($validate->fails()) {
            return Redirect::back()->withErrors($validate);
        } else {
            $data = $this->festival->update(Input::all(), $id);
            return Redirect::back()->with('message', 'Festival Campaign Updated!');
        }
    }

    public function getCampaignData()
    {
        $festivals = DB::table('jocom_festival_campaigns')->select(['id', 'title', 'effect', 'related_effect', 'qrcode', 'status']);

        return \Datatables::of($festivals)
            ->addColumn('action', function ($item) {
                return '<div class="text-center">
                    <a href="festival-campaigns/'.$item->id.'/edit" class="btn btn-warning sub" data-toggle="modal"><i class="fa fa-pencil"></i> Edit</a>
                </div>';
            })
            ->editColumn('effect',
                '@if($effect == 1)
                <h5><span class="label label-custm label-lg label-default">Screen</h5>
                @elseif($effect == 2)
                <h5><span class="label label-custm label-lg label-default">Confetti</h5>
                @elseif($effect == 3)
                <h5><span class="label label-custm label-lg label-default">Christmas</h5>
                @else
                <h5><span class="label label-custm label-lg label-default">Other</h5>
                @endif'
            )
            ->editColumn('related_effect',
                '@if($related_effect == 1)
                <h5><span class="label label-custm label-lg label-success">Yes</h5>
                @else
                <h5><span class="label label-custm label-lg label-danger">No</h5>
                @endif'
            )
            ->editColumn('status',
                '@if($status == 1)
                <h5><span class="label label-custm label-lg label-info">Active</h5>
                @else
                <h5><span class="label label-custm label-lg label-warning">Inactive</h5>
                @endif'
            )
            ->make(true);
    }

    public function fetchData()
    {
        $data = $this->festival->fetchLatest();
        if($data !== null) {
            return $this->results(true, 'Festival Campaigns', $data, []);
        } else {
            return $this->results(false, 'Festival Campaigns', $data, []);
        }
    }

    public function fetchDetails($id)
    {
        try {
            $data = $this->festival->find($id);
            if($data !== null) {
                return $this->results(true, 'Festival Campaigns', $data, []);
            }
        } catch (Exception $e) {
            return $this->results(false, 'Festival Campaigns', $data, []);
        }
    }

    private function results($status, $msg = [], $data = [], $errors = [])
    {
        if($status == true) {
            return Response::json(['status' => $status, 'message' => $msg, 'data' => $data]);
        } else {
            return Response::json(['status' => $status, 'message' => $msg, 'errors' => $errors]);
        }
    }

    private function campaignsValidate($data)
    {
        $messsages = [
            'title.required'=>'You cant leave title empty',
            'items.min'=>'Items field required, QRCODE(S) have to be separated with commas',
            'valid_from.required'=>'You cant leave From Date empty',
            'valid_to.required'=>'You cant leave To Date empty',
            'description_1.min'=>'Description has to be :min chars long',
            'description_2.min'=>'Description has to be :min chars long',
            'description_2.max'=>'Description has to be within :max chars',
        ];
        $rules = [
            'title' => 'required|min:3',
            'items' => 'required|min:3',
            'valid_from' => 'required',
            'valid_to' => 'required',
            'description_1' => 'required|min:8|max:300',
            'description_2' => 'min:8|max:300',
        ];
        $validator = Validator::make($data, $rules, $messsages);
        return $validator;
    }
}
