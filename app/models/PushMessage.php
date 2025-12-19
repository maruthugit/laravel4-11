<?php

class PushMessage extends Eloquent
{
    protected $table    = 'notification_messages';
    protected $fillable = ['messages', 'status', 'conditions'];
    protected $rules    = [
        'message' => 'required|max:255',
    ];

    public function scopeDatatables($query)
    {
        return $query->select(['id', 'title','message','main_category','sub_category','link_id', 'status', 'created_at', 'target', 'target_platform' , 'filename_image'])
                ->orderBy("id","desc");
    }

    public function scopeInQueue($query)
    {
        return $query->where('status', '=', 'Processing');
    }

    public function store($data)
    {
        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            return $validator->messages();
        } else {
            foreach ($data as $column => $data) {
                if (!empty($data)) {
                    $this->$column = $data;
                }
            }

            $this->save();

            return true;
        }
    }
}
