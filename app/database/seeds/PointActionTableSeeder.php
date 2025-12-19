<?php

class PointActionTableSeeder extends Seeder {

	public function run()
	{
		DB::table('point_actions')->delete();

		PointAction::create(['action' => 'Earn']);
		PointAction::create(['action' => 'Redeem']);
		PointAction::create(['action' => 'Convert']);
		PointAction::create(['action' => 'Cash Buy']);
		PointAction::create(['action' => 'Cash Out']);
		PointAction::create(['action' => 'Reversal']);
		PointAction::create(['action' => 'Refund']);
	}

}
