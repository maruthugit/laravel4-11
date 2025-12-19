<?php

class PushQueue extends Eloquent
{
    protected $table    = 'notification_queues';
    protected $fillable = ['push_message_id', 'device_id', 'begin'];

    public function device()
    {
        return $this->hasOne('Device', 'id', 'device_id');
    }

    public function message()
    {
        return $this->hasOne('PushMessage', 'id', 'push_message_id');
    }

    public function scopeOperatingSystem($query, $os)
    {
        return $query->select('notification_queues.id', 'notification_devices.token', 'notification_messages.*', 'jocom_user.username','notification_devices.os','notification_devices.id AS device_id')
            ->join('notification_devices', 'notification_queues.device_id', '=', 'notification_devices.id')
            ->join('notification_messages', 'notification_queues.push_message_id', '=', 'notification_messages.id')
            ->leftJoin('jocom_user', 'notification_devices.username', '=', 'jocom_user.username')
            ->where('notification_devices.os', '=', $os)
            ->where('notification_queues.begin', '<=', date('Y-m-d H:i:s'))
            ->whereNull('notification_queues.sent_at')
            ->orderBy('notification_queues.push_message_id');
    }

    public function scopeDatatables($query)
    {
        return $query->select([
            'notification_queues.id', 'notification_devices.os', 'notification_messages.message',
            'jocom_user.full_name', 'notification_queues.begin',
        ])
            ->join('notification_messages', 'notification_queues.push_message_id', '=', 'notification_messages.id')
            ->join('notification_devices', 'notification_queues.device_id', '=', 'notification_devices.id')
            ->leftJoin('jocom_user', 'notification_devices.username', '=', 'jocom_user.username')
            ->whereNull('sent_at');
    }

    public function scopeDatatablesHistory($query)
    {
        return $query->select([
            'notification_queues.id', 'notification_devices.os', 'notification_messages.message',
            'jocom_user.full_name', 'notification_queues.sent_at',
        ])
            ->join('notification_messages', 'notification_queues.push_message_id', '=', 'notification_messages.id')
            ->join('notification_devices', 'notification_queues.device_id', '=', 'notification_devices.id')
            ->leftJoin('jocom_user', 'notification_devices.username', '=', 'jocom_user.username')
            ->where('sent_at', '!=', 'NULL');
    }

    public function scopeSent($query, $messageId)
    {
        return $query->where('push_message_id', '=', $messageId)
            ->whereNotNull('sent_at');
    }
}
