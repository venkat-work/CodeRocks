<?php

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Common\CoreCities::truncate();
		$dataObj = [
			[
				'short_name' => 'HYD',
				'city_name' => 'Hyderabad',
				'state_id' => '2',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'short_name' => 'NLR',
				'city_name' => 'Nellore',
				'state_id' => '1',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'short_name' => 'VIJ',
				'city_name' => 'Vijayawada',
				'state_id' => '1',
				'status' => 'active',
				'inserted_by' => '1',
				'updated_by' => '1'
			]
		];
		for($i=0; $i< count($dataObj); $i++){
			App\Models\Common\CoreCities::create($dataObj[$i]);
		}
    }
}
