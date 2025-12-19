<?php

class Device extends Eloquent
{
    protected $table    = 'notification_devices';
    protected $fillable = ['username', 'os', 'token', 'push', 'uuid'];

    public function scopeDatatables($query)
    {
        return $query->select([
            'notification_devices.id', 'notification_devices.os',
            'notification_devices.token', 'jocom_user.full_name',
            'notification_devices.push', 'notification_devices.updated_at',
            'notification_devices.username',
        ])
            ->leftJoin('jocom_user',
                'notification_devices.username', '=', 'jocom_user.username'
            );
    }

    public function scopePushEnabled($query)
    {
        return $query->where('push', '=', 1);
    }
    
    public function scopePushIOSEnabled()
    {
        return $query->where('push', '=', 1)->where('os', '=', 'iOS');
    }
    
    public function scopePushAndroidEnabled()
    {
        return $query->where('push', '=', 1)->where('os', '=', 'Android');
    }

    public function register($data)
    {
        switch (strtolower(array_get($data, 'os'))) {
            case 'android':
                $device = $this->firstOrNew([
                    'os'   => 'Android',
                    'uuid' => array_get($data, 'uuid'),
                ]);
                break;
            case 'ios':
                $device = $this->firstOrNew([
                    'os'    => 'iOS',
                    'token' => array_get($data, 'token'),
                ]);
                break;
            case 'ipad':
                $device = $this->firstOrNew([
                    'os'    => 'iPad',
                    'token' => array_get($data, 'token'),
                ]);
                break;
            case 'web':
                $device = $this->firstOrNew([
                    'os'    => 'Web',
                    'token' => array_get($data, 'token'),
                ]);
                break;
        }

        foreach ($data as $column => $data) {
            $device->$column = $data;
        }

        return $device->save();
    }

}
