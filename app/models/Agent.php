<?php

class Agent extends Eloquent
{
    protected $table = 'jocom_agents';

    public function setCreatedAtAttribute($value)
    {
        // Disable Eloquent default `created_at` column in DB
    }

    public function datatables()
    {
        return Agent::select('id', 'username', 'full_name', 'agent_code', 'email', 'contact_no', 'active_status');
    }

    public function scopeAgentCode($query, $code)
    {
        return $query->where('agent_code', '=', $code);
    }
}
