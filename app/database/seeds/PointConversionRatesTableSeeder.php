<?php

class PointConversionRatesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('point_conversion_rates')->delete();

        PointConversionRate::create([
            'type_from' => PointType::JOPOINT,
            'type_to'   => PointType::BCARD,
            'rate'      => 1,
            'charges'   => 0.05,
            'status'    => 1,
            'minimum'   => 100,
        ]);

        PointConversionRate::create([
            'type_from' => PointType::BCARD,
            'type_to'   => PointType::JOPOINT,
            'rate'      => 1,
            'charges'   => 0,
            'status'    => 1,
            'minimum'   => 100,
        ]);
    }
}
