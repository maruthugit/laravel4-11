<?php

class PointUser extends Eloquent
{
    const ACTIVE = 1;

    const ACTIVE_ONLY = true;

    const DELETED = 2;

    const INACTIVE = 0;

    protected $fillable = [
        'user_id', 'point', 'point_type_id', 'status', 'expiry',
    ];

    public function getDatatables()
    {
        return PointUser::select(
            'jocom_user.id',
            'jocom_user.username',
            'jocom_user.full_name',
            'jocom_user.ic_no',
            DB::raw('group_concat(concat(point_types.type, ": ", point_users.point) ORDER BY point_types.id SEPARATOR "<br>")'))
            ->join('jocom_user', 'point_users.user_id', '=', 'jocom_user.id')
            ->join('point_types', 'point_users.point_type_id', '=',
                'point_types.id')
            ->groupBy('jocom_user.id');
    }

    public function getDeactivateStatus($userId, $pointType = null)
    {
        if ($pointType) {
            $deactivate = DB::table('point_deactivate_users')
                ->where('point_type_id', '=', $pointType)
                ->where('user_id', '=', $userId)
                ->first();

            return isset($deactivate) ? false : true;
        } else {
            $deactivate = DB::table('point_deactivate_users')
                ->where('user_id', '=', $userId)
                ->lists('point_type_id');

            return $deactivate;
        }
    }

    public function getOrCreate($userId, $pointType = PointType::JOPOINT, $activeOnly = false)
    {
        if (PointUser::getDeactivateStatus($userId, $pointType) === false) {
            return null;
        }

        $user = PointUser::where('user_id', '=', $userId)
            ->where('point_type_id', '=', $pointType);

        if ($activeOnly) {
            $user = $user->where('point_users.status', '=', ($activeOnly) ? 1 : 0);
        }

        $pointUser = $user->orderBy('id', 'desc')->first();

        if (isset($pointUser) && $pointUser->count() > 0) {
            return $pointUser;
        } else {
            return PointUser::create([
                'user_id'       => $userId,
                'point'         => 0,
                'point_type_id' => $pointType,
                'status'        => PointUser::ACTIVE,
            ]);
        }
    }

    public function getPoint($userId, $pointType = PointType::JOPOINT, $activeOnly = false)
    {
        if (PointUser::getDeactivateStatus($userId, $pointType) === false) {
            return null;
        }

        $pointUser = PointUser::where('user_id', '=', $userId)
            ->where('point_type_id', '=', $pointType);

        if ($activeOnly) {
            $pointUser = $pointUser->where('point_users.status', '=', $activeOnly);
        }

        return $pointUser->orderBy('id', 'desc')->first();
    }

    public function getPoints($userId, $activeOnly = false)
    {
        $deactivatedIds = PointUser::getDeactivateStatus($userId, $pointType);
// OLD QUERY //
/*
        $pointUser = PointUser::select('point_users.*', 'point_types.*',
            'point_users.id AS id', 'point_users.status AS status')
            ->join('point_types', 'point_users.point_type_id', '=',
                'point_types.id')
            ->where('point_users.user_id', '=', $userId);
*/
// OLD QUERY //
        $pointUser = PointUser::select('point_users.*', 'point_types.*','point_users.id AS id', 'point_users.status AS status','bcard.username AS bcardUsername')
            ->leftjoin('jocom_user', 'point_users.user_id', '=','jocom_user.id')
            ->leftjoin('bcard', 'bcard.username', '=','jocom_user.username')
            ->join('point_types', 'point_users.point_type_id', '=','point_types.id')
            ->where('point_users.user_id', '=', $userId);

        if ($activeOnly) {
            $pointUser = $pointUser->where('point_types.status', '=', $activeOnly)
                ->where('point_users.status', '=', $activeOnly);
        }

        if (count($deactivatedIds) > 0) {
            $pointUser = $pointUser->whereRaw('point_type_id NOT IN ('.implode(', ', $deactivatedIds).')');
        }

        return $pointUser->get();
    }

    public function scopeActive($query)
    {
        return $query->where('point_users.status', '=', 1);
    }

    public function scopePointType($query, $pointTypeId)
    {
        return $query->where('point_type_id', '=', $pointTypeId);
    }

    public function scopeUsername($query, $username)
    {
        return $query->select('jocom_user.username', 'point_users.*')
            ->join('jocom_user', 'point_users.user_id', '=', 'jocom_user.id')
            ->where('jocom_user.username', '=', $username);
    }
}
