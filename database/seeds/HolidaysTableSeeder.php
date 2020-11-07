<?php

use Illuminate\Database\Seeder;

class HolidaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\HR\Masters\HrHolidaysM::truncate();
		$dataObj = [
			[
				'fin_year' => 'Fin Year 1',
				'holiday_date' => 'Holiday Date 1',
				'description' => 'Description 1',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'fin_year' => 'Fin Year 2',
				'holiday_date' => 'Holiday Date 2',
				'description' => 'Description 2',
				'inserted_by' => '1',
				'updated_by' => '1'
			],
			[
				'fin_year' => 'Fin Year 3',
				'holiday_date' => 'Holiday Date 3',
				'description' => 'Description 3',
				'inserted_by' => '1',
				'updated_by' => '1'
			]
		];
		for($i=0; $i< count($dataObj); $i++){
			App\Models\HR\Masters\HrHolidaysM::create($dataObj[$i]);
		}
    }
}
