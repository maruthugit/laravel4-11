<?php

class PointTypeTableSeeder extends Seeder {

	public function run()
	{
		PointType::create([
			'type' => 'BCard',
			'earn_rate' => 1,
			'redeem_rate' => 0,
			'status' => 0,
		]);
	}

}
