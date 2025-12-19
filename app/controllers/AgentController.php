<?php

class AgentController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function index()
    {
        return View::make('agents/index');
    }

    public function create()
    {
        return View::make('agents/create');
    }

    public function store()
    {
        $validator = Validator::make(Input::all(), [
            'username'   => 'required|unique:jocom_agents',
            'agent_code' => 'required|unique:jocom_agents',
            'email'      => 'required|email|unique:jocom_agents',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $agent = new Agent;
        $agent->username     = Input::get('username');
        $agent->full_name    = Input::get('full_name');
        $agent->agent_code   = Input::get('agent_code');
        $agent->email        = Input::get('email');
        $agent->contact_no   = Input::get('contact_no');
        $agent->created_by   = Session::get('username');
        $agent->created_date = date('Y-m-d H:i:s');
        
        if ($agent->save()) {
            Session::flash('success', 'Agent added successfully.');
        }

        return Redirect::to('agents');
    }

    public function edit($id)
    {
        $agent = Agent::find($id);

        if (! $agent) {
            Session::flash('error', 'Agent not found.');

            return Redirect::to('agents');
        }

        return View::make('agents/edit', ['agent' => $agent]);
    }

    public function update($id)
    {
        $agent = Agent::find($id);
        $rules = [];

        if (! $agent) {
            Session::flash('error', 'Agent not found.');

            return Redirect::to('agents');
        }

        $agent->username      = Input::get('username');
        $agent->full_name     = Input::get('full_name');
        $agent->agent_code    = Input::get('agent_code');
        $agent->email         = Input::get('email');
        $agent->contact_no   = Input::get('contact_no');
        $agent->active_status = Input::get('active_status');
        $agent->modify_by     = Session::get('username');
        $agent->modify_date   = date('Y-m-d H:i:s');

        if ($agent->isDirty('username')) {
            $rules = array_merge($rules, ['username' => 'required|unique:jocom_agents']);
        }

        if ($agent->isDirty('agent_code')) {
            $rules = array_merge($rules, ['agent_code' => 'required|unique:jocom_agents']);
        }

        if ($agent->isDirty('email')) {
            $rules = array_merge($rules, ['email' => 'required|email|unique:jocom_agents']);
        }

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            if ($agent->save()) {
                Session::flash('success', 'Agent updated successfully.');
            }
        }

        return Redirect::to('agents');
    }

    public function destroy($id)
    {
        $agent = Agent::find($id);

        if ($agent) {
            $agent->active_status = 0;
            $agent->save();
        }

        return Redirect::to('agents');
    }

    public function datatables()
    {
        return Datatables::of(Agent::datatables())
            ->edit_column('active_status', function ($agent) {
                if ($agent->active_status == 1) {
                    $label = '<span class="label label-success">Active</span>';
                } else {
                    $label = '<span class="label label-danger">Inactive</span>';
                }

                return $label;
            })
            ->add_column('action', function ($agent) {
                if ( Permission::CheckAccessLevel(Session::get("role_id"), 21, 5, "AND"))
                {
                    $actionBar = '<a class="btn btn-primary btn-sm" href="'.url('agents/'.$agent->id.'/edit').'"><i class="fa fa-pencil"></i></a>
                    <a class="btn btn-danger btn-sm" data-target="#deactivate" data-toggle="modal" data-agent-id="'.$agent->id.'" data-agent-name="'.$agent->username.'"><i class="fa fa-ban"></i></a>';
                }
                else
                {
                    $actionBar = "";
                }
                return $actionBar;
            })
            ->make();
    }
}
