<?php

class PointConversionController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function index()
    {
        return View::make('points.conversions.index');
    }

    public function datatables()
    {
        return Datatables::of(PointConversion::select('point_conversions.id', 'jocom_user.username', 'point_types_from.type AS from_type', 'point_conversions.point_from', 'point_types_to.type AS to_type', 'point_conversions.point_to', 'point_conversions.rate', 'point_conversions.charges', 'point_conversions.status', 'point_conversions.created_at')
                ->join('point_users', 'point_conversions.point_user_id', '=', 'point_users.id')
                ->join('jocom_user', 'point_users.user_id', '=', 'jocom_user.id')
                ->join('point_types AS point_types_from', 'point_conversions.type_from', '=', 'point_types_from.id')
                ->join('point_types AS point_types_to', 'point_conversions.type_to', '=', 'point_types_to.id')
            )
            ->edit_column('charges', '<?php echo ($charges * 100)."%"; ?>')
            ->edit_column('status', '<?php echo ($status == 1) ? "Success" : "Failed"; ?>')
            ->make();
    }
}
