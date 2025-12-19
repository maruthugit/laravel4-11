<?php

class PointCustomerController extends BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth');
	}

	public function index()
	{
		return View::make('points.customers.index');
	}

	public function show($id)
	{
		$user = Customer::find($id);

		if ( ! $user) {
			return Redirect::to('points/customers');
		}

		$userPoints = PointUser::getPoints($id);

		foreach ($userPoints as $userPoint)
		{
			$transaction = new PointTransaction($userPoint);
			$transactions[$userPoint->id] = $transaction->history();
		}

		return View::make('points.customers.show', [
			'user' => $user,
			'points' => $userPoints,
			'transactions' => $transactions,
		]);
	}

	public function update($userId)
	{
		if (Input::has('action_id'))
		{
			return $this->updatePointStatus($userId);
		}

		if (Input::has('remark'))
		{
			return $this->updateTransactionRemark($userId);
		}
	}

	public function datatables()
	{
		return Datatables::of(PointUser::getDatatables())
			->add_column('action', function($point) {
				return '<a class="btn btn-primary btn-sm" href="'.url("points/customers/{$point->id}").'"><i class="fa fa-pencil"></i></a>';
			})
			->make();
	}

	public function activeCheck()
	{
		$userId = Input::get('user_id');
		$pointType = Input::get('point_type');

		$activeCount = PointUser::join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
			->where('point_users.user_id', '=', $userId)
			->where('point_types.type', '=', $pointType)
			->where('point_users.status', '=', 1)
			->count();

		if ($activeCount > 0)
		{
			echo 1;
			return;
		}
	}

	public function refundCheck()
	{
		$transactionId = Input::get('id');

		if ($transactionId)
		{
			$transaction = Transaction::findOrFail($transactionId);
			$userPoints  = PointUser::getPoints($transaction->buyer_id);

			foreach ($userPoints as $point)
			{
				$pointTransaction = DB::table('point_transactions')
					->where('point_transactions.transaction_id', '=', $transactionId)
					->where('point_transactions.point_user_id', '=', $point->id)
					->where('point_transactions.point_action_id', '=', PointAction::EARN)
					->first();

				if ($pointTransaction && $point->point - $pointTransaction->point < 0)
				{
					echo $point->point - $pointTransaction->point;
					return;
				}
			}
		}
	}

	private function updatePointStatus($userId)
	{
		$pointId   = Input::get('point_id');
		$action_id = Input::get('action_id');

		$point = PointUser::find($pointId);

		if ($point)
		{
			$point->status = $action_id;
			$point->save();

			switch ($action_id)
			{
				case PointUser::INACTIVE:
					$remark = 'Suspend user point';
					break;
				case PointUser::ACTIVE:
					$remark = 'Unsuspend user point';
					break;
			}

			General::audit_trail('PointCustomerController.php', 'updatePointStatus()', $remark, Session::get('username'), 'CMS');

			return Redirect::to("points/customers/{$userId}")->withSuccess('Successfully updated.');
		}
		else
		{
			return Redirect::to("points/customers/{$userId}")->withError('Failed to update.');
		}
	}

	private function updateTransactionRemark($userId)
	{
		$remark        = Input::get('remark');
		$transactionId = Input::get('transaction_id');

		$transaction = DB::table('point_transactions')
			->where('id', '=', $transactionId)
			->first();

		if ($transaction)
		{
			DB::table('point_transactions')
				->where('id', '=', $transactionId)
				->update(['remark' => $remark]);

			return Redirect::to("points/customers/{$userId}")->withSuccess('Successfully updated.');
		}
		else
		{
			return Redirect::to("points/customers/{$userId}")->withError('Failed to update.');
		}
	}

}
